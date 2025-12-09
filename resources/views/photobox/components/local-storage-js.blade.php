{{-- Local Storage & Upload Manager --}}
<script>
    // IndexedDB Configuration
    const DB_NAME = 'FotokuDB';
    const DB_VERSION = 1;
    const STORE_PHOTOS = 'photos';

    class PhotoStorage {
        constructor() {
            this.db = null;
            this.ready = this.init();
        }

        init() {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open(DB_NAME, DB_VERSION);

                request.onerror = (event) => {
                    console.error('IndexedDB error:', event.target.error);
                    reject(event.target.error);
                };

                request.onsuccess = (event) => {
                    this.db = event.target.result;
                    console.log('✅ IndexedDB initialized');
                    resolve();
                };

                request.onupgradeneeded = (event) => {
                    const db = event.target.result;
                    if (!db.objectStoreNames.contains(STORE_PHOTOS)) {
                        const store = db.createObjectStore(STORE_PHOTOS, { keyPath: 'id' });
                        store.createIndex('sessionId', 'sessionId', { unique: false });
                        store.createIndex('status', 'status', { unique: false });
                    }
                };
            });
        }

        async savePhoto(photo) {
            await this.ready;
            return new Promise((resolve, reject) => {
                const transaction = this.db.transaction([STORE_PHOTOS], 'readwrite');
                const store = transaction.objectStore(STORE_PHOTOS);
                const request = store.put(photo);

                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        }

        async getPhotosBySession(sessionId) {
            await this.ready;
            return new Promise((resolve, reject) => {
                const transaction = this.db.transaction([STORE_PHOTOS], 'readonly');
                const store = transaction.objectStore(STORE_PHOTOS);
                const index = store.index('sessionId');
                const request = index.getAll(sessionId);

                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        }

        async getPhoto(id) {
            await this.ready;
            return new Promise((resolve, reject) => {
                const transaction = this.db.transaction([STORE_PHOTOS], 'readonly');
                const store = transaction.objectStore(STORE_PHOTOS);
                const request = store.get(id);
                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        }

        async deletePhoto(id) {
            await this.ready;
            return new Promise((resolve, reject) => {
                const transaction = this.db.transaction([STORE_PHOTOS], 'readwrite');
                const store = transaction.objectStore(STORE_PHOTOS);
                const request = store.delete(id);
                request.onsuccess = () => resolve();
                request.onerror = () => reject(request.error);
            });
        }

        async clearSession(sessionId) {
            await this.ready;
            // Get all photos for session first to revoke URLs if needed (optional)
            // For now just delete
            const photos = await this.getPhotosBySession(sessionId);
            const transaction = this.db.transaction([STORE_PHOTOS], 'readwrite');
            const store = transaction.objectStore(STORE_PHOTOS);

            photos.forEach(p => {
                store.delete(p.id);
            });

            return new Promise((resolve) => {
                transaction.oncomplete = () => resolve();
            });
        }

        async cleanupOtherSessions(currentSessionId) {
            await this.ready;
            return new Promise((resolve, reject) => {
                const transaction = this.db.transaction([STORE_PHOTOS], 'readwrite');
                const store = transaction.objectStore(STORE_PHOTOS);
                const request = store.openCursor();

                request.onsuccess = (event) => {
                    const cursor = event.target.result;
                    if (cursor) {
                        const photo = cursor.value;
                        // If photo belongs to a different session, delete it
                        if (photo.sessionId && String(photo.sessionId) !== String(currentSessionId)) {
                            console.log(`🧹 Cleaning up stale photo from session ${photo.sessionId}`);
                            cursor.delete();
                        }
                        cursor.continue();
                    } else {
                        resolve();
                    }
                };
                request.onerror = () => reject(request.error);
            });
        }
    }

    // Upload Manager
    class UploadManager {
        constructor(storage) {
            this.storage = storage;
            this.isUploading = false;
            this.queue = [];
            this.retryDelays = [1000, 3000, 5000, 10000]; // Backoff
        }

        async addToQueue(photo) {
            // Save to DB first with status 'pending'
            photo.status = 'pending';
            photo.retryCount = 0;
            await this.storage.savePhoto(photo);

            this.queue.push(photo);
            this.processQueue();
        }

        async processQueue() {
            if (this.isUploading || this.queue.length === 0) return;

            this.isUploading = true;
            const photo = this.queue[0]; // Peek

            try {
                console.log(`🚀 Uploading photo ${photo.sequence_number}...`);

                // Notify UI of start
                document.dispatchEvent(new CustomEvent('fotoku:uploadStart', { detail: { photoId: photo.id } }));

                // Convert blob to base64
                let base64 = await this.blobToBase64(photo.blob);

                // OPTIMIZATION: Resize image if too large to prevent upload timeouts
                // Target: Max 1920px width/height, 0.85 quality
                try {
                    base64 = await this.resizeImage(base64, 1920, 0.85);
                } catch (resizeError) {
                    console.warn('Image resize failed, using original', resizeError);
                }

                // Upload
                const response = await axios.post(`/photobox/${photoboxCode}/capture`, {
                    photo_data: base64,
                    sequence_number: photo.sequence_number
                }, {
                    timeout: 60000 // 60s timeout
                });

                if (response.data && response.data.success) {
                    // Update photo with server ID
                    photo.status = 'uploaded';
                    photo.serverId = response.data.photo.id;
                    photo.serverData = response.data.photo;

                    // Update DB
                    await this.storage.savePhoto(photo);

                    // Remove from queue
                    this.queue.shift();

                    console.log(`✅ Photo ${photo.sequence_number} uploaded! Server ID: ${photo.serverId}`);

                    // Notify UI
                    document.dispatchEvent(new CustomEvent('fotoku:photoUploaded', {
                        detail: {
                            sequence_number: photo.sequence_number,
                            serverId: photo.serverId,
                            serverData: photo.serverData,
                            localId: photo.id
                        }
                    }));
                } else {
                    throw new Error('Upload failed response');
                }

            } catch (error) {
                console.error(`❌ Upload failed for photo ${photo.sequence_number}:`, error);

                // Handle 400 Bad Request (likely session invalid or photo already exists)
                if (error.response && error.response.status === 400) {
                    console.warn(`⚠️ Skipping photo ${photo.sequence_number} due to 400 Bad Request (likely duplicate or invalid session)`);
                    // Mark as failed/skipped locally so we don't retry forever
                    photo.status = 'failed_400';
                    await this.storage.savePhoto(photo);
                    this.queue.shift(); // Remove from queue

                    // IMPORTANT: Must reset uploading flag and trigger next item
                    this.isUploading = false;
                    if (this.queue.length > 0) {
                        this.processQueue();
                    }
                    return;
                }

                photo.retryCount = (photo.retryCount || 0) + 1;

                if (photo.retryCount < this.retryDelays.length) {
                    const delay = this.retryDelays[photo.retryCount];
                    console.log(`⏳ Retrying in ${delay}ms...`);
                    await new Promise(r => setTimeout(r, delay));
                    // Keep in queue to retry
                } else {
                    console.error(`💀 Max retries reached for photo ${photo.sequence_number}`);
                    // Move to end of queue or mark as failed? 
                    // For now, keep trying but with max delay
                    await new Promise(r => setTimeout(r, 10000));
                }
            } finally {
                // Only reset if we didn't return early (like in the 400 case above)
                if (this.isUploading) {
                    this.isUploading = false;
                    // Process next
                    if (this.queue.length > 0) {
                        this.processQueue();
                    }
                }
            }
        }

        blobToBase64(blob) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(blob);
            });
        }

        resizeImage(base64Str, maxWidth = 1920, quality = 0.85) {
            return new Promise((resolve) => {
                const img = new Image();
                img.src = base64Str;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxWidth) {
                            width *= maxWidth / height;
                            height = maxWidth;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    resolve(canvas.toDataURL('image/jpeg', quality));
                };
                img.onerror = () => resolve(base64Str); // Fallback to original
            });
        }

        // Restore queue from DB on page load
        async restoreQueue(sessionId) {
            const photos = await this.storage.getPhotosBySession(sessionId);
            const pending = photos.filter(p => p.status === 'pending');
            // Sort by sequence or timestamp
            pending.sort((a, b) => a.sequence_number - b.sequence_number);

            if (pending.length > 0) {
                console.log(`♻️ Restoring ${pending.length} pending uploads...`);
                this.queue = pending;
                this.processQueue();
            }
            return photos;
        }
    }

    // Initialize Global Instances
    window.photoStorage = new PhotoStorage();
    window.uploadManager = new UploadManager(window.photoStorage);

</script>
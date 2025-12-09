{{-- Modern Share Section --}}
<section class="fade-in-delay max-w-5xl mx-auto">
    <div class="glass-card">
        <div class="p-8">
            {{-- Section Header --}}
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon" style="background: linear-gradient(135deg, var(--sandy-brown), #f4a261);">
                        <i class="fas fa-share-nodes"></i>
                    </div>
                    <div>
                        <h2 class="heading-2 mb-0">Bagikan Gallery</h2>
                        <p class="text-muted">Share hasil foto dengan keluarga dan teman</p>
                    </div>
                </div>
            </div>
            
            {{-- Share Options --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- WhatsApp Share --}}
                    <button onclick="shareToSocial()" class="group flex items-center gap-4 p-6 bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 rounded-2xl transition-all border border-green-200 w-full">
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center text-white flex-shrink-0">
                        <i class="fab fa-whatsapp text-xl"></i>
                    </div>
                    <div class="text-left flex-1">
                        <h3 class="font-semibold text-gray-800 mb-1">WhatsApp</h3>
                        <p class="text-sm text-gray-600">Bagikan ke kontak WhatsApp</p>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-gray-600 transition-colors flex-shrink-0"></i>
                </button>
                
                {{-- Copy Link --}}
                <button onclick="copyGalleryLink()" class="group flex items-center gap-4 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 rounded-2xl transition-all border border-blue-200 w-full">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-link text-xl"></i>
                    </div>
                    <div class="text-left flex-1">
                        <h3 class="font-semibold text-gray-800 mb-1">Copy Link</h3>
                        <p class="text-sm text-gray-600">Salin link gallery ini</p>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-gray-600 transition-colors flex-shrink-0"></i>
                </button>
            </div>
            
            {{-- Info Banner --}}
            <div class="mt-6 flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-info text-amber-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-amber-800">
                        <span class="font-medium">Link gallery aktif selama 30 hari</span> - 
                        Pastikan untuk mendownload foto sebelum masa berlaku habis
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

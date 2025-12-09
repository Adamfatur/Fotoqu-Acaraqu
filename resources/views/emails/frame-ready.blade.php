<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FOTOKU: Foto Kenangan Indahmu Sudah Siap! 📸✨</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); line-height: 1.6; color: #334155;">
    <div style="max-width: 600px; margin: 20px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
        <!-- Header with Brand -->
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #059669 100%); color: white; padding: 40px 30px; text-align: center; position: relative;">
            <h1 style="margin: 0; font-size: 2.5em; font-weight: bold; text-shadow: 0 2px 4px rgba(0,0,0,0.3); position: relative; z-index: 1;">📸 FOTOKU</h1>
            <p style="margin: 0; font-size: 1.2em; opacity: 0.9;">Menangkap Momen, Menciptakan Kenangan</p>
        </div>
        
        <!-- Main Content -->
        <div style="padding: 40px 30px;">
            <!-- Personal Greeting -->
            <div style="font-size: 1.3em; color: #1e293b; margin-bottom: 16px; font-weight: 600;">
                Halo {{ $photoSession->customer_name ?? 'Sobat Fotoku' }}! 👋✨
            </div>
            
            <div style="color: #64748b; font-size: 1.1em; margin-bottom: 24px;">
                🎉 <strong>Kabar gembira!</strong> Foto-foto kenangan indahmu sudah berhasil kami olah menjadi frame yang spektakuler dan siap untuk dibawa pulang!
            </div>

            <!-- Session Details -->
            <div style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); padding: 20px; border-radius: 12px; margin: 20px 0; border-left: 4px solid #059669;">
                <h3 style="margin-top: 0; color: #047857;">
                    📋 Detail Sesi Fotoku #{{ $photoSession->session_code }}
                </h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; padding: 8px;">
                            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; text-align: center; border: 1px solid #e2e8f0;">
                                <div style="font-size: 1.5em;">📅</div>
                                <strong>{{ $photoSession->created_at ? $photoSession->created_at->format('d M Y') : 'Hari Ini' }}</strong>
                            </div>
                        </td>
                        <td style="width: 50%; padding: 8px;">
                            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; text-align: center; border: 1px solid #e2e8f0;">
                                <div style="font-size: 1.5em;">📸</div>
                                <strong>{{ $photoSession->photos ? $photoSession->photos->count() : '5' }} Foto</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; padding: 8px;">
                            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; text-align: center; border: 1px solid #e2e8f0;">
                                <div style="font-size: 1.5em;">�️</div>
                                <strong>{{ $photoSession->frame_slots ?? '4' }} Slot Frame</strong>
                            </div>
                        </td>
                        <td style="width: 50%; padding: 8px;">
                            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; text-align: center; border: 1px solid #e2e8f0;">
                                <div style="font-size: 1.5em;">�</div>
                                <strong>{{ $photoSession->photobox->code ?? 'BOX-01' }}</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Main CTA Section -->
            <div style="background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%); padding: 24px; border-radius: 12px; margin: 24px 0; border-left: 4px solid #3b82f6;">
                <h3 style="margin-top: 0; color: #1d4ed8; text-align: center;">
                    🚀 Lihat & Ambil Semua Kenangan Kamu!
                </h3>
                <p style="text-align: center; margin-bottom: 24px; color: #64748b;">
                    Frame final dan semua foto individual sudah tersedia di gallery pribadi kamu. Klik tombol di bawah untuk mengakses gallery lengkap!
                </p>
                
                <div style="text-align: center;">
                    @php
                        // Generate gallery URL
                        $galleryUrl = route('photobox.user-gallery', ['session' => $photoSession->session_code]);
                    @endphp
                    
                    <a href="{{ $galleryUrl }}" style="display: inline-block; padding: 18px 36px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white !important; text-decoration: none; border-radius: 12px; margin: 20px 0; font-weight: bold; font-size: 16px; box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3); text-align: center; min-width: 200px;">
                        🖼️ Buka Gallery Kenangan Kamu
                    </a>
                </div>
                
                <p style="text-align: center; font-size: 13px; color: #64748b; margin-bottom: 0;">
                    Gallery pribadi tersedia 24/7 dan bisa diakses kapan saja 🌐<br>
                    Dari gallery, kamu bisa lihat semua foto dan unduh sesuai kebutuhan!
                </p>
            </div>

            <!-- What's Available in Gallery -->
            <div style="background: #fefce8; padding: 20px; border-radius: 12px; border-left: 4px solid #eab308;">
                <h3 style="margin-top: 0; color: #a16207;">📦 Yang Tersedia di Gallery:</h3>
                <ul style="margin-bottom: 0; color: #374151; padding-left: 20px;">
                    <li><strong>Frame Final 4x6 (2 strip 2x3)</strong> - Siap cetak dengan kualitas tinggi untuk dibagikan</li>
                    <li><strong>Semua Foto Individual</strong> - Setiap momen tersimpan dalam resolusi penuh</li>
                    <li><strong>Download ZIP</strong> - Unduh semuanya sekaligus untuk kemudahan</li>
                    <li><strong>Akses Selamanya</strong> - Link gallery tidak akan pernah kedaluwarsa</li>
                </ul>
            </div>

            <!-- Gallery Info -->
            <div style="text-align: center; margin: 32px 0; background: #f0f9ff; padding: 20px; border-radius: 12px;">
                <h3 style="margin-top: 0; color: #1d4ed8;">🖼️ Gallery Lengkap Menunggumu!</h3>
                <p style="color: #64748b; margin-bottom: 0;">
                    Di gallery, kamu bisa melihat frame final, semua foto individual, dan mengunduh sesuai kebutuhan. 
                    Semua tersedia dalam satu tempat yang mudah diakses! 🌟
                </p>
            </div>

            <!-- Tips Section -->
            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 24px; border-radius: 12px; margin: 32px 0;">
                <h3 style="margin-top: 0; color: #0369a1;">💡 Tips Maksimalkan Kenangan Kamu:</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; padding: 8px; text-align: center;">
                            <div style="font-size: 2em;">🖨️</div>
                            <strong>Cetak Frame</strong><br>
                            <small style="color: #64748b;">Ukuran 4x6 (2 strip 2x3) untuk hasil terbaik</small>
                        </td>
                        <td style="width: 50%; padding: 8px; text-align: center;">
                            <div style="font-size: 2em;">📱</div>
                            <strong>Share di Sosmed</strong><br>
                            <small style="color: #64748b;">Biar semua tau keseruannya!</small>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; padding: 8px; text-align: center;">
                            <div style="font-size: 2em;">☁️</div>
                            <strong>Backup ke Cloud</strong><br>
                            <small style="color: #64748b;">Google Drive, iCloud, dll</small>
                        </td>
                        <td style="width: 50%; padding: 8px; text-align: center;">
                            <div style="font-size: 2em;">🎁</div>
                            <strong>Jadikan Hadiah</strong><br>
                            <small style="color: #64748b;">Perfect buat orang tersayang</small>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Call to Action for Next Visit -->
            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fef7cd 100%); padding: 24px; border-radius: 12px; text-align: center; border-left: 4px solid #f59e0b;">
                <h3 style="margin-top: 0; color: #92400e;">🎪 Kapan Main ke FOTOKU Lagi?</h3>
                <p style="margin-bottom: 0; color: #78350f;">
                    Pengalaman yang seru kan? Ajak teman, keluarga, atau pasangan untuk menciptakan kenangan baru di FOTOKU! 
                    Setiap kunjungan adalah petualangan foto yang berbeda dan menyenangkan! 🌟
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="text-align: center; padding: 30px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #64748b;">
            <p style="font-size: 1.1em; margin-bottom: 8px;"><strong>Terima kasih telah mempercayai FOTOKU! 💙</strong></p>
            <p style="font-size: 0.95em; margin-bottom: 16px;">📸 <strong>Menangkap Momen, Menciptakan Kenangan Selamanya</strong></p>
            <p style="font-size: 0.8em; color: #94a3b8;">
                Email ini dikirim dengan cinta ke <strong>{{ $photoSession->customer_email ?? 'teman fotoku' }}</strong><br>
                © {{ date('Y') }} FOTOKU. Semua kenangan dilindungi dengan hati. ✨
            </p>
        </div>
    </div>
</body>
</html>

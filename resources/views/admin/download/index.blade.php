@extends('admin.layout')

@section('header', 'Unduh Aplikasi FotoQu')
@section('description', 'Dapatkan versi terbaru aplikasi desktop FotoQu untuk Mac dan Windows.')

@section('content')
    <div class="max-w-5xl mx-auto py-10">
        <div class="grid md:grid-cols-2 gap-8 mb-10">
            <!-- macOS -->
            <div
                class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col hover:-translate-y-1">
                <div
                    class="bg-gradient-to-br from-gray-50 to-white p-8 flex-1 flex flex-col items-center text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fab fa-apple text-9xl"></i>
                    </div>

                    <div
                        class="relative z-10 w-24 h-24 bg-white shadow-md rounded-2xl flex items-center justify-center mb-6 border border-gray-100">
                        <i class="fab fa-apple text-5xl text-gray-800"></i>
                    </div>
                    <h3 class="relative z-10 text-2xl font-bold text-gray-800 mb-2">FotoQu for macOS</h3>
                    <p class="relative z-10 text-gray-500 mb-6 px-4">Aplikasi Photobooth Profesional untuk macOS.
                        Terintegrasi penuh dengan cloud dashboard FotoQu.</p>

                    <div class="relative z-10 space-y-3 w-full text-left max-w-xs mx-auto">
                        <div
                            class="flex items-center text-sm text-gray-600 bg-white/60 p-2 rounded-lg backdrop-blur-sm border border-gray-100">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 shrink-0 text-xs">
                                <i class="fas fa-check"></i>
                            </div>
                            Dukungan Kamera DSLR & Webcam
                        </div>
                        <div
                            class="flex items-center text-sm text-gray-600 bg-white/60 p-2 rounded-lg backdrop-blur-sm border border-gray-100">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 shrink-0 text-xs">
                                <i class="fas fa-check"></i>
                            </div>
                            Pencetakan Otomatis & Kiosk Mode
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border-t border-gray-100">
                    <a href="https://drive.google.com/file/d/1vMUWmrrQK8oWFq5X937HtPbI-UDdK3Gl/view?usp=sharing"
                        target="_blank"
                        class="flex items-center justify-center w-full px-6 py-4 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-all shadow-lg shadow-gray-900/20 font-bold text-lg group">
                        <i class="fas fa-download mr-3 group-hover:scale-110 transition-transform"></i>
                        Unduh untuk Mac
                    </a>
                    <p class="text-center text-xs text-gray-400 mt-3 flex items-center justify-center gap-1">
                        <i class="fab fa-google-drive"></i> Hosted on Google Drive • .dmg
                    </p>
                </div>
            </div>

            <!-- Windows -->
            <div
                class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col hover:-translate-y-1">
                <div
                    class="bg-gradient-to-br from-blue-50/50 to-white p-8 flex-1 flex flex-col items-center text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fab fa-windows text-9xl text-blue-600"></i>
                    </div>

                    <div
                        class="relative z-10 w-24 h-24 bg-white shadow-md rounded-2xl flex items-center justify-center mb-6 border border-gray-100">
                        <i class="fab fa-windows text-5xl text-blue-600"></i>
                    </div>
                    <h3 class="relative z-10 text-2xl font-bold text-gray-800 mb-2">FotoQu for Windows</h3>
                    <p class="relative z-10 text-gray-500 mb-6 px-4">Aplikasi Photobooth Profesional untuk Windows.
                        Terintegrasi penuh dengan cloud dashboard FotoQu.</p>

                    <div class="relative z-10 space-y-3 w-full text-left max-w-xs mx-auto">
                        <div
                            class="flex items-center text-sm text-gray-600 bg-white/60 p-2 rounded-lg backdrop-blur-sm border border-gray-100">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 shrink-0 text-xs">
                                <i class="fas fa-check"></i>
                            </div>
                            Dukungan Kamera DSLR & Webcam
                        </div>
                        <div
                            class="flex items-center text-sm text-gray-600 bg-white/60 p-2 rounded-lg backdrop-blur-sm border border-gray-100">
                            <div
                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 shrink-0 text-xs">
                                <i class="fas fa-check"></i>
                            </div>
                            Pencetakan Otomatis & Kiosk Mode
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-white border-t border-gray-100">
                    <a href="https://drive.google.com/file/d/1t_X_HfAtO5z1Il1y9nDgzU_3qhjLWggt/view?usp=sharing"
                        target="_blank"
                        class="flex items-center justify-center w-full px-6 py-4 bg-[#1a90d6] text-white rounded-xl hover:bg-[#053a63] transition-all shadow-lg shadow-blue-600/20 font-bold text-lg group">
                        <i class="fas fa-download mr-3 group-hover:scale-110 transition-transform"></i>
                        Unduh untuk Windows
                    </a>
                    <p class="text-center text-xs text-gray-400 mt-3 flex items-center justify-center gap-1">
                        <i class="fab fa-google-drive"></i> Hosted on Google Drive • .exe
                    </p>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-[#053a63] to-[#1a90d6] rounded-2xl p-8 relative overflow-hidden shadow-xl text-white">
            <!-- Decoration -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                <div
                    class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shrink-0 border border-white/30">
                    <i class="fas fa-rocket text-4xl text-white"></i>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h4 class="text-2xl font-bold mb-2">Siap untuk Memulai?</h4>
                    <p class="text-white/80 max-w-xl">
                        Setelah mengunduh, pastikan Anda juga telah membuat <b>Photobox</b> dan mendapatkan <b>Access
                            Token</b> di halaman manajemen Photobox untuk menghubungkan aplikasi.
                    </p>
                </div>
                <a href="{{ route('admin.photoboxes.index') }}"
                    class="px-8 py-4 bg-white text-[#053a63] rounded-xl hover:bg-gray-50 font-bold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 whitespace-nowrap">
                    Kelola Photobox <i class="fas fa-arrow-right ml-2 opacity-70"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
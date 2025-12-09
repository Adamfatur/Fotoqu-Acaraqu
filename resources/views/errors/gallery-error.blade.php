<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Tidak Tersedia - {{ $session->session_code ?? 'Error' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-sans">
    <div class="min-h-screen py-8 px-4 flex items-center justify-center">
        <div class="max-w-md mx-auto bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-500/20 backdrop-blur-md rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-orange-500 text-3xl"></i>
            </div>
            
            <h1 class="text-3xl font-bold text-white mb-4">Gallery Tidak Tersedia</h1>
            
            <p class="text-white/80 text-lg mb-6">{{ $error_message ?? 'Terjadi kesalahan saat menampilkan gallery foto.' }}</p>
            
            @if(isset($session) && isset($session->session_code))
                <div class="bg-white/10 rounded-lg p-4 mb-6">
                    <p class="text-white/70">Kode Sesi: <span class="font-mono">{{ $session->session_code }}</span></p>
                </div>
            @endif
            
            <div class="space-y-4">
                <a href="/" class="block px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
                
                <p class="text-white/60 text-sm">Silakan mencoba kembali nanti atau hubungi bantuan jika masalah berlanjut.</p>
            </div>
        </div>
    </div>
</body>
</html>

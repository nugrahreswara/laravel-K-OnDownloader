@extends('layouts.app')

@section('title', 'Unduh Media - NekoDrop')

@section('content')
{{-- Hero Section --}}
<section class="relative overflow-hidden bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 text-white">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="absolute inset-0">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white px-5 py-2 rounded-full text-sm font-semibold mb-6">
                <i class="fas fa-bolt text-yellow-400"></i>
                <span>Unduh 2x Lebih Cepat dengan Teknologi Terbaru</span>
            </div>
            
            <h1 class="text-5xl md:text-6xl font-extrabold leading-tight tracking-tight mb-6">
                STOP RIBET<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-pink-500">START DROP</span>
            </h1>
            
            <p class="text-xl md:text-2xl text-white/90 max-w-3xl mx-auto mb-10">
                Nikmati kemudahan mengunduh **video dan audio** dari YouTube, Instagram, TikTok, dan Facebook dengan kualitas terbaik. Gratis selamanya tanpa iklan.
            </p>
            
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <a href="#download" class="bg-white text-indigo-900 hover:bg-gray-100 px-8 py-4 rounded-full font-bold text-lg transition-all transform hover:scale-105 shadow-xl">
                    <i class="fas fa-download mr-2"></i> Mulai Download
                </a>
                <a href="#features" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-indigo-900 px-8 py-4 rounded-full font-bold text-lg transition-all transform hover:scale-105">
                    <i class="fas fa-info-circle mr-2"></i> Pelajari Lebih Lanjut
                </a>
            </div>
            
            <div class="flex justify-center space-x-8 text-white">
                <div class="text-center">
                    <div class="text-3xl font-bold">2M+</div>
                    <div class="text-sm opacity-80">Total Download</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">4+</div>
                    <div class="text-sm opacity-80">Platform Didukung</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">99.5%</div>
                    <div class="text-sm opacity-80">Uptime Server</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Download Section --}}
<section id="download" class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Download Media Favorit Anda</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Cukup tempel URL dari platform media sosial favorit Anda dan pilih format yang Anda inginkan</p>
        </div>
        
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 p-8 md:p-10">
            <div class="mb-8">
                <div class="flex flex-wrap justify-center gap-3 mb-8">
                    <div class="flex items-center gap-2 bg-red-50 text-red-600 px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="fab fa-youtube"></i>
                        <span>YouTube</span>
                    </div>
                    <div class="flex items-center gap-2 bg-pink-50 text-pink-600 px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="fab fa-instagram"></i>
                        <span>Instagram</span>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="fab fa-tiktok"></i>
                        <span>TikTok</span>
                    </div>
                    <div class="flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="fab fa-facebook"></i>
                        <span>Facebook</span>
                    </div>
                </div>
                
                <form id="downloadForm" method="POST" action="{{ route('downloads.store') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="url" class="block text-sm font-semibold text-gray-700 mb-2">
                            Tempel URL Konten
                        </label>
                        <div class="relative">
                            <input type="url"
                                   id="url"
                                   name="url"
                                   class="w-full px-5 py-4 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all text-lg"
                                   placeholder="https://youtube.com/watch?v=..."
                                   required>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-link text-gray-400 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div id="videoInfo" class="hidden opacity-0 transition-all duration-500">
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-200">
                            <div class="flex gap-4">
                                <div id="videoThumbnail" class="w-36 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-3">
                                        <div id="platformBadge" class="w-12 h-12 bg-white rounded-xl flex items-center justify-center flex-shrink-0 border border-gray-200 shadow-sm">
                                            <i class="fas fa-video text-gray-600 text-xl"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 id="videoTitle" class="font-bold text-gray-900 text-lg line-clamp-2 mb-2">
                                                Mendeteksi video...
                                            </h4>
                                            <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                                <span id="videoUploader" class="flex items-center gap-1">
                                                    <i class="fas fa-user"></i>
                                                    <span>Tidak Diketahui</span>
                                                </span>
                                                <span id="videoDuration" class="flex items-center gap-1">
                                                    <i class="fas fa-clock"></i>
                                                    <span>--:--</span>
                                                </span>
                                                <span id="videoViews" class="flex items-center gap-1 hidden">
                                                    <i class="fas fa-eye"></i>
                                                    <span>0</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="downloadOptions" class="hidden opacity-0 transition-all duration-500 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div id="qualitySection" class="hidden">
                                <label for="quality" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kualitas Video
                                </label>
                                <select id="quality"
                                        name="quality"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all">
                                    <option value="best">Kualitas Terbaik</option>
                                    <option value="1080p">1080p Full HD</option>
                                    <option value="720p">720p HD</option>
                                    <option value="480p">480p SD</option>
                                    <option value="360p">360p</option>
                                    <option value="240p">240p</option>
                                </select>
                            </div>

                            <div id="formatSection" class="hidden">
                                <label for="format" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Format File
                                </label>
                                <select id="format"
                                        name="format"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all">
                                    <option value="mp4">MP4 Video</option>
                                    <option value="mp3">MP3 Audio</option>
                                    <option value="m4a">M4A Audio</option>
                                    <option value="wav">WAV Audio</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" id="downloadBtn" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-4 rounded-xl font-bold text-lg transition-all shadow-lg shadow-indigo-500/30 active:shadow-none active:translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-download mr-2"></i>
                            Download Sekarang
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="text-center text-gray-500 text-sm">
                <p>Dengan menggunakan layanan kami, Anda menyetujui <a href="#" class="text-indigo-600 hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-indigo-600 hover:underline">Kebijakan Privasi</a> kami.</p>
            </div>
        </div>
    </div>
</section>

{{-- Features Section --}}
<section id="features" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Mengapa Memilih NekoDrop?</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Kami menawarkan fitur-fitur terbaik untuk pengalaman download yang optimal</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl p-6 border border-green-200 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center mb-4 group-hover:bg-green-600 transition-colors">
                    <i class="fas fa-check text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Kualitas 4K</h3>
                <p class="text-gray-600">Unduh video dalam kualitas tertinggi hingga 4K Ultra HD dengan audio yang jernih</p>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-cyan-100 rounded-2xl p-6 border border-blue-200 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-600 transition-colors">
                    <i class="fas fa-bolt text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Super Cepat</h3>
                <p class="text-gray-600">Teknologi terbaru kami memungkinkan download 2x lebih cepat dari layanan lain</p>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-2xl p-6 border border-purple-200 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-600 transition-colors">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aman & Privat</h3>
                <p class="text-gray-600">Data Anda aman bersama kami dengan enkripsi end-to-end dan tanpa penyimpanan riwayat</p>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-50 to-orange-100 rounded-2xl p-6 border border-yellow-200 hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-yellow-500 rounded-xl flex items-center justify-center mb-4 group-hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-infinity text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Gratis Selamanya</h3>
                <p class="text-gray-600">Nikmati semua fitur premium kami tanpa biaya tersembunyi atau iklan yang mengganggu</p>
            </div>
        </div>
    </div>
</section>

{{-- How It Works Section --}}
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Cara Kerja NekoDrop</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Hanya 3 langkah sederhana untuk mengunduh media favorit Anda</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="relative">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 h-full hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <span class="text-2xl font-bold text-indigo-600">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Salin URL Konten</h3>
                    <p class="text-gray-600 text-center">Buka video atau audio di platform media sosial, lalu salin URL dari browser atau aplikasi</p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 z-10">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-arrow-right text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="relative">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 h-full hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <span class="text-2xl font-bold text-purple-600">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Tempel & Pilih Format</h3>
                    <p class="text-gray-600 text-center">Tempel URL ke kotak yang tersedia, lalu pilih kualitas dan format file yang Anda inginkan</p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 z-10">
                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-arrow-right text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="relative">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 h-full hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <span class="text-2xl font-bold text-green-600">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Unduh & Nikmati</h3>
                    <p class="text-gray-600 text-center">Klik tombol Download dan media Anda akan tersimpan di perangkat dalam hitungan detik</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Platform Details Section --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Platform yang Didukung</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Unduh konten dari berbagai platform media sosial populer</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-center">
                    <i class="fab fa-youtube text-white text-5xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">YouTube</h3>
                    <p class="text-gray-600 mb-4">Unduh video, playlist, dan audio dari YouTube dengan kualitas hingga 4K</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Video & Audio</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Playlist Support</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Subtitle Download</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                <div class="bg-gradient-to-r from-pink-500 to-purple-600 p-6 text-center">
                    <i class="fab fa-instagram text-white text-5xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Instagram</h3>
                    <p class="text-gray-600 mb-4">Simpan foto, video, reels, dan stories dari Instagram dengan mudah</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Photos & Videos</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Reels & IGTV</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Stories & Highlights</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                <div class="bg-gradient-to-r from-gray-800 to-black p-6 text-center">
                    <i class="fab fa-tiktok text-white text-5xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">TikTok</h3>
                    <p class="text-gray-600 mb-4">Unduh video TikTok tanpa watermark dan simpan musik favorit Anda</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Videos No Watermark</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Audio Only</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Profile Downloads</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center">
                    <i class="fab fa-facebook text-white text-5xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Facebook</h3>
                    <p class="text-gray-600 mb-4">Simpan video, reels, dan konten publik dari Facebook dengan kualitas HD</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Public Videos</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Reels & Stories</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>HD Quality</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Testimonials Section --}}
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Apa Kata Pengguna Kami</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Bergabung dengan jutaan pengguna yang telah mempercayai NekoDrop</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center mb-4">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold text-gray-900">Ahmad Rizki</h4>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600">"Aplikasi download terbaik yang pernah saya gunakan! Cepat, mudah, dan tanpa iklan. Sangat membantu untuk pekerjaan saya sebagai content creator."</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center mb-4">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold text-gray-900">Siti Nurhaliza</h4>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600">"Saya suka bisa mengunduh video dari berbagai platform dalam satu tempat. Kualitasnya juga sangat bagus, terutama untuk YouTube 4K!"</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center mb-4">
                    <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="User" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold text-gray-900">Budi Santoso</h4>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600">"Interface yang sangat user-friendly dan proses download yang super cepat. Sangat direkomendasikan untuk siapa saja yang membutuhkan media downloader!"</p>
            </div>
        </div>
    </div>
</section>

{{-- FAQ Section --}}
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-xl text-gray-600">Temukan jawaban untuk pertanyaan umum tentang NekoDrop</p>
        </div>
        
        <div class="space-y-6">
            <div class="bg-gray-50 rounded-xl overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFAQ(this)">
                    <h3 class="text-lg font-semibold text-gray-900">Apakah NekoDrop benar-benar gratis?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                </button>
                <div class="hidden px-6 pb-4">
                    <p class="text-gray-600">Ya, NekoDrop sepenuhnya gratis untuk digunakan. Kami tidak memungut biaya tersembunyi atau membatasi jumlah unduhan Anda. Anda dapat mengunduh sebanyak yang Anda inginkan tanpa membayar sepeser pun.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFAQ(this)">
                    <h3 class="text-lg font-semibold text-gray-900">Apakah data saya aman dengan NekoDrop?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                </button>
                <div class="hidden px-6 pb-4">
                    <p class="text-gray-600">Keamanan data Anda adalah prioritas utama kami. Kami menggunakan enkripsi SSL untuk semua transmisi data dan tidak menyimpan riwayat unduhan atau informasi pribadi Anda. Data yang Anda berikan hanya digunakan untuk memproses permintaan unduhan.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFAQ(this)">
                    <h3 class="text-lg font-semibold text-gray-900">Platform apa saja yang didukung oleh NekoDrop?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                </button>
                <div class="hidden px-6 pb-4">
                    <p class="text-gray-600">Saat ini, NekoDrop mendukung unduhan dari YouTube, Instagram, TikTok, dan Facebook. Kami terus berupaya untuk menambahkan dukungan untuk platform media sosial lainnya di masa mendatang.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl overflow-hidden">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFAQ(this)">
                    <h3 class="text-lg font-semibold text-gray-900">Bagaimana cara mengunduh video dalam kualitas tertinggi?</h3>
                    <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                </button>
                <div class="hidden px-6 pb-4">
                    <p class="text-gray-600">Untuk mengunduh video dalam kualitas tertinggi, pilih opsi "Kualitas Terbaik" atau "4K" (jika tersedia) di menu dropdown kualitas. Perlu diingat bahwa kualitas tertinggi mungkin tidak tersedia untuk semua video, tergantung pada kualitas asli video tersebut.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- About Section --}}
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl font-bold text-gray-900 mb-6">Tentang NekoDrop</h2>
                <div class="space-y-4 text-gray-600 mb-8">
                    <p>NekoDrop adalah solusi modern untuk kebutuhan download media sosial. Kami memahami betapa pentingnya konten digital dalam kehidupan sehari-hari, dan hadir untuk mempermudah akses Anda terhadap konten favorit.</p>
                    <p>Dengan teknologi terkini dan interface yang intuitif, kami berkomitmen menyediakan pengalaman download yang cepat, aman, dan tanpa kompromi pada kualitas.</p>
                    <p>Tim kami terdiri dari para ahli teknologi yang berdedikasi untuk terus meningkatkan layanan kami dan menambahkan fitur-fitur baru berdasarkan masukan dari pengguna.</p>
                </div>
                
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl p-4 shadow-md">
                        <div class="text-3xl font-bold text-indigo-600 mb-1">2022</div>
                        <div class="text-sm text-gray-600">Tahun Berdiri</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-md">
                        <div class="text-3xl font-bold text-indigo-600 mb-1">2M+</div>
                        <div class="text-sm text-gray-600">Pengguna Aktif</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-md">
                        <div class="text-3xl font-bold text-indigo-600 mb-1">10M+</div>
                        <div class="text-sm text-gray-600">Total Download</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-md">
                        <div class="text-3xl font-bold text-indigo-600 mb-1">99.9%</div>
                        <div class="text-sm text-gray-600">Uptime Server</div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-envelope mr-2"></i> Hubungi Kami
                    </a>
                    <a href="#" class="bg-transparent border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 px-6 py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-question-circle mr-2"></i> Bantuan
                    </a>
                </div>
            </div>
            
            <div class="relative">
                <img src="https://ik.imagekit.io/hdxn6kcob/cat.png?updatedAt=1761654620477" alt="NekoDrop" class="w-full rounded-2xl shadow-xl border border-gray-100">
                <div class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-lg p-4 max-w-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">100% Aman</div>
                            <div class="text-sm text-gray-500">Tanpa malware atau virus</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Team Section --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Tim Pengembang</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Berkenalan dengan tim di balik NekoDrop</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-8 border border-indigo-100 hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center">
                    <img src="https://ik.imagekit.io/hdxn6kcob/Screenshot%20from%202025-10-28%2017-09-32.png?updatedAt=1761642587185" alt="Developer" class="w-32 h-32 rounded-full border-4 border-indigo-200 mb-4 object-cover">
                    <h3 class="text-xl font-bold text-gray-900 mb-1">@meowzheta</h3>
                    <p class="text-indigo-600 font-semibold mb-3">Fullstack Developer</p>
                    <p class="text-gray-600 text-center mb-4">Pengembangan & Maintenance Platform dengan fokus pada pengalaman pengguna yang optimal</p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition-shadow">
                            <i class="fab fa-github text-gray-700"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition-shadow">
                            <i class="fab fa-linkedin text-blue-600"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition-shadow">
                            <i class="fab fa-twitter text-blue-400"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 border border-purple-100 hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col items-center">
                    <img src="https://ik.imagekit.io/hdxn6kcob/Screenshot%20from%202025-10-28%2016-47-17.png?updatedAt=1761642508504" alt="Developer" class="w-32 h-32 rounded-full border-4 border-purple-200 mb-4 object-cover">
                    <h3 class="text-xl font-bold text-gray-900 mb-1">@nugrahreswara</h3>
                    <p class="text-purple-600 font-semibold mb-3">Network Administrator</p>
                    <p class="text-gray-600 text-center mb-4">Manajemen Server & Jaringan untuk memastikan layanan yang cepat dan andal</p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition-shadow">
                            <i class="fab fa-github text-gray-700"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition-shadow">
                            <i class="fab fa-linkedin text-blue-600"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition-shadow">
                            <i class="fab fa-twitter text-blue-400"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-6">Siap untuk Mulai Download?</h2>
        <p class="text-xl mb-8 opacity-90">Bergabunglah dengan jutaan pengguna yang telah menikmati layanan kami</p>
        <a href="#download" class="bg-white text-indigo-600 hover:bg-gray-100 px-8 py-4 rounded-full font-bold text-lg transition-all transform hover:scale-105 shadow-xl">
            <i class="fas fa-download mr-2"></i> Mulai Sekarang
        </a>
    </div>
</section>

{{-- Footer --}}
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <img src="https://ik.imagekit.io/hdxn6kcob/cat.png?updatedAt=1761654620477" alt="NekoDrop" class="w-10 h-10 rounded-lg">
                    <span class="text-xl font-bold">NekoDrop</span>
                </div>
                <p class="text-gray-400">Solusi modern untuk kebutuhan download media sosial Anda.</p>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold mb-4">Layanan</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white transition-colors">YouTube Downloader</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Instagram Downloader</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">TikTok Downloader</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Facebook Downloader</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold mb-4">Perusahaan</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white transition-colors">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Tim</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Karir</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold mb-4">Dukungan</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white transition-colors">Bantuan</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">FAQ</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Kontak</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Status</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 mb-4 md:mb-0">© 2023 NekoDrop. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Syarat & Ketentuan</a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">Kebijakan Privasi</a>
            </div>
        </div>
    </div>
</footer>

<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3"></div>
@endsection

@push('styles')
<style>
{{-- Custom animations --}}
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Professional base styles */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

{{-- Custom scrollbar --}}
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f3f4f6;
}

::-webkit-scrollbar-thumb {
    background: #6366f1; /* Indigo-500 */
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #4f46e5; /* Indigo-600 */
}

{{-- Text selection --}}
::selection {
    background: rgba(99, 102, 241, 0.2);
}

{{-- Smooth transitions --}}
.transition-smooth {
    transition: all 0.2s ease;
}
</style>
@endpush

@push('scripts')
<script>
// FAQ toggle function
function toggleFAQ(element) {
    const content = element.nextElementSibling;
    const icon = element.querySelector('i');
    
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

const storageUrl = '{{ asset('storage/') }}';

{{-- Stats update --}}
function updateStats() {
    fetch('{{ route("stats") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const totalUsersElement = document.getElementById('totalUsers');
            if (totalUsersElement) {
                totalUsersElement.textContent = formatNumber(data.total_users);
            }

            const totalDownloadsElement = document.getElementById('totalDownloads');
            if (totalDownloadsElement) {
                // If the PHP provides a raw number, update it. Otherwise, keep the static 2M+
                if (data.total_downloads && data.total_downloads !== '2M+') {
                    totalDownloadsElement.textContent = formatNumber(data.total_downloads);
                }
            }
        }
    })
    .catch(error => {
        console.warn('Failed to update stats:', error);
    });
}

function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// Update stats every 30 seconds
setInterval(updateStats, 30000);

// Initial update
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
});

// Main functionality
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const downloadForm = document.getElementById('downloadForm');
    const urlInput = document.getElementById('url');
    const videoInfo = document.getElementById('videoInfo');
    const downloadOptions = document.getElementById('downloadOptions');
    const qualitySection = document.getElementById('qualitySection');
    const formatSection = document.getElementById('formatSection');
    const qualitySelect = document.getElementById('quality');
    const formatSelect = document.getElementById('format');
    const downloadBtn = document.getElementById('downloadBtn');

    // Video info elements
    const videoThumbnail = document.getElementById('videoThumbnail');
    const platformBadge = document.getElementById('platformBadge');
    const videoTitle = document.getElementById('videoTitle');
    const videoUploader = document.getElementById('videoUploader');
    const videoDuration = document.getElementById('videoDuration');
    const videoViews = document.getElementById('videoViews');

    let videoDetected = false;
    const videoInfoCache = new Map();

    // Utility for showing Toast notifications (assumed to be implemented elsewhere)
    function showToast(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        
        let icon = '';
        let colorClasses = '';

        if (type === 'success') {
            icon = '<i class="fas fa-check-circle mr-2"></i>';
            colorClasses = 'bg-green-500 border-green-700';
        } else if (type === 'error') {
            icon = '<i class="fas fa-times-circle mr-2"></i>';
            colorClasses = 'bg-red-500 border-red-700';
        } else {
            icon = '<i class="fas fa-info-circle mr-2"></i>';
            colorClasses = 'bg-blue-500 border-blue-700';
        }

        toast.className = `p-4 text-white rounded-lg shadow-lg border-b-4 ${colorClasses} opacity-0 transform translate-x-full transition-all duration-300`;
        toast.innerHTML = `<div class="flex items-center">${icon}<span>${message}</span></div>`;

        container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-x-full');
        }, 10);

        // Animate out and remove
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-full');
            toast.addEventListener('transitionend', () => toast.remove());
        }, 5000);
    }

    // Debounce function
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // URL validation (basic)
    function isValidUrl(string) {
        try {
            const url = new URL(string);
            return url.protocol === "http:" || url.protocol === "https:";
        } catch (_) {
            return false;
        }
    }

    // Platform detection (basic)
    function detectPlatform(url) {
        if (url.includes('youtube.com') || url.includes('youtu.be')) return 'youtube';
        if (url.includes('instagram.com')) return 'instagram';
        if (url.includes('tiktok.com')) return 'tiktok';
        if (url.includes('facebook.com') || url.includes('fb.watch')) return 'facebook';
        return null;
    }

    // Show/Hide Logic
    function showDownloadOptions(showQuality) {
        downloadOptions.classList.remove('hidden');
        setTimeout(() => {
            downloadOptions.classList.remove('opacity-0');
        }, 10);
        formatSection.classList.remove('hidden');
        
        if (showQuality) {
            qualitySection.classList.remove('hidden');
        } else {
            qualitySection.classList.add('hidden');
        }
    }

    function hideVideoInfo() {
        videoInfo.classList.add('opacity-0');
        downloadOptions.classList.add('opacity-0');
        setTimeout(() => {
            videoInfo.classList.add('hidden');
            downloadOptions.classList.add('hidden');
        }, 300);
        videoDetected = false;
        urlInput.classList.remove('border-green-500', 'border-red-500');
        urlInput.classList.add('border-gray-300');
    }

    // URL input handler
    urlInput.addEventListener('input', debounce(function() {
        const url = this.value.trim();
        if (url && isValidUrl(url)) {
            detectVideo(url);
        } else {
            hideVideoInfo();
        }
    }, 500)); // Increased debounce for better performance

    urlInput.addEventListener('blur', function() {
        const url = this.value.trim();
        if (url && isValidUrl(url) && !videoDetected) {
            detectVideo(url);
        }
    });

    // Form submission - Direct download
    if (downloadForm) {
        downloadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!videoDetected) {
                showToast('Silakan masukkan URL video yang valid', 'error');
                urlInput.classList.remove('border-gray-300');
                urlInput.classList.add('border-red-500');
                return;
            }

            const formData = new FormData(this);
            const originalText = downloadBtn.innerHTML;

            downloadBtn.disabled = true;
            downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

            // Reset border on success/error logic
            urlInput.classList.remove('border-green-500', 'border-red-500');
            urlInput.classList.add('border-gray-300');

            // Prepare data for direct download
            const downloadData = {
                url: formData.get('url'),
                platform: detectPlatform(formData.get('url')),
                quality: formData.get('quality') || 'best',
                format: formData.get('format') || 'mp4',
                audio_only: formData.get('format') === 'mp3' || formData.get('format') === 'm4a' || formData.get('format') === 'wav'
            };

            fetch('{{ route("downloads.direct-download") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(downloadData)
            })
            .then(response => {
                if (response.ok) {
                    // Trigger download directly
                    return response.blob().then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;

                        // Get filename from response headers or use default
                        const contentDisposition = response.headers.get('Content-Disposition');
                        let filename = 'download';
                        if (contentDisposition) {
                            const matches = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                            if (matches && matches[1]) {
                                filename = matches[1].replace(/['"]/g, '');
                            }
                        }

                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        showToast('Download berhasil dimulai!', 'success');
                    });
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Gagal memulai download');
                    });
                }
            })
            .catch(error => {
                console.error('Download error:', error);
                showToast(error.message || 'Terjadi kesalahan saat memulai download', 'error');
            })
            .finally(() => {
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = originalText;
            });
        });
    }

    // Format change handler
    formatSelect.addEventListener('change', function() {
        const format = this.value;
        const audioFormats = ['mp3', 'm4a', 'wav'];

        if (audioFormats.includes(format)) {
            qualitySection.classList.add('hidden');
        } else {
            qualitySection.classList.remove('hidden');
        }
    });

    // Dummy fetch for formats (In a real app, this route would dynamically fetch quality options)
    function fetchAvailableFormats(url, platform) {
        // Mocking API call for quality options
        return new Promise(resolve => {
            setTimeout(() => {
                // Example dynamic options for YouTube
                const qualityOptions = {
                    'best': 'Kualitas Terbaik (Auto)',
                    '2160p': '4K UHD',
                    '1080p': '1080p Full HD',
                    '720p': '720p HD',
                    '480p': '480p SD',
                    '360p': '360p',
                };
                resolve(qualityOptions);
            }, 100);
        });
    }

    // Detect video function
    function detectVideo(url) {
        const cacheKey = `${url}`;
        if (videoInfoCache.has(cacheKey)) {
            const cachedData = videoInfoCache.get(cacheKey);
            updateVideoInfo(cachedData.video_info, cachedData.platform);
            if (cachedData.platform === 'youtube') {
                populateQualityOptions(cachedData.qualityOptions);
                showDownloadOptions(true);
            } else {
                qualitySection.classList.add('hidden');
                showDownloadOptions(false);
            }
            videoDetected = true;
            urlInput.classList.remove('border-gray-300', 'border-red-500');
            urlInput.classList.add('border-green-500');
            return;
        }

        showVideoInfoLoading();

        const platform = detectPlatform(url);
        if (!platform) {
            hideVideoInfo();
            showToast('Platform tidak didukung', 'error');
            urlInput.classList.remove('border-gray-300');
            urlInput.classList.add('border-red-500');
            return;
        }

        updatePlatformBadge(platform);

        fetch('{{ route("downloads.video-info") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                url: url,
                platform: platform
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.video_info) {
                updateVideoInfo(data.video_info, platform);
                urlInput.classList.remove('border-gray-300', 'border-red-500');
                urlInput.classList.add('border-green-500');


                if (platform === 'youtube') {
                    fetchAvailableFormats(url, platform)
                        .then(qualityOptions => {
                            populateQualityOptions(qualityOptions);
                            showDownloadOptions(true);
                            videoDetected = true;
                            videoInfoCache.set(cacheKey, {
                                video_info: data.video_info,
                                platform: platform,
                                qualityOptions: qualityOptions
                            });
                        })
                        .catch(error => {
                            // Fallback to default if format fetching fails
                            const defaultOptions = {
                                'best': 'Kualitas Terbaik (Auto)',
                                '1080p': '1080p Full HD',
                                '720p': '720p HD',
                                '480p': '480p SD',
                                '360p': '360p',
                                '240p': '240p'
                            };
                            populateQualityOptions(defaultOptions);
                            showDownloadOptions(true);
                            videoDetected = true;
                            videoInfoCache.set(cacheKey, {
                                video_info: data.video_info,
                                platform: platform,
                                qualityOptions: defaultOptions
                            });
                        });
                } else {
                    // For non-YouTube platforms, only show format
                    qualitySection.classList.add('hidden');
                    showDownloadOptions(false);
                    videoDetected = true;
                    videoInfoCache.set(cacheKey, {
                        video_info: data.video_info,
                        platform: platform,
                        qualityOptions: null
                    });
                }
            } else {
                hideVideoInfo();
                showToast(data.message || 'Gagal mendeteksi video', 'error');
                videoDetected = false;
                urlInput.classList.remove('border-gray-300', 'border-green-500');
                urlInput.classList.add('border-red-500');
            }
        })
        .catch(error => {
            console.error('Error detecting video:', error);
            hideVideoInfo();
            showToast('Terjadi kesalahan saat mendeteksi video', 'error');
            videoDetected = false;
            urlInput.classList.remove('border-gray-300', 'border-green-500');
            urlInput.classList.add('border-red-500');
        });
    }

    function showVideoInfoLoading() {
        videoInfo.classList.remove('hidden');
        setTimeout(() => {
            videoInfo.classList.remove('opacity-0');
        }, 10);

        videoTitle.textContent = 'Mendeteksi video...';
        videoUploader.querySelector('span').textContent = 'Tidak Diketahui';
        videoDuration.querySelector('span').textContent = '--:--';
        videoViews.classList.add('hidden');
    }

    function updateVideoInfo(info, platform) {
        videoTitle.textContent = info.title || 'Judul tidak tersedia';
        videoUploader.querySelector('span').textContent = info.uploader || 'Tidak diketahui';
        videoDuration.querySelector('span').textContent = info.duration || '--:--';

        if (info.view_count) {
            videoViews.querySelector('span').textContent = formatNumber(info.view_count);
            videoViews.classList.remove('hidden');
        } else {
            videoViews.classList.add('hidden');
        }

        if (info.thumbnail) {
            let thumbnailSrc = info.thumbnail;
            // NOTE: Assuming there's a thumbnail proxy route in Laravel for external images
            if (thumbnailSrc.startsWith('http')) {
                thumbnailSrc = `/thumbnail-proxy/${btoa(thumbnailSrc)}`;
            } else if (thumbnailSrc) {
                thumbnailSrc = storageUrl + thumbnailSrc;
            }
            videoThumbnail.innerHTML = `<img src="${thumbnailSrc}" alt="Thumbnail" class="w-full h-full object-cover">`;
        } else {
            videoThumbnail.innerHTML = '<div class="w-full h-full flex items-center justify-center bg-gray-200"><i class="fas fa-image text-gray-400"></i></div>';
        }
    }

    function updatePlatformBadge(platform) {
        const badges = {
            youtube: '<i class="fab fa-youtube text-red-500 text-xl"></i>',
            instagram: '<i class="fab fa-instagram text-pink-500 text-xl"></i>',
            tiktok: '<i class="fab fa-tiktok text-gray-900 text-xl"></i>',
            facebook: '<i class="fab fa-facebook text-blue-600 text-xl"></i>'
        };

        platformBadge.innerHTML = badges[platform] || '<i class="fas fa-video text-gray-600 text-xl"></i>';
    }

    function populateQualityOptions(options) {
        qualitySelect.innerHTML = '';
        for (const [key, value] of Object.entries(options)) {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = value;
            qualitySelect.appendChild(option);
        }
    }

    // Initial check in case a URL is pre-filled (though unlikely for a fresh download page)
    const initialUrl = urlInput.value.trim();
    if (initialUrl && isValidUrl(initialUrl)) {
        detectVideo(initialUrl);
    }
});
</script>
@endpush
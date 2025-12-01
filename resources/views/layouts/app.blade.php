<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NekoDrop Media Downloader - Download Media Favorit Anda')</title>
    <meta name="description" content="NekoDrop Media Downloader: Download video dan audio dari YouTube, TikTok, Instagram, dan Facebook dengan mudah dan cepat!">
    <meta name="google-site-verification" content="oMYA8JTdhBECgn1xZ8UgdQJmVEbukjzRaERFFUIPyJA" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&family=M+PLUS+Rounded+1c:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/enhanced-styles.css') }}">
    @vite(['resources/css/app.css'])

    @stack('styles')

    <style>
        /* Enhanced CSS Variables */
        :root {
            --color-primary: #f97316;
            --color-primary-dark: #ea580c;
            --color-secondary: #fb923c;
            --color-accent: #fdba74;
            --color-background: #fff7ed;
            --color-surface: rgba(255, 255, 255, 0.9);
            --color-text-primary: #1f2937;
            --color-text-secondary: #6b7280;
            --color-border: rgba(249, 115, 22, 0.1);
        }

        /* Base Styles */
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background: var(--color-background);
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .logo-text {
            font-family: 'M PLUS Rounded 1c', sans-serif;
            font-weight: 900;
        }

        /* Enhanced Navigation */
        .futuristic-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--color-border);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.08);
        }

        .nav-link {
            position: relative;
            transition: color 0.2s ease;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: linear-gradient(to right, var(--color-primary), var(--color-primary-dark));
            border-radius: 2px;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            width: 80%;
        }

        .nav-link.active {
            color: var(--color-primary);
            font-weight: 700;
        }

        /* Mobile Menu */
        #mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        #mobile-menu.open {
            transform: translateX(0);
        }

        #mobile-menu-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            background: rgba(0, 0, 0, 0.3);
        }

        #mobile-menu-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* Enhanced Cards */
        .futuristic-card {
            background: var(--color-surface);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--color-border);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.08);
            transition: all 0.2s ease;
        }

        .futuristic-card:hover {
            box-shadow: 0 6px 16px rgba(249, 115, 22, 0.12);
        }

        /* Enhanced Buttons */
        .futuristic-button {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: white;
            font-weight: 600;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .futuristic-button:hover {
            box-shadow: 0 6px 16px rgba(249, 115, 22, 0.25);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        /* Loading Spinner */
        .loading-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Footer Wave */
        .footer-wave {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        }

        .footer-wave::before {
            content: '';
            position: absolute;
            top: -60px;
            left: 0;
            width: 100%;
            height: 120px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z' fill='%23fff7ed'/%3E%3C/svg%3E") no-repeat;
            background-size: cover;
        }

        /* Enhanced Tooltip */
        .tooltip-enhanced {
            position: relative;
        }

        .tooltip-enhanced::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-8px);
            padding: 6px 10px;
            background: rgba(31, 41, 55, 0.9);
            color: white;
            font-size: 12px;
            font-weight: 500;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 1000;
        }

        .tooltip-enhanced:hover::after {
            opacity: 1;
            transform: translateX(-50%) translateY(-4px);
        }

        /* Footer Link Animation */
        .footer-link {
            position: relative;
            transition: color 0.2s ease;
        }
        
        .footer-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
            transition: width 0.2s ease;
        }
        
        .footer-link:hover::after {
            width: 100%;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(249, 115, 22, 0.05);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, var(--color-primary), var(--color-primary-dark));
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, var(--color-primary-dark), #dc2626);
        }

        /* Focus States */
        button:focus-visible,
        a:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        /* Selection Color */
        ::selection {
            background: rgba(249, 115, 22, 0.2);
        }

        /* Responsive Typography */
        @media (max-width: 768px) {
            .logo-text {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="text-gray-900 min-h-screen flex flex-col">
    <!-- Skip to content for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg z-50 font-bold shadow">
        Skip to content
    </a>

    <!-- Enhanced Navigation -->
    <nav class="futuristic-nav sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('downloads.create') }}" class="flex items-center gap-4">
                        <div class="w-16 h-16 overflow-hidden">
                            <img src="https://ik.imagekit.io/hdxn6kcob/nekodrop.png?updatedAt=1761014269538" alt="NekoDrop Logo" class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col">
                            <span class="logo-text text-3xl bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-orange-500">NEKODROP</span>
                            <span class="text-sm text-gray-600 font-bold tracking-wider">MEDIA DOWNLOADER</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('downloads.create') }}" 
                       class="nav-link text-gray-700 hover:text-orange-600 px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-3 {{ request()->routeIs('downloads.create') ? 'active' : '' }}">
                        <i class="fas fa-download text-lg"></i>
                        <span>UNDUH</span>
                    </a>
                    <a href="{{ route('downloads.index') }}" 
                       class="nav-link text-gray-700 hover:text-orange-600 px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-3 {{ request()->routeIs('downloads.index') || request()->routeIs('downloads.show') ? 'active' : '' }}">
                        <i class="fas fa-history text-lg"></i>
                        <span>RIWAYAT</span>
                    </a>
                    <a href="{{ route('donasi') }}" 
                       class="nav-link text-gray-700 hover:text-orange-600 px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-3 {{ request()->routeIs('downloads.index') || request()->routeIs('downloads.show') ? 'active' : '' }}">
                        <i class="fas fa-donate text-lg"></i>
                        <span>DONASI</span>
                    </a>

                    <!-- Divider -->
                    <div class="w-px h-8 bg-gray-300 mx-4"></div>

                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" 
                            class="tooltip-enhanced text-gray-700 hover:text-orange-600 p-3 rounded-xl hover:bg-orange-50 transition-colors"
                            data-tooltip="Toggle Dark Mode"
                            aria-label="Toggle dark mode">
                        <i class="fas fa-moon text-xl"></i>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center gap-3">
                    <button id="theme-toggle-mobile" 
                            class="text-gray-700 hover:text-orange-600 p-3 rounded-xl hover:bg-orange-50 transition-colors"
                            aria-label="Toggle dark mode">
                        <i class="fas fa-moon text-xl"></i>
                    </button>
                    <button id="mobile-menu-button" 
                            class="text-gray-700 hover:text-orange-600 p-3 rounded-xl hover:bg-orange-50 transition-colors"
                            aria-label="Toggle menu"
                            aria-expanded="false">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Enhanced Mobile Navigation Menu -->
    <div id="mobile-menu" class="md:hidden fixed top-0 left-0 w-80 h-full z-40 overflow-y-auto">
        <div class="p-6 space-y-4">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl overflow-hidden">
                        <img src="https://ik.imagekit.io/hdxn6kcob/nekodrop.png?updatedAt=1761014269538" alt="NekoDrop Logo" class="w-full h-full object-cover">
                    </div>
                    <span class="logo-text text-2xl bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-transparent">NEKODROP</span>
                </div>
                <button id="mobile-menu-close" class="text-gray-500 hover:text-orange-600 p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- User Section -->
            <div class="futuristic-card rounded-2xl p-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-user text-black text-2xl"></i>
                    </div>
                    <div>
                        <p class="font-black text-gray-900 text-lg">Halo! 👋</p>
                        <p class="text-gray-600 font-medium">Selamat datang di NekoDrop</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="space-y-2">
                <a href="{{ route('downloads.create') }}" 
                   class="flex items-center gap-4 text-gray-700 hover:bg-orange-50 px-4 py-4 rounded-2xl text-base font-bold transition-colors {{ request()->routeIs('downloads.create') ? 'bg-orange-50 text-orange-600' : '' }}">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-download text-black text-lg"></i>
                    </div>
                    <span>UNDUH MEDIA</span>
                </a>
                
                <a href="{{ route('downloads.index') }}" 
                   class="flex items-center gap-4 text-gray-700 hover:bg-orange-50 px-4 py-4 rounded-2xl text-base font-bold transition-colors {{ request()->routeIs('downloads.index') ? 'bg-orange-50 text-orange-600' : '' }}">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-history text-black text-lg"></i>
                    </div>
                    <span>RIWAYAT DOWNLOAD</span>
                </a>

                <a href="{{ route('donasi') }}" 
                   class="flex items-center gap-4 text-gray-700 hover:bg-orange-50 px-4 py-4 rounded-2xl text-base font-bold transition-colors {{ request()->routeIs('downloads.index') ? 'bg-orange-50 text-orange-600' : '' }}">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-donate text-black text-lg"></i>
                    </div>
                    <span>DONASI</span>
                </a>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-6"></div>

            <!-- Social Links -->
            <div class="pt-6">
                <p class="text-gray-600 font-bold mb-4 text-sm uppercase tracking-wider">IKUTI KAMI</p>
                <div class="flex gap-3">
                    <a href="#" class="w-12 h-12 futuristic-card rounded-xl flex items-center justify-center hover:bg-orange-50 transition-colors">
                        <i class="fab fa-github text-gray-700 text-xl"></i>
                    </a>
                    <a href="#" class="w-12 h-12 futuristic-card rounded-xl flex items-center justify-center hover:bg-blue-50 transition-colors">
                        <i class="fab fa-twitter text-gray-700 text-xl"></i>
                    </a>
                    <a href="#" class="w-12 h-12 futuristic-card rounded-xl flex items-center justify-center hover:bg-purple-50 transition-colors">
                        <i class="fab fa-discord text-gray-700 text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="md:hidden fixed inset-0 z-30"></div>

    <!-- Main Content -->
    <main id="main-content" class="flex-1">
        <div class="py-8">
            <!-- Enhanced Flash Messages -->
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="futuristic-card rounded-2xl p-6 flex items-start gap-4 fade-in border-l-4 border-green-500" role="alert">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-black text-gray-900 text-lg mb-1">Berhasil! 🎉</p>
                            <p class="text-gray-700 font-medium">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors p-2">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="futuristic-card rounded-2xl p-6 flex items-start gap-4 fade-in border-l-4 border-red-500" role="alert">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-black text-gray-900 text-lg mb-1">Error! ⚠️</p>
                            <p class="text-gray-700 font-medium">{{ session('error') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors p-2">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="futuristic-card rounded-2xl p-6 flex items-start gap-4 fade-in border-l-4 border-yellow-500" role="alert">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-black text-gray-900 text-lg mb-1">Peringatan! 📢</p>
                            <p class="text-gray-700 font-medium">{{ session('warning') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 transition-colors p-2">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Enhanced Footer -->
    <footer class="footer-wave text-white pt-20 pb-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-12 mb-12">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-20 h-20 rounded-2xl overflow-hidden">
                            <img src="https://ik.imagekit.io/hdxn6kcob/nekodrop.png?updatedAt=1761014269538" alt="NekoDrop Media Downloader Logo" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <span class="logo-text text-3xl font-bold text-white">NekoDrop</span>
                            <p class="text-orange-200 font-bold text-sm">MEDIA DOWNLOADER</p>
                        </div>
                    </div>
                    <p class="text-orange-100 mb-6 max-w-md text-lg leading-relaxed">
                        NekoDrop Media Downloader: Download media favorit Anda dari berbagai platform dengan mudah, cepat, dan gratis! 🐱✨
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors">
                            <i class="fab fa-github text-white text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors">
                            <i class="fab fa-twitter text-white text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors">
                            <i class="fab fa-discord text-white text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors">
                            <i class="fab fa-telegram text-white text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-black text-white mb-6 text-xl">Tautan Cepat</h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="{{ route('downloads.create') }}" class="footer-link text-orange-100 hover:text-white flex items-center gap-3 text-lg font-medium">
                                <i class="fas fa-arrow-right text-orange-400 text-sm"></i>Unduh Media
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('downloads.index') }}" class="footer-link text-orange-100 hover:text-white flex items-center gap-3 text-lg font-medium">
                                <i class="fas fa-arrow-right text-orange-400 text-sm"></i>Riwayat Download
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="font-black text-white mb-6 text-xl">Dukungan</h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="#" class="footer-link text-orange-100 hover:text-white flex items-center gap-3 text-lg font-medium">
                                <i class="fas fa-arrow-right text-orange-400 text-sm"></i>Pusat Bantuan
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link text-orange-100 hover:text-white flex items-center gap-3 text-lg font-medium">
                                <i class="fas fa-arrow-right text-orange-400 text-sm"></i>Hubungi Kami
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link text-orange-100 hover:text-white flex items-center gap-3 text-lg font-medium">
                                <i class="fas fa-arrow-right text-orange-400 text-sm"></i>Lapor Bug
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-orange-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <p class="text-orange-200 text-center md:text-left text-lg">
                        © {{ date('Y') }} <span class="font-black text-white">NekoDrop Media Downloader</span>. Made with <i class="fas fa-heart text-red-400"></i> by NekoDrop Team. All rights reserved. 🐾
                    </p>
                    <div class="flex gap-6 text-lg">
                        <a href="{{ route('privacy') }}" class="footer-link text-orange-200 hover:text-white transition-colors font-medium">Privasi</a>
                        <span class="text-orange-600">•</span>
                        <a href="{{ route('terms') }}" class="footer-link text-orange-200 hover:text-white transition-colors font-medium">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Enhanced Scroll to Top Button -->
    <button id="scroll-to-top" 
            class="fixed bottom-8 right-8 w-14 h-14 futuristic-button rounded-full opacity-0 pointer-events-none transition-all duration-300 z-40"
            aria-label="Scroll to top">
        <i class="fas fa-arrow-up text-lg"></i>
    </button>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Enhanced JavaScript with modern features
        class NekoDropApp {
            constructor() {
                this.init();
            }

            init() {
                this.setupCSRF();
                this.initTheme();
                this.initMobileMenu();
                this.initScrollToTop();
                this.initFlashMessages();
                this.initKeyboardShortcuts();
                this.initPageLoad();
                this.setupEventListeners();
            }

            setupCSRF() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            }

            initTheme() {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.className = savedTheme;
                this.updateThemeIcon(savedTheme);
            }

            toggleTheme() {
                const currentTheme = document.documentElement.className;
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                document.documentElement.className = newTheme;
                localStorage.setItem('theme', newTheme);
                this.updateThemeIcon(newTheme);
            }

            updateThemeIcon(theme) {
                const icons = document.querySelectorAll('#theme-toggle i, #theme-toggle-mobile i');
                icons.forEach(icon => {
                    icon.className = theme === 'dark' ? 'fas fa-sun text-xl' : 'fas fa-moon text-xl';
                });

                const mobileText = document.querySelector('#mobile-theme-toggle span');
                if (mobileText) {
                    mobileText.textContent = theme === 'dark' ? 'LIGHT MODE' : 'DARK MODE';
                }
                
                const mobileIcon = document.querySelector('#mobile-theme-toggle i');
                if (mobileIcon) {
                    mobileIcon.className = theme === 'dark' ? 'fas fa-sun text-white text-lg' : 'fas fa-moon text-white text-lg';
                }
            }

            initMobileMenu() {
                const menuButton = document.getElementById('mobile-menu-button');
                const closeButton = document.getElementById('mobile-menu-close');
                const mobileMenu = document.getElementById('mobile-menu');
                const overlay = document.getElementById('mobile-menu-overlay');

                const toggleMenu = (open) => {
                    if (open) {
                        mobileMenu.classList.add('open');
                        overlay.classList.add('open');
                        document.body.style.overflow = 'hidden';
                    } else {
                        mobileMenu.classList.remove('open');
                        overlay.classList.remove('open');
                        document.body.style.overflow = '';
                    }
                };

                if (menuButton && mobileMenu && overlay) {
                    menuButton.addEventListener('click', () => toggleMenu(true));
                    closeButton?.addEventListener('click', () => toggleMenu(false));
                    overlay.addEventListener('click', () => toggleMenu(false));

                    // Close menu when clicking on links
                    mobileMenu.querySelectorAll('a').forEach(link => {
                        link.addEventListener('click', () => toggleMenu(false));
                    });
                }
            }

            initScrollToTop() {
                const button = document.getElementById('scroll-to-top');
                
                if (button) {
                    window.addEventListener('scroll', () => {
                        const show = window.scrollY > 300;
                        button.classList.toggle('opacity-0', !show);
                        button.classList.toggle('pointer-events-none', !show);
                        button.classList.toggle('opacity-100', show);
                    });

                    button.addEventListener('click', () => {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    });
                }
            }

            initFlashMessages() {
                const alerts = document.querySelectorAll('[role="alert"]');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    }, 5000);
                });
            }

            initKeyboardShortcuts() {
                document.addEventListener('keydown', (e) => {
                    // ESC to close mobile menu
                    if (e.key === 'Escape') {
                        const mobileMenu = document.getElementById('mobile-menu');
                        if (mobileMenu?.classList.contains('open')) {
                            this.closeMobileMenu();
                        }
                    }

                    // Ctrl/Cmd + K to focus search
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        const searchInput = document.querySelector('input[type="search"], input[placeholder*="Cari"]');
                        searchInput?.focus();
                    }
                });
            }

            initPageLoad() {
                document.body.style.opacity = '0';
                requestAnimationFrame(() => {
                    document.body.style.transition = 'opacity 0.3s ease';
                    document.body.style.opacity = '1';
                });
            }

            setupEventListeners() {
                // Theme toggle listeners
                document.getElementById('theme-toggle')?.addEventListener('click', () => this.toggleTheme());
                document.getElementById('theme-toggle-mobile')?.addEventListener('click', () => this.toggleTheme());
                document.getElementById('mobile-theme-toggle')?.addEventListener('click', () => this.toggleTheme());

                // Online/offline status
                window.addEventListener('online', () => {
                    console.log('Koneksi internet tersambung kembali!');
                });

                window.addEventListener('offline', () => {
                    console.log('Koneksi internet terputus!');
                });

                // Prevent form resubmission
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.href);
                }
            }

            showLoading(element) {
                const $element = $(element);
                const originalText = $element.html();
                $element.data('original-text', originalText);
                $element.prop('disabled', true).html('<div class="loading-spinner mx-auto"></div>');
            }

            hideLoading(element) {
                const $element = $(element);
                const text = $element.data('original-text');
                $element.prop('disabled', false).html(text);
            }

            closeMobileMenu() {
                const mobileMenu = document.getElementById('mobile-menu');
                const overlay = document.getElementById('mobile-menu-overlay');
                mobileMenu?.classList.remove('open');
                overlay?.classList.remove('open');
                document.body.style.overflow = '';
            }
        }

        // Initialize the app
        document.addEventListener('DOMContentLoaded', () => {
            window.nekoDropApp = new NekoDropApp();
            
            // Debug mode
            const DEBUG = {{ config('app.debug') ? 'true' : 'false' }};
            if (DEBUG) {
                console.log('🚀 NekoDrop Enhanced App Loaded');
                console.log('📍 Current Route:', '{{ Route::currentRouteName() }}');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
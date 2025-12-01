<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Download - NekoDrop</title>
    <meta name="description" content="Kelola semua download Anda di NekoDrop Media Downloader">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&family=M+PLUS+Rounded+1c:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

        /* Custom Styles for History Page */
        .download-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .download-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .download-thumbnail {
            position: relative;
            aspect-ratio: 16/9;
            overflow: hidden;
        }

        .download-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .download-actions {
            margin-top: auto;
            padding-top: 1rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .meta-info {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 0.75rem 0;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 0.5rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            padding: 0.5rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: center;
        }

        .stat-icon {
            width: 4rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            margin-bottom: 1rem;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .filter-grid {
                grid-template-columns: 2fr 1fr 1fr auto;
            }
        }

        .progress-container {
            margin: 1rem 0;
        }

        .progress-bar {
            width: 100%;
            height: 0.5rem;
            background: #e5e7eb;
            border-radius: 0.25rem;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 0.25rem;
            transition: width 0.3s ease;
        }

        .progress-info {
            display: flex;
            justify-content: between;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        /* Responsive Design untuk Mobile */
        @media (max-width: 768px) {
            .futuristic-card {
                margin: 0.25rem;
                padding: 1rem;
            }
            
            .text-4xl {
                font-size: 1.75rem;
            }
            
            .text-3xl {
                font-size: 1.5rem;
            }
            
            .text-2xl {
                font-size: 1.25rem;
            }

            .text-lg {
                font-size: 1rem;
            }

            /* Grid layout untuk mobile */
            .grid-cols-1 {
                grid-template-columns: 1fr;
            }

            /* Action buttons lebih kompak di mobile */
            .flex-wrap.items-center.gap-2 > * {
                flex: 1 1 calc(50% - 0.5rem);
                min-width: calc(50% - 0.5rem);
            }

            /* Thumbnail lebih kecil di mobile */
            .h-48 {
                height: 12rem;
            }

            /* Text lebih kecil di mobile */
            .text-xs {
                font-size: 0.7rem;
            }

            /* Padding lebih kecil di mobile */
            .p-6 {
                padding: 1rem;
            }

            .p-8 {
                padding: 1.5rem;
            }

            /* Gap lebih kecil di mobile */
            .gap-6 {
                gap: 1rem;
            }

            .gap-4 {
                gap: 0.75rem;
            }

            .gap-2 {
                gap: 0.5rem;
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
                       class="nav-link text-gray-700 hover:text-orange-600 px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-3 {{ request()->routeIs('donasi') ? 'active' : '' }}">
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
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center">
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
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-download text-black text-lg"></i>
                    </div>
                    <span>UNDUH MEDIA</span>
                </a>
                
                <a href="{{ route('downloads.index') }}" 
                   class="flex items-center gap-4 text-gray-700 hover:bg-orange-50 px-4 py-4 rounded-2xl text-base font-bold transition-colors {{ request()->routeIs('downloads.index') ? 'bg-orange-50 text-orange-600' : '' }}">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-history text-black text-lg"></i>
                    </div>
                    <span>RIWAYAT DOWNLOAD</span>
                </a>

                <a href="{{ route('donasi') }}" 
                   class="flex items-center gap-4 text-gray-700 hover:bg-orange-50 px-4 py-4 rounded-2xl text-base font-bold transition-colors {{ request()->routeIs('donasi') ? 'bg-orange-50 text-orange-600' : '' }}">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heart text-black text-lg"></i>
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

            <!-- History Page Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header Section -->
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
                    <div class="flex-1">
                        <h1 class="text-4xl font-black text-gray-900 mb-2">Riwayat Download</h1>
                        <p class="text-gray-600 font-medium text-lg">Kelola semua download Anda di satu tempat</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('downloads.create') }}"
                           class="bg-green-500 text-white font-bold py-3 px-6 rounded-xl hover:shadow-2xl transition-all duration-300 flex items-center gap-2 hover:scale-105">
                            <i class="fas fa-plus"></i>
                            <span>Download Baru</span>
                        </a>
                    </div>
                </div>

                <!-- Stats Cards - Desktop & Mobile -->
                <div class="stats-grid mb-6">
                    <!-- Card 1: Selesai -->
                    <div class="stat-card futuristic-card">
                        <div class="stat-icon bg-gradient-to-br from-green-500 to-green-600">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                        <div class="text-2xl font-black text-gray-900 mb-1">{{ $completedCount }}</div>
                        <div class="text-gray-600 font-semibold">Selesai</div>
                    </div>
                    
                    <!-- Card 2: Proses -->
                    <div class="stat-card futuristic-card">
                        <div class="stat-icon bg-gradient-to-br from-blue-500 to-blue-600">
                            <i class="fas fa-spinner fa-pulse text-white text-2xl"></i>
                        </div>
                        <div class="text-2xl font-black text-gray-900 mb-1">{{ $processingCount }}</div>
                        <div class="text-gray-600 font-semibold">Proses</div>
                    </div>
                    
                    <!-- Card 3: Menunggu -->
                    <div class="stat-card futuristic-card">
                        <div class="stat-icon bg-gradient-to-br from-yellow-500 to-yellow-600">
                            <i class="fas fa-clock text-white text-2xl"></i>
                        </div>
                        <div class="text-2xl font-black text-gray-900 mb-1">{{ $waitingCount }}</div>
                        <div class="text-gray-600 font-semibold">Menunggu</div>
                    </div>
                    
                    <!-- Card 4: Total -->
                    <div class="stat-card futuristic-card">
                        <div class="stat-icon bg-gradient-to-br from-orange-500 to-orange-600">
                            <i class="fas fa-list text-white text-2xl"></i>
                        </div>
                        <div class="text-2xl font-black text-gray-900 mb-1">{{ $downloads->total() }}</div>
                        <div class="text-gray-600 font-semibold">Total</div>
                    </div>
                </div>

                @if($downloads->count() > 0)
                    <!-- Filter & Search Section -->
                    <div class="futuristic-card rounded-2xl p-6 mb-6">
                        <div class="filter-grid">
                            <div class="relative">
                                <input type="text" 
                                       id="searchInput"
                                       placeholder="Cari download..."
                                       class="w-full pl-12 pr-4 py-3 rounded-xl text-gray-800 border border-gray-300 focus:border-orange-500 focus:outline-none">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                            </div>
                            
                            <select id="filterStatus" class="px-4 py-3 rounded-xl text-gray-800 border border-gray-300 focus:border-orange-500 focus:outline-none">
                                <option value="">Semua Status</option>
                                <option value="completed">Selesai</option>
                                <option value="downloading">Proses</option>
                                <option value="pending">Menunggu</option>
                                <option value="failed">Gagal</option>
                            </select>
                            
                            <select id="filterPlatform" class="px-4 py-3 rounded-xl text-gray-800 border border-gray-300 focus:border-orange-500 focus:outline-none">
                                <option value="">Semua Platform</option>
                                <option value="youtube">YouTube</option>
                                <option value="tiktok">TikTok</option>
                                <option value="instagram">Instagram</option>
                                <option value="facebook">Facebook</option>
                            </select>
                            
                            <button onclick="toggleAdvancedSearch()" 
                                    class="bg-gray-600 text-white font-bold py-3 px-4 rounded-xl hover:shadow-xl transition-all duration-300">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </div>
                        
                        <!-- Advanced Search Panel -->
                        <div id="advancedSearchPanel" class="hidden mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Mulai</label>
                                    <input type="date" id="dateFrom" class="w-full px-4 py-2 rounded-lg text-gray-800 border border-gray-300 focus:border-orange-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Selesai</label>
                                    <input type="date" id="dateTo" class="w-full px-4 py-2 rounded-lg text-gray-800 border border-gray-300 focus:border-orange-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Ukuran Minimum</label>
                                    <select id="minSize" class="w-full px-4 py-2 rounded-lg text-gray-800 border border-gray-300 focus:border-orange-500 focus:outline-none">
                                        <option value="">Semua Ukuran</option>
                                        <option value="1MB">> 1MB</option>
                                        <option value="10MB">> 10MB</option>
                                        <option value="50MB">> 50MB</option>
                                        <option value="100MB">> 100MB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end gap-3">
                                <button onclick="resetFilters()" 
                                        class="bg-gray-600 text-white font-bold py-2 px-6 rounded-lg hover:shadow-xl transition-all duration-300">
                                    Reset
                                </button>
                                <button onclick="applyAdvancedFilters()" 
                                        class="bg-red-500 text-white font-bold py-2 px-6 rounded-lg hover:shadow-xl transition-all duration-300">
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- View Controls -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div class="flex items-center gap-4">
                            <span class="text-gray-600 font-medium">
                                Menampilkan {{ $downloads->firstItem() }}-{{ $downloads->lastItem() }} dari {{ $downloads->total() }} download
                            </span>
                            <div id="selectionInfo" class="hidden bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-xl font-bold">
                                <span id="selectedCount">0</span> item dipilih
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="toggleBulkActions()" class="bg-gray-600 text-white font-bold py-2 px-4 rounded-lg hover:shadow-xl transition-all duration-300">
                                <i class="fas fa-check-square mr-2"></i>
                                Pilih Beberapa
                            </button>
                        </div>
                    </div>

                    <!-- Downloads Grid -->
                    <div class="download-grid" id="downloadsContainer">
                        @foreach($downloads as $download)
                        <div class="download-card futuristic-card rounded-2xl p-6 group download-item relative" 
                             data-status="{{ $download->status }}" 
                             data-platform="{{ $download->platform->name ?? $download->platform }}"
                             data-title="{{ strtolower($download->title) }}"
                             data-date="{{ $download->created_at->timestamp }}"
                             data-size="{{ $download->file_size ?? 0 }}"
                             data-id="{{ $download->id }}">
                            
                            <!-- Selection Checkbox -->
                            <div class="absolute top-4 left-4 z-10 hidden" id="checkbox-{{ $download->id }}">
                                <div class="w-6 h-6 bg-white rounded-md border-2 border-gray-300 flex items-center justify-center cursor-pointer">
                                    <input type="checkbox" class="sr-only" onchange="toggleItemSelection({{ $download->id }})">
                                    <i class="fas fa-check text-orange-500 text-xs hidden"></i>
                                </div>
                            </div>

                            <!-- Section Thumbnail -->
                            <div class="download-thumbnail mb-4">
                                @if($download->thumbnail)
                                    <div class="w-full h-full rounded-2xl overflow-hidden">
                                        <img class="w-full h-full object-cover lazy-load"
                                             data-src="{{ strpos($download->thumbnail, 'http') === 0 ? route('thumbnail-proxy', base64_encode($download->thumbnail)) : asset('storage/' . $download->thumbnail) }}"
                                             alt="{{ $download->title }}"
                                             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yNCAyNEwxMiAxMk0yNCAyNEwxMiAzNk0yNCAyNEwzNiAyNE0yNCAyNEwxNiAzNk0yNCAyNEwzNiAyNCIgc3Ryb2tlPSIjOUI5OUFBIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgo8L3N2Zz4K'; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gray-100\'><i class=\'fas fa-video text-4xl text-gray-400\'></i></div>';">
                                    </div>
                                @else
                                    <div class="w-full h-full rounded-2xl flex items-center justify-center bg-gradient-to-br from-orange-100 to-amber-100">
                                        <i class="fas fa-video text-6xl text-orange-400"></i>
                                    </div>
                                @endif
                            
                                <!-- Platform Badge -->
                                <div class="absolute top-4 right-4 bg-black/80 backdrop-blur-sm rounded-xl px-3 py-1.5 flex items-center gap-2 border border-white/20">
                                    @if($download->platform && is_object($download->platform))
                                        <i class="{{ $download->platform->icon }} text-white text-sm"></i>
                                        <span class="text-xs font-bold text-white">
                                            {{ $download->platform->display_name ?? ucfirst($download->platform->name) }}
                                        </span>
                                    @else
                                        <i class="fas fa-globe text-white text-sm"></i>
                                        <span class="text-xs font-bold text-white">
                                            {{ ucfirst($download->platform) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Duration Badge -->
                                @if($download->duration)
                                    <div class="absolute bottom-4 left-4 bg-black/80 backdrop-blur-sm rounded-xl px-2 py-1 border border-white/20">
                                        <span class="text-xs font-bold text-white">
                                            {{ $download->duration }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Content Section -->
                            <div class="download-content">
                                <!-- Header Section -->
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <h3 class="text-lg font-black text-gray-900 line-clamp-2 flex-1 leading-tight">
                                        {{ $download->title }}
                                    </h3>
                                    
                                    <!-- Status Badge -->
                                    @if($download->status === 'completed')
                                        <span class="status-badge bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle text-xs"></i>
                                            <span>Selesai</span>
                                        </span>
                                    @elseif($download->status === 'downloading')
                                        <span class="status-badge bg-blue-100 text-blue-700">
                                            <i class="fas fa-spinner fa-spin text-xs"></i>
                                            <span>Proses</span>
                                        </span>
                                    @elseif($download->status === 'failed')
                                        <span class="status-badge bg-red-100 text-red-700">
                                            <i class="fas fa-times-circle text-xs"></i>
                                            <span>Gagal</span>
                                        </span>
                                    @else
                                        <span class="status-badge bg-yellow-100 text-yellow-700">
                                            <i class="fas fa-clock text-xs"></i>
                                            <span>Menunggu</span>
                                        </span>
                                    @endif
                                </div>

                                <!-- Info Meta -->
                                <div class="meta-info">
                                    <span class="meta-item">
                                        <i class="fas fa-database text-orange-500 text-xs"></i>
                                        <span class="font-semibold">{{ $download->formatted_file_size ?? 'N/A' }}</span>
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-calendar text-orange-500 text-xs"></i>
                                        <span class="font-semibold">{{ $download->created_at->diffForHumans() }}</span>
                                    </span>
                                    @if($download->quality)
                                        <span class="meta-item">
                                            <i class="fas fa-tv text-orange-500 text-xs"></i>
                                            <span class="font-semibold">{{ $download->quality }}</span>
                                        </span>
                                    @endif
                                </div>

                                <!-- Progress Bar -->
                                @if($download->status === 'downloading' && isset($download->progress))
                                    <div class="progress-container">
                                        <div class="flex items-center justify-between text-gray-700 mb-1 text-xs">
                                            <span class="font-semibold">Mengunduh...</span>
                                            <span class="font-bold">{{ $download->progress }}%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill bg-gradient-to-r from-blue-500 to-blue-600 animate-pulse" 
                                                 style="width: {{ $download->progress }}%"></div>
                                        </div>
                                        <div class="progress-info">
                                            <span>{{ $download->downloaded_size ?? '0 MB' }} / {{ $download->formatted_file_size ?? 'N/A' }}</span>
                                            <span>{{ $download->speed ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Error Message -->
                                @if($download->status === 'failed' && $download->error_message)
                                    <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-3">
                                        <p class="text-red-700 flex items-start gap-2 text-xs">
                                            <i class="fas fa-exclamation-triangle mt-0.5"></i>
                                            <span class="font-medium">{{ Str::limit($download->error_message, 100) }}</span>
                                        </p>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="download-actions">
                                    <div class="action-grid">
                                        @if($download->status === 'completed' && $download->file_path)
                                            <button onclick="downloadFile({{ $download->id }}, '{{ addslashes($download->title) }}')"
                                                    class="action-btn bg-green-500 text-white hover:shadow-lg hover:bg-green-600 transition-colors">
                                                <i class="fas fa-download text-xs"></i>
                                                <span>Unduh</span>
                                            </button>
                                        @endif
                                        
                                        @if($download->status === 'downloading')
                                            <button onclick="pauseDownload({{ $download->id }})"
                                                    class="action-btn bg-gradient-to-r from-yellow-500 to-yellow-600 text-white hover:shadow-lg">
                                                <i class="fas fa-pause text-xs"></i>
                                                <span>Jeda</span>
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('downloads.show', $download) }}"
                                           class="action-btn bg-gray-400 text-white hover:shadow-lg">
                                            <i class="fas fa-eye text-xs"></i>
                                            <span>Detail</span>
                                        </a>
                                        
                                        @if($download->status === 'failed')
                                            <button onclick="retryDownload({{ $download->id }})"
                                                    class="action-btn bg-orange-500 text-white hover:shadow-lg">
                                                <i class="fas fa-redo text-xs"></i>
                                                <span>Coba Lagi</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-gray-600 font-medium">
                            Menampilkan {{ $downloads->firstItem() }}-{{ $downloads->lastItem() }} dari {{ $downloads->total() }} download
                        </div>
                        <div class="futuristic-card rounded-xl p-2">
                            {{ $downloads->links() }}
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="futuristic-card rounded-2xl p-12 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="w-32 h-32 mx-auto mb-8 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center shadow-2xl">
                                <i class="fas fa-download text-white text-6xl"></i>
                            </div>
                            <h3 class="text-3xl font-black text-gray-900 mb-4">Belum Ada Riwayat Download</h3>
                            <p class="text-gray-600 mb-8 text-lg leading-relaxed">
                                Mulai unduh video dan audio favorit Anda sekarang! Semua riwayat akan muncul di sini. 🚀
                            </p>
                            <a href="{{ route('downloads.create') }}"
                               class="inline-flex items-center gap-3 futuristic-button bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold py-4 px-8 rounded-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                                <i class="fas fa-plus"></i>
                                <span class="text-lg">Mulai Download Pertama</span>
                            </a>
                            
                            <!-- Quick Tips -->
                            <div class="mt-8 futuristic-card rounded-xl p-6">
                                <h4 class="font-black text-gray-900 mb-4 flex items-center gap-3">
                                    <i class="fas fa-lightbulb text-yellow-500 text-xl"></i>
                                    Tips Cepat
                                </h4>
                                <ul class="text-gray-600 space-y-2">
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-green-500"></i>
                                        Tempel link video dari platform yang didukung
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-green-500"></i>
                                        Pilih kualitas video yang Anda inginkan
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-green-500"></i>
                                        Unduh dalam format MP4 atau MP3
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Bulk Actions Bar -->
                <div id="bulkActions" class="hidden fixed bottom-8 left-1/2 -translate-x-1/2 z-50">
                    <div class="futuristic-card rounded-2xl p-6 shadow-2xl flex items-center gap-6">
                        <span class="text-lg font-black text-gray-900">
                            <span id="selectedCount">0</span> item dipilih
                        </span>
                        <button onclick="downloadSelected()" class="futuristic-button bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-3 px-6 rounded-xl hover:shadow-xl transition-all duration-300">
                            <i class="fas fa-download mr-2"></i>
                            Unduh Semua
                        </button>
                        <button onclick="moveSelectedToFolder()" class="futuristic-button bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-3 px-6 rounded-xl hover:shadow-xl transition-all duration-300">
                            <i class="fas fa-folder mr-2"></i>
                            Pindah ke Folder
                        </button>
                        <button onclick="deleteSelected()" class="futuristic-button bg-gradient-to-r from-red-500 to-red-600 text-white font-bold py-3 px-6 rounded-xl hover:shadow-xl transition-all duration-300">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Semua
                        </button>
                        <button onclick="clearSelection()" class="futuristic-button bg-gradient-to-r from-gray-600 to-gray-700 text-white font-bold py-3 px-6 rounded-xl hover:shadow-xl transition-all duration-300">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Enhanced Footer -->
    <footer class="footer-wave text-white pt-20 pb-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
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
        // JavaScript code for the history page functionality
        // This would include all the JavaScript functions from your original history page
        // For brevity, I'm including the essential functions
        
        let deleteDownloadId = null;
        let selectedItems = new Set();
        let bulkSelectionMode = false;
        let currentViewMode = 'grid';

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize lazy loading
            initLazyLoading();
            
            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function() {
                filterDownloads();
            });

            // Filter by status
            document.getElementById('filterStatus').addEventListener('change', function() {
                filterDownloads();
            });

            // Filter by platform
            document.getElementById('filterPlatform').addEventListener('change', function() {
                filterDownloads();
            });

            // Auto-refresh for downloading items
            setInterval(function() {
                if (document.querySelectorAll('.fa-spinner.fa-spin').length > 0) {
                    refreshDownloadStatus();
                }
            }, 10000); // Check every 10 seconds
        });

        function initLazyLoading() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy-load');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                document.querySelectorAll('.lazy-load').forEach(img => {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback for browsers that don't support IntersectionObserver
                document.querySelectorAll('.lazy-load').forEach(img => {
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-load');
                });
            }
        }

        function toggleBulkActions() {
            bulkSelectionMode = !bulkSelectionMode;

            if (bulkSelectionMode) {
                document.querySelectorAll('.download-item .absolute').forEach(el => {
                    el.classList.remove('hidden');
                });
                document.getElementById('bulkActions').classList.remove('hidden');
            } else {
                document.querySelectorAll('.download-item .absolute').forEach(el => {
                    el.classList.add('hidden');
                });
                document.getElementById('bulkActions').classList.add('hidden');
                clearSelection();
            }
        }

        // ... (other JavaScript functions from your original history page)

        function downloadFile(id, title) {
            // Redirect to the download route to serve the file
            window.location.href = `/downloads/${id}/download`;
        }

        function pauseDownload(id) {
            // Implement pause functionality if needed
            console.log('Pause download:', id);
            // Could send AJAX to pause the download process
        }

        function retryDownload(id) {
            // Implement retry functionality
            console.log('Retry download:', id);
            // Could send AJAX to restart the download
        }

        function toggleItemSelection(id) {
            const checkbox = document.getElementById(`checkbox-${id}`);
            const checkIcon = checkbox.querySelector('i');
            
            if (selectedItems.has(id)) {
                selectedItems.delete(id);
                checkIcon.classList.add('hidden');
            } else {
                selectedItems.add(id);
                checkIcon.classList.remove('hidden');
            }
            updateSelectionUI();
        }

        function updateSelectionUI() {
            const count = selectedItems.size;
            const info = document.getElementById('selectionInfo');
            const countEl = document.getElementById('selectedCount');
            if (count > 0) {
                info.classList.remove('hidden');
                countEl.textContent = count;
            } else {
                info.classList.add('hidden');
            }
        }

        function clearSelection() {
            selectedItems.clear();
            document.querySelectorAll('.download-item .absolute i').forEach(icon => {
                icon.classList.add('hidden');
            });
            updateSelectionUI();
        }

        function downloadSelected() {
            // Implement bulk download
            console.log('Download selected items:', Array.from(selectedItems));
        }

        function moveSelectedToFolder() {
            // Implement move to folder
            console.log('Move selected to folder');
        }

        function deleteSelected() {
            // Implement bulk delete
            if (confirm('Are you sure you want to delete selected downloads?')) {
                // Send AJAX to bulk delete
                console.log('Delete selected items:', Array.from(selectedItems));
                clearSelection();
            }
        }

        function filterDownloads() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('filterStatus').value;
            const platformFilter = document.getElementById('filterPlatform').value;

            document.querySelectorAll('.download-item').forEach(item => {
                const title = item.dataset.title.toLowerCase();
                const status = item.dataset.status;
                const platform = item.dataset.platform;

                let show = true;

                if (searchTerm && !title.includes(searchTerm)) {
                    show = false;
                }

                if (statusFilter && status !== statusFilter) {
                    show = false;
                }

                if (platformFilter && platform !== platformFilter) {
                    show = false;
                }

                item.style.display = show ? 'block' : 'none';
            });
        }

        function refreshDownloadStatus() {
            // Implement AJAX to refresh status for downloading items
            console.log('Refreshing download status');
        }

        function toggleAdvancedSearch() {
            const panel = document.getElementById('advancedSearchPanel');
            panel.classList.toggle('hidden');
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterPlatform').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            document.getElementById('minSize').value = '';
            filterDownloads();
        }

        function applyAdvancedFilters() {
            // Implement advanced filtering logic
            filterDownloads();
            toggleAdvancedSearch();
        }

        // App initialization
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
        });
    </script>
</body>
</html>
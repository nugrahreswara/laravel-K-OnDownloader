<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Download - NekoDrop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Ubah warna latar belakang body agar tidak terlalu terang, tetap hangat */
        body {
            background: linear-gradient(135deg, #f7f9fc 0%, #ffe9dc 100%);
            min-height: 100vh;
        }
        
        /* Glass Card Effect - Ditingkatkan untuk lebih elegan */
        .glass-card {
            background: rgba(255, 255, 255, 0.95); /* Lebih solid */
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 140, 0, 0.15); /* Garis sedikit lebih terlihat */
            border-radius: 24px; /* Sedikit lebih membulat */
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); /* Bayangan lebih halus */
            transition: all 0.3s ease;
        }

        /* Gradient Text (Pertahankan) */
        .gradient-text {
            background: linear-gradient(135deg, #ff8c00 0%, #ff6b35 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Status Badge - Peningkatan Keterbacaan dan kejelasan */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 9999px; /* Full pill shape */
            font-size: 13px;
            font-weight: 700; /* Lebih tebal */
            text-transform: uppercase; /* Lebih tegas */
        }
        
        .status-completed {
            background: #d1fae5; /* Tailwind green-100 */
            color: #059669; /* Tailwind green-700 */
        }
        
        .status-downloading {
            background: #eff6ff; /* Tailwind blue-100 */
            color: #2563eb; /* Tailwind blue-700 */
        }
        
        .status-failed {
            background: #fee2e2; /* Tailwind red-100 */
            color: #dc2626; /* Tailwind red-700 */
        }
        
        .status-pending {
            background: #fffbe6; /* Tailwind yellow-100 */
            color: #b45309; /* Tailwind amber-700 */
        }
        
        /* Futuristic Button - Disederhanakan untuk UX yang lebih baik */
        .futuristic-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 24px; /* Ukuran lebih nyaman diklik */
            border-radius: 16px; /* Lebih membulat */
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
            overflow: hidden;
            border: none; /* Hilangkan border transparan yang rumit */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Bayangan sederhana */
        }
        
        .futuristic-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .futuristic-btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Warna Button dipertahankan */
        .btn-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .btn-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
        .btn-info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }
        
        /* Media Container (Disederhanakan) */
        .media-container {
            width: 100%;
            aspect-ratio: 16/9;
            background: #0d0c1b; /* Background gelap solid */
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* Progress Bar (Animasi Shimmer Dihapus agar tidak berlebihan) */
        .progress-container {
            position: relative;
            width: 100%;
            height: 10px;
            background: #e5e7eb; /* Abu-abu terang */
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 10px;
            transition: width 0.4s ease-out; /* Animasi halus untuk transisi progress */
        }
        
        /* Info Item - Fokus pada kejelasan konten */
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0; /* Padding lebih besar */
            border-bottom: 1px solid #f3f4f6; /* Garis pemisah yang halus */
        }
        
        .info-item:last-child { border-bottom: none; }
        
        .info-label {
            font-size: 14px;
            color: #6b7280; /* Teks abu-abu untuk label */
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-value {
            font-size: 15px;
            color: #1f2937; /* Teks gelap untuk nilai */
            font-weight: 600;
            text-align: right;
        }
        
        /* Back Button - Dibuat lebih kontras dan jelas */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: #f3f4f6; /* Abu-abu muda */
            border: 1px solid #d1d5db;
            border-radius: 12px;
            color: #1f2937; /* Teks gelap */
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .back-btn:hover {
            background: #e5e7eb;
            transform: none; /* Hilangkan translasi yang memecah fokus */
        }
        
        /* Platform Badge - Ditingkatkan */
        .platform-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #ecf0f1; /* Warna netral */
            border: 1px solid #dcdfe3;
            border-radius: 12px;
            color: #34495e;
            font-size: 13px;
            font-weight: 600;
        }
        
        /* Section Title - Diperjelas */
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937; /* Teks gelap */
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .section-title i {
            font-size: 20px;
        }
        
        .section-title::before {
            content: none; /* Hilangkan pseudo-element untuk desain yang lebih bersih */
        }
        
        /* Card Info Item (Kecil) */
        .info-stat-card {
            background: #f9fafb; /* Latar belakang sangat terang */
            border: 1px solid #f3f4f6;
            border-radius: 16px;
            padding: 16px;
            transition: all 0.2s ease;
        }
        .info-stat-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        /* Error Card */
        .error-card {
            background: #fef2f2; /* Latar belakang merah muda lembut */
            border: 1px solid #fecaca;
            border-left: 6px solid #ef4444; /* Garis tebal */
        }
        
        /* Penyesuaian Spinner */
        .spinner {
            animation: spin 1s linear infinite;
        }

        /* Utility classes for clarity */
        .text-orange-main {
            color: #ff8c00;
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 2px;
            background: #e5e7eb;
        }
        
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -34px;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ff8c00;
        }
        
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        
        .timeline-date {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 4px;
        }
        
        .timeline-content {
            font-size: 14px;
            color: #4b5563;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }
        
        .tab.active {
            color: #ff8c00;
            border-bottom-color: #ff8c00;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Detail Grid */
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        
        @media (max-width: 640px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animation Keyframes (Pertahankan, tapi pastikan tidak berlebihan) */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-10">
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('downloads.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Daftar</span>
                </a>
                <div class="platform-badge">
                    <i class="fas fa-{{ $download->platform->icon ?? 'globe' }}"></i>
                    <span>{{ $download->platform->display_name ?? ucfirst($download->platform) }}</span>
                </div>
            </div>
            
            @if($download->status === 'completed')
                <span class="status-badge status-completed">
                    <i class="fas fa-check-circle"></i>
                    <span>Selesai</span>
                </span>
            @elseif($download->status === 'downloading')
                <span class="status-badge status-downloading">
                    <i class="fas fa-spinner spinner"></i>
                    <span>Mengunduh</span>
                </span>
            @elseif($download->status === 'failed')
                <span class="status-badge status-failed">
                    <i class="fas fa-times-circle"></i>
                    <span>Gagal</span>
                </span>
            @else
                <span class="status-badge status-pending">
                    <i class="fas fa-clock"></i>
                    <span>Menunggu</span>
                </span>
            @endif
        </div>

        <div class="mb-10">
            <h1 class="text-3xl sm:text-3xl font-bold text-gray-800 mb-2 leading-tight">
                {{ $download->title }}
            </h1>
            <p class="text-gray-500 text-lg">Detail lengkap dan informasi aktivitas pengunduhan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                
                <div class="glass-card p-6">
                    <h2 class="section-title">
                        <i class="fas fa-film"></i>
                        PREVIEW MEDIA
                    </h2>
                    
                    <div class="media-container">
                        @if($download->status === 'completed' && $download->file_path)
                            <video class="w-full h-full object-contain" 
                                   controls 
                                   poster="{{ $download->thumbnail ? asset('storage/' . $download->thumbnail) : '' }}">
                                <source src="{{ route('downloads.stream', $download) }}" type="video/mp4">
                                Browser Anda tidak mendukung pemutar video.
                            </video>
                        @elseif($download->thumbnail)
                            <img src="{{ asset('storage/' . $download->thumbnail) }}" 
                                 alt="{{ $download->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <div class="text-center text-gray-400">
                                    <i class="fas fa-video text-6xl mb-4 opacity-70"></i>
                                    <p class="text-lg font-medium">Preview Tidak Tersedia</p>
                                    <p class="text-sm mt-1 opacity-80">Video akan ditampilkan setelah download **Selesai**</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="glass-card p-6">
                    <div class="tabs">
                        <div class="tab active" onclick="switchTab('status')">
                            <i class="fas fa-chart-line mr-2"></i>Status Download
                        </div>
                        <div class="tab" onclick="switchTab('info')">
                            <i class="fas fa-info-circle mr-2"></i>Informasi File
                        </div>
                        <div class="tab" onclick="switchTab('details')">
                            <i class="fas fa-list-ul mr-2"></i>Detail Lengkap
                        </div>
                    </div>
                    
                    <!-- Tab Content: Status Download -->
                    <div id="status-tab" class="tab-content active">
                        @if($download->status === 'downloading' && isset($download->progress))
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-2xl font-bold text-gray-800">{{ $download->progress }}%</span>
                                <span class="text-sm font-medium text-gray-500">
                                    {{ $download->downloaded_size ?? '0 MB' }} / {{ $download->formatted_file_size ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-fill" style="width: {{ $download->progress }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center gap-2 text-gray-600">
                                <i class="fas fa-tachometer-alt text-blue-500"></i>
                                <span>**Kecepatan:** {{ $download->speed ?? 'Menghitung...' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <i class="fas fa-clock text-blue-500"></i>
                                <span>**Estimasi Sisa:** {{ $download->eta ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <i class="fas fa-hdd text-blue-500"></i>
                                <span>**Sisa Ruang:** {{ $download->remaining_space ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <i class="fas fa-server text-blue-500"></i>
                                <span>**Server:** {{ $download->server ?? 'Utama' }}</span>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-info-circle text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Status download tidak tersedia atau download telah selesai/gagal.</p>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Tab Content: Informasi File -->
                    <div id="info-tab" class="tab-content">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="info-stat-card">
                                <i class="fas fa-file-video text-purple-500 text-xl mb-2 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Ukuran</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $download->formatted_file_size ?? 'N/A' }}</p>
                            </div>
                            
                            <div class="info-stat-card">
                                <i class="fas fa-clock text-cyan-500 text-xl mb-2 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Durasi</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $download->duration ?? 'N/A' }}</p>
                            </div>
                            
                            <div class="info-stat-card">
                                <i class="fas fa-hd-video text-green-500 text-xl mb-2 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Kualitas</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $download->quality ?? 'Terbaik' }}</p>
                            </div>
                            
                            <div class="info-stat-card">
                                <i class="fas fa-file-code text-red-500 text-xl mb-2 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Format</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ strtoupper($download->format ?? 'MP4') }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="info-stat-card">
                                <i class="fas fa-film text-indigo-500 text-xl mb-2 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Resolusi</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $download->resolution ?? 'N/A' }}</p>
                            </div>
                            
                            <div class="info-stat-card">
                                <i class="fas fa-volume-up text-pink-500 text-xl mb-2 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Audio</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $download->audio_format ?? 'Stereo' }}</p>
                            </div>
                            
                            <div class="info-stat-card">
                                <i class="fas fa-tachometer-alt text-yellow-500 text-xl mb-2 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Bitrate</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $download->bitrate ?? 'N/A' }}</p>
                            </div>
                            
                            <div class="info-stat-card">
                                <i class="fas fa-closed-captioning text-blue-500 text-xl mb-2 block"></i>
                                <p class="text-xs text-gray-500 font-medium">Subtitle</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $download->subtitle ?? 'Tidak Ada' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Content: Detail Lengkap -->
                    <div id="details-tab" class="tab-content">
                        <div class="detail-grid">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-link text-orange-400"></i>
                                    URL Sumber
                                </span>
                                <span class="info-value text-xs truncate max-w-[200px]">{{ $download->source_url ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-user text-blue-400"></i>
                                    Pengunggah
                                </span>
                                <span class="info-value">{{ $download->uploader ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-eye text-green-400"></i>
                                    Jumlah View
                                </span>
                                <span class="info-value">{{ $download->view_count ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-thumbs-up text-purple-400"></i>
                                    Jumlah Like
                                </span>
                                <span class="info-value">{{ $download->like_count ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-calendar text-red-400"></i>
                                    Tanggal Upload
                                </span>
                                <span class="info-value">{{ $download->upload_date ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-tag text-yellow-400"></i>
                                    Kategori
                                </span>
                                <span class="info-value">{{ $download->category ?? 'N/A' }}</span>
                            </div>
                        </div>
                        
                        @if($download->description)
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h3>
                            <p class="text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">{{ $download->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Riwayat Aktivitas -->
                <div class="glass-card p-6">
                    <h2 class="section-title">
                        <i class="fas fa-history text-blue-500"></i>
                        RIWAYAT AKTIVITAS
                    </h2>
                    
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-date">{{ $download->created_at->format('d M Y, H:i') }}</div>
                            <div class="timeline-content">Download dimulai</div>
                        </div>
                        
                        @if($download->status !== 'pending')
                        <div class="timeline-item">
                            <div class="timeline-date">{{ $download->updated_at->format('d M Y, H:i') }}</div>
                            <div class="timeline-content">
                                @if($download->status === 'downloading')
                                    Sedang mengunduh ({{ $download->progress ?? 0 }}%)
                                @elseif($download->status === 'completed')
                                    Download selesai
                                @elseif($download->status === 'failed')
                                    Download gagal: {{ $download->error_message ?? 'Kesalahan tidak diketahui' }}
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                
                <div class="glass-card p-10">
                    <h2 class="section-title text-yellow-main">
                        <i class="fas fa-bolt text-yellow-500"></i>
                        AKSI CEPAT
                    </h2>
                    
                    <div class="action-grid grid-cols-1">
                        @if($download->status === 'completed' && $download->file_path)
                            <button onclick="window.location.href='{{ route('downloads.download', $download) }}'"
                                    class="futuristic-btn btn-success mb-5">
                                <i class="fas fa-download"></i>
                                <span>Unduh File</span>
                            </button>
                        @endif
                        
                        @if($download->status === 'downloading')
                            <button onclick="pauseDownload({{ $download->id }})"
                                    class="futuristic-btn btn-warning mb-5">
                                <i class="fas fa-pause"></i>
                                <span>**Jeda Download**</span>
                            </button>
                        @endif
                        
                        @if($download->status === 'failed')
                            <button onclick="retryDownload({{ $download->id }})"
                                    class="futuristic-btn btn-info">
                                <i class="fas fa-redo"></i>
                                <span>**Coba Lagi**</span>
                            </button>
                        @endif

                        <button onclick="shareDownload({{ $download->id }})"
                                class="futuristic-btn btn-primary bg-gray-200 mb-5 text-gray-800 border-2 border-gray-300 hover:bg-gray-300">
                            <i class="fas fa-share-alt text-white"></i>
                            <span class="text-white">Bagikan Link</span>
                        </button>

                        <button onclick="deleteDownload({{ $download->id }})"
                                class="futuristic-btn btn-danger bg-red-100 text-red-700 hover:bg-red-200">
                            <i class="fas fa-trash"></i>
                            <span>Hapus Download</span>
                        </button>
                    </div>
                </div>

                @if($download->status === 'failed' && $download->error_message)
                <div class="glass-card error-card p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-red-700 mb-2 text-xl">Download Gagal</h3>
                            <p class="text-red-600 text-sm mb-4 leading-relaxed font-mono bg-red-50 p-2 rounded-lg border border-red-200">
                                {{ $download->error_message }}
                            </p>
                            <button onclick="retryDownload({{ $download->id }})"
                                    class="futuristic-btn btn-info text-sm py-2 px-4 mt-2">
                                <i class="fas fa-redo"></i>
                                <span>Coba Lagi</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="glass-card p-6">
                    <h2 class="section-title text-gray-700">
                        <i class="fas fa-clipboard-list"></i>
                        DETAIL AKTIVITAS
                    </h2>
                    
                    <div class="space-y-0">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-globe-asia text-orange-400"></i>
                                Sumber Platform
                            </span>
                            <span class="info-value">{{ $download->platform->display_name ?? ucfirst($download->platform) }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-calendar-alt text-green-400"></i>
                                Tanggal Unduh
                            </span>
                            <span class="info-value">{{ $download->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-sync-alt text-blue-400"></i>
                                Pembaruan Terakhir
                            </span>
                            <span class="info-value">{{ $download->updated_at->diffForHumans() }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-fingerprint text-red-400"></i>
                                ID Download
                            </span>
                            <span class="info-value font-mono text-xs">{{ substr($download->id, 0, 8) }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Tambahan -->
                <div class="glass-card p-6">
                    <h2 class="section-title text-gray-700">
                        <i class="fas fa-info-circle"></i>
                        INFORMASI SISTEM
                    </h2>
                    
                    <div class="space-y-0">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-desktop text-purple-400"></i>
                                Perangkat
                            </span>
                            <span class="info-value">{{ $download->device ?? 'Desktop' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-wifi text-blue-400"></i>
                                Koneksi
                            </span>
                            <span class="info-value">{{ $download->connection_type ?? 'WiFi' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-map-marker-alt text-red-400"></i>
                                Lokasi Server
                            </span>
                            <span class="info-value">{{ $download->server_location ?? 'Indonesia' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-code-branch text-green-400"></i>
                                Versi Aplikasi
                            </span>
                            <span class="info-value">{{ $download->app_version ?? 'v2.1.0' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-overlay fixed inset-0 bg-black/70" onclick="closeDeleteModal()"></div>
                
            <div class="modal-content glass-card p-8 max-w-md w-full relative z-10">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-red-500 to-pink-500 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Hapus Download?</h3>
                    <p class="text-gray-500">
                        File yang sudah diunduh akan dihapus permanen dan tidak dapat dikembalikan.
                    </p>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="closeDeleteModal()"
                            class="futuristic-btn bg-gray-300 text-gray-700 hover:bg-gray-400">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </button>
                    <button onclick="confirmDelete()"
                            class="futuristic-btn btn-danger">
                        <i class="fas fa-trash"></i>
                        <span>Ya, Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3 max-w-sm"></div>

    <script>
    let deleteDownloadId = null;
    let statusCheckInterval = null;

    // Start polling for status updates when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const downloadStatus = '{{ $download->status }}';
        if (downloadStatus === 'downloading') {
            startStatusPolling();
        }
    });

    function startStatusPolling() {
        // Poll every 2 seconds for status updates
        statusCheckInterval = setInterval(checkDownloadStatus, 2000);
    }

    function stopStatusPolling() {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            statusCheckInterval = null;
        }
    }

    function checkDownloadStatus() {
        const downloadId = {{ $download->id }};
        const statusUrl = `/downloads/${downloadId}/status`;

        fetch(statusUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDownloadStatus(data.download);

                // Stop polling if download is completed or failed
                if (data.download.status === 'completed' || data.download.status === 'failed') {
                    stopStatusPolling();
                }
            }
        })
        .catch(error => {
            console.error('Status check error:', error);
            // Stop polling on persistent errors
            stopStatusPolling();
        });
    }

    function updateDownloadStatus(downloadData) {
        // Update status badge
        const statusBadge = document.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = 'status-badge';

            switch (downloadData.status) {
                case 'completed':
                    statusBadge.classList.add('status-completed');
                    statusBadge.innerHTML = '<i class="fas fa-check-circle"></i><span>Selesai</span>';
                    break;
                case 'downloading':
                    statusBadge.classList.add('status-downloading');
                    statusBadge.innerHTML = '<i class="fas fa-spinner spinner"></i><span>Mengunduh</span>';
                    break;
                case 'failed':
                    statusBadge.classList.add('status-failed');
                    statusBadge.innerHTML = '<i class="fas fa-times-circle"></i><span>Gagal</span>';
                    break;
                case 'pending':
                    statusBadge.classList.add('status-pending');
                    statusBadge.innerHTML = '<i class="fas fa-clock"></i><span>Menunggu</span>';
                    break;
            }
        }

        // Update progress if downloading
        if (downloadData.status === 'downloading' && downloadData.progress !== undefined) {
            const progressFill = document.querySelector('.progress-fill');
            const progressText = document.querySelector('.progress-container').previousElementSibling.querySelector('span:first-child');
            const sizeText = document.querySelector('.progress-container').previousElementSibling.querySelector('span:last-child');

            if (progressFill) {
                progressFill.style.width = downloadData.progress + '%';
            }
            if (progressText) {
                progressText.textContent = downloadData.progress + '%';
            }
            if (sizeText && downloadData.downloaded_size) {
                sizeText.textContent = downloadData.downloaded_size + ' / ' + (downloadData.formatted_file_size || 'N/A');
            }

            // Update speed and ETA if available
            const speedElement = document.querySelector('.text-sm .fa-tachometer-alt').nextElementSibling;
            const etaElement = document.querySelector('.text-sm .fa-clock').nextElementSibling;

            if (speedElement && downloadData.speed) {
                speedElement.innerHTML = '<strong>Kecepatan:</strong> ' + downloadData.speed;
            }
            if (etaElement && downloadData.eta) {
                etaElement.innerHTML = '<strong>Estimasi Sisa:</strong> ' + downloadData.eta;
            }
        }

        // Show download button if completed
        if (downloadData.status === 'completed') {
            const downloadBtn = document.querySelector('.btn-success');
            if (downloadBtn && downloadBtn.style.display === 'none') {
                downloadBtn.style.display = 'flex';
            }

            // Reload page to show video player
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // Show error message if failed
        if (downloadData.status === 'failed' && downloadData.error_message) {
            const errorCard = document.querySelector('.error-card');
            if (errorCard) {
                errorCard.style.display = 'block';
                const errorText = errorCard.querySelector('.font-mono');
                if (errorText) {
                    errorText.textContent = downloadData.error_message;
                }
            }
        }
    }

    function deleteDownload(id) {
        deleteDownloadId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteDownloadId = null;
    }

    function confirmDelete() {
        if (!deleteDownloadId) return;

        showToast('Menghapus download...', 'info', 'fa-spinner fa-spin');

        const deleteUrl = `/downloads/${deleteDownloadId}`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Download berhasil dihapus!', 'success', 'fa-check-circle');
                setTimeout(() => {
                    window.location.href = "{{ route('downloads.index') }}";
                }, 1500);
            } else {
                showToast('Gagal menghapus download', 'error', 'fa-times-circle');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showToast('Terjadi kesalahan', 'error', 'fa-times-circle');
        })
        .finally(() => {
            closeDeleteModal();
        });
    }

    function pauseDownload(id) {
        console.log('Pausing download:', id);
        showToast('Download dijeda', 'warning', 'fa-pause-circle');
        // TODO: Implement pause functionality
    }

    function retryDownload(id) {
        console.log('Retrying download:', id);
        showToast('Memulai ulang download...', 'info', 'fa-redo');
        // TODO: Implement retry functionality
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    }

    function shareDownload(id) {
        const shareData = {
            title: 'NekoDrop Download',
            text: 'Lihat download saya di NekoDrop',
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(shareData)
                .then(() => showToast('Berhasil dibagikan!', 'success', 'fa-check-circle'))
                .catch((error) => {
                    if (error.name !== 'AbortError') {
                        copyToClipboard();
                    }
                });
        } else {
            copyToClipboard();
        }
    }

    function copyToClipboard() {
        navigator.clipboard.writeText(window.location.href)
            .then(() => {
                showToast('Link berhasil disalin!', 'success', 'fa-copy');
            })
            .catch(() => {
                showToast('Gagal menyalin link', 'error', 'fa-times-circle');
            });
    }

    function showToast(message, type = 'info', icon = 'fa-info-circle') {
        const toastContainer = document.getElementById('toastContainer');

        const colors = {
            success: { bg: 'from-green-500 to-emerald-500', border: 'border-green-400' },
            error: { bg: 'from-red-500 to-pink-500', border: 'border-red-400' },
            warning: { bg: 'from-yellow-500 to-orange-500', border: 'border-yellow-400' },
            info: { bg: 'from-blue-500 to-cyan-500', border: 'border-blue-400' }
        };

        const color = colors[type] || colors.info;

        const toast = document.createElement('div');
        toast.className = 'glass-card p-4 border-l-4 ' + color.border;
        toast.style.animation = 'slideInRight 0.3s ease-out';

        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br ${color.bg} flex items-center justify-center flex-shrink-0">
                    <i class="fas ${icon} text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-800">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()"
                        class="text-gray-400 hover:text-gray-800 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        toastContainer.appendChild(toast);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Tab switching functionality
    function switchTab(tabName) {
        // Remove active class from all tabs and tab contents
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Add active class to selected tab and content
        event.target.closest('.tab').classList.add('active');
        document.getElementById(tabName + '-tab').classList.add('active');
    }

    // Keyframes for Toast (Add to style block in head if needed, or keep here for simplicity)
    const toastStyle = document.createElement('style');
    toastStyle.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(toastStyle);
    </script>
</body>
</html>
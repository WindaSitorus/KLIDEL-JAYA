<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Manajemen Dokter & Akun Login | Klinik Maristella</title>
  <link rel="icon" type="image/png" href="{{ asset('images/maristella-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @else
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
  @endif
  <style>
    :root {
      --bg-page: #ffffff;
      --bg-muted: #f8fafc;
      --bg-subtle: #f1f5f9;
      --border: #e2e8f0;
      --border-light: #f1f5f9;
      --text-primary: #0f172a;
      --text-secondary: #475569;
      --text-tertiary: #94a3b8;
      --icon-default: #64748b;
      --brand-primary: #012D68;
      --brand-primary-hover: #083E88;
      --brand-primary-tint: #EFF6FF;
      --brand-dark: #001F4D;
      --font-sans: "Plus Jakarta Sans", "Inter", system-ui, -apple-system, sans-serif;
      --radius-sm: 6px;
      --radius-md: 8px;
      --radius-lg: 14px;
      --shadow-xs: 0 1px 2px rgba(15, 23, 42, 0.05);
      --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.08);
      --shadow-lg: 0 12px 28px rgba(15, 23, 42, 0.12);
      --sidebar-w: 240px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: var(--font-sans);
      background-color: var(--bg-muted);
      color: var(--text-primary);
      min-height: 100vh;
      display: flex;
    }

    .app-layout { display: flex; width: 100%; min-height: 100vh; }

    /* Sidebar */
    .sidebar {
      width: var(--sidebar-w);
      background-color: #ffffff;
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 40;
      padding: 24px 16px;
    }

    .sidebar .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 0 8px 24px 8px;
      border-bottom: 1px solid var(--border-light);
      margin-bottom: 16px;
    }

    .sidebar .brand img { height: 32px; width: auto; object-fit: contain; }

    .role-indicator-badge {
      display: inline-flex;
      align-items: center;
      padding: 2px 8px;
      border-radius: 9999px;
      font-size: 11px;
      font-weight: 700;
      background-color: #EFF6FF;
      color: #012D68;
      border: 1px solid #BFDBFE;
    }

    .sidebar-nav { display: flex; flex-direction: column; gap: 4px; width: 100%; }

    .sidebar-nav .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 12px;
      border-radius: 12px;
      color: var(--text-secondary);
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      position: relative;
      transition: all 0.15s ease;
    }

    .sidebar-nav .nav-item svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      fill: none;
      stroke-width: 1.8;
      flex-shrink: 0;
      color: var(--icon-default);
    }

    .sidebar-nav .nav-item:hover {
      background-color: var(--brand-primary-tint);
      color: var(--brand-primary);
    }

    .sidebar-nav .nav-item:hover svg { color: var(--brand-primary); }

    .sidebar-nav .nav-item.active {
      background-color: var(--brand-primary-tint);
      color: var(--brand-primary);
      font-weight: 700;
    }

    .sidebar-nav .nav-item.active svg { color: var(--brand-primary); }

    .sidebar-nav .nav-item .active-indicator {
      display: block;
      position: absolute;
      left: -16px;
      top: 8px;
      bottom: 8px;
      width: 4px;
      border-radius: 0 4px 4px 0;
      background-color: var(--brand-primary);
    }

    .sidebar .spacer { flex: 1; }

    .user-card {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px;
      border-radius: 12px;
      background-color: var(--bg-muted);
      border: 1px solid var(--border);
      margin-bottom: 12px;
    }

    .user-card .avatar {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background-color: var(--brand-primary);
      color: white;
      font-weight: 700;
      font-size: 13px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .user-card .avatar .status {
      width: 8px;
      height: 8px;
      background-color: #10B981;
      border: 1.5px solid white;
      border-radius: 50%;
      position: absolute;
      bottom: -1px;
      right: -1px;
    }

    .user-card strong { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .user-card small { display: block; font-size: 11px; color: var(--text-secondary); }

    .logout-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      width: 100%;
      padding: 8px 12px;
      border-radius: 8px;
      color: #DC2626;
      font-size: 13px;
      font-weight: 500;
      background: transparent;
      border: 1px solid #FEE2E2;
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .logout-btn:hover { background: #FEF2F2; }

    /* Main Container */
    .main-wrap {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
      background-color: #FAFAFB;
    }

    @media (max-width: 768px) {
      .sidebar { display: none; }
      .main-wrap { margin-left: 0; }
    }
  </style>
</head>
<body class="antialiased">

  <!-- SVG Sprite Definitions -->
  <svg width="0" height="0" class="hidden" style="position: absolute; width: 0; height: 0; overflow: hidden;" aria-hidden="true">
    <defs>
      <symbol id="i-cal" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></symbol>
      <symbol id="i-bill" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/><path d="M7 15h2M12 15h5"/></symbol>
      <symbol id="i-doctor" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></symbol>
      <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></symbol>
      <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></symbol>
      <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
      <symbol id="i-check" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></symbol>
      <symbol id="i-edit" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></symbol>
      <symbol id="i-trash" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></symbol>
      <symbol id="i-lock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
      <symbol id="i-eye" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></symbol>
    </defs>
  </svg>

  <div class="app-layout">
    
    <!-- SIDEBAR ADMIN -->
    <aside class="sidebar">
      <div class="brand">
        <a href="{{ route('home') }}" title="Klinik Maristella">
          <img src="{{ asset('images/maristella-logo-horizontal.png') }}" alt="Klinik Maristella" />
        </a>
        <span class="role-indicator-badge">Admin</span>
      </div>

      <nav class="sidebar-nav" aria-label="Navigasi admin">
        <a href="{{ route('admin.calendar') }}" class="nav-item" title="Jadwal Dokter">
          <svg><use href="#i-cal"/></svg>
          <span>Jadwal Dokter</span>
        </a>

        <a href="{{ route('admin.dashboard') }}" class="nav-item" title="Riwayat Pembayaran">
          <svg><use href="#i-bill"/></svg>
          <span>Riwayat Pembayaran</span>
        </a>

        <!-- Menu Manajemen Dokter (Active) -->
        <a href="{{ route('admin.doctors.index') }}" class="nav-item active" aria-current="page" title="Manajemen Dokter">
          <span class="active-indicator"></span>
          <svg><use href="#i-doctor"/></svg>
          <span>Manajemen Dokter</span>
        </a>
      </nav>

      <div class="spacer"></div>

      <a href="{{ route('profile.edit') }}" class="user-card" title="Kelola Profil Saya" style="text-decoration:none;">
        <div class="avatar" style="overflow:hidden; display:flex; align-items:center; justify-content:center;">
          @if(!empty($user['avatar_url']))
            <img src="{{ $user['avatar_url'] }}" alt="{{ $user['name'] }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            {{ $user['initials'] ?? 'AL' }}
          @endif
          <i class="status"></i>
        </div>
        <div class="min-w-0 flex-1">
          <strong>{{ $user['name'] ?? 'Amélie Laurent' }}</strong>
          <small>{{ $user['role'] ?? 'Administrator' }} · Profil</small>
        </div>
      </a>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn" title="Keluar dari Portal Admin">
          <svg style="width:14px;height:14px"><use href="#i-logout"/></svg>
          <span>Keluar Portal</span>
        </button>
      </form>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="main-wrap">
      
      <!-- Top Alert Notification (Success / Error) -->
      @if (session('success'))
        <div class="m-6 mb-0 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-3 text-emerald-800 text-sm font-medium shadow-xs">
          <div class="flex items-center gap-2.5">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            <span>{{ session('success') }}</span>
          </div>
          <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
      @endif

      @if ($errors->any())
        <div class="m-6 mb-0 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-sm font-medium shadow-xs">
          <div class="font-bold mb-1 flex items-center gap-1.5 text-rose-900">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Terdapat beberapa kesalahan input:</span>
          </div>
          <ul class="list-disc list-inside text-xs space-y-0.5">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Topbar Header -->
      <header class="p-6 pb-2 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Dokter & Akun Login</h1>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-[#012D68] border border-blue-200">
              {{ $stats['total_doctors'] }} Dokter
            </span>
          </div>
          <p class="text-sm text-slate-500 mt-1">
            Daftarkan dokter baru, kelola spesialisasi, tarif konsultasi, dan buat akun login dokter secara langsung.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ route('home') }}" target="_blank" class="h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs inline-flex items-center gap-1.5 transition-all shadow-xs">
            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            <span>Lihat Tampilan Pasien</span>
          </a>
          <button type="button" onclick="openAddDoctorModal()" class="h-10 px-5 rounded-xl bg-[#012D68] hover:bg-[#023E8A] text-white font-bold text-xs inline-flex items-center gap-2 transition-all shadow-sm active:scale-[0.98]">
            <svg class="w-4 h-4"><use href="#i-plus"/></svg>
            <span>Daftar Dokter Baru</span>
          </button>
        </div>
      </header>

      <!-- KPI Stat Tiles -->
      <section class="p-6 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
          <div>
            <small class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Dokter</small>
            <b class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $stats['total_doctors'] }}</b>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Tampil di portal pasien</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#012D68] flex items-center justify-center">
            <svg class="w-6 h-6"><use href="#i-doctor"/></svg>
          </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
          <div>
            <small class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Akun Portal Aktif</small>
            <b class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ $stats['doctors_with_account'] }}</b>
            <span class="text-[11px] text-emerald-600 mt-0.5 block">Siap login ke /login/dokter</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
          <div>
            <small class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Poliklinik Aktif</small>
            <b class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $stats['total_polis'] }}</b>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Departemen spesialisasi</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
          <div>
            <small class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Rata-rata Tarif Sesi</small>
            <b class="text-xl font-extrabold text-slate-900 mt-1 block">Rp {{ number_format($stats['avg_fee'], 0, ',', '.') }}</b>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Sesi konsultasi dasar</span>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
            <svg class="w-6 h-6"><use href="#i-bill"/></svg>
          </div>
        </div>
      </section>

      <!-- Search & Filter Controls -->
      <section class="px-6 py-2">
        <form method="GET" action="{{ route('admin.doctors.index') }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
          <div class="relative w-full md:w-96">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama dokter, spesialisasi, atau email..." class="w-full h-10 pl-10 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-xs focus:bg-white focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"><use href="#i-search"/></svg>
          </div>

          <div class="flex items-center gap-3 w-full md:w-auto">
            <select name="poli_id" onchange="this.form.submit()" class="h-10 px-3 rounded-xl border border-slate-200 bg-slate-50 text-xs text-slate-700 font-medium outline-none focus:border-[#012D68] focus:bg-white transition-all">
              <option value="">Semua Poliklinik ({{ $stats['total_polis'] }})</option>
              @foreach ($polis as $poli)
                <option value="{{ $poli->id }}" {{ (string)$selectedPoliId === (string)$poli->id ? 'selected' : '' }}>
                  {{ $poli->nama_poli }} ({{ $poli->name_en ?? $poli->slug }})
                </option>
              @endforeach
            </select>

            <button type="submit" class="h-10 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs inline-flex items-center gap-1.5 transition-all">
              <span>Filter</span>
            </button>

            @if ($search !== '' || !empty($selectedPoliId))
              <a href="{{ route('admin.doctors.index') }}" class="h-10 px-3 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold inline-flex items-center">
                Reset
              </a>
            @endif
          </div>
        </form>
      </section>

      <!-- Doctors Table Section -->
      <section class="p-6 pt-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
              <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[11px] tracking-wider">
                <tr>
                  <th class="py-3.5 px-4">Dokter</th>
                  <th class="py-3.5 px-4">Poliklinik & Spesialisasi</th>
                  <th class="py-3.5 px-4">Akun Login Portal</th>
                  <th class="py-3.5 px-4">Tarif Sesi</th>
                  <th class="py-3.5 px-4">Jam Praktik & Pengalaman</th>
                  <th class="py-3.5 px-4 text-center">Pasien</th>
                  <th class="py-3.5 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @forelse ($doctors as $doc)
                  <tr class="hover:bg-slate-50/80 transition-colors">
                    
                    <!-- Doctor Info & Photo -->
                    <td class="py-3.5 px-4">
                      <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 overflow-hidden flex items-end justify-center flex-shrink-0">
                          <img src="{{ asset($doc->image ?: 'images/doctors/doctor-1.png') }}" alt="{{ $doc->nama_dokter }}" class="w-full h-full object-contain object-bottom" onerror="this.src='{{ asset('images/doctors/doctor-1.png') }}'">
                        </div>
                        <div class="min-w-0">
                          <b class="text-sm font-bold text-slate-900 block truncate">{{ $doc->nama_dokter }}</b>
                          <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="inline-flex items-center gap-0.5 text-amber-600 font-bold text-[11px]">
                              ★ {{ $doc->rating ?? '4.8' }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-400 font-mono text-[10.5px]">ID #DOC-{{ str_pad($doc->id, 3, '0', STR_PAD_LEFT) }}</span>
                          </div>
                        </div>
                      </div>
                    </td>

                    <!-- Poli & Specialty -->
                    <td class="py-3.5 px-4">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-blue-50 text-[#012D68] border border-blue-100 mb-1">
                        {{ $doc->poli?->nama_poli ?? 'Umum' }}
                      </span>
                      <span class="block font-medium text-slate-700 truncate max-w-xs">{{ $doc->spesialisasi }}</span>
                    </td>

                    <!-- Doctor User Login Account -->
                    <td class="py-3.5 px-4">
                      @if ($doc->user)
                        <div class="space-y-0.5">
                          <span class="font-bold text-slate-800 block truncate max-w-xs">{{ $doc->user->email }}</span>
                          <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Role: Dokter (Aktif)
                          </span>
                        </div>
                      @else
                        <span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200">
                          Belum ada akun login
                        </span>
                      @endif
                    </td>

                    <!-- Tariff -->
                    <td class="py-3.5 px-4 font-bold text-slate-900">
                      {{ $doc->price ?: 'Rp ' . number_format($doc->price_num, 0, ',', '.') }}
                      <small class="block font-normal text-slate-400 text-[10px]">/per konsultasi</small>
                    </td>

                    <!-- Availability & Experience -->
                    <td class="py-3.5 px-4 text-slate-600">
                      <div class="space-y-0.5">
                        <span class="block font-semibold text-slate-700">{{ $doc->availability }}</span>
                        <span class="block text-[11px] text-slate-400">Pengalaman: {{ $doc->experience }}</span>
                      </div>
                    </td>

                    <!-- Bookings Count -->
                    <td class="py-3.5 px-4 text-center">
                      <span class="inline-flex items-center justify-center min-w-[28px] h-6 px-2 rounded-full font-bold bg-slate-100 text-slate-700 text-xs">
                        {{ $doc->bookings_count ?? 0 }}
                      </span>
                    </td>

                    <!-- Actions -->
                    <td class="py-3.5 px-4 text-right">
                      <div class="inline-flex items-center gap-1.5">
                        <button type="button" onclick="openEditDoctorModal({{ json_encode($doc) }})" class="w-8 h-8 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 text-slate-600 hover:text-[#012D68] flex items-center justify-center transition-all shadow-xs" title="Edit Data & Akun Dokter">
                          <svg class="w-3.5 h-3.5"><use href="#i-edit"/></svg>
                        </button>
                        <button type="button" onclick="confirmDeleteDoctor({{ $doc->id }}, '{{ addslashes($doc->nama_dokter) }}')" class="w-8 h-8 rounded-lg border border-slate-200 hover:border-rose-300 hover:bg-rose-50 text-slate-600 hover:text-rose-600 flex items-center justify-center transition-all shadow-xs" title="Hapus Dokter">
                          <svg class="w-3.5 h-3.5"><use href="#i-trash"/></svg>
                        </button>
                      </div>
                    </td>

                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="py-12 text-center text-slate-400">
                      <div class="max-w-xs mx-auto">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="18" y1="8" x2="23" y2="13"/><line x1="23" y1="8" x2="18" y2="13"/></svg>
                        <p class="font-bold text-slate-700">Tidak ada dokter ditemukan</p>
                        <p class="text-xs mt-1">Coba sesuaikan kata kunci pencarian atau daftarkan dokter baru.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- MODAL: DAFTAR DOKTER BARU + AKUN LOGIN DOKTER                              -->
  <!-- ========================================================================= -->
  <div id="addDoctorModalBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden opacity-0 transition-opacity duration-200 flex items-center justify-center p-4">
    <div id="addDoctorModal" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-100 flex flex-col max-h-[92vh] overflow-hidden transform scale-95 transition-all duration-200" role="dialog" aria-modal="true">
      
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 sticky top-0 z-10">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#012D68] flex items-center justify-center">
            <svg class="w-5 h-5"><use href="#i-doctor"/></svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Pendaftaran Dokter & Akun Baru</h3>
            <p class="text-xs text-slate-500">Profil dokter akan langsung tampil di beranda pasien dan dokter bisa langsung login.</p>
          </div>
        </div>
        <button type="button" onclick="closeAddDoctorModal()" class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center transition-colors">✕</button>
      </div>

      <form action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-6 space-y-5">
        @csrf

        <!-- SECTION 1: PROFIL DOKTER -->
        <div>
          <div class="flex items-center gap-2 pb-2 mb-3 border-b border-slate-100">
            <span class="w-5 h-5 rounded-full bg-[#012D68] text-white text-xs font-bold flex items-center justify-center">1</span>
            <h4 class="font-bold text-slate-900 text-sm">Informasi Profil Dokter</h4>
          </div>

          <div class="space-y-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Nama Lengkap Dokter & Gelar <span class="text-rose-500">*</span>
              </label>
              <input type="text" name="nama_dokter" required placeholder="Contoh: Dr. Budi Santoso, Sp.A" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Poliklinik / Departemen <span class="text-rose-500">*</span>
                </label>
                <select name="poli_id" id="add-poli-id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                  <option value="">Pilih Poliklinik...</option>
                  @foreach ($polis as $poli)
                    <option value="{{ $poli->id }}" data-name="{{ $poli->name_en ?? $poli->nama_poli }}">
                      {{ $poli->nama_poli }} ({{ $poli->name_en ?? $poli->slug }})
                    </option>
                  @endforeach
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Spesialisasi Klinis
                </label>
                <input type="text" name="spesialisasi" placeholder="Contoh: Dokter Spesialis Anak" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Tarif Sesi (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="price_num" value="180000" min="0" step="5000" required placeholder="180000" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Jam Praktik / Ketersediaan
                </label>
                <input type="text" name="availability" value="09:00 - 17:00 WIB" placeholder="09:00 - 17:00 WIB" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Pengalaman Praktik
                </label>
                <input type="text" name="experience" value="5 tahun" placeholder="5 tahun" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>
            </div>

            <!-- Avatar & Preset Selection -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilihan Foto Profil Dokter</label>
              <div class="grid grid-cols-3 gap-3 mb-2">
                <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                  <input type="radio" name="image_preset" value="images/doctors/doctor-1.png" checked class="text-[#012D68] focus:ring-0">
                  <img src="{{ asset('images/doctors/doctor-1.png') }}" class="w-8 h-8 rounded-lg object-contain bg-blue-50">
                  <span class="text-xs font-medium">Avatar 1</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                  <input type="radio" name="image_preset" value="images/doctors/doctor-2.png" class="text-[#012D68] focus:ring-0">
                  <img src="{{ asset('images/doctors/doctor-2.png') }}" class="w-8 h-8 rounded-lg object-contain bg-blue-50">
                  <span class="text-xs font-medium">Avatar 2</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                  <input type="radio" name="image_preset" value="images/doctors/doctor-3.png" class="text-[#012D68] focus:ring-0">
                  <img src="{{ asset('images/doctors/doctor-3.png') }}" class="w-8 h-8 rounded-lg object-contain bg-blue-50">
                  <span class="text-xs font-medium">Avatar 3</span>
                </label>
              </div>
              <div class="flex items-center gap-2 text-xs text-slate-500">
                <span>Atau upload foto sendiri:</span>
                <input type="file" name="image_file" accept="image/*" class="text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#012D68] hover:file:bg-blue-100">
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 2: AKUN LOGIN DOKTER -->
        <div>
          <div class="flex items-center gap-2 pb-2 mb-3 border-b border-slate-100">
            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">2</span>
            <h4 class="font-bold text-slate-900 text-sm">Pendaftaran Akun Login Portal Dokter</h4>
          </div>

          <div class="p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl text-xs text-[#012D68] mb-3 flex items-start gap-2">
            <svg class="w-4 h-4 text-[#012D68] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>Akun ini otomatis diberi role <b>Dokter</b>. Dokter dapat langsung login ke portal <code>/login/dokter</code> menggunakan email dan password di bawah ini.</span>
          </div>

          <div class="space-y-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Email Login Dokter <span class="text-rose-500">*</span>
              </label>
              <input type="email" name="email" required placeholder="Contoh: budi@aihealth.com" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Kata Sandi (Password) <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Ulangi Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password_confirmation" required minlength="6" placeholder="Ketik ulang kata sandi" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>
            </div>
          </div>
        </div>

        <!-- Action Footer -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
          <button type="button" onclick="closeAddDoctorModal()" class="h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs transition-colors">
            Batal
          </button>
          <button type="submit" class="h-9 px-5 rounded-xl bg-[#012D68] hover:bg-[#023E8A] text-white font-bold text-xs inline-flex items-center gap-1.5 transition-all shadow-sm">
            <svg class="w-3.5 h-3.5"><use href="#i-check"/></svg>
            <span>Simpan & Daftarkan Dokter</span>
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- MODAL: EDIT DOKTER & AKUN                                                 -->
  <!-- ========================================================================= -->
  <div id="editDoctorModalBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden opacity-0 transition-opacity duration-200 flex items-center justify-center p-4">
    <div id="editDoctorModal" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-100 flex flex-col max-h-[92vh] overflow-hidden transform scale-95 transition-all duration-200" role="dialog" aria-modal="true">
      
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 sticky top-0 z-10">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#012D68] flex items-center justify-center">
            <svg class="w-5 h-5"><use href="#i-edit"/></svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Perbarui Data Dokter & Akun</h3>
            <p class="text-xs text-slate-500">Ubah profil dokter, tarif, dan kredensial login portal.</p>
          </div>
        </div>
        <button type="button" onclick="closeEditDoctorModal()" class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center transition-colors">✕</button>
      </div>

      <form id="editDoctorForm" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-6 space-y-5">
        @csrf
        @method('PUT')

        <!-- SECTION 1: PROFIL DOKTER -->
        <div>
          <div class="flex items-center gap-2 pb-2 mb-3 border-b border-slate-100">
            <span class="w-5 h-5 rounded-full bg-[#012D68] text-white text-xs font-bold flex items-center justify-center">1</span>
            <h4 class="font-bold text-slate-900 text-sm">Informasi Profil Dokter</h4>
          </div>

          <div class="space-y-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Nama Lengkap Dokter & Gelar <span class="text-rose-500">*</span>
              </label>
              <input type="text" id="edit-nama-dokter" name="nama_dokter" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Poliklinik / Departemen <span class="text-rose-500">*</span>
                </label>
                <select name="poli_id" id="edit-poli-id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                  @foreach ($polis as $poli)
                    <option value="{{ $poli->id }}">{{ $poli->nama_poli }} ({{ $poli->name_en ?? $poli->slug }})</option>
                  @endforeach
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Spesialisasi Klinis
                </label>
                <input type="text" id="edit-spesialisasi" name="spesialisasi" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Tarif Sesi (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="edit-price-num" name="price_num" min="0" step="5000" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Jam Praktik / Ketersediaan
                </label>
                <input type="text" id="edit-availability" name="availability" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Pengalaman Praktik
                </label>
                <input type="text" id="edit-experience" name="experience" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 2: AKUN LOGIN DOKTER -->
        <div>
          <div class="flex items-center gap-2 pb-2 mb-3 border-b border-slate-100">
            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">2</span>
            <h4 class="font-bold text-slate-900 text-sm">Akun Login Portal Dokter</h4>
          </div>

          <div class="space-y-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Email Login Dokter <span class="text-rose-500">*</span>
              </label>
              <input type="email" id="edit-email" name="email" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
            </div>

            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
              <span class="text-xs font-bold text-slate-700 block">Ganti Kata Sandi (Kosongkan jika tidak ingin mengubah password)</span>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <input type="password" name="password" minlength="6" placeholder="Password baru..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                </div>
                <div>
                  <input type="password" name="password_confirmation" minlength="6" placeholder="Konfirmasi password baru..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Footer -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
          <button type="button" onclick="closeEditDoctorModal()" class="h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs transition-colors">
            Batal
          </button>
          <button type="submit" class="h-9 px-5 rounded-xl bg-[#012D68] hover:bg-[#023E8A] text-white font-bold text-xs inline-flex items-center gap-1.5 transition-all shadow-sm">
            <svg class="w-3.5 h-3.5"><use href="#i-check"/></svg>
            <span>Simpan Perubahan</span>
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- MODAL: KONFIRMASI HAPUS DOKTER                                            -->
  <!-- ========================================================================= -->
  <div id="deleteDoctorModalBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden opacity-0 transition-opacity duration-200 flex items-center justify-center p-4">
    <div id="deleteDoctorModal" class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-100 p-6 text-center transform scale-95 transition-all duration-200" role="dialog" aria-modal="true">
      <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
        <svg class="w-6 h-6"><use href="#i-trash"/></svg>
      </div>
      <h3 class="text-base font-bold text-slate-900 mb-1">Hapus Dokter?</h3>
      <p class="text-xs text-slate-500 mb-4">
        Apakah Anda yakin ingin menghapus data <b id="delete-doctor-name" class="text-slate-800"></b> beserta akun login terkait? Tindakan ini tidak dapat dibatalkan.
      </p>

      <form id="deleteDoctorForm" method="POST" class="flex items-center justify-center gap-2.5">
        @csrf
        @method('DELETE')
        <button type="button" onclick="closeDeleteDoctorModal()" class="h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs transition-colors">
          Batal
        </button>
        <button type="submit" class="h-9 px-5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs inline-flex items-center gap-1.5 transition-all shadow-sm">
          <span>Ya, Hapus Dokter</span>
        </button>
      </form>
    </div>
  </div>

  <!-- Inline Script for Modals -->
  <script>
    function openAddDoctorModal() {
      const b = document.getElementById('addDoctorModalBackdrop');
      const m = document.getElementById('addDoctorModal');
      b.classList.remove('hidden');
      setTimeout(() => {
        b.classList.remove('opacity-0');
        m.classList.remove('scale-95');
      }, 10);
    }

    function closeAddDoctorModal() {
      const b = document.getElementById('addDoctorModalBackdrop');
      const m = document.getElementById('addDoctorModal');
      b.classList.add('opacity-0');
      m.classList.add('scale-95');
      setTimeout(() => b.classList.add('hidden'), 200);
    }

    function openEditDoctorModal(doctor) {
      const form = document.getElementById('editDoctorForm');
      form.action = '/admin/doctors/' + doctor.id;
      document.getElementById('edit-nama-dokter').value = doctor.nama_dokter || '';
      document.getElementById('edit-poli-id').value = doctor.poli_id || '';
      document.getElementById('edit-spesialisasi').value = doctor.spesialisasi || '';
      document.getElementById('edit-price-num').value = doctor.price_num || 180000;
      document.getElementById('edit-availability').value = doctor.availability || '';
      document.getElementById('edit-experience').value = doctor.experience || '';
      document.getElementById('edit-email').value = (doctor.user && doctor.user.email) ? doctor.user.email : '';

      const b = document.getElementById('editDoctorModalBackdrop');
      const m = document.getElementById('editDoctorModal');
      b.classList.remove('hidden');
      setTimeout(() => {
        b.classList.remove('opacity-0');
        m.classList.remove('scale-95');
      }, 10);
    }

    function closeEditDoctorModal() {
      const b = document.getElementById('editDoctorModalBackdrop');
      const m = document.getElementById('editDoctorModal');
      b.classList.add('opacity-0');
      m.classList.add('scale-95');
      setTimeout(() => b.classList.add('hidden'), 200);
    }

    function confirmDeleteDoctor(id, name) {
      document.getElementById('deleteDoctorForm').action = '/admin/doctors/' + id;
      document.getElementById('delete-doctor-name').textContent = name;

      const b = document.getElementById('deleteDoctorModalBackdrop');
      const m = document.getElementById('deleteDoctorModal');
      b.classList.remove('hidden');
      setTimeout(() => {
        b.classList.remove('opacity-0');
        m.classList.remove('scale-95');
      }, 10);
    }

    function closeDeleteDoctorModal() {
      const b = document.getElementById('deleteDoctorModalBackdrop');
      const m = document.getElementById('deleteDoctorModal');
      b.classList.add('opacity-0');
      m.classList.add('scale-95');
      setTimeout(() => b.classList.add('hidden'), 200);
    }

    // Close on backdrop click
    ['addDoctorModalBackdrop', 'editDoctorModalBackdrop', 'deleteDoctorModalBackdrop'].forEach(id => {
      document.getElementById(id)?.addEventListener('click', (e) => {
        if (e.target.id === id) {
          if (id === 'addDoctorModalBackdrop') closeAddDoctorModal();
          if (id === 'editDoctorModalBackdrop') closeEditDoctorModal();
          if (id === 'deleteDoctorModalBackdrop') closeDeleteDoctorModal();
        }
      });
    });
  </script>

</body>
</html>

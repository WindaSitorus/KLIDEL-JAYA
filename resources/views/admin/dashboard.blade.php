<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Riwayat Pembayaran &amp; Statistik | Klinik Maristella</title>
  <link rel="icon" type="image/png" href="{{ asset('images/maristella-icon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
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
      
      /* Klinik Maristella Navy Brand Colors */
      --brand-primary: #012D68;
      --brand-primary-hover: #083E88;
      --brand-primary-tint: #EFF6FF;
      --brand-dark: #001F4D;
      --action-bg: #012D68;
      --action-text: #ffffff;

      --status-online: #10B981;
      --now-indicator: #012D68;

      --font-sans: "Plus Jakarta Sans", "Inter", system-ui, -apple-system, sans-serif;
      --radius-sm: 6px;
      --radius-md: 8px;
      --radius-lg: 14px;
      --radius-full: 999px;
      --shadow-xs: 0 1px 2px rgba(15, 23, 42, 0.05);
      --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.08);
      --shadow-lg: 0 12px 28px rgba(15, 23, 42, 0.12);
      
      --sidebar-w: 240px;
    }

    * { box-sizing: border-box; }
    body {
      font-family: var(--font-sans);
      font-size: 13px;
      line-height: 20px;
      color: var(--text-secondary);
      background: var(--bg-muted);
      -webkit-font-smoothing: antialiased;
      margin: 0;
      padding: 0;
    }

    .app {
      display: flex;
      min-height: 100vh;
      background: var(--bg-muted);
      max-width: 1440px;
      width: 100%;
      margin: 0 auto;
    }

    /* ===== Sidebar (Matching Patient Home & Admin Calendar - Gambar 1) ===== */
    .sidebar {
      width: var(--sidebar-w);
      flex-shrink: 0;
      border-right: 1px solid var(--border);
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      background: var(--bg-page);
      position: sticky;
      top: 0;
      height: 100vh;
      box-sizing: border-box;
      z-index: 20;
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
      padding: 0;
    }
    .brand img {
      height: 36px;
      width: auto;
      object-fit: contain;
    }
    .role-indicator-badge {
      font-size: 10px;
      font-weight: 700;
      background: var(--brand-primary-tint);
      border: 1px solid #BFDBFE;
      color: var(--brand-primary);
      border-radius: 4px;
      padding: 2px 6px;
      margin-left: auto;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Admin Nav */
    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 4px;
      width: 100%;
    }
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
      text-align: left;
      width: 100%;
      background: transparent;
      border: none;
      cursor: pointer;
      position: relative;
      box-sizing: border-box;
      transition: background-color 0.15s ease, color 0.15s ease;
      font-family: inherit;
    }
    .sidebar-nav .nav-item svg {
      width: 20px;
      height: 20px;
      stroke: currentColor;
      fill: none;
      stroke-width: 1.75;
      stroke-linecap: round;
      stroke-linejoin: round;
      flex-shrink: 0;
      color: var(--text-secondary);
      transition: color 0.15s ease;
    }
    .sidebar-nav .nav-item:hover {
      background-color: rgba(239, 246, 255, 0.6);
      color: var(--brand-primary);
    }
    .sidebar-nav .nav-item:hover svg {
      color: var(--brand-primary);
    }
    .sidebar-nav .nav-item.active {
      background-color: var(--brand-primary-tint);
      color: var(--brand-primary);
      font-weight: 600;
    }
    .sidebar-nav .nav-item.active svg {
      color: var(--brand-primary);
    }
    .sidebar-nav .nav-item .active-indicator {
      display: block;
      position: absolute;
      left: -20px;
      top: 10px;
      bottom: 10px;
      width: 3px;
      border-radius: 0 4px 4px 0;
      background-color: var(--brand-primary);
    }
    hr.divider {
      border: 0;
      border-top: 1px solid var(--border);
      margin: 12px 0;
    }

    .spacer { flex: 1; }
    .user-card {
      display: flex;
      align-items: center;
      gap: 10px;
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 10px;
      box-shadow: var(--shadow-xs);
      background: #fff;
    }
    .avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: 12px;
      font-weight: 700;
      color: #fff;
      background: var(--brand-primary);
      position: relative;
      flex-shrink: 0;
    }
    .avatar .status {
      position: absolute;
      right: -1px;
      bottom: -1px;
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: var(--status-online);
      border: 2px solid #fff;
    }
    .user-card strong { display: block; font-size: 12px; color: var(--text-primary); line-height: 16px; font-weight: 600; }
    .user-card small { display: block; font-size: 11px; color: var(--text-tertiary); line-height: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 110px; }
    
    .logout-btn {
      margin-top: 8px;
      width: 100%;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: 11.5px;
      font-weight: 500;
      border-radius: var(--radius-md);
      border: 1px solid var(--border);
      background: #fff;
      color: var(--text-secondary);
      transition: all .15s;
      cursor: pointer;
    }
    .logout-btn:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

    /* ===== Main Content Area ===== */
    .main {
      flex: 1;
      min-width: 0;
      padding: 24px 32px 48px;
      background: #F8FAFC;
      overflow-x: hidden;
    }

    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 999px; }

    /* Mobile bottom navigation */
    @media (max-width: 860px) {
      .sidebar { display: none; }
      .main { padding: 16px 16px 80px; }
      .floating-bottom-nav {
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        bottom: 20px;
        display: flex;
        gap: 8px;
        background: #ffffff;
        padding: 6px 12px;
        border-radius: 999px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.14);
        border: 1px solid var(--border);
        z-index: 40;
      }
      .floating-bottom-nav a {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: transparent;
        color: var(--text-secondary);
        display: grid;
        place-items: center;
        transition: all 0.2s ease;
        text-decoration: none;
      }
      .floating-bottom-nav a.active {
        background: var(--brand-primary) !important;
        color: #ffffff !important;
      }
    }
    @media (min-width: 861px) {
      .floating-bottom-nav { display: none; }
    }
  </style>
</head>
<body class="text-slate-800 antialiased min-h-screen">

  <!-- SVG Sprite -->
  <svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
      <symbol id="i-cal" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></symbol>
      <symbol id="i-bill" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/><path d="M7 15h2M12 15h5"/></symbol>
      <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></symbol>
      <symbol id="i-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></symbol>
      <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></symbol>
      <symbol id="i-filter" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></symbol>
      <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
      <symbol id="i-download" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></symbol>
      <symbol id="i-check" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></symbol>
    </defs>
  </svg>

  <div class="app">

    <!-- ====================================================================== -->
    <!-- SIDEBAR: SESUAI DESAIN GAMBAR 1 (KLINIK MARISTELLA ADMIN SIDEBAR)      -->
    <!-- ====================================================================== -->
    <aside class="sidebar">
      
      <!-- Brand & Admin Role Indicator -->
      <div class="brand">
        <a href="{{ route('home') }}" title="Klinik Maristella">
          <img src="{{ asset('images/maristella-logo-horizontal.png') }}" alt="Klinik Maristella" />
        </a>
        <span class="role-indicator-badge">Admin</span>
      </div>

      <!-- Admin Navigation Menu (Matching Gambar 1 with Jadwal Dokter & Riwayat Pembayaran) -->
      <nav class="sidebar-nav" aria-label="Navigasi admin">
        
        <!-- Menu 1: Jadwal Dokter (Link to Calendar) -->
        <a href="{{ route('admin.calendar') }}" class="nav-item" title="Jadwal Dokter">
          <svg><use href="#i-cal"/></svg>
          <span>Jadwal Dokter</span>
        </a>

        <!-- Menu 2: Riwayat Pembayaran (Active Item on this page) -->
        <a href="{{ route('admin.dashboard') }}" class="nav-item active" aria-current="page" title="Riwayat Pembayaran">
          <span class="active-indicator"></span>
          <svg><use href="#i-bill"/></svg>
          <span>Riwayat Pembayaran</span>
        </a>

      </nav>

      <div class="spacer"></div>

      <!-- Active Admin User Profile Card -->
      <div class="user-card" id="adminUserCard">
        <div class="avatar">
          {{ $user['initials'] ?? 'AL' }}
          <i class="status"></i>
        </div>
        <div class="min-w-0 flex-1">
          <strong>{{ $user['name'] ?? 'Amélie Laurent' }}</strong>
          <small>{{ $user['role'] ?? 'Administrator' }}</small>
        </div>
      </div>

      <!-- Logout Button -->
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn" title="Keluar dari Portal Admin">
          <svg style="width:14px;height:14px"><use href="#i-logout"/></svg>
          <span>Keluar Portal</span>
        </button>
      </form>

    </aside>

    <!-- ====================================================================== -->
    <!-- MAIN CONTENT: RIWAYAT PEMBAYARAN SESUAI DESAIN KLINIK MARISTELLA       -->
    <!-- ====================================================================== -->
    <main class="main space-y-6">

      <!-- Topbar Header -->
      <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
          <div class="flex items-center gap-2.5">
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#012D68] tracking-tight">
              Riwayat Pembayaran
            </h1>
            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-[#012D68] border border-blue-200" title="Klinik Maristella / Hayat Clinic MMC">
              Hayat Clinic MMC
            </span>
          </div>
          <p class="text-xs text-slate-500 font-medium mt-0.5">
            Sistem Informasi Manajemen &amp; Riwayat Transaksi Klinik Maristella
          </p>
        </div>

        <!-- Right Action Controls -->
        <div class="flex items-center gap-2.5 flex-wrap">
          
          <!-- Timeframe Selector -->
          <div class="relative">
            <select id="timeframe-select" onchange="window.location.href='{{ route('admin.dashboard') }}?timeframe=' + this.value" class="appearance-none h-9.5 pl-3.5 pr-8 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#012D68]/20 cursor-pointer shadow-xs transition-all">
              <option value="week" {{ ($timeframe ?? 'week') === 'week' ? 'selected' : '' }}>This Week</option>
              <option value="month" {{ ($timeframe ?? 'week') === 'month' ? 'selected' : '' }}>This Month</option>
              <option value="year" {{ ($timeframe ?? 'week') === 'year' ? 'selected' : '' }}>This Year</option>
              <option value="all" {{ ($timeframe ?? 'week') === 'all' ? 'selected' : '' }}>All Time</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
          </div>

          <!-- Export CSV Button (Matching Image 4 with Maristella Brand Colors) -->
          <a href="{{ route('admin.exportCsv', ['timeframe' => $timeframe ?? 'all']) }}" class="h-9.5 px-4 rounded-xl bg-[#012D68] hover:bg-[#083E88] active:scale-[0.98] text-white text-xs font-bold flex items-center gap-2 shadow-xs transition-all" title="Unduh data pembayaran dalam format CSV">
            <svg class="w-4 h-4"><use href="#i-download"/></svg>
            <span>Export CSV</span>
          </a>

        </div>
      </header>

      @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between text-xs font-semibold shadow-xs">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>{{ session('success') }}</span>
          </div>
          <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">✕</button>
        </div>
      @endif

      <!-- ====================================================================== -->
      <!-- ROW 1: 3 KPI METRIC SUMMARY CARDS                                      -->
      <!-- ====================================================================== -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-4.5">

        <!-- Card 1: Total Visitors (Pasien) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-blue-200">
          <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
            <span class="font-bold text-slate-600 uppercase tracking-wider text-[11px]">Total Visitors</span>
            <button type="button" class="text-slate-400 hover:text-slate-600" title="Informasi Pasien Terdaftar: {{ $totalVisitors }} Orang">•••</button>
          </div>
          <div class="flex items-baseline justify-between mt-1">
            <div class="flex items-baseline gap-2">
              <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalVisitors }}</span>
              <span class="inline-flex items-center gap-0.5 text-xs font-bold {{ ($visitorsGrowthPositive ?? true) ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-1.5 py-0.5 rounded-md">
                {{ ($visitorsGrowthPositive ?? true) ? '▲ +' : '▼ ' }}{{ abs($visitorsGrowth ?? 14.8) }}%
              </span>
            </div>
          </div>
          <p class="text-[11.5px] text-slate-400 mt-2 font-medium">Compared to Last Week</p>
        </div>

        <!-- Card 2: Surgeries / Sesi Medis Selesai -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-blue-200">
          <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
            <span class="font-bold text-slate-600 uppercase tracking-wider text-[11px]">Surgeries</span>
            <button type="button" class="text-slate-400 hover:text-slate-600" title="Total Pemeriksaan Selesai: {{ $totalSurgeries }}">•••</button>
          </div>
          <div class="flex items-baseline justify-between mt-1">
            <div class="flex items-baseline gap-2">
              <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalSurgeries }}</span>
              <span class="inline-flex items-center gap-0.5 text-xs font-bold {{ ($surgeriesGrowthPositive ?? true) ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-1.5 py-0.5 rounded-md">
                {{ ($surgeriesGrowthPositive ?? true) ? '▲ +' : '▼ ' }}{{ abs($surgeriesGrowth ?? 4.5) }}%
              </span>
            </div>
          </div>
          <p class="text-[11.5px] text-slate-400 mt-2 font-medium">Compared to Last Week</p>
        </div>

        <!-- Card 3: Revenue / Total Pendapatan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-blue-200">
          <div class="flex items-center justify-between text-slate-400 text-xs mb-2">
            <span class="font-bold text-slate-600 uppercase tracking-wider text-[11px]">Revenue</span>
            <button type="button" class="text-slate-400 hover:text-slate-600" title="Total Transaksi Lunas: ${{ number_format($totalRevenue, 2) }}">•••</button>
          </div>
          <div class="flex items-baseline justify-between mt-1">
            <div class="flex items-baseline gap-2">
              <span class="text-3xl font-extrabold text-slate-900 tracking-tight">
                ${{ number_format($totalRevenue, 0) }}
              </span>
              <span class="inline-flex items-center gap-0.5 text-xs font-bold {{ ($revenueGrowthPositive ?? false) ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-1.5 py-0.5 rounded-md">
                {{ ($revenueGrowthPositive ?? false) ? '▲ +' : '▼ ' }}{{ abs($revenueGrowth ?? 7.9) }}%
              </span>
            </div>
          </div>
          <p class="text-[11.5px] text-slate-400 mt-2 font-medium">Compared to Last Week</p>
        </div>

      </section>

      <!-- ====================================================================== -->
      <!-- ROW 2: DAILY STATISTICS BAR CHART & MINI CALENDAR DYNAMIC DATABASE WIDGET -->
      <!-- ====================================================================== -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Column Left (2 cols): Daily Statistics Bar Chart (Directly from Database) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
          
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-sm font-bold text-slate-900 tracking-tight">Daily Statistics</h2>
              <p class="text-[11px] text-slate-400 font-medium">Perbandingan pasien medis &amp; pasien terjadwal</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
              <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-[#012D68]"></span>
                <span class="text-slate-600 text-[11.5px]">Medical Patients</span>
              </div>
              <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-300"></span>
                <span class="text-slate-400 text-[11.5px]">Appointed Patients</span>
              </div>
            </div>
          </div>

          <!-- Visual Bar Chart (Dynamic from Database) -->
          <div class="relative pt-6 pb-2">
            <!-- Y-Axis Gridlines -->
            @php
              $s100 = $maxChartVal ?? 10;
              $s75 = round(($maxChartVal ?? 10) * 0.75);
              $s50 = round(($maxChartVal ?? 10) * 0.5);
              $s25 = round(($maxChartVal ?? 10) * 0.25);
            @endphp
            <div class="absolute inset-x-0 top-0 bottom-8 flex flex-col justify-between pointer-events-none text-[10px] text-slate-300">
              <div class="border-b border-dashed border-slate-100 flex items-center justify-between pb-0.5"><span>{{ $s100 }}</span></div>
              <div class="border-b border-dashed border-slate-100 flex items-center justify-between pb-0.5"><span>{{ $s75 }}</span></div>
              <div class="border-b border-dashed border-slate-100 flex items-center justify-between pb-0.5"><span>{{ $s50 }}</span></div>
              <div class="border-b border-dashed border-slate-100 flex items-center justify-between pb-0.5"><span>{{ $s25 }}</span></div>
              <div class="border-b border-slate-200 flex items-center justify-between pb-0.5 text-slate-400 font-bold"><span>0</span></div>
            </div>

            <!-- Bar Columns (MON - SUN) Styled with Maristella Navy & Sky Blue -->
            <div class="relative h-44 flex items-end justify-between px-3 pt-4 z-10">
              @foreach (($dailyStats ?? []) as $stat)
                <div class="flex flex-col items-center gap-2 group flex-1">
                  <div class="flex items-end gap-1.5 h-36">
                    <div class="w-5.5 bg-[#012D68] rounded-t-md transition-all group-hover:opacity-90 relative" style="height: {{ $stat['medical_pct'] }}%;" title="{{ $stat['day'] }} ({{ $stat['date'] }}) - Medical: {{ $stat['medical'] }}">
                      @if (($stat['medical'] ?? 0) > 0)
                        <span class="opacity-0 group-hover:opacity-100 absolute -top-5 left-1/2 -translate-x-1/2 bg-[#012D68] text-white text-[9px] font-bold py-0.5 px-1 rounded pointer-events-none transition-opacity z-20">{{ $stat['medical'] }}</span>
                      @endif
                    </div>
                    <div class="w-5.5 bg-blue-200 rounded-t-md transition-all group-hover:opacity-90 relative" style="height: {{ $stat['appointed_pct'] }}%;" title="{{ $stat['day'] }} ({{ $stat['date'] }}) - Appointed: {{ $stat['appointed'] }}">
                      @if (($stat['appointed'] ?? 0) > 0)
                        <span class="opacity-0 group-hover:opacity-100 absolute -top-5 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-[9px] font-bold py-0.5 px-1 rounded pointer-events-none transition-opacity z-20">{{ $stat['appointed'] }}</span>
                      @endif
                    </div>
                  </div>
                  <span class="text-[11px] font-bold {{ ($stat['is_today'] ?? false) ? 'text-[#012D68] font-extrabold underline underline-offset-4' : 'text-slate-400' }} group-hover:text-slate-700">{{ $stat['day'] }}</span>
                </div>
              @endforeach
            </div>
          </div>

        </div>

        <!-- Column Right (1 col): Dynamic Mini Calendar Widget -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
          
          <div class="flex items-center justify-between mb-3">
            <div>
              <h2 class="text-sm font-bold text-slate-900 tracking-tight" id="calendar-header-title">{{ $calendarMonthName ?? 'November' }}, {{ $calendarYear ?? 2026 }}</h2>
              <span class="sr-only">January, 2026</span>
            </div>
            <div class="flex items-center gap-1 text-slate-400">
              <a href="{{ route('admin.dashboard', ['month' => $prevMonth ?? 10, 'year' => $prevYear ?? 2026, 'timeframe' => $timeframe ?? 'week']) }}" class="p-1 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors" title="Bulan Sebelumnya">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
              </a>
              <a href="{{ route('admin.dashboard', ['month' => $nextMonth ?? 12, 'year' => $nextYear ?? 2026, 'timeframe' => $timeframe ?? 'week']) }}" class="p-1 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors" title="Bulan Selanjutnya">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
              </a>
            </div>
          </div>

          <!-- Calendar Grid -->
          <div class="text-xs">
            <div class="grid grid-cols-7 text-center font-bold text-[10.5px] text-slate-400 mb-2">
              <span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span><span>SAT</span><span>SUN</span>
            </div>
            
            <div class="grid grid-cols-7 text-center gap-y-1.5 text-xs text-slate-700 font-medium">
              @foreach (($calendarGrid ?? []) as $cell)
                <div class="py-1 flex flex-col items-center justify-center relative">
                  @if (!empty($cell['is_today']))
                    <span class="w-6.5 h-6.5 rounded-full bg-[#012D68] text-white font-bold flex items-center justify-center shadow-xs">
                      {{ $cell['day'] }}
                    </span>
                  @elseif (empty($cell['is_current_month']))
                    <span class="text-slate-300">{{ $cell['day'] }}</span>
                  @else
                    <span class="text-slate-700 font-medium hover:text-[#012D68] transition-colors">{{ $cell['day'] }}</span>
                  @endif
                  @if (!empty($cell['has_booking']))
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 absolute bottom-0"></span>
                  @endif
                </div>
              @endforeach
            </div>
          </div>

          <!-- Bottom link to full calendar -->
          <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-400 font-medium">Jadwal Praktik Hari Ini</span>
            <a href="{{ route('admin.calendar') }}" class="font-bold text-[#012D68] hover:underline flex items-center gap-1">
              <span>Buka Jadwal</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>

        </div>

      </section>

      <!-- ====================================================================== -->
      <!-- ROW 3: TODAY'S APPOINTMENTS, RIWAYAT PEMBAYARAN & SEMUA PASIEN (ADMIN) -->
      <!-- ====================================================================== -->
      <section class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
        
        <!-- Section Header with View Mode Switcher -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <button type="button" id="admin-tab-btn-payments" onclick="switchAdminView('payments')" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#012D68] text-white transition-all shadow-xs">
              Today's Appointments &amp; Riwayat Pembayaran
            </button>
            <button type="button" id="admin-tab-btn-patients" onclick="switchAdminView('patients')" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-all">
              Daftar Seluruh Pasien (Semua Dokter)
              <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-800 font-bold">{{ $patients->count() }}</span>
            </button>
          </div>
          <span class="text-xs text-slate-400 font-medium hidden sm:inline">Portal Admin: Akses Penuh Semua Dokter &amp; Pasien</span>
        </div>

        <!-- VIEW 1: PAYMENTS & APPOINTMENTS TABLE -->
        <div id="view-payments" class="space-y-4">
        <!-- Filter & Search Controls Bar -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 text-xs">
          
          <!-- Search Box -->
          <div class="relative flex-1 max-w-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <svg class="w-4 h-4"><use href="#i-search"/></svg>
            </div>
            <input 
              type="text" 
              id="payment-search-input"
              placeholder="Search nama pasien, ID, dokter..." 
              class="w-full h-9.5 pl-9 pr-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#012D68]/20 focus:border-[#012D68] transition-all"
            >
          </div>

          <!-- Action Buttons Group -->
          <div class="flex items-center gap-2 flex-wrap">
            
            <!-- Filter Button -->
            <button 
              type="button" 
              onclick="document.getElementById('status-filter-bar').classList.toggle('hidden')" 
              class="h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-50 flex items-center gap-1.5 shadow-xs transition-all"
            >
              <svg class="w-3.5 h-3.5 text-slate-500"><use href="#i-filter"/></svg>
              <span>Filter Status</span>
            </button>

            <!-- New Appointment Button -->
            <button 
              type="button" 
              onclick="document.getElementById('quick-booking-modal').classList.remove('hidden')" 
              class="h-9.5 px-3.5 rounded-xl bg-[#012D68] hover:bg-[#083E88] text-white font-bold flex items-center gap-1.5 shadow-xs active:scale-[0.98] transition-all"
            >
              <svg class="w-4 h-4"><use href="#i-plus"/></svg>
              <span>New Appointment</span>
            </button>

            <!-- Sort Dropdown -->
            <div class="relative">
              <select id="sort-select" onchange="sortPaymentTable(this.value)" class="appearance-none h-9.5 pl-3 pr-8 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#012D68]/20 cursor-pointer shadow-xs">
                <option value="newest" selected>Newest to oldest</option>
                <option value="oldest">Oldest to newest</option>
                <option value="amount_high">Amount: High to Low</option>
                <option value="amount_low">Amount: Low to High</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </div>

          </div>

        </div>

        <!-- Optional Status Filter Pill Bar -->
        <div id="status-filter-bar" class="hidden flex items-center gap-2 pt-2 border-t border-slate-100 text-xs">
          <span class="text-slate-400 font-semibold">Status:</span>
          <button type="button" onclick="filterByStatus('all')" class="px-2.5 py-1 rounded-lg bg-[#012D68] text-white font-bold filter-btn" data-status="all">Semua</button>
          <button type="button" onclick="filterByStatus('confirmed')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold filter-btn" data-status="confirmed">Confirmed</button>
          <button type="button" onclick="filterByStatus('changed')" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-semibold filter-btn" data-status="changed">Changed</button>
          <button type="button" onclick="filterByStatus('canceled')" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-semibold filter-btn" data-status="canceled">Canceled</button>
        </div>

        <!-- Table Container -->
        <div id="table-container" class="overflow-x-auto custom-scrollbar border border-slate-100 rounded-xl">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
              <tr>
                <th class="py-3 px-3.5 w-8">
                  <input type="checkbox" id="check-all" class="rounded border-slate-300 text-[#012D68] focus:ring-[#012D68]/20">
                </th>
                <th class="py-3 px-3 font-semibold text-slate-600">Name</th>
                <th class="py-3 px-3 font-semibold text-slate-600">ID Number</th>
                <th class="py-3 px-3 font-semibold text-slate-600">Date &amp; Time</th>
                <th class="py-3 px-3 font-semibold text-slate-600">Type</th>
                <th class="py-3 px-3 font-semibold text-slate-600">Doctor</th>
                <th class="py-3 px-3 font-semibold text-slate-600">Status</th>
                <th class="py-3 px-3 font-semibold text-slate-600 text-center">Actions</th>
              </tr>
            </thead>
            <tbody id="payment-table-body" class="divide-y divide-slate-100 font-medium">
              @forelse ($payments as $payment)
                @php
                  $status = strtolower($payment->status ?? 'confirmed');
                  $statusConfig = match($status) {
                    'confirmed', 'paid' => [
                      'label' => 'Confirmed',
                      'dot' => 'bg-emerald-500',
                      'badge' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                    ],
                    'changed', 'pending' => [
                      'label' => 'Changed',
                      'dot' => 'bg-blue-500',
                      'badge' => 'text-blue-700 bg-blue-50 border-blue-200',
                    ],
                    'canceled' => [
                      'label' => 'Canceled',
                      'dot' => 'bg-rose-500',
                      'badge' => 'text-rose-700 bg-rose-50 border-rose-200',
                    ],
                    default => [
                      'label' => 'Confirmed',
                      'dot' => 'bg-emerald-500',
                      'badge' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                    ]
                  };
                  $displayId = $payment->invoice_number ?: ($payment->booking?->booking_code ?: '#BILL' . str_pad($payment->id, 5, '0', STR_PAD_LEFT));
                  $timestamp = $payment->paid_at ? $payment->paid_at->timestamp : ($payment->created_at ? $payment->created_at->timestamp : 0);
                  $docName = $payment->doctor?->nama_dokter ?? ($payment->doctor_name ?: '-');
                  $serviceName = $payment->service_type ?? ($payment->booking?->doctor?->spesialisasi ?: 'Pemeriksaan Klinis');
                @endphp
                <tr 
                  class="payment-row hover:bg-slate-50/80 transition-colors" 
                  data-name="{{ strtolower($payment->patient_name) }}" 
                  data-invoice="{{ strtolower($displayId) }}" 
                  data-doctor="{{ strtolower($docName) }}" 
                  data-service="{{ strtolower($serviceName) }}"
                  data-amount="{{ (float) $payment->amount }}"
                  data-date="{{ $timestamp }}"
                  data-status="{{ $status }}"
                >
                  
                  <td class="py-3 px-3.5">
                    <input type="checkbox" class="payment-check rounded border-slate-300 text-[#012D68] focus:ring-[#012D68]/20">
                  </td>

                  <!-- Patient Name with initials avatar -->
                  <td class="py-3 px-3">
                    <div class="flex items-center gap-2.5">
                      <div class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-[#012D68] font-bold text-[11px] flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr($payment->patient_name, 0, 1)) }}
                      </div>
                      <span class="font-bold text-slate-800 text-xs">{{ $payment->patient_name }}</span>
                    </div>
                  </td>

                  <!-- ID Number -->
                  <td class="py-3 px-3 text-slate-500 font-mono text-[11.5px]">
                    {{ $displayId }}
                  </td>

                  <!-- Date & Time -->
                  <td class="py-3 px-3 text-slate-600">
                    {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d - H:i') : ($payment->created_at ? \Carbon\Carbon::parse($payment->created_at)->format('M d - H:i') : '-') }}
                  </td>

                  <!-- Type / Layanan -->
                  <td class="py-3 px-3 text-slate-700 font-medium">
                    {{ $serviceName }}
                  </td>

                  <!-- Doctor -->
                  <td class="py-3 px-3 text-slate-600">
                    {{ $docName }}
                  </td>

                  <!-- Status with indicator dot -->
                  <td class="py-3 px-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusConfig['badge'] }}">
                      <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                      <span>{{ $statusConfig['label'] }}</span>
                    </span>
                  </td>

                  <!-- Actions dot -->
                  <td class="py-3 px-3 text-center">
                    <button 
                      type="button" 
                      class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" 
                      title="Aksi Transaksi"
                      onclick="alert('Detail Pembayaran:\nInvoice: {{ $displayId }}\nPasien: {{ $payment->patient_name }}\nNominal: ${{ number_format($payment->amount, 2) }}\nStatus: {{ ucfirst($payment->status) }}\nDokter: {{ $docName }}')"
                    >
                      <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20"><circle cx="4" cy="10" r="1.5"/><circle cx="10" cy="10" r="1.5"/><circle cx="16" cy="10" r="1.5"/></svg>
                    </button>
                  </td>

                </tr>
              @empty
                <tr>
                  <td colspan="8" class="py-8 text-center text-slate-400">
                    Belum ada riwayat pembayaran yang tercatat dalam database.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination / Footer Info -->
        <div class="flex items-center justify-between text-xs text-slate-500 pt-2">
          <span>Menampilkan {{ $payments->count() }} transaksi pembayaran terhubung database</span>
          <div class="flex items-center gap-1">
            <button type="button" class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-400 cursor-not-allowed">Previous</button>
            <button type="button" class="px-2.5 py-1 rounded-lg bg-[#012D68] text-white font-bold">1</button>
            <button type="button" class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Next</button>
          </div>
        </div>
        </div><!-- End #view-payments -->

        <!-- VIEW 2: ALL PATIENTS & MEDICAL RECORDS ACROSS ALL DOCTORS (ADMIN PRIVILEGE) -->
        <div id="view-patients" class="space-y-4 hidden">
          <div class="flex items-center justify-between text-xs pb-1">
            <p class="text-slate-600 font-medium">Administrator dapat memantau data seluruh rekam medis &amp; pasien dari semua dokter spesialis.</p>
            <span class="font-bold text-[#012D68] bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-lg">Akses Menyeluruh</span>
          </div>

          <div class="overflow-x-auto custom-scrollbar border border-slate-100 rounded-xl">
            <table class="w-full text-left text-xs whitespace-nowrap">
              <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                <tr>
                  <th class="py-3 px-3.5 font-semibold text-slate-600">Pasien / MRN</th>
                  <th class="py-3 px-3 font-semibold text-slate-600">Gender &amp; Usia</th>
                  <th class="py-3 px-3 font-semibold text-slate-600">No. Kontak</th>
                  <th class="py-3 px-3 font-semibold text-slate-600">Dokter Spesialis</th>
                  <th class="py-3 px-3 font-semibold text-slate-600">Diagnosis Klinis</th>
                  <th class="py-3 px-3 font-semibold text-slate-600">Terapi / Obat</th>
                  <th class="py-3 px-3 font-semibold text-slate-600 text-center">Status Rekam Medis</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium">
                @forelse($patients as $pat)
                  @php
                    $lastBooking = $pat->bookings->sortByDesc('id')->first();
                    $lastDoctor = $lastBooking?->doctor;
                    $lastRecord = $lastBooking?->medicalRecord;
                    $docBadge = match($lastDoctor?->nama_dokter) {
                      'Dr. Lina Wijaya' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'dot' => '#E11D48'],
                      'Dr. Anaya Kapoor' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'dot' => '#4F46E5'],
                      default => ['bg' => 'bg-blue-50', 'text' => 'text-[#012D68]', 'border' => 'border-blue-200', 'dot' => '#012D68'],
                    };
                    $age = $pat->dob ? \Carbon\Carbon::parse($pat->dob)->age . ' thn' : '-';
                  @endphp
                  <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3 px-3.5">
                      <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-[#012D68] font-bold text-xs flex items-center justify-center flex-shrink-0">
                          {{ strtoupper(substr($pat->full_name, 0, 1)) }}
                        </div>
                        <div>
                          <b class="text-xs text-slate-900 block">{{ $pat->full_name }}</b>
                          <small class="text-[11px] text-slate-400 font-mono">{{ $pat->mrn }}</small>
                        </div>
                      </div>
                    </td>
                    <td class="py-3 px-3 text-slate-600">
                      {{ $pat->gender ?? '-' }} · {{ $age }}
                    </td>
                    <td class="py-3 px-3 font-mono text-[11.5px] text-slate-600">
                      {{ $pat->phone ?? '-' }}
                    </td>
                    <td class="py-3 px-3">
                      @if($lastDoctor)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold {{ $docBadge['bg'] }} {{ $docBadge['text'] }} border {{ $docBadge['border'] }}">
                          <span class="w-2 h-2 rounded-full" style="background-color: {{ $docBadge['dot'] }}"></span>
                          {{ $lastDoctor->nama_dokter }}
                        </span>
                      @else
                        <span class="text-slate-400">-</span>
                      @endif
                    </td>
                    <td class="py-3 px-3 text-slate-800 max-w-xs truncate">
                      @if($lastRecord && !empty($lastRecord->assessmen['primary_diagnosis']))
                        <span class="font-semibold">{{ $lastRecord->assessmen['primary_diagnosis'] }}</span>
                      @elseif($lastBooking)
                        <span class="text-slate-500 italic">{{ $lastBooking->symptoms }}</span>
                      @else
                        <span class="text-slate-400">Belum ada diagnosis</span>
                      @endif
                    </td>
                    <td class="py-3 px-3 text-slate-600 max-w-xs truncate">
                      @if($lastRecord && !empty($lastRecord->plan['medications']))
                        {{ collect($lastRecord->plan['medications'])->pluck('name')->implode(', ') }}
                      @elseif($lastRecord && !empty($lastRecord->plan['clinical_action']))
                        {{ $lastRecord->plan['clinical_action'] }}
                      @else
                        <span class="text-slate-400">-</span>
                      @endif
                    </td>
                    <td class="py-3 px-3 text-center">
                      @if($lastRecord)
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai Diperiksa
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                          <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Terdaftar
                        </span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="py-8 text-center text-slate-400">Belum ada pasien terdaftar.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="text-xs text-slate-500 pt-1">
            Total terdaftar: <b>{{ $patients->count() }} Pasien</b> di semua poliklinik spesialis
          </div>
        </div><!-- End #view-patients -->

      </section>

    </main>

  </div>

  <!-- Quick Add Appointment / Payment Modal (Integrated with Database) -->
  <div id="quick-booking-modal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Buat Jadwal &amp; Transaksi Pasien</h3>
          <p class="text-[11px] text-slate-400">Terintegrasi langsung ke tabel database</p>
        </div>
        <button type="button" onclick="document.getElementById('quick-booking-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
      </div>
      <form action="{{ route('admin.storeTransaction') }}" method="POST" class="space-y-3.5 text-xs">
        @csrf
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Nama Pasien</label>
            <input type="text" name="patient_name" placeholder="cth: Rina Anggraini" required class="w-full h-9 px-3 rounded-xl border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#012D68]/20 focus:border-[#012D68]">
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
            <input type="text" name="phone" placeholder="cth: 0812-3456-7890" required class="w-full h-9 px-3 rounded-xl border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#012D68]/20 focus:border-[#012D68]">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Pilih Dokter Spesialis</label>
          <select name="doctor_id" required class="w-full h-9 px-3 rounded-xl border border-slate-200 text-slate-800">
            @foreach(($doctors ?? []) as $doc)
              <option value="{{ $doc->id }}">{{ $doc->nama_dokter }} ({{ $doc->spesialisasi ?? $doc->poli?->nama_poli ?? 'Spesialis' }})</option>
            @endforeach
          </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Tanggal</label>
            <input type="date" name="booking_date" value="{{ now()->toDateString() }}" required class="w-full h-9 px-3 rounded-xl border border-slate-200 text-slate-800">
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Jam Konsultasi</label>
            <select name="booking_time" class="w-full h-9 px-3 rounded-xl border border-slate-200 text-slate-800">
              <option value="09:00 AM">09:00 AM</option>
              <option value="10:00 AM">10:00 AM</option>
              <option value="11:00 AM">11:00 AM</option>
              <option value="01:00 PM">01:00 PM</option>
              <option value="02:30 PM">02:30 PM</option>
              <option value="03:30 PM">03:30 PM</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Layanan / Tarif</label>
            <select name="tariff_plan" class="w-full h-9 px-3 rounded-xl border border-slate-200 text-slate-800">
              <option value="with_medicine">Konsultasi + Obat ($435.00)</option>
              <option value="consultation_only">Konsultasi Saja ($180.00)</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Metode Bayar</label>
            <select name="payment_method" class="w-full h-9 px-3 rounded-xl border border-slate-200 text-slate-800">
              <option value="cash">Cash / Tunai</option>
              <option value="credit_card">Kartu Kredit / Debit</option>
              <option value="qris">QRIS</option>
              <option value="bank_transfer">Transfer Bank</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Status Pembayaran</label>
            <select name="payment_status" class="w-full h-9 px-3 rounded-xl border border-slate-200 text-slate-800">
              <option value="paid">Lunas (Paid)</option>
              <option value="unpaid">Belum Bayar (Unpaid)</option>
            </select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Keluhan / Catatan</label>
            <input type="text" name="symptoms" placeholder="cth: Nyeri sendi lutut" class="w-full h-9 px-3 rounded-xl border border-slate-200 text-slate-800">
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
          <button type="button" onclick="document.getElementById('quick-booking-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-xl bg-[#012D68] hover:bg-[#083E88] text-white font-bold shadow-xs">Simpan ke Database</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Mobile Floating Bottom Nav -->
  <div class="floating-bottom-nav">
    <a href="{{ route('admin.calendar') }}" title="Jadwal Dokter">
      <svg class="w-5 h-5"><use href="#i-cal"/></svg>
    </a>
    <a href="{{ route('admin.dashboard') }}" class="active" title="Riwayat Pembayaran">
      <svg class="w-5 h-5"><use href="#i-bill"/></svg>
    </a>
  </div>

  <!-- Live Search, Filtering & Sorting Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const searchInput = document.getElementById('payment-search-input');
      const rows = document.querySelectorAll('.payment-row');
      const checkAll = document.getElementById('check-all');
      const checks = document.querySelectorAll('.payment-check');

      // Live Search Filter
      searchInput?.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        rows.forEach(row => {
          const name = row.dataset.name || '';
          const invoice = row.dataset.invoice || '';
          const doctor = row.dataset.doctor || '';
          const service = row.dataset.service || '';
          const matches = name.includes(query) || invoice.includes(query) || doctor.includes(query) || service.includes(query);
          row.style.display = matches ? '' : 'none';
        });
      });

      // Select All Checkbox
      checkAll?.addEventListener('change', (e) => {
        checks.forEach(c => c.checked = e.target.checked);
      });
    });

    // Client-side instant table sorting
    function sortPaymentTable(order) {
      const tbody = document.getElementById('payment-table-body');
      if (!tbody) return;
      const rows = Array.from(tbody.querySelectorAll('.payment-row'));

      rows.sort((a, b) => {
        if (order === 'newest') {
          return (Number(b.dataset.date) || 0) - (Number(a.dataset.date) || 0);
        } else if (order === 'oldest') {
          return (Number(a.dataset.date) || 0) - (Number(b.dataset.date) || 0);
        } else if (order === 'amount_high') {
          return (parseFloat(b.dataset.amount) || 0) - (parseFloat(a.dataset.amount) || 0);
        } else if (order === 'amount_low') {
          return (parseFloat(a.dataset.amount) || 0) - (parseFloat(b.dataset.amount) || 0);
        }
        return 0;
      });

      rows.forEach(row => tbody.appendChild(row));
    }

    // Filter table rows by payment status
    function filterByStatus(status) {
      const rows = document.querySelectorAll('.payment-row');
      const buttons = document.querySelectorAll('.filter-btn');

      buttons.forEach(btn => {
        if (btn.dataset.status === status) {
          btn.classList.add('bg-[#012D68]', 'text-white', 'font-bold');
        } else {
          btn.classList.remove('bg-[#012D68]', 'text-white', 'font-bold');
        }
      });

      rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // Switch between Payments and All-Patients views
    function switchAdminView(view) {
      const viewPayments = document.getElementById('view-payments');
      const viewPatients = document.getElementById('view-patients');
      const btnPayments = document.getElementById('admin-tab-btn-payments');
      const btnPatients = document.getElementById('admin-tab-btn-patients');
      
      if (view === 'patients') {
        viewPayments?.classList.add('hidden');
        viewPatients?.classList.remove('hidden');
        if (btnPatients) btnPatients.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#012D68] text-white transition-all shadow-xs";
        if (btnPayments) btnPayments.className = "px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-all";
      } else {
        viewPatients?.classList.add('hidden');
        viewPayments?.classList.remove('hidden');
        if (btnPayments) btnPayments.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#012D68] text-white transition-all shadow-xs";
        if (btnPatients) btnPatients.className = "px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-all";
      }
    }

    // Copy to clipboard helper
    function copyToClipboard(text) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
          alert('ID ' + text + ' berhasil disalin ke clipboard.');
        });
      } else {
        alert('ID ' + text + ' disalin.');
      }
    }
  </script>

</body>
</html>
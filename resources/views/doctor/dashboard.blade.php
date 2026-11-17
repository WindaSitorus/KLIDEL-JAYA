<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Portal Dokter — Klinik Maristella</title>
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
      --color-primary: #012D68;
      --color-primary-hover: #083E88;
      --color-primary-tint: #EFF6FF;
      --color-primary-soft: #DBEAFE;
      --color-surface: #FFFFFF;
      --color-bg-app: #F8FAFC;
      --color-border: #E2E8F0;
      --color-text-main: #0F172A;
      --color-text-muted: #64748B;
    }

    body {
      background-color: var(--color-bg-app);
      font-family: "Plus Jakarta Sans", "Inter", sans-serif;
      color: var(--color-text-main);
    }
  </style>
</head>

<body class="min-h-screen flex flex-col bg-slate-50 antialiased">

  <!-- ================= TOPBAR DOKTER ================= -->
  <header class="bg-[#012D68] text-white sticky top-0 z-30 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

      <!-- Brand & Doctor Badge -->
      <div class="flex items-center gap-3">
        <a href="{{ route('doctor.dashboard') }}" class="flex items-center gap-2" title="Klinik Maristella">
          <img
            src="{{ asset('images/maristella-logo-white.png') }}"
            alt="Klinik Maristella"
            class="h-6 w-auto object-contain"
            style="max-height: 24px; max-width: 140px;"
          />
        </a>

        <div class="h-5 w-px bg-blue-400/40 hidden sm:block"></div>

        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-900/60 border border-blue-400/30 text-blue-100 text-xs font-semibold">
          Hi, {{ $doctor['name'] }}
        </span>
      </div>

      <!-- Navigation & Action Controls -->
      <div class="flex items-center gap-3">

        <!-- Doctor Profile Pill -->
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-full bg-blue-100 text-[#012D68] overflow-hidden flex items-end justify-center font-bold text-xs border border-white/60">
            @if(!empty($doctor['image']))
              <img src="{{ asset($doctor['image']) }}" alt="{{ $doctor['name'] }}" class="w-full h-full object-cover object-bottom">
            @else
              {{ $doctor['initials'] }}
            @endif
          </div>

          <div class="hidden lg:block leading-tight text-left">
            <b class="block text-xs font-bold text-white">{{ $doctor['name'] }}</b>
            <small class="block text-[10.5px] text-blue-200">{{ $doctor['specialty'] }}</small>
          </div>
        </div>

        <!-- Logout Form -->
        <form action="{{ route('logout') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="p-2 rounded-lg text-blue-200 hover:text-rose-300 hover:bg-white/10 transition-colors" title="Keluar / Logout">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- Flash Messages -->
  @if(session('success'))
    <div class="bg-emerald-600 text-white px-4 py-2.5 text-center text-xs font-semibold shadow-sm flex items-center justify-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
      </svg>
      <span>{{ session('success') }}</span>
    </div>

    <script>
      try {
        localStorage.setItem('klinik_doctor_examination_completed', Date.now().toString());
      } catch (e) {}
    </script>
  @endif

  @if($errors->any())
    <div class="bg-rose-600 text-white px-4 py-2.5 text-center text-xs font-semibold shadow-sm">
      @foreach($errors->all() as $err)
        <div>{{ $err }}</div>
      @endforeach
    </div>
  @endif

  <!-- ================= MAIN DOCTOR WORKSPACE ================= -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 w-full flex-1">

    <!-- Doctor Summary Banner -->
    <div class="bg-white rounded-2xl p-5 mb-6 border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-4">

      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-[#012D68]">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
        </div>

        <div>
          <h1 class="text-lg font-bold text-slate-900 leading-tight">Jadwal Praktik &amp; Antrean Poliklinik</h1>
          <p class="text-xs text-slate-500 mt-0.5">
            {{ now()->isoFormat('dddd, D MMMM Y') }} · {{ $doctor['hospital'] }}
          </p>
        </div>
      </div>

      <!-- Quick Metrics Counters -->
      <div class="flex items-center gap-2 sm:gap-4 flex-wrap">

        <div class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-center min-w-[80px]">
          <small class="text-[10.5px] text-slate-400 font-semibold block uppercase">Total</small>
          <b class="text-base font-bold text-slate-900">{{ $stats['total'] }}</b>
        </div>

        <div class="bg-amber-50 border border-amber-200/80 rounded-xl px-3.5 py-2 text-center min-w-[80px]">
          <small class="text-[10.5px] text-amber-600 font-semibold block uppercase">Menunggu</small>
          <b class="text-base font-bold text-amber-700">{{ $stats['waiting'] }}</b>
        </div>

        <div class="bg-blue-50 border border-blue-200/80 rounded-xl px-3.5 py-2 text-center min-w-[80px]">
          <small class="text-[10.5px] text-blue-600 font-semibold block uppercase">Diperiksa</small>
          <b class="text-base font-bold text-[#012D68]">{{ $stats['in_progress'] }}</b>
        </div>

        <div class="bg-emerald-50 border border-emerald-200/80 rounded-xl px-3.5 py-2 text-center min-w-[80px]">
          <small class="text-[10.5px] text-emerald-600 font-semibold block uppercase">Selesai</small>
          <b class="text-base font-bold text-emerald-700">{{ $stats['completed'] }}</b>
        </div>

      </div>
    </div>

    <!-- 2-Column Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

      <!-- ============================================================ -->
      <!-- USE CASE 1: LIHAT ANTREAN HARIAN -->
      <!-- ============================================================ -->
      <aside class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs sticky top-20">

        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#012D68]"></span>
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">Antrean Harian Pasien</h2>
          </div>

          <span class="text-xs bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded-full">
            {{ count($queues) }} Pasien
          </span>
        </div>

        <!-- Filter Queue Pills -->
        <div class="flex gap-1.5 mb-3" id="queue-filter-buttons">

          <button type="button" onclick="filterQueues('all')" class="queue-filter-btn active text-[11px] px-2.5 py-1 rounded-lg font-semibold bg-[#012D68] text-white">
            Semua
          </button>

          <button type="button" onclick="filterQueues('waiting')" class="queue-filter-btn text-[11px] px-2.5 py-1 rounded-lg font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200">
            Menunggu ({{ $stats['waiting'] }})
          </button>

          <button type="button" onclick="filterQueues('completed')" class="queue-filter-btn text-[11px] px-2.5 py-1 rounded-lg font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200">
            Selesai ({{ $stats['completed'] }})
          </button>

        </div>

        <!-- Queue Cards List -->
        <div class="space-y-2.5 max-h-[640px] overflow-y-auto pr-1" id="queue-cards-list">

          @foreach($queues as $q)

            @php
              $isActive = ($q['id'] === $activeQueueId);

              $statusBadge = match($q['status']) {
                'in_progress' => [
                  'bg' => 'bg-blue-50',
                  'text' => 'text-[#012D68]',
                  'border' => 'border-blue-200',
                  'label' => 'Sedang Diperiksa',
                  'dot' => 'bg-blue-600 animate-ping'
                ],
                'completed' => [
                  'bg' => 'bg-emerald-50',
                  'text' => 'text-emerald-700',
                  'border' => 'border-emerald-200',
                  'label' => 'Selesai',
                  'dot' => 'bg-emerald-500'
                ],
                default => [
                  'bg' => 'bg-amber-50',
                  'text' => 'text-amber-700',
                  'border' => 'border-amber-200',
                  'label' => 'Menunggu',
                  'dot' => 'bg-amber-500'
                ],
              };
            @endphp

            <a
              href="{{ route('doctor.dashboard', ['queue_id' => $q['id']]) }}"
              class="queue-card block p-3 rounded-xl border transition-all text-left no-underline {{ $isActive ? 'bg-blue-50/70 border-[#012D68] shadow-xs' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}"
              data-status="{{ $q['status'] }}"
            >

              <div class="flex items-center justify-between mb-1.5">

                <div class="flex items-center gap-2">
                  <span class="font-bold text-xs px-2 py-0.5 rounded-md {{ $isActive ? 'bg-[#012D68] text-white' : 'bg-slate-100 text-slate-700' }}">
                    {{ $q['queue_no'] }}
                  </span>

                  <span class="text-[11px] text-slate-500 font-medium">
                    {{ $q['time_slot'] }}
                  </span>
                </div>

                <span class="inline-flex items-center gap-1 text-[10.5px] font-semibold px-2 py-0.5 rounded-full {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} border {{ $statusBadge['border'] }}">
                  <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                  {{ $statusBadge['label'] }}
                </span>

              </div>

              <!-- Patient Info -->
              <div class="flex items-center gap-2.5">

                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs flex-shrink-0 border border-slate-200">
                  {{ $q['patient']['initials'] }}
                </div>

                <div class="min-w-0 flex-1">
                  <b class="text-xs font-bold text-slate-900 block truncate">
                    {{ $q['patient']['name'] }}
                  </b>

                  <small class="text-[11px] text-slate-500 block truncate">
                    {{ $q['patient']['gender'] }} · {{ $q['patient']['age'] }}
                  </small>
                </div>

              </div>

              <!-- Symptoms Preview -->
              <div class="mt-2 pt-2 border-t border-slate-100 text-[11.5px] text-slate-600 line-clamp-1">
                <span class="font-medium text-slate-800">Keluhan:</span>
                {{ $q['complaint']['symptoms'] }}
              </div>

            </a>

          @endforeach

        </div>
      </aside>

      <!-- ============================================================ -->
      <!-- RIGHT COLUMN: WORKSPACE EXAMINATION AREA -->
      <!-- ============================================================ -->
      <section class="lg:col-span-8 space-y-6">

        <!-- Active Patient Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">

          <div class="flex flex-wrap items-start justify-between gap-4">

            <div class="flex items-center gap-3.5">

              <div class="w-14 h-14 rounded-2xl bg-[#012D68] text-white flex items-center justify-center font-bold text-lg shadow-sm">
                {{ $activeQueue['patient']['initials'] }}
              </div>

              <div>
                <div class="flex items-center gap-2 flex-wrap">

                  <h2 class="text-base font-bold text-slate-900">
                    {{ $activeQueue['patient']['name'] }}
                  </h2>

                  <span class="text-xs font-semibold px-2 py-0.5 bg-blue-50 text-[#012D68] rounded-md border border-blue-200">
                    {{ $activeQueue['patient']['mrn'] }}
                  </span>

                  @if(!empty($activeQueue['patient']['allergies']) && !in_array('Tidak ada', $activeQueue['patient']['allergies']))
                    <span class="text-xs font-bold px-2 py-0.5 bg-rose-50 text-rose-700 rounded-md border border-rose-200 flex items-center gap-1" title="Alergi Pasien">
                      ⚠️ Alergi: {{ implode(', ', $activeQueue['patient']['allergies']) }}
                    </span>
                  @endif

                </div>

                <p class="text-xs text-slate-500 mt-1">
                  {{ $activeQueue['patient']['gender'] }} ·
                  {{ $activeQueue['patient']['age'] }}
                  (Lahir {{ $activeQueue['patient']['dob'] }}) ·
                  Golongan Darah: <b>{{ $activeQueue['patient']['blood_type'] }}</b>
                </p>

              </div>
            </div>

            <!-- Queue Token Status -->
            <div class="flex items-center gap-2">
              <span class="text-xs px-3 py-1.5 rounded-xl font-bold bg-[#012D68] text-white shadow-xs">
                Antrean {{ $activeQueue['queue_no'] }}
              </span>
            </div>

          </div>

          <!-- Use Case Tab Bar -->
          <div class="flex border-b border-slate-200 mt-5 gap-2" role="tablist">

            <button type="button" onclick="switchMainTab('soap')" id="tab-btn-soap" class="main-tab-btn active px-4 py-2 text-xs font-bold border-b-2 border-[#012D68] text-[#012D68] flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              <span>Isi Rekam Medis SOAP</span>
            </button>

            <button type="button" onclick="switchMainTab('patient-data')" id="tab-btn-patient-data" class="main-tab-btn px-4 py-2 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <span>Lihat Data Pasien</span>
            </button>

            <button type="button" onclick="switchMainTab('medical-history')" id="tab-btn-medical-history" class="main-tab-btn px-4 py-2 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Lihat Riwayat Medis</span>
            </button>

          </div>
        </div>

        <!-- ============================================================ -->
        <!-- TAB 1: ISI REKAM MEDIS SOAP -->
        <!-- ============================================================ -->
        <div id="tab-content-soap" class="tab-pane">

          <form action="{{ route('doctor.saveSoap', $activeQueue['id']) }}" method="POST" class="space-y-5">
            @csrf

            <!-- Patient Complaint Reference -->
            <div class="bg-blue-50/60 border border-blue-200/80 rounded-2xl p-4">

              <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-bold text-[#012D68] flex items-center gap-1.5">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Keluhan Awal Saat Pendaftaran Booking:
                </span>

                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-white text-slate-700 border border-blue-200">
                  Durasi: <b>{{ $activeQueue['complaint']['duration'] }}</b> ·
                  Skala: <b>{{ $activeQueue['complaint']['severity'] }}</b>
                </span>
              </div>

              <p class="text-xs text-slate-700 italic">
                "{{ $activeQueue['complaint']['symptoms'] }}"
              </p>

              @if(!empty($activeQueue['complaint']['notes']))
                <small class="text-[11px] text-slate-500 block mt-1">
                  Catatan Tambahan Pasien: {{ $activeQueue['complaint']['notes'] }}
                </small>
              @endif

            </div>

            <!-- SECTION S: SUBJEKTIF -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">

              <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 rounded-lg bg-[#012D68] text-white flex items-center justify-center font-bold text-xs">S</span>
                <h3 class="text-sm font-bold text-slate-900 uppercase">
                  Subjektif (Anamnesis &amp; Keluhan Pasien)
                </h3>
              </div>

              <div>
                <label for="subjective" class="block text-xs font-semibold text-slate-700 mb-1">
                  Keluhan Utama &amp; Riwayat Perjalanan Penyakit (RPS)
                  <span class="text-rose-500">*</span>
                </label>

                <textarea
                  id="subjective"
                  name="subjective"
                  rows="3"
                  required
                  placeholder="Tuliskan hasil tanya jawab anamnesis, onset, frekuensi, pemicu nyeri..."
                  class="w-full text-xs p-3 rounded-xl border border-slate-200 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                >{{ old('subjective', $activeQueue['soap']['subjective'] ?? $activeQueue['complaint']['symptoms']) }}</textarea>
              </div>

            </div>

            <!-- SECTION O: OBJEKTIF -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">

              <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 rounded-lg bg-[#012D68] text-white flex items-center justify-center font-bold text-xs">O</span>
                <h3 class="text-sm font-bold text-slate-900 uppercase">
                  Objektif (Tanda Vital &amp; Pemeriksaan Fisik)
                </h3>
              </div>

              <!-- Vital Signs Grid -->
              <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2.5 mb-4">

                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                  <label class="block text-[10px] text-slate-500 font-semibold uppercase">TD (mmHg)</label>
                  <input type="text" name="bp" value="{{ old('bp', $activeQueue['soap']['objective']['bp'] ?? '') }}" placeholder="120/80" class="w-full text-xs font-bold text-slate-900 bg-transparent border-0 outline-none p-0 mt-0.5">
                </div>

                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                  <label class="block text-[10px] text-slate-500 font-semibold uppercase">Nadi (x/m)</label>
                  <input type="text" name="hr" value="{{ old('hr', $activeQueue['soap']['objective']['hr'] ?? '') }}" placeholder="80" class="w-full text-xs font-bold text-slate-900 bg-transparent border-0 outline-none p-0 mt-0.5">
                </div>

                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                  <label class="block text-[10px] text-slate-500 font-semibold uppercase">RR (x/m)</label>
                  <input type="text" name="rr" value="{{ old('rr', $activeQueue['soap']['objective']['rr'] ?? '') }}" placeholder="18" class="w-full text-xs font-bold text-slate-900 bg-transparent border-0 outline-none p-0 mt-0.5">
                </div>

                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                  <label class="block text-[10px] text-slate-500 font-semibold uppercase">Suhu (°C)</label>
                  <input type="text" name="temp" value="{{ old('temp', $activeQueue['soap']['objective']['temp'] ?? '') }}" placeholder="36.5" class="w-full text-xs font-bold text-slate-900 bg-transparent border-0 outline-none p-0 mt-0.5">
                </div>

                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                  <label class="block text-[10px] text-slate-500 font-semibold uppercase">SpO2 (%)</label>
                  <input type="text" name="spo2" value="{{ old('spo2', $activeQueue['soap']['objective']['spo2'] ?? '') }}" placeholder="99" class="w-full text-xs font-bold text-slate-900 bg-transparent border-0 outline-none p-0 mt-0.5">
                </div>

                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                  <label class="block text-[10px] text-slate-500 font-semibold uppercase">BB (kg)</label>
                  <input type="text" name="weight" value="{{ old('weight', $activeQueue['soap']['objective']['weight'] ?? '') }}" placeholder="56" class="w-full text-xs font-bold text-slate-900 bg-transparent border-0 outline-none p-0 mt-0.5">
                </div>

                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
                  <label class="block text-[10px] text-slate-500 font-semibold uppercase">TB (cm)</label>
                  <input type="text" name="height" value="{{ old('height', $activeQueue['soap']['objective']['height'] ?? '') }}" placeholder="162" class="w-full text-xs font-bold text-slate-900 bg-transparent border-0 outline-none p-0 mt-0.5">
                </div>

              </div>

              <!-- Physical Examination Notes -->
              <div>
                <label for="physical_exam" class="block text-xs font-semibold text-slate-700 mb-1">
                  Hasil Pemeriksaan Fisik &amp; Status Lokalis
                </label>

                <textarea
                  id="physical_exam"
                  name="physical_exam"
                  rows="2"
                  placeholder="Kesadaran, kepala/leher, thoraks, abdomen, ekstremitas, pemeriksaan neurologis..."
                  class="w-full text-xs p-3 rounded-xl border border-slate-200 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                >{{ old('physical_exam', $activeQueue['soap']['objective']['physical_exam'] ?? '') }}</textarea>
              </div>

            </div>

            <!-- SECTION A: ASESMEN / DIAGNOSIS -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">

              <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 rounded-lg bg-[#012D68] text-white flex items-center justify-center font-bold text-xs">A</span>
                <h3 class="text-sm font-bold text-slate-900 uppercase">
                  Asesmen (Diagnosis Kerja / ICD-10)
                </h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">

                <div>
                  <label for="primary_diagnosis" class="block text-xs font-semibold text-slate-700 mb-1">
                    Diagnosis Utama (ICD-10) <span class="text-rose-500">*</span>
                  </label>

                  <input
                    type="text"
                    id="primary_diagnosis"
                    name="primary_diagnosis"
                    required
                    value="{{ old('primary_diagnosis', $activeQueue['soap']['assessment']['primary_diagnosis'] ?? '') }}"
                    placeholder="Contoh: G43.1 - Migraine with aura"
                    class="w-full text-xs p-3 rounded-xl border border-slate-200 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                  >
                </div>

                <div>
                  <label for="secondary_diagnosis" class="block text-xs font-semibold text-slate-700 mb-1">
                    Diagnosis Sekunder / Banding
                  </label>

                  <input
                    type="text"
                    id="secondary_diagnosis"
                    name="secondary_diagnosis"
                    value="{{ old('secondary_diagnosis', $activeQueue['soap']['assessment']['secondary_diagnosis'] ?? '') }}"
                    placeholder="Diagnosis pembanding (opsional)"
                    class="w-full text-xs p-3 rounded-xl border border-slate-200 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                  >
                </div>

              </div>

              <div>
                <label for="clinical_notes" class="block text-xs font-semibold text-slate-700 mb-1">
                  Catatan Klinis Dokter
                </label>

                <input
                  type="text"
                  id="clinical_notes"
                  name="clinical_notes"
                  value="{{ old('clinical_notes', $activeQueue['soap']['assessment']['clinical_notes'] ?? '') }}"
                  placeholder="Catatan perkembangan atau observasi tambahan"
                  class="w-full text-xs p-3 rounded-xl border border-slate-200 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                >
              </div>

            </div>

            <!-- ============================================================ -->
            <!-- SECTION P: PLAN (RENCANA TERAPI & RESEP) -->
            <!-- ============================================================ -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">

              <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 rounded-lg bg-[#012D68] text-white flex items-center justify-center font-bold text-xs">P</span>

                <h3 class="text-sm font-bold text-slate-900 uppercase">
                  Plan (Rencana Terapi, Resep &amp; Tindakan)
                </h3>
              </div>

              <!-- Pencarian Obat -->
              <div class="mb-4">

                <label for="search_obat" class="block text-xs font-semibold text-slate-700 mb-1">
                  Cari Obat dari Database Klinik
                </label>

                <div class="relative">
                  <input
                    type="text"
                    id="search_obat"
                    autocomplete="off"
                    placeholder="Ketik nama obat, kode obat, atau kategori (misal: Paracetamol, Amoxicillin)..."
                    data-search-url="{{ route('doctor.obat.search') }}"
                    class="w-full text-xs p-3 pr-10 rounded-xl border border-slate-200 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                  >
                  <div class="absolute right-3 top-3 text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                  </div>
                </div>

                <p class="text-[11px] text-slate-400 mt-1">
                  Ketik minimal 2 karakter untuk mencari obat dari database klinik.
                </p>

                <!-- Hasil Pencarian -->
                <div
                  id="hasil-pencarian-obat"
                  class="mt-2 space-y-2 max-h-60 overflow-y-auto"
                  aria-live="polite"
                ></div>

              </div>

              <!-- Daftar Resep Obat Terpilih -->
              <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-xs font-bold text-slate-800">
                    Daftar Resep Obat Pasien (Tersinkron ke Apoteker)
                  </label>
                  <span id="selected-meds-count" class="text-[11px] font-semibold text-slate-500">0 obat dipilih</span>
                </div>

                <!-- Hidden Input for structured JSON submitted to server -->
                <input type="hidden" name="medications_json" id="medications_json" value="">

                <!-- Selected Medications Container -->
                <div id="prescribed-medications-list" class="space-y-2.5">
                  <!-- Rendered dynamically by JavaScript -->
                </div>

                <!-- Empty State -->
                <div id="empty-meds-alert" class="p-3.5 rounded-xl border border-dashed border-slate-200 bg-slate-50/70 text-center text-xs text-slate-500">
                  Belum ada obat yang dipilih dari database. Cari obat di atas dan klik <b>"+ Tambah ke Resep"</b>.
                </div>
              </div>

              <!-- Resep Obat / Terapi Farmakologi -->
              <div class="mb-3">

                <label for="therapy_plan" class="block text-xs font-semibold text-slate-700 mb-1 flex items-center justify-between">
                  <span>Ringkasan / Catatan Terapi Farmakologi</span>
                  <span class="text-[10px] text-slate-400 font-normal">Tersinkron otomatis</span>
                </label>

                @php
                  $medLines = [];

                  if (!empty($activeQueue['soap']['plan']['medications'])) {
                    foreach ($activeQueue['soap']['plan']['medications'] as $m) {
                      $medLines[] = ($m['name'] ?? 'Obat').' — '.($m['quantity'] ?? 10).' '.($m['satuan'] ?? 'item').' · ('.($m['frequency'] ?? '3x1').')';
                    }
                  }

                  $medText = implode("\n", $medLines);
                @endphp

                <textarea
                  id="therapy_plan"
                  name="therapy_plan"
                  rows="3"
                  placeholder="Obat yang dipilih akan muncul di sini beserta aturan pakainya."
                  class="w-full text-xs p-3 rounded-xl border border-slate-200 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all font-mono"
                >{{ old('therapy_plan', $medText ?? '') }}</textarea>

                <p class="text-[11px] text-slate-400 mt-1">
                  Resep akan langsung diteruskan ke Apoteker dengan status <b>Perlu Diproses</b> setelah disimpan.
                </p>

              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                <div>
                  <label for="return_date" class="block text-xs font-semibold text-slate-700 mb-1">
                    Rencana Tanggal Kontrol Ulang
                  </label>

                  <input
                    type="text"
                    id="return_date"
                    name="return_date"
                    value="{{ old('return_date', $activeQueue['soap']['plan']['return_date'] ?? '') }}"
                    placeholder="Contoh: 18 Nov 2026 atau 14 hari ke depan"
                    class="w-full text-xs p-3 rounded-xl border border-slate-200 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                  >
                </div>

                <div class="flex items-end">
                  <div class="text-[11px] text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-200 w-full">
                    💡 Setelah disimpan, status antrean pasien akan otomatis menjadi <b>Selesai</b>.
                  </div>
                </div>

              </div>
            </div>

            <!-- Submit CTA -->
            <div class="flex items-center justify-end gap-3 pt-2">

              <button type="submit" class="px-6 py-3 rounded-xl bg-[#012D68] hover:bg-[#083E88] text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>

                <span>Simpan Rekam Medis SOAP &amp; Selesaikan Sesi</span>

              </button>

            </div>

          </form>
        </div>

        <!-- ============================================================ -->
        <!-- TAB 2: LIHAT DATA PASIEN -->
        <!-- ============================================================ -->
        <div id="tab-content-patient-data" class="tab-pane hidden space-y-5">

          <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">

            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">

              <h3 class="text-sm font-bold text-slate-900 uppercase">
                Data Demografis &amp; Profil Pasien
              </h3>

              <span class="text-xs bg-blue-50 text-[#012D68] font-bold px-2.5 py-1 rounded-full border border-blue-200">
                Terverifikasi
              </span>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">

              <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <small class="text-slate-400 font-semibold block uppercase text-[10px]">Nama Lengkap</small>
                <b class="text-slate-900 text-sm mt-0.5 block">{{ $activeQueue['patient']['name'] }}</b>
              </div>

              <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <small class="text-slate-400 font-semibold block uppercase text-[10px]">Nomor Rekam Medis (MRN)</small>
                <b class="text-slate-900 text-sm mt-0.5 block">{{ $activeQueue['patient']['mrn'] }}</b>
              </div>

              <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <small class="text-slate-400 font-semibold block uppercase text-[10px]">Nomor Induk Kependudukan (NIK)</small>
                <b class="text-slate-900 text-sm mt-0.5 block">{{ $activeQueue['patient']['nik'] }}</b>
              </div>

              <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <small class="text-slate-400 font-semibold block uppercase text-[10px]">Tanggal Lahir &amp; Usia</small>
                <b class="text-slate-900 text-sm mt-0.5 block">{{ $activeQueue['patient']['dob'] }} ({{ $activeQueue['patient']['age'] }})</b>
              </div>

              <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <small class="text-slate-400 font-semibold block uppercase text-[10px]">Jenis Kelamin &amp; Golongan Darah</small>
                <b class="text-slate-900 text-sm mt-0.5 block">
                  {{ $activeQueue['patient']['gender'] }} · Gol. Darah {{ $activeQueue['patient']['blood_type'] }}
                </b>
              </div>

              <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <small class="text-slate-400 font-semibold block uppercase text-[10px]">No. WhatsApp / Telepon</small>
                <b class="text-slate-900 text-sm mt-0.5 block">{{ $activeQueue['patient']['phone'] }}</b>
              </div>

              <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 md:col-span-2">
                <small class="text-slate-400 font-semibold block uppercase text-[10px]">Alamat Domisili</small>
                <b class="text-slate-900 text-sm mt-0.5 block">{{ $activeQueue['patient']['address'] }}</b>
              </div>

            </div>

            <!-- Health Flags -->
            <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4">

              <div class="p-4 rounded-xl bg-rose-50/70 border border-rose-200">

                <span class="text-xs font-bold text-rose-800 flex items-center gap-1.5 mb-1.5">
                  <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                  Riwayat Alergi Pasien
                </span>

                @if(!empty($activeQueue['patient']['allergies']))
                  <ul class="list-disc list-inside text-xs text-rose-900 space-y-0.5 font-medium">
                    @foreach($activeQueue['patient']['allergies'] as $allergy)
                      <li>{{ $allergy }}</li>
                    @endforeach
                  </ul>
                @else
                  <p class="text-xs text-slate-500">Tidak ada riwayat alergi yang dilaporkan.</p>
                @endif

              </div>

              <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">

                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5 mb-1.5">
                  <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                  Riwayat Penyakit Terdahulu &amp; Obat Rutin
                </span>

                @if(!empty($activeQueue['patient']['past_illnesses']))
                  <ul class="list-disc list-inside text-xs text-slate-700 space-y-0.5">
                    @foreach($activeQueue['patient']['past_illnesses'] as $illness)
                      <li>{{ $illness }}</li>
                    @endforeach
                  </ul>
                @else
                  <p class="text-xs text-slate-500">Tidak ada data penyakit terdahulu.</p>
                @endif

              </div>

            </div>

          </div>
        </div>

        <!-- ============================================================ -->
        <!-- TAB 3: LIHAT RIWAYAT MEDIS -->
        <!-- ============================================================ -->
        <div id="tab-content-medical-history" class="tab-pane hidden space-y-4">

          <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">

            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">

              <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase">
                  Riwayat Kunjungan &amp; Rekam Medis Masa Lalu
                </h3>

                <p class="text-xs text-slate-500 mt-0.5">
                  Daftar pemeriksaan klinis pasien {{ $activeQueue['patient']['name'] }} sebelumnya
                </p>
              </div>

            </div>

            @if(!empty($activeQueue['history']) && count($activeQueue['history']) > 0)

              <div class="relative border-l-2 border-blue-200 pl-4 space-y-6 ml-2 my-2">

                @foreach($activeQueue['history'] as $item)

                  <div class="relative">

                    <!-- Timeline Node Dot -->
                    <span class="absolute -left-[23px] top-1 w-3.5 h-3.5 rounded-full bg-[#012D68] border-2 border-white"></span>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 shadow-2xs">

                      <div class="flex items-center justify-between mb-1.5 flex-wrap gap-2">

                        <span class="text-xs font-bold text-[#012D68]">
                          {{ $item['date'] }}
                        </span>

                        <span class="text-[11px] text-slate-500 font-medium">
                          Pemeriksa: <b>{{ $item['doctor'] }}</b>
                        </span>

                      </div>

                      <div class="text-xs text-slate-800 font-semibold mb-1">
                        Diagnosis:
                        <span class="text-slate-900 font-bold">{{ $item['diagnosis'] }}</span>
                      </div>

                      <div class="text-xs text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200/80 mt-2">
                        <span class="font-medium text-slate-700 block text-[11px]">
                          Terapi &amp; Pengobatan Diberikan:
                        </span>
                        {{ $item['therapy'] }}
                      </div>

                    </div>
                  </div>

                @endforeach

              </div>

            @else

              <div class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">

                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>

                <b class="text-xs text-slate-700 block">
                  Belum Ada Riwayat Kunjungan Sebelumnya
                </b>

                <p class="text-[11px] text-slate-400 mt-0.5">
                  Ini adalah sesi konsultasi pertama pasien di Klinik Maristella.
                </p>

              </div>

            @endif

          </div>
        </div>

      </section>
    </div>
  </main>

  <!-- ============================================================ -->
  <!-- JAVASCRIPT: TABS, FILTER ANTREAN, DAN PENCARIAN OBAT -->
  <!-- ============================================================ -->
  <script>

    // ==========================================
    // SWITCH TAB DOKTER
    // ==========================================
    function switchMainTab(tabName) {

      // Hide all panes
      document.querySelectorAll('.tab-pane').forEach(el => {
        el.classList.add('hidden');
      });

      document.querySelectorAll('.main-tab-btn').forEach(btn => {
        btn.classList.remove(
          'active',
          'border-[#012D68]',
          'text-[#012D68]'
        );

        btn.classList.add(
          'border-transparent',
          'text-slate-500'
        );
      });

      // Show selected pane
      const targetPane = document.getElementById('tab-content-' + tabName);
      const targetBtn = document.getElementById('tab-btn-' + tabName);

      if (targetPane) {
        targetPane.classList.remove('hidden');
      }

      if (targetBtn) {
        targetBtn.classList.add(
          'active',
          'border-[#012D68]',
          'text-[#012D68]'
        );

        targetBtn.classList.remove(
          'border-transparent',
          'text-slate-500'
        );
      }
    }


    // ==========================================
    // FILTER ANTREAN PASIEN
    // ==========================================
    function filterQueues(status) {

      const buttons = document.querySelectorAll('.queue-filter-btn');

      buttons.forEach(btn => {
        btn.classList.remove(
          'bg-[#012D68]',
          'text-white'
        );

        btn.classList.add(
          'bg-slate-100',
          'text-slate-600'
        );
      });

      // Menggunakan tombol yang diklik
      const clickedButton = Array.from(buttons).find(btn => {
        return btn.getAttribute('onclick') === `filterQueues('${status}')`;
      });

      if (clickedButton) {
        clickedButton.classList.add(
          'bg-[#012D68]',
          'text-white'
        );

        clickedButton.classList.remove(
          'bg-slate-100',
          'text-slate-600'
        );
      }

      const cards = document.querySelectorAll('.queue-card');

      cards.forEach(card => {

        if (status === 'all') {
          card.style.display = 'block';
        } else {
          card.style.display =
            (card.dataset.status === status) ? 'block' : 'none';
        }

      });
    }


    // ==========================================
    // MANAJEMEN RESEP & PENCARIAN OBAT DOKTER
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {

      const searchInput = document.getElementById('search_obat');
      const resultContainer = document.getElementById('hasil-pencarian-obat');
      const therapyPlan = document.getElementById('therapy_plan');
      const medsListEl = document.getElementById('prescribed-medications-list');
      const emptyAlert = document.getElementById('empty-meds-alert');
      const medsCountEl = document.getElementById('selected-meds-count');
      const medsJsonInput = document.getElementById('medications_json');

      if (!searchInput || !resultContainer || !therapyPlan) {
        return;
      }

      // Initial data dari server (jika sudah ada rekam medis sebelumnya)
      const rawInitialMeds = @json($activeQueue['soap']['plan']['medications'] ?? []);
      let selectedMedications = [];

      if (Array.isArray(rawInitialMeds) && rawInitialMeds.length > 0) {
        selectedMedications = rawInitialMeds.map((m, idx) => ({
          id: 'init_' + idx + '_' + Date.now(),
          obat_id: m.obat_id || null,
          kode_obat: m.kode_obat || '',
          name: m.name || 'Obat',
          satuan: m.satuan || 'Item',
          stok: m.stok || 100,
          quantity: parseInt(m.quantity || m.qty || 10),
          frequency: m.frequency || '3x1 sehari sesudah makan',
          custom_frequency: '',
          dose: m.dose || '1 tablet',
          notes: m.notes || ''
        }));
      }

      // Common frequency presets
      const FREQ_OPTIONS = [
        '3x1 sehari sesudah makan',
        '2x1 sehari sesudah makan',
        '1x1 sehari sesudah makan',
        '3x1 sehari sebelum makan',
        '2x1 sehari sebelum makan',
        '1x1 malam sebelum tidur',
        'Bila demam / nyeri (PRN)',
        'Oles tipis pada area sakit',
        'Tetes pada mata/telinga',
        'Kustom / Aturan Lain...'
      ];

      function syncToForm() {
        if (medsJsonInput) {
          medsJsonInput.value = JSON.stringify(selectedMedications);
        }

        if (medsCountEl) {
          medsCountEl.textContent = `${selectedMedications.length} obat dipilih`;
        }

        if (emptyAlert) {
          emptyAlert.style.display = selectedMedications.length === 0 ? 'block' : 'none';
        }

        // Sinkronisasi teks therapy_plan
        if (selectedMedications.length > 0) {
          const lines = selectedMedications.map(m => {
            const freq = m.frequency === 'Kustom / Aturan Lain...' && m.custom_frequency ? m.custom_frequency : m.frequency;
            return `${m.name} — ${m.quantity} ${m.satuan || 'item'} · (${freq}, ${m.dose || '1 tablet'})`;
          });
          therapyPlan.value = lines.join('\n');
        }
      }

      function renderPrescribedList() {
        if (!medsListEl) return;
        medsListEl.innerHTML = '';

        selectedMedications.forEach((med, index) => {
          const card = document.createElement('div');
          card.className = 'border border-slate-200 rounded-xl p-3.5 bg-slate-50/80 hover:bg-white hover:border-[#012D68]/30 transition-all shadow-xs';
          card.dataset.id = med.id;

          // Header: Nama obat, kode, sisa stok, & tombol hapus
          const header = document.createElement('div');
          header.className = 'flex items-center justify-between gap-2 pb-2.5 mb-2.5 border-b border-slate-200/80';
          header.innerHTML = `
            <div class="flex items-center gap-2 flex-wrap">
              <span class="w-2 h-2 rounded-full bg-[#012D68]"></span>
              <strong class="text-xs font-bold text-slate-900">${med.name}</strong>
              ${med.kode_obat ? `<span class="font-mono text-[10px] bg-slate-200 text-slate-700 font-bold px-1.5 py-0.5 rounded">${med.kode_obat}</span>` : ''}
              ${med.stok !== undefined ? `<span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded font-medium">Stok: ${med.stok}</span>` : ''}
            </div>
            <button type="button" class="btn-remove-med text-red-500 hover:text-red-700 hover:bg-red-50 p-1 rounded-lg transition-colors" title="Hapus dari resep">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          `;

          // Inputs Row
          const inputsGrid = document.createElement('div');
          inputsGrid.className = 'grid grid-cols-1 sm:grid-cols-3 gap-2.5';

          // Kolom 1: Jumlah / Qty
          const colQty = document.createElement('div');
          colQty.innerHTML = `
            <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Jumlah (${med.satuan || 'Item'})</label>
            <div class="flex items-center gap-1.5">
              <input type="number" min="1" class="inp-med-qty w-full text-xs p-2 rounded-lg border border-slate-200 focus:border-[#012D68] focus:ring-1 focus:ring-blue-100 bg-white" value="${med.quantity || 10}">
            </div>
          `;

          // Kolom 2: Berapa kali penggunaan obat (Frekuensi)
          const colFreq = document.createElement('div');
          const isCustomFreq = !FREQ_OPTIONS.includes(med.frequency) || med.frequency === 'Kustom / Aturan Lain...';
          const selectedFreq = isCustomFreq ? 'Kustom / Aturan Lain...' : med.frequency;

          let freqOptionsHtml = FREQ_OPTIONS.map(opt => `<option value="${opt}" ${opt === selectedFreq ? 'selected' : ''}>${opt}</option>`).join('');

          colFreq.innerHTML = `
            <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Berapa Kali Penggunaan / Aturan</label>
            <select class="sel-med-freq w-full text-xs p-2 rounded-lg border border-slate-200 focus:border-[#012D68] focus:ring-1 focus:ring-blue-100 bg-white">
              ${freqOptionsHtml}
            </select>
            <input type="text" placeholder="Tuliskan frekuensi kustom..." class="inp-custom-freq mt-1.5 w-full text-xs p-2 rounded-lg border border-amber-300 focus:border-[#012D68] bg-amber-50/50 ${isCustomFreq ? '' : 'hidden'}" value="${isCustomFreq ? (med.custom_frequency || med.frequency) : ''}">
          `;

          // Kolom 3: Dosis / Petunjuk Khusus
          const colDose = document.createElement('div');
          colDose.innerHTML = `
            <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Dosis / Petunjuk Khusus</label>
            <input type="text" placeholder="mis. 1 tablet / 1 sendok takar" class="inp-med-dose w-full text-xs p-2 rounded-lg border border-slate-200 focus:border-[#012D68] focus:ring-1 focus:ring-blue-100 bg-white" value="${med.dose || '1 tablet'}">
          `;

          inputsGrid.append(colQty, colFreq, colDose);
          card.append(header, inputsGrid);
          medsListEl.appendChild(card);

          // Event Listeners pada item
          card.querySelector('.btn-remove-med').addEventListener('click', () => {
            selectedMedications = selectedMedications.filter(item => item.id !== med.id);
            renderPrescribedList();
            syncToForm();
          });

          const qtyInput = card.querySelector('.inp-med-qty');
          qtyInput.addEventListener('input', (e) => {
            med.quantity = Math.max(1, parseInt(e.target.value) || 1);
            syncToForm();
          });

          const freqSelect = card.querySelector('.sel-med-freq');
          const customFreqInput = card.querySelector('.inp-custom-freq');
          freqSelect.addEventListener('change', (e) => {
            med.frequency = e.target.value;
            if (e.target.value === 'Kustom / Aturan Lain...') {
              customFreqInput.classList.remove('hidden');
              customFreqInput.focus();
            } else {
              customFreqInput.classList.add('hidden');
            }
            syncToForm();
          });

          customFreqInput.addEventListener('input', (e) => {
            med.custom_frequency = e.target.value;
            syncToForm();
          });

          const doseInput = card.querySelector('.inp-med-dose');
          doseInput.addEventListener('input', (e) => {
            med.dose = e.target.value;
            syncToForm();
          });
        });

        syncToForm();
      }

      // Initial render
      renderPrescribedList();

      let searchTimeout;

      searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const keyword = this.value.trim();

        if (keyword.length < 2) {
          resultContainer.replaceChildren();
          return;
        }

        resultContainer.innerHTML = '<div class="p-3 text-xs text-slate-500 bg-slate-50 rounded-xl border border-slate-200 flex items-center gap-2"><svg class="animate-spin h-3.5 w-3.5 text-[#012D68]" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Mencari obat di database klinik...</div>';

        searchTimeout = setTimeout(async function () {
          try {
            const response = await fetch(
              searchInput.dataset.searchUrl + '?q=' + encodeURIComponent(keyword),
              {
                headers: {
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                }
              }
            );

            if (!response.ok) {
              throw new Error('Gagal mengambil data obat.');
            }

            const obats = await response.json();
            resultContainer.replaceChildren();

            if (!Array.isArray(obats) || obats.length === 0) {
              resultContainer.innerHTML = '<div class="p-3 text-xs text-slate-500 bg-slate-50 rounded-xl border border-slate-200">Obat tidak ditemukan dalam database.</div>';
              return;
            }

            obats.forEach(function (obat) {
              const card = document.createElement('div');
              card.className = 'border border-slate-200 hover:border-[#012D68]/40 rounded-xl p-3 flex flex-wrap items-center justify-between gap-3 bg-white shadow-xs transition-all';

              const info = document.createElement('div');
              info.className = 'flex-1 min-w-0';

              const name = document.createElement('strong');
              name.className = 'block text-xs font-bold text-slate-900';
              name.textContent = obat.nama_obat;

              const details = document.createElement('p');
              details.className = 'text-[11px] text-slate-500 mt-0.5';
              details.innerHTML = `<span class="font-mono font-bold text-slate-700">${obat.kode_obat}</span> · Kategori: <b>${obat.kategori}</b> · Satuan: ${obat.satuan || 'Item'}`;

              const stockPrice = document.createElement('p');
              stockPrice.className = 'text-[11px] mt-1 flex items-center gap-2';
              stockPrice.innerHTML = `
                <span class="font-bold text-[#012D68]">Rp${Number(obat.harga).toLocaleString('id-ID')}</span>
                <span class="text-slate-300">|</span>
                <span class="font-semibold ${obat.stok > 10 ? 'text-emerald-600' : 'text-amber-600'}">Sisa Stok: ${obat.stok}</span>
              `;

              info.append(name, details, stockPrice);

              const button = document.createElement('button');
              button.type = 'button';
              button.className = 'px-3.5 py-2 rounded-xl bg-[#012D68] hover:bg-[#083E88] text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5';
              button.innerHTML = `
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Tambah ke Resep</span>
              `;

              button.addEventListener('click', function () {
                // Periksa apakah obat sudah ada di daftar resep
                const existing = selectedMedications.find(m => m.kode_obat === obat.kode_obat || m.obat_id === obat.id);
                if (existing) {
                  existing.quantity += 10;
                } else {
                  selectedMedications.push({
                    id: 'med_' + obat.id + '_' + Date.now(),
                    obat_id: obat.id,
                    kode_obat: obat.kode_obat,
                    name: obat.nama_obat,
                    satuan: obat.satuan || 'Strip',
                    stok: obat.stok,
                    quantity: 10,
                    frequency: '3x1 sehari sesudah makan',
                    custom_frequency: '',
                    dose: '1 tablet',
                    notes: ''
                  });
                }

                // Kosongkan pencarian
                searchInput.value = '';
                resultContainer.replaceChildren();

                // Re-render list resep
                renderPrescribedList();
              });

              card.append(info, button);
              resultContainer.appendChild(card);
            });

          } catch (error) {
            resultContainer.innerHTML = '<div class="p-3 text-xs text-red-500 bg-red-50 rounded-xl border border-red-200">Terjadi kesalahan saat mencari obat.</div>';
            console.error(error);
          }
        }, 250);
      });

    });


    // ==========================================
    // SAAT HALAMAN SELESAI DIMUAT
    // ==========================================
    window.addEventListener('DOMContentLoaded', () => {

      const tabParam =
        new URLSearchParams(window.location.search).get('tab');

      if (tabParam) {
        switchMainTab(tabParam);
      }

      @if(session('success'))
        try {
          localStorage.setItem(
            'klinik_doctor_examination_completed',
            Date.now().toString()
          );
        } catch (e) {}
      @endif

    });

  </script>

</body>
</html>
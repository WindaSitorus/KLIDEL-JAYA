@extends('layouts.app')

@section('content')
@php
    $selectedDoctor = $doctors[2] ?? $doctors[0] ?? [
        'id' => 0,
        'name' => 'Dokter Belum Dipilih',
        'specialty' => '-',
        'category' => 'all',
        'rating' => 5.0,
        'price' => 'Rp 0',
        'initials' => 'DR',
        'image' => 'images/doctors/doctor-1.png',
        'availability' => '9AM - 5PM',
        'experience' => '10 years',
        'patients' => '1k Up',
    ];
@endphp
<script>
    window.currentBookingCode = "{{ $nextAppointment['booking_code'] ?? '#BILL00124' }}";
    window.currentBookingId = {{ $latestBooking?->id ?? 'null' }};
</script>
<div class="relative w-full min-h-screen">

    <!-- ========================================== -->
    <!-- 1. DESKTOP LAYOUT (Screen width >= 1024px) -->
    <!-- ========================================== -->
    <div class="desktop-layout hidden lg:grid lg:grid-cols-[72px_1fr_340px] xl:grid-cols-[240px_1fr_360px] max-w-[1440px] w-full mx-auto min-h-screen">
        
        <!-- SIDEBAR -->
        <aside class="side bg-[var(--color-surface)] border-r border-[var(--color-border)] p-5 flex flex-col gap-1.5 sticky top-0 h-screen z-20">
            <!-- Logo (Klinik Maristella) -->
            <a href="{{ route('home') }}" class="logo flex items-center gap-2.5 mb-6 no-underline" title="Klinik Maristella">
                <img src="{{ asset('images/maristella-logo-horizontal.png') }}" alt="Klinik Maristella" class="h-9 w-auto object-contain hidden xl:block" style="max-height: 36px; max-width: 170px;" />
                <img src="{{ asset('images/maristella-icon.png') }}" alt="Klinik Maristella" class="h-9 w-auto object-contain block xl:hidden" style="max-height: 36px; max-width: 36px;" />
            </a>

            <!-- Navigation Items -->
            <nav class="flex flex-col gap-1 w-full" aria-label="Navigasi Utama">
                <!-- Beranda (Active) -->
                <button class="nav flex items-center gap-3 p-2.5 rounded-xl text-[var(--color-primary)] bg-[var(--color-primary-tint)] font-semibold relative text-left w-full transition-colors" aria-current="page">
                    <span class="hidden xl:block absolute -left-5 top-2.5 bottom-2.5 w-[3px] rounded-r bg-[var(--color-primary)]"></span>
                    <svg class="app-svg-icon text-[var(--color-primary)]"><use href="#icon-home"/></svg>
                    <span class="hidden xl:inline text-[14px]">Beranda</span>
                </button>

                <!-- Jadwal -->
                <a href="{{ route('calendar') }}" class="nav flex items-center gap-3 p-2.5 rounded-xl text-[var(--color-text-secondary)] hover:bg-[var(--color-primary-tint)]/60 text-left w-full transition-colors">
                    <svg class="app-svg-icon"><use href="#icon-cal"/></svg>
                    <span class="hidden xl:inline text-[14px]">Jadwal</span>
                </a>

                <!-- Rekam Medis -->
                <a href="{{ route('rekam-medis') }}" class="nav flex items-center gap-3 p-2.5 rounded-xl text-[var(--color-text-secondary)] hover:bg-[var(--color-primary-tint)]/60 text-left w-full transition-colors">
                    <svg class="app-svg-icon"><use href="#icon-doc"/></svg>
                    <span class="hidden xl:inline text-[14px]">Rekam Medis</span>
                </a>

                <!-- Pengaturan / Profil -->
                <a href="{{ route('profile.edit') }}" class="nav flex items-center gap-3 p-2.5 rounded-xl text-[var(--color-text-secondary)] hover:bg-[var(--color-primary-tint)]/60 text-left w-full transition-colors" title="Profil & Pengaturan">
                    <svg class="app-svg-icon"><use href="#icon-set"/></svg>
                    <span class="hidden xl:inline text-[14px]">Profil & Pengaturan</span>
                </a>

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav flex items-center gap-3 p-2.5 rounded-xl text-purple-700 bg-purple-50 hover:bg-purple-100 text-left w-full transition-colors font-medium">
                            <svg class="app-svg-icon text-purple-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span class="hidden xl:inline text-[14px]">Portal Admin</span>
                        </a>
                    @endif
                    @if (auth()->user()->isDoctor())
                        <a href="{{ route('doctor.dashboard') }}" class="nav flex items-center gap-3 p-2.5 rounded-xl text-blue-800 bg-blue-50 hover:bg-blue-100 text-left w-full transition-colors font-medium">
                            <svg class="app-svg-icon text-[#012D68]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span class="hidden xl:inline text-[14px]">Portal Dokter</span>
                        </a>
                    @endif

                    <!-- Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="nav flex items-center gap-3 p-2.5 rounded-xl text-red-600 hover:bg-red-50 text-left w-full transition-colors font-medium">
                            <svg class="app-svg-icon text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span class="hidden xl:inline text-[14px]">Keluar</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav flex items-center gap-3 p-2.5 rounded-xl text-[var(--color-primary)] bg-[var(--color-primary-tint)] hover:brightness-105 text-left w-full transition-colors font-semibold">
                        <svg class="app-svg-icon text-[var(--color-primary)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        <span class="hidden xl:inline text-[14px]">Masuk / Login</span>
                    </a>
                @endauth
            </nav>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="main-content px-8 py-6 min-w-0 flex flex-col gap-6">
            
            <!-- Top Bar -->
            <header class="top-bar flex items-center gap-4">
                <!-- Search Input Pill -->
                <div class="search-box flex-1 max-w-[480px] flex items-center gap-2.5 bg-[var(--color-surface)] border border-[var(--color-border)] rounded-full px-4 h-11 text-[var(--color-text-muted)] focus-within:border-[var(--color-primary)] focus-within:shadow-[var(--focus-ring)] transition-all">
                    <svg class="app-svg-icon w-4 h-4 text-[var(--color-text-muted)]"><use href="#icon-search"/></svg>
                    <input id="search-input" type="search" placeholder="Cari dokter atau spesialisasi..." class="flex-1 bg-transparent border-none text-[var(--color-text)] placeholder-[var(--color-text-muted)] text-[13.5px] outline-none h-full" aria-label="Cari dokter">
                </div>

                <div class="flex-1"></div>

                <!-- Theme Toggle Button -->
                <button id="theme-toggle" class="glass-btn !bg-[var(--color-surface)] !text-[var(--color-text)] !border-[var(--color-border)] hover:!border-[var(--color-primary)]" aria-label="Ubah tema gelap / terang" title="Ubah tema">
                    <svg class="app-svg-icon w-4 h-4"><use href="#icon-sun"/></svg>
                </button>

                <!-- Notification Bell with Interactive Dropdown -->
                <div class="relative" id="desktop-notif-container">
                    <button id="desktop-notif-btn" type="button" class="glass-btn !bg-[var(--color-surface)] !text-[var(--color-text)] !border-[var(--color-border)] relative hover:!border-[var(--color-primary)] transition-all" aria-label="Notifikasi" aria-expanded="false">
                        <svg class="app-svg-icon w-4 h-4"><use href="#icon-bell"/></svg>
                        <span id="desktop-notif-badge" class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-900 {{ ($unreadCount ?? 0) > 0 ? '' : 'hidden' }}"></span>
                    </button>

                    <!-- Dropdown Panel -->
                    <div id="desktop-notif-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-[#101E35] rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 z-50 overflow-hidden transition-all duration-200">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-900/50">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs text-slate-800 dark:text-white uppercase tracking-wider">Notifikasi</span>
                                <span id="desktop-notif-counter" class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ ($unreadCount ?? 0) > 0 ? 'bg-[#012D68] text-white' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                    {{ $unreadCount ?? 0 }} Baru
                                </span>
                            </div>
                            @if(($unreadCount ?? 0) > 0)
                                <button type="button" onclick="window.markAllNotificationsRead?.()" class="text-[11px] text-[#012D68] dark:text-blue-400 font-semibold hover:underline">Tandai Dibaca</button>
                            @endif
                        </div>
                        <div id="desktop-notif-list" class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 p-1">
                            @forelse(($notifications ?? []) as $notif)
                                <div class="p-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-xl transition-colors flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 
                                        @if($notif['type'] === 'bill') bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400
                                        @elseif($notif['type'] === 'appointment') bg-blue-50 text-[#012D68] dark:bg-blue-950/50 dark:text-blue-400
                                        @elseif($notif['type'] === 'pharmacy') bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400
                                        @else bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 @endif">
                                        @if($notif['type'] === 'bill')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif($notif['type'] === 'appointment')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @elseif($notif['type'] === 'pharmacy')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <b class="text-xs font-bold text-slate-900 dark:text-white truncate block">{{ $notif['title'] }}</b>
                                            <small class="text-[10px] text-slate-400 whitespace-nowrap">{{ $notif['time'] }}</small>
                                        </div>
                                        <p class="text-[11.5px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">{{ $notif['message'] }}</p>
                                        @if(!empty($notif['action_label']))
                                            <div class="mt-2">
                                                @if(!empty($notif['onclick']))
                                                    <button type="button" onclick="{{ $notif['onclick'] }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#012D68] text-white text-[11px] font-bold hover:bg-[#023E8A] transition-colors shadow-2xs">
                                                        <span>{{ $notif['action_label'] }}</span>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                    </button>
                                                @elseif(!empty($notif['action_url']))
                                                    <a href="{{ $notif['action_url'] }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#012D68] text-white text-[11px] font-bold hover:bg-[#023E8A] transition-colors shadow-2xs">
                                                        <span>{{ $notif['action_label'] }}</span>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div id="desktop-notif-empty" class="py-8 text-center">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Belum ada notifikasi baru</p>
                                    <small class="text-[11px] text-slate-400 mt-0.5 block">Notifikasi jadwal, resep, dan tagihan akan tampil di sini.</small>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Profile / Auth Action -->
                @auth
                    <a href="{{ route('profile.edit') }}" class="user-pill flex items-center gap-2.5 pl-2 hover:opacity-90 transition-opacity" title="Kelola Profil Saya">
                        <div class="avatar-initials w-11 h-11 text-[14px] overflow-hidden flex items-center justify-center">
                            @if(!empty($user['avatar_url']))
                                <img src="{{ $user['avatar_url'] }}" alt="{{ $user['name'] }}" class="w-full h-full object-cover">
                            @else
                                {{ $user['initials'] }}
                            @endif
                        </div>
                        <div class="hidden xl:block leading-tight text-left">
                            <b class="block text-[13.5px] text-[var(--color-text)]">{{ $user['name'] }}</b>
                            <small class="block text-[11.5px] text-[var(--color-text-muted)]">{{ $user['role'] }}</small>
                        </div>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary !h-11 !px-5 !py-0 flex items-center gap-2 text-[13.5px] rounded-full font-semibold shadow-sm hover:opacity-95 transition-all">
                        <svg class="app-svg-icon w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        <span>Masuk</span>
                    </a>
                @endauth
            </header>

            <!-- Greeting Banner with Integrated Next Appointment Card (Solid Klinik Maristella Navy) -->
            <section class="banner rounded-[24px] p-7 flex justify-between items-center gap-6 flex-wrap text-white shadow-sm">
                <div class="max-w-[340px]">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#001F4D] text-blue-100 border border-blue-700/60 text-xs font-semibold mb-2.5 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Layanan Prioritas Klinik Maristella
                    </div>
                    <small class="text-white/80 text-[13px] block">Selamat siang,</small>
                    <h1 class="text-[30px] font-bold leading-[1.2] mt-1 text-white">What’s on your health today?</h1>
                </div>

                <!-- Next Appointment Card (Hanya muncul jika ada janji temu aktif: sebelum periksa atau siap bayar) -->
                <div class="next-appt-card bg-white dark:bg-[#101E35] text-slate-900 dark:text-white rounded-[22px] p-4 w-[385px] max-w-full shadow-md border border-slate-100 dark:border-slate-800 transition-all duration-300 {{ $appointmentMode === 'welcome' ? 'hidden' : '' }}" data-appointment-mode="{{ $appointmentMode }}">

                    <!-- State 1: SEBELUM PERIKSA (Default / Upcoming) -->
                    <div id="desktop-state-before" class="appointment-state-panel flex flex-col justify-between min-h-[148px] {{ $appointmentMode === 'before' ? '' : 'hidden' }}" data-status="Sebelum Periksa" aria-label="Status Janji Temu: Sebelum Periksa">
                        <span class="sr-only">Sebelum Periksa</span>
                        <div class="flex items-center justify-between mb-1">
                            <small class="text-slate-400 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider">Next Appointment</small>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Upcoming
                            </span>
                        </div>
                        <div class="flex items-center gap-3 py-1">
                            <div class="w-[50px] h-[50px] rounded-[14px] bg-blue-50 dark:bg-blue-950/50 overflow-hidden flex items-end justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900">
                                <img src="{{ asset($nextAppointment['image']) }}" alt="{{ $nextAppointment['doctor_name'] }}" class="h-full w-auto object-contain object-bottom">
                            </div>
                            <div class="min-w-0 flex-1">
                                <b class="block text-[14.5px] text-slate-900 dark:text-white truncate font-bold">{{ $nextAppointment['doctor_name'] }}</b>
                                <small class="block text-slate-500 dark:text-slate-400 text-[11.5px] mt-0.5 truncate">{{ $nextAppointment['specialty'] }}</small>
                                <small class="block text-[#012D68] dark:text-blue-300 font-semibold text-[11px] mt-0.5">{{ $nextAppointment['date_time'] }}</small>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <button type="button" class="btn-reschedule h-9 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 font-semibold text-[12px] hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" onclick="window.showToast('Jadwal konsultasi dapat diatur melalui menu Jadwal.')">Reschedule</button>
                            <button type="button" class="btn-join-call h-9 rounded-xl bg-[#012D68] hover:bg-[#023E8A] text-white font-bold text-[12px] flex items-center justify-center gap-1.5 transition-all shadow-xs" onclick="window.showToast('Menghubungkan ke ruang panggilan video Dr. Saran Mitchell...')">
                                <svg class="app-svg-icon w-3.5 h-3.5"><use href="#icon-vid"/></svg>
                                Join Call
                            </button>
                        </div>
                    </div>

                    <!-- State 2: SESUDAH PERIKSA (Meminta Pembayaran / Unpaid) -->
                    <div id="desktop-state-after" class="appointment-state-panel flex flex-col justify-between min-h-[148px] {{ $appointmentMode === 'after' ? '' : 'hidden' }}" data-status="Sesudah Periksa" aria-label="Status Janji Temu: Sesudah Periksa">
                        <span class="sr-only">Sesudah Periksa</span>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                <small class="text-slate-400 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider">Tagihan Pemeriksaan</small>
                            </div>
                            <span id="desktop-unpaid-badge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                UNPAID
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="relative w-[48px] h-[48px] rounded-[14px] bg-blue-50 dark:bg-blue-950/50 overflow-hidden flex items-end justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900">
                                <img id="desktop-after-doc-img" src="{{ asset($nextAppointment['image']) }}" alt="{{ $nextAppointment['doctor_name'] }}" class="h-full w-auto object-contain object-bottom">
                                <span class="absolute bottom-0 right-0 bg-emerald-500 text-white rounded-tl-lg p-0.5 shadow-xs" title="Pemeriksaan Selesai">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <b id="desktop-after-doc-name" class="block text-[14px] text-slate-900 dark:text-white truncate font-bold">{{ $nextAppointment['doctor_name'] }}</b>
                                <div class="flex items-center gap-1.5 text-[11px] mt-0.5 truncate">
                                    <span class="text-slate-500 dark:text-slate-400">Selesai · 14:55 WIB</span>
                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                    <span id="desktop-status-pill" class="text-rose-600 dark:text-rose-400 font-semibold">Menunggu Pembayaran</span>
                                </div>
                                <div class="flex items-baseline justify-between text-[11px] mt-0.5">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-slate-400 text-[10.5px]">Total:</span>
                                        <b id="desktop-total-display" class="text-[14px] font-extrabold text-[#012D68] dark:text-blue-400">Rp {{ number_format((float) ($nextAppointment['total_amount'] ?? 435000), 0, ',', '.') }}</b>
                                    </div>
                                    <span id="desktop-after-ref-code" class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">Ref: {{ $nextAppointment['booking_code'] }}</span>
                                </div>
                                <span id="desktop-service-desc" class="hidden">Layanan Poliklinik Maristella</span>
                            </div>
                        </div>

                        <!-- Penetapan Tindakan & Resep oleh Dokter (Readonly) -->
                        <div class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-xl border border-slate-200/80 dark:border-slate-700/80 text-[11px] my-1 flex items-center justify-between" id="desktop-tariff-badge-container">
                            <span class="text-slate-500 dark:text-slate-400 font-medium text-[10.5px]">Keputusan Dokter:</span>
                            <span id="desktop-tariff-badge" class="font-bold text-[#012D68] dark:text-blue-300 inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span id="desktop-tariff-label">{{ ($nextAppointment['tariff_plan'] ?? 'with_medicine') === 'with_medicine' ? 'Konsultasi + Resep Obat' : 'Hanya Konsultasi Dokter' }}</span>
                            </span>
                        </div>
                        <p id="desktop-medicine-summary" class="text-[10.5px] text-slate-500 dark:text-slate-400 truncate -mt-0.5 mb-1 {{ empty($bill['medications']) ? 'hidden' : '' }}" title="{{ collect($bill['medications'])->pluck('name')->implode(', ') }}">
                            Obat: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ collect($bill['medications'])->pluck('name')->implode(', ') }}</span>
                            · Rp {{ number_format((float) $bill['medicine_cost'], 0, ',', '.') }}
                        </p>

                        <!-- Action Buttons: Rincian & Bayar Sekarang -->
                        <div id="desktop-action-buttons-unpaid" class="grid grid-cols-2 gap-2 mt-0.5">
                            <button type="button" class="btn-open-bill-drawer h-9 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-[12px] flex items-center justify-center gap-1.5 transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <span>Rincian Tagihan</span>
                            </button>
                            <button type="button" class="btn-trigger-payment h-9 rounded-xl bg-gradient-to-r from-[#012D68] to-[#023E8A] dark:from-blue-600 dark:to-blue-700 hover:brightness-110 active:scale-[0.98] text-white font-bold text-[12px] flex items-center justify-center gap-1.5 transition-all shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                <span id="desktop-pay-btn-label">Bayar Sekarang (Rp {{ number_format((float) ($nextAppointment['total_amount'] ?? 435000), 0, ',', '.') }})</span>
                            </button>
                        </div>

                        <!-- Action Buttons when PAID -->
                        <div id="desktop-action-buttons-paid" class="hidden grid grid-cols-2 gap-2 mt-0.5">
                            <button type="button" class="btn-open-success-modal h-9 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 text-emerald-800 dark:text-emerald-300 font-semibold text-[12px] flex items-center justify-center gap-1.5 transition-colors">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <span>Lihat Kuitansi</span>
                            </button>
                            <button type="button" class="btn-print-receipt h-9 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[12px] flex items-center justify-center gap-1.5 transition-all shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                <span>Cetak Bukti</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Category Specialty Chips with Dynamic Color Dots -->
            <div class="specialty-chips-wrapper flex flex-wrap gap-2.5 pt-1" role="group" aria-label="Kategori Spesialisasi">
                @foreach ($categories as $cat)
                    <button class="specialty-chip desktop-chip {{ $cat['id'] === 'all' ? 'active' : '' }}" data-cat="{{ $cat['id'] }}">
                        @if ($cat['id'] === 'Cardiologist')
                            <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                        @elseif ($cat['id'] === 'Neurologist')
                            <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                        @elseif ($cat['id'] === 'Orthopedist')
                            <span class="w-2 h-2 rounded-full bg-sky-500 flex-shrink-0"></span>
                        @endif
                        {{ $cat['name'] }}
                    </button>
                @endforeach
            </div>

            <!-- Doctors Section Header -->
            <div class="section-header flex justify-between items-center mt-2">
                <h2 class="text-[20px] font-semibold text-[var(--color-text)]">Find a Doctor</h2>
                <a href="javascript:void(0)" onclick="window.showToast('Menampilkan seluruh dokter')" class="text-[var(--color-primary)] hover:underline text-[13px] font-medium">View All</a>
            </div>

            <!-- Doctor Grid with Dynamic Specialty Pastel Colors -->
            <div id="doctors-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-10 pb-8">
                @foreach ($doctors as $index => $doc)
                    @php
                        $cardBg = match($doc['category']) {
                            'Cardiologist' => 'bg-rose-50/90 border-b border-rose-100',
                            'Neurologist' => 'bg-indigo-50/90 border-b border-indigo-100',
                            default => 'bg-blue-50/90 border-b border-blue-100'
                        };
                        $slotClass = match($doc['category']) {
                            'Cardiologist' => 'bg-rose-100/90 text-rose-800',
                            'Neurologist' => 'bg-indigo-100/90 text-indigo-800',
                            default => 'bg-blue-100/90 text-[#012D68]'
                        };
                        $pulseClass = match($doc['category']) {
                            'Cardiologist' => 'bg-rose-600',
                            'Neurologist' => 'bg-indigo-600',
                            default => 'bg-[#012D68]'
                        };
                        $slotText = match($index) {
                            0 => '4 Slots Available',
                            1 => '6 Slots Available',
                            default => '5 Slots Available'
                        };
                    @endphp
                    <button class="doctor-card group {{ $index === 2 ? 'selected' : '' }}" 
                            data-id="{{ $doc['id'] }}"
                            data-name="{{ $doc['name'] }}"
                            data-specialty="{{ $doc['specialty'] }}"
                            data-category="{{ $doc['category'] }}"
                            data-rating="{{ $doc['rating'] }}"
                            data-price="{{ $doc['price'] }}"
                            data-initials="{{ $doc['initials'] }}"
                            data-image="{{ asset($doc['image']) }}"
                            data-availability="{{ $doc['availability'] }}"
                            data-experience="{{ $doc['experience'] }}"
                            data-patients="{{ $doc['patients'] }}">
                        
                        <!-- 3D Microscope Icon (Out-of-Bounds Pop-out at Top Right) -->
                        <div class="absolute -top-7 -right-2 w-16 h-16 sm:w-20 sm:h-20 z-30 pointer-events-none transition-transform duration-300 ease-out group-hover:scale-110 group-hover:-rotate-6 drop-shadow-[0_8px_16px_rgba(0,0,0,0.12)]">
                            <img src="{{ asset('images/3d/microscope.png') }}" alt="3D Microscope" class="w-full h-full object-contain">
                        </div>

                        <!-- 3D Stethoscope with Heart (Subtle Depth in Background) -->
                        <div class="absolute top-12 left-3 w-14 h-14 sm:w-16 sm:h-16 z-0 pointer-events-none opacity-20 -rotate-12 transition-transform duration-500 ease-out group-hover:rotate-0 group-hover:scale-105">
                            <img src="{{ asset('images/3d/stethoscope.png') }}" alt="3D Stethoscope" class="w-full h-full object-contain">
                        </div>

                        <!-- Top pastel block with dynamic specialty tone -->
                        <div class="relative w-full h-[180px] {{ $cardBg }} rounded-t-[22px] flex items-end justify-center overflow-visible">
                            <!-- Rating pill at top left -->
                            <div class="absolute top-3 left-3 z-30 inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-xs px-2.5 py-1 rounded-full shadow-xs border border-slate-100">
                                <svg class="w-3.5 h-3.5 text-amber-400 fill-amber-400" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-xs font-bold text-slate-900 leading-none">{{ $doc['rating'] }}</span>
                            </div>

                            <!-- Doctor Cut-out Photo with Out-of-Bounds Pop-out Effect -->
                            <div class="relative w-full h-full flex items-end justify-center overflow-visible">
                                <img src="{{ asset($doc['image']) }}" 
                                     alt="{{ $doc['name'] }}" 
                                     onerror="this.src='{{ asset('images/doctors/doctor-1.png') }}'"
                                     class="relative z-20 h-[126%] w-auto max-w-none object-contain object-bottom -top-6 sm:-top-7 pointer-events-none transition-transform duration-300 ease-out group-hover:scale-108 group-hover:-translate-y-2 drop-shadow-[0_14px_18px_rgba(1,45,104,0.20)]">
                            </div>
                        </div>

                        <!-- Bottom content area (solid white background, rounded bottom) -->
                        <div class="relative z-20 p-4 sm:p-5 flex flex-col flex-1 justify-between bg-white rounded-b-[22px] text-left">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 truncate leading-snug">{{ $doc['name'] }}</h3>
                                <p class="text-sm font-normal text-slate-500 truncate mt-0.5">{{ $doc['specialty'] }}</p>
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold {{ $slotClass }} px-2.5 py-0.5 rounded-full mt-2">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $pulseClass }} animate-pulse"></span>
                                    {{ $slotText }}
                                </span>
                            </div>
                            <div class="mt-3.5 flex items-baseline gap-1">
                                <span class="text-base font-extrabold text-[#012D68]">{{ $doc['price'] }}</span>
                                <span class="text-xs font-normal text-slate-400">/per session</span>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

            <div id="doctors-empty" class="hidden text-center py-12 text-[var(--color-text-muted)]">
                Tidak ada dokter yang cocok dengan kriteria pencarian.
            </div>
        </main>

        <!-- RIGHT PANEL (Doctor Details & Booking) -->
        <aside class="panel bg-[var(--color-surface)] border-l border-[var(--color-border)] p-6 sticky top-0 h-screen overflow-y-auto flex flex-col z-20">
            <!-- Doctor Header -->
            <div class="flex items-center gap-3.5">
                <div class="w-[84px] h-[84px] rounded-[18px] bg-blue-50 overflow-hidden flex items-end justify-center flex-shrink-0 shadow-sm border border-blue-100">
                    <img id="pav-img" src="{{ asset($selectedDoctor['image']) }}" alt="{{ $selectedDoctor['name'] }}" class="h-full w-auto object-contain object-bottom">
                </div>
                <div class="min-w-0">
                    <h3 id="pn" class="text-[19px] font-bold text-[var(--color-text)] leading-tight truncate">{{ $selectedDoctor['name'] }}</h3>
                    <small id="ps" class="text-[var(--color-text-secondary)] text-[12.5px] block mt-0.5">{{ $selectedDoctor['specialty'] }}</small>
                    <div class="text-[12px] text-slate-600 mt-1 flex items-center gap-1 font-semibold">
                        <span class="text-amber-500">★</span> <span id="pr">{{ $selectedDoctor['rating'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Pricing & Stats Card (Solid Clean Surface, No Gradient) -->
            <div class="pricing-card mt-5 border border-slate-200/90 rounded-[22px] p-4 bg-slate-50 shadow-xs">
                <small class="text-slate-500 text-[11.5px] font-medium">Starting from</small>
                <b id="pp" class="block text-[24px] font-extrabold text-slate-900 my-0.5">
                    {{ $selectedDoctor['price'] }} <span class="text-[11px] font-normal text-slate-400">/per session</span>
                </b>

                <!-- 3 Stat Tiles with Vibrant Solid Icons -->
                <div class="grid grid-cols-3 gap-2 mt-3.5">
                    <div class="stat-tile bg-white border border-slate-200/80 rounded-[14px] p-2.5 text-center shadow-xs">
                        <i class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 grid place-items-center mx-auto mb-1">
                            <svg class="app-svg-icon w-3.5 h-3.5"><use href="#icon-clock"/></svg>
                        </i>
                        <small class="text-[9.5px] text-slate-400 font-medium block">Availability</small>
                        <b id="pav-avail" class="text-[11px] text-slate-900 font-bold block mt-0.5">{{ $selectedDoctor['availability'] }}</b>
                    </div>
                    <div class="stat-tile bg-white border border-slate-200/80 rounded-[14px] p-2.5 text-center shadow-xs">
                        <i class="w-7 h-7 rounded-full bg-sky-100 text-sky-700 grid place-items-center mx-auto mb-1">
                            <svg class="app-svg-icon w-3.5 h-3.5"><use href="#icon-cal"/></svg>
                        </i>
                        <small class="text-[9.5px] text-slate-400 font-medium block">Experience</small>
                        <b id="pav-exp" class="text-[11px] text-slate-900 font-bold block mt-0.5">{{ $selectedDoctor['experience'] }}</b>
                    </div>
                    <div class="stat-tile bg-white border border-slate-200/80 rounded-[14px] p-2.5 text-center shadow-xs">
                        <i class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 grid place-items-center mx-auto mb-1">
                            <svg class="app-svg-icon w-3.5 h-3.5"><use href="#icon-user"/></svg>
                        </i>
                        <small class="text-[9.5px] text-slate-400 font-medium block">Patients</small>
                        <b id="pav-patients" class="text-[11px] text-slate-900 font-bold block mt-0.5">{{ $selectedDoctor['patients'] }}</b>
                    </div>
                </div>
            </div>

            <!-- Date Picker -->
            <div class="flex items-center justify-between mt-5 mb-2.5">
                <div class="font-bold text-[14.5px] text-slate-900 dark:text-white">Select a date &amp; time</div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                    <span class="week-month-label text-[11.5px] font-semibold text-slate-700 dark:text-slate-300">{{ $currentMonthYear ?? date('M Y') }}</span>
                    <div class="flex items-center gap-0.5">
                        <button type="button" class="btn-prev-week w-6 h-6 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center text-slate-600 dark:text-slate-400 transition-colors" title="Minggu Sebelumnya">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" class="btn-next-week w-6 h-6 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 transition-colors" title="Minggu Berikutnya">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="days-container grid grid-cols-7 gap-1.5" id="desktop-days-container">
                @foreach ($bookingDays ?? [] as $index => $day)
                    <button type="button"
                        class="day-pill {{ $day['is_past'] ? 'disabled opacity-40 cursor-not-allowed pointer-events-none' : ($index === ($activeDayIndex ?? 0) ? 'active' : '') }}"
                        data-day="{{ $day['name'] }}"
                        data-date="{{ $day['date'] }}"
                        data-full-date="{{ $day['full_date'] }}"
                        data-is-past="{{ $day['is_past'] ? '1' : '0' }}"
                        data-is-today="{{ $day['is_today'] ? '1' : '0' }}"
                        {{ $day['is_past'] ? 'disabled aria-disabled=true' : '' }}
                        title="{{ $day['is_past'] ? 'Tanggal sudah terlewati' : ($day['is_today'] ? 'Hari ini' : $day['full_date']) }}">
                        <small>{{ $day['name'] }}</small>
                        <b>{{ $day['date'] }}</b>
                    </button>
                @endforeach
            </div>

            <!-- Time Slots -->
            <div class="slots-container grid grid-cols-3 gap-2 mt-3" id="desktop-slots-container">
                @php
                    $isFirstDayToday = ($bookingDays[$activeDayIndex ?? 0]['is_today'] ?? false);
                    $firstActiveSlotFound = false;
                @endphp
                @foreach ($timeSlots ?? [] as $slot)
                    @php
                        $slotIsPast = $isFirstDayToday && (($currentHour ?? 0) >= $slot['hour']);
                        $isSlotActive = false;
                        if (! $slotIsPast && ! $firstActiveSlotFound) {
                            $isSlotActive = true;
                            $firstActiveSlotFound = true;
                        }
                    @endphp
                    <button type="button"
                        class="slot-pill {{ $slotIsPast ? 'disabled opacity-40 cursor-not-allowed pointer-events-none' : ($isSlotActive ? 'active' : '') }}"
                        data-time="{{ $slot['time'] }}"
                        data-hour="{{ $slot['hour'] }}"
                        data-start="{{ $slot['start'] }}"
                        data-end="{{ $slot['end'] }}"
                        data-is-past="{{ $slotIsPast ? '1' : '0' }}"
                        {{ $slotIsPast ? 'disabled aria-disabled=true' : '' }}
                        title="{{ $slotIsPast ? 'Jam ini sudah terlewati' : $slot['time'] }}">
                        {{ $slot['time'] }}
                    </button>
                @endforeach
            </div>

            <!-- Sticky CTA Button (Solid White Container with Border) -->
            <div class="mt-auto pt-5 sticky bottom-0 bg-white border-t border-slate-100 pb-1">
                <button class="btn-book-session w-full h-12 rounded-[16px] bg-[#012D68] hover:bg-[#023E8A] active:bg-[#001F4D] text-white font-bold text-[14.5px] transition-all shadow-md">
                    Book a Session
                </button>
            </div>
        </aside>
    </div>


    <!-- ========================================== -->
    <!-- 2. MOBILE LAYOUT (Screen width < 1024px)   -->
    <!-- ========================================== -->
    <div class="mobile-layout block lg:hidden min-h-screen relative pb-28">

        <!-- Solid Dynamic Klinik Maristella Navy Header (No Gradient) -->
        <header class="bg-[#012D68] pt-12 px-5 pb-8 text-white">
            <div class="flex justify-between items-center">
                <!-- User Avatar -->
                <div class="avatar-initials w-10 h-10 border-2 border-white text-[14px]">
                    {{ $user['initials'] }}
                </div>

                <!-- Klinik Maristella Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-1.5 bg-white/20 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/30 no-underline shadow-sm" title="Klinik Maristella">
                    <img src="{{ asset('images/maristella-logo-white.png') }}" alt="Klinik Maristella" class="h-5 w-auto object-contain" style="max-height: 20px; max-width: 120px;" />
                </a>

                <!-- Right Glass Buttons -->
                <div class="flex items-center gap-2">
                    <button id="mobile-search-trigger" class="glass-btn" aria-label="Cari Dokter">
                        <svg class="app-svg-icon w-4 h-4"><use href="#icon-search"/></svg>
                    </button>
                    <!-- Mobile Notification Bell -->
                    <div class="relative" id="mobile-notif-container">
                        <button id="mobile-notif-btn" type="button" class="glass-btn relative" aria-label="Notifikasi">
                            <svg class="app-svg-icon w-4 h-4"><use href="#icon-bell"/></svg>
                            <span id="mobile-notif-badge" class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-[#012D68] {{ ($unreadCount ?? 0) > 0 ? '' : 'hidden' }}"></span>
                        </button>

                        <!-- Mobile Notification Dropdown Panel -->
                        <div id="mobile-notif-dropdown" class="hidden absolute right-0 mt-2 w-72 max-w-[90vw] bg-white dark:bg-[#101E35] text-slate-900 dark:text-white rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-800 z-50 overflow-hidden">
                            <div class="px-3.5 py-2.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-900/50">
                                <span class="font-bold text-[11px] uppercase tracking-wider text-slate-800 dark:text-white">Notifikasi</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[9.5px] font-bold bg-[#012D68] text-white">
                                    {{ $unreadCount ?? 0 }} Baru
                                </span>
                            </div>
                            <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 p-1">
                                @forelse(($notifications ?? []) as $notif)
                                    <div class="p-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-xl transition-colors flex items-start gap-2.5">
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 
                                            @if($notif['type'] === 'bill') bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400
                                            @elseif($notif['type'] === 'appointment') bg-blue-50 text-[#012D68] dark:bg-blue-950/50 dark:text-blue-400
                                            @elseif($notif['type'] === 'pharmacy') bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400
                                            @else bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 @endif">
                                            @if($notif['type'] === 'bill')
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @elseif($notif['type'] === 'appointment')
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <b class="text-[11px] font-bold text-slate-900 dark:text-white truncate block">{{ $notif['title'] }}</b>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight mt-0.5">{{ $notif['message'] }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-5 text-center text-xs text-slate-400">
                                        Tidak ada notifikasi baru
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expandable Search Bar on Mobile -->
            <div id="mobile-search-bar" class="hidden mt-4">
                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-md rounded-full px-4 h-10 border border-white/30 text-white">
                    <svg class="app-svg-icon w-4 h-4 text-white"><use href="#icon-search"/></svg>
                    <input id="mobile-search-input" type="search" placeholder="Cari dokter atau spesialis..." class="flex-1 bg-transparent border-none text-white placeholder-white/70 text-[13px] outline-none">
                </div>
            </div>

            <!-- Personal Greeting -->
            <div class="text-center mt-5">
                <span class="text-white/80 text-[13px]">Selamat siang,</span>
                <b class="block text-[21px] font-bold text-white mt-0.5">What’s on your health today?</b>
            </div>

            <!-- Next Appointment Label (Hanya tampil jika ada appointment aktif) -->
            <div id="mobile-next-appt-label" class="text-center text-blue-100 text-[11px] font-bold tracking-wider mt-4 mb-2 uppercase {{ $appointmentMode === 'welcome' ? 'hidden' : '' }}">
                Next Appointment
            </div>

            <!-- Stacked Appointment Card (Hanya muncul jika ada appointment aktif) -->
            <div class="stacked-card mx-1 bg-white dark:bg-[#101E35] text-slate-900 dark:text-white rounded-[22px] p-4 shadow-md border border-slate-100 dark:border-slate-800 transition-all duration-300 {{ $appointmentMode === 'welcome' ? 'hidden' : '' }}" data-appointment-mode="{{ $appointmentMode }}">

                <!-- State 1: SEBELUM PERIKSA (Default / Upcoming) -->
                <div id="mobile-state-before" class="appointment-state-panel flex flex-col justify-between min-h-[144px] {{ $appointmentMode === 'before' ? '' : 'hidden' }}" data-status="Sebelum Periksa" aria-label="Status Janji Temu: Sebelum Periksa">
                    <span class="sr-only">Sebelum Periksa</span>
                    <div class="flex items-center justify-between mb-1">
                        <small class="text-slate-400 dark:text-slate-400 text-[10.5px] font-bold uppercase tracking-wider">Next Appointment</small>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Upcoming
                        </span>
                    </div>
                    <div class="flex items-center gap-3 py-1">
                        <div class="w-11 h-11 rounded-[14px] bg-blue-50 dark:bg-blue-950/50 overflow-hidden flex items-end justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900">
                            <img src="{{ asset($nextAppointment['image']) }}" alt="{{ $nextAppointment['doctor_name'] }}" class="h-full w-auto object-contain object-bottom">
                        </div>
                        <div class="min-w-0 flex-1">
                            <b class="text-[13.5px] text-slate-900 dark:text-white truncate block font-bold">{{ $nextAppointment['doctor_name'] }}</b>
                            <small class="text-slate-500 dark:text-slate-400 text-[11px] block mt-0.5">{{ $nextAppointment['specialty'] }}</small>
                            <small class="text-[#012D68] dark:text-blue-300 text-[11px] font-semibold block mt-0.5">{{ $nextAppointment['date_time'] }}</small>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <button type="button" class="btn-reschedule h-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-[11.5px] hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" onclick="window.showToast('Jadwal konsultasi dapat diatur melalui menu Jadwal.')">Reschedule</button>
                        <button type="button" class="btn-join-call h-8 rounded-xl bg-[#012D68] hover:bg-[#023E8A] text-white font-bold text-[11.5px] flex items-center justify-center gap-1 shadow-xs" onclick="window.showToast('Menghubungkan ke ruang panggilan video Dr. Saran Mitchell...')">
                            <svg class="app-svg-icon w-3 h-3"><use href="#icon-vid"/></svg>
                            Join Call
                        </button>
                    </div>
                </div>

                <!-- State 2: SESUDAH PERIKSA (Meminta Pembayaran / Unpaid) -->
                <div id="mobile-state-after" class="appointment-state-panel flex flex-col justify-between min-h-[144px] {{ $appointmentMode === 'after' ? '' : 'hidden' }}" data-status="Sesudah Periksa" aria-label="Status Janji Temu: Sesudah Periksa">
                    <span class="sr-only">Sesudah Periksa</span>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <small class="text-slate-400 dark:text-slate-400 text-[10.5px] font-bold uppercase tracking-wider">Tagihan Pemeriksaan</small>
                        </div>
                        <span id="mobile-unpaid-badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                            UNPAID
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="relative w-11 h-11 rounded-[14px] bg-blue-50 dark:bg-blue-950/50 overflow-hidden flex items-end justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900">
                            <img id="mobile-after-doc-img" src="{{ asset($nextAppointment['image']) }}" alt="{{ $nextAppointment['doctor_name'] }}" class="h-full w-auto object-contain object-bottom">
                            <span class="absolute bottom-0 right-0 bg-emerald-500 text-white rounded-tl-lg p-0.5" title="Selesai">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <b id="mobile-after-doc-name" class="text-[13.5px] text-slate-900 dark:text-white truncate block font-bold">{{ $nextAppointment['doctor_name'] }}</b>
                            <div class="flex items-center gap-1 text-[10.5px] mt-0.5 truncate">
                                <span class="text-slate-500 dark:text-slate-400">Selesai · 14:55 WIB</span>
                                <span class="text-slate-300 dark:text-slate-600">•</span>
                                <span id="mobile-status-pill" class="text-rose-600 dark:text-rose-400 font-semibold truncate">Menunggu Bayar</span>
                            </div>
                            <div class="flex items-baseline justify-between text-[10.5px] mt-0.5">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-slate-400 text-[10px]">Total:</span>
                                    <b id="mobile-total-display" class="text-[13.5px] font-extrabold text-[#012D68] dark:text-blue-400">Rp {{ number_format((float) ($nextAppointment['total_amount'] ?? 435000), 0, ',', '.') }}</b>
                                </div>
                                <span id="mobile-after-ref-code" class="text-[9.5px] text-slate-400 dark:text-slate-500 font-mono">{{ $nextAppointment['booking_code'] }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Penetapan Tindakan & Resep oleh Dokter Mobile (Readonly) -->
                    <div class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-xl border border-slate-200/80 dark:border-slate-700/80 text-[10.5px] my-1 flex items-center justify-between" id="mobile-tariff-badge-container">
                        <span class="text-slate-500 dark:text-slate-400 font-medium text-[10px]">Keputusan Dokter:</span>
                        <span id="mobile-tariff-badge" class="font-bold text-[#012D68] dark:text-blue-300 inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span id="mobile-tariff-label">{{ ($nextAppointment['tariff_plan'] ?? 'with_medicine') === 'with_medicine' ? 'Konsultasi + Resep Obat' : 'Hanya Konsultasi Dokter' }}</span>
                        </span>
                    </div>
                    <p id="mobile-medicine-summary" class="text-[10px] text-slate-500 dark:text-slate-400 truncate -mt-0.5 mb-1 {{ empty($bill['medications']) ? 'hidden' : '' }}" title="{{ collect($bill['medications'])->pluck('name')->implode(', ') }}">
                        Obat: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ collect($bill['medications'])->pluck('name')->implode(', ') }}</span>
                        · Rp {{ number_format((float) $bill['medicine_cost'], 0, ',', '.') }}
                    </p>
                    <div id="mobile-action-buttons-unpaid" class="grid grid-cols-2 gap-2 mt-0.5">
                        <button type="button" class="btn-open-bill-drawer h-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-[11px] flex items-center justify-center gap-1 transition-colors">
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <span>Rincian</span>
                        </button>
                        <button type="button" class="btn-trigger-payment h-8 rounded-xl bg-gradient-to-r from-[#012D68] to-[#023E8A] dark:from-blue-600 dark:to-blue-700 text-white font-bold text-[11px] flex items-center justify-center gap-1 shadow-xs">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            <span id="mobile-pay-btn-label">Bayar (Rp {{ number_format((float) ($nextAppointment['total_amount'] ?? 435000), 0, ',', '.') }})</span>
                        </button>
                    </div>
                    <div id="mobile-action-buttons-paid" class="hidden grid grid-cols-2 gap-2 mt-0.5">
                        <button type="button" class="btn-open-success-modal h-8 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 font-semibold text-[11px] flex items-center justify-center gap-1">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <span>Lihat Kuitansi</span>
                        </button>
                        <button type="button" class="btn-print-receipt h-8 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] flex items-center justify-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            <span>Cetak Bukti</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- White Curved Sheet -->
        <section class="sheet bg-[var(--color-surface)] rounded-t-[28px] -mt-4 px-4 pt-5 pb-6 min-h-[500px]">
            <!-- Specialty Chips (Horizontal Scrollable with Dynamic Color Dots) -->
            <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                @foreach ($categories as $cat)
                    <button class="specialty-chip mobile-chip flex-none text-[12px] h-[32px] px-3.5 {{ $cat['id'] === 'all' ? 'active' : '' }}" data-cat="{{ $cat['id'] }}">
                        @if ($cat['id'] === 'Cardiologist')
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 flex-shrink-0"></span>
                        @elseif ($cat['id'] === 'Neurologist')
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 flex-shrink-0"></span>
                        @elseif ($cat['id'] === 'Orthopedist')
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500 flex-shrink-0"></span>
                        @endif
                        {{ $cat['name'] }}
                    </button>
                @endforeach
            </div>

            <!-- Section Header -->
            <div class="flex justify-between items-center mt-4 mb-3 px-1">
                <b class="text-[15px] text-slate-900 font-bold">Find a Doctor</b>
                <a href="javascript:void(0)" onclick="window.showToast('Menampilkan seluruh dokter')" class="text-[12px] text-[#012D68] font-bold">View All</a>
            </div>

            <!-- Horizontal Doctor Cards (Scrollable with Dynamic Specialty Pastels) -->
            <div class="flex gap-4 overflow-x-auto overflow-y-visible no-scrollbar pt-6 pb-3 px-1">
                @foreach ($doctors as $index => $doc)
                    @php
                        $mCardBg = match($doc['category']) {
                            'Cardiologist' => 'bg-rose-50/90 border-b border-rose-100',
                            'Neurologist' => 'bg-indigo-50/90 border-b border-indigo-100',
                            default => 'bg-blue-50/90 border-b border-blue-100'
                        };
                    @endphp
                    <div class="doctor-card flex-none w-[155px] cursor-pointer group {{ $index === 2 ? 'selected' : '' }}"
                         data-id="{{ $doc['id'] }}"
                         data-name="{{ $doc['name'] }}"
                         data-specialty="{{ $doc['specialty'] }}"
                         data-category="{{ $doc['category'] }}"
                         data-rating="{{ $doc['rating'] }}"
                         data-price="{{ $doc['price'] }}"
                         data-initials="{{ $doc['initials'] }}"
                         data-image="{{ asset($doc['image']) }}"
                         data-availability="{{ $doc['availability'] }}"
                         data-experience="{{ $doc['experience'] }}"
                         data-patients="{{ $doc['patients'] }}">
                        
                        <!-- 3D Microscope Icon (Out-of-bounds top right) -->
                        <div class="absolute -top-5 -right-1.5 w-12 h-12 z-30 pointer-events-none drop-shadow-sm">
                            <img src="{{ asset('images/3d/microscope.png') }}" alt="3D Microscope" class="w-full h-full object-contain">
                        </div>

                        <!-- Top pastel block (rounded top, dynamic specialty pastel) -->
                        <div class="relative w-full h-[135px] {{ $mCardBg }} rounded-t-[22px] flex items-end justify-center overflow-visible">
                            <div class="absolute top-2 left-2 z-30 inline-flex items-center gap-1 bg-white/95 px-2 py-0.5 rounded-full shadow-xs border border-slate-100">
                                <svg class="w-3 h-3 text-amber-400 fill-amber-400" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-[10px] font-bold text-slate-900 leading-none">{{ $doc['rating'] }}</span>
                            </div>
                            <div class="relative w-full h-full flex items-end justify-center overflow-visible">
                                <img src="{{ asset($doc['image']) }}" alt="{{ $doc['name'] }}" onerror="this.src='{{ asset('images/doctors/doctor-1.png') }}'" class="relative z-20 h-[122%] w-auto max-w-none object-contain object-bottom -top-4 pointer-events-none drop-shadow-[0_8px_12px_rgba(1,45,104,0.18)]">
                            </div>
                        </div>

                        <!-- Bottom content area -->
                        <div class="relative z-20 p-3 bg-white rounded-b-[22px] text-left">
                            <b class="text-sm font-bold text-slate-900 block truncate">{{ $doc['name'] }}</b>
                            <small class="text-slate-500 font-normal text-xs block truncate mt-0.5">{{ $doc['specialty'] }}</small>
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-xs font-extrabold text-[#012D68]">{{ $doc['price'] }}</span>
                                <span class="text-[10px] font-normal text-slate-400">/per session</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Floating Bottom Navigation -->
        <nav class="floating-bottom-nav" aria-label="Navigasi Bawah">
            <a href="{{ route('calendar') }}" aria-label="Jadwal" class="grid place-items-center w-11 h-11 rounded-full text-[var(--color-text-secondary)] hover:text-[#012D68] hover:bg-[#EFF6FF] transition-all">
                <svg class="app-svg-icon w-5 h-5"><use href="#icon-cal"/></svg>
            </a>
            <button class="active" aria-label="Beranda">
                <svg class="app-svg-icon w-5 h-5"><use href="#icon-home"/></svg>
            </button>
            <button aria-label="Pengaturan" onclick="window.showToast('Tab Pengaturan...')">
                <svg class="app-svg-icon w-5 h-5"><use href="#icon-set"/></svg>
            </button>
        </nav>

        <!-- Mobile Doctor Detail & Booking Bottom Sheet Modal -->
        <div id="mobile-sheet-backdrop" class="mobile-sheet-backdrop"></div>
        <div id="mobile-detail-sheet" class="mobile-sheet">
            <!-- Drag Handle & Close -->
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-1 bg-[var(--color-border)] rounded-full mx-auto"></div>
                <button id="mobile-sheet-close" class="p-1 text-[var(--color-text-muted)] hover:text-[var(--color-text)]" aria-label="Tutup Detail">
                    <svg class="app-svg-icon w-5 h-5"><use href="#icon-close"/></svg>
                </button>
            </div>

            <!-- Doctor Profile -->
            <div class="flex items-center gap-3">
                <div class="w-16 h-16 rounded-[14px] bg-blue-50 overflow-hidden flex items-end justify-center flex-shrink-0 shadow-sm border border-blue-100">
                    <img id="m-pav-img" src="{{ asset($selectedDoctor['image']) }}" alt="{{ $selectedDoctor['name'] }}" class="h-full w-auto object-contain object-bottom">
                </div>
                <div class="min-w-0">
                    <span class="rating-pill text-[11px] mb-1 inline-flex">★ <span id="m-pr">{{ $selectedDoctor['rating'] }}</span></span>
                    <h3 id="m-pn" class="text-[17px] font-bold text-slate-900 leading-tight truncate">{{ $selectedDoctor['name'] }}</h3>
                    <small id="m-ps" class="text-slate-500 text-[12px] block">{{ $selectedDoctor['specialty'] }}</small>
                </div>
            </div>

            <!-- Price & Stat Tiles (Solid Clean Surface, No Gradient) -->
            <div class="pricing-card mt-4 border border-slate-200/90 rounded-[20px] p-3.5 bg-slate-50 shadow-xs">
                <small class="text-slate-500 text-[11px] font-medium">Starting from</small>
                <b id="m-pp" class="block text-[22px] font-extrabold text-slate-900 my-0.5">
                    {{ $selectedDoctor['price'] }} <span class="text-[10px] font-normal text-slate-400">/per session</span>
                </b>

                <div class="grid grid-cols-3 gap-1.5 mt-2.5">
                    <div class="stat-tile bg-white border border-slate-200/80 rounded-[12px] p-2 text-center shadow-xs">
                        <i class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 grid place-items-center mx-auto mb-1">
                            <svg class="app-svg-icon w-3 h-3"><use href="#icon-clock"/></svg>
                        </i>
                        <small class="text-[8.5px] text-slate-400 font-medium block">Availability</small>
                        <b id="m-pav-avail" class="text-[10.5px] text-slate-900 font-bold block mt-0.5">{{ $selectedDoctor['availability'] }}</b>
                    </div>
                    <div class="stat-tile bg-white border border-slate-200/80 rounded-[12px] p-2 text-center shadow-xs">
                        <i class="w-6 h-6 rounded-full bg-sky-100 text-sky-700 grid place-items-center mx-auto mb-1">
                            <svg class="app-svg-icon w-3 h-3"><use href="#icon-cal"/></svg>
                        </i>
                        <small class="text-[8.5px] text-slate-400 font-medium block">Experience</small>
                        <b id="m-pav-exp" class="text-[10.5px] text-slate-900 font-bold block mt-0.5">{{ $selectedDoctor['experience'] }}</b>
                    </div>
                    <div class="stat-tile bg-white border border-slate-200/80 rounded-[12px] p-2 text-center shadow-xs">
                        <i class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 grid place-items-center mx-auto mb-1">
                            <svg class="app-svg-icon w-3 h-3"><use href="#icon-user"/></svg>
                        </i>
                        <small class="text-[8.5px] text-slate-400 font-medium block">Patients</small>
                        <b id="m-pav-patients" class="text-[10.5px] text-slate-900 font-bold block mt-0.5">{{ $selectedDoctor['patients'] }}</b>
                    </div>
                </div>
            </div>

            <!-- Date Selection -->
            <div class="flex items-center justify-between mt-4 mb-2">
                <div class="font-semibold text-[13.5px] text-[var(--color-text)]">Select a date &amp; time</div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                    <span class="week-month-label text-[11px] font-semibold text-slate-700 dark:text-slate-300">{{ $currentMonthYear ?? date('M Y') }}</span>
                    <div class="flex items-center gap-0.5">
                        <button type="button" class="btn-prev-week w-5 h-5 rounded hover:bg-slate-100 dark:hover:bg-slate-800 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center text-slate-600 dark:text-slate-400 transition-colors" title="Minggu Sebelumnya">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" class="btn-next-week w-5 h-5 rounded hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 transition-colors" title="Minggu Berikutnya">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="days-container grid grid-cols-7 gap-1" id="mobile-days-container">
                @foreach ($bookingDays ?? [] as $index => $day)
                    <button type="button"
                        class="day-pill text-[10px] py-1 {{ $day['is_past'] ? 'disabled opacity-40 cursor-not-allowed pointer-events-none' : ($index === ($activeDayIndex ?? 0) ? 'active' : '') }}"
                        data-day="{{ $day['name'] }}"
                        data-date="{{ $day['date'] }}"
                        data-full-date="{{ $day['full_date'] }}"
                        data-is-past="{{ $day['is_past'] ? '1' : '0' }}"
                        data-is-today="{{ $day['is_today'] ? '1' : '0' }}"
                        {{ $day['is_past'] ? 'disabled aria-disabled=true' : '' }}
                        title="{{ $day['is_past'] ? 'Tanggal sudah terlewati' : ($day['is_today'] ? 'Hari ini' : $day['full_date']) }}">
                        <small class="text-[8.5px]">{{ $day['name'] }}</small>
                        <b class="text-[13px]">{{ $day['date'] }}</b>
                    </button>
                @endforeach
            </div>

            <!-- Time Slots Selection -->
            <div class="slots-container grid grid-cols-3 gap-1.5 mt-2.5" id="mobile-slots-container">
                @php
                    $isFirstDayToday = ($bookingDays[$activeDayIndex ?? 0]['is_today'] ?? false);
                    $firstActiveSlotFoundMobile = false;
                @endphp
                @foreach ($timeSlots ?? [] as $slot)
                    @php
                        $slotIsPast = $isFirstDayToday && (($currentHour ?? 0) >= $slot['hour']);
                        $isSlotActive = false;
                        if (! $slotIsPast && ! $firstActiveSlotFoundMobile) {
                            $isSlotActive = true;
                            $firstActiveSlotFoundMobile = true;
                        }
                    @endphp
                    <button type="button"
                        class="slot-pill h-8 text-[11px] {{ $slotIsPast ? 'disabled opacity-40 cursor-not-allowed pointer-events-none' : ($isSlotActive ? 'active' : '') }}"
                        data-time="{{ $slot['time'] }}"
                        data-hour="{{ $slot['hour'] }}"
                        data-start="{{ $slot['start'] }}"
                        data-end="{{ $slot['end'] }}"
                        data-is-past="{{ $slotIsPast ? '1' : '0' }}"
                        {{ $slotIsPast ? 'disabled aria-disabled=true' : '' }}
                        title="{{ $slotIsPast ? 'Jam ini sudah terlewati' : $slot['time'] }}">
                        {{ $slot['time'] }}
                    </button>
                @endforeach
            </div>

            <!-- CTA Button -->
            <button class="btn-book-session w-full h-11 rounded-[14px] bg-[var(--color-primary)] text-white font-semibold text-[14px] mt-5 shadow-md">
                Book a Session
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. BOOKING REGISTRATION MODAL              -->
    <!-- ========================================== -->
    <div id="booking-modal-backdrop" class="modal-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-200 flex items-center justify-center p-4">
        <div id="booking-modal" class="modal-dialog bg-white dark:bg-[#101E35] w-full max-w-lg rounded-[24px] shadow-2xl border border-slate-100 dark:border-slate-800 flex flex-col max-h-[92vh] overflow-hidden transform scale-95 transition-all duration-200" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-[#101E35] sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-[#012D68] dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 id="modal-title" class="text-[17px] font-bold text-slate-900 dark:text-white leading-tight">Pendaftaran Sesi Konsultasi</h3>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-0.5">Lengkapi keluhan dan detail kesehatan Anda sebelum sesi dimulai</p>
                    </div>
                </div>
                <button type="button" id="btn-close-modal" class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-300 flex items-center justify-center transition-colors" aria-label="Tutup formulir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-5 text-slate-800 dark:text-slate-200">
                
                <!-- Doctor & Schedule Summary Pill -->
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-xl bg-blue-100/70 overflow-hidden flex items-end justify-center flex-shrink-0 border border-blue-200">
                            <img id="modal-doc-img" src="{{ asset('images/doctors/doctor-2.png') }}" alt="Dokter" class="w-full h-full object-cover object-bottom">
                            <span id="modal-doc-initials" class="hidden font-bold text-blue-900 text-sm">AK</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <b id="modal-doc-name" class="text-[14px] text-slate-900 dark:text-white font-bold truncate">Dr. Anaya Kapoor</b>
                                <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-amber-600">
                                    ★ <span id="modal-doc-rating">4.8</span>
                                </span>
                            </div>
                            <span id="modal-doc-specialty" class="text-[12px] text-slate-500 dark:text-slate-400 block truncate">Neurologist</span>
                            <div class="flex items-center gap-2 mt-1 text-[11.5px] font-medium text-[#012D68] dark:text-blue-300">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <span id="modal-doc-schedule">{{ ($bookingDays[$activeDayIndex ?? 0]['name'] ?? 'Hari Ini').' '.($bookingDays[$activeDayIndex ?? 0]['date'] ?? '') }} · 01 PM</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <small class="text-[11px] text-slate-400 block">Tarif Sesi</small>
                        <b id="modal-doc-price" class="text-[15px] font-bold text-[#012D68] dark:text-blue-400">Rp 180.000</b>
                    </div>
                </div>

                <!-- Form Inputs -->
                <form id="booking-intake-form" onsubmit="return false;" class="space-y-4">
                    
                    <!-- Patient Name & Phone -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="reg-patient-name" class="block text-[12.5px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Pasien <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="reg-patient-name" value="{{ $user['name'] }}" required placeholder="Nama lengkap pasien" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-[13px] focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        </div>
                        <div>
                            <label for="reg-patient-phone" class="block text-[12.5px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                No. WhatsApp / HP <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" id="reg-patient-phone" value="0812-9988-7766" required placeholder="Contoh: 081234567890" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-[13px] focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Symptoms / Gejala (Wajib) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="reg-symptoms" class="text-[12.5px] font-semibold text-slate-700 dark:text-slate-300">
                                Gejala / Keluhan Utama <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-slate-400">Pilih cepat atau ketik manual</span>
                        </div>

                        <!-- Quick Symptom Tags -->
                        <div class="flex flex-wrap gap-1.5 mb-2" id="quick-symptoms-chips">
                            <button type="button" class="symptom-chip text-[11px] px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700 hover:border-[#012D68] hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-600 dark:text-slate-300 transition-colors" data-symptom="Sakit kepala berdenyut">+ Sakit Kepala</button>
                            <button type="button" class="symptom-chip text-[11px] px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700 hover:border-[#012D68] hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-600 dark:text-slate-300 transition-colors" data-symptom="Demam tinggi">+ Demam</button>
                            <button type="button" class="symptom-chip text-[11px] px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700 hover:border-[#012D68] hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-600 dark:text-slate-300 transition-colors" data-symptom="Pusing & mual">+ Pusing / Vertigo</button>
                            <button type="button" class="symptom-chip text-[11px] px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700 hover:border-[#012D68] hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-600 dark:text-slate-300 transition-colors" data-symptom="Nyeri persendian & kaku">+ Nyeri Sendi</button>
                            <button type="button" class="symptom-chip text-[11px] px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700 hover:border-[#012D68] hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-600 dark:text-slate-300 transition-colors" data-symptom="Sesak napas ringan">+ Sesak Napas</button>
                            <button type="button" class="symptom-chip text-[11px] px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700 hover:border-[#012D68] hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-600 dark:text-slate-300 transition-colors" data-symptom="Nyeri dada sebelah kiri">+ Nyeri Dada</button>
                        </div>

                        <textarea id="reg-symptoms" rows="3" required placeholder="Jelaskan secara spesifik keluhan atau rasa sakit yang Anda alami..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-[13px] focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all resize-none"></textarea>
                        <p id="symptoms-error" class="hidden text-rose-500 text-[11.5px] mt-1 font-medium">Mohon tuliskan gejala atau keluhan yang dirasakan.</p>
                    </div>

                    <!-- Duration (Sudah Berapa Lama) (Wajib) -->
                    <div>
                        <label class="block text-[12.5px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Sudah Berapa Lama Dirasakan? <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="duration-selector">
                            <button type="button" class="duration-pill py-2 px-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-center text-[12px] font-medium text-slate-700 dark:text-slate-300 hover:border-[#012D68] transition-all" data-duration="< 24 Jam">
                                &lt; 24 Jam
                                <small class="block text-[10px] text-slate-400 font-normal">Baru muncul</small>
                            </button>
                            <button type="button" class="duration-pill active py-2 px-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-center text-[12px] font-medium text-slate-700 dark:text-slate-300 hover:border-[#012D68] transition-all" data-duration="1 - 3 Hari">
                                1 - 3 Hari
                                <small class="block text-[10px] text-slate-400 font-normal">Beberapa hari</small>
                            </button>
                            <button type="button" class="duration-pill py-2 px-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-center text-[12px] font-medium text-slate-700 dark:text-slate-300 hover:border-[#012D68] transition-all" data-duration="1 - 2 Minggu">
                                1 - 2 Minggu
                                <small class="block text-[10px] text-slate-400 font-normal">Subakut</small>
                            </button>
                            <button type="button" class="duration-pill py-2 px-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-center text-[12px] font-medium text-slate-700 dark:text-slate-300 hover:border-[#012D68] transition-all" data-duration="> 1 Bulan">
                                &gt; 1 Bulan
                                <small class="block text-[10px] text-slate-400 font-normal">Kronis / Menahun</small>
                            </button>
                        </div>
                        <input type="hidden" id="reg-duration" value="1 - 3 Hari">
                    </div>

                    <!-- Severity Level -->
                    <div>
                        <label class="block text-[12.5px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tingkat Keparahan / Skala Gangguan
                        </label>
                        <div class="grid grid-cols-3 gap-2" id="severity-selector">
                            <button type="button" class="severity-pill py-2 px-2 rounded-xl border border-slate-200 dark:border-slate-700 text-center text-[12px] font-medium text-slate-700 dark:text-slate-300 hover:border-emerald-500 transition-all" data-severity="Ringan">
                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1"></span> Ringan
                                <small class="block text-[10px] text-slate-400 font-normal">Bisa beraktivitas</small>
                            </button>
                            <button type="button" class="severity-pill active py-2 px-2 rounded-xl border border-slate-200 dark:border-slate-700 text-center text-[12px] font-medium text-slate-700 dark:text-slate-300 hover:border-amber-500 transition-all" data-severity="Sedang">
                                <span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-1"></span> Sedang
                                <small class="block text-[10px] text-slate-400 font-normal">Terganggu</small>
                            </button>
                            <button type="button" class="severity-pill py-2 px-2 rounded-xl border border-slate-200 dark:border-slate-700 text-center text-[12px] font-medium text-slate-700 dark:text-slate-300 hover:border-rose-500 transition-all" data-severity="Berat">
                                <span class="inline-block w-2 h-2 rounded-full bg-rose-500 mr-1"></span> Berat
                                <small class="block text-[10px] text-slate-400 font-normal">Butuh segera</small>
                            </button>
                        </div>
                        <input type="hidden" id="reg-severity" value="Sedang">
                    </div>

                    <!-- Additional Notes / Allergies (Optional) -->
                    <div>
                        <label for="reg-notes" class="block text-[12.5px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Riwayat Alergi / Catatan Tambahan <span class="text-slate-400 text-[11px] font-normal">(Opsional)</span>
                        </label>
                        <input type="text" id="reg-notes" placeholder="Contoh: Alergi obat penisilin, riwayat asam lambung..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-[13px] focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                    </div>
                </form>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 sticky bottom-0">
                <button type="button" id="btn-cancel-booking" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold text-[13px] transition-colors">
                    Batal
                </button>
                <button type="button" id="btn-confirm-booking" class="px-6 py-2.5 rounded-xl bg-[#012D68] hover:bg-[#023E8A] active:bg-[#001F4D] text-white font-bold text-[13.5px] transition-all shadow-md flex items-center gap-2">
                    <span>Konfirmasi Pemesanan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- 4. BILLING DRAWER & CASH PAYMENT POP-UP (ADAPTED SPEC)  -->
    <!-- ======================================================= -->
    
    <!-- Background Overlay for Drawer & Payment Modal -->
    <div class="overlay bill-overlay" id="overlay"></div>

    <!-- DRAWER: Detail Bill (#BILL00124) -->
    <aside class="drawer bill-drawer" id="drawer" aria-label="Detail tagihan konsultasi">
        <!-- Drawer Header -->
        <div class="d-head">
            <div class="id">
                <span>Bill ID</span>
                <b>#BILL00124</b>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    Ortopedi
                </span>
            </div>
            <div class="sp"></div>
            <button type="button" class="drawer-btn" onclick="window.showToast('Membuka riwayat konsultasi & catatan dokter...')">
                <svg style="width:16px;height:16px"><use href="#i-chat"/></svg>
                <span>Catatan</span>
            </button>
            <button type="button" class="drawer-btn icon-only" aria-label="Lainnya" onclick="window.showToast('Menu opsi tagihan')">
                <svg style="width:16px;height:16px"><use href="#i-more"/></svg>
            </button>
            <div class="vline"></div>
            <button type="button" class="drawer-btn icon-only hover:!bg-rose-50 hover:!text-rose-600" id="closeDrawer" aria-label="Tutup tagihan">
                <svg style="width:16px;height:16px"><use href="#i-x"/></svg>
            </button>
        </div>

        <!-- Drawer Body -->
        <div class="d-body">
            <span class="lbl">Select Account</span>
            <div class="select">
                <span class="dot"><svg><use href="#i-pulse"/></svg></span>
                <div class="leading-tight">
                    <span class="block text-[13px] font-bold">Treatment fund</span>
                    <small class="text-[11px] text-slate-500">Klinik Maristella · Rawat Jalan</small>
                </div>
                <svg class="chev"><use href="#i-down"/></svg>
            </div>

            <!-- Bill To Section -->
            <div class="billto">
                <div>
                    <div class="ov">Bill to</div>
                    <div class="nm">{{ $user['name'] }}</div>
                    <div class="ad">Jl. Boulevard Maristella No. 45, Kebayoran Baru,<br>Jakarta Selatan 12170</div>
                </div>
                <div class="r">
                    <div class="ov">Bill date</div>
                    <b style="font-weight:600">14/08/2026</b>
                    <span id="drawer-bill-badge" class="badge unpaid">UNPAID</span>
                </div>
            </div>

            <!-- Keputusan Dokter Pemeriksa (Readonly) -->
            <div class="mb-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-[12px]">
                <span class="lbl" style="margin-bottom:4px">Keputusan Dokter Pemeriksa</span>
                <div class="flex items-center justify-between mt-1">
                    <span id="drawer-tariff-name" class="font-bold text-[#012D68] dark:text-blue-300">
                        {{ ($nextAppointment['tariff_plan'] ?? 'with_medicine') === 'with_medicine' ? 'Konsultasi + Resep Obat' : 'Hanya Konsultasi Dokter' }}
                    </span>
                    <b id="drawer-tariff-badge-price" class="text-[#012D68] dark:text-blue-400 text-[13px]">
                        Rp {{ number_format((float) ($nextAppointment['total_amount'] ?? 435000), 0, ',', '.') }}
                    </b>
                </div>
                <small class="text-slate-500 dark:text-slate-400 text-[11px] block mt-1">
                    *Jenis tindakan dan kebutuhan obat ditentukan oleh dokter saat pemeriksaan medis.
                </small>
            </div>

            <!-- Itemized Services & Medicine List (from doctor's examination) -->
            @php
                $billMedications = $bill['medications'] ?? [];
                $formatRupiah = fn ($value) => 'Rp '.number_format((float) $value, 0, ',', '.');
            @endphp
            <script type="application/json" id="bill-data">@json($bill)</script>
            <div class="items">
                <div class="h">
                    <span>Bill Name</span>
                    <span>Amount</span>
                </div>
                <div class="row">
                    <svg><use href="#i-tooth"/></svg>
                    <div>
                        <span id="drawer-item-treatment-title">Jasa Konsultasi (1)</span>
                        <small id="drawer-item-treatment-desc" class="block text-[11px] text-slate-400 font-normal">{{ $bill['doctor_name'] }}{{ $bill['specialty'] ? ' · '.$bill['specialty'] : '' }}</small>
                    </div>
                    <span class="tt" id="drawer-item-treatment-amount">{{ $formatRupiah($bill['consultation_fee']) }}</span>
                </div>
                <div class="row" id="drawer-row-medicine">
                    <svg><use href="#i-pill"/></svg>
                    <div>
                        <span id="drawer-item-medicine-title">Obat ({{ count($billMedications) }})</span>
                        <small id="drawer-item-medicine-desc" class="block text-[11px] text-slate-400 font-normal">
                            {{ count($billMedications) ? collect($billMedications)->pluck('name')->implode(', ') : 'Tidak ada resep obat dari dokter' }}
                        </small>
                    </div>
                    <span class="tt" id="drawer-item-medicine-amount">{{ $formatRupiah($bill['medicine_cost']) }}</span>
                </div>
                <button type="button" class="btn view" id="btn-toggle-item-breakdown" aria-expanded="true" aria-controls="items-breakdown-panel">
                    <span id="view-details-text">Sembunyikan Rincian</span>
                </button>
            </div>

            <!-- Detailed Breakdown: consultation + every prescribed medicine with qty x price -->
            <div id="items-breakdown-panel" class="-mt-2 mb-4 p-3 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 text-[11.5px] space-y-2">
                <div class="flex justify-between gap-3 text-slate-600 dark:text-slate-300">
                    <span>Jasa Konsultasi <span data-bill-doctor>{{ $bill['doctor_name'] }}</span></span>
                    <b class="font-semibold whitespace-nowrap" data-bill-consultation>{{ $formatRupiah($bill['consultation_fee']) }}</b>
                </div>
                <div class="pt-2 border-t border-dashed border-slate-200 dark:border-slate-700">
                    <span class="block text-[10.5px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Resep Obat dari Dokter</span>
                    <ul id="drawer-medicine-list" class="space-y-2">
                        @forelse ($billMedications as $medication)
                            <li class="flex justify-between gap-3">
                                <div class="min-w-0">
                                    <b class="block font-semibold text-slate-800 dark:text-slate-100">{{ $medication['name'] }}</b>
                                    <small class="block text-[10.5px] text-slate-500 dark:text-slate-400">
                                        {{ $medication['quantity'] }} {{ $medication['satuan'] }} × {{ $formatRupiah($medication['harga']) }}
                                    </small>
                                    @if ($medication['frequency'] || $medication['dose'])
                                        <small class="block text-[10.5px] text-slate-400">Aturan pakai: {{ trim($medication['dose'].' · '.$medication['frequency'], ' ·') }}</small>
                                    @endif
                                </div>
                                <b class="font-semibold whitespace-nowrap text-slate-700 dark:text-slate-200">{{ $formatRupiah($medication['subtotal']) }}</b>
                            </li>
                        @empty
                            <li class="italic text-slate-400">Dokter tidak meresepkan obat (hanya konsultasi).</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Note Section -->
            <div class="note-box">
                <div class="note-h">
                    <span class="lbl" style="margin-bottom:0">Add Note <small>(Optional)</small></span>
                    <small id="cnt">0/200</small>
                </div>
                <textarea id="note" maxlength="200" placeholder="Type a message or pharmacy note..."></textarea>
            </div>

            <!-- Total Price Summary -->
            <div class="sum">
                <span>Subtotal</span>
                <span id="drawer-subtotal-display">{{ $formatRupiah($bill['total_amount']) }}</span>
            </div>
            <div class="sum">
                <span style="display:flex;gap:6px;align-items:center">
                    Tax (0%) <svg style="width:14px;height:14px"><use href="#i-info"/></svg>
                </span>
                <span>Rp 0</span>
            </div>
            <div class="sum total">
                <span>Total</span>
                <span id="drawer-total-display">{{ $formatRupiah($bill['total_amount']) }}</span>
            </div>

            <!-- Security Assurance -->
            <div class="secure">
                <svg><use href="#i-shield"/></svg>
                <span>All your transactions are secure and fast with Klinik Maristella.</span>
            </div>

            <!-- Drawer Paid Notice and Action -->
            <div id="drawer-paid-actions" class="hidden mb-4 p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center">
                <div class="flex items-center justify-center gap-1.5 text-emerald-700 dark:text-emerald-300 font-bold text-[13px] mb-1">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Tagihan <span data-bill-code>{{ $bill['booking_code'] }}</span> Lunas</span>
                </div>
                <p class="text-[11.5px] text-emerald-600 dark:text-emerald-400 mb-2.5">Pembayaran resmi telah tercatat dan kuitansi telah terbit.</p>
                <button type="button" class="btn-open-success-modal w-full h-9 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[12px] flex items-center justify-center gap-1.5 transition-all shadow-xs">
                    <svg class="w-4 h-4"><use href="#i-receipt"/></svg>
                    <span>Buka Kuitansi Resmi</span>
                </button>
            </div>

            <!-- Payment Method Selection -->
            <div class="pay" id="drawer-unpaid-actions">
                <div class="ov">Select a payment method</div>
                <button type="button" class="method" id="openCash">
                    <svg><use href="#i-cash"/></svg>
                    <div>
                        <span class="block">Cash (Tunai di Kasir)</span>
                        <small class="block text-[11px] text-slate-400 font-normal">Bayar langsung di kasir poliklinik</small>
                    </div>
                    <svg class="chev"><use href="#i-chev"/></svg>
                </button>
                <button type="button" class="method" id="openCardMethod" onclick="window.showToast('Metode Kartu Kredit/Debit dipilih. Silakan gesek kartu di kasir.')">
                    <svg><use href="#i-card"/></svg>
                    <div>
                        <span class="block">Credit / Debit card</span>
                        <small class="block text-[11px] text-slate-400 font-normal">Visa, Mastercard, JCB, GPN</small>
                    </div>
                    <svg class="chev"><use href="#i-chev"/></svg>
                </button>
                <div id="more-payment-methods" class="hidden space-y-2 mt-2">
                    <button type="button" class="method" onclick="window.showToast('Metode QRIS dipilih. Pindai kode QR dinamis di loket kasir.')">
                        <svg><use href="#i-wallet"/></svg>
                        <div>
                            <span class="block">QRIS / Instant E-Wallet</span>
                            <small class="block text-[11px] text-slate-400 font-normal">GoPay, OVO, Dana, ShopeePay</small>
                        </div>
                        <svg class="chev"><use href="#i-chev"/></svg>
                    </button>
                    <button type="button" class="method" onclick="window.showToast('Nomor Virtual Account: 8899-0814-4350 (BCA/Mandiri)')">
                        <svg><use href="#i-money"/></svg>
                        <div>
                            <span class="block">Virtual Account Bank</span>
                            <small class="block text-[11px] text-slate-400 font-normal">BCA, Mandiri, BRI, BNI</small>
                        </div>
                        <svg class="chev"><use href="#i-chev"/></svg>
                    </button>
                </div>
                <button type="button" class="more" id="btn-show-more-methods">
                    <span id="show-more-text">Show More</span>
                    <svg id="show-more-icon" style="width:14px;height:14px"><use href="#i-down"/></svg>
                </button>
            </div>
        </div>
    </aside>

    <!-- MODAL: Cash Payment Pop-up Dialog -->
    <div class="modal-wrap">
        <div class="modal" id="modal" role="dialog" aria-modal="true" aria-label="Pembayaran Cash">
            <div class="m-head">
                <span class="flex items-center gap-2">
                    <svg style="width:18px;height:18px;color:#012D68"><use href="#i-cash"/></svg>
                    <span>Pembayaran Cash</span>
                </span>
                <button type="button" class="circ" id="closeModal" aria-label="Kembali ke tagihan">
                    <svg style="width:16px;height:16px"><use href="#i-chev"/></svg>
                </button>
            </div>
            <div class="m-body">
                <div class="m-total">
                    <span>Total Payment</span>
                    <span id="modal-total-display">Rp {{ number_format((float) ($nextAppointment['total_amount'] ?? 435000), 0, ',', '.') }}</span>
                </div>
                <div class="box">
                    <div class="bh flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <svg><use href="#i-money"/></svg>
                            <span class="font-bold">Tarif Resmi Sistem Klinik</span>
                        </div>
                        <span class="text-[10.5px] px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/60 text-[#012D68] dark:text-blue-300 font-bold">Otomatis</span>
                    </div>
                    <div class="bb space-y-2.5">
                        <span class="lbl" style="color:var(--color-text, #0E2A2D);font-weight:700;font-size:11.5px;margin-bottom:6px">PENETAPAN HASIL PEMERIKSAAN DOKTER:</span>

                        <!-- Doctor Decision Card (Readonly) -->
                        <div class="p-3.5 rounded-xl border-2 border-[#012D68] bg-blue-50/70 dark:bg-blue-950/40" id="modal-doctor-decision-card">
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-[#012D68] text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <b id="modal-plan-name" class="text-[13.5px] text-slate-900 dark:text-white font-bold">
                                            {{ ($nextAppointment['tariff_plan'] ?? 'with_medicine') === 'with_medicine' ? 'Konsultasi + Resep Obat Klinik' : 'Hanya Konsultasi Dokter' }}
                                        </b>
                                        <b id="modal-plan-price" class="text-[15px] font-extrabold text-[#012D68] dark:text-blue-400">
                                            Rp {{ number_format((float) ($nextAppointment['total_amount'] ?? 435000), 0, ',', '.') }}
                                        </b>
                                    </div>
                                    <p id="modal-plan-desc" class="text-[11px] text-slate-600 dark:text-slate-300 mt-1 leading-snug">
                                        {{ ($nextAppointment['tariff_plan'] ?? 'with_medicine') === 'with_medicine' ? 'Dokter meresepkan tindakan medis beserta obat farmasi klinik.' : 'Dokter menetapkan sesi konsultasi/edukasi tanpa resep obat farmasi klinik.' }}
                                    </p>
                                    <span class="inline-flex items-center gap-1 mt-2 text-[10.5px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md">
                                        ✓ Ditetapkan oleh Dokter Pemeriksa
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Pasien Notice -->
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-[11px] text-slate-600 dark:text-slate-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                            <span>Nominal tagihan dihitung otomatis berdasarkan tarif dokter dan obat yang diresepkan.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-foot">
                <button type="button" class="btn-primary" id="payBtn">
                    <span id="modal-pay-button-label">Bayar Rp {{ number_format((float) ($nextAppointment['total_amount'] ?? 435000), 0, ',', '.') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- 5. PAYMENT SUCCESS RECEIPT MODAL (ADAPTED FROM SPEC)    -->
    <!-- ======================================================= -->
    <section class="success-modal" id="payment-success-modal" role="dialog" aria-modal="true" aria-labelledby="success-modal-title">
        <button type="button" class="x" id="successCloseBtn" aria-label="Tutup kuitansi">
            <svg style="width:20px;height:20px"><use href="#i-x"/></svg>
        </button>

        <!-- Receipt Illustration with Green Verified Circle -->
        <svg class="illu" viewBox="0 0 110 130" style="stroke:none">
            <path d="M22 28h66a6 6 0 016 6v84l-9-7-9 7-9-7-9 7-9-7-9 7-9-7-9 7V34a6 6 0 016-6z" fill="#E8EEFB"/>
            <path d="M82 28h6a6 6 0 016 6v90l-6-5V34a6 6 0 00-6-6z" fill="#012D68"/>
            <path d="M22 118l0 0 9-7 9 7 9-7 9 7 9-7 9 7 9-7v7l-9 7-9-7-9 7-9-7-9 7-9-7-9 7z" fill="#012D68" opacity="0"/>
            <rect x="40" y="42" width="40" height="7" rx="1" fill="#1F2937"/>
            <rect x="22" y="58" width="30" height="3" rx="1.5" fill="#BFD0F5"/><rect x="58" y="58" width="22" height="3" rx="1.5" fill="#BFD0F5"/>
            <rect x="22" y="68" width="22" height="3" rx="1.5" fill="#BFD0F5"/><rect x="50" y="68" width="14" height="3" rx="1.5" fill="#BFD0F5"/>
            <rect x="22" y="80" width="22" height="3" rx="1.5" fill="#7C9AF0"/><rect x="50" y="80" width="14" height="3" rx="1.5" fill="#7C9AF0"/>
            <path d="M20 108l7 6 7-6 7 6 7-6 7 6 7-6 7 6 7-6 7 6V110l-7 6-7-6-7 6-7-6-7 6-7-6-7 6-7-6z" fill="none"/>
            <path d="M22 112l7 7 7-7 7 7 7-7 7 7 7-7 7 7 7-7V106H22z" fill="#012D68" opacity="0"/>
            <path d="M20 104h72l0 14-9-8-9 8-9-8-9 8-9-8-9 8-9-8-9 8z" fill="#012D68"/>
            <circle cx="20" cy="20" r="18" fill="#22C08B"/>
            <path d="M12 20.5l6 6 11-12" stroke="#fff" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        <h2 id="success-modal-title">Payment success</h2>
        <div class="amt-l">Total Amount Paid</div>
        <div class="amt" id="success-amount">{{ $formatRupiah($bill['total_amount']) }}</div>
        <div class="meta" id="success-meta">{{ now()->translatedFormat('d M Y') }}<i>•</i>Bill ID: <span data-bill-code>{{ $bill['booking_code'] }}</span><i>•</i><span data-bill-specialty>{{ $bill['specialty'] ?: 'Poliklinik' }}</span></div>

        <div class="details">
            <h3>Payment Details</h3>
            <div class="acc">
                <!-- Accordion Item 1: Consultation -->
                <div class="acc-item open" id="success-acc-treatment">
                    <button type="button" class="acc-head" aria-expanded="true">
                        <svg class="ico"><use href="#i-tooth"/></svg>
                        <span id="success-item-treatment-title">Jasa Konsultasi (1)</span>
                        <span class="tt" id="success-item-treatment-amount">{{ $formatRupiah($bill['consultation_fee']) }}</span>
                        <svg class="chev"><use href="#i-down"/></svg>
                    </button>
                    <div class="acc-body" id="success-body-treatment">
                        <div><span>Jasa Konsultasi {{ $bill['doctor_name'] }}</span><span>{{ $formatRupiah($bill['consultation_fee']) }}</span></div>
                    </div>
                </div>

                <!-- Accordion Item 2: Prescribed Medicine -->
                <div class="acc-item open" id="success-acc-medicine">
                    <button type="button" class="acc-head" aria-expanded="true">
                        <svg class="ico"><use href="#i-pill"/></svg>
                        <span id="success-item-medicine-title">Obat ({{ count($billMedications) }})</span>
                        <span class="tt" id="success-item-medicine-amount">{{ $formatRupiah($bill['medicine_cost']) }}</span>
                        <svg class="chev"><use href="#i-down"/></svg>
                    </button>
                    <div class="acc-body" id="success-body-medicine">
                        @forelse ($billMedications as $medication)
                            <div><span>{{ $medication['name'] }} ({{ $medication['quantity'] }} {{ $medication['satuan'] }} × {{ $formatRupiah($medication['harga']) }})</span><span>{{ $formatRupiah($medication['subtotal']) }}</span></div>
                        @empty
                            <div><span class="italic text-slate-400">Pemeriksaan konsultasi saja tanpa resep obat</span><span>Rp 0</span></div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Transaction Metadata -->
            <div class="dt">
                <div class="ov">Detail Transaction</div>
                <div class="r">
                    <span>Pasien</span>
                    <b>{{ $user['name'] }}</b>
                </div>
                <div class="r">
                    <span>Amount paid</span>
                    <b id="success-amount-paid">{{ $formatRupiah($bill['total_amount']) }}</b>
                </div>
                <div class="r">
                    <span>Change money</span>
                    <b>Rp 0</b>
                </div>
                <div class="r">
                    <span>Payment method</span>
                    <b class="m"><svg><use href="#i-cash"/></svg>Cash (Kasir Poliklinik)</b>
                </div>
            </div>
        </div>

        <!-- Modal Actions -->
        <div class="actions">
            <button type="button" class="btn-o" id="successBackBtn">
                <svg style="width:18px;height:18px"><use href="#i-cal"/></svg>
                <span>Back to calendar</span>
            </button>
            <button type="button" class="btn-o primary" id="successPrintBtn">
                <svg style="width:18px;height:18px"><use href="#i-receipt"/></svg>
                <span>Print Receipt</span>
            </button>
        </div>
    </section>
</div>
@endsection

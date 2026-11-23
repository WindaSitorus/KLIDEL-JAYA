<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya — {{ config('app.name', 'KLIDEL-JAYA') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/maristella-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#EFF6FF',
                            100: '#DBEAFE',
                            700: '#023E8A',
                            800: '#012D68',
                            900: '#001F4D',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --brand-primary: #012D68;
            --brand-hover: #023E8A;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #F8FAFC;
            color: #0F172A;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased flex flex-col">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @php
                    $backUrl = route('home');
                    if ($user->isAdmin()) {
                        $backUrl = route('admin.dashboard');
                    } elseif ($user->isDoctor()) {
                        $backUrl = route('doctor.dashboard');
                    } elseif ($user->isApoteker()) {
                        $backUrl = route('apoteker.dashboard');
                    }
                @endphp
                <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Dashboard</span>
                </a>
                <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/maristella-icon.png') }}" alt="Logo" class="h-7 w-auto object-contain">
                    <span class="font-extrabold text-[#012D68] tracking-tight text-base sm:text-lg">Klinik Maristella</span>
                </div>
            </div>

            <!-- Role Badge & Header User Dropdown -->
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                    @if($user->isAdmin()) bg-purple-100 text-purple-700 border border-purple-200
                    @elseif($user->isDoctor()) bg-blue-100 text-blue-800 border border-blue-200
                    @elseif($user->isApoteker()) bg-emerald-100 text-emerald-700 border border-emerald-200
                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                    {{ $user->isAdmin() ? 'Administrator' : ($user->isDoctor() ? 'Dokter Spesialis' : ($user->isApoteker() ? 'Apoteker' : 'Pasien')) }}
                </span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl shadow-xs">
                <div class="flex items-center gap-2 font-bold text-sm mb-1">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Terjadi kesalahan saat memproses data:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-7">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile Page Heading -->
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-[#012D68]">Pengaturan Profil Pengguna</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Kelola data informasi akun, foto profil, dan kata sandi keamanan Anda.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: Profile Overview Card -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col items-center text-center">
                
                <!-- Avatar with Default Fallback -->
                <div class="relative group">
                    <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-slate-100 shadow-md flex items-center justify-center bg-[#012D68] text-white">
                        @if($user->avatar_url)
                            <img id="avatar-preview-img" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span id="avatar-preview-initials" class="text-3xl font-extrabold tracking-wider">{{ $user->initials }}</span>
                            <img id="avatar-preview-img" src="" alt="{{ $user->name }}" class="w-full h-full object-cover hidden">
                        @endif
                    </div>

                    @if($user->avatar)
                        <form action="{{ route('profile.avatar.remove') }}" method="POST" class="mt-2 text-center" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil dan kembali ke avatar default?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[11.5px] font-semibold text-rose-600 hover:text-rose-700 underline">
                                Hapus Foto Profil
                            </button>
                        </form>
                    @endif
                </div>

                <h2 class="mt-4 text-lg font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $user->username ? '@'.$user->username : 'Username belum diatur' }}</p>

                <!-- Role Pill -->
                <div class="mt-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                        @if($user->isAdmin()) bg-purple-50 text-purple-700 border border-purple-200
                        @elseif($user->isDoctor()) bg-blue-50 text-blue-800 border border-blue-200
                        @elseif($user->isApoteker()) bg-emerald-50 text-emerald-700 border border-emerald-200
                        @else bg-slate-50 text-slate-700 border border-slate-200 @endif">
                        {{ $user->isAdmin() ? 'Administrator' : ($user->isDoctor() ? 'Dokter Spesialis' : ($user->isApoteker() ? 'Apoteker' : 'Pasien')) }}
                    </span>
                </div>

                <!-- Profile Meta details -->
                <div class="w-full mt-6 pt-6 border-t border-slate-100 text-left text-xs space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Email</span>
                        <span class="text-slate-700 font-semibold truncate max-w-[170px]" title="{{ $user->email }}">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Telepon</span>
                        <span class="text-slate-700 font-semibold">{{ $user->phone ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">ID Akun</span>
                        <span class="text-slate-700 font-mono font-semibold">#USR-{{ str_pad((string)$user->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Terdaftar Sejak</span>
                        <span class="text-slate-700 font-semibold">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                    </div>

                    @if($user->isDoctor() && $user->doctor)
                        <div class="pt-3 border-t border-slate-100">
                            <span class="block text-[11px] font-bold text-[#012D68] uppercase tracking-wider mb-2">Informasi Dokter Praktik</span>
                            <div class="space-y-1.5 text-[11.5px]">
                                <div class="flex justify-between"><span class="text-slate-400">Poli / Spesialisasi</span><span class="font-semibold text-slate-800">{{ $user->doctor->spesialisasi }}</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Biaya Konsultasi</span><span class="font-semibold text-emerald-700">{{ $user->doctor->price }}</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Jam Praktik</span><span class="font-semibold text-slate-800">{{ $user->doctor->availability }}</span></div>
                            </div>
                        </div>
                    @endif

                    @if($user->isUser() && $user->patient)
                        <div class="pt-3 border-t border-slate-100">
                            <span class="block text-[11px] font-bold text-[#012D68] uppercase tracking-wider mb-2">Rekam Medis Pasien</span>
                            <div class="space-y-1.5 text-[11.5px]">
                                <div class="flex justify-between"><span class="text-slate-400">No. Rekam Medis (MRN)</span><span class="font-mono font-bold text-blue-800">{{ $user->patient->mrn }}</span></div>
                                <div class="flex justify-between"><span class="text-slate-400">Jenis Kelamin</span><span class="font-semibold text-slate-800">{{ $user->patient->gender ?: '-' }}</span></div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <!-- RIGHT COLUMN: Profile Edit & Password Forms -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- CARD 1: Edit Profile Information -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-5 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#012D68] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Ubah Informasi Pribadi</h3>
                            <p class="text-xs text-slate-500">Perbarui biodata akun Anda. Role dan ID akun bersifat permanen.</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
                        @csrf
                        @method('PUT')

                        <!-- Photo Profile Upload -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Unggah Foto Profil Baru</label>
                            <div class="flex items-center gap-4">
                                <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#012D68] file:text-white hover:file:bg-[#023E8A] file:cursor-pointer transition-colors border border-slate-200 rounded-xl p-1.5 focus:outline-none">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                        </div>

                        <!-- Name & Username -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            </div>
                            <div>
                                <label for="username" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Username
                                </label>
                                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="Contoh: rina_anggraini"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            </div>
                        </div>

                        <!-- Email & Phone -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Alamat Email <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            </div>
                            <div>
                                <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nomor Telepon / WhatsApp
                                </label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: +62 812-3456-7890"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            </div>
                        </div>

                        <!-- Read-only Security Fields (Role & Account ID) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1 flex items-center gap-1">
                                    <span>Hak Akses / Role</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </label>
                                <input type="text" value="{{ ucfirst($user->role) }}" disabled
                                    class="w-full px-3.5 py-2 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-500 font-semibold cursor-not-allowed">
                                <p class="text-[10.5px] text-slate-400 mt-1">Role dikelola otomatis oleh sistem.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1 flex items-center gap-1">
                                    <span>ID Akun</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </label>
                                <input type="text" value="#USR-{{ str_pad((string)$user->id, 4, '0', STR_PAD_LEFT) }}" disabled
                                    class="w-full px-3.5 py-2 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-500 font-mono font-semibold cursor-not-allowed">
                                <p class="text-[10.5px] text-slate-400 mt-1">ID unik akun tidak dapat diubah.</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#012D68] hover:bg-[#023E8A] active:bg-[#001F4D] text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                                <span>Simpan Perubahan Profil</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- CARD 2: Change Password -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-5 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Ubah Kata Sandi</h3>
                            <p class="text-xs text-slate-500">Gunakan kata sandi kuat minimal 8 karakter demi keamanan akun Anda.</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.password') }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1">
                                Password Lama <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" id="current_password" name="current_password" required placeholder="Masukkan kata sandi saat ini"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Password Baru <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Konfirmasi Password Baru <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password baru"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:border-[#012D68] focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                                <span>Perbarui Kata Sandi</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-center text-xs text-slate-400">
        <p>© {{ date('Y') }} Klinik Maristella (KLIDEL-JAYA). Sistem Informasi Manajemen Klinik & Rekam Medis Elektronik.</p>
    </footer>

    <!-- Image Preview Script -->
    <script>
        document.getElementById('avatar')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const img = document.getElementById('avatar-preview-img');
                    const initials = document.getElementById('avatar-preview-initials');
                    if (img) {
                        img.src = evt.target.result;
                        img.classList.remove('hidden');
                    }
                    if (initials) {
                        initials.classList.add('hidden');
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>

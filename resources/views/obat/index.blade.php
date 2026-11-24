
@extends('layouts.app')

@section('content')
@php
    $isApoteker = auth()->check() && auth()->user()->role === 'apoteker';
@endphp

<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-8">
    <div class="mx-auto max-w-7xl">

        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#012D68]">
                    Data Obat
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Informasi obat, kategori, harga, dan ketersediaan stok klinik.
                </p>
            </div>

            @if($isApoteker)
                <a href="{{ route('obat.create') }}"
                   class="rounded-xl bg-[#012D68] px-5 py-3 text-center text-sm font-bold text-white hover:bg-[#083E88]">
                    + Tambah Obat
                </a>
            @endif
        </div>

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-5 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-700">
                {{ session('warning') }}
            </div>
        @endif

        {{-- Statistik --}}
        <div class="mb-7 grid gap-4 sm:grid-cols-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Total Obat</p>
                <h2 class="mt-2 text-3xl font-extrabold text-[#012D68]">
                    {{ $totalObat }}
                </h2>
                <p class="mt-1 text-xs text-slate-400">Seluruh data obat</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Obat Aktif</p>
                <h2 class="mt-2 text-3xl font-extrabold text-green-600">
                    {{ $obatAktif }}
                </h2>
                <p class="mt-1 text-xs text-slate-400">Obat tersedia dalam sistem</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Obat Nonaktif</p>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-500">
                    {{ $obatNonaktif }}
                </h2>
                <p class="mt-1 text-xs text-slate-400">Obat yang dinonaktifkan</p>
            </div>

        </div>

        {{-- Pencarian dan Filter --}}
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <form action="{{ route('obat.index') }}" method="GET"
                  class="grid gap-4 sm:grid-cols-3">

                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Cari Obat
                    </label>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari kode, nama, atau kategori obat..."
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Filter Status
                    </label>

                    <select name="status"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none">

                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            Nonaktif
                        </option>

                    </select>
                </div>

                <div class="flex flex-wrap gap-2 sm:col-span-3">
                    <button type="submit"
                            class="rounded-xl bg-[#012D68] px-5 py-3 text-sm font-bold text-white hover:bg-[#083E88]">
                        Cari Data
                    </button>

                    <a href="{{ route('obat.index') }}"
                       class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        Reset
                    </a>
                </div>

            </form>
        </div>

        {{-- Tabel Data Obat --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-5 py-5">
                <h2 class="font-bold text-[#012D68]">
                    Daftar Obat Klinik
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Informasi obat yang tersimpan dalam sistem.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-4">No.</th>
                            <th class="px-5 py-4">Kode Obat</th>
                            <th class="px-5 py-4">Nama Obat</th>
                            <th class="px-5 py-4">Kategori</th>
                            <th class="px-5 py-4">Harga</th>
                            <th class="px-5 py-4">Stok</th>
                            <th class="px-5 py-4">Status</th>

                            @if($isApoteker)
                                <th class="px-5 py-4 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse($obats as $index => $obat)

                            <tr class="hover:bg-slate-50">

                                <td class="px-5 py-4 text-slate-500">
                                    {{ $obats->firstItem() + $index }}
                                </td>

                                <td class="px-5 py-4 font-semibold text-slate-700">
                                    {{ $obat->kode_obat }}
                                </td>

                                <td class="px-5 py-4 font-semibold text-[#012D68]">
                                    {{ $obat->nama_obat }}
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $obat->kategori }}
                                </td>

                                <td class="px-5 py-4 font-semibold text-slate-700">
                                    Rp {{ number_format($obat->harga, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $obat->stok }} {{ $obat->satuan }}
                                </td>

                                <td class="px-5 py-4">
                                    @if($obat->is_active)
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                @if($isApoteker)
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">

                                            <a href="{{ route('obat.edit', $obat->id) }}"
                                               class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                                Ubah
                                            </a>

                                            @if($obat->is_active)
                                                <form action="{{ route('obat.destroy', $obat->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Yakin ingin menonaktifkan obat ini?')">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                        Nonaktifkan
                                                    </button>

                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                @endif

                            </tr>

                        @empty

                            <tr>
                                <td colspan="{{ $isApoteker ? 8 : 7 }}"
                                    class="px-5 py-12 text-center">

                                    <p class="font-semibold text-slate-600">
                                        Data obat tidak ditemukan.
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Coba ubah kata kunci pencarian atau filter status.
                                    </p>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($obats->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $obats->links() }}
                </div>
            @endif

        </div>

        <div class="mt-5 text-xs text-slate-400">
            Menampilkan {{ $obats->count() }} dari {{ $obats->total() }} data obat.
        </div>

    </div>
</div>
@endsection

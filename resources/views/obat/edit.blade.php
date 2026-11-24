
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-8">
    <div class="mx-auto max-w-3xl">

        <div class="mb-8">
            <a href="{{ route('obat.index') }}"
               class="text-sm font-semibold text-blue-700 hover:underline">
                &larr; Kembali ke Data Obat
            </a>

            <h1 class="mt-4 text-2xl font-extrabold text-[#012D68]">
                Ubah Data Obat
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Perbarui informasi obat klinik.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('obat.update', $obat->id) }}" method="POST"
              class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            @csrf
            @method('PUT')

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Kode Obat</label>
                <input type="text" name="kode_obat"
                       value="{{ old('kode_obat', $obat->kode_obat) }}" required
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Obat</label>
                <input type="text" name="nama_obat"
                       value="{{ old('nama_obat', $obat->nama_obat) }}" required
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Kategori</label>
                    <input type="text" name="kategori"
                           value="{{ old('kategori', $obat->kategori) }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Satuan</label>
                    <input type="text" name="satuan"
                           value="{{ old('satuan', $obat->satuan) }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Harga (Rupiah)</label>
                    <input type="number" name="harga"
                           value="{{ old('harga', $obat->harga) }}" min="0" step="0.01" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Stok</label>
                    <input type="number" name="stok"
                           value="{{ old('stok', $obat->stok) }}" min="0" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">{{ old('deskripsi', $obat->deskripsi) }}</textarea>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Status Obat</label>
                <select name="is_active" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="1" {{ old('is_active', $obat->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $obat->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex flex-col-reverse gap-3 pt-3 sm:flex-row sm:justify-end">
                <a href="{{ route('obat.index') }}"
                   class="rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </a>

                <button type="submit"
                        class="rounded-xl bg-[#012D68] px-5 py-3 text-sm font-bold text-white hover:bg-[#083E88]">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

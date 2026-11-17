<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ObatController extends Controller
{
    /**
     * Menampilkan daftar obat.
     */
    public function index(Request $request)
    {
        $query = Obat::query();

        // Pencarian obat
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_obat', 'like', "%{$search}%")
                    ->orWhere('nama_obat', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        // Filter status obat
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        // Data obat
        $obats = $query->latest()->paginate(10)->withQueryString();

        // Statistik obat
        $totalObat = Obat::count();
        $obatAktif = Obat::where('is_active', true)->count();
        $obatNonaktif = Obat::where('is_active', false)->count();

        return view('obat.index', compact(
            'obats',
            'totalObat',
            'obatAktif',
            'obatNonaktif'
        ));
    }

    /**
     * Menampilkan form tambah obat.
     */
    public function create()
    {
        return view('obat.create');
    }

    /**
     * Menyimpan data obat baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_obat' => 'required|string|max:255|unique:obats,kode_obat',
            'nama_obat' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'satuan' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Obat::create($validated);

        return redirect()
            ->route('obat.index')
            ->with('success', 'Data obat berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail obat.
     */
    public function show(Obat $obat)
    {
        return redirect()->route('obat.edit', $obat->id);
    }

    /**
     * Menampilkan form edit obat.
     */
    public function edit(Obat $obat)
    {
        return view('obat.edit', compact('obat'));
    }

    /**
     * Memperbarui data obat.
     */
    public function update(Request $request, Obat $obat)
    {
        $validated = $request->validate([
            'kode_obat' => [
                'required',
                'string',
                'max:255',
                Rule::unique('obats', 'kode_obat')->ignore($obat->id),
            ],
            'nama_obat' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'satuan' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $obat->update($validated);

        return redirect()
            ->route('obat.index')
            ->with('success', 'Data obat berhasil diperbarui.');
    }

    /**
     * Menonaktifkan obat.
     */
    public function destroy(Obat $obat)
    {
        $obat->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('obat.index')
            ->with('success', 'Obat berhasil dinonaktifkan.');
    }
}

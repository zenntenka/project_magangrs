<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUnitRequest;
use App\Http\Requests\Admin\UpdateUnitRequest;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * Menampilkan daftar master data unit rumah sakit.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $units = Unit::withCount(['tickets', 'users'])
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.units.index', compact('units', 'search'));
    }

    /**
     * Menampilkan form penambahan unit baru.
     */
    public function create(): View
    {
        return view('admin.units.create');
    }

    /**
     * Menyimpan unit baru ke database.
     */
    public function store(StoreUnitRequest $request): RedirectResponse
    {
        Unit::create($request->validated());

        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit rumah sakit baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit unit.
     */
    public function edit(Unit $unit): View
    {
        return view('admin.units.edit', compact('unit'));
    }

    /**
     * Memperbarui data unit di database.
     */
    public function update(UpdateUnitRequest $request, Unit $unit): RedirectResponse
    {
        $unit->update($request->validated());

        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Data unit berhasil diperbarui.');
    }

    /**
     * Menghapus unit.
     * Mencegah penghapusan jika unit sudah pernah digunakan pada tiket untuk menjaga integritas data.
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        $ticketCount = $unit->tickets()->count();
        if ($ticketCount > 0) {
            return back()->with('error', "Unit \"{$unit->nama}\" tidak dapat dihapus karena masih digunakan oleh {$ticketCount} tiket pengaduan.");
        }

        $userCount = $unit->users()->count();
        if ($userCount > 0) {
            return back()->with('error', "Unit \"{$unit->nama}\" tidak dapat dihapus karena masih terhubung dengan {$userCount} akun staf/pengguna.");
        }

        $unit->delete();

        return redirect()
            ->route('admin.units.index')
            ->with('success', "Unit \"{$unit->nama}\" berhasil dihapus.");
    }
}


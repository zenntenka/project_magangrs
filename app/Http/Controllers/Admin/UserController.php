<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna sistem dengan filter dan pencarian.
     */
    public function index(Request $request): View
    {
        $role = $request->input('role');
        $unitId = $request->input('unit_id');
        $search = $request->input('search');

        $users = User::with('unit')
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->when($unitId, function ($query, $unitId) {
                $query->where('unit_id', $unitId);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $units = Unit::orderBy('nama')->get();

        return view('admin.users.index', compact('users', 'units', 'role', 'unitId', 'search'));
    }

    /**
     * Menampilkan form penambahan pengguna baru.
     */
    public function create(): View
    {
        $units = Unit::orderBy('nama')->get();

        return view('admin.users.create', compact('units'));
    }

    /**
     * Menyimpan pengguna baru ke sistem.
     * Password default di-hash (default: password123 jika tidak diisi manual).
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Tetapkan password default 'password123' jika tidak diisi manual
        $passwordPlain = $data['password'] ?? 'password123';
        $data['password'] = Hash::make($passwordPlain);

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun pengguna baru berhasil dibuat.');
    }

    /**
     * Menampilkan form edit pengguna.
     */
    public function edit(User $user): View
    {
        $units = Unit::orderBy('nama')->get();

        return view('admin.users.edit', compact('user', 'units'));
    }

    /**
     * Memperbarui profil data akun pengguna.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus akun pengguna.
     * Admin tidak boleh menghapus akunnya sendiri dan pengguna yang memiliki tiket tidak boleh dihapus.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // 1. Admin tidak boleh menghapus akunnya sendiri
        if ((int) $user->id === (int) $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri demi keamanan sistem.');
        }

        // 2. Cegah penghapusan jika pengguna memiliki riwayat tiket atau penugasan
        $reportedCount = $user->reportedTickets()->count();
        if ($reportedCount > 0) {
            return back()->with('error', "Pengguna \"{$user->name}\" tidak dapat dihapus karena tercatat sebagai pelapor di {$reportedCount} tiket.");
        }

        $assignedCount = $user->assignedTickets()->count();
        if ($assignedCount > 0) {
            return back()->with('error', "Pengguna \"{$user->name}\" tidak dapat dihapus karena tercatat sebagai teknisi penanggung jawab di {$assignedCount} tiket.");
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Akun pengguna \"{$user->name}\" berhasil dihapus.");
    }
}


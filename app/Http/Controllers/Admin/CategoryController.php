<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar master data kategori kendala IT.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $categories = Category::withCount('tickets')
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    /**
     * Menampilkan form penambahan kategori kendala baru.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori kendala IT baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit kategori.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Memperbarui kategori di database.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Data kategori kendala berhasil diperbarui.');
    }

    /**
     * Menghapus kategori kendala.
     * Mencegah penghapusan jika kategori sudah digunakan pada tiket keluhan.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $ticketCount = $category->tickets()->count();
        if ($ticketCount > 0) {
            return back()->with('error', "Kategori \"{$category->nama}\" tidak dapat dihapus karena masih digunakan oleh {$ticketCount} tiket pengaduan.");
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->nama}\" berhasil dihapus.");
    }
}


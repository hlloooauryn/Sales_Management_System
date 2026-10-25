<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Tampilkan daftar supplier/pemasok bunga
     */
    public function index()
    {
        $suppliers = Supplier::withCount('purchases')->latest()->get();
        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Form tambah supplier baru (Khusus Admin)
     */
    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.suppliers.create');
    }

    /**
     * Simpan supplier baru (Khusus Admin)
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:suppliers,name',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama pemasok / supplier wajib diisi.',
            'name.unique' => 'Nama pemasok ini sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Data pemasok berhasil ditambahkan.');
    }

    /**
     * Form edit supplier (Khusus Admin)
     */
    public function edit(Supplier $supplier)
    {
        $this->authorizeAdmin();
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update supplier (Khusus Admin)
     */
    public function update(Request $request, Supplier $supplier)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:suppliers,name,' . $supplier->id,
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama pemasok / supplier wajib diisi.',
            'name.unique' => 'Nama pemasok ini sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Data pemasok berhasil diperbarui.');
    }

    /**
     * Hapus data supplier (Khusus Admin)
     */
    public function destroy(Supplier $supplier)
    {
        $this->authorizeAdmin();

        // Cek integritas referensial: jangan hapus jika ada riwayat pembelian
        if ($supplier->purchases()->count() > 0) {
            return back()->with('error', 'Pemasok "' . $supplier->name . '" tidak dapat dihapus karena tercatat dalam ' . $supplier->purchases()->count() . ' riwayat transaksi pembelian barang masuk.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil dihapus.');
    }

    /**
     * Helper proteksi otorisasi khusus Admin
     */
    private function authorizeAdmin(): void
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin/Owner yang dapat memodifikasi data pemasok.');
        }
    }
}

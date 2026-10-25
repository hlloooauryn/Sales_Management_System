<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan identitas brand dan logo
     */
    public function index()
    {
        // Pemeriksaan otorisasi ganda di level server
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin/Owner yang memiliki kewenangan mengubah logo dan identitas merek toko.');
        }

        $storeName = Setting::get('store_name', 'Bloom Moment');
        $tagline = Setting::get('tagline', 'Artisan Florist & Botanicals');
        $phone = Setting::get('phone', '0812-9988-7766');
        $address = Setting::get('address', 'Jl. Cendana No. 18, Kebayoran Baru, Jakarta Selatan');
        $logoUrl = Setting::logoUrl();

        return view('admin.settings.index', compact('storeName', 'tagline', 'phone', 'address', 'logoUrl'));
    }

    /**
     * Simpan pembaruan identitas brand dan logo
     */
    public function update(Request $request)
    {
        // Pemeriksaan otorisasi ganda di level server
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin/Owner yang memiliki kewenangan mengubah logo dan identitas merek toko.');
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'tagline' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ], [
            'store_name.required' => 'Nama toko wajib diisi.',
            'logo.image' => 'File logo harus berupa gambar yang valid.',
            'logo.mimes' => 'Format logo yang didukung: PNG, JPG, JPEG, WEBP, atau SVG.',
            'logo.max' => 'Ukuran file logo maksimal 2MB.',
        ]);

        Setting::set('store_name', $validated['store_name']);
        Setting::set('tagline', $validated['tagline'] ?? '');
        Setting::set('phone', $validated['phone'] ?? '');
        Setting::set('address', $validated['address'] ?? '');

        // Upload logo baru jika disertakan
        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo_path');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('branding', 'public');
            Setting::set('logo_path', $path);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan identitas toko & logo berhasil disimpan.');
    }

    /**
     * Hapus file logo dan kembali ke brandmark default
     */
    public function destroyLogo()
    {
        // Pemeriksaan otorisasi ganda di level server
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Admin/Owner yang memiliki kewenangan mengubah logo toko.');
        }

        $oldLogo = Setting::get('logo_path');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        Setting::set('logo_path', null);

        return redirect()->route('settings.index')->with('success', 'Logo kustom berhasil dihapus. Sistem kembali menampilkan logo default Bloom Moment.');
    }
}

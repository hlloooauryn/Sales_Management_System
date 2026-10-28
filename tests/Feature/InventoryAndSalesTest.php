<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InventoryAndSalesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Uji 1: Pengunjung publik dapat melihat halaman beranda dan katalog tanpa login
     */
    public function test_public_can_view_home_and_catalog_without_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $responseCatalog = $this->get('/catalog');
        $responseCatalog->assertStatus(200);
    }

    /**
     * Uji 2: Pengunjung publik ditolak dan dialihkan saat membuka halaman internal
     */
    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Uji 3: Staff dapat login dan mengakses dashboard operasional
     */
    public function test_staff_can_login_and_access_dashboard(): void
    {
        $staff = User::where('role', 'staff')->first();
        $this->assertNotNull($staff);

        $response = $this->actingAs($staff)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    /**
     * Uji 4: Staff DITOLAK (403 Forbidden) saat membuka menu khusus Admin (users, categories, settings, reports)
     *        dan Staff DAPAT mengakses menu pembelian (200 OK)
     */
    public function test_staff_cannot_access_admin_only_routes_but_can_access_purchases(): void
    {
        $staff = User::where('role', 'staff')->first();

        $responseUsers = $this->actingAs($staff)->get('/admin/users');
        $responseUsers->assertStatus(403);

        $responseCategories = $this->actingAs($staff)->get('/admin/categories');
        $responseCategories->assertStatus(403);

        // Staff diizinkan mengakses menu pembelian dan restock produk
        $responsePurchases = $this->actingAs($staff)->get('/admin/purchases');
        $responsePurchases->assertStatus(200);

        $responsePurchasesCreate = $this->actingAs($staff)->get('/admin/purchases/create');
        $responsePurchasesCreate->assertStatus(200);
    }

    /**
     * Uji 5: Admin dapat mengakses menu manajemen pengguna dan kategori
     */
    public function test_admin_can_access_user_and_category_management(): void
    {
        $admin = User::where('role', 'admin')->first();

        $responseUsers = $this->actingAs($admin)->get('/admin/users');
        $responseUsers->assertStatus(200);

        $responseCategories = $this->actingAs($admin)->get('/admin/categories');
        $responseCategories->assertStatus(200);

        // Admin dapat mengakses halaman pembelian dan restok
        $responsePurchases = $this->actingAs($admin)->get('/admin/purchases');
        $responsePurchases->assertStatus(200);
    }

    /**
     * Uji 6: Transaksi Pembelian barang masuk oleh Admin otomatis menambah stok fisik produk
     */
    public function test_purchase_transaction_increments_stock_automatically(): void
    {
        $admin = User::where('role', 'admin')->first();
        $product = Product::first();
        $supplier = Supplier::first();

        $initialStock = $product->stock;
        $qtyToBuy = 15;

        $response = $this->actingAs($admin)->post('/admin/purchases', [
            'reference_number' => 'TEST-PUR-' . uniqid(),
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'notes' => 'Uji otomatis pasokan',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $qtyToBuy,
                    'unit_cost' => 10000,
                ]
            ]
        ]);

        $response->assertSessionHasNoErrors();

        $product->refresh();
        $this->assertEquals($initialStock + $qtyToBuy, $product->stock, 'Stok produk harus bertambah tepat sebesar kuantitas pembelian.');
    }

    /**
     * Uji 7: Transaksi Penjualan kasir otomatis mengurangi stok fisik produk
     */
    public function test_sale_transaction_decrements_stock_automatically(): void
    {
        $staff = User::where('role', 'staff')->first();
        $product = Product::where('stock', '>=', 5)->first();

        $initialStock = $product->stock;
        $qtyToSell = 3;

        $response = $this->actingAs($staff)->post('/admin/sales', [
            'invoice_number' => 'TEST-INV-' . uniqid(),
            'sale_date' => now()->toDateString(),
            'customer_name' => 'Uji Pembeli',
            'payment_method' => 'Cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $qtyToSell,
                ]
            ]
        ]);

        $response->assertSessionHasNoErrors();

        $product->refresh();
        $this->assertEquals($initialStock - $qtyToSell, $product->stock, 'Stok produk harus berkurang tepat sebesar kuantitas penjualan.');
    }

    /**
     * Uji 8: Penjualan DITOLAK jika kuantitas melebihi ketersediaan stok fisik (stok tidak boleh negatif)
     */
    public function test_sale_is_rejected_when_stock_is_insufficient(): void
    {
        $staff = User::where('role', 'staff')->first();
        $product = Product::first();

        $initialStock = $product->stock;
        $excessiveQty = $initialStock + 1000; // Jauh melebihi stok

        $response = $this->actingAs($staff)->post('/admin/sales', [
            'invoice_number' => 'TEST-REJECT-' . uniqid(),
            'sale_date' => now()->toDateString(),
            'customer_name' => 'Uji Pembeli Melebihi Stok',
            'payment_method' => 'Cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $excessiveQty,
                ]
            ]
        ]);

        // Harus ada pesan error di session dan stok fisik tidak boleh berubah
        $response->assertSessionHas('error');

        $product->refresh();
        $this->assertEquals($initialStock, $product->stock, 'Stok fisik tidak boleh berkurang ketika transaksi ditolak.');
    }

    /**
     * Uji 9: Admin dan Staff dapat mengunggah foto saat membuat produk dan mengganti foto saat edit
     * Serta mempertahankan foto lama jika tidak memilih foto baru saat edit
     */
    public function test_staff_and_admin_can_upload_product_photo_and_preserve_old_photo_when_editing(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $staff = User::where('role', 'staff')->first();
        $category = Category::first();

        // 1. Staff mengunggah foto baru saat tambah produk (menggunakan create() agar tidak membutuhkan ekstensi GD)
        $fakeImage = \Illuminate\Http\UploadedFile::fake()->create('bunga_mawar.jpg', 150, 'image/jpeg');
        $uniqueName = 'Bunga Test Upload ' . uniqid();

        $responseCreate = $this->actingAs($staff)->post('/admin/products', [
            'category_id' => $category->id,
            'name' => $uniqueName,
            'description' => 'Deskripsi uji coba foto',
            'buy_price' => 10000,
            'sell_price' => 20000,
            'initial_stock' => 10,
            'min_stock' => 2,
            'image' => $fakeImage,
            'is_active' => '1',
        ]);

        $responseCreate->assertRedirect('/admin/products');
        $responseCreate->assertSessionHasNoErrors();

        $newProduct = Product::where('name', $uniqueName)->first();
        $this->assertNotNull($newProduct);
        $this->assertNotNull($newProduct->image);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($newProduct->image);

        $initialSavedImagePath = $newProduct->image;

        // 2. Edit produk TANPA memilih foto baru -> foto lama harus dipertahankan
        $responseEditNoPhoto = $this->actingAs($staff)->put('/admin/products/' . $newProduct->id, [
            'category_id' => $category->id,
            'name' => $uniqueName . ' Updated',
            'description' => 'Deskripsi diedit',
            'sell_price' => 25000,
            'min_stock' => 3,
            'is_active' => '1',
            // image file tidak disertakan
        ]);

        $responseEditNoPhoto->assertRedirect('/admin/products');
        $newProduct->refresh();
        $this->assertEquals($initialSavedImagePath, $newProduct->image, 'Foto lama harus tetap dipertahankan jika tidak mengunggah foto pengganti.');

        // 3. Edit produk DENGAN foto baru pengganti -> foto terganti
        $replacementImage = \Illuminate\Http\UploadedFile::fake()->create('bunga_pengganti.png', 200, 'image/png');

        $responseEditWithPhoto = $this->actingAs($staff)->put('/admin/products/' . $newProduct->id, [
            'category_id' => $category->id,
            'name' => $uniqueName . ' Updated 2',
            'sell_price' => 25000,
            'min_stock' => 3,
            'image' => $replacementImage,
            'is_active' => '1',
        ]);

        $responseEditWithPhoto->assertRedirect('/admin/products');
        $newProduct->refresh();
        $this->assertNotEquals($initialSavedImagePath, $newProduct->image, 'Path foto harus diperbarui dengan foto pengganti.');
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($newProduct->image);
    }

    /**
     * Uji 10: Hanya Admin/Owner yang dapat membuka dan memperbarui logo & identitas toko;
     * Pengguna Staff DITOLAK (403 Forbidden) di level server.
     */
    public function test_only_admin_can_access_and_update_logo_and_brand_settings(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $staff = User::where('role', 'staff')->first();
        $admin = User::where('role', 'admin')->first();

        // 1. Staff mencoba akses halaman settings -> DITOLAK 403
        $responseStaffGet = $this->actingAs($staff)->get('/admin/settings');
        $responseStaffGet->assertStatus(403);

        // 2. Staff mencoba submit pembaruan logo -> DITOLAK 403
        $responseStaffPost = $this->actingAs($staff)->post('/admin/settings', [
            'store_name' => 'Hacked Name',
        ]);
        $responseStaffPost->assertStatus(403);

        // 3. Admin mengakses halaman settings -> DIIZINKAN 200
        $responseAdminGet = $this->actingAs($admin)->get('/admin/settings');
        $responseAdminGet->assertStatus(200);

        // 4. Admin mengunggah logo baru dan nama toko
        $fakeLogo = \Illuminate\Http\UploadedFile::fake()->create('bloom_logo.png', 100, 'image/png');
        $responseAdminPost = $this->actingAs($admin)->post('/admin/settings', [
            'store_name' => 'Bloom Moment Test Store',
            'tagline' => 'Fresh Botanicals Test',
            'phone' => '0812-9988-7766',
            'address' => 'Jl. Test No. 123',
            'logo' => $fakeLogo,
        ]);

        $responseAdminPost->assertRedirect('/admin/settings');
        $responseAdminPost->assertSessionHasNoErrors();

        $this->assertEquals('Bloom Moment Test Store', \App\Models\Setting::get('store_name'));
        $savedLogo = \App\Models\Setting::get('logo_path');
        $this->assertNotNull($savedLogo);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($savedLogo);

        // 5. Admin menghapus logo kustom kembali ke default
        $responseDelete = $this->actingAs($admin)->delete('/admin/settings/logo');
        $responseDelete->assertRedirect('/admin/settings');
        $this->assertNull(\App\Models\Setting::get('logo_path'));
    }

    /**
     * Uji 11: Halaman Pembelian dan Penjualan dapat difilter berdasarkan tanggal awal dan akhir
     */
    public function test_purchases_and_sales_can_be_filtered_by_date_range(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Filter pembelian berdasarkan tanggal
        $responsePurchases = $this->actingAs($admin)->get('/admin/purchases?start_date=2026-10-01&end_date=2026-10-31');
        $responsePurchases->assertStatus(200);
        $responsePurchases->assertViewHas('purchases');

        // 2. Filter penjualan berdasarkan tanggal
        $responseSales = $this->actingAs($admin)->get('/admin/sales?start_date=2026-10-01&end_date=2026-10-31');
        $responseSales->assertStatus(200);
        $responseSales->assertViewHas('sales');
    }

    /**
     * Uji 12: Halaman Laporan dapat difilter berdasarkan rentang tanggal dan kata kunci pencarian
     */
    public function test_reports_can_be_filtered_by_search_keyword_and_date_with_pagination(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Filter laporan dengan tanggal dan kata kunci pencarian
        $responseReport = $this->actingAs($admin)->get('/admin/reports?start_date=2026-10-01&end_date=2026-10-31&search=INV');
        $responseReport->assertStatus(200);
        $responseReport->assertViewHas('sales');
        $responseReport->assertViewHas('purchases');
        $responseReport->assertViewHas('totalSales');
        $responseReport->assertViewHas('totalSalesCount');
    }

    /**
     * Uji 13: Admin dapat menghapus produk secara konsisten dan aman meskipun sudah memiliki riwayat transaksi (Soft Deletes)
     */
    public function test_admin_can_delete_product_safely_even_if_it_has_transactions(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Ambil produk yang sudah memiliki relasi transaksi
        $product = Product::whereHas('saleItems')->first();
        $this->assertNotNull($product, 'Harus ada produk dengan riwayat penjualan');

        $productId = $product->id;
        $productName = $product->name;

        // Admin melakukan penghapusan produk
        $responseDelete = $this->actingAs($admin)->delete('/admin/products/' . $productId);
        $responseDelete->assertRedirect('/admin/products');
        $responseDelete->assertSessionHas('success');

        // Produk hilang dari daftar aktif
        $this->assertNull(Product::find($productId));

        // Namun data produk tetap tersimpan sebagai soft-deleted tanpa merusak foreign key
        $trashedProduct = Product::withTrashed()->find($productId);
        $this->assertNotNull($trashedProduct);
        $this->assertNotNull($trashedProduct->deleted_at);

        // Riwayat penjualan item masih dapat membaca nama produk tanpa error
        $saleItem = \App\Models\SaleItem::where('product_id', $productId)->first();
        $this->assertNotNull($saleItem);
        $this->assertEquals($productName, $saleItem->product->name);
    }

    /**
     * Uji 14: Staff DITOLAK (403 Forbidden) saat mencoba menghapus produk
     */
    public function test_staff_cannot_delete_product(): void
    {
        $staff = User::where('role', 'staff')->first();
        $product = Product::first();

        $responseDelete = $this->actingAs($staff)->delete('/admin/products/' . $product->id);
        $responseDelete->assertStatus(403);
    }
}

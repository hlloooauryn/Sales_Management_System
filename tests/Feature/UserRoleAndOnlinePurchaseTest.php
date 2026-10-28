<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserRoleAndOnlinePurchaseTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Uji 1: Calon pelanggan dapat mendaftar akun baru dengan role 'user'
     */
    public function test_user_can_register_with_role_user(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso.' . uniqid() . '@example.com',
            'phone' => '081298765432',
            'address' => 'Jl. Tebet Barat No. 20, Jakarta Selatan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('user', $user->role);
        $this->assertTrue($user->isUser());
    }

    /**
     * Uji 2: Pengguna dengan role 'user' dialihkan ke beranda publik setelah login (bukan ke dashboard admin)
     */
    public function test_user_role_can_login_and_is_redirected_to_home(): void
    {
        $user = User::create([
            'name' => 'Siti Pelanggan',
            'email' => 'siti.' . uniqid() . '@example.com',
            'phone' => '081300001111',
            'password' => Hash::make('secret123'),
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Uji 3: Role 'user' ditolak jika mencoba membuka area backoffice admin
     */
    public function test_user_role_cannot_access_backoffice_admin(): void
    {
        $user = User::create([
            'name' => 'Customer Biasa',
            'email' => 'cust.' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    /**
     * Uji 4: Pelanggan dapat memasukkan bunga ke keranjang belanja
     */
    public function test_user_can_add_flowers_to_cart_and_view_cart(): void
    {
        $product = Product::where('is_active', true)->where('stock', '>', 5)->first();
        $this->assertNotNull($product);

        $response = $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(2, session('cart')[$product->id]['quantity']);

        $viewCart = $this->get('/cart');
        $viewCart->assertStatus(200);
        $viewCart->assertSee($product->name);
    }

    /**
     * Uji 5: Pelanggan dapat checkout pesanan online, stok berkurang, dan nomor invoice online terbit
     */
    public function test_user_can_checkout_online_with_payment_method_and_stock_is_decremented(): void
    {
        $user = User::create([
            'name' => 'Nadia Florist Lover',
            'email' => 'nadia.' . uniqid() . '@example.com',
            'phone' => '081234567899',
            'address' => 'Jl. Wijaya No. 8, Kebayoran Baru',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $product = Product::where('is_active', true)->where('stock', '>', 5)->first();
        $initialStock = $product->stock;

        // Isi keranjang
        $cart = [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->sell_price,
                'quantity' => 3,
                'image_url' => $product->image_url,
                'category_name' => 'Hand Bouquet',
                'stock' => $product->stock,
            ]
        ];

        $response = $this->actingAs($user)->withSession(['cart' => $cart])->post('/checkout', [
            'recipient_name' => 'Nadia Florist Lover',
            'customer_phone' => '081234567899',
            'shipping_address' => 'Jl. Wijaya No. 8, Kebayoran Baru, Jakarta Selatan',
            'delivery_date' => now()->addDay()->toDateString(),
            'payment_method' => 'Transfer BCA',
            'notes' => 'Tolong dibungkus kertas warna dusty pink cantik ya.',
        ]);

        // Verifikasi stok fisik berkurang tepat 3 tangkai
        $product->refresh();
        $this->assertEquals($initialStock - 3, $product->stock);

        // Verifikasi transaksi tercatat di database dengan order_type 'online'
        $sale = Sale::where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($sale);
        $this->assertEquals('online', $sale->order_type);
        $this->assertEquals('pending', $sale->status);
        $this->assertEquals('Transfer BCA', $sale->payment_method);
        $this->assertStringStartsWith('INV-ONL-', $sale->invoice_number);

        $response->assertRedirect(route('orders.show', $sale->invoice_number));
    }

    /**
     * Uji 6: Pelanggan dapat melihat panduan pembayaran & rekening bank
     */
    public function test_user_can_view_order_details_and_payment_instructions(): void
    {
        $user = User::create([
            'name' => 'Clara Customer',
            'email' => 'clara.' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $sale = Sale::create([
            'invoice_number' => 'INV-ONL-TEST-0001',
            'user_id' => $user->id,
            'sale_date' => now()->toDateString(),
            'order_type' => 'online',
            'status' => 'pending',
            'customer_name' => 'Clara Customer',
            'customer_phone' => '081122334455',
            'shipping_address' => 'Jl. Senopati No. 45',
            'payment_method' => 'Transfer BCA',
            'total_amount' => 250000,
        ]);

        $response = $this->actingAs($user)->get('/orders/' . $sale->invoice_number);
        $response->assertStatus(200);
        $response->assertSee('8820-1234-5678'); // Rekening BCA
        $response->assertSee('Bloom Moment Florist');
    }

    /**
     * Uji 7: Pelanggan dapat mengunggah bukti pembayaran
     */
    public function test_user_can_upload_payment_proof(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Dewi Customer',
            'email' => 'dewi.' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $sale = Sale::create([
            'invoice_number' => 'INV-ONL-TEST-0002',
            'user_id' => $user->id,
            'sale_date' => now()->toDateString(),
            'order_type' => 'online',
            'status' => 'pending',
            'customer_name' => 'Dewi Customer',
            'payment_method' => 'Transfer BCA',
            'total_amount' => 150000,
        ]);

        $file = UploadedFile::fake()->create('bukti_transfer.png', 200, 'image/png');

        $response = $this->actingAs($user)->post('/orders/' . $sale->invoice_number . '/proof', [
            'payment_proof' => $file,
        ]);

        $response->assertSessionHas('success');

        $sale->refresh();
        $this->assertNotNull($sale->payment_proof);
        $this->assertEquals('paid', $sale->status);
    }

    /**
     * Uji 8: Staf backoffice dapat melihat pesanan online dan memperbarui status pesanan
     */
    public function test_staff_can_view_online_order_in_admin_and_update_status(): void
    {
        $staff = User::where('role', 'staff')->first();
        $this->assertNotNull($staff);

        $sale = Sale::where('order_type', 'online')->first();
        $this->assertNotNull($sale);

        // Buka detail di admin
        $viewAdmin = $this->actingAs($staff)->get('/admin/sales/' . $sale->id);
        $viewAdmin->assertStatus(200);
        $viewAdmin->assertSee('Kelola Pesanan Online Pelanggan');

        // Update status menjadi 'processing' (sedang dirangkai)
        $updateResponse = $this->actingAs($staff)->post('/admin/sales/' . $sale->id . '/status', [
            'status' => 'processing',
        ]);

        $updateResponse->assertSessionHas('success');
        $sale->refresh();
        $this->assertEquals('processing', $sale->status);
    }

    /**
     * Uji 9: Pelanggan dapat melihat panduan pembayaran untuk E-Wallet (GoPay/DANA), VA, dan Retail Minimarket
     */
    public function test_user_can_view_diverse_payment_guides(): void
    {
        $user = User::create([
            'name' => 'Maya E-Wallet User',
            'email' => 'maya.' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // Uji E-Wallet GoPay
        $saleGoPay = Sale::create([
            'invoice_number' => 'INV-ONL-TEST-GOPAY',
            'user_id' => $user->id,
            'sale_date' => now()->toDateString(),
            'order_type' => 'online',
            'status' => 'pending',
            'customer_name' => 'Maya',
            'payment_method' => 'GoPay',
            'total_amount' => 175000,
        ]);
        $responseGoPay = $this->actingAs($user)->get('/orders/' . $saleGoPay->invoice_number);
        $responseGoPay->assertStatus(200);
        $responseGoPay->assertSee('0812-9988-7766');
        $responseGoPay->assertSee('GoPay');

        // Uji Gerai Retail Indomaret
        $saleRetail = Sale::create([
            'invoice_number' => 'INV-ONL-TEST-INDO',
            'user_id' => $user->id,
            'sale_date' => now()->toDateString(),
            'order_type' => 'online',
            'status' => 'pending',
            'customer_name' => 'Maya',
            'payment_method' => 'Indomaret',
            'total_amount' => 200000,
        ]);
        $responseRetail = $this->actingAs($user)->get('/orders/' . $saleRetail->invoice_number);
        $responseRetail->assertStatus(200);
        $responseRetail->assertSee('Indomaret');
        $responseRetail->assertSee('BLM-INDO-');

        // Uji COD (tanpa upload bukti transfer)
        $saleCOD = Sale::create([
            'invoice_number' => 'INV-ONL-TEST-COD',
            'user_id' => $user->id,
            'sale_date' => now()->toDateString(),
            'order_type' => 'online',
            'status' => 'processing',
            'customer_name' => 'Maya',
            'payment_method' => 'COD',
            'total_amount' => 300000,
        ]);
        $responseCOD = $this->actingAs($user)->get('/orders/' . $saleCOD->invoice_number);
        $responseCOD->assertStatus(200);
        $responseCOD->assertSee('Metode Bayar di Tempat (COD)');
        $responseCOD->assertSee('tidak perlu mengunggah bukti transfer');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Pengaturan Awal Toko Bloom Moment
        Setting::set('store_name', 'Bloom Moment');
        Setting::set('tagline', 'Artisan Florist & Botanicals');
        Setting::set('phone', '0812-9988-7766');
        Setting::set('address', 'Jl. Cendana No. 18, Kebayoran Baru, Jakarta Selatan');

        // 1. Akun Pengguna Internal (Admin/Owner dan Staff)
        $admin = User::firstOrCreate(
            ['email' => 'admin@bloommoment.com'],
            [
                'name' => 'Owner Bloom Moment',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@bloommoment.com'],
            [
                'name' => 'Staff Florist',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
            ]
        );

        // 2. Kategori Bunga
        $categoriesData = [
            [
                'name' => 'Fresh Cut Flowers',
                'description' => 'Bunga potong segar pilihan harian untuk dekorasi dan hadiah.',
            ],
            [
                'name' => 'Hand Bouquet',
                'description' => 'Rangkaian buket bunga tangan elegan untuk momen spesial.',
            ],
            [
                'name' => 'Flower Box',
                'description' => 'Bunga premium tertata dalam kotak mewah berbalut pita satin.',
            ],
            [
                'name' => 'Dried & Preserved',
                'description' => 'Bunga abadi dan kering beraroma manis tahan bertahun-tahun.',
            ],
            [
                'name' => 'Table Vase Arrangement',
                'description' => 'Rangkaian vas bunga meja pemanis ruang tamu atau meja kerja.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Str::slug($cat['name']),
                    'description' => $cat['description'],
                ]
            );
        }

        // 3. Pemasok Bunga (Suppliers)
        $suppliersData = [
            [
                'name' => 'PT Lembang Fresh Flora',
                'contact_person' => 'Bpk. Hendra Wijaya',
                'phone' => '0812-3456-7890',
                'email' => 'sales@lembangflora.id',
                'address' => 'Jl. Raya Maribaya No. 45, Lembang, Bandung Barat',
            ],
            [
                'name' => 'CV Mawar Nusantara Batu',
                'contact_person' => 'Ibu Citra Lestari',
                'phone' => '0813-9876-5432',
                'email' => 'info@mawarnusantara.com',
                'address' => 'Kawasan Agro Bunga Sidomulyo, Kota Batu, Jawa Timur',
            ],
            [
                'name' => 'Jakarta Orchid & Exotic Import',
                'contact_person' => 'Bpk. David Tan',
                'phone' => '0811-2233-4455',
                'email' => 'david@jakartaflowerimport.com',
                'address' => 'Sentra Bunga Rawa Belong Blok B No. 12, Jakarta Barat',
            ],
            [
                'name' => 'PT Medan Flora Berastagi',
                'contact_person' => 'Bpk. Faisal Nasution',
                'phone' => '0812-6012-3488',
                'email' => 'sales@medanfloraberastagi.co.id',
                'address' => 'Jl. Jamin Ginting No. 128, Medan Tuntungan, Kota Medan, Sumatera Utara',
            ],
            [
                'name' => 'UD Seulanga Indah Banda Aceh',
                'contact_person' => 'Ibu Cut Nurul Hayati',
                'phone' => '0852-6091-7722',
                'email' => 'kontak@seulangaindah.com',
                'address' => 'Jl. Teuku Nyak Arief No. 45, Syiah Kuala, Kota Banda Aceh, Aceh',
            ],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[] = Supplier::firstOrCreate(
                ['name' => $sup['name']],
                $sup
            );
        }

        // 4. Produk Bunga Realistis
        $productsData = [
            [
                'category' => 'Fresh Cut Flowers',
                'name' => 'Mawar Merah Holland Velvet',
                'description' => 'Mawar merah pekat berukuran besar dengan kelopak lembut bagai beludru. Tahan segar hingga 7 hari.',
                'buy_price' => 15000,
                'sell_price' => 25000,
                'stock' => 45,
                'min_stock' => 10,
                'image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'category' => 'Fresh Cut Flowers',
                'name' => 'Lily Putih Casablanca Wangi',
                'description' => 'Bunga lily putih murni dengan aroma semerbak menenangkan, melambangkan ketulusan dan keanggunan.',
                'buy_price' => 30000,
                'sell_price' => 52000,
                'stock' => 20,
                'min_stock' => 5,
                'image' => 'https://images.unsplash.com/photo-1526047932273-341f2a7631f9?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'category' => 'Fresh Cut Flowers',
                'name' => 'Sunflower Bright Sunshine',
                'description' => 'Bunga matahari mekar sempurna yang membawa aura keceriaan dan energi positif.',
                'buy_price' => 12000,
                'sell_price' => 22000,
                'stock' => 28,
                'min_stock' => 8,
                'image' => 'https://images.unsplash.com/photo-1597848212624-a19eb35e2651?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'category' => 'Fresh Cut Flowers',
                'name' => 'Baby\'s Breath White Cloud',
                'description' => 'Ranting baby\'s breath lebat putih bagai awan lembut, cocok sebagai pelengkap maupun tunggal.',
                'buy_price' => 18000,
                'sell_price' => 32000,
                'stock' => 18,
                'min_stock' => 6,
                'image' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'category' => 'Fresh Cut Flowers',
                'name' => 'Tulip Pastel Dutch Spring',
                'description' => 'Tulip segar import gradasi warna merah muda dan kuning pastel lembut.',
                'buy_price' => 35000,
                'sell_price' => 60000,
                'stock' => 3, // Khusus untuk demonstrasi stok menipis (3 <= min_stock 5)
                'min_stock' => 5,
                'image' => 'https://images.unsplash.com/photo-1520763185298-1b434c919102?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'category' => 'Hand Bouquet',
                'name' => 'Pastel Romance Hand Bouquet',
                'description' => 'Kombinasi mawar merah muda, eustoma putih, dan eucalyptus berbalut wrapping kertas korea rose gold.',
                'buy_price' => 130000,
                'sell_price' => 245000,
                'stock' => 12,
                'min_stock' => 4,
                'image' => 'https://images.unsplash.com/photo-1561181286-d3fee7d55364?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'category' => 'Hand Bouquet',
                'name' => 'Elegance Red Rose Bouquet',
                'description' => 'Buket 12 tangkai mawar merah holland dengan baby\'s breath dan pita satin maroon mewah.',
                'buy_price' => 170000,
                'sell_price' => 310000,
                'stock' => 8,
                'min_stock' => 3,
                'image' => 'https://images.unsplash.com/photo-1525310072745-f49212b5ac6d?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'category' => 'Flower Box',
                'name' => 'Luxury Bloom Box Romantic Pink',
                'description' => 'Kotak silinder blush pink berisi mawar gradasi dan foliage import, dipercantik kartu ucapan kustom.',
                'buy_price' => 190000,
                'sell_price' => 365000,
                'stock' => 6,
                'min_stock' => 3,
                'image' => 'https://images.unsplash.com/photo-1582794543139-8ac9cb0f7b11?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'category' => 'Dried & Preserved',
                'name' => 'Rustic Provence Dried Lavender',
                'description' => 'Bunga lavender kering asli beraroma harum alami yang menenangkan dan awet hingga 2 tahun.',
                'buy_price' => 70000,
                'sell_price' => 135000,
                'stock' => 24,
                'min_stock' => 5,
                'image' => 'https://images.unsplash.com/photo-1508746829417-e6f548d8d6ed?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'category' => 'Table Vase Arrangement',
                'name' => 'Serene Meadow Table Vase',
                'description' => 'Vas kaca minimalis dengan perpaduan bunga lili, krisan putih, dan daun monstera mini.',
                'buy_price' => 155000,
                'sell_price' => 285000,
                'stock' => 7,
                'min_stock' => 2,
                'image' => 'https://images.unsplash.com/photo-1587556930799-8dca6a23dc41?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $prod) {
            $cat = $categories[$prod['category']];
            $createdProducts[] = Product::firstOrCreate(
                ['slug' => Str::slug($prod['name'])],
                [
                    'category_id' => $cat->id,
                    'name' => $prod['name'],
                    'slug' => Str::slug($prod['name']),
                    'description' => $prod['description'],
                    'buy_price' => $prod['buy_price'],
                    'sell_price' => $prod['sell_price'],
                    'stock' => $prod['stock'],
                    'min_stock' => $prod['min_stock'],
                    'image' => $prod['image'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Transaksi Pembelian Sampel (Barang Masuk)
        $purchase = Purchase::firstOrCreate(
            ['reference_number' => 'PB-IN-202610-0001'],
            [
                'supplier_id' => $suppliers[0]->id,
                'user_id' => $admin->id,
                'purchase_date' => now()->subDays(3)->format('Y-m-d'),
                'total_amount' => 750000,
                'notes' => 'Pasokan bunga mawar merah dan baby\'s breath awal pekan.',
            ]
        );

        PurchaseItem::firstOrCreate(
            ['purchase_id' => $purchase->id, 'product_id' => $createdProducts[0]->id],
            [
                'quantity' => 30,
                'unit_cost' => 15000,
                'subtotal' => 450000,
            ]
        );

        PurchaseItem::firstOrCreate(
            ['purchase_id' => $purchase->id, 'product_id' => $createdProducts[3]->id],
            [
                'quantity' => 16,
                'unit_cost' => 18000,
                'subtotal' => 288000,
            ]
        );

        // 6. Transaksi Penjualan Sampel (Barang Keluar)
        $sale = Sale::firstOrCreate(
            ['invoice_number' => 'INV-202610-0001'],
            [
                'user_id' => $staff->id,
                'sale_date' => now()->subDays(1)->format('Y-m-d'),
                'customer_name' => 'Kak Amanda Putri',
                'payment_method' => 'Cash',
                'total_amount' => 295000,
                'notes' => 'Hadiah wisuda teman kampus.',
            ]
        );

        SaleItem::firstOrCreate(
            ['sale_id' => $sale->id, 'product_id' => $createdProducts[0]->id],
            [
                'quantity' => 2,
                'unit_price' => 25000,
                'subtotal' => 50000,
            ]
        );

        SaleItem::firstOrCreate(
            ['sale_id' => $sale->id, 'product_id' => $createdProducts[5]->id],
            [
                'quantity' => 1,
                'unit_price' => 245000,
                'subtotal' => 245000,
            ]
        );
    }
}

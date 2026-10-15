<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Seed Demo Users
        User::updateOrCreate(
            ['email' => 'admin@inventory.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@inventory.local'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('staff'),
            ]
        );

        // 2. Seed Categories
        $elektronik = Category::create([
            'name' => 'Elektronik',
            'description' => 'Perangkat dan komponen elektronik',
        ]);

        $aksesori = Category::create([
            'name' => 'Aksesori',
            'description' => 'Aksesori komputer dan gadget',
        ]);

        $komputer = Category::create([
            'name' => 'Komputer & Laptop',
            'description' => 'Unit komputer, laptop, dan peripheral',
        ]);

        $atk = Category::create([
            'name' => 'Alat Tulis Kantor',
            'description' => 'Perlengkapan ATK operasional gudang dan kantor',
        ]);

        // 3. Seed Suppliers
        $s1 = Supplier::create([
            'name' => 'CV Teknologi Jaya',
            'phone' => '081234567891',
            'email' => 'sales@teknologijaya.co.id',
            'address' => 'Jl. Soekarno Hatta No. 45, Bandung',
        ]);

        $s2 = Supplier::create([
            'name' => 'PT Sumber Makmur',
            'phone' => '081234567890',
            'email' => 'supplier@sumbermakmur.com',
            'address' => 'Kawasan Industri Pulogadung Blok B, Jakarta',
        ]);

        $s3 = Supplier::create([
            'name' => 'PT Digital Mitra Nusantara',
            'phone' => '081987654321',
            'email' => 'contact@digitalmitra.id',
            'address' => 'Jl. Basuki Rahmat No. 88, Surabaya',
        ]);

        // 4. Seed Products (matching P4 dataset)
        $p1 = Product::create([
            'category_id' => $elektronik->id,
            'sku' => 'EL-001',
            'name' => 'Keyboard Mechanical RGB',
            'price' => 125000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);
        $p1->suppliers()->sync([$s1->id, $s2->id]);

        $p2 = Product::create([
            'category_id' => $aksesori->id,
            'sku' => 'AK-001',
            'name' => 'Mouse Pad Speed Edition',
            'price' => 25000,
            'stock' => 40,
            'minimum_stock' => 5,
        ]);
        $p2->suppliers()->sync([$s2->id]);

        $p3 = Product::create([
            'category_id' => $elektronik->id,
            'sku' => 'EL-002',
            'name' => 'Mouse Wireless Silent Click',
            'price' => 85000,
            'stock' => 3, // Low stock indicator triggered
            'minimum_stock' => 5,
        ]);
        $p3->suppliers()->sync([$s1->id]);

        $p4 = Product::create([
            'category_id' => $komputer->id,
            'sku' => 'PC-001',
            'name' => 'Monitor LED IPS 24 Inch',
            'price' => 1450000,
            'stock' => 15,
            'minimum_stock' => 4,
        ]);
        $p4->suppliers()->sync([$s1->id, $s3->id]);

        $p5 = Product::create([
            'category_id' => $aksesori->id,
            'sku' => 'AK-002',
            'name' => 'Webcam Full HD 1080p Autofocus',
            'price' => 320000,
            'stock' => 2, // Low stock indicator triggered
            'minimum_stock' => 5,
        ]);
        $p5->suppliers()->sync([$s2->id, $s3->id]);

        $p6 = Product::create([
            'category_id' => $atk->id,
            'sku' => 'ATK-001',
            'name' => 'Label Barcode Thermal 100x150mm',
            'price' => 45000,
            'stock' => 60,
            'minimum_stock' => 10,
        ]);
        $p6->suppliers()->sync([$s2->id]);

        // 5. Seed Initial Stock Movements
        StockMovement::create([
            'product_id' => $p1->id,
            'type' => 'in',
            'quantity' => 20,
            'note' => 'Stok awal gudang penerimaan supplier',
            'user_name' => 'admin',
            'created_at' => now()->subDays(3),
        ]);

        StockMovement::create([
            'product_id' => $p2->id,
            'type' => 'in',
            'quantity' => 50,
            'note' => 'Penerimaan barang baru dari PT Sumber Makmur',
            'user_name' => 'admin',
            'created_at' => now()->subDays(2),
        ]);

        StockMovement::create([
            'product_id' => $p2->id,
            'type' => 'out',
            'quantity' => 10,
            'note' => 'Pengeluaran pengiriman order cabang #104',
            'user_name' => 'staff',
            'created_at' => now()->subDay(),
        ]);

        StockMovement::create([
            'product_id' => $p3->id,
            'type' => 'in',
            'quantity' => 10,
            'note' => 'Pengiriman barang PO-2026-004',
            'user_name' => 'admin',
            'created_at' => now()->subDays(2),
        ]);

        StockMovement::create([
            'product_id' => $p3->id,
            'type' => 'out',
            'quantity' => 7,
            'note' => 'Penjualan retail store & e-commerce',
            'user_name' => 'staff',
            'created_at' => now()->subHours(6),
        ]);
    }
}

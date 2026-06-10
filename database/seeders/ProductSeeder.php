<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\StoreType;
use App\Models\Subcategory;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $storeType = StoreType::query()->first() ?? StoreType::query()->create([
            'name' => [
                'ar' => 'متجر',
                'en' => 'Store',
            ],
        ]);

        $vendor = Vendor::query()->first() ?? Vendor::query()->create([
            'owner_name' => 'Vendor Owner',
            'phone' => '01000000001',
            'password' => Hash::make('12345678'),
            'store_name' => 'Demo Store',
            'store_type_id' => $storeType->id,
            'latitude' => 30.044420,
            'longitude' => 31.235712,
            'address_description' => 'Demo vendor address',
            'delivery_fee' => 20,
            'is_active' => true,
            'is_verified' => true,
        ]);

        $subcategory = Subcategory::query()
            ->where('store_type_id', $vendor->store_type_id)
            ->first()
            ?? Subcategory::query()->create([
                'store_type_id' => $vendor->store_type_id,
                'vendor_id' => null,
                'name' => [
                    'ar' => 'وجبات',
                    'en' => 'Meals',
                ],
            ]);

        $products = [
            [
                'name' => ['ar' => 'برجر لحم', 'en' => 'Beef Burger'],
                'description' => ['ar' => 'برجر لحم مشوي مع الجبن', 'en' => 'Grilled beef burger with cheese'],
                'brand' => ['ar' => 'مطعم البيت', 'en' => 'Home Restaurant'],
                'quantity' => 30,
                'remaining_quantity' => 24,
                'discount' => 10,
                'is_available' => true,
                'unit' => 'piece',
                'price' => 150,
                'expiry_date' => now()->addMonths(3)->toDateString(),
                'main_image' => '/uploads/products/demo-main-1.jpg',
                'images' => [
                    '/uploads/products/demo-1-1.jpg',
                    '/uploads/products/demo-1-2.jpg',
                ],
            ],
            [
                'name' => ['ar' => 'عصير مانجو', 'en' => 'Mango Juice'],
                'description' => ['ar' => 'عصير مانجو طبيعي طازج', 'en' => 'Fresh natural mango juice'],
                'brand' => ['ar' => 'فريش', 'en' => 'Fresh'],
                'quantity' => 40,
                'remaining_quantity' => 18,
                'discount' => 5,
                'is_available' => true,
                'unit' => 'cup',
                'price' => 60,
                'expiry_date' => now()->addMonth()->toDateString(),
                'main_image' => '/uploads/products/demo-main-2.jpg',
                'images' => [
                    '/uploads/products/demo-2-1.jpg',
                ],
            ],
            [
                'name' => ['ar' => 'سلطة سيزر', 'en' => 'Caesar Salad'],
                'description' => ['ar' => 'سلطة سيزر بالدجاج', 'en' => 'Caesar salad with chicken'],
                'brand' => ['ar' => 'جرين', 'en' => 'Green'],
                'quantity' => 20,
                'remaining_quantity' => 0,
                'discount' => 0,
                'is_available' => false,
                'unit' => 'bowl',
                'price' => 95,
                'expiry_date' => now()->addWeeks(2)->toDateString(),
                'main_image' => '/uploads/products/demo-main-3.jpg',
                'images' => [
                    '/uploads/products/demo-3-1.jpg',
                    '/uploads/products/demo-3-2.jpg',
                ],
            ],
        ];

        foreach ($products as $item) {
            $product = Product::query()->firstOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'name->ar' => $item['name']['ar'],
                ],
                [
                    'vendor_id' => $vendor->id,
                    'subcategory_id' => $subcategory->id,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'brand' => $item['brand'],
                    'quantity' => $item['quantity'],
                    'remaining_quantity' => $item['remaining_quantity'],
                    'discount' => $item['discount'],
                    'is_available' => $item['is_available'],
                    'unit' => $item['unit'],
                    'price' => $item['price'],
                    'expiry_date' => $item['expiry_date'],
                ]
            );

            if (! $product->images()->exists()) {
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image' => $item['main_image'],
                    'is_main' => true,
                ]);

                foreach ($item['images'] as $image) {
                    ProductImage::query()->create([
                        'product_id' => $product->id,
                        'image' => $image,
                        'is_main' => false,
                    ]);
                }
            }
        }
    }
}

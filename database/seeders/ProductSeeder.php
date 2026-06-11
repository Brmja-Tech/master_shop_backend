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
        $vendorsData = [
            [
                'owner_name' => 'Ahmed Restaurant Owner',
                'phone' => '01000000001',
                'store_name' => 'Burger House',
                'store_type_name' => 'مطعم',
                'latitude' => 30.044420,
                'longitude' => 31.235712,
                'address_description' => 'Restaurant demo address',
                'delivery_fee' => 20,
                'rate' => 4.8,
            ],
            [
                'owner_name' => 'Mona Cafe Owner',
                'phone' => '01000000002',
                'store_name' => 'Mona Cafe',
                'store_type_name' => 'كافيه',
                'latitude' => 30.050000,
                'longitude' => 31.240000,
                'address_description' => 'Cafe demo address',
                'delivery_fee' => 15,
                'rate' => 4.2,
            ],
            [
                'owner_name' => 'Khaled Market Owner',
                'phone' => '01000000003',
                'store_name' => 'Fresh Market',
                'store_type_name' => 'سوبر ماركت',
                'latitude' => 30.060000,
                'longitude' => 31.250000,
                'address_description' => 'Supermarket demo address',
                'delivery_fee' => 10,
                'rate' => 3.9,
            ],
            [
                'owner_name' => 'Sara Pharmacy Owner',
                'phone' => '01000000004',
                'store_name' => 'Care Pharmacy',
                'store_type_name' => 'صيدلية',
                'latitude' => 30.070000,
                'longitude' => 31.260000,
                'address_description' => 'Pharmacy demo address',
                'delivery_fee' => 12,
                'rate' => 4.6,
            ],
        ];

        foreach ($vendorsData as $vendorData) {
            $storeType = StoreType::query()
                ->where('name', $vendorData['store_type_name'])
                ->first();

            if (! $storeType) {
                continue;
            }

            $vendor = Vendor::query()->updateOrCreate(
                [
                    'phone' => $vendorData['phone'],
                ],
                [
                    'owner_name' => $vendorData['owner_name'],
                    'password' => Hash::make('12345678'),
                    'store_name' => $vendorData['store_name'],
                    'store_type_id' => $storeType->id,
                    'latitude' => $vendorData['latitude'],
                    'longitude' => $vendorData['longitude'],
                    'address_description' => $vendorData['address_description'],
                    'delivery_fee' => $vendorData['delivery_fee'],
                    'rate' => $vendorData['rate'],
                    'is_active' => true,
                    'is_verified' => true,
                ]
            );

            $subcategory = Subcategory::query()
                ->where('store_type_id', $vendor->store_type_id)
                ->first();

            if (! $subcategory) {
                continue;
            }

            $products = $this->productsForStoreType($vendorData['store_type_name']);

            foreach ($products as $item) {
                $product = Product::query()->firstOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'name' => $item['name'],
                    ],
                    [
                        'vendor_id' => $vendor->id,
                        'subcategory_id' => $subcategory->id,
                        'name' => $item['name'],
                        'description' => $item['description'],
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

    private function productsForStoreType(string $storeTypeName): array
    {
        return match ($storeTypeName) {
            'مطعم' => [
                [
                    'name' => 'برجر لحم',
                    'description' => 'برجر لحم مشوي مع الجبن',
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
                    'name' => 'سلطة سيزر',
                    'description' => 'سلطة سيزر بالدجاج',
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
            ],
            'كافيه' => [
                [
                    'name' => 'عصير مانجو',
                    'description' => 'عصير مانجو طبيعي طازج',
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
                    'name' => 'قهوة تركي',
                    'description' => 'قهوة تركي ساخنة',
                    'quantity' => 50,
                    'remaining_quantity' => 35,
                    'discount' => 0,
                    'is_available' => true,
                    'unit' => 'cup',
                    'price' => 45,
                    'expiry_date' => now()->addMonth()->toDateString(),
                    'main_image' => '/uploads/products/demo-main-4.jpg',
                    'images' => [
                        '/uploads/products/demo-4-1.jpg',
                    ],
                ],
            ],
            'سوبر ماركت' => [
                [
                    'name' => 'عيش بلدي',
                    'description' => 'عيش بلدي طازج',
                    'quantity' => 100,
                    'remaining_quantity' => 80,
                    'discount' => 0,
                    'is_available' => true,
                    'unit' => 'pack',
                    'price' => 20,
                    'expiry_date' => now()->addDays(3)->toDateString(),
                    'main_image' => '/uploads/products/demo-main-5.jpg',
                    'images' => [
                        '/uploads/products/demo-5-1.jpg',
                    ],
                ],
                [
                    'name' => 'لبن كامل الدسم',
                    'description' => 'لبن كامل الدسم 1 لتر',
                    'quantity' => 60,
                    'remaining_quantity' => 42,
                    'discount' => 5,
                    'is_available' => true,
                    'unit' => 'bottle',
                    'price' => 38,
                    'expiry_date' => now()->addDays(10)->toDateString(),
                    'main_image' => '/uploads/products/demo-main-6.jpg',
                    'images' => [
                        '/uploads/products/demo-6-1.jpg',
                    ],
                ],
            ],
            'صيدلية' => [
                [
                    'name' => 'كريم مرطب',
                    'description' => 'كريم مرطب للبشرة',
                    'quantity' => 25,
                    'remaining_quantity' => 20,
                    'discount' => 15,
                    'is_available' => true,
                    'unit' => 'tube',
                    'price' => 120,
                    'expiry_date' => now()->addYear()->toDateString(),
                    'main_image' => '/uploads/products/demo-main-7.jpg',
                    'images' => [
                        '/uploads/products/demo-7-1.jpg',
                    ],
                ],
                [
                    'name' => 'فيتامين سي',
                    'description' => 'أقراص فيتامين سي',
                    'quantity' => 35,
                    'remaining_quantity' => 30,
                    'discount' => 10,
                    'is_available' => true,
                    'unit' => 'box',
                    'price' => 90,
                    'expiry_date' => now()->addYear()->toDateString(),
                    'main_image' => '/uploads/products/demo-main-8.jpg',
                    'images' => [
                        '/uploads/products/demo-8-1.jpg',
                    ],
                ],
            ],
            default => [],
        };
    }
}

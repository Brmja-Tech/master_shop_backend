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
                'rate' => 4.8,
                'logo' => 'uploads/images/image.png',
                'banner' => 'uploads/images/image.png',
            ],
            [
                'owner_name' => 'Mona Cafe Owner',
                'phone' => '01000000002',
                'store_name' => 'Mona Cafe',
                'store_type_name' => 'كافيه',
                'latitude' => 30.050000,
                'longitude' => 31.240000,
                'address_description' => 'Cafe demo address',
                'rate' => 4.2,
                'logo' => 'uploads/images/image.png',
                'banner' => 'uploads/images/image.png',
            ],
            [
                'owner_name' => 'Khaled Market Owner',
                'phone' => '01000000003',
                'store_name' => 'Fresh Market',
                'store_type_name' => 'سوبر ماركت',
                'latitude' => 30.060000,
                'longitude' => 31.250000,
                'address_description' => 'Supermarket demo address',
                'rate' => 3.9,
                'logo' => 'uploads/images/image.png',
                'banner' => 'uploads/images/image.png',
            ],
            [
                'owner_name' => 'Sara Pharmacy Owner',
                'phone' => '01000000004',
                'store_name' => 'Care Pharmacy',
                'store_type_name' => 'صيدلية',
                'latitude' => 30.070000,
                'longitude' => 31.260000,
                'address_description' => 'Pharmacy demo address',
                'rate' => 4.6,
                'logo' => 'uploads/images/image.png',
                'banner' => 'uploads/images/image.png',
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
                    'logo' => $vendorData['logo'],
                    'rate' => $vendorData['rate'],
                    'is_active' => true,
                    'is_verified' => true,
                ]
            );

            $products = $this->productsForStoreType($vendorData['store_type_name']);

            foreach ($products as $item) {
                $subcategory = $this->resolveSubcategory($vendor->store_type_id, $item['subcategory_name'] ?? null);

                if (! $subcategory) {
                    continue;
                }

                $product = Product::query()->updateOrCreate(
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

    private function resolveSubcategory(int $storeTypeId, ?string $subcategoryName): ?Subcategory
    {
        $query = Subcategory::query()->where('store_type_id', $storeTypeId);

        if ($subcategoryName) {
            $query->where('name', $subcategoryName);
        }

        return $query->first()
            ?? Subcategory::query()
                ->where('store_type_id', $storeTypeId)
                ->first();
    }

    private function productsForStoreType(string $storeTypeName): array
    {
        return match ($storeTypeName) {
            'مطعم' => [
                [
                    'name' => 'برجر لحم فاخر',
                    'subcategory_name' => 'وجبات',
                    'description' => 'برجر لحم مشوي مع الجبن والخضار الطازجة',
                    'quantity' => 30,
                    'remaining_quantity' => 24,
                    'discount' => 10,
                    'is_available' => true,
                    'unit' => 'piece',
                    'price' => 150,
                    'expiry_date' => now()->addMonths(3)->toDateString(),
                    'main_image' => 'uploads/products/beef_burger.png',
                    'images' => [],
                ],
                [
                    'name' => 'بيتزا مارجريتا',
                    'subcategory_name' => 'وجبات',
                    'description' => 'بيتزا مارجريتا كلاسيكية بجبنة الموزاريلا والريحان الطازج',
                    'quantity' => 25,
                    'remaining_quantity' => 15,
                    'discount' => 5,
                    'is_available' => true,
                    'unit' => 'piece',
                    'price' => 120,
                    'expiry_date' => now()->addDays(5)->toDateString(),
                    'main_image' => 'uploads/products/pizza.png',
                    'images' => [],
                ],
                [
                    'name' => 'سلطة سيزر بالدجاج',
                    'subcategory_name' => 'مقبلات',
                    'description' => 'سلطة سيزر الطازجة مع قطع الدجاج المشوي والجبن البارميزان',
                    'quantity' => 20,
                    'remaining_quantity' => 10,
                    'discount' => 0,
                    'is_available' => true,
                    'unit' => 'bowl',
                    'price' => 95,
                    'expiry_date' => now()->addDays(2)->toDateString(),
                    'main_image' => 'uploads/products/caesar_salad.png',
                    'images' => [],
                ],
            ],
            'كافيه' => [
                [
                    'name' => 'عصير مانجو طبيعي',
                    'subcategory_name' => 'عصائر',
                    'description' => 'عصير مانجو طبيعي طازج بدون مواد حافظة',
                    'quantity' => 40,
                    'remaining_quantity' => 18,
                    'discount' => 5,
                    'is_available' => true,
                    'unit' => 'cup',
                    'price' => 60,
                    'expiry_date' => now()->addDays(3)->toDateString(),
                    'main_image' => 'uploads/products/mango_juice.png',
                    'images' => [],
                ],
                [
                    'name' => 'قهوة تركي ممتازة',
                    'subcategory_name' => 'قهوة',
                    'description' => 'قهوة تركي ساخنة محضرة من أجود أنواع البن',
                    'quantity' => 50,
                    'remaining_quantity' => 35,
                    'discount' => 0,
                    'is_available' => true,
                    'unit' => 'cup',
                    'price' => 45,
                    'expiry_date' => now()->addMonths(6)->toDateString(),
                    'main_image' => 'uploads/products/turkish_coffee.png',
                    'images' => [],
                ],
                [
                    'name' => 'كرواسون زبدة مقرمش',
                    'subcategory_name' => 'حلويات',
                    'description' => 'كرواسون فرنسي طازج ومقرمش بالزبدة',
                    'quantity' => 30,
                    'remaining_quantity' => 12,
                    'discount' => 0,
                    'is_available' => true,
                    'unit' => 'piece',
                    'price' => 40,
                    'expiry_date' => now()->addDays(3)->toDateString(),
                    'main_image' => 'uploads/products/croissant.png',
                    'images' => [],
                ],
            ],
            'سوبر ماركت' => [
                [
                    'name' => 'لبن كامل الدسم',
                    'subcategory_name' => 'ألبان',
                    'description' => 'لبن بقري كامل الدسم طازج 1 لتر',
                    'quantity' => 60,
                    'remaining_quantity' => 42,
                    'discount' => 5,
                    'is_available' => true,
                    'unit' => 'bottle',
                    'price' => 38,
                    'expiry_date' => now()->addDays(10)->toDateString(),
                    'main_image' => 'uploads/products/milk.png',
                    'images' => [],
                ],
                [
                    'name' => 'جبنة بيضاء طبيعية',
                    'subcategory_name' => 'ألبان',
                    'description' => 'جبنة بيضاء طبيعية فيتا طازجة',
                    'quantity' => 45,
                    'remaining_quantity' => 20,
                    'discount' => 0,
                    'is_available' => true,
                    'unit' => 'pack',
                    'price' => 55,
                    'expiry_date' => now()->addMonths(2)->toDateString(),
                    'main_image' => 'uploads/products/white_cheese.png',
                    'images' => [],
                ],
                [
                    'name' => 'أرز مصري فاخر',
                    'subcategory_name' => 'مخبوزات',
                    'description' => 'أرز مصري بلدي فاخر درجة أولى 1 كجم',
                    'quantity' => 100,
                    'remaining_quantity' => 80,
                    'discount' => 0,
                    'is_available' => true,
                    'unit' => 'pack',
                    'price' => 28,
                    'expiry_date' => now()->addYear()->toDateString(),
                    'main_image' => 'uploads/products/egyptian_rice.png',
                    'images' => [],
                ],
            ],
            'صيدلية' => [
                [
                    'name' => 'كريم مرطب طبيعي',
                    'subcategory_name' => 'عناية بالبشرة',
                    'description' => 'كريم مرطب للبشرة الجافة والحساسة',
                    'quantity' => 25,
                    'remaining_quantity' => 20,
                    'discount' => 15,
                    'is_available' => true,
                    'unit' => 'tube',
                    'price' => 120,
                    'expiry_date' => now()->addYears(2)->toDateString(),
                    'main_image' => 'uploads/products/moisturizer.png',
                    'images' => [],
                ],
                [
                    'name' => 'فيتامين سي فوار',
                    'subcategory_name' => 'فيتامينات',
                    'description' => 'أقراص فيتامين سي فوارة لتعزيز المناعة',
                    'quantity' => 35,
                    'remaining_quantity' => 30,
                    'discount' => 10,
                    'is_available' => true,
                    'unit' => 'box',
                    'price' => 90,
                    'expiry_date' => now()->addYear()->toDateString(),
                    'main_image' => 'uploads/products/vitamin_c.png',
                    'images' => [],
                ],
                [
                    'name' => 'شامبو مغذي للشعر',
                    'subcategory_name' => 'عناية بالبشرة',
                    'description' => 'شامبو مغذي للشعر التالف والجاف بخلاصة الزيوت الطبيعية',
                    'quantity' => 30,
                    'remaining_quantity' => 15,
                    'discount' => 0,
                    'is_available' => true,
                    'unit' => 'bottle',
                    'price' => 110,
                    'expiry_date' => now()->addYears(2)->toDateString(),
                    'main_image' => 'uploads/products/shampoo.png',
                    'images' => [],
                ],
            ],
            default => [],
        };
    }
}

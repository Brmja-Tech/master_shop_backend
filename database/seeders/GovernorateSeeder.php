<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Governorate;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        $egyptId = Country::query()->where('name->en', 'Egypt')->value('id');
        $saudiId = Country::query()->where('name->en', 'Saudi Arabia')->value('id');

        if ($egyptId) {
            $items = [
                [
                    "en" => "Cairo",
                    "ar" => "القاهرة"
                ],
                [
                    "en" => "Giza",
                    "ar" => "الجيزة"
                ],
                [
                    "en" => "Alexandria",
                    "ar" => "الإسكندرية"
                ],
                [
                    "en" => "Dakahlia",
                    "ar" => "الدقهلية"
                ],
                [
                    "en" => "Red Sea",
                    "ar" => "البحر الأحمر"
                ],
                [
                    "en" => "Beheira",
                    "ar" => "البحيرة"
                ],
                [
                    "en" => "Fayoum",
                    "ar" => "الفيوم"
                ],
                [
                    "en" => "Gharbia",
                    "ar" => "الغربية"
                ],
                [
                    "en" => "Ismailia",
                    "ar" => "الإسماعيلية"
                ],
                [
                    "en" => "Menofia",
                    "ar" => "المنوفية"
                ],
                [
                    "en" => "Minya",
                    "ar" => "المنيا"
                ],
                [
                    "en" => "Qaliubia",
                    "ar" => "القليوبية"
                ],
                [
                    "en" => "New Valley",
                    "ar" => "الوادي الجديد"
                ],
                [
                    "en" => "Suez",
                    "ar" => "السويس"
                ],
                [
                    "en" => "Aswan",
                    "ar" => "أسوان"
                ],
                [
                    "en" => "Assiut",
                    "ar" => "أسيوط"
                ],
                [
                    "en" => "Beni Suef",
                    "ar" => "بني سويف"
                ],
                [
                    "en" => "Port Said",
                    "ar" => "بورسعيد"
                ],
                [
                    "en" => "Damietta",
                    "ar" => "دمياط"
                ],
                [
                    "en" => "Sharkia",
                    "ar" => "الشرقية"
                ],
                [
                    "en" => "South Sinai",
                    "ar" => "جنوب سيناء"
                ],
                [
                    "en" => "Kafr El Sheikh",
                    "ar" => "كفر الشيخ"
                ],
                [
                    "en" => "Matrouh",
                    "ar" => "مطروح"
                ],
                [
                    "en" => "Luxor",
                    "ar" => "الأقصر"
                ],
                [
                    "en" => "Qena",
                    "ar" => "قنا"
                ],
                [
                    "en" => "North Sinai",
                    "ar" => "شمال سيناء"
                ],
                [
                    "en" => "Sohag",
                    "ar" => "سوهاج"
                ]
            ];
            foreach ($items as $it) {
                Governorate::updateOrCreate(
                    ['country_id' => $egyptId, 'name->en' => $it['en']],
                    [
                        'country_id'      => $egyptId,
                        'name'            => ['ar' => $it['ar'], 'en' => $it['en']],
                        'shipping_price'  => 50,
                        'status'       => true,
                    ]
                );
            }
        }

        if ($saudiId) {
            $items = [
                [
                    "en" => "Riyadh",
                    "ar" => "الرياض"
                ],
                [
                    "en" => "Makkah",
                    "ar" => "مكة المكرمة"
                ],
                [
                    "en" => "Madinah",
                    "ar" => "المدينة المنورة"
                ],
                [
                    "en" => "Eastern Province",
                    "ar" => "المنطقة الشرقية"
                ],
                [
                    "en" => "Asir",
                    "ar" => "عسير"
                ],
                [
                    "en" => "Tabuk",
                    "ar" => "تبوك"
                ],
                [
                    "en" => "Hail",
                    "ar" => "حائل"
                ],
                [
                    "en" => "Northern Borders",
                    "ar" => "الحدود الشمالية"
                ],
                [
                    "en" => "Jazan",
                    "ar" => "جازان"
                ],
                [
                    "en" => "Najran",
                    "ar" => "نجران"
                ],
                [
                    "en" => "Al Bahah",
                    "ar" => "الباحة"
                ],
                [
                    "en" => "Al Jawf",
                    "ar" => "الجوف"
                ],
                [
                    "en" => "Al Qassim",
                    "ar" => "القصيم"
                ]
            ];
            foreach ($items as $it) {
                Governorate::updateOrCreate(
                    ['country_id' => $saudiId, 'name->en' => $it['en']],
                    [
                        'country_id'      => $saudiId,
                        'name'            => ['ar' => $it['ar'], 'en' => $it['en']],
                        'shipping_price'  => 50,
                        'status'       => true,
                    ]
                );
            }
        }
    }
}

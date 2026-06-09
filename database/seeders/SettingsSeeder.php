<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Terms;
use App\Models\Privacy;
use App\Models\Setting;
use App\Models\Faq;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultImage = 'uploads/images/logo.png';

        $data = [
            // Translatable
            'site_name' => [
                'en' => 'FIX Store',
                'ar' => 'FIX Store',
            ],
            'site_title' => [
                'en' => 'FIX Store — Mobiles & Spare Parts',
                'ar' => 'FIX Store — موبايلات وقطع غيار',
            ],
            'site_desc' => [
                'en' => 'Shop mobiles, accessories, and original spare parts with fast delivery and secure checkout.',
                'ar' => 'تسوّق الموبايلات والإكسسوارات وقطع الغيار الأصلية مع توصيل سريع ودفع آمن.',
            ],
            'site_address' => [
                'en' => 'Cairo, Egypt',
                'ar' => 'القاهرة، مصر',
            ],
            'meta_key' => [
                'en' => 'mobiles, smartphones, spare parts, phone parts, accessories, screens, batteries, chargers',
                'ar' => 'موبايلات, هواتف, قطع غيار, اكسسوارات, شاشات, بطاريات, شواحن',
            ],
            'meta_desc' => [
                'en' => 'FIX Store offers mobiles, accessories, and trusted spare parts with clear pricing and support.',
                'ar' => 'FIX Store يوفر موبايلات وإكسسوارات وقطع غيار موثوقة بأسعار واضحة ودعم سريع.',
            ],

            // Non-translatable
            'site_phone'    => '+201000000000',
            'site_email'    => 'info@fix-store.com',
            'email_support' => 'support@fix-store.com',

            // Social
            'facebook'  => 'https://facebook.com/fixstore',
            'x_url'     => 'https://x.com/fixstore',
            'youtube'   => 'https://youtube.com/@fixstore',
            'instagram' => 'https://instagram.com/fixstore',
            'tiktok'    => 'https://tiktok.com/@fixstore',
            'linkedin'  => 'https://linkedin.com/company/fixstore',
            'whatsapp'  => '+201000000000',

            // Media (use default image everywhere for now)
            'logo'    => $defaultImage,
            'favicon' => $defaultImage,

            // Others
            'site_copyright' => '© ' . now()->year . ' FIX Store. All rights reserved.',
            'promotion_url'  => 'https://fix-store.com/offers',
        ];

        // Update first row if exists, else create
        $existing = Setting::query()->first();
        if ($existing) {
            $existing->update($data);
        } else {
            Setting::query()->create($data);
        }

        // About (idempotent)
        About::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'en' => 'About FIX Store',
                    'ar' => 'عن FIX Store',
                ],
                'desc' => [
                    'en' => 'FIX Store is your destination for mobiles, accessories, and reliable spare parts. We focus on quality, fair pricing, and fast delivery for consumers and merchants.',
                    'ar' => 'FIX Store هو وجهتك للموبايلات والإكسسوارات وقطع الغيار الموثوقة. نركز على الجودة والسعر المناسب والتوصيل السريع للمستهلك والتاجر.',
                ],
                'banner' => $defaultImage,
                'image'  => $defaultImage,
            ]
        );

        // Privacy (idempotent)
        Privacy::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'en' => 'Privacy Policy',
                    'ar' => 'سياسة الخصوصية',
                ],
                'desc' => [
                    'en' => 'We respect your privacy. Your data is used to process orders, provide support, and improve your shopping experience. We do not sell your personal information.',
                    'ar' => 'نحترم خصوصيتك. تُستخدم بياناتك لمعالجة الطلبات وتقديم الدعم وتحسين تجربة التسوق. لا نقوم ببيع بياناتك الشخصية.',
                ],
                'banner' => $defaultImage,
                'image'  => $defaultImage,
            ]
        );

        // Terms (idempotent)
        Terms::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'en' => 'Terms & Conditions',
                    'ar' => 'الشروط والأحكام',
                ],
                'desc' => [
                    'en' => 'By using FIX Store, you agree to our terms regarding orders, payments, returns, and warranty policies. Please read them carefully before purchasing.',
                    'ar' => 'باستخدام FIX Store، أنت توافق على الشروط الخاصة بالطلبات والدفع والاسترجاع وسياسات الضمان. يُرجى قراءتها جيدًا قبل الشراء.',
                ],
                'banner' => $defaultImage,
                'image'  => $defaultImage,
            ]
        );

        // FAQs (idempotent by English question to avoid duplicates)
        $faqs = [
            [
                'question' => [
                    'en' => 'How can I order from FIX Store?',
                    'ar' => 'إزاي أطلب من FIX Store؟',
                ],
                'answer' => [
                    'en' => 'Browse products, add items to your cart, then proceed to checkout and confirm your order.',
                    'ar' => 'اختار المنتجات، ضيفها للسلة، وبعد كده كمل خطوة الدفع وأكد الطلب.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'Do you sell original spare parts?',
                    'ar' => 'هل بتبيعوا قطع غيار أصلية؟',
                ],
                'answer' => [
                    'en' => 'We offer reliable spare parts with clear specifications. Product pages show condition, compatibility, and warranty details when available.',
                    'ar' => 'بنوفّر قطع غيار موثوقة بمواصفات واضحة. صفحة المنتج بتوضح الحالة والتوافق والضمان إن وُجد.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'How long does delivery take?',
                    'ar' => 'التوصيل بياخد قد إيه؟',
                ],
                'answer' => [
                    'en' => 'Delivery time depends on your location. Most orders arrive within 1–3 business days.',
                    'ar' => 'مدة التوصيل بتختلف حسب المكان. أغلب الطلبات بتوصل خلال 1–3 أيام عمل.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'Can merchants buy in bulk?',
                    'ar' => 'هل التاجر يقدر يشتري جملة؟',
                ],
                'answer' => [
                    'en' => 'Yes. Register as a merchant to access business details and support for bulk purchasing.',
                    'ar' => 'أيوه. سجّل كتاجر علشان تضيف بيانات النشاط وتحصل على دعم للشراء بالجملة.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'What is your return policy?',
                    'ar' => 'إيه سياسة الاسترجاع؟',
                ],
                'answer' => [
                    'en' => 'Returns are accepted according to item condition and category. Please contact support with your order number.',
                    'ar' => 'الاسترجاع متاح حسب حالة المنتج ونوعه. تواصل مع الدعم برقم الطلب.',
                ],
                'status' => 1,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::query()->updateOrCreate(
                ['question->en' => $faq['question']['en']],
                $faq
            );
        }
    }
}

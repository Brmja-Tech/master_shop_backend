<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Banner;
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
        $defaultImage = 'uploads/images/logo.jpeg';

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        $data = [
            // Translatable
            'site_name' => [
                'en' => 'Master shop',
                'ar' => 'Master shop',
            ],
            'site_title' => [
                'en' => 'FIX Store — Multi Vendor Marketplace',
                'ar' => 'FIX Store — منصة تسوق متعددة التجار',
            ],
            'site_desc' => [
                'en' => 'FIX Store is a multi-vendor marketplace that brings customers, stores, and trusted vendors together in one simple shopping platform.',
                'ar' => 'FIX Store هي منصة تسوق متعددة التجار تجمع العملاء والمتاجر والبائعين الموثوقين في مكان واحد لتجربة شراء سهلة.',
            ],
            'site_address' => [
                'en' => 'Cairo, Egypt',
                'ar' => 'القاهرة، مصر',
            ],
            'meta_key' => [
                'en' => 'multi vendor marketplace, online shopping, ecommerce, vendors, stores, merchants, products, delivery, offers',
                'ar' => 'منصة متعددة التجار, تسوق أونلاين, تجارة إلكترونية, بائعين, متاجر, تجار, منتجات, توصيل, عروض',
            ],
            'meta_desc' => [
                'en' => 'FIX Store connects customers with multiple vendors and stores, making it easy to browse products, compare options, place orders, and shop with confidence.',
                'ar' => 'FIX Store يربط العملاء بعدة تجار ومتاجر، لتصفح المنتجات ومقارنة الاختيارات وإتمام الطلبات بسهولة وثقة.',
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

            // Media
            'logo'    => $defaultImage,
            'favicon' => $defaultImage,

            // Others
            'site_copyright' => '© ' . now()->year . ' FIX Store. All rights reserved.',
            'promotion_url'  => 'https://fix-store.com/offers',
        ];

        $existing = Setting::query()->first();

        if ($existing) {
            $existing->update($data);
        } else {
            Setting::query()->create($data);
        }

        \App\Models\DeliverySetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'price_per_km' => 5.00,
                'min_delivery_fee' => 15.00,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Banners
        |--------------------------------------------------------------------------
        */

        $banners = [
            [
                'banner' => $defaultImage,
                'status' => 1,
            ],
            [
                'banner' => $defaultImage,
                'status' => 1,
            ],
            [
                'banner' => $defaultImage,
                'status' => 1,
            ],
        ];

        foreach ($banners as $index => $banner) {
            Banner::query()->updateOrCreate(
                ['id' => $index + 1],
                $banner
            );
        }

        /*
        |--------------------------------------------------------------------------
        | About
        |--------------------------------------------------------------------------
        */

        About::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'en' => 'About FIX Store',
                    'ar' => 'عن FIX Store',
                ],
                'desc' => [
                    'en' => 'FIX Store is a multi-vendor marketplace designed to connect customers with trusted stores and sellers. Our platform allows vendors to showcase their products, manage their store, and reach more customers, while giving shoppers a smooth, secure, and reliable shopping experience from different vendors in one place.',
                    'ar' => 'FIX Store هي منصة تسوق متعددة التجار مصممة لربط العملاء بمتاجر وبائعين موثوقين. تتيح المنصة للتجار عرض منتجاتهم وإدارة متاجرهم والوصول لعملاء أكثر، وفي نفس الوقت توفر للعملاء تجربة تسوق سهلة وآمنة وموثوقة من أكثر من تاجر في مكان واحد.',
                ],
                'banner' => $defaultImage,
                'image'  => $defaultImage,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Privacy
        |--------------------------------------------------------------------------
        */

        Privacy::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'en' => 'Privacy Policy',
                    'ar' => 'سياسة الخصوصية',
                ],
                'desc' => [
                    'en' => 'At FIX Store, we respect the privacy of customers and vendors. We collect and use data only to manage accounts, operate stores, process orders, provide delivery updates, improve the marketplace experience, and offer customer support. We do not sell personal information to third parties.',
                    'ar' => 'في FIX Store نحترم خصوصية العملاء والتجار. نقوم بجمع واستخدام البيانات فقط لإدارة الحسابات وتشغيل المتاجر ومعالجة الطلبات وتحديثات التوصيل وتحسين تجربة المنصة وتقديم الدعم. لا نقوم ببيع البيانات الشخصية لأي طرف ثالث.',
                ],
                'banner' => $defaultImage,
                'image'  => $defaultImage,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Terms
        |--------------------------------------------------------------------------
        */

        Terms::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'en' => 'Terms & Conditions',
                    'ar' => 'الشروط والأحكام',
                ],
                'desc' => [
                    'en' => 'By using FIX Store, customers and vendors agree to the marketplace terms related to accounts, store management, product listings, orders, payments, delivery, returns, commissions, and platform policies. Vendors are responsible for the accuracy of their products, prices, and availability.',
                    'ar' => 'باستخدام FIX Store، يوافق العملاء والتجار على شروط المنصة المتعلقة بالحسابات وإدارة المتاجر وعرض المنتجات والطلبات والدفع والتوصيل والاسترجاع والعمولات وسياسات المنصة. يتحمل التجار مسؤولية دقة بيانات المنتجات والأسعار وتوفر المنتجات.',
                ],
                'banner' => $defaultImage,
                'image'  => $defaultImage,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | FAQs
        |--------------------------------------------------------------------------
        */

        $faqs = [
            [
                'question' => [
                    'en' => 'What is FIX Store?',
                    'ar' => 'ما هو FIX Store؟',
                ],
                'answer' => [
                    'en' => 'FIX Store is a multi-vendor marketplace where customers can browse and buy products from different trusted vendors and stores in one platform.',
                    'ar' => 'FIX Store هي منصة تسوق متعددة التجار تتيح للعملاء تصفح وشراء المنتجات من أكثر من تاجر ومتجر موثوق في مكان واحد.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'How can I place an order?',
                    'ar' => 'إزاي أعمل طلب؟',
                ],
                'answer' => [
                    'en' => 'You can browse products or stores, add items to your cart, then complete checkout and confirm your order.',
                    'ar' => 'تقدر تتصفح المنتجات أو المتاجر، تضيف المنتجات للسلة، وبعد كده تكمل خطوات الطلب وتأكد الأوردر.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'Can vendors sell on FIX Store?',
                    'ar' => 'هل التجار يقدروا يبيعوا على FIX Store؟',
                ],
                'answer' => [
                    'en' => 'Yes. Vendors can create their store, add products, manage prices and availability, and receive orders through the platform.',
                    'ar' => 'أيوه. التجار يقدروا ينشئوا متجرهم ويضيفوا المنتجات ويديروا الأسعار والتوفر ويستقبلوا الطلبات من خلال المنصة.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'Are products sold by FIX Store or vendors?',
                    'ar' => 'المنتجات بتتباع من FIX Store ولا من التجار؟',
                ],
                'answer' => [
                    'en' => 'FIX Store is a marketplace. Products are listed by different vendors, and each product may be linked to its related store or seller.',
                    'ar' => 'FIX Store هي منصة ماركت بليس. المنتجات بيتم عرضها من تجار مختلفين، وكل منتج بيكون مرتبط بالمتجر أو البائع الخاص به.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'How long does delivery take?',
                    'ar' => 'التوصيل بياخد قد إيه؟',
                ],
                'answer' => [
                    'en' => 'Delivery time depends on your location, the vendor, product availability, and order processing time.',
                    'ar' => 'مدة التوصيل بتختلف حسب مكانك والتاجر وتوفر المنتج ووقت تجهيز الطلب.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'What is the return policy?',
                    'ar' => 'ما هي سياسة الاسترجاع؟',
                ],
                'answer' => [
                    'en' => 'Returns depend on the product condition, category, and vendor policy. Please contact support with your order details for assistance.',
                    'ar' => 'الاسترجاع بيختلف حسب حالة المنتج ونوعه وسياسة التاجر. تقدر تتواصل مع الدعم ببيانات الطلب للمساعدة.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'How can I contact support?',
                    'ar' => 'إزاي أتواصل مع الدعم؟',
                ],
                'answer' => [
                    'en' => 'You can contact support through email, phone, WhatsApp, or the contact form available on the platform.',
                    'ar' => 'تقدر تتواصل مع الدعم من خلال الإيميل أو الهاتف أو واتساب أو نموذج التواصل المتاح على المنصة.',
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

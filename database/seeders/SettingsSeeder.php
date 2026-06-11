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
        $defaultImage = 'uploads/images/logo.png';

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        $data = [
            // Translatable
            'site_name' => [
                'en' => 'FIX Store',
                'ar' => 'FIX Store',
            ],
            'site_title' => [
                'en' => 'FIX Store — Multi Vendor Marketplace',
                'ar' => 'FIX Store — منصة متعددة التجار',
            ],
            'site_desc' => [
                'en' => 'FIX Store is a multi-vendor marketplace where customers can shop from trusted stores and vendors in one place.',
                'ar' => 'FIX Store هي منصة متعددة التجار تتيح للعملاء التسوق من متاجر وبائعين موثوقين في مكان واحد.',
            ],
            'site_address' => [
                'en' => 'Cairo, Egypt',
                'ar' => 'القاهرة، مصر',
            ],
            'meta_key' => [
                'en' => 'multi vendor, marketplace, vendors, merchants, stores, online shopping, ecommerce, products',
                'ar' => 'متعدد التجار, ماركت بليس, بائعين, تجار, متاجر, تسوق اونلاين, تجارة إلكترونية, منتجات',
            ],
            'meta_desc' => [
                'en' => 'FIX Store connects customers with multiple trusted vendors and stores, offering a smooth shopping experience, clear pricing, and reliable support.',
                'ar' => 'FIX Store يربط العملاء بعدة تجار ومتاجر موثوقة مع تجربة تسوق سهلة وأسعار واضحة ودعم موثوق.',
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

        /*
        |--------------------------------------------------------------------------
        | Banners
        |--------------------------------------------------------------------------
        | جدول banners عندك فيه:
        | id, banner, status, created_at, updated_at
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
                    'en' => 'FIX Store is a multi-vendor marketplace built to connect customers with trusted vendors and stores. We help vendors showcase their products, manage their online presence, and reach more customers while giving shoppers a simple and reliable buying experience.',
                    'ar' => 'FIX Store هي منصة متعددة التجار هدفها ربط العملاء بتجار ومتاجر موثوقين. نساعد التجار على عرض منتجاتهم وإدارة وجودهم أونلاين والوصول لعملاء أكثر، مع توفير تجربة شراء سهلة وموثوقة للعملاء.',
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
                    'en' => 'We respect your privacy. Customer and vendor data is used to manage accounts, process orders, provide support, improve services, and operate the marketplace. We do not sell personal information.',
                    'ar' => 'نحترم خصوصيتك. تُستخدم بيانات العملاء والتجار لإدارة الحسابات ومعالجة الطلبات وتقديم الدعم وتحسين الخدمات وتشغيل المنصة. لا نقوم ببيع البيانات الشخصية.',
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
                    'en' => 'By using FIX Store, customers and vendors agree to our terms regarding accounts, products, orders, payments, delivery, returns, commissions, and marketplace policies.',
                    'ar' => 'باستخدام FIX Store، يوافق العملاء والتجار على الشروط الخاصة بالحسابات والمنتجات والطلبات والدفع والتوصيل والاسترجاع والعمولات وسياسات المنصة.',
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
                    'en' => 'How can I order from FIX Store?',
                    'ar' => 'إزاي أطلب من FIX Store؟',
                ],
                'answer' => [
                    'en' => 'Browse stores or products, add items to your cart, then proceed to checkout and confirm your order.',
                    'ar' => 'تصفح المتاجر أو المنتجات، ضيف المنتجات للسلة، وبعد كده كمل خطوة الدفع وأكد الطلب.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'Can vendors sell on FIX Store?',
                    'ar' => 'هل التجار يقدروا يبيعوا على FIX Store؟',
                ],
                'answer' => [
                    'en' => 'Yes. Vendors can register, add their store details, list products, and manage their sales through the platform.',
                    'ar' => 'أيوه. التجار يقدروا يسجلوا ويضيفوا بيانات المتجر ويعرضوا المنتجات ويديروا المبيعات من خلال المنصة.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'Are products sold by FIX Store or vendors?',
                    'ar' => 'المنتجات بتتباع من FIX Store ولا من التجار؟',
                ],
                'answer' => [
                    'en' => 'FIX Store is a marketplace. Products may be listed by different vendors, and each product page shows the related store or vendor details when available.',
                    'ar' => 'FIX Store هي منصة ماركت بليس. المنتجات ممكن تكون معروضة من تجار مختلفين، وصفحة المنتج بتوضح بيانات المتجر أو التاجر إن وُجدت.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'How long does delivery take?',
                    'ar' => 'التوصيل بياخد قد إيه؟',
                ],
                'answer' => [
                    'en' => 'Delivery time depends on your location, vendor, and product availability. Most orders are processed as quickly as possible.',
                    'ar' => 'مدة التوصيل بتختلف حسب مكانك والتاجر وتوفر المنتج. أغلب الطلبات بيتم تجهيزها في أسرع وقت ممكن.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'What is your return policy?',
                    'ar' => 'إيه سياسة الاسترجاع؟',
                ],
                'answer' => [
                    'en' => 'Returns depend on the product condition, category, and vendor policy. Please contact support with your order number for assistance.',
                    'ar' => 'الاسترجاع بيختلف حسب حالة المنتج ونوعه وسياسة التاجر. تواصل مع الدعم برقم الطلب للمساعدة.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'How can I contact support?',
                    'ar' => 'إزاي أتواصل مع الدعم؟',
                ],
                'answer' => [
                    'en' => 'You can contact our support team through email, phone, WhatsApp, or the contact options available on the website.',
                    'ar' => 'تقدر تتواصل مع فريق الدعم من خلال الإيميل أو الهاتف أو واتساب أو وسائل التواصل المتاحة على الموقع.',
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

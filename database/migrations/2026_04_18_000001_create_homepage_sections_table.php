<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('type');                         // hero | features | pricing | about | testimonials | faq | contact | footer
            $table->string('label');                        // human-readable name in admin
            $table->json('content')->nullable();            // all text/button/image data
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Seed default sections ──────────────────────────────────────
        $now = now();

        DB::table('homepage_sections')->insert([
            [
                'type'       => 'hero',
                'label'      => 'القسم الرئيسي (Hero)',
                'content'    => json_encode([
                    'badge'        => 'الإصدار 2.0 متاح الآن',
                    'title'        => "تحكّم في شبكتك\nوحقّق أقصى ربح",
                    'subtitle'     => 'منصة SaaS متكاملة لمزودي خدمة الإنترنت — أنشئ بطاقات الإنترنت، راقب المستخدمين عبر MikroTik، وتابع أرباحك من لوحة تحكم واحدة.',
                    'cta_primary'  => ['text' => 'ابدأ مجاناً', 'url' => '#pricing'],
                    'cta_secondary'=> ['text' => 'عرض تجريبي', 'url' => '#how'],
                    'trust_items'  => ['بدون رسوم إعداد', '14 يوم تجريبي مجاني', 'دعم فني 24/7'],
                    'bg_gradient'  => 'from-blue-50 to-slate-50',
                ]),
                'sort_order' => 1,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type'       => 'features',
                'label'      => 'المميزات',
                'content'    => json_encode([
                    'title'    => 'كل أدوات الإدارة في مكان واحد',
                    'subtitle' => 'منصة متكاملة تمنحك التحكم الكامل في شبكتك وعملاءك وأرباحك دون تعقيد.',
                    'items'    => [
                        ['icon' => 'bolt',    'title' => 'سرعة فائقة',       'desc' => 'أنشئ وبِع بطاقات الإنترنت في ثوانٍ. واجهة مُحسَّنة تعمل حتى على الإنترنت البطيء.'],
                        ['icon' => 'shield',  'title' => 'تحكم كامل',        'desc' => 'تحكم في المستخدمين عبر MikroTik، فعّل أو عطّل الحسابات، حدد الباقات بضغطة زر.'],
                        ['icon' => 'chart',   'title' => 'تحليلات متقدمة',   'desc' => 'تقارير مفصلة عن المبيعات والأرباح والمستخدمين. اتخذ قراراتك بناءً على بيانات حقيقية.'],
                        ['icon' => 'users',   'title' => 'إدارة العملاء',    'desc' => 'تواصل مع عملاءك عبر الدردشة الفورية وأرسل إشعارات لحظية.'],
                        ['icon' => 'card',    'title' => 'شحن الرصيد',       'desc' => 'نظام متكامل لطلبات شحن الرصيد والموافقة عليها آلياً أو يدوياً.'],
                        ['icon' => 'cog',     'title' => 'إعدادات مرنة',     'desc' => 'خصّص عملتك ورابط MikroTik وكل إعدادات شبكتك من لوحة تحكم مركزية.'],
                    ],
                ]),
                'sort_order' => 2,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type'       => 'pricing',
                'label'      => 'خطط الاشتراك',
                'content'    => json_encode([
                    'title'    => 'خطط تناسب كل حجم',
                    'subtitle' => 'ابدأ مجاناً، وسعّد مع نمو شبكتك. لا رسوم خفية.',
                    'note'     => '✓ تجربة 14 يوم مجانية · ✓ لا رسوم خفية · ✓ إلغاء في أي وقت',
                ]),
                'sort_order' => 3,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type'       => 'about',
                'label'      => 'من نحن',
                'content'    => json_encode([
                    'title'    => 'نبني مستقبل إدارة الشبكات',
                    'subtitle' => 'فريق من المهندسين المتخصصين في بناء حلول SaaS للشركات العربية.',
                    'stats'    => [
                        ['value' => '+500', 'label' => 'شبكة مُدارة'],
                        ['value' => '+50K', 'label' => 'بطاقة شهرياً'],
                        ['value' => '99.9%','label' => 'وقت التشغيل'],
                        ['value' => '12+',  'label' => 'دولة عربية'],
                    ],
                ]),
                'sort_order' => 4,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type'       => 'testimonials',
                'label'      => 'آراء العملاء',
                'content'    => json_encode([
                    'title'    => 'يثق بنا أصحاب أكثر من +500 شبكة',
                    'subtitle' => 'شركاؤنا يديرون شبكاتهم بكفاءة أعلى ويحققون أرباحاً أكبر.',
                    'items'    => [
                        ['name' => 'محمد أبو عمر',  'role' => 'مزود خدمة إنترنت · نابلس',  'text' => 'قبل بطاقتي كنت أضيع ساعتين يومياً في إدارة البطاقات يدوياً. الآن كل شيء آلي وأرباحي تضاعفت.', 'stars' => 5],
                        ['name' => 'سامي الرشيدي',  'role' => 'صاحب مقهى إنترنت · عمّان',  'text' => 'ممتاز جداً! الواجهة سهلة وسريعة والدعم الفني يرد في دقائق. أنصح به كل صاحب مقهى.', 'stars' => 5],
                        ['name' => 'عصام الدين نور','role' => 'مزود إنترنت لاسلكي · القاهرة','text' => 'استخدمنا 3 أنظمة مختلفة قبل بطاقتي. هذا النظام الوحيد الذي يجمع كل شيء في مكان واحد.', 'stars' => 5],
                    ],
                ]),
                'sort_order' => 5,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type'       => 'faq',
                'label'      => 'الأسئلة الشائعة',
                'content'    => json_encode([
                    'title'    => 'كل ما تريد معرفته',
                    'subtitle' => '',
                    'items'    => [
                        ['q' => 'هل أحتاج خبرة تقنية لاستخدام المنصة؟',        'a' => 'لا على الإطلاق. الواجهة مصممة لتكون بسيطة وسهلة. ستتمكن من إنشاء أول بطاقة في أقل من 5 دقائق.'],
                        ['q' => 'هل تدعمون أجهزة MikroTik لجميع الموديلات؟',  'a' => 'نعم، نتكامل مع جميع أجهزة MikroTik عبر صفحة HotSpot القياسية.'],
                        ['q' => 'هل بياناتي ومعاملاتي آمنة؟',                 'a' => 'الأمان أولويتنا. نستخدم تشفيراً كاملاً، نسخ احتياطية يومية، وعزل تام بين بيانات كل شبكة.'],
                        ['q' => 'ماذا يحدث بعد انتهاء التجربة المجانية؟',      'a' => 'ستُطلب منك بيانات الدفع للاستمرار. بياناتك تبقى محفوظة 30 يوماً إضافياً للتصدير.'],
                    ],
                ]),
                'sort_order' => 6,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type'       => 'cta',
                'label'      => 'دعوة للتسجيل (CTA)',
                'content'    => json_encode([
                    'title'        => 'جاهز لتحويل شبكتك إلى مصدر دخل ذكي؟',
                    'subtitle'     => 'انضم إلى أكثر من 500 شبكة تستخدم بطاقتي. لا رسوم إعداد، لا بطاقة ائتمانية.',
                    'cta_primary'  => ['text' => 'ابدأ مجاناً الآن', 'url' => '#pricing'],
                    'cta_secondary'=> ['text' => 'تحدّث مع فريقنا', 'url' => 'https://wa.me/972501234567'],
                    'note'         => '✓ تجربة 14 يوم مجانية · ✓ لا رسوم خفية · ✓ إلغاء في أي وقت',
                    'bg'           => 'dark',
                ]),
                'sort_order' => 7,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};

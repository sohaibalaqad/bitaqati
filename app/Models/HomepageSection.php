<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomepageSection extends Model
{
    protected $fillable = [
        'type', 'label', 'content', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'content'   => 'array',
        'is_active' => 'boolean',
        'sort_order'=> 'integer',
    ];

    /** All active sections ordered for the public page. */
    public static function active(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('homepage_sections', 3600, function () {
            return static::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    /** Clear the homepage cache (call after any admin update). */
    public static function clearCache(): void
    {
        Cache::forget('homepage_sections');
    }

    /** Human-readable type labels. */
    public static function typeLabels(): array
    {
        return [
            'hero'         => 'القسم الرئيسي (Hero)',
            'features'     => 'المميزات',
            'pricing'      => 'خطط الاشتراك',
            'about'        => 'من نحن',
            'testimonials' => 'آراء العملاء',
            'faq'          => 'الأسئلة الشائعة',
            'cta'          => 'دعوة للتسجيل (CTA)',
        ];
    }
}

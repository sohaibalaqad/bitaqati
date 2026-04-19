<?php

/**
 * Platform Configuration
 *
 * Controls the current version of the system and which features are enabled.
 * Use the `feature()` helper anywhere in your code:
 *
 *   feature('chat')              → true/false
 *   @if(feature('chat')) ... @endif
 *   if (feature('advanced_reports')) { ... }
 *
 * To release v2: set PLATFORM_VERSION=2.0.0 in .env and enable v2 features.
 */

return [

    // =========================================================
    // System Identity
    // =========================================================

    'name'    => env('PLATFORM_NAME', 'بطاقتي'),
    'version' => env('PLATFORM_VERSION', '2.0.0'),

    // =========================================================
    // Feature Flags
    // Features can be toggled via .env without touching code.
    // =========================================================

    'features' => [

        // ── v1.0 Core Features (always on) ───────────────────
        'packages'          => env('FEATURE_PACKAGES',         true),   // باقات الإنترنت
        'card_purchase'     => env('FEATURE_CARD_PURCHASE',    true),   // شراء البطاقات
        'recharge_requests' => env('FEATURE_RECHARGE',         true),   // طلبات شحن الرصيد
        'notifications'     => env('FEATURE_NOTIFICATIONS',    true),   // إشعارات (DB + Reverb)
        'support_tickets'   => env('FEATURE_SUPPORT',          true),   // تذاكر الدعم الفني
        'chat'              => env('FEATURE_CHAT',             true),   // الدردشة المباشرة
        'dealer_portal'     => env('FEATURE_DEALER',           true),   // بوابة الديلر

        // ── v2.0 Features (disabled by default) ──────────────
        'advanced_reports'  => env('FEATURE_ADV_REPORTS',     false),  // تقارير متقدمة + رسوم بيانية
        'multi_currency'    => env('FEATURE_MULTI_CURRENCY',  false),  // عملات متعددة لكل مستخدم
        'sms_notifications' => env('FEATURE_SMS',             false),  // إشعارات SMS

        // ── v3.0 Features ────────────────────────────────────
        'multi_tenant'      => env('FEATURE_MULTI_TENANT',    true),   // ✅ مفعّل في v2
        'api_access'        => env('FEATURE_API',             false),  // API عام للعملاء
        'white_label'       => env('FEATURE_WHITE_LABEL',     false),  // تخصيص كامل للعلامة

    ],

];

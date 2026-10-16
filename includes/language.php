<?php
/**
 * فهرستینو - مدیریت زبان (Fehrestino language handling)
 *
 * زبان پیش‌فرض فارسی (RTL) است. رشته‌های سورس افزونه فارسی هستند و با توابع استاندارد
 * gettext (__ / esc_html_e) و دامنه‌ی متنی «fehrestino» پیچیده شده‌اند؛ ترجمه‌ی انگلیسی در
 * languages/fehrestino-en_US.mo قرار دارد. وقتی کاربر از پنل، زبان «English» را انتخاب کند،
 * این فایل ترجمه بارگذاری می‌شود و جهت پنل و فهرست هم LTR می‌شود.
 * مستقل از زبان کلی وردپرس (Site Language) کار می‌کند.
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * زبان‌های قابل انتخاب (نام هر زبان به خود همان زبان نوشته می‌شود و ترجمه نمی‌شود)
 */
function fehrestino_available_languages() {
    return [
        'fa' => 'فارسی',
        'en' => 'English',
    ];
}

/**
 * زبان انتخاب‌شده در تنظیمات افزونه (پیش‌فرض: fa)
 */
function fehrestino_get_language() {
    $data = get_option('ctoc_data', []);
    $lang = ( is_array($data) && isset($data['language']) ) ? $data['language'] : 'fa';

    return array_key_exists($lang, fehrestino_available_languages()) ? $lang : 'fa';
}

/**
 * فارسی = RTL ، انگلیسی = LTR
 */
function fehrestino_is_rtl() {
    return fehrestino_get_language() !== 'en';
}

/**
 * بارگذاری فایل ترجمه‌ی انگلیسی فقط وقتی زبان افزونه English است.
 * برای فارسی نیازی به فایل ترجمه نیست (رشته‌های سورس خودشان فارسی‌اند).
 */
add_action('init', 'fehrestino_load_language', 1);
function fehrestino_load_language() {
    if ( fehrestino_get_language() !== 'en' ) {
        return;
    }

    $mofile = FEHRESTINO_PATH . 'languages/fehrestino-en_US.mo';

    if ( is_readable($mofile) ) {
        unload_textdomain('fehrestino');
        load_textdomain('fehrestino', $mofile);
    }
}

/**
 * عنوان پیش‌فرض فهرست (به‌صورت ذخیره‌شده در دیتابیس) و معادل انگلیسی آن
 */
function fehrestino_default_title_fa() {
    return 'در این مقاله می‌خوانید:';
}

function fehrestino_default_title_variants() {
    return [ fehrestino_default_title_fa(), 'In this article:' ];
}

/**
 * اگر عنوان همان پیش‌فرض باشد، به زبان انتخاب‌شده نمایش داده می‌شود؛
 * عنوان سفارشی کاربر هیچ‌وقت تغییر نمی‌کند.
 */
function fehrestino_display_title($title) {
    $title = trim((string) $title);

    if ( $title === '' || in_array($title, fehrestino_default_title_variants(), true) ) {
        return __('در این مقاله می‌خوانید:', 'fehrestino');
    }

    return $title;
}

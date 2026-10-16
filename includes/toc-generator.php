<?php

if ( ! defined('ABSPATH') ) exit;

add_filter('the_content', 'fehrestino_render_toc');
add_shortcode('fehrestino', 'fehrestino_shortcode');

/**
 * جلوگیری از رندر تکراری فهرست در یک درخواست (باگ: the_content ممکن است چند بار
 * روی یک پست فراخوانی شود - مثلاً در مطالب مرتبط، ابزارک‌ها یا قالب‌های خاص)
 */
function fehrestino_already_rendered($post_id) {
    static $rendered = [];
    if ( in_array($post_id, $rendered, true) ) {
        return true;
    }
    $rendered[] = $post_id;
    return false;
}

/**
 * جایگزینی فقط اولین رخداد یک رشته (برای جلوگیری از مشکلات preg_replace با کاراکترهای خاص)
 */
function fehrestino_str_replace_once($search, $replace, $subject) {
    $pos = strpos($subject, $search);
    if ($pos === false) {
        return $subject;
    }
    return substr_replace($subject, $replace, $pos, strlen($search));
}

/**
 * استخراج هدینگ‌های مجاز از محتوا و ساخت آرایه‌ای از تطبیق‌ها به ترتیب ظاهر شدن در متن
 * رجکس بهبودیافته نسبت به نسخه قبلی: مقادیر داخل اتریبیوت‌ها (مثل alt=">") دیگر باعث شکست نمی‌شود
 */
function fehrestino_extract_headings($content, $selected_hs) {
    $pattern = '/<h([2-6])((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>(.*?)<\/h\1\s*>/is';

    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

    if ( empty($matches) ) {
        return [];
    }

    $filtered = [];
    foreach ($matches as $m) {
        if ( in_array($m[1], $selected_hs, true) ) {
            $filtered[] = $m;
        }
    }

    return $filtered;
}

/**
 * شماره‌گذاری سلسله‌مراتبی (1, 1.1, 1.1.1 ...) بر اساس سطوح انتخاب‌شده
 */
function fehrestino_build_numbering(&$counters, $level_index) {
    // صفر کردن شمارنده‌های سطوح عمیق‌تر
    foreach ($counters as $depth => $val) {
        if ($depth > $level_index) {
            $counters[$depth] = 0;
        }
    }
    $counters[$level_index] = ($counters[$level_index] ?? 0) + 1;

    $parts = [];
    for ($i = 0; $i <= $level_index; $i++) {
        $parts[] = $counters[$i] ?? 1;
    }
    return implode('.', $parts);
}

/**
 * ساخت HTML نهایی فهرست + جایگذاری id روی هدینگ‌های اصلی محتوا
 */
function fehrestino_build_toc($content, $data) {
    $selected_hs = ! empty($data['headings']) ? $data['headings'] : ['2', '3'];
    $min_headings = max(1, intval($data['min_headings'] ?? 2));

    $matches = fehrestino_extract_headings($content, $selected_hs);

    if ( count($matches) < $min_headings ) {
        return ['content' => $content, 'toc' => ''];
    }

    sort($selected_hs);
    $level_map = array_flip($selected_hs); // سطح => عمق (برای شماره‌گذاری تو در تو)

    $theme        = sanitize_html_class($data['theme'] ?? 'neo');
    $accent       = preg_match('/^#[a-fA-F0-9]{6}$/', $data['accent_color'] ?? '') ? $data['accent_color'] : '#1a1a1b';
    $default_state= ($data['default_state'] ?? 'open') === 'closed' ? ' is-closed' : '';
    $numbering    = ! empty($data['numbering']);
    $title_text   = fehrestino_display_title($data['title'] ?? '');
    $dir_class    = fehrestino_is_rtl() ? '' : ' is-ltr';
    $dir_attr     = fehrestino_is_rtl() ? '' : ' dir="ltr"';

    $list_items = '';
    $counters   = [];

    foreach ($matches as $i => $m) {
        $level = $m[1];
        $id    = 'fehrestino-ref-' . ($i + 1);
        $raw_text = wp_strip_all_tags($m[3]);
        $text  = html_entity_decode($raw_text, ENT_QUOTES, 'UTF-8');
        $text  = esc_html(trim($text));

        if ($text === '') {
            continue;
        }

        $new_h   = "<h{$level}{$m[2]} id=\"{$id}\">{$m[3]}</h{$level}>";
        $content = fehrestino_str_replace_once($m[0], $new_h, $content);

        $number_html = '';
        if ($numbering) {
            $depth = $level_map[$level] ?? 0;
            $num   = fehrestino_build_numbering($counters, $depth);
            $number_html = '<span class="toc-number">' . esc_html($num) . '</span> ';
        }

        $list_items .= "<li class=\"toc-level-{$level}\"><a href=\"#{$id}\">{$number_html}<span class=\"toc-link-text\">{$text}</span></a></li>";
    }

    if ($list_items === '') {
        return ['content' => $content, 'toc' => ''];
    }

    $toc_html  = '<div class="custom-toc-wrapper theme-' . esc_attr($theme) . esc_attr($default_state) . $dir_class . '"' . $dir_attr . ' id="customToc" style="--fehrestino-accent: ' . esc_attr($accent) . ';">';
    $toc_html .= '<div class="custom-toc-header" onclick="fehrestinoToggle(this)" role="button" tabindex="0" aria-expanded="' . ($default_state ? 'false' : 'true') . '">';
    $toc_html .=   '<span class="toc-title-text">' . esc_html($title_text) . '</span>';
    $toc_html .=   '<span class="toc-icon">▼</span>';
    $toc_html .= '</div>';
    $toc_html .= '<div class="custom-toc-content">';
    $toc_html .=   '<ul class="custom-toc-links">' . $list_items . '</ul>';
    $toc_html .= '</div></div>';

    return ['content' => $content, 'toc' => $toc_html];
}

/**
 * نگهداری موقت HTML فهرست برای هماهنگی بین درج خودکار و شورت‌کد دستی در یک درخواست
 */
function fehrestino_toc_cache($post_id, $value = null) {
    static $cache = [];
    if ($value !== null) {
        $cache[$post_id] = $value;
        return $value;
    }
    return $cache[$post_id] ?? '';
}

/**
 * درج خودکار در the_content
 */
function fehrestino_render_toc($content) {

    if ( ! fehrestino_should_run() ) {
        return $content;
    }

    if ( ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    $post_id = get_the_ID();
    if ( ! $post_id || fehrestino_already_rendered($post_id) ) {
        return $content;
    }

    $data = wp_parse_args(get_option('ctoc_data', []), fehrestino_default_options());
    $has_shortcode = has_shortcode($content, 'fehrestino');

    // اگر نه درج خودکار فعال است و نه شورت‌کد در متن استفاده شده، کاری لازم نیست
    if ( empty($data['auto_insert']) && ! $has_shortcode ) {
        return $content;
    }

    $result = fehrestino_build_toc($content, $data);

    if ( empty($result['toc']) ) {
        return $result['content'];
    }

    $content  = $result['content'];
    $toc_html = $result['toc'];

    // اگر شورت‌کد دستی در متن وجود دارد، فهرست را برای استفاده بعدی توسط do_shortcode ذخیره می‌کنیم
    // و آن را به‌صورت خودکار بالای محتوا تکرار نمی‌کنیم
    if ( $has_shortcode ) {
        fehrestino_toc_cache($post_id, $toc_html);
        return $content;
    }

    if ( empty($data['auto_insert']) ) {
        return $content;
    }

    $p = strpos($content, '</p>');
    $h = strpos($content, '<h');

    if ($h !== false && ($p === false || $h < $p)) {
        return $toc_html . $content;
    } elseif ($p !== false) {
        return substr_replace($content, $toc_html, $p + 4, 0);
    }

    return $toc_html . $content;
}

/**
 * شورت‌کد [fehrestino] برای جایگذاری دستی فهرست در هر نقطه از محتوا
 * فهرست از کش مشترک خوانده می‌شود تا با id هدینگ‌های واقعاً درج‌شده در محتوا هماهنگ باشد
 */
function fehrestino_shortcode($atts, $content_param = null, $tag = '') {
    global $post;

    if ( ! $post ) {
        return '';
    }

    $cached = fehrestino_toc_cache($post->ID);

    if ($cached !== '') {
        return $cached;
    }

    // پشتیبان: اگر کش خالی بود (مثلاً شورت‌کد خارج از حلقه اصلی استفاده شده)، مستقیم می‌سازیم
    $data = wp_parse_args(get_option('ctoc_data', []), fehrestino_default_options());
    $result = fehrestino_build_toc($post->post_content, $data);

    return $result['toc'];
}

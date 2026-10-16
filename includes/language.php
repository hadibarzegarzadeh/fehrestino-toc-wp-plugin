<?php

if ( ! defined('ABSPATH') ) exit;

function fehrestino_available_languages() {
    return [
        'fa' => 'فارسی',
        'en' => 'English',
    ];
}

function fehrestino_get_language() {
    $data = get_option('ctoc_data', []);
    $lang = ( is_array($data) && isset($data['language']) ) ? $data['language'] : 'fa';

    return array_key_exists($lang, fehrestino_available_languages()) ? $lang : 'fa';
}

function fehrestino_is_rtl() {
    return fehrestino_get_language() !== 'en';
}

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

function fehrestino_default_title_fa() {
    return 'در این مقاله می‌خوانید:';
}

function fehrestino_default_title_variants() {
    return [ fehrestino_default_title_fa(), 'In this article:' ];
}

function fehrestino_display_title($title) {
    $title = trim((string) $title);

    if ( $title === '' || in_array($title, fehrestino_default_title_variants(), true) ) {
        return __('در این مقاله می‌خوانید:', 'fehrestino');
    }

    return $title;
}

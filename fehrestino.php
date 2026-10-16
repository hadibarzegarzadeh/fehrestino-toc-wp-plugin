<?php
/*
Plugin Name: فهرستینو
Description: فهرست مطالب حرفه‌ای با پنل مدیریت پیشرفته، چند قالب آماده، انتخاب H2-H6، رعایت سلسله‌مراتب، اسکرول نرم و انیمیشن بسیار نرم.
Version: 2.2
Author: تارنُما
Author URI: https://tarnoma.ir
Text Domain: fehrestino
*/

if ( ! defined('ABSPATH') ) exit;

define('FEHRESTINO_VERSION', '2.2');
define('FEHRESTINO_PATH', plugin_dir_path(__FILE__));
define('FEHRESTINO_URL', plugin_dir_url(__FILE__));

require_once FEHRESTINO_PATH . 'includes/language.php';
require_once FEHRESTINO_PATH . 'includes/settings-page.php';
require_once FEHRESTINO_PATH . 'includes/toc-generator.php';

function fehrestino_default_options() {
    return [
        'title'          => 'در این مقاله می‌خوانید:',
        'headings'       => ['2', '3'],
        'theme'          => 'neo',
        'accent_color'   => '#1a1a1b',
        'numbering'      => 0,
        'default_state'  => 'open',
        'smooth_scroll'  => 1,
        'scrollspy'      => 1,
        'enable_pages'   => 0,
        'auto_insert'    => 1,
        'min_headings'   => 2,
        'language'       => 'fa',
    ];
}

register_activation_hook(__FILE__, function () {
    if ( ! get_option('ctoc_data') ) {
        add_option('ctoc_data', fehrestino_default_options());
    }
});

add_action('wp_enqueue_scripts', function () {

    if ( ! fehrestino_should_run() ) {
        return;
    }

    $data = wp_parse_args(get_option('ctoc_data', []), fehrestino_default_options());

    wp_enqueue_style(
        'fehrestino-style',
        FEHRESTINO_URL . 'assets/css/toc-style.css',
        [],
        FEHRESTINO_VERSION
    );

    wp_enqueue_script(
        'fehrestino-script',
        FEHRESTINO_URL . 'assets/js/toc-script.js',
        [],
        FEHRESTINO_VERSION,
        true
    );

    wp_localize_script('fehrestino-script', 'fehrestinoSettings', [
        'smoothScroll' => ! empty($data['smooth_scroll']),
        'scrollspy'    => ! empty($data['scrollspy']),
    ]);
});

function fehrestino_should_run() {
    $data = wp_parse_args(get_option('ctoc_data', []), fehrestino_default_options());

    if ( is_single() ) {
        return true;
    }

    if ( is_page() && ! empty($data['enable_pages']) ) {
        return true;
    }

    return false;
}

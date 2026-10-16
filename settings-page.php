<?php

if ( ! defined('ABSPATH') ) exit;

add_action('admin_menu', 'fehrestino_admin_menu');
add_action('admin_init', 'fehrestino_settings_init');
add_action('admin_enqueue_scripts', 'fehrestino_admin_assets');

function fehrestino_admin_menu() {
    add_menu_page(
        __('فهرستینو', 'fehrestino'),
        __('فهرستینو', 'fehrestino'),
        'manage_options',
        'ctoc_settings_slug',
        'fehrestino_options_page',
        'data:image/svg+xml;base64,' . base64_encode(fehrestino_menu_icon_svg()),
        65
    );
}

function fehrestino_menu_icon_svg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#a7aaad"><path d="M4 5h16v2H4zM4 11h10v2H4zM4 17h16v2H4z"/></svg>';
}

function fehrestino_settings_init() {
    register_setting('ctoc_settings_group', 'ctoc_data', [
        'sanitize_callback' => 'fehrestino_sanitize',
        'default'           => fehrestino_default_options(),
    ]);
}

function fehrestino_admin_assets($hook) {
    if ( $hook !== 'toplevel_page_ctoc_settings_slug' ) {
        return;
    }

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    wp_enqueue_style(
        'fehrestino-admin-style',
        FEHRESTINO_URL . 'assets/css/admin-style.css',
        [],
        FEHRESTINO_VERSION
    );

    // برای نمایش پیش‌نمایش زنده در پنل از همان استایل فرانت‌اند استفاده می‌کنیم
    wp_enqueue_style(
        'fehrestino-style',
        FEHRESTINO_URL . 'assets/css/toc-style.css',
        [],
        FEHRESTINO_VERSION
    );

    wp_enqueue_script(
        'fehrestino-admin-script',
        FEHRESTINO_URL . 'assets/js/admin-script.js',
        ['jquery', 'wp-color-picker'],
        FEHRESTINO_VERSION,
        true
    );

    wp_localize_script('fehrestino-admin-script', 'fehrestinoAdmin', [
        'isRtl' => fehrestino_is_rtl(),
        'i18n'  => [
            'minOneHeading' => __('حداقل یک سطح هدینگ باید فعال باشد.', 'fehrestino'),
            'copied'        => __('کپی شد ✓', 'fehrestino'),
            'defaultTitle'  => __('در این مقاله می‌خوانید:', 'fehrestino'),
            'samples'       => [
                __('مقدمه و آشنایی با موضوع', 'fehrestino'),
                __('چرا این موضوع اهمیت دارد؟', 'fehrestino'),
                __('پیش‌نیازهای لازم', 'fehrestino'),
                __('بخش اصلی آموزش', 'fehrestino'),
                __('گام اول', 'fehrestino'),
                __('جمع‌بندی و نتیجه‌گیری', 'fehrestino'),
            ],
        ],
    ]);
}

/**
 * پاکسازی و اعتبارسنجی کامل تنظیمات قبل از ذخیره
 */
function fehrestino_sanitize($input) {
    $defaults = fehrestino_default_options();
    $output   = [];

    // اگر کاربر عنوان پیش‌فرض را (به هر یک از دو زبان) دست‌نخورده نگه داشته، مقدار اصلی فارسی ذخیره می‌شود
    // تا با تغییر زبان، عنوان پیش‌فرض هم هم‌زبان با پنل بماند.
    $title_in = isset($input['title']) ? trim($input['title']) : '';
    $output['title'] = ( $title_in !== '' && ! in_array($title_in, fehrestino_default_title_variants(), true) )
        ? sanitize_text_field($title_in)
        : $defaults['title'];

    $output['language'] = ( isset($input['language']) && array_key_exists($input['language'], fehrestino_available_languages()) )
        ? $input['language']
        : $defaults['language'];

    $allowed_levels = ['2', '3', '4', '5', '6'];
    $headings = [];
    if ( isset($input['headings']) && is_array($input['headings']) ) {
        foreach ($input['headings'] as $h) {
            $h = (string) $h;
            if ( in_array($h, $allowed_levels, true) && ! in_array($h, $headings, true) ) {
                $headings[] = $h;
            }
        }
    }
    sort($headings);
    $output['headings'] = ! empty($headings) ? $headings : $defaults['headings'];

    $allowed_themes = ['neo', 'minimal', 'gradient', 'card'];
    $output['theme'] = (isset($input['theme']) && in_array($input['theme'], $allowed_themes, true))
        ? $input['theme']
        : $defaults['theme'];

    $color = isset($input['accent_color']) ? sanitize_hex_color($input['accent_color']) : '';
    $output['accent_color'] = $color ? $color : $defaults['accent_color'];

    $output['numbering']     = ! empty($input['numbering']) ? 1 : 0;
    $output['smooth_scroll'] = ! empty($input['smooth_scroll']) ? 1 : 0;
    $output['scrollspy']     = ! empty($input['scrollspy']) ? 1 : 0;
    $output['enable_pages']  = ! empty($input['enable_pages']) ? 1 : 0;
    $output['auto_insert']   = ! empty($input['auto_insert']) ? 1 : 0;

    $output['default_state'] = (isset($input['default_state']) && $input['default_state'] === 'closed') ? 'closed' : 'open';

    $min = isset($input['min_headings']) ? intval($input['min_headings']) : $defaults['min_headings'];
    $output['min_headings'] = max(1, min(10, $min));

    return $output;
}

function fehrestino_options_page() {
    $data = wp_parse_args(get_option('ctoc_data', []), fehrestino_default_options());

    $title         = $data['title'];
    $selected_hs   = $data['headings'];
    $theme         = $data['theme'];
    $accent        = $data['accent_color'];
    $numbering     = $data['numbering'];
    $default_state = $data['default_state'];
    $smooth_scroll = $data['smooth_scroll'];
    $scrollspy     = $data['scrollspy'];
    $enable_pages  = $data['enable_pages'];
    $auto_insert   = $data['auto_insert'];
    $min_headings  = $data['min_headings'];
    $lang          = fehrestino_get_language();
    $is_rtl        = fehrestino_is_rtl();
    // عنوان پیش‌فرض در زبان انتخاب‌شده نمایش داده می‌شود
    $title         = fehrestino_display_title($title);

    $heading_labels = [
        '2' => __('H2 — عنوان اصلی', 'fehrestino'),
        '3' => __('H3 — زیرعنوان', 'fehrestino'),
        '4' => __('H4 — زیرعنوان جزئی‌تر', 'fehrestino'),
        '5' => 'H5',
        '6' => 'H6',
    ];

    $themes = [
        'neo'      => ['label' => __('نئو (پیش‌فرض)', 'fehrestino'), 'desc' => __('طراحی برجسته و پررنگ با حاشیه ضخیم', 'fehrestino')],
        'minimal'  => ['label' => __('مینیمال', 'fehrestino'),        'desc' => __('ساده، تمیز و سبک با خط رنگی کنار', 'fehrestino')],
        'gradient' => ['label' => __('گرادیانت', 'fehrestino'),       'desc' => __('هدر رنگی مدرن با گرادیان جذاب', 'fehrestino')],
        'card'     => ['label' => __('کارتی', 'fehrestino'),          'desc' => __('سایه نرم به سبک کارت‌های مدرن', 'fehrestino')],
    ];
    ?>
    <div class="wrap fehrestino-wrap<?php echo $is_rtl ? '' : ' is-ltr'; ?>" dir="<?php echo $is_rtl ? 'rtl' : 'ltr'; ?>">

        <div class="fehrestino-header">
            <div class="fehrestino-header-right">
                <span class="fehrestino-logo">📑</span>
                <div>
                    <h1><?php esc_html_e('فهرستینو', 'fehrestino'); ?></h1>
                    <p><?php esc_html_e('پنل مدیریت فهرست مطالب هوشمند', 'fehrestino'); ?></p>
                </div>
            </div>
            <div class="fehrestino-header-left">
                <select name="ctoc_data[language]" form="fehrestino-form" class="fehrestino-lang-select" aria-label="<?php echo esc_attr__('زبان پنل و فهرست', 'fehrestino'); ?>">
                    <?php foreach (fehrestino_available_languages() as $code => $lang_label): ?>
                        <option value="<?php echo esc_attr($code); ?>" <?php selected($lang, $code); ?>><?php echo esc_html($lang_label); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="fehrestino-version"><?php echo esc_html(sprintf(__('نسخه %s', 'fehrestino'), FEHRESTINO_VERSION)); ?></span>
            </div>
        </div>

        <form method="post" action="options.php" id="fehrestino-form">
            <?php settings_fields('ctoc_settings_group'); ?>

            <div class="fehrestino-layout">

                <div class="fehrestino-main">

                    <div class="fehrestino-body">

                    <nav class="fehrestino-tabs">
                      <div class="fehrestino-tabs-inner">
                        <button type="button" class="fehrestino-tab active" data-tab="general">
                            <span class="fehrestino-tab-ico">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.2a1.7 1.7 0 0 1 3.4 0l.16.9a1.7 1.7 0 0 0 2.42 1.2l.8-.42a1.7 1.7 0 0 1 2.1 2.55l-.6.7a1.7 1.7 0 0 0 0 2.14l.6.7a1.7 1.7 0 0 1-2.1 2.55l-.8-.42a1.7 1.7 0 0 0-2.42 1.2l-.16.9a1.7 1.7 0 0 1-3.4 0l-.16-.9a1.7 1.7 0 0 0-2.42-1.2l-.8.42a1.7 1.7 0 0 1-2.1-2.55l.6-.7a1.7 1.7 0 0 0 0-2.14l-.6-.7a1.7 1.7 0 0 1 2.1-2.55l.8.42a1.7 1.7 0 0 0 2.42-1.2z"/><circle cx="12" cy="12" r="2.6"/></svg>
                            </span>
                            <span class="fehrestino-tab-txt"><?php esc_html_e('تنظیمات عمومی', 'fehrestino'); ?></span>
                        </button>
                        <button type="button" class="fehrestino-tab" data-tab="appearance">
                            <span class="fehrestino-tab-ico">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="M2 12l10 5 10-5"/><path d="M2 17l10 5 10-5"/></svg>
                            </span>
                            <span class="fehrestino-tab-txt"><?php esc_html_e('ظاهر و قالب', 'fehrestino'); ?></span>
                        </button>
                        <button type="button" class="fehrestino-tab" data-tab="advanced">
                            <span class="fehrestino-tab-ico">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z"/></svg>
                            </span>
                            <span class="fehrestino-tab-txt"><?php esc_html_e('پیشرفته', 'fehrestino'); ?></span>
                        </button>
                      </div>
                    </nav>

                    <div class="fehrestino-panels">

                    <!-- تب تنظیمات عمومی -->
                    <section class="fehrestino-panel" data-panel="general">

                        <div class="fehrestino-card">
                            <h2><?php esc_html_e('متن عنوان فهرست', 'fehrestino'); ?></h2>
                            <p class="fehrestino-hint"><?php esc_html_e('این متن بالای جعبه‌ی فهرست مطالب نمایش داده می‌شود.', 'fehrestino'); ?></p>
                            <input type="text" name="ctoc_data[title]" value="<?php echo esc_attr($title); ?>" class="fehrestino-input">
                        </div>

                        <div class="fehrestino-card">
                            <h2><?php esc_html_e('هدینگ‌های نمایش داده‌شده', 'fehrestino'); ?></h2>
                            <p class="fehrestino-hint"><?php esc_html_e('حداقل یک سطح باید فعال بماند؛ سلسله‌مراتب به‌صورت خودکار رعایت می‌شود.', 'fehrestino'); ?></p>
                            <div class="fehrestino-pills" id="fehrestino-heading-pills">
                                <?php foreach ($heading_labels as $level => $label): ?>
                                    <label class="fehrestino-pill">
                                        <input type="checkbox" name="ctoc_data[headings][]" value="<?php echo esc_attr($level); ?>" <?php checked(in_array($level, $selected_hs, true)); ?>>
                                        <span><?php echo esc_html($label); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="fehrestino-card">
                            <h2><?php esc_html_e('محل نمایش', 'fehrestino'); ?></h2>
                            <div class="fehrestino-toggle-row">
                                <div>
                                    <strong><?php esc_html_e('درج خودکار در محتوا', 'fehrestino'); ?></strong>
                                    <p class="fehrestino-hint"><?php esc_html_e('فهرست به‌طور خودکار بالای مطلب درج می‌شود.', 'fehrestino'); ?></p>
                                </div>
                                <label class="fehrestino-switch">
                                    <input type="hidden" name="ctoc_data[auto_insert]" value="0">
                                    <input type="checkbox" name="ctoc_data[auto_insert]" value="1" <?php checked($auto_insert, 1); ?>>
                                    <span class="fehrestino-slider"></span>
                                </label>
                            </div>

                            <div class="fehrestino-toggle-row">
                                <div>
                                    <strong><?php esc_html_e('فعال‌سازی روی برگه‌ها (Pages)', 'fehrestino'); ?></strong>
                                    <p class="fehrestino-hint"><?php esc_html_e('به‌طور پیش‌فرض فقط روی نوشته‌ها (Posts) فعال است.', 'fehrestino'); ?></p>
                                </div>
                                <label class="fehrestino-switch">
                                    <input type="hidden" name="ctoc_data[enable_pages]" value="0">
                                    <input type="checkbox" name="ctoc_data[enable_pages]" value="1" <?php checked($enable_pages, 1); ?>>
                                    <span class="fehrestino-slider"></span>
                                </label>
                            </div>

                            <div class="fehrestino-toggle-row">
                                <div>
                                    <strong><?php esc_html_e('حداقل تعداد هدینگ برای نمایش فهرست', 'fehrestino'); ?></strong>
                                    <p class="fehrestino-hint"><?php esc_html_e('اگر تعداد هدینگ‌های مطلب کمتر از این عدد باشد، فهرست نمایش داده نمی‌شود.', 'fehrestino'); ?></p>
                                </div>
                                <input type="number" min="1" max="10" name="ctoc_data[min_headings]" value="<?php echo esc_attr($min_headings); ?>" class="fehrestino-input fehrestino-input-number">
                            </div>
                        </div>

                        <div class="fehrestino-card fehrestino-shortcode-card">
                            <h2><?php esc_html_e('درج دستی با شورت‌کد', 'fehrestino'); ?></h2>
                            <p class="fehrestino-hint"><?php esc_html_e('برای جایگذاری فهرست در نقطه‌ی دلخواهی از متن (به‌جای درج خودکار)، این شورت‌کد را در ویرایشگر وارد کنید:', 'fehrestino'); ?></p>
                            <code class="fehrestino-shortcode-box" id="fehrestino-shortcode-copy">[fehrestino]</code>
                        </div>

                    </section>

                    <!-- تب ظاهر و قالب -->
                    <section class="fehrestino-panel" data-panel="appearance" style="display:none;">

                        <div class="fehrestino-card">
                            <h2><?php esc_html_e('انتخاب قالب فهرست', 'fehrestino'); ?></h2>
                            <p class="fehrestino-hint"><?php esc_html_e('یکی از قالب‌های آماده را انتخاب کنید — پیش‌نمایش به‌صورت زنده در سمت راست به‌روزرسانی می‌شود.', 'fehrestino'); ?></p>

                            <div class="fehrestino-theme-grid">
                                <?php foreach ($themes as $key => $t): ?>
                                    <label class="fehrestino-theme-card fehrestino-theme-<?php echo esc_attr($key); ?> <?php echo $theme === $key ? 'is-selected' : ''; ?>">
                                        <input type="radio" name="ctoc_data[theme]" value="<?php echo esc_attr($key); ?>" <?php checked($theme, $key); ?>>
                                        <span class="fehrestino-theme-thumb">
                                            <span class="thumb-header"></span>
                                            <span class="thumb-line w-80"></span>
                                            <span class="thumb-line w-60"></span>
                                            <span class="thumb-line w-70"></span>
                                        </span>
                                        <span class="fehrestino-theme-name"><?php echo esc_html($t['label']); ?></span>
                                        <span class="fehrestino-theme-desc"><?php echo esc_html($t['desc']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="fehrestino-card">
                            <h2><?php esc_html_e('رنگ اصلی (Accent)', 'fehrestino'); ?></h2>
                            <p class="fehrestino-hint"><?php esc_html_e('این رنگ در حاشیه‌ها، آیکون‌ها و عناصر برجسته‌ی فهرست استفاده می‌شود.', 'fehrestino'); ?></p>
                            <input type="text" name="ctoc_data[accent_color]" value="<?php echo esc_attr($accent); ?>" class="fehrestino-color-picker" data-default-color="<?php echo esc_attr(fehrestino_default_options()['accent_color']); ?>">
                        </div>

                        <div class="fehrestino-card">
                            <div class="fehrestino-toggle-row">
                                <div>
                                    <strong><?php esc_html_e('شماره‌گذاری خودکار', 'fehrestino'); ?></strong>
                                    <p class="fehrestino-hint"><?php esc_html_e('نمایش شماره کنار هر آیتم فهرست (مثل ۱، ۱.۱)', 'fehrestino'); ?></p>
                                </div>
                                <label class="fehrestino-switch">
                                    <input type="hidden" name="ctoc_data[numbering]" value="0">
                                    <input type="checkbox" name="ctoc_data[numbering]" value="1" <?php checked($numbering, 1); ?>>
                                    <span class="fehrestino-slider"></span>
                                </label>
                            </div>

                            <div class="fehrestino-toggle-row">
                                <div>
                                    <strong><?php esc_html_e('وضعیت پیش‌فرض جعبه', 'fehrestino'); ?></strong>
                                    <p class="fehrestino-hint"><?php esc_html_e('فهرست هنگام بارگذاری صفحه باز یا بسته باشد.', 'fehrestino'); ?></p>
                                </div>
                                <div class="fehrestino-radio-group">
                                    <label><input type="radio" name="ctoc_data[default_state]" value="open" <?php checked($default_state, 'open'); ?>> <?php esc_html_e('باز', 'fehrestino'); ?></label>
                                    <label><input type="radio" name="ctoc_data[default_state]" value="closed" <?php checked($default_state, 'closed'); ?>> <?php esc_html_e('بسته', 'fehrestino'); ?></label>
                                </div>
                            </div>
                        </div>

                    </section>

                    <!-- تب پیشرفته -->
                    <section class="fehrestino-panel" data-panel="advanced" style="display:none;">
                        <div class="fehrestino-card">
                            <div class="fehrestino-toggle-row">
                                <div>
                                    <strong><?php esc_html_e('اسکرول نرم', 'fehrestino'); ?></strong>
                                    <p class="fehrestino-hint"><?php esc_html_e('با کلیک روی لینک‌ها، صفحه به‌جای پرش ناگهانی، به‌نرمی اسکرول می‌شود.', 'fehrestino'); ?></p>
                                </div>
                                <label class="fehrestino-switch">
                                    <input type="hidden" name="ctoc_data[smooth_scroll]" value="0">
                                    <input type="checkbox" name="ctoc_data[smooth_scroll]" value="1" <?php checked($smooth_scroll, 1); ?>>
                                    <span class="fehrestino-slider"></span>
                                </label>
                            </div>

                            <div class="fehrestino-toggle-row">
                                <div>
                                    <strong><?php esc_html_e('هایلایت لینک فعال (Scrollspy)', 'fehrestino'); ?></strong>
                                    <p class="fehrestino-hint"><?php esc_html_e('هنگام اسکرول کاربر در مطلب، لینک بخش مربوطه در فهرست برجسته می‌شود.', 'fehrestino'); ?></p>
                                </div>
                                <label class="fehrestino-switch">
                                    <input type="hidden" name="ctoc_data[scrollspy]" value="0">
                                    <input type="checkbox" name="ctoc_data[scrollspy]" value="1" <?php checked($scrollspy, 1); ?>>
                                    <span class="fehrestino-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="fehrestino-card fehrestino-info-card">
                            <h2><?php esc_html_e('درباره عملکرد', 'fehrestino'); ?></h2>
                            <ul class="fehrestino-info-list">
                                <li><?php esc_html_e('✅ فهرست فقط در نوشته‌ها (و در صورت فعال‌سازی، برگه‌ها) بارگذاری می‌شود.', 'fehrestino'); ?></li>
                                <li><?php esc_html_e('✅ از رندر تکراری فهرست در یک صفحه جلوگیری شده است.', 'fehrestino'); ?></li>
                                <li><?php esc_html_e('✅ استخراج هدینگ‌ها در برابر اتریبیوت‌های پیچیده مقاوم شده است.', 'fehrestino'); ?></li>
                            </ul>
                        </div>
                    </section>

                    </div><!-- /.fehrestino-panels -->
                    </div><!-- /.fehrestino-body -->

                    <?php submit_button(__('ذخیره تنظیمات', 'fehrestino'), 'primary', 'submit', false, ['class' => 'button button-primary fehrestino-save-btn']); ?>

                </div>

                <!-- پیش‌نمایش زنده -->
                <aside class="fehrestino-preview-col">
                    <div class="fehrestino-preview-sticky">
                        <h3><?php esc_html_e('پیش‌نمایش زنده', 'fehrestino'); ?></h3>
                        <div class="fehrestino-preview-frame" id="fehrestino-live-preview">
                            <!-- با جاوااسکریپت ساخته می‌شود -->
                        </div>
                    </div>
                </aside>

            </div>
        </form>
    </div>
    <?php
}

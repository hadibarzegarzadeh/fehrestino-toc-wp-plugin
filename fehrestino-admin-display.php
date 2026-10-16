<?php
$options = get_option('fehrestino_options', array(
    'template' => 'minimal',
    'auto_insert' => true,
    'heading_text' => 'فهرست مطالب'
));
?>
<div class="wrap fehrestino-wrap">
    <div class="fehrestino-glass-container">
        <header class="fehrestino-header">
            <div class="fehrestino-logo">
                <span class="dashicons dashicons-list-view"></span>
                <h1>تنظیمات فهرستینو</h1>
            </div>
            <p>افزونه مدرن فهرست محتوا (Table of Contents)</p>
        </header>

        <form method="post" action="options.php" class="fehrestino-form">
            <?php settings_fields('fehrestino_options_group'); ?>

            <div class="fehrestino-tabs">
                <ul class="fehrestino-tab-links">
                    <li class="active"><a href="#tab-general">تنظیمات عمومی</a></li>
                    <li><a href="#tab-appearance">قالب و ظاهر</a></li>
                </ul>

                <div class="fehrestino-tab-content">
                    <div id="tab-general" class="fehrestino-tab active">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="fehrestino_auto_insert">درج خودکار در نوشته‌ها</label></th>
                                <td>
                                    <label class="fehrestino-switch">
                                        <input type="checkbox" id="fehrestino_auto_insert" name="fehrestino_options[auto_insert]" value="1" <?php checked(1, isset($options['auto_insert']) ? $options['auto_insert'] : 1); ?> />
                                        <span class="fehrestino-slider round"></span>
                                    </label>
                                    <p class="description">اگر غیرفعال باشد، باید از شورت‌کد <code>[fehrestino]</code> استفاده کنید.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="fehrestino_heading_text">عنوان فهرست</label></th>
                                <td>
                                    <input type="text" id="fehrestino_heading_text" name="fehrestino_options[heading_text]" value="<?php echo esc_attr($options['heading_text']); ?>" class="regular-text" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="tab-appearance" class="fehrestino-tab">
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label>انتخاب قالب</label></th>
                                <td>
                                    <div class="fehrestino-template-selector">
                                        <label class="fehrestino-template-card <?php echo ($options['template'] == 'minimal') ? 'selected' : ''; ?>">
                                            <input type="radio" name="fehrestino_options[template]" value="minimal" <?php checked('minimal', $options['template']); ?>>
                                            <div class="card-content">مینیمال</div>
                                        </label>
                                        <label class="fehrestino-template-card <?php echo ($options['template'] == 'glass') ? 'selected' : ''; ?>">
                                            <input type="radio" name="fehrestino_options[template]" value="glass" <?php checked('glass', $options['template']); ?>>
                                            <div class="card-content">گلس‌مورفیسم</div>
                                        </label>
                                        <label class="fehrestino-template-card <?php echo ($options['template'] == 'modern') ? 'selected' : ''; ?>">
                                            <input type="radio" name="fehrestino_options[template]" value="modern" <?php checked('modern', $options['template']); ?>>
                                            <div class="card-content">مدرن متریال</div>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="fehrestino-submit">
                <?php submit_button('ذخیره تغییرات', 'primary fehrestino-btn'); ?>
            </div>
        </form>
    </div>
</div>

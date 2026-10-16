jQuery(function ($) {

    var cfg = (typeof fehrestinoAdmin !== 'undefined') ? fehrestinoAdmin : { isRtl: true, i18n: {} };
    var t = cfg.i18n || {};
    var DEFAULT_TITLE = t.defaultTitle || 'در این مقاله می‌خوانید:';

    $('.fehrestino-tab').on('click', function () {
        var tab = $(this).data('tab');
        $('.fehrestino-tab').removeClass('active');
        $(this).addClass('active');
        $('.fehrestino-panel').hide();
        $('.fehrestino-panel[data-panel="' + tab + '"]').show();
    });

    $('.fehrestino-theme-card').on('click', function () {
        $('.fehrestino-theme-card').removeClass('is-selected');
        $(this).addClass('is-selected');
        $(this).find('input[type=radio]').prop('checked', true);
        updatePreview();
    });

    function syncPillState() {
        $('.fehrestino-pill').each(function () {
            var checked = $(this).find('input').is(':checked');
            $(this).toggleClass('is-checked', checked);
        });
    }
    $('#fehrestino-heading-pills input[type=checkbox]').on('change', function () {
        var checkedCount = $('#fehrestino-heading-pills input:checked').length;
        if (checkedCount === 0) {
            $(this).prop('checked', true);
            window.alert(t.minOneHeading || 'حداقل یک سطح هدینگ باید فعال باشد.');
        }
        syncPillState();
        updatePreview();
    });
    syncPillState();

    $('input[name="ctoc_data[title]"], input[name="ctoc_data[numbering]"], input[name="ctoc_data[default_state]"]').on('input change', updatePreview);

    $('#fehrestino-shortcode-copy').on('click', function () {
        var text = $(this).text();
        var $el = $(this);
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function () {
                $el.addClass('copied').text(t.copied || 'کپی شد ✓');
                setTimeout(function () {
                    $el.removeClass('copied').text(text);
                }, 1500);
            });
        }
    });

    function getThemeKey() {
        return $('input[name="ctoc_data[theme]"]:checked').val() || 'neo';
    }
    function getAccentColor() {
        var val = $('.fehrestino-color-picker').val();
        return val || '#1a1a1b';
    }
    function getTitle() {
        return $('input[name="ctoc_data[title]"]').val() || DEFAULT_TITLE;
    }
    function isNumbering() {
        return $('input[name="ctoc_data[numbering]"][type=checkbox]').is(':checked');
    }
    function isClosedDefault() {
        return $('input[name="ctoc_data[default_state]"]:checked').val() === 'closed';
    }

    var sampleTexts = t.samples || [
        'مقدمه و آشنایی با موضوع',
        'چرا این موضوع اهمیت دارد؟',
        'پیش‌نیازهای لازم',
        'بخش اصلی آموزش',
        'گام اول',
        'جمع‌بندی و نتیجه‌گیری'
    ];
    var sampleItems = [
        { level: 2, text: sampleTexts[0], num: '1' },
        { level: 3, text: sampleTexts[1], num: '1.1' },
        { level: 3, text: sampleTexts[2], num: '1.2' },
        { level: 2, text: sampleTexts[3], num: '2' },
        { level: 3, text: sampleTexts[4], num: '2.1' },
        { level: 2, text: sampleTexts[5], num: '3' }
    ];

    function updatePreview() {
        var theme = getThemeKey();
        var accent = getAccentColor();
        var title = getTitle();
        var numbering = isNumbering();
        var closed = isClosedDefault();

        var html = '<div class="custom-toc-wrapper theme-' + theme + (closed ? ' is-closed' : '') + (cfg.isRtl ? '' : ' is-ltr') + '" style="--fehrestino-accent:' + accent + ';">';
        html += '<div class="custom-toc-header"><span class="toc-title-text">' + escapeHtml(title) + '</span><span class="toc-icon">▼</span></div>';
        html += '<div class="custom-toc-content"><ul class="custom-toc-links">';

        sampleItems.forEach(function (item) {
            html += '<li class="toc-level-' + item.level + '"><a href="#">';
            if (numbering) {
                html += '<span class="toc-number">' + item.num + '</span> ';
            }
            html += '<span class="toc-link-text">' + escapeHtml(item.text) + '</span></a></li>';
        });

        html += '</ul></div></div>';

        $('#fehrestino-live-preview').html(html);
    }

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }

    updatePreview();

    try {
        if ($.fn.wpColorPicker) {
            $('.fehrestino-color-picker').wpColorPicker({
                change: function (event, ui) {

                    setTimeout(updatePreview, 10);
                },
                clear: function () {
                    setTimeout(updatePreview, 10);
                }
            });
        } else {

            $('.fehrestino-color-picker').on('input change', updatePreview);
        }
    } catch (e) {
        $('.fehrestino-color-picker').on('input change', updatePreview);
    }
});

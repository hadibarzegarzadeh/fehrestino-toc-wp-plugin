jQuery(document).ready(function($) {
    // Toggle TOC
    $('.fehrestino-toggle').on('click', function(e) {
        e.preventDefault();
        var content = $(this).closest('.fehrestino-toc').find('.fehrestino-content');
        
        content.slideToggle(300, function() {
            if ($(this).is(':visible')) {
                $(this).removeClass('collapsed');
            } else {
                $(this).addClass('collapsed');
            }
        });

        // Change toggle text
        var text = $(this).text();
        $(this).text(text === '[نمایش]' ? '[مخفی کردن]' : '[نمایش]');
    });

    // Smooth scroll
    $('.fehrestino-toc a').on('click', function(e) {
        var target = $(this).attr('href');
        
        if (target.startsWith('#')) {
            e.preventDefault();
            var targetElement = $(target);
            
            if (targetElement.length) {
                $('html, body').animate({
                    scrollTop: targetElement.offset().top - 100 // 100px offset for sticky headers
                }, 800);
            }
        }
    });
});

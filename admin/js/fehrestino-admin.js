jQuery(document).ready(function($) {

    $('.fehrestino-tab-links a').on('click', function(e) {
        e.preventDefault();

        var target = $(this).attr('href');

        $('.fehrestino-tab-links li').removeClass('active');
        $('.fehrestino-tab').removeClass('active');

        $(this).parent('li').addClass('active');
        $(target).addClass('active');
    });

    $('.fehrestino-template-card').on('click', function() {
        $('.fehrestino-template-card').removeClass('selected');
        $(this).addClass('selected');
    });
});

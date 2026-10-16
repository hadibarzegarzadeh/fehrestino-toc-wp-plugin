jQuery(document).ready(function($) {
    // Tabs functionality
    $('.fehrestino-tab-links a').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).attr('href');
        
        // Remove active class from all links and tabs
        $('.fehrestino-tab-links li').removeClass('active');
        $('.fehrestino-tab').removeClass('active');
        
        // Add active class to current link and tab
        $(this).parent('li').addClass('active');
        $(target).addClass('active');
    });

    // Template selector functionality
    $('.fehrestino-template-card').on('click', function() {
        $('.fehrestino-template-card').removeClass('selected');
        $(this).addClass('selected');
    });
});

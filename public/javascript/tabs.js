$(document).ready(function(){
    var responsiveTabs = function() {
        var viewportWidth = $(window).width();
        var tabsWidth = $('.tabs-container').width();

        if (viewportWidth <= tabsWidth) {
            $('.header-tabs').css('justify-content','flex-start');
            $('.tabs-container').css('padding-left','20px');
            $('.tabs-container').css('padding-right','20px');
        }
        else {
            $('.header-tabs').css('justify-content','center');
            $('.tabs-container').css('padding-left','0px');
            $('.tabs-container').css('padding-right','0px');
        }
    }

    responsiveTabs();

    $( window ).resize(function() {
        responsiveTabs();
    });
});
define([
    "jquery"
], function($) {
    return {
        updateScroll : function() {
            var header = $("#header");
            $(window).scroll(function() {
                if ($(window).scrollTop() >= 20) {
                    header.addClass('color-header');
                } else {
                    header.removeClass("color-header");
                }
            });
        }
    };
});
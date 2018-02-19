(function ($, undefined) {
    "use strict";
    var win = $(window);


    var navbar = $('.navbar');
    win.scroll(function () {
        var scroll_top = $(this).scrollTop();
        if (scroll_top >= 24) {
            navbar.removeClass('transparent');
        } else {
            navbar.addClass('transparent');
        }
    });
}(jQuery));


function themeSelectTest(teme) {
    removeClassRegex($('body'), /^theme-/);
    $('body').addClass('theme-' + teme);
    $('#id_s_theme_degrade_background_color').val(teme);
}

function removeClassRegex(element, regex) {
    return element.removeClass(function (index, classes) {
        return classes.split(/\s+/).filter(function (c) {
            return regex.test(c);
        }).join(' ');
    });
};


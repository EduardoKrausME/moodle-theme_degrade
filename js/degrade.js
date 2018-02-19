(function($) {
    "use strict";
    var win = $(window);

    var navbar = $('.navbar');
    win.scroll(function() {
        var scrolltop = $(this).scrollTop();
        if (scrolltop >= 24) {
            navbar.removeClass('transparent');
        } else {
            navbar.addClass('transparent');
        }
    });
}(jQuery));

/**
 * Theme Select Test for theme
 *
 * @param teme
 */
function themeSelectTest(teme) {
    removeClassRegex(jQuery('body'), /^theme-/);
    jQuery('body').addClass('theme-' + teme);
    jQuery('#id_s_theme_degrade_background_color').val(teme);
}

/**
 * Remove Class Regex for Selector.
 *
 * @param element
 * @param regex
 *
 * @return string
 */
function removeClassRegex(element, regex) {
    return element.removeClass(function(index, classes) {
        return classes.split(/\s+/).filter(function(c) {
            return regex.test(c);
        }).join(' ');
    });
}

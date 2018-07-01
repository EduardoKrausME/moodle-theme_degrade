require(['jquery'], function($) {
    "use strict";
    var win = $(window);

    var navbar = $('.navbar');
    win.scroll(function() {
        var scrolltop = win.scrollTop();
        if (scrolltop >= 24) {
            navbar.removeClass('transparent');
        } else {
            navbar.addClass('transparent');
        }
    });

    $('.theme-select-item').click(function(event) {
        console.log(event.currentTarget);
        var teme = $(event.currentTarget).attr('data-teme');
        themeSelectTest(teme);
    })
});

/**
 * Theme Select Test for theme
 *
 * @param teme
 */
function themeSelectTest(teme) {
    require(['jquery'], function($) {
        removeClassRegex($('body'), /^theme-/);
        $('body').addClass('theme-' + teme);
        $('#id_s_theme_degrade_background_color').val(teme);
    });
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

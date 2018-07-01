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
        var theme = $(event.currentTarget).attr('data-theme');
        themeSelectTest(theme);
    })
});

/**
 * Theme Select Test for theme
 *
 * @param {string} theme
 */
function themeSelectTest(theme) {
    require(['jquery'], function($) {
        removeClassRegex($('body'), /^theme-/);
        $('body').addClass('theme-' + theme);
        $('#id_s_theme_degrade_background_color').val(theme);
    });
}

/**
 * Remove Class Regex for Selector.
 *
 * @param {string} element
 * @param {string} regex
 *
 * @return {string} new class
 */
function removeClassRegex(element, regex) {
    return element.removeClass(function(index, classes) {
        return classes.split(/\s+/).filter(function(c) {
            return regex.test(c);
        }).join(' ');
    });
}

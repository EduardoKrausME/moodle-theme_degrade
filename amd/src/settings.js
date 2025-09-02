define(["jquery", "theme_degrade/minicolors"], function($, minicolors) {
    return {
        minicolors: function(elementid) {
            $("#" + elementid).minicolors();
        },

        form_hide: function () {
            function toggleConfig() {
                let top_scroll_fix = $("#id_s_theme_degrade_top_scroll_fix").is(":checked");
                if (top_scroll_fix) {
                    $("#admin-top_scroll_background_color").show(200);
                } else {
                    $("#admin-top_scroll_background_color").hide(200);
                }
            }

            toggleConfig();
            $("#id_s_theme_degrade_top_scroll_fix").change(toggleConfig);
        }
    };
});

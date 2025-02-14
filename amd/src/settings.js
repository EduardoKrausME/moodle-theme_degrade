define(["jquery", "theme_degrade/minicolors"], function($, minicolors) {
    var theme_degrade = {

        top_scroll : function() {
            var top_scroll = $("#id_s_theme_degrade_top_scroll");
            top_scroll.change(top_scroll_changue);
            top_scroll.click(top_scroll_changue);
            top_scroll_changue(0);

            function top_scroll_changue(timeDelay) {
                timeDelay = (timeDelay === 0) ? 0 : 300;
                if (top_scroll.is(":checked")) {
                    $("#admin-top_scroll_background_color").hide(timeDelay)
                        .prev().hide(timeDelay);
                    $("#admin-top_scroll_text_color").hide(timeDelay);
                    $("#admin-logo_write").hide(timeDelay);
                } else {
                    $("#admin-top_scroll_background_color").show(timeDelay)
                        .prev().show(timeDelay);
                    $("#admin-top_scroll_text_color").show(timeDelay);
                    $("#admin-logo_write").show(timeDelay);
                }
            }
        },

        minicolors : function(elementid) {
            $("#" + elementid).minicolors();
        },

        login : function() {
            var login_theme = $("#id_s_theme_degrade_login_theme");

            login_theme.change(login_changue);
            login_changue();

            function login_changue() {
                var login_backgroundfoto = $("#admin-login_backgroundfoto");
                var login_description = $("#admin-login_login_description, #admin-login_forgot_description, #admin-login_signup_description");
                var login_backgroundcolor = $("#admin-login_backgroundcolor");

                login_backgroundfoto.hide();
                login_description.hide();
                login_backgroundcolor.hide();

                switch (login_theme.val()) {
                    case 'login_theme_block':
                        login_backgroundfoto.show();
                        login_backgroundcolor.show();
                        break;
                    case 'login_theme_image_login' :
                        login_backgroundfoto.show();
                        break;
                    case 'login_theme_imagetext_login' :
                        login_backgroundfoto.show();
                        login_description.show();
                        break;
                    case  'login_theme_login' :
                        break;
                    case 'theme_login_branco' :
                        break;
                }
            }
        },

        about : function() {
            var about_enable = $("#id_s_theme_degrade_frontpage_about_enable");
            about_enable.change(about_changue);
            about_enable.click(about_changue);
            about_changue();

            function about_changue() {
                if (about_enable.is(":checked")) {
                    $("#theme_degrade_about > fieldset > div").show();
                    $("#theme_degrade_about > fieldset > h3").show();
                } else {
                    $("#theme_degrade_about > fieldset > div").hide();
                    $("#theme_degrade_about > fieldset > h3").hide();
                }

                $("#admin-frontpage_about_enable").show();
                setTimeout(function() {
                    $("#admin-frontpage_about_enable").show();
                }, 200);
            }
        },

        autosubmit : function(element_id) {
            $("#" + element_id).change(function() {
                $("#adminsettings").submit();
            });
        }
    };

    return theme_degrade;
});

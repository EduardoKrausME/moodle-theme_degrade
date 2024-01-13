<?php
/**
 * User: Eduardo Kraus
 * Date: 05/04/2023
 * Time: 18:59
 */

namespace theme_degrade\template;


class login_data {

    /**
     * @return array
     * @throws \coding_exception
     */
    public static function get_data() {
        global $OUTPUT;

        $backgroundurl = theme_degrade_get_setting_image("login_backgroundfoto");
        if (!$backgroundurl) {
            $backgroundurl = $OUTPUT->image_url("backgroundfoto", 'theme');
        }
        $background_image = "background-image: url(\"{$backgroundurl}\");";

        return [
            'logourl_header' => theme_degrade_get_logo("header"),
            'login_theme' => theme_degrade_get_setting('login_theme'),
            'login_background_image' => $background_image,

            'login_login_description' => theme_degrade_get_setting('login_login_description', FORMAT_HTML),
            'login_forgot_description' => theme_degrade_get_setting('login_forgot_description', FORMAT_HTML),
            'login_signup_description' => theme_degrade_get_setting('login_signup_description', FORMAT_HTML),
        ];
    }
}
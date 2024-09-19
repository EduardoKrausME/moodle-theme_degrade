<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme custom Installation.
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Theme_degrade install function.
 *
 * @return void
 * @throws Exception
 */
function xmldb_theme_degrade_install() {
    global $DB, $SITE, $CFG;

    if (method_exists("core_plugin_manager", "reset_caches")) {
        core_plugin_manager::reset_caches();
    }

    set_config("theme_color", "theme_color_blue", "theme_degrade");
    set_config("theme_color__color_primary", "#2B4E84", "theme_degrade");
    set_config("theme_color__color_secondary", "#3E65A0", "theme_degrade");
    set_config("theme_color__color_buttons", "#183054", "theme_degrade");
    set_config("theme_color__color_names", "#C0CCDC", "theme_degrade");
    set_config("theme_color__color_titles", "#E8F0FB", "theme_degrade");

    set_config("frontpage_about_title", "", "theme_degrade");
    set_config("frontpage_avaliablecourses_text", "", "theme_degrade");
    set_config("frontpage_avaliablecourses_instructor", 1, "theme_degrade");

    set_config("top_scroll_background_color", "#5C5D5F", "theme_degrade");
    set_config("top_scroll_text_color", "#FFFFFF", "theme_degrade");

    set_config("slideshow_numslides", 0, "theme_degrade");
    for ($i = 1; $i <= 9; $i++) {
        set_config("slideshow_info_{$i}", "", "theme_degrade");
        set_config("slideshow_image_{$i}", "", "theme_degrade");
        set_config("slideshow_text_{$i}", "", "theme_degrade");
        set_config("slideshow_url_{$i}", $CFG->wwwroot, "theme_degrade");
    }

    set_config("mycourses_numblocos", 4, "theme_degrade");
    for ($i = 1; $i <= 4; $i++) {
        $blocks = [
            [
                "url" => "{$CFG->wwwroot}/message/index.php",
                "title" => get_string("messages", "message"),
                "icon" => "message",
                "color" => "#2441e7",
            ], [
                "url" => "{$CFG->wwwroot}/user/profile.php",
                "title" => get_string("profile"),
                "icon" => "profile",
                "color" => "#FF1053",
            ], [
                "url" => "{$CFG->wwwroot}/user/preferences.php",
                "title" => get_string("preferences"),
                "icon" => "preferences",
                "color" => "#00A78E",
            ], [
                "url" => "{$CFG->wwwroot}/grade/report/overview/index.php",
                "title" => get_string("grades", "grades"),
                "icon" => "grade",
                "color" => "#ECD06F",
            ],
        ];
        $block = $blocks[$i - 1];

        $fs = get_file_storage();
        $filerecord = new stdClass();
        $filerecord->component = "theme_degrade";
        $filerecord->contextid = context_system::instance()->id;
        $filerecord->userid = get_admin()->id;
        $filerecord->filearea = "mycourses_icon_{$i}";
        $filerecord->filepath = "/";
        $filerecord->itemid = 0;
        $filerecord->filename = "{$block["icon"]}.svg";
        $file = $fs->create_file_from_pathname($filerecord, "{$CFG->dirroot}/theme/degrade/pix/blocks/{$block["icon"]}.svg");

        set_config("mycourses_icon_{$i}", $file->get_id(), "theme_degrade");
        set_config("mycourses_title_{$i}", $block["title"], "theme_degrade");
        set_config("mycourses_url_{$i}", $block["url"], "theme_degrade");
        set_config("mycourses_color_{$i}", $block["color"], "theme_degrade");
        set_config("frontpage_about_text_{$i}", "", "theme_degrade");
    }

    set_config("frontpage_about_enable", 0, "theme_degrade");
    set_config("frontpage_about_logo", "", "theme_degrade");
    set_config("frontpage_about_title", get_string("frontpage_about_title_default", "theme_degrade"));
    set_config("frontpage_about_description", "", "theme_degrade");
    for ($i = 1; $i <= 4; $i++) {
        set_config("frontpage_about_text_{$i}", get_string("frontpage_about_text_{$i}_defalt", "theme_degrade"));
        if ($i == 1) {
            $count = $DB->get_field_select("course", "COUNT(*)", "id != {$SITE->id}");
            set_config("frontpage_about_number_{$i}", $count, "theme_degrade");
        } else if ($i == 2) {
            $roleid = $DB->get_field_select("role", "id", "shortname = 'teacher'");
            $count = $DB->get_field_select("role_assignments", "COUNT(DISTINCT userid)", "roleid = {$roleid}");
            set_config("frontpage_about_number_{$i}", $count, "theme_degrade");
        } else if ($i == 3) {
            $roleid = $DB->get_field_select("role", "id", "shortname = 'student'");
            $count = $DB->get_field_select("role_assignments", "COUNT(DISTINCT userid)", "roleid = {$roleid}");
            set_config("frontpage_about_number_{$i}", $count, "theme_degrade");
        } else if ($i == 4) {
            $count = $DB->get_field_select("course_modules", "COUNT(*)", "visible = 1 AND course != {$SITE->id}");
            set_config("frontpage_about_number_{$i}", $count, "theme_degrade");
        }
    }

    set_config("footer_links_title", "", "theme_degrade");
    set_config("footer_social_title", "", "theme_degrade");
    set_config("footer_type", 0, "theme_degrade");
    set_config("footer_description", $SITE->fullname, "theme_degrade");
    set_config("footer_links_title", get_string("footer_links_title_default", "theme_degrade"));
    set_config("footer_links", "", "theme_degrade");
    set_config("footer_social_title", get_string("footer_social_title_default", "theme_degrade"));
    set_config("social_youtube", "", "theme_degrade");
    set_config("social_linkedin", "", "theme_degrade");
    set_config("social_facebook", "", "theme_degrade");
    set_config("social_twitter", "", "theme_degrade");
    set_config("social_instagram", "", "theme_degrade");

    set_config("contact_footer_title", "", "theme_degrade");
    set_config("contact_footer_title", get_string("footer_contact_title_default", "theme_degrade"));
    set_config("contact_address", "", "theme_degrade");
    set_config("contact_phone", "", "theme_degrade");
    set_config("contact_email", "", "theme_degrade");

    set_config("login_theme", "theme_image_login", "theme_degrade");
    set_config("login_backgroundfoto", "", "theme_degrade");
    set_config("login_backgroundcolor", "", "theme_degrade");

    set_config("login_login_description", "", "theme_degrade");
    set_config("login_forgot_description", "", "theme_degrade");
    set_config("login_signup_description", "", "theme_degrade");

    set_config("home_type", 0, "theme_degrade");
    set_config("frontpage_mycourses_text", "", "theme_degrade");
    set_config("frontpage_mycourses_instructor", "", "theme_degrade");
    set_config("logo_color", "", "theme_degrade");
    set_config("logo_write", "", "theme_degrade");
    set_config("fontfamily", "Roboto", "theme_degrade");
    set_config("fontfamily_title", "Montserrat", "theme_degrade");
    set_config("fontfamily_menus", "Roboto", "theme_degrade");
    set_config("fontfamily_sitename", "Oswald", "theme_degrade");
    set_config("customcss", "", "theme_degrade");
    set_config("footer_show_copywriter", 1, "theme_degrade");

    $fonts = "<style>\n@import url('https://fonts.googleapis.com/css2?"
        . "family=Acme" .
        "&family=Almendra:ital,wght@0,400;0,700;1,400;1,700" .
        "&family=Bad+Script" .
        "&family=Dancing+Script:wght@400..700" .
        "&family=Great+Vibes" .
        "&family=Marck+Script" .
        "&family=Nanum+Pen+Script" .
        "&family=Orbitron:wght@400..900" .
        "&family=Ubuntu+Condensed" .
        "&family=Ubuntu+Mono:ital,wght@0,400;0,700;1,400;1,700" .
        "&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700" .
        "&display=swap');\n</style>";
    set_config("pagefonts", $fonts, "theme_degrade");

    $fonts = "<style>\n@import url('https://fonts.googleapis.com/css2?" .
        "&family=Briem+Hand:wght@100..900" .
        "&family=Epilogue:ital,wght@0,100..900;1,100..900" .
        "&family=Inter+Tight:ital,wght@0,100..900;1,100..900" .
        "&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900" .
        "&family=Manrope:wght@200..800" .
        "&family=Montserrat:ital,wght@0,100..900;1,100..900" .
        "&family=Open+Sans:ital,wght@0,300..800;1,300..800" .
        "&family=Oswald:wght@200..700" .
        "&family=Oxygen:wght@300;400;700" .
        "&family=Poetsen+One" .
        "&family=Poppins:ital,wght@0,100;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700" .
        "&family=Raleway:ital,wght@0,100..900;1,100..900" .
        "&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900" .
        "&display=swap');\n</style>";
    set_config("sitefonts", $fonts, "theme_degrade");

    // Icons.
    degrade_install_settings_icons();
}

/**
 * degrade_install_settings_icons function
 *
 * @throws dml_exception
 */
function degrade_install_settings_icons() {
    global $CFG;

    for ($i = 1; $i <= 20; $i++) {
        set_config("settings_icons_name_{$i}", "", "theme_degrade");
        set_config("settings_icons_image_{$i}", "", "theme_degrade");
    }

    $files = ["audio_file", "video_file", "book", "game", "money", "slide", "support", "download"];
    set_config("settings_icons_num", count($files), "theme_degrade");

    $fs = get_file_storage();
    $filerecord = new stdClass();
    $filerecord->component = "theme_degrade";
    $filerecord->contextid = context_system::instance()->id;
    $filerecord->userid = get_admin()->id;
    $filerecord->filepath = "/";
    $filerecord->itemid = 0;

    $i = 1;
    foreach ($files as $file) {
        $filerecord->filearea = "settings_icons_image_{$i}";
        $filerecord->filename = "{$file}.svg";
        try {
            $fs->create_file_from_pathname($filerecord, "{$CFG->dirroot}/theme/degrade/pix/material/{$file}.svg");

            $default = get_string("settings_icons_default_{$file}", "theme_degrade");
            set_config("settings_icons_name_{$i}", $default, "theme_degrade");
        } catch (Exception $e) {
            echo $e->getMessage() . "<br>";
        }

        $i++;
    }
}

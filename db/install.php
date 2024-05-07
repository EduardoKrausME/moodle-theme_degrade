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
    global $DB, $SITE;

    if (method_exists('core_plugin_manager', 'reset_caches')) {
        core_plugin_manager::reset_caches();
    }

    theme_degrade_set_config("frontpage_avaliablecourses_text", '');
    theme_degrade_set_config("frontpage_avaliablecourses_instructor", 1);

    theme_degrade_set_config("slideshow_numslides", 0);
    for ($i = 1; $i <= 9; $i++) {
        theme_degrade_set_config("slideshow_info_{$i}", '');
        theme_degrade_set_config("slideshow_image_{$i}", '');
        theme_degrade_set_config("slideshow_url_{$i}", '');
        theme_degrade_set_config("slideshow_text_{$i}", '');
    }

    theme_degrade_set_config("frontpage_about_enable", 0);
    theme_degrade_set_config("frontpage_about_logo", '');
    theme_degrade_set_config("frontpage_about_title", degrade_get_string("frontpage_about_title_default"));
    theme_degrade_set_config("frontpage_about_description", '');
    for ($i = 1; $i <= 4; $i++) {
        theme_degrade_set_config("frontpage_about_text_{$i}", degrade_get_string("frontpage_about_text_{$i}_defalt"));
        if ($i == 1) {
            $count = $DB->get_field_select("course", "COUNT(*)", "id != {$SITE->id}");
            theme_degrade_set_config("frontpage_about_number_{$i}", $count);
        } else if ($i == 2) {
            $roleid = $DB->get_field_select("role", "id", "shortname = 'teacher'");
            $count = $DB->get_field_select("role_assignments", "COUNT(DISTINCT userid)", "roleid = {$roleid}");
            theme_degrade_set_config("frontpage_about_number_{$i}", $count);
        } else if ($i == 3) {
            $roleid = $DB->get_field_select("role", "id", "shortname = 'student'");
            $count = $DB->get_field_select("role_assignments", "COUNT(DISTINCT userid)", "roleid = {$roleid}");
            theme_degrade_set_config("frontpage_about_number_{$i}", $count);
        } else if ($i == 4) {
            $count = $DB->get_field_select("course_modules", "COUNT(*)", "visible = 1 AND course != {$SITE->id}");
            theme_degrade_set_config("frontpage_about_number_{$i}", $count);
        }
    }

    theme_degrade_set_config("footer_type", 0);
    theme_degrade_set_config("footer_description", $SITE->fullname);
    theme_degrade_set_config("footer_links_title", degrade_get_string("footer_links_title_default"));
    theme_degrade_set_config("footer_links", '');
    theme_degrade_set_config("footer_social_title", degrade_get_string("footer_social_title_default"));
    theme_degrade_set_config("social_youtube", '');
    theme_degrade_set_config("social_linkedin", '');
    theme_degrade_set_config("social_facebook", '');
    theme_degrade_set_config("social_twitter", '');
    theme_degrade_set_config("social_instagram", '');
    theme_degrade_set_config("contact_footer_title", degrade_get_string("footer_contact_title_default"));
    theme_degrade_set_config("contact_address", '');
    theme_degrade_set_config("contact_phone", '');
    theme_degrade_set_config("contact_email", '');

    theme_degrade_set_config("login_theme", "theme_image_login");
    theme_degrade_set_config("login_backgroundfoto", '');
    theme_degrade_set_config("login_backgroundcolor", '');

    theme_degrade_set_config("login_login_description", '');
    theme_degrade_set_config("login_forgot_description", '');
    theme_degrade_set_config("login_signup_description", '');

    theme_degrade_set_config("home_type", 0);
    theme_degrade_set_config("frontpage_mycourses_text", '');
    theme_degrade_set_config("frontpage_mycourses_instructor", '');
    theme_degrade_set_config("logo_color", '');
    theme_degrade_set_config("logo_write", '');
    theme_degrade_set_config("fontfamily", 'Roboto');
    theme_degrade_set_config("customcss", '');
    theme_degrade_set_config("footer_show_copywriter", 1);

    $fonts = "<style>\n@import url('https://fonts.googleapis.com/css2?family=Acme" .
        "&family=Almendra:ital,wght@0,400;0,700;1,400;1,700" .
        "&family=Bad+Script" .
        "&family=Dancing+Script:wght@400..700" .
        "&family=Great+Vibes" .
        "&family=Marck+Script" .
        "&family=Nanum+Pen+Script" .
        "&family=Orbitron:wght@400..900" .
        "&family=Ubuntu+Condensed" .
        "&family=Ubuntu+Mono:ital,wght@0,400;0,700;1,400;1,700" .
        "&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');\n</style>";
    set_config("pagefonts", $fonts, "theme_degrade");

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

    $files = ['audio_file', 'video_file', 'book', 'game', 'money', 'slide', 'support', 'download'];
    set_config('settings_icons_num', count($files), "theme_degrade");

    $fs = get_file_storage();
    $filerecord = new stdClass();
    $filerecord->component = 'theme_degrade';
    $filerecord->contextid = context_system::instance()->id;
    $filerecord->userid = get_admin()->id;
    $filerecord->filepath = '/';
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

/**
 * theme_degrade_set_config function
 *
 * @param object $name
 * @param object $value
 *
 * @return mixed
 */
function theme_degrade_set_config($name, $value) {
    return set_config($name, $value, "theme_degrade");
}

/**
 * @param object $name
 *
 * @return string
 *
 * @throws coding_exception
 */
function degrade_get_string($name) {
    return get_string($name, "theme_degrade");
}

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
 * @copyright   2023 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Theme_degrade install function.
 *
 * @return void
 * @throws coding_exception
 */
function xmldb_theme_degrade_install() {
    global $SITE, $DB, $SITE;

    if (method_exists('core_plugin_manager', 'reset_caches')) {
        core_plugin_manager::reset_caches();
    }

    set_config("frontpage_avaliablecourses_text", "", "theme_degrade");
    set_config("frontpage_avaliablecourses_instructor", 1, "theme_degrade");

    set_config("slideshow_numslides", 0, "theme_degrade");
    for ($i = 1; $i <= 9; $i++) {
        set_config("slideshow_info_{$i}", "", "theme_degrade");
        set_config("slideshow_image_{$i}", "", "theme_degrade");
        set_config("slideshow_url_{$i}", "", "theme_degrade");
        set_config("slideshow_text_{$i}", "", "theme_degrade");
    }

    set_config("frontpage_about_enable", 1, "theme_degrade");
    set_config("frontpage_about_logo", "", "theme_degrade");
    set_config("frontpage_about_title", get_string("frontpage_about_title_default", "theme_degrade"), "theme_degrade");
    set_config("frontpage_about_description", "", "theme_degrade");
    for ($i = 1; $i <= 4; $i++) {
        set_config("frontpage_about_text_{$i}", get_string("frontpage_about_text_{$i}_defalt", "theme_degrade"), "theme_degrade");
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

    set_config("background_color", "default1", "theme_degrade");

    set_config("theme", "theme_blue", "theme_degrade");
    set_config("customcss",
        ":root{\n--color_primary: #2B4E84;\n--color_secondary: #3E65A0;\n--color_buttons: #183054;" .
        "\n--color_names: #C0CCDC;\n--color_titles: #E8F0FB;\n}", "theme_degrade");

    set_config("footer_description", $SITE->fullname, "theme_degrade");
    set_config("footer_links_title", get_string("footer_links_title_default", "theme_degrade"), "theme_degrade");
    set_config("footer_links", "", "theme_degrade");
    set_config("footer_social_title", get_string("footer_social_title_default", "theme_degrade"), "theme_degrade");
    set_config("social_facebook", "", "theme_degrade");
    set_config("social_twitter", "", "theme_degrade");
    set_config("social_instagram", "", "theme_degrade");
    set_config("contact_footer_title", get_string("footer_contact_title_default", "theme_degrade"), "theme_degrade");
    set_config("contact_address", "", "theme_degrade");
    set_config("contact_phone", "", "theme_degrade");
    set_config("contact_email", "", "theme_degrade");
}

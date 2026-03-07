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
 * Breadcrumb settings file
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage(
    "theme_degrade_breadcrumb",
    get_string("breadcrumb_settings", "theme_degrade")
);

$setting = new admin_setting_configcheckbox(
    "theme_degrade/breadcrumb_show_mycourses_courses",
    get_string("breadcrumb_show_mycourses_courses", "theme_degrade"),
    get_string("breadcrumb_show_mycourses_courses_desc", "theme_degrade"),
    0
);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$setting = new admin_setting_configcheckbox(
    "theme_degrade/breadcrumb_show_categories",
    get_string("breadcrumb_show_categories", "theme_degrade"),
    get_string("breadcrumb_show_categories_desc", "theme_degrade"),
    0
);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$setting = new admin_setting_configcheckbox(
    "theme_degrade/breadcrumb_show_course",
    get_string("breadcrumb_show_course", "theme_degrade"),
    get_string("breadcrumb_show_course_desc", "theme_degrade"),
    0
);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$setting = new admin_setting_configcheckbox(
    "theme_degrade/breadcrumb_show_navigation_duplicates",
    get_string("breadcrumb_show_navigation_duplicates", "theme_degrade"),
    get_string("breadcrumb_show_navigation_duplicates_desc", "theme_degrade"),
    0
);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$setting = new admin_setting_configcheckbox(
    "theme_degrade/breadcrumb_show_sections",
    get_string("breadcrumb_show_sections", "theme_degrade"),
    get_string("breadcrumb_show_sections_desc", "theme_degrade"),
    0
);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$setting = new admin_setting_configcheckbox(
    "theme_degrade/breadcrumb_show_no_link_items",
    get_string("breadcrumb_show_no_link_items", "theme_degrade"),
    get_string("breadcrumb_show_no_link_items_desc", "theme_degrade"),
    0
);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$settings->add($page);

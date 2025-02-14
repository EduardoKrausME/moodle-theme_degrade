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
 * settings-course.php
 *
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage("theme_degrade_course", get_string("settings_course_heading", "theme_degrade"));

// Profile background image.
$setting = new admin_setting_configstoredfile("theme_degrade/background_course_image",
    get_string("background_course_image", "theme_degrade"),
    get_string("background_course_image_desc", "theme_degrade"),
    "background_course_image", 0,
    ["maxfiles" => 1, "accepted_types" => [".jpg", ".jpeg", ".svg", ".png"]]);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

if (file_exists("{$CFG->dirroot}/customfield/field/picture/version.php")) {
    require_once(__DIR__ . "/../db/version-background_course_image.php");
} else {
    $setting = new admin_setting_description("theme_degrade/customfield_picture_missing",
        "", get_string("customfield_picture_missing", "theme_degrade"));
    $page->add($setting);
}

$settings->add($page);

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
 * Hooks
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Category
$category = $DB->get_record("customfield_category",
    ["id" => intval(@$CFG->theme_customfield_picture)]);
if (!$category) {
    $category = (object)[
        "name" => get_string("customfield_category_name", "theme_degrade"),
        "description" => null,
        "descriptionformat" => "0",
        "sortorder" => "0",
        "timecreated" => time(),
        "timemodified" => time(),
        "component" => "core_course",
        "area" => "course",
        "itemid" => "0",
        "contextid" => context_system::instance()->id,
    ];
    $category->id = $DB->insert_record("customfield_category", $category);
    $CFG->theme_customfield_picture = $category->id;
    set_config("theme_customfield_picture", $category->id);
}

$field = $DB->get_record("customfield_field", ["shortname" => "show_image_top_course"]);
if (!$field) {
    $field = [
        "shortname" => "show_image_top_course",
        "name" => get_string("customfield_field_name", "theme_degrade"),
        "description" => get_string("customfield_field_name_desc", "theme_degrade"),
        "type" => "select",
        "descriptionformat" => 1,
        "sortorder" => 1,
        "categoryid" => $CFG->theme_customfield_picture,
        "configdata" => json_encode([
            "required" => "0",
            "uniquevalues" => "0",
            "options" => get_string("yes") . "\r\n" . get_string("no"),
            "defaultvalue" => get_string("no"),
            "locked" => "0",
            "visibility" => "1",
        ]),
        "timecreated" => time(),
        "timemodified" => time(),
    ];
    $DB->insert_record("customfield_field", $field);
}

if (file_exists("{$CFG->dirroot}/customfield/field/picture/version.php")) {
    $field = $DB->get_record("customfield_field", ["shortname" => "background_course_image"]);
    if (!$field) {
        $field = [
            "shortname" => "background_course_image",
            "name" => get_string("customfield_field_image", "theme_degrade"),
            "description" => get_string("customfield_field_image_desc", "theme_degrade"),
            "type" => "picture",
            "descriptionformat" => 1,
            "sortorder" => 2,
            "categoryid" => $CFG->theme_customfield_picture,
            "configdata" => json_encode([
                "required" => "0",
                "uniquevalues" => "0",
                "maximumbytes" => "0",
                "locked" => "0",
                "visibility" => "2",
            ]),
            "timecreated" => time(),
            "timemodified" => time(),
        ];
        $DB->insert_record("customfield_field", $field);
    }
}

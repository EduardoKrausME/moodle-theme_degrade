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
 * view file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_course\external\course_summary_exporter;

require_once("../../../config.php");
require_once("../lib.php");
global $CFG, $PAGE, $OUTPUT, $DB, $USER;

$courseid = required_param("courseid", PARAM_INT);

if (!isloggedin()) {
    $PAGE->set_url(new moodle_url("/course/view.php", ["id" => $courseid]));
}

require_admin();

if (optional_param("POST", false, PARAM_INT)) {
    require_sesskey();

    // Save configs.
    $configkeys = [
        "course_summary_banner" => PARAM_INT,
        "override_course_color" => PARAM_RAW,
    ];
    foreach ($configkeys as $name => $type) {
        $value = optional_param($name, false, $type);
        if ($value !== false) {
            set_config("{$name}_{$courseid}", $value, "theme_degrade");
        }
    }

    // Upload files.
    require_once("{$CFG->libdir}/filelib.php");
    $filefields = [
        "banner_course_url" => "theme_degrade",
        "banner_course_file" => "theme_degrade",
    ];

    $fs = get_file_storage();
    $syscontext = context_system::instance();
    foreach ($filefields as $fieldname => $component) {
        if ($fieldname == "banner_course_url") {
            $hasupload = optional_param($fieldname, null, PARAM_RAW);
            if (!$hasupload) {
                continue;
            }
            $filestring = file_get_contents($hasupload);
            if ($filestring) {
                $fieldname = "banner_course_file";
            } else {
                continue;
            }
            $filename = pathinfo($hasupload, PATHINFO_BASENAME);
        } else {
            $hasupload = !empty($_FILES[$fieldname]) && is_uploaded_file($_FILES[$fieldname]["tmp_name"]);
            $filename = clean_param($_FILES[$fieldname]["name"], PARAM_FILE);
            $filestring = false;
        }
        if ($hasupload) {
            $filearea = "{$fieldname}_{$courseid}";

            // Delete old files (if you want to keep a single file).
            $fs->delete_area_files($syscontext->id, $component, $filearea, 0);
            $filerecord = [
                "contextid" => $syscontext->id,
                "component" => $component,
                "filearea" => $filearea,
                "itemid" => 0,
                "filepath" => "/",
                "filename" => $filename,
            ];

            // Save the new file.
            if ($filestring) {
                $fs->create_file_from_string($filerecord, $filestring);
            } else {
                $fs->create_file_from_pathname($filerecord, $_FILES[$fieldname]["tmp_name"]);
            }

            set_config($filearea, $filename, $component);
        }
    }

    cache::make("theme_degrade", "course_cache")->purge();
    cache::make("theme_degrade", "css_cache")->purge();
    cache::make("theme_degrade", "frontpage_cache")->purge();
    purge_caches(["theme", "courses", "template"]);
    purge_caches();

    redirect(new moodle_url("/course/view.php?id={$courseid}"), get_string("quickstart_banner-saved", "theme_degrade"));
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url("/theme/degrade/quickstart/index.php#home");
$PAGE->set_title(get_string("quickstart_title", "theme_degrade"));
$PAGE->set_heading(get_string("quickstart_title", "theme_degrade"));

$PAGE->requires->css("/theme/degrade/quickstart/style.css");
$PAGE->requires->css("/theme/degrade/scss/colors.css");
$PAGE->requires->jquery();
echo $OUTPUT->header();

// Course.
$bannerfileurl = theme_degrade_setting_file_url("banner_course_file_{$courseid}");
if (!$bannerfileurl) {
    $bannerfileurl = theme_degrade_setting_file_url("banner_course_file");
}
$bannerfileurl = $bannerfileurl ? $bannerfileurl->out() : false;
if (!$bannerfileurl) {
    $course = $DB->get_record("course", ["id" => $courseid]);
    $course = new core_course_list_element($course);
    $courseimage = course_summary_exporter::get_course_image($course);
    if ($courseimage) {
        $bannerfileurl = $courseimage;
    }
}

$action = "{$CFG->wwwroot}/theme/degrade/quickstart/course-banner.php?courseid={$courseid}";
echo '<form action="' . $action . '" style="display:block;"
            enctype="multipart/form-data" method="post"
            class="quickstart-content">';
echo '<input type="hidden" name="POST" value="1" />';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';

$coursesummary = get_config("theme_degrade", "course_summary_banner");
$coursesummarycourse = get_config("theme_degrade", "course_summary_banner_{$courseid}");
if ( $coursesummarycourse !== false) {
    $coursesummary = $coursesummarycourse;
}

$showchangecolors = false;
$savetheme = optional_param("savetheme", "degrade", PARAM_TEXT);
if ($savetheme == "degrade") {
    require_once("{$CFG->dirroot}/theme/degrade/lib.php");
    $themecolors = theme_degrade_colors();
    $showchangecolors = true;
} else {
    $themecolors = [];
}
$brandcolor = theme_degrade_default("brandcolor", "#1a2a6c", "theme_boost");
$coursesmustache = [
    "no_accordion" => true, // For when calling out of the accordion.
    "course_summary_banner_0" => $coursesummary == 0,
    "course_summary_banner_1" => $coursesummary == 1,
    "course_summary_banner_2" => $coursesummary == 2,
    "banner_course_file_url" => $bannerfileurl,
    "banner_course_file_extensions" => "PNG, JPG",
    "show_change_colors" => $showchangecolors,
    "courseid" => $courseid,
    "override_course_color" => get_config("theme_degrade", "override_course_color_{$courseid}"),
    "colorselect" => $OUTPUT->render_from_template("theme_degrade/settings/colors", [
        "coursecolor" => true,
        "colors" => $themecolors,
        "defaultcolor" => theme_degrade_default("override_course_color_{$courseid}", $brandcolor),
        "defaultcolorfooter" => theme_degrade_default("footer_background_color", "#1a2a6c"),
        "brandcolor_background_menu" => (int) theme_degrade_default("brandcolor_background_menu", 0),
    ]),
];
echo $OUTPUT->render_from_template("theme_degrade/quickstart/courses", $coursesmustache);
$PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", ["override_course_color"]);

echo "</form>";

if (!optional_param("modal", false, PARAM_INT)) {
    echo $OUTPUT->footer();
}

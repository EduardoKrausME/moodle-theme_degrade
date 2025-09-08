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
 * @package   theme_eadtraining
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_course\external\course_summary_exporter;

require_once("../../../config.php");
require_once("../lib.php");
global $CFG, $PAGE, $OUTPUT, $DB, $USER;

$courseid = required_param("courseid", PARAM_INT);
$modal = optional_param("modal", false, PARAM_INT);

if (!isloggedin() && $modal) {
    $PAGE->set_url(new moodle_url("/course/view.php", ["id" => $courseid]));
}

require_admin();

if (optional_param("POST", false, PARAM_INT)) {
    require_sesskey();

    // Save configs.
    $configkeys = [
        "course_summary_banner" => PARAM_INT,
    ];
    foreach ($configkeys as $name => $type) {
        $value = optional_param($name, false, $type);
        if ($value !== false) {
            set_config("{$name}_{$courseid}", $value, "theme_eadtraining");
        }
    }

    // Upload files.
    require_once("{$CFG->libdir}/filelib.php");
    $filefields = [
        "banner_course_url" => "theme_eadtraining",
        "banner_course_file" => "theme_eadtraining",
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

    \cache::make("theme_eadtraining", "course_cache")->purge();
    \cache::make("theme_eadtraining", "css_cache")->purge();
    \cache::make("theme_eadtraining", "frontpage_cache")->purge();

    redirect(new moodle_url("/course/view.php?id={$courseid}"), get_string("quickstart_banner-saved", "theme_eadtraining"));
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url("/theme/eadtraining/quickstart/index.php#home");
$PAGE->set_title(get_string("quickstart_title", "theme_eadtraining"));
$PAGE->set_heading(get_string("quickstart_title", "theme_eadtraining"));

$PAGE->requires->css("/theme/eadtraining/quickstart/style.css");
if ($modal) {
    echo "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/eadtraining/quickstart/style.css\"/>";
} else {
    echo $OUTPUT->header();
}

// Course.
$bannerfileurl = theme_eadtraining_setting_file_url("banner_course_file_{$courseid}");
if (!$bannerfileurl) {
    $bannerfileurl = theme_eadtraining_setting_file_url("banner_course_file");
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

$action = "{$CFG->wwwroot}/theme/eadtraining/quickstart/course-banner.php?courseid={$courseid}";
echo '<form action="' . $action . '" style="display:block;"
            enctype="multipart/form-data" method="post"
            class="quickstart-content">';
echo '<input type="hidden" name="POST" value="1" />';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';

$coursesummary = get_config("theme_eadtraining", "course_summary_banner");
$coursesummarycourse = get_config("theme_eadtraining", "course_summary_banner_{$courseid}");
if ( $coursesummarycourse !== false) {
    $coursesummary = $coursesummarycourse;
}

$coursesmustache = [
    "no_accordion" => true, // For when calling out of the accordion.
    "course_summary_banner_0" => $coursesummary == 0,
    "course_summary_banner_1" => $coursesummary == 1,
    "course_summary_banner_2" => $coursesummary == 2,
    "banner_course_file_url" => $bannerfileurl,
    "banner_course_file_extensions" => "PNG, JPG",
];
echo $OUTPUT->render_from_template("theme_eadtraining/quickstart/courses", $coursesmustache);

echo "</form>";

if (!optional_param("modal", false, PARAM_INT)) {
    echo $OUTPUT->footer();
}

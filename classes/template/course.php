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
 * Course template data
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\template;

use core_completion\cm_completion_details;
use core_course_list_element;
use moodle_url;
use theme_degrade\output\core\course_renderer_util;

/**
 * Class course
 *
 * @package theme_degrade\template
 */
class course {

    /**
     * Function courseindex
     *
     * @param $courseid
     *
     * @return bool | string
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function courseindex($courseid) {
        global $USER, $OUTPUT, $DB, $CFG, $PAGE;

        // If the course index is explicitly set and if it should be hidden.
        if ($PAGE->get_show_course_index() === false) {
            return false;
        }

        // Only add course index on non-site course pages.
        if (!$PAGE->course || $PAGE->course->id == SITEID) {
            return false;
        }

        // Show course index to users can access the course only.
        if (!can_access_course($PAGE->course, null, '', true)) {
            return false;
        }

        $result = [
            "all-completion" => 0,
            "count-completion" => 0,
        ];

        $courseformat = course_get_format($courseid);
        $modinfo = $courseformat->get_modinfo();
        $completioninfo = new \completion_info(get_course($courseid));

        $sql = "
            SELECT itemid, contextid, filename
              FROM {files}
             WHERE filearea LIKE 'theme_degrade_customicon'
               AND filename LIKE '__%'";
        $customicons = $DB->get_records_sql($sql);

        $sections = $modinfo->get_section_info_all();
        /** @var \section_info $section */
        foreach ($sections as $section) {
            if ($courseformat->is_section_visible($section)) {
                if ($section->visible) {
                    $sessions = [
                        "id" => $section->id,
                        "name" => self::get_section_name($section, $courseformat->get_course()->format),
                        "cms" => [],
                        "iscompletion" => true,
                        "hascompletion" => false,
                    ];

                    /** @var \cm_info $cminfo */
                    foreach ($modinfo->cms as $cminfo) {
                        if ($cminfo->is_visible_on_course_page() && $cminfo->uservisible) {
                            if ($cminfo->get_section_info()->id == $section->id) {

                                if ($cminfo->modname == "label") {
                                    continue;
                                }

                                if (isset($customicons[$cminfo->id])) {
                                    $customicon = $customicons[$cminfo->id];
                                    $iconurl = moodle_url::make_file_url(
                                        "{$CFG->wwwroot}/pluginfile.php",
                                        implode("/", [
                                            "",
                                            $customicon->contextid,
                                            "theme_degrade",
                                            "theme_degrade_customicon",
                                            $customicon->itemid,
                                            $customicon->filename,
                                        ]));
                                } else {
                                    $iconurl = $cminfo->get_icon_url()->out();
                                }

                                $hascompletion = $completioninfo->is_enabled($cminfo) != COMPLETION_DISABLED;
                                $isautomatic = $cminfo->completion == COMPLETION_TRACKING_AUTOMATIC;
                                $ismanual = $cminfo->completion == COMPLETION_TRACKING_MANUAL;
                                $iscompletion = false;

                                $class = [];
                                if ($hascompletion) {
                                    $sessions["hascompletion"] = true;
                                    $class[] = "completioninfo-completion";
                                    $result["count-completion"]++;

                                    $state = $completioninfo->internal_get_state($cminfo, $USER->id, null);
                                    $iscompletion = $state == COMPLETION_COMPLETE;
                                    if ($iscompletion) {
                                        $result["all-completion"]++;
                                        if ($isautomatic) {
                                            $class[] = "completioninfo-auto";
                                        }
                                        if ($ismanual) {
                                            $class[] = "completioninfo-manual";
                                        }
                                    } else {
                                        $sessions["iscompletion"] = false;
                                    }
                                }

                                $sessions["cms"][] = [
                                    "cmid" => $cminfo->id,
                                    "activityname" => $cminfo->get_formatted_name(),
                                    "url" => $cminfo->get_url() ? $cminfo->get_url()->out() : "",
                                    "iconurl" => $iconurl,

                                    "hascompletion" => $hascompletion,
                                    "iscompletion" => $iscompletion,
                                    "isautomatic" => $isautomatic,
                                    "ismanual" => $ismanual,
                                    "completion-class" => implode(" ", $class),
                                ];
                            }
                        }
                    }

                    $result["sessions"][] = $sessions;
                }
            }
        }

        if ($result["count-completion"] == 0) {
            $result["percentage"] = 0;
        } else {
            $result["percentage"] = intval(($result["all-completion"] / $result["count-completion"]) * 100);
        }

        return $OUTPUT->render_from_template("theme_degrade/includes/courseindex", $result);
    }

    /**
     * Function get_section_name
     *
     * @param $section
     * @param $format
     *
     * @return string
     * @throws \coding_exception
     */
    private static function get_section_name($section, $format) {
        if (isset($section->name[3])) {
            return $section->name;
        }

        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }

        if ($sectionnum == 0) {
            if (get_string_manager()->string_exists("section0name", "format_{$format}")) {
                return get_string("section0name", "format_{$format}");
            }
        } else if (get_string_manager()->string_exists("sectionname", "format_{$format}")) {
            return get_string("sectionname", "format_{$format}") . " " . $sectionnum;
        }

        return "";
    }

    /**
     * Function show_image_top_course
     *
     * @param \stdClass $course
     *
     * @return bool
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public static function show_image_top_course($course) {
        global $DB;

        $sql = "
            SELECT *
              FROM {customfield_data}
             WHERE fieldid  IN(
                    SELECT id
                      FROM {customfield_field}
                     WHERE shortname = 'show_image_top_course'
                 )
               AND instanceid = :courseid";
        $data = $DB->get_record_sql($sql, ["courseid" => $course->id]);

        if (!isset($data->intvalue)) {
            $data = (object)["intvalue" => 0];
        }

        // Marcou não nas configurações.
        if ($data->intvalue == 2) {
            return false;
        }
        $backgroundurl = theme_degrade_get_setting_image("background_course_image");

        // Marcado (vazio) nas configurações.
        if ($data->intvalue == 0) {
            if ($backgroundurl) {
                return $backgroundurl;
            }
        }

        $sql = "
            SELECT *
              FROM {customfield_data}
             WHERE fieldid  IN(
                    SELECT id
                      FROM {customfield_field}
                     WHERE shortname = 'background_course_image'
                 )
               AND instanceid = :courseid";
        $data = $DB->get_record_sql($sql, ["courseid" => $course->id]);

        $sql = "
            SELECT contextid, itemid, filename
              FROM {files}
             WHERE component = 'customfield_picture'
               AND filearea  = 'file'
               AND itemid    = :itemid
               AND filesize  > 10";
        $file = $DB->get_record_sql($sql, ["itemid" => $data->id]);
        if ($file) {
            return moodle_url::make_pluginfile_url($file->contextid, "customfield_picture", "file",
                $file->itemid, "/", $file->filename)->out(true);
        }

        if ($backgroundurl) {
            return $backgroundurl;
        }

        if ($data->intvalue == 1) {
            return course_renderer_util::couse_image(new core_course_list_element($course));
        }

        return false;
    }
}

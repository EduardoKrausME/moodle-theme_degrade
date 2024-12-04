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
 * course_renderer.php
 *
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\output\core;

use context_course;
use moodle_url;
use user_picture;

/**
 * Class course_renderer_util
 *
 * @package theme_degrade\output\core
 */
class course_renderer_util {
    /**
     * Function couse_image
     *
     * @param \core_course_list_element $course
     *
     * @return bool|string
     */
    public static function couse_image(\core_course_list_element $course) {
        global $CFG, $OUTPUT;

        $courseimage = false;

        /** @var \stored_file $file */
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $courseimage = file_encode_url("{$CFG->wwwroot}/pluginfile.php",
                    "/{$file->get_contextid()}/{$file->get_component()}/" .
                    "{$file->get_filearea()}{$file->get_filepath()}{$file->get_filename()}", !$isimage);

            }
        }

        if (empty($courseimage)) {
            $courseimage = $OUTPUT->image_url('course-default', 'theme')->out();
        }

        return $courseimage;
    }

    /**
     * Function get_teachers
     *
     * @param $course
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_teachers($course) {
        global $PAGE, $DB;

        $teachers = [];
        if ($course->has_course_contacts()) {
            foreach ($course->get_course_contacts() as $coursecontact) {

                $user = $DB->get_record('user', ['id' => $coursecontact['user']->id],
                    implode(',', \core_user\fields::get_picture_fields()), MUST_EXIST);
                $userpicture = new user_picture($user);

                $teachers[$coursecontact['user']->id] = [
                    "instructorname" => $coursecontact['username'],
                    "instructorurl" => $userpicture->get_url($PAGE)->out(),
                    "instructortitle" => get_string("instructor", "theme_degrade"),
                ];

                // Limita a dois instrutores.
                if (isset(array_keys($teachers)[1])) {
                    return array_values($teachers);
                }
            }
        }
        return array_values($teachers);
    }

    /**
     * Function course_url
     *
     * @param $course
     *
     * @return string
     * @throws \moodle_exception
     */
    public static function course_url($course) {
        $viewurl = new moodle_url('/course/view.php', ['id' => $course->id]);

        return $viewurl->out();
    }

    /**
     * Function count_lessson
     *
     * @param $course
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function count_lessson($course) {
        global $DB;

        $countlesson = $DB->get_field_select("course_modules", "COUNT(*)", "visible = 1 AND course = {$course->id}");

        if ($countlesson < 2) {
            return get_string("countlesson", "theme_degrade", $countlesson);
        } else {
            return get_string("countlessons", "theme_degrade", $countlesson);
        }
    }

    /**
     * Function is_enrolled
     *
     * @param $course
     *
     * @return bool
     */
    public static function is_enrolled($course) {
        global $USER;
        $context = context_course::instance($course->id);
        $enrolled = is_enrolled($context, $USER->id, '', true);

        return $enrolled;
    }
}

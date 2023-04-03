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
 * User: Eduardo Kraus
 * Date: 01/04/2023
 * Time: 08:21
 */

namespace theme_degrade\output\core;


use context_course;
use moodle_url;
use user_picture;

class course_renderer_util {
    /**
     * @param $course
     *
     * @return string
     */
    public static function couse_image($course) {
        global $CFG, $OUTPUT;

        $imgurl = false;
        $noimgurl = $OUTPUT->image_url('curso-no-photo', 'theme')->out();

        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                "/{$file->get_contextid()}/{$file->get_component()}/" .
                "{$file->get_filearea()}{$file->get_filepath()}{$file->get_filename()}", !$isimage);
            if (!$isimage) {
                $imgurl = $noimgurl;
            }
        }

        if (empty($imgurl)) {
            $imgurl = $noimgurl;
        }

            return $imgurl;
    }

    /**
     * @param $courseid
     *
     * @return array
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function get_teachers($course) {
        global $PAGE, $DB;

        $teachers = [];
        if ($course->has_course_contacts()) {
            foreach ($course->get_course_contacts() as $coursecontact) {

                $user = $DB->get_record('user', array('id' => $coursecontact['user']->id),
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
     * @param $course
     *
     * @return string
     * @throws \moodle_exception
     */
    public static function course_url($course) {
        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));

        return $courseurl->out();
    }

    /**
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

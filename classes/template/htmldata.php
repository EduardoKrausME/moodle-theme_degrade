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
 * Footer template data
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\template;

use core_course_list_element;
use theme_degrade\output\core\course_renderer_util;

/**
 * Class htmldata
 *
 * @package theme_degrade\template
 */
class htmldata {
    /**
     * Function vvveb__change_my_courses
     *
     * @param $html
     *
     * @return null|string|string[]
     * @throws \dml_exception
     */
    public static function vvveb__change_my_courses($html) {
        if (strpos($html, "vvveb_home_automatically_my_course") === false) {
            return $html;
        }

        global $OUTPUT, $DB, $USER;
        $sql = "
        SELECT c.*
          FROM {user_enrolments} ue
          JOIN {enrol}           e  ON e.id = ue.enrolid
          JOIN {course}          c  ON c.id = e.courseid
         WHERE ue.userid = {$USER->id}
      ORDER BY c.fullname";
        $courses = $DB->get_records_sql($sql);

        $data = [];
        foreach ($courses as $course) {
            $course->courseimage = course_renderer_util::couse_image(new core_course_list_element($course));
            $data[] = $course;
        }
        $courseshtml = $OUTPUT->render_from_template('theme_degrade/vvveb/course', ['couses' => $data]);

        return preg_replace('/<div.*?vvveb_home_automatically_my_course.*?<\/div>/s',
            "<div class='row'>{$courseshtml}</div>", $html);
    }
}

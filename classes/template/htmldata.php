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
use local_kopere_dashboard\util\course;
use local_kopere_dashboard\util\enroll_util;
use local_kopere_pay\util\CourseUtil;
use theme_degrade\output\core\course_renderer_util;

/**
 * Class htmldata
 *
 * @package theme_degrade\template
 */
class htmldata {

    /**
     * Var replaces
     *
     * @var array
     */
    private static $replaces = [];

    /**
     * Function vvveb__change_courses
     *
     * @param $html
     *
     * @return mixed|null|string|string[]
     */
    public static function vvveb__change_courses($html) {
        self::vvveb__change_my_courses($html);
        self::vvveb__change_popular_courses($html);
        self::vvveb__change_catalogo($html);
        self::vvveb__change_category($html);

        $html = preg_replace('/<.*?vvveb-remove.*?>/', '', $html);
        foreach (self::$replaces as $replace) {
            foreach ($replace["from"] as $from) {
                $html = str_replace($from, $replace["to"], $html);
            }
        }

        $html = "
            <link href=\"https://moodle.aulaemvideo.com.br/theme/degrade/_editor/libs/aos/aos.css\" rel=\"stylesheet\">
            <link href=\"https://moodle.aulaemvideo.com.br/theme/degrade/_editor/libs/aos/aos.js\" rel=\"stylesheet\">
            " . trim($html);
        return $html;
    }

    /**
     * Function vvveb__change_my_courses
     *
     * @param $html
     *
     * @return mixed
     */
    private static function vvveb__change_my_courses($html) {

        if (strpos($html, "vvveb_home_automatically_my_course") === false) {
            return;
        }

        if (!isloggedin()) {
            preg_match_all('/<div.*?vvveb_home_automatically_my_course.*?>(.*?)<\/div>/s', $html, $htmls);
            self::$replaces[] = [
                "from" => $htmls[1],
                "to" => "",
            ];
            return;
        }

        global $USER;
        $cache = \cache::make("theme_degrade", "layout_cache");
        $cachekey = "vvveb__change_my_courses-{$USER->id}" . current_language();
        if ($cache->has($cachekey)) {
            $data = $cache->get($cachekey);
        }
        if (!isset($data["from"])) {
            global $OUTPUT, $DB, $CFG;
            $sql = "
                    SELECT c.*
                      FROM {user_enrolments} ue
                      JOIN {enrol}           e  ON e.id = ue.enrolid
                      JOIN {course}          c  ON c.id = e.courseid
                     WHERE ue.userid = {$USER->id}
                       AND c.visible = 1
                  ORDER BY c.fullname";
            $courses = $DB->get_records_sql($sql);

            $datasave = [];
            foreach ($courses as $course) {
                $course->title = $course->fullname;
                $course->link = "{$CFG->wwwroot}/course/view.php?id={$course->id}";
                $course->access = get_string("course_access", "theme_degrade");

                $course->courseimage = course_renderer_util::couse_image(new core_course_list_element($course));
                if (!isset($course->courseimage[3])) {
                    $course->courseimage = $OUTPUT->image_url("course-default", "theme")->out();
                }
                $datasave[] = $course;
            }
            $courseshtml = $OUTPUT->render_from_template("theme_degrade/vvveb/course", ["courses" => $datasave]);

            preg_match_all('/<div.*?vvveb_home_automatically_my_course.*?>(.*?)<\/div>/s', $html, $htmls);
            $data = [
                "from" => $htmls[1],
                "to" => $courseshtml,
            ];

            $cache->set($cachekey, $data);
        }

        self::$replaces[] = $data;
    }

    /**
     * Function vvveb__change_popular_courses
     *
     * @param $html
     *
     * @return mixed
     */
    private static function vvveb__change_popular_courses($html) {

        if (strpos($html, "vvveb_home_automatically_popular") === false) {
            return;
        }

        $cache = \cache::make("theme_degrade", "layout_cache");
        $cachekey = "vvveb__change_popular_courses-" . current_language();
        if ($cache->has($cachekey)) {
            $data = $cache->get($cachekey);
        }
        if (!isset($data["from"])) {
            global $OUTPUT, $DB, $CFG;
            $sql = "
                    SELECT c.*, COUNT(ue.id) AS enrolments
                      FROM {course}          c
                      JOIN {enrol}           e  ON e.courseid = c.id
                      JOIN {user_enrolments} ue ON ue.enrolid = e.id
                     WHERE c.visible = 1
                  GROUP BY c.id
                  ORDER BY enrolments DESC
                     LIMIT 12";
            $courses = $DB->get_records_sql($sql);

            $datasave = [];
            foreach ($courses as $course) {
                $course->title = $course->fullname;
                $course->link = "{$CFG->wwwroot}/course/view.php?id={$course->id}";
                $course->text = self::text_course($course);

                if (self::enrolled($course->id)) {
                    $course->access = get_string("course_access", "theme_degrade");
                } else {
                    $course->access = get_string("course_moore", "theme_degrade");

                    if (file_exists("{$CFG->dirroot}/local/kopere_pay/lib.php") && $course->id) {
                        $koperepaydetalhe = $DB->get_record("kopere_pay_detalhe", ["course" => $course->id]);
                        if ($koperepaydetalhe) {
                            $precoint = str_replace(".", "", $koperepaydetalhe->preco);
                            $precoint = str_replace(",", ".", $precoint);
                            $precoint = floatval("0{$precoint}");

                            if (!$precoint) {
                                $course->cursopreco = get_string("webpages_free", "local_kopere_dashboard");
                            } else {
                                $course->cursopreco = "R$ {$koperepaydetalhe->preco}";
                            }

                            $enable = get_config("local_kopere_dashboard", "builder_enable_{$course->id}");
                            if ($enable) {
                                $course->link = "{$CFG->wwwroot}/local/kopere_pay/view.php?id={$course->id}";
                                $course->title = get_config("local_kopere_dashboard", "builder_titulo_{$course->id}");

                                $course->offprice = get_config("local_kopere_dashboard", "builder_offprice_{$course->id}");
                            } else {
                                $course->link = "{$CFG->wwwroot}/local/kopere_pay/?id={$course->id}";
                            }
                        }
                    }
                }

                $course->courseimage = course_renderer_util::couse_image(new core_course_list_element($course));
                if (!isset($course->courseimage[3])) {
                    $course->courseimage = $OUTPUT->image_url("course-default", "theme")->out();
                }

                $datasave[] = $course;
            }

            $courseshtml = $OUTPUT->render_from_template("theme_degrade/vvveb/course", ["courses" => $datasave]);

            preg_match_all('/<div.*?vvveb_home_automatically_popular.*?>(.*?)<\/div>/s', $html, $htmls);
            $data = [
                "from" => $htmls[1],
                "to" => $courseshtml,
            ];

            $cache->set($cachekey, $data);
        }

        self::$replaces[] = $data;
    }

    /**
     * Function vvveb__change_catalogo
     *
     * @param $html
     *
     * @return mixed
     */
    private static function vvveb__change_catalogo($html) {
        if (strpos($html, "vvveb_home_automatically_catalogo") === false) {
            return;
        }

        global $CFG;
        if (!file_exists("{$CFG->dirroot}/local/kopere_pay/lib.php")) {
            return;
        }

        $cache = \cache::make("theme_degrade", "layout_cache");
        $cachekey = "vvveb__change_catalogo-" . current_language();
        if ($cache->has($cachekey)) {
            $data = $cache->get($cachekey);
        }
        if (!isset($data["from"])) {
            global $OUTPUT, $DB, $CFG;
            $sql = "SELECT * FROM {kopere_pay_detalhe} WHERE status = 'aberto' AND portfolio = 'visivel'";
            $koperepaydetalhes = $DB->get_records_sql($sql);

            $datasave = [];
            /** @var \local_kopere_pay\vo\kopere_pay_detalhe $koperepaydetalhe */
            foreach ($koperepaydetalhes as $koperepaydetalhe) {

                $course = CourseUtil::find($koperepaydetalhe->course, false);
                if (!$course) {
                    continue;
                }

                $course->text = self::text_course($course);

                if (self::enrolled($koperepaydetalhe->course)) {
                    $course->access = get_string("course_access", "theme_degrade");
                    $course->link = "{$CFG->wwwroot}/course/view.php?id={$course->id}";
                    $course->title = $course->fullname;
                } else {
                    $course->access = get_string("course_moore", "theme_degrade");

                    $precoint = str_replace(".", "", $koperepaydetalhe->preco);
                    $precoint = str_replace(",", ".", $precoint);
                    $precoint = floatval("0{$precoint}");

                    if (!$precoint) {
                        $course->cursopreco = get_string("webpages_free", "local_kopere_dashboard");
                    } else {
                        $course->cursopreco = "R$ {$koperepaydetalhe->preco}";
                    }

                    $enable = get_config("local_kopere_dashboard", "builder_enable_{$koperepaydetalhe->course}");
                    if ($enable) {
                        $course->link = "{$CFG->wwwroot}/local/kopere_pay/view.php?id={$koperepaydetalhe->course}";
                        $course->title = get_config("local_kopere_dashboard", "builder_titulo_{$koperepaydetalhe->course}");
                        $course->offprice = get_config("local_kopere_dashboard", "builder_offprice_{$koperepaydetalhe->course}");
                    } else {
                        $course->link = "{$CFG->wwwroot}/local/kopere_pay/?id={$koperepaydetalhe->course}";
                    }
                }

                $course->courseimage = course::overview_image($koperepaydetalhe->course);
                if (!isset($course->courseimage[3])) {
                    $course->courseimage = $OUTPUT->image_url("course-default", "theme")->out();
                }

                $datasave[] = $course;
            }

            $courseshtml = $OUTPUT->render_from_template("theme_degrade/vvveb/course", ["courses" => $datasave]);

            preg_match_all('/<div.*?vvveb_home_automatically_catalogo.*?>(.*?)<\/div>/s', $html, $htmls);

            $data = [
                "from" => $htmls[1],
                "to" => $courseshtml,
            ];
            $cache->set($cachekey, $data);
        }

        self::$replaces[] = $data;
    }

    /**
     * Function vvveb__change_category
     *
     * @param $html
     */
    private static function vvveb__change_category($html) {
        if (strpos($html, "vvveb_home_automatically_category") === false) {
            return;
        }

        $cache = \cache::make("theme_degrade", "layout_cache");
        $cachekey = "vvveb__change_category-" . current_language();
        if ($cache->has($cachekey)) {
            $data = $cache->get($cachekey);
        }
        if (!isset($data["from"])) {
            global $OUTPUT, $DB, $CFG;

            $courseshtml = "";
            $categories = $DB->get_records("course_categories", ["visible" => 1]);
            foreach ($categories as $category) {

                $sql = "
                        SELECT c.*
                          FROM {course}          AS  c
                         WHERE c.category = {$category->id}
                           AND c.visible = 1
                      GROUP BY c.id";
                $courses = $DB->get_records_sql($sql);

                $datasave = [];
                foreach ($courses as $course) {
                    $course->title = $course->fullname;
                    $course->access = get_string("course_access", "theme_degrade");
                    $course->link = "{$CFG->wwwroot}/course/view.php?id={$course->id}";

                    $course->text = self::text_course($course);

                    if (self::enrolled($course->id)) {
                        $course->access = get_string("course_access", "theme_degrade");
                    } else {
                        $course->access = get_string("course_moore", "theme_degrade");
                        if (file_exists("{$CFG->dirroot}/local/kopere_pay/lib.php") && $course->id) {
                            $koperepaydetalhe = $DB->get_record("kopere_pay_detalhe", ["course" => $course->id]);
                            if ($koperepaydetalhe) {
                                $precoint = str_replace(".", "", $koperepaydetalhe->preco);
                                $precoint = str_replace(",", ".", $precoint);
                                $precoint = floatval("0{$precoint}");

                                if (!$precoint) {
                                    $course->cursopreco = get_string("webpages_free", "local_kopere_dashboard");
                                } else {
                                    $course->cursopreco = "R$ {$koperepaydetalhe->preco}";
                                }

                                $enable = get_config("local_kopere_dashboard", "builder_enable_{$course->id}");
                                if ($enable) {
                                    $course->link = "{$CFG->wwwroot}/local/kopere_pay/view.php?id={$course->id}";
                                    $course->title = get_config("local_kopere_dashboard", "builder_titulo_{$course->id}");

                                    $course->offprice = get_config("local_kopere_dashboard", "builder_offprice_{$course->id}");
                                } else {
                                    $course->link = "{$CFG->wwwroot}/local/kopere_pay/?id={$course->id}";
                                }
                            }
                        }
                    }

                    $course->courseimage = course_renderer_util::couse_image(new core_course_list_element($course));
                    if (!isset($course->courseimage[3])) {
                        $course->courseimage = $OUTPUT->image_url("course-default", "theme")->out();
                    }
                    $datasave[] = $course;
                }

                $courseshtml .= "<fieldset>";
                $courseshtml .= "<legend class='category'>{$category->name}</legend>";
                $courseshtml .= $OUTPUT->render_from_template("theme_degrade/vvveb/course", ["courses" => $datasave]);
                $courseshtml .= "</fieldset>";
            }

            preg_match_all('/<div.*?vvveb_home_automatically_category.*?>(.*?)<\/div>/s', $html, $htmls);
            $data = [
                "from" => $htmls[1],
                "to" => $courseshtml,
            ];
            $cache->set($cachekey, $data);
        }

        self::$replaces[] = $data;
    }

    /**
     * Function text_course
     *
     * @param $course
     *
     * @return string
     */
    private static function text_course($course) {
        $text = get_config("local_kopere_dashboard", "builder_topo_{$course->id}");

        if (!isset($text[50])) {
            $text = $course->summary;
        }

        $text = preg_replace('/<h\d.*?<\/h\d>/', '', $text);
        $text = strip_tags($text);
        return self::truncate_text($text, 300);
    }

    /**
     * Function enrolled
     *
     * @param $courseid
     *
     * @return bool
     */
    private static function enrolled($courseid) {
        global $DB, $USER;

        // Evita erro.
        $context = \context_course::instance($courseid, IGNORE_MISSING);
        if ($context == null) {
            return false;
        }

        $enrol = $DB->get_record("enrol",
            ["courseid" => $courseid, "enrol" => "manual"], "*", IGNORE_MULTIPLE);
        if ($enrol == null) {
            return false;
        }

        $testroleassignments = $DB->get_record("role_assignments",
            ["roleid" => 5, "contextid" => $context->id, "userid" => $USER->id], "*", IGNORE_MULTIPLE);
        if ($testroleassignments == null) {
            return false;
        }

        $userenrolments = $DB->get_record("user_enrolments",
            ["enrolid" => $enrol->id, "userid" => $USER->id], "*", IGNORE_MULTIPLE);
        if ($userenrolments != null) {
            return !$userenrolments->status;
        } else {
            return false;
        }
    }

    /**
     * Function truncate_text
     *
     * @param $texto
     * @param $caracteres
     *
     * @return string
     */
    private static function truncate_text($texto, $caracteres) {
        if (strlen($texto) > $caracteres) {
            $a = explode(" ", $texto);
            if (count($a) > 1) {
                array_pop($a);
                $texto = implode(" ", $a);
                $texto .= "...";

                return self::truncate_text($texto, $caracteres);
            } else {
                return $texto;
            }
        } else {
            return $texto;
        }
    }
}

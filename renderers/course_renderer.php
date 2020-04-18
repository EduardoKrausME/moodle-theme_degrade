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
 * File for renderer the moodle predefined function.
 * @package    theme_degrade
 * @copyright  2020 Eduardo Kraus (https://www.eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . "/course/renderer.php");

/**
 * Theme Degrade course renderer class inherit from core course renderer class.
 * @copyright  2020 Eduardo Kraus (https://www.eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_degrade_core_course_renderer extends core_course_renderer {

    /**
     * Course list for course menu on header
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function top_course_menu() {
        global $CFG, $DB;
        // require_once($CFG->libdir. '/coursecatlib.php');.
        $list = core_course_category::make_categories_list();
        $mclist = array();

        $sql = "SELECT a.category , a.cnt from ( SELECT category , count(category) as cnt FROM {course}";
        $sql .= " WHERE category != '0' and visible = ? group by category ) as a order by a.cnt desc ";

        $params = array('1');
        $result = $DB->get_records_sql($sql, $params, 0, 0);
        shuffle($result);
        if ($result) {
            foreach ($result as $rowcat) {
                if ($result = $DB->record_exists('course_categories', array('id' => $rowcat->category))) {
                    $mclist[] = $rowcat->category;
                }
            }
        }
        $mclist1 = array_slice($mclist, 0, 4, true);
        $rcourseids = array();
        foreach ($mclist1 as $catid) {
            $coursecat = core_course_category::get($catid);
            $cname = $coursecat->get_formatted_name();
            $menuheader = '<div class="cols"><h6>' . $cname . '</h6><ul>' . "\n";
            $menufooter = '</ul></div>' . "\n";
            $href = $CFG->wwwroot . '/course/index.php?categoryid=' . $catid;
            $mmenuheader = '<li class="dropdown-submenu"><a href="' . $href . '" class="">' . $cname . '</a><ul class="dropdown-menu">';
            $mmenufooter = '</ul></li>';
            $menuitems = '';
            $options = array();
            $options['recursive'] = true;
            $options['offset'] = 0;
            $options['limit'] = 6;
            $options['sort'] = array('sortorder' => 'ASC');
            if ($ccc = $coursecat->get_courses($options)) {
                foreach ($ccc as $cc) {
                    if ($cc->visible == "0" || $cc->id == "1") {
                        continue;
                    }
                    $courseurl = new moodle_url("/course/view.php", array("id" => $cc->id));
                    $menuitems .= '<li><a href="' . $courseurl . '">' . $cc->get_formatted_name() . '</a></li>' . "\n";
                }
                if (!empty($menuitems)) {
                    $rcourseids[$catid] = array("desk" => $menuheader . $menuitems . $menufooter,
                        "mobile" => $mmenuheader . $menuitems . $mmenufooter
                    );
                }
            }
        }
        $mcourseids = array_slice($rcourseids, 0, 4);
        $strcourse = $mstrcourse = '';
        foreach ($mcourseids as $ctid => $marr) {
            $strcourse .= $marr["desk"] . "\n";
            $mstrcourse .= $marr["mobile"] . "\n";
        }

        $courseaurl = new moodle_url('/course/index.php');
        if (!empty($strcourse)) {
            $topcmenu = '<div class="custom-dropdown-menu" id="cr_menu" style="display:none;">';
            $topcmenu .= '<div class="cols-wrap">' . $strcourse . '<div class="clearfix"></div></div></div>';
        } else {
            $topcmenu = "";
        }
        $topmmenu = "
            <ul class=\"dropdown-menu\">
                {$mstrcourse}
                <li><a href=\"{$courseaurl}\">" . get_string('viewall', 'theme_degrade') . "</a></li>
            </ul>";
        return compact('topcmenu', 'topmmenu');
    }

}
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
 * Class mod_icon
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\external;

use theme_degrade\template\frontapage_data;

defined('MOODLE_INTERNAL') || die;
require_once("{$CFG->libdir}/externallib.php");

/**
 * Class mycourses
 *
 * @package theme_degrade\external
 */
class mycourses extends \external_api {

    /**
     * html_parameters function
     *
     * @return \external_function_parameters
     */
    public static function html_parameters() {
        return new \external_function_parameters([]);
    }

    /**
     * html function
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function html() {
        global $OUTPUT, $CFG, $PAGE;

        require_once("{$CFG->dirroot}/theme/degrade/lib.php");
        $PAGE->set_context(\context_system::instance());

        $mycoursesnumblock = theme_degrade_get_setting("mycourses_numblocos");
        $templatedata = [
            "mycourses_edit_settings" => frontapage_data::edit_settings("theme_degrade_mycourses"),
            "mycourses_numblock" => $mycoursesnumblock,
            "mycourses_block" => [],
        ];

        if (!$mycoursesnumblock) {
            return ["html" => ""];
        }

        switch ($mycoursesnumblock) {
            case 1:
                $templatedata['mycourses_col'] = 12;
                break;
            case 2:
                $templatedata['mycourses_col'] = 6;
                break;
            case 3:
                $templatedata['mycourses_col'] = 4;
                break;
            case 4:
                $templatedata['mycourses_col'] = 3;
                break;
        }

        if (!$mycoursesnumblock) {
            return $templatedata;
        }

        for ($i = 1; $i <= $mycoursesnumblock; $i++) {
            $mycoursesicon = theme_degrade_get_setting_image("mycourses_icon_{$i}");
            $mycoursesurl = theme_degrade_get_setting("mycourses_url_{$i}", true);
            $mycoursestitle = theme_degrade_get_setting("mycourses_title_{$i}", true);
            $mycoursescolor = theme_degrade_get_setting("mycourses_color_{$i}", true);

            if ($mycoursesicon) {
                $templatedata["mycourses_block"][] = [
                    "mycourses_icon" => $mycoursesicon,
                    "mycourses_url" => $mycoursesurl,
                    "mycourses_title" => $mycoursestitle,
                    "mycourses_color" => $mycoursescolor,
                    "mycourses_num" => $i,
                ];
            } else {
                $templatedata["mycourses_numblocos"]--;
            }
        }

        return ["html" => $OUTPUT->render_from_template('theme_degrade/block_myoverview/block-my-links', $templatedata)];
    }

    /**
     * html_returns function
     *
     * @return \external_description
     */
    public static function html_returns() {
        return new \external_single_structure([
            'html' => new \external_value(PARAM_RAW, 'HTML'),
        ]);
    }
}

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
 * Class userprerence
 *
 * @package    theme_degrade
 * @copyright  2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\external;

defined('MOODLE_INTERNAL') || die;
require_once("{$CFG->libdir}/externallib.php");

/**
 * Class userprerence
 *
 * @package theme_degrade\external
 */
class userprerence extends \external_api {

    /**
     * layout_parameters function
     *
     * @return \external_function_parameters
     */
    public static function layout_parameters() {
        return new \external_function_parameters([
            'thememode' => new \external_value(PARAM_TEXT, 'The layout mode'),
        ]);
    }

    /**
     * layout function
     *
     * @param string $thememode
     *
     * @return array
     */
    public static function layout($thememode) {

        set_user_preference("theme_mode", $thememode);

        return ["status" => true];
    }

    /**
     * layout_returns function
     *
     * @return \external_description
     */
    public static function layout_returns() {
        return new \external_single_structure([
            'status' => new \external_value(PARAM_BOOL, 'the status'),
        ]);
    }
}

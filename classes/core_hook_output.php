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
 * Class core_hook_output
 *
 * @package    theme_degrade
 * @copyright  2024 Eduardo Kraus {@link http://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade;

/**
 * Class core_hook_output
 *
 * @package theme_degrade
 */
class core_hook_output {
    /**
     * Function before_html_attributes
     *
     * @return array
     */
    public static function before_html_attributes(\core\hook\output\before_html_attributes $hook): void {

        $darkmode = "auto";
        if (isset($_COOKIE["darkmode"])) {
            $darkmode = $_COOKIE["darkmode"];
        }

        if (!isguestuser()) {
            $darkmode = get_user_preferences("darkmode", $darkmode);
        }
        if ($layouturl = optional_param("darkmode", false, PARAM_TEXT)) {
            $darkmode = $layouturl;
        }
        $hook->add_attribute("data-bs-theme", $darkmode);
    }
}

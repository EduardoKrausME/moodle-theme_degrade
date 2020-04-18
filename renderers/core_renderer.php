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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_degrade
 * @copyright  2020 Eduardo Kraus (https://www.eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * This class has function for renderer primary menu and top course menus
 * @copyright  2020 Eduardo Kraus (https://www.eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_degrade_core_renderer extends theme_boost\output\core_renderer {
    /**
     * get Favicon URL.
     *
     * @return string
     */
    public function theme_degrade_get_favicon() {
        global $PAGE, $OUTPUT;
        if (!empty($PAGE->theme->settings->favicon)) {
            return $PAGE->theme->setting_file_url('favicon', 'favicon');
        } else {
            return $OUTPUT->image_url('favicon', 'theme');
        }
    }

    /**
     * get Footer Text.
     *
     * @return string
     */
    public function footer_text() {
        global $PAGE;

        if (!empty($PAGE->theme->settings->footnote)) {
            return $PAGE->theme->settings->footnote;
        }

        return '';
    }

    public function footer_developer() {
        global $PAGE;

        if ($PAGE->theme->settings->footdeveloper) {
            return '<span class="developer">Desenvolvido com  <span class="coracao">♥</span>︎ por Eduardo Kraus</span>';
        }

        return '';
    }
}
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
     * Function html_attributes
     *
     * @return array
     */
    public static function html_attributes() {
        global $CFG;

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
        $atributes = ["data-bs-theme" => $darkmode];

        $backgroundcolor = theme_degrade_get_setting("background_color", false);

        if ($CFG->theme == "degrade") {
            switch ($backgroundcolor) {
                case "default1":
                    $atributes["data-theme-color"] = "default1";
                    $atributes["data-background-color"] = "#f55ff2";
                    $atributes["data-background-gradient"] = "linear-gradient(45deg, #f54266, #3858f9)";
                    $atributes["data-primary-color"] = "default1";
                    break;

                case "default2":
                    $atributes["data-theme-color"] = "default2";
                    $atributes["data-background-color"] = "#fd81b5";
                    $atributes["data-background-gradient"] = "linear-gradient(45deg, #fd81b5, #c961f7, #8089ff)";
                    $atributes["data-primary-color"] = "default2";
                    break;

                case "brasil1":
                    $atributes["data-theme-color"] = "brasil1";
                    $atributes["data-background-color"] = "#00c3b0";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #ffe150, #19934a)";
                    $atributes["data-primary-color"] = "brasil1";
                    break;

                case "green1":
                    $atributes["data-theme-color"] = "green1";
                    $atributes["data-background-color"] = "#00c3b0";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #00c3b0, #339625)";
                    $atributes["data-primary-color"] = "green1";
                    break;

                case "green2":
                    $atributes["data-theme-color"] = "green2";
                    $atributes["data-background-color"] = "#30e8bf";
                    $atributes["data-background-gradient"] = "linear-gradient(-45deg, #30e8bf, #ff8235)";
                    $atributes["data-primary-color"] = "green2";
                    break;

                case "green3":
                    $atributes["data-theme-color"] = "green3";
                    $atributes["data-background-color"] = "#00bf8f";
                    $atributes["data-background-gradient"] = "linear-gradient(-45deg, #00bf8f, #001510)";
                    $atributes["data-primary-color"] = "green3";
                    break;

                case "blue1":
                    $atributes["data-theme-color"] = "blue1";
                    $atributes["data-background-color"] = "#007bc3";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #007bc3, #2eb8b7)";
                    $atributes["data-primary-color"] = "blue1";
                    break;

                case "blue2":
                    $atributes["data-theme-color"] = "blue2";
                    $atributes["data-background-color"] = "#000428";
                    $atributes["data-background-gradient"] = "linear-gradient(-45deg, #000428, #0074da)";
                    $atributes["data-primary-color"] = "blue2";
                    break;

                case "blue3":
                    $atributes["data-theme-color"] = "blue3";
                    $atributes["data-background-color"] = "#314755";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #314755, #26a0da)";
                    $atributes["data-primary-color"] = "blue3";
                    break;

                case "blue4":
                    $atributes["data-theme-color"] = "blue4";
                    $atributes["data-background-color"] = "#03001e";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #03001e, #7303c0, #ec38bc)";
                    $atributes["data-primary-color"] = "blue4";
                    break;

                case "blue5":
                    $atributes["data-theme-color"] = "blue5";
                    $atributes["data-background-color"] = "#00f0ff";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #00f0ff, #ff00f6)";
                    $atributes["data-primary-color"] = "blue5";
                    break;

                case "blue6":
                    $atributes["data-theme-color"] = "blue6";
                    $atributes["data-background-color"] = "#83a4d4";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #83a4d4, #b6fbff)";
                    $atributes["data-primary-color"] = "blue6";
                    break;

                case "red1":
                    $atributes["data-theme-color"] = "red1";
                    $atributes["data-background-color"] = "#c10f41";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #c10f41, #233b88)";
                    $atributes["data-primary-color"] = "red1";
                    break;

                case "red2":
                    $atributes["data-theme-color"] = "red2";
                    $atributes["data-background-color"] = "#1a2a6c";
                    $atributes["data-background-gradient"] = "linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d)";
                    $atributes["data-primary-color"] = "red2";
                    break;

                case "red3":
                    $atributes["data-theme-color"] = "red3";
                    $atributes["data-background-color"] = "#ceac7a";
                    $atributes["data-background-gradient"] = "linear-gradient(-45deg, #ceac7a, #ef629f)";
                    $atributes["data-primary-color"] = "red3";
                    break;

                case "red4":
                    $atributes["data-theme-color"] = "red4";
                    $atributes["data-background-color"] = "#e65c00";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #e65c00, #f9d423)";
                    $atributes["data-primary-color"] = "red4 .degrade-theme-red4";
                    break;

                case "red5":
                    $atributes["data-theme-color"] = "red5";
                    $atributes["data-background-color"] = "#d12924";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #d12924, #60090c)";
                    $atributes["data-primary-color"] = "red5";
                    break;

                case "red6":
                    $atributes["data-theme-color"] = "red6";
                    $atributes["data-background-color"] = "#ff512f";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #ff512f, #dd2476)";
                    $atributes["data-primary-color"] = "red6";
                    break;

                case "red7":
                    $atributes["data-theme-color"] = "red7";
                    $atributes["data-background-color"] = "#fc354c";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #fc354c, #0abfbc)";
                    $atributes["data-primary-color"] = "red7";
                    break;

                case "red8":
                    $atributes["data-theme-color"] = "red8";
                    $atributes["data-background-color"] = "#86377b";
                    $atributes["data-background-gradient"] = "linear-gradient(30deg, #86377b, #27273c)";
                    $atributes["data-primary-color"] = "red8";
                    break;

                case "black1":
                    $atributes["data-theme-color"] = "black1";
                    $atributes["data-background-color"] = "#070000";
                    $atributes["data-background-gradient"] = "linear-gradient(135deg, #070000, #4c0001, #070000)";
                    $atributes["data-primary-color"] = "black1";
                    break;
            }
        } else {
            $atributes["data-background-color"] = $backgroundcolor;
            $color = theme_degrade_get_setting("theme_color__color_primary", false);
            $atributes["data-primary-color"] = $color;
        }

        return $atributes;
    }

    /**
     * Function before_html_attributes
     */
    public static function before_html_attributes(\core\hook\output\before_html_attributes $hook): void {

        $atributes = self::html_attributes();

        foreach ($atributes as $id => $value) {
            $hook->add_attribute($id, $value);
        }
    }
}

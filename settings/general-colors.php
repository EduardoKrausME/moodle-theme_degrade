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
 * General file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_degrade\setting\admin_setting_configrange;

defined('MOODLE_INTERNAL') || die;

$htmlselect = "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/degrade/scss/colors.css\" />";
$htmlselect .= "\n\n" . $OUTPUT->render_from_template("theme_degrade/settings/colors-degrade", [
        "colors" => theme_degrade_settings_colors(),
        "defaultcolor" => theme_degrade_default("brandcolor", "#f55ff2", "theme_boost"),
        "angle" => theme_degrade_default("angle", 30),
        "gradient_1" => theme_degrade_default("brandcolor_gradient_1", "#f54266"),
        "gradient_2" => theme_degrade_default("brandcolor_gradient_2", "#3858f9"),
    ]);

// We use an empty default value because the default colour should come from the preset.
$setting = new admin_setting_configtext(
    "theme_boost/brandcolor",
    get_string("brandcolor", "theme_degrade"),
    get_string("brandcolor_desc", "theme_degrade"), "#f55ff2"
);
$setting->set_updatedcallback("theme_degrade_change_color");
$page->add($setting);
$PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", [$setting->get_id()]);

// Background rotation angle.
$setting = new admin_setting_configrange("theme_degrade/angle",
    get_string("brandcolor_angle", "theme_degrade"),
    get_string("brandcolor_angle_desc", "theme_degrade"),
    30, 0, 360 );
$page->add($setting);
$setting->set_updatedcallback("theme_reset_all_caches");

// Gradient color.
$setting = new admin_setting_configtext("theme_degrade/brandcolor_gradient_1",
    get_string("brandcolor_gradient_1", "theme_degrade"), "", "#f54266");
$page->add($setting);
$PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", [$setting->get_id()]);
$setting->set_updatedcallback("theme_reset_all_caches");

$setting = new admin_setting_configtext("theme_degrade/brandcolor_gradient_2",
    get_string("brandcolor_gradient_2", "theme_degrade"),
    get_string("brandcolor_gradient_2_desc", "theme_degrade") . $htmlselect, "#3858f9");
$page->add($setting);
$PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", [$setting->get_id()]);
$setting->set_updatedcallback("theme_reset_all_caches");

set_config("brandcolor_background_menu", 1, "theme_degrade");
set_config("top_scroll_background_color", "", "theme_degrade");

/**
 * List degrade colors.
 *
 * @return array
 */
function theme_degrade_settings_colors() {
    return [
        [
            "name" => "default1",
            "primary" => "#8e4eb7",
            "angle" => 45,
            "gradient_1" => "#f54266",
            "gradient_2" => "#3858f9",
        ],
        [
            "name" => "default2",
            "primary" => "#d76ae6",
            "angle" => 45,
            "gradient_1" => "#fd81b5",
            "gradient_2" => "#c961f7",
        ],
        [
            "name" => "brasil1",
            "primary" => "#00c3b0",
            "angle" => 30,
            "gradient_1" => "#ffe150",
            "gradient_2" => "#19934a",
        ],
        [
            "name" => "green1",
            "primary" => "#00c3b0",
            "angle" => 30,
            "gradient_1" => "#00c3b0",
            "gradient_2" => "#339625",
        ],
        [
            "name" => "green2",
            "primary" => "#89bd84",
            "angle" => 315,
            "gradient_1" => "#30e8bf",
            "gradient_2" => "#ff8235",
        ],
        [
            "name" => "green3",
            "primary" => "#00bf8f",
            "angle" => 315,
            "gradient_1" => "#00bf8f",
            "gradient_2" => "#001510",
        ],
        [
            "name" => "blue1",
            "primary" => "#007bc3",
            "angle" => 30,
            "gradient_1" => "#007bc3",
            "gradient_2" => "#2eb8b7",
        ],
        [
            "name" => "blue2",
            "primary" => "#000428",
            "angle" => 315,
            "gradient_1" => "#000428",
            "gradient_2" => "#0074da",
        ],
        [
            "name" => "blue3",
            "primary" => "#314755",
            "angle" => 30,
            "gradient_1" => "#314755",
            "gradient_2" => "#26a0da",
        ],
        [
            "name" => "blue4",
            "primary" => "#7303c0",
            "angle" => 30,
            "gradient_1" => "#03001e",
            "gradient_2" => "#7303c0",
        ],
        [
            "name" => "blue5",
            "primary" => "#8870f7",
            "angle" => 30,
            "gradient_1" => "#00f0ff",
            "gradient_2" => "#ff00f6",
        ],
        [
            "name" => "blue6",
            "primary" => "#83a4d4",
            "angle" => 30,
            "gradient_1" => "#83a4d4",
            "gradient_2" => "#b6fbff",
        ],
        [
            "name" => "red1",
            "primary" => "#c10f41",
            "angle" => 30,
            "gradient_1" => "#c10f41",
            "gradient_2" => "#233b88",
        ],
        [
            "name" => "red2",
            "primary" => "#b21f1f",
            "angle" => 135,
            "gradient_1" => "#1a2a6c",
            "gradient_2" => "#b21f1f",
        ],
        [
            "name" => "red3",
            "primary" => "#ef629f",
            "angle" => 315,
            "gradient_1" => "#ceac7a",
            "gradient_2" => "#ef629f",
        ],
        [
            "name" => "red4",
            "primary" => "#e65c00",
            "angle" => 30,
            "gradient_1" => "#e65c00",
            "gradient_2" => "#f9d423",
        ],
        [
            "name" => "red5",
            "primary" => "#d12924",
            "angle" => 30,
            "gradient_1" => "#d12924",
            "gradient_2" => "#60090c",
        ],
        [
            "name" => "red6",
            "primary" => "#ff512f",
            "angle" => 30,
            "gradient_1" => "#ff512f",
            "gradient_2" => "#dd2476",
        ],
        [
            "name" => "red7",
            "primary" => "#fc354c",
            "angle" => 30,
            "gradient_1" => "#fc354c",
            "gradient_2" => "#0abfbc",
        ],
        [
            "name" => "red8",
            "primary" => "#86377b",
            "angle" => 30,
            "gradient_1" => "#86377b",
            "gradient_2" => "#27273c",
        ],
        [
            "name" => "black1",
            "primary" => "#4c0001",
            "angle" => 135,
            "gradient_1" => "#070000",
            "gradient_2" => "#4c0001",
        ],
    ];
}

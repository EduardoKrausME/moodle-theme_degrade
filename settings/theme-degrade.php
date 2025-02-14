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
 * Theme Degrade file
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo Kraus https://eduardokraus.com/
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$choices = [
    "default1" => get_string("background_color_default", "theme_degrade", 1),
    "default2" => get_string("background_color_default", "theme_degrade", 2),
    "brasil1" => get_string("background_color_brasil", "theme_degrade", 2),
    "green1" => get_string("background_color_green", "theme_degrade", 1),
    "green2" => get_string("background_color_green", "theme_degrade", 2),
    "green3" => get_string("background_color_green", "theme_degrade", 3),
    "blue1" => get_string("background_color_blue", "theme_degrade", 1),
    "blue2" => get_string("background_color_blue", "theme_degrade", 2),
    "blue3" => get_string("background_color_blue", "theme_degrade", 3),
    "blue4" => get_string("background_color_blue", "theme_degrade", 4),
    "blue5" => get_string("background_color_blue", "theme_degrade", 5),
    "blue6" => get_string("background_color_blue", "theme_degrade", 6),
    "red1" => get_string("background_color_red", "theme_degrade", 1),
    "red2" => get_string("background_color_red", "theme_degrade", 2),
    "red3" => get_string("background_color_red", "theme_degrade", 3),
    "red4" => get_string("background_color_red", "theme_degrade", 4),
    "red5" => get_string("background_color_red", "theme_degrade", 5),
    "red6" => get_string("background_color_red", "theme_degrade", 6),
    "red7" => get_string("background_color_red", "theme_degrade", 7),
    "red8" => get_string("background_color_red", "theme_degrade", 8),
    "black1" => get_string("background_color_black", "theme_degrade", 1),
];
$colors = [
    "default1" => "#f55ff2",
    "default2" => "#fd81b5",
    "brasil1" => "#00c3b0",
    "green1" => "#00c3b0",
    "green2" => "#30e8bf",
    "green3" => "#00bf8f",
    "blue1" => "#007bc3",
    "blue2" => "#000428",
    "blue3" => "#314755",
    "blue4" => "#7303c0",
    "blue5" => "#00f0ff",
    "blue6" => "#83a4d4",
    "red1" => "#c10f41",
    "red2" => "#b21f1f",
    "red3" => "#ef629f",
    "red4" => "#e65c00",
    "red5" => "#d12924",
    "red6" => "#ff512f",
    "red7" => "#fc354c",
    "red8" => "#86377b",
    "black1" => "#4c0001",
];

if (isset($_SERVER["REQUEST_URI"]) && strpos($_SERVER["REQUEST_URI"], "admin/upgradesettings.php") > 0) {
    $htmlselect = "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/degrade/style/initial.css\" />";
    $htmlselect .= "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/degrade/style/style.css\" />";
} else {
    $htmlselect = "";
}
foreach ($choices as $choice => $lang) {
    $arraycolors=[];
    foreach ($colors as $name => $color ){
        $arraycolors[]=[
            "name"=>$name,
            "color"=>$color,
        ];
    }
    $htmlselect .= $OUTPUT->render_from_template("theme_degrade/settings/theme-degrade", [
        "choice" => $choice,
        "background" => $colors[$choice],
        "colors" => $arraycolors,
    ]);
}
$setting = new admin_setting_configselect("theme_degrade/background_color",
    get_string("background_color", "theme_degrade"),
    get_string("background_color_desc", "theme_degrade") . $htmlselect,
    "default1", $choices);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);


// Cores dos botões.
$colors = ["color_primary", "color_secondary", "color_buttons"];
foreach ($colors as $color) {
    $setting = new admin_setting_configtext("theme_degrade/theme_color__{$color}",
        get_string("theme_color-{$color}", "theme_degrade"),
        get_string("theme_color-{$color}_desc", "theme_degrade"), "");
    $setting->set_updatedcallback("theme_reset_all_caches");
    $page->add($setting);
    $PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", [$setting->get_id()]);
}

// Top text color.
$setting = new admin_setting_configtext("theme_degrade/background_text_color",
    get_string("background_text_color", "theme_degrade"),
    get_string("background_text_color_desc", "theme_degrade"),
    "#FFFFFF");
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

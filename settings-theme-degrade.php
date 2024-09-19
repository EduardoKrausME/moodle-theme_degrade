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
 * @package     theme_degrade
 * @copyright   2024 Eduardo Kraus https://eduardokraus.com/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$choices = [
    'default1' => get_string('background_color_default', 'theme_degrade', 1),
    'default2' => get_string('background_color_default', 'theme_degrade', 2),
    'green1' => get_string('background_color_green', 'theme_degrade', 1),
    'green2' => get_string('background_color_green', 'theme_degrade', 2),
    'green3' => get_string('background_color_green', 'theme_degrade', 3),
    'blue1' => get_string('background_color_blue', 'theme_degrade', 1),
    'blue2' => get_string('background_color_blue', 'theme_degrade', 2),
    'blue3' => get_string('background_color_blue', 'theme_degrade', 3),
    'blue4' => get_string('background_color_blue', 'theme_degrade', 4),
    'blue5' => get_string('background_color_blue', 'theme_degrade', 5),
    'blue6' => get_string('background_color_blue', 'theme_degrade', 6),
    'red1' => get_string('background_color_red', 'theme_degrade', 1),
    'red2' => get_string('background_color_red', 'theme_degrade', 2),
    'red3' => get_string('background_color_red', 'theme_degrade', 3),
    'red4' => get_string('background_color_red', 'theme_degrade', 4),
    'red5' => get_string('background_color_red', 'theme_degrade', 5),
    'red6' => get_string('background_color_red', 'theme_degrade', 6),
    'red7' => get_string('background_color_red', 'theme_degrade', 7),
    'red8' => get_string('background_color_red', 'theme_degrade', 8),
    'black1' => get_string('background_color_black', 'theme_degrade', 1),
];

if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], "admin/upgradesettings.php") > 0) {
    $htmlselect = "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/degrade/style/initial.css\" />";
    $htmlselect .= "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/degrade/style/style.css\" />";
} else {
    $htmlselect = "";
}
foreach ($choices as $choice => $lang) {
    $onclick = "$('#id_s_theme_degrade_background_color').val('{$choice}');";
    $onclick .= "$('body').attr('class',function(i,c){return c.replace(/(^|\s)degrade-theme-\S+/g,'')+' degrade-theme-{$choice}';})";
    $htmlselect .=
        "<div id=\"degrade-theme-select-{$choice}\" class=\"degrade-theme-select-{$choice} degrade-theme-select-item\" data-theme=\"{$choice}\"
                  onclick=\"{$onclick}\">
                 <div class=\"preview\"></div>
             </div>";
}
$setting = new admin_setting_configselect('theme_degrade/background_color',
    get_string('background_color', 'theme_degrade'),
    get_string('background_color_desc', 'theme_degrade') . $htmlselect,
    'default1', $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


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
 * User: Eduardo Kraus
 * Date: 02/04/2023
 * Time: 19:54
 */

defined('MOODLE_INTERNAL') || die;
global $PAGE;

$page = new admin_settingpage('theme_degrade_css', get_string('settings_theme_heading', 'theme_degrade'));


$name = 'theme_degrade/background_color';
$title = get_string('background_color', 'theme_degrade');
$description = get_string('background_color_desc', 'theme_degrade');
$default = 'default1';
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

$htmlselect = "";
foreach ($choices as $choice => $lang) {
    $onclick = "$('#id_s_theme_degrade_background_color').val('{$choice}');";
    $onclick .= "$('body').attr('class',function(i,c){return c.replace(/(^|\s)theme-\S+/g,'')+' theme-{$choice}';})";
    $htmlselect
        .= "<div id=\"theme-select-{$choice}\" class=\"theme-select-{$choice} theme-select-item\" data-theme=\"{$choice}\"
                 onclick=\"{$onclick}\">
                <div class=\"preview\"></div>
            </div>";
}
$setting = new admin_setting_configselect($name, $title, $description . $htmlselect, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


$name = 'theme_degrade/theme';
$title = get_string('theme', 'theme_degrade');
$description = get_string('theme_desc', 'theme_degrade');
$default = 'theme_blue';
$choices = [
    'theme_blue' => get_string('theme_blue', 'theme_degrade'),
    'theme_violet' => get_string('theme_violet', 'theme_degrade'),
    'theme_red_d' => get_string('theme_red_d', 'theme_degrade'),
    'theme_green' => get_string('theme_green', 'theme_degrade'),
    'theme_green_d' => get_string('theme_green_d', 'theme_degrade')
];
$colorss = [
    'theme_blue' => [
        'color_primary' => '#2b4e84',
        'color_secondary' => '#3e65a0',
        'color_buttons' => '#183054',
        'color_names' => '#c0ccdc',
        'color_titles' => '#e8f0fb'
    ],
    'theme_violet' => [
        'color_primary' => '#8e558e',
        'color_secondary' => '#a55ba5',
        'color_buttons' => '#382738',
        'color_names' => '#edd3ed',
        'color_titles' => '#feffef'
    ],
    'theme_red_d' => [
        'color_primary' => '#561209',
        'color_secondary' => '#a64437',
        'color_buttons' => '#5e1e15',
        'color_names' => '#f7e3e1',
        'color_titles' => '#fff1ef'
    ],
    'theme_green' => [
        'color_primary' => '#426e17',
        'color_secondary' => '#7abb3b',
        'color_buttons' => '#2f510f',
        'color_names' => '#bad3a3',
        'color_titles' => '#f2fde8'
    ],
    'theme_green_d' => [
        'color_primary' => '#20897b',
        'color_secondary' => '#4ba89c',
        'color_buttons' => '#103430',
        'color_names' => '#c0dcdb',
        'color_titles' => '#e4f7f6'
    ]
];
foreach ($colorss as $colorname => $colors) {

    $css = $html = "";
    foreach ($colors as $key => $cor) {
        $cor = strtoupper($cor);
        $css .= "    --{$key}: {$cor};\n";

        $styles = "display: inline-block;padding: 2px;margin: 3px;border-radius: 4px;";
        if (preg_match('/#[B-F]/', $cor)) {
            $html .= "<span style='background:{$cor};color:#515151;' style='{$styles}'
                            data-name='{$key}' data-color='{$cor}'>{$cor}</span>";
        } else {
            $html .= "<span style='background:{$cor};color:#ffffff;' style='{$styles}'
                            data-name='{$key}' data-color='{$cor}'>{$cor}</span>";
        }
    }
    $themename = get_string($colorname, 'theme_degrade');
    $styles = "display: flex;align-items: center;background: #e6e6e6;width: fit-content;border-radius: 4px;margin-bottom: 5px;";
    $description .= "<div class='seletor-de-theme' id='theme-{$colorname}' style='{$styles}'
                          data-name='{$colorname}' data-css=':root{\n{$css}}'>{$themename}: {$html}</div>";
}

$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


$PAGE->requires->js_call_amd('theme_degrade/settings', 'theme');


$name = 'theme_degrade/customcss';
$title = get_string('customcss', 'theme_degrade');
$description = get_string('customcss_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


$fontsarr = [
    'Roboto' => 'Roboto',
    'Open Sans' => 'Open Sans',
    'Lato' => 'Lato',
    'Montserrat' => 'Montserrat',
    'Poppins' => 'Poppins',
    'Nunito' => 'Nunito',
    'Inter' => 'Inter',
    'Raleway' => 'Raleway',
    'Sora' => 'Sora',
    'Epilogue' => 'Epilogue',
    'Manrope' => 'Manrope',
    'Oxygen' => 'Oxygen',
];

$name = 'theme_degrade/fontfamily';
$title = get_string('fontfamily', 'theme_degrade');
$description = get_string('fontfamily_desc', 'theme_degrade');
$setting = new admin_setting_configselect($name, $title, $description, 'Roboto', $fontsarr);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);

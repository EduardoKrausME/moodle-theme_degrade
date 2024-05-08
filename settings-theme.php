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
 * @package     theme_degrade
 * @copyright   2024 Eduardo Kraus https://eduardokraus.com/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
global $PAGE;

$page = new admin_settingpage('theme_degrade_theme',
    get_string('settings_theme_heading', 'theme_degrade'));

if ($CFG->theme != "boost_training") {
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

    if (strpos($_SERVER['REQUEST_URI'], "admin/upgradesettings.php") > 0) {
        $htmlselect = "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/degrade/style/initial.css\" />";
        $htmlselect .= "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/degrade/style/style.css\" />";
    } else {
        $htmlselect = "";
    }
    foreach ($choices as $choice => $lang) {
        $onclick = "$('#id_s_theme_degrade_background_color').val('{$choice}');";
        $onclick .= "$('body').attr('class',function(i,c){return c.replace(/(^|\s)theme-\S+/g,'')+' theme-{$choice}';})";
        $htmlselect
            .= "<div id=\"theme-select-{$choice}\" class=\"theme-select-{$choice} theme-select-item\" data-theme=\"{$choice}\"
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
}

$setting = new admin_setting_configstoredfile('theme_degrade/logo_color',
    get_string('logo_color', 'theme_degrade'),
    get_string('logo_color_desc', 'theme_degrade'),
    'logo_color', 0,
    ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Cores do topo.
if ($CFG->theme != "boost_training") {
    $setting = new admin_setting_heading("theme_degrade/top_color_heading",
        get_string('top_color_heading', 'theme_degrade'), '');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker("theme_degrade/top_scroll_background_color",
        get_string("top_scroll_background_color", 'theme_degrade'),
        get_string("top_scroll_background_color_desc", 'theme_degrade'), '#5C5D5F');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker("theme_degrade/top_scroll_text_color",
        get_string("top_scroll_text_color", 'theme_degrade'),
        get_string("top_scroll_text_color_desc", 'theme_degrade'), '#FFFFFF');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile('theme_degrade/logo_write',
        get_string('logo_write', 'theme_degrade'),
        get_string('logo_write_desc', 'theme_degrade'),
        'logo_write', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}

// Cores dos botões.
$setting = new admin_setting_heading("theme_degrade/theme_color_heading",
    get_string('theme_color_heading', 'theme_degrade'), '');
$page->add($setting);

$colorss = [
    'theme_color_blue' => [
        'color_primary' => '#2b4e84',
        'color_secondary' => '#3e65a0',
        'color_buttons' => '#183054',
        'color_names' => '#c0ccdc',
        'color_titles' => '#e8f0fb',
    ],
    'theme_color_violet' => [
        'color_primary' => '#8e558e',
        'color_secondary' => '#a55ba5',
        'color_buttons' => '#382738',
        'color_names' => '#edd3ed',
        'color_titles' => '#feffef',
    ],
    'theme_color_red_d' => [
        'color_primary' => '#561209',
        'color_secondary' => '#a64437',
        'color_buttons' => '#5e1e15',
        'color_names' => '#f7e3e1',
        'color_titles' => '#fff1ef',
    ],
    'theme_color_green' => [
        'color_primary' => '#426e17',
        'color_secondary' => '#7abb3b',
        'color_buttons' => '#2f510f',
        'color_names' => '#bad3a3',
        'color_titles' => '#f2fde8',
    ],
    'theme_color_green_d' => [
        'color_primary' => '#20897b',
        'color_secondary' => '#4ba89c',
        'color_buttons' => '#103430',
        'color_names' => '#c0dcdb',
        'color_titles' => '#e4f7f6',
    ]
];
$choices = [];
$description = get_string('theme_color_desc', 'theme_degrade');
foreach ($colorss as $colorname => $colors) {
    $html = '';
    foreach ($colors as $key => $cor) {
        $cor = strtoupper($cor);

        $styles = "display:inline-block;padding:2px;margin:3px;border-radius:4px;";
        if (preg_match('/#[B-F]/', $cor)) {
            $html .= "<span style='{$styles}background:{$cor};color:#515151;'
                            class='{$key}' data-color='{$cor}'>{$cor}</span>";
        } else {
            $html .= "<span style='{$styles}background:{$cor};color:#ffffff;'
                            class='{$key}' data-color='{$cor}'>{$cor}</span>";
        }
    }
    $themename = get_string($colorname, 'theme_degrade');
    $styles = "display:flex;align-items:center;background:#e6e6e6;width:fit-content;border-radius:4px;margin-bottom:5px;";
    $description .= "<div class='seletor-de-theme-degrade' id='theme-{$colorname}' style='{$styles}'
                          data-name='{$colorname}'>{$themename}: {$html}</div>";

    $choices[$colorname] = $themename;
}
$setting = new admin_setting_configselect('theme_degrade/theme_color',
    get_string('theme_color', 'theme_degrade'),
    $description,
    'theme_blue', $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
$PAGE->requires->js_call_amd('theme_degrade/settings', 'theme_color');

$colors = ['color_primary', 'color_secondary', 'color_buttons', 'color_names', 'color_titles'];
foreach ($colors as $color) {

    $setting = new admin_setting_configcolourpicker("theme_degrade/theme_color__{$color}",
        get_string("theme_color-{$color}", 'theme_degrade'),
        get_string("theme_color-{$color}_desc", 'theme_degrade'), '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}

// Favicon.
$setting = new admin_setting_heading("theme_degrade/favicon_heading",
    get_string('favicon', 'admin'), '');
$page->add($setting);

$setting = new admin_setting_configstoredfile('core_admin/favicon',
    get_string('favicon', 'theme_degrade'),
    get_string('favicon_desc', 'theme_degrade'),
    'favicon', 0,
    ['maxfiles' => 1, 'accepted_types' => ['image']]);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);

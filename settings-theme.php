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
 * Theme Settings File
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo Kraus https://eduardokraus.com/
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
global $PAGE;

$page = new admin_settingpage('theme_degrade_theme',
    get_string('settings_theme_heading', 'theme_degrade'));

if (file_exists(__DIR__ . "/settings-theme-degrade.php")) {
    require_once(__DIR__ . "/settings-theme-degrade.php");
}

$setting = new admin_setting_configstoredfile('theme_degrade/logo_color',
    get_string('logo_color', 'theme_degrade'),
    get_string('logo_color_desc', 'theme_degrade'),
    'logo_color', 0,
    ['maxfiles' => 1, 'accepted_types' => [".jpg", ".jpeg", ".svg", ".png"]]);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Cores do topo.
if ($CFG->theme != "boost_training") {

    $setting = new admin_setting_configcheckbox('theme_degrade/top_scroll',
        get_string('top_scroll', 'theme_degrade'),
        get_string('top_scroll_desc', 'theme_degrade'),
        0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $PAGE->requires->js_call_amd('theme_degrade/settings', 'top_scroll');

    $setting = new admin_setting_heading("theme_degrade/top_color_heading",
        get_string('top_color_heading', 'theme_degrade'), '');
    $page->add($setting);

    $setting = new admin_setting_configtext("theme_degrade/top_scroll_background_color",
        get_string("top_scroll_background_color", 'theme_degrade'),
        get_string("top_scroll_background_color_desc", 'theme_degrade'), '#5C5D5F');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $PAGE->requires->js_call_amd('theme_degrade/settings', 'minicolors', [$setting->get_id()]);

    $setting = new admin_setting_configtext("theme_degrade/top_scroll_text_color",
        get_string("top_scroll_text_color", 'theme_degrade'),
        get_string("top_scroll_text_color_desc", 'theme_degrade'), '#FFFFFF');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $PAGE->requires->js_call_amd('theme_degrade/settings', 'minicolors', [$setting->get_id()]);

    $setting = new admin_setting_configstoredfile('theme_degrade/logo_write',
        get_string('logo_write', 'theme_degrade'),
        get_string('logo_write_desc', 'theme_degrade'),
        'logo_write', 0,
        ['maxfiles' => 1, 'accepted_types' => [".jpg", ".jpeg", ".svg", ".png"]]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}

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
    ],
];
$description = "<div class='row'>";
$description .= "<h5 class='col-sm-3'>" . get_string('theme_color_sugestion', 'theme_degrade') . "</h5>";
$description .= "<div class='col-sm-9'>";
$description .= get_string('theme_color_sugestion_text', 'theme_degrade');
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
    $description .= "<div class='degrade-seletor-de-theme' id='theme-{$colorname}' style='{$styles}'
                          data-name='{$colorname}'>{$themename}: {$html}</div>";
}

// Cores dos botões.
$setting = new admin_setting_heading("theme_degrade/theme_color_heading",
    get_string('theme_color_heading', 'theme_degrade'),
    $description . "</div></div>");
$page->add($setting);
$PAGE->requires->js_call_amd('theme_degrade/settings', 'theme_color');

$colors = ['color_primary', 'color_secondary', 'color_buttons', 'color_names', 'color_titles'];
foreach ($colors as $color) {

    $setting = new admin_setting_configtext("theme_degrade/theme_color__{$color}",
        get_string("theme_color-{$color}", 'theme_degrade'),
        get_string("theme_color-{$color}_desc", 'theme_degrade'), '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $PAGE->requires->js_call_amd('theme_degrade/settings', 'minicolors', [$setting->get_id()]);
}

// Favicon.
$setting = new admin_setting_heading("theme_degrade/favicon_heading",
    get_string('favicon', 'admin'), '');
$page->add($setting);

$setting = new admin_setting_configstoredfile('core_admin/favicon',
    get_string('favicon', 'theme_degrade'),
    get_string('favicon_desc', 'theme_degrade'),
    'favicon', 0,
    ['maxfiles' => 1, 'accepted_types' => ['.png', '.ico']]);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);

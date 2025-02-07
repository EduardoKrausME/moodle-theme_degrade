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
 * MyCourses Settings File
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo Kraus https://eduardokraus.com/
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage('theme_degrade_mycourses', get_string('settings_mycourses_heading', 'theme_degrade'));

$colors = ["#2441e7", "#FF1053", "#00A78E", "#ECD06F"];

// Number of slides.
$choices = [
    0 => get_string("mycourses_numblocos_nenhum", 'theme_degrade'),
    1 => '1',
    2 => '2',
    3 => '3',
    4 => '4',
];
$settingmycoursesnumblocos = new admin_setting_configselect('theme_degrade/mycourses_numblocos',
    get_string('mycourses_numblocos', 'theme_degrade'),
    get_string('mycourses_numblocos_desc', 'theme_degrade'), 0, $choices);
$page->add($settingmycoursesnumblocos);

$mycoursesnumblocos = get_config('theme_degrade', 'mycourses_numblocos');
for ($i = 1; $i <= $mycoursesnumblocos; $i++) {
    $heading = get_string('mycourses_info', 'theme_degrade', $i);
    $setting = new admin_setting_heading("theme_degrade/mycourses_info_{$i}",
        "<span id='admin-mycourses_info_{$i}'>{$heading}</span>", '');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile("theme_degrade/mycourses_icon_{$i}",
        get_string('mycourses_icon', 'theme_degrade'),
        get_string('mycourses_icon_desc', 'theme_degrade'),
        "mycourses_icon_{$i}", 0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.svg']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configtext("theme_degrade/mycourses_title_{$i}",
        get_string('mycourses_title', 'theme_degrade'),
        get_string('mycourses_title_desc', 'theme_degrade'),
        "", PARAM_TEXT);
    $page->add($setting);

    $setting = new admin_setting_configtext("theme_degrade/mycourses_url_{$i}",
        get_string('mycourses_url', 'theme_degrade'),
        get_string('mycourses_url_desc', 'theme_degrade'),
        $CFG->wwwroot, PARAM_URL);
    $page->add($setting);

    $default = $colors[$mycoursesnumblocos - 1];
    $setting = new admin_setting_configtext("theme_degrade/mycourses_color_{$i}",
        get_string("mycourses_color", 'theme_degrade'),
        get_string("mycourses_color_desc", 'theme_degrade'), $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $PAGE->requires->js_call_amd('theme_degrade/settings', 'minicolors', [$setting->get_id()]);
}

$settings->add($page);

global $PAGE;
$PAGE->requires->js_call_amd('theme_degrade/settings', 'autosubmit', [$settingmycoursesnumblocos->get_id()]);

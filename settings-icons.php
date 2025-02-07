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
 * Icons Settings File
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo Kraus https://eduardokraus.com/
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage('theme_degrade_icons', get_string('settings_icons_heading', 'theme_degrade'));

// Number of icons.
$choices = [0 => get_string("settings_icons_none", 'theme_degrade')];
for ($i = 1; $i < 20; $i++) {
    $choices[$i] = $i;
}
$setting = new admin_setting_configselect('theme_degrade/settings_icons_num',
    get_string('settings_icons_num', 'theme_degrade'),
    get_string('settings_icons_num_desc', 'theme_degrade'), 0, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

for ($i = 1; $i <= 20; $i++) {
    $heading = get_string('settings_icons_block', 'theme_degrade', $i);
    $setting = new admin_setting_heading("theme_degrade/settings_icons_block_{$i}",
        "<span id='admin-settings_icons_block_{$i}'>{$heading}</span>", '');
    $page->add($setting);

    $setting = new admin_setting_configtext("theme_degrade/settings_icons_name_{$i}",
        get_string('settings_icons_name', 'theme_degrade'),
        get_string('settings_icons_name_desc', 'theme_degrade'), '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile("theme_degrade/settings_icons_image_{$i}",
        get_string('settings_icons_image', 'theme_degrade'),
        get_string('settings_icons_image_desc', 'theme_degrade'),
        "settings_icons_image_{$i}", 0,
        ['maxfiles' => 1, 'accepted_types' => ['.svg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}

$settings->add($page);
global $PAGE;
$PAGE->requires->js_call_amd('theme_degrade/settings', 'icons');

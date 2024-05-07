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

$page = new admin_settingpage('theme_degrade_about', get_string('settings_about_heading', 'theme_degrade'));

$setting = new admin_setting_configcheckbox('theme_degrade/frontpage_about_enable',
    get_string('frontpage_about_enable', 'theme_degrade'),
    get_string('frontpage_about_enable_desc', 'theme_degrade'),
    1);
$page->add($setting);

$setting = new admin_setting_configstoredfile("theme_degrade/frontpage_about_logo",
    get_string('frontpage_about_logo', 'theme_degrade'),
    get_string('frontpage_about_logo_desc', 'theme_degrade'),
    "frontpage_about_logo");
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$setting = new admin_setting_configtext('theme_degrade/frontpage_about_title',
    get_string('frontpage_about_title', 'theme_degrade'), '', '');
$page->add($setting);

$setting = new admin_setting_confightmleditor('theme_degrade/frontpage_about_description',
    get_string('frontpage_about_description', 'theme_degrade'),
    get_string('frontpage_about_description_desc', 'theme_degrade'), '');
$page->add($setting);

for ($i = 1; $i <= 4; $i++) {
    $setting = new admin_setting_heading("theme_degrade/frontpage_about_info_{$i}",
        get_string('frontpage_about_info', 'theme_degrade', $i), '');
    $page->add($setting);

    $setting = new admin_setting_configtext("theme_degrade/frontpage_about_text_{$i}",
        get_string('frontpage_about_text', 'theme_degrade'),
        get_string('frontpage_about_text_desc', 'theme_degrade'), '', PARAM_TEXT);
    $page->add($setting);

    $setting = new admin_setting_configtext("theme_degrade/frontpage_about_number_{$i}",
        get_string('frontpage_about_number', 'theme_degrade'),
        get_string('frontpage_about_number_desc', 'theme_degrade'), '', PARAM_INT);
    $page->add($setting);
}

$settings->add($page);

global $PAGE;
$PAGE->requires->js_call_amd('theme_degrade/settings', 'about');

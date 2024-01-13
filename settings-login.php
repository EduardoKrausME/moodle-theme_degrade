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
 * Time: 19:16
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage('theme_degrade_login', get_string('settings_login_heading', 'theme_degrade'));


$name = 'theme_degrade/login_theme';
$title = get_string('login_theme', 'theme_degrade');
$description = get_string('login_theme_desc', 'theme_degrade');
$default = 'default1';
$choices = [
    'login_theme_image_login' => get_string('login_theme_image_login', 'theme_degrade'),
    'login_theme_imagetext_login' => get_string('login_theme_imagetext_login', 'theme_degrade'),
    'login_theme_login' => get_string('login_theme_login', 'theme_degrade'),
    'theme_login_branco' => get_string('theme_login_branco', 'theme_degrade'),
];

$htmlselect = "";
foreach ($choices as $choice => $lang) {
}
$setting = new admin_setting_configselect($name, $title, $description . $htmlselect, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


$name = "theme_degrade/login_backgroundfoto";
$title = get_string('login_backgroundfoto', 'theme_degrade');
$link = '<a href="https://www.freepik.com/free-photo/teacher-talking-with-her-students-online_11332964.htm" 
            target="_blank">Teacher talking with her students online</a>';
$description = get_string('login_backgroundfoto_desc', 'theme_degrade', $link);
$setting = new admin_setting_configstoredfile($name, $title, $description, "login_backgroundfoto");
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_degrade/login_login_description';
$title = get_string('login_login_description', 'theme_degrade');
$description = get_string('login_login_description_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/login_forgot_description';
$title = get_string('login_forgot_description', 'theme_degrade');
$description = get_string('login_forgot_description_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/login_signup_description';
$title = get_string('login_signup_description', 'theme_degrade');
$description = get_string('login_signup_description_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

global $PAGE;
$PAGE->requires->js_call_amd('theme_degrade/settings', 'login');


$settings->add($page);

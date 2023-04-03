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
 * Time: 20:20
 */

defined('MOODLE_INTERNAL') || die;

$temp = new admin_settingpage('theme_degrade_frontpage_home',
    get_string('theme_degrade_frontpage_home', 'theme_degrade'));

$name = "theme_degrade/theme_degrade_frontpage_bloco";
$heading = get_string('theme_degrade_frontpage_bloco', 'theme_degrade', get_string('availablecourses'));
$setting = new admin_setting_heading($name, $heading, "");
$temp->add($setting);

$name = 'theme_degrade/frontpage_avaliablecourses_text';
$title = get_string('footer_frontpage_blockcourses_text', 'theme_degrade', get_string('availablecourses'));
$description = get_string('footer_frontpage_blockcourses_text_desc', 'theme_degrade', get_string('availablecourses'));
$default = "";
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$temp->add($setting);

$name = 'theme_degrade/frontpage_avaliablecourses_instructor';
$title = get_string('footer_frontpage_blockcourses_instructor', 'theme_degrade');
$description = get_string('footer_frontpage_blockcourses_instructor_desc', 'theme_degrade');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$temp->add($setting);


$name = "theme_degrade/theme_degrade_frontpage_bloco";
$heading = get_string('theme_degrade_frontpage_bloco', 'theme_degrade', get_string('mycourses'));
$setting = new admin_setting_heading($name, $heading, "");
$temp->add($setting);

$name = 'theme_degrade/frontpage_mycourses_text';
$title = get_string('footer_frontpage_blockcourses_text', 'theme_degrade', get_string('mycourses'));
$description = get_string('footer_frontpage_blockcourses_text_desc', 'theme_degrade', get_string('mycourses'));
$default = "";
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$temp->add($setting);

$name = 'theme_degrade/frontpage_mycourses_instructor';
$title = get_string('footer_frontpage_blockcourses_instructor', 'theme_degrade');
$description = get_string('footer_frontpage_blockcourses_instructor_desc', 'theme_degrade');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$temp->add($setting);


$settings->add($temp);

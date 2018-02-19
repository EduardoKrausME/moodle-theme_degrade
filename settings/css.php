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
 * Date: 30/01/2018
 * Time: 09:10
 */

defined('MOODLE_INTERNAL') || die();


$pagesettings = new admin_settingpage('theme_degrade_css', get_string('cssheading', 'theme_degrade'));
$pagesettings->add(new admin_setting_heading('theme_degrade_css',
    get_string('cssheading_desc', 'theme_degrade'), ''));


// Custom CSS file.
$name = 'theme_degrade/customcss';
$title = get_string('customcss', 'theme_degrade');
$description = get_string('customcss_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$pagesettings->add($setting);


$ADMIN->add('theme_degrade', $pagesettings);
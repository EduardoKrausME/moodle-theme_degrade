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

$page = new admin_settingpage('theme_degrade_logos', get_string('settings_logos_heading', 'theme_degrade'));

$setting = new admin_setting_configstoredfile('theme_degrade/logo_color',
    get_string('logo_color', 'theme_degrade'),
    get_string('logo_color_desc', 'theme_degrade'),
    'logo_color', 0,
    ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$setting = new admin_setting_configstoredfile('theme_degrade/logo_write',
    get_string('logo_write', 'theme_degrade'),
    get_string('logo_write_desc', 'theme_degrade'),
    'logo_write', 0,
    ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$setting = new admin_setting_configstoredfile('core_admin/favicon',
    get_string('favicon', 'theme_degrade'),
    get_string('favicon_desc', 'theme_degrade'),
    'favicon', 0,
    ['maxfiles' => 1, 'accepted_types' => ['image']]);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);

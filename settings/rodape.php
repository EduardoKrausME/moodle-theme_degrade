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
 * The Setting rodapé layout..
 *
 * @package    theme_degrade
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$pagesettings = new admin_settingpage('theme_degrade_rodape', get_string('footerheading', 'theme_degrade'));
$pagesettings->add(new admin_setting_heading('theme_degrade_rodape',
    get_string('footerheading_desc', 'theme_degrade'), ''));

$name = 'theme_degrade/footerheading';
$heading = get_string('footerheading', 'theme_degrade');
$information = get_string('footerheading_desc', 'theme_degrade');
$setting = new admin_setting_heading($name, $heading, $information);
$pagesettings->add($setting);

// Footnote setting.
$name = 'theme_degrade/footnote';
$title = get_string('footnote', 'theme_degrade');
$description = get_string('footnote_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$pagesettings->add($setting);

// Background fixed setting.
$name = 'theme_degrade/footdeveloper';
$title = get_string('footdeveloper', 'theme_degrade');
$description = get_string('footdeveloper_desc', 'theme_degrade');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$pagesettings->add($setting);

$ADMIN->add('theme_degrade', $pagesettings);
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
 * The Setting Redes Sociais layout.
 *
 * @package    theme_degrade
 * @copyright  2020 Eduardo Kraus (https://www.eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


$pagesettings = new admin_settingpage('theme_degrade_social', get_string('socialiconsheading', 'theme_degrade'));

// Website url setting.
$name = 'theme_degrade/website';
$title = get_string('website', 'theme_degrade');
$description = get_string('website_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// Facebook url setting.
$name = 'theme_degrade/facebook';
$title = get_string('facebook', 'theme_degrade');
$description = get_string('facebook_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// Twitter url setting.
$name = 'theme_degrade/twitter';
$title = get_string('twitter', 'theme_degrade');
$description = get_string('twitter_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// Google+ url setting.
$name = 'theme_degrade/googleplus';
$title = get_string('googleplus', 'theme_degrade');
$description = get_string('googleplus_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// Flickr url setting.
$name = 'theme_degrade/flickr';
$title = get_string('flickr', 'theme_degrade');
$description = get_string('flickr_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// Pinterest url setting.
$name = 'theme_degrade/pinterest';
$title = get_string('pinterest', 'theme_degrade');
$description = get_string('pinterest_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// Instagram url setting.
$name = 'theme_degrade/instagram';
$title = get_string('instagram', 'theme_degrade');
$description = get_string('instagram_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// LinkedIn url setting.
$name = 'theme_degrade/linkedin';
$title = get_string('linkedin', 'theme_degrade');
$description = get_string('linkedin_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// YouTube url setting.
$name = 'theme_degrade/youtube';
$title = get_string('youtube', 'theme_degrade');
$description = get_string('youtube_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// Apple url setting.
$name = 'theme_degrade/apple';
$title = get_string('apple', 'theme_degrade');
$description = get_string('apple_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);

// Android url setting.
$name = 'theme_degrade/android';
$title = get_string('android', 'theme_degrade');
$description = get_string('android_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$pagesettings->add($setting);


$settings->add($pagesettings);
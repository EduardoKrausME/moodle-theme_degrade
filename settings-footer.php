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

$page = new admin_settingpage('theme_degrade_footer', get_string('settings_footer_heading', 'theme_degrade'));

$name = 'theme_degrade_footerblock_description';
$heading = get_string('footerblock_description', 'theme_degrade');
$information = '';
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_degrade/footer_description';
$title = get_string('footer_description', 'theme_degrade');
$description = get_string('footer_description_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade_footerblock_links';
$heading = get_string('footerblock_links', 'theme_degrade');
$information = '';
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_degrade/footer_links_title';
$title = get_string('footer_links_title', 'theme_degrade');
$description = '';
$default = get_string("footer_links_title_default", "theme_degrade");
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/footer_links';
$title = get_string('footerblink', 'theme_degrade') . ' 2';
$description = get_string('footerblink_desc', 'theme_degrade');
$default = "";
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade_footerblock_social';
$heading = get_string('footerblock_social', 'theme_degrade');
$information = '';
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_degrade/footer_social_title';
$title = get_string('footer_social_title', 'theme_degrade');
$description = get_string('footer_social_title_desc', 'theme_degrade');
$default = get_string("footer_social_title_default", "theme_degrade");
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/social_facebook';
$title = get_string('social_facebook', 'theme_degrade');
$description = get_string('social_facebook_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/social_youtube';
$title = get_string('social_youtube', 'theme_degrade');
$description = get_string('social_youtube_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/social_linkedin';
$title = get_string('social_linkedin', 'theme_degrade');
$description = get_string('social_linkedin_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/social_twitter';
$title = get_string('social_twitter', 'theme_degrade');
$description = get_string('social_twitter_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/social_instagram';
$title = get_string('social_instagram', 'theme_degrade');
$description = get_string('social_instagram_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);


$name = 'theme_degrade_footerblock_contact';
$heading = get_string('footerblock_contact', 'theme_degrade') . ' 4 ';
$information = '';
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_degrade/contact_footer_title';
$title = get_string('footer_contact_title', 'theme_degrade');
$description = get_string('footer_contact_title_desc', 'theme_degrade');
$default = get_string("footer_contact_title_default", "theme_degrade");
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/contact_address';
$title = get_string('contact_address', 'theme_degrade');
$description = '';
$default = "";
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/contact_phone';
$title = get_string('contact_phone', 'theme_degrade');
$description = '';
$default = "";
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = 'theme_degrade/contact_email';
$title = get_string('contact_email', 'theme_degrade');
$description = '';
$default = "";
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);


$name = 'theme_degrade_footerblock_copywriter';
$heading = get_string('footerblock_copywriter', 'theme_degrade');
$information = '';
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_degrade/footer_show_copywriter';
$title = get_string('footer_show_copywriter', 'theme_degrade');
$description = get_string('footer_show_copywriter_desc', 'theme_degrade');
$default = 1;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);

$settings->add($page);

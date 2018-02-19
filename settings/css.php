<?php
/**
 * User: Eduardo Kraus
 * Date: 30/01/2018
 * Time: 09:10
 */

defined('MOODLE_INTERNAL') || die();


$page_settings = new admin_settingpage('theme_degrade_css', get_string('cssheading', 'theme_degrade'));
$page_settings->add(new admin_setting_heading('theme_degrade_css',
    get_string('cssheading_desc', 'theme_degrade'), ''));


// Custom CSS file.
$name = 'theme_degrade/customcss';
$title = get_string('customcss', 'theme_degrade');
$description = get_string('customcss_desc', 'theme_degrade');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page_settings->add($setting);


$ADMIN->add('theme_degrade', $page_settings);
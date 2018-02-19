<?php
/**
 * User: Eduardo Kraus
 * Date: 30/01/2018
 * Time: 09:10
 */

defined ( 'MOODLE_INTERNAL' ) || die();


$page_settings = new admin_settingpage( 'theme_degrade_social', get_string ( 'socialiconsheading', 'theme_degrade' ) );
$page_settings->add ( new admin_setting_heading( 'theme_degrade_social', get_string ( 'socialiconsheading_desc', 'theme_degrade' ), '' ) );

/*****************
 * Redes Sociais
 *****************/
// Website url setting
$name        = 'theme_degrade/website';
$title       = get_string ( 'website', 'theme_degrade' );
$description = get_string ( 'website_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// Facebook url setting
$name        = 'theme_degrade/facebook';
$title       = get_string ( 'facebook', 'theme_degrade' );
$description = get_string ( 'facebook_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// Twitter url setting
$name        = 'theme_degrade/twitter';
$title       = get_string ( 'twitter', 'theme_degrade' );
$description = get_string ( 'twitter_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// Google+ url setting
$name        = 'theme_degrade/googleplus';
$title       = get_string ( 'googleplus', 'theme_degrade' );
$description = get_string ( 'googleplus_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// Flickr url setting
$name        = 'theme_degrade/flickr';
$title       = get_string ( 'flickr', 'theme_degrade' );
$description = get_string ( 'flickr_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// Pinterest url setting
$name        = 'theme_degrade/pinterest';
$title       = get_string ( 'pinterest', 'theme_degrade' );
$description = get_string ( 'pinterest_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// Instagram url setting
$name        = 'theme_degrade/instagram';
$title       = get_string ( 'instagram', 'theme_degrade' );
$description = get_string ( 'instagram_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// LinkedIn url setting
$name        = 'theme_degrade/linkedin';
$title       = get_string ( 'linkedin', 'theme_degrade' );
$description = get_string ( 'linkedin_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// YouTube url setting
$name        = 'theme_degrade/youtube';
$title       = get_string ( 'youtube', 'theme_degrade' );
$description = get_string ( 'youtube_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// Apple url setting
$name        = 'theme_degrade/apple';
$title       = get_string ( 'apple', 'theme_degrade' );
$description = get_string ( 'apple_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );

// Android url setting
$name        = 'theme_degrade/android';
$title       = get_string ( 'android', 'theme_degrade' );
$description = get_string ( 'android_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_configtext( $name, $title, $description, $default );
$page_settings->add ( $setting );


$ADMIN->add ( 'theme_degrade', $page_settings );
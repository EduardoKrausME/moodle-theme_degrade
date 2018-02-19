<?php
/**
 * User: Eduardo Kraus
 * Date: 30/01/2018
 * Time: 09:11
 */

defined ( 'MOODLE_INTERNAL' ) || die();

$page_settings = new admin_settingpage( 'theme_degrade_rodape', get_string ( 'footerheading', 'theme_degrade' ) );
$page_settings->add ( new admin_setting_heading( 'theme_degrade_rodape',
    get_string ( 'footerheading_desc', 'theme_degrade' ), '' ) );

/*****************
 * Rodapé
 *****************/
$name        = 'theme_degrade/footerheading';
$heading     = get_string ( 'footerheading', 'theme_degrade' );
$information = get_string ( 'footerheading_desc', 'theme_degrade' );
$setting     = new admin_setting_heading( $name, $heading, $information );
$page_settings->add ( $setting );

// Footnote setting.
$name        = 'theme_degrade/footnote';
$title       = get_string ( 'footnote', 'theme_degrade' );
$description = get_string ( 'footnote_desc', 'theme_degrade' );
$default     = '';
$setting     = new admin_setting_confightmleditor( $name, $title, $description, $default );
$setting->set_updatedcallback ( 'theme_reset_all_caches' );
$page_settings->add ( $setting );

// Background fixed setting.
$name        = 'theme_degrade/footdeveloper';
$title       = get_string ( 'footdeveloper', 'theme_degrade' );
$description = get_string ( 'footdeveloper_desc', 'theme_degrade' );
$setting     = new admin_setting_configcheckbox( $name, $title, $description, 1 );
$setting->set_updatedcallback ( 'theme_reset_all_caches' );
$page_settings->add ( $setting );

$ADMIN->add ( 'theme_degrade', $page_settings );
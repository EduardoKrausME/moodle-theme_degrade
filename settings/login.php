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
 * Login file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\context\course as context_course;

defined('MOODLE_INTERNAL') || die;

global $CFG, $OUTPUT, $PAGE, $SITE;
require_once("{$CFG->dirroot}/theme/degrade/lib.php");

$page = new admin_settingpage("theme_degrade_login",
    get_string("loginsettings", "theme_degrade"));

$options = [
    "aurora" => get_string("logintheme_aurora", "theme_degrade"),
    "dark-elegante" => get_string("logintheme_dark-elegante", "theme_degrade"),
    "selva-canopy" => get_string("logintheme_selva-canopy", "theme_degrade"),
    "clean-minimal" => get_string("logintheme_clean-minimal", "theme_degrade"),
    "clean-outline" => get_string("logintheme_clean-outline", "theme_degrade"),
    "glassmorphism" => get_string("logintheme_glassmorphism", "theme_degrade"),
    "serenity-med-blue" => get_string("logintheme_serenity-med-blue", "theme_degrade"),
    "serenity-med-red" => get_string("logintheme_serenity-med-red", "theme_degrade"),
];
$mustachecontext = (object)[];
$url = $OUTPUT->get_logo_url();
$mustachecontext->logourl = $url ? $url->out(false) : null;
$mustachecontext->sitename = format_string($SITE->fullname);
$mustachecontext->themes=[];
foreach ($options as $loginthemename => $option){
    $mustachecontext->themes[] = [
        "login_theme" => $loginthemename,
        "loginbackgroundimageurl" => $OUTPUT->image_url("login/{$loginthemename}", "theme_degrade")->out(false),
    ];
}
$htmldescextra = $OUTPUT->render_from_template("theme_degrade/settings/login", $mustachecontext);
$htmldescextra = preg_replace('/\s+/', ' ', $htmldescextra);

$setting = new admin_setting_configselect("theme_degrade/logintheme",
    get_string("logintheme", "theme_degrade"),
    get_string("logintheme_desc", "theme_degrade") . $htmldescextra,
    "aurora", $options);
$page->add($setting);

// Login Background image setting.
$setting = new admin_setting_configstoredfile("theme_degrade/loginbackgroundimage",
    get_string("loginbackgroundimage", "theme_degrade"),
    get_string("loginbackgroundimage_desc", "theme_degrade"), "loginbackgroundimage");
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

// Small logo file setting.
$setting = new admin_setting_configstoredfile("theme_degrade/loginlogo",
    get_string("loginlogo", "theme_degrade"),
    get_string("loginlogo_desc", "theme_degrade"),
    "loginlogo", 0,
    ["maxfiles" => 1, "accepted_types" => [".jpg", ".jpeg", ".svg", ".png"]]);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$settings->add($page);

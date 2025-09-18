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
 * A login page layout for the boost theme.
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @copyright based on work by 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\language_menu;

defined('MOODLE_INTERNAL') || die();

$bodyattributes = $OUTPUT->body_attributes();

$loginthemename = theme_degrade_default( "logintheme", "dark-elegante");
$loginbackgroundimageurl = theme_degrade_setting_file_url("loginbackgroundimage");
if (!$loginbackgroundimageurl) {
    $loginbackgroundimageurl = $OUTPUT->image_url("login/{$loginthemename}", "theme_degrade")->out(false);
}

$templatecontext = [
    "sitename" => format_string($SITE->shortname, true, ["context" => context_course::instance(SITEID), "escape" => false]),
    "output" => $OUTPUT,
    "bodyattributes" => $bodyattributes,
    "login_theme" => $loginthemename,
    "footer_show_copywriter" => get_config("theme_boost_magnific", "footer_show_copywriter"),
    "languagemenu" => (new language_menu($PAGE))->export_for_action_menu($OUTPUT),
    "loginbackgroundimageurl" => $loginbackgroundimageurl,
];

echo $OUTPUT->render_from_template("theme_degrade/login", $templatecontext);


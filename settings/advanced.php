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
 * Advanced file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_degrade\admin\setting_scss;

defined('MOODLE_INTERNAL') || die;

// Advanced settings.
$page = new admin_settingpage("theme_degrade_advanced", get_string("advancedsettings", "theme_degrade"));

// Raw SCSS to include before the content.
$setting = new setting_scss(
    "theme_degrade/scsspre",
    get_string("rawscsspre", "theme_boost"),
    get_string("rawscsspre_desc", "theme_boost"),
    "", PARAM_RAW
);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

// Raw SCSS to include after the content.
$setting = new setting_scss(
    "theme_degrade/scsspos", get_string("rawscss", "theme_boost"),
    get_string("rawscss_desc", "theme_boost"),
    "", PARAM_RAW
);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$settings->add($page);

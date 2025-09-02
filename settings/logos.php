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
 * Logos file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Logos section.
$page = new admin_settingpage("theme_degrade_logos",
    get_string("logossettings", "admin"));

$url = "{$CFG->wwwroot}/theme/degrade/quickstart/#logos";
$setting = new admin_setting_heading("theme_degrade_quickstart_logos", "",
    get_string("quickstart_settings_link", "theme_degrade", $url));
$page->add($setting);

// Small logo file setting.
$title = get_string("logocompact", "admin");
$description = get_string("logocompact_desc", "admin");
$setting = new admin_setting_configstoredfile("core_admin/logocompact", $title, $description, "logocompact", 0,
    ["maxfiles" => 1, "accepted_types" => [".jpg", ".png", ".svg"]]);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

// Favicon file setting.
$title = get_string("favicon", "admin");
$description = get_string("favicon_desc", "admin");
$setting = new admin_setting_configstoredfile("core_admin/favicon", $title, $description, "favicon", 0,
    ["maxfiles" => 1, "accepted_types" => ["image"]]);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$settings->add($page);

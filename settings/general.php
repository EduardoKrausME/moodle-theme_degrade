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
 * General file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG, $OUTPUT, $PAGE;
require_once("{$CFG->dirroot}/theme/degrade/lib.php");

$page = new admin_settingpage("theme_degrade_general",
    get_string("generalsettings", "theme_degrade"));

$url = "{$CFG->wwwroot}/theme/degrade/quickstart/#brandcolor";
$setting = new admin_setting_heading("theme_degrade_quickstart_brandcolor", "",
    get_string("quickstart_settings_link", "theme_degrade", $url));
$page->add($setting);

$htmlselect = "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/degrade/scss/colors.css\" />";
$config = get_config("theme_degrade");
if (!isset($config->startcolor[2])) {
    $htmlselect .= "\n\n" . $OUTPUT->render_from_template("theme_degrade/settings/colors", [
            "startcolor" => true,
            "colors" => theme_degrade_colors(),
            "defaultcolor" => theme_degrade_default_color("startcolor", "#1a2a6c"),
        ]);

    $setting = new admin_setting_configtext("theme_degrade/startcolor",
        get_string("brandcolor", "theme_boost"),
        get_string("brandcolor_desc", "theme_degrade") . $htmlselect,
        "#1a2a6c");
    $PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", [$setting->get_id()]);
    $setting->set_updatedcallback("theme_degrade_change_color");
    $page->add($setting);
} else {
    $htmlselect .= "\n\n" . $OUTPUT->render_from_template("theme_degrade/settings/colors", [
            "brandcolor" => true,
            "colors" => theme_degrade_colors(),
            "defaultcolor" => theme_degrade_default_color("brandcolor", "#1a2a6c", "theme_boost"),
        ]);

    // We use an empty default value because the default colour should come from the preset.
    $setting = new admin_setting_configtext("theme_boost/brandcolor",
        get_string("brandcolor", "theme_degrade"),
        get_string("brandcolor_desc", "theme_degrade") . $htmlselect,
        "#1a2a6c");
    $setting->set_updatedcallback("theme_degrade_change_color");
    $page->add($setting);
    $PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", [$setting->get_id()]);
}

$page->add(new admin_setting_configcheckbox("theme_degrade/brandcolor_background_menu",
    get_string("brandcolor_background_menu", "theme_degrade"),
    get_string("brandcolor_background_menu_desc", "theme_degrade"), 0));

// Cores do topo.
$setting = new admin_setting_heading("theme_degrade/top_color_heading",
    get_string("top_color_heading", "theme_degrade"), "");
$page->add($setting);
$PAGE->requires->js_call_amd("theme_degrade/settings", "form_hide");

$setting = new admin_setting_configcheckbox("theme_degrade/top_scroll_fix",
    get_string("top_scroll_fix", "theme_degrade"),
    get_string("top_scroll_fix_desc", "theme_degrade"),
    0);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$setting = new admin_setting_configtext("theme_degrade/top_scroll_background_color",
    get_string("top_scroll_background_color", "theme_degrade"),
    get_string("top_scroll_background_color_desc", "theme_degrade"), "#5C5D5F");
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);
$PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", [$setting->get_id()]);

// Images.
$setting = new admin_setting_heading("theme_degrade/favicon_heading",
    get_string("logocompact", "admin") . " / " . get_string("favicon", "admin"), "");
$page->add($setting);

// Small logo file setting.
$setting = new admin_setting_configstoredfile("core_admin/logocompact",
    get_string("logocompact", "admin"),
    get_string("logocompact_desc", "admin"),
    "logocompact", 0,
    ["maxfiles" => 1, "accepted_types" => [".jpg", ".jpeg", ".svg", ".png"]]);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

// Favicon file setting.
$setting = new admin_setting_configstoredfile("core_admin/favicon",
    get_string("favicon", "admin"),
    get_string("favicon_desc", "admin"),
    "favicon", 0,
    ["maxfiles" => 1, "accepted_types" => [".jpg", ".jpeg", ".png"]]);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

// Background image setting.
$setting = new admin_setting_heading("theme_degrade/backgroundimage_heading",
    get_string("backgroundimage", "theme_degrade"), "");
$page->add($setting);

$name = "theme_degrade/backgroundimage";
$setting = new admin_setting_configstoredfile($name,
    get_string("backgroundimage", "theme_degrade"),
    get_string("backgroundimage_desc", "theme_degrade"),
    "backgroundimage", 0,
    ["maxfiles" => 1, "accepted_types" => [".jpg", ".jpeg", ".svg", ".png"]]);
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

// Login Background image setting.
$setting = new admin_setting_configstoredfile("theme_degrade/loginbackgroundimage",
    get_string("loginbackgroundimage", "theme_degrade"),
    get_string("loginbackgroundimage_desc", "theme_degrade"), "loginbackgroundimage");
$setting->set_updatedcallback("theme_reset_all_caches");
$page->add($setting);

$settings->add($page);

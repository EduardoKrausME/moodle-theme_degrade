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
 * Footer file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $PAGE, $CFG, $OUTPUT;
require_once("{$CFG->dirroot}/theme/degrade/lib.php");

// Footer section.
$page = new admin_settingpage("theme_degrade_footer",
    get_string("footersettings", "theme_degrade"));

$url = "{$CFG->wwwroot}/theme/degrade/quickstart/#footer";
$setting = new admin_setting_heading("theme_degrade_quickstart_footer", "",
    get_string("quickstart_settings_link", "theme_degrade", $url));
$page->add($setting);

$htmlselect = "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}/theme/degrade/scss/colors.css\" />";
$htmlselect .= "\n\n" . $OUTPUT->render_from_template("theme_degrade/settings/colors", [
        "footercolor" => true,
        "colors" => theme_degrade_colors(),
        "defaultcolor" => theme_degrade_default_color("brandcolor", "#1a2a6c", "theme_boost"),
        "defaultcolorfooter" => theme_degrade_default_color("footer_background_color", "#1a2a6c"),
    ]);
$setting = new admin_setting_configtext("theme_degrade/footer_background_color",
    get_string("footer_background_color", "theme_degrade"),
    get_string("footer_background_color_desc", "theme_degrade") . $htmlselect,
    "#1a2a6c");
$setting->set_updatedcallback("theme_reset_all_caches");
$PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", [$setting->get_id()]);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$setting = new admin_setting_heading("theme_degrade_footer_heading_description",
    get_string("footer_heading_description_title", "theme_degrade"),
    get_string("footer_heading_description_desc", "theme_degrade"));
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

for ($i = 1; $i <= 4; $i++) {

    $setting = new admin_setting_heading("theme_degrade_footer_heading_{$i}",
        get_string("footer_heading", "theme_degrade", $i), "");
    $page->add($setting);

    $setting = new admin_setting_configtext("theme_degrade/footer_title_{$i}",
        get_string("footer_title", "theme_degrade", $i),
        get_string("footer_title_desc", "theme_degrade", $i), "");
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_confightmleditor("theme_degrade/footer_html_{$i}",
        get_string("footer_html", "theme_degrade", $i),
        get_string("footer_html_desc", "theme_degrade", $i), "");
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}

$setting = new admin_setting_heading('theme_degrade_footerblock_copywriter',
    get_string('footer_copywriter', 'theme_degrade'), '');
$page->add($setting);

$setting = new admin_setting_configcheckbox('theme_degrade/footer_show_copywriter',
    get_string('footer_show_copywriter', 'theme_degrade'),
    get_string('footer_show_copywriter_desc', 'theme_degrade'), 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);

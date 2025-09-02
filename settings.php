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
 * Settings file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->dirroot}/theme/degrade/lib.php");

if ($hassiteconfig) {
    $pluginname = get_string("pluginname", "theme_degrade");
    $title = "{$pluginname} - " . get_string("advancedsettings");
    $url = new moodle_url("/admin/settings.php?section=themesettingdegrade");
    $ADMIN->add("themes", new admin_externalpage("theme_degrade_link1", $title, $url));

    $title = "{$pluginname} - " . get_string("quickstart_title", "theme_degrade");
    $url = new moodle_url("/theme/degrade/quickstart/");
    $ADMIN->add("themes", new admin_externalpage("theme_degrade_link2", $title, $url));
}

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs(
        "themesettingdegrade",
        get_string("configtitle", "theme_degrade")
    );

    require_once("settings/general.php");
    require_once("settings/advanced.php");
    require_once("settings/userprofile.php");
    require_once("settings/accessibility.php");
    require_once("settings/course.php");
    require_once("settings/footer.php");
}

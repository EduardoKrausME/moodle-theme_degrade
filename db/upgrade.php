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
 * Upgrade file
 *
 * @package    theme_degrade
 * @copyright  2024 Eduardo kraus (http://eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * function xmldb_supervideo_upgrade
 *
 * @param int $oldversion
 *
 * @return bool
 *
 * @throws Exception
 */
function xmldb_theme_degrade_upgrade($oldversion) {
    if ($oldversion < 2024031007) {
        require_once(__DIR__ . "/install.php");
        degrade_install_settings_icons();

        upgrade_plugin_savepoint(true, 2024031007, 'theme', 'degrade');
    }

    if ($oldversion < 2024042301) {
        set_config("footer_type", 0, "theme_degrade");
        set_config("home_type", 0, "theme_degrade");

        upgrade_plugin_savepoint(true, 2024042301, 'theme', 'degrade');
    }

    if ($oldversion < 2024042400) {
        $htmldata = get_config("theme_degrade", "home_htmldata");
        $cssdata = get_config("theme_degrade", "home_cssdata");
        $html = "{$htmldata}\n<style>{$cssdata}</style>";
        set_config("home_htmleditor_all", $html, "theme_degrade");

        $htmldata = get_config("theme_degrade", "footer_htmldata");
        $cssdata = get_config("theme_degrade", "footer_cssdata");
        $html = "{$htmldata}\n<style>{$cssdata}</style>";
        set_config("footer_htmleditor_all", $html, "theme_degrade");

        upgrade_plugin_savepoint(true, 2024042400, 'theme', 'degrade');
    }

    if ($oldversion < 2024050200) {
        $fonts = "<style>
@import url('https://fonts.googleapis.com/css2?family=Acme&family=Almendra:ital,wght@0,400;0,700;1,400;1,700&family=Amatic+SC:wght@400;700&family=Bad+Script&family=Dancing+Script:wght@400..700&family=Great+Vibes&family=Marck+Script&family=Nanum+Pen+Script&family=Orbitron:wght@400..900&family=Ubuntu+Condensed&family=Ubuntu+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');
</style>";
        set_config("pagefonts", $fonts, "theme_degrade");
        upgrade_plugin_savepoint(true, 2024050200, 'theme', 'degrade');
    }

    return true;
}

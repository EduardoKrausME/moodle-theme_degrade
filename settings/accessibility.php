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
 * Theme Settings File
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage("theme_degrade_accessibility",
    get_string("settings_accessibility_heading", "theme_degrade"));

$url = "{$CFG->wwwroot}/theme/degrade/quickstart/#accessibility";
$setting = new admin_setting_heading("theme_degrade_quickstart_accessibility", "",
    get_string("quickstart_settings_link", "theme_degrade", $url));
$page->add($setting);

$page->add(new admin_setting_configcheckbox("theme_degrade/enable_accessibility",
    get_string("settings_accessibility", "theme_degrade"),
    get_string("settings_accessibility_desc", "theme_degrade"), 1));

if ($CFG->lang == "pt_br") {
    $page->add(new admin_setting_configcheckbox("theme_degrade/enable_vlibras",
        "Habilitar VLibras",
        "", 0));

    $page->add(new admin_setting_configselect(
        "theme_degrade/vlibras_position",
        "Posição do balão do VLibras",
        "",
        "R",
        [
            "L" => "Esquerda",
            "R" => "Direita",
        ]
    ));

    $page->add(new admin_setting_configselect(
        "theme_degrade/vlibras_avatar",
        "Avatar do VLibras",
        "",
        "icaro",
        [
            "icaro"  => "Ícaro",
            "hosana" => "Hosana",
            "guga"   => "Guga",
            "random" => "Randômico",
        ]
    ));
}

$settings->add($page);

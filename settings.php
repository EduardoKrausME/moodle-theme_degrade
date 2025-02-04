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
 * settings.php
 *
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingdegrade',
        get_string('pluginname', 'theme_degrade'));

    require_once(__DIR__ . "/settings-theme.php");

    require_once(__DIR__ . "/settings-css.php");

    require_once(__DIR__ . "/settings-accessibility.php");

    require_once(__DIR__ . "/settings-topo.php");

    require_once(__DIR__ . "/settings-home.php");

    require_once(__DIR__ . "/settings-slideshow.php");

    require_once(__DIR__ . "/settings-mycourses.php");

    if (get_config('theme_degrade', 'home_type') == 0) {
        require_once(__DIR__ . "/settings-about.php");
    }

    require_once(__DIR__ . "/settings-footer.php");

    require_once(__DIR__ . "/settings-login.php");
}

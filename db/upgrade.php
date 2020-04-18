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
 * Theme upgradation process functions and its values.
 *
 * @package    theme_degrade
 * @copyright  2020 Eduardo Kraus (https://www.eduardokraus.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Theme_degrade upgrad function.
 *
 * @param string $oldversion
 * @return string
 * @throws dml_exception
 */
function xmldb_theme_degrade_upgrade($oldversion) {
    if ($oldversion < 2020041801) {
        set_config('enablegravatar', 1);
        set_config('gravatardefaulturl', 'mm');

        upgrade_plugin_savepoint(true, 2020041802, 'theme', 'degrade');
    }

    return true;
}

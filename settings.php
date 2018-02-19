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
 * The Setting layout.
 *
 * @package   theme_degrade
 * @copyright 2018 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$settings = null;

defined('MOODLE_INTERNAL') || die;

if (is_siteadmin()) {
    global $PAGE;
    $ADMIN->add('themes', new admin_category('theme_degrade', get_string('configtitle', 'theme_degrade')));

    require(dirname(__FILE__) . "/settings/cores.php");
    require(dirname(__FILE__) . "/settings/css.php");
    require(dirname(__FILE__) . "/settings/social.php");
    require(dirname(__FILE__) . "/settings/rodape.php");
}



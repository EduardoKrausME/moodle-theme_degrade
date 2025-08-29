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
 * Version 2025020600
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

try {
    // Profile background image.
    $fs = get_file_storage();
    $filerecord = [
        "component" => "theme_degrade",
        "contextid" => context_system::instance()->id,
        "userid" => get_admin()->id,
        "filearea" => "background_profile_image",
        "filepath" => "/",
        "itemid" => 0,
        "filename" => "user-modal-background.jpg",
    ];
    $fs->create_file_from_pathname($filerecord, "{$CFG->dirroot}/theme/degrade/pix/user-modal-background.jpg");
    set_config("background_profile_image", "/user-modal-background.jpg", "theme_degrade");
} catch (Exception $e) { // phpcs:disable
}
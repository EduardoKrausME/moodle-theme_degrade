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

// phpcs:disable moodle.Files.RequireLogin.Missing

require_once('../../config.php');

header("content-disposition: attachment; filename=\"course.svg\"");
header("content-type: image/svg+xml");

$courseid = required_param('id', PARAM_INT);

$cache = \cache::make("theme_degrade", "course_cache");
$cachekey = "course_svg_{$courseid}";
if (false && $cache->has($cachekey)) {
    die($cache->get($cachekey));
} else {
    $PAGE->set_context(context_course::instance($courseid));
    $svg = $OUTPUT->get_generated_svg_for_id($courseid);
    $cache->set($cachekey, $svg);
    die($svg);
}

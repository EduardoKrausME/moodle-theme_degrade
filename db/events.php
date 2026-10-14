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
 * Events file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\event\config_log_created;
use core\event\course_created;
use core\event\course_deleted;
use core\event\course_module_created;
use core\event\course_module_deleted;
use core\event\course_module_updated;
use core\event\course_updated;
use core\event\user_enrolment_created;
use core\event\user_enrolment_updated;
use theme_degrade\events\event_observers;

defined('MOODLE_INTERNAL') || die;

$observers = [
    [
        "eventname" => course_deleted::class,
        "callback" => [event_observers::class, "process_event"],
    ],
    [
        "eventname" => course_updated::class,
        "callback" => [event_observers::class, "process_event"],
    ],
    [
        "eventname" => course_created::class,
        "callback" => [event_observers::class, "process_event"],
    ],
    [
        "eventname" => config_log_created::class,
        "callback" => [event_observers::class, "process_event"],
    ],
    [
        "eventname" => course_module_deleted::class,
        "callback" => [event_observers::class, "course_module_deleted"],
    ],
    [
        "eventname" => course_module_created::class,
        "callback" => [event_observers::class, "course_module_created"],
    ],
    [
        "eventname" => course_module_updated::class,
        "callback" => [event_observers::class, "course_module_updated"],
    ],
    [
        "eventname" => user_enrolment_created::class,
        "callback" => [event_observers::class, "enrolment"],
    ],
    [
        "eventname" => user_enrolment_updated::class,
        "callback" => [event_observers::class, "enrolment"],
    ],
];

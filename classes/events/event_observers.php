<?php
// This file is part of the theme_degrade plugin for Moodle - http://moodle.org/
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
 * Event observers
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo Kraus https://eduardokraus.com/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\events;

/**
 * Class event_observers
 *
 * @package local_kopere_dashboard\events
 */
class event_observers {

    /**
     * Function process_event
     *
     * @param \core\event\base $event
     *
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public static function process_event(\core\event\base $event) {
        $eventname = str_replace("\\\\", "\\", $event->eventname);
        switch ($eventname) {
            case "\\core\\event\\course_deleted":
            case "\\core\\event\\course_updated":
            case "\\core\\event\\course_created":
            case "\\core\\event\\config_log_created":
                \cache::make("theme_degrade", "layout_cache")->purge();
                \cache::make("theme_degrade", "css_cache")->purge();
                \cache::make("theme_degrade", "logo_cache")->purge();
                break;
        }
    }
}

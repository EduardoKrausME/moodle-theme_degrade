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
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\events;

use cache;
use core\event\base;
use core\event\course_module_deleted;
use Exception;

/**
 * Class event_observers
 *
 * @package local_kopere_dashboard\events
 */
class event_observers {

    /**
     * Function process_event
     *
     * @param base $event
     * @throws Exception
     */
    public static function process_event(base $event) {
        $eventname = str_replace("\\\\", "\\", $event->eventname);
        switch ($eventname) {
            case '\core\event\course_deleted':
            case '\core\event\course_updated':
            case '\core\event\course_created':
            case '\core\event\config_log_created':
                theme_reset_all_caches();
                break;
        }
    }

    /**
     * Function course_module_deleted
     *
     * @param course_module_deleted $event
     * @return void
     * @throws \dml_exception
     */
    public static function course_module_deleted(course_module_deleted $event): void {
        $cmid = self::get_deleted_coursemodule_id($event);

        if (!$cmid) {
            return;
        }

        self::delete_coursemodule_filearea($cmid, "theme_degrade_customimage");
        self::delete_coursemodule_filearea($cmid, "theme_degrade_customicon");

        set_config("theme_degrade_customimage_{$cmid}", null, "theme_degrade");
        set_config("theme_degrade_customicon_{$cmid}", null, "theme_degrade");
        set_config("theme_degrade_customcolor_{$cmid}", null, "theme_degrade");

        cache::make("theme_degrade", "css_cache")->purge();
    }

    /**
     * Get the deleted course module id from the event.
     *
     * @param course_module_deleted $event
     * @return int
     */
    private static function get_deleted_coursemodule_id(course_module_deleted $event): int {
        if (!empty($event->objectid)) {
            return $event->objectid;
        }

        if (!empty($event->other["coursemodule"])) {
            if (is_object($event->other["coursemodule"]) && !empty($event->other["coursemodule"]->id)) {
                return (int) $event->other["coursemodule"]->id;
            }

            return (int) $event->other["coursemodule"];
        }

        return 0;
    }

    /**
     * Delete all files from a course module custom filearea.
     *
     * @param int $cmid
     * @param string $filearea
     * @return void
     * @throws \dml_exception
     */
    private static function delete_coursemodule_filearea(int $cmid, string $filearea): void {
        global $DB;

        $fs = get_file_storage();

        $params = [
            "component" => "theme_degrade",
            "filearea" => $filearea,
            "itemid" => $cmid,
        ];
        $contextids = $DB->get_fieldset_select(
            "files",
            "DISTINCT contextid",
            "component = :component AND filearea = :filearea AND itemid = :itemid",
            $params
        );

        foreach ($contextids as $contextid) {
            $fs->delete_area_files(
                (int) $contextid,
                "theme_degrade",
                $filearea,
                $cmid
            );
        }
    }

    /**
     * Function enrolment
     *
     * @param base $event
     * @return void
     */
    public static function enrolment(base $event) {
        cache::make("theme_degrade", "frontpage_cache")->purge();
    }
}

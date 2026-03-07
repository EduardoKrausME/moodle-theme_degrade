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
 * restore_theme_degrade_plugin.class.php
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState
require_once("{$CFG->dirroot}/backup/moodle2/restore_theme_plugin.class.php");

/**
 * Class restore_theme_degrade_plugin
 */
class restore_theme_degrade_plugin extends restore_theme_plugin {

    /**
     * Defines the course-level structure data.
     *
     * @return restore_path_element[]
     */
    protected function define_course_plugin_structure() {
        return [
            new restore_path_element(
                "degrade_courseopts",
                $this->get_pathfor("/courseopts")
            ),
        ];
    }

    /**
     * Process
     *
     * @param array $data
     * @return void
     */
    public function process_degrade_courseopts($data) {
        $data = (object) $data;
        $courseid = $this->task->get_courseid();

        if ($data->course_summary_banner !== null && $data->course_summary_banner !== "") {
            set_config("course_summary_banner_{$courseid}", $data->course_summary_banner, "theme_degrade");
        }

        if ($data->course_sections_icons !== null && $data->course_sections_icons !== "") {
            set_config("course_sections_icons_{$courseid}", $data->course_sections_icons, "theme_degrade");
        }

        if ($data->override_course_primarycolor !== null && $data->override_course_primarycolor !== "") {
            set_config("override_course_primarycolor_{$courseid}", $data->override_course_primarycolor, "theme_degrade");
        }

        if ($data->override_course_secondarycolor !== null && $data->override_course_secondarycolor !== "") {
            set_config("override_course_secondarycolor_{$courseid}", $data->override_course_secondarycolor, "theme_degrade");
        }

        // Keep filename reference to build URL.
        if (!empty($data->banner_course_file)) {
            set_config("banner_course_file_{$courseid}", $data->banner_course_file, "theme_degrade");
        }
    }

    /**
     * Execute
     *
     * @return void
     */
    protected function after_execute_course() {
        $this->add_related_files("theme_degrade", "banner_course_file", null);
    }
}

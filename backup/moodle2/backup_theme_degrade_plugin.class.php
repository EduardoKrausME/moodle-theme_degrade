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
 * backup_theme_degrade_plugin.class.php
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState
require_once("{$CFG->dirroot}/backup/moodle2/backup_theme_plugin.class.php");

/**
 * Class backup_theme_degrade_plugin
 */
class backup_theme_degrade_plugin extends backup_theme_plugin {

    /**
     * Defines the course-level structure for theme_degrade data.
     *
     * @return backup_plugin_element
     * @throws base_element_struct_exception
     */
    protected function define_course_plugin_structure() {
        global $DB;

        $plugin = $this->get_plugin_element(null, $this->get_theme_condition(), "degrade");

        $pluginwrapper = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($pluginwrapper);

        $courseopts = new backup_nested_element("courseopts", ["courseid"], [
            "course_summary_banner",
            "course_sections_icons",
            "override_course_primarycolor",
            "override_course_secondarycolor",
            "banner_course_file",
        ]);
        $pluginwrapper->add_child($courseopts);

        $namecoursesummary = $DB->sql_concat("'course_summary_banner_'", "c.id");
        $namesectionsicons = $DB->sql_concat("'course_sections_icons_'", "c.id");
        $nameprimary = $DB->sql_concat("'override_course_primarycolor_'", "c.id");
        $namesecondary = $DB->sql_concat("'override_course_secondarycolor_'", "c.id");
        $namebannerfile = $DB->sql_concat("'banner_course_file_'", "c.id");

        $courseopts->set_source_sql(
            "
        SELECT
            c.id AS courseid,
            csb.value AS course_summary_banner,
            csi.value AS course_sections_icons,
            pcol.value AS override_course_primarycolor,
            scol.value AS override_course_secondarycolor,
            bfile.value AS banner_course_file
          FROM {course} c
     LEFT JOIN {config_plugins} csb
            ON csb.plugin = 'theme_degrade'
           AND csb.name = {$namecoursesummary}
     LEFT JOIN {config_plugins} csi
            ON csi.plugin = 'theme_degrade'
           AND csi.name = {$namesectionsicons}
     LEFT JOIN {config_plugins} pcol
            ON pcol.plugin = 'theme_degrade'
           AND pcol.name = {$nameprimary}
     LEFT JOIN {config_plugins} scol
            ON scol.plugin = 'theme_degrade'
           AND scol.name = {$namesecondary}
     LEFT JOIN {config_plugins} bfile
            ON bfile.plugin = 'theme_degrade'
           AND bfile.name = {$namebannerfile}
         WHERE c.id = :courseid
        ",
            ["courseid" => backup::VAR_COURSEID]
        );

        $courseopts->annotate_files("theme_degrade", "banner_course_file", null);

        // Course module visual customizations.
        $moduleopts = new backup_nested_element("moduleopts");
        $moduleopt = new backup_nested_element("moduleopt", ["cmid"], [
            "contextid",
            "customimage",
            "customicon",
            "customcolor",
        ]);

        $pluginwrapper->add_child($moduleopts);
        $moduleopts->add_child($moduleopt);

        $namecustomimage = $DB->sql_concat("'theme_degrade_customimage_'", "cm.id");
        $namecustomicon = $DB->sql_concat("'theme_degrade_customicon_'", "cm.id");
        $namecustomcolor = $DB->sql_concat("'theme_degrade_customcolor_'", "cm.id");

        $moduleopt->set_source_sql(
            "
        SELECT DISTINCT
            cm.id AS cmid,
            ctx.id AS contextid,
            img.value AS customimage,
            ico.value AS customicon,
            col.value AS customcolor
          FROM {course_modules} cm
          JOIN {context} ctx
            ON ctx.contextlevel = :contextmodule
           AND ctx.instanceid = cm.id
     LEFT JOIN {config_plugins} img
            ON img.plugin = 'theme_degrade'
           AND img.name = {$namecustomimage}
     LEFT JOIN {config_plugins} ico
            ON ico.plugin = 'theme_degrade'
           AND ico.name = {$namecustomicon}
     LEFT JOIN {config_plugins} col
            ON col.plugin = 'theme_degrade'
           AND col.name = {$namecustomcolor}
     LEFT JOIN {files} imgf
            ON imgf.contextid = ctx.id
           AND imgf.component = 'theme_degrade'
           AND imgf.filearea = 'theme_degrade_customimage'
           AND imgf.itemid = cm.id
           AND imgf.filename <> '.'
     LEFT JOIN {files} icof
            ON icof.contextid = ctx.id
           AND icof.component = 'theme_degrade'
           AND icof.filearea = 'theme_degrade_customicon'
           AND icof.itemid = cm.id
           AND icof.filename <> '.'
         WHERE cm.course = :courseid
           AND (
                img.value IS NOT NULL
             OR ico.value IS NOT NULL
             OR col.value IS NOT NULL
             OR imgf.id IS NOT NULL
             OR icof.id IS NOT NULL
           )
        ",
            [
                "courseid" => backup::VAR_COURSEID,
                "contextmodule" => backup_helper::is_sqlparam(CONTEXT_MODULE),
            ]
        );

        // Files are stored in module context and use cmid as itemid.
        $moduleopt->annotate_files(
            "theme_degrade",
            "theme_degrade_customimage",
            "cmid",
            "contextid"
        );

        $moduleopt->annotate_files(
            "theme_degrade",
            "theme_degrade_customicon",
            "cmid",
            "contextid"
        );

        return $plugin;
    }
}

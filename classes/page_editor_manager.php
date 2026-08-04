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
 * Integration between Moodle page activity and the theme visual editor.
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade;

use cache;
use context_course;
use context_module;
use context_system;
use moodle_url;
use stdClass;

/**
 * Helper for linking mod_page content to records stored in theme_degrade_pages.
 */
class page_editor_manager {

    /** @var string Local prefix used in theme_degrade_pages.local. */
    private const LOCAL_PREFIX = 'mod_page_';

    /** @var string Default visual-editor model for Moodle pages. */
    private const DEFAULT_TEMPLATE = 'blank';

    /** @var string Request flag sent by the mod_page form. */
    private const REQUEST_FIELD = 'theme_degrade_use_editor';

    /**
     * Checks if the current user/request asked to use the visual editor.
     *
     * @param int|null $cmid
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function was_requested(?int $cmid = null): bool {
        if (!optional_param(self::REQUEST_FIELD, 0, PARAM_BOOL)) {
            return false;
        }

        if ($cmid) {
            return self::can_manage_cmid($cmid);
        }

        return self::can_manage_current_modedit_course();
    }

    /**
     * Gets the local key for a course module id.
     *
     * @param int $cmid
     * @return string
     */
    public static function local_from_cmid(int $cmid): string {
        return self::LOCAL_PREFIX . $cmid;
    }

    /**
     * Checks whether a theme editor page is linked to a Moodle page activity.
     *
     * @param stdClass $editorpage
     * @return bool
     */
    public static function is_mod_page_editor_record(stdClass $editorpage): bool {
        if (strpos($editorpage->local, self::LOCAL_PREFIX) === 0) {
            return true;
        }

        $info = json_decode($editorpage->info ?? '');
        return !empty($info->mod_page->cmid);
    }

    /**
     * Gets the editor record linked to a course module.
     *
     * @param int $cmid
     * @return stdClass|false
     * @throws \dml_exception
     */
    public static function get_by_cmid(int $cmid) {
        global $DB;

        $conditions = ['local' => self::local_from_cmid($cmid)];
        $records = $DB->get_records('theme_degrade_pages', $conditions, 'sort ASC, id ASC', '*', 0, 1);
        if ($records) {
            return reset($records);
        }

        $likepretty = $DB->sql_like('info', ':likepretty', false, false);
        $likecompact = $DB->sql_like('info', ':likecompact', false, false);
        $records = $DB->get_records_select(
            'theme_degrade_pages',
            "{$likepretty} OR {$likecompact}",
            [
                'likepretty' => '%"cmid": ' . $cmid . ',%',
                'likecompact' => '%"cmid":' . $cmid . ',%',
            ],
            'sort ASC, id ASC',
            '*',
            0,
            1
        );

        return $records ? reset($records) : false;
    }

    /**
     * Returns the visual editor URL for a course module, when linked.
     *
     * @param int $cmid
     * @return moodle_url|null
     * @throws \dml_exception
     * @throws \core\exception\moodle_exception
     */
    public static function get_editor_url_by_cmid(int $cmid): ?moodle_url {
        $editorpage = self::get_by_cmid($cmid);
        if (!$editorpage) {
            return null;
        }

        return self::get_builder_url($cmid);
    }

    /**
     * Returns the page builder URL for a Moodle page activity.
     *
     * @param int $cmid
     * @return moodle_url
     * @throws \core\exception\moodle_exception
     */
    public static function get_builder_url(int $cmid): moodle_url {
        return new moodle_url('/theme/degrade/_editor/page-builder.php', ['cmid' => $cmid]);
    }

    /**
     * Returns the visual editor URL for an editor record.
     *
     * @param stdClass $editorpage
     * @return moodle_url
     * @throws \core\exception\coding_exception
     * @throws \core\exception\moodle_exception
     */
    public static function get_editor_url(stdClass $editorpage): moodle_url {
        $params = ['dataid' => $editorpage->id];
        $cmid = self::get_cmid_from_editor_record($editorpage);
        if ($cmid) {
            $params['returnurl'] = self::get_builder_url($cmid)->out_as_local_url(false);
        }

        return new moodle_url('/theme/degrade/_editor/editor.php', $params);
    }

    /**
     * Gets the mod_page view URL for an editor record.
     *
     * @param stdClass $editorpage
     * @return moodle_url|null
     * @throws \core\exception\moodle_exception
     */
    public static function get_view_url_from_editor_record(stdClass $editorpage): ?moodle_url {
        $cmid = self::get_cmid_from_editor_record($editorpage);
        if (!$cmid) {
            return null;
        }

        return new moodle_url('/mod/page/view.php', ['id' => $cmid]);
    }

    /**
     * Gets the course module id from an editor record.
     *
     * @param stdClass $editorpage
     * @return int
     */
    public static function get_cmid_from_editor_record(stdClass $editorpage): int {
        if (strpos($editorpage->local, self::LOCAL_PREFIX) === 0) {
            return (int) substr($editorpage->local, strlen(self::LOCAL_PREFIX));
        }

        $info = json_decode($editorpage->info ?? '');
        if (!empty($info->mod_page->cmid)) {
            return (int) $info->mod_page->cmid;
        }

        return 0;
    }

    /**
     * Creates or refreshes the editor record linked to a Moodle page module.
     *
     * @param int $cmid
     * @return stdClass|null
     * @throws \Exception
     */
    public static function create_or_update_for_cmid(int $cmid): ?stdClass {
        global $CFG, $DB, $USER;

        $cm = get_coursemodule_from_id('page', $cmid);
        if (!$cm) {
            return null;
        }

        $modulepage = $DB->get_record('page', ['id' => $cm->instance]);
        if (!$modulepage) {
            return null;
        }

        $editorpage = self::get_by_cmid($cmid);

        if (!$editorpage) {
            require_once($CFG->dirroot . '/theme/degrade/_editor/editor-lib.php');

            $lang = $USER->lang ?? $CFG->lang;
            $editorpage = theme_degrade_editor_create_page(self::DEFAULT_TEMPLATE, $lang, self::local_from_cmid($cmid));

            if (!empty($modulepage->content)) {
                $editorpage->html = self::clean_stored_html($modulepage->content);
            }
        }

        $editorpage->title = format_string($modulepage->name, true, ['context' => context_module::instance($cmid)]);
        $editorpage->local = self::local_from_cmid($cmid);
        $editorpage->info = self::with_mod_page_info($editorpage, $cm, $modulepage);

        $DB->update_record('theme_degrade_pages', $editorpage);

        self::sync_editor_record_to_mod_page($editorpage);

        return $editorpage;
    }

    /**
     * Keeps the existing mod_page DB content equal to the visual editor content.
     *
     * @param stdClass $editorpage
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function sync_editor_record_to_mod_page(stdClass $editorpage): void {
        if (!self::is_mod_page_editor_record($editorpage)) {
            return;
        }

        $cmid = self::get_cmid_from_editor_record($editorpage);
        if (!$cmid) {
            return;
        }

        self::sync_cmid_to_mod_page($cmid);
    }

    /**
     * Compiles all visual-editor blocks linked to one Moodle page activity into mod_page.content.
     *
     * @param int $cmid
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \Exception
     */
    public static function sync_cmid_to_mod_page(int $cmid): void {
        global $CFG, $DB, $USER;

        $cm = get_coursemodule_from_id('page', $cmid);
        if (!$cm) {
            return;
        }

        $modulepage = $DB->get_record('page', ['id' => $cm->instance]);
        if (!$modulepage) {
            return;
        }

        require_once($CFG->dirroot . '/theme/degrade/_editor/editor-lib.php');

        $lang = $USER->lang ?? $CFG->lang;
        $local = self::local_from_cmid($cmid);
        $records = $DB->get_records('theme_degrade_pages', ['local' => $local], 'sort ASC, id ASC');

        if (!$records) {
            $modulepage->content = '<p></p>';
        } else {
            $compiled = theme_degrade_compile_pages($records, $lang, false);
            $content = '';

            foreach ($compiled->css as $cssfile) {
                if (strpos($cssfile, '/') === 0) {
                    $content .= '<link rel="stylesheet" href="' . $CFG->wwwroot . $cssfile . '">' . "\n";
                }
            }

            foreach ($compiled->pages as $compiledpage) {
                $content .= $compiledpage->html . "\n";
            }

            foreach ($compiled->js as $jsfile) {
                if (strpos($jsfile, '/') === 0) {
                    $content .= '<script src="' . $CFG->wwwroot . $jsfile . '"></script>' . "\n";
                } else if (strpos($jsfile, 'require') === 0) {
                    $content .= '<script>' . $jsfile . '</script>' . "\n";
                }
            }

            $modulepage->content = trim($content) ?: '<p></p>';
        }

        $modulepage->contentformat = FORMAT_HTML;
        $modulepage->timemodified = time();

        $DB->update_record('page', $modulepage);
        rebuild_course_cache($modulepage->course, true);
        cache::make('theme_degrade', 'frontpage_cache')->purge();
    }

    /**
     * Updates the editor title after activity settings are changed.
     *
     * @param int $cmid
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function refresh_from_mod_page(int $cmid): void {
        global $DB;

        $records = $DB->get_records('theme_degrade_pages', ['local' => self::local_from_cmid($cmid)], 'sort ASC, id ASC');
        if (!$records) {
            return;
        }

        $cm = get_coursemodule_from_id('page', $cmid);
        if (!$cm) {
            return;
        }

        $modulepage = $DB->get_record('page', ['id' => $cm->instance]);
        if (!$modulepage) {
            return;
        }

        foreach ($records as $editorpage) {
            if ((int) $editorpage->sort === 0) {
                $editorpage->title = format_string($modulepage->name, true, ['context' => context_module::instance($cmid)]);
            }
            $editorpage->info = self::with_mod_page_info($editorpage, $cm, $modulepage);
            $DB->update_record('theme_degrade_pages', $editorpage);
        }

        self::sync_cmid_to_mod_page($cmid);
    }

    /**
     * Deletes the editor record linked to a Moodle page module.
     *
     * @param int $cmid
     * @return void
     * @throws \dml_exception
     */
    public static function delete_for_cmid(int $cmid): void {
        global $DB;

        $DB->delete_records('theme_degrade_pages', ['local' => self::local_from_cmid($cmid)]);
        cache::make('theme_degrade', 'frontpage_cache')->purge();
    }

    /**
     * Returns mod_page edit form data for the current request, if applicable.
     *
     * @return array|null
     * @throws \coding_exception
     * @throws \core\exception\moodle_exception
     * @throws \dml_exception
     */
    public static function get_current_modedit_data(): ?array {
        global $PAGE;

        $path = $PAGE->url->get_path(false);
        if ($path !== '/course/modedit.php') {
            return null;
        }

        $add = optional_param('add', '', PARAM_PLUGIN);
        if ($add === 'page') {
            if (!self::can_manage_current_modedit_course()) {
                return null;
            }

            return [
                'enabled' => true,
                'linked' => false,
                'editorurl' => '',
                'requestfield' => self::REQUEST_FIELD,
            ];
        }

        $update = optional_param('update', 0, PARAM_INT);
        if (!$update) {
            return null;
        }

        $cm = get_coursemodule_from_id('', $update);
        if (!$cm || $cm->modname !== 'page' || !self::can_manage_cmid((int) $cm->id)) {
            return null;
        }

        $editorpage = self::get_by_cmid((int) $cm->id);

        return [
            'enabled' => true,
            'linked' => (bool) $editorpage,
            'editorurl' => $editorpage ? self::get_builder_url((int) $cm->id)->out(false) : '',
            'requestfield' => self::REQUEST_FIELD,
        ];
    }

    /**
     * Returns data for adding a guaranteed visual-editor link on the mod_page view screen.
     *
     * @return array|null
     * @throws \coding_exception
     * @throws \core\exception\moodle_exception
     * @throws \dml_exception
     */
    public static function get_current_view_data(): ?array {
        global $PAGE;

        $path = $PAGE->url->get_path(false);
        if ($path !== '/mod/page/view.php') {
            return null;
        }

        $cm = $PAGE->cm ?? null;
        if (!$cm || $cm->modname !== 'page' || !self::can_manage_cmid((int) $cm->id)) {
            return null;
        }

        $editorpage = self::get_by_cmid((int) $cm->id);
        if (!$editorpage) {
            return null;
        }

        return [
            'editorurl' => self::get_builder_url((int) $cm->id)->out(false),
        ];
    }

    /**
     * Checks whether the current user can manage a Moodle page course module.
     *
     * @param int $cmid
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private static function can_manage_cmid(int $cmid): bool {
        $context = context_module::instance($cmid, IGNORE_MISSING);
        if (!$context) {
            return has_capability('moodle/site:config', context_system::instance());
        }

        return has_capability('moodle/course:manageactivities', $context)
            || has_capability('moodle/site:config', context_system::instance());
    }

    /**
     * Checks whether the current user can add/edit activities in the current modedit course.
     *
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private static function can_manage_current_modedit_course(): bool {
        global $PAGE;

        $courseid = optional_param('course', 0, PARAM_INT);
        if (!$courseid && !empty($PAGE->course->id)) {
            $courseid = (int) $PAGE->course->id;
        }

        if ($courseid) {
            $context = context_course::instance($courseid, IGNORE_MISSING);
            if ($context && has_capability('moodle/course:manageactivities', $context)) {
                return true;
            }
        }

        return has_capability('moodle/site:config', context_system::instance());
    }

    /**
     * Adds mod_page metadata to the existing JSON info field.
     *
     * @param stdClass $editorpage
     * @param stdClass $cm
     * @param stdClass $modulepage
     * @return string
     */
    private static function with_mod_page_info(stdClass $editorpage, stdClass $cm, stdClass $modulepage): string {
        $info = json_decode($editorpage->info ?? '');
        if (!$info) {
            $info = new stdClass();
        }

        $info->mod_page = (object) [
            'enabled' => 1,
            'cmid' => (int) $cm->id,
            'instanceid' => (int) $modulepage->id,
            'courseid' => (int) $modulepage->course,
            'timemodified' => time(),
        ];

        return json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Cleans HTML before it is inserted into the visual editor.
     *
     * @param string $html
     * @return string
     */
    private static function clean_stored_html(string $html): string {
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            $html = trim($matches[1]);
        }

        return $html;
    }
}

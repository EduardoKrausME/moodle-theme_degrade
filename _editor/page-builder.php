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
 * Visual page builder for Moodle page activities.
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_degrade\page_editor_manager;

require_once('../../../config.php');
require_once('editor-lib.php');

global $CFG, $DB, $OUTPUT, $PAGE, $USER;

$cmid = required_param('cmid', PARAM_INT);
$cm = get_coursemodule_from_id('page', $cmid, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$context = context_module::instance($cmid);
$modulepage = $DB->get_record('page', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, false, $cm);
require_capability('moodle/course:manageactivities', $context);

$builderurl = new moodle_url('/theme/degrade/_editor/page-builder.php', ['cmid' => $cmid]);
$returnurl = $builderurl->out_as_local_url(false);
$viewurl = new moodle_url('/mod/page/view.php', ['id' => $cmid]);
$editurl = new moodle_url('/course/modedit.php', ['update' => $cmid, 'return' => 1]);

page_editor_manager::create_or_update_for_cmid($cmid);

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_cm($cm);
$PAGE->set_url($builderurl);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(format_string($modulepage->name, true, ['context' => $context]));
$PAGE->set_heading(format_string($course->fullname, true, ['context' => context_course::instance($course->id)]));

$lang = $USER->lang ?? $CFG->lang;
$local = page_editor_manager::local_from_cmid($cmid);
$records = $DB->get_records('theme_degrade_pages', ['local' => $local], 'sort ASC, id ASC');
$compiledpages = theme_degrade_compile_pages($records, $lang, true);

$csslink = '';
foreach ($compiledpages->css as $cssfile) {
    if (strpos($cssfile, '/') === 0) {
        $csslink .= '<link rel="stylesheet" href="' . $CFG->wwwroot . $cssfile . '">' . "\n";
    }
}
foreach ($compiledpages->js as $jsfile) {
    if (strpos($jsfile, 'require') === 0) {
        $PAGE->requires->js_init_code($jsfile);
    } else if (strpos($jsfile, '/') === 0) {
        $PAGE->requires->js($jsfile);
    }
}

foreach ($compiledpages->pages as $blockpage) {
    $blockpage->editorurl = page_editor_manager::get_editor_url($blockpage)->out(false);
    $blockpage->deleteurl = (new moodle_url('/theme/degrade/_editor/editor.php', [
        'dataid' => $blockpage->id,
        'delete' => 1,
        'returnurl' => $returnurl,
    ]))->out(false);
}

$editorcontext = [
    'editing' => true,
    'homemode_pages' => array_values($compiledpages->pages),
    'homemode_page_warningnopages' => count($compiledpages->pages) === 0,
];

$templatecontext = [
    'title' => get_string('pageeditor_builder_title', 'theme_degrade'),
    'description' => get_string('pageeditor_builder_description', 'theme_degrade'),
    'viewurl' => $viewurl->out(false),
    'editurl' => $editurl->out(false),
    'csslink' => $csslink,
    'editorcontext' => $editorcontext,
];

$PAGE->requires->strings_for_js(['preview', 'add_block_edit'], 'theme_degrade');
$PAGE->requires->js_call_amd('theme_degrade/frontpage', 'add_block', [[
    'lang' => $lang,
    'local' => $local,
    'returnurl' => $returnurl,
]]);
$PAGE->requires->js_call_amd('theme_degrade/frontpage', 'block_order', [$local]);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('theme_degrade/page_builder', $templatecontext);
echo $OUTPUT->footer();

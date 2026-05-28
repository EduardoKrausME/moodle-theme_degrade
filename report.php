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
 * Redirects administrators to the Kopere BI accessibility reports.
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_kopere_bi\install\reports;

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/theme/degrade/report.php'));
$PAGE->set_title(get_string('report_accessibility_title', 'theme_degrade'));
$PAGE->set_heading(get_string('report_accessibility_title', 'theme_degrade'));
$PAGE->navbar->add(get_string('report_accessibility_title', 'theme_degrade'));

$pluginpath = core_component::get_plugin_directory('local', 'kopere_bi');
$dbman = $DB->get_manager();
$koperebipagetable = new xmldb_table('local_kopere_bi_page');
$koperebiinstalled = $pluginpath && $dbman->table_exists($koperebipagetable);

if (!$koperebiinstalled) {
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('generalbox theme-degrade-report-install');
    echo html_writer::tag('h3', get_string('report_kopere_bi_missing_title', 'theme_degrade'));
    echo html_writer::tag('p', get_string('report_kopere_bi_missing_intro', 'theme_degrade'));
    echo html_writer::alist([
        get_string('report_kopere_bi_install_step_1', 'theme_degrade'),
        get_string('report_kopere_bi_install_step_2', 'theme_degrade'),
        get_string('report_kopere_bi_install_step_3', 'theme_degrade'),
    ]);
    echo html_writer::link(
        new moodle_url('https://moodle.org/plugins/local_kopere_bi'),
        get_string('report_kopere_bi_plugin_link', 'theme_degrade'),
        ['class' => 'btn btn-primary', 'target' => '_blank', 'rel' => 'noopener noreferrer']
    );
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die;
}

require_once($pluginpath . '/classes/install/reports.php');

$pagefile = __DIR__ . '/db/reports/kopere-bi-accessibility.json';
if (!file_exists($pagefile)) {
    throw new moodle_exception('reportfilemissing', 'theme_degrade');
}

try {
    reports::from_file($pagefile);
} catch (Throwable $exception) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification($exception->getMessage(), 'notifyproblem');
    echo $OUTPUT->footer();
    die;
}

$page = $DB->get_record('local_kopere_bi_page', ['refkey' => 'theme_degrade_accessibility'], '*', MUST_EXIST);
redirect(new moodle_url('/local/kopere_bi/', [
    'classname' => 'dashboard',
    'method' => 'preview',
    'page_id' => $page->id,
]));

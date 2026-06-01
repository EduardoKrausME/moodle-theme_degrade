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
 * phpcs:disable moodle.Files.RequireLogin.Missing
 * Stores accessibility toolbar usage events.
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');

require_sesskey();

header('Content-Type: application/json; charset=utf-8');

if (!isloggedin() || isguestuser()) {
    echo json_encode(['success' => true, 'stored' => false, 'reason' => 'guest']);
    die;
}

$action = required_param('action', PARAM_ALPHANUMEXT);
$item = optional_param('item', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHANUMEXT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$pageurl = optional_param('pageurl', '', PARAM_RAW_TRIMMED);
$activeitems = optional_param('activeitems', '', PARAM_RAW_TRIMMED);
$statejson = optional_param('statejson', '', PARAM_RAW_TRIMMED);

$allowedactions = [
    'vlibras_tab_click',
    'toolbar_open',
    'toolbar_item',
    'toolbar_reset',
];
$allowedstatuses = [
    'clicked',
    'opened',
    'enabled',
    'disabled',
];

if (!in_array($action, $allowedactions, true) || ($status !== '' && !in_array($status, $allowedstatuses, true))) {
    echo json_encode(['success' => false, 'stored' => false, 'reason' => 'invalid']);
    die;
}

$dbman = $DB->get_manager();
$table = new xmldb_table('theme_degrade_accesslog');
if (!$dbman->table_exists($table)) {
    echo json_encode(['success' => false, 'stored' => false, 'reason' => 'tablemissing']);
    die;
}

$record = (object) [
    'userid' => $USER->id,
    'courseid' => $courseid,
    'cmid' => $cmid,
    'action' => core_text::substr($action, 0, 40),
    'item' => core_text::substr($item, 0, 100),
    'status' => core_text::substr($status, 0, 20),
    'activeitems' => $activeitems,
    'statejson' => $statejson,
    'pageurl' => $pageurl,
    'timecreated' => time(),
];

$DB->insert_record('theme_degrade_accesslog', $record);

echo json_encode(['success' => true, 'stored' => true]);

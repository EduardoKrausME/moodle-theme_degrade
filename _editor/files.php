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
 * Editor.
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once('../lib.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$chave = required_param('chave', PARAM_TEXT);

$component = 'theme_degrade';
$contextid = $context->id;
$adminid = get_admin()->id;
$filearea = "editor_{$chave}";

if (isset($_FILES['files']['name'])) {
    foreach ($_FILES['files']['name'] as $fileid => $name) {
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
            $fs = get_file_storage();
            $filerecord = [
                "component" => $component,
                "contextid" => $contextid,
                "userid" => $adminid,
                "filearea" => $filearea,
                "filepath" => '/',
                "itemid" => time() - 1714787612,
                "filename" => $_FILES['files']['name'][$fileid],
            ];
            $fs->create_file_from_pathname($filerecord, $_FILES['files']['tmp_name'][$fileid]);
        }
    }
}

$fs = get_file_storage();
$files = $fs->get_area_files($contextid, $component, $filearea, false, $sort = "filename", false);

$images = [];
/** @var stored_file $file */
foreach ($files as $file) {
    $url = moodle_url::make_file_url(
        "$CFG->wwwroot/pluginfile.php",
        "/{$contextid}/theme_degrade/{$file->get_filearea()}/{$file->get_itemid()}{$file->get_filepath()}{$file->get_filename()}");
    $images[] = $url->out(false);
}

header("Content-Type: application/json");
echo json_encode(['data' => $images]);
die();

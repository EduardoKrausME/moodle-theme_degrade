<?php
/**
 * User: Eduardo Kraus
 * Date: 03/05/2024
 * Time: 22:28
 */

require_once('../../../config.php');
require_once('../lib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

$chave = required_param('chave', PARAM_TEXT);

$component = 'theme_degrade';
$contextid = context_system::instance()->id;
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

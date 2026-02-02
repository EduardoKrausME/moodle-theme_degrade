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
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once('../lib.php');
require_once('editor-lib.php');

global $CFG, $DB, $USER;

if (!has_capability('moodle/site:config', context_system::instance())) {
    echo json_encode(["error" => "You don't have permission to access this page.",]);
    die;
}
$context = context_system::instance();
require_sesskey();

$local = required_param('local', PARAM_TEXT);
$action = required_param('action', PARAM_TEXT);

$component = 'theme_degrade';
$filearea = "editor_{$local}";

if ($action == "langedit") {
    $dataid = required_param("dataid", PARAM_INT);

    $page = $DB->get_record("theme_degrade_pages", ["id" => $dataid], "*", MUST_EXIST);
    $page->lang = required_param("lang", PARAM_TEXT);

    $DB->update_record("theme_degrade_pages", $page);

    header("Content-Type: application/json");
    echo json_encode(["status" => "ok"]);
    die;
} elseif ($action == "homemode") {
    $homemode = optional_param("homemode", 0, PARAM_INT);
    set_config("homemode", $homemode, "theme_degrade");

    redirect(new moodle_url("/", ["redirect" => 0]));
} elseif ($action == "loaddata") {
    $datakey = required_param("datakey", PARAM_TEXT);
    switch ($datakey) {
        case "courses":
            $courses = $DB->get_records_select(
                "course",
                "id>1 AND visible=1",
                null,
                "fullname ASC",
                "id,fullname AS name"
            );

            header("Content-Type: application/json");
            echo json_encode(array_values($courses));
    }
} elseif ($action == "page-save") {
    $dataid = required_param("dataid", PARAM_INT);
    $page = $DB->get_record("theme_degrade_pages", ["id" => $dataid], "*", MUST_EXIST);

    switch ($page->type) {
        case "html":
        case "html-form":
            $html = required_param("html", PARAM_RAW);
            $css = required_param("css", PARAM_RAW);
            if (isset($html[3]) && isset($css[3])) {
                if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
                    $html = trim($matches[1]);
                }
                $page->html = "{$html}<style>{$css}</style>";
            }
        case "form":
            break;
        default:
            throw new Exception("Type not found");
    }


    if (isset($_POST["save"])) {
        $savedata = theme_degrade_clear_params_array($_POST["save"], PARAM_RAW);
        $info = json_decode($page->info);
        $info->savedata = array_values($savedata);
        $page->info = json_encode($info);
    }

    $DB->update_record("theme_degrade_pages", $page);

    \cache::make("theme_degrade", "frontpage_cache")->purge();
    if ($page->local == "home") {
        redirect(new moodle_url("/", ["redirect" => 0]));
    }
    die;
} elseif ($action == "page-order") {
    $orders = theme_degrade_clear_params_array($_POST["order"], PARAM_INT);

    $pageorder = 0;
    foreach ($orders as $pageid) {
        if ($pageid) {
            $page = (object)[
                "id" => $pageid,
                "sort" => $pageorder++,
            ];
            try {
                $DB->update_record("theme_degrade_pages", $page);
            } catch (dml_exception $e) {
                error_log($e->getMessage());
            }
        }
    }

    \cache::make("theme_degrade", "frontpage_cache")->purge();
    die("OK");
} elseif ($action == "file-upload") {
    if (isset($_FILES['files']['name'])) {
        $aloweb = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'webm', 'mp4', 'mp3', 'pdf', 'doc', 'docx'];

        $extension = pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION);
        if (in_array($extension, $aloweb)) {
            $fs = get_file_storage();
            $filerecord = (object)["component" => $component, "contextid" => $context->id, "userid" => $USER->id, "filearea" => $filearea, "filepath" => '/', "itemid" => time() - 1714787612, "filename" => $_FILES['files']['name'],];
            $fs->create_file_from_pathname($filerecord, $_FILES['files']['tmp_name']);

            $url = moodle_url::make_pluginfile_url(
                $context->id,
                "theme_degrade",
                $filerecord->filearea,
                $filerecord->itemid,
                $filerecord->filepath,
                $filerecord->filename
            );

            echo json_encode([(object)["name" => $_FILES['files']['name'], "type" => "image", "src" => $url->out(false), "size" => filesize($_FILES['files']['tmp_name']),]]);

            die();
        }
    }
} elseif ($action == "file-delete") {
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, $component, $filearea, false, $sort = "filename", false);

    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData);

    /** @var stored_file $file */
    foreach ($files as $file) {
        if ($file->get_id() == $data->id) {
            $file->delete();
        }
    }
} elseif ($action == "file-list") {
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, $component, $filearea, false, $sort = "filename", false);

    $items = [];
    /** @var stored_file $file */
    foreach ($files as $file) {
        $url = moodle_url::make_pluginfile_url(
            $context->id, "theme_degrade",
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        );
        $items[] = [
            "id" => $file->get_id(),
            "name" => $file->get_filename(),
            "type" => "image",
            "src" => $url->out(false),
            "size" => $file->get_filesize(),
            "info" => "Upload file",
            "delete" => true,
        ];
    }

    $sql = "SELECT * FROM {course}";
    $courses = $DB->get_records_sql($sql);

    foreach ($courses as $course) {
        $courseobj = new core_course_list_element($course);
        foreach ($courseobj->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $courseimage = file_encode_url(
                    "{$CFG->wwwroot}/pluginfile.php",
                    "/{$file->get_contextid()}/{$file->get_component()}/" . "{$file->get_filearea()}{$file->get_filepath()}{$file->get_filename()}",
                    !$isimage
                );

                $items[] = [
                    "id" => 2,
                    "name" => $file->get_filename(),
                    "type" => "image",
                    "src" => $courseimage,
                    "size" => $file->get_filesize(),
                    "info" => "Course file",
                    "delete" => false,
                ];
            }
        }
    }

    header("Content-Type: application/json");
    echo json_encode($items);
}
die();

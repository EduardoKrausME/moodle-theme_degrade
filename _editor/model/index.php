<?php

require_once("../../../../config.php");
require_once("../editor-lib.php");

global $CFG;

$files = glob("*");

$items = [];
foreach ($files as $file) {
    $infojson = "{$file}/info.json";
    if (file_exists($infojson)) {
        $data = json_decode(load_info_json($infojson));
        if ($data) {
            $items[] = [
                "id" => $file,
                "location" => $file,
                "title" => $data->title,
                "image" => "{$CFG->wwwroot}/theme/degrade/_editor/model/{$file}/print.png",
                "preview" => "{$CFG->wwwroot}/theme/degrade/_editor/model/{$file}/preview.html",
            ];
        }
    }
}

header("Content-Type: application/json");
echo json_encode($items);

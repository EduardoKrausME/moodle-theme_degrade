<?php

require_once("../editor-lib.php");

$_GET["current_language"] = "en";
$path = $_GET['path'];
$path = preg_replace("/[^-a-zA-Z0-9\s]/", "", $path);

if (file_exists(__DIR__ . "/{$path}/preview.html")) {
    header("Location: {$path}/preview.html");
    die;
} else if (file_exists(__DIR__ . "/{$path}/editor.html")) {
    echo "<!DOCTYPE html>
            <html lang=\"en\">
            <head>
                <meta charset=\"UTF-8\">
                <title>Preview</title>
                <link rel=\"stylesheet\" href=\"../css/bootstrap.css\">
                <link rel=\"stylesheet\" href=\"{$path}/style.css\">
            </head>
            <body style=\"background: linear-gradient(135deg, #f9fafb, #f0f4f8);\">";
    $html = file_get_contents(__DIR__ . "/{$path}/editor.html");
    echo theme_degrade_replace_lang_by_string($html);
    echo "</body></html>";
} else {
    echo "{$path} not found";
}

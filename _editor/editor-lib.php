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
 * Functions.
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Actionurl function
 *
 * @param $action
 * @return string
 */
function actionurl($action) {
    global $local, $lang;
    return "actions.php?action={$action}&local={$local}&lang={$lang}&sesskey=" . sesskey();
}

/**
 * editor_create_page function
 *
 * @param $template
 * @param $lang
 * @param $local
 * @return object
 * @throws Exception
 */
function editor_create_page($template, $lang, $local) {
    global $CFG, $DB;

    $infofile = __DIR__ . "/model/{$template}/info.json";
    if (file_exists($infofile)) {
        $info = json_decode(load_info_json($infofile));
        $htmlfile = __DIR__ . "/model/{$template}/editor.html";
        $html = file_get_contents($htmlfile);

        $html = str_replace("src=\"../", "src=\"{$CFG->wwwroot}/theme/degrade/_editor/model/{$template}/../", $html);
        $html = str_replace("url(\"../", "url(\"{$CFG->wwwroot}/theme/degrade/_editor/model/{$template}/../", $html);
    } else {
        throw new Exception("File template not found");
    }

    $page = (object)[
        "local" => $local,
        "type" => $info->type,
        "title" => $info->title,
        "html" => $html,
        "info" => json_encode($info),
        "template" => $template,
        "lang" => $lang,
        "sort" => time(),
    ];
    $page->id = $DB->insert_record("theme_degrade_pages", $page);

    return $page;
}

/**
 * compile_pages
 *
 * @param $pages
 * @return object
 * @throws Exception
 */
function compile_pages($pages) {
    $return = (object)["pages" => [], "css" => [], "js" => []];

    $previewdataid = optional_param("dataid", false, PARAM_INT);

    foreach ($pages as $page) {
        if ($page->id == $previewdataid) {
            $html = required_param("html", PARAM_RAW);
            $css = required_param("css", PARAM_RAW);
            if (isset($html[3])) {
                $html = preg_replace('/<\/?body.*?>/', '', $html);
                $page->html = "<div class='alert alert-warning page-editor-preview'>{$html}<style>{$css}</style></div>";
            }

            $savedata = theme_degrade_clear_params_array($_POST["save"], PARAM_RAW);
            $info = json_decode($page->info);
            $info->savedata = array_values($savedata);
            $page->info = json_encode($info);
        }

        if (isset($page->info[5])) {
            $info = json_decode($page->info);

            if ($info->type == "html-form" || $info->type == "form") {
                $file = __DIR__ . "/model/{$page->template}/create-block.php";
                if (file_exists($file)) {
                    require_once($file);

                    $createblocks = str_replace("-", "_", "{$page->template}_createblocks");
                    $block = $createblocks($page);

                    if (strpos($page->html, "[[change-to-blocks]]") !== false) {
                        $page->html = str_replace("[[change-to-blocks]]", $block, $page->html);
                    } else {
                        $page->html = $page->html . $block;
                    }
                } else {
                    echo "{$file} not found<br>";
                }
            }

            // The file name is added to CSS and JS to ensure no duplication.
            // Faster in PHP and less code than isset().
            if (isset($info->form->scripts)) {
                foreach ($info->form->scripts as $script) {
                    if ($script == "jquery") {
                        $return->js["jquery"] = "jquery";
                    } elseif ($script == "jqueryui") {
                        $return->js["jqueryui"] = "jqueryui";
                    } elseif (strpos($script, "http") === 0) {
                        $js = "require(['jquery'],function($){ $.getScript('{$script}')})";
                        $return->js[$script] = $js;
                    } else {
                        if (file_exists(__DIR__ . "/model/{$page->template}/{$script}")) {
                            $file = "/theme/degrade/_editor/model/{$page->template}/{$script}";
                            $return->js["{$page->template}/{$script}"] = $file;
                        }
                    }
                }
            }
            if (isset($info->form->styles)) {
                $file = "/theme/degrade/_editor/model/{$page->template}/style.css";
                $return->css["{$page->template}/style.css"] = $file;
                foreach ($info->form->styles as $style) {
                    if ($style != "bootstrap") {
                        if (file_exists(__DIR__ . "/model/{$page->template}/{$style}")) {
                            $file = "/theme/degrade/_editor/model/{$page->template}/{$style}";
                            $return->css["{$page->template}/{$style}"] = $file;
                        };
                    }
                }
            }
        }
        $return->js = array_values($return->js);
        $return->css = array_values($return->css);

        $return->pages[] = $page;
    }

    return $return;
}

/**
 * I made clean_param_array recursive
 *
 * @param $in
 * @param $type
 * @return array|mixed
 */
function theme_degrade_clear_params_array($in, $type) {
    $out = [];
    if (is_array($in)) {
        foreach ($in as $key => $value) {
            $out[$key] = theme_degrade_clear_params_array($value, $type);
        }
    } elseif (is_string($in)) {
        try {
            return clean_param($in, $type);
        } catch (\coding_exception $e) {
            debugging($e->getMessage());
        }
    } else {
        return $in;
    }

    return $out;
}

/**
 * load_info_json
 *
 * @param $filepath
 * @return string
 * @throws Exception
 */
function load_info_json($filepath) {
    $json = file_get_contents($filepath);
    $data = json_decode($json, true);
    replace_lang_strings($data);

    return json_encode($data, JSON_PRETTY_PRINT);
}

/**
 * replace_lang_strings
 *
 * @param $data
 * @return void
 * @throws Exception
 */
function replace_lang_strings(&$data) {
    foreach ($data as $key => &$value) {
        if (is_array($value)) {
            replace_lang_strings($value); // Recursive call.
        } elseif (is_string($value) && str_starts_with($value, 'lang::')) {
            $langkey = substr($value, 6); // Remove 'lang::'.
            $value = get_string($langkey, 'theme_degrade');
        }
    }
}

/**
 * @param object $course
 * @return object
 * @throws Exception
 */
function get_editor_course_link($course) {
    global $CFG, $USER;

    $title = $course->fullname;
    $link = "{$CFG->wwwroot}/course/view.php?id={$course->id}";
    $access = get_string("access_course", "theme_degrade");

    if (file_exists("{$CFG->dirroot}/local/kopere_pay/lib.php")) {
        $coursecontext = context_course::instance($course->id);
        if (!has_capability("moodle/course:view", $coursecontext, $USER)) {
            $enable = get_config("local_kopere_dashboard", "builder_enable_{$course->id}");

            $access = get_string("access_course_buy", "theme_degrade");
            if ($enable) {
                $link = "{$CFG->wwwroot}/local/kopere_pay/view.php?id={$course->id}";
                $title = get_config("local_kopere_dashboard", "builder_titulo_{$course->id}");
            } else {
                $link = "{$CFG->wwwroot}/local/kopere_pay/?id={$course->id}";
            }
        }
    }

    return (object)[
        "link" => $link,
        "title" => $title,
        "access" => $access,
    ];
}

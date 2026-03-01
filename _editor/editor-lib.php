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
function theme_degrade_actionurl($action) {
    global $local, $lang;
    return "actions.php?action={$action}&local={$local}&lang={$lang}&sesskey=" . sesskey();
}

/**
 * editor create page function
 *
 * @param $template
 * @param $lang
 * @param $local
 * @return object
 * @throws Exception
 */
function theme_degrade_editor_create_page($template, $lang, $local) {
    global $CFG, $DB;

    $infofile = __DIR__ . "/model/{$template}/info.json";
    if (file_exists($infofile)) {
        $info = json_decode(theme_degrade_load_info_json($infofile));
        $htmlfile = __DIR__ . "/model/{$template}/editor.html";
        $html = file_get_contents($htmlfile);
        $html = theme_degrade_replace_lang_by_string($html);

        $html = str_replace("src=\"../", "src=\"{$CFG->wwwroot}/theme/degrade/_editor/model/{$template}/../", $html);
        $html = str_replace("url(\"../", "url(\"{$CFG->wwwroot}/theme/degrade/_editor/model/{$template}/../", $html);
    } else {
        throw new Exception("File template not found");
    }

    $page = (object) [
        "local" => $local,
        "type" => $info->type,
        "title" => $info->title,
        "html" => $html,
        "info" => json_encode($info, JSON_PRETTY_PRINT),
        "template" => $template,
        "lang" => $lang,
        "sort" => time(),
    ];
    $page->id = $DB->insert_record("theme_degrade_pages", $page);

    return $page;
}

/**
 * Compile pages
 *
 * @param array $pages
 * @param string $lang
 * @param bool $editing
 * @return object
 * @throws Exception
 */
function theme_degrade_compile_pages($pages, $lang, $editing) {
    global $USER;

    $return = (object) ["pages" => [], "css" => [], "js" => []];
    $return->css["_assets/style.css"] = "/theme/degrade/_editor/model/_assets/style.css";
    $previewdataid = optional_param("dataid", false, PARAM_INT);

    foreach ($pages as $page) {
        $info = json_decode($page->info);

        if (isset($info->cachekey)) {
            $cachekey = $info->cachekey;
            $cachekey = str_replace("{USER}", $USER->id, $cachekey);
        } else {
            $cachekey = "homemode_pages";
        }
        $cachekey = "{$cachekey}_{$page->id}_{$lang}_v2";

        $cache = cache::make("theme_degrade", "frontpage_cache");

        if (!$editing && $cache->has($cachekey) && !$previewdataid) {
            $localreturn = $cache->get($cachekey);
        } else {
            $localreturn = (object) ["pages" => [], "css" => [], "js" => []];

            $file = "/theme/degrade/_editor/model/{$page->template}/style.css";
            $localreturn->css["{$page->template}/style.css"] = $file;

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
                $page->info = json_encode($info, JSON_PRETTY_PRINT);
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
                            $localreturn->js["jquery"] = "jquery";
                        } else if ($script == "jqueryui") {
                            $localreturn->js["jqueryui"] = "jqueryui";
                        } else if (strpos($script, "http") === 0) {
                            $js = "require(['jquery'],function($){ $.getScript('{$script}')})";
                            $localreturn->js[$script] = $js;
                        } else {
                            if (file_exists(__DIR__ . "/model/{$page->template}/{$script}")) {
                                $file = "/theme/degrade/_editor/model/{$page->template}/{$script}";
                                $localreturn->js["{$page->template}/{$script}"] = $file;
                            }
                        }
                    }
                }
                if (isset($info->form->styles)) {
                    foreach ($info->form->styles as $style) {
                        if ($style != "bootstrap") {
                            if (file_exists(__DIR__ . "/model/{$page->template}/{$style}")) {
                                $file = "/theme/degrade/_editor/model/{$page->template}/{$style}";
                                $localreturn->css["{$page->template}/{$style}"] = $file;
                            };
                        }
                    }
                }
            }
            $localreturn->page = $page;

            $cache->set($cachekey, $localreturn);
        }

        $return->pages[] = $localreturn->page;
        $return->js = array_merge($return->js, $localreturn->js);
        $return->css = array_merge($return->css, $localreturn->css);
    }

    $return->js = array_values($return->js);
    $return->css = array_values($return->css);

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
    } else if (is_string($in)) {
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
function theme_degrade_load_info_json($filepath) {
    $info = file_get_contents($filepath);
    $data = json_decode($info, true);
    theme_degrade_replace_lang_by_array($data);

    return json_encode($data, JSON_PRETTY_PRINT);
}

/**
 * replace lang by array
 *
 * @param array $data
 * @return void
 * @throws Exception
 */
function theme_degrade_replace_lang_by_array(&$data) {
    $stringlang = theme_degrade_get_strings_langs();

    foreach ($data as &$value) {
        if (is_array($value)) {
            theme_degrade_replace_lang_by_array($value); // Recursive call.
        } else if (is_string($value) && strpos($value, 'lang::') === 0) {
            $langkey = substr($value, 6); // Remove 'lang::'.
            if (isset($stringlang[$langkey])) {
                $value = $stringlang[$langkey];
            } else {
                $value = "[{$langkey}]";
            }
        }
    }
}

/**
 * replace lang by string
 *
 * @param string $data
 * @throws Exception
 */
function theme_degrade_replace_lang_by_string($data) {
    $stringlang = theme_degrade_get_strings_langs();

    preg_match_all('/lang::(\w+)/', $data, $output_data);
    foreach ($output_data[1] as $langkey) {
        if (isset($stringlang[$langkey])) {
            $value = $stringlang[$langkey];
        } else {
            $value = "[{$langkey}]";
        }
        $data = str_replace("lang::{$langkey}", $value, $data);
    }

    return $data;
}

/**
 * get strings langs
 *
 * @return array
 */
function theme_degrade_get_strings_langs() {
    static $stringlang = [];
    if (!$stringlang) {
        if (isset($_GET["current_language"])) {
            $lang = $_GET["current_language"];
        } else {
            $lang = current_language();
        }
        require_once(__DIR__ . "/model/_assets/lang/en.php");
        if (file_exists(__DIR__ . "/model/_assets/lang/{$lang}.php")) {
            require_once(__DIR__ . "/model/_assets/lang/{$lang}.php");
        }
    }

    return $stringlang;
}

/**
 * @param object $course
 * @return object
 * @throws Exception
 */
function theme_degrade_get_editor_course_link($course) {
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

    return (object) [
        "link" => $link, "title" => $title, "access" => $access,
    ];
}

/**
 * List templates
 *
 * @return array
 * @throws Exception
 */
function theme_degrade_list_templates() {
    global $CFG;

    $lang = current_language();
    $files = glob(__DIR__ . "/model/*/info.json");

    $items = [];
    foreach ($files as $file) {
        $dir = pathinfo(pathinfo($file, PATHINFO_DIRNAME), PATHINFO_BASENAME);
        $data = json_decode(theme_degrade_load_info_json($file));
        if (isset($data->disable) && $data->disable) {
            continue;
        }

        if ($data) {
            $items[] = [
                "id" => $dir, "title" => $data->title, "category" => $data->category,
                "image" => "{$CFG->wwwroot}/theme/degrade/_editor/model/{$dir}/print.png",
                "preview" => "{$CFG->wwwroot}/theme/degrade/_editor/model/preview.php?path={$dir}&current_language={$lang}",
            ];
        }
    }

    return $items;
}

/**
 * List templates category
 *
 * @return array
 * @throws Exception
 */
function theme_degrade_list_templates_category() {
    $categorys = [];
    foreach (theme_degrade_list_templates() as $item) {
        $categorys[$item["category"]]["category"] = $item["category"];
        $categorys[$item["category"]]["itens"][] = $item;
    }

    $categorys = array_values($categorys);

    return $categorys;
}

class FormSaveValidator {
    /**
     * Validate the full $_POST["save"] payload based on $json->form->block and $json->form->blocks.
     *
     * - form->block   : single values in $_POST["save"][$key]
     * - form->blocks  : repeatable rows in $_POST["save"][N][$key]
     *
     * Returns only valid items/rows (invalid ones are skipped), plus an error list.
     */
    public static function validate(\stdClass $json): stdClass {
        $save = $_POST["save"];
        $result = [];
        $errors = [];

        $form = isset($json->form) && is_object($json->form) ? $json->form : null;
        if ($form === null) {
            return (object) $result;
        }

        // 1) Validate single block fields: $_POST["save"][$key]
        $blockOut = self::validateBlock($form->block ?? null, $save, $errors);
        foreach ($blockOut as $k => $v) {
            $result[$k] = $v;
        }

        // 2) Validate repeatable rows: $_POST["save"][N][$key]
        $blocksOut = self::validateBlocks($form->blocks ?? null, $save, $errors);
        foreach ($blocksOut as $index => $rowObj) {
            // Keep numeric indexes as properties (prints as [0], [1], ...)
            $result[$index] = $rowObj;
        }

        return (object) $result;
    }

    /**
     * Validate $json->form->block definitions against $_POST["save"][$key].
     * Returns only valid items.
     */
    public static function validateBlock($blockDef, array $save, array &$errors): array {
        $out = [];

        if ($blockDef === null) {
            return $out;
        }

        $defs = self::objectToArray($blockDef);

        foreach ($defs as $key => $fieldDef) {
            if (!is_string($key) || $key === "") {
                continue;
            }

            if (!is_object($fieldDef)) {
                $errors[] = ["path" => "block." . $key, "reason" => "invalid_field_definition"];
                continue;
            }

            $required = !empty($fieldDef->required);
            $hasDefault = property_exists($fieldDef, "default_data");

            $valueExists = array_key_exists($key, $save);
            $value = $valueExists ? $save[$key] : null;

            if (!$valueExists && $hasDefault) {
                $value = $fieldDef->default_data;
                $valueExists = true;
            }

            if (!$valueExists) {
                if ($required) {
                    $errors[] = ["path" => "block." . $key, "reason" => "required_missing"];
                }
                continue;
            }

            [$ok, $clean, $reason] = self::validateValueByFieldDef($value, $fieldDef);
            if (!$ok) {
                $errors[] = ["path" => "block." . $key, "reason" => $reason];
                continue;
            }

            $out[$key] = $clean;
        }

        return $out;
    }

    /**
     * Validate $json->form->blocks definitions against $_POST["save"][N][$key].
     * A row is valid only if ALL defined keys are valid (and required keys exist).
     * Returns only valid rows.
     */
    public static function validateBlocks($blocksDef, array $save, array &$errors): array {
        $out = [];

        if ($blocksDef === null) {
            return $out;
        }

        $defs = self::objectToArray($blocksDef);

        // Detect rows by numeric indexes in $save (0,1,2,... or "0","1"...)
        foreach ($save as $idx => $row) {
            if (!self::isNumericIndex($idx)) {
                continue;
            }

            if (is_object($row)) {
                $row = self::objectToArray($row);
            }

            if (!is_array($row)) {
                $errors[] = ["path" => "blocks[" . $idx . "]", "reason" => "row_not_array"];
                continue;
            }

            $rowOut = [];
            $rowValid = true;

            // Validate all defined fields for this row
            foreach ($defs as $key => $fieldDef) {
                if (!is_string($key) || $key === "") {
                    continue;
                }

                if (!is_object($fieldDef)) {
                    $errors[] = ["path" => "blocks[" . $idx . "]." . $key, "reason" => "invalid_field_definition"];
                    $rowValid = false;
                    break;
                }

                $required = !empty($fieldDef->required);
                $hasDefault = property_exists($fieldDef, "default_data");

                $valueExists = array_key_exists($key, $row);
                $value = $valueExists ? $row[$key] : null;

                if (!$valueExists && $hasDefault) {
                    $value = $fieldDef->default_data;
                    $valueExists = true;
                }

                if (!$valueExists) {
                    if ($required) {
                        $errors[] = ["path" => "blocks[" . $idx . "]." . $key, "reason" => "required_missing"];
                        $rowValid = false;
                        break;
                    }
                    // Not required and missing -> just skip
                    continue;
                }

                [$ok, $clean, $reason] = self::validateValueByFieldDef($value, $fieldDef);
                if (!$ok) {
                    $errors[] = ["path" => "blocks[" . $idx . "]." . $key, "reason" => $reason];
                    $rowValid = false;
                    break;
                }

                $rowOut[$key] = $clean;
            }

            if (!$rowValid) {
                // Entire row rejected
                continue;
            }

            $out[$idx] = (object) $rowOut;
        }

        return $out;
    }

    /**
     * Validate a single value using field definition.
     * Supports valuetype=int|text (defaults to text).
     */
    private static function validateValueByFieldDef($value, \stdClass $fieldDef): array {
        $valuetype = "text";
        if (property_exists($fieldDef, "valuetype") && is_string($fieldDef->valuetype) && $fieldDef->valuetype !== "") {
            $valuetype = strtolower(trim($fieldDef->valuetype));
        }

        if ($valuetype === "int") {
            return self::validateInt($value);
        }

        // Default: treat as text
        return self::validateText($value);
    }

    private static function validateInt($value): array {
        if (is_int($value)) {
            return [true, $value, ""];
        }

        if (is_bool($value) || is_array($value) || is_object($value)) {
            return [false, null, "invalid_int"];
        }

        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        if ($filtered === false) {
            return [false, null, "invalid_int"];
        }

        return [true, (int) $filtered, ""];
    }

    private static function validateText($value): array {
        if (is_array($value) || is_object($value)) {
            return [false, null, "invalid_text"];
        }

        $text = self::sanitizeText($value);

        // If you want "required text" to reject empty strings:
        // if ($text === "") { return [false, null, "empty_text"]; }

        return [true, $text, ""];
    }

    private static function sanitizeText($value): string {
        $text = trim((string) $value);

        // Basic hardening: remove control chars (except \n and \t)
        $text = preg_replace("/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/u", "", $text) ?? "";

        return $text;
    }

    private static function objectToArray($obj): array {
        if (!is_object($obj)) {
            return [];
        }
        return get_object_vars($obj);
    }

    private static function isNumericIndex($idx): bool {
        if (is_int($idx)) {
            return $idx >= 0;
        }
        if (!is_string($idx)) {
            return false;
        }
        return $idx !== "" && ctype_digit($idx);
    }
}

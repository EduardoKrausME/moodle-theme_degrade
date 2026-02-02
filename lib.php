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
 * Theme functions.
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @copyright based on work by 2016 Frédéric Massart - FMCorz.net
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use theme_degrade\admin\setting_scss;
use theme_degrade\autoprefixer;
use theme_degrade\icon_extractor;
use theme_degrade\thumb_generator;

/**
 * Post process the CSS tree.
 *
 * @param string $tree The CSS tree.
 * @param theme_config $theme The theme config object.
 */
function theme_degrade_css_tree_post_processor($tree, $theme) {
    $prefixer = new autoprefixer($tree);
    $prefixer->prefix();
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_degrade_get_extra_scss($theme) {
    $content = "";

    // Sets the background image, and its settings.
    $imageurl = $theme->setting_file_url("backgroundimage", "backgroundimage");
    if (!empty($imageurl)) {
        $content .= "
            @media (min-width: 768px) {
                body {
                    background-image: url('$imageurl'); background-size: cover;
                 }
             }";
    }

    $scsspos = "";
    if (isset($theme->settings->scsspos[5])) {
        $settingscss = new setting_scss("test", "test", "", "");
        $result = $settingscss->validate($theme->settings->scsspos);
        if ($result === true) {
            $scsspos = $theme->settings->scsspos;
        } else {
            $scsspos = "
                #page::before {
                    content: 'theme_degrade::scsspos Error: {$result}';
                    color: #c00;
                    display: block;
                    padding: 8px 12px;
                    white-space: pre-wrap;
                    background: #FFEB3B;
                    margin: 14px;
                    border-radius: 10px;
                    font-weight: bold;
                } ";
        }
    }

    return "{$content}\n{$scsspos}";
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 * @throws Exception
 */
function theme_degrade_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if (strpos($filearea, "editor_") === 0) {
            $fullpath = sha1("/{$context->id}/theme_degrade/{$filearea}/{$args[0]}/{$args[1]}");
            $fs = get_file_storage();
            if ($file = $fs->get_file_by_hash($fullpath)) {
                return send_stored_file($file, 0, 0, false, $options);
            }
        } else {
            $theme = theme_config::load("degrade");
            // By default, theme files must be cache-able by both browsers and proxies.
            if (!array_key_exists("cacheability", $options)) {
                $options["cacheability"] = "public";
            }
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        }
        send_file_not_found();
    } else if ($context->contextlevel == CONTEXT_MODULE) {
        $fs = get_file_storage();
        $fullpath = sha1("/{$context->id}/theme_degrade/{$filearea}/{$args[0]}/{$args[1]}");
        if (!$file = $fs->get_file_by_hash($fullpath)) {
            return false;
        }
        if ($filearea == "theme_degrade_customimage" || $filearea == "theme_degrade_customicon") {
            $thumb = (new thumb_generator())
                ->set_height(($filearea == 'theme_degrade_customicon') ? 50 : 150)
                ->set_cache_filearea("{$filearea}_thumb")
                ->set_cache_itemid($args[0])
                ->get_or_create($file, $context);
            if ($thumb) {
                return send_stored_file($thumb, 0, 0, false, $options);
            }
        }

        // Fallback: image original.
        return send_stored_file($file, 0, 0, false, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Get the current user preferences that are available
 *
 * @return array[]
 */
function theme_degrade_user_preferences(): array {
    return [
        "drawer-open-block" => [
            "type" => PARAM_BOOL,
            "null" => NULL_NOT_ALLOWED,
            "default" => false,
            "permissioncallback" => [core_user::class, "is_current_user"],
        ],
        "drawer-open-index" => [
            "type" => PARAM_BOOL,
            "null" => NULL_NOT_ALLOWED,
            "default" => true,
            "permissioncallback" => [core_user::class, "is_current_user"],
        ],
    ];
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_degrade_get_main_scss_content($theme) {
    global $CFG;
    return file_get_contents("{$CFG->dirroot}/theme/degrade/scss/style.scss");
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 * @throws Exception
 */
function theme_degrade_get_pre_scss($theme) {
    global $CFG;

    $primarycolorscss = "";
    $brandcolor = get_config("theme_boost", "brandcolor");
    if (isset($brandcolor[3]) && preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $brandcolor)) {
        $primarycolorscss = "\$primary: {$brandcolor};\n";
    }
    $courseid = optional_param("courseid", 0, PARAM_INT);
    if ($courseid) {
        $coursecolor = get_config("theme_degrade", "override_course_color_{$courseid}");
        if (isset($coursecolor[3]) && preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $coursecolor)) {
            $primarycolorscss = "\$primary: {$coursecolor};\n";
        }
    }
    $scss = $primarycolorscss;

    if ($CFG->theme == "degrade") {
        $angle = theme_degrade_default("angle", 30);
        $gradient1 = theme_degrade_default("brandcolor_gradient_1", "#f54266");
        $gradient2 = theme_degrade_default("brandcolor_gradient_2", "#3858f9");
        $scss .= "
            .navbar.fixed-top.brandcolor-background {
                background: linear-gradient({$angle}deg, {$gradient1}, {$gradient2}) !important;
            }
            .navbar.fixed-top.brandcolor-background .navbar-content-background {
                background-color: transparent !important;
            }\n";
    } else {
        if ($topscrollbackgroundcolor = get_config("theme_degrade", "top_scroll_background_color")) {
            $scss .= "\$top_scroll_background_color: {$topscrollbackgroundcolor};\n";
        }
    }

    $callbacks = get_plugins_with_function("krausthemes__get_pre_scss");
    foreach ($callbacks as $plugins) {
        foreach ($plugins as $callback) {
            if ($newscss = $callback()) {
                $scss = $newscss;
            }
        }
    }

    // Prepend pre-scss.
    if (isset($theme->settings->scsspre[5])) {
        $settingscss = new setting_scss("test", "test", "", "");
        $result = $settingscss->validate($theme->settings->scsspre);
        if ($result === true) {
            $scss .= $theme->settings->scsspre;
        } else {
            $scss .= "
                #page::before {
                    content: 'theme_degrade::scsspre Error: {$result}';
                    color: #c00;
                    display: block;
                    padding: 8px 12px;
                    white-space: pre-wrap;
                    background: #FFEB3B;
                    margin: 14px;
                    border-radius: 10px;
                    font-weight: bold;
                } ";
        }
    }

    return $scss;
}

/**
 * Function theme_degrade_progress_content
 *
 * @return array
 * @throws coding_exception
 */
function theme_degrade_progress_content() {
    global $USER, $COURSE, $SESSION;

    $completion = new completion_info($COURSE);

    // First, let's make sure completion is enabled.
    if (!$completion->is_enabled()) {
        return ["isprogress" => false];
    }

    if (!$completion->is_tracked_user($USER->id)) {
        $SESSION->notifications[] = (object) [
            "message" => get_string("notenrolledincourse", "theme_degrade"),
            "type" => notification::NOTIFY_WARNING,
        ];
        return ["isprogress" => false];
    }

    // Before we check how many modules have been completed see if the course has.
    if ($completion->is_course_complete($USER->id)) {
        return [
            "isprogress" => true,
            "progress" => 100,
        ];
    }

    // Get the number of modules that support completion.
    $modules = $completion->get_activities();
    $count = count($modules);
    if (!$count) {
        return ["isprogress" => false];
    }

    // Get the number of modules that have been completed.
    $completed = 0;
    foreach ($modules as $module) {
        $data = $completion->get_data($module, true, $USER->id);
        if (($data->completionstate == COMPLETION_INCOMPLETE) || ($data->completionstate == COMPLETION_COMPLETE_FAIL)) {
            $completed += 0;
        } else {
            $completed += 1;
        };
    }

    return [
        "isprogress" => true,
        "progress" => intval(($completed / $count) * 100),
        "progress_completed" => $completed,
        "progress_count" => $count,
    ];
}

/**
 * Function theme_degrade_setting_file_url
 *
 * @param $setting
 * @return bool|moodle_url
 * @throws dml_exception
 */
function theme_degrade_setting_file_url($setting) {
    $filepath = get_config("theme_degrade", $setting);
    if (!$filepath) {
        return false;
    }
    $syscontext = context_system::instance();

    $url = moodle_url::make_pluginfile_url($syscontext->id, "theme_degrade", $setting, 0, "/", $filepath);

    return $url;
}

/**
 * theme_degrade_coursemodule_standard_elements
 *
 * @param moodleform_mod $formwrapper The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 * @throws Exception
 */
function theme_degrade_coursemodule_standard_elements(&$formwrapper, $mform) {
    static $executed = false;
    if ($executed) {
        return;
    }
    $executed = true;

    if ($formwrapper->get_current()->modulename == "label") {
        return;
    }

    global $CFG, $PAGE;
    if ($CFG->theme == "degrade" || $CFG->theme == "eadflix") {
        // Icones.
        $mform->addElement(
            "header",
            "theme_degrade_icons",
            get_string("settings_icons_change_icons", "theme_degrade")
        );

        $filemanageroptions = [
            "accepted_types" => [".svg", ".png", ".jpg", ".jpeg"],
            "maxbytes" => -1,
            "maxfiles" => 1,
        ];

        // Background.
        if (isset($formwrapper->get_current()->coursemodule) && $formwrapper->get_current()->coursemodule) {
            $context = context_module::instance($formwrapper->get_current()->coursemodule);
            $draftitemid = file_get_submitted_draft_itemid("theme_degrade_customimage");
            file_prepare_draft_area(
                $draftitemid,
                $context->id,
                "theme_degrade",
                "theme_degrade_customimage",
                $formwrapper->get_current()->coursemodule
            );
            $formwrapper->set_data(["theme_degrade_customimage" => $draftitemid]);
        }
        $mform->addElement(
            "filemanager",
            "theme_degrade_customimage",
            get_string("settings_icons_upload_image", "theme_degrade"),
            null,
            $filemanageroptions
        );

        $mform->addElement(
            "static",
            "theme_degrade_custom",
            "",
            get_string("settings_icons_upload_image_desc", "theme_degrade")
        );

        // Icon.
        if (isset($formwrapper->get_current()->coursemodule) && $formwrapper->get_current()->coursemodule) {
            $context = context_module::instance($formwrapper->get_current()->coursemodule);

            $draftitemid = file_get_submitted_draft_itemid("theme_degrade_customicon");
            file_prepare_draft_area(
                $draftitemid,
                $context->id,
                "theme_degrade",
                "theme_degrade_customicon",
                $formwrapper->get_current()->coursemodule
            );
            $formwrapper->set_data(["theme_degrade_customicon" => $draftitemid]);
        }
        $filemanageroptions["accepted_types"] = [".svg", ".png"];
        $mform->addElement(
            "filemanager",
            "theme_degrade_customicon",
            get_string("settings_icons_upload_icon", "theme_degrade"),
            null,
            $filemanageroptions
        );

        // Color.
        $mform->addElement(
            "text",
            "theme_degrade_customcolor",
            get_string("settings_icons_color_icon", "theme_degrade"),
            []
        );
        $mform->setType("theme_degrade_customcolor", PARAM_TEXT);
        $PAGE->requires->js_call_amd(
            "theme_degrade/settings",
            "minicolors",
            ["id_theme_degrade_customcolor"]
        );

        $mform->addElement(
            "static",
            "theme_degrade_custom",
            "",
            get_string("settings_icons_color_icon_desc", "theme_degrade")
        );
    }
}

/**
 * Hook the add/edit of the course module.
 *
 * @param stdClass $data Data from the form submission.
 * @param stdClass $course The course.
 * @return stdClass
 * @throws Exception
 */
function theme_degrade_coursemodule_edit_post_actions($data, $course) {
    $context = context_module::instance($data->coursemodule);

    $hascustomimage =
        isset($data->theme_degrade_customimage) &&
        theme_degrade_draft_has_files($data->theme_degrade_customimage);
    $hascustomicon =
        isset($data->theme_degrade_customicon) &&
        theme_degrade_draft_has_files($data->theme_degrade_customicon);

    // Save Background Image (customimage).
    if ($hascustomimage) {
        $options = ["subdirs" => true, "embed" => true];
        $filesave = file_save_draft_area_files(
            $data->theme_degrade_customimage,
            $context->id,
            "theme_degrade",
            "theme_degrade_customimage",
            $data->coursemodule,
            $options
        );

        $name = "theme_degrade_customimage_{$data->coursemodule}";
        set_config($name, $filesave, "theme_degrade");

        cache::make("theme_degrade", "css_cache")->purge();
    }

    // Save Icon (customicon) if user uploaded one.
    if ($hascustomicon) {
        $options = ["subdirs" => true, "embed" => true];
        $filesave = file_save_draft_area_files(
            $data->theme_degrade_customicon,
            $context->id,
            "theme_degrade",
            "theme_degrade_customicon",
            $data->coursemodule,
            $options
        );

        $name = "theme_degrade_customicon_{$data->coursemodule}";
        set_config($name, $filesave, "theme_degrade");

        cache::make("theme_degrade", "css_cache")->purge();
    }

    // Auto-generate icon when:
    //  - customimage was uploaded
    //  - customicon was NOT uploaded (draft has zero files)
    if ($hascustomimage && !$hascustomicon) {
        // Get the saved background image from module context area.
        $fs = get_file_storage();
        $areafiles = $fs->get_area_files(
            $context->id,
            "theme_degrade",
            "theme_degrade_customimage",
            $data->coursemodule,
            "id DESC",
            false
        );

        if (!empty($areafiles)) {
            /** @var stored_file $sourcefile */
            $sourcefile = reset($areafiles);

            // Only raster images can be processed by GD.
            $mimetype = $sourcefile->get_mimetype();
            $supported = ["image/png", "image/jpeg"];
            if (in_array($mimetype, $supported, true)) {
                try {
                    $extractor = new icon_extractor();

                    $tmpdir = make_temp_directory("theme_degrade_icons");
                    $tmpfile = $tmpdir . DIRECTORY_SEPARATOR . "cm{$data->coursemodule}_" . uniqid("", true) . ".png";

                    // Configure extractor defaults (tune if you want).
                    $extractor->set_source_blob($sourcefile->get_content())
                        ->set_corner_tolerance(20)
                        ->set_background_tolerance(20)
                        ->set_crop_padding(2)
                        ->process()
                        ->get_result_png($tmpfile, 45);

                    if (!file_exists($tmpfile) || filesize($tmpfile) <= 0) {
                        @unlink($tmpfile);
                        throw new Exception("File not generated");
                    }
                    $component = "theme_degrade";
                    $filearea = "theme_degrade_customicon";
                    $itemid = $data->coursemodule;

                    $countfiles = $fs->get_area_files($context->id, $component, $filearea, $itemid);
                    if (count($countfiles)===0) {
                        global $USER;
                        $filerecord = [
                            "contextid" => $context->id,
                            "component" => $component,
                            "filearea" => $filearea,
                            "itemid" => $itemid,
                            "filepath" => "/",
                            "filename" => "generated-icon.png",
                            "userid" => $USER->id,
                            "mimetype" => "image/png",
                        ];
                        $fs->create_file_from_pathname($filerecord, $tmpfile);

                        // Keep the same config pattern used by your code.
                        $name = "theme_degrade_customicon_{$data->coursemodule}";
                        set_config($name, 1, "theme_degrade");

                        cache::make("theme_degrade", "css_cache")->purge();
                    }
                } catch (Throwable $e) {
                    // Fail silently: image was saved, but icon generation failed.
                    debugging("Icon generation failed: {$e->getMessage()}", DEBUG_DEVELOPER);
                }
            }
        }
    }

    if (isset($data->theme_degrade_customcolor)) {
        $name = "theme_degrade_customcolor_{$data->coursemodule}";
        set_config($name, $data->theme_degrade_customcolor, "theme_degrade");

        cache::make("theme_degrade", "css_cache")->purge();
    }

    return $data;
}

/**
 * Helper: filemanager draft is always set, so we must check if it has files.
 *
 * @param $draftitemid
 * @return bool
 */
function theme_degrade_draft_has_files($draftitemid): bool {
    if (empty($draftitemid)) {
        return false;
    }
    $info = file_get_draft_area_info($draftitemid);
    return !empty($info["filecount"]) && (int) $info["filecount"] > 0;
}

/**
 * List colors.
 *
 * @return array
 */
function theme_degrade_colors() {
    return [
        "#000428", // Azul Escuro.
        "#070000", // Preto.
        "#1a2a6c", // Azul Escuro.
        "#314755", // Cinza Escuro.
        "#007bc3", // Azul.
        "#007fff", // Azul Neon.
        "#00bf8f", // Verde Azulado.
        "#00c3b0", // Turquesa.
        "#30e8bf", // Verde Claro.
        "#83a4d4", // Azul Claro.
        "#7303c0", // Roxo.
        "#8000ff", // Roxo Neon.
        "#86377b", // Roxo Escuro.
        "#b21f1f", // Vermelho Escuro.
        "#c10f41", // Vermelho.
        "#d12924", // Vermelho Claro.
        "#fc354c", // Vermelho Claro.
        "#ff0000", // Vermelho Brilhante.
        "#ff007f", // Rosa Neon.
        "#ff00ff", // Magenta Brilhante.
        "#f55ff2", // Rosa.
        "#fd81b5", // Rosa Claro.
        "#ff512f", // Laranja Claro.
        "#e65c00", // Laranja.
        "#ff8000", // Laranja Neon.
        "#c99b10", // Amarelo Neon.
        "#997540", // Bege.
    ];
}

/**
 * Change color.
 *
 * @throws dml_exception
 */
function theme_degrade_change_color() {
    $config = get_config("theme_degrade");
    $configboost = get_config("theme_boost");

    if (isset($config->startcolor[5])) {
        $brandcolor = $config->startcolor;
    } else {
        $brandcolor = $configboost->brandcolor;
    }

    set_config("startcolor", "#000", "theme_degrade");
    set_config("footer_background_color", $brandcolor, "theme_degrade");

    theme_reset_all_caches();
}

/**
 * get_config default
 *
 * @param string $configname
 * @param string $default
 * @return string
 * @throws Exception
 */
function theme_degrade_default($configname, $default, $plugin = "theme_degrade") {
    $value = get_config($plugin, $configname);
    if ($value === false) {
        return $default;
    }

    return $value;
}

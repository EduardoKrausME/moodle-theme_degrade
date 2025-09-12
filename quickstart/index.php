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
 * view file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../config.php");
global $CFG, $PAGE, $OUTPUT, $DB, $USER;
require_admin();

if (optional_param("POST", false, PARAM_INT)) {
    require_sesskey();

    // Save configs.
    $configkeys = [
        // Home.
        "homemode" => PARAM_INT,

        // Course.
        "course_summary_banner" => PARAM_INT,

        // Brandcolor.
        "brandcolor" => PARAM_RAW,
        "brandcolor_background_menu" => PARAM_RAW,

        // Accessibility.
        "enable_accessibility" => PARAM_INT,
        "enable_vlibras" => PARAM_INT,

        // Footer.
        "footer_background_color" => PARAM_RAW,
        "footer_title_1" => PARAM_TEXT,
        "footer_html_1" => PARAM_RAW,
        "footer_title_2" => PARAM_TEXT,
        "footer_html_2" => PARAM_RAW,
        "footer_title_3" => PARAM_TEXT,
        "footer_html_3" => PARAM_RAW,
        "footer_title_4" => PARAM_TEXT,
        "footer_html_4" => PARAM_RAW,
    ];
    foreach ($configkeys as $name => $type) {
        $value = optional_param($name, false, $type);
        if ($value !== false) {
            set_config($name, $value, "theme_degrade");
        }
    }

    // Save banners home.
    require_once("../_editor/editor-lib.php");
    $pages = $DB->get_records("theme_degrade_pages", ["local" => "home"]);
    $homemodebanners = optional_param_array("homemode_banners", [], PARAM_TEXT);
    foreach ($homemodebanners as $template) {
        $located = false;
        foreach ($pages as $page) {
            if (isset($page->template[3]) && $page->template == $template) {
                $located = true;
            }
        }

        if (!$located) {
            try {
                editor_create_page($template, $USER->lang, "home");
            } catch (Exception $e) { // phpcs:disable
            }
        }
    }

    // Upload files.
    require_once("{$CFG->libdir}/filelib.php");
    $filefields = [
        "logocompact" => "core",
        "favicon" => "core",
        "banner_course_url" => "theme_degrade",
        "banner_course_file" => "theme_degrade",
        "background_profile_image" => "theme_degrade",
    ];

    $fs = get_file_storage();
    $syscontext = context_system::instance();
    foreach ($filefields as $fieldname => $component) {
        if ($fieldname == "banner_course_url") {
            $hasupload = optional_param($fieldname, null, PARAM_RAW);
            if (!$hasupload) {
                continue;
            }
            $filestring = file_get_contents($hasupload);
            if ($filestring) {
                $fieldname = "banner_course_file";
            } else {
                continue;
            }
        } else {
            $hasupload = !empty($_FILES[$fieldname]) && is_uploaded_file($_FILES[$fieldname]["tmp_name"]);
            $filestring = false;
        }
        if ($hasupload) {
            // Delete old files (if you want to keep a single file).
            $fs->delete_area_files($syscontext->id, $component, $fieldname, 0);
            $filename = clean_param($_FILES[$fieldname]["name"], PARAM_FILE);
            $filerecord = [
                "contextid" => $syscontext->id,
                "component" => $component,
                "filearea" => $fieldname,
                "itemid" => 0,
                "filepath" => "/",
                "filename" => $filename,
            ];

            // Save the new file.
            if ($filestring) {
                $fs->create_file_from_string($filerecord, $filestring);
            } else {
                $fs->create_file_from_pathname($filerecord, $_FILES[$fieldname]["tmp_name"]);
            }

            set_config($fieldname, $filename, $component);
        }
    }

    if (optional_param("homemode", false, PARAM_INT)) {
        $USER->editing = true;
    }

    theme_reset_all_caches();

    redirect(new moodle_url("/"), get_string("quickstart_banner-saved", "theme_degrade"));
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url("/theme/degrade/quickstart/index.php#home");
$PAGE->set_title(get_string("quickstart_title", "theme_degrade"));
$PAGE->set_heading(get_string("quickstart_title", "theme_degrade"));

$PAGE->requires->css("/theme/degrade/quickstart/style.css");
$PAGE->requires->css("/theme/degrade/scss/colors.css");
$PAGE->requires->js("/theme/degrade/quickstart/script.js");
$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin("ui");

if (file_exists("{$CFG->libdir}/editor/tiny/lib.php")) {
    require("{$CFG->libdir}/editor/tiny/lib.php");
    $editor = new \theme_degrade\editor\editor_tiny();
    $editor->head_setup();
}

echo $OUTPUT->header();

echo '<form class="quickstart-content" method="post" enctype="multipart/form-data">';
echo '<input type="hidden" name="POST" value="1" />';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';

$savetheme = optional_param("savetheme", "degrade", PARAM_TEXT);

if ($savetheme == "degrade") {
    require_once("{$CFG->dirroot}/theme/degrade/lib.php");
    $themecolors = theme_degrade_colors();
} else if ($savetheme == "eadflix") {
    require_once("{$CFG->dirroot}/theme/eadflix/lib.php");
    $themecolors = theme_eadflix_colors();
} else {
    $themecolors = [];
}

// Home.
require_once(__DIR__ . "/../_editor/editor-lib.php");
$pages = $DB->get_records("theme_degrade_pages", ["local" => "home"]);
$templates = [];
foreach ($pages as $page) {
    if (isset($page->template[3])) {
        $templates[$page->template] = true;
    }
}
$homemustache = [
    "homemode" => get_config("theme_degrade", "homemode"),
    "templates" => $templates,
    "next" => "courses",
    "all_templates" => list_templates_category(),
];
echo $OUTPUT->render_from_template("theme_degrade/quickstart/home", $homemustache);

// Course.
$bannerfile = theme_degrade_setting_file_url("banner_course_file");
$coursesmustache = [
    "course_summary_banner_0" => get_config("theme_degrade", "course_summary_banner") == 0,
    "course_summary_banner_1" => get_config("theme_degrade", "course_summary_banner") == 1,
    "course_summary_banner_2" => get_config("theme_degrade", "course_summary_banner") == 2,
    "banner_course_file_url" => $bannerfile ? $bannerfile->out() : false,
    "banner_course_file_extensions" => "PNG, JPG",
    "return" => "home",
    "next" => "logos",
];
echo $OUTPUT->render_from_template("theme_degrade/quickstart/courses", $coursesmustache);

// Logos.
$logosmustache = [
    "logocompact_url" => $OUTPUT->get_compact_logo_url(300, 300),
    "logocompact_extensions" => "PNG, SVG, JPG",
    "favicon_url" => $OUTPUT->favicon(),
    "favicon_extensions" => "PNG, SVG, JPG",
    "return" => "courses",
    "next" => "brandcolor",
];
echo $OUTPUT->render_from_template("theme_degrade/quickstart/logos", $logosmustache);

// Brandcolor.
$brandcolormustache = [
    "brandcolor" => get_config("theme_boost", "brandcolor"),
    "brandcolor_background_menu" => get_config("theme_degrade", "brandcolor_background_menu"),
    "htmlselect" => $OUTPUT->render_from_template("theme_degrade/settings/colors", [
        "brandcolor" => true,
        "colors" => $themecolors,
        "defaultcolor" => theme_degrade_default_color("brandcolor", "#1a2a6c", "theme_boost"),
    ]),
    "return" => "logos",
    "next" => "user-profile",
];
echo $OUTPUT->render_from_template("theme_degrade/quickstart/brandcolor", $brandcolormustache);
$PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", ["id_s_theme_boost_brandcolor"]);

// User profile.
$backgroundprofileimage = theme_degrade_setting_file_url("background_profile_image");
$usermustache = [
    "background_profile_image_url" => $backgroundprofileimage ? $backgroundprofileimage->out() : false,
    "background_profile_image_extensions" => "PNG, JPG",
    "return" => "logos",
    "next" => "accessibility",
];
echo $OUTPUT->render_from_template("theme_degrade/quickstart/user-profile", $usermustache);

// Accessibility.
$accessibilitymustache = [
    "enable_accessibility" => get_config("theme_degrade", "enable_accessibility"),
    "lang_has_ptbr" => $CFG->lang == "pt_br",
    "enable_vlibras" => get_config("theme_degrade", "enable_vlibras"),
    "return" => "user-profile",
    "next" => "footer",
];
echo $OUTPUT->render_from_template("theme_degrade/quickstart/accessibility", $accessibilitymustache);

// Footer.
if (file_exists("{$CFG->libdir}/editor/tiny/lib.php")) {
    $footermustache = [
        "footer_background_color" => get_config("theme_degrade", "footer_background_color"),
        "htmlselect" => $OUTPUT->render_from_template("theme_degrade/settings/colors", [
            "footercolor" => true, "colors" => $themecolors,
            "defaultcolor" => theme_degrade_default_color("brandcolor", "#1a2a6c"),
        ]),
        "blocks" => [
            [
                "num" => 1,
                "active" => true,
                "footer_title" => get_config("theme_degrade", "footer_title_1"),
                "footer_html" => get_config("theme_degrade", "footer_html_1"),
            ], [
                "num" => 2,
                "footer_title" => get_config("theme_degrade", "footer_title_2"),
                "footer_html" => get_config("theme_degrade", "footer_html_2"),
            ], [
                "num" => 3,
                "footer_title" => get_config("theme_degrade", "footer_title_3"),
                "footer_html" => get_config("theme_degrade", "footer_html_3"),
            ], [
                "num" => 4,
                "footer_title" => get_config("theme_degrade", "footer_title_4"),
                "footer_html" => get_config("theme_degrade", "footer_html_4"),
            ],
        ],
        "tyni_editor_config" => $editor->tyni_editor_config(),
        "return" => "accessibility",
    ];
    echo $OUTPUT->render_from_template("theme_degrade/quickstart/footer", $footermustache);
    $PAGE->requires->js_call_amd("theme_degrade/settings", "minicolors", ["id_footer_background_color"]);
}
echo "</form>";

echo $OUTPUT->footer();

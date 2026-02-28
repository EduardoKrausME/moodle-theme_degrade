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
 * Class core_hook_output
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade;

use cache;
use core\hook\output\before_html_attributes;
use Exception;
use moodle_url;

/**
 * Class core_hook_output
 *
 * @package theme_degrade
 */
class hook_callbacks {

    /**
     * Function before_html_attributes
     *
     * @throws Exception
     */
    public static function before_html_attributes(before_html_attributes $hook): void {
        global $CFG;

        $theme = $CFG->theme;
        if (isset($_SESSION["SESSION"]->theme)) {
            $theme = $_SESSION["SESSION"]->theme;
        }
        if ($theme != "degrade") {
            return;
        }

        $hook->add_attribute("data-themename", "degrade");
        $hook->add_attribute("data-background-color", get_config("theme_boost", "brandcolor"));
    }

    /**
     * Function before_footer_html_generation
     *
     * @throws Exception
     */
    public static function before_footer_html_generation() {
        global $CFG, $COURSE, $SITE;

        static $processed = false;
        if ($processed) {
            return;
        }
        $processed = true;

        $theme = $CFG->theme;
        if (isset($_SESSION["SESSION"]->theme)) {
            $theme = $_SESSION["SESSION"]->theme;
        }
        if ($theme != "degrade" && $theme != "eadflix") {
            return;
        }

        self::background_profile_image();
        self::acctoolbar();
        self::vlibras();

        if ($COURSE->id == $SITE->id) {
            return;
        }

        self::course_personalization();
    }

    /**
     * Background profile image
     *
     * @return void
     * @throws Exception
     */
    private static function background_profile_image() {
        $cache = cache::make("theme_degrade", "css_cache");
        $cachekey = "background_profile_image";
        if ($cache->has($cachekey)) {
            $css = $cache->get($cachekey);
            echo "<style>{$css}</style>";
        } else {
            $backgroundprofileurl = theme_degrade_setting_file_url("background_profile_image");
            if ($backgroundprofileurl) {
                $profileimagecss = ":root { --background_profile: url({$backgroundprofileurl}); }";

                $cache->set($cachekey, $profileimagecss);
                $css = $profileimagecss;
                echo "<style>{$css}</style>";
            }
        }
    }

    /**
     * ACCtoolbar
     *
     * @return void
     * @throws Exception
     */
    private static function acctoolbar() {
        if (get_config("theme_degrade", "enable_accessibility")) {
            global $PAGE;
            $PAGE->requires->strings_for_js(["acctoolbar_image_without_alt"], "theme_degrade");
            $PAGE->requires->js_call_amd("theme_degrade/acctoolbar", "init");
        }
    }

    /**
     * VLibras only Brasiliam
     *
     * @return void
     * @throws Exception
     */
    private static function vlibras() {
        global $CFG, $OUTPUT;

        $vlibras = get_config("theme_degrade", "enable_vlibras") && $CFG->lang == "pt_br";
        if ($vlibras) {
            echo $OUTPUT->render_from_template("theme_degrade/settings/vlibras", [
                "position" => get_config("theme_degrade", "vlibras_position") ?: "R",
                "avatar" => get_config("theme_degrade", "vlibras_avatar") ?: "icaro",
            ]);
        }
    }

    /**
     * Function course_personalization
     *
     * @return void
     * @throws Exception
     */
    public static function course_personalization() {
        global $COURSE, $DB, $OUTPUT, $PAGE;

        $images = ["blocks" => [], "icons" => [], "colors" => []];

        $cache = cache::make("theme_degrade", "css_cache");
        $cachekey = "theme_degrade_customimages_{$COURSE->id}";
        if ($cache->has($cachekey)) {
            $images = json_decode($cache->get($cachekey), true);
        } else {
            // Backgrounds images modules.
            $sql = "
                SELECT itemid, contextid, filename
                  FROM {files}
                 WHERE component LIKE 'theme_degrade'
                   AND filearea  LIKE 'theme_degrade_customimage'
                   AND filename  LIKE '__%'";
            $customimages = $DB->get_records_sql($sql);
            foreach ($customimages as $customimage) {
                $imageurl = moodle_url::make_pluginfile_url(
                    $customimage->contextid,
                    "theme_degrade",
                    "theme_degrade_customimage",
                    $customimage->itemid,
                    "/",
                    $customimage->filename
                );
                $images["blocks"][] = ["cmid" => $customimage->itemid, "thumb" => $imageurl->out()];
            }

            // Icons modules.
            $sql = "
                SELECT itemid, contextid, filename
                  FROM {files}
                 WHERE component LIKE 'theme_degrade'
                   AND filearea  LIKE 'theme_degrade_customicon'
                   AND filename  LIKE '__%'";
            $customicons = $DB->get_records_sql($sql);
            foreach ($customicons as $customicon) {
                $imageurl = moodle_url::make_pluginfile_url(
                    $customicon->contextid,
                    "theme_degrade",
                    "theme_degrade_customicon",
                    $customicon->itemid,
                    "/",
                    $customicon->filename
                );

                $images["icons"][] = ["cmid" => $customicon->itemid, "thumb" => $imageurl->out()];
            }

            // Icons Color.
            $sql = "
                SELECT *
                  FROM {config_plugins}
                 WHERE plugin  = 'theme_degrade'
                   AND name LIKE 'theme_degrade_customcolor_%'";
            $customcolors = $DB->get_records_sql($sql);
            foreach ($customcolors as $customcolor) {
                $moduleid = str_replace("theme_degrade_customcolor_", "", $customcolor->name);

                $images["colors"][] = ["cmid" => $moduleid, "color" => $customcolor->value];
            }

            $cache->set($cachekey, json_encode($images));
        }

        foreach ($images["blocks"] as $block) {
            $PAGE->requires->js_call_amd("theme_degrade/blocks", "create", [$block["cmid"], $block["thumb"]]);
        }
        foreach ($images["icons"] as $icons) {
            $PAGE->requires->js_call_amd("theme_degrade/blocks", "icons", [$icons["cmid"], $icons["thumb"]]);
        }
        foreach ($images["colors"] as $color) {
            $PAGE->requires->js_call_amd("theme_degrade/blocks", "color", [$color["cmid"], $color["color"]]);
        }

        $requesturi = $_SERVER["REQUEST_URI"];
        $hasuri = strpos($requesturi, "course/view.php") || strpos($requesturi, "course/section.php");
        if ($hasuri) {
            $header = (object) [];
            $header->hasnavbarcourse = false;
            $header->hasbannercourse = false;
            $header->hasnavbar = empty($PAGE->layout_options["nonavbar"]);
            $header->navbar = $OUTPUT->navbar();
            $header->headeractions = $PAGE->get_header_actions();

            $showcoursesummary = get_config("theme_degrade", "course_summary_banner");
            $showcoursesummarycourse = get_config("theme_degrade", "course_summary_banner_{$COURSE->id}");
            if ($showcoursesummarycourse !== false) {
                $showcoursesummary = $showcoursesummarycourse;
            }

            if ($showcoursesummary) {
                if ($showcoursesummary == 1) {
                    $header->hasnavbarcourse = true;
                    $categoryname = $DB->get_field("course_categories", "name", ["id" => $PAGE->course->category]);
                    $header->categoryname = format_string($categoryname);
                    $header->overviewfiles = $OUTPUT->get_course_image();

                    if (has_capability("moodle/category:manage", $PAGE->context)) {
                        $cache = \cache::make("theme_degrade", "course_cache");
                        $cachekey = "header_details_{$PAGE->course->id}";
                        if ($cache->has($cachekey)) {
                            $header->details = json_decode($cache->get($cachekey));
                        } else {
                            $header->details = $OUTPUT->get_details();
                            $cache->set($cachekey, json_encode($header->details));
                        }
                    }
                }
                if ($showcoursesummary == 2) {
                    $bannerfileurl = $OUTPUT->get_course_image();
                    if ($bannerfileurl) {
                        $categoryname = $DB->get_field("course_categories", "name", ["id" => $PAGE->course->category]);
                        $header->categoryname = format_string($categoryname);
                        $header->hasbannercourse = true;
                        $header->banner_course_file_url = $bannerfileurl;
                    }
                }
            }

            $header->hasnosumary = !$header->hasbannercourse && !$header->hasnavbarcourse;

            if ($PAGE->user_is_editing() && has_capability("moodle/site:config", $PAGE->context)) {
                $url = new moodle_url("/theme/degrade/quickstart/course-banner.php", ["courseid" => $COURSE->id]);
                $header->headeractions_edit = true;
                $header->headeractions_edit_href = $url;
                $header->headeractions_banner_courseid = $COURSE->id;
            }

            if ($header->hasnavbarcourse || $header->hasbannercourse || $header->headeractions_edit) {
                $header->contextheader = $OUTPUT->context_header();
                echo $OUTPUT->render_from_template("theme_degrade/core/course_full_header", $header);
            }
        }
    }
}

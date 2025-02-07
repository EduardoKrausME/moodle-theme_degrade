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
 * Frontpage template data
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\template;

use theme_degrade\fonts\font_util;

/**
 * frontapage_data.php
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontapage_data {

    /**
     * Function topo
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function topo() {
        $cache = \cache::make("theme_degrade", "layout_cache");
        $cachekey = "topo-" . current_language();
        if ($cache->has($cachekey)) {
            $data = $cache->get($cachekey);
        }
        if (!isset($data["logourl_header"])) {
            $data = [
                "logourl_header" => theme_degrade_get_logo("header"),
                "top_scroll" => theme_degrade_get_setting("top_scroll") ? 1 : 0,
            ];
            $cache->set($cachekey, $data);
        }

        return $data;

    }

    /**
     * Function slideshow
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function slideshow() {
        $cache = \cache::make("theme_degrade", "layout_cache");
        $cachekey = "slideshow-" . current_language();
        if ($cache->has($cachekey)) {
            $data = $cache->get($cachekey);
        }
        if (!isset($data["slideshow_numslides"])) {
            $slideshownumslides = theme_degrade_get_setting("slideshow_numslides");
            $data = [
                "slideshow_numslides" => $slideshownumslides,
                "slideshow_edit_settings" => self::edit_settings("theme_degrade_slideshow"),
                "slideshow_slides" => [],
            ];

            if (!$slideshownumslides) {
                return $data;
            }

            for ($i = 1; $i <= $slideshownumslides; $i++) {
                $slideshowimage = theme_degrade_get_setting_image("slideshow_image_{$i}");
                $slideshowurl = theme_degrade_get_setting("slideshow_url_{$i}", true);
                $slideshowtext = theme_degrade_get_setting("slideshow_text_{$i}", true);

                if ($slideshowimage) {
                    $data["slideshow_slides"][] = [
                        "slideshow_image" => $slideshowimage,
                        "slideshow_url" => $slideshowurl,
                        "slideshow_text" => $slideshowtext,
                        "slideshow_num" => $i,
                    ];
                } else {
                    $data["slideshow_numslides"]--;
                }
            }
            $cache->set($cachekey, $data);
        }

        return $data;
    }

    /**
     * Function about
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function about() {
        $cache = \cache::make("theme_degrade", "layout_cache");
        $cachekey = "about-" . current_language();
        if ($cache->has($cachekey)) {
            $data = $cache->get($cachekey);
        }
        if (!isset($data["frontpage_about_enable"])) {
            global $OUTPUT;

            $frontpageaboutenable = theme_degrade_get_setting("frontpage_about_enable");

            $logo = theme_degrade_get_setting_image("frontpage_about_logo");
            if (!$logo) {
                /** @var \moodle_url $url */
                $url = $OUTPUT->get_logo_url();
                if ($url) {
                    $logo = $url->out(false);
                }
            }

            $data = [
                "frontpage_about_enable" => $frontpageaboutenable,
                "frontpage_about_logo" => $logo,
                "frontpage_about_title" => theme_degrade_get_setting("frontpage_about_title"),
                "frontpage_about_description" => theme_degrade_get_setting("frontpage_about_description", FORMAT_HTML),
                "frontpage_about_edit_settings" => self::edit_settings("theme_degrade_about"),
                "about_numbers" => [],
            ];
            if (!$frontpageaboutenable) {
                return $data;
            }

            for ($i = 1; $i <= 4; $i++) {
                $number = theme_degrade_get_setting("frontpage_about_number_{$i}");
                $text = theme_degrade_get_setting("frontpage_about_text_{$i}");

                if ($number && $text) {
                    $data["about_numbers"][] = [
                        "frontpage_about_number" => $number,
                        "frontpage_about_text" => $text,
                    ];
                }
            }

            $cache->set($cachekey, $data);
        }

        return $data;
    }

    /**
     * Function home_html
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function home_html() {
        $cache = \cache::make("theme_degrade", "layout_cache");
        $cachekey = "home_html-" . current_language();
        if ($cache->has($cachekey)) {
            $data = $cache->get($cachekey);
        }
        if (!isset($data["home_html"])) {
            $hometype = get_config("theme_degrade", "home_type");
            $chave = optional_param("chave", false, PARAM_TEXT);

            if ($hometype == 1 || $chave == "home") {
                if ($chave == "home") {
                    $htmldata = optional_param("htmldata", false, PARAM_RAW);
                } else {
                    $lang = current_language();
                    $htmldata = get_config("theme_degrade", "home_htmleditor_{$lang}");
                    if (!isset($htmldata[3])) {
                        $htmldata = get_config("theme_degrade", "home_htmleditor_all");
                    }
                }

                $htmldata = htmldata::vvveb__change_courses($htmldata);

                $htmldata .= font_util::print_only_unique();
                $data = [
                    "home_html" => true,
                    "home_htmleditor" => $htmldata,
                ];
            } else {
                $data = [
                    "home_html" => false,
                ];
            }

            $cache->set($cachekey, $data);
        }

        return $data;
    }

    /**
     * Function edit_settings
     *
     * @param $hasteg
     *
     * @return bool|string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function edit_settings($hasteg) {
        global $CFG;

        $settingsedit = false;
        if (has_capability("moodle/site:config", \context_system::instance())) {
            $lang = get_string("{$hasteg}_editbooton", "theme_degrade");
            $settingsedit = "<a href='{$CFG->wwwroot}/admin/settings.php?section=themesettingdegrade#{$hasteg}'";
            $settingsedit .= "   class='bt-edit-setting' target='_blank'>";
            $settingsedit .= "    <img src = '{$CFG->wwwroot}/theme/degrade/pix/edit.svg' >";
            $settingsedit .= "    {$lang}";
            $settingsedit .= "</a>";
        }

        return $settingsedit;
    }
}

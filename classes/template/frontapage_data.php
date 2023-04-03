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

namespace theme_degrade\template;

/**
 * slideshow.php
 *
 * @package     theme_degrade
 * @copyright   2023 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontapage_data {

    public static function topo() {
        return [
            'logourl_header' => theme_degrade_get_logo("header")
        ];
    }

    /**
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function slideshow() {
        $slideshownumslides = theme_degrade_get_setting("slideshow_numslides");
        $data = [
            "slideshow_numslides" => $slideshownumslides,
            "slideshow_edit_settings" => self::edit_settings("theme_degrade_slideshow"),
            "slideshow_slides" => []
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

        return $data;
    }

    /**
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function about() {
        global $OUTPUT;


        $frontpageaboutenable = theme_degrade_get_setting("frontpage_about_enable");

        $logo = theme_degrade_get_setting_image("frontpage_about_logo");
        if (!$logo) {
            $url = $OUTPUT->get_logo_url();
            if ($url) {
                $logo = $url->out(false);
            }
        }

        $data = [
            "frontpage_about_enable" => $frontpageaboutenable,
            "frontpage_about_logo" => $logo,
            "frontpage_about_title" => theme_degrade_get_setting("frontpage_about_title"),
            "frontpage_about_description" => theme_degrade_get_setting("frontpage_about_description", false),
            "frontpage_about_edit_settings" => self::edit_settings("theme_degrade_about"),
            "about_numbers" => []
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

        return $data;
    }

    /**
     * @param $hasteg
     *
     * @return bool|string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private static function edit_settings($hasteg) {
        global $CFG;

        $settingsedit = false;
        if (has_capability('moodle/site:config', \context_system::instance())) {
            $editlang = get_string("{$hasteg}_editbooton", "theme_degrade");
            $settingsedit = "<a href='{$CFG->wwwroot}/admin/settings.php?section=themesettingdegrade#{$hasteg}'";
            $settingsedit .= "   class='bt-edit-setting' target='_blank'>";
            $settingsedit .= "    <img src = '{$CFG->wwwroot}/theme/degrade/pix/edit.svg' >";
            $settingsedit .= "    {$editlang}";
            $settingsedit .= "</a>";
        }

        return $settingsedit;
    }
}

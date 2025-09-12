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

namespace theme_degrade\output;

use Exception;

/**
 * Renderers footer
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @copyright based on work by 2012 Bas Brands, www.basbrands.nl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class footer_renderer { // phpcs:disable moodle.PHP.ForbiddenGlobalUse.BadGlobal
    /**
     * Mustache data
     *
     * @return array
     * @throws Exception
     */
    public static function mustache_data() {
        global $PAGE;

        $cache = \cache::make("theme_degrade", "data_cache");
        $cachekey = "mustache_data";
        if (!$PAGE->user_is_editing() && $cache->has($cachekey)) {
            return json_decode($cache->get($cachekey), true);
        }

        $brandcolor = get_config("theme_boost", "brandcolor");
        $footercolor = theme_degrade_default_color("footer_background_color", $brandcolor);

        $data = [
            "footercount" => 0,
            "footercontents" => [],
            "footer_background_color" => $footercolor,
            "footer_background_text_color" => theme_degrade_get_footer_color($footercolor, "#333", false),
            "footer_show_copywriter" => get_config("theme_degrade", "footer_show_copywriter"),
        ];
        for ($i = 1; $i <= 4; $i++) {
            $footertitle = get_config("theme_degrade", "footer_title_{$i}");
            $footerhtml = get_config("theme_degrade", "footer_html_{$i}");
            if (isset($footerhtml[5])) {
                $data["footercount"]++;
                $data["footercontents"][] = [
                    "footertitle" => $footertitle,
                    "footerhtml" => $footerhtml,
                ];
            }
        }

        $cache->set($cachekey, json_encode($data));

        return $data;
    }
}

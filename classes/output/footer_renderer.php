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
 * phpcs:disable moodle.PHP.ForbiddenGlobalUse.BadGlobal
 *
 * Renderers footer
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @copyright based on work by 2012 Bas Brands, www.basbrands.nl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class footer_renderer {
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

        $data = [
            "footercount" => 0,
            "footercontents" => [],
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

    /**
     * get_footer_color
     *
     * @param string $bgcolor
     * @param string $darkcolor
     * @param string $lightcolor
     * @return float|null
     */
    public static function get_footer_color($bgcolor, $darkcolor, $lightcolor) {
        // Remove o # e garante que tenha 6 caracteres.
        $bgcolor = ltrim($bgcolor, "#");
        if (strlen($bgcolor) !== 6) {
            return 1; // Cor inválida.
        }

        // Converte para números (base 16).
        $r = hexdec(substr($bgcolor, 0, 2));
        $g = hexdec(substr($bgcolor, 2, 2));
        $b = hexdec(substr($bgcolor, 4, 2));

        // Calcula a luminância percebida (fórmula de acessibilidade W3C).
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.6 ? $darkcolor : $lightcolor;
    }
}

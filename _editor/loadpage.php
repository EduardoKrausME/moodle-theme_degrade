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
 * Editor.
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once('./function.php');
$PAGE->set_context(\context_system::instance());

$chave = required_param('chave', PARAM_TEXT);
$editlang = required_param('editlang', PARAM_TEXT);

if (file_exists(__DIR__ . "/_default/default-{$chave}.html")) {
    $html = get_config("theme_degrade", "{$chave}_htmleditor_{$editlang}");
    if (isset($html[40])) {

        $html = vvveb__add_css($chave, $html);
        die($html);
    } else {
        $html = "<link href=\"{wwwroot}/theme/degrade/_editor/_default/bootstrap-vvveb.css\" rel=\"stylesheet\">\n\n";
        $html .= file_get_contents(__DIR__ . "/_default/default-{$chave}.html");

        $html = vvveb__add_css($chave, $html);
        $html = vvveb__changue_langs($html);
        $html = vvveb__change_courses($html);
        die($html);
    }
}

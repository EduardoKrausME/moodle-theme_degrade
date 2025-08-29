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
 * The embedded layout.
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:ignore
defined('MOODLE_INTERNAL') || die;


echo "{$OUTPUT->doctype()}
<html {$OUTPUT->htmlattributes()}>
<head>
    <title>{$OUTPUT->page_title()}</title>
    <link rel=\"shortcut icon\" href=\"{$OUTPUT->favicon()}\"/>
    {$OUTPUT->standard_head_html()}
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
</head>

<body
<body data-layout=\"embedded\" {$OUTPUT->body_attributes([theme_degrade_get_body_class()])}>
{$OUTPUT->standard_top_of_body_html()}
<div id=\"page\">
    <div id=\"page-content\" class=\"clearfix\">
        {$OUTPUT->main_content()}
    </div>
</div>
{$OUTPUT->standard_end_of_body_html()}
</body>
</html>";

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
 * The one column layout.
 *
 * @package   theme_degrade
 * @copyright 2018 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Get the HTML for the settings bits.
$html = theme_degrade_get_html_for_settings($OUTPUT, $PAGE);


$PAGE->requires->jquery();
$PAGE->requires->js('/theme/degrade/js/degrade.js');

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo theme_degrade_get_favicon(); ?>"/>
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600"/>
</head>

<body <?php echo $OUTPUT->body_attributes(theme_degrade_get_classes('one-column', $COURSE)); ?>>

<div id="page-content" class="row-fluid">
    <section id="region-main" class="span12">
        <?php
        echo $OUTPUT->course_content_header();
        echo $OUTPUT->main_content();
        echo $OUTPUT->course_content_footer();
        ?>
    </section>
</div>

<?php echo $OUTPUT->standard_end_of_body_html() ?>

</body>
</html>

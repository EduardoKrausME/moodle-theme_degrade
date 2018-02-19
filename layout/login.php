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
 * The login layout.
 *
 * @package   theme_degrade
 * @copyright 2018 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get the HTML for the settings bits.
$html = theme_degrade_get_html_for_settings ( $OUTPUT, $PAGE );

if ( !empty( $PAGE->theme->settings->favicon ) )
    $favicon = $PAGE->theme->setting_file_url ( 'favicon', 'favicon' );
else
    $favicon = $OUTPUT->favicon ();

$PAGE->requires->css ( '/theme/degrade/style/login-v4.css' );
$PAGE->requires->css ( '/theme/degrade/style/font-awesome.css' );

echo $OUTPUT->doctype () ?>
<html <?php echo $OUTPUT->htmlattributes (); ?>>
<head>
    <title><?php echo $OUTPUT->page_title (); ?></title>
    <link rel="shortcut icon" href="<?php echo $favicon; ?>"/>
    <?php echo $OUTPUT->standard_head_html () ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600"/>
</head>


<?php
$additionalclasses = array( 'one-column' );
if ( isloggedin () )
    $additionalclasses[] = 'logado';
if ( isset( $COURSE->id ) && $COURSE->id != $CFG->defaulthomepage && $COURSE->id > 1 )
    $additionalclasses[] = 'area-courses';

$additionalclasses[] = 'theme-' . $this->page->theme->settings->background_color;

?>
<body <?php echo $OUTPUT->body_attributes ( $additionalclasses ); ?>>

<div id="loginone-wrapper">
    <div class="login-content text-center">
        <div class="login-inner-wrapper-2">
            <div class="mb-2">
                <span class="login-icone"><i class="fa fa-graduation-cap"></i></span>
            </div>
            <div role="main"><span id="maincontent"></span>
                <div class="m-y-3 hidden-sm-down"></div>
                <div class="row">
                    <div class="col-xl-6 push-xl-3 m-2-md col-sm-8 push-sm-2">
                        <div class="card">
                            <div class="card-block">
                                <section id="region-main" class="span12">
                                    <?php
                                    echo $OUTPUT->course_content_header ();
                                    echo $OUTPUT->main_content ();
                                    echo $OUTPUT->course_content_footer ();
                                    ?>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>

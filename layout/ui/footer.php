<?php

if ( empty( $PAGE->theme->settings->footnote ) )
    $class1 = 'span6';
else
    $class1 = 'span3';
?>

<div class="row-fluid">
    <!-- Widget 1 -->
    <div class="<?php echo $class1 ?>">
        <div id="footer-left" class="block-region">
            <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
            <div class="region-content">
                <?php
                echo $OUTPUT->login_info();
                //echo $OUTPUT->home_link();
                echo $OUTPUT->standard_footer_html();
                ?>
            </div>
        </div>
    </div>

    <?php
    if( !empty($PAGE->theme->settings->footnote) ){?>
        <!-- widget 2 -->
        <div class="span6">
            <div id="footer-middle" class="block-region">
                <div class="region-content">
                    <?php echo $PAGE->theme->settings->footnote; ?>
                </div>
            </div>
        </div> <?php
    }?>

    <!-- Widget 3 -->
    <div class="<?php echo $class1 ?>">
        <div id="footer-right" class="block-region">
            <div class="region-content">

                <?php

                if ( !empty( $PAGE->theme->settings->android ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->android . '"><span
                        class="footer-icon android"><i class="fa fa-android"></i></span></a>';
                if ( !empty( $PAGE->theme->settings->apple ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->apple . '"><span
                        class="footer-icon apple"><i class="fa fa-apple"></i></span></a> ';
                if ( !empty( $PAGE->theme->settings->youtube ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->youtube . '"><span
                        class="footer-icon youtube"><i class="fa fa-youtube"></i></span></a> ';
                if ( !empty( $PAGE->theme->settings->pinterest ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->pinterest . '"><span
                        class="footer-icon pinterest"><i class="fa fa-pinterest"></i></span></a> ';
                if ( !empty( $PAGE->theme->settings->linkedin ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->linkedin . '"><span
                        class="footer-icon linkedin"><i class="fa fa-linkedin"></i></span></a> ';
                if ( !empty( $PAGE->theme->settings->instagram ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->instagram . '"><span
                        class="footer-icon instagram"><i class="fa fa-instagram"></i></span></a> ';
                if ( !empty( $PAGE->theme->settings->flickr ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->flickr . '"><span
                        class="footer-icon flickr"><i class="fa fa-flickr"></i></span></a> ';
                if ( !empty( $PAGE->theme->settings->googleplus ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->googleplus . '"><span
                        class="footer-icon googleplus"><i class="fa fa-google-plus"></i></span></a> ';
                if ( !empty( $PAGE->theme->settings->twitter ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->twitter . '"><span
                        class="footer-icon twitter"><i class="fa fa-twitter"></i></span></a> ';
                if ( !empty( $PAGE->theme->settings->facebook ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->facebook . '"><span
                        class="footer-icon facebook"><i class="fa fa-facebook"></i></span></a> ';
                if ( !empty( $PAGE->theme->settings->website ) )
                    echo '<a target="_blank" href="' . $PAGE->theme->settings->website . '"><span
                        class="footer-icon website"><i class="fa fa-globe"></i></span></a> ';
                ?>

            </div>
        </div>
    </div>
</div>


<?php
if ( !empty( $PAGE->theme->settings->footdeveloper ) ) { ?>
    <div class="developer">
        <p>
            Desenvolvido com <span class="heart">♥︎</span> por
            <a target="_blank" href="https://www.eduardokraus.com/">Eduardo Kraus</a>
        </p>
    </div>
<?php } ?>
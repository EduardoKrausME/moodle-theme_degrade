<?php
if (isloggedin()) {

    $userpicture = new \user_picture($USER);
    $userpicture->size = 1;
    $profileimageurl = $userpicture->get_url($PAGE)->out(false);
    ?>

    <li class="icons">
        <a href="<?php echo $CFG->wwwroot ?>/message">
            <i class="fa fa-comment"></i>
            <!--span class="toolbar-button-badge">3</span-->
        </a>
    </li>
    <li class="usericon">
        <a href="<?php echo $CFG->wwwroot ?>/user/profile.php">
            <img src="<?php echo $profileimageurl ?>" alt="<?php echo fullname($USER) ?>">
        </a>
    </li>


    <?php
}
?>
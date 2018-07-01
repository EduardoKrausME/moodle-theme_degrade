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
 * The user-right layout.
 *
 * @package   theme_degrade
 * @copyright 2018 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if (isloggedin()) {

    $userpicture = new \user_picture($USER);
    $userpicture->size = 1;
    $profileimageurl = $userpicture->get_url($PAGE)->out(false);
    ?>
    <ul class="nav pull-right">
        <li class="icons">
            <a href="<?php echo $CFG->wwwroot ?>/message">
                <i class="fa fa-comment"></i>
            </a>
        </li>
        <li class="dropdown usericon">
            <a href="<?php echo $CFG->wwwroot ?>/user/profile.php"
               class="dropdown-toggle" data-toggle="dropdown"
               title="<?php echo fullname($USER) ?>">
                <img src="<?php echo $profileimageurl ?>" alt="<?php echo fullname($USER) ?>">
            </a>
            <ul class="dropdown-menu dropdown-user-menu">
                <li>
                    <a href="<?php echo $CFG->wwwroot . '/user/profile.php' ?>"><?php echo get_string('viewprofile') ?></a>
                </li>
                <li>
                    <a href="<?php echo $CFG->wwwroot . 'user/edit.php' ?>"><?php echo get_string('editmyprofile') ?></a>
                </li>
                <li>
                    <a href="<?php echo $CFG->wwwroot . '/user/preferences.php' ?>"><?php echo get_string('preferences') ?></a>
                </li>
                <?php
                if ( !function_exists ( "user_convert_text_to_menu_items" ) ) {
                    require ("{$CFG->dirroot}/user/lib.php");
                }
                $items = user_convert_text_to_menu_items($CFG->customusermenuitems, $PAGE);
                foreach ($items as $item) {
                    echo "<li><a title=\"{$item->title}\"
                                 href=\"{$item->url->out(true)}\">{$item->title}</a></li>";
                }
                if ($CFG->version > 2016120500 &&
                    !is_role_switched($COURSE->id) &&
                    has_capability('moodle/role:switchroles', context_course::instance($COURSE->id))) {
                    $returnurl = theme_degrade_get_current_page_url();
                    $url = $CFG->wwwroot . '/course/switchrole.php?id=' . $COURSE->id . '&switchrole=-1&returnurl=' . $returnurl;
                    $text = get_string('switchroleto');

                    echo "<li>
                              <a href=\"{$url}\">{$text}</a>
                          </li>";
                } ?>
                <li>
                    <?php $link = $CFG->wwwroot . '/login/logout.php?sesskey=' . sesskey()?>
                    <a href="<?php echo $link ?>"><?php echo get_string('logout') ?></a>
                </li>
            </ul>
        </li>

        <li><?php echo $OUTPUT->page_heading_menu(); ?></li>

    </ul>
    <?php
}
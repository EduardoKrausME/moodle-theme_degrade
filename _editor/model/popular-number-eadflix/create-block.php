<?php

use core_course\external\course_summary_exporter;

/**
 * @param $page
 * @return void
 */
function popular_number_eadflix_createblocks($page) {
    global $DB, $OUTPUT, $CFG;

    $page->info = json_decode($page->info);

    $num = 0;
    $blocks = "";
    if (isset($page->info->savedata[0]->courseid)) {
        foreach ($page->info->savedata as $course) {
            $course = $DB->get_record("course", array("id" => $course->courseid));
            if ($course) {
                $course = new core_course_list_element($course);

                $courseimage = course_summary_exporter::get_course_image($course);
                if (!$courseimage) {
                    $courseimage = $OUTPUT->get_default_image_for_courseid($course->id);
                }

                $courseinfo = theme_degrade_get_editor_course_link($course);
                $num++;

                $extraclass = $num >= 10 ? " eadflix-nunber-dozen" : "";
                $blocks .= "
                   <div class=\"top-courses-item slider-item\">
                       <div class=\"top-courses-inner top-courses-number\">
                           <div class=\"eadflix-nunber{$extraclass}\">{$num}</div>
                           <a href=\"{$courseinfo->link}\"
                              style=\"
                                    background:          url('{$courseimage}');
                                    display:             block;
                                    width:               200px;
                                    height:              320px;
                                    background-size:     cover;
                                    background-position: center;
                                    background-repeat:   no-repeat;\">
                           </a>
                       </div>
                   </div>\n";
            }
        }
    }

    return "<div class=\"owl-courses-content owl-carousel\">{$blocks}</div>";
}

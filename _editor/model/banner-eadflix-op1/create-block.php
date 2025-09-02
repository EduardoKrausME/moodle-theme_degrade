<?php

use core_course\external\course_summary_exporter;

function banner_eadflix_op1_createblocks($page) {
    global $DB, $OUTPUT;

    $page->info = json_decode($page->info);

    $blocks = "";
    if (isset($page->info->savedata)) {
        foreach ($page->info->savedata as $data) {
            $course = $DB->get_record("course", ["id" => $data->courseid]);
            if ($course) {
                $course = new core_course_list_element($course);

                $courseimage = course_summary_exporter::get_course_image($course);
                if (!$courseimage) {
                    $courseimage = $OUTPUT->get_default_image_for_courseid($course->id);
                }

                $courseinfo = get_editor_course_link($course);
                $blocks .= "
                    <div class=\"course-banner-item\">
                        <div class=\"course-bg-banner\">
                            <div style=\"background-image:url('{$courseimage}');\" class=\"course-bg-images\">
                            </div>
                            <div class=\"video-bg-player\"
                                 data-trailer=\"https://www.youtube.com/watch?v={$data->youtubeid}\">
                            </div>
                            <div class=\"course-bg-overlay\"></div>
                        </div>
                        <div class=\"course-banner-content\">
                            <h3 class=\"course-title\">
                                <a href=\"{$courseinfo->link}\">{$course->fullname}</a>
                            </h3>
                            <div class=\"course-text-description\">
                                {$data->description}
                            </div>
                            <a class=\"btn btn-access\" href=\"{$courseinfo->link}\">{$courseinfo->access}</a>
                        </div>
                    </div>\n";
            }
        }
    }

    return "<div class=\"owl-carousel owl-course-banner\">{$blocks}</div>";
}

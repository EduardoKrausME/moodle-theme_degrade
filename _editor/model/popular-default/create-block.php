<?php

use core_course\external\course_summary_exporter;
use core_external\util;

function popular_default_createblocks($page) {
    global $DB, $OUTPUT, $CFG;

    $page->info = json_decode($page->info);

    $blocks = "";
    if (isset($page->info->savedata)) {
        foreach ($page->info->savedata as $data) {
            $course = $DB->get_record("course", array("id" => $data->courseid));
            if ($course) {
                $course = new core_course_list_element($course);

                $courseimage = course_summary_exporter::get_course_image($course);
                if (!$courseimage) {
                    $courseimage = $OUTPUT->get_default_image_for_courseid($course->id);
                }

                $context = context_course::instance($course->id, IGNORE_MISSING);
                $summary = util::format_text($course->summary, $course->summaryformat, $context, "course", "summary", 0);
                $summary = strip_tags($summary[0]);

                $courseinfo = theme_degrade_get_editor_course_link($course);
                $blocks .= "
                    <div class=\"top-courses-item overflow-hidden\">
                        <div class=\"top-courses-inner\">
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
                            <div class=\"content-back\">
                                <h6 class=\"course-title\">
                                    <a href=\"{$courseinfo->link}\">{$course->fullname}</a>
                                </h6>
                                <div class=\"video-description\">{$summary}</div>
                            </div>
                        </div>
                    </div>\n";
            }
        }
    }

    return "<div class=\"owl-courses-content owl-carousel\">{$blocks}</div>";
}

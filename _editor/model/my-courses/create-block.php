<?php

/**
 * featured3_createblocks
 *
 * @param $page
 * @return string
 */
function my_courses_createblocks($page) {
    global $USER, $CFG;

    $page->infoobj = json_decode($page->info);

    $class = 'col-12 col-sm-6 col-md-4 col-lg-4';
    if (isset($page->infoobj->savedata->rows)) {
        switch ($page->infoobj->savedata->rows) {
            case 2:
                $class = 'col-12 col-sm-6 col-md-6 col-lg-6'; // 1 -> 2.
                break;
            case 3:
                $class = 'col-12 col-sm-6 col-md-4 col-lg-4'; // 1 -> 2 -> 3.
                break;
            case 4:
                $class = 'col-12 col-sm-6 col-md-4 col-lg-3'; // 1 -> 2 -> 3 -> 4.
                break;
            case 6:
                $class = 'col-12 col-sm-6 col-md-4 col-lg-2'; // 1 -> 2 -> 3 -> 6.
                break;
        }
    }

    $blocks = "";

    $courses = enrol_get_users_courses($USER->id, true, '*');
    foreach ($courses as $course) {
        $backgroundimage = my_courses_couse_image(new core_course_list_element($course));
        $progress = my_courses_course_progress($course);
        $blocks .= "
            <div class=\"{$class} course-card\">
                <div class=\"course-inner\"
                     style=\"background-image: url('{$backgroundimage}');\">
                    <div class=\"course-overlay\">
                        <h3><a href=\"{$CFG->wwwroot}/course/view.php?id={$course->id}\">{$course->fullname}</a></h3>
                        <div class=\"progress-bar\">
                            <div class=\"progress\" style=\"width: {$progress}%;\"></div>
                        </div>
                    </div>
                </div>
            </div>\n";
    }

    return "<div class=\"row courses-grid\">{$blocks}</div>";
}

function my_courses_course_progress($course) {
    global $USER;

    $all = 0;
    $count = 0;

    $courseformat = course_get_format($course->id);
    $modinfo = $courseformat->get_modinfo();
    $completioninfo = new completion_info($course);

    $sections = $modinfo->get_section_info_all();
    /** @var section_info $section */
    foreach ($sections as $section) {
        if ($courseformat->is_section_visible($section)) {
            if ($section->visible) {
                /** @var cm_info $cminfo */
                foreach ($modinfo->cms as $cminfo) {
                    if ($cminfo->is_visible_on_course_page() && $cminfo->uservisible) {
                        if ($cminfo->get_section_info()->id == $section->id) {
                            if ($cminfo->modname == "label") {
                                continue;
                            }

                            $hascompletion = $completioninfo->is_enabled($cminfo) != COMPLETION_DISABLED;
                            if ($hascompletion) {
                                $count++;

                                $state = $completioninfo->internal_get_state($cminfo, $USER->id, null);
                                if ($state == COMPLETION_COMPLETE) {
                                    $all++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    if ($count == 0) {
        return 0;
    } else {
        return intval(($all / $count) * 100);
    }
}

function my_courses_couse_image(core_course_list_element $course) {
    global $OUTPUT;

    /** @var stored_file $file */
    foreach ($course->get_course_overviewfiles() as $file) {

        if ($file->is_valid_image()) {
            $contextid = $file->get_contextid();
            $component = $file->get_component();
            $filearea = $file->get_filearea();
            $filepath = $file->get_filepath();
            $filename = $file->get_filename();
            return moodle_url::make_pluginfile_url($contextid, $component, $filearea, null, $filepath, $filename);
        }
    }

    return $OUTPUT->get_generated_url_for_course(context_course::instance($course->id));
}

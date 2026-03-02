<?php

/**
 * featured3_createblocks
 *
 * @param $page
 * @return string
 * @throws dml_exception
 */
function courses_minimalist_createblocks($page) {
    global $DB, $CFG;

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

    if (isset($page->infoobj->savedata)) {
        foreach ($page->infoobj->savedata as $data) {
            if (isset($data->courseid) && $data->courseid > 0) {
                $course = $DB->get_record("course", ["id" => $data->courseid]);
            } else {
                continue;
            }

            $backgroundimage = courses_minimalist_couse_image(new core_course_list_element($course));
            $progress = courses_minimalist_course_progress($course);
            $blocks .= "
                <div class=\"{$class}\">
                    <div class=\"curso-card\" style=\"background-image: url('{$backgroundimage}');\">
                        <a href=\"{$CFG->wwwroot}/course/view.php?id={$course->id}\" class=\"course-overlay\">
                            <h3 class=\"curso-title\"><a href=\"{$CFG->wwwroot}/course/view.php?id={$course->id}\">{$course->fullname}</a></h3>
                            <div class=\"progress mt-3\">
                                <div class=\"progress-bar bg-primary\" role=\"progressbar\" style=\"width: {$progress}%;\"
                                     aria-valuenow=\"{$progress}\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>
                            </div>
                            <span class=\"percent\">{$progress}%</span>
                        </a>
                    </div>
                </div>\n";
        }
    }

    return "<div class=\"row g-4\">{$blocks}</div>";
}

/**
 * Function courses_minimalist_course_progress
 *
 * @param $course
 * @return int
 */
function courses_minimalist_course_progress($course) {
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

/**
 * Function courses_minimalist_couse_image
 *
 * @param core_course_list_element $course
 * @return \core\url
 */
function courses_minimalist_couse_image(core_course_list_element $course) {
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

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
 * course_renderer.php
 *
 * This is built using the boost template to allow for new theme"s using
 * Moodle"s new Boost theme engine
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\output\core;

use cache;
use context_course;
use context_system;
use core_course\external\course_summary_exporter;
use Exception;
use html_writer;
use moodle_url;
use core_course_category;
use coursecat_helper;
use stdClass;
use core_course_list_element;

/**
 * This class has function for core course renderer
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends \core_course_renderer {

    /**
     * Get Class Function
     *
     * @return string
     */
    private function get_class_row() {
        return "card-grid mx-0 row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 ";
    }

    /**
     * Outputs contents for frontpage as configured in $CFG->frontpage or $CFG->frontpageloggedin
     *
     * @return string
     * @throws Exception
     */
    public function frontpage() {
        global $DB, $CFG, $SITE, $USER;

        // Home with block editor.
        if (get_config("theme_degrade", "homemode")) {
            require_once("{$CFG->dirroot}/theme/degrade/_editor/editor-lib.php");

            $editing = $this->page->user_is_editing();
            $lang = $USER->lang ?? $CFG->lang;

            $where = "local='home' AND lang IN(:lang, 'all')";
            $pages = $DB->get_records_select("theme_degrade_pages", $where, ['lang' => $lang], "sort ASC");
            $compiledpages = theme_degrade_compile_pages($pages, $lang, $editing);

            $csslink = "";
            foreach ($compiledpages->css as $cssfile) {
                $csslink .= "<link rel=\"stylesheet\" href=\"{$CFG->wwwroot}{$cssfile}\">";
            }
            foreach ($compiledpages->js as $jsfile) {
                // JQuery already loaded.
                // JQueryui already loaded.
                if (strpos($jsfile, "require") === 0) {
                    $this->page->requires->js_init_code($jsfile);
                } else if (strpos($jsfile, "/") === 0) {
                    $this->page->requires->js($jsfile);
                }
            }

            $templatecontext["homemode_pages"] = $compiledpages->pages;

            if ($editing) {
                $templatecontext["editing"] = true;

                if (count($compiledpages->pages) == 0 && has_capability("moodle/site:config", context_system::instance())) {
                    $templatecontext["homemode_page_warningnopages"] = true;
                }

                $this->page->requires->strings_for_js(["preview"], "theme_degrade");
                $this->page->requires->js_call_amd("theme_degrade/frontpage", "add_block", [$lang]);
                $this->page->requires->js_call_amd("theme_degrade/frontpage", "block_order");
            }

            return $csslink . $this->output->render_from_template("theme_degrade/frontpage_editor", $templatecontext);
        }

        // Traditional home.
        $output = "";

        if (isloggedin() && !isguestuser() && isset($CFG->frontpageloggedin)) {
            $frontpagelayout = $CFG->frontpageloggedin;
        } else {
            $frontpagelayout = $CFG->frontpage;
        }

        foreach (explode(",", $frontpagelayout) as $v) {
            switch ($v) {
                // Display the main part of the front page.
                case FRONTPAGENEWS:
                    if ($SITE->newsitems) {
                        // Print forums only when needed.
                        require_once($CFG->dirroot . "/mod/forum/lib.php");
                        if (($newsforum = forum_get_course_forum($SITE->id, "news")) &&
                            ($forumcontents = $this->frontpage_news($newsforum))) {
                            $newsforumcm = get_fast_modinfo($SITE)->instances["forum"][$newsforum->id];
                            $output .= $this->frontpage_part("skipsitenews", "site-news-forum",
                                $newsforumcm->get_formatted_name(), $forumcontents);
                        }
                    }
                    break;

                case FRONTPAGEENROLLEDCOURSELIST:
                    $mycourseshtml = $this->frontpage_my_courses();
                    if (!empty($mycourseshtml)) {
                        $output .= $this->frontpage_part("skipmycourses", "frontpage-course-list",
                            get_string("mycourses"), $mycourseshtml);
                    }
                    break;

                case FRONTPAGEALLCOURSELIST:
                    $availablecourseshtml = $this->frontpage_available_courses();
                    $output .= $this->frontpage_part("skipavailablecourses", "frontpage-available-course-list",
                        get_string("availablecourses"), $availablecourseshtml);
                    break;

                case FRONTPAGECATEGORYNAMES:
                    $output .= $this->frontpage_part("skipcategories", "frontpage-category-names",
                        get_string("categories"), $this->frontpage_categories_list());
                    break;

                case FRONTPAGECATEGORYCOMBO:
                    $output .= $this->frontpage_part("skipcourses", "frontpage-category-combo",
                        get_string("courses"), $this->frontpage_combo_list());
                    break;

                case FRONTPAGECOURSESEARCH:
                    $output .= $this->box($this->course_search_form(""), "d-flex justify-content-center");
                    break;

            }
            $output .= "<br>";
        }

        return $output;
    }

    /**
     * Returns HTML to print list of available courses for the frontpage
     *
     * @return string
     * @throws \Exception
     */
    public function frontpage_available_courses() {
        global $CFG;

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_courses_display_options([
            "recursive" => true,
            "limit" => $CFG->frontpagecourselimit,
            "viewmoreurl" => new moodle_url("/course/index.php"),
            "viewmoretext" => get_string("fulllistofcourses")]);

        $chelper->set_attributes(["class" => "frontpage-course-list-all {$this->get_class_row()}"]);
        $courses = core_course_category::top()->get_courses($chelper->get_courses_display_options());
        $totalcount = core_course_category::top()->get_courses_count($chelper->get_courses_display_options());
        if (!$totalcount && !$this->page->user_is_editing() && has_capability("moodle/course:create", context_system::instance())) {
            // Print link to create a new course, for the 1st available category.
            return $this->add_new_course_button();
        }
        return $this->coursecat_courses($chelper, $courses, $totalcount);
    }

    /**
     * Returns HTML to print list of courses user is enrolled to for the frontpage
     *
     * Also lists remote courses or remote hosts if MNET authorisation is used
     *
     * @return string
     * @throws \Exception
     */
    public function frontpage_my_courses() {
        global $USER, $CFG, $DB;

        if (!isloggedin() || isguestuser()) {
            return "";
        }

        $output = "";
        $courses = enrol_get_my_courses("summary, summaryformat");
        $rhosts = [];
        $rcourses = [];
        if (!empty($CFG->mnet_dispatcher_mode) && $CFG->mnet_dispatcher_mode === "strict") {
            $rcourses = get_my_remotecourses($USER->id);
            $rhosts = get_my_remotehosts();
        }

        if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {

            $chelper = new coursecat_helper();
            $totalcount = count($courses);
            if (count($courses) > $CFG->frontpagecourselimit) {
                // There are more enrolled courses than we can display, display link to "My courses".
                $courses = array_slice($courses, 0, $CFG->frontpagecourselimit, true);
                $chelper->set_courses_display_options([
                    "viewmoreurl" => new moodle_url("/my/courses.php"),
                    "viewmoretext" => get_string("mycourses"),
                ]);
            } else if (core_course_category::top()->is_uservisible()) {
                // All enrolled courses are displayed, display link to "All courses" if there are more courses in system.
                $chelper->set_courses_display_options([
                    "viewmoreurl" => new moodle_url("/course/index.php"),
                    "viewmoretext" => get_string("fulllistofcourses"),
                ]);
                $totalcount = $DB->count_records("course") - 1;
            }
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_attributes([
                "class" => "frontpage-course-list-enrolled {$this->get_class_row()}",
            ]);
            $output .= $this->coursecat_courses($chelper, $courses, $totalcount);

            // MNET.
            if (!empty($rcourses)) {
                // At the IDP, we know of all the remote courses.
                $output .= html_writer::start_tag("div", ["class" => "courses"]);
                foreach ($rcourses as $course) {
                    $output .= $this->frontpage_remote_course($course);
                }
                $output .= html_writer::end_tag("div"); // Courses.
            } else if (!empty($rhosts)) {
                // Non-IDP, we know of all the remote servers, but not courses.
                $output .= html_writer::start_tag("div", ["class" => "courses"]);
                foreach ($rhosts as $host) {
                    $output .= $this->frontpage_remote_host($host);
                }
                $output .= html_writer::end_tag("div"); // Courses.
            }
        }
        return $output;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_list_element|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *                                  depend on the course position in list - first/last/even/odd)
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = "") {
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string("summary");
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return "";
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        $icons = $this->course_enrolment_list_icons($course);

        $courseimage = course_summary_exporter::get_course_image($course);
        if (!$courseimage) {
            if (method_exists($this->output, "get_default_image_for_courseid")) {
                $courseimage = $this->output->get_default_image_for_courseid($course->id);
            } else {
                $context = context_course::instance($course->id);
                $courseimage = $this->output->get_generated_url_for_course($context);
            }
        }

        $cardhomemustache = [
            "courseid" => $course->id,
            "fullname" => format_string($course->fullname),
            "viewurl" => new moodle_url("/course/view.php", ["id" => $course->id]),
            "courseimage" => $courseimage,
            "category" => $this->course_category_name($chelper, $course),
            "has_enrolment_icons" => count($icons),
            "enrolment_icons" => $icons,
        ];

        return $this->output->render_from_template("theme_degrade/core_course/cardhome", $cardhomemustache);
    }

    /**
     * Returns HTML to display course overview files.
     *
     * @param core_course_list_element $course
     *
     * @return string
     */
    protected function course_overview_image(core_course_list_element $course): string {
        global $CFG;

        /** @var \stored_file $file */
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                return moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    null,
                    $file->get_filepath(),
                    $file->get_filename()
                );
            }
        }
        return "";
    }

    /**
     * Returns HTML to display course enrolment icons.
     *
     * @param core_course_list_element $course
     *
     * @return array
     */
    protected function course_enrolment_list_icons(core_course_list_element $course) {
        $content = [];
        if ($icons = enrol_get_course_info_icons($course)) {
            foreach ($icons as $icon) {
                $content[] = $this->render($icon);
            }
        }
        return $content;
    }

    /**
     * Renders the list of courses
     *
     * This is internal function, please use {@link core_course_renderer::courses_list()} or another public
     * method from outside of the class
     *
     * If list of courses is specified in $courses; the argument $chelper is only used
     * to retrieve display options and attributes, only methods get_show_courses(),
     * get_courses_display_option() and get_and_erase_attributes() are called.
     *
     * @param coursecat_helper $chelper various display options
     * @param array $courses            the list of courses to display
     * @param int|null $totalcount      total number of courses (affects display mode if it is AUTO or pagination if
     *                                  applicable), defaulted to count($courses)
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;
        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit.
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // Prepare content of paging bar if it is needed.
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // There are more results that can fit on one page.
            if ($paginationurl) {
                // The option paginationurl was specified, display pagingbar.
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                    $paginationurl->out(false, ['perpage' => $perpage]));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, ['perpage' => 'all']),
                        get_string('showall', '', $totalcount)), ['class' => 'paging paging-showall']);
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // The option for 'View more' link was specified, display more link.
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', get_string('viewmore'));
                $morelink = html_writer::tag(
                    'div',
                    html_writer::link($viewmoreurl, $viewmoretext, ['class' => 'btn btn-secondary']),
                    ['class' => 'paging paging-morelink']
                );
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // There are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode.
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, ['perpage' => $CFG->coursesperpage]),
                get_string('showperpage', '', $CFG->coursesperpage)), ['class' => 'paging paging-showperpage']);
        }

        // Display list of courses.
        $attributes = $chelper->get_and_erase_attributes("courses {$this->get_class_row()}");
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 0;
        foreach ($courses as $course) {
            $coursecount++;
            $classes = ($coursecount % 2) ? 'odd' : 'even';
            if ($coursecount == 1) {
                $classes .= ' first';
            }
            if ($coursecount >= count($courses)) {
                $classes .= ' last';
            }
            $content .= $this->coursecat_coursebox($chelper, $course, $classes);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // Courses.
        return $content;
    }

}

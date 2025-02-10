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
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\output\core;

use html_writer;
use moodle_url;
use core_course_category;
use coursecat_helper;
use stdClass;
use core_course_list_element;
use context_system;

/**
 * This class has function for core course renderer
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends \core_course_renderer {

    /**
     * Renderer function for the frontpage available courses.
     *
     * @return string
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function frontpage_available_courses() {
        global $CFG, $DB;

        if (file_exists("{$CFG->dirroot}/local/kopere_pay/lib.php")) {
            require_once("{$CFG->dirroot}/local/kopere_pay/classes/util/Formater.php");
        }

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_courses_display_options([
            'recursive' => true,
            'limit' => $CFG->frontpagecourselimit,
            'viewmoreurl' => new moodle_url('/course/index.php'),
            'viewmoretext' => get_string('fulllistofcourses'),
        ]);
        $courses = core_course_category::get(0)->get_courses($chelper->get_courses_display_options());

        $hascoursecreate = has_capability('moodle/course:create', context_system::instance());
        $datacursos = [
            "root_class" => "frontpage_available_courses",
            "title" => get_string('availablecourses'),
            "text" => theme_degrade_get_setting("frontpage_avaliablecourses_text"),
            "courses" => [],
            "has_course_create" => $hascoursecreate,
            "add_new_course_button" => $hascoursecreate ? $this->add_new_course_button() : '',
        ];

        foreach ($courses as $course) {

            $viewurl = course_renderer_util::course_url($course);
            $freename = $priceval = false;
            if (file_exists("{$CFG->dirroot}/local/kopere_pay/lib.php")) {

                $koperepaydetalhe = $DB->get_record("kopere_pay_detalhe", ['course' => $course->id]);
                if ($koperepaydetalhe && $koperepaydetalhe->course) {
                    $viewurl = (new moodle_url('/local/kopere_pay/', ['id' => $course->id]))->out();
                    $preco = \local_kopere_pay\util\Formater::precoToFloat($koperepaydetalhe->preco);
                    if ($preco <= 5) {
                        $freename = "<a href='{$viewurl}'>" . get_string("free_name", "theme_degrade") . "</a>";
                    } else {
                        $preco = \local_kopere_pay\util\Formater::numberFormater($koperepaydetalhe->preco);
                        $priceval = "<a href='{$viewurl}'>R$ {$preco}</a>";
                    }
                }
            }

            $datacursos["courses"][] = [
                "couse_class" => "col-xg-3 col-lg-4 col-sm-6",
                "courseimage" => course_renderer_util::couse_image($course),
                "viewurl" => $viewurl,
                "fullname" => $course->get_formatted_name(),
                "countlessons" => course_renderer_util::count_lessson($course),
                "showinstructor" => theme_degrade_get_setting("frontpage_avaliablecourses_instructor")
                    && count(course_renderer_util::get_teachers($course)),
                "instructor" => course_renderer_util::get_teachers($course),
                "is_enrolled" => $hascoursecreate || course_renderer_util::is_enrolled($course),
                "access_course" => get_string("access_course", "theme_degrade"),
                "matricular" => $priceval ?
                    get_string("matricular", "theme_degrade") :
                    get_string("access_course", "theme_degrade"),
                "freename" => $freename,
                "priceval" => $priceval,
            ];
        }

        return $this->output->render_from_template('theme_degrade/frontpage-courselist', $datacursos);
    }

    /**
     * Returns HTML to print list of courses user is enrolled to for the frontpage
     *
     * Also lists remote courses or remote hosts if MNET authorisation is used
     *
     * @return string
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function frontpage_my_courses() {
        global $CFG;

        if (!isloggedin() || isguestuser()) {
            return '';
        }

        $hascoursecreate = has_capability('moodle/course:create', context_system::instance());

        $datacursos = [
            "root_class" => "frontpage_my_courses",
            "title" => get_string('mycourses'),
            "text" => theme_degrade_get_setting("frontpage_mycourses_text"),
            "courses" => [],
            "has_course_create" => $hascoursecreate,
        ];

        $courses = enrol_get_my_courses('summary, summaryformat');
        foreach ($courses as $course) {
            $course = new core_course_list_element(get_course($course->id));

            $datacursos["courses"][] = [
                "couse_class" => "col-xg-3 col-lg-4 col-sm-6",
                "courseimage" => course_renderer_util::couse_image($course),
                "viewurl" => course_renderer_util::course_url($course),
                "fullname" => $course->get_formatted_name(),
                "countlessons" => course_renderer_util::count_lessson($course),
                "showinstructor" => theme_degrade_get_setting("frontpage_mycourses_instructor") &&
                    count(course_renderer_util::get_teachers($course)),
                "instructor" => course_renderer_util::get_teachers($course),
                "is_enrolled" => true,
                "access_course" => get_string("continuar", "theme_degrade"),
            ];
        }

        return $this->output->render_from_template('theme_degrade/frontpage-courselist', $datacursos);
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper                            various display options
     * @param stdClass $course
     * @param string $additionalclasses                            additional classes to add to the main <div> tag
     *                                                             (usually depend on the course position in list -
     *                                                             first/last/even/odd)
     *
     * @return string
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG;

        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        $hascoursecreate = has_capability('moodle/course:create', context_system::instance());

        $datacurso = [
            "couse_class" => "col-xg-3 col-lg-4 col-sm-6",
            "courseimage" => course_renderer_util::couse_image($course),
            "viewurl" => course_renderer_util::course_url($course),
            "fullname" => $course->get_formatted_name(),
            "countlessons" => course_renderer_util::count_lessson($course),
            "showinstructor" => true,
            "instructor" => course_renderer_util::get_teachers($course),
            "is_enrolled" => $hascoursecreate || course_renderer_util::is_enrolled($course),
        ];

        return $this->output->render_from_template('theme_degrade/frontpage-course', $datacurso);
    }

    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|core_course_list_element $course
     *
     * @return string
     * @throws \moodle_exception
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        global $CFG;

        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }

        $datacurso = [
            "couse_class" => "col-xg-3 col-lg-4 col-sm-6",
            "courseimage" => course_renderer_util::couse_image($course),
            "viewurl" => course_renderer_util::course_url($course),
            "fullname" => $course->get_formatted_name(),
            "countlessons" => course_renderer_util::count_lessson($course),
            "showinstructor" => true,
            "instructor" => course_renderer_util::get_teachers($course),
            "is_enrolled" => true,
        ];

        return $this->output->render_from_template('theme_degrade/frontpage-course', $datacurso);
    }

    /**
     * Renders html to display search result page
     *
     * @param array $searchcriteria may contain elements: search, blocklist, modulelist, tagid
     *
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function search_courses($searchcriteria) {
        global $CFG;
        $content = '';

        $search = '';
        if (!empty($searchcriteria['search'])) {
            $search = $searchcriteria['search'];
        }
        $content .= $this->course_search_form($search);

        if (!empty($searchcriteria)) {
            $displayoptions = ['sort' => ['displayname' => 1]];
            $perpage = optional_param('perpage', 0, PARAM_RAW);
            if ($perpage !== 'all') {
                $displayoptions['limit'] = ((int)$perpage <= 0) ? $CFG->coursesperpage : (int)$perpage;
                $page = optional_param('page', 0, PARAM_INT);
                $displayoptions['offset'] = $displayoptions['limit'] * $page;
            }
            $displayoptions['paginationurl'] = new moodle_url('/course/search.php', $searchcriteria);
            $displayoptions['paginationallowall'] = true;

            $class = 'row course-search-result';
            foreach ($searchcriteria as $key => $value) {
                if (!empty($value)) {
                    $class .= ' course-search-result-' . $key;
                }
            }
            $chelper = new coursecat_helper();
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT);
            $chelper->set_courses_display_options($displayoptions);
            $chelper->set_search_criteria($searchcriteria);
            $chelper->set_attributes(['class' => $class]);

            $courses = core_course_category::search_courses($searchcriteria, $chelper->get_courses_display_options());
            $totalcount = core_course_category::search_courses_count($searchcriteria);
            $courseslist = $this->coursecat_courses($chelper, $courses, $totalcount);

            if (!$totalcount) {
                if (!empty($searchcriteria['search'])) {
                    $content .= $this->heading(get_string('nocoursesfound', '', $searchcriteria['search']));
                } else {
                    $content .= $this->heading(get_string('novalidcourses'));
                }
            } else {
                $content .= $this->heading(get_string('searchresults') . ": $totalcount");
                $content .= $courseslist;
            }
        }
        return $content;
    }

    /**
     * Renders HTML to display particular course category - list of it's subcategories and courses
     *
     * Invoked from /course/index.php
     *
     * @param int|stdClass|core_course_category $category
     *
     * @return bool|string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function course_category($category) {
        global $CFG;
        $usertop = core_course_category::user_top();
        if (empty($category)) {
            $coursecat = $usertop;
        } else if (is_object($category) && $category instanceof core_course_category) {
            $coursecat = $category;
        } else {
            $coursecat = core_course_category::get(is_object($category) ? $category->id : $category);
        }
        $site = get_site();
        $actionbar = new \core_course\output\category_action_bar($this->page, $coursecat);
        $output = $this->render_from_template('core_course/category_actionbar', $actionbar->export_for_template($this));

        if (core_course_category::is_simple_site()) {
            $strfulllistofcourses = get_string('fulllistofcourses');
            $this->page->set_title("$site->shortname: $strfulllistofcourses");
        } else if (!$coursecat->id || !$coursecat->is_uservisible()) {
            $strcategories = get_string('categories');
            $this->page->set_title("$site->shortname: $strcategories");
        } else {
            $strfulllistofcourses = get_string('fulllistofcourses');
            $this->page->set_title("$site->shortname: $strfulllistofcourses");
        }

        $chelper = new coursecat_helper();
        if ($description = $chelper->get_category_formatted_description($coursecat)) {
            $output .= $this->box($description, ['class' => 'generalbox info']);
        }

        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)
            ->set_attributes(['class' => 'row category-browse category-browse-' . $coursecat->id]);

        $coursedisplayoptions = [];
        $catdisplayoptions = [];
        $browse = optional_param('browse', null, PARAM_ALPHA);
        $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/course/index.php');
        if ($coursecat->id) {
            $baseurl->param('categoryid', $coursecat->id);
        }
        if ($perpage != $CFG->coursesperpage) {
            $baseurl->param('perpage', $perpage);
        }
        $coursedisplayoptions['limit'] = $perpage;
        $catdisplayoptions['limit'] = $perpage;
        if ($browse === 'courses' || !$coursecat->get_children_count()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, ['browse' => 'courses']);
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, ['browse' => 'categories']);
            $catdisplayoptions['viewmoretext'] = get_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->get_courses_count()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, ['browse' => 'categories']);
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, ['browse' => 'courses']);
            $coursedisplayoptions['viewmoretext'] = get_string('viewallcourses');
        } else {
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, ['browse' => 'courses', 'page' => 1]);
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, ['browse' => 'categories', 'page' => 1]);
        }
        $chelper->set_courses_display_options($coursedisplayoptions);
        $chelper->set_categories_display_options($catdisplayoptions);

        $output .= $this->coursecat_tree($chelper, $coursecat);

        return $output;
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
     * @param coursecat_helper $chelper    various display options
     * @param array $courses               the list of courses to display
     * @param int|null $totalcount         total number of courses (affects display mode if it is AUTO or pagination if
     *                                     applicable), defaulted to count($courses)
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;
        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            if ($paginationurl) {
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                    $paginationurl->out(false, ['perpage' => $perpage]));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, ['perpage' => 'all']),
                        get_string('showall', '', $totalcount)), ['class' => 'paging paging-showall']);
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', get_string('viewmore'));
                $morelink = html_writer::tag(
                    'div',
                    html_writer::link($viewmoreurl, $viewmoretext, ['class' => 'btn btn-secondary']),
                    ['class' => 'paging paging-morelink']
                );
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            $pagingbar = html_writer::tag('div',
                html_writer::link($paginationurl->out(false, ['perpage' => $CFG->coursesperpage]),
                    get_string('showperpage', '', $CFG->coursesperpage)), ['class' => 'paging paging-showperpage']);
        }

        $attributes = $chelper->get_and_erase_attributes('courses row');
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

        $content .= html_writer::end_tag('div');
        return $content;
    }

    /**
     * Serves requests to /course/category.ajax.php
     *
     * In this renderer implementation it may expand the category content or
     * course content.
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function coursecat_ajax() {
        global $DB, $CFG;

        $type = required_param('type', PARAM_INT);

        if ($type === self::COURSECAT_TYPE_CATEGORY) {
            $categoryid = required_param('categoryid', PARAM_INT);
            $showcourses = required_param('showcourses', PARAM_INT);
            $depth = required_param('depth', PARAM_INT);

            $category = core_course_category::get($categoryid);

            $chelper = new coursecat_helper();
            $baseurl = new moodle_url('/course/index.php', ['categoryid' => $categoryid]);
            $coursedisplayoptions = [
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url($baseurl, ['browse' => 'courses', 'page' => 1]),
            ];
            $catdisplayoptions = [
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url($baseurl, ['browse' => 'categories', 'page' => 1]),
            ];
            $chelper->set_show_courses($showcourses);
            $chelper->set_courses_display_options($coursedisplayoptions);
            $chelper->set_categories_display_options($catdisplayoptions);

            $chelper->set_attributes(["class" => "row"]);
            return $this->coursecat_category_content($chelper, $category, $depth);
        } else if ($type === self::COURSECAT_TYPE_COURSE) {
            $courseid = required_param('courseid', PARAM_INT);

            $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

            $chelper = new coursecat_helper();
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            $chelper->set_attributes(["class" => "row"]);
            return $this->coursecat_coursebox_content($chelper, $course);
        } else {
            throw new \coding_exception('Invalid request type');
        }
    }

    /**
     * Returns HTML to print tree with course categories and courses for the frontpage
     *
     * @return string
     * @throws \moodle_exception
     */
    public function frontpage_combo_list() {
        global $CFG;
        $tree = core_course_category::top();
        if (!$tree->get_children_count()) {
            return '';
        }
        $chelper = new coursecat_helper();
        $chelper->set_subcat_depth($CFG->maxcategorydepth)->set_categories_display_options([
            'limit' => $CFG->coursesperpage,
            'viewmoreurl' => new moodle_url('/course/index.php',
                [
                    'browse' => 'categories',
                    'page' => 1,
                ]),
        ])->set_courses_display_options([
            'limit' => $CFG->coursesperpage,
            'viewmoreurl' => new moodle_url('/course/index.php',
                [
                    'browse' => 'courses',
                    'page' => 1,
                ]),
        ])->set_attributes(['class' => 'row frontpage-category-combo']);
        return $this->coursecat_tree($chelper, $tree);
    }

    /**
     * Renders html to display a course search form
     *
     * @param string $value default value to populate the search field
     *
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function course_search_form($value = '') {

        $data = [
            'action' => \core_search\manager::get_course_search_url(),
            'btnclass' => 'btn-primary',
            'inputname' => 'q',
            'searchstring' => get_string('searchcourses'),
            'hiddenfields' => (object)['name' => 'areaids', 'value' => 'core_course-course'],
            'query' => $value,
        ];
        return $this->render_from_template('theme_degrade/frontpage-search', $data);
    }

    /**
     * Outputs contents for frontpage as configured in $CFG->frontpage or $CFG->frontpageloggedin
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function frontpage() {
        global $CFG, $SITE;
        $output = '';

        if (isloggedin() && !isguestuser() && isset($CFG->frontpageloggedin)) {
            $frontpagelayout = $CFG->frontpageloggedin;
        } else {
            $frontpagelayout = $CFG->frontpage;
        }

        foreach (explode(',', $frontpagelayout) as $v) {
            switch ($v) {
                case FRONTPAGENEWS:
                    if ($SITE->newsitems) {

                        require_once($CFG->dirroot . '/mod/forum/lib.php');

                        if (($newsforum = forum_get_course_forum($SITE->id, 'news')) &&
                            ($forumcontents = $this->frontpage_news($newsforum))) {
                            $newsforumcm = get_fast_modinfo($SITE)->instances['forum'][$newsforum->id];
                            $output .= $this->frontpage_part('skipsitenews', 'site-news-forum',
                                $newsforumcm->get_formatted_name(), $forumcontents);
                        }
                    }
                    break;

                case FRONTPAGEENROLLEDCOURSELIST:
                    $output .= $this->frontpage_my_courses();
                    break;

                case FRONTPAGEALLCOURSELIST:
                    $output .= $this->frontpage_available_courses();
                    break;

                case FRONTPAGECATEGORYNAMES:
                    $output .= $this->frontpage_part('skipcategories', 'frontpage-category-names',
                        get_string('categories'), $this->frontpage_categories_list());
                    break;

                case FRONTPAGECATEGORYCOMBO:
                    $output .= $this->frontpage_part('skipcourses', 'frontpage-category-combo',
                        get_string('courses'), $this->frontpage_combo_list());
                    break;

                case FRONTPAGECOURSESEARCH:
                    $output .= $this->box($this->course_search_form(''), 'd-flex justify-content-center');
                    break;

            }
            $output .= '<br />';
        }
        return $output;
    }
}

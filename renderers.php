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
 * Class renderer
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_course\management\helper;

/**
 * Class renderer
 */
class theme_degrade_core_course_management_renderer extends core_course_management_renderer {
    /**
     * Renders a category list item.
     *
     * This function gets called recursively to render sub categories.
     *
     * @param core_course_category $category The category to render as listitem.
     * @param core_course_category[] $subcategories The subcategories belonging to the category being rented.
     * @param int $totalsubcategories The total number of sub categories.
     * @param null $selectedcategory The currently selected category
     * @param int[] $selectedcategories The path to the selected category and its ID.
     * @return string
     * @throws Exception
     */
    public function category_listitem(
        core_course_category $category,
        array $subcategories,
        $totalsubcategories,
        $selectedcategory = null,
        $selectedcategories = []
    ) {
        $isexpandable = ($totalsubcategories > 0);
        $isexpanded = (!empty($subcategories));
        $activecategory = ($selectedcategory == $category->id);

        $attributes = [
            "class" => "listitem listitem-category list-group-item list-group-item-action",
            "data-id" => $category->id,
            "data-expandable" => $isexpandable ? "1" : "0",
            "data-expanded" => $isexpanded ? "1" : "0",
            "data-selected" => $activecategory ? "1" : "0",
            "data-visible" => $category->visible ? "1" : "0",
            "role" => "treeitem",
            "aria-expanded" => $isexpanded ? "true" : "false",
        ];

        $text = $category->get_formatted_name();

        $textlabel = null;
        if (($parent = $category->get_parent_coursecat()) && $parent->id) {
            $a = (object) [
                "category" => $text,
                "parentcategory" => $parent->get_formatted_name(),
            ];
            $textlabel = get_string("categorysubcategoryof", "moodle", $a);
        }

        $showcheckbox = ($category->can_resort_subcategories() || $category->has_manage_capability());

        $viewcaturl = new moodle_url("/course/management.php", ["categoryid" => $category->id]);

        // Expand/collapse icon HTML (kept as HTML, injected via {{{icon}}}).
        if ($isexpanded) {
            $icon = $this->output->pix_icon(
                "t/switch_minus",
                get_string("collapse"),
                "moodle",
                ["class" => "tree-icon", "title" => ""]
            );
            $icon = html_writer::link(
                $viewcaturl,
                $icon,
                [
                    "class" => "float-start",
                    "data-action" => "collapse",
                    "title" => get_string("collapsecategory", "moodle", $text),
                    "aria-controls" => "subcategoryof" . $category->id,
                ]
            );
        } else if ($isexpandable) {
            $icon = $this->output->pix_icon(
                "t/switch_plus",
                get_string("expand"),
                "moodle",
                ["class" => "tree-icon", "title" => ""]
            );
            $icon = html_writer::link(
                $viewcaturl,
                $icon,
                [
                    "class" => "float-start",
                    "data-action" => "expand",
                    "title" => get_string("expandcategory", "moodle", $text),
                ]
            );
        } else {
            $icon = $this->output->pix_icon(
                "i/navigationitem",
                "",
                "moodle",
                ["class" => "tree-icon"]
            );
            $icon = html_writer::span($icon, "float-start");
        }

        $actions = helper::get_category_listitem_actions($category);
        $hasactions = !empty($actions) || $category->can_create_course();

        $nameclass = $hasactions ? "float-start categoryname aalink" : "float-start categoryname without-actions";

        $courseicon = $this->output->pix_icon(
            "i/course",
            get_string("courses"),
            "core",
            ["class" => "ps-1"]
        );

        $childrenhtml = "";
        if ($isexpanded) {
            $catatlevel = helper::get_expanded_categories($category->path);
            $catatlevel[] = array_shift($selectedcategories);
            $catatlevel = array_unique($catatlevel);

            foreach ($subcategories as $listitem) {
                $childcategories = (in_array($listitem->id, $catatlevel)) ? $listitem->get_children() : [];
                $childrenhtml .= $this->category_listitem(
                    $listitem,
                    $childcategories,
                    $listitem->get_children_count(),
                    $selectedcategory,
                    $selectedcategories
                );
            }
        }

        $countid = "course-count-" . $category->id;

        $data = [
            "attributes" => html_writer::attributes($attributes),
            "id" => $category->id,

            // Content.
            "name" => $text,
            "viewurl" => $viewcaturl->out(false),
            "nameclass" => $nameclass,
            "arialabel" => $textlabel,

            // Checkbox.
            "showcheckbox" => $showcheckbox,

            // Right side.
            "idnumber" => $category->idnumber ? s($category->idnumber) : null,
            "hasactions" => $hasactions,
            "actionshtml" => $hasactions ? $this->category_listitem_actions($category, $actions) : "",
            "coursecount" => $category->get_courses_count(),
            "countid" => $countid,
            "courseicon" => $courseicon,

            // Left icon.
            "icon" => $icon,

            // Children.
            "expanded" => $isexpanded,
            "childrenhtml" => $childrenhtml,
        ];

        return $this->output->render_from_template("theme_degrade/course_management/category_listitem", $data);
    }

    /**
     * Renders a course list item using Mustache template.
     *
     * @param core_course_category $category The currently selected category and the category the course belongs to.
     * @param core_course_list_element $course The course to produce HTML for.
     * @param int $selectedcourse The id of the currently selected course.
     * @return string
     * @throws Exception
     */
    public function course_listitem(
        core_course_category $category,
        core_course_list_element $course,
        $selectedcourse
    ) {
        $coursename = $course->get_formatted_name();
        $viewcourseurl = new moodle_url($this->page->url, ["courseid" => $course->id]);

        $data = [
            "courseid" => $course->id,
            "dataselected" => ($selectedcourse == $course->id) ? "1" : "0",
            "datavisible" => !empty($course->visible) ? "1" : "0",

            "coursename" => $coursename,
            "viewcourseurl" => $viewcourseurl->out(false),

            "canresort" => $category->can_resort_courses(),
            "moveicon" => $this->output->pix_icon("i/move_2d", get_string("dndcourse")),

            "showcheckbox" => $category->has_manage_capability(),
            "checkboxid" => "courselistitem" . $course->id,
            "checkboxname" => "bc[]",
            "checkboxvalue" => $course->id,
            "bulkactionlabel" => get_string("bulkactionselect", "moodle", $coursename),

            "hasidnumber" => !empty($course->idnumber),
            "idnumber" => !empty($course->idnumber) ? s($course->idnumber) : "",

            "actions" => $this->course_listitem_actions($category, $course),
        ];

        return $this->output->render_from_template("theme_degrade/course_management/course_listitem", $data);
    }
}

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
 * A drawer based layout for the boost theme.
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @copyright based on work by 2021 Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/behat/lib.php");
require_once("{$CFG->dirroot}/course/lib.php");

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = "";
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$templatecontext = [
    "sitename" => format_string($SITE->shortname, true, ["context" => context_course::instance(SITEID), "escape" => false]),
    "output" => $OUTPUT,
    "sidepreblocks" => $blockshtml,
    "hasblocks" => $hasblocks,
    "bodyattributes" => $bodyattributes,
    "courseindexopen" => $courseindexopen,
    "blockdraweropen" => $blockdraweropen,
    "courseindex" => $courseindex,
    "primarymoremenu" => $primarymenu["moremenu"],
    "secondarymoremenu" => $secondarynavigation ?: false,
    "mobileprimarynav" => $primarymenu["moremenu"]["nodearray"],
    "usermenu" => $primarymenu["user"],
    "langmenu" => $primarymenu["lang"],
    "forceblockdraweropen" => $forceblockdraweropen,
    "regionmainsettingsmenu" => $regionmainsettingsmenu,
    "hasregionmainsettingsmenu" => !empty($regionmainsettingsmenu),
    "overflow" => $overflow,
    "headercontent" => $headercontent,
    "addblockbutton" => $addblockbutton,
    "course_summary" => get_config("theme_degrade", "course_summary"),
];

if (optional_param("embed-frame-top", 0, PARAM_INT)) {
    echo $OUTPUT->render_from_template("theme_degrade/drawers_embed", $templatecontext);
} else {
    if (strpos($_SERVER["REQUEST_URI"], "course/view.php") || strpos($_SERVER["REQUEST_URI"], "course/section.php")) {
        $templatecontext["hasnavbarcourse"] = true;

        if (strpos($_SERVER["REQUEST_URI"], "course/view.php")) {
            $templatecontext["course_summary_banner"] = get_config("theme_degrade", "course_summary_banner");
            if ($templatecontext["course_summary_banner"]) {
                $options = ["context" => $this->page->context];
                $summary = file_rewrite_pluginfile_urls(
                    $this->page->course->summary, "pluginfile.php", $this->page->context->id, "course", "summary", null);
                $summary = format_text($summary, $this->page->course->summaryformat, $options);
                $templatecontext["course_summary_banner"] = $summary;
            }
        }
    }

    if ($courseindex || $hasblocks) {
        $templatecontext += theme_degrade_progress_content();
    }

    $brandcolor = get_config("theme_boost", "brandcolor");
    $templatecontext["footercount"] = 0;
    $templatecontext["footercontents"] = [];
    $templatecontext["footer_background_color"] =
        theme_degrade_default_color("footer_background_color", $brandcolor);
    $templatecontext["footer_background_text_color"] =
        theme_degrade_get_footer_color($templatecontext["footer_background_color"], "#333", false);

    for ($i = 1; $i <= 4; $i++) {
        $footertitle = get_config("theme_degrade", "footer_title_{$i}");
        $footerhtml = get_config("theme_degrade", "footer_html_{$i}");

        if (isset($footertitle[2]) && isset($footerhtml[5])) {
            $templatecontext["footercount"]++;
            $templatecontext["footercontents"][] = [
                "footertitle" => $footertitle,
                "footerhtml" => $footerhtml,
            ];
        }
    }

    $templatecontext["footer_show_copywriter"] = get_config("theme_degrade", "footer_show_copywriter");
    $templatecontext["editing"] = $PAGE->user_is_editing();

    echo $OUTPUT->render_from_template("theme_degrade/drawers", $templatecontext);
}

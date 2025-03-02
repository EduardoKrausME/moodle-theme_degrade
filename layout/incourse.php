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
 * A incourse based layout for the boost theme.
 *
 * @package   theme_degrade
 * @copyright 2024 Eduardo kraus (http://eduardokraus.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/behat/lib.php");
require_once($CFG->dirroot . "/course/lib.php");
require_once(__DIR__ . "/../lib.php");

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

$USER->ajax_updatable_user_prefs["drawer-open-nav"] = PARAM_ALPHA;
$USER->ajax_updatable_user_prefs["drawer-open-index"] = PARAM_BOOL;
$USER->ajax_updatable_user_prefs["drawer-open-block"] = PARAM_BOOL;

if (isloggedin()) {
    $blockdraweropen = (get_user_preferences("drawer-open-block") == true);
} else {
    $blockdraweropen = false;
}

if (defined("BEHAT_SITE_RUNNING")) {
    $blockdraweropen = true;
}

$blockshtml = $OUTPUT->blocks("side-pre");
$hasblocks = (strpos($blockshtml, "data-block=") !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}

$bodyattributes = $OUTPUT->body_attributes(["uses-drawers", theme_degrade_get_body_class()]);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$contextcourse = context_course::instance($COURSE->id);
$courseupdate = has_capability("moodle/course:update", $contextcourse);

$secondarynavigation = false;
$overflow = "";
if ($courseupdate && $PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, "nav-tabs", true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new \theme_degrade\navigation\primary($PAGE);
$renderer = $PAGE->get_renderer("core");
$primarymenu = $primary->export_for_template($renderer);

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);


$templatedata = [
    "sitename" => format_string($SITE->shortname, true, ["context" => context_course::instance(SITEID), "escape" => false]),
    "output" => $OUTPUT,
    "sidepreblocks" => $blockshtml,
    "hasblocks" => $hasblocks,
    "bodyattributes" => $bodyattributes,
    "blockdraweropen" => $blockdraweropen,
    "courseindex" => \theme_degrade\template\course::courseindex($COURSE->id),
    "show_image_top_course" => \theme_degrade\template\course::show_image_top_course($COURSE),
    "hasregionmainsettingsmenu" => !empty($regionmainsettingsmenu),
    "primarymoremenu" => $primarymenu["moremenu"],
    "secondarymoremenu" => $secondarynavigation,
    "mobileprimarynav" => $primarymenu["mobileprimarynav"],
    "usermenu" => $primarymenu["user"],
    "langmenu" => $primarymenu["lang"],
    "forceblockdraweropen" => $forceblockdraweropen,
    "regionmainsettingsmenu" => $regionmainsettingsmenu,
    "overflow" => $overflow,
    "headercontent" => $headercontent,
    "addblockbutton" => $addblockbutton,
    "course" => $COURSE,
];

if (isset($_SESSION["return_course_id"]) && isset($_SESSION["refcourse_course_id"])) {
    if ($_SESSION["refcourse_course_id"] == $COURSE->id) {
        $templatedata["return_course_id"] = $_SESSION["return_course_id"];
        $templatedata["return_course_name"] = $_SESSION["return_course_name"];
        $templatedata["refcourse_course_id"] = $_SESSION["refcourse_course_id"];
    }
}

require_once("{$CFG->dirroot}/theme/degrade/classes/template/frontapage_data.php");
$templatedata = array_merge($templatedata, \theme_degrade\template\frontapage_data::topo());

require_once("{$CFG->dirroot}/theme/degrade/classes/template/footer_data.php");
$templatedata = array_merge($templatedata, \theme_degrade\template\footer_data::get_data());

if ($PAGE->pagetype == "enrol-index" && file_exists("{$CFG->dirroot}/local/kopere_pay/lib.php")) {
    global $DB;

    $koperepaydetalhe = $DB->get_record('kopere_pay_detalhe', ['course' => $COURSE->id, 'status' => 'aberto']);
    if ($koperepaydetalhe) {
        $enable = \local_kopere_dashboard\util\config::get_key("builder_enable_{$COURSE->id}");
        if ($enable) {
            redirect("/local/kopere_pay/view.php?id=13");
        } else {
            redirect("/local/kopere_pay/?id={$COURSE->id}");
        }
    }
}

if (!$courseupdate && strpos($_SERVER["REQUEST_URI"], "/scorm/player.php") > 1) {
    echo $OUTPUT->render_from_template("theme_degrade/incourse_scorm", $templatedata);
} else {
    if ($COURSE->format == "topics" || $COURSE->format == "weeks") {
        echo $OUTPUT->render_from_template("theme_degrade/incourse", $templatedata);
    } else {
        echo $OUTPUT->render_from_template("theme_degrade/drawers", $templatedata);
    }
}

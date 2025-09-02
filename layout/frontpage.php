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
 * A frontpage based layout for the boost theme.
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG, $PAGE, $OUTPUT, $USER, $DB;

require_once("{$CFG->libdir}/behat/lib.php");
require_once("{$CFG->dirroot}/course/lib.php");

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

$extraclasses = ["uses-drawers"];

$blockshtml = $OUTPUT->blocks("side-pre");

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = "";
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, "nav-tabs", true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer("core");
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don"t add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$templatecontext = [
    "sitename" => format_string($SITE->shortname, true, ["context" => context_course::instance(SITEID), "escape" => false]),
    "output" => $OUTPUT,
    "sidepreblocks" => $blockshtml,
    "bodyattributes" => $bodyattributes,
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
];

$config = get_config("theme_degrade");

$brandcolor = get_config("theme_boost", "brandcolor");
$templatecontext["footercount"] = 0;
$templatecontext["footercontents"] = [];
$templatecontext["footer_background_color"] =
    theme_degrade_default_color("footer_background_color", $brandcolor);
$templatecontext["footer_background_text_color"] =
    theme_degrade_get_footer_color($templatecontext["footer_background_color"], "#333", false);
for ($i = 1; $i <= 4; $i++) {
    $footertitle = $config->{"footer_title_{$i}"};
    $footerhtml = $config->{"footer_html_{$i}"};

    if (isset($footerhtml[5])) {
        $templatecontext["footercount"]++;
        $templatecontext["footercontents"][] = [
            "footertitle" => $footertitle,
            "footerhtml" => $footerhtml,
        ];
    }
}
$templatecontext["footer_show_copywriter"] = $config->footer_show_copywriter;

$editing = $PAGE->user_is_editing();
if (isset($config->homemode) && $config->homemode) {
    $templatecontext["homemode_status"] = 1;

    $PAGE->requires->jquery();
    $PAGE->requires->jquery_plugin("ui");
    $PAGE->requires->jquery_plugin("ui-css");

    if ($editing) {
        $PAGE->requires->js_call_amd("theme_degrade/frontpage", "editingswitch", []);
        $PAGE->requires->js_call_amd("theme_degrade/frontpage", "block_order", []);
    }
}
if ($editing) {
    $templatecontext["editing"] = true;
    $url = "{$CFG->wwwroot}/theme/degrade/_editor/actions.php?action=homemode&local=editing&sesskey=" . sesskey();
    $templatecontext["homemode_form_action"] = $url;
}

echo $OUTPUT->render_from_template("theme_degrade/frontpage", $templatecontext);

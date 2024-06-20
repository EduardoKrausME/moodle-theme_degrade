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
 * @package     theme_degrade
 * @copyright   2024 Eduardo Kraus https://eduardokraus.com/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage('theme_degrade_slideshow', get_string('settings_slideshow_heading', 'theme_degrade'));

// Number of slides.
$choices = [
    0 => get_string("slideshow_numslides_nenhum", 'theme_degrade'),
    1 => '1',
    2 => '2',
    3 => '3',
    4 => '4',
    5 => '5',
    6 => '6',
    7 => '7',
    8 => '8',
    9 => '9',
];
$settingslideshownumslides = new admin_setting_configselect('theme_degrade/slideshow_numslides',
    get_string('slideshow_numslides', 'theme_degrade'),
    get_string('slideshow_numslides_desc', 'theme_degrade'), 0, $choices);
$page->add($settingslideshownumslides);

$slideshownumslides = get_config('theme_degrade', 'slideshow_numslides');
for ($i = 1; $i <= $slideshownumslides; $i++) {
    $heading = get_string('slideshow_info', 'theme_degrade', $i);
    $setting = new admin_setting_heading("theme_degrade/slideshow_info_{$i}",
        "<span id='admin-slideshow_info_{$i}'>{$heading}</span>", '');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile("theme_degrade/slideshow_image_{$i}",
        get_string('slideshow_image', 'theme_degrade'),
        get_string('slideshow_image_desc', 'theme_degrade'),
        "slideshow_image_{$i}", 0,
        ['maxfiles' => 1, 'accepted_types' => ['image']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configtext("theme_degrade/slideshow_url_{$i}",
        get_string('slideshow_url', 'theme_degrade'),
        get_string('slideshow_url_desc', 'theme_degrade'),
        $CFG->wwwroot, PARAM_URL);
    $page->add($setting);

    $setting = new admin_setting_configtext("theme_degrade/slideshow_text_{$i}",
        get_string('slideshow_text', 'theme_degrade'),
        get_string('slideshow_text_desc', 'theme_degrade'), '', PARAM_TEXT);
    $page->add($setting);
}

$settings->add($page);

global $PAGE;
$PAGE->requires->js_call_amd('theme_degrade/settings', 'autosubmit', [$settingslideshownumslides->get_id()]);

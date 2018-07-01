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
 * The Setting cores layout.
 *
 * @package    theme_degrade
 * @copyright  2018 Eduardo Kraus
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$pagesettings = new admin_settingpage('theme_degrade_cores', get_string('coresheading', 'theme_degrade'));
$pagesettings->add(new admin_setting_heading('theme_degrade_cores',
    get_string('coresheading_desc', 'theme_degrade'), ''));

// Top Background.
$name = 'theme_degrade/background_color';
$title = get_string('background_color', 'theme_degrade');
$description = get_string('background_color_desc', 'theme_degrade');
$default = 'default';
$choices = [
    'default' => get_string('background_color_default', 'theme_degrade'),
    'green1' => get_string('background_color_green', 'theme_degrade', 1),
    'green2' => get_string('background_color_green', 'theme_degrade', 2),
    'green3' => get_string('background_color_green', 'theme_degrade', 3),
    'blue1' => get_string('background_color_blue', 'theme_degrade', 1),
    'blue2' => get_string('background_color_blue', 'theme_degrade', 2),
    'blue3' => get_string('background_color_blue', 'theme_degrade', 3),
    'blue4' => get_string('background_color_blue', 'theme_degrade', 4),
    'blue5' => get_string('background_color_blue', 'theme_degrade', 5),
    'blue6' => get_string('background_color_blue', 'theme_degrade', 6),
    'red1' => get_string('background_color_red', 'theme_degrade', 1),
    'red2' => get_string('background_color_red', 'theme_degrade', 2),
    'red3' => get_string('background_color_red', 'theme_degrade', 3),
    'red4' => get_string('background_color_red', 'theme_degrade', 4),
    'red5' => get_string('background_color_red', 'theme_degrade', 5),
    'red6' => get_string('background_color_red', 'theme_degrade', 6),
    'red7' => get_string('background_color_red', 'theme_degrade', 7),
    'red8' => get_string('background_color_red', 'theme_degrade', 8),
    'black1' => get_string('background_color_black', 'theme_degrade', 1),
];

$htmlselect = "";
foreach ($choices as $choice => $lang) {
    $htmlselect
        .= "<div class=\"theme-select-{$choice} theme-select-item\" onclick=\"themeSelectTest('{$choice}')\">
                    <div class=\"preview\"></div>
                </div>";
}

$setting = new admin_setting_configselect($name, $title, $description . $htmlselect, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$pagesettings->add($setting);

// Logo file setting.
$title = get_string('logocolor', 'theme_degrade');
$description = get_string('logocolor_desc', 'theme_degrade');
$setting = new admin_setting_configstoredfile('theme_degrade/logo', $title, $description, 'logo', 0,
    ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png', '.svg', '.gif']]);
$setting->set_updatedcallback('theme_reset_all_caches');
$pagesettings->add($setting);

$title = get_string('logowhite', 'theme_degrade');
$description = get_string('logowhite_desc', 'theme_degrade');
$setting = new admin_setting_configstoredfile('theme_degrade/logowhite', $title, $description, 'logowhite', 0,
    ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png', '.svg', '.gif']]);
$setting->set_updatedcallback('theme_reset_all_caches');
$pagesettings->add($setting);

// Favicon file setting.
$name = 'theme_degrade/favicon';
$title = get_string('favicon', 'theme_degrade');
$description = get_string('favicon_desc', 'theme_degrade');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon', 0,
    array('maxfiles' => 1, 'accepted_types' => array('png', 'jpg', 'ico')));
$setting->set_updatedcallback('theme_reset_all_caches');
$pagesettings->add($setting);


$ADMIN->add('theme_degrade', $pagesettings);
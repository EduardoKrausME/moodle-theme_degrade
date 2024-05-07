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
 * @date        03/05/2024 21:26
 */

defined('MOODLE_INTERNAL') || die;
global $PAGE;

$page = new admin_settingpage('theme_degrade_css', 'CSS');

$fontslist = [
    'Epilogue' => 'Epilogue',
    'Inter' => 'Inter',
    'Lato' => 'Lato',
    'Manrope' => 'Manrope',
    'Montserrat' => 'Montserrat',
    'Nunito' => 'Nunito',
    'Nunito Sans' => 'Nunito Sans',
    'Open Sans' => 'Open Sans',
    'Oxygen' => 'Oxygen',
    'Poppins' => 'Poppins',
    'Raleway' => 'Raleway',
    'Roboto' => 'Roboto',
    'Sora' => 'Sora',
];
$description = "";
foreach ($fontslist as $font) {
    $description .= "<div style='font-family:\"{$font}\";font-size:1.2em'>
                         <a href='https://fonts.google.com/specimen/{$font}'
                            target='_blank' style='font-family:\"{$font}\"'>{$font}</a>
                         - \"Lorem ipsum dolor sit amet, consectetur adipiscing elit\"</div>";
}
$setting = new admin_setting_configselect('theme_degrade/fontfamily',
    get_string('fontfamily', 'theme_degrade'),
    get_string('fontfamily_desc', 'theme_degrade') . $description,
    'Roboto', $fontslist);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$setting = new admin_setting_configtextarea('theme_degrade/customcss',
    get_string('customcss', 'theme_degrade'),
    get_string('customcss_desc', 'theme_degrade'), '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);

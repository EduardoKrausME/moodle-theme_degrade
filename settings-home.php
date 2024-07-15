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

$page = new admin_settingpage('theme_degrade_frontpage_home',
    get_string('theme_degrade_frontpage_home', 'theme_degrade'));

$home = get_string('content_type_home', 'theme_degrade');
if (get_config('theme_degrade', 'home_type') != 0) {
    $description = get_string('content_type_home_desc', 'theme_degrade', $home) . "<br>";

    $emptytext = get_string('content_type_empty', 'theme_degrade');

    $text = get_string('editor_link_home_all', 'theme_degrade');
    $html = "<a class='btn btn-info mt-1 mb-2' target='_blank'
                href='{$CFG->wwwroot}/theme/degrade/_editor/?chave=home&editlang=all'>{$text}</a>";
    if (!isset(get_config("theme_degrade", "home_htmleditor_all")[40])) {
        $html = "{$html} <strong class='alert-warning'>{$emptytext}</strong>";
    }
    $description .= "{$html}<br>";

    if ($CFG->langmenu) {
        $listoftranslations = get_string_manager()->get_list_of_translations();
        $langname = $listoftranslations[$CFG->lang];

        $text = get_string('editor_link_home', 'theme_degrade', $langname);
        $html = "<a class='btn btn-info mt-1 mb-2' target='_blank'
                    href='{$CFG->wwwroot}/theme/degrade/_editor/?chave=home&editlang={$CFG->lang}'>{$text}</a>";
        if (!isset(get_config("theme_degrade", "home_htmleditor_{$CFG->lang}")[40])) {
            $html = "{$html} <strong class='alert-warning'>{$emptytext}</strong>";
        }
        $description .= "{$html}<br>";

        foreach ($listoftranslations as $langkey => $langname) {
            if ($CFG->lang == $langkey) {
                continue;
            }

            $text = get_string('editor_link_home', 'theme_degrade', $langname);
            $html = "<a class='btn btn-info mt-1' target='_blank'
                        href='{$CFG->wwwroot}/theme/degrade/_editor/?chave=home&editlang={$langkey}'>{$text}</a>";
            if (!isset(get_config("theme_degrade", "home_htmleditor_{$langkey}")[40])) {
                $html = "{$html} <strong class='alert-warning'>{$emptytext}</strong>";
            }
            $description .= "{$html}<br>";
        }
    }

} else {
    $description = get_string('content_type_home_desc', 'theme_degrade');
}
$choices = [
    0 => get_string("content_type_default", 'theme_degrade'),
    1 => get_string("content_type_html", 'theme_degrade'),
];
$setting = new admin_setting_configselect('theme_degrade/home_type',
    get_string('content_type_home', 'theme_degrade'),
    $description, 0, $choices);
$page->add($setting);
$PAGE->requires->js_call_amd('theme_degrade/settings', 'autosubmit', [$setting->get_id()]);

if (get_config('theme_degrade', 'home_type') != 0) {
    $icon = $OUTPUT->image_url("google-fonts", "theme_degrade")->out(false);
    $setting = new admin_setting_configtextarea('theme_degrade/pagefonts',
        get_string('content_pagefonts', 'theme_degrade'),
        get_string('content_pagefonts_desc', 'theme_degrade', $icon), "");
    $page->add($setting);
}

if (get_config('theme_degrade', 'home_type') == 0) {
    $setting = new admin_setting_heading("theme_degrade/heart", '',
        get_string('heart', 'theme_degrade', 'https://moodle.org/plugins/theme_degrade'));
    $page->add($setting);

    $setting = new admin_setting_heading("theme_degrade/theme_degrade_frontpage_bloco",
        get_string('theme_degrade_frontpage_bloco', 'theme_degrade', get_string('availablecourses')), '');
    $page->add($setting);

    $setting = new admin_setting_configtextarea('theme_degrade/frontpage_avaliablecourses_text',
        get_string('footer_frontpage_blockcourses_text', 'theme_degrade', get_string('availablecourses')),
        get_string('footer_frontpage_blockcourses_text_desc', 'theme_degrade', get_string('availablecourses')), '');
    $page->add($setting);

    $setting = new admin_setting_configcheckbox('theme_degrade/frontpage_avaliablecourses_instructor',
        get_string('footer_frontpage_blockcourses_instructor', 'theme_degrade'),
        get_string('footer_frontpage_blockcourses_instructor_desc', 'theme_degrade'),
        1);
    $page->add($setting);

    $setting = new admin_setting_heading("theme_degrade/theme_degrade_frontpage_bloco",
        get_string('theme_degrade_frontpage_bloco', 'theme_degrade', get_string('mycourses')), '');
    $page->add($setting);

    $setting = new admin_setting_configtextarea('theme_degrade/frontpage_mycourses_text',
        get_string('footer_frontpage_blockcourses_text', 'theme_degrade', get_string('mycourses')),
        get_string('footer_frontpage_blockcourses_text_desc', 'theme_degrade', get_string('mycourses')), '');
    $page->add($setting);

    $setting = new admin_setting_configcheckbox('theme_degrade/frontpage_mycourses_instructor',
        get_string('footer_frontpage_blockcourses_instructor', 'theme_degrade'),
        get_string('footer_frontpage_blockcourses_instructor_desc', 'theme_degrade'),
        1);
    $page->add($setting);
}

$settings->add($page);

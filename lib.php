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
 * @package   theme_degrade
 * @copyright 2018 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 *
 * @return string The parsed CSS The parsed CSS.
 */
function theme_degrade_process_css($css, $theme) {
    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_degrade_set_customcss($css, $customcss);

    $css = theme_degrade_set_awesome($css);

    return $css;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 *
 * @return bool
 */
function theme_degrade_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $theme = theme_config::load('degrade');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }

        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 *
 * @return string The CSS which now contains our custom CSS.
 */
function theme_degrade_set_customcss($css, $customcss) {
    $tag = '/*setting:customcss*/';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

function theme_degrade_set_awesome($css) {
    global $CFG;

    $wwwroot = str_replace('http://', '//', $CFG->wwwroot);
    $wwwroot = str_replace('https://', '//', $wwwroot);

    $css = str_replace('fonts/fontawesome', $wwwroot . '/theme/degrade/style/fonts/fontawesome', $css);

    return $css;
}

/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * Do not add Degrade specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 *
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_degrade_get_html_for_settings(renderer_base $output, moodle_page $page) {
    global $CFG;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    // Only display the logo on the front page and login page, if one is defined.
    if (!empty($page->theme->settings->logo) &&
        ($page->pagelayout == 'frontpage' || $page->pagelayout == 'login')
    ) {
        $return->heading = html_writer::tag('div', '', array('class' => 'logo'));
    } else {
        $return->heading = $output->page_heading();
    }

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = '<div class="footnote text-center">' . format_text($page->theme->settings->footnote) . '</div>';
    }

    return $return;
}

/**
 * All theme functions should start with theme_degrade_
 *
 * @deprecated since 2.5.1
 */
function degrade_process_css() {
    throw new coding_exception('Please call theme_' . __FUNCTION__ . ' instead of ' . __FUNCTION__);
}

/**
 * All theme functions should start with theme_degrade_
 *
 * @deprecated since 2.5.1
 */
function degrade_set_logo() {
    throw new coding_exception('Please call theme_' . __FUNCTION__ . ' instead of ' . __FUNCTION__);
}

/**
 * All theme functions should start with theme_degrade_
 *
 * @deprecated since 2.5.1
 */
function degrade_set_customcss() {
    throw new coding_exception('Please call theme_' . __FUNCTION__ . ' instead of ' . __FUNCTION__);
}

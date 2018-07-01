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
 * Core renderer fie.
 *
 * @package   theme_degrade
 * @copyright 2018 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/theme/bootstrapbase/renderers.php');

/**
 * Degrade core renderers.
 *
 * @package    theme_degrade
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_degrade_core_renderer extends theme_bootstrapbase_core_renderer {

    /**
     * Either returns the parent version of the header bar, or a version with the logo replacing the header.
     *
     * @since Moodle 2.9
     *
     * @param array $headerinfo An array of header information, dependant on what type of header is being displayed.
     *                            The following array example is user specific. heading => Override the page heading.
     *                            user => User object. usercontext => user context.
     * @param int $headinglevel What level the 'h' tag will be.
     *
     * @return string HTML for the header bar.
     */
    public function context_header($headerinfo = null, $headinglevel = 1) {
        if ($this->should_render_logo($headinglevel)) {
            return html_writer::tag('div', '', array('class' => 'logo'));
        }
        $headerinfo = null;
        $headinglevel = 1;

        return parent::context_header($headerinfo, $headinglevel);
    }

    /**
     * Determines if we should render the logo.
     *
     * @param int $headinglevel What level the 'h' tag will be.
     *
     * @return bool Should the logo be rendered.
     */
    protected function should_render_logo($headinglevel = 1) {
        global $PAGE;

        // Only render the logo if we're on the front page or login page
        // and the theme has a logo.
        if ($headinglevel == 1 && !empty($this->page->theme->settings->logo)) {
            if ($PAGE->pagelayout == 'frontpage' || $PAGE->pagelayout == 'login') {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the navigation bar home reference.
     *
     * The small logo is only rendered on pages where the logo is not displayed.
     *
     * @param bool $returnlink Whether to wrap the icon and the site name in links or not
     *
     * @return string The site name, the small logo or both depending on the theme settings.
     */
    public function navbar_home($returnlink = true) {
        global $CFG, $SITE;

        $imageurl1 = $this->page->theme->setting_file_url('logo', 'logo');
        if (!empty($imageurl1)) {
            $image1 = html_writer::img($imageurl1, get_string('sitelogo', 'theme_' . $this->page->theme->name),
                array('class' => 'logo-title'));

            $imageurl2 = $this->page->theme->setting_file_url('logowhite', 'logowhite');
            if (!empty($imageurl2)) {
                $image2 = html_writer::img ( $imageurl2, get_string ( 'sitelogo', 'theme_' . $this->page->theme->name ),
                    array( 'class' => 'logowhite-title' ) );
            }else{
                $image2 = html_writer::img($imageurl1, get_string('sitelogo', 'theme_' . $this->page->theme->name),
                    array('class' => 'logowhite-title'));
            }

            if ($returnlink) {
                return html_writer::link($CFG->wwwroot, $image1.$image2,
                    array('class' => 'logo-container', 'title' => get_string('home')));
            } else {
                return html_writer::tag('span', $image1.$image2, array('class' => 'logo-container'));
            }
        }

        if ($returnlink) {
            return html_writer::link($CFG->wwwroot, $SITE->fullname,
                array('class' => 'fullname-container', 'title' => get_string('home')));
        } else {
            return html_writer::tag('span', $SITE->fullname,
                array('class' => 'fullname-container'));
        }
    }
}

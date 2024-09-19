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
 * core_renderer.php
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\output;

use custom_menu;
use custom_menu_item;
use html_writer;

/**
 * Class core_renderer
 *
 * @package theme_degrade\output
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * custom_menu_drawer
     *
     * @return string
     *
     * @throws \coding_exception
     */
    public function custom_menu_drawer() {
        global $CFG;

        if (!empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        } else {
            return '';
        }

        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu, '', '', 'custom-menu-drawer');
    }

    /**
     * render_custom_menu
     *
     * @param custom_menu $menu
     * @param string $wrappre
     * @param string $wrappost
     * @param string $menuid
     *
     * @return string
     *
     * @throws \coding_exception
     */
    public function render_custom_menu(custom_menu $menu, $wrappre = '', $wrappost = '', $menuid = '') {
        if (!$menu->has_children()) {
            return '';
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            if (stristr($menuid, 'drawer')) {
                $content .= $this->render_custom_menu_item_drawer($item, 0, $menuid, false);
            } else {
                $content .= $this->render_custom_menu_item($item, 0, $menuid);
            }
        }
        $content = $wrappre . $content . $wrappost;
        return $content;
    }

    /**
     * render_custom_menu_item
     *
     * @param custom_menu_item $menunode
     * @param int $level = 0
     * @param string $menuid
     *
     * @return string
     *
     * @throws \coding_exception
     */
    protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0, $menuid = '') {
        static $submenucount = 0;

        // If the node has a url, then use it, even if it has children as the URL could be that of an overview page.
        if ($menunode->get_url() !== null) {
            $url = $menunode->get_url();
        } else {
            $url = '#';
        }
        if ($menunode->has_children()) {
            $content = "
                <li class='nav-item dropdown my-auto'>
                    <a href='{$url}'
                       class='dropdown-item dropdown-toggle nav-link aaaa'
                       role='button'
                       id='{$menuid}{$submenucount}'
                       aria-haspopup='true'
                       aria-expanded='false'
                       aria-controls='dropdown{$menuid}{$submenucount}'
                       data-target='{$url}'
                       data-toggle='dropdown'
                       title='{$menunode->get_title()}'>
                        {$this->get_text($menunode)}
                    </a>
                    <ul role='menu'
                        class='dropdown-menu'
                        id='dropdown{$menuid}{$submenucount}'
                        aria-labelledby='{$menuid}{$submenucount}'>";

            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 1, "{$menuid}{$submenucount}");
            }
            $content .= '</ul></li>';
        } else {
            if (preg_match("/^#+$/", $this->get_text($menunode))) {
                // This is a divider.
                $content = html_writer::start_tag('li', ['class' => 'dropdown-divider']);
            } else {
                if ($level == 0) {
                    $content = '<li class="nav-item">';
                    $linkclass = 'nav-link';
                } else {
                    $content = '<li>';
                    $linkclass = 'dropdown-item';
                }

                /* This is a bit of a cludge, but allows us to pass url, of type moodle_url with a param of
                 * "helptarget", which when equal to "_blank", will create a link with target="_blank" to allow the link to open
                 * in a new window.  This param is removed once checked.
                 */
                $attributes = [
                    'title' => $menunode->get_title(),
                    'class' => $linkclass,
                ];
                if (is_object($url) && (get_class($url) == 'moodle_url')) {
                    $helptarget = $url->get_param('helptarget');
                    if ($helptarget != null) {
                        $url->remove_params('helptarget');
                        $attributes['target'] = $helptarget;
                    }
                }
                $content .= html_writer::link($url, $this->get_text($menunode), $attributes);

                $content .= "</li>";
            }
        }
        return $content;
    }

    /**
     * render_custom_menu_item_drawer
     *
     * @param custom_menu_item $menunode
     * @param int $level = 0
     * @param string $menuid
     * @param bool $indent
     *
     * @return string
     *
     * @throws \coding_exception
     */
    protected function render_custom_menu_item_drawer(custom_menu_item $menunode, $level = 0, $menuid = '', $indent = false) {
        static $submenucount = 0;

        if ($menunode->has_children()) {
            $submenucount++;
            $content = "
                <li class='m-l-0'>
                    <a href='#{$menuid}{$submenucount}'
                       class='list-group-item dropdown-toggle'
                       aria-haspopup='true'
                       data-target='#'
                       data-toggle'collapse'
                       title='{$menunode->get_title()}'>
                        {$this->get_text($menunode)}
                    </a>
                    <ul class='collapse' id='{$menuid}{$submenucount}'>";
            $indent = true;
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item_drawer($menunode, 1, "{$menuid}{$submenucount}", $indent);
            }
            $content .= '</ul></li>';
        } else {
            // The node doesn't have children so produce a final menuitem.
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#';
            }

            if ($indent) {
                $dataindent = 1;
                $marginclass = 'm-l-1';
            } else {
                $dataindent = 0;
                $marginclass = 'm-l-0';
            }

            $content = "
                <li class='{$marginclass}'>
                    <a href='{$url}'
                       class='list-group-item list-group-item-action'
                       data-key=''
                       data-isexpandable='0'
                       data-indent='{$dataindent}'
                       data-showdivider='0'
                       data-type='1'
                       data-nodetype='1'
                       data-collapse='0'
                       data-forceopen='1'
                       data-isactive='1'
                       data-hidden='0'
                       data-preceedwithhr='0'
                       data-parent-key='{$menuid}'>
                        <div class='{$marginclass}'>
                            {$this->get_text($menunode)}
                        </div>
                    </a>
                </li>";
        }
        return $content;
    }

    /**
     * Get text translate
     *
     * @param custom_menu_item $menunode
     *
     * @return string
     *
     * @throws \coding_exception
     */
    public function get_text($menunode) {
        $text = $menunode->get_text();
        if (strpos($text, ",")) {
            $texts = explode(",", $text);

            return get_string($texts[0], $texts[1]);
        }

        return $text;
    }
}

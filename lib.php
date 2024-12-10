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
 * lib.php
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package     theme_degrade
 * @copyright   2024 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Page init functions runs every time page loads.
 *
 * @param moodle_page $page
 */
function theme_degrade_page_init(moodle_page $page) {
    global $CFG;

    $page->requires->jquery();

    $CFG->enableuserfeedback = false;
}

/**
 * Function theme_degrade_process_color_hex
 *
 * @param $colorname
 *
 * @return string
 * @throws coding_exception
 */
function theme_degrade_process_color_hex($colorname) {
    $color = theme_degrade_get_setting($colorname);

    $hex = [hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2))];
    return implode(",", $hex);
}

/**
 * Serves CSS for image file updated to styles.
 *
 * @param string $filename
 */
function theme_degrade_serve_css($filename) {
    global $CFG;
    if (!empty($CFG->themedir)) {
        $thestylepath = $CFG->themedir . '/degrade/style/';
    } else {
        $thestylepath = $CFG->dirroot . '/theme/degrade/style/';
    }

    $thesheet = $thestylepath . $filename;
    $etagfile = md5_file($thesheet);
    // File.
    $lastmodified = filemtime($thesheet);
    // Header.
    $ifmodifiedsince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
    $etagheader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

    if ((($ifmodifiedsince) && (strtotime($ifmodifiedsince) == $lastmodified)) || $etagheader == $etagfile) {
        theme_degrade_send_unmodified($lastmodified, $etagfile);
    }
    theme_degrade_send_cached_css($thestylepath, $filename, $lastmodified, $etagfile);
}

/**
 * Set browser cache used in php header.
 *
 * @param string $lastmodified
 * @param string $etag
 */
function theme_degrade_send_unmodified($lastmodified, $etag) {
    $lifetime = 60 * 60 * 24 * 60;
    header('HTTP/1.1 304 Not Modified');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Content-Type: text/css; charset=utf-8');
    header('Etag: "' . $etag . '"');
    if ($lastmodified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    }
    die;
}

/**
 * Cached css.
 *
 * @param string $path
 * @param string $filename
 * @param integer $lastmodified
 * @param string $etag
 */
function theme_degrade_send_cached_css($path, $filename, $lastmodified, $etag) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/configonlylib.php');
    // For min_enable_zlib_compression.
    // 60 days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;

    header('Etag: "' . $etag . '"');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . filesize($path . $filename));
    }

    readfile($path . $filename);
    die;
}

/**
 * Returns an object containing HTML for the areas affected by settings.
 * Do not add Clean specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page     Pass in $PAGE.
 *
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footer_description HTML to use as a footer_description. By default ''.
 * @throws coding_exception
 */
function theme_degrade_get_html_for_settings(renderer_base $output, moodle_page $page) {
    global $CFG;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    if (!empty($page->theme->settings->logo)) {
        $return->heading = html_writer::link($CFG->wwwroot, '', ['title' => get_string('home'), 'class' => 'logo']);
    } else {
        $return->heading = $output->page_heading();
    }

    $return->footer_description = '';
    if (!empty($page->theme->settings->footer_description)) {
        $return->footer_description = '<div class="footer_description text-center">';
        $return->footer_description .= format_text($page->theme->settings->footer_description) . '</div>';
    }

    return $return;
}

/**
 * Logo Image URL Fetch from theme settings
 *
 * @param string $local
 *
 * @return string $logo
 * @throws dml_exception
 */
function theme_degrade_get_logo($local = null) {
    global $SITE;

    $logocolor = get_config('theme_degrade', 'logo_color');
    $logowrite = get_config('theme_degrade', 'logo_write');
    if (empty($logocolor)) {
        return "<span>{$SITE->shortname}</span>";
    }

    $urllogocolor = moodle_url::make_pluginfile_url(context_system::instance()->id, 'theme_degrade', 'logo_color', '',
        theme_get_revision(), $logocolor);
    $urllogowrite = moodle_url::make_pluginfile_url(context_system::instance()->id, 'theme_degrade', 'logo_write', '',
        theme_get_revision(), $logowrite);

    if ($urllogocolor) {
        if ($urllogowrite) {
            return "
                <img class='logo-color' src='{$urllogocolor->out(false)}' alt='{$SITE->fullname}'>
                <img class='logo-write' src='{$urllogowrite->out(false)}' alt='{$SITE->fullname}'>";
        } else {
            return "<img class='logo-all' src='{$urllogocolor->out(false)}' alt='{$SITE->fullname}'>";
        }
    } else {
        return "<span>{$SITE->shortname}</span>";
    }
}

/**
 * theme_degrade_get_body_class
 *
 * @return string
 * @throws coding_exception
 */
function theme_degrade_get_body_class() {
    return "degrade-theme-" . theme_degrade_get_setting("background_color", false);
}

/**
 * Functions helps to get the admin config values which are related to the
 * theme
 *
 * @param string $setting
 * @param bool $format
 *
 * @return bool
 * @throws coding_exception
 */
function theme_degrade_get_setting($setting, $format = true) {
    global $CFG;

    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('degrade');
    }

    if (empty($theme->settings->$setting)) {
        return false;
    } else if ($format === true) {
        return format_string($theme->settings->$setting);
    } else if ($format === FORMAT_PLAIN) {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === FORMAT_HTML) {
        return format_text($theme->settings->$setting, FORMAT_HTML);
    } else {
        return $theme->settings->$setting;
    }
}

/**
 * Renderer the slider images.
 *
 * @param string $imagesetting
 *
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function theme_degrade_get_setting_image($imagesetting) {
    if (theme_degrade_get_setting($imagesetting)) {
        $imagesettingurl = theme_degrade_setting_file_url($imagesetting, $imagesetting);
    }
    if (empty($imagesettingurl)) {
        $imagesettingurl = '';
    }

    return $imagesettingurl;
}

/**
 * theme_degrade_setting_file_url
 *
 * @param string $setting
 * @param string $filearea
 *
 * @return moodle_url|null
 * @throws dml_exception
 */
function theme_degrade_setting_file_url($setting, $filearea) {
    global $CFG, $PAGE;

    if (empty($PAGE->theme->settings->$setting)) {
        return null;
    }

    $itemid = theme_get_revision();
    $filepath = $PAGE->theme->settings->$setting;
    $syscontext = context_system::instance();

    $url = moodle_url::make_file_url(
        "$CFG->wwwroot/pluginfile.php",
        "/{$syscontext->id}/theme_degrade/{$filearea}/{$itemid}{$filepath}");

    return $url;
}

/**
 * Return the current theme url
 *
 * @return string
 */
function theme_degrade_theme_url() {
    global $CFG, $PAGE;
    $themeurl = $CFG->wwwroot . '/theme/' . $PAGE->theme->name;
    return $themeurl;
}

/**
 * Display Footer Block Custom Links
 *
 * @param string $menuname Footer block link name.
 *
 * @return string The Footer links are return.
 * @throws coding_exception
 * @throws moodle_exception
 */
function theme_degrade_generate_links($menuname = '') {
    $htmlstr = '';
    $menustr = theme_degrade_get_setting($menuname);
    $menusettings = explode("\n", $menustr);
    foreach ($menusettings as $menukey => $menuval) {
        $expset = explode("|", $menuval);
        if (!empty($expset) && isset($expset[0]) && isset($expset[1])) {
            list($ltxt, $lurl) = $expset;
            $ltxt = trim($ltxt);
            $lurl = trim($lurl);
            if (empty($ltxt)) {
                continue;
            }
            if (empty($lurl)) {
                $lurl = 'javascript:void(0);';
            }

            $pos = strpos($lurl, 'http');
            if ($pos === false) {
                $lurl = new moodle_url($lurl);
            }
            $htmlstr .= '<li><a href="' . $lurl . '">' . $ltxt . '</a></li>' . "\n";
        }
    }
    return $htmlstr;
}

/**
 * Fetch the hide course ids
 *
 * @return array
 * @throws dml_exception
 */
function theme_degrade_hidden_courses_ids() {
    global $DB;
    $hcourseids = [];
    $result = $DB->get_records_sql("SELECT id FROM {course} WHERE visible='0' ");
    if (!empty($result)) {
        foreach ($result as $row) {
            $hcourseids[] = $row->id;
        }
    }
    return $hcourseids;
}

/**
 * Remove the html special tags from course content.
 * This function used in course home page.
 *
 * @param string $text
 *
 * @return string
 */
function theme_degrade_strip_html_tags($text) {
    $text = preg_replace(
        [
            // Remove invisible content.
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks.
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ],
        [
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ],
        $text
    );
    return strip_tags($text);
}

/**
 * Cut the Course content.
 *
 * @param string $str
 * @param integer $n
 * @param string $endchar
 *
 * @return string $out
 */
function theme_degrade_course_trim_char($str, $n = 500, $endchar = '&#8230;') {
    if (strlen($str) < $n) {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(["\r\n", "\r", "\n"], ' ', $str));
    if (strlen($str) <= $n) {
        return $str;
    }

    $small = substr($str, 0, $n);
    $out = $small . $endchar;
    return $out;
}

/**
 * Function returns the rgb format with the combination of passed color hex and opacity.
 *
 * @param string $hexa
 * @param int $opacity
 *
 * @return string
 */
function theme_degrade_get_hexa($hexa, $opacity) {
    if (!empty($hexa)) {
        list($r, $g, $b) = sscanf($hexa, "#%02x%02x%02x");
        if ($opacity == '') {
            $opacity = 0.0;
        } else {
            $opacity = $opacity / 10;
        }
        return "rgba($r, $g, $b, $opacity)";
    }

    return "";
}

/**
 * theme_degrade_coursemodule_standard_elements
 *
 * @param moodleform_mod $data   The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 *
 * @throws coding_exception
 * @throws dml_exception
 */
function theme_degrade_coursemodule_standard_elements($data, $mform) {
    global $CFG;

    if ($CFG->theme == "degrade") {
        $mform->addElement('header', 'theme_degrade_icons',
            get_string('settings_icons_change_icons', 'theme_degrade'));
        $configuration = get_string('configuration');
        $link = "<a href='{$CFG->wwwroot}/admin/settings.php?section=themesettingdegrade#theme_degrade_icons'
                target='_blank'>{$configuration}</a>";

        if ($settingsiconsnum = get_config('theme_degrade', 'settings_icons_num')) {

            $choices = [0 => get_string("settings_icons_none", 'theme_degrade')];
            for ($i = 1; $i <= $settingsiconsnum; $i++) {
                $name = get_config('theme_degrade', "settings_icons_name_{$i}");
                $image = get_config('theme_degrade', "settings_icons_image_{$i}");

                if ($name && $image) {
                    $choices[$i] = $name;
                }
            }

            if ($data->get_coursemodule() && isset($data->get_coursemodule()->id)) {
                $name = "theme_degrade_customicon_{$data->get_coursemodule()->id}";
                $customicon = get_config('theme_degrade', $name);

                $data->set_data(['theme_degrade_customicon' => $customicon]);
            }

            $mform->addElement('select', 'theme_degrade_customicon',
                get_string('settings_icons_select_icon', 'theme_degrade', $link),
                $choices);
        } else {
            $mform->addElement('html', get_string('settings_icons_module_disable', 'theme_degrade', $link));
        }
    }
}

/**
 * Hook the add/edit of the course module.
 *
 * @param moodleform $data Data from the form submission.
 * @param stdClass $course The course.
 *
 * @return moodleform
 * @throws dml_exception
 */
function theme_degrade_coursemodule_edit_post_actions($data, $course) {
    $name = "theme_degrade_customicon_{$data->coursemodule}";
    $customicon = get_config('theme_degrade', $name);
    if (isset($data->theme_degrade_customicon)) {
        if ($customicon != $data->theme_degrade_customicon) {
            set_config($name, $data->theme_degrade_customicon, 'theme_degrade');
            theme_reset_all_caches();
        }
    }

    return $data;
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
 * @throws coding_exception
 * @throws moodle_exception
 */
function theme_degrade_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    static $theme;

    if (empty($theme)) {
        $theme = theme_config::load('degrade');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea === 'style') {
            theme_degrade_serve_css($args[1]);
        } else if ($filearea === 'editor_home' || $filearea === 'editor_footer') {
            $fullpath = sha1("/{$context->id}/theme_degrade/{$filearea}/{$args[0]}/{$args[1]}");
            $fs = get_file_storage();
            if ($file = $fs->get_file_by_hash($fullpath)) {
                return send_stored_file($file, 0, 0, false, $options);
            }
            send_file_not_found();
        } else if ($filearea === 'pagebackground') {
            return $theme->setting_file_serve('pagebackground', $args, $forcedownload, $options);
        } else if (preg_match("/slide[1-9][0-9]*image/", $filearea) !== false) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }

    return false;
}

/**
 * Loads the CSS Styles and replace the background images.
 * If background image not available in the settings take the default images.
 *
 * @param string $css
 * @param string $theme
 *
 * @return string $css
 * @throws coding_exception
 * @throws dml_exception
 */
function theme_degrade_process_css($css, $theme) {
    global $DB;

    // Import site fonts.
    $fontimport = \theme_degrade\fonts\font_util::css('sitefonts');
    $css = "@import url('{$fontimport}');\n{$css}";

    // Local css.
    if (@file_exists(__DIR__ . "/style/degrade.css")) {
        $customcss = file_get_contents(__DIR__ . "/style/degrade.css");

        $css = "{$css}\n{$customcss}";
    }

    // Color list.
    $css =
        ":root {\n" .
        "    --color_primary:   " . theme_degrade_process_color_hex("theme_color__color_primary") . "   !important;\n" .
        "    --color_secondary: " . theme_degrade_process_color_hex("theme_color__color_secondary") . " !important;\n" .
        "    --color_buttons:   " . theme_degrade_process_color_hex("theme_color__color_buttons") . "   !important;\n" .
        "    --color_names:     " . theme_degrade_process_color_hex("theme_color__color_names") . "     !important;\n" .
        "    --color_titles:    " . theme_degrade_process_color_hex("theme_color__color_titles") . "    !important;\n" .
        "}" . $css;

    // Custom CSS.
    $customcss = str_replace("&gt;", ">", theme_degrade_get_setting("customcss"));
    $css = "{$css}\n{$customcss}";

    // Icons modules.
    $sql = "SELECT name, value  FROM {config_plugins} WHERE name LIKE 'theme_degrade_customicon_%'";
    $customicons = $DB->get_records_sql($sql);
    foreach ($customicons as $customicon) {
        $moduleid = str_replace("theme_degrade_customicon_", "", $customicon->name);
        $slideshowimage = theme_degrade_get_setting_image("settings_icons_image_{$customicon->value}");
        $css .= "
            #module-{$moduleid} .courseicon img,
            .cmid-{$moduleid} #page-header .activityiconcontainer img {
                content : url('{$slideshowimage}');
            }
            #course-index-cm-{$moduleid} .courseindex-link {
                display     : flex;
                align-items : center;
            }
            #course-index-cm-{$moduleid} .courseindex-link::before {
                content           : '';
                display           : block;
                height            : 20px;
                width             : 20px;
                min-width         : 20px;
                background-image  : url('{$slideshowimage}');
                background-size   : contain;
                background-repeat : no-repeat;
                margin-right      : 5px;
            }
            #course-index-cm-{$moduleid}.pageitem .courseindex-link::before {
                filter: invert(1);
            }
            #course-index-cm-{$moduleid}.pageitem:hover .courseindex-link::before {
                filter: invert(0);
            }";
    }

    // Color on roll page.
    global $CFG;
    if ($CFG->theme != "boost_training") {
        $topscrollbackgroundcolor = theme_degrade_get_setting("top_scroll_background_color");
        $topscrolltextcolor = theme_degrade_get_setting("top_scroll_text_color");

        $css .= "
            #header.fixed-top .header-menubar .navbar-nav .simplesearchform .btn-open,
            #header.fixed-top  .popover-region .popover-region-toggle i.icon,
            #header.fixed-top .header-menubar .navbar-nav .usermenu .dropdown a#user-menu-toggle,
            #header.fixed-top .header-menubar .navbar-nav .editmode-switch-form .input-group label,
            #header.fixed-top .usermenu .moodle-actionmenu a.dropdown-toggle,
            .navbar-light.fixed-top .navbar-nav .show>.nav-link,
            .navbar-light.fixed-top .navbar-nav .active>.nav-link,
            .navbar-light.fixed-top .navbar-nav .nav-link.show,
            .navbar-light.fixed-top .navbar-nav .nav-link.active,
            #header.fixed-top .header-logo a.navbar-brand img,
            #header.fixed-top .header-logo a.navbar-brand span,
            #header.fixed-top .header-menubar .primary-navigation ul.navbar-nav > li > a {
                color: {$topscrolltextcolor} !important;
            }
            #header.fixed-top {
                background: {$topscrollbackgroundcolor} !important;
            }

            #header.ever-fixed-top .header-menubar .navbar-nav .simplesearchform .btn-open,
            #header.ever-fixed-top  .popover-region .popover-region-toggle i.icon,
            #header.ever-fixed-top .header-menubar .navbar-nav .usermenu .dropdown a#user-menu-toggle,
            #header.ever-fixed-top .header-menubar .navbar-nav .editmode-switch-form .input-group label,
            #header.ever-fixed-top .usermenu .moodle-actionmenu a.dropdown-toggle,
            .navbar-light.ever-fixed-top .navbar-nav .show>.nav-link,
            .navbar-light.ever-fixed-top .navbar-nav .active>.nav-link,
            .navbar-light.ever-fixed-top .navbar-nav .nav-link.show,
            .navbar-light.ever-fixed-top .navbar-nav .nav-link.active,
            #header.ever-fixed-top .header-logo a.navbar-brand img,
            #header.ever-fixed-top .header-logo a.navbar-brand span,
            #header.ever-fixed-top .header-menubar .primary-navigation ul.navbar-nav > li > a {
                color: {$topscrolltextcolor} !important;
            }
            #header.ever-fixed-top .custom-switch .custom-control-label:after{
                background: {$topscrolltextcolor} !important;
            }
            #header.ever-fixed-top {
                background: {$topscrollbackgroundcolor} !important;
            }";
    }

    // Fonts.
    $fontfamilytext = theme_degrade_get_setting("fontfamily");
    $fontfamilytext = isset($fontfamilytext[3]) ? "{$fontfamilytext}," : "";
    $fontfamilytext = "body,* {font-family:{$fontfamilytext} Arial, Helvetica, sans-serif}";

    $fontfamilytitle = theme_degrade_get_setting("fontfamily_title");
    $fontfamilytitle = isset($fontfamilytitle[3]) ? "{$fontfamilytitle}," : "";
    $fontfamilytitle = "h1,h2,h3,h4,h5,h6 {font-family:{$fontfamilytitle} Arial, Helvetica, sans-serif}";

    $fontfamilysitename = theme_degrade_get_setting("fontfamily_sitename");
    $fontfamilysitename = isset($fontfamilysitename[3]) ? "{$fontfamilysitename}," : "";
    $fontfamilysitename = ".header-logo a.navbar-brand span {font-family:{$fontfamilysitename} Arial, Helvetica, sans-serif}";

    $fontfamilymenus = theme_degrade_get_setting("fontfamily_menus");
    $fontfamilymenus = isset($fontfamilymenus[3]) ? "{$fontfamilymenus}," : "";
    $fontfamilymenus = ".header-menubar .primary-navigation ul.navbar-nav li a,
            .header-menubar .navbar-nav .usermenu .dropdown a#user-menu-toggle,
            .header-menubar .navbar-nav .editmode-switch-form .input-group label,
            .usermenu .moodle-actionmenu a.dropdown-toggle,
            .navbar-light .navbar-nav .show>.nav-link,
            .navbar-light .navbar-nav .active>.nav-link,
            .navbar-light .navbar-nav .nav-link.show,
            .navbar-light .navbar-nav .nav-link.active,
            .header-logo a.navbar-brand img {
                font-family : {$fontfamilymenus} Arial, Helvetica, sans-serif
            }";

    $themecss = "";
    $backgroundcolor = theme_degrade_get_setting("background_color", false);
    if (preg_match('/^#[a-fA-F0-9]{6}$/', $backgroundcolor)) {
        $themecss = "
            #header,
            #nav-drawer .list-group-item.active > div,
            #nav-drawer .list-group-item.active:hover,
            #footer .footer-main {
                background: {$backgroundcolor};
            }
            #site-news-forum,
            .frontpage-course-list {
                /* background: {$backgroundcolor}3d;*/
            }
            .frontpage-course-list .btn-primary {
                background: {$backgroundcolor};
            }";
    }

    $css = "{$css}\n{$fontfamilytext}\n{$fontfamilytitle}\n{$fontfamilysitename}\n{$fontfamilymenus}\n{$themecss}";

    return $css;
}

/**
 * Function theme_degrade_add_htmlattributes
 *
 * @return array
 */
function theme_degrade_add_htmlattributes() {
    $thememode = get_user_preferences("theme_mode", "light");
    $layouturl = optional_param("theme_mode", false, PARAM_TEXT);
    if ($layouturl) {
        $thememode = $layouturl;
    }
    return ['data-bs-theme' => $thememode];
}

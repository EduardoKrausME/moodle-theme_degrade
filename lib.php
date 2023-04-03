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
 *
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package     theme_degrade
 * @copyright   2023 Eduardo kraus (http://eduardokraus.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Page init functions runs every time page loads.
 *
 * @param moodle_page $page
 *
 * @return null
 */
function theme_degrade_page_init(moodle_page $page) {
    $page->requires->jquery();
    $page->requires->js('/theme/degrade/javascript/theme.js');
}

/**
 * Loads the CSS Styles and replace the background images.
 * If background image not available in the settings take the default images.
 *
 * @param string $css
 * @param string $theme
 *
 * @return string $css
 */
function theme_degrade_process_css($css, $theme) {

    if (preg_match('/root.*--color_theme_primary:/s', $css)) {
        if (isset($theme->settings->customcss[3])) {
            $customcss = $theme->settings->customcss;

            preg_match_all('/#(\w{3,6});/', $customcss, $cores);

            foreach ($cores[1] as $color) {
                if (isset($color[4])) {
                    $hex = array("{$color[0]}{$color[1]}", "{$color[2]}{$color[3]}", "{$color[4]}{$color[5]}");
                } else {
                    $hex = array("{$color[0]}{$color[0]}", "{$color[1]}{$color[1]}", "{$color[2]}{$color[2]}");
                }
                $rgb = implode(",", array_map('hexdec', $hex));
                $customcss = str_replace("#{$color}", $rgb, $customcss);
            }


            $themas = ['color_primary', 'color_secondary', 'color_buttons', 'color_names', 'color_titles'];
            foreach ($themas as $thema) {
                preg_match("/{$thema}:(.*?);/", $customcss, $partes);
                if (isset($partes[1])) {
                    $newthema = str_replace("color_", "color_theme_", $thema);
                    $css = preg_replace("/{$newthema}:(.*?);/", "{$newthema}:{$partes[1]};", $css);;
                }
            }
        }

        if (isset($theme->settings->fontfamily[3])) {
            $fontfamily = "@import url(https://fonts.googleapis.com/css2?family={$theme->settings->fontfamily}:" .
                "ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap);\n";
            $fontfamily .= "body{font-family: {$theme->settings->fontfamily}, Arial, Helvetica, sans-serif;}";
        } else {
            $fontfamily = "body{font-family: Arial, Helvetica, sans-serif;}";
        }

        $css = "{$css}\n{$theme->settings->customcss}\n{$fontfamily}";
    }

    return $css;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context  $context
 * @param string   $filearea
 * @param array    $args
 * @param bool     $forcedownload
 * @param array    $options
 *
 * @return bool
 * @throws coding_exception
 * @throws moodle_exception
 */
function theme_degrade_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $theme;

    if (empty($theme)) {
        $theme = theme_config::load('degrade');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea === 'style') {
            theme_degrade_serve_css($args[1]);
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
}

/**
 * Serves CSS for image file updated to styles.
 *
 * @param string $filename
 *
 * @return string
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
 *
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
 * @param string  $path
 * @param string  $filename
 * @param integer $lastmodified
 * @param string  $etag
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
 *
 * Do not add Clean specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page   $page   Pass in $PAGE.
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
        $return->heading = html_writer::link($CFG->wwwroot, '', array('title' => get_string('home'), 'class' => 'logo'));
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
 * @param string $type
 *
 * @return string $logo
 */
function theme_degrade_get_logo($local) {

    global $OUTPUT, $SITE;

    $url = $OUTPUT->get_logo_url();
    if ($url) {
        return "<img src='{$url->out(false)}' alt='{$SITE->fullname}'>";
    } else {
        return "<span>{$SITE->shortname}</span>";
    }
}


/**
 * @return string
 * @throws coding_exception
 */
function theme_degrade_get_body_class() {
    return "theme-" . theme_degrade_get_setting("background_color", false);
}


/**
 * Functions helps to get the admin config values which are related to the
 * theme
 *
 * @param string $setting
 * @param bool   $format
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
    } else {
        return $theme->settings->$setting;
    }
}

/**
 * Renderer the slider images.
 *
 * @param $slideshowimage
 *
 * @return string
 * @throws coding_exception
 */
function theme_degrade_get_setting_image($slideshowimage) {
    global $PAGE;

    if (theme_degrade_get_setting($slideshowimage)) {
        $slideshowimageurl = $PAGE->theme->setting_file_url($slideshowimage, $slideshowimage);
    }
    if (empty($slideshowimageurl)) {
        $slideshowimageurl = '';
    }

    return $slideshowimageurl;
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
 */
function theme_degrade_generate_links($menuname = '') {
    global $CFG, $PAGE;
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
 */
function theme_degrade_hidden_courses_ids() {
    global $DB;
    $hcourseids = array();
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
        array(
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
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text
    );
    return strip_tags($text);
}

/**
 * Cut the Course content.
 *
 * @param string  $str
 * @param integer $n
 * @param string  $endchar
 *
 * @return string $out
 */
function theme_degrade_course_trim_char($str, $n = 500, $endchar = '&#8230;') {
    if (strlen($str) < $n) {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));
    if (strlen($str) <= $n) {
        return $str;
    }

    $out = "";
    $small = substr($str, 0, $n);
    $out = $small . $endchar;
    return $out;
}

/**
 * Function returns the rgb format with the combination of passed color hex and opacity.
 *
 * @param string $hexa
 * @param int    $opacity
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
}

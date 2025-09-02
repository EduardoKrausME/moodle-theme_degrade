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
 * Upgrade file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * function xmldb_supervideo_upgrade
 *
 * @param int $oldversion
 * @return bool
 * @throws Exception
 */
function xmldb_theme_degrade_upgrade($oldversion) {
    global $DB, $USER, $CFG;

    $color = get_config("theme_degrade", "background_color");
    set_config("startcolor", $color, "theme_degrade");
    set_config("brandcolor", $color, "theme_degrade");

    // Home Editor.
    if (get_config('theme_degrade', 'home_type') != 0) {
        $homehtmleditor = get_config("theme_degrade", "home_htmleditor_{$CFG->lang}");
        if (!isset($homehtmleditor[40])) {
            $page = (object) [
                "local" => "home",
                "type" => "html",
                "title" => "Home",
                "html" => $homehtmleditor,
                "info" => "{}",
                "template" => "",
                "lang" => $USER->lang,
                "sort" => time(),
            ];
            $DB->insert_record("theme_degrade_pages", $page);
        }

        $listoftranslations = get_string_manager()->get_list_of_translations();
        foreach ($listoftranslations as $langkey => $langname) {
            if ($CFG->lang == $langkey) {
                continue;
            }

            $homehtmleditor = get_config("theme_degrade", "home_htmleditor_{$langkey}");
            if (!isset($homehtmleditor[40])) {
                $page = (object) [
                    "local" => "home",
                    "type" => "html",
                    "title" => "Home {$langkey}",
                    "html" => $homehtmleditor,
                    "info" => "{}",
                    "template" => "",
                    "lang" => $langkey,
                    "sort" => time(),
                ];
                $DB->insert_record("theme_degrade_pages", $page);
            }
        }
    } else {
        if (get_config("theme_degrade", "frontpage_about_enable")) {
            $frontpage_about_logo = get_config("theme_degrade", "frontpage_about_logo");
            $frontpage_about_title = get_config("theme_degrade", "frontpage_about_title");
            $frontpage_about_description = get_config("theme_degrade", "frontpage_about_description");

            if (!empty($frontpage_about_logo)) {
                $frontpage_about_logo = '<img class="frontpage_about_logo" src="' . $frontpage_about_logo . '" alt="Logo">';
            }
            $about = '
                <style>
                    .frontpage_about_area {
                        display: flex;
                        flex-direction: column;
                        gap: 40px;
                        margin: 40px auto;
                        max-width: 1200px;
                        padding: 20px;
                    }
                    .frontpage_about_logoarea {
                        text-align: center;
                    }
                    .frontpage_about_logoarea img.frontpage_about_logo {
                        max-height: 120px;
                        margin-bottom: 15px;
                    }
                    .frontpage_about_logoarea h3 {
                        font-size: 28px;
                        font-weight: 600;
                        margin-bottom: 10px;
                        color: var(--primary, #2c3e50);
                    }
                    .frontpage_about_description {
                        font-size: 16px;
                        color: #555;
                        margin: 0 auto 20px;
                        max-width: 700px;
                        line-height: 1.6;
                    }
                    .frontpage_about_counterbox {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                        gap: 30px;
                    }
                    .frontpage_about_box {
                        background: #fff;
                        border-radius: 12px;
                        padding: 20px 30px;
                        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
                        text-align: center;
                        width: 220px;
                        position: relative;
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                    }
                    .frontpage_about_box:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
                    }
                    .frontpage_about_box .separator {
                        width: 40px;
                        height: 3px;
                        background: var(--primary, #0073e6);
                        display: block;
                        margin: 0 auto 15px;
                        border-radius: 2px;
                    }
                    .frontpage_about_box .number {
                        font-size: 32px;
                        font-weight: 700;
                        color: var(--primary, #0073e6);
                        margin-bottom: 10px;
                    }
                    .frontpage_about_box .title {
                        font-size: 16px;
                        font-weight: 500;
                        color: #333;
                    }
                </style>
                <div class="frontpage_about_area">
                    <div class="frontpage_about_logoarea text-center">
                        ' . $frontpage_about_logo . '
                        <h3>' . $frontpage_about_title . '</h3>
                        <div class="frontpage_about_description">' . $frontpage_about_description . '</div>
                    </div>
                    <div class="frontpage_about_counterbox text-center">';

            for ($i = 1; $i <= 4; $i++) {
                $frontpage_about_text = get_config("theme_degrade", "frontpage_about_text_{$i}");
                $frontpage_about_number = get_config("theme_degrade", "frontpage_about_number_{$i}");

                if ($frontpage_about_number && isset($frontpage_about_text[3])) {
                    $about .= '
                        <div class="frontpage_about_box">
                            <span class="separator"></span>
                            <div class="number">
                                <span class="number_counter text-primary">' . $frontpage_about_number . '</span>
                            </div>
                            <div class="title_counter">
                                <h4 class="title">' . $frontpage_about_text . '</h4>
                            </div>
                        </div>';
                }
            }

            $about .= "</div></div>";

            $page = (object) [
                "local" => "home",
                "type" => "html",
                "title" => "About",
                "html" => $about,
                "info" => "{}",
                "template" => "",
                "lang" => $USER->lang,
                "sort" => time(),
            ];
            $DB->insert_record("theme_degrade_pages", $page);
        }
    }

    if($customcss = get_config("theme_degrade", "customcss")) {
        set_config("scss", $customcss, "theme_degrade");
    }

    // Footer.
    if (get_config("theme_degrade", "footer_type") == 0) {
        // Footer description.
        $footerdescription = get_config("theme_degrade", "footer_description");
        if (isset($footerdescription[3])) {
            set_config("footer_html_1", $footerdescription, "theme_degrade");
        }

        // Links Util.
        $footerlinkstitle = get_config("theme_degrade", "footer_links_title");
        $footerlinks = get_config("theme_degrade", "footer_links");
        if (!empty($footerlinkstitle) && !empty($footerlinks)) {
            set_config("footer_title_2", $footerlinkstitle, "theme_degrade");

            $html = theme_degrade_generate_links($footerlinks);
            set_config("footer_html_2", $html, "theme_degrade");
        }

        // Social.
        $footersocialtitle = get_config("theme_degrade", "footer_social_title");
        $facebook = get_config("theme_degrade", "social_facebook");
        $youtube = get_config("theme_degrade", "social_youtube");
        $linkedin = get_config("theme_degrade", "social_linkedin");
        $twitter = get_config("theme_degrade", "social_twitter");
        $instagram = get_config("theme_degrade", "social_instagram");
        if (!empty($footersocialtitle) && ($facebook || $youtube || $linkedin || $twitter || $instagram)) {
            set_config("footer_title_3", $footersocialtitle, "theme_degrade");

            $html = "<div>";
            $html .= "<divclass='footer-icons'>";
            if ($facebook) {
                $html .= html_writer::link($facebook, html_writer::tag("i", "", ["class" => "fa fa-facebook"]),
                    ["target" => "_blank"]);
            }
            if ($youtube) {
                $html .= html_writer::link($youtube, html_writer::tag("i", "",
                    ["class" => "fa fa-youtube"]), ["target" => "_blank"]
                );
            }
            if ($linkedin) {
                $html .= html_writer::link($linkedin, html_writer::tag("i", "",
                    ["class" => "fa fa-linkedin"]),
                    ["target" => "_blank"]);
            }
            if ($twitter) {
                $html .= html_writer::link($twitter, html_writer::tag("i", "",
                    ["class" => "fa fa-twitter"]), ["target" => "_blank"]
                );
            }
            if ($instagram) {
                $html .= html_writer::link($instagram, html_writer::tag("i", "",
                    ["class" => "fa fa-instagram"]),
                    ["target" => "_blank"]);
            }
            $html .= "<div><div>";
            set_config("footer_html_3", $html, "theme_degrade");
        }

        // Contact.
        $contacttitle = get_config("theme_degrade", "contact_footer_title");
        $address = get_config("theme_degrade", "contact_address");
        $phone = get_config("theme_degrade", "contact_phone");
        $email = get_config("theme_degrade", "contact_email");
        if ($contacttitle && ($address || $phone || $email)) {
            set_config("footer_title_4", $contacttitle, "theme_degrade");

            $html = '<div class="footer-contact">';
            if ($address) {
                $html .= '<p class="contact-address">' . format_text($address, FORMAT_HTML) . '</p>';
            }
            if ($phone) {
                $html .= '<p class="contact-phone"><a href="tel:' . preg_replace('/\D+/', '', $phone) . '">' . s($phone) .
                    '</a></p>';
            }
            if ($email) {
                $html .= '<p class="contact-email"><a href="mailto:' . s($email) . '">' . s($email) . '</a></p>';
            }
            $html .= '</div>';

            set_config("footer_html_4", $html, 'theme_degrade');
        }
    }

    return true;
}

/**
 * Convert Footer Links
 *
 * @param string $menustr Footer block link name.
 * @return string The Footer links are return.
 * @throws Exception
 */
function theme_degrade_generate_links($menustr = "") {
    $htmlstr = "";
    $menusettings = explode("\n", $menustr);
    foreach ($menusettings as $menukey => $menuval) {
        $expset = explode("|", $menuval);
        if (!empty($expset) && isset($expset[0]) && isset($expset[1])) {
            [$ltxt, $lurl] = $expset;
            $ltxt = trim($ltxt);
            $lurl = trim($lurl);
            if (empty($ltxt)) {
                continue;
            }
            if (empty($lurl)) {
                $lurl = "javascript:void(0);";
            }

            $pos = strpos($lurl, "http");
            if ($pos === false) {
                $lurl = new moodle_url($lurl);
            }
            $htmlstr .= '<li><a href="' . $lurl . '">' . $ltxt . '</a></li>' . "\n";
        }
    }
    return $htmlstr;
}

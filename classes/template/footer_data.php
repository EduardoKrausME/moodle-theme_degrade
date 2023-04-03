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
 * User: Eduardo Kraus
 * Date: 01/04/2023
 * Time: 11:38
 */

namespace theme_degrade\template;


class footer_data {

    /**
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_data() {
        global $CFG;

        $data = array_merge(self::description(), self::links(), self::social(), self::contact());

        $footerenableblock = $data['enable_block_description'] +
            $data['enable_block_links'] +
            $data['enable_block_social'] +
            $data['enable_block_contact'];

        switch ($footerenableblock) {
            case 1:
                $footerblockclass = 'col-md-12';
                break;
            case 2:
                $footerblockclass = 'col-md-6';
                break;
            case 3:
                $footerblockclass = 'col-md-4';
                break;
            case 4:
                $footerblockclass = 'col-md-3';
                break;
            default:
                $footerblockclass = '';
        }

        $data['footer_enable_block'] = $footerenableblock;
        $data['footer_block_class'] = $footerblockclass;
        if (has_capability('moodle/site:config', \context_system::instance())) {
            $data['footer_settings_edit'] = "{$CFG->wwwroot}/admin/settings.php?section=themesettingdegrade#theme_degrade_footer";
        }
        $data['logourl_footer'] = theme_degrade_get_logo("footer");

        return $data;
    }

    /**
     * @throws \coding_exception
     */
    private static function description() {
        $footerdescription = theme_degrade_get_setting('footer_description');
        $footerdescription = !empty($footerdescription) ? $footerdescription : '';

        return [
            'enable_block_description' => 1,// + ($footerdescription != ''),
            'footer_description' => $footerdescription,
        ];
    }

    /**
     * @return array
     * @throws \coding_exception
     */
    private static function links() {
        $footerlinkstitle = theme_degrade_get_setting('footer_links_title');
        $footerlinkstitle = !empty($footerlinkstitle) ? $footerlinkstitle : '';
        $footerlinks = theme_degrade_generate_links('footer_links');

        return [
            'enable_block_links' => 0 + ($footerlinks != ''),
            'footer_links' => $footerlinks,
            'footer_links_title' => $footerlinkstitle,
        ];
    }

    /**
     * @return array
     * @throws \coding_exception
     */
    private static function social() {
        $footersocialtitle = theme_degrade_get_setting('footer_social_title');
        $footersocialtitle = !empty($footersocialtitle) ? $footersocialtitle : '';

        $socialfacebook = theme_degrade_get_setting('social_facebook');
        $socialfacebook = trim($socialfacebook);
        $socialtwitter = theme_degrade_get_setting('social_twitter');
        $socialtwitter = trim($socialtwitter);
        $socialinstagram = theme_degrade_get_setting('social_instagram');
        $socialinstagram = trim($socialinstagram);

        $socialurls = ($socialfacebook != '' || $socialinstagram != '' || $socialtwitter != '') ? 1 : 0;

        return [
            'enable_block_social' => 0 + ($footersocialtitle != '' || $socialurls != 0),
            'footer_social_title' => $footersocialtitle,
            'social_facebook' => $socialfacebook,
            'social_instagram' => $socialinstagram,
            'social_twitter' => $socialtwitter,
            'instagram_name' => get_string("social_instagram", 'theme_degrade'),
            'twitter_name' => get_string("social_twitter", 'theme_degrade'),
            'facebook_name' => get_string("social_facebook", 'theme_degrade'),
        ];
    }

    /**
     * @return array
     * @throws \coding_exception
     */
    private static function contact() {
        $contactfootertitle = theme_degrade_get_setting('contact_footer_title');
        $contactfootertitle = !empty($contactfootertitle) ? $contactfootertitle : '';

        $contactaddress = theme_degrade_get_setting('contact_address') ? theme_degrade_get_setting('contact_address') : '';
        $contactemail = theme_degrade_get_setting('contact_email');
        $contactphone = theme_degrade_get_setting('contact_phone');

        return [
            'enable_block_contact' => 0 + ($contactaddress != '' || $contactemail != '' || $contactphone != ''),
            'contact_footer_title' => $contactfootertitle,
            'contact_address' => $contactaddress,
            'contact_phone' => $contactphone,
            'contact_email' => $contactemail,
        ];
    }
}

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

namespace theme_degrade\output;

use context_system;
use core\context\course as context_course;
use core\di;
use core\hook\manager as hook_manager;
use core_auth\output\login;
use core_course\external\course_summary_exporter;
use core_message\api;
use core_message\helper;
use Exception;
use html_writer;
use moodle_url;
use user_picture;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @copyright based on work by 2012 Bas Brands, www.basbrands.nl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \core_renderer {

    /**
     * Returns HTML to display a "Turn editing on/off" button in a form.
     *
     * @param moodle_url $url The URL + params to send through when clicking the button
     * @param string $method
     * @return string HTML the button
     * @throws Exception
     */
    public function edit_button(moodle_url $url, string $method = "post") {
        if ($this->page->theme->haseditswitch) {
            return;
        }
        $url->param("sesskey", sesskey());
        if ($this->page->user_is_editing()) {
            $url->param("edit", "off");
            $editstring = get_string("turneditingoff");
        } else {
            $url->param("edit", "on");
            $editstring = get_string("turneditingon");
        }
        $button = new \single_button($url, $editstring, $method, \single_button::BUTTON_PRIMARY);
        return $this->render_single_button($button);
    }

    /**
     * Renders the "breadcrumb" for all pages in boost.
     *
     * @return string the HTML for the navbar.
     * @throws Exception
     */
    public function navbar(): string {
        $newnav = new navbar($this->page);
        return $this->render_from_template("core/navbar", $newnav);
    }

    /**
     * Renders the context header for the page.
     *
     * @param array $headerinfo Heading information.
     * @param int $headinglevel What 'h' level to make the heading.
     *
     * @return string A rendered context header.
     *
     * @throws Exception
     */
    public function context_header($headerinfo = null, $headinglevel = 1): string {
        global $DB, $USER, $CFG;
        require_once("{$CFG->dirroot}/user/lib.php");
        $context = $this->page->context;
        $imagedata = null;
        $userbuttons = null;

        // Make sure to use the heading if it has been set.
        if (isset($headerinfo["heading"])) {
            $heading = $headerinfo["heading"];
        } else {
            $heading = $this->page->heading;
        }

        // The user context currently has images and buttons. Other contexts may follow.
        if ((isset($headerinfo["user"]) || $context->contextlevel == CONTEXT_USER) && $this->page->pagetype !== "my-index") {
            if (isset($headerinfo["user"])) {
                $user = $headerinfo["user"];
            } else {
                // Look up the user information if it is not supplied.
                $user = $DB->get_record("user", ["id" => $context->instanceid]);
            }

            // If the user context is set, then use that for capability checks.
            if (isset($headerinfo["usercontext"])) {
                $context = $headerinfo["usercontext"];
            }

            // Only provide user information if the user is the current user, or a user which the current user can view.
            // When checking user_can_view_profile(), either:
            // If the page context is course, check the course context (from the page object) or;
            // If page context is NOT course, then check across all courses.
            $course = ($this->page->context->contextlevel == CONTEXT_COURSE) ? $this->page->course : null;

            if (user_can_view_profile($user, $course)) {
                // Use the user's full name if the heading isn't set.
                if (empty($heading)) {
                    $heading = fullname($user);
                }

                $imagedata = $this->user_picture($user, ["size" => 100]);

                // Check to see if we should be displaying a message button.
                if (!empty($CFG->messaging) && has_capability("moodle/site:sendmessage", $context)) {
                    $userbuttons = [
                        "messages" => [
                            "buttontype" => "message",
                            "title" => get_string("message", "message"),
                            "url" => new moodle_url("/message/index.php", ["id" => $user->id]),
                            "image" => "t/message",
                            "linkattributes" => helper::messageuser_link_params($user->id),
                            "page" => $this->page,
                        ],
                    ];

                    if ($USER->id != $user->id) {
                        $iscontact = api::is_contact($USER->id, $user->id);
                        $isrequested = api::get_contact_requests_between_users($USER->id, $user->id);
                        $contacturlaction = "";
                        $linkattributes = helper::togglecontact_link_params(
                            $user,
                            $iscontact,
                            true,
                            !empty($isrequested),
                        );
                        // If the user is not a contact.
                        if (!$iscontact) {
                            if ($isrequested) {
                                // We just need the first request.
                                $requests = array_shift($isrequested);
                                if ($requests->userid == $USER->id) {
                                    // If the user has requested to be a contact.
                                    $contacttitle = "contactrequestsent";
                                } else {
                                    // If the user has been requested to be a contact.
                                    $contacttitle = "waitingforcontactaccept";
                                }
                                $linkattributes = array_merge($linkattributes, [
                                    "class" => "disabled",
                                    "tabindex" => "-1",
                                ]);
                            } else {
                                // If the user is not a contact and has not requested to be a contact.
                                $contacttitle = "addtoyourcontacts";
                                $contacturlaction = "addcontact";
                            }
                            $contactimage = "t/addcontact";
                        } else {
                            // If the user is a contact.
                            $contacttitle = "removefromyourcontacts";
                            $contacturlaction = "removecontact";
                            $contactimage = "t/removecontact";
                        }
                        $userbuttons["togglecontact"] = [
                            "buttontype" => "togglecontact",
                            "title" => get_string($contacttitle, "message"),
                            "url" => new moodle_url("/message/index.php", [
                                "user1" => $USER->id,
                                "user2" => $user->id,
                                $contacturlaction => $user->id,
                                "sesskey" => sesskey(),
                            ]),
                            "image" => $contactimage,
                            "linkattributes" => $linkattributes,
                            "page" => $this->page,
                        ];
                    }

                    $this->page->requires->string_for_js("changesmadereallygoaway", "moodle");
                }
            } else {
                $heading = null;
            }
        }

        $prefix = null;
        if ($context->contextlevel == CONTEXT_MODULE) {
            if ($this->page->course->format === "singleactivity") {
                $heading = format_string($this->page->course->fullname, true, ["context" => $context]);
            } else {
                $heading = $this->page->cm->get_formatted_name();
                $iconurl = $this->page->cm->get_icon_url();
                $iconclass = $iconurl->get_param("filtericon") ? "" : "nofilter";
                $iconattrs = [
                    "class" => "icon activityicon $iconclass",
                    "aria-hidden" => "true",
                ];
                $imagedata = html_writer::img($iconurl->out(false), "", $iconattrs);
                $purposeclass = plugin_supports("mod", $this->page->activityname, FEATURE_MOD_PURPOSE);
                $purposeclass .= " activityiconcontainer icon-size-6";
                $purposeclass .= " modicon_" . $this->page->activityname;
                $isbranded = component_callback("mod_" . $this->page->activityname, "is_branded", [], false);
                $imagedata = html_writer::tag("div", $imagedata, ["class" => $purposeclass . ($isbranded ? " isbranded" : "")]);
                if (!empty($USER->editing)) {
                    $prefix = get_string("modulename", $this->page->activityname);
                }
            }
        }

        $contextheader = new \context_header($heading, $headinglevel, $imagedata, $userbuttons, $prefix);
        return $this->render($contextheader);
    }

    /**
     * See if this is the first view of the current cm in the session if it has fake blocks.
     *
     * (We track up to 100 cms so as not to overflow the session.)
     * This is done for drawer regions containing fake blocks so we can show blocks automatically.
     *
     * @return boolean true if the page has fakeblocks and this is the first visit.
     */
    public function firstview_fakeblocks(): bool {
        global $SESSION;

        $firstview = false;
        if ($this->page->cm) {
            if (!$this->page->blocks->region_has_fakeblocks("side-pre")) {
                return false;
            }
            if (!property_exists($SESSION, "firstview_fakeblocks")) {
                $SESSION->firstview_fakeblocks = [];
            }
            if (array_key_exists($this->page->cm->id, $SESSION->firstview_fakeblocks)) {
                $firstview = false;
            } else {
                $SESSION->firstview_fakeblocks[$this->page->cm->id] = true;
                $firstview = true;
                if (count($SESSION->firstview_fakeblocks) > 100) {
                    array_shift($SESSION->firstview_fakeblocks);
                }
            }
        }
        return $firstview;
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     * @throws Exception
     */
    public function full_header() {
        global $DB, $CFG;

        $pagetype = $this->page->pagetype;
        $homepage = get_home_page();

        $homepagetype = null;
        // Add a special case since /my/courses is a part of the /my subsystem.
        if ($homepage == HOMEPAGE_MY || $homepage == HOMEPAGE_MYCOURSES) {
            $homepagetype = "my-index";
        } else {
            if ($homepage == HOMEPAGE_SITE) {
                $homepagetype = "site-index";
            }
        }
        if ($this->page->include_region_main_settings_in_header_actions() && !$this->page->blocks->is_block_present("settings")) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(
                html_writer::div(
                    $this->region_main_settings_menu(),
                    "d-print-none",
                    ["id" => "region-main-settings-menu"]
                )
            );
        }

        $header = new \stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options["nonavbar"]);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();
        if (!empty($pagetype) && !empty($homepagetype) && $pagetype == $homepagetype) {
            $header->welcomemessage = \core_user::welcome_message();
        }

        $courseid = $this->page->course->id;

        $header->hasnavbarcourse = false;
        $header->hasbannercourse = false;
        $requesturi = $_SERVER["REQUEST_URI"];
        $hasuri = strpos($requesturi, "course/view.php") || strpos($requesturi, "course/section.php");
        if ($hasuri) {
            $showcoursesummary = get_config("theme_degrade", "course_summary_banner");
            $showcoursesummarycourse = get_config("theme_degrade", "course_summary_banner_{$courseid}");
            if ($showcoursesummarycourse !== false) {
                $showcoursesummary = $showcoursesummarycourse;
            }

            if ($showcoursesummary) {
                if ($showcoursesummary == 1) {
                    $header->hasnavbarcourse = true;
                    $header->categoryname = $DB->get_field("course_categories", "name", ["id" => $this->page->course->category]);
                    $header->overviewfiles = $this->get_course_image();

                    if (has_capability("moodle/category:manage", $this->page->context)) {
                        $cache = \cache::make("theme_degrade", "course_cache");
                        $cachekey = "header_details_{$this->page->course->id}";
                        if ($cache->has($cachekey)) {
                            $header->details = json_decode($cache->get($cachekey));
                        } else {
                            $header->details = $this->get_details();
                            $cache->set($cachekey, json_encode($header->details));
                        }
                    }
                }
                if ($showcoursesummary == 2) {
                    $bannerfileurl = $this->get_course_image();
                    if ($bannerfileurl) {
                        $header->categoryname =
                            $DB->get_field("course_categories", "name", ["id" => $this->page->course->category]);
                        $header->hasbannercourse = true;
                        $header->banner_course_file_url = $bannerfileurl;
                    }
                }
            }

            $header->hasnosumary = !$header->hasbannercourse && !$header->hasnavbarcourse;

            if ($this->page->user_is_editing() && has_capability("moodle/site:config", $this->page->context)) {
                $url = "{$CFG->wwwroot}/theme/degrade/quickstart/course-banner.php?courseid={$courseid}";
                $header->headeractions_banner_course_edithref = $url;
                $header->headeractions_banner_courseid = $courseid;
                $header->headeractions_banner_course_edit = true;
            }
        }

        return $this->render_from_template("theme_degrade/core/full_header", $header);
    }

    /**
     * get_details
     * @return array
     * @throws Exception
     */
    private function get_details() {
        global $DB;

        $decsep = get_string("decsep", "langconfig");
        $thousandssep = get_string("thousandssep", "langconfig");
        $details = [];

        // Students.
        $sql = "
            SELECT COUNT(DISTINCT userid)
              FROM {role_assignments}
             WHERE roleid    = 5
               AND contextid = :contextid";
        $total = $DB->get_field_sql($sql, ["contextid" => $this->page->context->id]);
        $details[] = [
            "id" => "users",
            "icon" => "fa-users fa-fw",
            "link" => false,
            "number" => number_format($total, 0, $decsep, $thousandssep),
            "text" => get_string("details-users", "theme_degrade"),
        ];

        // Teachers.
        $sql = "
            SELECT u.id, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic,
                   u.middlename, u.alternatename, u.imagealt, u.email
              FROM {role_assignments} ra
              JOIN {user}              u ON u.id = ra.userid
             WHERE ra.roleid    IN(3,4)
               AND ra.contextid = :contextid";
        $teachers = $DB->get_records_sql($sql, ["contextid" => $this->page->context->id]);
        if (count($teachers)) {
            $teachershtml = "";
            foreach ($teachers as $teacher) {
                // URL da imagem de perfil.
                $userpicture = new user_picture($teacher);
                $userpicture->size = 1; // 1 = small, 0 = large.
                $imgurl = $userpicture->get_url($this->page)->out(false);

                $name = fullname($teacher);
                $teachershtml .= "<div><img class='teacher-icon' src='{$imgurl}' alt='{$name}'></div>";
            }
            $details[] = [
                "id" => "teachers",
                "icon" => "fa fa-graduation-cap fa-fw",
                "link" => false,
                "number" => "<div class='d-flex'>{$teachershtml}</div>",
                "text" => get_string("details-teachers", "theme_degrade"),
            ];
        }

        // Completo e em progresso.
        $sql = "
            SELECT DISTINCT ra.userid, cc.timecompleted
              FROM {role_assignments}   ra
         LEFT JOIN {course_completions} cc ON cc.userid = ra.userid
                                          AND cc.course = :courseid
             WHERE ra.contextid = :contextid";
        $users = $DB->get_records_sql($sql, [
            "contextid" => $this->page->context->id,
            "courseid" => $this->page->course->id,
        ]);

        // Separa por status.
        $completaram = 0;
        $emprogresso = 0;

        foreach ($users as $user) {
            if (!empty($user->timecompleted)) {
                $completaram++;
            } else {
                $emprogresso++;
            }
        }

        $details[] = [
            "id" => "emprogresso",
            "icon" => "fa fa-spinner fa-fw",
            "link" => false,
            "number" => number_format($emprogresso, 0, $decsep, $thousandssep),
            "text" => get_string("details-emprogresso", "theme_degrade"),
        ];
        $details[] = [
            "id" => "completaram",
            "icon" => "fa fa-user-slash fa-fw",
            "link" => false,
            "number" => number_format($completaram, 0, $decsep, $thousandssep),
            "text" => get_string("details-completaram", "theme_degrade"),
        ];

        // Usuários que nunca acessaram.
        $sql = "
            SELECT COUNT(DISTINCT ra.userid) AS total
              FROM {role_assignments} ra
         LEFT JOIN {user_lastaccess}  la ON la.userid = ra.userid
             WHERE ra.contextid = :contextid
               AND la.timeaccess IS NULL";
        $total = $DB->get_field_sql($sql, ["contextid" => $this->page->context->id]);
        $details[] = [
            "id" => "not-access",
            "icon" => "fa fa-user-slash fa-fw",
            "link" => false,
            "number" => number_format($total, 0, $decsep, $thousandssep),
            "text" => get_string("details-not-access", "theme_degrade"),
        ];

        return $details;
    }

    /**
     * get_course_image
     *
     * @return false|string
     * @throws Exception
     */
    public function get_course_image() {
        $course = ($this->page->context->contextlevel == CONTEXT_COURSE) ? $this->page->course : null;
        if (!$course) {
            return false;
        }
        $bannerfileurl = theme_degrade_setting_file_url("banner_course_file_{$course->id}");
        if ($bannerfileurl) {
            return $bannerfileurl->out();
        }
        $bannerfileurl = theme_degrade_setting_file_url("banner_course_file");

        if ($bannerfileurl) {
            return $bannerfileurl->out();
        }

        if ($course) {
            return course_summary_exporter::get_course_image($course);
        }

        return false;
    }

    /**
     * Whether we should display the logo in the navbar.
     *
     * We will when there are no main logos, and we have compact logo.
     *
     * @return bool
     * @throws Exception
     */
    public function should_display_navbar_logo() {
        $logo = $this->get_compact_logo_url();
        return !empty($logo);
    }

    /**
     * Return the site's logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     * @throws Exception
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        return $this->get_compact_logo_url($maxwidth, $maxheight);
    }

    /**
     * Return the site's compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     *
     * @return moodle_url|false
     *
     * @throws Exception
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        static $return = null;
        if ($return !== null) {
            return $return;
        }

        $callbacks = get_plugins_with_function("theme_degrade_get_logo");
        foreach ($callbacks as $plugintype => $plugins) {
            foreach ($plugins as $plugin => $callback) {
                $urllogo = $callback();

                if ($urllogo) {
                    return $return = $urllogo->out(false);
                }
            }
        }

        $logo = get_config("core_admin", "logocompact");
        if (empty($logo)) {
            return $return = false;
        }

        // Hide the requested size in the file path.
        $filepath = ((int)$maxwidth . "x" . (int)$maxheight) . "/";

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return $return = moodle_url::make_pluginfile_url(
            context_system::instance()->id,
            "core_admin",
            "logocompact",
            $filepath,
            theme_get_revision(),
            $logo
        );
    }

    /**
     * Get the course pattern image URL.
     *
     * @param int $courseid course id
     * @return string URL of the course pattern image in SVG format
     * @throws Exception
     */
    public function get_default_image_for_courseid($courseid): string {
        global $CFG;
        return "{$CFG->wwwroot}/theme/degrade/course-image-default.php?id={$courseid}";
    }

    /**
     * Brandcolor background menu class
     * @return string
     * @throws Exception
     */
    public function brandcolor_background_menu_class() {
        $class = [];
        if (get_config("theme_degrade", "brandcolor_background_menu")) {
            $class[] = "brandcolor-background";
        }
        if (get_config("theme_degrade", "top_scroll_fix")) {
            $class[] = "top-scroll-fix";
        }

        return implode(" ", $class);
    }

    /**
     * Renders the login form.
     *
     * @param login $form The renderable.
     * @return string
     * @throws Exception
     */
    public function render_login(login $form) {
        global $SITE;

        $context = context_course::instance(SITEID);
        $showloginform = get_config("core", "showloginform");

        $mustachecontext = $form->export_for_template($this);
        $mustachecontext->showloginform = $showloginform === false || $showloginform;
        $mustachecontext->errorformatted = $this->error_text($mustachecontext->error);
        $mustachecontext->sitename = format_string($SITE->fullname, true, ["context" => $context, "escape" => false]);

        $url = $this->get_login_logo_url();
        if (!$url) {
            $url = $this->get_logo_url();
        }
        if ($url) {
            $mustachecontext->logourl = $url->out(false);
        }

        return $this->render_from_template("theme_degrade/core/theme_login_form", $mustachecontext);
    }

    /**
     * Return the site's compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     *
     * @return moodle_url|false
     *
     * @throws Exception
     */
    private function get_login_logo_url($maxwidth = 250, $maxheight = 130) {
        static $return = null;
        if ($return !== null) {
            return $return;
        }

        $logologin = get_config("theme_degrade", "logologin");
        if (empty($logologin)) {
            return false;
        }

        // Hide the requested size in the file path.
        $filepath = ((int)$maxwidth . "x" . (int)$maxheight) . "/";

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return $return = moodle_url::make_pluginfile_url(
            context_system::instance()->id,
            "theme_degrade",
            "logologin",
            $filepath,
            theme_get_revision(),
            $logologin
        );
    }

    /**
     * The standard tags (meta tags, links to stylesheets and JavaScript, etc.)
     * that should be included in the <head> tag. Designed to be called in theme
     * layout.php files.
     *
     * @return string HTML fragment.
     * @throws Exception
     */
    public function standard_head_html() {
        global $CFG, $SITE;

        // Before we output any content, we need to ensure that certain
        // page components are set up.

        // Blocks must be set up early as they may require javascript which
        // has to be included in the page header before output is created.
        foreach ($this->page->blocks->get_regions() as $region) {
            $this->page->blocks->ensure_content_created($region, $this);
        }

        // Give plugins an opportunity to add any head elements. The callback
        // must always return a string containing valid html head content.

        $hook = new \core\hook\output\before_standard_head_html_generation($this);
        $hook->process_legacy_callbacks();
        di::get(hook_manager::class)->dispatch($hook);

        // Allow a url_rewrite plugin to setup any dynamic head content.
        if (isset($CFG->urlrewriteclass) && !isset($CFG->upgraderunning)) {
            $class = $CFG->urlrewriteclass;
            $hook->add_html($class::html_head_setup());
        }

        $hook->add_html('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n");
        $hook->add_html('<meta name="keywords" content="moodle, ' . $this->page->title . '" />' . "\n");
        // This is only set by the {@see redirect()} method.
        $hook->add_html($this->metarefreshtag);

        // Check if a periodic refresh delay has been set and make sure we arn't
        // already meta refreshing.
        if ($this->metarefreshtag == '' && $this->page->periodicrefreshdelay !== null) {
            $hook->add_html(
                \core\output\html_writer::empty_tag('meta', [
                    'http-equiv' => 'refresh',
                    'content' => $this->page->periodicrefreshdelay . ';url=' . $this->page->url->out(),
                ]),
            );
        }

        $output = $hook->get_output();

        // Set up help link popups for all links with the helptooltip class.
        $this->page->requires->js_init_call('M.util.help_popups.setup');

        $focus = $this->page->focuscontrol;
        if (!empty($focus)) {
            if (preg_match("#forms\['([a-zA-Z0-9]+)'\].elements\['([a-zA-Z0-9]+)'\]#", $focus, $matches)) {
                // This is a horrifically bad way to handle focus but it is passed in
                // through messy formslib::moodleform.
                $this->page->requires->js_function_call('old_onload_focus', [$matches[1], $matches[2]]);
            } else if (strpos($focus, '.') !== false) {
                // Old style of focus, bad way to do it.
                $text = 'This code is using the old style focus event, ';
                $text .= 'Please update this code to focus on an element id or the moodleform focus method.';
                debugging($text, DEBUG_DEVELOPER);
                $this->page->requires->js_function_call('old_onload_focus', explode('.', $focus, 2));
            } else {
                // Focus element with given id.
                $this->page->requires->js_function_call('focuscontrol', [$focus]);
            }
        }

        // Get the theme stylesheet - this has to be always first CSS, this loads also styles.css from all plugins;
        // any other custom CSS can not be overridden via themes and is highly discouraged.
        $urls = $this->page->theme->css_urls($this->page);
        /** @var moodle_url $url */
        foreach ($urls as $url) {
            $coursecolor = get_config("theme_degrade", "override_course_color_{$this->page->course->id}");
            if (isset($coursecolor[3])) {
                $newurl = $url->out(false);
                $newurl = preg_replace_callback(
                    '/degrade\/(\d+)(?:_\d+)?\//',
                    function ($matches) {
                        $novoid = ((int) $matches[1]);

                        return "degrade/{$novoid}_{$this->page->course->id}/";
                    },
                    $newurl
                );
                $newurl = str_replace("theme/styles.php", "theme/degrade/theme-styles.php", $newurl);
                $courseid = $this->page->course->id;
            } else {
                $newurl = $url->out(false);
                $courseid = 0;
            }

            $url = new moodle_url($newurl);
            if ($courseid) {
                $url->param("courseid", $courseid);
            }

            $this->page->requires->css_theme($url);
        }

        // Get the theme javascript head and footer.
        if ($jsurl = $this->page->theme->javascript_url(true)) {
            $this->page->requires->js($jsurl, true);
        }
        if ($jsurl = $this->page->theme->javascript_url(false)) {
            $this->page->requires->js($jsurl);
        }

        // Get any HTML from the page_requirements_manager.
        $output .= $this->page->requires->get_head_code($this->page, $this);

        // List alternate versions.
        foreach ($this->page->alternateversions as $type => $alt) {
            $output .= html_writer::empty_tag('link', [
                'rel' => 'alternate',
                'type' => $type,
                'title' => $alt->title,
                'href' => $alt->url,
            ]);
        }

        // Add noindex tag if relevant page and setting applied.
        $allowindexing = isset($CFG->allowindexing) ? $CFG->allowindexing : 0;
        $loginpages = ['login-index', 'login-signup'];
        if ($allowindexing == 2 || ($allowindexing == 0 && in_array($this->page->pagetype, $loginpages))) {
            if (!isset($CFG->additionalhtmlhead)) {
                $CFG->additionalhtmlhead = '';
            }
            if (stripos($CFG->additionalhtmlhead, '<meta name="robots" content="noindex" />') === false) {
                $CFG->additionalhtmlhead .= '<meta name="robots" content="noindex" />';
            }
        }

        if (!empty($CFG->additionalhtmlhead)) {
            $output .= "\n" . $CFG->additionalhtmlhead;
        }

        if ($this->page->pagelayout == 'frontpage') {
            $summary = s(strip_tags(format_text($SITE->summary, FORMAT_HTML)));
            if (!empty($summary)) {
                $output .= "<meta name=\"description\" content=\"$summary\" />\n";
            }
        }

        return $output;
    }
}

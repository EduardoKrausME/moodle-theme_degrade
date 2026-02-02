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

namespace theme_degrade\output\core;

use core_admin_renderer;
use Exception;

/**
 * Standard HTML output renderer for core_admin subsystem.
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_renderer extends core_admin_renderer {
    /**
     * Display the admin notifications page.
     *
     * @param int $maturity
     * @param bool $insecuredataroot warn dataroot is invalid
     * @param bool $errorsdisplayed warn invalid dispaly error setting
     * @param bool $cronoverdue warn cron not running
     * @param bool $dbproblems warn db has problems
     * @param bool $maintenancemode warn in maintenance mode
     * @param bool $buggyiconvnomb warn iconv problems
     * @param array|null $availableupdates array of \core\update\info objects or null
     * @param int|null $availableupdatesfetch timestamp of the most recent updates fetch or null (unknown)
     * @param string[] $cachewarnings An array containing warnings from the Cache API.
     * @param array $eventshandlers Events 1 API handlers.
     * @param bool $themedesignermode Warn about the theme designer mode.
     * @param bool $devlibdir Warn about development libs directory presence.
     * @param bool $mobileconfigured Whether the mobile web services have been enabled
     * @param bool $overridetossl Whether or not ssl is being forced.
     * @param bool $invalidforgottenpasswordurl Whether the forgotten password URL does not link to a valid URL.
     * @param bool $croninfrequent If true, warn that cron hasn't run in the past few minutes
     * @param bool $showcampaigncontent Whether the campaign content should be visible or not.
     * @param bool $showfeedbackencouragement Whether the feedback encouragement content should be displayed or not.
     * @param bool $showservicesandsupport Whether the services and support content should be displayed or not.
     * @param string $xmlrpcwarning XML-RPC deprecation warning message.
     *
     * @return string HTML to output.
     * @throws Exception
     */
    public function admin_notifications_page(
        $maturity, $insecuredataroot, $errorsdisplayed, $cronoverdue, $dbproblems, $maintenancemode, $availableupdates,
        $availableupdatesfetch, $buggyiconvnomb, $registered, array $cachewarnings = [], $eventshandlers = 0,
        $themedesignermode = false, $devlibdir = false, $mobileconfigured = false, $overridetossl = false,
        $invalidforgottenpasswordurl = false, $croninfrequent = false, $showcampaigncontent = false,
        bool $showfeedbackencouragement = false, bool $showservicesandsupport = false, $xmlrpcwarning = ''
    ) {
        global $CFG;
        $output = '';

        $output .= $this->header();
        $output .= $this->output->heading(get_string('notifications', 'admin'));
        if (method_exists($this, "upgrade_news_message")) {
            $output .= $this->upgrade_news_message();
        }
        $output .= $this->maturity_info($maturity);
        if (empty($CFG->disableupdatenotifications)) {
            $output .= $this->available_updates($availableupdates, $availableupdatesfetch);
        }
        $output .= $this->insecure_dataroot_warning($insecuredataroot);
        $output .= $this->development_libs_directories_warning($devlibdir);
        $output .= $this->themedesignermode_warning($themedesignermode);
        $output .= $this->display_errors_warning($errorsdisplayed);
        $output .= $this->buggy_iconv_warning($buggyiconvnomb);
        $output .= $this->cron_overdue_warning($cronoverdue);
        $output .= $this->cron_infrequent_warning($croninfrequent);
        $output .= $this->db_problems($dbproblems);
        $output .= $this->maintenance_mode_warning($maintenancemode);
        $output .= $this->overridetossl_warning($overridetossl);
        $output .= $this->cache_warnings($cachewarnings);
        $output .= $this->events_handlers($eventshandlers);
        $output .= $this->registration_warning($registered);
        $output .= $this->mobile_configuration_warning($mobileconfigured);
        $output .= $this->forgotten_password_url_warning($invalidforgottenpasswordurl);
        $output .= $this->campaign_content($showcampaigncontent);

        // It is illegal and a violation of the gpl to hide, remove or modify this copyright notice.
        $output .= $this->moodle_copyright();

        $output .= $this->footer();

        return $output;
    }
}

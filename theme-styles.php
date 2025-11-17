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
 * phpcs:disable Generic.CodeAnalysis.EmptyStatement.DetectedIf
 *
 * This file is responsible for serving the one huge CSS of each theme.
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @copyright 2009 Petr Skoda (skodak) {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);
define('ABORT_AFTER_CONFIG', true);
// Ok, now we need to start normal moodle script, we need to load all libs and $DB.
define('ABORT_AFTER_CONFIG_CANCEL', true);
define('NO_MOODLE_COOKIES', true); // Session not used here.
define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check.

require("../../config.php");
require_once("{$CFG->dirroot}/lib/csslib.php");
require_once("{$CFG->dirroot}/lib/configonlylib.php");
require_once("{$CFG->dirroot}/lib/setup.php");

if ($slashargument = min_get_slash_argument()) {
    $slashargument = ltrim($slashargument, '/');
    if (substr_count($slashargument, '/') < 2) {
        css_send_css_not_found();
    }

    if (strpos($slashargument, '_s/') === 0) {
        // Can't use SVG.
        $slashargument = substr($slashargument, 3);
        $usesvg = false;
    } else {
        $usesvg = true;
    }

    [$themename, $rev, $type] = explode('/', $slashargument, 3);
    $rev = min_clean_param($rev, 'RAW');
    $type = min_clean_param($type, 'SAFEDIR');
} else {
    $rev = min_optional_param('rev', 0, 'RAW');
    $type = min_optional_param('type', 'all', 'SAFEDIR');
    $usesvg = (bool) min_optional_param('svg', '1', 'INT');
}

// Check if we received a theme sub revision which allows us
// to handle local caching on a per theme basis.
$values = explode('_', $rev);
$rev = min_clean_param(array_shift($values), 'INT');
$themesubrev = array_shift($values);

if (!is_null($themesubrev)) {
    $themesubrev = min_clean_param($themesubrev, 'INT');
}

// Check that type fits into the expected values.
if (!in_array($type, ['all', 'all-rtl'])) {
    css_send_css_not_found();
}

if (file_exists("{$CFG->dirroot}/theme/degrade/config.php")) {
    // The theme exists in standard location - ok.
} else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/degrade/config.php")) {
    // Alternative theme location contains this theme - ok.
} else {
    header('HTTP/1.0 404 not found');
    die('Theme was not found, sorry.');
}

$candidatesheet = theme_styles_get_filename($rev, $type, $themesubrev, $usesvg);
$etag = theme_styles_get_etag($rev, $type, $themesubrev, $usesvg);

if (file_exists($candidatesheet)) {
    if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        // We do not actually need to verify the etag value because our files
        // never change in cache because we increment the rev counter.
        css_send_unmodified(filemtime($candidatesheet), $etag);
    }
    css_send_cached_css($candidatesheet, $etag);
}

$theme = theme_config::load("degrade");
$theme->force_svg_use($usesvg);
$theme->set_rtl_mode(substr($type, -4) === '-rtl');

make_localcache_directory('theme', false);

// Attempt to fetch the lock.
$lockfactory = \core\lock\lock_config::get_lock_factory('core_theme_get_css_content');
$lock = $lockfactory->get_lock("degrade", rand(90, 120));

if ($lock) {
    // Either the lock was successful, or the lock was unsuccessful but the content *must* be sent.

    // The content does not exist locally.
    // Generate and save it.
    $csscontent = $theme->get_css_content();
    file_put_contents($candidatesheet, $csscontent);

    if ($lock) {
        $lock->release();
    }

    css_send_cached_css($candidatesheet, $etag);
}

/**
 * Get the filename for the specified configuration.
 *
 * @param string $type The requested sheet type
 * @param int $themesubrev The theme sub-revision
 * @param bool $usesvg Whether SVGs are allowed
 * @return  string  The filename for this sheet
 */
function theme_styles_get_filename($rev, $type, $themesubrev = 0, $usesvg = true) {
    global $CFG;

    $filename = $type;
    $filename .= ($themesubrev > 0) ? "_{$themesubrev}" : '';
    $filename .= $usesvg ? '' : '-nosvg';

    $cssbasedir = "{$CFG->localcachedir}/theme/{$rev}/degrade/css";
    if (!file_exists($cssbasedir)) {
        mkdir($cssbasedir, 0777, true);
    }

    return "{$cssbasedir}/{$filename}.css";
}

/**
 * Determine the correct etag for the specified configuration.
 *
 * @param int $rev The revision number
 * @param string $type The requested sheet type
 * @param int $themesubrev The theme sub-revision
 * @param bool $usesvg Whether SVGs are allowed
 * @return  string  The etag to use for this request
 */
function theme_styles_get_etag($rev, $type, $themesubrev, $usesvg) {
    $etag = [$rev, $type, $themesubrev];

    if (!$usesvg) {
        $etag[] = 'nosvg';
    }

    return sha1(implode('/', $etag));
}

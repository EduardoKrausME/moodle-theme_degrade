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
 * Settings file
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.RequireLogin.Missing

require_once('../../config.php');

header("Content-Type: image/png");
header("Cache-Control: public, max-age=86400");
header("Pragma: cache");

$courseid = required_param('id', PARAM_INT);

$cache = \cache::make("theme_degrade", "course_cache");
$cachekey = "get_generated_image_for_{$courseid}";
if ($cache->has($cachekey)) {
    die($cache->get($cachekey));
} else {
    $imagepath = get_generated_image_for_id($courseid);
    $imagedata = file_get_contents($imagepath);
    $cache->set($cachekey, $imagedata);
    die($imagedata);
}

/**
 * Deterministically generates a cropped image for a course id.
 *
 * @param int $courseid
 * @param int $height
 * @param int $width
 * @return string
 * @throws coding_exception
 */
function get_generated_image_for_id(int $courseid, int $height = 300, int $width = 600): string {
    global $CFG;

    if ($courseid <= 0) {
        throw new coding_exception("Invalid courseid.");
    }
    if ($height <= 0 || $width <= 0) {
        throw new coding_exception("Invalid target dimensions.");
    }

    $imagelist = glob("{$CFG->dirroot}/theme/degrade/pix/generated/*.png");
    if (empty($imagelist)) {
        throw new coding_exception("No generated images found in theme/degrade/pix/generated/.");
    }

    // Ensure stable ordering across environments.
    sort($imagelist, SORT_STRING);

    // Cache output (Moodle temp dir).
    require_once($CFG->libdir . "/filelib.php");
    $tempdir = make_temp_directory("generatedcourseimages");
    $hashhex = hash("sha256", "courseid:" . $courseid);
    $tempfile = "{$tempdir}/course_{$courseid}_{$width}x{$height}_" . substr($hashhex, 0, 12) . ".png";

    // Pick the image index deterministically (1st number).
    $imagecount = count($imagelist);
    $nums = courseid_to_three_uint32($courseid);

    $imageindex = $nums[0] % $imagecount;
    $sourcefile = $imagelist[$imageindex];

    $src = @imagecreatefrompng($sourcefile);
    if (!$src) {
        throw new coding_exception("Failed to open image: {$sourcefile}");
    }

    $srcw = imagesx($src);
    $srch = imagesy($src);

    // If requested crop is larger than source, we "fit" the source into the target.
    // This keeps the function always producing an image.
    if ($width > $srcw || $height > $srch) {
        $dst = imagecreatetruecolor($width, $height);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        // Scale source to cover target (center-crop style).
        $scale = max($width / $srcw, $height / $srch);
        $neww = (int) ceil($srcw * $scale);
        $newh = (int) ceil($srch * $scale);

        $tmp = imagecreatetruecolor($neww, $newh);
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);

        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $neww, $newh, $srcw, $srch);

        // Deterministic X/Y inside the scaled image.
        $maxx = max(0, $neww - $width);
        $maxy = max(0, $newh - $height);
        $x = ($maxx > 0) ? ($nums[1] % ($maxx + 1)) : 0;
        $y = ($maxy > 0) ? ($nums[2] % ($maxy + 1)) : 0;

        imagecopy($dst, $tmp, 0, 0, $x, $y, $width, $height);

        imagedestroy($tmp);
        imagedestroy($src);

        imagepng($dst, $tempfile, 6);
        imagedestroy($dst);

        return $tempfile;
    }

    // Normal case: crop inside source using deterministic X/Y (2nd and 3rd numbers).
    $maxx = max(0, $srcw - $width);
    $maxy = max(0, $srch - $height);

    $x = ($maxx > 0) ? ($nums[1] % ($maxx + 1)) : 0;
    $y = ($maxy > 0) ? ($nums[2] % ($maxy + 1)) : 0;

    $dst = imagecreatetruecolor($width, $height);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);

    imagecopy($dst, $src, 0, 0, $x, $y, $width, $height);

    imagedestroy($src);

    imagepng($dst, $tempfile, 6);
    imagedestroy($dst);

    return $tempfile;
}

/**
 * Turns a course id into 3 deterministic uint32 numbers.
 *
 * @param int $courseid
 * @return array
 */
function courseid_to_three_uint32(int $courseid): array {
    $raw = hash("sha256", "courseid:" . $courseid, true); // 32 bytes
    $part = substr($raw, 0, 12); // first 12 bytes => 3 uint32
    $u = unpack("N3", $part); // big-endian unsigned 32-bit

    // Return as zero-based array.
    return [(int) $u[1], (int) $u[2], (int) $u[3]];
}

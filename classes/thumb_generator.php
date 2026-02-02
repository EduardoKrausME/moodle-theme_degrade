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
 * Thumbnail generator for theme stored files.
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade;

use context;
use GdImage;
use stored_file;
use Throwable;

/**
 * Thumbnail generator for theme stored files.
 *
 * - Uses GD to resize/crop.
 * - Caches the generated thumb as a stored_file (fast on next requests).
 * - All options are configured via set_XXXX().
 */
class thumb_generator {

    /** @var int|null */
    protected $width = 0;

    /** @var int|null */
    protected $height = 0;

    /**
     * cover  = resize preserving aspect ratio and crop center to exact WxH
     * contain = resize preserving aspect ratio and fit inside WxH (no crop)
     *
     * @var string
     */
    protected $mode = "cover";

    /** @var bool */
    protected $allowupscale = false;

    /** @var string auto|png|jpeg */
    protected $outputformat = "auto";

    /** @var int 0..100 */
    protected $jpegquality = 85;

    /** @var string */
    protected $cachefilearea = "";

    /** @var int */
    protected $cacheitemid = 0;

    /** @var string */
    protected $cachefilepath = "/";

    /** @var bool */
    protected $forceregenerate = false;

    /** @var int|null User id for file record (0 = system). */
    protected $userid = 0;

    /**
     * Function set_width
     *
     * @param int $width
     * @return $this
     */
    public function set_width(int $width): self {
        $this->width = max(0, $width);
        return $this;
    }

    /**
     * Function set_height
     *
     * @param int $height
     * @return $this
     */
    public function set_height(int $height): self {
        $this->height = max(0, $height);
        return $this;
    }

    /**
     * Function set_mode
     *
     * @param string $mode
     * @return $this
     */
    public function set_mode(string $mode): self {
        // Values...
        // cover = cut to fill...
        // contain = keep everything and there may be a border left over.
        $mode = strtolower(trim($mode));
        $this->mode = in_array($mode, ["cover", "contain"], true) ? $mode : "cover";
        return $this;
    }

    /**
     * Function set_allow_upscale
     *
     * @param bool $allow
     * @return $this
     */
    public function set_allow_upscale(bool $allow): self {
        $this->allowupscale = $allow;
        return $this;
    }

    /**
     * Function set_output_format
     *
     * @param string $format
     * @return $this
     */
    public function set_output_format(string $format): self {
        // Values: auto|png|jpeg.
        $format = strtolower(trim($format));
        $this->outputformat = in_array($format, ["auto", "png", "jpeg"], true) ? $format : "auto";
        return $this;
    }

    /**
     * Function set_jpeg_quality
     *
     * @param int $quality
     * @return $this
     */
    public function set_jpeg_quality(int $quality): self {
        $this->jpegquality = min(100, max(0, $quality));
        return $this;
    }

    /**
     * Function set_cache_filearea
     *
     * @param string $filearea
     * @return $this
     */
    public function set_cache_filearea(string $filearea): self {
        $this->cachefilearea = trim($filearea);
        return $this;
    }

    /**
     * Function set_cache_itemid
     *
     * @param int $itemid
     * @return $this
     */
    public function set_cache_itemid(int $itemid): self {
        $this->cacheitemid = max(0, $itemid);
        return $this;
    }

    /**
     * Function set_cache_filepath
     *
     * @param string $filepath
     * @return $this
     */
    public function set_cache_filepath(string $filepath): self {
        $filepath = trim($filepath);
        $this->cachefilepath = ($filepath === "" || $filepath[0] !== "/") ? "/{$filepath}" : $filepath;
        if (substr($this->cachefilepath, -1) !== "/") {
            $this->cachefilepath .= "/";
        }
        return $this;
    }

    /**
     * Function set_force_regenerate
     *
     * @param bool $force
     * @return $this
     */
    public function set_force_regenerate(bool $force): self {
        $this->forceregenerate = $force;
        return $this;
    }

    /**
     * Function set_userid
     *
     * @param int $userid
     * @return $this
     */
    public function set_userid(int $userid): self {
        $this->userid = max(0, $userid);
        return $this;
    }

    /**
     * Returns an existing cached thumbnail, or generates it and stores in filepool.
     *
     * @param stored_file $source
     * @param context $context
     * @return stored_file|null
     */
    public function get_or_create(stored_file $source, context $context): ?stored_file {
        if (!$this->is_supported_image($source)) {
            return null;
        }

        $dims = $this->resolve_target_dimensions($source);
        if ($dims === null) {
            return null;
        }

        [$targetw, $targeth] = $dims;
        $fs = get_file_storage();

        $thumbfilename = $this->build_thumb_filename($source, $targetw, $targeth);
        $existing = $fs->get_file(
            $context->id,
            "theme_degrade",
            $this->cachefilearea,
            $this->cacheitemid,
            $this->cachefilepath,
            $thumbfilename
        );

        if ($existing && !$this->forceregenerate) {
            return $existing;
        }

        $tmp = $this->create_temp_image($source, $targetw, $targeth);
        if ($tmp === null) {
            return null;
        }

        $record = [
            "contextid" => $context->id,
            "component" => "theme_degrade",
            "filearea" => $this->cachefilearea,
            "itemid" => $this->cacheitemid,
            "filepath" => $this->cachefilepath,
            "filename" => $thumbfilename,
            "userid" => $this->userid,
        ];

        try {
            $thumb = $fs->create_file_from_pathname($record, $tmp);
        } catch (Throwable) {
            // Likely a race condition: another request created it first.
            $thumb = $fs->get_file(
                $context->id,
                "theme_degrade",
                $this->cachefilearea,
                $this->cacheitemid,
                $this->cachefilepath,
                $thumbfilename
            );
        }

        @unlink($tmp);

        return $thumb ?: null;
    }

    /**
     * Basic validation for image types.
     *
     * @param stored_file $file
     * @return bool
     */
    protected function is_supported_image(stored_file $file): bool {
        if ($file->is_directory()) {
            return false;
        }

        $mimetype = $file->get_mimetype();
        if (strpos($mimetype, "image/") !== 0) {
            return false;
        }

        // If available in your Moodle, keep it strict:
        if (method_exists($file, "is_valid_image")) {
            return $file->is_valid_image();
        }

        return true;
    }

    /**
     * Resolves final dimensions considering width/height = 0 as "not provided".
     *
     * @param stored_file $source
     * @return array<int,int>|null
     */
    protected function resolve_target_dimensions(stored_file $source): ?array {
        $srcinfo = @getimagesizefromstring($source->get_content());
        if (!$srcinfo || empty($srcinfo[0]) || empty($srcinfo[1])) {
            return null;
        }

        $srcw = (int) $srcinfo[0];
        $srch = (int) $srcinfo[1];

        $w = max(0, (int) $this->width);
        $h = max(0, (int) $this->height);

        // Both missing.
        if ($w === 0 && $h === 0) {
            return null;
        }

        // If only height is provided, calculate width proportionally.
        if ($w === 0 && $h > 0) {
            $h = $this->apply_upscale_limit($h, $srch);
            $w = (int) max(1, round($srcw * ($h / $srch)));
            return [$w, $h];
        }

        // If only width is provided, calculate height proportionally.
        if ($h === 0 && $w > 0) {
            $w = $this->apply_upscale_limit($w, $srcw);
            $h = (int) max(1, round($srch * ($w / $srcw)));
            return [$w, $h];
        }

        // Both provided.
        if (!$this->allowupscale) {
            $w = min($w, $srcw);
            $h = min($h, $srch);
        }

        $w = (int) max(1, $w);
        $h = (int) max(1, $h);

        return [$w, $h];
    }

    /**
     * Prevent upscaling if disabled.
     *
     * @param int $target
     * @param int $source
     * @return int
     */
    protected function apply_upscale_limit(int $target, int $source): int {
        if ($this->allowupscale) {
            return $target;
        }
        return min($target, $source);
    }

    /**
     * Creates a temporary resized/cropped image and returns the temp pathname.
     *
     * @param stored_file $source
     * @param int $targetw
     * @param int $targeth
     * @return string|null
     */
    protected function create_temp_image(stored_file $source, int $targetw, int $targeth): ?string {
        $blob = $source->get_content();
        $src = @imagecreatefromstring($blob);
        if (!$src) {
            return null;
        }

        $srcw = imagesx($src);
        $srch = imagesy($src);

        $outformat = $this->resolve_output_format($source);

        if ($this->mode === "cover" && $this->width !== null && $this->height !== null) {
            // Scale so it covers the target, then crop center.
            $scale = max($targetw / $srcw, $targeth / $srch);
            $neww = (int) max(1, ceil($srcw * $scale));
            $newh = (int) max(1, ceil($srch * $scale));

            $tmp = $this->new_canvas($neww, $newh, $outformat);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $neww, $newh, $srcw, $srch);

            $dest = $this->new_canvas($targetw, $targeth, $outformat);
            $srcx = (int) max(0, floor(($neww - $targetw) / 2));
            $srcy = (int) max(0, floor(($newh - $targeth) / 2));
            imagecopy($dest, $tmp, 0, 0, $srcx, $srcy, $targetw, $targeth);

            imagedestroy($tmp);
        } else {
            // contain or single dimension: just resample to target size.
            $dest = $this->new_canvas($targetw, $targeth, $outformat);

            if ($this->mode === "contain" && $this->width !== null && $this->height !== null) {
                // Fit inside target canvas, center it.
                $scale = min($targetw / $srcw, $targeth / $srch);
                if (!$this->allowupscale) {
                    $scale = min(1.0, $scale);
                }

                $neww = (int) max(1, floor($srcw * $scale));
                $newh = (int) max(1, floor($srch * $scale));
                $dstx = (int) max(0, floor(($targetw - $neww) / 2));
                $dsty = (int) max(0, floor(($targeth - $newh) / 2));

                imagecopyresampled($dest, $src, $dstx, $dsty, 0, 0, $neww, $newh, $srcw, $srch);
            } else {
                imagecopyresampled($dest, $src, 0, 0, 0, 0, $targetw, $targeth, $srcw, $srch);
            }
        }

        imagedestroy($src);

        $tmpdir = make_temp_directory("theme_degrade_thumb");
        $ext = ($outformat === "jpeg") ? ".jpg" : ".png";
        $tmpfile = tempnam($tmpdir, "thumb_");
        if ($tmpfile === false) {
            imagedestroy($dest);
            return null;
        }

        // tempnam creates a file; we want extension.
        $finaltmp = $tmpfile . $ext;
        @rename($tmpfile, $finaltmp);

        if ($outformat === "jpeg") {
            $ok = imagejpeg($dest, $finaltmp, $this->jpegquality);
        } else {
            // png: 0 (no compression) .. 9 (max). Use 6 default-like.
            $ok = imagepng($dest, $finaltmp, 6);
        }

        imagedestroy($dest);

        if (!$ok) {
            @unlink($finaltmp);
            return null;
        }

        return $finaltmp;
    }

    /**
     * Creates a new image canvas with proper alpha/padding defaults.
     *
     * @param int $w
     * @param int $h
     * @param string $format png|jpeg
     * @return GdImage|resource
     */
    protected function new_canvas(int $w, int $h, string $format) {
        $img = imagecreatetruecolor($w, $h);

        if ($format === "png") {
            // Transparent background.
            imagealphablending($img, false);
            imagesavealpha($img, true);
            $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
            imagefilledrectangle($img, 0, 0, $w, $h, $transparent);
        } else {
            // White background for jpeg.
            $white = imagecolorallocate($img, 255, 255, 255);
            imagefilledrectangle($img, 0, 0, $w, $h, $white);
        }

        return $img;
    }

    /**
     * Decides output format based on config and source mime.
     *
     * @param stored_file $source
     * @return string png|jpeg
     */
    protected function resolve_output_format(stored_file $source): string {
        if ($this->outputformat === "png") {
            return "png";
        }
        if ($this->outputformat === "jpeg") {
            return "jpeg";
        }

        $mime = $source->get_mimetype();
        // Keep alpha-friendly formats as png in auto mode.
        if ($mime === "image/png" || $mime === "image/gif" || $mime === "image/webp") {
            return "png";
        }

        return "jpeg";
    }

    /**
     * Builds a deterministic cache filename.
     *
     * @param stored_file $source
     * @param int $w
     * @param int $h
     * @return string
     */
    protected function build_thumb_filename(stored_file $source, int $w, int $h): string {
        $hash = $source->get_contenthash();
        $mode = $this->mode;
        $fmt = $this->resolve_output_format($source);
        $ext = ($fmt === "jpeg") ? "jpg" : "png";

        // Example: thumb_240x136_cover_ab12cd34....png
        return "thumb_{$w}x{$h}_{$mode}_{$hash}.{$ext}";
    }
}

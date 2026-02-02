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
 * Icon extractor for "solid background" images.
 *
 * @package   theme_degrade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade;

use GdImage;
use RuntimeException;
use SplQueue;

/**
 * Icon extractor for "solid background" images.
 *
 * Strategy:
 *  - Check only the 4 corners to decide if the background is solid (within tolerance).
 *  - If solid, flood-fill from the borders removing background-like pixels (connected to the edges only).
 *  - Compute content bounding box and crop.
 *  - Optionally force a square output (pad or crop).
 *
 * Requirements:
 *  - PHP GD extension enabled.
 */
class icon_extractor {

    /** @var string|null */
    private $sourcefile = null;

    /** @var string|null */
    private $sourceblob = null;

    /** @var int */
    private $cornertolerance = 8;

    /** @var int */
    private $backgroundtolerance = 12;

    /** @var int */
    private $croppadding = 0;

    /** @var bool */
    private $forcesquare = true;

    /**
     * Square mode:
     *  - "pad": keeps full content, adds transparent padding to make a square.
     *  - "crop": crops the longer side to the shorter side (centered).
     *
     * @var string
     */
    private $squaremode = "pad";

    /** @var bool */
    private $usediagonals = false;

    /** @var int */
    private $maxinputpixels = 12000000; // Safety guard.

    /** @var resource|GdImage|null */
    private $result = null;

    /**
     * Function set_source_file
     *
     * @param string $filepath
     * @return $this
     */
    public function set_source_file(string $filepath): self {
        $this->sourcefile = $filepath;
        $this->sourceblob = null;
        return $this;
    }

    /**
     * Function set_source_blob
     *
     * @param string $blob
     * @return $this
     */
    public function set_source_blob(string $blob): self {
        $this->sourceblob = $blob;
        $this->sourcefile = null;
        return $this;
    }

    /**
     * Function set_cornertolerance
     *
     * @param int $tolerance
     * @return $this
     */
    public function set_cornertolerance(int $tolerance): self {
        $this->cornertolerance = max(0, min(255, $tolerance));
        return $this;
    }

    /**
     * Function set_backgroundtolerance
     *
     * @param int $tolerance
     * @return $this
     */
    public function set_backgroundtolerance(int $tolerance): self {
        $this->backgroundtolerance = max(0, min(255, $tolerance));
        return $this;
    }

    /**
     * Function set_croppadding
     *
     * @param int $padding
     * @return $this
     */
    public function set_croppadding(int $padding): self {
        $this->croppadding = max(0, $padding);
        return $this;
    }

    /**
     * Function set_forcesquare
     *
     * @param bool $enabled
     * @return $this
     */
    public function set_forcesquare(bool $enabled): self {
        $this->forcesquare = $enabled;
        return $this;
    }

    /**
     * Function set_squaremode
     *
     * @param string $mode
     * @return $this
     */
    public function set_squaremode(string $mode): self {
        $mode = strtolower(trim($mode));
        if (!in_array($mode, ["pad", "crop"], true)) {
            throw new RuntimeException("Invalid squaremode. Allowed: pad, crop");
        }
        $this->squaremode = $mode;
        return $this;
    }

    /**
     * Function set_usediagonals
     *
     * @param bool $enabled
     * @return $this
     */
    public function set_usediagonals(bool $enabled): self {
        $this->usediagonals = $enabled;
        return $this;
    }

    /**
     * Function set_maxinputpixels
     *
     * @param int $maxpixels
     * @return $this
     */
    public function set_maxinputpixels(int $maxpixels): self {
        $this->maxinputpixels = max(100000, $maxpixels);
        return $this;
    }

    /**
     * Processes the source and stores the extracted icon as an internal PNG (GD image) with alpha.
     *
     * @return self
     */
    public function process(): self {
        $this->assert_gd_available();

        $img = $this->load_source_image();
        $w = imagesx($img);
        $h = imagesy($img);

        if (($w * $h) > $this->maxinputpixels) {
            imagedestroy($img);
            throw new RuntimeException("Image too large ({$w}x{$h}). Increase maxinputpixels if needed.");
        }

        // Ensure alpha output is possible.
        imagealphablending($img, false);
        imagesavealpha($img, true);

        $bg = $this->background_from_corners($img);
        if (!$this->corners_are_solid($img, $bg)) {
            imagedestroy($img);
            throw new RuntimeException("Background is not solid (corners differ beyond tolerance).");
        }

        // 1) Build mask of pixels that look like background color.
        $mask = $this->build_background_mask($img, $w, $h, $bg);

        // 2) Flood-fill from borders through background-like pixels to mark removable background.
        $visited = $this->flood_fill_border($mask, $w, $h);

        // 3) Compute content bbox (anything not visited is considered content, including internal whites).
        [$minx, $miny, $maxx, $maxy] = $this->compute_content_bbox($visited, $w, $h);

        // Apply padding.
        $minx = max(0, $minx - $this->croppadding);
        $miny = max(0, $miny - $this->croppadding);
        $maxx = min($w - 1, $maxx + $this->croppadding);
        $maxy = min($h - 1, $maxy + $this->croppadding);

        // 4) Create cropped transparent PNG (copy only content pixels, skip visited background).
        $cropped = $this->copy_crop_transparent($img, $visited, $w, $h, $minx, $miny, $maxx, $maxy);

        imagedestroy($img);

        // 5) Force square if enabled.
        if ($this->forcesquare) {
            $cropped = $this->make_square($cropped);
        }

        $this->result = $cropped;
        return $this;
    }

    /**
     * Returns the resulting PNG binary (after process()).
     * If width/height are provided (>0), the output will be resized.
     *
     * - If only width is provided, height is calculated to keep aspect ratio.
     * - If only height is provided, width is calculated to keep aspect ratio.
     * - If both are provided, the image is resized to exact dimensions.
     *
     * @param string $file save filepath
     * @param int $width Target width in pixels (0 = keep original)
     * @param int $height Target height in pixels (0 = keep original)
     */
    public function get_result_png($file = null, int $width = 0, int $height = 0) {
        if ($this->result === null) {
            throw new RuntimeException("No result yet. Call process() first.");
        }

        $img = $this->result;
        if ($width > 0 || $height > 0) {
            $ow = imagesx($img);
            $oh = imagesy($img);

            // Keep aspect ratio if one dimension is missing.
            if ($width > 0 && $height === 0) {
                $height = max(1, round($oh * ($width / $ow)));
            } else if ($height > 0 && $width === 0) {
                $width = max(1, round($ow * ($height / $oh)));
            }

            // Avoid unnecessary work.
            if ($width !== $ow || $height !== $oh) {
                $tmp = imagecreatetruecolor($width, $height);

                // Preserve alpha.
                imagealphablending($tmp, false);
                imagesavealpha($tmp, true);
                $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
                imagefill($tmp, 0, 0, $transparent);

                // High-quality resample.
                imagecopyresampled($tmp, $img, 0, 0, 0, 0, $width, $height, $ow, $oh);

                $img = $tmp;
            }
        }

        if ($file) {
            imagepng($img, $file);
        } else {
            header('Content-Type: image/png');
            imagepng($img);
        }
    }

    /**
     * Saves the resulting PNG to a file (after process()).
     *
     * @param string $filepath
     * @return self
     */
    public function save_result(string $filepath): self {
        if ($this->result === null) {
            throw new RuntimeException("No result yet. Call process() first.");
        }

        $dir = dirname($filepath);
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new RuntimeException("Destination directory is not writable: {$dir}");
        }

        if (!imagepng($this->result, $filepath)) {
            throw new RuntimeException("Failed to save PNG: {$filepath}");
        }

        return $this;
    }

    /**
     * Destroys internal result image (optional housekeeping).
     *
     * @return self
     */
    public function destroy_result(): self {
        if ($this->result !== null) {
            imagedestroy($this->result);
            $this->result = null;
        }
        return $this;
    }

    /**
     * Function assert_gd_available
     *
     * @return void
     */
    private function assert_gd_available(): void {
        if (!extension_loaded("gd")) {
            throw new RuntimeException("GD extension is required.");
        }
        if (!function_exists("imagecreatefrompng")) {
            throw new RuntimeException("GD image functions are not available.");
        }
    }

    /**
     * Function load_source_image
     *
     * @return GdImage|resource
     */
    private function load_source_image() {
        if ($this->sourceblob !== null) {
            $img = @imagecreatefromstring($this->sourceblob);
            if (!$img) {
                throw new RuntimeException("Unable to load image from blob.");
            }
            return $this->to_truecolor_with_alpha($img);
        }

        if ($this->sourcefile === null || $this->sourcefile === "") {
            throw new RuntimeException("No source provided. Use set_source_file() or set_source_blob().");
        }
        if (!is_readable($this->sourcefile)) {
            throw new RuntimeException("Source file is not readable: {$this->sourcefile}");
        }

        $info = @getimagesize($this->sourcefile);
        if (!$info || empty($info["mime"])) {
            throw new RuntimeException("Unable to detect image type: {$this->sourcefile}");
        }

        $mime = strtolower($info["mime"]);
        switch ($mime) {
            case "image/png":
                $img = @imagecreatefrompng($this->sourcefile);
                break;
            case "image/jpeg":
                $img = @imagecreatefromjpeg($this->sourcefile);
                break;
            case "image/gif":
                $img = @imagecreatefromgif($this->sourcefile);
                break;
            case "image/webp":
                if (!function_exists("imagecreatefromwebp")) {
                    throw new RuntimeException("WebP not supported by GD in this environment.");
                }
                $img = @imagecreatefromwebp($this->sourcefile);
                break;
            default:
                throw new RuntimeException("Unsupported image type: {$mime}");
        }

        if (!$img) {
            throw new RuntimeException("Unable to load image: {$this->sourcefile}");
        }

        return $this->to_truecolor_with_alpha($img);
    }

    /**
     * Converts image to truecolor and enables alpha channel.
     *
     * @param resource|GdImage $img
     * @return resource|GdImage
     */
    private function to_truecolor_with_alpha($img) {
        // If not truecolor, convert.
        if (!imageistruecolor($img)) {
            $w = imagesx($img);
            $h = imagesy($img);

            $tmp = imagecreatetruecolor($w, $h);
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);

            $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
            imagefill($tmp, 0, 0, $transparent);

            imagecopy($tmp, $img, 0, 0, 0, 0, $w, $h);
            imagedestroy($img);

            $img = $tmp;
        } else {
            imagealphablending($img, false);
            imagesavealpha($img, true);
        }

        return $img;
    }

    /**
     * Background color is computed as the average of 4 corners (RGB only).
     *
     * @param resource|GdImage $img
     * @return array{r:int,g:int,b:int}
     */
    private function background_from_corners($img): array {
        $w = imagesx($img);
        $h = imagesy($img);

        $c1 = $this->rgba_at($img, 0, 0);
        $c2 = $this->rgba_at($img, $w - 1, 0);
        $c3 = $this->rgba_at($img, 0, $h - 1);
        $c4 = $this->rgba_at($img, $w - 1, $h - 1);

        $r = intdiv(($c1["r"] + $c2["r"] + $c3["r"] + $c4["r"]), 4);
        $g = intdiv(($c1["g"] + $c2["g"] + $c3["g"] + $c4["g"]), 4);
        $b = intdiv(($c1["b"] + $c2["b"] + $c3["b"] + $c4["b"]), 4);

        return ["r" => $r, "g" => $g, "b" => $b];
    }

    /**
     * Checks only the 4 corners against the computed background.
     *
     * @param resource|GdImage $img
     * @param array{r:int,g:int,b:int} $bg
     * @return bool
     */
    private function corners_are_solid($img, array $bg): bool {
        $w = imagesx($img);
        $h = imagesy($img);

        $corners = [
            $this->rgba_at($img, 0, 0),
            $this->rgba_at($img, $w - 1, 0),
            $this->rgba_at($img, 0, $h - 1),
            $this->rgba_at($img, $w - 1, $h - 1),
        ];

        foreach ($corners as $c) {
            $dr = abs($c["r"] - $bg["r"]);
            $dg = abs($c["g"] - $bg["g"]);
            $db = abs($c["b"] - $bg["b"]);
            if (max($dr, $dg, $db) > $this->cornertolerance) {
                return false;
            }
        }

        return true;
    }

    /**
     * Builds a mask string where "\1" means "pixel is background-like", "\0" otherwise.
     *
     * @param resource|GdImage $img
     * @param int $w
     * @param int $h
     * @param array{r:int,g:int,b:int} $bg
     * @return string
     */
    private function build_background_mask($img, int $w, int $h, array $bg): string {
        $mask = str_repeat("\0", $w * $h);
        $idx = 0;

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorat($img, $x, $y);
                if ($c < 0) {
                    $c = $c & 0xFFFFFFFF;
                }
                $r = ($c >> 16) & 0xFF;
                $g = ($c >> 8) & 0xFF;
                $b = $c & 0xFF;

                $dr = abs($r - $bg["r"]);
                $dg = abs($g - $bg["g"]);
                $db = abs($b - $bg["b"]);

                if (max($dr, $dg, $db) <= $this->backgroundtolerance) {
                    $mask[$idx] = "\1";
                }

                $idx++;
            }
        }

        return $mask;
    }

    /**
     * Flood-fill from image borders through background-like pixels.
     * Returns a "visited" string where "\1" are removable background pixels.
     *
     * @param string $mask
     * @param int $w
     * @param int $h
     * @return string
     */
    private function flood_fill_border(string $mask, int $w, int $h): string {
        $visited = str_repeat("\0", $w * $h);
        $q = new SplQueue();

        $lastrow = ($h - 1) * $w;

        // Top & bottom borders.
        for ($x = 0; $x < $w; $x++) {
            $top = $x;
            if ($mask[$top] === "\1" && $visited[$top] === "\0") {
                $visited[$top] = "\1";
                $q->enqueue($top);
            }

            $bottom = $lastrow + $x;
            if ($mask[$bottom] === "\1" && $visited[$bottom] === "\0") {
                $visited[$bottom] = "\1";
                $q->enqueue($bottom);
            }
        }

        // Left & right borders (excluding corners already handled).
        for ($y = 1; $y < $h - 1; $y++) {
            $left = $y * $w;
            if ($mask[$left] === "\1" && $visited[$left] === "\0") {
                $visited[$left] = "\1";
                $q->enqueue($left);
            }

            $right = ($y * $w) + ($w - 1);
            if ($mask[$right] === "\1" && $visited[$right] === "\0") {
                $visited[$right] = "\1";
                $q->enqueue($right);
            }
        }

        // Neighbors: 4-connected by default; optional diagonals.
        while (!$q->isEmpty()) {
            $idx = $q->dequeue();
            $x = $idx % $w;

            // Left.
            if ($x > 0) {
                $n = $idx - 1;
                if ($visited[$n] === "\0" && $mask[$n] === "\1") {
                    $visited[$n] = "\1";
                    $q->enqueue($n);
                }
            }

            // Right.
            if ($x < ($w - 1)) {
                $n = $idx + 1;
                if ($visited[$n] === "\0" && $mask[$n] === "\1") {
                    $visited[$n] = "\1";
                    $q->enqueue($n);
                }
            }

            // Up.
            if ($idx >= $w) {
                $n = $idx - $w;
                if ($visited[$n] === "\0" && $mask[$n] === "\1") {
                    $visited[$n] = "\1";
                    $q->enqueue($n);
                }
            }

            // Down.
            if ($idx < ($w * ($h - 1))) {
                $n = $idx + $w;
                if ($visited[$n] === "\0" && $mask[$n] === "\1") {
                    $visited[$n] = "\1";
                    $q->enqueue($n);
                }
            }

            if ($this->usediagonals) {
                // Up-left.
                if ($x > 0 && $idx >= $w) {
                    $n = $idx - $w - 1;
                    if ($visited[$n] === "\0" && $mask[$n] === "\1") {
                        $visited[$n] = "\1";
                        $q->enqueue($n);
                    }
                }
                // Up-right.
                if ($x < ($w - 1) && $idx >= $w) {
                    $n = $idx - $w + 1;
                    if ($visited[$n] === "\0" && $mask[$n] === "\1") {
                        $visited[$n] = "\1";
                        $q->enqueue($n);
                    }
                }
                // Down-left.
                if ($x > 0 && $idx < ($w * ($h - 1))) {
                    $n = $idx + $w - 1;
                    if ($visited[$n] === "\0" && $mask[$n] === "\1") {
                        $visited[$n] = "\1";
                        $q->enqueue($n);
                    }
                }
                // Down-right.
                if ($x < ($w - 1) && $idx < ($w * ($h - 1))) {
                    $n = $idx + $w + 1;
                    if ($visited[$n] === "\0" && $mask[$n] === "\1") {
                        $visited[$n] = "\1";
                        $q->enqueue($n);
                    }
                }
            }
        }

        return $visited;
    }

    /**
     * Finds min/max coordinates for pixels that are NOT removable background (visited == "\0").
     *
     * @param string $visited
     * @param int $w
     * @param int $h
     * @return array{0:int,1:int,2:int,3:int}
     */
    private function compute_content_bbox(string $visited, int $w, int $h): array {
        $minx = $w;
        $miny = $h;
        $maxx = -1;
        $maxy = -1;

        $idx = 0;
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                if ($visited[$idx] === "\0") {
                    if ($x < $minx) {
                        $minx = $x;
                    }
                    if ($y < $miny) {
                        $miny = $y;
                    }
                    if ($x > $maxx) {
                        $maxx = $x;
                    }
                    if ($y > $maxy) {
                        $maxy = $y;
                    }
                }
                $idx++;
            }
        }

        if ($maxx < 0 || $maxy < 0) {
            throw new RuntimeException("No content detected after background analysis.");
        }

        return [$minx, $miny, $maxx, $maxy];
    }

    /**
     * Creates a cropped image with transparent background by copying only non-visited pixels.
     *
     * @param resource|GdImage $src
     * @param string $visited
     * @param int $w
     * @param int $h
     * @param int $minx
     * @param int $miny
     * @param int $maxx
     * @param int $maxy
     * @return resource|GdImage
     */
    private function copy_crop_transparent($src, string $visited, int $w, int $h, int $minx, int $miny, int $maxx, int $maxy) {
        $cw = ($maxx - $minx) + 1;
        $ch = ($maxy - $miny) + 1;

        $dst = imagecreatetruecolor($cw, $ch);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        for ($y = $miny; $y <= $maxy; $y++) {
            $rowbase = $y * $w;
            for ($x = $minx; $x <= $maxx; $x++) {
                $idx = $rowbase + $x;
                if ($visited[$idx] === "\1") {
                    continue;
                }

                $color = imagecolorat($src, $x, $y);
                imagesetpixel($dst, $x - $minx, $y - $miny, $color);
            }
        }

        return $dst;
    }

    /**
     * Makes the output square by either padding or cropping (centered).
     *
     * @param resource|GdImage $img
     * @return resource|GdImage
     */
    private function make_square($img) {
        $w = imagesx($img);
        $h = imagesy($img);

        if ($w === $h) {
            return $img;
        }

        if ($this->squaremode === "crop") {
            $side = min($w, $h);
            $sx = intdiv(max(0, $w - $side), 2);
            $sy = intdiv(max(0, $h - $side), 2);

            if (function_exists("imagecrop")) {
                $cropped = imagecrop($img, ["x" => $sx, "y" => $sy, "width" => $side, "height" => $side]);
                if ($cropped) {
                    imagedestroy($img);
                    imagealphablending($cropped, false);
                    imagesavealpha($cropped, true);
                    return $cropped;
                }
            }

            // Fallback: manual crop copy.
            $dst = imagecreatetruecolor($side, $side);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $transparent);

            imagecopy($dst, $img, 0, 0, $sx, $sy, $side, $side);
            imagedestroy($img);

            return $dst;
        }

        // Default: pad.
        $side = max($w, $h);
        $dst = imagecreatetruecolor($side, $side);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        $dx = intdiv(($side - $w), 2);
        $dy = intdiv(($side - $h), 2);

        imagecopy($dst, $img, $dx, $dy, 0, 0, $w, $h);
        imagedestroy($img);

        return $dst;
    }

    /**
     * Reads RGBA at coordinate.
     *
     * @param resource|GdImage $img
     * @param int $x
     * @param int $y
     * @return array{r:int,g:int,b:int,a:int}
     */
    private function rgba_at($img, int $x, int $y): array {
        $c = imagecolorat($img, $x, $y);
        if ($c < 0) {
            $c = $c & 0xFFFFFFFF;
        }

        $a = ($c >> 24) & 0x7F; // 0 => opaque, 127 => transparent.
        $r = ($c >> 16) & 0xFF;
        $g = ($c >> 8) & 0xFF;
        $b = $c & 0xFF;

        return ["r" => $r, "g" => $g, "b" => $b, "a" => $a];
    }
}

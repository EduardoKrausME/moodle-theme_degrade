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
 * Settings class that validates a SCSS snippet entered in a textarea.
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\admin;

use admin_setting_configtextarea;
use Exception;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use Throwable;

require_once("{$CFG->dirroot}/lib/adminlib.php");

/**
 * Settings class that validates a SCSS snippet entered in a textarea.
 *
 * This class extends Moodle's admin_setting_configtextarea to provide
 * on-save validation by compiling the input as SCSS using scssphp.
 *
 * @package   theme_degrade
 */
class setting_scss extends admin_setting_configtextarea {
    /**
     * Validate the SCSS provided by the admin.
     *
     * Compiles the input in isolation to catch syntax errors before saving.
     *
     * @param mixed $data Raw textarea input.
     * @return bool|string True when valid, or a localized error string when invalid.
     * @throws Exception
     */
    public function validate($data) {
        // Syntactic validation: compile the snippet in isolation.
        try {
            $compiler = new Compiler();

            // If your SCSS uses theme-relative @import directives, enable import paths.
            $compiler->setImportPaths([__DIR__ . "/../../scss"]);

            $compiler->compileString((string)$data);

            return true;
        } catch (Throwable $e) {
            // The scssphp exceptions usually include "line X, column Y" in the message.
            $msg = $e->getMessage();
            return get_string("error_invalidscss", "theme_degrade", $msg);
        } catch (SassException $e) {
            // The scssphp exceptions usually include "line X, column Y" in the message.
            $msg = $e->getMessage();
            return get_string("error_invalidscss", "theme_degrade", $msg);
        }
    }
}

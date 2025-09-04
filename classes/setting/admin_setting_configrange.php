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
 * The most flexible setting, the user enters range.
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_degrade\setting;

use admin_setting;
use core\exception\moodle_exception;
use Exception;

/**
 * Class admin_setting_configrange
 */
class admin_setting_configrange extends admin_setting {

    /** @var int */
    public $min;

    /** @var int */
    public $max;

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param int $min
     * @param int $max
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $min = 0, $max = 100) {
        $this->paramtype = PARAM_INT;
        $this->min = $min;
        $this->max = $max;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * write_setting
     *
     * @param $data
     * @return string|true
     * @throws Exception
     */
    public function write_setting($data) {
        if ($data === "") {
            $data = 0;
        }
        // Data is a string.
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? "" : get_string("errorsetting", "admin"));
    }

    /**
     * Validate data before storage
     *
     * @param string $data
     * @return string|true true if ok string if error found
     * @throws Exception
     */
    public function validate($data) {
        // Validation of limits (minimum / maximum).
        if (isset($this->min) || isset($this->max)) {
            // If numeric.
            if (is_numeric($data)) {
                if (isset($this->min) && $data < $this->min) {
                    return get_string("validateerror", "admin");
                }
                if (isset($this->max) && $data > $this->max) {
                    return get_string("validateerror", "admin");
                }
            }
        }

        return true;
    }


    /**
     * Return an XHTML string for the setting
     *
     * @param $data
     * @param string $query
     * @return string Returns an XHTML string
     * @throws moodle_exception
     */
    public function output_html($data, $query = "") {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $context = (object) [
            "id" => $this->get_id(),
            "name" => $this->get_full_name(),
            "value" => $data,
            "min" => $this->min,
            "max" => $this->max,
        ];
        $element = $OUTPUT->render_from_template("theme_degrade/core_admin/setting_configrange", $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, "", $default, $query);
    }
}

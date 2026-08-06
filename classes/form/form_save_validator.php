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

namespace theme_degrade\form;

use stdClass;

/**
 * phpcs:disable Squiz.PHP.CommentedOutCode.Found
 *
 * Class form_save_validator
 *
 * @package   theme_degrade
 * @copyright 2025 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class form_save_validator {
    /**
     * Validate the full $_POST["save"] payload based on $json->form->block and $json->form->blocks.
     * Returns only valid items/rows (invalid ones are skipped), plus an error list.
     *
     * @param \stdClass $json
     * @return \stdClass
     */
    public static function validate(stdClass $json): stdClass {
        $save = $_POST["save"];
        $result = [];
        $errors = [];

        $form = isset($json->form) && is_object($json->form) ? $json->form : null;
        if ($form === null) {
            return (object) $result;
        }

        // 1) Validate single block fields: $_POST["save"][$key].
        $blockout = self::validate_block($form->block ?? null, $save, $errors);
        foreach ($blockout as $k => $v) {
            $result[$k] = $v;
        }

        // 2) Validate repeatable rows: $_POST["save"][N][$key].
        $blocksout = self::validate_blocks($form->blocks ?? null, $save, $errors);
        foreach ($blocksout as $index => $rowobj) {
            // Keep numeric indexes as properties (prints as [0], [1], ...).
            $result[$index] = $rowobj;
        }

        return (object) $result;
    }

    /**
     * Validate $json->form->block definitions against $_POST["save"][$key].
     * Returns only valid items.
     *
     * @param $blockdef
     * @param array $save
     * @param array $errors
     * @return array
     */
    public static function validate_block($blockdef, array $save, array &$errors): array {
        $out = [];

        if ($blockdef === null) {
            return $out;
        }

        $defs = self::object_to_array($blockdef);

        foreach ($defs as $key => $fielddef) {
            if (!is_string($key) || $key === "") {
                continue;
            }

            if (!is_object($fielddef)) {
                $errors[] = ["path" => "block." . $key, "reason" => "invalid_field_definition"];
                continue;
            }

            $required = !empty($fielddef->required);
            $hasdefault = property_exists($fielddef, "default_data");

            $valueexists = array_key_exists($key, $save);
            $value = $valueexists ? $save[$key] : null;

            if (!$valueexists && $hasdefault) {
                $value = $fielddef->default_data;
                $valueexists = true;
            }

            if (!$valueexists) {
                if ($required) {
                    $errors[] = ["path" => "block.{$key}", "reason" => "required_missing"];
                }
                continue;
            }

            [$ok, $clean, $reason] = self::validate_value_by_field_def($value, $fielddef);
            if (!$ok) {
                $errors[] = ["path" => "block.{$key}", "reason" => $reason];
                continue;
            }

            $out[$key] = $clean;
        }

        return $out;
    }

    /**
     * Validate $json->form->blocks definitions against $_POST["save"][N][$key].
     * A row is valid only if ALL defined keys are valid (and required keys exist).
     * Returns only valid rows.
     *
     * @param $blocksdef
     * @param array $save
     * @param array $errors
     * @return array
     */
    public static function validate_blocks($blocksdef, array $save, array &$errors): array {
        $out = [];

        if ($blocksdef === null) {
            return $out;
        }

        $defs = self::object_to_array($blocksdef);

        // Detect rows by numeric indexes in $save (0,1,2,... or "0","1"...).
        foreach ($save as $idx => $row) {
            if (!self::is_numeric_index($idx)) {
                continue;
            }

            if (is_object($row)) {
                $row = self::object_to_array($row);
            }

            if (!is_array($row)) {
                $errors[] = ["path" => "blocks[{$idx}]", "reason" => "row_not_array"];
                continue;
            }

            $rowout = [];
            $rowvalid = true;

            // Validate all defined fields for this row.
            foreach ($defs as $key => $fielddef) {
                if (!is_string($key) || $key === "") {
                    continue;
                }

                if (!is_object($fielddef)) {
                    $errors[] = ["path" => "blocks[{$idx}].{$key}", "reason" => "invalid_field_definition"];
                    $rowvalid = false;
                    break;
                }

                $required = !empty($fielddef->required);
                $hasdefault = property_exists($fielddef, "default_data");

                $valueexists = array_key_exists($key, $row);
                $value = $valueexists ? $row[$key] : null;

                if (!$valueexists && $hasdefault) {
                    $value = $fielddef->default_data;
                    $valueexists = true;
                }

                if (!$valueexists) {
                    if ($required) {
                        $errors[] = ["path" => "blocks[{$idx}].{$key}", "reason" => "required_missing"];
                        $rowvalid = false;
                        break;
                    }
                    // Not required and missing -> just skip.
                    continue;
                }

                [$ok, $clean, $reason] = self::validate_value_by_field_def($value, $fielddef);
                if (!$ok) {
                    $errors[] = ["path" => "blocks[{$idx}].{$key}", "reason" => $reason];
                    $rowvalid = false;
                    break;
                }

                $rowout[$key] = $clean;
            }

            if (!$rowvalid) {
                // Entire row rejected.
                continue;
            }

            $out[$idx] = (object) $rowout;
        }

        return $out;
    }

    /**
     * Validate a single value using field definition.
     * Supports valuetype=int|text (defaults to text).
     *
     * @param $value
     * @param \stdClass $fielddef
     * @return array
     */
    private static function validate_value_by_field_def($value, stdClass $fielddef): array {
        $valuetype = "text";
        if (property_exists($fielddef, "valuetype") && is_string($fielddef->valuetype) && $fielddef->valuetype !== "") {
            $valuetype = strtolower(trim($fielddef->valuetype));
        }

        if ($valuetype === "int") {
            return self::validate_int($value);
        }

        // Default: treat as text.
        return self::validate_text($value);
    }

    /**
     * Function validate_int
     *
     * @param $value
     * @return array
     */
    private static function validate_int($value): array {
        if (is_int($value)) {
            return [true, $value, ""];
        }

        if (is_bool($value) || is_array($value) || is_object($value)) {
            return [false, null, "invalid_int"];
        }

        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        if ($filtered === false) {
            return [false, null, "invalid_int"];
        }

        return [true, (int) $filtered, ""];
    }

    /**
     * Function validate_text
     *
     * @param $value
     * @return array
     */
    private static function validate_text($value): array {
        if (is_array($value) || is_object($value)) {
            return [false, null, "invalid_text"];
        }

        $text = self::sanitize_text($value);

        return [true, $text, ""];
    }

    /**
     * Function sanitize_text
     *
     * @param $value
     * @return string
     */
    private static function sanitize_text($value): string {
        $text = trim((string) $value);

        // Basic hardening: remove control chars (except \n and \t).
        $text = preg_replace("/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/u", "", $text) ?? "";

        return $text;
    }

    /**
     * Function object_to_array
     *
     * @param $obj
     * @return array
     */
    private static function object_to_array($obj): array {
        if (!is_object($obj)) {
            return [];
        }
        return get_object_vars($obj);
    }

    /**
     * Function is_numeric_index
     *
     * @param $idx
     * @return bool
     */
    private static function is_numeric_index($idx): bool {
        if (is_int($idx)) {
            return $idx >= 0;
        }
        if (!is_string($idx)) {
            return false;
        }
        return $idx !== "" && ctype_digit($idx);
    }
}

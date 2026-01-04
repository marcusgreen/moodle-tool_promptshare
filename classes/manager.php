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

namespace tool_promptshare;

/**
 * Class manager
 *
 * @package    tool_promptshare
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /**
     * Fetch prompts available to a specific user.
     * Includes their own prompts + those marked as 'is_global'.
     *
     * @param int $userid The user ID
     * @return array Array of prompt records
     */
    public static function get_available_prompts($userid) {
        global $DB;
        
        $sql = "userid = :userid OR is_global = 1";
        $params = ['userid' => $userid];
        return $DB->get_records_select('tool_promptshare', $sql, $params);
    }

    /**
     * Create a new prompt.
     *
     * @param int $userid The user ID
     * @param string $name The prompt name
     * @param string $template The prompt template with placeholders
     * @param int $is_global Whether the prompt is global (1) or private (0)
     * @return int The new prompt ID
     */
    public static function create_prompt($userid, $name, $template, $is_global = 0) {
        global $DB;
        
        $prompt = new \stdClass();
        $prompt->userid = $userid;
        $prompt->name = $name;
        $prompt->template = $template;
        $prompt->is_global = $is_global;
        $prompt->timecreated = time();
        $prompt->timemodified = time();
        
        return $DB->insert_record('tool_promptshare', $prompt);
    }

    /**
     * Update an existing prompt.
     *
     * @param int $id The prompt ID
     * @param string $name The prompt name
     * @param string $template The prompt template with placeholders
     * @param int $is_global Whether the prompt is global (1) or private (0)
     * @return bool Success
     */
    public static function update_prompt($id, $name, $template, $is_global = 0) {
        global $DB;
        
        $prompt = new \stdClass();
        $prompt->id = $id;
        $prompt->name = $name;
        $prompt->template = $template;
        $prompt->is_global = $is_global;
        $prompt->timemodified = time();
        
        return $DB->update_record('tool_promptshare', $prompt);
    }

    /**
     * Delete a prompt.
     *
     * @param int $id The prompt ID
     * @return bool Success
     */
    public static function delete_prompt($id) {
        global $DB;
        
        return $DB->delete_records('tool_promptshare', ['id' => $id]);
    }

    /**
     * Get a prompt by ID.
     *
     * @param int $id The prompt ID
     * @return object|null The prompt record or null
     */
    public static function get_prompt_by_id($id) {
        global $DB;
        
        return $DB->get_record('tool_promptshare', ['id' => $id]);
    }

    /**
     * Regex manipulation to find all {{placeholder}} keys
     * so the UI can generate input fields for them.
     */
    public static function get_placeholders($content) {
        preg_match_all('/\{\{(.*?)\}\}/', $content, $matches);
        return array_unique($matches[1]);
    }
}
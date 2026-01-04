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
 * English language pack for Promptshare
 *
 * @package    tool_promptshare
 * @category   string
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Promptshare';
$string['privacy:metadata'] = 'The Promptshare plugin stores prompts created by users.';
$string['privacy:metadata:tool_promptshare'] = 'Information about prompts created by users.';
$string['privacy:metadata:tool_promptshare:userid'] = 'The user who created the prompt.';
$string['privacy:metadata:tool_promptshare:name'] = 'The name of the prompt.';
$string['privacy:metadata:tool_promptshare:template'] = 'The prompt template content.';
$string['privacy:metadata:tool_promptshare:is_global'] = 'Whether the prompt is shared globally.';
$string['privacy:metadata:tool_promptshare:timecreated'] = 'When the prompt was created.';
$string['privacy:metadata:tool_promptshare:timemodified'] = 'When the prompt was last modified.';

// Form strings
$string['promptname'] = 'Prompt name';
$string['template'] = 'Prompt template';
$string['template_help'] = 'Enter your prompt template. Use {{placeholder}} syntax for variables that will be replaced in the UI.';
$string['shareglobally'] = 'Share this prompt globally with all users';

// Page strings
$string['manageprompts'] = 'Manage prompts';
$string['createprompt'] = 'Create new prompt';
$string['editprompt'] = 'Edit prompt';
$string['deleteprompt'] = 'Delete prompt';
$string['confirmdelete'] = 'Are you sure you want to delete this prompt?';
$string['promptcreated'] = 'Prompt created successfully';
$string['promptupdated'] = 'Prompt updated successfully';
$string['promptdeleted'] = 'Prompt deleted successfully';
$string['myprompts'] = 'My prompts';
$string['globalprompts'] = 'Global prompts';
$string['noprompts'] = 'No prompts found';
$string['placeholder'] = 'Placeholder';
$string['preview'] = 'Preview';

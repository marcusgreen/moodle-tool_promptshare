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
 * Edit prompt page for Promptshare tool
 *
 * @package    tool_promptshare
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Check permissions
require_login();
$context = \context_system::instance();
require_capability('moodle/site:config', $context);

// Get parameters
$action = required_param('action', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

// Set up page
admin_externalpage_setup('tool_promptshare_manage', '', null, '', array('pagelayout' => 'admin'));

// Set up page navigation
$PAGE->set_url(new moodle_url('/admin/tool/promptshare/edit.php', ['action' => $action, 'id' => $id]));
$PAGE->set_title(get_string('pluginname', 'tool_promptshare'));
$PAGE->set_heading(get_string('pluginname', 'tool_promptshare'));

// Load form
require_once(__DIR__ . '/classes/form/edit_prompt_form.php');

$mform = new \tool_promptshare\form\edit_prompt();

// Handle form submission
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/promptshare/index.php'));
} else if ($data = $mform->get_data()) {
    if ($action === 'create') {
        \tool_promptshare\manager::create_prompt(
            $USER->id,
            $data->name,
            $data->template,
            $data->is_global ? 1 : 0
        );
        \core\notification::success(get_string('promptcreated', 'tool_promptshare'));
    } else if ($action === 'edit' && $id) {
        \tool_promptshare\manager::update_prompt(
            $id,
            $data->name,
            $data->template,
            $data->is_global ? 1 : 0
        );
        \core\notification::success(get_string('promptupdated', 'tool_promptshare'));
    }
    redirect(new moodle_url('/admin/tool/promptshare/index.php'));
}

// Set up form for editing
if ($action === 'edit' && $id) {
    $prompt = \tool_promptshare\manager::get_prompt_by_id($id);
    if (!$prompt) {
        print_error('invalidpromptid', 'tool_promptshare');
    }
    $mform->set_data($prompt);
    $PAGE->navbar->add(get_string('editprompt', 'tool_promptshare'));
    $PAGE->set_title(get_string('editprompt', 'tool_promptshare'));
    $PAGE->set_heading(get_string('editprompt', 'tool_promptshare'));
} else {
    $PAGE->navbar->add(get_string('createprompt', 'tool_promptshare'));
    $PAGE->set_title(get_string('createprompt', 'tool_promptshare'));
    $PAGE->set_heading(get_string('createprompt', 'tool_promptshare'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($action === 'edit' ? get_string('editprompt', 'tool_promptshare') : get_string('createprompt', 'tool_promptshare'));

$mform->display();

echo $OUTPUT->footer();
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
 * Main admin page for Promptshare tool
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

// Set up page
admin_externalpage_setup('tool_promptshare_manage', '', null, '', array('pagelayout' => 'admin'));

// Get parameters
$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);

// Handle actions
if ($action === 'delete' && $id) {
    require_sesskey();
    \tool_promptshare\manager::delete_prompt($id);
    \core\notification::success(get_string('promptdeleted', 'tool_promptshare'));
    redirect(new moodle_url('/admin/tool/promptshare/index.php'));
}

// Set up page navigation
$PAGE->set_url(new moodle_url('/admin/tool/promptshare/index.php'));
$PAGE->set_title(get_string('pluginname', 'tool_promptshare'));
$PAGE->set_heading(get_string('pluginname', 'tool_promptshare'));
$PAGE->navbar->add(get_string('manageprompts', 'tool_promptshare'));

echo $OUTPUT->header();

// Display actions
echo $OUTPUT->heading(get_string('manageprompts', 'tool_promptshare'));

$createurl = new moodle_url('/admin/tool/promptshare/edit.php', ['action' => 'create']);
echo $OUTPUT->single_button($createurl, get_string('createprompt', 'tool_promptshare'), 'get');

// Display prompts
$prompts = \tool_promptshare\manager::get_available_prompts($USER->id);

if (empty($prompts)) {
    echo $OUTPUT->notification(get_string('noprompts', 'tool_promptshare'), 'info');
} else {
    // Separate user prompts from global prompts
    $userprompts = array_filter($prompts, function($prompt) {
        return $prompt->userid == $USER->id && !$prompt->is_global;
    });
    
    $globalprompts = array_filter($prompts, function($prompt) {
        return $prompt->is_global;
    });

    // Display user prompts
    if (!empty($userprompts)) {
        echo $OUTPUT->heading(get_string('myprompts', 'tool_promptshare'), 3);
        
        echo html_writer::start_tag('div', ['class' => 'table-responsive']);
        echo html_writer::start_tag('table', ['class' => 'table table-striped']);
        echo html_writer::start_tag('thead');
        echo html_writer::start_tag('tr');
        echo html_writer::tag('th', get_string('promptname', 'tool_promptshare'));
        echo html_writer::tag('th', get_string('template', 'tool_promptshare'));
        echo html_writer::tag('th', get_string('shareglobally', 'tool_promptshare'));
        echo html_writer::tag('th', get_string('actions', 'tool_promptshare'));
        echo html_writer::end_tag('tr');
        echo html_writer::end_tag('thead');
        echo html_writer::start_tag('tbody');
        
        foreach ($userprompts as $prompt) {
            echo html_writer::start_tag('tr');
            echo html_writer::tag('td', format_string($prompt->name));
            echo html_writer::tag('td', format_text($prompt->template, FORMAT_PLAIN));
            echo html_writer::tag('td', $prompt->is_global ? get_string('yes') : get_string('no'));
            echo html_writer::start_tag('td');
            
            // Edit link
            $editurl = new moodle_url('/admin/tool/promptshare/edit.php', ['action' => 'edit', 'id' => $prompt->id]);
            echo html_writer::link($editurl, get_string('edit'), ['class' => 'btn btn-sm btn-primary']);
            
            // Delete link
            $deleteurl = new moodle_url('/admin/tool/promptshare/index.php', [
                'action' => 'delete', 
                'id' => $prompt->id, 
                'sesskey' => sesskey()
            ]);
            echo ' ' . html_writer::link($deleteurl, get_string('delete'), [
                'class' => 'btn btn-sm btn-danger',
                'onclick' => 'return confirm(\'' . get_string('confirmdelete', 'tool_promptshare') . '\');'
            ]);
            
            echo html_writer::end_tag('td');
            echo html_writer::end_tag('tr');
        }
        
        echo html_writer::end_tag('tbody');
        echo html_writer::end_tag('table');
        echo html_writer::end_tag('div');
    }

    // Display global prompts
    if (!empty($globalprompts)) {
        echo $OUTPUT->heading(get_string('globalprompts', 'tool_promptshare'), 3);
        
        echo html_writer::start_tag('div', ['class' => 'table-responsive']);
        echo html_writer::start_tag('table', ['class' => 'table table-striped']);
        echo html_writer::start_tag('thead');
        echo html_writer::start_tag('tr');
        echo html_writer::tag('th', get_string('promptname', 'tool_promptshare'));
        echo html_writer::tag('th', get_string('template', 'tool_promptshare'));
        echo html_writer::tag('th', get_string('actions', 'tool_promptshare'));
        echo html_writer::end_tag('tr');
        echo html_writer::end_tag('thead');
        echo html_writer::start_tag('tbody');
        
        foreach ($globalprompts as $prompt) {
            echo html_writer::start_tag('tr');
            echo html_writer::tag('td', format_string($prompt->name));
            echo html_writer::tag('td', format_text($prompt->template, FORMAT_PLAIN));
            echo html_writer::start_tag('td');
            
            // Preview link
            $previewurl = new moodle_url('/admin/tool/promptshare/preview.php', ['id' => $prompt->id]);
            echo html_writer::link($previewurl, get_string('preview', 'tool_promptshare'), ['class' => 'btn btn-sm btn-info']);
            
            echo html_writer::end_tag('td');
            echo html_writer::end_tag('tr');
        }
        
        echo html_writer::end_tag('tbody');
        echo html_writer::end_tag('table');
        echo html_writer::end_tag('div');
    }
}

echo $OUTPUT->footer();
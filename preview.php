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
 * Preview prompt page for Promptshare tool
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
$id = required_param('id', PARAM_INT);

// Set up page
admin_externalpage_setup('tool_promptshare_manage', '', null, '', array('pagelayout' => 'admin'));

// Get prompt
$prompt = \tool_promptshare\manager::get_prompt_by_id($id);
if (!$prompt) {
    print_error('invalidpromptid', 'tool_promptshare');
}

// Get placeholders
$placeholders = \tool_promptshare\manager::get_placeholders($prompt->template);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $template = $prompt->template;
    foreach ($placeholders as $placeholder) {
        $value = optional_param($placeholder, '', PARAM_TEXT);
        $template = str_replace('{{' . $placeholder . '}}', $value, $template);
    }
    $rendered = format_text($template, FORMAT_PLAIN);
} else {
    $rendered = '';
}

// Set up page navigation
$PAGE->set_url(new moodle_url('/admin/tool/promptshare/preview.php', ['id' => $id]));
$PAGE->set_title(get_string('pluginname', 'tool_promptshare'));
$PAGE->set_heading(get_string('pluginname', 'tool_promptshare'));
$PAGE->navbar->add(get_string('preview', 'tool_promptshare'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('preview', 'tool_promptshare') . ': ' . format_string($prompt->name));

// Display prompt info
echo $OUTPUT->box_start('generalbox');
echo html_writer::tag('h4', get_string('promptname', 'tool_promptshare') . ':');
echo html_writer::tag('p', format_string($prompt->name));

echo html_writer::tag('h4', get_string('template', 'tool_promptshare') . ':');
echo html_writer::tag('p', format_text($prompt->template, FORMAT_PLAIN));

echo html_writer::tag('h4', get_string('placeholder', 'tool_promptshare') . '(s):');
if (empty($placeholders)) {
    echo html_writer::tag('p', 'None');
} else {
    echo html_writer::start_tag('ul');
    foreach ($placeholders as $placeholder) {
        echo html_writer::tag('li', $placeholder);
    }
    echo html_writer::end_tag('ul');
}
echo $OUTPUT->box_end();

// Display form for variables
if (!empty($placeholders)) {
    echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url]);
    echo $OUTPUT->box_start('generalbox');
    
    foreach ($placeholders as $placeholder) {
        echo html_writer::tag('label', $placeholder);
        echo html_writer::empty_tag('br');
        echo html_writer::empty_tag('input', [
            'type' => 'text',
            'name' => $placeholder,
            'class' => 'form-control',
            'placeholder' => $placeholder
        ]);
        echo html_writer::empty_tag('br');
        echo html_writer::empty_tag('br');
    }
    
    echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('preview', 'tool_promptshare'), 'class' => 'btn btn-primary']);
    echo $OUTPUT->box_end();
    echo html_writer::end_tag('form');
}

// Display rendered result
if (!empty($rendered)) {
    echo $OUTPUT->box_start('generalbox');
    echo html_writer::tag('h4', 'Rendered Prompt:');
    echo html_writer::tag('div', $rendered, ['class' => 'alert alert-info']);
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();
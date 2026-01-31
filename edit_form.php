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
 *
 * Edit themes form
 *
 * This does the same as the standard xml import but easier
 * @package    tool_promptshare
 * @copyright  2023 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../..//../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once("$CFG->dirroot/cohort/lib.php");


$page   = optional_param('page', 0, PARAM_INT);
$newrecord = optional_param('newrecord', '', PARAM_TEXT);
$save = optional_param('save', '', PARAM_TEXT);
$delete = optional_param('delete', '', PARAM_TEXT);


$context = context_system::instance();
$PAGE->set_context($context);
admin_externalpage_setup('tool_edit_form');

/**
 *  Edit tool_promptshare code
 * @package tool_promptshare
 * @copyright Marcus Green 2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * Form for editing tweak (css and javascript)
 */
class tool_edit_form_form extends moodleform {
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pagetypes = [];

    /**
     * Interface elements of the editing form.
     */
    protected function definition() {


        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $navbuttons = [];
        $navbuttons[] = $mform->createElement('submit', 'save', get_string('save'));
        $navbuttons[] = $mform->createElement('submit', 'cancel', get_string('cancel'));
        $navbuttons[] = $mform->createElement('submit', 'newrecord', get_string('new'));
        $navbuttons[] = $mform->createElement('submit', 'delete', get_string('delete'));

        $mform->addGroup($navbuttons);

        $mform->addElement('text', 'promptname', get_string('name'));
        $mform->setType('promptname', PARAM_TEXT);
        $mform->addHelpButton('promptname', 'promptname', 'tool_promptshare');

        $options['multiple'] = true;
        $options['tags'] = true;

        $mform->addElement('textarea', 'prompttext', get_string('prompttext', 'tool_promptshare'),
         ['rows' => 15, 'cols' => 80]);
       // $mform->addHelpButton('promptshare', 'promptshare', 'tool_promptshare');
        $mform->setType('promptshare', PARAM_RAW);

    }

}
$recordcount = $DB->count_records('tool_promptshare');


$record = get_page_record($page);

if ($delete) {
    $DB->delete_records('tool_promptshare', ['id' => $record->id]);
    $page--;
    $record = get_page_record($page);
    $recordcount = $DB->count_records('tool_promptshare');
    if ($recordcount == 0) {
        $id = $DB->insert_record('tool_promptshare', (object) ['tweakname' => '', 'cohort' => '', 'css' => '']);
        $record = $DB->get_record('tool_promptshare', ['id' => $id]);
        $recordcount = 1;
    }
}
$baseurl = new moodle_url('/admin/tool/tweak/edit_form.php', ['page' => $page]);

$record->page = $page;

$mform = new tool_edit_form_form($baseurl);

if ($data = $mform->get_data()) {
    if (isset($data->save)) {
            $params = [
                'id' => $data->id,
                'promptname' => $data->promptname,
                'prompttext' => $data->prompttext,

            ];
            $DB->update_record('tool_promptshare', $params);
            update_pagetypes($data);
            $record = $DB->get_record('tool_promptshare', ['id' => $data->id]);
    }
    if (isset($data->upload)) {
        $upload = true;
    }
    if (isset($data->export)) {
        do_download($data->id);
    }
    if (isset($data->exportall)) {
        do_download();
    }
}



echo $OUTPUT->header();

echo $OUTPUT->paging_bar($recordcount, $page, 1, $baseurl);
$mform->display();
echo $OUTPUT->footer();

/**
 * Get the database record to match the current page
 * @package tool_promptshare
 * @param int $page
 * @return \stdClass
 */
function get_page_record(int $page) : \stdClass {
    global $DB;
    $record = (object) [];
    $recordset = $DB->get_recordset('tool_promptshare');
    $count = 0;
    foreach ($recordset as $key => $value) {
        if ($count == $page) {
            $record = $value;
            break;
        }
        $count++;
    }
    return $record;
}


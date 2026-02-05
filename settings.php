<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Set default values for a new install
 *
 * @package     tool_promptshare
 * @category    admin
 * @copyright   2026 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (is_siteadmin()) {
    $ADMIN->add('tools', new admin_category('promptshare', get_string('pluginname', 'tool_promptshare')));
    $settingspage = new admin_settingpage('promptsettings', get_string('promptsettings', 'tool_promptshare'));

    $managepage = new admin_externalpage('tool_promptshare_manage',
        get_string('editprompts', 'tool_promptshare'),
        new moodle_url('/admin/tool/promptshare/edit_form.php'),
    );

    $ADMIN->add('promptshare', $managepage);
    $ADMIN->add('promptshare', $settingspage);
}

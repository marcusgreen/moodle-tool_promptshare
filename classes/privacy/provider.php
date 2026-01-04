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
 * Privacy API implementation for tool_promptshare.
 *
 * @package    tool_promptshare
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_promptshare\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider for tool_promptshare.
 *
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'tool_promptshare',
            [
                'userid' => 'privacy:metadata:tool_promptshare:userid',
                'name' => 'privacy:metadata:tool_promptshare:name',
                'template' => 'privacy:metadata:tool_promptshare:template',
                'is_global' => 'privacy:metadata:tool_promptshare:is_global',
                'timecreated' => 'privacy:metadata:tool_promptshare:timecreated',
                'timemodified' => 'privacy:metadata:tool_promptshare:timemodified',
            ],
            'privacy:metadata:tool_promptshare'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // Only system context is used for this plugin.
        $contextlist->add_system_context();

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of all users who have data in this context.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_system) {
            return;
        }

        $sql = "SELECT userid
                  FROM {tool_promptshare}
                 WHERE userid IS NOT NULL";

        $userlist->add_from_sql('userid', $sql, []);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        $sql = "SELECT *
                  FROM {tool_promptshare}
                 WHERE userid = :userid";

        $records = $DB->get_records_sql($sql, ['userid' => $user->id]);

        foreach ($records as $record) {
            $contextdata = \core_privacy\local\request\transform::obfuscate_object($record, ['id', 'timecreated', 'timemodified']);
            
            writer::with_context(\context_system::instance())
                ->export_data(
                    [get_string('pluginname', 'tool_promptshare'), $record->name],
                    $contextdata
                );
                
            writer::with_context(\context_system::instance())
                ->export_metadata(
                    [get_string('pluginname', 'tool_promptshare'), $record->name],
                    'tool_promptshare',
                    $record,
                    get_string('privacy:metadata:tool_promptshare', 'tool_promptshare')
                );
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_system) {
            return;
        }

        $DB->delete_records('tool_promptshare', []);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        $DB->delete_records('tool_promptshare', ['userid' => $user->id]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_system) {
            return;
        }

        $userids = $userlist->get_userids();

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $sql = "SELECT id
                  FROM {tool_promptshare}
                 WHERE userid {$usersql}";

        $records = $DB->get_records_sql($sql, $userparams);
        if (!empty($records)) {
            list($recordsql, $recordparams) = $DB->get_in_or_equal(array_keys($records), SQL_PARAMS_NAMED);
            $DB->delete_records_select('tool_promptshare', "id {$recordsql}", $recordparams);
        }
    }
}
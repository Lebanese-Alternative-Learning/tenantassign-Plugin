<?php
// This file is part of Moodle - https://moodle.org/
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * Code to be executed after the plugin's database schema has been installed is defined here.
 *
 * @package     local_tenantassign
 * @category    upgrade
 * @copyright   2025 Lebanese Alternative Learning
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Custom code to be run on installing the plugin.
 */
function xmldb_local_tenantassign_install() {
    global $DB;

    // Define table structure
    $table = new xmldb_table('local_tenantassign_rules');

    // Add fields to the table
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
    $table->add_field('domain', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
    $table->add_field('tenantid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);

    // Define primary key
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Check if the table exists, and create it if not
    if (!$DB->get_manager()->table_exists($table)) {
        $DB->get_manager()->create_table($table);
    }

    return true;
}

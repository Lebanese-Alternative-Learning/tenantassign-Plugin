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
 * Plugin administration pages are defined here.
 *
 * @package     local_tenantassign
 * @category    admin
 * @copyright   2025 Lebanese Alternative Learning
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Create a settings page for the plugin.
    $settings = new admin_settingpage('local_tenantassign_settings', new lang_string('pluginname', 'local_tenantassign'));

    // Add the settings page to the admin tree.
    $ADMIN->add('localplugins', $settings);

    // Check if the full admin tree is being displayed.
    if ($ADMIN->fulltree) {
        // Add a link to the rules management page.
        $settings->add(new admin_setting_heading(
            'local_tenantassign/rules',
            get_string('rules', 'local_tenantassign'),
            html_writer::link(
                new moodle_url('/local/tenantassign/rules.php'),
                get_string('managerules', 'local_tenantassign')
            )
        ));
    }

    // Add the external page for managing rules.
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_tenantassign_rules', // External page name.
        get_string('managerules', 'local_tenantassign'), // Page title.
        new moodle_url('/local/tenantassign/rules.php') // URL to the rules page.
    ));
}
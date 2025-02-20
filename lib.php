<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Hook to assign a user to a tenant based on their email domain.
 */
function local_tenantassign_after_user_created($event) {
    // Log the event trigger to check if it's being called
    error_log('User created: ' . $event->userid);  // Logs the user ID for verification
    global $DB;

    // Extract the domain from the user's email.
    $user = $event->get_record_snapshot('user', $event->userid);  // Fetch user record
    $emailparts = explode('@', $user->email);
    $domain = trim($emailparts[1]);

    // Find the matching tenant for the domain.
    $rule = $DB->get_record('local_tenantassign_rules', ['domain' => $domain]);
    if ($rule) {
        try {
            $DB->set_field('user', 'tenantid', $rule->tenantid, ['id' => $user->id]);
            error_log('Assigning tenant ID: ' . $rule->tenantid . ' to user: ' . $user->email);
        } catch (Exception $e) {
            // Log the error.
            error_log("Failed to assign user to tenant: " . $e->getMessage());
        }
    } else {
        error_log('No rule found for domain: ' . $domain);
    }
}

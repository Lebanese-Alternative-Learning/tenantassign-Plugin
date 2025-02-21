<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Hook to assign a user to a tenant based on their email domain.
 */
function local_tenantassign_after_user_created($event) {
    global $DB;

    // Fetch user record using the event's userid
    $user = $event->get_record_snapshot('user', $event->userid);
    if ($user) {
        // Split email to extract domain
        $emailparts = explode('@', $user->email);
        if (count($emailparts) > 1) {
            $domain = trim($emailparts[1]);

            // Find the matching tenant for the domain
            $rule = $DB->get_record('local_tenantassign_rules', ['domain' => $domain]);
            if ($rule) {
                try {
                    // Assign the tenant ID to the user
                    $DB->set_field('user', 'tenantid', $rule->tenantid, ['id' => $user->id]);
                    error_log('Assigning tenant ID: ' . $rule->tenantid . ' to user: ' . $user->email);
                } catch (Exception $e) {
                    // Log the error
                    error_log("Failed to assign user to tenant: " . $e->getMessage());
                }
            } else {
                // Log that no rule was found
                error_log('No rule found for domain: ' . $domain);
            }
        } else {
            // Log if email format is invalid
            error_log('Invalid email format for user ID: ' . $user->id);
        }
    } else {
        // Log if user record was not found
        error_log('User record not found for user ID: ' . $event->userid);
    }
}

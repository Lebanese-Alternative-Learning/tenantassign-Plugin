<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Hook to assign a user to a tenant based on their email domain.
 */
function local_tenantassign_after_user_created($user) {
    global $DB;

    // Extract the domain from the user's email.
    $emailparts = explode('@', $user->email);
    $domain = trim($emailparts[1]);

    // Find the matching tenant for the domain.
    $rule = $DB->get_record('local_tenantassign_rules', ['domain' => $domain]);
    if ($rule) {
        try {
            $DB->set_field('user', 'tenantid', $rule->tenantid, ['id' => $user->id]);
        } catch (Exception $e) {
            // Log the error.
            error_log("Failed to assign user to tenant: " . $e->getMessage());
        }
    }
}

// Register the hook.
$observers = [
    [
        'eventname' => '\core\event\user_created',
        'callback' => 'local_tenantassign_after_user_created',
    ],
];
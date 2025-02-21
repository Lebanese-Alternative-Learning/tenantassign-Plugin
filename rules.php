<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');
use local_tenantassign\form\rules_form; // Use the correct namespace

// Set up the external admin page.
admin_externalpage_setup('local_tenantassign_rules'); // Ensure this matches the external page name in settings.php.

$context = context_system::instance();
require_capability('local/tenantassign:manage', $context);

$PAGE->set_url(new moodle_url('/local/tenantassign/rules.php'));
$PAGE->set_title(get_string('managerules', 'local_tenantassign'));
$PAGE->set_heading(get_string('managerules', 'local_tenantassign'));

echo $OUTPUT->header();

// Handle form submissions.
$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

if ($action === 'delete' && $id) {
    try {
        // Delete a rule.
        $DB->delete_records('local_tenantassign_rules', ['id' => $id]);
        error_log('Deleted rule with ID: ' . $id); // Log rule deletion
        redirect($PAGE->url, get_string('ruledeleted', 'local_tenantassign'));
    } catch (Exception $e) {
        // Log any error during deletion
        error_log("Failed to delete rule ID {$id}: " . $e->getMessage());
    }
}

// Display the form to add/edit rules.
$mform = new rules_form();

if ($mform->is_cancelled()) {
    error_log('Rule form cancelled'); // Log form cancellation
    redirect($PAGE->url);
} elseif ($data = $mform->get_data()) {
    try {
        // Save or update a rule.
        $rule = new stdClass();
        $rule->id = $data->id ?? 0;
        $rule->domain = $data->domain;
        $rule->tenantid = $data->tenantid;

        if ($rule->id) {
            $DB->update_record('local_tenantassign_rules', $rule);
            error_log('Updated rule with ID: ' . $rule->id); // Log rule update
        } else {
            $DB->insert_record('local_tenantassign_rules', $rule);
            error_log('Created new rule with domain: ' . $rule->domain); // Log rule creation
        }
        redirect($PAGE->url, get_string('rulesaved', 'local_tenantassign'));
    } catch (Exception $e) {
        // Log any error during save or update
        error_log("Failed to save/update rule: " . $e->getMessage());
    }
}

// Display the list of rules.
try {
    $rules = $DB->get_records('local_tenantassign_rules');
    $table = new html_table();
    $table->head = [get_string('domain', 'local_tenantassign'), get_string('tenantid', 'local_tenantassign'), get_string('actions', 'local_tenantassign')];
    foreach ($rules as $rule) {
        $editurl = new moodle_url($PAGE->url, ['action' => 'edit', 'id' => $rule->id]);
        $deleteurl = new moodle_url($PAGE->url, ['action' => 'delete', 'id' => $rule->id]);
        $actions = html_writer::link($editurl, get_string('edit')) . ' ' .
                   html_writer::link($deleteurl, get_string('delete'));
        $table->data[] = [$rule->domain, $rule->tenantid, $actions];
    }
    echo html_writer::table($table);
    error_log('Displayed rule list with ' . count($rules) . ' rules'); // Log rule display
} catch (Exception $e) {
    // Log any error during the fetching or display of rules
    error_log("Failed to fetch/display rules: " . $e->getMessage());
}

// Display the form.
$mform->display();

echo $OUTPUT->footer();

<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');

admin_externalpage_setup('local_tenantassign_rules');

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
    // Delete a rule.
    $DB->delete_records('local_tenantassign_rules', ['id' => $id]);
    redirect($PAGE->url, get_string('ruledeleted', 'local_tenantassign'));
}

// Display the form to add/edit rules.
$mform = new local_tenantassign_rules_form();

if ($mform->is_cancelled()) {
    redirect($PAGE->url);
} elseif ($data = $mform->get_data()) {
    // Save or update a rule.
    $rule = new stdClass();
    $rule->id = $data->id ?? 0;
    $rule->domain = $data->domain;
    $rule->tenantid = $data->tenantid;

    if ($rule->id) {
        $DB->update_record('local_tenantassign_rules', $rule);
    } else {
        $DB->insert_record('local_tenantassign_rules', $rule);
    }
    redirect($PAGE->url, get_string('rulesaved', 'local_tenantassign'));
}

// Display the list of rules.
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

// Display the form.
$mform->display();

echo $OUTPUT->footer();
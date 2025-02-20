<?php
namespace local_tenantassign\form;  // Use the correct namespace

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class rules_form extends \moodleform {
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'domain', get_string('domain', 'local_tenantassign'));
        $mform->setType('domain', PARAM_TEXT);
        $mform->addRule('domain', get_string('required'), 'required');

        $mform->addElement('text', 'tenantid', get_string('tenantid', 'local_tenantassign'));
        $mform->setType('tenantid', PARAM_INT);
        $mform->addRule('tenantid', get_string('required'), 'required');

        $this->add_action_buttons();
    }
}

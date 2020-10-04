<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(__DIR__ . '/../../config.php');

require_once(__DIR__ . '/lib.php');

global $PAGE;

$config = get_config('local_bbzcal');
$propname = $config->propertyname;

require_login();

$user_id = required_param('id', PARAM_ALPHANUMEXT);
$PAGE->set_url('/local/bbzcal/view.php?id='.$user_id);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

$title = 'BBZ Kalender';

$PAGE->set_heading($title);
$PAGE->navbar->add($title);

echo $OUTPUT->header();
echo "CAL";
echo $OUTPUT->footer();

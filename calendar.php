<?php

require(__DIR__ . '/../../config.php');

global $PAGE, $DB, $CFG;

date_default_timezone_set("UTC");

$events = [];
$course_id = optional_param('courseid', null, PARAM_INT);

$renderer = new local_bbzcal\renderer($OUTPUT, 'global', null);
$title = get_string('global_nav_item', 'local_bbzcal');

if($course_id != null) {
  // course context
  $title = get_string('course_nav_item', 'local_bbzcal');
  $course = $DB->get_record('course', array('id' => $course_id));
  require_login($course);
  $PAGE->set_context(\context_course::instance($course_id));
  $PAGE->set_pagelayout('incourse');
  $PAGE->set_course($course);
  $renderer = new local_bbzcal\renderer($OUTPUT, 'course', $course_id);
  // $events = local_bbzcal\event::get_course_events($DB, $course_id);
  $events = local_bbzcal\event::get_user_events($DB, $USER->id);
} else {
  // global context
  require_login();
  $PAGE->set_context(\context_system::instance());
  $PAGE->set_pagelayout('admin');
  $events = local_bbzcal\event::get_user_events($DB, $USER->id);
}

$usr = new local_bbzcal\user($USER->id);
$admin_course_ids = $usr->get_admin_course_ids($DB);

$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_url('/local/bbzcal/calendar.php');

$PAGE->navbar->add($title);

$renderer->header();
$renderer->calendar($events, $admin_course_ids);
$renderer->modal();
$renderer->js();
$renderer->footer();

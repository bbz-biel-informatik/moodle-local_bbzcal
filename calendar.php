<?php

require(__DIR__ . '/../../config.php');

global $PAGE, $DB, $CFG;

date_default_timezone_set("UTC");

$events = [];
$course_id = optional_param('courseid', null, PARAM_INT);
$date = optional_param('date', null, PARAM_TEXT);
if(!$date) {
  $today = new \DateTime();
  $today->setTimezone(new \DateTimeZone('UTC'));
  $today->setTime(0, 0, 0);
  $date = $today->format('Y-m-d');
}

$labels = ['-'];
$renderer = new local_bbzcal\renderer($OUTPUT, 'global', null, $date, implode(', ', $labels));
$title = get_string('global_nav_item', 'local_bbzcal');

// course context
$title = get_string('course_nav_item', 'local_bbzcal');
$course = $DB->get_record('course', array('id' => $course_id));
require_login($course);
$PAGE->set_context(\context_course::instance($course_id));
$PAGE->set_pagelayout('incourse');
$PAGE->set_course($course);

$u = new local_bbzcal\user($USER->id);
$c = new local_bbzcal\course($course_id);

if($u->is_teacher($DB)) {
  // get the course labels (i.e. classes of all participants)
  // get all events of courses with this label (i.e. all events of all participants)
  $labels = $c->get_labels($DB);
  $courses = $c->ids_from_labels($DB, $labels);
  $events = local_bbzcal\event::get_courses_events($DB, $courses);
} else {
  // get the user labels (i.e. classes of this participant)
  // get all events of courses with this label (i.e. all events of this user)
  $labels = $u->get_labels();
  $courses = $c->ids_from_labels($DB, $labels);
  $events = local_bbzcal\event::get_courses_events($DB, $courses);
}

$renderer = new local_bbzcal\renderer($OUTPUT, 'course', $course_id, $date, implode(', ', $labels));

$usr = new local_bbzcal\user($USER->id);
$admin_course_ids = $usr->get_teacher_course_ids($DB);

$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_url('/local/bbzcal/calendar.php');

$PAGE->navbar->add($title);

$renderer->header();
$renderer->calendar($events, $admin_course_ids);
$renderer->modal();
$renderer->js();
$renderer->footer();

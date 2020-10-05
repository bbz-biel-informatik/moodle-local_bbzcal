<?php

require(__DIR__ . '/../../config.php');

global $PAGE, $DB, $CFG;

date_default_timezone_set("UTC");

$events = [];
$course_id = optional_param('courseid', null, PARAM_INT); {}
if($course_id != null) {
  $course = get_course($course_id);
  require_login($course_id);
  $events = local_bbzcal\event::get_course_events($DB, $course_id);
} else {
  require_login();
  $events = local_bbzcal\event::get_user_events($DB, $USER->id);
}

$renderer = new local_bbzcal\renderer($CFG, $DB, $PAGE, $OUTPUT, 'course');

echo $renderer->header();

echo $renderer->calendar($events);

echo $renderer->modal();

echo $renderer->js();

echo $renderer->footer();

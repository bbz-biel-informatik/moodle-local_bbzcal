<?php

defined('MOODLE_INTERNAL') || die();

function local_bbzcal_extend_navigation_course(navigation_node $navigation) {
  global $PAGE;

  $course_id = $PAGE->course->id;

  $course_calendar_item = navigation_node::create(
    get_string('course_nav_item', 'local_bbzcal'),
    new moodle_url('/local/bbzcal/calendar.php?courseid=' . $course_id),
    null,
    'bbzcal',
    'bbzcal',
    new pix_icon('i/calendar', '')
  );
  // https://moodle.org/mod/forum/discuss.php?d=445503
  $navigation->add_node($course_calendar_item, 'grades');
}

<?php

defined('MOODLE_INTERNAL') || die();

function local_bbzcal_extend_navigation(global_navigation $navigation) {
  global $USER, $PAGE;

  $url = new moodle_url('/local/bbzcal/calendar.php');
  $global_calendar_item = navigation_node::create(
    get_string('global_nav_item', 'local_bbzcal'),
    $url,
    navigation_node::NODETYPE_LEAF,
    'bbzglobalcal',
    'bbzglobalcal',
    new pix_icon('i/calendar', 'bbzglobalcal')
  );

  $global_calendar_item->showinflatnavigation = true;

  // insert before privatefiles
  $navigation->add_node($global_calendar_item, 'currentcourse');

  /*if ($calendar = $navigation->find('calendar', global_navigation::TYPE_CUSTOM)) {
    $calendar->remove();
  }*/

  $course_id = $PAGE->course->id;

  $course_nav = $navigation->find($course_id, navigation_node::TYPE_COURSE);
  if($course_id != '1') {
    $course_calendar_item = navigation_node::create(
      get_string('course_nav_item', 'local_bbzcal'),
      new moodle_url('/local/bbzcal/calendar.php?courseid=' . $course_id),
      navigation_node::NODETYPE_LEAF,
      'bbzcoursecal',
      'bbzcoursecal',
      new pix_icon('i/calendar', 'bbzcoursecal')
    );
    $course_nav->add_node($course_calendar_item, 'participants');
  }
}

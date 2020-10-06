<?php

defined('MOODLE_INTERNAL') || die();

function local_bbzcal_extend_navigation(global_navigation $navigation) {
  global $PAGE;

  /*if ($calendar = $navigation->find('calendar', global_navigation::TYPE_CUSTOM)) {
    $calendar->remove();
  }*/

  $course_id = $PAGE->course->id;

  if($course_id != '1') {
    $course_nav = $navigation->find($course_id, navigation_node::TYPE_COURSE);
    $course_calendar_item = navigation_node::create(
      get_string('course_nav_item', 'local_bbzcal'),
      new moodle_url('/local/bbzcal/calendar.php?courseid=' . $course_id),
      navigation_node::TYPE_SETTING,
      'bbzcoursecal',
      'bbzcoursecal',
      new pix_icon('i/calendar', 'bbzcoursecal')
    );
    $course_nav->add_node($course_calendar_item, 'participants');
  } else {
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
    $navigation->add_node($global_calendar_item, 'currentcourse');
  }
}

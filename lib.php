<?php

defined('MOODLE_INTERNAL') || die();

function local_bbzcal_extend_navigation(global_navigation $navigation) {
  global $USER;

  $url = new moodle_url('/local/bbzcal/view.php', array('id' => $USER->id));
  $navigation_node = navigation_node::create(
    'BBZ Kalender',
    $url,
    navigation_node::NODETYPE_LEAF,
    'bbzcal',
    'bbzcal',
    new pix_icon('i/calendar', 'bbzcal')
  );

  $navigation_node->showinflatnavigation = true;

  // insert before privatefiles
  $navigation->add_node($navigation_node, 'currentcourse');

  /*if ($calendar = $navigation->find('calendar', global_navigation::TYPE_CUSTOM)) {
    $calendar->remove();
  }*/
}

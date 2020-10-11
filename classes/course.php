<?php

namespace local_bbzcal;

require_once($CFG->dirroot.'/user/profile/lib.php');

defined('MOODLE_INTERNAL') || die();

class course {
  /**
   * Get courses for labels
   */
  public static function get_courses($DB, $labels) {
    echo "Displaying for course " . $course_id;
    return $DB->get_records('local_bbzcal', ['course_id' => $course_id]);
  }
}

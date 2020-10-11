<?php

namespace local_bbzcal;

require_once($CFG->dirroot.'/user/profile/lib.php');

defined('MOODLE_INTERNAL') || die();

class event {
  /**
   * Get all events for a specific course ID
   */
  public static function get_course_events($DB, $course_id) {
    echo "Displaying for course " . $course_id;
    return $DB->get_records('local_bbzcal', ['course_id' => $course_id]);
  }

  /**
   * Get all events for the courses of a specific user
   */
  public static function get_user_events($DB, $user_id) {
    $user = new user($user_id);
    $admin_course_ids = $user->get_teacher_course_ids($DB);
    $student_course_ids = $user->get_student_course_ids($DB);
    if(count($admin_course_ids) > 0) {
      // this is a teacher
      $course_ids = $admin_course_ids;
    } else {
      $course_ids = $student_course_ids;
    }

    $events_sql = "SELECT *
                     FROM {local_bbzcal}
                    WHERE course_id ";
    list($in_sql, $params) = $DB->get_in_or_equal($course_ids, SQL_PARAMS_NAMED);
    return $DB->get_records_sql($events_sql . $in_sql, $params);
  }
}

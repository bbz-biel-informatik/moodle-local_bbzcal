<?php

namespace local_bbzcal;

require_once($CFG->dirroot.'/user/profile/lib.php');

defined('MOODLE_INTERNAL') || die();

class event {
  public static function get_course_events($DB, $course_id) {
    echo "Displaying for course " . $course_id;
    return $DB->get_records('local_bbzcal', ['course_id' => $course_id]);
  }

  public static function get_user_events($DB, $user_id) {
    $user = new user($user_id);
    $klasses = explode(", ", $user->get_property_value());
    $admin_course_ids = $user->get_admin_course_ids($DB);
    if(count($admin_course_ids) > 0) {
      // this is a teacher
      $course_ids = $admin_course_ids;
    } else {
      $courses_sql = "SELECT course.id, customfield_data.value
                        FROM {course} course
                  INNER JOIN {customfield_data} customfield_data
                          ON customfield_data.instanceid = course.id;";
      $all_courses = $DB->get_records_sql($courses_sql);

      $courses = array_filter($all_courses, function ($c) use ($klasses) {
        $parts = explode(", ", $c->value);
        $intersect = array_intersect($klasses, $parts);
        return count($intersect) > 0;
      });

      $course_ids = array_map(function ($c) {
        return $c->id;
      }, $courses);
    }

    $events_sql = "SELECT *
                     FROM {local_bbzcal}
                    WHERE course_id ";
    list($in_sql, $params) = $DB->get_in_or_equal($course_ids, SQL_PARAMS_NAMED);
    return $DB->get_records_sql($events_sql . $in_sql, $params);
  }
}

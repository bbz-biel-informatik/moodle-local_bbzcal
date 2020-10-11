<?php

namespace local_bbzcal;

require_once($CFG->dirroot.'/user/profile/lib.php');

defined('MOODLE_INTERNAL') || die();

class course {
  private $course_id;

  public function __construct($course_id) {
    $this->course_id = $course_id;
  }

  public function get_labels($DB) {
    $sql = "SELECT c.id AS courseid, cd.value as labels
            FROM {customfield_data} cd
            INNER JOIN {course} c ON cd.instanceid = c.id
            inner join {customfield_field} cf ON cf.id = cd.fieldid
            WHERE c.id = :courseid AND cf.shortname = 'canonicalclassnames';";
    $course = $DB->get_record_sql($sql, array('courseid' => $this->course_id), '*', MUST_EXIST);
    $labels = $course->labels;
    return explode(', ', $labels);
  }

  /**
   * Get courses for labels
   */
  public static function ids_from_labels($DB, $labels) {
    $courses = course::all($DB);
    $filtered = array_filter($courses, function ($c) use ($labels) {
      $c_labels = explode(', ', $c->labels);
      return count(array_intersect($labels, $c_labels)) > 0;
    });
    return array_map(function ($c) {
      return $c->courseid;
    }, $filtered);
  }

  /**
   * Load all courses and their labels
   */
  public static function all($DB) {
    $sql = "SELECT c.id AS courseid, cd.value as labels
            FROM {customfield_data} cd
            INNER JOIN {course} c ON cd.instanceid = c.id
            inner join {customfield_field} cf ON cf.id = cd.fieldid
            WHERE cf.shortname = 'canonicalclassnames';";
    return $DB->get_records_sql($sql);
  }
}

<?php

namespace local_bbzcal;

require_once($CFG->dirroot.'/user/profile/lib.php');

defined('MOODLE_INTERNAL') || die();

class user {
  private $user_id;

  public function __construct($user_id) {
    $this->user_id = $user_id;
  }

  public function is_teacher($DB) {
    return count($this->get_teacher_course_ids($DB)) > 0;
  }

  /**
   * Get the course IDs where the user is teacher
   */
  public function get_teacher_course_ids($DB) {
    return $this->get_user_course_ids($DB, 'editingteacher');
  }

  /**
   * Get the course IDs where the user is student
   */
  public function get_student_course_ids($DB) {
    return $this->get_user_course_ids($DB, 'student');
  }

  /**
   * Get the course IDs where the user has a specific role
   */
  public function get_user_course_ids($DB, $role) {
    $sql = "SELECT c.instanceid AS courseid
            FROM {context} c
            INNER JOIN {role_assignments} ra ON c.id = ra.contextid
            INNER JOIN {role} r ON r.id = ra.roleid
            WHERE r.shortname = :role AND ra.userid = :userid;";
    $courses = $DB->get_records_sql($sql, array('role' => $role, 'userid' => $this->user_id));
    return array_map(function ($c) {
      return $c->courseid;
    }, $courses);
  }
}

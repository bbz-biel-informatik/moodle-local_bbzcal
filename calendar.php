<?php

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->libdir.'/enrollib.php');

global $PAGE, $DB, $CFG;

date_default_timezone_set("UTC");

$events = [];
$course_id = optional_param('courseid', null, PARAM_INT);
$date = optional_param('date', null, PARAM_TEXT);
if(!$date) {
  $today = new \DateTime();
  $today->setTimezone(new \DateTimeZone('UTC'));
  $today->setTime(0, 0, 0);
  $date = $today->format('Y-m-d');
}

// course context
$title = get_string('course_nav_item', 'local_bbzcal');
$course = $DB->get_record('course', array('id' => $course_id));
require_login($course);
$PAGE->set_context(\context_course::instance($course_id));
$PAGE->set_pagelayout('incourse');
$PAGE->set_course($course);

$u = new local_bbzcal\user($USER->id);

// Get the semester identifer (e.g. 24H) of the given course
function get_course_semester(int $course_id): string {
  global $DB;

  $course = get_course($course_id);
  $shortname = $course->shortname;
  $pos = strpos($shortname, ' - ');
  return substr($shortname, 0, $pos);
}

// Get all students of the given course
function get_course_students(int $course_id): array {
  global $DB;

  $course_context = context_course::instance($course_id);
  $user_list = get_enrolled_users($course_context, null, null, 'u.id');

  $student_ids = [];
  foreach($user_list as &$user) {
    if (user_has_role_assignment($user->id, 5, $course_context->id)) {
      $student_ids[] = $user->id;
    }
  }
  return $student_ids;
}

// Get all the courses the given students are enrolled
function get_students_courses(array $student_ids, string $semester): array {
  global $DB;

  error_log('$get_students_courses: '. print_r(array_map('intval', $student_ids), true));
  $student_ids_string = implode(',', array_map('intval', $student_ids));

  $sql = "SELECT DISTINCT c.id
          FROM {course} c
          JOIN {enrol} e ON e.courseid = c.id
          JOIN {user_enrolments} ue ON ue.enrolid = e.id
          WHERE ue.userid IN ($student_ids_string)
          AND c.shortname LIKE :prefix";

  $params = array('prefix' => $semester . '%');

  $course_records = $DB->get_records_sql($sql, $params);

  $course_ids = array_keys($course_records);
 
  return $course_ids;
}

function get_courses_events(array $course_ids): array {
  global $DB;

  error_log('$get_courses_events: '. print_r($course_ids, true));
  $course_id_list = implode(", ", $course_ids);
  $sql = "SELECT cal.*, course.shortname
          FROM mdl_local_bbzcal cal
          INNER JOIN mdl_course course ON cal.course_id = course.id
          WHERE course_id IN ($course_id_list)";
  $events = $DB->get_records_sql($sql);
  return $events;
}

$semester = get_course_semester($course_id);

if($u->is_teacher($DB)) {
  // get course, all student's classes, then courses, and show their events
  $student_ids = get_course_students($course_id);
  $course_ids = get_students_courses($student_ids, $semester);
  $events = get_courses_events($course_ids);
} else {
  // get users courses, and show their events
  $course_ids = get_students_courses([$USER->id], $semester);
  $events = get_courses_events($course_ids);
}

foreach($events as &$event) {
  $event->shortname = explode(' - ', $event->shortname)[1];
}

$renderer = new local_bbzcal\renderer($OUTPUT, 'course', $course_id, $date);

$usr = new local_bbzcal\user($USER->id);
$admin_course_ids = $usr->get_teacher_course_ids($DB);

$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_url('/local/bbzcal/calendar.php');

$PAGE->navbar->add($title);

$renderer->header();
$renderer->calendar($events, $admin_course_ids);
$renderer->modal();
$renderer->js();
$renderer->footer();

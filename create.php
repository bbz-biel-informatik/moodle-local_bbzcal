<?php

require(__DIR__ . '/../../config.php');

global $DB;

$timestamp = $_POST['timestamp'];
$title = $_POST['title'];
$course_id = $_POST['course_id'];

$course = $DB->get_record('course', array('id' => $course_id));
require_login($course);

// get courses administrated by user
$usr = new local_bbzcal\user($USER->id);
$admin_ids = $usr->get_admin_course_ids($DB);

// can only add to course where user is admin
if(!in_array($course_id, $admin_ids)) {
  header("Location: " . $_POST['location']);
  die();
}

// add record if all checks passed
$item = new stdClass();
$item->date = $timestamp;
$item->title = $title;
$item->course_id = $course_id;
$DB->insert_record('local_bbzcal', $item);

header("Location: " . $_POST['location']);
die();

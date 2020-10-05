<?php

require(__DIR__ . '/../../config.php');

global $DB;

$timestamp = $_POST['timestamp'];
$title = $_POST['title'];
$course_id = $_POST['course_id'];

$item = new stdClass();
$item->date = $timestamp;
$item->title = $title;
$item->course_id = $course_id;
$DB->insert_record('local_bbzcal', $item);

header("Location: " . $_POST['location']);
die();

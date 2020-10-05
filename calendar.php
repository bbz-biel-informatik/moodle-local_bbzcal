<?php

require(__DIR__ . '/../../config.php');

global $PAGE, $DB, $CFG;

date_default_timezone_set("UTC");

$renderer = new local_bbzcal\renderer($CFG, $DB, $PAGE, $OUTPUT, 'course');

echo $renderer->header();

$events = [];
$course_id = optional_param('courseid', null, PARAM_INT); {}
if($course_id != null) {
  $events = local_bbzcal\event::get_course_events($DB, $course_id);
} else {
  $events = local_bbzcal\event::get_user_events($DB, $USER->id);
}

$today = new DateTime();
$today->setTimezone(new DateTimeZone('UTC'));
$dayOfMonth = $today->format('j');
$currentMonth = $today->format('n');
$daysInCurrentMonth = $today->format('t');

$date = (clone $today)->modify('first day of this month');
if ($date->format('w') != 1) {
  $date->modify('previous monday');
}

$shouldStopRendering = false;

echo '<div class="local_bbzcal table">';

$weekdays = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
foreach($weekdays as $weekday) {
  echo '<div class="dayhead">' . $weekday . '</div>';
}

while (!$shouldStopRendering) {
  $weekDay = $date->format('w');
  $month = $date->format('n');
  $day = $date->format('j');

  if ($month != $currentMonth) {
    // we're either at the beginning or end of our table
    echo '<div class="day">';
  } else if ($day == $dayOfMonth) {
    // highlight the current date
    echo '<div class="day current">' . $day;
  } else {
    echo '<div class="day">' . $day;
  }

  $matches = array_filter($events, function ($k) use ($date) {
    return $k->date == $date->getTimestamp();
  });
  foreach($matches as $match) {
    echo '<div class="event">' . $match->title . '</div>';
  }

  echo '</div>';

  if ($weekDay == 0) {
    if ($month != $currentMonth || $day == $daysInCurrentMonth) {
        $shouldStopRendering = true;
    }
  }

  // move on to the next day we need to display
  $date->modify('+1 day');
}
echo '</div>';

echo $renderer->footer();

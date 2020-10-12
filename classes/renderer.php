<?php

namespace local_bbzcal;

defined('MOODLE_INTERNAL') || die();

class renderer {
  private $OUTPUT;
  private $type;
  private $course_id;
  private $date;

  public function __construct($OUTPUT, $type, $course_id, $date, $klasslist) {
    $this->OUTPUT = $OUTPUT;
    $this->type = $type;
    $this->course_id = $course_id;
    $this->date = $date;
    $this->klasslist = $klasslist;
  }

  public function header() {
    echo $this->OUTPUT->header();
  }

  public function calendar($events, $admin_course_ids) {
    $today = new \DateTime();
    $today->setTimezone(new \DateTimeZone('UTC'));
    $today->setTime(0, 0, 0);
    $calendarDate = \DateTime::createFromFormat('Y-m-d', $this->date);
    $calendarDate->setTimezone(new \DateTimeZone('UTC'));
    $calendarDate->setTime(0, 0, 0);

    $currentMonth = $calendarDate->format('n');

    $startDate = (clone $calendarDate)->modify('first day of this month');
    if ($startDate->format('w') != 1) {
      $startDate->modify('previous monday');
    }

    $endDate = (clone $calendarDate)->modify('last day of this month');
    if($endDate->format('w') != 0) {
      $endDate->modify('next sunday');
    }

    $weekdays = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];

    $dates = array();

    for($date = (clone $startDate); $date <= (clone $endDate); $date->modify('+1 day')) {

      $item = new \stdClass();
      $item->day = $date->format('j');
      $item->timestamp = $date->getTimestamp();
      $item->klasslist = $this->klasslist;
      $item->events = array();

      $month = $date->format('n');

      if ($month != $currentMonth) {
        $item->offlimit = true;
      } else if ($date == $today) {
        $item->current = true;
      }

      $matches = array_filter($events, function ($k) use ($date) {
        return $k->date == $date->getTimestamp();
      });
      foreach($matches as $match) {
        if(intval($match->course_id) != intval($this->course_id)) {
          $match->external = true;
        }
        if(in_array($match->course_id, $admin_course_ids)) {
          $match->editable = true;
        }
        array_push($item->events, $match);
      }

      array_push($dates, $item);

    }
    $data = new \stdClass();
    $data->weekdays = $weekdays;
    $data->dates = $dates;
    $data->courseid = $this->course_id;
    $data->admin = in_array($this->course_id, $admin_course_ids);
    $data->current_month = $calendarDate->format('F Y');
    $data->previous_month = (clone $calendarDate)->modify('first day of last month')->format('Y-m-d');
    $data->next_month = (clone $calendarDate)->modify('first day of next month')->format('Y-m-d');
    $data->today = $today->format('Y-m-d');
    $data->course_id = $this->course_id;
    echo $this->OUTPUT->render_from_template('local_bbzcal/calendar', $data);
  }

  public function footer() {
    $content = $this->OUTPUT->footer();
    echo $content;
  }

  public function modal() {
    $data = new \stdClass();
    echo $this->OUTPUT->render_from_template('local_bbzcal/modal', $data);
  }

  public function js() {
    $data = new \stdClass();
    echo $this->OUTPUT->render_from_template('local_bbzcal/js', $data);
  }
}

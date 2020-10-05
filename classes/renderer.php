<?php

namespace local_bbzcal;

defined('MOODLE_INTERNAL') || die();

class renderer {
  private $CFG;
  private $DB;
  private $PAGE;
  private $OUTPUT;
  private $type;

  public function __construct($CFG, $DB, $PAGE, $OUTPUT, $type) {
    $this->CFG = $CFG;
    $this->DB = $DB;
    $this->PAGE = $PAGE;
    $this->OUTPUT = $OUTPUT;
    $this->type = $type;
  }

  public function header() {
    $title = get_string('global_nav_item', 'local_bbzcal');
    if($this->type == 'course') {
      $title = get_string('course_nav_item', 'local_bbzcal');
    }

    $this->PAGE->set_context(\context_system::instance());
    $this->PAGE->set_pagelayout('standard');
    $this->PAGE->set_title($title);
    $this->PAGE->set_url('/local/bbzcal/calendar.php');

    $this->PAGE->set_heading($title);
    $this->PAGE->navbar->add($title);

    $this->PAGE->requires->js_call_amd('local_bbzcal/modal', 'init');

    return $this->OUTPUT->header();
  }

  public function calendar($events) {
    $str = '';
    $today = new \DateTime();
    $today->setTimezone(new \DateTimeZone('UTC'));
    $dayOfMonth = $today->format('j');
    $currentMonth = $today->format('n');
    $daysInCurrentMonth = $today->format('t');

    $date = (clone $today)->modify('first day of this month');
    if ($date->format('w') != 1) {
      $date->modify('previous monday');
    }

    $shouldStopRendering = false;

    $str .= '<div class="local_bbzcal table">';

    $weekdays = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
    foreach($weekdays as $weekday) {
      $str .= '<div class="dayhead">' . $weekday . '</div>';
    }

    while (!$shouldStopRendering) {
      $weekDay = $date->format('w');
      $month = $date->format('n');
      $day = $date->format('j');

      if ($month != $currentMonth) {
        // we're either at the beginning or end of our table
        $str .= '<div class="day off">';
      } else if ($day == $dayOfMonth) {
        // highlight the current date
        $str .= '<div class="day current">';
      } else {
        $str .= '<div class="day">';
      }

      $str .= '<div class="date">' . $day . '</div><div class="content">';

      $matches = array_filter($events, function ($k) use ($date) {
        return $k->date == $date->getTimestamp();
      });
      foreach($matches as $match) {
        $str .= '<div class="event">' . $match->title . '</div>';
      }

      $str .= '</div><div class="add" data-date="' . $date->getTimestamp() . '">&plus;</div></div>';

      if ($weekDay == 0) {
        if ($month != $currentMonth || $day == $daysInCurrentMonth) {
            $shouldStopRendering = true;
        }
      }

      // move on to the next day we need to display
      $date->modify('+1 day');
    }
    $str .= '</div>';
    return $str;
  }

  public function footer() {
    $content = $this->OUTPUT->footer();
    return $content;
  }

  public function modal() {
    return "
      <div class='local_bbzcal_modal'>
        <div class='title'>Add Event</div>
        <div class='body'>
          Date <input name='date' type='text' disabled><br>
          Title <input name='title' type='text'>
          <input type='submit' value='Save'>
        </div>
      </div>
    ";
  }

  public function js() {
    $code = "
    <script>
      var modal = document.querySelector('.local_bbzcal_modal');
      var buttons = document.querySelectorAll('.local_bbzcal .add');
      buttons.forEach(button => {
        button.addEventListener('click', function() {
          modal.style.display = 'block';
          modal.style.top = this.offsetTop;
          modal.style.left = this.offsetLeft;
        });
      });
    </script>";
    return $code;
  }
}

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
    $today->setTime(0, 0, 0);
    $currentMonth = $today->format('n');

    $startDate = (clone $today)->modify('first day of this month');
    if ($startDate->format('w') != 1) {
      $startDate->modify('previous monday');
    }

    $endDate = (clone $today)->modify('last day of this month');
    if($endDate->format('w') != 0) {
      $endDate->modify('next sunday');
    }

    $weekdays = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];

    $dates = array();

    for($date = (clone $startDate); $date <= (clone $endDate); $date->modify('+1 day')) {

      $item = new \stdClass();
      $item->day = $date->format('j');
      $item->timestamp = $date->getTimestamp();
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
        array_push($item->events, $match);
      }

      array_push($dates, $item);

    }
    $data = new \stdClass();
    $data->weekdays = $weekdays;
    $data->dates = $dates;
    return $this->OUTPUT->render_from_template('local_bbzcal/calendar', $data);
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

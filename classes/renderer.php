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
    require_login();

    $title = get_string('global_nav_item', 'local_bbzcal');
    if($this->type == 'course') {
      $title = get_string('course_nav_item', 'local_bbzcal');
    }

    $this->PAGE->set_context(\context_system::instance());
    $this->PAGE->set_pagelayout('standard');
    $this->PAGE->set_title($title);
    $this->PAGE->set_url('/local/bbzcal/' . $this->type . '.php');

    $this->PAGE->set_heading($title);
    $this->PAGE->navbar->add($title);

    return $this->OUTPUT->header();
  }

  public function footer() {
    $content = $this->OUTPUT->footer();
    return $content;
  }
}

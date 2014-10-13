<?php

  /**
   * Schedule framework initialization file
   *
   * @package angie.frameworks.schedule
   */

  const SCHEDULE_FRAMEWORK = 'schedule';
  const SCHEDULE_FRAMEWORK_PATH = __DIR__;

  defined('SCHEDULE_FRAMEWORK_INJECT_INTO') or define('SCHEDULE_FRAMEWORK_INJECT_INTO', 'system');

  AngieApplication::setForAutoload(array(
    'ISchedule' => SCHEDULE_FRAMEWORK_PATH . '/models/ISchedule.class.php',
    'IScheduleImplementation' => SCHEDULE_FRAMEWORK_PATH . '/models/IScheduleImplementation.class.php',
  ));
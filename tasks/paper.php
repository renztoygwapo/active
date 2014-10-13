<?php
  
  /**
   * Tasks file that is executed every day
   * 
   * @package activeCollab
   * @subpackage tasks
   */

  require_once dirname(__FILE__) . '/init.php';

  echo 'Sending morning paper at ' . strftime(FORMAT_DATETIME) . ".\n";
  MorningPaper::send(DateValue::makeFromTimestamp(time()));
  echo 'Morning paper sent at ' . strftime(FORMAT_DATETIME) . ".\n";
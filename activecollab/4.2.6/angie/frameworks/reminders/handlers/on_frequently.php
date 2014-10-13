<?php

  /**
   * on_frequently event handler
   *
   * @package angie.framework.reminders
   * @subpackage handlers
   */

  /**
   * Do frequently check
   */
  function reminders_handle_on_frequently() {
    $reminders = Reminders::findDueForSend();
    
    if($reminders) {
     	foreach($reminders as $reminder) {
        if ($reminder->getParent() instanceof ApplicationObject) {
          $reminder->send();
        } // if
     	} // foreach
    } // if
  } // reminders_handle_on_frequently
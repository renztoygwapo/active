<?php

  /**
   * Check incoming email on_frequently event handler
   *
   * @package angie.framework.email
   * @subpackage handlers
   */

  /**
   * Do frequently check
   */
  function email_handle_on_frequently() {

    // Send messages that are set to be sent instantly but something was wrong and they aren't sent
    $messages = OutgoingMessages::findByMethod(AngieMailerDelegate::SEND_INSTANTNLY, MAILING_QUEUE_MAX_PER_REQUEST);
    if(is_foreachable($messages)) {
      foreach($messages as $message) {
        $message->send();
      } // foreach
    } // if

    if(!AngieApplication::isOnDemand()) {
      // Send messages that are set to be sent in background
      $messages = OutgoingMessages::findByMethod(AngieMailerDelegate::SEND_IN_BACKGROUD, MAILING_QUEUE_MAX_PER_REQUEST);
      if(is_foreachable($messages)) {
        foreach($messages as $message) {
          $message->send();
        } // foreach
      } // if

      // Import email from active mailboxes
      AngieApplication::incomingMail()->importFromMailboxes(20);

    }//if

    
  } // email_handle_on_frequently
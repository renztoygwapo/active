<?php

  /**
   * on_hourly event handler implementation
   * 
   * @package angie.frameworks.email
   * @subpackage handlers
   */

  /**
   * Handle on_hourly event
   */
  function email_handle_on_hourly() {
  	AngieApplication::mailer()->sendDigest(OutgoingMessages::findByMethod(AngieMailerDelegate::SEND_HOURLY));
  } // email_handle_on_hourly
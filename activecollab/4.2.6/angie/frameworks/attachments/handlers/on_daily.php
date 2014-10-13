<?php

  /**
   * on_daily event handler
   * 
   * @package angie.frameworks.attachments
   * @subpackage handlers
   */

  /**
   * Handle on daily task
   */
  function attachments_handle_on_daily() {
    Attachments::cleanUp();
  } // attachments_handle_on_daily
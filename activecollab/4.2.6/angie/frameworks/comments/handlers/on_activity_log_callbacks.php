<?php

  /**
   * on_activity_log_callbacks event handler
   * 
   * @package angie.frameworks.comments
   * @subpackage helpers
   */

  /**
   * Handle on_activity_log_callbacks event
   * 
   * @param array $callbacks
   */
  function comments_handle_on_activity_log_callbacks(&$callbacks) {
    $callbacks['comment/created'] = new CommentCreatedActivityLogCallback();
  } // comments_handle_on_activity_log_callbacks
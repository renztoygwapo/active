<?php

  /**
   * Tracking on_user_cleanup event handler
   *
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_user_cleanup event
   *
   * @param array $cleanup
   */
  function tracking_handle_on_user_cleanup(&$cleanup) {
    $cleanup['time_records'] = array(
      array(
        'id' => 'user_id', 
        'name' => 'user_name', 
        'email' => 'user_email', 
      ),
      array(
        'id' => 'created_by_id', 
        'name' => 'created_by_name', 
        'email' => 'created_by_email', 
      )
    );
    
    $cleanup['expenses'] = array(
      array(
        'id' => 'user_id', 
        'name' => 'user_name', 
        'email' => 'user_email', 
      ),
      array(
        'id' => 'created_by_id', 
        'name' => 'created_by_name', 
        'email' => 'created_by_email', 
      )
    );
  } // tracking_handle_on_user_cleanup

?>
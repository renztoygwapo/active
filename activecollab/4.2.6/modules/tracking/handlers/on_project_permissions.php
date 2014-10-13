<?php

  /**
   * Tracking handle on_project_permissions event
   *
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   */
  function tracking_handle_on_project_permissions(&$permissions) {
  	$permissions['tracking'] = lang('Time and Expenses');
  } // tracking_handle_on_project_permissions
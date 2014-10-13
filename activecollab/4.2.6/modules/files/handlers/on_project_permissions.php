<?php

  /**
   * Assets handle on_project_permissions event
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   */
  function files_handle_on_project_permissions(&$permissions) {
  	$permissions['file'] = lang('Files');
  } // files_handle_on_project_permissions
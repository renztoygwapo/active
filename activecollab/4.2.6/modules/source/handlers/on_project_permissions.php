<?php

  /**
   * Source handle on_project_permissions event
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   */
  function source_handle_on_project_permissions(&$permissions) {
  	$permissions['repository'] = lang('Repositories');
  } // source_handle_on_project_permissions
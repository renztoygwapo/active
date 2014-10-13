<?php

  /**
   * Milestones handle on_project_permissions event
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   */
  function system_handle_on_project_permissions(&$permissions) {
  	$permissions['milestone'] = lang('Milestones');
  } // system_handle_on_project_permissions
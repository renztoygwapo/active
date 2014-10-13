<?php

  /**
   * Notebooks handle on_project_permissions event
   *
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   */
  function notebooks_handle_on_project_permissions(&$permissions) {
  	$permissions['notebook'] = lang('Notebooks');
  } // notebooks_handle_on_project_permissions
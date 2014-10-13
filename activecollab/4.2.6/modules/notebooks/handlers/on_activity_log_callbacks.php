<?php

  /**
   * on_activity_log_callbacks event handler
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */

  /**
   * Handle on_activity_log_callbacks event
   * 
   * @param array $callbacks
   */
  function notebooks_handle_on_activity_log_callbacks(&$callbacks) {
    $callbacks['notebook_page/new_version'] = new NotebookPageVersionCreatedActivityLogCallback();
  } // notebooks_handle_on_activity_log_callbacks
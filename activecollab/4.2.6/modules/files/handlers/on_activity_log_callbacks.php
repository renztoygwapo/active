<?php

  /**
   * on_activity_log_callbacks event handler
   * 
   * @package activeCollab.modules.files
   * @subpackage helpers
   */

  /**
   * Handle on_activity_log_callbacks event
   * 
   * @param array $callbacks
   */
  function files_handle_on_activity_log_callbacks(&$callbacks) {
    $callbacks['file/new_version'] = new FileVersionCreatedActivityLogCallback();
    $callbacks['text_document/new_version'] = new TextDocumentVersionCreatedActivityLogCallback();
  } // files_handle_on_activity_log_callbacks
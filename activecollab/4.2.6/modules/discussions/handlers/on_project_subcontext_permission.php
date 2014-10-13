<?php

  /**
   * on_project_subcontext_permission event handler implementation
   * 
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on_project_subcontext_permission event
   * 
   * @param array $map
   */
  function discussions_handle_on_project_subcontext_permission(&$map) {
    $map['discussions'] = 'discussion';
  } // discussions_handle_on_project_subcontext_permission
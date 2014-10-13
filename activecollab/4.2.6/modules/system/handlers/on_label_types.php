<?php

  /**
   * on_label_types event handler
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Register label type
   *
   * @param array $types
   */
  function system_handle_on_label_types(&$types) {
    $types['ProjectLabel'] = array(
      'text' => lang('Project Labels'), 
      'supports_fg_color' => true, 
      'supports_bg_color' => true, 
    );
    
    $types['AssignmentLabel'] = array(
      'text' => lang('Assignment Labels'), 
      'supports_fg_color' => true, 
      'supports_bg_color' => true, 
    );
  } // system_handle_on_label_types
<?php

  /**
   * Milestones module register_milestone_type handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Register milestone type
   * 
   * This is handler that will register milestone type to any event that requires type registration
   *
   * @return string
   */
  function system_handle_register_milestone_type() {
    return 'Milestone';
  } // system_handle_register_milestone_type
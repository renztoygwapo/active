<?php

  /**
   * on_homescreen_tab_types event handler
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle on_homescreen_tab_types event
   * 
   * @param array $types
   * @param IUser $user
   */
  function system_handle_on_homescreen_tab_types(&$types, &$user) {
    $types[] = new AssignmentFiltersHomescreenTab();
  } // system_handle_on_homescreen_tab_types
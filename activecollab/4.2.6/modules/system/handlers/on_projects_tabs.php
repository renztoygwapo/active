<?php

  /**
   * System module on_projects_tabs event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Handle on prepare projects tabs event
   *
   * @param WireframeTabs $tabs
   * @param IUser $logged_user
   */
  function system_handle_on_projects_tabs(WireframeTabs &$tabs, IUser &$logged_user) {
	  $tabs->add('projects_timeline', lang('Timeline'), Router::assemble('projects_timeline'));

    if($logged_user instanceof User) {
      if(ConfigOptions::getValue('project_requests_enabled') && ProjectRequests::canUse($logged_user)) {
        $tabs->add('project_requests', lang('Requests'), Router::assemble('project_requests'));
      } // if

      if($logged_user->isAdministrator() || $logged_user->isProjectManager()) {
        $tabs->add('project_templates', lang('Templates'), Router::assemble('project_templates'));
      } // if
    } // if
  } // system_handle_on_projects_tabs
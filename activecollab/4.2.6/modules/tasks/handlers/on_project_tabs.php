<?php

  /**
   * Tasks module on_project_tabs event handler
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */
  
  /**
   * Handle on prepare project overview event
   *
   * @param NamedList $tabs
   * @param User $logged_user
   * @param Project $project
   * @param array $tabs_settings
   * @param string $interface
   */
  function tasks_handle_on_project_tabs(&$tabs, &$logged_user, &$project, &$tabs_settings, $interface) {
    if(in_array('tasks', $tabs_settings) && Tasks::canAccess($logged_user, $project, false)) {
    	$tabs->add('tasks', array(
        'text' => lang('Tasks'),
        'url' => Router::assemble('project_tasks', array('project_slug' => $project->getSlug())),
        'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? 
        	AngieApplication::getImageUrl('icons/16x16/tasks-tab-icon.png', TASKS_MODULE) : 
        	AngieApplication::getImageUrl('icons/listviews/tasks.png', TASKS_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } // if
  } // tasks_handle_on_project_tabs
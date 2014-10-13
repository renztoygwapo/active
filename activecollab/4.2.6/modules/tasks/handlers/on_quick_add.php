<?php

  /**
   * Tasks module on_quick_add event handler
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */
  
  /**
   * Handle on quick add event
   *
   * @param NamedList $items
   * @param NamedList $subitems
   * @param array $map
   * @param User $logged_user
   * @param DBResult $projects 
   * @param DBResult $companies
   * @param string $interface
   */
  function tasks_handle_on_quick_add($items, $subitems, &$map, $logged_user, $projects, $companies, $interface = AngieApplication::INTERFACE_DEFAULT) {
  	$item_id = 'task';

  	if(is_foreachable($projects)) {
  		foreach($projects as $project) {
  			if(Tasks::canAdd($logged_user, $project)) {
  				$map[$item_id][] = 'project_' . $project->getId();
  			} // if
  		} // foreach
  		
  		if(isset($map[$item_id])) {
		  	$items->add($item_id, array(
		  		'text'	=> lang('Task'),
		  		'title' => lang('Add Task to the Project'),
		  		'dialog_title' => lang('Add Task to the :name Project'),
		  		'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/task.png', TASKS_MODULE) : AngieApplication::getImageUrl('icons/96x96/task.png', TASKS_MODULE, $interface),
		  		'url'		=> Router::assemble('project_tasks_add', array('project_slug' => '--PROJECT-SLUG--')),
		  		'group' => QuickAddCallback::GROUP_PROJECT,
		  		'event'	=> 'task_created'
		  	));
  		} // if
  	} // if

  } // tasks_handle_on_project_tabs
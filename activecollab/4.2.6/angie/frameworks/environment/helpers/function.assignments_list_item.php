<?php

  /**
   * assignments_list_item helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render assignments list item
   * 
   * @param array $params
   * @param Smarty $smarty
   */
  function smarty_function_assignments_list_item($params, &$smarty) {
  	$object = array_required_var($params, 'object', true);
  	
  	$urls = array_var($params, 'urls', null, true);
  	$project_slugs = array_var($params, 'project_slugs', null, true);
  	
  	$interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
  	
  	$result = '';
  	
  	// Phone interface
  	if($interface == AngieApplication::INTERFACE_PHONE) {
  		$result = '<li><a href="';
  		
  		$project_slug = !is_null($project_slugs) && isset($project_slugs[$object['project_id']]) ? $project_slugs[$object['project_id']] : null;
  		if(!is_null($project_slug)) {
	  		if($object['type'] == 'Task') {
	  			$result .= strtr($urls['task_url'], array('--PROJECT-SLUG--' => $project_slug, '--TASK-ID--' => $object['task_id']));
	  		} else {
	  			$result .= strtr($$urls['todo_url'], array('--PROJECT-SLUG--' => $project_slug, '--TODO-LIST-ID--' => $object['task_id']));
	  		} // if
	  	} else {
	  		$result .= '#';
	  	} // if
	  	
	  	return $result .= '"><span class="object_type '.lcfirst($object['type']).'">'.$object['type'].'</span> '.clean($object['name']).'</a></li>';
	  	
	  // Other interfaces
  	} else {
  		return $result;
  	} // if
  } // smarty_function_assignments_list_item
<?php

  /**
   * Subscriptions class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Subscriptions extends FwSubscriptions {

    /**
     * Paginate subscriptions by user, optionally filter by project
     *
     * @param User $user
     * @param Project $project
     * @return array
     */
    static function showByUser(User $user, $project = null) {
      $project_objects_ids = $subtasks_ids = array();
      $project_objects = $subtasks = array();

      $subscriptions = DB::execute("SELECT id, parent_id, parent_type FROM ".TABLE_PREFIX."subscriptions WHERE user_id = '{$user->getId()}'");
      if (is_foreachable($subscriptions)) {
        foreach ($subscriptions as $subscription) {
        	if ($subscription['parent_type'] == "ProjectObjectSubtask") {
        		$subtasks_ids[] = $subscription['parent_id'];
        	} else {
        		$project_objects_ids[] = $subscription['parent_id'];
        	} //if
        } // foreach
      } // if

      $results = array();
      $and_project_id = ($project instanceof Project) ? "AND project_id = {$project->getId()}" : "";

      // We first have to get all the subtasks user is subscribed to
      if (is_foreachable($subtasks_ids)) {
        $subtasks = DB::execute("SELECT id, type, parent_id, parent_type, priority, body, created_on, created_by_id FROM ".TABLE_PREFIX."subtasks WHERE id in (?) AND state >= ?", $subtasks_ids, STATE_VISIBLE);
      } // if

	    if (is_foreachable($subtasks)) {
	    	$subtasks_parents = array();
	    	foreach ($subtasks as $subtask) {
	    		$subtasks_parents[$subtask['parent_type']][] = $subtask['parent_id'];
	     	} //foreach

		    foreach($subtasks_parents as $k => $v) {
		      $subtasks_parents[$k] = DB::prepare("(type = ? AND id IN (?) $and_project_id)", $k, $v);
		    } // foreach

		    $subtasks_parents = implode(' OR ', $subtasks_parents);
		    $parents = DB::execute("SELECT DISTINCT id,project_id,integer_field_1 FROM ".TABLE_PREFIX."project_objects WHERE $subtasks_parents");

		    $parent_keys = array();

		    foreach($parents as $parent) {
		    	$parent_keys[$parent['id']] = array('id' => $parent['project_id'], 'integer_field_1' => $parent['integer_field_1']);
		    } //foreach
	      foreach ($subtasks as $subtask) {
	      	if (!($project instanceof Project) || (!$parents) || (!in_array($subtask['parent_id'], array_keys($parent_keys)))) {
	      		continue;
	      	} //if

	      	$subscription_id = null;
	      	foreach ($subscriptions as $subscription) {
	      		if (($subtask['id'] === $subscription['parent_id']) && ($subscription['parent_type'] == "ProjectObjectSubtask")) {
	      			$subscription_id = $subscription['id'];
	      			break;
	      		} //if
	      	} //foreach

	      	list($route_name, $route_param_name) = Subscriptions::projectObjectTypeToRouteNameAndParam($subtask['parent_type']);
	      	$parent_id = $subtask['parent_type'] === 'Task' ? $parent_keys[$subtask['parent_id']]['integer_field_1'] : $subtask['parent_id'];
	      	
	      	$results[] = array(
	      		'id' => $subtask['id'],
	      		'subscription_id' => $subscription_id,
	      		'priority' => $subtask['priority'],
	      		'type' => 'Subtask',
	      		'type_short' => 'subtask',
	      		'name' => $subtask['body'],
	      		'created_on' => $subtask['created_on'],
	      		'created_by_id' => $subtask['created_by_id'],
	      		'project_id' =>  $parent_keys[$subtask['parent_id']]['id'],
	      		'object_link' => Router::assemble($route_name, array('project_slug' => $project->getSlug(), $route_param_name => $parent_id))
	      	); //array
	      } //foreach
	    } //if

      // now we get the rest of the project objects
      if (is_foreachable($project_objects_ids)) {
        $project_objects = DB::execute("SELECT id,type,project_id,priority,name,created_on,created_by_id,integer_field_1 FROM ".TABLE_PREFIX."project_objects WHERE id in (?) $and_project_id AND state >= ? AND visibility >= ?",$project_objects_ids,STATE_VISIBLE,$user->getMinVisibility());
      } // if

      if (is_foreachable($project_objects)) {
	      foreach ($project_objects as $project_object) {

	      	$subscription_id = null;
	      	foreach ($subscriptions as $subscription) {
	      		if (($project_object['id'] === $subscription['parent_id']) && ($subscription['parent_type'] !== "ProjectObjectSubtask")) {
	      			$subscription_id = $subscription['id'];
	      			break;
	      		} //if
	      	} // foreach
	      	
	      	list($route_name, $route_param_name) = Subscriptions::projectObjectTypeToRouteNameAndParam($project_object['type']);

	      	$object_id = $project_object['type'] === 'Task' ? $project_object['integer_field_1'] : $project_object['id'];
		      $results[] = array(
		      	'id' => $project_object['id'],
		        'priority' => $project_object['priority'],
		      	'subscription_id' => $subscription_id,
		      	'type' => $project_object['type'],
            'type_short' => Inflector::underscore($project_object['type']),
		      	'name' => $project_object['name'],
		      	'created_on' => $project_object['created_on'],
		      	'created_by_id' => $project_object['created_by_id'],
		      	'project_id' =>  $project_object['project_id'],
		      	'object_link' => Router::assemble($route_name, array('project_slug' => $project->getSlug(), $route_param_name => $object_id))
		      ); //array
	      } //foreach
      } //if

      return $results;
    } // showByUser
    
    /**
     * Cached array of types and matching route names
     *
     * @var array
     */
    static private $type_to_route_name = array();
    
    /**
     * Return route name and route parameter for given project object type
     * 
     * @param string $type
     * @return string
     */
    static private function projectObjectTypeToRouteNameAndParam($type) {
      if(!isset(self::$type_to_route_name[$type])) {
        $asset_types = AngieApplication::isModuleLoaded('files') ? ProjectAssets::getAssetTypes() : array();
        $underscore_type = Inflector::underscore($type);
        
    	  if(in_array($type, $asset_types)) {
    	    self::$type_to_route_name[$type] = array("project_assets_{$underscore_type}", 'asset_id');
      	} else {
      	  self::$type_to_route_name[$type] = array("project_{$underscore_type}", "{$underscore_type}_id");
      	} // if
      } // if
      
      return self::$type_to_route_name[$type];
    } // projectObjectTypeToRouteNameAndParam

    /**
     * Mass Unsubscribe function - unsubscribes an user from one or more object at time
     *
     * @param Array $unsubscribe_ids
     * @param User $user
     * @return mixed
     */
    static function massUnsubscribe($unsubscribe_ids,User $user) {
    	if (is_foreachable($unsubscribe_ids)) {
	   		foreach ($unsubscribe_ids as $subscription_id) {
	   			if($user instanceof User) {
          	DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE id = ? AND user_id = ?', $subscription_id, $user->getId());
             AngieApplication::cache()->removeByObject($user, 'subscriptions');
	        } elseif($user instanceof AnonymousUser) {
	          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'subscriptions WHERE id = ? AND user_id = ? AND user_email = ?', $subscription_id, 0, $user->getEmail());
	        } // if
	   		} //foreach
    	} //if
    	return true;
    } //massUnsubscribe

  }
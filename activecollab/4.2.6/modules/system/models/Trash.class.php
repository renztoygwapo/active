<?php

  /**
   * Application level trash implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  final class Trash extends FwTrash {
  	
    /**
     * Return trash sections that given user can access and use
     *
     * @param User $user
     * @return array
     */
    static function getSections(User $user) {

    	// load map
    	self::loadTrashedMap($user);
    	self::$sections = new NamedList();
    	
	  	// projects in trash
	  	$trashed_projects = Projects::findTrashed($user, self::$trashed_map);
			if (is_foreachable($trashed_projects)) {
				self::$sections->add('projects', array(
					'label' => lang('Projects'),
					'count' => count($trashed_projects),
					'items' => $trashed_projects
				));
			}; // if
			
			// companies in trash
			$trashed_companies = Companies::findTrashed($user, self::$trashed_map);
			if (is_foreachable($trashed_companies)) {
				self::$sections->add('companies', array(
					'label' => lang('Companies'),
					'count' => count($trashed_companies),
					'items' => $trashed_companies
				));
			}; // if
					
			// users which are in trash, and their companies are not in trash
			$trashed_users = Users::findTrashed($user, self::$trashed_map);
			if (is_foreachable($trashed_users)) {
				self::$sections->add('users', array(
					'label' => lang('Users'),
					'count' => count($trashed_users),
					'items' => $trashed_users
				));
			}; // if
					
			// project objects in trash
			$trashed_project_objects = ProjectObjects::findTrashed($user, self::$trashed_map);
			if (is_foreachable($trashed_project_objects)) {
				$sorted_project_objects = array();
				foreach ($trashed_project_objects as $trashed_project_object) {				
					$type = $trashed_project_object['type'];
					if (!isset($sorted_project_objects[$type])) {
						$sorted_project_objects[$type] = array();
					} // if
	
					$sorted_project_objects[$type][] = $trashed_project_object;
				}; // foreach
										
				foreach ($sorted_project_objects as $object_type => $object_type_items) {
					self::$sections->add($object_type, array(
						'label' => lang(Inflector::pluralize(Inflector::humanize(Inflector::underscore($object_type)))),
						'count' => count($object_type_items),
						'items' => $object_type_items
					));
				}; // foreach
			}; // if
			
			// invoke parents section retriever
			parent::getSections($user);
			
			// return the result
			return self::$sections;
    } // getSections
    
    /**
     * Empty the trash
     * 
     * @param User $user
     */
    static function purge(User $user) {
	  	Projects::deleteTrashed($user);
	  	Companies::deleteTrashed($user);
	  	Users::deleteTrashed($user);
	  	ProjectObjects::deleteTrashed($user);
	  	parent::purge($user);
    } // purge
    
    /**
     * Load trashed map
     * 
     * @param User $user
     * @return null
     */
    static function loadTrashedMap(User $user) {
    	self::$trashed_map = array();
    	
    	self::$trashed_map = array_merge(
    		self::$trashed_map,
    		(array) Projects::getTrashedMap($user),
    		(array) Companies::getTrashedMap($user),
    		(array) Users::getTrashedMap($user),
    		(array) ProjectObjects::getTrashedMap($user)
    	);
    	
    	parent::loadTrashedMap($user);
    } // loadTrashendMap
    
  }
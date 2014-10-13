<?php

  /**
   * Framework level trash implementation
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwTrash {
    
    /**
     * Cached trash sections instance
     *
     * @var NamedList
     */
    static protected $sections = array();
    
    /**
     * map of trashed objects
     * 
     * @var array
     */
    static protected $trashed_map = false;
    
    /**
     * Return trash sections that given user can access and use
     *
     * @param User $user
     * @return array
     */
    static function getSections(User $user) {
    	if (!(self::$sections instanceof NamedList)) {
	    	self::loadTrashedMap($user);
	    	self::$sections = new NamedList();
    	} // if
    	
      // find trashed subtasks
      if (AngieApplication::isFrameworkLoaded('subtasks')) {
      	$trashed_subtasks = Subtasks::findTrashed($user, self::$trashed_map);
				if (is_foreachable($trashed_subtasks)) {
					self::$sections->add('subtasks', array(
						'label' => lang('Subtasks'),
						'count' => count($trashed_subtasks),
						'items' => $trashed_subtasks
					));
				}; // if
      } // if
              
      // find trashed comments
      if (AngieApplication::isFrameworkLoaded('comments')) {
      	$trashed_comments = Comments::findTrashed($user, self::$trashed_map);
        if (is_foreachable($trashed_comments)) {
					self::$sections->add('comments', array(
						'label' => lang('Comments'),
						'count' => count($trashed_comments),
						'items' => $trashed_comments
					));
        } // if
      } // if
        
      // find trashed attachments
      if (AngieApplication::isFrameworkLoaded('attachments')) {
      	$trashed_attachments = Attachments::findTrashed($user, self::$trashed_map);
        if (is_foreachable($trashed_attachments)) {
					self::$sections->add('attachments', array(
						'label' => lang('Attachments'),
						'count' => count($trashed_attachments),
						'items' => $trashed_attachments
					));
        } // if
      } // if
        
			EventsManager::trigger('on_trash_sections', array(&self::$sections, &self::$trashed_map, &$user));
			
      return self::$sections;
    } // getSections
    
    /**
     * Empty the trash
     * 
     * @param User $user
     */
    static function purge(User $user) {
    	// delete trashed subtasks if framework is loaded
    	if (AngieApplication::isFrameworkLoaded('subtasks')) {
    		Subtasks::deleteTrashed($user);
    	} // if
    	
    	// delete trashed comments if framework is loaded
    	if (AngieApplication::isFrameworkLoaded('comments')) {
    		Comments::deleteTrashed($user);
    	} // if
    	
    	// delete trashed attachments if framework is loaded
    	if (AngieApplication::isFrameworkLoaded('attachments')) {
    		Attachments::deleteTrashed($user);
    	} // if
    	
    	EventsManager::trigger('on_empty_trash', array(&$user));
    } // purge
    
    /**
     * Load trashed map
     * 
     * @param User $user
     * @return null
     */
    static function loadTrashedMap(User $user) {
    	if (self::$trashed_map === false) {
    		self::$trashed_map = array();
    	} // if

      $subtasks_map = AngieApplication::isFrameworkLoaded('subtasks') ? (array) Subtasks::getTrashedMap($user) : array();
      $comments_map = AngieApplication::isFrameworkLoaded('comments') ? (array) Comments::getTrashedMap($user) : array();
      $attachments_map = AngieApplication::isFrameworkLoaded('attachments') ? (array) Attachments::getTrashedMap($user) : array();

    	self::$trashed_map = array_merge(
    		self::$trashed_map,
    		$subtasks_map,
    		$comments_map,
    		$attachments_map
    	);
    	
    	EventsManager::trigger('on_trash_map', array(&self::$trashed_map, &$user));
    } // loadTrashedMap
    
    /**
     * Get parent query for $map
     * 
     * @param array $map
     * @return array
     */
    static function getParentQuery(&$map) {
			$result = array();
    	if (!is_foreachable($map)) {
    		return '';
    	} // if
    	
    	foreach ($map as $object_type => $object_ids) {
    		if ($object_ids) {
    			$result[] = '(' . DB::prepareConditions(array('parent_type = ? AND parent_id IN (?)', $object_type, $object_ids)) . ')';
    		} // if
    	} // foreach
    	
    	if (!is_foreachable($result) || !count($result)) {
    		return null;
    	} // if
    	
    	return 'NOT (' . implode(' OR ', $result) . ')';
    } // getParentQuery
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Return true if $user can access trash
     *
     * @param IUser $user
     * @return boolean
     */
    static function canAccess(IUser $user) {
      return $user instanceof User && $user->canManageTrash();
    } // canAccess
    
  }
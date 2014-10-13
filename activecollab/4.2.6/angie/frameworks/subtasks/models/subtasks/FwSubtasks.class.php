<?php

  /**
   * Framework level subtask implementation
   *
   * @package angie.frameworks.subtasks
   * @subpackage models
   */
  abstract class FwSubtasks extends BaseSubtasks {
    
    /**
     * Cached array of class names and is subtask class values
     *
     * @var array
     */
    static private $is_subtask_class = array();
    
    /**
     * Returns true if $class_name is subtask class
     *
     * @param string $class_name
     * @return boolean
     */
    function isSubtaskClass($class_name) {
      if($class_name == 'Subtask') {
        return true;
      } // if
      
      if(!isset(self::$is_subtask_class[$class_name])) {
        $class = new ReflectionClass($class_name);
        
        self::$is_subtask_class[$class_name] = $class->isSubclassOf('Subtask');
      } // if
      
      return self::$is_subtask_class[$class_name];
    } // isSubtaskClass

    /**
     * Advance subtasks by parent
     *
     * @param ISubtasks $parent
     * @param integer $advance
     * @throws InvalidParamError
     * @throws Exception
     */
    static function advanceByParent($parent, $advance) {
      $advance = (integer) $advance;

      if($parent instanceof ISubtasks) {
        $parent_class = get_class($parent);
        $parent_id = $parent->getId();
      } elseif(is_array($parent) && count($parent) == 2) {
        list($parent_class, $parent_id) = $parent;
      } else {
        throw new InvalidParamError('parent', $parent, '$parent is expected to be ISubtasks instance or an array where first element is parent type and second is parent id');
      } // if

      if($advance != 0) {
        $subtasks_table = TABLE_PREFIX . 'subtasks';

        try {
          DB::beginWork('Rescheduling subtasks @ ' . __CLASS__);

          $subtasks = DB::execute("SELECT id, due_on FROM $subtasks_table WHERE parent_type = ? AND parent_id = ? AND completed_on IS NULL AND due_on IS NOT NULL", $parent_class, $parent_id);

          if($subtasks) {
            foreach($subtasks as $subtask) {
              $due_on = new DateValue($subtask['due_on']);
              $due_on->advance($advance); // Initial advance

              while(!Globalization::isWorkday($due_on)) {
                $due_on->advance(86400);
              } // while

              DB::execute("UPDATE $subtasks_table SET due_on = ? WHERE id = ?", $due_on, $subtask['id']);
            } // foreach
          } // if

          DB::commit('Subtasks rescheduled @ ' . __CLASS__);

          AngieApplication::cache()->removeByModel('subtasks');
        } catch(Exception $e) {
          DB::rollback('Failed to reschedule subtasks @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // advanceByParent

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return all tasks that belong to a given object
     *
     * @param ISubtasks $parent
     * @return Subtask[]
     */
    static function findByParent(ISubtasks $parent) {
      return self::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ? AND state >= ?', get_class($parent), $parent->getId(), ($parent instanceof IState ? $parent->getState() : STATE_VISIBLE)),
        'order' => 'ISNULL(position) ASC, position, priority DESC, created_on'
      ));
    } // findByParent
    
    /**
     * Return number of tasks in a given object
     *
     * @param ISubtasks|array $parent
     * @param IUser $user
     * @param boolean $use_cache
     * @return array
     */
    static function countByParent($parent, $user = null, $use_cache = true) {
      return AngieApplication::cache()->getByObject($parent, 'subtasks_count', function() use ($parent) {
        if($parent instanceof ISubtasks) {
          $parent_type = get_class($parent);
          $parent_id = $parent->getId();
        } elseif(is_array($parent) && count($parent) == 2) {
          list($parent_type, $parent_id) = $parent;
        } else {
          throw new InvalidParamError('parent', $parent, 'ISubtasks instance or type-ID pair expected');
        } // if

        $min_state = $parent instanceof IState ? $parent->getState() : STATE_VISIBLE;

        return array(
          (integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'subtasks WHERE parent_type = ? AND parent_id = ? AND state >= ?', $parent_type, $parent_id, $min_state),
          (integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'subtasks WHERE parent_type = ? AND parent_id = ? AND state >= ? AND completed_on IS NULL', $parent_type, $parent_id, $min_state),
        );
      }, !$use_cache);
    } // countByParent
    
    /**
     * Return open tasks that belong to a given object
     *
     * @param ISubtasks $parent
     * @return Subtask[]
     */
    static function findOpenByParent(ISubtasks $parent) {
      return self::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ? AND state >= ? AND completed_on IS NULL', get_class($parent), $parent->getId(), ($parent instanceof IState ? $parent->getState() : STATE_VISIBLE)),
        'order' => 'ISNULL(position) ASC, position, priority DESC, created_on'
      ));
    } // findOpenByParent
    
    /**
     * Return only completed tasks that belong to a specific object
     *
     * @param ISubtasks $parent
     * @return Subtask[]
     */
    static function findCompletedByParent(ISubtasks $parent) {
      return self::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ? AND state >= ? AND completed_on IS NOT NULL', get_class($parent), $parent->getId(), ($parent instanceof IState ? $parent->getState() : STATE_VISIBLE)),
        'order' => 'completed_on DESC'
      ));
    } // findCompletedByParent
    
    /**
     * Return next position by parent object
     *
     * @param ISubtasks $parent
     * @return integer
     */
    static function nextPositionByParent(ISubtasks $parent) {
      return (integer) DB::executeFirstCell('SELECT MAX(position) FROM ' . TABLE_PREFIX . 'subtasks WHERE parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()) + 1;
    } // nextPositionByParent
    
    // ---------------------------------------------------
    //  State
    // ---------------------------------------------------
    
    /**
     * Archive subtasks attached to a given parent object
     *
     * @param ISubtasks $parent
     * @throws InvalidInstanceError
     */
    static function archiveByParent(ISubtasks &$parent) {
      if($parent instanceof IState) {
        $parent->state()->archiveSubitems(TABLE_PREFIX . 'subtasks', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // archiveByParent
    
    /**
     * Unarchive subtasks attached to a given parent object
     *
     * @param ISubtasks $parent
     * @throws InvalidInstanceError
     */
    static function unarchiveByParent(ISubtasks &$parent) {
      if($parent instanceof IState) {
        $parent->state()->unarchiveSubitems(TABLE_PREFIX . 'subtasks', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // unarchiveByParent
    
    /**
     * Trash subtasks attached to a given parent object
     *
     * @param ISubtasks $parent
     * @throws InvalidInstanceError
     */
    static function trashByParent(ISubtasks &$parent) {
      if($parent instanceof IState) {
        $parent->state()->trashSubitems(TABLE_PREFIX . 'subtasks', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // trashByParent
    
    /**
     * Restore from trash subtasks attached to a given parent object
     *
     * @param ISubtasks $parent
     * @throws InvalidInstanceError
     */
    static function untrashByParent(ISubtasks &$parent) {
      if($parent instanceof IState) {
        $parent->state()->untrashSubitems(TABLE_PREFIX . 'subtasks', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // untrashByParent
    
    /**
     * Trash subtasks attached to a given parent object
     *
     * @param ISubtasks $parent
     * @param boolean $soft
     * @throws Exception
     */
    static function deleteByParent(ISubtasks &$parent, $soft = true) {
      $subtasks_table = TABLE_PREFIX . 'subtasks';
      
      if($soft && $parent instanceof IState) {
        $parent->state()->deleteSubitems($subtasks_table, array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        try {
          DB::beginWork('Dropping subtasks @ ' . __CLASS__);
          
          $rows = DB::execute("SELECT id, type FROM $subtasks_table WHERE parent_type = ? AND parent_id = ?", get_class($parent), $parent->getId());
          
          if($rows) {
            $subtask_ids = array();
            $subtask_type_ids = array();
            
            foreach($rows as $row) {
              $id = (integer) $row['id'];
              $type = $row['type'];
              
              $subtask_ids[] = $id;
              
              if(isset($subtask_type_ids[$type])) {
                $subtask_type_ids[$type][] = $id;
              } else {
                $subtask_type_ids[$type] = array($id);
              } // if
            } // foreach
            
            $subtask_conditions = array();
            foreach($subtask_type_ids as $type => $ids) {
              $subtask_conditions[] = DB::prepare('(parent_type = ? AND parent_id IN (?))', $type, $ids);
            } // foreach
            $subtask_conditions = implode(' AND ', $subtask_conditions);
            
            DB::execute("DELETE FROM $subtasks_table WHERE id IN (?)", $subtask_ids);
            
            DB::execute("DELETE FROM " . TABLE_PREFIX . "assignments WHERE $subtask_conditions");
            DB::execute("DELETE FROM " . TABLE_PREFIX . "subscriptions WHERE $subtask_conditions");
            DB::execute("DELETE FROM " . TABLE_PREFIX . "estimates WHERE $subtask_conditions");
            
            if($parent instanceof IActivityLogs) {
              $activity_logs_table = TABLE_PREFIX . 'activity_logs';
              
              $activity_log_ids = array();
              
              $rows = DB::execute("SELECT id, raw_additional_properties FROM $activity_logs_table WHERE parent_type = ? AND parent_id = ?", get_class($parent), $parent->getId());
              if($rows) {
                foreach($rows as $row) {
                  $attributes = isset($row['raw_additional_properties']) && $row['raw_additional_properties'] ? unserialize($row['raw_additional_properties']) : null;
                  
                  if($attributes && isset($attributes['subtask_id']) && in_array($attributes['subtask_id'], $subtask_ids)) {
                    $activity_log_ids[] = (integer) $row['id'];
                  } // if
                } // if
              } // if
              
              if($activity_log_ids) {
                DB::execute("DELETE FROM $activity_logs_table WHERE id IN (?)", $activity_log_ids);
              } // if
            } // if
          } // if
          
          DB::commit('Subtasks dropped @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to rollback subtasks @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteByParent
    
    /**
     * Delete entries by parents
     * 
     * $parents is an array where key is parent type and value is array of 
     * object ID-s of that particular parent
     * 
     * @param array $parents
     * @throws Exception
     */
    static function deleteByParents($parents) {
      $subtasks_table = TABLE_PREFIX . 'subtasks';
      
      try {
        DB::beginWork('Removing subtasks by parent type and parent IDs @ ' . __CLASS__);
        
        if(is_foreachable($parents)) {
          foreach($parents as $parent_type => $parent_ids) {
            $rows = DB::execute("SELECT id, type FROM $subtasks_table WHERE parent_type = ? AND parent_id IN (?)", $parent_type, $parent_ids);
            
            if($rows) {
              $subtasks = array();
              
              foreach($rows as $row) {
                if(array_key_exists($row['type'], $subtasks)) {
                  $subtasks[$row['type']][] = (integer) $row['id'];
                } else {
                  $subtasks[$row['type']] = array((integer) $row['id']);
                } // if
              } // foreach
              
              DB::execute("DELETE FROM $subtasks_table WHERE parent_type = ? AND parent_id IN (?)", $parent_type, $parent_ids);
              
              ActivityLogs::deleteByParents($subtasks);
              Assignments::deleteByParents($subtasks);
              Subscriptions::deleteByParents($subtasks);
              ModificationLogs::deleteByParents($subtasks);
            } // if
          } // foreach
        } // if
        
        DB::commit('Comments removed by parent type and parent IDs @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove comments by parent type and parent IDs @ ' . __CLASS__);
        throw $e;
      } // try
    } // deleteByParents
    
    /**
     * Delete by parent types
     * 
     * @param array $types
     * @throws Exception
     */
    static function deleteByParentTypes($types) {
      $subtasks_table = TABLE_PREFIX . 'subtasks';
      
      $rows = DB::executeFirstColumn("SELECT id, type FROM $subtasks_table WHERE parent_type IN (?)", $types);
      
      if($rows) {
        $parents = array();
        
        foreach($rows as $row) {
          if(array_key_exists($row['type'], $parents)) {
            $parents[$row['type']][] = (integer) $row['id'];
          } else {
            $parents[$row['type']] = array((integer) $row['id']);
          } // if
        } // foreach
        
        try {
          DB::beginWork('Cleaning up subtask data @ ' . __CLASS__);
          
          DB::execute("DELETE FROM $subtasks_table WHERE parent_type IN (?)", $types);
        
          ActivityLogs::deleteByParents($parents);
          Assignments::deleteByParents($parents);
          Subscriptions::deleteByParents($parents);
          ModificationLogs::deleteByParents($parents);
          
          DB::commit('Subtask data cleaned up @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to clean up subtask data @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteByParentTypes
    
    /**
     * Find subtasks for widget
     * 
     * 	- Return array MUST resemble fwSubtask->describe() output
     * 
     * @param ISubtasks $parent
     * @param IUser $user
     * @return array
     */
    static function findForWidget(Isubtasks $parent, IUser $user) {
    	$subtasks = DB::execute('SELECT id, type, body, priority, assignee_id, label_id, completed_on, due_on, state FROM `' . TABLE_PREFIX . 'subtasks` WHERE parent_type = ? AND parent_id = ? AND state >= ? ORDER BY ISNULL(position) ASC, position, priority DESC, created_on', get_class($parent), $parent->getId(), $parent->getState());
    	if (!is_foreachable($subtasks)) {
    		return null;
    	} // if
    	
    	// cast dates
    	$subtasks->setCasting(array(
    		'completed_on'	=> DBResult::CAST_DATETIME,
    		'due_on'				=> DBResult::CAST_DATE
    	));
    	
    	// extract all subtask and user ids subtask ids
    	$subtask_ids = array();
    	$user_ids = array();
    	$subtask_type = null;
    	foreach ($subtasks as $subtask) {
    		$subtask_ids[] = $subtask['id'];
    		if ($subtask['assignee_id'] && !in_array($subtask['assignee_id'], $user_ids)) {
    			$user_ids[] = $subtask['assignee_id'];
    		} // if
    		if ($subtask_type === null) {
    			$subtask_type = $subtask['type'];
    		} // if
    	} // foreach
    	
    	// check if we can edit parent
    	$can_edit_parent = $parent->canEdit($user);    	
    	
    	// find labels
    	$labels = Labels::getIdDetailsMap('AssignmentLabel');
    	
    	// find users
    	if (is_foreachable($user_ids)) {
    		$users = Users::getIdDetailsMap($user_ids, array('first_name', 'last_name', 'email'));    		
    	} // if
    	
    	// return array of subtasks to which user is subscribed
    	$subscriptions = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_id IN (?) AND parent_type = ? AND user_id = ?', $subtask_ids, $subtask_type, $user->getId());

    	// urls bases
    	$routing_context = $parent->getRoutingContext() . '_subtask';
    	$routing_context_params = array_merge((array) $parent->getRoutingContextParams(), array('subtask_id' => '--SUBTASK--ID--'));
    	$edit_subtask_url_base = Router::assemble($routing_context . '_edit', $routing_context_params);
    	$trash_subtask_url_base = Router::assemble($routing_context . '_trash', $routing_context_params);
    	$complete_url_base = Router::assemble($routing_context . '_complete', $routing_context_params);
    	$open_url_base = Router::assemble($routing_context . '_reopen', $routing_context_params);
    	$subscribe_url_base = Router::assemble($routing_context . '_subscribe', $routing_context_params);
    	$unsubscribe_url_base = Router::assemble($routing_context . '_unsubscribe', $routing_context_params);
      $update_label_url_base = Router::assemble($routing_context . '_update_label', $routing_context_params);

    	// icons
    	$edit_icon = AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK);
    	$trash_icon = AngieApplication::getImageUrl('/icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK);
    	$subscription_active_icon = AngieApplication::getImageUrl('icons/12x12/object-subscription-active.png', SUBSCRIPTIONS_FRAMEWORK);
    	$subscription_inactive_icon = AngieApplication::getImageUrl('icons/12x12/object-subscription-inactive.png', SUBSCRIPTIONS_FRAMEWORK);
    	    	
    	// form the final result in similar form to $subtask->describe() but with considrable savings in resources (memory and queries)
    	$result = array();
    	foreach ($subtasks as $subtask) {    		
    		$new_subtask = array(
    			'id'						=> $subtask['id'],
    			'name'					=> $subtask['body'],
    			'completed_on'	=> $subtask['completed_on'],
    			'due_on'				=> $subtask['due_on'],
    			'priority'			=> $subtask['priority'],
    			'permissions'		=> array(
            'can_change_complete_status'  => !($subtask['state'] < STATE_VISIBLE) && ($can_edit_parent || $subtask['assignee_id'] == $user->getId())
    			),
    			'urls'					=> array(
    					'open'								=> str_replace('--SUBTASK--ID--', $subtask['id'], $open_url_base),
    					'complete'						=> str_replace('--SUBTASK--ID--', $subtask['id'], $complete_url_base),
              'update_label'        => str_replace('--SUBTASK--ID--', $subtask['id'], $update_label_url_base)
    			),
    		);
    		
    		// if label exists describe it

    		$label = isset($labels[$subtask['label_id']]) ? $labels[$subtask['label_id']] : null;
				if ($label) {
					$new_subtask['label'] = $label;
				}  // if
				
				// if assignee exists describe it
				$assignee = isset($users[$subtask['assignee_id']]) ? $users[$subtask['assignee_id']] : null;
				if ($assignee) {
					$new_subtask['assignee'] = array(
						'short_display_name' => Users::getUserDisplayName($assignee, true)
					);
				} // if
				
				$new_subtask['options'] = new NamedList();
				if ($can_edit_parent) {
					// edit action
	        $new_subtask['options']->add('edit', array(
	        	'text' => lang('Edit'),
	          'url' => str_replace('--SUBTASK--ID--', $subtask['id'], $edit_subtask_url_base),
	        	'icon' => $edit_icon,
	        	'classes' => array('for_active_only'),  
	        ));
	        
	        // move to trash action	        
	        $new_subtask['options']->add('trash', array(
	          'text' => lang('Trash'),
	          'url' => str_replace('--SUBTASK--ID--', $subtask['id'], $trash_subtask_url_base),
	          'icon' => $trash_icon
	        ));
	      } // if
	      
	      // subscriptions toggler
      	$new_subtask['options']->add('subscription', array(
	        'text' => lang('Subscription'), 
	      	'url' => '#',
	        'classes' => array('always_show', 'for_active_only'), 
	        'onclick' => new AsyncTogglerCallback(array(
		          'url' => str_replace('--SUBTASK--ID--', $subtask['id'], $unsubscribe_url_base), 
		      		'text' => lang('Subscribed'), 
		      		'title' => lang('Click to Unsubscribe'), 
		      		'icon' => $subscription_active_icon, 
		        ), array(
		          'url' => str_replace('--SUBTASK--ID--', $subtask['id'], $subscribe_url_base),
		        	'text' => lang('Not Subscribed'),  
		      		'title' => lang('Click to Subscribe'), 
		      		'icon' => $subscription_inactive_icon, 
		        ),
		        (bool) in_array($subtask['id'], $subscriptions)
	        ), 
	      ));	      

    		$result[] = $new_subtask;
    	} // foreach
    	
    	EventsManager::trigger('on_subtasks_for_widget_options', array(&$parent, &$user, &$result, &$subtask_ids, array(
    		'subtask_type'						=> $subtask_type,
    		'routing_context'					=> $routing_context,
    		'routing_context_params'	=> $routing_context_params,
    	)));
    	
    	return $result;
    } // findForWidget
    
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
    	$trashed_subtasks = DB::execute('SELECT id, type FROM ' . TABLE_PREFIX . 'subtasks WHERE state = ?', STATE_TRASHED);

      if($trashed_subtasks) {
        $result = array();

        foreach ($trashed_subtasks as $trashed_subtask) {
          $type = strtolower($trashed_subtask['type']);

          if (!isset($result[$type])) {
            $result[$type] = array();
          } // if

          $result[$type][] = $trashed_subtask['id'];
        } // foreach

        return $result;
      } else {
        return null;
      } // if
    } // getTrashedMap
    
    /**
     * Find trashed subtasks
     * 
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
    	$query = Trash::getParentQuery($map);    	
    	
    	if ($query) {
    		$trashed_subtasks = DB::execute('SELECT id, body, type, parent_id, parent_type FROM ' . TABLE_PREFIX . 'subtasks WHERE state = ? AND ' . $query . ' ORDER BY created_on DESC', STATE_TRASHED);
    	} else {
    		$trashed_subtasks = DB::execute('SELECT id, body, type, parent_id, parent_type FROM ' . TABLE_PREFIX . 'subtasks WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED);
    	} // if
    	
    	if (!is_foreachable($trashed_subtasks)) {
    		return null;
    	} // if
    	
    	$items = array();
    	foreach ($trashed_subtasks as $subtask) {
    		$items[] = array(
    			'id' => $subtask['id'],
    			'name' => $subtask['body'],
    			'type' => $subtask['type'],
    		);
    	} // foreach
    	
    	return $items;
    } // findTrashed
    
    /**
     * Delete trashed subtasks
     * 
     * @param User $user
     * @return boolean
     */
    static function deleteTrashed(User $user) {
    	$subtasks = Subtasks::find(array(
    		'conditions' => array('state = ?', STATE_TRASHED)
    	));
    	
    	if (is_foreachable($subtasks)) {
    		foreach ($subtasks as $subtask) {
    			$subtask->state()->delete();
    		} // foreach
    	} // if
    	
    	return true;
    } // deleteTrashed
    
  }
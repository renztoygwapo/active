<?php

  /**
   * TimeRecords class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class TimeRecords extends BaseTimeRecords {
    
    /**
     * Return timerecords by a given list of ID-s
     *
     * @param array $ids
     * @param integer $min_state
     * @return TimeRecord[]
     */
    static function findByIds($ids, $min_state = STATE_ARCHIVED) {
      return self::find(array(
        'conditions' => array('id IN (?) AND state >= ?', $ids, $min_state),
      ));
    } // findByIds
    
    /**
     * Return time records by parent
     * 
     * @param ITracking $parent
     * @param integer $billable_status
     * @return DBResult
     */
    static function findByParent(ITracking $parent, $billable_status = null) {
      $min_state = $parent instanceof IState ? $parent->getState() : STATE_VISIBLE;
      
      if($billable_status) {
        return self::find(array(
        	'conditions' => array('parent_type = ? AND parent_id = ? AND billable_status = ? AND state >= ?', get_class($parent), $parent->getId(), $billable_status, $min_state)
        ));
      } else {
        return self::find(array(
        	'conditions' => array('parent_type = ? AND parent_id = ? AND state >= ?', get_class($parent), $parent->getId(),$min_state)
        ));
      }//if
    } // findByParent
    
    /**
     * Sum time by parent
     * 
     * @param User $user
     * @param ITracking $parent
     * @param array $statuses
     * @param boolean $include_subitems
     * @return float
     */
    static function sumByParent(User $user, ITracking $parent, $statuses = null, $include_subitems = false) {
      $min_state = $parent instanceof IState ? $parent->getState() : STATE_ARCHIVED;
      
      if($statuses === null) {
        return (float) DB::executeFirstCell('SELECT SUM(value) FROM ' . TABLE_PREFIX . 'time_records WHERE ' . TrackingObjects::prepareParentTypeFilter($user, $parent, $include_subitems) . ' AND state >= ?', $min_state);
      } else {
        return (float) DB::executeFirstCell('SELECT SUM(value) FROM ' . TABLE_PREFIX . 'time_records WHERE ' . TrackingObjects::prepareParentTypeFilter($user, $parent, $include_subitems) . ' AND state >= ? AND billable_status IN (?)', $min_state, $statuses);
      } // if
    } // sumByParent
    
    /**
     * Sum time records by milestone
     * 
     * @param User $user
     * @param Milestone $milestone
     * @param mixed $statuses
     * @return float
     */
    static function sumByMilestone(User $user, Milestone $milestone, $statuses = null) {
      if(AngieApplication::isModuleLoaded('tasks') && Tasks::canAccess($user, $milestone->getProject())) {
        $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND milestone_id = ? AND state >= ? AND visibility >= ?', 'Task', $milestone->getId(), STATE_VISIBLE, $user->getMinVisibility());
        
        if($task_ids) {
          if($statuses === null) {
            return (float) DB::executeFirstCell('SELECT SUM(value) FROM ' . TABLE_PREFIX . 'time_records WHERE parent_type = ? AND parent_id IN (?) AND state >= ?', 'Task', $task_ids, STATE_VISIBLE);
          } else {
            return (float) DB::executeFirstCell('SELECT SUM(value) FROM ' . TABLE_PREFIX . 'time_records WHERE parent_type = ? AND parent_id IN (?) AND state >= ? AND billable_status IN (?)', 'Task', $task_ids, STATE_VISIBLE, $statuses);
          } // if
        } // if
      } // if
      
      return 0;
    } // sumByMilestone
    
    /**
     * Find time records by milestone
     * 
     * @param User $user
     * @param Milestone $milestone
     * @param mixed $statuses
     * @return array
     */
    static function findByMilestone(User $user, Milestone $milestone, $statuses = null) {
      if(AngieApplication::isModuleLoaded('tasks') && Tasks::canAccess($user, $milestone->getProject())) {
        $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND milestone_id = ? AND project_id = ? AND state >= ? AND visibility >= ?', 'Task', $milestone->getId(), $milestone->getProjectId(), STATE_VISIBLE, $user->getMinVisibility()); // Milestone ID + Project ID (integrity issue from activeCollab 2)
        
        if($statuses === null) {
          return TimeRecords::find(array(
            'conditions' => array('parent_type = ? AND parent_id IN (?) AND state >= ?', 'Task', $task_ids, STATE_VISIBLE)
          ));
        } else {
          return TimeRecords::find(array(
            'conditions' => array('parent_type = ? AND parent_id IN (?) AND billable_status IN (?) AND state >= ?', 'Task', $task_ids, $statuses, STATE_VISIBLE)
          ));
        }//if
      }//if
      
      return null;
    } // fundByMilestone

    /**
     * Count time records by milestone
     *
     * @param User $user
     * @param Milestone $milestone
     * @param mixed $statuses
     * @return array
     */
    static function countByMilestone(User $user, Milestone $milestone, $statuses = null) {
      if(AngieApplication::isModuleLoaded('tasks') && Tasks::canAccess($user, $milestone->getProject())) {
        $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND milestone_id = ? AND state >= ? AND visibility >= ?', 'Task', $milestone->getId(), STATE_VISIBLE, $user->getMinVisibility());

        if($statuses === null) {
          return TimeRecords::count(array('parent_type = ? AND parent_id IN (?) AND state >= ?', 'Task', $task_ids, STATE_VISIBLE));
        } else {
          return TimeRecords::count(array('parent_type = ? AND parent_id IN (?) AND billable_status IN (?) AND state >= ?', 'Task', $task_ids, $statuses, STATE_VISIBLE));
        }//if
      }//if

      return null;
    } // countByMilestone

    /**
     * Group records by job type
     * 
     * @param array $records
     * @return array
     */
    static function groupByJobType($records) {
      if(is_foreachable($records)) {
        $grouped = array();
        foreach($records as $time_record) {
          $key = $time_record->getJobTypeName();
          
          if(!isset($grouped[$key])) {
            $grouped[$key] = array();
          } // if
          
          $grouped[$key][] = $time_record;  
        } // foreach
      } // if
      return $grouped;
    } // groupByJobType
    
    /**
     * Group record hours by user and day
     * 
     * @param array $records
     * @return array 
    */
    static function groupByUserDaily($records) {
      $grouped = array();
    	$timestamps_array = array();
    	if(is_foreachable($records)) {
        $grouped = array();
        foreach($records as $record) {
          $user_email = $record->getUserEmail();
          $user_name = $record->getUserName();
          $timestamp_day = $record->getRecordDate()->getTimestamp();
          $timestamps_array[] = $timestamp_day;
          $grouped[$user_email.'||'.$user_name][$timestamp_day] += $record->getValue();
        } //foreach
        foreach ($timestamps_array as $timestamp) {
        	foreach($grouped as $key=>$value) {
        		if (!$grouped[$key][$timestamp]) {
        			$grouped[$key][$timestamp] = 0;
        		}//if
        	} //foreach
        } //foreach
      } //if
	    return $grouped;
    } // groupByUserDaily
    
    /**
     * Check if all time records have the same job hourly rate
     * 
     * @param array $records
     * @return mixed unit_cost or false
     */
    static function isIdenticalJobRate($records) {
      if(is_foreachable($records)) {
        $previous = null;

        foreach($records as $time_record) {
          $job_type_id = $time_record->getJobTypeId();
          $project = $time_record->getProject();

          $job_type_rates = JobTypes::getIdRateMapFor($project); //job_type_id => cost

          $job_type_rate = isset($job_type_rates[$job_type_id]) ? $job_type_rates[$job_type_id] : 0;

          if($previous !== null && $job_type_rate != $previous) {
            return false;
          } // if

          $previous = $job_type_rate;
        } // foreach
        
        return $previous;
      } // if

      return true;
    } // isIdenticalJobRate
    
    /**
     * Return number of time records that use this particular job type
     * 
     * @param JobType $job_type
     * @return integer
     */
    static function countByJobType(JobType $job_type) {
      return TimeRecords::count(array('job_type_id = ?', $job_type->getId()));
    } // countByJobType
    
    /**
     * Remove time records by parent type
     * 
     * @param array $types
     */
    static function deleteByParents($parents) {
      $time_records_table = TABLE_PREFIX . 'time_records'; 
      
      if(is_foreachable($parents)) {
        try {
          DB::beginWork('Removing time records by parent type and parent IDs @ ' . __CLASS__);
          
          foreach($parents as $parent_type => $parent_ids) {
            $ids = DB::execute("SELECT id FROM $time_records_table WHERE parent_type = ? AND parent_id IN (?)", $parent_type, $parent_ids);
            if($ids) {
              DB::execute("DELETE FROM $time_records_table WHERE id IN (?)", $ids);
              
              ActivityLogs::deleteByParents(array('TimeRecord' => $ids));
              ModificationLogs::deleteByParents(array('TimeRecord' => $ids));
              
              if(AngieApplication::isModuleLoaded('invoicing')) {
                DB::execute('DELETE FROM ' . TABLE_PREFIX  . 'invoice_related_records WHERE parent_type = ? AND parent_id IN (?)', 'Expense', $ids);
              } // if
            } // if
            
            DB::execute('DELETE FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id IN (?)', $parent_type, $parent_ids);
          } // foreach
        
          DB::commit('Time records removed by parent type and parent IDs @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to remove time records by parent type and parent IDs @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteByParents
    
    /**
     * Remove time records by parent type
     * 
     * @param array $types
     */
    static function deleteByParentTypes($types) {
      $time_records_table = TABLE_PREFIX . 'time_records'; 
      
      try {
        DB::beginWork('Removing time records by parent type @ ' . __CLASS__);
        
        $ids = DB::execute("SELECT id FROM $time_records_table WHERE parent_type IN (?)", $types);
        if($ids) {
          DB::execute("DELETE FROM $time_records_table WHERE id IN (?)", $ids);
          
          if(AngieApplication::isModuleLoaded('invoicing')) {
            DB::execute('DELETE FROM ' . TABLE_PREFIX  . 'invoice_related_records WHERE parent_type = ? AND parent_id IN (?)', 'TimeRecord', $ids);
          } // if
        } // if
        
        DB::commit('Time records removed by parent type @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove time records by parent type @ ' . __CLASS__);
        throw $e;
      } // try
    } // deleteByParentTypes
    
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
    	return array(
    		'timerecord' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'time_records WHERE state = ?', STATE_TRASHED)
    	);
    } // getTrashedMap
    
    /**
     * Find trashed time records
     * 
     * @param User $user
     * @return array
     */
    static function findTrashed(User $user, &$map) {
    	$query = Trash::getParentQuery($map);
    	    	
    	if ($query) {
	    	$trashed_time_records = DB::execute('SELECT id, value, parent_id, parent_type FROM ' . TABLE_PREFIX . 'time_records WHERE state = ? AND ' . $query . ' ORDER BY created_on DESC', STATE_TRASHED);
    	} else {
	    	$trashed_time_records = DB::execute('SELECT id, value, parent_id, parent_type FROM ' . TABLE_PREFIX . 'time_records WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED);
    	} // if
    	
    	if (!is_foreachable($trashed_time_records)) {
    		return null;
    	} // if
    	
    	$items = array();
    	foreach ($trashed_time_records as $time_record) {
    		$items[] = array(
    			'id' => $time_record['id'],
    			'name' => lang(':time hours', array('time' => $time_record['value'])),
    			'type' => 'TimeRecord'
    		);
    	} // foreach
    	
    	return $items;
    } // findTrashed
    
    /**
     * Delete trashed time records
     * 
     * @param User $user
     * @return boolean
     */
    static function deleteTrashed(User $user) {
    	$time_records = TimeRecords::find(array(
    		'conditions' => array('state = ?', STATE_TRASHED)
    	));
    	
    	if (is_foreachable($time_records)) {
    		foreach ($time_records as $time_record) {
    			$time_record->state()->delete();
    		} // foreach
    	} // if
    	
    	return true;
    } // deleteTrashed

    // ---------------------------------------------------
    //  State
    // ---------------------------------------------------

    /**
     * Archive time records attached to a given parent object
     *
     * @param ITracking $parent
     */
    static function archiveByParent(ITracking $parent) {
      if($parent instanceof IState) {
        $parent->state()->archiveSubitems(TABLE_PREFIX . 'time_records', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // archiveByParent

    /**
     * Unarchive time records attached to a given parent object
     *
     * @param ITracking $parent
     */
    static function unarchiveByParent(ITracking $parent) {
      if($parent instanceof IState) {
        $parent->state()->unarchiveSubitems(TABLE_PREFIX . 'time_records', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // unarchiveByParent

    /**
     * Trash timerecords attached to a given parent object
     *
     * @param ITracking $parent
     */
    static function trashByParent(ITracking $parent) {
      if($parent instanceof IState) {
        $parent->state()->trashSubitems(TABLE_PREFIX . 'time_records', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // trashByParent

    /**
     * Restore from trash timerecords attached to a given parent object
     *
     * @param ITracking $parent
     */
    static function untrashByParent(ITracking $parent) {
      if($parent instanceof IState) {
        $parent->state()->untrashSubitems(TABLE_PREFIX . 'time_records', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // untrashByParent

    /**
     * Trash timerecords attached to a given parent object
     *
     * @param ITracking $parent
     * @param boolean $soft
     */
    static function deleteByParent(ITracking $parent, $soft = true) {
      $time_records_table = TABLE_PREFIX . 'time_records';

      if($soft && $parent instanceof IState) {
        $parent->state()->deleteSubitems($time_records_table, array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        return DB::execute("DELETE FROM $time_records_table WHERE parent_type = ? AND parent_id = ?", get_class($parent), $parent->getId());
      } // if
    } // deleteByParent

    /**
     * Change billable status by IDs
     *
     * @param $ids
     * @param $new_status
     * @return DbResult
     */
    static function changeBilableStatusByIds($ids, $new_status) {
      $time_records_table = TABLE_PREFIX . 'time_records';
      return DB::execute("UPDATE $time_records_table SET billable_status = ? WHERE id IN (?)", $new_status , $ids);
    } //changeBilableStatusByIds

  }
<?php

  /**
   * Expenses class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class Expenses extends BaseExpenses {
    
    /**
     * Return expenses by a given list of ID-s
     *
     * @param array $ids
     * @param integer $min_state
     * @return Expense[]
     */
    static function findByIds($ids, $min_state = STATE_ARCHIVED) {
      return self::find(array(
        'conditions' => array('id IN (?) AND state >= ?', $ids, $min_state),
      ));
    } // findByIds
    
     /**
     * Return expenses by given category
     *
     * @param expenseCategory
     * @param integer $min_state
     * @return array
     */
    static function findByCategory(ExpenseCategory $category, $min_state = STATE_ARCHIVED) {
      return self::find(array(
        'conditions' => array('category_id = ? AND state >= ?', $category->getId(), $min_state),
      ));
    } // findByCategory
    
    /**
     * Return number of expenses by category
     * 
     * @param ExpenseCategory $category
     * @return integer
     */
    static function countByCategory(ExpenseCategory $category) {
      return Expenses::count(array('category_id = ?', $category->getId()));
    } // countByCategory
    
    /**
     * Return expenses by parent
     * 
     * @param ITracking $parent
     * @param integer $billable_status
     * @return DBResult
     */
    static function findByParent(ITracking $parent, $billable_status = null) {
      $min_state = $parent instanceof IState ? $parent->getState() : STATE_ARCHIVED;
      
      if($billable_status) {
        return self::find(array(
        	'conditions' => array('parent_type = ? AND parent_id = ? AND billable_status = ? AND state >= ?', get_class($parent), $parent->getId(), $billable_status, $min_state)
        ));
      } else {
        return self::find(array(
        	'conditions' => array('parent_type = ? AND parent_id = ? AND state >= ?', get_class($parent), $parent->getId(), $min_state)
        ));
      }//if
    } // findByParent
    
    /**
     * Sum expenses by parent
     * 
     * @param IUser $user
     * @param ITracking $parent
     * @param array $statuses
     * @param boolean $include_subitems
     * @return float
     */
    static function sumByParent(IUser $user, ITracking $parent, $statuses = null, $include_subitems = false) {
      $min_state = $parent instanceof IState ? $parent->getState() : STATE_ARCHIVED;
      
      if($statuses === null) {
        return (float) DB::executeFirstCell('SELECT SUM(value) FROM ' . TABLE_PREFIX . 'expenses WHERE ' . TrackingObjects::prepareParentTypeFilter($user, $parent, $include_subitems) . ' AND state >= ?', $min_state);
      } else {
        return (float) DB::executeFirstCell('SELECT SUM(value) FROM ' . TABLE_PREFIX . 'expenses WHERE ' . TrackingObjects::prepareParentTypeFilter($user, $parent, $include_subitems) . ' AND state >= ? AND billable_status IN (?)', $min_state, $statuses);
      } // if
    } // sumByParent
    
    /**
     * Sum expenses by milestone
     * 
     * @param User $user
     * @param Milestone $milestone
     * @param mixed $statuses
     * @return float
     */
    static function sumByMilestone(User $user, Milestone $milestone, $statuses = null) {
      if(AngieApplication::isModuleLoaded('tasks') && Tasks::canAccess($user, $milestone->getProject())) {
        $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND milestone_id = ? AND state >= ? AND visibility >= ?', 'Task', $milestone->getId(), STATE_ARCHIVED, $user->getMinVisibility());
        
        if($task_ids) {
          if($statuses === null) {
            return (float) DB::executeFirstCell('SELECT SUM(value) FROM ' . TABLE_PREFIX . 'expenses WHERE parent_type = ? AND parent_id IN (?) AND state >= ?', 'Task', $task_ids, STATE_ARCHIVED);
          } else {
            return (float) DB::executeFirstCell('SELECT SUM(value) FROM ' . TABLE_PREFIX . 'expenses WHERE parent_type = ? AND parent_id IN (?) AND state >= ? AND billable_status IN (?)', 'Task', $task_ids, STATE_ARCHIVED, $statuses);
          } // if
        } // if
      } // if
      
      return 0;
    } // sumByMilestone
    
    /**
     * Find expenses by milestone
     * 
     * @param User $user
     * @param Milestone $milestone
     * @param mixed $statuses
     * @return array
     */
    static function findByMilestone(User $user, Milestone $milestone, $statuses = null) {
      if(AngieApplication::isModuleLoaded('tasks') && Tasks::canAccess($user, $milestone->getProject())) {
        $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND milestone_id = ? AND project_id = ? AND state >= ? AND visibility >= ?', 'Task', $milestone->getId(), $milestone->getProjectId(), STATE_ARCHIVED, $user->getMinVisibility()); // Milestone ID + Project ID (integrity issue from activeCollab 2)
        
        if($statuses === null) {
          return Expenses::find(array(
            'conditions' => array('parent_type = ? AND parent_id IN (?) AND state >= ?', 'Task', $task_ids, STATE_ARCHIVED)
          ));
        } else {
          return Expenses::find(array(
            'conditions' => array('parent_type = ? AND parent_id IN (?) AND billable_status IN (?) AND state >= ?', 'Task', $task_ids, $statuses, STATE_ARCHIVED)
          ));
        }//if
      }//if
      
      return null;
    } // findByMilestone

    /**
     * Find expenses by milestone
     *
     * @param User $user
     * @param Milestone $milestone
     * @param mixed $statuses
     * @return array
     */
    static function countByMilestone(User $user, Milestone $milestone, $statuses = null) {
      if(AngieApplication::isModuleLoaded('tasks') && Tasks::canAccess($user, $milestone->getProject())) {
        $task_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND milestone_id = ? AND state >= ? AND visibility >= ?', 'Task', $milestone->getId(), STATE_ARCHIVED, $user->getMinVisibility());

        if($statuses === null) {
          return Expenses::count(array('parent_type = ? AND parent_id IN (?) AND state >= ?', 'Task', $task_ids, STATE_ARCHIVED));
        } else {
          return Expenses::count(array('parent_type = ? AND parent_id IN (?) AND billable_status IN (?) AND state >= ?', 'Task', $task_ids, $statuses, STATE_ARCHIVED));
        }//if
      }//if

      return null;
    } // countByMilestone
    
    /**
     * Remove expenses by parent type
     * 
     * @param array $parents
     * @throws Exception
     */
    static function deleteByParents($parents) {
      $expenses_table = TABLE_PREFIX . 'expenses'; 
      
      if(is_foreachable($parents)) {
        try {
          DB::beginWork('Removing expenses by parent type and parent IDs @ ' . __CLASS__);
          
          foreach($parents as $parent_type => $parent_ids) {
            $ids = DB::execute("SELECT id FROM $expenses_table WHERE parent_type = ? AND parent_id IN (?)", $parent_type, $parent_ids);
            if($ids) {
              DB::execute("DELETE FROM $expenses_table WHERE id IN (?)", $ids);
              
              ActivityLogs::deleteByParents(array('Expense' => $ids));
              ModificationLogs::deleteByParents(array('Expense' => $ids));
              
              if(AngieApplication::isModuleLoaded('invoicing')) {
                DB::execute('DELETE FROM ' . TABLE_PREFIX  . 'invoice_related_records WHERE parent_type = ? AND parent_id IN (?)', 'Expense', $ids);
              } // if
            } // if
            
            DB::execute('DELETE FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id IN (?)', $parent_type, $parent_ids);
          } // foreach
        
          DB::commit('Expenses removed by parent type and parent IDs @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to remove expenses by parent type and parent IDs @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // deleteByParents

    /**
     * Remove expenses by parent type
     * 
     * @param array $types
     * @throws Exception
     */
    static function deleteByParentTypes($types) {
      $expenses_table = TABLE_PREFIX . 'expenses'; 
      
      try {
        DB::beginWork('Removing expenses by parent type @ ' . __CLASS__);
        
        $ids = DB::execute("SELECT id FROM $expenses_table WHERE parent_type IN (?)", $types);
        if($ids) {
          DB::execute("DELETE FROM $expenses_table WHERE id IN (?)", $ids);
          
          if(AngieApplication::isModuleLoaded('invoicing')) {
            DB::execute('DELETE FROM ' . TABLE_PREFIX  . 'invoice_related_records WHERE parent_type = ? AND parent_id IN (?)', 'Expense', $ids);
          } // if
        } // if
        
        DB::commit('Expenses removed by parent type @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove expenses by parent type @ ' . __CLASS__);
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
    		'expense' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'expenses WHERE state = ?', STATE_TRASHED)
    	);
    } // getTrashedMap
    
    /**
     * Find trashed expenses
     * 
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
    	$query = Trash::getParentQuery($map);    	
    	if ($query) {
    		$trashed_expenses = DB::execute('SELECT id, value, parent_id, parent_type FROM ' . TABLE_PREFIX . 'expenses WHERE state = ? AND ' . $query . ' ORDER BY created_on DESC', STATE_TRASHED);
    	} else {
    		$trashed_expenses = DB::execute('SELECT id, value, parent_id, parent_type FROM ' . TABLE_PREFIX . 'expenses WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED);
    	} // if
    	
    	if (!is_foreachable($trashed_expenses)) {
    		return null;
    	} // if
    	
    	$items = array();
    	foreach ($trashed_expenses as $trashed_expense) {
    		$items[] = array(
    			'id' => $trashed_expense['id'],
    			'name' => lang(':time expenses', array('time' => $trashed_expense['value'])),
    			'type' => 'Expense'
    		);
    	} // foreach
    	
    	return $items;
    } // findTrashed
    
    /**
     * Delete trashed expenses
     * 
     * @param User $user
     * @return boolean
     */
    static function deleteTrashed(User $user) {
    	$expenses = Expenses::find(array(
    		'conditions' => array('state = ?', STATE_TRASHED)
    	));
    	
    	if (is_foreachable($expenses)) {
    		foreach ($expenses as $expense) {
    			$expense->state()->delete();
    		} // foreach
    	} // if
    	
    	return true;
    } // deleteTrashed

    // ---------------------------------------------------
    //  State
    // ---------------------------------------------------

    /**
     * Archive expenses attached to a given parent object
     *
     * @param ITracking $parent
     * @throws InvalidInstanceError
     */
    static function archiveByParent(ITracking $parent) {
      if($parent instanceof IState) {
        $parent->state()->archiveSubitems(TABLE_PREFIX . 'expenses', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // archiveByParent

    /**
     * Unarchive expenses attached to a given parent object
     *
     * @param ITracking $parent
     * @throws InvalidInstanceError
     */
    static function unarchiveByParent(ITracking $parent) {
      if($parent instanceof IState) {
        $parent->state()->unarchiveSubitems(TABLE_PREFIX . 'expenses', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // unarchiveByParent

    /**
     * Trash expenses attached to a given parent object
     *
     * @param ITracking $parent
     * @throws InvalidInstanceError
     */
    static function trashByParent(ITracking $parent) {
      if($parent instanceof IState) {
        $parent->state()->trashSubitems(TABLE_PREFIX . 'expenses', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // trashByParent

    /**
     * Restore from trash expenses attached to a given parent object
     *
     * @param ITracking $parent
     * @throws InvalidInstanceError
     */
    static function untrashByParent(ITracking $parent) {
      if($parent instanceof IState) {
        $parent->state()->untrashSubitems(TABLE_PREFIX . 'expenses', array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        throw new InvalidInstanceError('parent', $parent, 'IState');
      } // if
    } // untrashByParent

    /**
     * Trash expenses attached to a given parent object
     *
     * @param ITracking $parent
     * @param boolean $soft
     * @return mixed
     */
    static function deleteByParent(ITracking $parent, $soft = true) {
      $expenses_table = TABLE_PREFIX . 'expenses';

      if($soft && $parent instanceof IState) {
        $parent->state()->deleteSubitems($expenses_table, array('parent_type = ? AND parent_id = ?', get_class($parent), $parent->getId()), true);
      } else {
        return DB::execute("DELETE FROM $expenses_table WHERE parent_type = ? AND parent_id = ?", get_class($parent), $parent->getId());
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
      $time_records_table = TABLE_PREFIX . 'expenses';
      return DB::execute("UPDATE $time_records_table SET billable_status = ? WHERE id IN (?)", $new_status , $ids);
    } //changeBilableStatusByIds
    
  }
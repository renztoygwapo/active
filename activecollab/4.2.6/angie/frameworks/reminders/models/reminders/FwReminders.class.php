<?php

  /**
   * Framework level reminder management implementation
   * 
   * @package angie.frameworks.reminders
   * @subpackage models
   */
  abstract class FwReminders extends BaseReminders {
  
  	/**
     * Return active reminders for a given user. If second parameter is true, it will not return reminders whose parent is state archived or trashed
     *
     * @param IUser $user
     * @param Boolean $only_with_visible_parents
     * @return Reminder[]
     * @throws InvalidInstanceError
     */
    static function findActiveByUser(IUser $user, $only_with_visible_parents = false) {
      $reminder_users_table = TABLE_PREFIX . 'reminder_users';
      $reminders_table = TABLE_PREFIX . 'reminders';

      if ($user instanceof User) {
        $reminders = FwReminders::findBySQL("SELECT $reminders_table.* FROM $reminders_table, $reminder_users_table WHERE $reminders_table.id = $reminder_users_table.reminder_id AND $reminder_users_table.user_id = ? AND $reminder_users_table.dismissed_on IS NULL ORDER BY created_on DESC", $user->getId());
      } else if ($user instanceof AnonymousUser) {
        $reminders = FwReminders::findBySQL("SELECT $reminders_table.* FROM $reminders_table, $reminder_users_table WHERE $reminders_table.id = $reminder_users_table.reminder_id AND $reminder_users_table.user_email = ? AND $reminder_users_table.dismissed_on IS NULL ORDER BY created_on DESC", $user->getEmail());
      } else {
        throw new InvalidInstanceError('user', $user, array('User', 'AnonymousUser'));
      } // if

      if ($only_with_visible_parents === true && is_foreachable($reminders)) {
        $filtered_reminders = array();
        foreach($reminders as $reminder) {
          /**
           * @var Reminder $reminder
           */
          $parent = $reminder->getParent();
          if (!$parent instanceof ApplicationObject) {
            continue;
          } // if

          if (!($parent instanceof IState) || ($parent instanceof IState && $parent->getState() === STATE_VISIBLE)) {
            $filtered_reminders[] = $reminder;
          } //if
        } // foreach
        return $filtered_reminders;
      } else {
        return $reminders;
      } //if
    } // findActiveByUser
    
    /**
     * Return number of active reminders for a given user. If second parameter is true, it will not return reminders whose parent is state archived or trashed
     *
     * @param IUser $user
     * @param Boolean $only_with_visible_parents
     * @return integer
     * @throws InvalidInstanceError
     */
    static function countActiveByUser(IUser $user, $only_with_visible_parents = false) {
      $reminder_users_table = TABLE_PREFIX . 'reminder_users';
      $reminders_table = TABLE_PREFIX . 'reminders';

      if ($user instanceof User) {
        $reminders = FwReminders::findBySQL("SELECT $reminders_table.* FROM $reminders_table, $reminder_users_table WHERE $reminders_table.id = $reminder_users_table.reminder_id AND $reminder_users_table.user_id = ? AND $reminder_users_table.dismissed_on IS NULL ORDER BY created_on DESC", $user->getId());
      } else if ($user instanceof AnonymousUser) {
        $reminders = FwReminders::findBySQL("SELECT $reminders_table.* FROM $reminders_table, $reminder_users_table WHERE $reminders_table.id = $reminder_users_table.reminder_id AND $reminder_users_table.user_email = ? AND $reminder_users_table.dismissed_on IS NULL ORDER BY created_on DESC", $user->getEmail());
      } else {
        throw new InvalidInstanceError('user', $user, array('User', 'AnonymousUser'));
      } // if

      if ($only_with_visible_parents === true && is_foreachable($reminders)) {
        $filtered_reminders_count = 0;
        foreach($reminders as $reminder) {
          /**
           * @var Reminder $reminder
           */
          $parent = $reminder->getParent();
          if (!$parent instanceof ApplicationObject) {
            continue;
          } // if

          if (!($parent instanceof IState) || ($parent instanceof IState && $parent->getState() === STATE_VISIBLE)) {
            $filtered_reminders_count++;
          } //if
        } // foreach
        return $filtered_reminders_count;
      } else {
        return ($reminders instanceof DBResult) ? $reminders->count() : 0;
      } //if
    } // countActiveByUser
    
    /**
     * Return all reminders that are due to be sent
     * 
     * @return Reminder[]
     */
    static function findDueForSend() {
    	return Reminders::find(array(
    	  'conditions' => array('send_on <= ? AND sent_on IS NULL', DateTimeValue::now()), 
    	  'order' => 'send_on', 
    	));
    } // findDueForSend
    
    /**
     * Drop all reminders by user
     *
     * @param User $user
     * @return boolean
     * @throws Exception
     */
    static function deleteByUser(User $user) {
    	if($user instanceof User) {
	    	try {
	    		$reminders_table = TABLE_PREFIX . 'reminders';
	    	  $reminder_users_table = TABLE_PREFIX . 'reminder_users';
	    		
	    	  DB::beginWork('Removing reminders by user @ ' . __CLASS__);
	    	  
	    	  // Clean up all upcoming reminders created by this user
	    	  DB::execute("DELETE FROM $reminders_table WHERE created_by_id = ? AND sent_on IS NULL", $user->getId());
	    	  
	    	  // Reset to anonymous user
	    	  DB::execute("UPDATE $reminders_table SET created_by_id = ?, created_by_name = ?, created_by_email = ? WHERE created_by_id = ?", 0, $user->getDisplayName(), $user->getEmail(), $user->getId());
	    	  
	    	  // Dismiss all reminders for this user
	    	  $reminder_ids = DB::executeFirstColumn("SELECT DISTINCT reminder_id FROM $reminder_users_table WHERE user_id = ? AND dismissed_on IS NULL", $user->getId());
	    	  if($reminder_ids) {
	    	  	DB::execute("UPDATE $reminder_users_table SET dismissed_on = UTC_TIMESTAMP() WHERE reminder_id IN (?) AND user_id = ?", $reminder_ids, $user->getId());
	    	  	
	    	  	// Dismiss reminders in case this is the last user who dismissed them
	    	  	foreach($reminder_ids as $reminder_id) {
	    	  		if((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $reminders_table, $reminder_users_table WHERE $reminders_table.id = $reminder_users_table.reminder_id AND $reminder_users_table.dismissed_on IS NULL") == 0) {
	    	  			DB::execute("UPDATE $reminders_table SET dismissed_on = UTC_TIMESTAMP() WHERE id = ?", $reminder_id);
	    	  		} // if
	    	  	} // foreach
	    	  } // if
	    	  
	    	  // Reset to anonymous
	    	  DB::execute("UPDATE $reminder_users_table SET user_id = ?, user_name = ?, user_email = ? WHERE user_id = ?", 0, $user->getDisplayName(), $user->getEmail(), $user->getId());
	    	  
	    	  DB::commit('Removed reminders by user @ ' . __CLASS__);
	    	} catch(Exception $e) {
	    	  DB::rollback('Failed to remove reminders by user @ ' . __CLASS__);
	    	  throw $e;
	    	} // try
    	} else {
    		throw new InvalidInstanceError('user', $user, 'User');
    	} // if
    } // deleteByUser
    
    /**
     * Clear reminders by object
     *
     * @param IReminders $object
     * @throws Exception
     */
    static function deleteByParent(IReminders &$object) {
    	$reminders_table = TABLE_PREFIX . 'reminders';
    	$reminder_users_table = TABLE_PREFIX . 'reminder_users';
    	
    	$reminder_ids = DB::executeFirstColumn("SELECT id FROM $reminders_table WHERE parent_type = ? AND parent_id = ?", get_class($object), $object->getId());
    	
    	if($reminder_ids && count($reminder_ids)) {
    		try {
    		  DB::beginWork('Deleting reminders by parent @ ' . __CLASS__);
    		  
    		  DB::execute("DELETE FROM $reminder_users_table WHERE reminder_id IN (?)", $reminder_ids);
    		  DB::execute("DELETE FROM $reminders_table WHERE id IN (?)", $reminder_ids);
    		  
    		  DB::commit('Reminders deleted by parent @ ' . __CLASS__);
    		} catch(Exception $e) {
    		  DB::rollback('Failed to delete reminders by parent @ ' . __CLASS__);
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
      $reminders_table = TABLE_PREFIX . 'reminders';
    	$reminder_users_table = TABLE_PREFIX . 'reminder_users';
      
      try {
        DB::beginWork('Removing reminders by parent type and parent IDs @ ' . __CLASS__);
        
        if(is_foreachable($parents)) {
          foreach($parents as $parent_type => $parent_ids) {
            $reminder_ids = DB::executeFirstColumn("SELECT id FROM $reminders_table WHERE parent_type = ? AND parent_id IN (?)", $parent_type, $parent_ids);
            
            if($reminder_ids) {
              DB::execute("DELETE FROM $reminder_users_table WHERE reminder_id IN (?)", $reminder_ids);
              DB::execute("DELETE FROM $reminders_table WHERE id IN (?)", $reminder_ids);
            } // if
          } // foreach
        } // if
        
        DB::commit('Reminders removed by parent type and parent IDs @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::commit('Failed to delete reminders by parent type and parent IDs @ ' . __CLASS__);
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
      $reminders_table = TABLE_PREFIX . 'reminders';
    	$reminder_users_table = TABLE_PREFIX . 'reminder_users';
      
      $reminder_ids = DB::executeFirstColumn("SELECT id FROM $reminders_table WHERE parent_type IN (?)", $types);
    	
    	if($reminder_ids && count($reminder_ids)) {
    		try {
    		  DB::beginWork('Deleting reminders by parent types @ ' . __CLASS__);
    		  
    		  DB::execute("DELETE FROM $reminder_users_table WHERE reminder_id IN (?)", $reminder_ids);
    		  DB::execute("DELETE FROM $reminders_table WHERE id IN (?)", $reminder_ids);
    		  
    		  DB::commit('Reminders deleted by parent type @ ' . __CLASS__);
    		} catch(Exception $e) {
    		  DB::rollback('Failed to delete reminders by parent type @ ' . __CLASS__);
    		  throw $e;
    		} // try
    	} // if
    } // deleteByParentTypes
  	
  }
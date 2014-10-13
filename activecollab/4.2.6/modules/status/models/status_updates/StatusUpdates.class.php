<?php

  /**
   * StatusUpdates class
   * 
   * @package activeCollab.modules.status
   * @subpackage models
   */
  class StatusUpdates extends BaseStatusUpdates {

    /**
     * Returns true if $user can access status updates
     *
     * @param User $user
     * @return bool
     */
    static function canUse(User $user) {
      return $user->isAdministrator() || $user->getSystemPermission('can_use_status_updates');
    } // canUse

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return status updates created by $user
     *
     * @param User $user
     * @param integer $limit
     * @return array
     */
    static function findByUser(User $user, $limit = null) {
      $criteria = array(
        'conditions' => array('(parent_id IS NULL OR parent_id = ?) AND created_by_id = ?', 0, $user->getID()),
        'order' => 'last_update_on DESC',
      );

      if (!is_null($limit)) {
        $criteria['limit'] = $limit;
      } // if

      return StatusUpdates::find($criteria);
    } // findByUser
  
    /**
     * Return status updates that are visible to provided user
     *
     * @param User $user
     * @param integer $limit
     * @return array
     */
    static function findVisibleForUser(User $user, $limit = null) {
      $criteria = array(
    	  'conditions' => array('(parent_id IS NULL OR parent_id = ?) AND created_by_id IN (?)', 0, $user->visibleUserIds()),
    	  'order' => 'last_update_on DESC',
    	); // if
    	
    	if ($limit) {
    	  $criteria['limit'] = $limit;
    	} // if
    	
    	return StatusUpdates::find($criteria);
    } // findActiveByUser
    
    /**
     * Return messages by user ID-s
     *
     * @param array $user_ids
     * @return array
     */
    static function findByUserIds($user_ids = null) {
      $status_updates_table = TABLE_PREFIX . 'status_updates';
      $status_update_ids = array();
      
      $rows = DB::execute("SELECT id, parent_id FROM $status_updates_table WHERE created_by_id IN (?)", $user_ids);
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          if($row['parent_id'] == 0) {
            $status_update_ids[] = (integer) $row['id'];
          } else {
            $parent_id = (integer) $row['parent_id'];
            if(!in_array($parent_id, $status_update_ids)) {
              $status_update_ids[] = $parent_id;
            } // if
          } // if
        } // foreach
      } // if
      
      if(!is_foreachable($status_update_ids)) {
      	return null;
      } // if
      
      return StatusUpdates::find(array(
        'conditions' => array('id IN (?)', $status_update_ids),
        'order' => 'last_update_on DESC',
      ));
    } // findByUserIds
    
    /**
     * Return paginated status updates by user
     *
     * @param User $user
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    static function paginateByUser($user, $page = 1, $per_page = 30) {
      return StatusUpdates::paginateByUserIds(array($user->getId()), $page, $per_page);
    } // paginateByProject
    
    /**
     * Return paginated status updates for user ids
     *
     * @param array $user_ids
     * @param integer $page
     * @param integer $per_page
     * @return array
     */
    static function paginateByUserIds($user_ids = null, $page = 1, $per_page = 30) {
      $status_updates_table = TABLE_PREFIX . 'status_updates';
      $status_update_ids = array();
      
      $rows = DB::execute("SELECT id, parent_id FROM $status_updates_table WHERE created_by_id IN (?)", $user_ids);
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          if($row['parent_id'] == 0) {
            $status_update_ids[] = (integer) $row['id'];
          } else {
            $parent_id = (integer) $row['parent_id'];
            if(!in_array($parent_id, $status_update_ids)) {
              $status_update_ids[] = $parent_id;
            } // if
          } // if
        } // foreach
      } // if

      if (!is_foreachable($status_update_ids)) {
        return null;
      } // if

      return StatusUpdates::paginate(array(
        'conditions' => array('id IN (?)', $status_update_ids),
        'order' => 'last_update_on DESC',
      ), $page, $per_page);
    } // paginateByUserIds
    
    /**
     * Count new messages since date for provided user
     *
     * @param User $user
     * @return integer
     */
    static function countNewMessagesForUser(User &$user) {
      return (integer) StatusUpdates::count(array("created_by_id IN (?) AND created_on > ?", $user->visibleUserIds(), ConfigOptions::getValueFor('status_update_last_visited', $user)));
    } // countNewMessages
    
    /**
     * Find replies to given parent message
     *
     * @param StatusUpdate $parent
     * @return array
     */
    static function findByParent($parent) {
      return StatusUpdates::find(array(
        'conditions' => array('parent_id = ?', $parent->getId()),
        'order' => 'created_on'
      ));
    } // findByParent
    
    /**
     * Return number of replies for a given parent
     *
     * @param StatusUpdate $parent
     * @return integer
     */
    static function countByParent($parent) {
      return StatusUpdates::count(array('parent_id = ?', $parent->getId()));
    } // countByParent
    
    /**
     * Drop all status messages by parent
     *
     * @param StatusUpdate $parent
     * @return boolean
     */
    static function dropByParent($parent) {
      return StatusUpdates::delete(array('parent_id = ?', $parent->getId()));
    } // dropByParent
    
  }
<?php

  /**
   * Company state implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ICompanyStateImplementation extends IStateImplementation {
    
    /**
     * Move object to archive
     */
    function archive() {
      try {
        DB::beginWork('Archiving company @ ' . __CLASS__);
        
        parent::archive();

        $users = Users::findByCompany($this->object, null, STATE_VISIBLE);
        if (is_foreachable($users)) {
          $administrators_count = Users::countAdministrators(); // lets not make overhead in foreach()
          foreach ($users as $user) {
            if ($user->isAdministrator() && $administrators_count == 1) {
              throw new Exception("Cannot archive a company with last administrator in the system");
            } // if

            if ($user instanceof IActivityLogs) {
              $user->activityLogs()->gag();
            } // if
            $user->state()->archive();
          } // foreach
        } // if

        AngieApplication::cache()->removeByModel('users');
        
        DB::commit('Company archived @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to archive company @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // archive
    
    /**
     * Restore object from archive
     */
    function unarchive() {
      try {
        DB::beginWork('Unarchiving company @ ' . __CLASS__);
        
        parent::unarchive();

        $users = Users::findByCompany($this->object, null, STATE_ARCHIVED);
        if (is_foreachable($users)) {
          foreach ($users as $user) {
            if ($user->getState() == STATE_ARCHIVED) {
              if ($user instanceof IActivityLogs) {
                $user->activityLogs()->gag();
              } // if
              $user->state()->unarchive();
            } // if
          } // foreach
        } // if

        AngieApplication::cache()->removeByModel('users');
        
        DB::commit('Company unarchived @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to unarchive company @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // unarchive
    
    /**
     * Move object to trash
     *
     * @param boolean $trash_already_trashed
     */
    function trash($trash_already_trashed = false) {
      try {
        DB::beginWork('Moving company to trash @ ' . __CLASS__);
        
        parent::trash($trash_already_trashed);

        $users = Users::findByCompany($this->object, null, STATE_ARCHIVED);
        if (is_foreachable($users)) {
          foreach ($users as $user) {
            if ($user instanceof IActivityLogs) {
              $user->activityLogs()->gag();
            } // if
            $user->state()->trash(true);
          } // foreach
        } // if

        AngieApplication::cache()->removeByModel('users');
        
        DB::commit('Company moved to trash @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to move company to trash @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // trash
    
    /**
     * Restore object from trash
     */
    function untrash() {
      try {
        DB::beginWork('Restoring company from a trash @ ' . __CLASS__);
        
        parent::untrash();

        $users = Users::findByCompany($this->object, null, STATE_TRASHED);
        if (is_foreachable($users)) {
          foreach ($users as $user) {
            if ($user->getState() == STATE_TRASHED) {
              if ($user instanceof IActivityLogs) {
                $user->activityLogs()->gag();
              } // if
              $user->state()->untrash();
            } // if
          } // foreach
        } // if

        AngieApplication::cache()->removeByModel('users');
        
        DB::commit('Company restored from a trash @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to restore company from trash @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // untrash
    
    /**
     * Mark object as deleted
     */
    function delete() {
      try {
        DB::beginWork('Deleting company @ ' . __CLASS__);
        
        parent::delete();

        $users = Users::findByCompany($this->object, null, STATE_TRASHED);
        if (is_foreachable($users)) {
          foreach ($users as $user) {
            $user->state()->delete();
          } // foreach
        } // if

        AngieApplication::cache()->removeByModel('users');
        
        DB::commit('Company deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete company @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // delete
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can mark this object as archived
     *
     * @param User $user
     * @return boolean
     */
    function canArchive(User $user) {
      // companies that are not visible can't be archived
      if ($this->object->getState() < STATE_VISIBLE) {
        return false;
      } // if

      return $this->object->isOwner() ? false : $user->isPeopleManager() && ($user->getCompanyId() !== $this->object->getId());
    } // canArchive


    /**
     * Returns true if $user can mark this object as not archived
     *
     * @param User $user
     * @return boolean
     */
    function canUnarchive(User $user) {
      // cannot unarchive company which is not archived
      if ($this->object->getState() != STATE_ARCHIVED) {
        return false;
      } // if

      return $this->object->isOwner() ? false : $user->isPeopleManager();
    } // canUnarchive

	  /**
	   * Cached can delete data
	   *
	   * @var array
	   */
	  private $can_delete = array();

    /**
     * Returns true if $user can mark this object as trashed
     *
     * @param User $user
     * @return boolean
     */
    function canTrash(User $user) {
      if ($this->object->getState() == STATE_TRASHED) {
        return false;
      } // if

	    $user_id = $user->getId();

	    if(!isset($this->can_delete[$user_id])) {
		    if($this->object->isOwner() || $user->getCompanyId() == $this->object->getId()) {
			    $this->can_delete[$user_id] = false;
		    } else {
			    $has_last_admin = false;

			    $users = $this->object->users()->get($user);
			    if(is_foreachable($users)) {
				    foreach($users as $v) {
					    if($v->isLastAdministrator()) {
						    $this->can_delete[$user_id] = false;

						    $has_last_admin = true;
						    break;
					    } // if
				    } // foreach
			    } // if

			    if(!$has_last_admin) {
				    $this->can_delete[$user_id] = $user->isPeopleManager();
			    } // if
		    } // if
	    } // if

	    return $this->can_delete[$user_id];
    } // canTrash

    /**
     * Returns true if $user can mark this obejct as untrashed
     *
     * @param User $user
     * @return boolean
     */
    function canUntrash(User $user) {
      if ($this->object->getState() != STATE_TRASHED) {
        return false;
      } // if

      return $user->canManageTrash();
    } // canUntrash
    
    /**
     * Returns true if $user can mark this object as deleted
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->canManageTrash();
    } // canDelete
    
  }
<?php

  /**
   * User state implementation
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  class IUserStateImplementation extends IStateImplementation {
    
    /**
     * Returns true if $user can mark this object as archived
     *
     * @param IUser $user
     * @return boolean
     */
    function canArchive(IUser $user) {

      //don't allow to archive account owner
      if($this->object instanceof User) {
        if(AngieApplication::isOnDemand() && OnDemand::isAccountOwner($this->object)) {
          return false;
        } //if
      } //if
      if($user instanceof User) {
        if($this->object->getId() == $user->getId()) {
          return false;
        } else {
          // cannot archive object which is not visible
          if ($this->object->getState() < STATE_VISIBLE) {
            return false;
          } // if
          return $this->object->isAdministrator() ? $user->isAdministrator() : $user->isPeopleManager();
        } // if
      } // if

      return false;
    } // canArchive

    /**
     * Restore object from archive
     */
    function unarchive() {
      if(AngieApplication::isOnDemand()) {
        if(!OnDemand::canAddUsersBasedOnCurrentPlan(get_class($this->object))) {
          throw new Error(OnDemand::ERROR_USERS_LIMITATION_REACHED);
        }//if
      } //if

      parent::unarchive();
    } // unarchive

    /**
     * Restore object from trash
     */
    function untrash() {
      if(AngieApplication::isOnDemand()) {
        if(!OnDemand::canAddUsersBasedOnCurrentPlan(get_class($this->object))) {
          throw new Error(OnDemand::ERROR_USERS_LIMITATION_REACHED);
        }//if
      } //if

      parent::untrash();
    } // untrash

	  /**
	   * Cached can delete data
	   *
	   * @var array
	   */
	  private $can_delete = array();

    /**
     * Returns true if $user can mark this object as trashed
     *
     * @param IUser $user
     * @return boolean
     */
    function canTrash(IUser $user) {
	    if($user instanceof User) {

		    //don't allow to delete account owner
		    if($this->object instanceof User) {
			    if(AngieApplication::isOnDemand() && OnDemand::isAccountOwner($this->object)) {
				    return false;
			    } //if
		    } //if

		    $user_id = $user->getId();

		    if($this->object->getId() == $user_id) {
			    return false;
		    } // if

		    if($user_id) {
			    if(!array_key_exists($user_id, $this->can_delete)) {
				    if($user->getId() == $this->object->getId()) {
					    $this->can_delete[$user_id] = false;
				    } else {
					    if($this->object->isAdministrator() && !$user->isAdministrator()) {
						    $this->can_delete[$user_id] = false; // Administrators can be deleted by administrators only
					    } else {
						    if($this->object->isLastAdministrator()) {
							    $this->can_delete[$user_id] = false; // Last Administrator can't be deleted
						    } else {
							    $this->can_delete[$user_id] = $user->isPeopleManager();
						    } // if
					    } // if
				    } // if
			    } // if

			    return $this->can_delete[$user_id];
		    } else {
			    return $user->isPeopleManager();
		    } // if
	    } else {
		    return true;
	    } // if
    } // canTrash

    /**
     * Returns true if $user can mark this object as deleted
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->canManageTrash();
    } // canDelete

    /**
     * Mark as permanently
     *
     * @throws Exception
     */
    function delete() {
      try {
        DB::beginWork('Permanently deleting user @ ' . __CLASS__);

        Subscriptions::deleteByUser($this->object);
        Assignments::deleteByUser($this->object);

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'user_addresses WHERE user_id = ?', $this->object->getId());

        parent::delete();

        DB::commit('User permanently deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to permanently delete user @ ' . __CLASS__);
        throw $e;
      } // try
    } // delete
    
  }
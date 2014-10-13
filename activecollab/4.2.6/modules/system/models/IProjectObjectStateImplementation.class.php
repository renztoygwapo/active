<?php

  /**
   * Project object state helper
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectObjectStateImplementation extends IStateImplementation {
    
    /**
     * Construct project object state helper
     *
     * @param ProjectObject $object
     */
    function __construct(ProjectObject $object) {
      if($object instanceof ProjectObject) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'ProjectObject');
      } // if
    } // __construct
    
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
      // project object which is not visible cannot be archived
      if ($this->object->getState() < STATE_VISIBLE) {
        return false;
      } // if

      if($this->object instanceof IComplete && !$this->object->complete()->isCompleted()) {
        return false;
      } else {
        $project = $this->object->getProject();
        if($project instanceof Project) {
          if($user->isProjectManager() || $project->isLeader($user)) {
            return true; // Project manager, administrator or project leader
          } else {
            return $user->canSee($this->object) && ProjectObjects::canManage($user, $this->object->getProject(), $this->object->getProjectPermissionName());
          } // if
        } else {
          $this->can_delete[$user->getId()] = false;
        } // if
        
        return false;
      } // if
    } // canArchive

    /**
     * Can unarchive
     *
     * @param User $user
     * @return boolean
     */
    function canUnarchive(User $user) {
      // project object which is not archived can't be unarchived
      if ($this->object->getState() != STATE_ARCHIVED) {
        return false;
      } // if

      $project = $this->object->getProject();
      if($project instanceof Project) {
        // if project is not visible, we cannot unarchive project object
        if ($project->getState() < STATE_VISIBLE) {
          return false;
        } // if

        if($user->isProjectManager() || $project->isLeader($user)) {
          return true; // Project manager, administrator or project leader
        } else {
          return $user->canSee($this->object) && ProjectObjects::canManage($user, $this->object->getProject(), $this->object->getProjectPermissionName());
        } // if
      } else {
        $this->can_delete[$user->getId()] = false;
      } // if

      return false;
    } // canUnarchive

	  /**
	   * Cached delete permission values
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
      if($this->object->isNew()) {
        return false;
      } // if

      // object is trashed so it cannot be trashed again
      if ($this->object->getState() == STATE_TRASHED) {
        return false;
      } // if

	    $user_id = $user->getId();

	    if(!isset($this->can_delete[$user_id])) {
		    $project = $this->object->getProject();
		    if($project instanceof Project) {
			    if($user->isProjectManager() || $project->isLeader($user)) {
				    $this->can_delete[$user_id] = true; // Project manager, administrator or project leader
			    } else {
				    if($user->canSee($this->object)) {
					    if(ProjectObjects::canManage($user, $this->object->getProject(), $this->object->getProjectPermissionName())) {
						    $this->can_delete[$user_id] = true; // Can manage in given section
					    } else {
						    if($this->object->getCreatedById() == $user->getId() && (time() < ($this->object->getCreatedOn()->getTimestamp() + 10800))) {
							    $this->can_delete[$user_id] = true; // Author within first 3 hours
						    } else {
							    $this->can_delete[$user_id] = false;
						    } // if
					    } // if
				    } else {
					    $this->can_delete[$user_id] = false; // Can't see object
				    } // if
			    } // if
		    } else {
			    $this->can_delete[$user_id] = false;
		    } // if
	    } // if

      return $this->can_delete[$user_id];
    } // canTrash

	  /**
	   * Returns true if $user can mark this object as untrashed
	   *
	   * @param User $user
	   * @return bool
	   */
	  function canUntrash(User $user) {
      if($this->object->isNew()) {
        return false;
      } // if

      // object is not trashed so it cannot be untrashed
      if ($this->object->getState() != STATE_TRASHED) {
        return false;
      } // if

      $project = $this->object->getProject();

      // project is already deleted, so this project object cannot be untrashed
      if (!($project instanceof Project && $project->isLoaded())) {
        return false;
      } // if

      // project is in trash so project object cannot be untrashed
      if ($project->getState() == STATE_TRASHED) {
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

    /**
     * Trash project object
     *
     * @param boolean $trash_already_trashed
     */
    function trash($trash_already_trashed = false) {
      if ($this->object instanceof IComplete || $this->object instanceof ISubtasks) {
        ProjectProgress::dropProjectProgressCache($this->object->getProjectId());
      } // if

      return parent::trash($trash_already_trashed);
    } // trash

    /**
     * Untrash project object
     */
    function untrash() {
      if ($this->object instanceof IComplete || $this->object instanceof ISubtasks) {
        ProjectProgress::dropProjectProgressCache($this->object->getProjectId());
      } // if

      return parent::untrash();
    } // untrash
    
  }
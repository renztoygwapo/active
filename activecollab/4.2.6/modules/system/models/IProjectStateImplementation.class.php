<?php

  /**
   * Project state implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectStateImplementation extends IStateImplementation {
    
    /**
     * Construct project state implementation instance
     *
     * @param Project $object
     */
    function __construct(Project $object) {
      if($object instanceof Project) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Project');
      } // if
    } // __construct
    
    /**
     * Move object to trash
     *
     * @param boolean $trash_already_trashed
     */
    function trash($trash_already_trashed = false) {
      try {
        DB::beginWork('Moving project to trash @ ' . __CLASS__);
        
        parent::trash($trash_already_trashed);

        // trash project objects
        $project_objects = ProjectObjects::findByProject($this->object, STATE_ARCHIVED, VISIBILITY_PRIVATE);
        if (is_foreachable($project_objects)) {
          foreach ($project_objects as $project_object) {
            if ($project_object instanceof IActivityLogs) {
              $project_object->activityLogs()->gag();
            } // if
            $project_object->state()->trash(true);
          } // foreach
        } // if

        // trash expenses & time records
        if (AngieApplication::isModuleLoaded('tracking')) {
          Expenses::trashByParent($this->object);
          TimeRecords::trashByParent($this->object);
        } // if

        clean_menu_projects_and_quick_add_cache();
        
        DB::commit('Project moved to trash @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to move project to trash @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // trash
    
    /**
     * Restore object from trash
     */
    function untrash() {
      try {
        DB::beginWork('Restoring project from a trash @ ' . __CLASS__);

        parent::untrash();

        $project_objects = ProjectObjects::findByProject($this->object, STATE_TRASHED, VISIBILITY_PRIVATE);
        if (is_foreachable($project_objects)) {
          foreach ($project_objects as $project_object) {
            if ($project_object->getState() == STATE_TRASHED) {
              if ($project_object instanceof IActivityLogs) {
                $project_object->activityLogs()->gag();
              } // if
              $project_object->state()->untrash();
            } // if
          } // foreach
        } // if

        // untrash expenses & time records
        if (AngieApplication::isModuleLoaded('tracking')) {
          Expenses::untrashByParent($this->object);
          TimeRecords::untrashByParent($this->object);
        } // if

        clean_menu_projects_and_quick_add_cache();
        
        DB::commit('Project restored from a trash @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to restore project from trash @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // untrash
    
    /**
     * Mark object as deleted
     */
    function delete() {
      try {
        DB::beginWork('Deleting project @ ' . __CLASS__);
        
        parent::delete();

        $project_objects = ProjectObjects::findByProject($this->object, STATE_TRASHED, VISIBILITY_PRIVATE);
        if (is_foreachable($project_objects)) {
          foreach ($project_objects as $project_object) {
            $project_object->state()->delete();
          } // foreach
        } // if

        // untrash expenses & time records
        if (AngieApplication::isModuleLoaded('tracking')) {
          Expenses::deleteByParent($this->object, true);
          TimeRecords::deleteByParent($this->object, true);
        } // if

        clean_menu_projects_and_quick_add_cache();

        // Permanently delete categories since they are not implementing IState
        Categories::deleteByParent($this->object->getId());

        EventsManager::trigger('on_project_deleted', array($this->object));

        DB::commit('Project deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete project @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // delete
    
    /**
     * Move object to archive
     */
    function archive() {
      try {
        DB::beginWork('Archiving project @ ' . __CLASS__);

        parent::archive();

        $project_objects = ProjectObjects::findByProject($this->object, STATE_VISIBLE, VISIBILITY_PRIVATE);
        if (is_foreachable($project_objects)) {
          foreach ($project_objects as $project_object) {
            if ($project_object instanceof IActivityLogs) {
              $project_object->activityLogs()->gag();
            } // if
            $project_object->state()->archive();
          } // foreach
        } // if

        if (AngieApplication::isModuleLoaded('tracking')) {
          Expenses::archiveByParent($this->object);
          TimeRecords::archiveByParent($this->object);
        } // if

        clean_menu_projects_and_quick_add_cache();

        DB::commit('Project archived @ ' . __CLASS__);

      } catch (Exception $e) {
        DB::rollback('Failed to archive project @ ' . __CLASS__);

        throw $e;
      } // try
    } // archive
    
    /**
     * restore object from archive
     */
    function unarchive() {
      try {
        DB::beginWork('Restoring project from trash @ ' . __CLASS__);


        parent::unarchive();

        $project_objects = ProjectObjects::findByProject($this->object, STATE_ARCHIVED, VISIBILITY_PRIVATE);
        if (is_foreachable($project_objects)) {
          foreach ($project_objects as $project_object) {
            if ($project_object->getState() == STATE_ARCHIVED) {
              if ($project_object instanceof IActivityLogs) {
                $project_object->activityLogs()->gag();
              } // if
              $project_object->state()->unarchive();
            } // if
          } // foreach
        } // if

        if (AngieApplication::isModuleLoaded('tracking')) {
          Expenses::unarchiveByParent($this->object);
          TimeRecords::unarchiveByParent($this->object);
        } // if

        clean_menu_projects_and_quick_add_cache();

        DB::commit('Project restored from trash @ ' . __CLASS__);
      } catch (Exception $e) {
        DB::commit('Failed to restore project from trash @ ' . __CLASS__);

        throw $e;
      } // try
    } // archive
    
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
      // we cannot archive obejct which is not visible
      if ($this->object->getState() < STATE_VISIBLE) {
        return false;
      } // if

      return $user->isProjectManager() && $this->object->complete()->isCompleted();
    } // canArchive


    /**
     * Returns true if $user can mark this object as not archived
     *
     * @param User $user
     * @return boolean
     */
    function canUnarchive(User $user) {
      if ($this->object->getState() != STATE_ARCHIVED) {
        return false;
      } // if
      return $user->isProjectManager() && $this->object->complete()->isCompleted();
    } // canArchive
    
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

      return $user->isProjectManager();
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
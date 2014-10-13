<?php

  /**
   * State helper implementation
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class IStateImplementation {
    
    /**
     * Parent object instance
     *
     * @var IState|ApplicationObject
     */
    protected $object;
    
    /**
     * Construct state helper
     *
     * @param IState $object
     */
    function __construct(IState $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Prepare state related object options
     *
     * @param NamedList $options
     * @param User $user
     * @param string $interface
     */
    function prepareObjectOptions(NamedList $options, User $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	if ($this->object instanceof ApplicationObject) {
    		$updated_event = $this->object->getUpdatedEventName();
    		$deleted_event = $this->object->getDeletedEventName();
    	} else {
      	$updated_event = Inflector::underscore(get_class($this->object)) . '_updated';    		
      	$deleted_event = Inflector::underscore(get_class($this->object)) . '_deleted';    		
    	} // if

      if ($this->canArchive($user)) {
        $options->add('archive', array(
          'text' => lang('Move to Archive'),
          'url'  => $this->getArchiveUrl(),
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/move-to-archive.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : AngieApplication::getImageUrl('icons/navbar/archive.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to move this :object_type to archive?', array("object_type"=>$this->object->getVerboseType(true, $user->getLanguage()))),
            'success_message' => lang(':object_type has been successfully archived', array("object_type"=>$this->object->getVerboseType(false, $user->getLanguage()))),
            'success_event' => $updated_event,
          ))
        ));
      } // if

      if ($this->canUnarchive($user)) {
        $options->add('unarchive', array(
          'text' => lang('Restore from Archive'),
          'url'  => $this->getUnarchiveUrl(),
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/restore-from-archive.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : AngieApplication::getImageUrl('icons/navbar/unarchive.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
          'important' => true,
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to restore this :object_type from archive?', array("object_type"=>$this->object->getVerboseType(true, $user->getLanguage()))),
            'success_message' => lang(':object_type has been successfully restored from the archive', array("object_type"=>$this->object->getVerboseType(false, $user->getLanguage()))),
            'success_event' => $updated_event,
          )),
        ));
      } // if

      if ($this->canTrash($user)) {
        $options->add('trash', array(
          'text' => lang('Move to Trash'),
          'url'  => $this->getTrashUrl(),
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/move-to-trash.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : AngieApplication::getImageUrl('icons/navbar/trash.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to move this :object_type to trash?', array("object_type"=>$this->object->getVerboseType(true, $user->getLanguage()))),
            'success_message' => lang(':object_type has been successfully moved to trash', array("object_type"=>$this->object->getVerboseType(false, $user->getLanguage()))),
            'success_event' => $deleted_event,
          )),
        ));
      } // if

      if ($this->canUntrash($user)) {
        $options->add('untrash', array(
          'text' => lang('Restore from Trash'),
          'url'  => $this->getUntrashUrl(),
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/restore-from-trash.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : AngieApplication::getImageUrl('icons/navbar/untrash.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
          'importnat' => true,
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to restore this :object_type from trash?', array("object_type"=>$this->object->getVerboseType(true, $user->getLanguage()))),
            'success_message' => lang(':object_type has been successfully restored from trash', array("object_type"=>$this->object->getVerboseType(false, $user->getLanguage()))),
            'success_event' => $updated_event,
          )),
        ));
      } // if
    } // prepareObjectOptions
    
    /**
     * Describe state of the parent object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['permissions']['can_archive'] = false;
      $result['permissions']['can_unarchive'] = false;
      $result['permissions']['can_trash'] = false;
      $result['permissions']['can_untrash'] = false;

      if ($this->canArchive($user)) {
        $result['permissions']['can_archive'] = true;
        if($this->object instanceof IRoutingContext) {
          $result['urls']['archive'] = $this->getArchiveUrl();
        } // if
      } // if

      if ($this->canUnarchive($user)) {
        $result['permissions']['can_unarchive'] = true;
        if($this->object instanceof IRoutingContext) {
          $result['urls']['unarchive'] = $this->getUnarchiveUrl();
        } // if
      } // if

      if ($this->canTrash($user)) {
        $result['permissions']['can_trash'] = true;
        if($this->object instanceof IRoutingContext) {
          $result['urls']['trash'] = $this->getTrashUrl();
        } // if
      } // if

      if ($this->canUntrash($user)) {
        $result['permissions']['can_untrash'] = true;
        if($this->object instanceof IRoutingContext) {
          $result['urls']['untrash'] = $this->getUntrashUrl();
        } // if
      } // if

      $result['state'] = $this->object->getState();

      $result['is_archived'] = $this->object->getState() == STATE_ARCHIVED ? 1 : 0;
      $result['is_trashed'] = $this->object->getState() == STATE_TRASHED ? 1 : 0;
    } // describe

    /**
     * Describe state of the parent object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['state'] = $this->object->getState();

      if($detailed || $this->object->additionallyDescribeInBriefApiResponse('state')) {
        //$result['is_archived'] = $this->object->getState() == STATE_ARCHIVED ? 1 : 0;
        //$result['is_trashed'] = $this->object->getState() == STATE_TRASHED ? 1 : 0;

        $result['permissions']['can_archive'] = false;
        $result['permissions']['can_unarchive'] = false;
        $result['permissions']['can_trash'] = false;
        $result['permissions']['can_untrash'] = false;

        if ($this->canArchive($user)) {
          $result['permissions']['can_archive'] = true;

          if($this->object instanceof IRoutingContext) {
            $result['urls']['archive'] = $this->getArchiveUrl();
          } // if
        } // if

        if ($this->canUnarchive($user)) {
          $result['permissions']['can_unarchive'] = true;

          if($this->object instanceof IRoutingContext) {
            $result['urls']['unarchive'] = $this->getUnarchiveUrl();
          } // if
        } // if

        if ($this->canTrash($user)) {
          $result['permissions']['can_trash'] = true;

          if($this->object instanceof IRoutingContext) {
            $result['urls']['trash'] = $this->getTrashUrl();
          } // if
        } // if

        if ($this->canUntrash($user)) {
          $result['permissions']['can_untrash'] = true;

          if($this->object instanceof IRoutingContext) {
            $result['urls']['untrash'] = $this->getUntrashUrl();
          } // if
        } // if
      } // if
    } // describeForApi
    
    /**
     * Move object to archive
     *
     * @throws NotImplementedError
     */
    function archive() {
      if($this->object->getState() == STATE_VISIBLE) {
        try {
          DB::beginWork('Moving object to archive @ ' . __CLASS__);
          
          $this->object->setOriginalState(STATE_VISIBLE);
          $this->object->setState(STATE_ARCHIVED);

          if($this->object instanceof ISubtasks) {
            Subtasks::archiveByParent($this->object);
          } // if

          if($this->object instanceof IComments) {
            Comments::archiveByParent($this->object);
          } // if

          if($this->object instanceof IAttachments) {
            Attachments::archiveByParent($this->object);
          } // if
          
          $this->object->save();

          if($this->object instanceof IActivityLogs) {
            if (!$this->object->activityLogs()->isGagged()) {
              $this->object->activityLogs()->logMoveToArchive(Authentication::getLoggedUser());
            } else {
              $this->object->activityLogs()->ungag();
            } // if
          } // if
          
          DB::commit('Object moved to archive @ ' . __CLASS__);

          AngieApplication::cache()->clear();
        } catch(Exception $e) {
          DB::rollback('Failed to move object to archive @ ' . __CLASS__);
          
          throw $e;
        } // try
      } else {
        throw new NotImplementedError(__METHOD__, 'Only visible objects can be archived');
      } // if
    } // archive
    
    /**
     * Restore object from archive
     *
     * @throws NotImplementedError
     */
    function unarchive() {
      if($this->object->getState() === STATE_ARCHIVED) {
        try {
          DB::beginWork('Restoring object from archive @ ' . __CLASS__);
          
          $this->object->setState(STATE_VISIBLE);
          $this->object->setOriginalState(null);

          if($this->object instanceof ISubtasks) {
            Subtasks::unarchiveByParent($this->object);
          } // if

          if($this->object instanceof IComments) {
            Comments::unarchiveByParent($this->object);
          } // if

          if($this->object instanceof IAttachments) {
            Attachments::unarchiveByParent($this->object);
          } // if
          
          $this->object->save();

          if($this->object instanceof IActivityLogs) {
            if (!$this->object->activityLogs()->isGagged()) {
              $this->object->activityLogs()->logRestoreFromArchive(Authentication::getLoggedUser());
            } else {
              $this->object->activityLogs()->ungag();
            } // if
          } // if
          
          DB::commit('Object restored from archive @ ' . __CLASS__);

          AngieApplication::cache()->clear();
        } catch(Exception $e) {
          DB::rollback('Failed to restore object from archive @ ' . __CLASS__);
          
          throw $e;
        } // try
      } else {
        throw new NotImplementedError(__METHOD__, 'Only objects marked as archived can be restored from archive');
      } // if
    } // unarchive
    
    /**
     * Move object to trash
     *
     * @param Boolean $trash_already_trashed
     * @throws NotImplementedError
     * @throws Exception
     */
    function trash($trash_already_trashed = false) {
      $original_state = $this->object->getState();
      
      if($trash_already_trashed || $original_state > STATE_TRASHED) {
        try {
          DB::beginWork('Moving object to trash @ ' . __CLASS__);
          
          $this->object->setOriginalState($original_state);
          $this->object->setState(STATE_TRASHED);

          if($this->object instanceof ISubtasks) {
            Subtasks::trashByParent($this->object);
          } // if

          if($this->object instanceof IComments) {
            Comments::trashByParent($this->object);
          } // if

          if($this->object instanceof IAttachments) {
            Attachments::trashByParent($this->object);
          } // if
          
          $this->object->save();

          if($this->object instanceof IActivityLogs) {
            if (!$this->object->activityLogs()->isGagged()) {
              $this->object->activityLogs()->logMoveToTrash(Authentication::getLoggedUser());
            } else {
              $this->object->activityLogs()->ungag();
            } // if
          } // if
          
          DB::commit('Object moved to trash @ ' . __CLASS__);

          AngieApplication::cache()->clear();
        } catch(Exception $e) {
          DB::rollback('Faield to move object to trash @ ' . __CLASS__);
          
          throw $e;
        } // try
      } else {
        throw new NotImplementedError(__METHOD__, 'Only visible and archived objects can be moved to trash');
      } // if
    } // trash
    
    /**
     * Restore object from trash
     *
     * @throws NotImplementedError
     */
    function untrash() {
      $current_state = $this->object->getState();
      $original_state = $this->object->getOriginalState();
      
      if($current_state === STATE_TRASHED) {
        try {
          DB::beginWork('Restoring object from trash @ ' . __CLASS__);

          if ($original_state == $current_state && $original_state == STATE_TRASHED) {
            $new_state = STATE_TRASHED;
          } else {
            $new_state = !$original_state ? STATE_VISIBLE : $original_state;
          } // if

          $this->object->setState($new_state);
          $this->object->setOriginalState(null);

          if($this->object instanceof ISubtasks) {
            Subtasks::untrashByParent($this->object);
          } // if

          if($this->object instanceof IComments) {
            Comments::untrashByParent($this->object);
          } // if

          if($this->object instanceof IAttachments) {
            Attachments::untrashByParent($this->object);
          } // if
          
          $this->object->save();

          if($this->object instanceof IActivityLogs) {
            if (!$this->object->activityLogs()->isGagged()) {
              $this->object->activityLogs()->logRestoreFromTrash(Authentication::getLoggedUser());
            } else {
              $this->object->activityLogs()->ungag();
            } // if
          } // if
          
          DB::commit('Object restored from trash @ ' . __CLASS__);

          AngieApplication::cache()->clear();
        } catch(Exception $e) {
          DB::rollback('Failed to restore object from trash @ ' . __CLASS__);
          
          throw $e;
        } // try
      } else {
        throw new NotImplementedError(__METHOD__, 'Only objects marked as trashed can be restored from trash');
      } // if
    } // untrash
    
    /**
     * Mark object as deleted
     *
     * @throws NotImplementedError
     */
    function delete() {
      $original_state = $this->object->getState();
      
      if($original_state > STATE_DELETED) {
        try {
          DB::beginWork('Softly delete object @ ' . __CLASS__);
          
          $this->object->setOriginalState($original_state);
          $this->object->setState(STATE_DELETED);

          if($this->object instanceof ISubtasks) {
            Subtasks::deleteByParent($this->object, true);
          } // if

          if($this->object instanceof IComments) {
            Comments::deleteByParent($this->object, true);
          } // if

          if($this->object instanceof IAttachments) {
            Attachments::deleteByParent($this->object, true);
          } // if

          if($this->object instanceof IReminders) {
            Reminders::deleteByParent($this->object);
          } // if

          if ($this->object instanceof IActivityLogs) {
            ActivityLogs::deleteByParent($this->object);
          } // if
          
          $this->object->save();
        
          DB::commit('Object softly deleted @ ' . __CLASS__);

          AngieApplication::cache()->clear();
        } catch(Exception $e) {
          DB::rollback('Failed to softly delete object @ ' . __CLASS__);
          
          throw $e;
        } // try
      } else {
        throw new NotImplementedError(__METHOD__, 'Objects that are already marked as deleted cannot be deleted again');
      } // if
    } // delete
    
    // ---------------------------------------------------
    //  Subitems
    // ---------------------------------------------------
    
    /**
     * Archive subitems
     *
     * @param string $table_name
     * @param string $conditions
     * @param boolean $update_updated_on_fields
     */
    function archiveSubitems($table_name, $conditions = null, $update_updated_on_fields = false) {
      $conditions = $conditions ? ' AND (' . DB::prepareConditions($conditions) . ')' : '';

      if(!$update_updated_on_fields) {
        DB::execute("UPDATE $table_name SET $table_name.state = ?, $table_name.original_state = ? WHERE $table_name.state = ? $conditions", STATE_ARCHIVED, STATE_VISIBLE, STATE_VISIBLE);
      } else {
        $logged_user = Authentication::getLoggedUser();
        if($logged_user instanceof IUser) {
          DB::execute("UPDATE $table_name SET $table_name.state = ?, $table_name.original_state = ?, $table_name.updated_on = ?, $table_name.updated_by_id = ?, $table_name.updated_by_name = ?, $table_name.updated_by_email = ? WHERE $table_name.state = ? $conditions", STATE_ARCHIVED, STATE_VISIBLE, DateTimeValue::now(), $logged_user->getId(), $logged_user->getName(), $logged_user->getEmail(), STATE_VISIBLE);
        } // if
      } // if
    } // archiveSubitems
    
    /**
     * Restore subitems from archive
     *
     * @param string $table_name
     * @param string $conditions
     * @param boolean $update_updated_on_fields
     */
    function unarchiveSubitems($table_name, $conditions = null, $update_updated_on_fields = false) {
      $conditions = $conditions ? ' AND (' . DB::prepareConditions($conditions) . ')' : '';

      if(!$update_updated_on_fields) {
        DB::execute("UPDATE $table_name SET $table_name.state = ?, $table_name.original_state = ? WHERE $table_name.state = ? AND $table_name.original_state = ? $conditions", STATE_VISIBLE, null, STATE_ARCHIVED, STATE_VISIBLE);
      } else {
        $logged_user = Authentication::getLoggedUser();
        if($logged_user instanceof IUser) {
          DB::execute("UPDATE $table_name SET $table_name.state = ?, $table_name.original_state = ?, $table_name.updated_on = ?, $table_name.updated_by_id = ?, $table_name.updated_by_name = ?, $table_name.updated_by_email = ? WHERE $table_name.state = ? AND $table_name.original_state = ? $conditions", STATE_VISIBLE, null, DateTimeValue::now(), $logged_user->getId(), $logged_user->getName(), $logged_user->getEmail(), STATE_ARCHIVED, STATE_VISIBLE);
        } // if
      } // if
    } // unarchiveSubitems
    
    /**
     * Move subitems to trash
     *
     * @param string $table_name
     * @param string $conditions
     * @param boolean $update_updated_on_fields
     */
    function trashSubitems($table_name, $conditions = null, $update_updated_on_fields = false) {
      $conditions = $conditions ? ' AND (' . DB::prepareConditions($conditions) . ')' : '';

      if(!$update_updated_on_fields) {
        DB::execute("UPDATE $table_name SET $table_name.original_state = $table_name.state, $table_name.state = ? WHERE $table_name.state >= ? $conditions", STATE_TRASHED, STATE_TRASHED);
      } else {
        $logged_user = Authentication::getLoggedUser();
        if($logged_user instanceof IUser) {
          DB::execute("UPDATE $table_name SET $table_name.original_state = $table_name.state, $table_name.state = ?, $table_name.updated_on = ?, $table_name.updated_by_id = ?, $table_name.updated_by_name = ?, $table_name.updated_by_email = ? WHERE $table_name.state >= ? $conditions", STATE_TRASHED, DateTimeValue::now(), $logged_user->getId(), $logged_user->getName(), $logged_user->getEmail(), STATE_TRASHED);
        } // if
      } // if
    } // trashSubitems
    
    /**
     * Restore subitems from trash
     *
     * @param string $table_name
     * @param string $conditions
     * @param boolean $update_updated_on_fields
     */
    function untrashSubitems($table_name, $conditions = null, $update_updated_on_fields = false) {
      $conditions = $conditions ? ' AND (' . DB::prepareConditions($conditions) . ')' : '';

      if(!$update_updated_on_fields) {
        DB::execute("UPDATE $table_name SET $table_name.state = ?, $table_name.original_state = ? WHERE $table_name.state = ? AND $table_name.original_state = ? $conditions", STATE_ARCHIVED, STATE_VISIBLE, STATE_TRASHED, STATE_ARCHIVED);
        DB::execute("UPDATE $table_name SET $table_name.state = ?, $table_name.original_state = ? WHERE $table_name.state = ? AND $table_name.original_state = ? $conditions", STATE_VISIBLE, null, STATE_TRASHED, STATE_VISIBLE);
      } else {
        $logged_user = Authentication::getLoggedUser();
        if($logged_user instanceof IUser) {
          DB::execute("UPDATE $table_name SET $table_name.state = ?, $table_name.original_state = ?, $table_name.updated_on = ?, $table_name.updated_by_id = ?, $table_name.updated_by_name = ?, $table_name.updated_by_email = ? WHERE $table_name.state = ? AND $table_name.original_state = ? $conditions", STATE_ARCHIVED, STATE_VISIBLE, DateTimeValue::now(), $logged_user->getId(), $logged_user->getName(), $logged_user->getEmail(), STATE_TRASHED, STATE_ARCHIVED);
          DB::execute("UPDATE $table_name SET $table_name.state = ?, $table_name.original_state = ?, $table_name.updated_on = ?, $table_name.updated_by_id = ?, $table_name.updated_by_name = ?, $table_name.updated_by_email = ? WHERE $table_name.state = ? AND $table_name.original_state = ? $conditions", STATE_VISIBLE, null, DateTimeValue::now(), $logged_user->getId(), $logged_user->getName(), $logged_user->getEmail(), STATE_TRASHED, STATE_VISIBLE);
        } // if
      } // if
    } // untrashSubitems
    
    /**
     * Mark subitems as deleted
     *
     * @param string $table_name
     * @param string $conditions
     * @param boolean $update_updated_on_fields
     */
    function deleteSubitems($table_name, $conditions = null, $update_updated_on_fields = false) {
      $conditions = $conditions ? ' AND (' . DB::prepareConditions($conditions) . ')' : '';

      if(!$update_updated_on_fields) {
        DB::execute("UPDATE $table_name SET $table_name.original_state = $table_name.state, $table_name.state = ? WHERE $table_name.state >= ? $conditions", STATE_DELETED, STATE_ARCHIVED);
        DB::execute("UPDATE $table_name SET $table_name.state = ? WHERE $table_name.state = ? $conditions", STATE_DELETED, STATE_TRASHED);
      } else {
        $logged_user = Authentication::getLoggedUser();
        if($logged_user instanceof IUser) {
          DB::execute("UPDATE $table_name SET $table_name.original_state = $table_name.state, $table_name.state = ?, $table_name.updated_on = ?, $table_name.updated_by_id = ?, $table_name.updated_by_name = ?, $table_name.updated_by_email = ? WHERE $table_name.state >= ? $conditions", STATE_DELETED, DateTimeValue::now(), $logged_user->getId(), $logged_user->getName(), $logged_user->getEmail(), STATE_ARCHIVED);
          DB::execute("UPDATE $table_name SET $table_name.state = ?, $table_name.updated_on = ?, $table_name.updated_by_id = ?, $table_name.updated_by_name = ?, $table_name.updated_by_email = ? WHERE $table_name.state = ? $conditions", STATE_DELETED, DateTimeValue::now(), $logged_user->getId(), $logged_user->getName(), $logged_user->getEmail(), STATE_TRASHED);
        } // if
      } // if
    } // deleteSubitems
    
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
      // cannot archive object which is not visible
      if ($this->object->getState() < STATE_VISIBLE) {
        return false;
      } // if

      if($this->object->canEdit($user)) {
        return $this->object instanceof IComplete ? $this->object->complete()->isCompleted() : true;
      } // if
      
      return false;
    } // canArchive

    /**
     * Returns true if $user can mark this object as not archived
     *
     * @param User $user
     * @return boolean
     */
    function canUnarchive(User $user) {
      // cannot unarchive object which is not archived
      if ($this->object->getState() != STATE_ARCHIVED) {
        return false;
      } // if

      // check if parent object is visible
      if ($this->object->fieldExists('parent_id') && $this->object->fieldExists('parent_type')) {
        $parent_object = DataObjectPool::get($this->object->getParentType(), $this->object->getParentId());

        // parent object does not exist
        if (!$parent_object || $parent_object->isNew()) {
          return false;
        } // if

        // parent object is trashed
        if ($parent_object instanceof IState && $parent_object->getState() < STATE_VISIBLE) {
          return false;
        } // if
      } // if

      return $this->object->canEdit($user);
    } // canUnarchive
    
    /**
     * Returns true if $user can mark this object as trashed
     *
     * @param User $user
     * @return boolean
     */
    function canTrash(User $user) {
      // object is already trashed so it cannot be trashed again
      if ($this->object->getState() == STATE_TRASHED) {
        return false;
      } // if

      return $this->object->canEdit($user);
    } // canTrash

    /**
     * Returns true if $user can mark this object as untrashed
     *
     * @param User $user
     * @return bool
     */
    function canUntrash(User $user) {
      // object is not trashed so it cannot be untrashed
      if ($this->object->getState() != STATE_TRASHED) {
        return false;
      } // if

      // check if parent object is trashed
      if ($this->object->fieldExists('parent_id') && $this->object->fieldExists('parent_type')) {
        $parent_object = DataObjectPool::get($this->object->getParentType(), $this->object->getParentId());

        // parent object does not exist
        if (!$parent_object || $parent_object->isNew()) {
          return false;
        } // if

        // parent object is trashed
        if ($parent_object instanceof IState && $parent_object->getState() == STATE_TRASHED) {
          return false;
        } // if
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
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return archive object URL
     *
     * @return string
     */
    function getArchiveUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_archive', $this->object->getRoutingContextParams());
    } // getArchiveUrl
    
    /**
     * Return unarchive object URL
     *
     * @return string
     */
    function getUnarchiveUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_unarchive', $this->object->getRoutingContextParams());
    } // getUnarchiveUrl
    
    /**
     * Return trash object URL
     *
     * @return string
     */
    function getTrashUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_trash', $this->object->getRoutingContextParams());
    } // getTrashUrl
    
    /**
     * Return untrash object URL
     *
     * @return string
     */
    function getUntrashUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_untrash', $this->object->getRoutingContextParams());
    } // getUntrashUrl
    
    /**
     * Return delete object URL
     *
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_delete', $this->object->getRoutingContextParams());
    } // getDeleteUrl
    
  }
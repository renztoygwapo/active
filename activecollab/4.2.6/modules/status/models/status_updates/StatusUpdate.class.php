<?php

  /**
   * StatusUpdate class
   *
   * @package activeCollab.modules.status
   * @subpackage models
   */
  class StatusUpdate extends BaseStatusUpdate {
    
    /**
     * Cached array of replies
     *
     * @var array
     */
    var $replies = false;
    
    /**
     * Return array of status message replies
     *
     * @param void
     * @return array
     */
    function getReplies() {
      if($this->replies === false) {
        $this->replies = StatusUpdates::findByParent($this);
      } // if
      return $this->replies;
    } // getReplies
    
    /**
     * Returns true if this message has replies
     *
     * @param boolean $preload
     * @return boolean
     */
    function hasReplies($preload = false) {
      if($preload) {
        $this->getReplies();
        return is_foreachable($this->replies);
      } else {
        return (boolean) StatusUpdates::countByParent($this);
      } // if
    } // hasReplies
    
    /**
     * Parent message
     *
     * @var StatusUpdate
     */
    var $parent = false;
    
    /**
     * Return parent update instance
     *
     * @return StatusUpdate
     */
    function getParent() {
      if($this->parent === false) {
        $this->parent = $this->getParentId() ? StatusUpdates::findById($this->getParentId()) : null;
      } // if
      return $this->parent;
    } // getParent
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      AngieApplication::useHelper('clickable', ENVIRONMENT_FRAMEWORK, 'modifier');

      $result = parent::describe($user, $detailed, $for_interface);
      unset($result['name']);

      $result['message'] = $this->getMessage();
      $result['message_clickable'] = smarty_modifier_clickable(clean($this->getMessage()));
      $result['created_by'] = $this->getCreatedBy() instanceof IUser ? $this->getCreatedBy()->describe($user, !$this->getParentId(), false) : null;
      $result['parent_id'] = $this->getParentId();
      $result['urls']['delete'] = $this->getDeleteUrl();
      $result['replies'] = $this->getReplies();

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      AngieApplication::useHelper('clickable', ENVIRONMENT_FRAMEWORK, 'modifier');

      $result = parent::describeForApi($user, $detailed);
      unset($result['name']);

      $result['message'] = $this->getMessage();
      $result['message_clickable'] = smarty_modifier_clickable($this->getMessage());
      $result['created_by'] = $this->getCreatedBy()->describeForApi($user);
      $result['parent_id'] = $this->getParentId();
      $result['urls']['delete'] = $this->getDeleteUrl();
      $result['replies'] = $this->getReplies();

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Check if $user can view this message
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $this->getCreatedById() == $user->getId() || in_array($this->getCreatedById(), $user->visibleUserIds());
    } // canView

    /**
     * Check if $user can edit this message
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      if($user->isAdministrator()) {
        return true;
      } // if

      if($this->getCreatedById() == $user->getId()) {
        $created_on = $this->getCreatedOn();
        return $created_on->getTimestamp() + 1800 < time(); // Available for edit for 30 minutes after the post
      } // if

      return false;
    } // canEdit
    
    /**
     * Check if $user can delete this message
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $this->canEdit($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * View status update url
     *
     * @param null
     * @return string
     */
    function getViewUrl() {
      return Router::assemble('status_update', array(
        'status_update_id' => $this->getParentId() ? $this->getParentId() : $this->getId()
      ));
    } // getViewUrl
    
    /**
     * Get reply to status update URL
     *
     * @param void
     * @return string
     */
    function getReplyUrl() {
      return Router::assemble('status_update_reply', array(
        'status_update_id' => $this->getParentId() ? $this->getParentId() : $this->getId()
      ));
    } // getReplyUrl

    /**
     * Get the URL for deleting the status update
     *
     * @return string
     */
    function getEditUrl() {
      return '#';
    } // getDeleteUrl
    
    /**
     * Get the URL for deleting the status update
     *
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble('status_update_delete', array(
        'status_update_id' => $this->getId()
      ));
    } // getDeleteUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     * @return null
     */
    function validate(&$errors) {
      if(!$this->validatePresenceOf('message')) {
        $errors->addError(lang('Status message is required'), 'message');
      } // if
    } // validate
    
    /**
     * Save this status message into database
     *
     * @param void
     * @return boolean
     */
    function save() {
      $now = new DateTimeValue();
      if($this->isNew()) {
        $this->setLastUpdateOn($now);
      } // if
      
      DB::beginWork();
      
      $save = parent::save();
      if($save && !is_error($save)) {
        $parent = $this->getParent();
        if($parent instanceof StatusUpdate) {
          $parent->setLastUpdateOn($now);
          $parent->save();
        } // if
        
        DB::commit();
        return true;
      } else {
        DB::rollback();
        return $save;
      } // if
    } // save
    
    /**
     * Drop this record from database
     *
     * @param void
     * @return boolean
     */
    function delete() {
      StatusUpdates::dropByParent($this);
      return parent::delete();
    } // delete

  }
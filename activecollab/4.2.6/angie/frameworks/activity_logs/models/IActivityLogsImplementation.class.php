<?php

  /**
   * Activity logs for a given object
   *
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  class IActivityLogsImplementation {
    
    /**
     * Parent object
     *
     * @var IActivityLogs
     */
    protected $object;
    
    /**
     * Gag indicator
     * 
     * When implementation is gagged, log events that are automatically called 
     * will not create new entries 
     *
     * @var unknown_type
     */
    protected $is_gagged = false;
    
    /**
     * Construct activity logs implementation instance
     *
     * @param IActivityLogs $object
     */
    function __construct(IActivityLogs $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Log creation
     * 
     * @param IUser $by
     * @return ActivityLog
     */
    function logCreation(IUser $by) {
      return ActivityLogs::log($this->object, $this->getActionString('created'), $by, $this->getTarget('created'), $this->getComment('created'));
    } // logCreation
    
    /**
     * Log update
     * 
     * @param IUser $by
     * @param array $modifications
     * @return ActivityLog
     */
    function logUpdate(IUser $by, $modifications = null) {
      
    } // logUpdate
    
    /**
     * Log object completion
     * 
     * @param IUser $by
     * @return ActivityLog
     */
    function logCompletion(IUser $by) {
      if($this->object instanceof IComplete) {
        return ActivityLogs::log($this->object, $this->getActionString('completed'), $by, $this->getTarget('completed'), $this->getComment('completed'));
      } else {
        throw new InvalidInstanceError('parent', $this->object, 'IComplete');
      } // if
    } // logCompletion
    
    /**
     * Log object reopening
     * 
     * @param IUser $by
     * @return ActivityLog
     */
    function logReopening(IUser $by) {
      if($this->object instanceof IComplete) {
        return ActivityLogs::log($this->object, $this->getActionString('reopened'), $by, $this->getTarget('reopened'), $this->getComment('reopened'));
      } else {
        throw new InvalidInstanceError('parent', $this->object, 'IComplete');
      } // if
    } // logReopening
    
    /**
     * Log move to archive
     * 
     * @param IUser $by
     * @return AccessLog
     */
    function logMoveToArchive(IUser $by) {
      if($this->object instanceof IState) {
        return ActivityLogs::log($this->object, $this->getActionString('moved_to_archive'), $by, $this->getTarget('moved_to_archive'), $this->getComment('moved_to_archive'));
      } else {
        throw new InvalidInstanceError('parent', $this->object, 'IState');
      } // if
    } // logMoveToArchive
    
    /**
     * Log restore from archive
     * 
     * @param IUser $by
     * @return AccessLog
     */
    function logRestoreFromArchive(IUser $by) {
      if($this->object instanceof IState) {
        return ActivityLogs::log($this->object, $this->getActionString('restored_from_archive'), $by, $this->getTarget('restored_from_archive'), $this->getComment('restored_from_archive'));
      } else {
        throw new InvalidInstanceError('parent', $this->object, 'IState');
      } // if
    } // logRestoreFromArchive
    
    /**
     * Log move to trash
     * 
     * @param IUser $by
     * @return AccessLog
     */
    function logMoveToTrash(IUser $by) {
      if($this->object instanceof IState) {
        return ActivityLogs::log($this->object, $this->getActionString('moved_to_trash'), $by, $this->getTarget('moved_to_trash'), $this->getComment('moved_to_trash'));
      } else {
        throw new InvalidInstanceError('parent', $this->object, 'IState');
      } // if
    } // logMoveToTrash
    
    /**
     * Log restore from trash
     * 
     * @param IUser $by
     * @return AccessLog
     */
    function logRestoreFromTrash(IUser $by) {
      if($this->object instanceof IState) {
        return ActivityLogs::log($this->object, $this->getActionString('restored_from_trash'), $by, $this->getTarget('restored_from_trash'), $this->getComment('restored_from_trash'));
      } else {
        throw new InvalidInstanceError('parent', $this->object, 'IState');
      } // if
    } // logRestoreFromTrash
    
    // ---------------------------------------------------
    //  Type specific
    // ---------------------------------------------------
    
    /**
     * Return full action string
     * 
     * @param string $action
     * @return string
     */
    function getActionString($action) {
      return $this->object->getBaseTypeName() . '/' . $action;
    } // getActionString
    
    /**
     * Return target for given action
     * 
     * @param string $action
     * @return ApplicationObject
     */
    function getTarget($action = null) {
      return null;
    } // getTarget
    
    /**
     * Return comment string for this activity log type
     * 
     * @param string $action
     * @return string
     */
    function getComment($action = null) {
      return null;
    } // getComment
    
    // ---------------------------------------------------
    //  Gagging
    // ---------------------------------------------------
    
    /**
     * Returns true if this implementation is gagged
     * 
     * @return boolean
     */
    function isGagged() {
      return $this->is_gagged;
    } // isGagged
    
    /**
     * Set gag flag to true
     */
    function gag() {
      $this->is_gagged = true;
    } // gag
    
    /**
     * Set gag to false
     */
    function ungag() {
      $this->is_gagged = false;
    } // ungag

    // ---------------------------------------------------
    //  Describe for log
    // ---------------------------------------------------

    /**
     * Describe for log
     *
     * @param IUser $user
     * @return array
     */
    function describeForLog(IUser $user) {
      $language = $user->getLanguage();

      return array(
        'name' => $this->object->getName(),
        'verbose_type' => $this->object->getVerboseType(false, $language),
        'verbose_type_lowercase' => $this->object->getVerboseType(true, $language),
        'urls' => array(
          'view' => $this->object->getViewUrl()
        ),
        'permalink' => $this->object->getViewUrl()
      );
    } // describeForLog
    
  }
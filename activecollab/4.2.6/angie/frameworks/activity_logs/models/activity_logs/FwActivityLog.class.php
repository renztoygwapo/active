<?php

  /**
   * Framework level activity log row
   *
   * @package angie.frameworks.activity_logs
   * @subpackage models
   */
  abstract class FwActivityLog extends BaseActivityLog {
    
    /**
     * Cached log subject
     *
     * @var ApplicationObject
     */
    private $subject = false;
    
    /**
     * Return log subject
     * 
     * @return ApplicationObject
     */
    function getSubject() {
      if($this->subject === false) {
        $subject_class = $this->getSubjectType();
        
        if(empty($subject_class)) {
          $this->subject = null;
        } else {
          $this->subject = new $subject_class($this->getSubjectId());
        } // if
      } // if
      
      return $this->subject;
    } // getSubject
    
    /**
     * Set log subject
     * 
     * @param ApplicationObject $subject
     * @param boolean $save
     * @return ApplicationObject
     * @throws InvalidInstanceError
     */
    function setSubject($subject, $save = false) {
      if($subject instanceof ApplicationObject) {
        $this->setSubjectType(get_class($subject));
        $this->setSubjectId($subject->getId());
        
        $this->subject = $subject;
      } elseif($subject === null) {
        $this->setSubjectType(null);
        $this->setSubjectId(null);
        
        $this->subject = null;
      } else {
        throw new InvalidInstanceError('subject', $subject, 'ApplicationObject');
      } // if
      
      if($save) {
        $this->save();
      } // if
      
      return $subject;
    } // setSubject
    
    /**
     * Cached log target
     *
     * @var ApplicationObject
     */
    private $target = false;
    
    /**
     * Return log target
     * 
     * @return ApplicationObject
     */
    function getTarget() {
      if($this->target === false) {
        $target_class = $this->getTargetType();
        
        if(empty($target_class)) {
          $this->target = null;
        } else {
          $this->target = new $target_class($this->getTargetId());
        } // if
      } // if
      
      return $this->target;
    } // getTarget
    
    /**
     * Set log target
     * 
     * @param mixed $target
     * @param boolean $save
     * @return mixed
     * @throws InvalidInstanceError
     */
    function setTarget($target, $save = false) {
      if($target instanceof ApplicationObject) {
        $this->setTargetType(get_class($target));
        $this->setTargetId($target->getId());
        
        $this->target = $target;
      } elseif($target === null) {
        $this->setTargetType(null);
        $this->setTargetId(null);
        
        $this->target = null;
      } else {
        throw new InvalidInstanceError('target', $target, 'ApplicationObject');
      } // if
      
      if($save) {
        $this->save();
      } // if
      
      return $target;
    } // setTarget
    
    /**
     * Check if this log is new since $user last visited the system
     *
     * @param User $user
     * @return boolean
     */
    function isNewSinceLastVisit(User $user) {
      if($this->getCreatedById() == $user->getId()) {
        return false; // Not new since $user logged the action
      } // if
      
      if($user->getLastVisitOn() instanceof DateTimeValue) {
        return $this->getCreatedOn()->getTimestamp() > $user->getLastVisitOn()->getTimestamp();
      } else {
        return true;
      } // if
    } // isNewSinceLastVisit
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view URL for this log entry
     * 
     * @return string
     */
    function getViewUrl() {
      return $this->getSubject() instanceof ApplicationObject ? $this->getSubject()->getViewUrl() : '#';
    } // getViewUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
   
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('subject_type') || !$this->validatePresenceOf('subject_id')) {
        $errors->addError(lang('Subject is required'));
      } // if
      
      if(!$this->validatePresenceOf('action')) {
        $errors->addError(lang('Action is required'));
      } // if
    } // validate
    
  }
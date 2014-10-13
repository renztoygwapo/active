<?php

  /**
   * Estimate class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class Estimate extends BaseEstimate {
    
    /**
     * Return job type
     * 
     * @return JobType
     */
    function getJobType() {
      return DataObjectPool::get('JobType', $this->getJobTypeId());
    } // getJobType
    
    /**
     * Set job type
     * 
     * @param JobType $job_type
     * @return JobType
     */
    function setJobType(JobType $job_type) {
      if($job_type instanceof JobType) {
        $this->setJobTypeId($job_type->getId());
      } else {
        throw new InvalidInstanceError('job_type', $job_type, 'JobType');
      } // if
    } // setJobType
    
    /**
     * Return job type name
     * 
     * @return string
     */
    function getJobTypeName() {
      return $this->getJobType() instanceof JobType ? $this->getJobType()->getName() : lang('Unknown');
    } // getJobTypeName
    
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
      $result = parent::describe($user, $detailed, $for_interface);
      
      $result['parent'] = $this->getParent()->describe($user, $detailed, $for_interface);
      $result['value'] = $this->getValue();
      $result['job_type'] = array(
        'id' => $this->getJobType() instanceof JobType ? $this->getJobType()->getId() : null, 
        'name' => $this->getJobType() instanceof JobType ? $this->getJobType()->getName() : null, 
      );
      $result['comment'] = $this->getComment();
      
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
      $result = parent::describeForApi($user, $detailed);

      $result['parent'] = $this->getParent()->describe($user, $detailed);
      $result['value'] = $this->getValue();
      $result['job_type'] = array(
        'id' => $this->getJobType() instanceof JobType ? $this->getJobType()->getId() : null,
        'name' => $this->getJobType() instanceof JobType ? $this->getJobType()->getName() : null,
      );
      $result['comment'] = $this->getComment();

      return $result;
    } // describe
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('parent_type') || !$this->validatePresenceOf('parent_id')) {
        $errors->addError('Estimate parent is required', 'parent');
      } // if
      
      if(!$this->validatePresenceOf('job_type_id')) {
        $errors->addError('Job type is required', 'job_type_id');
      } // if
    } // validate

    /**
     * Save estimate into the database
     *
     * @throws DBQueryError
     * @throws ValidationErrors
     */
    function save() {
      if($this->fieldExists('job_type_id') && $this->isModifiedField('job_type_id')) {
        $job_type = JobTypes::findById($this->getFieldValue('job_type_id'));

        if(!($job_type instanceof JobType) || !$job_type->getIsActive()) {
          $this->setJobTypeId(JobTypes::getDefaultJobTypeId());
        } // if
      } // if

      return parent::save();
    } // save
    
  }
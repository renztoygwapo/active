<?php

  /**
   * TimeRecord class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class TimeRecord extends BaseTimeRecord implements IRoutingContext {
    
    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? 
        lang('time record', null, true, $language) : 
        lang('Time Record', null, true, $language);
    } // getVerboseType
    
    /**
     * Return time record job type
     * 
     * @return JobType
     */
    function getJobType() {
      return DataObjectPool::get('JobType', $this->getJobTypeId());
    } // getJobType
    
    /**
     * Return name of the job type
     * 
     * @return string
     */
    function getJobTypeName() {
      return $this->getJobType() instanceof JobType ? $this->getJobType()->getName() : JobTypes::getNameById($this->getJobTypeId());
    } // getJobTypeName
    
    /**
     * Set job type for a given time record
     * 
     * @param JobType $job_type
     * @throws InvalidInstanceError
     */
    function setJobType(JobType $job_type) {
      if($job_type instanceof JobType) {
        $this->setJobTypeId($job_type->getId());
      } else {
        throw new InvalidInstanceError('job_type', $job_type, 'JobType');
      } // if
    } // setJobType
   
    /**
     * Return name string
     *
     * @param bool $with_value
     * @return string
     */
    function getName($with_value = false) {
      $user = $this->getUser();
      $value = $this->getValue();
      
      if($with_value) {
        $value_job = $this->getJobType() instanceof JobType ? $this->getFormatedValue($value * $this->getJobType()->getHourlyRateFor($this->getProject())) : 0;
        
        return $value == 1 ? 
            lang(':value hour of :job (:costs)', array('value' => $value, 'job' => $this->getJobTypeName(), 'costs' => $value_job)) : 
            lang(':value hours of :job (:costs)', array('value' => $value, 'job' => $this->getJobTypeName(), 'costs' => $value_job));
        
      } else {
        if($user instanceof User) {
          return $value == 1 ? 
            lang(':value hour of :job by :name', array('value' => $value, 'job' => $this->getJobTypeName(), 'name' => $user->getDisplayName(true))) : 
            lang(':value hours of :job by :name', array('value' => $value, 'job' => $this->getJobTypeName(), 'name' => $user->getDisplayName(true)));
        } else {
          return $value == 1 ? 
            lang(':value hour of :job', array('value' => $value, 'job' => $this->getJobTypeName(),)) : 
            lang(':value hours of :job', array('value' => $value, 'job' => $this->getJobTypeName(),));
        } // if
      }//if
    } // getName
    
    /**
     * Return value formated with currency
     *
     * @param float $value
     * @return string
     */
    function getFormatedValue($value) {
      return Globalization::formatNumber($value);
    }//getFormatedValue
  
    /**
     * Return Currency 
     * 
     * @return Currency
     */
    function getCurrency() {
      return $this->getProject() instanceof Project && $this->getProject()->getCurrency() instanceof Currency ? $this->getProject()->getCurrency() : null;
    } // getCurrency

    /**
     * Convert time to money
     *
     * @return float
     */
    function calculateExpense() {
      return $this->getValue() * $this->getJobType()->getHourlyRateFor($this->getProject());
    } // calculateExpense
    
    /**
     * Bulk set object attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['value'])) {
        $attributes['value'] = time_to_float($attributes['value']);

        // prevent rounding to 0.00 for very small values because afterwards we get validation errors
        if ($attributes['value'] < 0.01) {
          $attributes['value'] = 0.01;
        } // if
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
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

      if($detailed) {
        $result['job_type'] = $this->getJobType() instanceof JobType ? $this->getJobType()->describe($user, false, $for_interface) : null;
      } else {
        $result['job_type_id'] = $this->getJobTypeId();
      } // if

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

      if($detailed) {
        $result['job_type'] = $this->getJobType() instanceof JobType ? $this->getJobType()->describeForApi($user) : null;
      } else {
        $result['job_type_id'] = $this->getJobTypeId();
      } // if

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return parent::getObjectContextPath() . '/time/' . $this->getId();
    } // getObjectContextPath
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return $this->getParent()->getRoutingContext() . '_tracking_time_record';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      $parent_context_params = $this->getParent()->getRoutingContextParams();
      
      return is_array($parent_context_params) ? array_merge($parent_context_params, array('time_record_id' => $this->getId())) : array('time_record_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Return history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      return parent::history()->alsoTrackFields('job_type_id');
    } // history
    
    /**
     * Cached inspector instance
     * 
     * @var ITrackingInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return ITrackingInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new ITrackingInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {      
      if(!$this->validatePresenceOf('job_type_id')) {
        $errors->addError(lang('Job type is required'), 'job_type_id');
      } // if

      parent::validate($errors);
    } // validate

    /**
     * Save time record into the database
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
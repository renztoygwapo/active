<?php

  /**
   * JobType class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class JobType extends BaseJobType implements IRoutingContext {
    
    /**
     * Return hourly rate for given project
     * 
     * This function will first check if we have custom hourly rate set for a 
     * given project. If we do, it will return custom rate, and default it no 
     * custom hourly rate is set
     * 
     * @param Project $project
     * @return float
     */
    function getHourlyRateFor(Project $project) {
      return $this->hasCustomHourlyRateFor($project) ? $this->getCustomHourlyRateFor($project) : $this->getDefaultHourlyRate();
    } // getHourlyRateFor
    
    /**
     * Cached custom hourly rates (indexed by project ID)
     *
     * @var array
     */
    protected $custom_hourly_rates = array();
    
    /**
     * Returns true if there's custom hourly rate set for given project
     * 
     * @param Project $project
     * @return boolean
     */
    function hasCustomHourlyRateFor(Project $project) {
      return $this->getCustomHourlyRateFor($project) !== null;
    } // hasCustomHourlyRateFor
    
    /**
     * Return custom hourly rate for given project
     * 
     * @param Project $project
     * @return float
     */
    function getCustomHourlyRateFor(Project $project) {
      $project_id = $project->getId();
      
      if(!array_key_exists($project_id, $this->custom_hourly_rates)) {
        $custom_hourly_rate = DB::executeFirstCell('SELECT hourly_rate FROM ' . TABLE_PREFIX . 'project_hourly_rates WHERE project_id = ? AND job_type_id = ?', $project_id, $this->getId());
        
        if($custom_hourly_rate === null) {
          $this->custom_hourly_rates[$project_id] = null;
        } else {
          $this->custom_hourly_rates[$project_id] = (float) $custom_hourly_rate;
        } // if
      } // if
      
      return $this->custom_hourly_rates[$project_id];
    } // getCustomHourlyRateFor
    
    /**
     * Set custom hourly rate for given project
     * 
     * @param Project $project
     * @param float $value
     * @throws ValidationErrors
     */
    function setCustomHourlyRateFor(Project $project, $value) {
      $value = is_numeric($value) ? round($value, 2) : null;
      
      if($value) {
        DB::execute('REPLACE INTO ' . TABLE_PREFIX . 'project_hourly_rates (project_id, job_type_id, hourly_rate) VALUES (?, ?, ?)', $project->getId(), $this->getId(), $value);
        $this->custom_hourly_rates[$project->getId()] = $value;
      } else {
        throw new ValidationErrors(array(
          'hourly_rate' => lang('Hourly rate is required'), 
        ));
      } // if
    } // setCustomHourlyRateFori
    
    /**
     * Remove custom hourly rate for given project, if set
     * 
     * @param Project $project
     */
    function dropCustomHourlyRateFor(Project $project) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'project_hourly_rates WHERE project_id = ? AND job_type_id = ?', $project->getId(), $this->getId());
      $this->custom_hourly_rates[$project->getId()] = null;
    } // dropCustomHourlyRateFor
    
    /**
     * Set this job type as default
     */
    function setAsDefault() {
      if(!$this->getIsDefault()) {
        try {
          DB::beginWork('Setting job type as default @ ' . __CLASS__);
          
          DB::execute('UPDATE ' . TABLE_PREFIX . 'job_types SET is_default = 0');
          
          $this->setIsDefault(true);
          $this->save();

          AngieApplication::cache()->removeByModel('job_types');
          
          DB::commit('Job type set as default @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to set job type as default @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // setAsDefault
    
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
      
      $result['default_hourly_rate'] = $this->getDefaultHourlyRate();
      $result['is_default'] = $this->getIsDefault();
      $result['is_active'] = $this->getIsActive();

      $result['permissions']['can_archive'] = $this->canArchive($user);
      
      $result['urls']['set_as_default'] = $this->getSetAsDefaultUrl();
      $result['urls']['archive'] = $this->getArchiveUrl();
      $result['urls']['unarchive'] = $this->getUnarchiveUrl();
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      return array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'default_hourly_rate' => $this->getDefaultHourlyRate(),
        'is_default' => $this->getIsDefault(),
        'is_active' => $this->getIsActive()
      );
    } // describeForApi
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'job_type';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('job_type_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
     * Return set as default URL
     * 
     * @return string
     */
    function getSetAsDefaultUrl() {
      return Router::assemble('job_type_set_as_default', array(
      	'job_type_id' => $this->getId()
      ));
    } // getSetAsDefaultUrl

    /**
     * Return job type archive URL
     *
     * @return string
     */
    function getArchiveUrl() {
      return Router::assemble('job_type_archive', array(
        'job_type_id' => $this->getId()
      ));
    } // getArchiveUrl

    /**
     * Return job type unarchive URL
     *
     * @return string
     */
    function getUnarchiveUrl() {
      return Router::assemble('job_type_unarchive', array(
        'job_type_id' => $this->getId()
      ));
    } // getUnarchiveUrl
    
    /**
     * Return project hourly rate URL
     * 
     * @param Project $project
     * @return string
     */
    function getProjectHourlyRateUrl(Project $project) {
      return Router::assemble('project_hourly_rate', array(
        'project_slug' => $project->getSlug(), 
        'job_type_id' => $this->getId(), 
      ));
    } // getProjectHourlyRateUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can see details of this job type
     * 
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $user->isAdministrator();
    } // canView
  
    /**
     * Return true if $user can update this job type
     * 
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can set this job type as default
     * 
     * @param User $user
     * @return boolean
     */
    function canSetAsDefault(User $user) {
      return $this->canEdit($user) && $this->getIsActive();
    } // canSetAsDefault

    /**
     * Returns true if $user can archive this job type
     *
     * @param User $user
     * @return boolean
     */
    function canArchive(User $user) {
      return $this->canEdit($user) && !$this->getIsDefault();
    } // canArchive
    
    /**
     * Return true if $user can delete this user
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isAdministrator() && !$this->inUse();
    } // canDelete
    
    /**
     * Returns true if this job type is in use
     * 
     * @return boolean
     */
    function inUse() {
      return $this->getIsDefault() || JobTypes::count() == 1 || Estimates::countByJobType($this) || TimeRecords::countByJobType($this);
    } // inUse
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError(lang('Job type name must be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Job type name is required'), 'name');
      } // if
      
      if($this->getDefaultHourlyRate() <= 0) {
        $errors->addError(lang('Default hourly rate is required'), 'default_hourly_rate');
      } // if
    } // validate
    
  }
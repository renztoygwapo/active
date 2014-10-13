<?php

  /**
   * Default tracking implementation
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class ITrackingImplementation {
    
    /**
     * Parnet object
     *
     * @var ITracking
     */
    protected $object;
    
    /**
     * Construct helper object
     *
     * @param ITracking $object
     */
    function __construct(ITracking $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Returns true if parent object has time or expenses tracked
     *
     * @param IUser $user
     * @return boolean
     */
    function has(IUser $user, $include_subobjects = false) {
      return (boolean) $this->sumTime($user,$include_subobjects) || (boolean) $this->sumExpenses($user,$include_subobjects);
    } // has
    
    /**
     * Returns true if parent object has billable time or expenses tracked
     * 
     * @param IUser $user
     * @return boolean
     */
    function hasBillable(IUser $user, $include_subobjects = false) {
      return (boolean) $this->sumBillableExpenses($user,$include_subobjects) || (boolean) $this->sumBillableTime($user,$include_subobjects);
    } // hasBillable
    
    /**
     * Cached get() result
     *
     * @var DBResult
     */
    private $records = false;
    
    /**
     * Return all time and expenses tracked for parent object
     *
     * @param User $user
     * @return DBResult
     */
    function get(User $user) {
      if($this->records === false) {
        $this->records = TrackingObjects::findByParent($this->object);
      } // if
      
      return $this->records;
    } // get

    /**
     * Return default billable status for this object type
     *
     * @return integer
     */
    function getDefaultBillableStatus() {
      if($this->object instanceof Project) {
        return ConfigOptions::getValueFor('default_billable_status', $this->object) ? 1 : 0;
      } elseif($this->object instanceof ProjectObject && $this->object->getProject() instanceof Project) {
        return ConfigOptions::getValueFor('default_billable_status', $this->object->getProject()) ? 1 : 0;
      } else {
        return ConfigOptions::getValue('default_billable_status') ? 1 : 0;
      } // if
    } // getDefaultBillableStatus
    
    // ---------------------------------------------------
    //  Time
    // ---------------------------------------------------
    
    /**
     * Log time and return time record
     * 
     * @param float $value
     * @param IUser $user
     * @param JobType $job_type
     * @param DateValue $date
     * @param integer $billable_status
     * @param IUser $by
     * @return TimeRecord
     */
    function logTime($value, IUser $user, JobType $job_type, DateValue $date, $billable_status = BILLABLE_STATUS_BILLABLE, IUser $by = null) {
      $record = new TimeRecord();
      
      $record->setParent($this->object);
      $record->setState(STATE_VISIBLE);
      $record->setJobType($job_type);
      $record->setRecordDate($date);
      $record->setValue($value);
      $record->setUser($user);
      $record->setBillableStatus($billable_status);
      
      if($by instanceof IUser) {
        $record->setCreatedBy($by);
      } else {
        $record->setCreatedBy($user);
      } // if
      
      $record->save();
      
      return $record;
    } // logTime
    
    /**
     * Returns time records attached to parent object
     * 
     * Optional filter is billable status (or array of statuses)
     *
     * @param User $user
     * @param mixed $billable_status
     * @return DBresult
     */
    function getTimeRecords(User $user, $billable_status = null) {
      return TimeRecords::findByParent($this->object, $billable_status);
    } // getTimeRecords
    
    /**
     * Return total time tracked 
     *
     * @param User $user
     * @param boolean $include_subobjects
     * @return float
     */
    function sumTime(User $user, $include_subobjects = false) {
      return TimeRecords::sumByParent($user, $this->object, null, $include_subobjects);
    } // sumTime
    
    /**
     * Return sum billable time records
     * 
     * @param IUser $user
     * @param boolean $include_subobjects
     * @return float
     */
    function sumBillableTime(IUser $user, $include_subobjects = false) {
      return TimeRecords::sumByParent($user, $this->object, BILLABLE_STATUS_BILLABLE, $include_subobjects);
   } // sumBillableTime
   
   // ---------------------------------------------------
   //  Expenses
   // ---------------------------------------------------
   
   /**
     * Log time and return time record
     * 
     * @param float $value
     * @param IUser $user
     * @param ExpenseCategory $category
     * @param DateValue $date
     * @param integer $billable_status
     * @param IUser $by
     * @return TimeRecord
     */
    function logExpense($value, IUser $user, ExpenseCategory $category, DateValue $date, $billable_status = BILLABLE_STATUS_BILLABLE, IUser $by = null) {
      $record = new Expense();
      
      $record->setParent($this->object);
      $record->setState(STATE_VISIBLE);
      $record->setCategory($category);
      $record->setRecordDate($date);
      $record->setValue($value);
      $record->setUser($user);
      $record->setBillableStatus($billable_status);
      
      if($by instanceof IUser) {
        $record->setCreatedBy($by);
      } else {
        $record->setCreatedBy($user);
      } // if
      
      $record->save();
      
      return $record;
    } // logExpense
   
   /**
     * Returns tracked expenses attached to the parent parent object
     * 
     * Optional filter is billable status (or array of statuses)
     *
     * @param User $user
     * @param mixed $billable_status
     * @return DBResult
     */
    function getExpenses(User $user, $billable_status = null) {
      return Expenses::findByParent($this->object, $billable_status);
    } // getExpenses
    
    /**
     * Sum up total expenses tracked for parent object
     *
     * @param User $user
     * @param integer $include_subobjects
     * @return float
     */
    function sumExpenses(User $user, $include_subobjects = false) {
      return Expenses::sumByParent($user, $this->object, null, $include_subobjects);
    } // sumExpenses
    
    /**
     * Return sum billable expenses
     * 
     * @param IUser $user
     * @param boolean $include_subobjects
     * @return boolean
     */
    function sumBillableExpenses(IUser $user, $include_subobjects = false) {
      return Expenses::sumByParent($user, $this->object, BILLABLE_STATUS_BILLABLE, $include_subobjects);
    } // sumBillableExpenses
    
    // ---------------------------------------------------
    //  Estimates
    // ---------------------------------------------------
    
    /**
     * Return all estimates tracked for parent object
     * 
     * @return DBResult
     */
    function getEstimates() {
      return Estimates::findByParent($this->object);
    } // getEstimates
    
    /**
     * Cached last estimate instance
     *
     * @var Estimate
     */
    private $estimate = false;
    
    /**
     * Return all estimates for this object
     *
     * @return Estimate
     */
    function getEstimate() {
      if($this->estimate === false) {
        $this->estimate = Estimates::findLatestByParent($this->object);
      } // if
      
      return $this->estimate;
    } // getEstimate
    
    /**
     * Set estimated value
     * 
     * @param float $value
     * @param JobType|integer $job_type
     * @param string $comment
     * @param IUser $by
     * @param boolean $check_for_duplicate
     * @return Estimate
     */
    function setEstimate($value, $job_type, $comment, IUser $by, $check_for_duplicate = true) {
      $job_type_id = $job_type instanceof JobType ? $job_type->getId() : $job_type;

      if($check_for_duplicate && $this->getEstimate() instanceof Estimate && $this->getEstimate()->getValue() == $value && $this->getEstimate()->getJobTypeId() == $job_type_id) {
        return $this->getEstimate();
      } // if
      
      $estimate = new Estimate();
      
      $estimate->setParent($this->object);
      $estimate->setValue($value);
      $estimate->setJobTypeId($job_type_id);
      $estimate->setComment($comment);
      $estimate->setCreatedBy($by);
      
      $estimate->save();

      $this->estimate = $estimate;
      
      return $this->estimate;
    } // setEstimate
    
    /**
     * Cached array of previous estimates
     *
     * @var Estimate[]
     */
    private $previous_estimates = false;
    
    /**
     * Return previous estimates
     * 
     * @return DBResult
     */
    function getPreviousEstimates() {
      $estimates_table = TABLE_PREFIX . 'estimates';
      
      $parent_type = get_class($this->object);
      $parent_id = $this->object->getId();
      
      $current_estimate_id = DB::executeFirstCell("SELECT id FROM $estimates_table WHERE parent_type = ? AND parent_id = ? ORDER BY created_on DESC LIMIT 0, 1", $parent_type, $parent_id);
      if($current_estimate_id) {
        $rows = DB::execute("SELECT * FROM $estimates_table WHERE parent_type = ? AND parent_id = ? AND id != ? ORDER BY created_on DESC", $parent_type, $parent_id, $current_estimate_id);
        
        if($rows) {
          $this->previous_estimates = array();
          
          foreach($rows as $row) {
            $estimate = array(
              'value' => floatval($row['value']), 
              'created_on' => DateTimeValue::makeFromString($row['created_on']),
              'created_by' => $row['created_by_id'] ? Users::findByIds($row['created_by_id']) : null, 
            );
            
            if(empty($estimate['created_by'])) {
              $estimate['created_by'] = new AnonymousUser($row['created_by_name'], $row['created_by_email']);
            } // if
            
            $this->previous_estimates[] = $estimate;
          } // foreach
        } else {
          $this->previous_estimates = null;
        } // if
      } else {
        $this->previous_estimates = null;
      } // if
    } // getPreviousEstimates
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can track time or expenses for parent object
     *
     * @param User $user
     * @return boolean
     */
    function canAdd(User $user) {
      $project = $this->object instanceof Project ? $this->object : $this->object->getProject();
      
      if($project instanceof Project) {
        return $user->isProjectManager() || $project->isLeader($user) || $user->projects()->getPermission('tracking', $project) >= ProjectRole::PERMISSION_CREATE;
      } else {
        return false;
      }  // if
    } // canAdd
    
    /**
     * Returns true if $user can track time and expenses for $for user
     *
     * @param User $user
     * @param User $for
     * @return boolean
     */
    function canAddFor(User $user, User $for) {
      if($this->canAdd($user)) {
        return $user->getId() == $for->getId() || $user->isProjectManager();
      } else {
        return false;
      } // if
    } // canAddFor
    
    /**
     * Returns true if $user can set or change estimate for the parent object
     *
     * @param User $user
     * @return boolean
     */
    function canEstimate(User $user) {
      return $this->object->canEdit($user);
    } // canEstimate
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return object tacking URL
     *
     * @return string
     */
    function getUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_tracking', $this->object->getRoutingContextParams());
    } // getUrl
    
    /**
     * Return add time URL
     *
     * @return string
     */
    function getAddTimeUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_tracking_time_records_add', $this->object->getRoutingContextParams());
    } // getAddTimeUrl
    
    /**
     * Return add expense URL
     *
     * @return string
     */
    function getAddExpenseUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_tracking_expenses_add', $this->object->getRoutingContextParams());
    } // getAddExpenseUrl
    
    /**
     * Returns get / set estimate URL
     *
     * @return string
     */
    function getEstimatesUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_tracking_estimates', $this->object->getRoutingContextParams());
    } // getEstimatesUrl
    
    /**
     * Return set estimate URL
     * 
     * @return string
     */
    function getSetEstimateUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_tracking_estimate_set', $this->object->getRoutingContextParams());
    } // getSetEstimateUrl
    
    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------
    
    /**
     * Describe tracking of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['object_time'] = $this->sumTime($user); 
    	$result['object_expenses'] = $this->sumExpenses($user);
    	
    	$estimate = $this->getEstimate();
    	
    	if($estimate instanceof Estimate) {
    	  $result['estimate'] = array(
    	    'value' => (float) $estimate->getValue(),
    	    'job_type_id' => $estimate->getJobTypeId(),
    	    'job_type_name' => $estimate->getJobTypeName(),
    	    'comment' => $estimate->getComment(), 
    	  );
    	} else {
    	  $result['estimate'] = null;
    	} // if

			$result['urls']['tracking'] = $this->getUrl();
			$result['permissions']['can_manage_tracking'] = $this->canAdd($user);
    } // describe

    /**
     * Describe tracking of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      if($detailed) {
        $result['object_time'] = $this->sumTime($user);
        $result['object_expenses'] = $this->sumExpenses($user);

        $estimate = $this->getEstimate();

        if($estimate instanceof Estimate) {
          $result['estimate'] = array(
            'value' => $estimate->getValue(),
            'job_type_id' => $estimate->getJobTypeId(),
            'job_type_name' => $estimate->getJobTypeName(),
            'comment' => $estimate->getComment(),
          );
        } else {
          $result['estimate'] = null;
        } // if

        $result['urls']['tracking'] = $this->getUrl();
        $result['permissions']['can_manage_tracking'] = $this->canAdd($user);
      } // if
    } // describeForApi
    
  }
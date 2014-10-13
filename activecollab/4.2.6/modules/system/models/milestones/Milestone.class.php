<?php

  /**
   * Milestone record class
   *
   * @package activeCollab.modules.milestones
   * @subpackage models
   */
  class Milestone extends ProjectObject implements IComplete, IComments, ISubscriptions, IAttachments, IAssignees, ISchedule, IInvoiceBasedOn, ICanBeFavorite, ISearchItem, ICalendarEventContext {
    
    /**
     * Permission name
     * 
     * @var string
     */
    protected $permission_name = 'milestone';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    protected $fields = array(
      'id', 
      'type', 
      'module', 
      'project_id', 'assignee_id', 'delegated_by_id',  
      'name', 'body',  
      'state', 'original_state', 'visibility', 'original_visibility', 'is_locked', 'priority', 'due_on', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 
      'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 
      'date_field_1', // for start_on
      'version', 'position',
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'start_on' => 'date_field_1',
    );
  
    /**
     * Construct milestone
     *
     * @param integer $id
     * @return Milestone
     */
    function __construct($id = null) {
      $this->setModule(SYSTEM_MODULE);
      parent::__construct($id);
    } // __construct

    /**
     * Return verbose type name
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('milestone', null, true, $language) : lang('Milestone', null, true, $language);
    } // getVerboseType

    /**
     * Cached search helper instance
     *
     * @var IMilestoneSearchItemImplementation
     */
    private $search = false;

    /**
     * Return search heper instance
     *
     * @return IMilestoneSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new IMilestoneSearchItemImplementation($this);
      } // if

      return $this->search;
    } // search
    
    /**
     * Cached inspector instance
     * 
     * @var IMilestoneInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return IMilestoneInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new IMilestoneInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector

	  /**
	   * Cached calendar event context helper instance
	   *
	   * @var IMilestoneCalendarEventContextImplementation
	   */
	  private $calendar_event_context = false;

	  /**
	   * Return calendar event context helper instance
	   *
	   * @return IMilestoneCalendarEventContextImplementation
	   */
	  function calendar_event_context() {
		  if($this->calendar_event_context === false) {
			  $this->calendar_event_context = new IMilestoneCalendarEventContextImplementation($this);
		  } // if

		  return $this->calendar_event_context;
	  } // calendar_event_context
    
    /**
     * Override default set attributes method
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['to_be_determined']) && $attributes['to_be_determined']) {
        $attributes['start_on'] = null;
        $attributes['due_on'] = null;
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
      
      unset($result['due_on']);;
      unset($result['milestone_id']);
      
      $result['start_on'] = $this->getStartOn();
      $result['due_on'] = $this->getDueOn();
      $result['percents_done'] = $this->getPercentsDone();
      $result['total_tasks'] = $this->getTotalTasksCount();
      $result['open_tasks'] = $this->getOpenTasksCount();
      $result['completed_tasks'] = $this->getCompletedTaskCount();
      $result['urls']['reschedule'] = $this->getRescheduleUrl();
      
      $result['progress'] = lang(':completed of :total task completed (:percentage % done)', array('completed' => $this->getCompletedTaskCount(), 'total' => $this->getTotalTasksCount(), 'percentage' => $this->getPercentsDone()));
      
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

      unset($result['due_on']);;
      unset($result['milestone_id']);

      $result['start_on'] = $this->getStartOn();
      $result['due_on'] = $this->getDueOn();

      if($detailed) {
        $result['percents_done'] = $this->getPercentsDone();
        $result['total_tasks'] = $this->getTotalTasksCount();
        $result['open_tasks'] = $this->getOpenTasksCount();
        $result['completed_tasks'] = $this->getCompletedTaskCount();
        $result['urls']['reschedule'] = $this->getRescheduleUrl();

        // Added on customer's request, and for compatibility with activeCollab 2 API responses
        $result['progress'] = array(
          'percent_done' => $this->getPercentsDone(),
          'total_tasks' => $this->getTotalTasksCount(),
          'open_tasks' => $this->getOpenTasksCount(),
        );
      } // if

      return $result;
    } // describeForApi
    
    /**
     * Returns if this milestone start and due dates are to be determined
     *
     * @return boolean
     */
    function isToBeDetermined() {
      return $this->getDueOn() === null;
    } // isToBeDetermined
    
    /**
     * Returns true if start on and due on are the same day
     *
     * @return boolean
     */
    function isDayMilestone() {
      $start_on = $this->getStartOn();
      $due_on = $this->getDueOn();
      
      return $start_on instanceof DateValue && $due_on instanceof DateValue && $start_on->getTimestamp() == $due_on->getTimestamp();
    } // isDayMilestone
    
    /**
     * Advance for give number of seconts
     *
     * @param integer $seconds
     * @param boolean $save
     * @return boolean
     */
    function advance($seconds, $save = false) {
      if($seconds != 0) {
      	$start_on = $this->getStartOn();
      	$due_on = $this->getDueOn();
      	
      	$this->setStartOn($start_on->advance($seconds, false));
      	$this->setDueOn($due_on->advance($seconds, false));
      	
      	if($save) {
      	  return $this->save();
      	} // if
      } // if
    	return true;
    } // advance
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canEdit($user)) {
        $options->add('reschedule', array(
          'text' => lang('Reschedule'),
          'url' => $this->getRescheduleUrl(),
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/reschedule.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : AngieApplication::getImageUrl('icons/navbar/reschedule.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
        	'onclick' => new FlyoutFormCallback($this->getUpdatedEventName(), array(
            'success_message' => lang('Milestone has been rescheduled'),
            'width' => 'narrow'
           )),
           'important' => true
        ), true);
      } // if
      
      if(AngieApplication::isModuleLoaded('invoicing')) {
        if(Invoices::canAdd($user) && $this->hasBillableItems($user)) {
          $options->add('make_invoice', array(
            'url' => $this->invoice()->getUrl(),
            'text' => lang('Create Invoice'),
            'onclick' => new FlyoutFormCallback('create_invoice_from_milestone', array(
              'focus_first_field' => false,
            )),
          ));
        } // if
      } // if
      
      parent::prepareOptionsFor($user, $options, $interface);
    } // prepareOptionsFor
    
    /**
     * Return true if has billable time records or expenses
     *
     * @param IUser $user
     * @return boolean
     */
    protected function hasBillableItems(IUser $user) {
      if(AngieApplication::isModuleLoaded('tracking')) {
        return TimeRecords::countByMilestone($user, $this, BILLABLE_STATUS_BILLABLE) || Expenses::countByMilestone($user, $this, BILLABLE_STATUS_BILLABLE);
      } // if

      return false;
    }//hasBillableItems
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return parent::getObjectContextPath() . '/milestones/' . $this->getId();
    } // getContextPath
    
    // ---------------------------------------------------
    //  Utilities
    // ---------------------------------------------------
    
    /**
     * Copy this milestone to $project
     *
     * @param Project $project
     * @param array $update_attributes
     * @param array $categories_map
     * @param boolean $bulk
     * @return Milestone
     * @throws Exception
     */
    function copyToProject(Project $project, $update_attributes = null, $categories_map = null, $bulk = false) {
      try {
        DB::beginWork('Creating a milestone copy @ ' . __CLASS__);
        
        $copy = parent::copyToProject($project, $update_attributes, $bulk);
        
        if($copy instanceof Milestone) {
          $objects = ProjectObjects::findByMilestone($this, STATE_ARCHIVED);
          
          if($objects) {
            $copy_id = $copy->getId();
            $created_on = new DateTimeValue();
            
            foreach($objects as $object) {
              $update_subobject_attributes = array(
                'milestone_id' => $copy_id,
                'created_on' => $created_on
              );
              
              if($categories_map && $object instanceof ICategory && $object->getCategoryId()) {
                $update_subobject_attributes['category_id'] = isset($categories_map[$object->getCategoryId()]) ? $categories_map[$object->getCategoryId()] : 0;
              } // if
              
              $object->copyToProject($project, $update_subobject_attributes, $bulk);
            } // foreach
          } // if
        } // if
        
        DB::commit('Milestone copy created @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to create milestone copy @ ' . __CLASS__);
        throw $e;
      } // try
      
      return $copy;
    } // copyToProject
    
    /**
     * Move this milestone to $project
     *
     * @param Project $project
     * @param mixed $update_attributes
     * @throws Exception
     */
    function moveToProject(Project $project, $update_attributes = null) {
      try {
        DB::beginWork('Moving milestone to a project @ ' . __CLASS__);
        
        $old_project = $this->getProject();
        $subobjects = ProjectObjects::findByMilestone($this, STATE_ARCHIVED);
        
        parent::moveToProject($project, $update_attributes);
        
        if($subobjects) {
          foreach($subobjects as $subobject) {
            $subobject->moveToProject($project, array(
              'milestone_id' => $this->getId(), 
            ));
          } // foreach
        } // if
        
        //DB::execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET milestone_id = NULL WHERE milestone_id = ? AND project_id = ?', $this->getId(), $old_project->getId());
        
        DB::commit('Milestone moved to a project @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to move milestone to a project @ ' . __CLASS__);
        throw $e;
      } // try
    } // moveToProject
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------
    
    /**
     * Schedule helper
     * 
     * @var IScheduleImplementation
     */
    private $schedule = false;
    
    /**
     * Return schedule helper instance
     * 
     * @return IScheduleImplementation
     */
    function schedule() {
    	if ($this->schedule === false) {
    		$this->schedule = new IScheduleImplementation($this);	
    	} // if
    	
    	return $this->schedule;
    } // schedule
    
    /**
     * Cached complete implementation instance
     *
     * @var IProjectObjectCompleteImplementation
     */
    private $complete = false;
    
    /**
     * Return complete interface implementation
     *
     * @return IProjectObjectCompleteImplementation
     */
    function complete() {
      if($this->complete === false) {
        $this->complete = new IProjectObjectCompleteImplementation($this);
      } // if
      
      return $this->complete;
    } // complete
    
    /**
     * Cached assignees implementation instance
     *
     * @var IProjectObjectAssigneesImplementation
     */
    private $assignees = false;
    
    /**
     * Return assignees implementation instance for this object
     *
     * @return IProjectObjectAssigneesImplementation
     */
    function assignees() {
      if($this->assignees === false) {
        $this->assignees = new IProjectObjectAssigneesImplementation($this);
      } // if
      
      return $this->assignees;
    } // assignees
    
    /**
     * Cached attachment implementation instance
     *
     * @var IAttachmentsImplementation
     */
    private $attachments;
    
    /**
     * Return attachments implementation instance for this object
     *
     * @return IAttachmentsImplementation
     */
    function &attachments() {
      if(empty($this->attachments)) {
        $this->attachments = new IAttachmentsImplementation($this);
      } // if
      
      return $this->attachments;
    } // attachments
    
    /**
     * Comment interface instance
     *
     * @var IMilestoneCommentsImplementation
     */
    private $comments;
    
    /**
     * Return project object comments interface instance
     *
     * @return IMilestoneCommentsImplementation
     */
    function &comments() {
      if(empty($this->comments)) {
        $this->comments = new IMilestoneCommentsImplementation($this);
      } // if
      return $this->comments;
    } // comments
    
    /**
     * Subscriptions helper instance
     *
     * @var IProjectObjectSubscriptionsImplementation
     */
    private $subscriptions;
    
    /**
     * Return subscriptions helper for this object
     *
     * @return ISubscriptionsImplementation
     */
    function &subscriptions() {
      if(empty($this->subscriptions)) {
        $this->subscriptions = new IProjectObjectSubscriptionsImplementation($this);
      } // if
      
      return $this->subscriptions;
    } // subscriptions
    
    /**
     * Return history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      return parent::history()->alsoTrackFields('date_field_1')->alsoRemoveFields('milestone_id');
    } // history
    
    /**
     * Return invoice implementation
     * 
     * @return IInvoiceBasedOnMilestoneImplementation
     */
    function &invoice() {
      return $this->getDelegateInstance('invoice', function() {
        return AngieApplication::isModuleLoaded('invoicing') ? 'IInvoiceBasedOnMilestoneImplementation' : 'IInvoiceBasedOnImplementationStub';
      });
    } // invoice
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get start_on
     *
     * @return DateValue
     */
    function getStartOn() {
      return $this->getDateField1();
    } // getStartOn
    
    /**
     * Set start_on value
     *
     * @param DateValue $value
     * @return DateValue
     */
    function setStartOn($value) {
      return $this->setDateField1($value);
    } // setStartOn
    
    // ---------------------------------------------------
    //  Progress calculations
    // ---------------------------------------------------
    
    /**
     * Milestone progress
     * 
     * @var array
     */
    private $milestone_progress = false;
    
    /**
     * Return value of total_tasks_count field
     *
     * @return integer
     */
    function getTotalTasksCount() {
    	if ($this->milestone_progress === false) {
    		$this->milestone_progress = ProjectProgress::getMilestoneProgress($this); 
    	} // if
    	
      return array_var($this->milestone_progress, 0);
    } // getTotalTasksCount
    
    /**
     * Return value of open_tasks_count field
     *
     * @return integer
     */
    function getOpenTasksCount() {
    	if ($this->milestone_progress === false) {
    		$this->milestone_progress = ProjectProgress::getMilestoneProgress($this); 
    	} // if
    	
      return array_var($this->milestone_progress, 1);
    } // getOpenTasksCount
    
    /**
     * Return number of completed tasks in this project
     *
     * @return integer
     */
    function getCompletedTaskCount() {
      return $this->getTotalTasksCount() - $this->getOpenTasksCount();
    } // getCompletedTaskCount
    
    /**
     * Return number of percents this object is done
     *
     * @return integer
     */
    function getPercentsDone() {
      $total = $this->getTotalTasksCount();

      if($total > 0) {
        $completed = $total - $this->getOpenTasksCount();

        return $completed ? floor($completed / $total * 100) : 0;
      } // if

      return $this->complete()->isCompleted() ? 100 : 0;
    } // getPercentsDone
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return reschedule milestone URL
     *
     * @return string
     */
    function getRescheduleUrl() {
      return Router::assemble('project_milestone_reschedule', array(
        'project_slug' => $this->getProject()->getSlug(),
        'milestone_id' => $this->getId(),
      ));
    } // getRescheduleUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Save milestone record to the database
     * 
     * @return boolean
     */
    function save() {
      $starts_on = $this->getStartOn();
      $due_on = $this->getDueOn();
      
      if($starts_on instanceof DateValue && empty($due_on)) {
        $this->setDueOn($starts_on);
      } elseif($due_on instanceof DateValue && empty($starts_on)) {
        $this->setStartOn($due_on);
      } // if

      // Set proper position based on start date
      if($this->isLoaded()) {
        if($this->isModifiedField('start_on')) {
          if($this->getStartOn() instanceof DateValue) {
            $next_position = DB::executeFirstCell('SELECT MAX(position) FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND project_id = ? AND date_field_1 = ? AND id != ?', 'Milestone', $this->getProjectId(), $this->getStartOn(), $this->getId()) + 1;
          } else {
            $next_position = DB::executeFirstCell('SELECT MAX(position) FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND project_id = ? AND date_field_1 IS NULL AND id != ?', 'Milestone', $this->getProjectId(), $this->getId()) + 1;
          } // if

          $this->setPosition($next_position);
        } // if
      } else {
        if($this->getStartOn() instanceof DateValue) {
          $next_position = DB::executeFirstCell('SELECT MAX(position) FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND project_id = ? AND date_field_1 = ?', 'Milestone', $this->getProjectId(), $this->getStartOn()) + 1;
        } else {
          $next_position = DB::executeFirstCell('SELECT MAX(position) FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND project_id = ? AND date_field_1 IS NULL', 'Milestone', $this->getProjectId()) + 1;
        } // if

        $this->setPosition($next_position);
      } // if
      
      return parent::save();
    } // save
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Milestone name is required'), 'name');
      } // if
      
      $start_on = $this->getStartOn();
      $due_on = $this->getDueOn();

      if ($start_on instanceof DateValue && $due_on instanceof DateValue) {
        if($start_on->getTimestamp() > $due_on->getTimestamp()) {
          $errors->addError(lang('Start date needs to be before due date'), 'date_range');
        } // if
      } //if

      parent::validate($errors, true);
    } // validate
  
  }
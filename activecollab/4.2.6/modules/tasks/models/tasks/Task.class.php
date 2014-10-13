<?php

  /**
   * Task record class
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class Task extends ProjectObject implements IComplete, IAssignees, IComments, ICategory, ISubscriptions, IAttachments, ISubtasks, ILabel, ITracking, IReminders, ISharing, ISearchItem, ICanBeFavorite, IInvoiceBasedOn, ISchedule, ICustomFields, ICalendarEventContext {
    
    /**
     * Permission name
     * 
     * @var string
     */
    protected $permission_name = 'task';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    protected $fields = array(
      'id', 
      'type', 'source', 'module', 
      'project_id', 'milestone_id', 'category_id', 'label_id', 'assignee_id', 'delegated_by_id', 
      'name', 'body', 
      'state', 'original_state', 'visibility', 'original_visibility', 'is_locked', 'priority', 'due_on',
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 
      'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email',
      'integer_field_1', // for task ID (on project level)
      'custom_field_1', 'custom_field_2', 'custom_field_3',
      'position', 'version'
    );
    
    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'task_id' => 'integer_field_1'
    );
    
    /**
     * Construct a new task
     *
     * @param mixed $id
     */
    function __construct($id = null) {
      $this->setModule(TASKS_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Cached inspector instance
     * 
     * @var ITaskInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return ITaskInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new ITaskInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector

    /**
     * Return verbose type name
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('task', null, true, $language) : lang('Task', null, true, $language);
    } // getVerboseType
       
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
      
      $result['task_id'] = $this->getTaskId();
      $this->relatedTasks()->describe($user, $detailed, $for_interface, $result);
      
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

      $result['task_id'] = $this->getTaskId();

      return $result;
    } // describeForApi
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	parent::prepareOptionsFor($user, $options, $interface);
      
      // Default interface
      if($interface == AngieApplication::INTERFACE_DEFAULT) {
        $options->add('related_tasks', array(
          'url' => $this->relatedTasks()->getUrl(),
          'text' => lang('Related Tasks'),
          'onclick' => new FlyoutCallback(array(
            'width' => 900,
          )),
        ));

        if(AngieApplication::isModuleLoaded('invoicing') && $user->isFinancialManager() && $this->tracking()->hasBillable($user,true)) {
          $options->add('make_invoice', array(
            'url' => $this->invoice()->getUrl(),
            'text' => lang('Create Invoice'),
            'onclick' => new FlyoutFormCallback('create_invoice_from_task', array(
              'focus_first_field' => false,
            )),
          	'important' => true 
          ));
        } // if
      } // if
      
      return $options;
    } // prepareOptionsFor
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return parent::getObjectContextPath() . '/tasks/' . ($this->getVisibility() == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $this->getId();
    } // getContextPath
    
    // ---------------------------------------------------
    //  Copy and move
    // ---------------------------------------------------
    
    /**
     * Copy this object to $project
     *
     * $milestone can be an instance of Milestone class or milestone ID
     *
     * @param Project $project
     * @param array $update_attributes
     * @param boolean $bulk
     * @return Task
     * @throws Exception
     */
    function copyToProject(Project $project, $update_attributes = null, $bulk = false) {
      try {
        DB::beginWork('Making a task copy in a project @ ' . __CLASS__);

        $next_task_id = empty($bulk) ? Tasks::findNextTaskIdByProject($project) : null;
      
        $copy = parent::copyToProject($project, $update_attributes, $bulk);
        
        // Update task ID
        if(empty($bulk) && $copy instanceof Task) {
          $copy->setTaskId($next_task_id);
          $copy->save();
        } // if

        if(AngieApplication::isModuleLoaded('tracking') && $this->tracking()->getEstimate() instanceof Estimate && $this->tracking()->getEstimate()->getValue() > 0) {
          $copy->tracking()->setEstimate($this->tracking()->getEstimate()->getValue(), $this->tracking()->getEstimate()->getJobTypeId(), null, $this->tracking()->getEstimate()->getCreatedBy(), false);
        } // if
        
        DB::commit('Task copy made @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to make task copy @ ' . __CLASS__);
        throw $e;
      } // try
      
      return $copy;
    } // copyToProject
    
    /**
     * Move this task to $project
     *
     * @param Project $project
     * @param mixed $update_attributes
     * @throws Exception
     */
    function moveToProject(Project $project, $update_attributes = null) {
      try {
        DB::beginWork('Moving task to a project @ ' . __CLASS__);
        
        $next_task_id = Tasks::findNextTaskIdByProject($project);
        
        parent::moveToProject($project, $update_attributes);
        
        // Update task ID
        $this->setTaskId($next_task_id);
        $this->save();
        
        DB::commit('Task moved to a project @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to move task to a project @ ' . __CLASS__);
        throw $e;
      } // try
    } // moveToProject
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------

    /**
     * State helper instance
     *
     * @var ITaskStateImplementation
     */
    private $state = false;

    /**
     * Return state helper instance
     *
     * @return ITaskStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new ITaskStateImplementation($this);
      } // if

      return $this->state;
    } // state
    
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
     * Routing context parameters
     *
     * @var array
     */
    private $routing_context_params = false;
    
    /**
     * Return routing context parameters
     *
     * @return array
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = array(
          'project_slug' => $this->getProject()->getSlug(), 
          'task_id' => $this->getTaskId(), 
        );
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
    
    /**
     * Return invoice implementation
     * 
     * @return IInvoiceBasedOnTaskImplementation
     */
    function &invoice() {
      return $this->getDelegateInstance('invoice', function() {
        return AngieApplication::isModuleLoaded('invoicing') ? 'IInvoiceBasedOnTaskImplementation' : 'IInvoiceBasedOnImplementationStub';
      });
    } // invoice
    
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
     * Comment interface instance
     *
     * @var ITaskCommentsImplementation
     */
    private $comments;
    
    /**
     * Return project object comments interface instance
     *
     * @return ITaskCommentsImplementation
     */
    function &comments() {
      if(empty($this->comments)) {
        $this->comments = new ITaskCommentsImplementation($this);
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
     * @return IProjectObjectSubscriptionsImplementation
     */
    function &subscriptions() {
      if(empty($this->subscriptions)) {
        $this->subscriptions = new IProjectObjectSubscriptionsImplementation($this);
      } // if
      
      return $this->subscriptions;
    } // subscriptions
    
    /**
     * Category implementation instance
     *
     * @var ITaskCategoryImplementation
     */
    private $category = false;
    
    /**
     * Return category implementation
     *
     * @return ITaskCategoryImplementation
     */
    function category() {
      if($this->category === false) {
        $this->category = new ITaskCategoryImplementation($this);
      } // if
      
      return $this->category;
    } // category
    
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
     * Subtasks implementation instance for this object
     *
     * @var IProjectObjectSubtasksImplementation
     */
    private $subtasks;
    
    /**
     * Return subtasks implementation for this object
     *
     * @return IProjectObjectSubtasksImplementation
     */
    function subtasks() {
      if(empty($this->subtasks)) {
        $this->subtasks = new IProjectObjectSubtasksImplementation($this);
      } // if
      
      return $this->subtasks;
    } // subtasks
    
    /**
     * Cached labels implementation instance
     *
     * @var ILabelImplementation
     */
    private $label = false;
    
    /**
     * Return labels implementation instance for this object
     *
     * @return IAssignmentLabelImplementation
     */
    function label() {
      if($this->label === false) {
        $this->label = new IAssignmentLabelImplementation($this);
      } // if
      
      return $this->label;
    } // labels
    
    /**
     * Return history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      return parent::history()->alsoTrackFields(array('integer_field_1'));
    } // history
    
    /**
     * Tracking helper
     *
     * @var ITrackingImplementation
     */
    private $tracking = false;
    
    /**
     * Return tracking helper instance
     *
     * @return ITrackingImplementation
     */
    function tracking() {
      if($this->tracking === false) {
        if(AngieApplication::isModuleLoaded('tracking')) {
          $this->tracking = new ITrackingImplementation($this);
        } else {
          $this->tracking = new ITrackingImplementationStub($this);
        } // if
      } // if
      
      return $this->tracking;
    } // tracking
    
    /**
     * Reminders helper instance
     * 
     * @return IProjectObjectRemindersImplementation
     */
    private $reminders = false;
    
    /**
     * Return reminders helper for this task
     * 
     * @return IProjectObjectRemindersImplementation
     */
    function reminders() {
    	if($this->reminders === false) {
    		$this->reminders = new IProjectObjectRemindersImplementation($this);
    	} // if
    	
    	return $this->reminders;
    } // reminders
    
    /**
     * Sharing helper instance
     *
     * @var ISharingImplementation
     */
    private $sharing = false;
    
    /**
     * Return sharing helper
     * 
     * @return ISharingImplementation
     */
    function sharing() {
      if($this->sharing === false) {
        $this->sharing = new ITaskSharingImplementation($this);
      } // if
      
      return $this->sharing;
    } // sharing
    
    /**
     * Cached search helper instance
     *
     * @var ITaskSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search heper instance
     *
     * @return ITaskSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new ITaskSearchItemImplementation($this);
      } // if

      return $this->search;
    } // search

    /**
     * Cached search helper instance
     *
     * @var ITaskCustomFieldsImplementation
     */
    private $custom_fields = false;

    /**
     * Return search heper instance
     *
     * @return ITaskCustomFieldsImplementation
     */
    function customFields() {
      if($this->custom_fields === false) {
        $this->custom_fields = new ITaskCustomFieldsImplementation($this);
      } // if

      return $this->custom_fields;
    } // custom_fields

    /**
     * Cached related tasks instance
     *
     * @var IRelatedTasksImplementation
     */
    private $related_tasks = false;

    /**
     * Return related tasks helper instance
     *
     * @return IRelatedTasksImplementation
     */
    function relatedTasks() {
      if($this->related_tasks === false) {
        $this->related_tasks = new IRelatedTasksImplementation($this);
      } // if

      return $this->related_tasks;
    } // relatedTasks

	  /**
	   * Cached calendar event context helper instance
	   *
	   * @var ITaskCalendarEventContextImplementation
	   */
	  private $calendar_event_context = false;

	  /**
	   * Return calendar event context helper instance
	   *
	   * @return ITaskCalendarEventContextImplementation
	   */
	  function calendar_event_context() {
		  if($this->calendar_event_context === false) {
			  $this->calendar_event_context = new ITaskCalendarEventContextImplementation($this);
		  } // if

		  return $this->calendar_event_context;
	  } // calendar_event_context
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get task_id
     *
     * @return integer
     */
    function getTaskId() {
      return $this->getIntegerField1();
    } // getTaskId
    
    /**
     * Set task_id value
     *
     * @param integer $value
     * @return integer
     */
    function setTaskId($value) {
      return $this->setIntegerField1($value);
    } // setTaskId
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Task summary is required'), 'name');
      } // if

      parent::validate($errors, true);
    } // validate
    
    /**
     * Save task to database
     * 
     * @return boolean
     */
    function save() {
      if(!$this->getTaskId()) {
        $this->setTaskId(Tasks::findNextTaskIdByProject($this->getProjectId()));
      } // if
      
      return parent::save();
    } // save
  
  }
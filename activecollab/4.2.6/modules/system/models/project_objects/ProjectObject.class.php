<?php

  /**
   * Foundation of every project object
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectObject extends BaseProjectObject implements IRoutingContext, IState, IVisibility, IHistory, IReminders, IActivityLogs, IObjectContext, IAccessLog {
    
    /**
     * Permission name
     *
     * @var string
     */
    protected $permission_name = null;
    
    /**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
  	protected $protect = array(
  	  'id',
  	  'type',
  	  'module',
  	  'parent_type',
  	  'state',
  	  'created_on',
  	  'created_by_id',
  	  'created_by_name',
  	  'created_by_email',
  	  'updated_on',
  	  'updated_by_id',
  	  'updated_by_name',
  	  'updated_by_email',
  	  'completed_on',
  	  'completed_by_id',
  	  'completed_by_name',
  	  'completed_by_email',
  	  'position',
  	  'version'
  	);
  	
  	/**
  	 * List of rich text fields
  	 *
  	 * @var array
  	 */
  	protected $rich_text_fields = array('body');
    
    /**
     * Return permission name for project section where this object belongs to
     * 
     * @return string
     */
    function getProjectPermissionName() {
      return $this->permission_name;
    } // getProjectPermissionName
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return 'projects';
    } // getContextDomain
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return 'projects/' . $this->getProjectId();
    } // getContextPath

	  /**
	   * Cached access log helper instance
	   *
	   * @var IAccessLogImplementation
	   */
	  private $access_log = false;

	  /**
	   * Return access log helper instance
	   *
	   * @return IAccessLogImplementation
	   */
	  function accessLog() {
		  if($this->access_log === false) {
			  $this->access_log = new IAccessLogImplementation($this);
		  } // if

		  return $this->access_log;
	  } // accessLog
    
    // ---------------------------------------------------
    //  Attribute manipulation
    // ---------------------------------------------------
    
    /**
     * Return formatted priority
     *
     * @param Language $language
     * @return string
     */
    function getFormattedPriority($language = null) {
      switch($this->getPriority()) {
        case PRIORITY_LOWEST:
          return lang('Lowest', null, true, $language);
        case PRIORITY_LOW:
          return lang('Low', null, true, $language);
        case PRIORITY_HIGH:
          return lang('High', null, true, $language);
        case PRIORITY_HIGHEST:
          return lang('Highest', null, true, $language);
        default:
          return lang('Normal', null, true, $language);
      } // switch
    } // getFormattedPriority
    
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

      $result['body'] = $this->getBody();
      $result['version'] = $this->getVersion();

      if($this instanceof IComplete) {
        $result['priority'] = $this->getPriority();
        $result['due_on'] = $this->getDueOn();
      } // if

      $result['project_id'] = $this->getProjectId();
      $result['milestone_id'] = $this->getMilestoneId();

      if($detailed) {
        $result['project'] = $this->getProject() instanceof Project ? $this->getProject()->describe($user, false, $for_interface) : null;

        if(!($this instanceof Milestone)) {
          $result['milestone'] = $this->getMilestone() instanceof Milestone ? $this->getMilestone()->describe($user, false, $for_interface) : null;
        } // if

        if ($this->fieldExists('milestone_id')) {
        	$result['urls']['update_milestone'] = Router::assemble('project_object_update_milestone', array('project_slug' => $this->getProject()->getSlug(), 'object_id' => $this->getId()));
        } // if
      } // if

      $result['permissions']['can_move'] = $this->canMove($user);
      $result['permissions']['can_copy'] = $this->canCopy($user);

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

      //$result['body'] = $this->getBody();
      $result['version'] = $this->getVersion();

      if($this instanceof IComplete) {
        $result['priority'] = $this->getPriority();
        $result['due_on'] = $this->getDueOn();
      } // if

      $result['project_id'] = $this->getProjectId();
      $result['milestone_id'] = $this->getMilestoneId();

      if($detailed) {
        $result['project'] = $this->getProject() instanceof Project ? $this->getProject()->describeForApi($user) : null;

        if(!($this instanceof Milestone)) {
          $result['milestone'] = $this->getMilestone() instanceof Milestone ? $this->getMilestone()->describeForApi($user) : null;
        } // if

        if ($this->fieldExists('milestone_id')) {
          $result['urls']['update_milestone'] = Router::assemble('project_object_update_milestone', array('project_slug' => $this->getProject()->getSlug(), 'object_id' => $this->getId()));
        } // if

        $result['permissions']['can_move'] = $this->canMove($user);
        $result['permissions']['can_copy'] = $this->canCopy($user);
      } // if

      return $result;
    } // describeForApi

    /**
     * Disable describe cache
     *
     * @return bool
     */
    function disableDescribeCache() {
      return true;
    } // disableDescribeCache
    
    // ---------------------------------------------------
    //  Relations
    // ---------------------------------------------------
    
    /**
     * Return company instance
     *
     * @return Company
     */
    function getCompany() {
      return $this->getProject() instanceof Project && $this->getProject()->getCompany() instanceof Company ? $this->getProject()->getCompany() : Companies::findOwnerCompany();
    } // getCompany
    
    /**
     * Return parent project
     *
     * @return Project
     */
    function &getProject() {
      return DataObjectPool::get('Project', $this->getProjectId());
    } // getProject
    
    /**
     * Set parent project
     *
     * @param Project $project
     * @return Project
     * @throws InvalidInstanceError
     */
    function setProject(Project $project) {
      if($project instanceof Project) {
        $this->setProjectId($project->getId());
        $this->routing_context_params = false;
      } else {
        throw new InvalidInstanceError('project', $project, 'Project');
      } // if
      
      return $this->getProject();
    } // setProject
    
    /**
     * Set value of project_id field
     *
     * @param integer $value
     * @return integer
     */
    function setProjectId($value) {
    	$this->routing_context_params = false;
    	return parent::setProjectId($value);
    } // setProjectId
    
    /**
     * Return parent milestone
     *
     * @return Milestone
     */
    function &getMilestone() {
      $milestone = DataObjectPool::get('Milestone', $this->getMilestoneId());
      return ($milestone instanceof Milestone && $milestone->getState() >= STATE_ARCHIVED) ? $milestone : null;
    } // getMilestone
    
    /**
     * Set milestone for this object
     * 
     * @param Milestone $milestone
     * @return Milestone
     * @throws InvalidInstanceError
     */
    function setMilestone($milestone) {
      if($milestone instanceof Milestone) {
        $this->setMilestoneId($milestone->getId());
      } elseif($milestone === null) {
        $this->setMilestoneId(0);
      } else {
        throw new InvalidInstanceError('milestone', $milestone, '$milestone should be an instance of Milestone class or NULL');
      } // if
      
      return $milestone;
    } // setMilestone
    
    // ---------------------------------------------------
    //  Due on
    // ---------------------------------------------------
    
    /**
     * Returns true if this object is late
     *
     * @return boolean
     */
    function isLate() {
      $now = DateTimeValue::now();
      
      $due_on = $this->getDueOn();
      if($due_on instanceof DateTimeValue) {
        return ($due_on->getTimestamp() < $now->getTimestamp()) && !$this->isToday();
      } // if
      return false;
    } // isLate
    
    /**
     * Returns true if this object is due today
     *
     * @return boolean
     */
    function isToday() {
      return $this->getDueOn() instanceof DateTimeValue ? $this->getDueOn()->isToday() : false;
    } // isToday
    
    /**
     * Returns true if this object is due in future
     *
     * @return boolean
     */
    function isUpcoming() {
      return $this->getDueOn() instanceof DateTimeValue ? ($this->getDueOn()->getTimestamp() > DateTimeValue::now()->getTimestamp()) && !$this->isToday() : false;
    } // isUpcoming
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Routing context name
     *
     * @var string
     */
    protected $routing_context = false;
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      if($this->routing_context === false) {
        $this->routing_context = 'project_' . Inflector::underscore(get_class($this));
      } // if
      
      return $this->routing_context;
    } // getRoutingContext
    
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
          Inflector::underscore(get_class($this)) . '_id' => $this->getId(),
        );
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
    
    /**
     * State helper instance
     *
     * @var IProjectObjectStateImplementation
     */
    private $state = false;
    
    /**
     * Return state helper instance
     *
     * @return IProjectObjectStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new IProjectObjectStateImplementation($this);
      } // if
      
      return $this->state;
    } // state
    
    /**
     * Cached visibility helper
     *
     * @var IVisibilityImplementation
     */
    private $visibility = false;
    
    /**
     * Return visibility helper
     *
     * @return IVisibilityImplementation
     */
    function visibility() {
      if($this->visibility === false) {
        $this->visibility = new IVisibilityImplementation($this);
      } // if
      
      return $this->visibility;
    } // visibility
    
    /**
     * Cached history helper
     *
     * @var IHistoryImplementation
     */
    private $history = false;
    
    /**
     * Return history helper instance
     * 
     * @return IHistoryImplementation
     */
    function history() {
      if($this->history === false) {
        $this->history = new IHistoryImplementation($this, array('project_id', 'milestone_id'));
      } // if
      
      return $this->history;
    } // history
    
    /**
     * Cached reminder helper instance
     *
     * @var IRemindersImplementation
     */
    private $reminders = false;
    
    /**
     * Return reminders helper instance
     * 
     * @return IRemindersImplementation
     */
    function reminders() {
      if($this->reminders === false) {
        $this->reminders = new IRemindersImplementation($this);
      } // if
      
      return $this->reminders;
    } // reminders
    
    /**
     * Cached access log helper instance
     *
     * @var IProjectObjectActivityLogsImplementation
     */
    private $activity_logs = false;
    
    /**
     * Return activity logs helper instance
     * 
     * @return IProjectObjectActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new IProjectObjectActivityLogsImplementation($this);
      } // if
      
      return $this->activity_logs;
    } // activityLogs

    // ---------------------------------------------------
    //  Copy and move
    // ---------------------------------------------------
    
    /**
     * Copy this object to $project
     *
     * @param Project $project
     * @param array $update_attributes
     * @param boolean $bulk
     * @return ProjectObject
     * @throws Exception
     */
    function copyToProject(Project $project, $update_attributes = null, $bulk = false) {
      try {
        DB::beginWork('Copy project object to a project @ ' . __CLASS__);
      
        $copy = $this->copy();
        
        $copy->setProjectId($project->getId());
        
        if(is_foreachable($update_attributes)) {
          foreach($update_attributes as $attribute => $value) {
            if($this->fieldExists($attribute)) {
              $copy->setFieldValue($attribute, $value);
            }//if
          } // foreach
        } // if

        if($this instanceof IComplete) {
          $copy->setCompletedOn(null);
          $copy->setCompletedById(null);
          $copy->setCompletedByName(null);
          $copy->setCompletedByEmail(null);
        } // if

        if(!($this instanceof Milestone) && (!is_array($update_attributes) || !isset($update_attributes['milestone_id'])) && $copy->fieldExists('milestone_id')) {
          $copy->setMilestoneId(0); // Reset milestone ID, if it's not auto-populated by the parent milestone
        } // if

        $copy->setCreatedBy(Authentication::getLoggedUser());

        if (!isset($update_attributes['created_on']) || !$update_attributes['created_on'] instanceof DateTimeValue) {
          $copy->setCreatedOn(new DateTimeValue());
        } // if

        if($copy instanceof IComments) {
          $this->setIsLocked(false);
        } // if
        
        $copy->save();

        if($this instanceof ISubscriptions) {
          $this->subscriptions()->cloneTo($copy, !$bulk);
        } // if
        
        if($this instanceof IAssignees) {
          $this->assignees()->cloneTo($copy, !$bulk);
        } // if
        
        if($this instanceof IComments) {
          $this->comments()->cloneTo($copy);
        } // if
        
        if($this instanceof IAttachments) {
          $this->attachments()->cloneTo($copy);
        } // if
        
        if($this instanceof ISubtasks) {
          $this->subtasks()->cloneTo($copy);
        } // if
        
        EventsManager::trigger('on_project_object_copied', array(&$this, &$copy, &$project));
        
        DB::commit('Project object copied to a project @ ' . __CLASS__);
        
        ProjectProgress::dropProjectProgressCache($this->getProject());
        ProjectProgress::dropProjectProgressCache($project);
      } catch(Exception $e) {
        DB::rollback('Failed to copy project object to a project @ ' . __CLASS__);
        throw $e;
      } // try
      
      return $copy;
    } // copyToProject


    /**
     * Copy to project and preserve category
     *
     * This function will search for a catgory with the same name in target
     * project and create a relation if it exists
     *
     * @param Project $project
     * @param array $update_attributes
     * @param bool $bulk
     * @return ProjectObject
     */
    function copyToProjectAndPreserveCategory(Project $project, $update_attributes = null, $bulk = false) {
      if($this instanceof ICategory && $this->getCategoryId()) {
        $target_category_id = Categories::getMatchingCategoryId($this->getCategoryId(), $project);
        
        if($update_attributes) {
          $update_attributes['category_id'] = $target_category_id;
        } else {
          $update_attributes = array('category_id' => $target_category_id);
        } // if
      } // if
      
      return $this->copyToProject($project, $update_attributes, $bulk);
    } // copyToProjectAndPreserveCategory
    
    /**
     * Move this object to $project
     *
     * @param Project $project
     * @param mixed $update_attributes
     * @throws Exception
     */
    function moveToProject(Project $project, $update_attributes = null) {
      if($this->getProjectId() == $project->getId()) {
        return; // already in target $project
      } // if
      
      try {
        DB::beginWork('Moving object to project @ ' . __CLASS__);
      
        $old_project = $this->getProject();
        $this->setProject($project);
        
        if($this->fieldExists('milestone_id')) {
          if(empty($update_attributes)) {
            $update_attributes = array('milestone_id' => 0);
          } else {
            if(!isset($update_attributes['milestone_id'])) {
              $update_attributes['milestone_id'] = 0;
            } // if
          } // if
        } // if
        
        if(is_foreachable($update_attributes)) {
          foreach($update_attributes as $attribute => $value) {
            if($this->fieldExists($attribute)) {
              $this->setFieldValue($attribute, $value);
            } //if
          } // foreach
        } // if

        if ($this instanceof IAssignees || $this instanceof ISubscriptions) {
          $project_users_ids = $project->users()->getIds();
          $class_name = Inflector::pluralize(get_class($this));
          $valid_class_and_method = class_exists($class_name) && method_exists($class_name, 'canAccess');

          // check assignees
          if ($this instanceof IAssignees) {
            // examine presence and permissions of assignee in target project
            if ($this->assignees()->getAssignee() instanceof User) {
              if ($project_users_ids && !in_array($this->getAssigneeId(), $project_users_ids)) {
                $this->setAssigneeId(null);
              } else {
                if ($valid_class_and_method && !$class_name::canAccess($this->assignees()->getAssignee(), $project)) {
                  $this->setAssigneeId(null);
                } // if
              } // if
            } // if

            // examine presence and permissions of other assignees in target project
            if ($this->assignees()->getSupportsMultipleAssignees() && $this->getAssigneeId()) {
              $new_assignees = array();
              if (is_foreachable($this->assignees()->getOtherAssignees())) {
                foreach ($this->assignees()->getOtherAssignees() as $other_assignee) {
                  if (!in_array($other_assignee->getId(), $project_users_ids)) { // not in target project
                    continue;
                  } else if ($valid_class_and_method && $class_name::canAccess($other_assignee, $project)) {
                    $new_assignees[] = $other_assignee;
                  } // if
                } // foreach
              } // if

              $this->assignees()->setOtherAssignees($new_assignees);
            } //if

          } // if assignees


          // check subscriptions
          if ($this instanceof ISubscriptions) {
            $save_anonymous_subscribers = (boolean) array_var($update_attributes, 'save_anonymous_subscribers', false, true);
            $subscribers = $this->subscriptions()->get();
            if (is_foreachable($subscribers)) {
              $new_subscribers = array();
              foreach ($subscribers as $subscriber) {
                if($subscriber instanceof AnonymousUser) {
                  if($save_anonymous_subscribers) {
                    $new_subscribers[] = $subscriber;
                  } else {
                    continue;
                  } //if
                } else {
                  if (!in_array($subscriber->getId(), $project_users_ids)) { // not in target project
                    continue;
                  } else if ($valid_class_and_method && $class_name::canAccess($subscriber, $project)) {
                    $new_subscribers[] = $subscriber;
                  } // if
                } //if
              } // foreach

              $this->subscriptions()->set($new_subscribers);
            } // if
          } // if subscriptions
        } // if
        
        $this->save();
        
        DB::commit('Moved to project @ ' . __CLASS__);
        
        EventsManager::trigger('on_project_object_moved', array(&$this, &$old_project, &$project));
        
        ProjectProgress::dropProjectProgressCache($old_project);
        ProjectProgress::dropProjectProgressCache($project);
      } catch(Exception $e) {
        DB::rollback('Failed to move to project @ ' . __CLASS__);
        throw $e;
      } // try
    } // moveToProject
    
    /**
     * Move to $project and keep category relation if there's a category with 
     * the same name in the target project
     *
     * @param Project $project
     * @param mixed $update_attributes
     */
    function moveToProjectAndPreserveCategory(Project $project, $update_attributes = null) {

      if($this instanceof ICategory) {
        $target_category_id = $this->getCategoryId() ? Categories::getMatchingCategoryId($this->getCategoryId(), $project) : null;
        
        if($update_attributes) {
          $update_attributes['category_id'] = $target_category_id;
        } else {
          $update_attributes = array('category_id' => $target_category_id);
        } // if
      } // if
      $this->moveToProject($project, $update_attributes);
    } // moveToProjectAndPreserveCategory
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access this object
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      $project = $this->getProject();

      return $project instanceof Project && $user->getMinVisibility() <= $this->getVisibility() && ProjectObjects::canAccess($user, $project, $this->getProjectPermissionName());
    } // canView
    
    /**
     * Returns true if $user can update this object
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      if($this->getState() < STATE_VISIBLE) {
        return $user->isAdministrator(); // Only administrators can update archived objects
      } // if

      $project = $this->getProject();
      
      if($project instanceof Project) {
        if($user->isAdministrator() || $user->isProjectManager() || $project->isLeader($user)) {
          return true; // administrators and project managers have all permissions
        } // if
        
        if(($this->getVisibility() < VISIBILITY_NORMAL) && !$user->canSeePrivate()) {
          return false;
        } // if
        
        if($this->getProjectPermissionName() && ($user->projects()->getPermission($this->getProjectPermissionName(), $project) >= ProjectRole::PERMISSION_MANAGE)) {
          return true; // Management permissions
        } // if
        
        if($this->getCreatedById() == $user->getId()) {
          return true; // Author
        } // if
        
        return $this instanceof IAssignees && $this->assignees()->isAssignee($user);
      } else {
        return false;
      } // if
    } // canEdit
    
    /**
     * Returns true if $user can change objects visibility
     *
     * @param User $user
     * @return boolean
     */
    function canChangeVisibility(User $user) {
      return $this->canEdit($user) && $user->canSeePrivate();
    } // canChangeVisibility
    
    /**
     * Returns true if $user can delete this object
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      $project = $this->getProject();

      if($project instanceof Project) {
        if($user->isProjectManager() || $this->getProject()->isLeader($user)) {
          return true; // administrators and project managers have all permissions
        } // if

        if(($this->getVisibility() < VISIBILITY_NORMAL) && !$user->canSeePrivate()) {
          return false;
        } // if

        if($this->getProjectPermissionName() && $user->projects()->getPermission($this->getProjectPermissionName(), $project) >= ProjectRole::PERMISSION_MANAGE) {
          return true;
        } // if

        // Author in the next three hours
        if($this->getCreatedById() == $user->getId()) {
          $created_on = $this->getCreatedOn();
          return time() < ($created_on->getTimestamp() + 10800);
        } // if
      } // if
      
      return false;
    } // canDelete
    
    /**
     * Check if specific user can move this object
     *
     * @param User $user
     * @return boolean
     */
    function canMove(User $user) {
      return $this->getState() == STATE_VISIBLE && ($user->isProjectManager() || $this->getProject()->isLeader($user));
    } // canMove
    
    /**
     * Return true if specific user can copy this object
     *
     * @param User $user
     * @return boolean
     */
    function canCopy(User $user) {
      return $user->isProjectManager() || $this->getProject()->isLeader($user);
    } // canCopy
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return move object URL
     *
     * @return string
     */
    function getMoveUrl() {
      return Router::assemble($this->getRoutingContext() . '_move_to_project', $this->getRoutingContextParams());
    } // getMoveUrl
    
    /**
     * Return copy object URL
     *
     * @return string
     */
    function getCopyUrl() {
      return Router::assemble($this->getRoutingContext() . '_copy_to_project', $this->getRoutingContextParams());
    } // getCopyUrl
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($interface == AngieApplication::INTERFACE_DEFAULT) {
        $is_trashed = $this instanceof IState ? $this->getState() == STATE_TRASHED : false;

        if($this->canMove($user) && !$is_trashed) {
          $options->add('move_to_project', array(
            'text' => lang('Move to Project'),
            'url' => $this->getMoveUrl(),
          	'onclick' => new FlyoutFormCallback($this->getUpdatedEventName(), array(
              'success_message' => lang(':type has been moved to selected project', array('type' => $this->getVerboseType())), 
          		'width' => 'narrow'
            )),  
          ), true);
        } // if
        
        if($this->canCopy($user) && !$is_trashed) {
          $options->add('copy_to_project', array(
            'text' => lang('Copy to Project'),
            'url' => $this->getCopyUrl(),
          	'onclick' => new FlyoutFormCallback($this->getCreatedEventName(), array(
              'success_message' => lang(':type has been copied to selected project', array('type' => $this->getVerboseType())), 
          	  'width' => 'narrow'
            )),
          ), true);
        } // if
        
        if($this instanceof ISharing && $this->sharing()->canChangeSettings($user)) {
          $options->add('sharing_settings', array(
            'text' => lang('Sharing'),
            'url' => $this->sharing()->getSettingsUrl(),
          	'onclick' => new FlyoutCallback(array(
              'width'         => 'narrow'
          	)),
          ), true);
        } // if
      } // if

      parent::prepareOptionsFor($user, $options, $interface);
    } // prepareOptionsFor
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * If $flags_only is true this function will only validate system flags:
     * project_id, created_by_name, created_by_email and so on. Content fields
     * will be skipped
     *
     * @param ValidationErrors $errors
     * @param boolean $flags_only
     */
    function validate(ValidationErrors &$errors, $flags_only = false) {
      
      // ---------------------------------------------------
      //  Content
      // ---------------------------------------------------
      
      if(!$flags_only) {
        if(!$this->validatePresenceOf('name')) {
          $errors->addError(lang('Name field is required'), 'name');
        } // if
        if(!$this->validatePresenceOf('body')) {
          $errors->addError(lang('Content field is required'), 'body');
        } // if
      } // if
      
      // ---------------------------------------------------
      //  Flags
      // ---------------------------------------------------
      
      if(!$this->validatePresenceOf('project_id')) {
        $errors->addError(lang('Please select project'));
      } // if
      
      if(!$this->validatePresenceOf('type')) {
        $errors->addError(lang('Type flag value is required'));
      } // if
      
      if(!$this->validatePresenceOf('module')) {
        $errors->addError(lang('Module flag value is required'));
      } // if
      
      if($this->getCreatedById() == 0) {
        if(!$this->validatePresenceOf('created_by_name')) {
          $errors->addError(lang('Author name is required'));
        } // if
        
        if($this->validatePresenceOf('created_by_email')) {
          if(!is_valid_email($this->getCreatedByEmail())) {
            $errors->addError(lang('Authors email address is not valid'));
          } // if
        } else {
          $errors->addError(lang('Authors email address is required'));
        } // if
      } // if

      // check if the object is shared it cannot be set to private visibility
      if ($this instanceof ISharing && $this instanceof IVisibility) {
        if ($this->sharing()->isShared() && $this->getVisibility() === VISIBILITY_PRIVATE) {
          $errors->addError(lang('This :object is shared, it cannot be made private', array('object' => $this->getVerboseType(true))));
        } //if
      } //if

    } // validate
    
    /**
     * Save this object into the database
     *
     * @throws DBQueryError
     * @throws ValidationErrors
     */
    function save() {
      if($this->isModified()) {
        $this->setVersion($this->getVersion() + 1); // increment object version on save...
      } // if

      if ($this->fieldExists('milestone_id') && $this->isModifiedField('milestone_id')) {
        ProjectProgress::dropProjectProgressCache($this->getProjectId());
      } // if

      // set default project visibility if visiblity has not been set for a new object
      if($this->isNew() && !in_array('visibility', $this->getModifiedFields())) {
        $this->setVisibility($this->getProject()->getDefaultVisibility());
      }//if
      
      return parent::save();
    } // save
  
  }
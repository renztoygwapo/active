<?php

  // We need projects controller
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Tasks controller
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class TasksController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = TASKS_MODULE;
    
    /**
     * Active task
     *
     * @var Task
     */
    protected $active_task;
    
    /**
     * Complete controller delegate
     *
     * @var CompleteController
     */
    protected $complete_delegate;
    
    /**
     * Priority controller delegate
     * 
     * @var PriorityController
     */
    protected $priority_delegate;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Categories delegate instance
     *
     * @var CategoriesController
     */
    protected $categories_delegate;
    
    /**
     * Subtasks delegate instance
     *
     * @var SubtasksController
     */
    protected $subtasks_delegate;
    
    /**
     * Comments delegate instance
     *
     * @var CommentsController
     */
    protected $comments_delegate;
    
    /**
     * Subscriptions controller delegate
     *
     * @var SubscriptionsController
     */
    protected $subscriptions_delegate;
    
    /**
     * Attachments delegate instance
     *
     * @var AttachmentsController
     */
    protected $attachments_delegate;
    
    /**
     * Object tracking controller delegate
     *
     * @var ObjectTrackingController
     */
    protected $object_tracking_delegate;
    
    /**
     * Reminders controller instance
     * 
     * @var RemindersController
     */
    protected $reminders_delegate;
    
    /**
     * Sharing settings delegate
     *
     * @var SharingSettingsController
     */
    protected $sharing_settings_delegate;
    
    /**
     * Move to project delegate controller
     *
     * @var MoveToProjectController
     */
    protected $move_to_project_delegate;
    
    /**
     * Invoices controller delegate
     * 
     * @var InvoicesController
     */
    protected $invoice_delegate;
    
    /**
     * Schedule controller delegate
     * 
     * @var ScheduleController
     */
    protected $schedule_delegate;
    
    /**
     * Assignees controller delegate
     * 
     * @var AssigneesController
     */
    protected $assignees_delegate;
    
    /**
     * labels controller delegate
     * 
     * @var LabelsController
     */
    protected $labels_delegate;

	  /**
	   * Access logs controller delegate
	   *
	   * @var AccessLogsController
	   */
	  protected $access_logs_delegate;

	  /**
	   * History of changes controller delegate
	   *
	   * @var HistoryOfChangesController
	   */
	  protected $history_of_changes_delegate;
    
    /**
     * Actions that are exposed through API
     *
     * @var array
     */
    protected $api_actions = array('index', 'archive', 'view', 'add', 'edit');

    /**
     * Mapped priorities
     *
     * @var array
     */
    protected $priority_map = array();
    
    /**
     * Construct controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'tasks') {
        $this->complete_delegate = $this->__delegate('complete', COMPLETE_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->priority_delegate = $this->__delegate('priority', COMPLETE_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->categories_delegate = $this->__delegate('categories', CATEGORIES_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->subtasks_delegate = $this->__delegate('subtasks', SUBTASKS_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->comments_delegate = $this->__delegate('comments', COMMENTS_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->attachments_delegate = $this->__delegate('attachments', ATTACHMENTS_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->reminders_delegate = $this->__delegate('reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->sharing_settings_delegate = $this->__delegate('sharing_settings', SYSTEM_MODULE, 'project_task');
        $this->schedule_delegate = $this->__delegate('task_schedule', TASKS_MODULE, 'project_task');
        $this->move_to_project_delegate = $this->__delegate('move_to_project', SYSTEM_MODULE, 'project_task');
        $this->assignees_delegate = $this->__delegate('assignees', ASSIGNEES_FRAMEWORK_INJECT_INTO, 'project_task');
        $this->labels_delegate = $this->__delegate('labels', LABELS_FRAMEWORK_INJECT_INTO, 'project_task');
        
        if(AngieApplication::isModuleLoaded('tracking')) {
          $this->object_tracking_delegate = $this->__delegate('object_tracking', TRACKING_MODULE, 'project_task');
        } // if
        
        if(AngieApplication::isModuleLoaded('invoicing')) {
          $this->invoice_delegate = $this->__delegate('invoice_based_on', INVOICING_MODULE, 'project_task');
        } // if

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'project_task');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'project_task');
	      } // if

        $this->priority_map = array(
          PRIORITY_HIGHEST => lang('Highest'),
          PRIORITY_HIGH  => lang('High'),
          PRIORITY_NORMAL => lang('Normal'),
          PRIORITY_LOW  => lang('Low'),
          PRIORITY_LOWEST => lang('Lowest')
        );
      } // if
    } // __construct
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!Tasks::canAccess($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
      
      $this->wireframe->tabs->setCurrentTab('tasks');
      $this->wireframe->breadcrumbs->add('project_tasks', lang('Tasks'), Router::assemble('project_tasks', array('project_slug' => $this->active_project->getSlug())));

      // Shared page actions
      if(($this->request->isWebBrowser() || $this->request->isMobileDevice()) && in_array($this->request->getAction(), array('index', 'view'))) {
        $add_task_url = false;

        if(Tasks::canAdd($this->logged_user, $this->active_project)) {
          $add_task_url = Router::assemble('project_tasks_add', array('project_slug' => $this->active_project->getSlug()));
          $this->wireframe->actions->add('new_task', lang('New Task'), $add_task_url, array(
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'onclick' => new FlyoutFormCallback('task_created'),
            'primary' => true,
          ));
        } // if

        $this->response->assign('add_task_url', $add_task_url);
      } // if
      
      $task_id = $this->request->getId('task_id');
      if($task_id) {
        $this->active_task = Tasks::findByTaskId($this->active_project, $task_id);
      } // if
      
      if($this->active_task instanceof Task) {
        if (!$this->active_task->isAccessible()) {
          $this->response->notFound();
        } // if

        if ($this->active_task->getState() == STATE_ARCHIVED) {
          $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_tasks_archive', array('project_slug' => $this->active_project->getSlug())));
        } // if

        $this->wireframe->breadcrumbs->add('project_task', $this->active_task->getName(), $this->active_task->getViewUrl());
      } else {
        $this->active_task = new Task();
        $this->active_task->setProject($this->active_project);
      } // if
      
      $this->response->assign('active_task', $this->active_task);
      
      if($this->complete_delegate instanceof CompleteController) {
        $this->complete_delegate->__setProperties(array(
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if ($this->priority_delegate instanceof PriorityController) {
        $this->priority_delegate->__setProperties(array(
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if($this->categories_delegate instanceof CategoriesController) {
        $this->categories_delegate->__setProperties(array(
          'categories_context' => &$this->active_project,
          'routing_context' => 'project_task',
          'routing_context_params' => array('project_slug' => $this->active_project->getSlug()),
          'category_class' => 'TaskCategory',
          'active_object' => &$this->active_task
        ));
      } // if
      
      if($this->subtasks_delegate instanceof SubtasksController) {
        $this->subtasks_delegate->__setProperties(array(
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if($this->comments_delegate instanceof CommentsController) {
        $this->comments_delegate->__setProperties(array(
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if($this->subscriptions_delegate instanceof SubscriptionsController) {
        $this->subscriptions_delegate->__setProperties(array(
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if($this->attachments_delegate instanceof AttachmentsController) {
        $this->attachments_delegate->__setProperties(array(
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if($this->object_tracking_delegate instanceof ObjectTrackingController) {
        $this->object_tracking_delegate->__setProperties(array(
          'active_project' => &$this->active_project, 
          'active_tracking_object' => &$this->active_task,
        ));
      } // if
      
      if($this->reminders_delegate instanceof RemindersController) {
        $this->reminders_delegate->__setProperties(array(
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if($this->sharing_settings_delegate instanceof SharingSettingsController) {
        $this->sharing_settings_delegate->__setProperties(array(
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if($this->move_to_project_delegate instanceof MoveToProjectController) {
        $this->move_to_project_delegate->__setProperties(array(
          'active_project' => &$this->active_project,
          'active_object' => &$this->active_task,
        ));
      } // if
      
      if($this->invoice_delegate instanceof InvoiceBasedOnController) {
        $this->invoice_delegate->__setProperties(array(
          'active_object' => &$this->active_task
        ));
      } // if
      
      if($this->schedule_delegate instanceof TaskScheduleController) {
        $this->schedule_delegate->__setProperties(array(
          'active_object' => &$this->active_task
        ));
      } // if
      
      if ($this->assignees_delegate instanceof AssigneesController) {
        $this->assignees_delegate->__setProperties(array(
          'active_object' => &$this->active_task
        ));
      } // if
      
      if ($this->labels_delegate instanceof LabelsController) {
        $this->labels_delegate->__setProperties(array(
          'active_object' => &$this->active_task
        ));
      } // if

	    if ($this->access_logs_delegate instanceof AccessLogsController) {
		    $this->access_logs_delegate->__setProperties(array(
			    'active_object' => &$this->active_task
		    ));
	    } // if

	    if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		    $this->history_of_changes_delegate->__setProperties(array(
			    'active_object' => &$this->active_task
		    ));
	    } // if
      
    } // __construct
    
    /**
     * Show tasks index page
     */
    function index() {

      // Serve request made with web browser
      if($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();
                
        $this->response->assign(array(
          'tasks' => Tasks::findForObjectsList($this->active_project, $this->logged_user),
          'milestones' => Milestones::getIdNameMap($this->active_project),
          'categories' => Categories::getidNameMap($this->active_project, 'TaskCategory'),
          'labels' => Labels::getIdNameMap('AssignmentLabel'),
          'users' => Users::getIdNameMap(),
          'priority' => $this->priority_map,
          'can_add_task' => Tasks::canAdd($this->logged_user, $this->active_project),
          'can_manage_tasks' => Tasks::canManage($this->logged_user, $this->active_project),
          'manage_categories_url' => $this->active_project->availableCategories()->canManage($this->logged_user, 'TaskCategory') ? Router::assemble('project_task_categories', array('project_slug' => $this->active_project->getSlug())) : null,
          'in_archive'  => false,
          'clean_up_url' => Router::assemble('project_tasks_clean_up', array('project_slug' => $this->active_project->getSlug())),
          'to_clean_up' => $this->active_project->isLeader($this->logged_user) || $this->logged_user->isProjectManager() ? Tasks::countForCleanUp($this->logged_user, $this->active_project) : 0,
          'print_url' => Router::assemble('project_tasks', array('project_slug' => $this->active_project->getSlug(), 'print' => 1))
        ));
        
        // mass manager
        if (Tasks::canManage($this->logged_user, $this->active_project)) {
          $mass_manager = new MassManager($this->logged_user, $this->active_task);
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
        
      // Server request made with mobile device
      } elseif($this->request->isMobileDevice()) {
        $this->response->assign(array(
          'tasks' => DB::execute("SELECT id, name, category_id, milestone_id, integer_field_1 as task_id FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Task' AND project_id = ? AND completed_on IS NULL AND state >= ? AND visibility >= ? ORDER BY position, created_on", $this->active_project->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility()),
          'task_url' => Router::assemble('project_task', array('project_slug' => $this->active_project->getSlug(), 'task_id' => '--TASKID--')),
        ));
        
      // Printer
      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        $filter_by = $this->request->get('filter_by', null);
        
        // page title
        $filter_by_completion = array_var($filter_by, 'is_completed', null); 
        if ($filter_by_completion === '0') {
          $page_title = lang('Open Tasks in :project_name Project', array('project_name' => $this->active_project->getName()));
        } else if ($filter_by_completion === '1') {
          $page_title = lang('Completed Tasks in :project_name Project', array('project_name' => $this->active_project->getName()));
        } else {
          $page_title = lang('All Tasks in :project_name Project', array('project_name' => $this->active_project->getName()));
        } // if

        // maps
        $map = array();
        
        switch ($group_by) {
          case 'milestone_id':
            $map = Milestones::getIdNameMap($this->active_project);
            $map[0] = lang('Unknown Milestone');
            
            $getter = 'getMilestoneId';
            $page_title.= ' ' . lang('Grouped by Milestone');
            break;
          case 'category_id':
            $map = Categories::getidNameMap($this->active_project, 'TaskCategory');
            $map[0] = lang('Uncategorized');
            
            $getter = 'getCategoryId';
            $page_title.= ' ' . lang('Grouped by Category');
            break;
         case 'label_id':
            $map = Labels::getIdNameMap('AssignmentLabel');
            $map[0] = lang('No Label');
            
            $getter = 'getLabelId';
            $page_title.= ' ' . lang('Grouped by Label');
            break;
         case 'assignee_id':
            $map = Users::getIdNameMap();
            $map[0] = lang('No Assignee');
            
            $getter = 'getAssigneeId';
            $page_title.= ' ' . lang('Grouped by Assignee');
            break;
         case 'delegated_by_id':
            $map = Users::getIdNameMap();
            $map[0] = lang('No Delegate');
            
            $getter = 'getDelegatedById';
            $page_title.= ' ' . lang('Grouped by Delegate');
            break;
         case 'priority':
            $map = $this->priority_map;
            
            $getter = 'getPriority';
            $page_title.= ' ' . lang('Grouped by Priority');
            break;
            
        }//switch

        // find tasks
        $tasks = Tasks::findForPrint($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility(), $group_by, $filter_by);

        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map, $tasks, $getter, false);

        $this->response->assignByRef('tasks', $print_list);
        $this->response->assignByRef('map', $map);
        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));
        
      // API
      } elseif($this->request->isApiCall()) {
        $this->response->respondWithData(Tasks::findActiveByProject($this->active_project, $this->logged_user), array(
          'as' => 'tasks'
        ));
      } // if
    } // index
    
    /**
     * Show completed tasks (mobile devices only)
     */
    function archive() {
      if($this->request->isMobileDevice()) {
        $this->response->assign('tasks', Tasks::findCompletedByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility()));
      } else if ($this->request->isWebBrowser()) {

        $this->wireframe->list_mode->enable();
        $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_tasks_archive', array('project_slug' => $this->active_project->getSlug())));

        $this->response->assign(array(
          'tasks' => Tasks::findForObjectsList($this->active_project, $this->logged_user, STATE_ARCHIVED),
          'milestones' => Milestones::getIdNameMap($this->active_project),
          'categories' => Categories::getidNameMap($this->active_project, 'TaskCategory'),
          'labels' => Labels::getIdNameMap('AssignmentLabel'),
          'users' => Users::getIdNameMap(),
          'priority' => $this->priority_map,
          'can_add_task' => Tasks::canAdd($this->logged_user, $this->active_project),
          'can_manage_tasks' => Tasks::canManage($this->logged_user, $this->active_project),
          'in_archive' => true,
          'print_url' => Router::assemble('project_tasks_archive', array('project_slug' => $this->active_project->getSlug(), 'print' => 1))
        ));

        // mass manager
        if (Tasks::canManage($this->logged_user, $this->active_project)) {
          $this->active_task->setState(STATE_ARCHIVED);
          $mass_manager = new MassManager($this->logged_user, $this->active_task);
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if

        // Printer
      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));

        // page title
        $page_title = lang('Archived Tasks in :project_name Project', array('project_name' => $this->active_project->getName()));

        // find tasks
        $tasks = Tasks::findForPrint($this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility(), $group_by);

        // maps
        $map = array();

        switch ($group_by) {
          case 'milestone_id':
            $map = Milestones::getIdNameMap($this->active_project);
            $map[0] = lang('Unknown Milestone');

            $getter = 'getMilestoneId';
            $page_title.= ' ' . lang('Grouped by Milestone');
            break;
          case 'category_id':
            $map = Categories::getidNameMap($this->active_project, 'TaskCategory');
            $map[0] = lang('Uncategorized');

            $getter = 'getCategoryId';
            $page_title.= ' ' . lang('Grouped by Category');
            break;
          case 'label_id':
            $map = Labels::getIdNameMap('AssignmentLabel');
            $map[0] = lang('No Label');

            $getter = 'getLabelId';
            $page_title.= ' ' . lang('Grouped by Label');
            break;
          case 'assignee_id':
            $map = Users::getIdNameMap();
            $map[0] = lang('No Assignee');

            $getter = 'getAssigneeId';
            $page_title.= ' ' . lang('Grouped by Assignee');
            break;
          case 'delegated_by_id':
            $map = Users::getIdNameMap();
            $map[0] = lang('No Delegate');

            $getter = 'getDelegatedById';
            $page_title.= ' ' . lang('Grouped by Delegate');
            break;
          case 'priority':
            $map = $this->priority_map;

            $getter = 'getPriority';
            $page_title.= ' ' . lang('Grouped by Priority');
            break;

        }//switch

        $this->response->assignByRef('tasks', $tasks);
        $this->response->assignByRef('map', $map);
        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));

      } else if($this->request->isApiCall()) {
        $this->response->respondWithData(Tasks::findArchivedByProject($this->active_project, $this->logged_user), array(
          'as' => 'tasks',
        ));
      } else {
        $this->response->badRequest();
      } // if

    } // archive
    
    /**
     * Mass Edit action
     */
    function mass_edit() {
      if ($this->getControllerName() == 'tasks') {
        $this->mass_edit_objects = Tasks::findByIds($this->request->post('selected_item_ids'), STATE_ARCHIVED, $this->logged_user->getMinVisibility());
      } // if

      parent::mass_edit();
    } // mass_edit
    
    /**
     * Show single task
     */
    function view() {
      if($this->active_task->isLoaded()) {
        if($this->active_task->canView($this->logged_user)) {
          $this->wireframe->setPageObject($this->active_task, $this->logged_user);
          
          // API call
          if($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_task, array(
              'as' => 'task', 
              'detailed' => true, 
            ));
            
          // Regular web browser request
          } elseif($this->request->isWebBrowser()) {
            $this->wireframe->print->enable();

	          // log access to task
	          $this->active_task->accessLog()->log($this->logged_user);
            
            if($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
              $this->render();
            } else {
              if ($this->active_task->getState() == STATE_ARCHIVED) {
                $this->__forward('archive', 'archive');
              } else {
                $this->__forward('index', 'index');
              } // if
            } // if
            
          // Phone device request
          } elseif($this->request->isPhone()) {
            if($this->active_task->subtasks()->canAdd($this->logged_user)) {
              $this->wireframe->actions->beginWith('new_subtask', lang('New Subtask'), $this->active_task->subtasks()->getAddUrl(), array(
                'icon' => AngieApplication::getImageUrl('icons/navbar/add.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
              ));
            } // if

            if(AngieApplication::isModuleLoaded('tracking') && $this->active_task->tracking()->canAdd($this->logged_user)) {
              $this->wireframe->actions->remove(array('lock'));

              $this->wireframe->actions->addAfter('log_time', lang('Log Time'), $this->active_task->tracking()->getAddTimeUrl(), 'complete', array(
                'icon' => AngieApplication::getImageUrl('icons/navbar/add-time.png', TRACKING_MODULE, AngieApplication::INTERFACE_PHONE)
              ));
            } // if

            $this->wireframe->actions->remove(array('favorites_toggler'));
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view
    
    /**
     * Create a new task
     */
    function add() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(Tasks::canAdd($this->logged_user, $this->active_project)) {
                    
          $task_data = $this->request->post('task', array(
            'visibility' => $this->active_project->getDefaultVisibility(),
            'milestone_id' => $this->request->get('milestone_id'),
            'category_id' => $this->request->get('category_id')
          ));
          
          $this->response->assign(array(
            'add_task_url' => Router::assemble('project_tasks_add', array('project_slug' => $this->active_project->getSlug())), 
            'task_data' => $task_data
          ));
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating a new task @ ' . __CLASS__);
              
              $this->active_task->attachments()->attachUploadedFiles($this->logged_user);

              $created_on = $created_by = null;
              if($this->request->isApiCall()) {
                if(isset($task_data['created_on']) && $task_data['created_on']) {
                  $created_on = DateTimeValue::makeFromString($task_data['created_on']);
                } // if

                if(isset($task_data['created_by_id']) && $task_data['created_by_id']) {
                  $created_by = Users::findById($task_data['created_by_id']);
                } elseif(isset($task_data['created_by_email']) && $task_data['created_by_email'] && is_valid_email($task_data['created_by_email'])) {
                  $created_by = new AnonymousUser(array_var($task_data, 'created_by_name'), $task_data['created_by_email']);
                } // if
              } // if
            
              $this->active_task->setAttributes($task_data);

              if($created_on instanceof DateTimeValue) {
                $this->active_task->setCreatedOn($created_on);
              } // if

              if($created_by instanceof IUser) {
                $this->active_task->setCreatedBy($created_by);
              } // if

              $this->active_task->setProject($this->active_project);
              $this->active_task->setState(STATE_VISIBLE);

              // Allow users to set created_by user when submitting task via API
              if($this->request->isApiCall() && isset($task_data['created_by_name']) && $task_data['created_by_name'] && isset($task_data['created_by_email']) && is_valid_email($task_data['created_by_email'])) {
                $created_by = Users::findByEmail($task_data['created_by_email'], true);

                if(empty($created_by)) {
                  $created_by = new AnonymousUser($task_data['created_by_name'], $task_data['created_by_email']);
                } // if

                $this->active_task->setCreatedBy($created_by);
              } // if
              
              $this->active_task->save();
              
              if(AngieApplication::isModuleLoaded('tracking') && TrackingObjects::canAdd($this->logged_user, $this->active_project)) {
                $estimated_time = (float) $task_data['estimate_value'];
                $estimated_job_type = isset($task_data['estimate_job_type_id']) && $task_data['estimate_job_type_id'] ? JobTypes::findById($task_data['estimate_job_type_id']) : null;

                if($estimated_time > 0 && $estimated_job_type instanceof JobType) {
                  $estimate = $this->active_task->tracking()->setEstimate($estimated_time, $estimated_job_type, null, $this->logged_user, false);
                } // if
              } // if
              
              $this->active_task->subscriptions()->set(array_unique(array_merge(
                (array) $this->active_task->getAssigneeId(),
                (array) $this->logged_user->getId(),
                (array) $this->active_project->getLeaderId(),
                (array) array_var($task_data, 'subscribers', array())
              )), false);
                            
              DB::commit('Task created @ ' . __CLASS__);

              AngieApplication::notifications()
                ->notifyAbout('tasks/new_task', $this->active_task, $this->logged_user)
                ->sendToSubscribers();

              if(AngieApplication::behaviour()->isTrackingEnabled()) {
                $extra_event_tags = array();

                if($this->active_task->getVisibility() == VISIBILITY_PRIVATE) {
                  $extra_event_tags[] = 'private';
                } // if

                if(isset($estimate) && $estimate instanceof Estimate) {
                  $extra_event_tags[] = 'with_estimate';
                } // if

                AngieApplication::behaviour()->recordFulfilment($this->request->post('_intent_id'), $extra_event_tags, null, function() use ($extra_event_tags) {
                  return array('task_created', $extra_event_tags);
                });
              } // if
              
              if ($this->request->isPageCall()) {
                $this->flash->success('Task ":name" has been created', array('name' => $this->active_task->getName()));
                $this->response->redirectToUrl($this->active_task->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_task, array(
                  'as' => 'task',
                  'detailed' => true,
                ));
              } // if
            } catch(Exception $e) {
              DB::rollback('Failed to create task @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add
    
    /**
     * Update existing task
     */
    function edit() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_task->isLoaded()) {
          if($this->active_task->canEdit($this->logged_user)) {
            
            $task_data = $this->request->post('task');
            
            if(!is_array($task_data)) {
              $task_data = array(
                'name' => $this->active_task->getName(),
                'body' => $this->active_task->getBody(),
                'visibility' => $this->active_task->getVisibility(),
                'category_id' => $this->active_task->getCategoryId(),
                'label_id' => $this->active_task->getLabelId(),
                'milestone_id' => $this->active_task->getMilestoneId(),
                'priority' => $this->active_task->getPriority(),
                'assignee_id' => $this->active_task->getAssigneeId(),
                'other_assignees' => $this->active_task->assignees()->getOtherAssigneeIds(),
                'due_on' => $this->active_task->getDueOn(),
                'custom_field_1' => $this->active_task->getCustomField1(),
                'custom_field_2' => $this->active_task->getCustomField2(),
                'custom_field_3' => $this->active_task->getCustomField3(),
              );

              if(AngieApplication::isModuleLoaded('tracking')) {
                $task_data['estimate'] = $this->active_task->tracking()->getEstimate() instanceof Estimate ? $this->active_task->tracking()->getEstimate()->getValue() : null;
                $task_data['estimate_job_type_id'] = $this->active_task->tracking()->getEstimate() instanceof Estimate ? $this->active_task->tracking()->getEstimate()->getJobTypeId() : null;
              } // if
            } // if

            $this->response->assign(array(
              'task_data' => $task_data,
            ));

	          $on_calendar = $this->request->get('on_calendar', false);
            
            if($this->request->isSubmitted()) {
              $current_assignee = $this->active_task->assignees()->getAssignee();

              try {
                DB::beginWork('Saving task changes @ ' . __CLASS__);
                
                $this->active_task->setAttributes($task_data);
                $this->active_task->attachments()->attachUploadedFiles($this->logged_user);
                
                $this->active_task->save();
                
                if(AngieApplication::isModuleLoaded('tracking') && TrackingObjects::canAdd($this->logged_user, $this->active_project)) {
                  $estimate_value = isset($task_data['estimate_value']) && $task_data['estimate_value'] ? (float) $task_data['estimate_value'] : null;
                  $estimate_job_type = isset($task_data['estimate_job_type_id']) && $task_data['estimate_job_type_id'] ? JobTypes::findById($task_data['estimate_job_type_id']) : null;
                  $estimate_comment = isset($task_data['estimate_comment']) ? $task_data['estimate_comment'] : null;
                  
                  if($estimate_value > 0 && $estimate_job_type instanceof JobType) {
                    $this->active_task->tracking()->setEstimate($estimate_value, $estimate_job_type, $estimate_comment, $this->logged_user);
                  } else {
                    if($this->active_task->tracking()->getEstimate() instanceof Estimate) {
                      $this->active_task->tracking()->setEstimate($estimate_value, $estimate_job_type, $estimate_comment, $this->logged_user);
                    } // if
                  } // if
                } // if
                
                DB::commit('Task changes saved @ ' . __CLASS__);

                $this->active_task->assignees()->notifyOnReassignment($current_assignee, $this->active_task->assignees()->getAssignee(), $this->logged_user);
                
                if($this->request->isPageCall()) {
                  $this->response->redirectToUrl($this->active_task->getViewUrl());
                } else {
	                if ($on_calendar && $this->active_task instanceof ICalendarEventContext) {
		                $this->response->respondWithData($this->active_task->calendar_event_context()->describe($this->logged_user), array(
			                'as' => 'calendar_event',
			                'detailed' => true,
		                ));
	                } else {
		                $this->response->respondWithData($this->active_task, array(
			                'as' => 'task',
			                'detailed' => true,
		                ));
	                } // if
                } // if
              } catch(Exception $e) {
                DB::rollback('Failed to save task changes @ ' . __CLASS__);
                
                if($this->request->isPageCall()) {
                  $this->response->assign('errors', $e);
                } else {
                  $this->response->exception($e);
                } // if
              } // try
            } else {
	            if ($on_calendar) {
		            $this->setView('_calendar_task_form');
	            } // if
            } // if
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // edit
    
    /**
     * Reorder tasks
     */
    function reorder() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $object_ids = $this->request->post('object_ids');

        if (is_foreachable($object_ids)) {
          try {
            $counter = 0;

            foreach ($object_ids as $object_id) {
              DB::execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET position = ? WHERE id = ? AND type = ?', $counter++, $object_id, 'Task');
            } // foreach

            $this->response->ok();
          } catch (Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->badRequest();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // reorder

    /**
     * Move old tasks to archive
     */
    function clean_up() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->logged_user->isProjectManager() || $this->active_project->isLeader($this->logged_user)) {
          $archived_tasks = array();
          $tasks = Tasks::findForCleanup($this->logged_user, $this->active_project);

          if($tasks) {
            try {
              DB::beginWork('Archiving tasks @ ' . __CLASS__);

              foreach($tasks as $task) {
                $task->state()->archive();

                if($task->getState() == STATE_ARCHIVED) {
                  $archived_tasks[] = $task->getId();
                } // if
              } // foreach

              DB::commit('Tasks archived @ ' . __CLASS__);
            } catch(Exception $e) {
              DB::rollback('Failed to clean up tasks @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if

          $this->response->respondWithData($archived_tasks);
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // clean_up
  
  }
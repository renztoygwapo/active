<?php

  // We need ProjectController
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Milestones controller
   *
   * @package activeCollab.modules.milestones
   * @subpackage models
   */
  class MilestonesController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = SYSTEM_MODULE;
    
    /**
     * Selected milestone
     *
     * @var Milestone
     */
    protected $active_milestone;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
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
     * Reminders controller instance
     * 
     * @var RemindersController
     */
    protected $reminders_delegate;
    
    /**
     * Schedule controller instance
     * 
     * @var ScheduleController
     */
    protected $schedule_delegate;
    
    /**
     * Move to project delegate controller
     *
     * @var MoveToProjectController
     */
    protected $move_to_project_delegate;
    
    /**
     * Invoice controller delegate
     * 
     * @var InvoiceBasedOnController
     */
    protected $invoice_delegate;
    
    /**
     * Assignees controller delegate
     * 
     * @var AssigneesController
     */
    protected $assignees_delegate;

	  /**
	   * Access log controller delegate
	   *
	   * @var AccessLogController
	   */
	  protected $access_logs_delegate;

	  /**
	   * History of changes controller delegate
	   *
	   * @var HistoryOfChangesController
	   */
	  protected $history_of_changes_delegate;
    
    /**
     * Actions available through API
     *
     * @var array
     */
    protected $api_actions = array('index', 'archive', 'view', 'add', 'edit');
    
    /**
     * Construct milestones controller
     *
     * @param Request $parent
     * @param string $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'milestones') {
        $this->complete_delegate = $this->__delegate('complete', COMPLETE_FRAMEWORK_INJECT_INTO, 'project_milestone');
        $this->priority_delegate = $this->__delegate('priority', COMPLETE_FRAMEWORK_INJECT_INTO, 'project_milestone');
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'project_milestone');
        $this->comments_delegate = $this->__delegate('comments', COMMENTS_FRAMEWORK_INJECT_INTO, 'project_milestone');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_milestone');
        $this->reminders_delegate = $this->__delegate('reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'project_milestone');
        $this->schedule_delegate = $this->__delegate('milestone_schedule', SYSTEM_MODULE, 'project_milestone');
        $this->move_to_project_delegate = $this->__delegate('move_to_project', SYSTEM_MODULE, 'project_milestone');
        $this->assignees_delegate = $this->__delegate('assignees', ASSIGNEES_FRAMEWORK_INJECT_INTO, 'project_milestone');
        
        if(AngieApplication::isModuleLoaded('invoicing')) {
          $this->invoice_delegate = $this->__delegate('invoice_based_on', INVOICING_MODULE, 'project_milestone');
        } // if

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'project_milestone');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'project_milestone');
	      } // if
      } // if
    } // __construct
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!Milestones::canAccess($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
      
      $milestones_url = Router::assemble('project_milestones', array('project_slug' => $this->active_project->getSlug()));
      
      $this->wireframe->tabs->setCurrentTab('milestones');
      $this->wireframe->breadcrumbs->add('milestones', lang('Milestones'), $milestones_url);
      
      $milestone_id = $this->request->getId('milestone_id');
      if($milestone_id) {
        $this->active_milestone = ProjectObjects::findById($milestone_id);
      } // if
      
      if($this->active_milestone instanceof Milestone) {
        if (!$this->active_milestone->isAccessible()) {
          $this->response->notFound();
        } // if

        if($this->active_milestone->getCompletedOn()) {
          $this->wireframe->breadcrumbs->add('milestones_archive', lang('Archive'), Router::assemble('project_milestones_archive', array(
            'project_slug' => $this->active_project->getSlug(),
          )));
        } // if
        
        $this->wireframe->breadcrumbs->add('milestone', $this->active_milestone->getName(), $this->active_milestone->getViewUrl());
      } else {
        $this->active_milestone = new Milestone();
        $this->active_milestone->setProject($this->active_project);
      } // if
      
      if($this->active_milestone->getProjectId() != $this->active_project->getId()) {
        $this->response->notFound();
      } // if
      
      $this->smarty->assign(array(
        'active_milestone' => $this->active_milestone,
        'milestones_url' => $milestones_url,
        'add_milestone_url' => Router::assemble('project_milestones_add', array('project_slug' => $this->active_project->getSlug())),
      ));

      if (($this->request->isWebBrowser() || $this->request->isMobileDevice()) && in_array($this->request->getAction(), array('index', 'view'))) {
        if(Milestones::canAdd($this->logged_user, $this->active_project)) {
          $flyout_options = false;
          if ($this->request->getAction() == 'view') {
            $flyout_options = array('success_message' => lang('Milestone has been created successfully'));
          } // if
          
          $this->wireframe->actions->add('new_milestone', lang('New Milestone'), Router::assemble('project_milestones_add', array('project_slug' => $this->active_project->getSlug())), array(
            'onclick' => new FlyoutFormCallback('milestone_created', $flyout_options),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'primary' => true
          ));
        } // if
      } // if
      
      if($this->complete_delegate instanceof CompleteController) {
        $this->complete_delegate->__setProperties(array(
          'active_object' => &$this->active_milestone,
        ));
      } // if
      
      if ($this->priority_delegate instanceof PriorityController) {
        $this->priority_delegate->__setProperties(array(
          'active_object' => &$this->active_milestone
        ));
      } // if
      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_milestone, 
        ));
      } // if
      
      if($this->comments_delegate instanceof CommentsController) {
        $this->comments_delegate->__setProperties(array(
          'active_object' => &$this->active_milestone, 
        ));
      } // if
      
      if($this->subscriptions_delegate instanceof SubscriptionsController) {
        $this->subscriptions_delegate->__setProperties(array(
          'active_object' => &$this->active_milestone, 
        ));
      } // if
      
      if($this->reminders_delegate instanceof RemindersController) {
        $this->reminders_delegate->__setProperties(array(
          'active_object' => &$this->active_milestone, 
        ));
      } // if
      
      if ($this->schedule_delegate instanceof MilestoneScheduleController) {
        $this->schedule_delegate->__setProperties(array(
          'active_object' => $this->active_milestone,
        ));
      } // if
      
      if($this->move_to_project_delegate instanceof MoveToProjectController) {
        $this->move_to_project_delegate->__setProperties(array(
          'active_project' => &$this->active_project,
          'active_object' => &$this->active_milestone,
        ));
      } // if
      
      if($this->invoice_delegate instanceof InvoiceBasedOnController) {
        $this->invoice_delegate->__setProperties(array(
          'active_object' => &$this->active_milestone
        ));
      } // if
      
      if ($this->assignees_delegate instanceof AssigneesController) {
        $this->assignees_delegate->__setProperties(array(
          'active_object' => &$this->active_milestone
        ));
      } // if

	    if ($this->access_logs_delegate instanceof AccessLogsController) {
		    $this->access_logs_delegate->__setProperties(array(
			    'active_object' => &$this->active_milestone
		    ));
	    } // if

	    if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		    $this->history_of_changes_delegate->__setProperties(array(
			    'active_object' => &$this->active_milestone
		    ));
	    } // if
    } // __construct
    
    /**
     * Show milestones index page
     */
    function index() {
      AngieApplication::useHelper('datetime', GLOBALIZATION_FRAMEWORK, 'modifier');
      
      // Phone call
      if($this->request->isPhone()) {
        $milestones = Milestones::findActiveByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility());
        $this->response->assign('milestones', $milestones);

      // API call
      } elseif($this->request->isApiCall()) {
        $milestones = Milestones::findActiveByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility());
        $this->response->respondWithData($milestones, array('as' => 'milestones'));
        

      } elseif($this->request->isPrintCall()) {
        $page_title = lang('All Milestones in :project_name Project', array('project_name' => $this->active_project->getName()));

        // find tasks
        $milestones = Milestones::findForPrint($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility());

        $this->response->assign(array(
          'page_title' => $page_title,
          'milestones' => $milestones
        ));

      } else {
        // Web browser request
        AngieApplication::useWidget('timeline_diagram', SYSTEM_MODULE);
        $this->wireframe->print->enable();

        if(Milestones::canManage($this->logged_user, $this->active_project)) {
          $this->wireframe->actions->add('reorder', lang('Reorder'), Router::assemble('project_milestones_reorder', array('project_slug' => $this->active_project->getSlug())), array(
            'onclick' => new FlyoutFormCallback('milestones_reordered', array(
              'title' => lang('Reorder Milestones'),
              'width' => 600,
            )),
          ));
        } // if

        $this->wireframe->actions->add('archive', lang('Archive'), Router::assemble('project_milestones_archive', array('project_slug' => $this->active_project->getSlug())));

	      if ($this->request->get('flyout')) {
		      $this->response->assign(array('flyout' => true));
	      } // if

        $day_width = 17;
        $this->smarty->assign(array(
          'page_title' => lang('Milestones in :project_name', array('project_name' => $this->active_project->getName())),
          'milestones' => Milestones::findForTimeline($this->active_project, $this->logged_user),
          'day_width'  => $day_width,
          'diagram_images' => array(
            'days' => AngieApplication::getProxyUrl("milestone_timeline_images", SYSTEM_MODULE, array('type' => 'days', 'day_width' => $day_width, 'work_days' => Globalization::getWorkdays())),
            'week_days'  => AngieApplication::getProxyUrl("milestone_timeline_images", SYSTEM_MODULE, array('type' => 'week_days', 'day_width' => $day_width, 'day_names' => Globalization::getShortDayNames())),
            'month_days'  => AngieApplication::getProxyUrl("milestone_timeline_images", SYSTEM_MODULE, array('type' => 'month_days', 'day_width' => $day_width))
          )
        ));
      } //if
    } // index
    
    /**
     * Show completed milestones
     */
    function archive() {
      $milestones = Milestones::findArchivedByProject($this->active_project, $this->logged_user->getMinVisibility());

      if($this->request->isApiCall()) {
        $this->response->respondWithData($milestones, array('as' => 'milestones'));
      } else if ($this->request->isWebBrowser()) {
        $this->wireframe->print->enable();
        $this->response->assign('milestones', $milestones);
      } else if($this->request->isPrintCall()) {
        $this->response->assign(array(
          'page_title' => lang('Archived Milestones')
        ));
        $this->response->assign('milestones', $milestones);
      } else {
        $this->response->assign('milestones', $milestones);
      } // if
    } // archive

    /**
     * Reorder milestones
     */
    function reorder() {
      if($this->request->isAsyncCall()) {
        if(Milestones::canManage($this->logged_user, $this->active_project)) {
          $milestones = Milestones::findActiveByProject($this->active_project);

          if($milestones) {
            $grouped_milestones = array();
            $to_be_determined = array();

            foreach($milestones as $milestone) {
              if($milestone->getStartOn() instanceof DateValue) {
                $timestamp = $milestone->getStartOn()->getTimestamp();

                if(!isset($grouped_milestones[$timestamp])) {
                  $grouped_milestones[$timestamp] = array(
                    'label' => $milestone->getStartOn()->formatForUser($this->logged_user, 0),
                    'milestones' => array(),
                  );
                } // if

                $grouped_milestones[$timestamp]['milestones'][] = $milestone;
              } else {
                $to_be_determined[] = $milestone;
              } // if
            } // foreach

            $grouped_milestones[] = array(
              'label' => lang('To Be Determined'),
              'milestones' => $to_be_determined,
            );
          } else {
            $grouped_milestones = null;
          } // if

          $this->response->assign(array(
            'grouped_milestones' => $grouped_milestones,
            'reorder_milestones_url' => Router::assemble('project_milestones_reorder', array('project_slug' => $this->active_project->getSlug())),
          ));

          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Updating milestone positions @ ' . __CLASS__);

              $updated_milestones = array();

              $milestone_positions = $this->request->post('milestones');

              if($milestone_positions) {
                foreach($milestone_positions as $milestone_id => $position) {
                  if($milestone_id) {
                    $milestone = Milestones::findById($milestone_id);

                    if($milestone instanceof Milestone && $milestone->getProjectId() == $this->active_project->getId() && $milestone->getState() == STATE_VISIBLE) {
                      $milestone->setPosition($position);
                      $milestone->save();

                      $updated_milestones[] = $milestone;
                    } // if
                  } // if
                } // foreach
              } // if

              DB::commit('Milestone positions updated @ ' . __CLASS__);

              $this->response->respondWithData($updated_milestones, array(
                'as' => 'milestone'
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to update milestone positions @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // reorder
    
    /**
     * Show single milestone
     */
    function view() {
      if($this->active_milestone->isLoaded()) {
        if($this->active_milestone->canView($this->logged_user)) {
          $this->wireframe->setPageObject($this->active_milestone, $this->logged_user);
          
          // Phone device
          if($this->request->isPhone()) {
            $this->wireframe->actions->remove(array('make_invoice', 'favorites_toggler'));
          
          // API call
          } elseif($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_milestone, array(
              'as' => 'milestone',
              'detailed' => true,
            ));
            
          // Regular web browser request
          } elseif($this->request->isPrintCall()) {
            
            
          // Regular web browser request
          } else {
            AngieApplication::useWidget('milestone_dates_widget', SYSTEM_MODULE);
            $this->wireframe->print->enable();
            
            $sections = new NamedList();
            EventsManager::trigger('on_milestone_sections', array(&$this->active_project, &$this->active_milestone, &$this->logged_user, &$sections, AngieApplication::INTERFACE_DEFAULT));
            $this->response->assign('milestone_sections', $sections);
            
            $this->active_milestone->accessLog()->log($this->logged_user);
          }
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view
    
    /**
     * Create a new milestone
     */
    function add() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(Milestones::canAdd($this->logged_user, $this->active_project)) {
          $milestone_data = $this->request->post('milestone');
          $this->smarty->assign('milestone_data', $milestone_data);
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating milestone @ ' . __CLASS__);
              
              $this->active_milestone = new Milestone();
            
              $this->active_milestone->setAttributes($milestone_data);

              $start_on = $this->active_milestone->getStartOn();
              if ($start_on instanceof DateValue) {
                if (Globalization::isWeekend($start_on) || Globalization::isDayOff($start_on)) {
                  throw new Error(lang('Start date needs to be set on working day'));
                } //if
              } //if

              $due_on = $this->active_milestone->getDueOn();
              if ($due_on instanceof DateValue){
                if (Globalization::isWeekend($due_on) || Globalization::isDayOff($due_on)) {
                  throw new Error(lang('Due date needs to be set on working day'));
                } //if
              } //if

              $this->active_milestone->setProjectId($this->active_project->getId());
              $this->active_milestone->setCreatedBy($this->logged_user);
              $this->active_milestone->setState(STATE_VISIBLE);
              $this->active_milestone->setVisibility(VISIBILITY_NORMAL);
              
              $this->active_milestone->save();
              
              $this->active_milestone->subscriptions()->set(array_unique(array_merge(
                (array) $this->logged_user->getId(),
                (array) $this->active_project->getLeaderId(),
                (array) array_var($milestone_data, 'subscribers', array())
              )), false);
              
              DB::commit('Milestone created @ ' . __CLASS__);

              AngieApplication::notifications()
                ->notifyAbout('system/new_milestone', $this->active_milestone, $this->logged_user)
                ->sendToSubscribers();
              
              if ($this->request->isPageCall()) {
                $this->flash->success('Milestone ":name" has been created', array('name' => $this->active_milestone->getName()));
                $this->response->redirectToUrl($this->active_milestone->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_milestone, array(
                  'as' => 'milestone',
                  'detailed' => true,
                ));
              } // if
            } catch(Exception $e) {
              DB::rollback('Failed to create milestone @ ' . __CLASS__);
              
              if ($this->request->isPageCall()) {
                $this->smarty->assign('errors', $e);
              } else {
                $this->response->exception($e);
              } // if
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
     * Edit specific milestone
     */
    function edit() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_milestone->isLoaded()) {
          if($this->active_milestone->canEdit($this->logged_user)) {
            
            $milestone_data = $this->request->post('milestone', array(
              'name' => $this->active_milestone->getName(),
              'body' => $this->active_milestone->getBody(),
              'start_on' => $this->active_milestone->getStartOn(),
              'due_on' => $this->active_milestone->getDueOn(),
              'priority' => $this->active_milestone->getPriority(),
              'assignee_id' => $this->active_milestone->getAssigneeId(), 
              'other_assignees' => $this->active_milestone->assignees()->getOtherAssigneeIds()
            ));

            $this->response->assign('milestone_data', $milestone_data);

	          $on_calendar = $this->request->get('on_calendar', false);
            
            if($this->request->isSubmitted()) {
              $current_assignee = $this->active_milestone->assignees()->getAssignee();

              try {
                DB::beginWork('Updating milestone @ ' . __CLASS__);

                $this->active_milestone->setAttributes($milestone_data);
                $this->active_milestone->save();

                DB::commit('Milestone updated @ ' . __CLASS__);

                $this->active_milestone->assignees()->notifyOnReassignment($current_assignee, $this->active_milestone->assignees()->getAssignee(), $this->logged_user);
                
                if ($this->request->isPageCall()) {
                  $this->flash->success('Milestone ":name" has been updated', array('name' => $this->active_milestone->getName()));
                  $this->response->redirectToUrl($this->active_milestone->getViewUrl());
                } else {
	                if ($on_calendar && $this->active_milestone instanceof ICalendarEventContext) {
		                $this->response->respondWithData($this->active_milestone->calendar_event_context()->describe($this->logged_user), array(
			                'as' => 'calendar_event',
			                'detailed' => true,
		                ));
	                } else {
		                $this->response->respondWithData($this->active_milestone, array(
			                'as' => 'milestone',
			                'detailed' => true,
		                ));
	                } // if
                } //if
              } catch(Exception $e) {
                DB::rollback('Failed to update milestone @ ' . __CLASS__);
                $this->response->exception($e);
              } // try
            } else {
	            if ($on_calendar) {
		            $this->setView('_calendar_milestone_form');
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
     * Milestone Comments page
     */
    function comments() {

    } // comments
    
    /**
     * Update milestone
     */
    function update_milestone() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if

      $object_id = $this->request->get('object_id');
      if (!$object_id) {
        $this->response->badRequest();
      } // if

      $object = ProjectObjects::findById($object_id);
      if (!($object instanceof ProjectObject)) {
        $this->response->notFound();
      } // if

      if (!$object->fieldExists('milestone_id')) {
        $this->response->badRequest();
      } // if

      if (!$object->canEdit($this->logged_user)) {
        $this->response->forbidden();
      } // if

      $object_data = $this->request->post('object', array(
        'milestone_id' => $object->getMilestoneId(),
      ));
      
      $this->smarty->assign(array(
        'form_url' => Router::assemble('project_object_update_milestone', array('project_slug' => $this->active_project->getSlug(), 'object_id' => $object->getId())),
        'object_data' => $object_data
      ));
      
      if ($this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating milestone @ ' . __CLASS__);
          $object->setAttributes($object_data);
          $object->save();
          DB::commit('Milestone Updated @ ' . __CLASS__);
          
          $this->response->respondWithData($object, array(
            'as' => $object->getBaseTypeName(),
            'detailed' => true
          ));
        } catch (Exception $e) {
          DB::rollback('Failed to save milestone change @ ' . __CLASS__);

          $this->response->exception($e);
        }
      } // if
    } // update_milestones
    
  }
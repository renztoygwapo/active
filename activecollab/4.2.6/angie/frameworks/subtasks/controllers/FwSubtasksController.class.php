<?php

  /**
   * Subtasks controller delegate
   *
   * @package angie.frameworks.subtasks
   * @subpackage controllers
   */
  abstract class FwSubtasksController extends Controller {
    
    /**
     * Active parent object
     *
     * @var ApplicationObject|ISubtasks
     */
    protected $active_object;
    
    /**
     * Selected subtask
     *
     * @var Subtask
     */
    protected $active_subtask;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Subscriptions controller delegate
     *
     * @var SubscriptionsController
     */
    protected $subscriptions_delagate;
    
    /**
     * List of available API actions
     *
     * @var array
     */
    protected $api_actions = array('subtasks', 'view_subtask', 'add_subtask', 'edit_subtask', 'complete_subtask', 'reopen_subtask');
    
    /**
     * Construct controller
     * 
     * @param mixed $parent
     * @param mixed $context
     */
    function __construct(&$parent, $context = null) {
      parent::__construct($parent, $context);
      
      $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, "{$context}_subtask");
      $this->subscriptions_delagate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, "{$context}_subtask");
    } // __construct
    
    /**
     * Prepare controller before action is being executed
     */
    function __before() {
      if($this->active_object instanceof ISubtasks) {
        if($this->active_object->isNew()) {
          $this->response->notFound();
        } // if
        
        $subtask_id = $this->request->getId('subtask_id');
        if($subtask_id) {
          $this->active_subtask = Subtasks::findById($subtask_id);
        } // if
        
        if($this->active_subtask instanceof Subtask) {
          if(!$this->active_subtask->isParent($this->active_object)) {
            $this->response->notFound();
          } // if
        } else {
          $this->active_subtask = $this->active_object->subtasks()->newSubtask();
        } // if
        
        $this->response->assign(array(
          'active_subtask' => $this->active_subtask,
          'active_object' => $this->active_object
        ));
      } else {
        $this->response->notFound();
      } // if
      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_subtask,
        ));
      } // if
      
      if($this->subscriptions_delagate instanceof SubscriptionsController) {
        $this->subscriptions_delagate->__setProperties(array(
          'active_object' => &$this->active_subtask,
        ));
      } // if
    } // __before
    
    /**
     * List all subtasks
     */
    function subtasks() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData($this->active_object->subtasks()->get($this->logged_user), array(
          'as' => 'subtasks', 
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // subtasks
    
    /**
     * List all completed subtasks (for mobile devices only)
     */
    function subtasks_archive() {
      if($this->request->isMobileDevice()) {
        $this->response->assign('subtasks', $this->active_object->subtasks()->get($this->logged_user, 'completed'));
      } else {
        $this->response->badRequest();
      } // if
    } // subtasks_archive
    
    /**
     * View task URL (redirects to parent object)
     */
    function view_subtask() {
      if($this->active_subtask->isLoaded()) {
        if($this->active_subtask->canView($this->logged_user)) {
          // API call
          if($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_subtask, array(
              'as' => 'subtask', 
              'detailed' => true, 
            ));
          } else {
            // Request by phone device
            if($this->request->isPhone()) {
              $this->wireframe->setPageObject($this->active_subtask, $this->logged_user);
              $this->wireframe->actions->remove(array('subscription', 'archive'));
              
              $this->setView('view');
            // Regular web browser request
            } else if ($this->request->isQuickViewCall()) {
              $this->response->redirectToUrl(extend_url($this->active_subtask->getParent()->getViewUrl(), array('quick_view' => $this->request->isQuickViewCall())));
            } else {
              $this->response->redirectToUrl($this->active_subtask->getParent()->getViewUrl());
            } // if
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // view_subtask
    
    /**
     * Show and process add subtask form
     */
    function add_subtask() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_object->subtasks()->canAdd($this->logged_user)) {
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating new subtask @ ' . __CLASS__);

              $subtask_data = $this->request->post('subtask');
            
              $this->active_subtask = $this->active_object->subtasks()->newSubtask();
              
              $this->active_subtask->setAttributes($subtask_data);
              $this->active_subtask->setCreatedBy($this->logged_user);
              $this->active_subtask->setState(STATE_VISIBLE);
              $this->active_subtask->setPosition(Subtasks::nextPositionByParent($this->active_object));
              
              $this->active_subtask->save();
              
              $subscribers = array($this->logged_user->getId());

              $assignee = $this->active_subtask->assignees()->getAssignee();
              if ($assignee instanceof User && !in_array($assignee->getId(), $subscribers)) {
                $subscribers[] = $assignee->getId(); // Subscribe subtask assignee
              } // if

              if($this->active_object instanceof IAssignees) {
                $assignee = $this->active_object->assignees()->getAssignee();

                if ($assignee instanceof User && !in_array($assignee->getId(), $subscribers)) {
                  $subscribers[] = $assignee->getId(); // Subscribe parent assignee
                } // if
              } // if
              
              $this->active_subtask->subscriptions()->set($subscribers);

              if($this->active_object instanceof ISubscriptions) {
                AngieApplication::notifications()
                  ->notifyAbout(SUBTASKS_FRAMEWORK_INJECT_INTO . '/new_subtask', $this->active_object, $this->logged_user)
                  ->setSubtask($this->active_subtask)
                  ->sendToUsers($this->active_subtask->subscriptions()->get());
              } // if
              
              DB::commit('Subtask created @ ' . __CLASS__);
              
              if($this->request->isPageCall()) {
                $this->flash->success('Subtask has been created');
                $this->response->redirectToUrl($this->active_object->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_subtask, array(
                  'as' => 'subtask', 
                  'detailed' => true, 
                ));
              } // if
            } catch(Exception $e) {
              DB::rollback('Failed to save subtask @ ' . __CLASS__);
              
              if($this->request->isPageCall()) {
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
    } // add_subtask
    
    /**
     * Show and process edit task form
     */
    function edit_subtask() {
      if($this->request->isAsyncCall() || $this->request->isApiCall() || $this->request->isMobileDevice()) {
        if($this->active_subtask->isLoaded()) {
          if($this->active_subtask->canEdit($this->logged_user)) {
            $subtask_data = $this->request->post('subtask', array(
              'body' => $this->active_subtask->getBody(),
              'assignee_id' => $this->active_subtask->getAssigneeId(),
              'priority' => $this->active_subtask->getPriority(),
              'label_id' => $this->active_subtask->getLabelId(),
              'due_on' => $this->active_subtask->getDueOn(),
            ));
            
            $this->response->assign('subtask_data', $subtask_data);

	          $on_calendar = $this->request->get('on_calendar', false);
            
            // Submitted? Process...
            if($this->request->isSubmitted()) {
              $current_assignee = $this->active_subtask->assignees()->getAssignee();

              if(!isset($subtask_data['assignees'])) {
                $subtask_data['assignees'] = array(array(), 0);
              } // if
              
              try {
                DB::beginWork('Updating subtask @ ' . __CLASS__);
              
                $this->active_subtask->setAttributes($subtask_data);
                $this->active_subtask->save();
                
                DB::commit('Subtask updated @ ' . __CLASS__);

                if ($this->active_subtask->assignees()->getAssignee() instanceof IUser) {
                  $this->active_subtask->subscriptions()->subscribe($this->active_subtask->assignees()->getAssignee());
                } // if

                $this->active_subtask->assignees()->notifyOnReassignment($current_assignee, $this->active_subtask->assignees()->getAssignee(), $this->logged_user);

                // unsubscribe current assignee if another one is set (except project leader)
                if ($current_assignee instanceof IUser && $current_assignee->getId() !== $this->active_subtask->getAssigneeId() && $current_assignee->getId() !== $this->active_object->getProject()->getLeaderId()) {
                  $this->active_subtask->subscriptions()->unsubscribe($current_assignee);
                } // if
                
                if($this->request->isPageCall()) {
                  $this->flash->success('Subtask has been updated');
                  $this->response->redirectToUrl($this->active_subtask->getViewUrl());
                } else {
	                if ($on_calendar && $this->active_subtask instanceof ICalendarEventContext) {
		                $this->response->respondWithData($this->active_subtask->calendar_event_context()->describe($this->logged_user), array(
			                'as' => 'calendar_event',
			                'detailed' => true,
		                ));
	                } else {
		                $this->response->respondWithData($this->active_subtask, array(
			                'as' => 'subtask',
			                'detailed' => true,
		                ));
	                } // if
                } // if
              } catch(Exception $e) {
                DB::rollback('Failed to update subtask @ ' . __CLASS__);
                
                if($this->request->isPageCall()) {
                  $this->smarty->assign('errors', $e);
                } else {
                  $this->response->exception($e);
                } // if
              } // try
              
            // Not submitted? Return form for async or error for API call
            } else {
              if($this->request->isAsyncCall()) {
	              $subtask_data = array(
		              'body' => $this->active_subtask->getBody(),
		              'assignee_id' => $this->active_subtask->getAssigneeId(),
		              'priority' => $this->active_subtask->getPriority(),
		              'label_id' => $this->active_subtask->getLabelId(),
		              'due_on' => $this->active_subtask->getDueOn()
	              );

	              if ($on_calendar) {
		              $this->response->assign('subtask_data', $subtask_data);
		              $this->setView(get_view_path('_object_subtask_form_calendar', null, SUBTASKS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
	              } else {
		              $view = $this->smarty->createTemplate(get_view_path('_object_subtask_form_row', null, SUBTASKS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));

		              $view->assign(array(
			              'subtask_parent' => $this->active_object,
			              'subtask' => $this->active_subtask,
			              'subtasks_id' => HTML::uniqueId('edit_subtask'),
			              'user' => $this->logged_user,
			              'subtask_data' => $subtask_data,
		              ));

		              $this->renderText($view->fetch());
	              } // if
              } elseif($this->request->isApiCall()) {
                $this->response->badRequest();
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
    } // edit_subtask
    
    /**
     * Mark selected subtask as completed
     */
    function complete_subtask() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted() || $this->request->isMobileDevice()) {
        if($this->active_subtask->isLoaded()) {
          if($this->active_subtask->complete()->canChangeStatus($this->logged_user)) {
            try {
              $this->active_subtask->complete()->complete($this->logged_user);
              
              if($this->active_object instanceof ISubscriptions) {
                AngieApplication::notifications()
                  ->notifyAbout(SUBTASKS_FRAMEWORK_INJECT_INTO . '/subtask_completed', $this->active_object, $this->logged_user)
                  ->setSubtask($this->active_subtask)
                  ->sendToGroupsOfUsers(array($this->active_object->subscriptions()->get(), $this->active_subtask->subscriptions()->get()));
              } // if
              
              if($this->request->isPageCall()) {
                $this->flash->success('Subtask has been completed');
                $this->response->redirectToUrl($this->active_subtask->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_subtask, array(
                  'as' => 'subtask', 
                  'detailed' => true, 
                ));
              } // if
            } catch(Exception $e) {
              if($this->request->isPageCall()) {
                $this->smarty->assign('errors', $e);
              } else {
                $this->response->exception($e);
              } // if
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // complete_subtask
    
    /**
     * Reopen selected subtask
     */
    function reopen_subtask() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted() || $this->request->isMobileDevice()) {
        if($this->active_subtask->isLoaded()) {
          if($this->active_subtask->complete()->canChangeStatus($this->logged_user)) {
            try {
              DB::beginWork('Marking subtask as open @ ' . __CLASS__);
              
              $this->active_subtask->complete()->open($this->logged_user);
              
              $this->active_subtask->setPosition(Subtasks::nextPositionByParent($this->active_object));
              $this->active_subtask->save();
              
              DB::commit('Subtask marked as open @ ' . __CLASS__);
              
              if($this->active_object instanceof ISubscriptions) {
                AngieApplication::notifications()
                  ->notifyAbout(SUBTASKS_FRAMEWORK_INJECT_INTO . '/subtask_reopened', $this->active_object, $this->logged_user)
                  ->setSubtask($this->active_subtask)
                  ->sendToGroupsOfUsers(array($this->active_object->subscriptions()->get(), $this->active_subtask->subscriptions()->get()));
              } // if
              
              if($this->request->isPageCall()) {
                $this->flash->success('Subtask has been completed');
                $this->response->redirectToUrl($this->active_subtask->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_subtask, array(
                  'as' => 'subtask', 
                  'detailed' => true,
                ));
              } // if
            } catch(Exception $e) {
              DB::rollback('Failed to mark subtask as open @ ' . __CLASS__);
              
              if($this->request->isPageCall()) {
                $this->smarty->assign('errors', $e);
              } else {
                $this->response->exception($e);
              } // if
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // reopen_subtask
    
    /**
     * Reorder subtasks
     */
    function reorder_subtasks() {
      if($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        if($this->active_object->subtasks()->canManage($this->logged_user)) {
          try {
            $subtaks_ids = $this->request->post('subtasks_ids');
            
            if(is_foreachable($subtaks_ids)) {
              DB::beginWork('Reordering subtasks @ ' . __CLASS__);
              
              $subtasks = Subtasks::findByIds($subtaks_ids);
              if(is_foreachable($subtasks)) {
                foreach($subtasks as $subtask) {
                  $subtask->setPosition(array_search($subtask->getId(), $subtaks_ids));
                  if(!$subtask->isParent($this->active_object)) {
                    $subtask->setParent($this->active_object);
                  } // if
                  
                  $subtask->save();
                } // foreach
              } // if
            
              DB::commit('Subtasks reordered @ ' . __CLASS__);
            } // if
            
            $this->response->ok();
          } catch(Exception $e) {
            DB::rollback('Failed to reorder subtasks @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // reorder_subtasks
    
  }
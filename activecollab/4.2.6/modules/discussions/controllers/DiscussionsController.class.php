<?php

  // We need ProjectsController
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Discussions controller
   *
   * @package activeCollab.modules.discussions
   * @subpackage controllers
   */
  class DiscussionsController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = DISCUSSIONS_MODULE;
    
    /**
     * Selected discussion
     *
     * @var Discussion
     */
    protected $active_discussion;
    
    /**
     * State delegate
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
     * Reminders controller instance
     *
     * @var RemindersController
     */
    protected $reminders_delegate;

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
     * Actions that are available through API
     *
     * @var array
     */
    protected $api_actions = array('index', 'view', 'add', 'edit');
    
    /**
     * Construct discussions controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'discussions') {
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'project_discussion');
        $this->categories_delegate = $this->__delegate('categories', CATEGORIES_FRAMEWORK_INJECT_INTO, 'project_discussion');
        $this->comments_delegate = $this->__delegate('comments', COMMENTS_FRAMEWORK_INJECT_INTO, 'project_discussion');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_discussion');
        $this->attachments_delegate = $this->__delegate('attachments', ATTACHMENTS_FRAMEWORK_INJECT_INTO, 'project_discussion');
        $this->sharing_settings_delegate = $this->__delegate('sharing_settings', SYSTEM_MODULE, 'project_discussion');
        $this->move_to_project_delegate = $this->__delegate('move_to_project', SYSTEM_MODULE, 'project_discussion');
        $this->reminders_delegate = $this->__delegate('reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'project_discussion');

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'project_discussion');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'project_discussion');
	      } // if
      } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if(!Discussions::canAccess($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
      
      $discussions_url = Router::assemble('project_discussions', array('project_slug' => $this->active_project->getSlug()));
      
      $this->wireframe->tabs->setCurrentTab('discussions');
      $this->wireframe->breadcrumbs->add('discussions', lang('Discussions'), $discussions_url);
      
      $discussion_id = $this->request->getId('discussion_id');
      if($discussion_id) {
        $this->active_discussion = ProjectObjects::findById($discussion_id);
      } // if
      
      if($this->active_discussion instanceof Discussion) {
        if (!$this->active_discussion->isAccessible()) {
          $this->response->notFound();
        } // if

        if ($this->active_discussion->getState() == STATE_ARCHIVED) {
          $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_discussions_archive', array('project_slug' => $this->active_project->getSlug())));
        } // if

        $this->wireframe->breadcrumbs->add('discussion', $this->active_discussion->getName(), $this->active_discussion->getViewUrl());
      } else {
        $this->active_discussion = new Discussion();
        $this->active_discussion->setProject($this->active_project);
      } // if
      
      $this->response->assign(array(
        'active_discussion' => $this->active_discussion,
        'discussions_url' => $discussions_url,
        'manage_categories_url' => $this->active_project->availableCategories()->canManage($this->logged_user, 'DiscussionCategory') ? Router::assemble('project_discussion_categories', array('project_slug' => $this->active_project->getSlug())) : null,
      ));

      if(($this->request->isWebBrowser() || $this->request->isMobileDevice()) && in_array($this->request->getAction(), array('index', 'view'))) {
        $add_discussion_url = false;
        if(Discussions::canAdd($this->logged_user, $this->active_project)) {
          $add_discussion_url = Router::assemble('project_discussions_add', array('project_slug' => $this->active_project->getSlug()));
          $this->wireframe->actions->add('new_discussion', lang('New Discussion'), $add_discussion_url, array(
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'primary' => true,
            'onclick' => new FlyoutFormCallback('discussion_created')
          ));
        } // if

        $this->response->assign('add_discussion_url', $add_discussion_url);
      } // if
      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_discussion,
        ));
      } // if
      
      if($this->categories_delegate instanceof CategoriesController) {
        $this->categories_delegate->__setProperties(array(
          'categories_context' => &$this->active_project,
          'routing_context' => 'project_discussion',
          'routing_context_params' => array('project_slug' => $this->active_project->getSlug()),
          'category_class' => 'DiscussionCategory',
          'active_object' => &$this->active_discussion
        ));
      } // if
      
      if($this->comments_delegate instanceof CommentsController) {
        $this->comments_delegate->__setProperties(array(
          'active_object' => &$this->active_discussion,
        ));
      } // if
      
      if($this->subscriptions_delegate instanceof SubscriptionsController) {
        $this->subscriptions_delegate->__setProperties(array(
          'active_object' => &$this->active_discussion,
        ));
      } // if
      
      if($this->attachments_delegate instanceof AttachmentsController) {
        $this->attachments_delegate->__setProperties(array(
          'active_object' => &$this->active_discussion,
        ));
      } // if
      
      if($this->sharing_settings_delegate instanceof SharingSettingsController) {
        $this->sharing_settings_delegate->__setProperties(array(
          'active_object' => &$this->active_discussion,
        ));
      } // if
      
      if($this->move_to_project_delegate instanceof MoveToProjectController) {
        $this->move_to_project_delegate->__setProperties(array(
          'active_project' => &$this->active_project,
          'active_object' => &$this->active_discussion,
        ));
      } // if

      if($this->reminders_delegate instanceof RemindersController) {
        $this->reminders_delegate->__setProperties(array(
          'active_object' => &$this->active_discussion,
        ));
      } // if

	    if ($this->access_logs_delegate instanceof AccessLogsController) {
		    $this->access_logs_delegate->__setProperties(array(
			    'active_object' => &$this->active_discussion
		    ));
	    } // if

	    if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		    $this->history_of_changes_delegate->__setProperties(array(
			    'active_object' => &$this->active_discussion
		    ));
	    } // if
    } // __construct
  
    /**
     * Show discussions module homepage
     */
    function index() {
      // refresh object list
      if ($this->request->get('objects_list_refresh')) {
        $this->response->respondWithData(Discussions::findForObjectsList($this->active_project, $this->logged_user));
        
      // Web browser
      } else if($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();

        $this->response->assign(array(
          'discussions' => Discussions::findForObjectsList($this->active_project, $this->logged_user),
          'milestones' => Milestones::getIdNameMap($this->active_project),
          'categories' => Categories::getidNameMap($this->active_project, 'DiscussionCategory'),
          'read_statuses' => array(0 => lang('Unread'), 1 => lang('Read')),
          'manage_categories_url' => $this->active_project->availableCategories()->canManage($this->logged_user, 'DiscussionCategory') ? Router::assemble('project_discussion_categories', array('project_slug' => $this->active_project->getSlug())) : null,
          'can_manage_discussions' => Discussions::canManage($this->logged_user, $this->active_project),
          'in_archive' => false,
          'print_url' => Router::assemble('project_discussions', array('print' => 1, 'project_slug' => $this->active_project->getSlug()))
        ));
        
        // mass manager
        if (Discussions::canManage($this->logged_user, $this->active_project)) {
          $mass_manager = new MassManager($this->logged_user, $this->active_discussion);          
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
        
      // Mobile device
      } elseif($this->request->isMobileDevice()) {
        $this->response->assign('discussions', Discussions::findForPhoneList($this->active_project, $this->logged_user));

      // Other
      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', ''));
        $filter_by = $this->request->get('filter_by', 'is_archived');
        
        // page title
        $filter_by_completion = array_var($filter_by, 'is_archived', null); 
        if ($filter_by_completion === '0') {
          $page_title = lang('Active Discussions in :project_name Project', array('project_name' => $this->active_project->getName()));
        } else if ($filter_by_completion === '1') {
          $page_title = lang('Archived Discussions in :project_name Project', array('project_name' => $this->active_project->getName()));          
        } else {
          $page_title = lang('All Discussions in :project_name Project', array('project_name' => $this->active_project->getName()));
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
            $map = Categories::getidNameMap($this->active_project, 'DiscussionCategory');
            $map[0] = lang('Uncategorized');
            
            $getter = 'getCategoryId';
            $page_title.= ' ' . lang('Grouped by Category');
            break;
         case 'is_read':
            $map = array(0 => lang('Unread'), 1 => lang('Read'));
            $group_by = "boolean_field_1 DESC";
          $getter = 'isRead';
            $page_title.= ' ' . lang('Grouped by Read Status');
            break;
        }//switch
        
        $discussions = Discussions::findForPrint($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility(), $group_by, $filter_by,$this->logged_user);
     
        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map,$discussions,$getter, false);
        
        $this->response->assignByRef('discussions', $print_list);
        $this->response->assignByRef('map', $map);
        
        $this->response->assign(array(
      'page_title' => $page_title,
          'getter' => $getter
        ));
      } else {
        $this->response->respondWithData(Discussions::findByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility(), 0, 25), array(
          'as' => 'discussions', 
        ));
      } // if
      
    } // index
    
    /**
     * Show archived discussions (mobile devices only)
     */
    function archive() {
      if($this->request->isMobileDevice()) {
        $this->response->assign('discussions', Discussions::findArchivedByProject($this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility()));
      } else if ($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();
        $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_discussions_archive', array('project_slug' => $this->active_project->getSlug())));

        $this->response->assign(array(
          'discussions' => Discussions::findForObjectsList($this->active_project, $this->logged_user, STATE_ARCHIVED),
          'milestones' => Milestones::getIdNameMap($this->active_project),
          'categories' => Categories::getidNameMap($this->active_project, 'DiscussionCategory'),
          'read_statuses' => array(0 => lang('Unread'), 1 => lang('Read')),
          'in_archive' => true,
          'print_url' => Router::assemble('project_discussions_archive', array('print' => 1, 'project_slug' => $this->active_project->getSlug()))
        ));

        // mass manager
        if (Discussions::canManage($this->logged_user, $this->active_project)) {
          $this->active_discussion->setState(STATE_ARCHIVED);
          $mass_manager = new MassManager($this->logged_user, $this->active_discussion);
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if

      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', ''));
        $page_title = lang('Archived Discussions in :project_name Project', array('project_name' => $this->active_project->getName()));

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
            $map = Categories::getidNameMap($this->active_project, 'DiscussionCategory');
            $map[0] = lang('Uncategorized');

            $getter = 'getCategoryId';
            $page_title.= ' ' . lang('Grouped by Category');
            break;
          case 'is_read':
            $map = array(0 => lang('Unread'), 1 => lang('Read'));
            $group_by = "boolean_field_1 DESC";
            $getter = 'isRead';
            $page_title.= ' ' . lang('Grouped by Read Status');
            break;
        }//switch

        $discussions = Discussions::findForPrint($this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility(), $group_by, null,$this->logged_user);

        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map, $discussions, $getter);

        $this->response->assignByRef('discussions', $print_list);
        $this->response->assignByRef('map', $map);

        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Mass Edit action
     */
    function mass_edit() {
      if ($this->getControllerName() == 'discussions') {
        $this->mass_edit_objects = Discussions::findByIds($this->request->post('selected_item_ids'), STATE_ARCHIVED, $this->logged_user->getMinVisibility());
      } // if
      
      parent::mass_edit();
    } // mass_edit
    
    /**
     * View specific discussion
     */
    function view() {
      if($this->active_discussion->isLoaded()) {
        if($this->active_discussion->canView($this->logged_user)) {
          $this->wireframe->print->enable();
          
          // Page options
          if($this->request->isWebBrowser() || $this->request->isPhone() || $this->request->isPrintCall()) {
            $this->wireframe->setPageObject($this->active_discussion, $this->logged_user);
          } // if

          // API call
          if($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_discussion, array(
              'as' => 'discussion',
              'detailed' => true, 
            ));
            
          // Regular request made by web browser
          } elseif($this->request->isWebBrowser()) {
            
            if($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
              $this->active_discussion->accessLog()->log($this->logged_user);
              $this->render();
            } else {
              if ($this->active_discussion->getState() == STATE_ARCHIVED) {
                $this->__forward('archive', 'archive');
              } else {
                $this->__forward('index', 'index');
              } // if
            } // if
            
          // Phone request
          } elseif($this->request->isPhone()) {
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
     * Create a new discussion
     */
    function add() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(Discussions::canAdd($this->logged_user, $this->active_project)) {
          $discussion_data = $this->request->post('discussion', array(
            'milestone_id' => $this->request->get('milestone_id'),
            'visibility' => $this->active_project->getDefaultVisibility()
          ));
          $this->response->assign(array(
            'add_discussion_url' => Router::assemble('project_discussions_add', array('project_slug' => $this->active_project->getSlug())), 
            'discussion_data' => $discussion_data
          ));
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating discussion @ ' . __CLASS__);
              
              $this->active_discussion = new Discussion();
              
              $this->active_discussion->attachments()->attachUploadedFiles($this->logged_user);
              
              $this->active_discussion->setAttributes($discussion_data);
              $this->active_discussion->setProject($this->active_project);
              $this->active_discussion->setCreatedBy($this->logged_user);
              $this->active_discussion->setState(STATE_VISIBLE);

              $visibility = $this->logged_user->canSeePrivate() ? array_var($discussion_data, 'visibility', $this->active_project->getDefaultVisibility()) : VISIBILITY_NORMAL;
              $this->active_discussion->setVisibility($visibility);
              
              $this->active_discussion->save();
              
              $this->active_discussion->subscriptions()->set(array_unique(array_merge(
                (array) $this->logged_user->getId(),
                (array) $this->active_project->getLeaderId(),
                (array) $this->request->post('notify_users')
              )), false);
              
              DB::commit('Discussion created @ ' . __CLASS__);

              AngieApplication::notifications()
                ->notifyAbout('discussions/new_discussion', $this->active_discussion, $this->logged_user)
                ->sendToSubscribers();
              
              if($this->request->isPageCall()) {
                $this->flash->success('Discussion has been created');
                $this->response->redirectToUrl($this->active_discussion->getViewUrl());
              } else {
                $this->active_discussion->setIsRead(true); // it's read for user who has created it
                $this->response->respondWithData($this->active_discussion, array(
                  'as' => 'discussion', 
                  'detailed' => true,  
                ));
              } //if
            } catch(Exception $e) {
              DB::rollback('Failed to create a discussion @ ' . __CLASS__);
              
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
    } // add
    
    /**
     * Update discussion
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_discussion->isLoaded()) {
          if($this->active_discussion->canEdit($this->logged_user)) {
            $discussion_data = $this->request->post('discussion', array(
              'name' => $this->active_discussion->getName(),
              'body' => $this->active_discussion->getBody(),
              'category_id' => $this->active_discussion->getCategoryId(),
              'milestone_id' => $this->active_discussion->getMilestoneId(),
              'visibility' => $this->active_discussion->getVisibility(),
            ));
            $this->response->assign(array(
              'discussion_data' => $discussion_data,
            ));
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating discussion @ ' . __CLASS__);
                
                $this->active_discussion->setAttributes($discussion_data);
                $this->active_discussion->attachments()->attachUploadedFiles($this->logged_user);
                
                $this->active_discussion->save();
                
                DB::commit('Discussion updated @ ' . __CLASS__);
                
                if($this->request->isPageCall()) {
                  $this->flash->success('Discussion ":name" has been updated', array('name' => $this->active_discussion->getName()));
                  $this->response->redirectToUrl($this->active_discussion->getViewUrl());
                } else {
                  $this->response->respondWithData($this->active_discussion, array(
                    'as' => 'discussion', 
                    'detailed' => true, 
                  ));
                } //if
              } catch(Exception $e) {
                DB::rollback('Failed to update discussion @ ' . __CLASS__);
                $this->response->exception($e);
              } // try
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
     * Pin specific discussion
     */
    function pin() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted() || $this->request->isMobileDevice()) {
        if($this->active_discussion->isLoaded()) {
          if($this->active_discussion->canEdit($this->logged_user)) {
            try {
              DB::beginWork('Maring discussion as pinned @ ' . __CLASS__);
              
              $this->active_discussion->setIsPinned(true);
              $this->active_discussion->save();
              
              DB::commit('Discussion marked as pinned @ ' . __CLASS__);
              
              if($this->request->isPageCall()) {
                $this->flash->success('Discussion ":name" has been marked as pinned', array('name' => $this->active_discussion->getName()));
                $this->response->redirectToUrl($this->active_discussion->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_discussion, array(
                  'as' => 'discussion', 
                  'detailed' => true, 
                ));
              } //if
            } catch(Exception $e) {
              DB::rollback('Failed to mark discussion as pinned @ ' . __CLASS__);
              
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
    } // pin
    
    /**
     * Unpin specific discussion
     */
    function unpin() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted() || $this->request->isMobileDevice()) {
        if($this->active_discussion->isLoaded()) {
          if($this->active_discussion->canEdit($this->logged_user)) {
            try {
              DB::beginWork('Maring discussion as unpinned @ ' . __CLASS__);
              
              $this->active_discussion->setIsPinned(false);
              $this->active_discussion->save();
              
              DB::commit('Discussion marked as unpinned @ ' . __CLASS__);
              
              if($this->request->isPageCall()) {
                $this->flash->success('Discussion ":name" has been marked as unpinned', array('name' => $this->active_discussion->getName()));
                $this->response->redirectToUrl($this->active_discussion->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_discussion, array(
                  'as' => 'discussion', 
                  'detailed' => true, 
                ));
              } //if
            } catch(Exception $e) {
              DB::rollback('Failed to mark discussion as unpinned @ ' . __CLASS__);
              
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
    } // pin
  
  }
<?php

  // Inherit projects controller
  AngieApplication::useController('projects', SYSTEM_MODULE);

  /**
   * Single project controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectController extends ProjectsController {
    
    /**
     * Active project
     *
     * @var Project
     */
    protected $active_project;
    
    /**
     * Complete controller delegate
     *
     * @var CompleteController
     */
    protected $complete_delegate;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Avatar controller delegate
     * 
     * @var ProjectAvatarController
     */
    protected $avatar_delegate;
    
    /**
     * Object tracking delegate controller
     *
     * @var ObjectTrackingController
     */
    protected $object_tracking_delegate;
    
    /**
     * Labels controller delegate
     * 
     * @var LabelsController
     */
    protected $labels_delegate;
    
    /**
     * Categories delegate controller instance
     *
     * @var CategoriesController
     */
    protected $categories_delegate;

    /**
     * Activity logs delegate
     *
     * @var ActivityLogsController
     */
    protected $activity_logs_delegate;
    
    /**
     * Invoice controller delegate
     * 
     * @var InvoiceBasedOnController
     */
    protected $invoice_delegate;

	  /**
	   * Access logs controller delegate
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
     * Actions exposed through API
     *
     * @var array
     */
    protected $api_actions = array('index', 'add', 'edit', 'edit_status', 'delete', 'user_tasks');
    
    /**
     * Construct project controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'project') {
        $this->complete_delegate = $this->__delegate('complete', COMPLETE_FRAMEWORK_INJECT_INTO, 'project');
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'project');
        $this->avatar_delegate = $this->__delegate('project_avatar', AVATAR_FRAMEWORK_INJECT_INTO, 'project');
        $this->categories_delegate = $this->__delegate('categories', CATEGORIES_FRAMEWORK_INJECT_INTO, 'project');
        $this->labels_delegate = $this->__delegate('labels', LABELS_FRAMEWORK_INJECT_INTO, 'project');
        $this->activity_logs_delegate = $this->__delegate('activity_logs', ACTIVITY_LOGS_FRAMEWORK_INJECT_INTO, 'project');
        
        if(AngieApplication::isModuleLoaded('tracking')) {
          $this->object_tracking_delegate = $this->__delegate('object_tracking', TRACKING_MODULE, 'project');
        } // if
        
        if(AngieApplication::isModuleLoaded('invoicing')) {
          $this->invoice_delegate = $this->__delegate('invoice_based_on', INVOICING_MODULE, 'project');
        } // if

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'project');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'project');
	      } // if
      } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->actions->clear();
      
      $project_slug = $this->request->get('project_slug');
      
      if($project_slug) {
        if(is_numeric($project_slug)) {
          $this->active_project = Projects::findById($project_slug);
        } else {
          $this->active_project = Projects::findBySlug($project_slug);
        } // if
      } // if
      
      if($this->active_project instanceof Project && $this->active_project->isLoaded()) {
        if ($this->active_project->getState() < STATE_TRASHED) {
          $this->response->notFound();
        } // if

        if(!$this->active_project->canView($this->logged_user)) {
          $this->response->forbidden();
        } // if

        // clear tabs
        $this->wireframe->tabs->clear();
        $this->wireframe->breadcrumbs->add('project', $this->active_project->getName(), $this->active_project->getViewUrl());

        // if project is in trash
        if ($this->active_project->getState() == STATE_TRASHED) {
          if (!(in_array($this->request->getAction(), array('index', 'project_state_untrash')) && $this->getControllerName() == 'project')) {
            $this->response->notFound();
          } else {
            $this->wireframe->tabs->add('overview', $this->active_project->getName(), $this->active_project->getViewUrl());
          } // if

        // if project is not in trash
        } else {
          if ($this->active_project->getState() == STATE_ARCHIVED) {
            $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('projects_archive'));
          } // if

          // Tabs
          foreach($this->active_project->getTabs($this->logged_user, AngieApplication::getPreferedInterface()) as $tab_name => $tab) {
            if($tab['text'] == '-') {
              $this->wireframe->tabs->addSeparator();
            } else {
              $this->wireframe->tabs->add($tab_name, $tab['text'], $tab['url'], $tab['icon']);
            } // if
          } // foreach
        } // if

        if($this->request->isWebBrowser()) {
          $this->wireframe->tabs->setCurrentTab('overview');
        } // if
        
        $this->wireframe->javascriptAssign('active_project_id', $this->active_project->getId());
        
      // New project
      } else {
        if($this->getControllerName() == 'project') {
          $this->active_project = new Project();
        } else {
          $this->response->notFound();
        } // if
      } // if
      
      $this->response->assign('active_project', $this->active_project);
      
      if($this->categories_delegate instanceof CategoriesController) {
        $this->categories_delegate->__setProperties(array(
          'routing_context' => 'project', 
          'category_class' => 'ProjectCategory',
          'routing_context_params' => array('project_slug' => $this->active_project->getSlug()),
          'active_object' => &$this->active_project
        ));
      } // if
      
      if($this->complete_delegate instanceof CompleteController) {
        $this->complete_delegate->__setProperties(array(
          'active_object' => &$this->active_project,
        ));
      } // if
      
      if ($this->labels_delegate instanceof LabelsController) {
        $this->labels_delegate->__setProperties(array(
          'active_object' => &$this->active_project
        ));
      } // if
      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_project, 
        ));
      } // if
      
      if($this->avatar_delegate instanceof ProjectAvatarController) {
        $this->avatar_delegate->__setProperties(array(
          'active_object' => &$this->active_project
        ));
      } // if

      if($this->activity_logs_delegate instanceof ActivityLogsController) {
        $this->activity_logs_delegate->__setProperties(array(
          'show_activities_in' => &$this->active_project
        ));
      } // if
      
      if($this->object_tracking_delegate instanceof ObjectTrackingController) {
        $this->object_tracking_delegate->__setProperties(array(
          'active_tracking_object' => &$this->active_project
        ));
      } // if
      
      if($this->invoice_delegate instanceof InvoiceBasedOnController) {
        $this->invoice_delegate->__setProperties(array(
          'active_object' => &$this->active_project
        ));
      } // if

	    if ($this->access_logs_delegate instanceof AccessLogsController) {
		    $this->access_logs_delegate->__setProperties(array(
			    'active_object' => &$this->active_project
		    ));
	    } // if

	    if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		    $this->history_of_changes_delegate->__setProperties(array(
			    'active_object' => &$this->active_project
		    ));
	    } // if
      
    } // __before
    
    /**
     * Show project overview page
     */
    function index() {
      if($this->active_project->isLoaded()) {
        $this->wireframe->print->enable();
        
        // Just serve the data
        if($this->request->isApiCall()) {
          $this->response->respondWithData($this->active_project, array(
            'as' => 'project', 
            'detailed' => true, 
          ));
        
        // Phone user
        } elseif($this->request->isPhone()) {
          $this->wireframe->setPageObject($this->active_project, $this->logged_user);
          
          if($this->active_project->canEdit($this->logged_user)) {
            $this->wireframe->actions->add('edit', lang('Edit'), $this->active_project->getEditUrl(), array(
              'icon' => AngieApplication::getImageUrl('layout/buttons/edit.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
              'primary' => true
            ));
          } // if
          
          $this->wireframe->tabs->add('project_people', lang('People'), Router::assemble('project_people', array('project_slug' => $this->active_project->getSlug())), AngieApplication::getImageUrl('icons/listviews/project-people.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE), true);
          
        // Brief
        } elseif($this->request->get('brief') || $this->active_project->getState() == STATE_TRASHED || $this->request->isQuickViewCall()) {
          if ($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
            $this->setView('brief');
          } else {
            if ($this->request->isSingleCall()) {
              $this->setView('brief');
  
              $project_brief_stats = array();
              EventsManager::trigger('on_project_brief_stats', array(&$this->active_project, &$project_brief_stats, &$this->logged_user));
  
              if(Projects::canAdd($this->logged_user) && $this->active_project->getState() != STATE_ARCHIVED) {
                $this->wireframe->actions->add('new_project', lang('New Project'), Router::assemble('projects_add'), array(
                  'onclick' => new FlyoutFormCallback('project_created'),
                  'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
                  'primary' => true
                ));
              } // if
  
              $this->response->assign(array(
                'project_brief_stats' => $project_brief_stats
              ));
            } else {
              if ($this->active_project->getState() == STATE_ARCHIVED) {
                parent::archive();
                parent::render(get_view_path('archive', 'projects', SYSTEM_MODULE));
              } else {
                parent::index();
                parent::render(get_view_path('index', 'projects', SYSTEM_MODULE));
              } // if
            } // if
          }//if
  
        // Full project overview
        } else {
          
          $this->wireframe->setPageObject($this->active_project, $this->logged_user);

          if($this->logged_user->isFeedUser()) {
            $this->wireframe->addRssFeed(
              lang('[:project] Recent Activities', array('project' => $this->active_project->getName())),
              $this->active_project->getRssUrl($this->logged_user)
            );
          } // if

          $day_types = get_day_project_object_types();
          
          $home_sidebars = array();
          EventsManager::trigger('on_project_overview_sidebars', array(&$home_sidebars, &$this->active_project, &$this->logged_user));

          $this->response->assign(array(
            'project_company' => $this->active_project->getCompany(),
            'late_and_today' => ProjectObjects::findLateAndToday($this->logged_user, $this->active_project, $day_types),
            'upcoming_objects' => ProjectObjects::findUpcoming($this->logged_user, $this->active_project, $day_types),
            'home_sidebars' => $home_sidebars
          ));

	        $this->active_project->accessLog()->log($this->logged_user);
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // index
    
    /**
     * Show user subscriptions on objects in selected project
     */
    function user_subscriptions() {
      $offset = ($this->request->get('offset')) ? $this->request->get('offset') : 0;
      $subscriptions = Subscriptions::showByUser($this->logged_user, $this->active_project);
      $temp_sub = array();
      $show_per_number = 50;
      for ($i = $offset * $show_per_number; $i < ($offset*$show_per_number+$show_per_number); $i++) {
        if ($subscriptions[$i]) {
          $temp_sub[] = $subscriptions[$i];
        } else {
          break;
        } //if
      } //for
      $this->response->assign('subscriptions', $temp_sub);
      if ($this->request->isAsyncCall() && $offset > 0) {
        if (empty($temp_sub)) {
          die('empty');
        } // if

        $this->setView(get_view_path('_user_subscriptions_loop',$this->getControllerName(),SYSTEM_MODULE));
      } else {
        $this->response->assign(array(
          'show_more_results_url'     => Router::assemble(project_user_subscriptions, array('project_slug' => $this->active_project->getSlug())),
          'mass_unsubscribe_url' => Router::assemble(project_user_subscriptions_mass_unsubscribe, array('project_slug' => $this->active_project->getSlug()))
        ));
      } //if
    } // user_subscriptions
    
    /**
     * Async mass unsubscription
     */
    function user_subscriptions_mass_unsubscribe() {
      if ($this->request->isAsyncCall()) {
        try {
          if (Subscriptions::massUnsubscribe($this->request->get('unsubscribes'),$this->logged_user)) {
            die('ok');
          } else {
            die('error');
          } //if
        } catch (Exception $e) {
          $this->response->exception($e);
        } //try
      } else {
        $this->response->badRequest();
      } //if
    } //user_subscriptions_mass_unsubscribe
    
    /**
     * Show tasks for a given user
     */
    function user_tasks() {
      $this->response->assign(array(
        'assignments' =>  $this->active_project->getUserAssignments($this->logged_user, true),
        'project_slugs' => Projects::getIdSlugMap(), 
        'task_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task', array('project_slug' => '--PROJECT_SLUG--', 'task_id' => '--TASK_ID--')) : '', 
        'task_subtask_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task_subtask', array('project_slug' => '--PROJECT_SLUG--', 'task_id' => '--TASK_ID--', 'subtask_id' => '--SUBTASK_ID--')) : '', 
        'todo_url' => AngieApplication::isModuleLoaded('todo') ? Router::assemble('project_todo_list', array('project_slug' => '--PROJECT_SLUG--', 'todo_list_id' => '--TODO_LIST_ID--')) : '', 
        'todo_subtask_url' => AngieApplication::isModuleLoaded('todo') ? Router::assemble('project_todo_list', array('project_slug' => '--PROJECT_SLUG--', 'todo_list_id' => '--TODO_LIST_ID--', 'subtask_id' => '--SUBTASK_ID--')) : '', 
        'labels' => Labels::getIdDetailsMap('AssignmentLabel'),
      ));
    } // user_tasks
    
    /**
     * Process add project form
     */
    function add() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(Projects::canAdd($this->logged_user)) {

          $project_data = $this->request->post('project', array(
            'company_id' => $this->owner_company->getId(), 
            'leader_id' => $this->logged_user->getId(), 
            'first_milestone_starts_on' => new DateValue(),
            'currency_id' => Currencies::getDefaultId(), 
          ));
          
          //  If project is based on a project request
          $project_request_id = $this->request->getId('project_request_id');
          if($project_request_id) {
            $project_request = ProjectRequests::findById($project_request_id);
            
            if($project_request instanceof ProjectRequest) {
              $project_data['based_on_id'] = $project_request_id;
              $project_data['based_on_type'] = get_class($project_request);
              $project_data['name'] = $project_request->getName();

              $verbose_custom_fields = $project_request->getVerboseCustomFields();
              if ($verbose_custom_fields) {
                $overview_data = implode("<br/><br/>", array($project_request->getBody(), $verbose_custom_fields));
              } else {
                $overview_data = $project_request->getBody();
              } // if

              $project_data['overview'] = HTML::toRichText($overview_data);
            } else {
              $this->response->notFound();
            } // if
          } // if
          
          // If project is based on a quote
          if(AngieApplication::isModuleLoaded('invoicing')) {
            $quote_id = $this->request->getId('quote_id');
            if($quote_id) {
              $quote = Quotes::findById($quote_id);
              
              if($quote instanceof Quote) {
                $project_data['based_on_id'] = $quote_id;
                $project_data['based_on_type'] = get_class($quote);
                $project_data['name'] = $quote->getName();
                $project_data['overview'] = $quote->getNote();
                $project_data['company_id'] = $quote->getCompanyId();
                $project_data['currency_id'] = $quote->getCurrencyId();

                $project_data['budget'] = Globalization::formatMoney($quote->getSubtotal(), $quote->getCurrency(), $quote->getLanguage());
              } else {
                $this->response->notFound();
              } // if
            } // if
          } // if

          $based_on = null;
          if(isset($project_data['based_on_type']) && $project_data['based_on_type'] && isset($project_data['based_on_id']) && $project_data['based_on_id']) {
            $based_on_class = $project_data['based_on_type'];
            if(class_exists($based_on_class)) {
              $based_on = new $based_on_class($project_data['based_on_id']);

              if(!($based_on instanceof IProjectBasedOn) || $based_on->isNew()) {
                $based_on = null;
              }  // if
            } // if
          } // if

          $this->response->assign(array(
            'project_data' => $project_data,
            'based_on' => $based_on
          ));
          
          if($this->request->isSubmitted()) {
            try {
              $leader = empty($project_data['leader_id']) ? null : Users::findById($project_data['leader_id']);
              $company = empty($project_data['company_id']) ? null : Companies::findById($project_data['company_id']);
              $category = empty($project_data['category_id']) ? null : Categories::findById($project_data['category_id']);
              $template = empty($project_data['project_template_id']) ? null : ProjectTemplates::findById($project_data['project_template_id']);
              $first_milestone_starts_on = empty($project_data['first_milestone_starts_on']) ? null : DateValue::makeFromString($project_data['first_milestone_starts_on']);

              $created_project = ProjectCreator::create(array_var($project_data, 'name'), array(
                'slug' => isset($project_data['slug']) && $project_data['slug'] ? $project_data['slug'] : null,
                'leader' => $leader, 
                'company' => $company, 
                'category' => $category, 
                'based_on' => $based_on, 
                'template' => $template,
	              'positions' => array_var($project_data, 'project_template_positions'),
                'overview' => $project_data['overview'],
                'first_milestone_starts_on' => $first_milestone_starts_on, 
                'label_id' => isset($project_data['label_id']) && $project_data['label_id'] ? $project_data['label_id'] : 0, 
                'budget' => isset($project_data['budget']) ? (float) $project_data['budget'] : null, 
                'currency_id' => isset($project_data['currency_id']) ? (integer) $project_data['currency_id'] : Currencies::getDefaultId(), 
                'custom_field_1' => isset($project_data['custom_field_1']) && $project_data['custom_field_1'] ? $project_data['custom_field_1'] : null,
                'custom_field_2' => isset($project_data['custom_field_2']) && $project_data['custom_field_2'] ? $project_data['custom_field_2'] : null,
                'custom_field_3' => isset($project_data['custom_field_3']) && $project_data['custom_field_3'] ? $project_data['custom_field_3'] : null,
              ));

              clean_menu_projects_and_quick_add_cache();

              // Track project creation
              if(AngieApplication::behaviour()->isTrackingEnabled()) {
                $extra_event_tags = array();

                if($based_on && $based_on instanceof ApplicationObject) {
                  $extra_event_tags = 'based_on_' . Inflector::underscore(get_class($based_on));
                } // if

                if($created_project->getTemplateId()) {
                  $extra_event_tags[] = 'from_template';
                } // if

                AngieApplication::behaviour()->recordFulfilment($this->request->post('_intent_id'), $extra_event_tags, null, function() use ($extra_event_tags) {
                  return array('project_created', $extra_event_tags);
                });
              } // if
              
              if ($this->request->isPageCall()) {
                $this->flash->success('Project ":name" has been created', array('name' => $created_project->getName()));
                $this->response->redirectToUrl($created_project->getViewUrl());
              } else {
                $this->response->respondWithData($created_project, array(
                  'as' => 'project', 
                  'detailed' => true, 
                ));
              } //if
            } catch(Exception $e) {
              DB::rollback('Failed to create project @ ' . __CLASS__);
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
     * Learn more about mail to project
     */
    function mail_to_project_learn_more() {
      if(!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } //if
    } //mail_to_project_learn_more
    
    /**
     * Update project
     */
    function edit() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_project->isLoaded()) {
          if($this->active_project->canEdit($this->logged_user)) {
            $project_data = $this->request->post('project', array(
              'name' => $this->active_project->getName(),
              'overview' => $this->active_project->getOverview(),
              'default_visibility' => $this->active_project->getDefaultVisibility(),
              'leader_id' => $this->active_project->getLeaderId(),
              'category_id' => $this->active_project->getCategoryId(),
              'company_id' => $this->active_project->getCompanyId(),
              'label_id' => $this->active_project->getLabelId(),
              'custom_field_1' => $this->active_project->getCustomField1(),
              'custom_field_2' => $this->active_project->getCustomField2(),
              'custom_field_3' => $this->active_project->getCustomField3(),
            ));
            
            // Manipulate with budget attribute based on user permissions
            if($this->logged_user->canSeeProjectBudgets()) {
              if(!isset($project_data['budget'])) {
                $project_data['budget'] = $this->active_project->getBudget();
              } // if
              
              if(!isset($project_data['currency_id'])) {
                $project_data['currency_id'] = $this->active_project->getCurrencyId();
              } // if
            } else {
              if(isset($project_data['budget'])) {
                unset($project_data['budget']);
              } // if
              
              if(isset($project_data['currency_id'])) {
                unset($project_data['currency_id']);
              } // if
            } // if
            
            $this->response->assign('project_data', $project_data);
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating project @ ' . __CLASS__);

                $new_leader_id = (integer) array_var($project_data, 'leader_id', null, true);

                if($new_leader_id) {
                  $new_leader = DataObjectPool::get('User', $new_leader_id);

                  if($new_leader instanceof User) {
                    $old_leader = $this->active_project->getLeader();

                    if($old_leader instanceof User && $this->active_project->users()->isMember($old_leader, false)) {
                      $this->active_project->users()->replace($old_leader, $new_leader, $this->logged_user, true);
                    } else {
                      $this->active_project->users()->add($new_leader);
                    } // if

                    $this->active_project->setLeader($new_leader);
                  } // if
                } // if
                
                $this->active_project->setAttributes($project_data);
                
                if($this->active_project->isModified('company_id')) {
                  AngieApplication::cache()->removeByObject($this->active_project, 'icons');
                } // if
                
                $this->active_project->save();
                
                DB::commit('Project updated @ ' . __CLASS__);

                clean_menu_projects_and_quick_add_cache();
                
                if($this->request->isPageCall()) {
                  $this->flash->success('Project ":name" has been edited', array('name' => $this->active_project->getName()));
                  $this->response->redirectToUrl($this->active_project->getViewUrl());
                } else {
                  $this->response->respondWithData($this->active_project, array(
                    'as' => 'project', 
                    'detailed' => true, 
                  ));
                } //if
              } catch(Exception $e) {
                DB::rollback('Failed to update project @ ' . __CLASS__);
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
     * Delete project
     */
    function delete() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_project->isLoaded()) {
          if($this->active_project->canDelete($this->logged_user)) {
            try {
              $this->active_project->delete();
              $this->response->respondWithData($this->active_project, array(
                'as' => 'project', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
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
    } // delete
    
    /**
     * Show project settings index page
     */
    function settings() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        $settings_data = $this->request->post('settings');
      
        if(empty($settings_data)) {
          if(ConfigOptions::hasValueFor('default_project_object_visibility', $this->active_project)) {
            $default_project_object_visibility = ConfigOptions::getValueFor('default_project_object_visibility', $this->active_project);
          } else {
            $default_project_object_visibility = null;
          } // if

          if(ConfigOptions::hasValueFor('clients_can_delegate_to_employees', $this->active_project)) {
            $clients_can_delegate_to_employees = ConfigOptions::getValueFor('clients_can_delegate_to_employees', $this->active_project);;
          } else {
            $clients_can_delegate_to_employees = null;
          } // if

          if(AngieApplication::isModuleLoaded('tracking') && ConfigOptions::hasValueFor('default_billable_status', $this->active_project)) {
            $default_billable_status = (boolean) ConfigOptions::getValueFor('default_billable_status', $this->active_project); // yes_no_default works only with boolean values
          } else {
            $default_billable_status = null;
          } // if
          
          $settings_data = array(
            'use_custom_tabs' => ConfigOptions::hasValueFor('project_tabs', $this->active_project),
            'project_tabs' => ConfigOptions::getValueFor('project_tabs', $this->active_project), 
            'default_project_object_visibility' => $default_project_object_visibility,
            'clients_can_delegate_to_employees' => $clients_can_delegate_to_employees,
            'default_billable_status' => $default_billable_status,
          );
        } // if
        
        $this->response->assign(array(
          'settings_data' => $settings_data,
          'default_clients_can_delegate_to_employees' => ConfigOptions::getValue('clients_can_delegate_to_employees'),
          'default_default_billable_status' => AngieApplication::isModuleLoaded('tracking') ? ConfigOptions::getValue('default_billable_status') : 1,
        ));
        
        if($this->request->isSubmitted()) {
          try {
            if(array_var($settings_data, 'use_custom_tabs')) {
              $project_tabs = array_var($settings_data, 'project_tabs');

              // remove outline from project tabs if milestone or task are also not there
              if (!in_array('milestones', $project_tabs) || !in_array('tasks', $project_tabs)) {
                array_remove_by_value($project_tabs, 'outline', false);
              } // if

              ConfigOptions::setValueFor('project_tabs', $this->active_project, $project_tabs);
            } else {
              ConfigOptions::removeValuesFor($this->active_project, 'project_tabs');
            } // if
            
            if((string) $settings_data['default_project_object_visibility'] === '') {
              ConfigOptions::removeValuesFor($this->active_project, 'default_project_object_visibility');
            } else {
              ConfigOptions::setValueFor('default_project_object_visibility', $this->active_project, (integer) $settings_data['default_project_object_visibility']);
            } // if

            if((string) $settings_data['clients_can_delegate_to_employees'] === '') {
              ConfigOptions::removeValuesFor($this->active_project, 'clients_can_delegate_to_employees');
            } else {
              ConfigOptions::setValueFor('clients_can_delegate_to_employees', $this->active_project, (integer) $settings_data['clients_can_delegate_to_employees']);
            } // if

            if(AngieApplication::isModuleLoaded('tracking')) {
              if((string) $settings_data['default_billable_status'] === '') {
                ConfigOptions::removeValuesFor($this->active_project, 'default_billable_status');
              } else {
                ConfigOptions::setValueFor('default_billable_status', $this->active_project, (integer) $settings_data['default_billable_status']);
              } // if
            } // if
                        
            $response = array(
              'settings' => array(
                'tabs'        => $this->active_project->getTabs($this->logged_user, AngieApplication::INTERFACE_DEFAULT, false),
                'visibility'  => ConfigOptions::getValueFor('default_project_object_visibility', $this->active_project)
              ),
              'project' => $this->active_project
            );

            clean_menu_projects_and_quick_add_cache();
            
            $this->response->respondWithData($response, array( 
              'detailed' => true, 
            ));
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // settings

    /**
     * List meta information about all project attachments
     */
    function &attachments() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Attachments::findForApiByProject($this->active_project), array(
          'as' => 'attachments',
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // attachments

    /**
     * Return all project comments in a single API response
     */
    function comments() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Comments::findForApiByProject($this->active_project), array(
          'as' => 'comments',
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // comments

    /**
     * Return all project comments in a single API response
     */
    function subtasks() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Subtasks::findForApiByProject($this->active_project), array(
          'as' => 'subtasks',
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // subtasks
    
    /**
     * Serve iCal data
     */
    function ical() {
      if ($this->active_project->isLoaded()) {
        if ($this->logged_user->isFeedUser()) {
          $filter = $this->logged_user->projects()->getVisibleTypesFilterByProject($this->active_project, get_completable_project_object_types());
          if ($filter) {
            $objects = ProjectObjects::find(array(
              'conditions' => array($filter . ' AND completed_on IS NULL AND state >= ? AND visibility >= ?', STATE_VISIBLE, $this->logged_user->getMinVisibility()),
              'order' => 'priority DESC',
            ));

	          $task_ids = array();
	          if (is_foreachable($objects)) {
		          foreach ($objects as $object) {
			          if ($object instanceof Task) {
				          $task_ids[] = $object->getId();
			          } // if
		          } // foreach
	          } // if

	          if ($task_ids) {
		          $subtasks = Subtasks::find(array(
			          'conditions' => array('parent_type = ? AND parent_id IN (?) AND state >= ? AND completed_on IS NULL', 'Task', $task_ids, STATE_VISIBLE)
		          ));

		          if ($subtasks) {
			          $objects = array_merge($objects->toArray(), $subtasks->toArray());
		          } // if
	          } // if

            render_icalendar($this->active_project->getName(), $objects);
            die();
          } else {
            $this->response->notFound();
          } // if
        } else {
          $this->response->forbidden();
        } //if
      } else {
        $this->response->notFound();
      } // if
    } // ical
    
    /**
     * Show iCal subscribe page
     */
    function ical_subscribe() {
      if ($this->active_project->isLoaded()) {
        if ($this->logged_user->isFeedUser()) {
          $this->wireframe->hidePrintButton();
          $feed_token  = $this->logged_user->getFeedToken();

          $ical_url = Router::assemble('project_ical', array('project_slug' => $this->active_project->getSlug(), 'auth_api_token' => $feed_token));

          $ical_subscribe_url = str_replace(array('http://', 'https://'), array('webcal://', 'webcal://'), $ical_url);

          $this->response->assign(array(
            'ical_url' => $ical_url,
            'ical_subscribe_url' => $ical_subscribe_url
          ));
        } else {
          $this->response->forbidden();
        } //if
      } else {
        $this->response->notFound();
      } // if
    } // ical_subscribe

	  /**
	   * Reschedule project
	   */
	  function reschedule() {
		  if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			  if ($this->active_project->isLoaded()) {
				  if ($this->active_project->canEdit($this->logged_user)) {
					  $new_start_on = $this->request->post('new_start_on');

					  $query = "SELECT MIN(date_field_1) as start_on FROM " . TABLE_PREFIX ."project_objects WHERE project_id = ? AND type = ? GROUP BY project_id";
					  $old_start_on = DB::executeFirstCell($query, $this->active_project->getId(), "Milestone");

					  if ($this->request->isSubmitted()) {
						  $old_start_on = new DateValue(strtotime($old_start_on));
						  $new_start_on = new DateValue(strtotime($new_start_on));

						  ProjectScheduler::rescheduleProject($this->active_project, $old_start_on, $new_start_on);

						  /*$query = "SELECT MIN(date_field_1) as start_on, MAX(due_on) as due_on FROM " . TABLE_PREFIX ."project_objects WHERE project_id = ? AND type = ? GROUP BY project_id";
						  $new_dates = DB::executeFirstRow($query, $this->active_project->getId(), 'Milestone');

						  $start_on = array_var($new_dates, 'start_on');
						  $due_on = array_var($new_dates, 'due_on');
						  $result = array(
							  'id'            => $this->active_project->getId(),
							  'slug'          => $this->active_project->getSlug(),
							  'name'          => $this->active_project->getName(),
							  'start_on'      => $start_on ? new DateValue(strtotime($start_on)) : null,
							  'due_on'        => $due_on ? new DateValue(strtotime($due_on)) : null,
							  'completed_on'  => $this->active_project->getCompletedOn() ? new DateValue(strtotime($this->active_project->getCompletedOn())) : null,
							  'permissions'   => array(
								  'can_edit'      => true,
								  'can_trash'     => true
							  ),
							  'urls'          => array(
								  'edit'          => $this->active_project->getEditUrl(),
								  'trash'         => $this->active_project->getDeleteUrl(),
								  'open'          => "#", //str_replace($project_slug_prefix_pattern, $project_slug, $reopen_project_url_pattern),
								  'complete'      => "#", //str_replace($project_slug_prefix_pattern, $project_slug, $complete_project_url_pattern),
								  'reschedule'    => "#" //str_replace($project_slug_prefix_pattern, $project_slug, $reschedule_project_url_pattern)
							  ),
							  'permalink'     => $this->active_project->getViewUrl()
						  );*/

						  $this->response->respondWithData($this->active_project, array(
							  'as' => 'project',
							  'detailed' => true
						  ));
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
	  } // reschedule

    /**
     * Export project as file
     */
    function export_as_file() {
      //if($this->request->isApiCall()) {
        if($this->active_project->isLoaded()) {
          if($this->active_project->canView($this->logged_user)) {
            try {
              $skip_files = false;
              if($this->request->get('skip_files')) {
                $skip_files = (boolean) $this->request->get('skip_files');
              } // if

              $changes_since = null;
              if($this->request->get('changes_since')) {
                $changes_since = (int) $this->request->get('changes_since');
              } // if

              $this->active_project->exportAsFile($this->logged_user, $skip_files, $changes_since);
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
//      } else {
//        $this->response->badRequest();
//      } // if
    } // export_as_file

    /**
     * Lock synchronization
     */
    function sync_lock() {
      if($this->request->isApiCall()) {
        if($this->active_project->isLoaded()) {
          if($this->active_project->canLockUnlockSync($this->logged_user)) {
            try {
              $changes_num = 1;
              if($this->request->get('changes_num')) {
                $changes_num = (integer) $this->request->get('changes_num');
              } // if

              $this->active_project->syncLock($changes_num);
              $this->response->ok();
            } catch(Exception $e) {
              $this->response->exception($e);
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
    } // sync_lock

    /**
     * Unlock synchronization
     */
    function sync_unlock() {
      if($this->request->isApiCall()) {
        if($this->active_project->isLoaded()) {
          if($this->active_project->canLockUnlockSync($this->logged_user)) {
            try {
              $this->active_project->syncUnlock();
              $this->response->ok();
            } catch(Exception $e) {
              $this->response->exception($e);
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
    } // sync_unlock

	  /**
	   * Convert to a template
	   */
	  function convert_to_a_template() {
		  if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			  $project_template_data = $this->request->post('project_template', array(
				  'name' => $this->active_project->getName(),
				  'start_date' => $this->active_project->getCreatedOn()
			  ));

			  $this->response->assign('project_template_data', $project_template_data);

			  if ($this->request->isSubmitted()) {
				  try {
					  DB::beginWork('Convert project to a template @ ' . __CLASS__);

					  // Create project template
					  $project_template = new ProjectTemplate();
					  $project_template->setAttributes($project_template_data);
					  $project_template->setCompanyId($this->active_project->getCompanyId());
					  $project_template->setCreatedBy($this->logged_user);
					  $project_template->save();

					  $first_milestone_starts_on = Milestones::getFirstMilestoneStartsOn($this->active_project);

					  // Copy and map task categories
					  $map_task_categories = array();
					  $task_categories = Categories::findBy($this->active_project, 'TaskCategory');
					  if (is_foreachable($task_categories)) {
						  foreach($task_categories as $task_category) {
							  if ($task_category instanceof TaskCategory) {
								  $task_category_template = new ProjectObjectTemplate('Category');
								  $task_category_template->setTemplate($project_template);
								  $task_category_template->setValue('name', $task_category->getName());
								  $task_category_template->setSubtype('task');

								  $task_category_template->save();

								  $map_task_categories[$task_category->getId()] = $task_category_template->getId();
							  } // if
						  } // foreach
					  } // if

					  // Copy and map discussion categories
					  $map_discussion_categories = array();
					  $discussion_categories = Categories::findBy($this->active_project, 'DiscussionCategory');
					  if (is_foreachable($discussion_categories)) {
						  foreach($discussion_categories as $discussion_category) {
							  if ($discussion_category instanceof DiscussionCategory) {
								  $discussion_category_template = new ProjectObjectTemplate('Category');
								  $discussion_category_template->setTemplate($project_template);
								  $discussion_category_template->setValue('name', $discussion_category->getName());
								  $discussion_category_template->setSubtype('discussion');

								  $discussion_category_template->save();

								  $map_discussion_categories[$discussion_category->getId()] = $discussion_category_template->getId();
							  } // if
						  } // foreach
					  } // if

					  // Copy and map file categories
					  $map_file_categories = array();
					  $file_categories = Categories::findBy($this->active_project, 'AssetCategory');
					  if (is_foreachable($file_categories)) {
						  foreach($file_categories as $file_category) {
							  if ($file_category instanceof AssetCategory) {
								  $file_category_template = new ProjectObjectTemplate('Category');
								  $file_category_template->setTemplate($project_template);
								  $file_category_template->setValue('name', $file_category->getName());
								  $file_category_template->setSubtype('file');

								  $file_category_template->save();

								  $map_file_categories[$file_category->getId()] = $file_category_template->getId();
							  } // if
						  } // foreach
					  } // if

					  // Copy and map positions
					  $positions = array_var($project_template_data, 'positions');
					  $map_positions = array();
					  if (is_foreachable($positions)) {
						  foreach ($positions as $user_id => $position_name) {
							  if ($position_name) {
								  $position_template = new ProjectObjectTemplate('Position');
								  $position_template_data = array(
									  'user_id' => (string) $user_id,
									  'name'    => $position_name
								  );
								  $row = DB::executeFirstRow('SELECT role_id, permissions FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ? AND project_id = ?', $user_id, $this->active_project->getId());
								  if(!empty($row)) {
									  $position_template_data['project_template_permissions'] = array(
										  'role_id' => $row['role_id'] ? $row['role_id'] : 0,
										  'permissions' => $row['permissions'] ? unserialize($row['permissions']) : null
									  );
								  } // if
								  $position_template->setValues($position_template_data);
								  $position_template->setTemplate($project_template);
								  $position_template->save();

								  $map_positions[$user_id] = $position_template->getId();
							  } // if
						  } // foreach
					  } // if

					  // Copy milestones
					  $map_milestone = array();
					  $milestones = Milestones::findByProject($this->active_project, $this->logged_user);
					  if (is_foreachable($milestones)) {
						  foreach($milestones as $milestone) {
							  if ($milestone instanceof Milestone) {
								  $milestone_template = new ProjectObjectTemplate('Milestone');

								  $assignee = $milestone->assignees()->getAssignee();
								  $map_other_assignees = array();

								  $other_assignee_ids = $milestone->assignees()->getOtherAssigneeIds();
								  //var_dump($other_assignee_ids); exit;
								  if (is_foreachable($other_assignee_ids)) {
									  foreach ($other_assignee_ids as $other_assignee_id) {
										  $map_other_assignees[] = (string) $map_positions[$other_assignee_id];
									  } // foreach
								  } // if
								  $milestone_template_data = array(
									  'other_assignees' => $map_other_assignees,
									  'assignee_id' => $assignee instanceof User ? (string) $map_positions[$assignee->getId()] : "0",
									  'name' => $milestone->getName(),
									  'body' => $milestone->getBody(),
									  'priority' => $milestone->getPriority()
								  );
								  if ($milestone->getStartOn() instanceof DateValue && $milestone->getDueOn() instanceof DateValue) {
									  $milestone_template_data['specify'] = 1;
									  $milestone_template_data['start_on'] = (string) ($milestone->getStartOn()->daysBetween($first_milestone_starts_on) + 1);
									  $milestone_template_data['due_on'] = (string) ($milestone->getDueOn()->daysBetween($first_milestone_starts_on) + 1);
								  }

								  $milestone_template->setValues($milestone_template_data);
								  $milestone_template->setTemplate($project_template);
								  $milestone_template->save();

								  $map_milestone[$milestone->getId()] = $milestone_template;
							  } // if
						  } // foreach
					  } // if

					  // Copy tasks
					  $tasks = Tasks::findByProject($this->active_project, $this->logged_user);
					  if (is_foreachable($tasks)) {
						  foreach ($tasks as $task) {
							  if ($task instanceof Task) {
								  $task_template = new ProjectObjectTemplate('Task');

								  $assignee = $task->assignees()->getAssignee();
								  $map_other_assignees = array();

								  $other_assignee_ids = $task->assignees()->getOtherAssigneeIds();
								  if (is_foreachable($other_assignee_ids)) {
									  foreach ($other_assignee_ids as $other_assignee_id) {
										  $map_other_assignees[] = (string) $map_positions[$other_assignee_id];
									  } // foreach
								  } // if
								  $task_template_data = array(
									  'other_assignees' => $map_other_assignees,
									  'assignee_id' => $assignee instanceof User ? (string) $map_positions[$assignee->getId()] : "0",
									  'name' => $task->getName(),
									  'body' => $task->getBody(),
									  'category_id' => $map_task_categories[$task->getCategoryId()],
									  'priority' => $task->getPriority(),
									  'visibility' => $task->getVisibility(),
									  'label_id' => $task->getLabelId()
								  );

								  if (AngieApplication::isModuleLoaded('tracking')) {
									  $estimate = $task->tracking()->getEstimate();

									  if ($estimate instanceof Estimate) {
										  $task_template_data['estimate_value'] = (string) $estimate->getValue();
										  $task_template_data['estimate_job_type_id'] = (string) $estimate->getJobTypeId();
									  } // if
								  } // if

								  $parent = $task->getMilestone();
								  $milestone_template = $map_milestone[$task->getMilestoneId()];

								  $parent_start_on = $parent instanceof Milestone && $parent->getStartOn() instanceof DateValue ? $parent->getStartOn() : $project_template->getCreatedOn();

								  if ($task->getDueOn() instanceof DateValue && $parent_start_on instanceof DateValue) {
									  $task_template_data['specify'] = 1;
									  if ($parent_start_on->getTimestamp() > $task->getDueOn()->getTimestamp()) {
										  $task_template_data['due_on'] = "1";
									  } else {
										  $task_template_data['due_on'] = (string) ($task->getDueOn()->daysBetween($parent_start_on) + 1);
									  } // if
								  } // if

								  $task_template->setValues($task_template_data);
								  $task_template->setParentId($milestone_template instanceof ProjectObjectTemplate ? $milestone_template->getId() : 0);
								  $task_template->setTemplate($project_template);
								  $task_template->setPosition($task->getPosition());
								  $task_template->save();

								  // Copy subtasks
								  $subtasks = Subtasks::findByParent($task);
								  if (is_foreachable($subtasks)) {
									  foreach ($subtasks as $subtask) {
										  if ($subtask instanceof Subtask) {
											  $subtask_template = new ProjectObjectTemplate('Subtask');

											  $assignee = $subtask->assignees()->getAssignee();
											  $subtask_template_data = array(
												  'body' => $subtask->getName(),
												  'assignee_id' => $assignee instanceof User ? (string) $map_positions[$assignee->getId()] : "0",
												  'priority' => $subtask->getPriority(),
												  'label_id' => $subtask->getLabelId()
											  );

											  if ($subtask->getDueOn() instanceof DateValue && $task->getDueOn() instanceof DateValue) {
												  $subtask_template_data['specify'] = 1;
												  if ($parent_start_on->getTimestamp() > $subtask->getDueOn()->getTimestamp()) {
													  $subtask_template_data['due_on'] = "1";
												  } else {
													  $subtask_template_data['due_on'] = (string) ($subtask->getDueOn()->daysBetween($parent_start_on) + 1);
												  } // if
											  } // if

											  $subtask_template->setValues($subtask_template_data);
											  $subtask_template->setTemplate($project_template);
											  $subtask_template->setParentId($task_template->getId());
											  $subtask_template->setPosition($subtask->getPosition());
											  $subtask_template->save();
										  } // if
									  } // foreach
								  } // if
							  } // if
						  } // foreach
					  } // if

					  // Copy files
						$files = ProjectAssets::findByTypeAndProject($this->active_project, array('File'));
					  if (is_foreachable($files)) {
						  foreach ($files as $file) {
							  if ($file instanceof File) {
									$file_template = new ProjectObjectTemplate('File');

								  $new_name = AngieApplication::getAvailableUploadsFileName();
								  copy($file->download()->getPath(), $new_name);

								  $file_data = array(
									  'name'        => $file->getName(),
									  'location'    => basename($new_name),
									  'mime_type'   => $file->getMimeType(),
									  'category_id' => $map_file_categories[$file->getCategoryId()]
								  );
								  $file_template->setValues($file_data);
								  $file_template->setFileSize($file->getSize());
								  $file_template->setTemplate($project_template);
								  $file_template->save();
							  } // if
						  } // foreach
					  } // if

					  DB::commit('Project converted to template @ ' . __CLASS__);

					  $this->response->respondWithData($project_template, array(
						  'as' => 'project_template',
						  'detailed' => true
					  ));
				  } catch (Exception $e) {
					  DB::rollback('Failed conversion project to template @ ' . __CLASS__);
					  $this->response->exception($e);
				  } // try
			  } // if
		  } else {
			  $this->response->badRequest();
		  } // if
	  } // convert_to_a_template

  }
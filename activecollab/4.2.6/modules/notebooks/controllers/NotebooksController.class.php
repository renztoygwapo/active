<?php

  // Extend ProjectsController
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Notebooks controller
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage controllers
   */
  class NotebooksController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = NOTEBOOKS_MODULE;
    
    /**
     * Notebook instance
     *
     * @var Notebook
     */
    protected $active_notebook;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Avatar controller delegate
     *
     * @var NotebookAvatarController
     */
    protected $avatar_delegate;
    
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
     * Reminders controller delegate
     *
     * @var RemindersController
     */
    protected $reminders_delegate;
    
    /**
     * Move to project delegate controller
     *
     * @var MoveToProjectController
     */
    protected $move_to_project_delegate;

    /**
     * Sharing settings delegate
     *
     * @var SharingSettingsController
     */
    protected $sharing_settings_delegate;

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
     * Construct notebooks controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'notebooks') {
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'project_notebook');
        $this->avatar_delegate = $this->__delegate('notebook_avatar', AVATAR_FRAMEWORK_INJECT_INTO, 'project_notebook');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_notebook');
        $this->attachments_delegate = $this->__delegate('attachments', ATTACHMENTS_FRAMEWORK_INJECT_INTO, 'project_notebook');
        $this->reminders_delegate = $this->__delegate('reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'project_notebook');
        $this->move_to_project_delegate = $this->__delegate('move_to_project', SYSTEM_MODULE, 'project_notebook');
        $this->sharing_settings_delegate = $this->__delegate('sharing_settings', SYSTEM_MODULE, 'project_notebook');

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'project_notebook');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'project_notebook');
	      } // if
      } // if
    } // __construct
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!Notebooks::canAccess($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
      
      $notebooks_url = Router::assemble('project_notebooks', array('project_slug' => $this->active_project->getSlug()));
      
      $this->wireframe->tabs->setCurrentTab('notebooks');
      $this->wireframe->breadcrumbs->add('notebooks', lang('Notebooks'), $notebooks_url);
      
      $notebook_id = $this->request->get('notebook_id');
      if($notebook_id) {
        $this->active_notebook = Notebooks::findById($notebook_id);
      } // if
      
      if($this->active_notebook instanceof Notebook) {
        if (!$this->active_notebook->isAccessible()) {
          $this->response->notFound();
        } // if

        if ($this->active_notebook->getState() == STATE_ARCHIVED) {
          $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_notebooks_archive', array('project_slug' => $this->active_project->getSlug())));
        } // if

        $this->wireframe->breadcrumbs->add('notebook', $this->active_notebook->getName(), $this->active_notebook->getViewUrl());

        if($this->active_notebook->canEdit($this->logged_user)) {
          if($this->request->isWebBrowser() || $this->request->isMobileDevice()) {
            $this->wireframe->actions->add('new_notebook_page', lang("New Page"), $this->active_notebook->getAddPageUrl(), array(
              'onclick' => new FlyoutFormCallback('notebook_page_created'),
              'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            ));
          } // if

          if($this->request->isPhone() && $this->active_notebook->canEdit($this->logged_user)) {
            $this->wireframe->actions->add('new_notebook_page', lang("New Page"), $this->active_notebook->getAddPageUrl(), array(
              'icon' => AngieApplication::getImageUrl('icons/navbar/add-pages.png', NOTEBOOKS_MODULE, AngieApplication::getPreferedInterface())
            ));
          } // if
        } // if
      } else {
        $this->active_notebook = new Notebook();
        $this->active_notebook->setProject($this->active_project);
      } // if
      
      $add_notebook_url = false;
      if(($this->request->isWebBrowser() || $this->request->isMobileDevice())) {
        if(Notebooks::canAdd($this->logged_user, $this->active_project)) {
          $add_notebook_url = Router::assemble('project_notebooks_add', array('project_slug' => $this->active_project->getSlug()));

          $this->wireframe->actions->add('new_notebook', lang('New Notebook'), $add_notebook_url, array(
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'onclick' => new FlyoutFormCallback('notebook_created'),
            'primary' => true
          ));
        } // if
      } // if
      
      $this->response->assign(array(
        'active_notebook' => $this->active_notebook,
        'notebooks_url' => $notebooks_url,
        'add_notebook_url' => $add_notebook_url
      ));
      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook
        ));
      } // if
      
      if($this->avatar_delegate instanceof NotebookAvatarController) {
        $this->avatar_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook
        ));
      } // if
      
      if($this->subscriptions_delegate instanceof SubscriptionsController) {
        $this->subscriptions_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook
        ));
      } // if
      
      if($this->attachments_delegate instanceof AttachmentsController) {
        $this->attachments_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook
        ));
      } // if
      
      if($this->reminders_delegate instanceof RemindersController) {
        $this->reminders_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook
        ));
      } // if
      
      if($this->move_to_project_delegate instanceof MoveToProjectController) {
        $this->move_to_project_delegate->__setProperties(array(
          'active_project' => &$this->active_project,
          'active_object' => &$this->active_notebook,
        ));
      } // if

      if($this->sharing_settings_delegate instanceof SharingSettingsController) {
        $this->sharing_settings_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook,
        ));
      } // if

	    if ($this->access_logs_delegate instanceof AccessLogsController) {
		    $this->access_logs_delegate->__setProperties(array(
			    'active_object' => &$this->active_notebook
		    ));
	    } // if

	    if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		    $this->history_of_changes_delegate->__setProperties(array(
			    'active_object' => &$this->active_notebook
		    ));
	    } // if
    } // __before
    
    /**
     * Notebooks homepage
     */
    function index() {

      // Shared properties
      if($this->request->isWebBrowser() || $this->request->isMobileDevice()) {
        $this->response->assign('notebooks', Notebooks::findForObjectsList($this->active_project, $this->logged_user));
      } // if

      // Regular web browser request
      if($this->request->isWebBrowser()) {
        $this->wireframe->javascriptAssign('reorder_notebooks_url', Router::assemble('project_notebooks_reorder', array('project_slug' => $this->active_project->getSlug())));
        
      // Tablet device
      } elseif($this->request->isTablet()) {
        throw new NotImplementedError(__METHOD__);
        
      // API call
      } elseif($this->request->isApiCall()) {
        $this->response->respondWithData(Notebooks::findByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility()), array(
          'as' => 'notebooks',
        ));
      } // if
    } // index
    
    /**
     * Show archived notebooks (for mobile devices only)
     */
    function archive() {
      if($this->request->isMobileDevice()) {
        $this->response->assign('notebooks', Notebooks::findArchivedByProject($this->active_project, $this->logged_user->getMinVisibility()));
      } else if ($this->request->isWebBrowser()) {
        $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_notebooks_archive', array('project_slug' => $this->active_project->getSlug())));
        $this->response->assign(array(
          'notebooks' => Notebooks::findForObjectsList($this->active_project, $this->logged_user, STATE_ARCHIVED),
          'in_archive' => true
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * View notebook
     */
    function view() {
      if($this->active_notebook->isLoaded()) {
        if($this->active_notebook->canView($this->logged_user)) {
          $this->wireframe->setPageObject($this->active_notebook, $this->logged_user);
          $this->wireframe->print->enable();

          $min_state = STATE_ARCHIVED;
          if ($this->active_notebook->getState() == STATE_TRASHED) {
            $min_state = STATE_TRASHED;
          } // if

          // Regular request
          if ($this->request->get('objects_list_refresh')) {
            $this->response->respondWithData(NotebookPages::findForObjectsList($this->active_notebook, $this->logged_user, $min_state, true));
          } else if($this->request->isWebBrowser()) {
            $this->wireframe->list_mode->enable();

            $this->active_notebook->accessLog()->log($this->logged_user);
            $this->response->assign('notebook_pages', NotebookPages::findForObjectsList($this->active_notebook, $this->logged_user, $min_state, true));
            
		        // mass manager
		        if ($this->active_notebook->canEdit($this->logged_user)) {
		        	$notebook_page = new NotebookPage();
		        	$notebook_page->setParent($this->active_notebook, false);
		        	
		        	$mass_manager = new MassManager($this->logged_user, $notebook_page);        	
		        	$this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
		        } // if

          // Phone request
          } elseif($this->request->isPhone()) {
            $this->wireframe->actions->remove(array('pin_unpin', 'favorites_toggler'));

            $this->response->assign('notebook_pages', NotebookPages::findByNotebook($this->active_notebook));
          
          //printer
          } elseif($this->request->isPrintCall()) {
            
            $this->response->assign('notebook_pages', NotebookPages::findByNotebook($this->active_notebook));
 
          // Tablet device
          } elseif($this->request->isTablet()) {
            throw new NotImplementedError(__METHOD__);

          // API
          } elseif($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_notebook, array(
              'as' => 'notebook', 
              'detailed' => true, 
            ));
          } // if
          
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view
    
    /**
     * Create a new notebook
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if(Notebooks::canAdd($this->logged_user, $this->active_project)) {
          $notebook_data = $this->request->post('notebook', array(
            'milestone_id' => $this->request->get('milestone_id'),
            'visibility' => $this->active_project->getDefaultVisibility()
          ));
          $this->response->assign('notebook_data', $notebook_data);

          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating a new notebook @ ' . __CLASS__);

              $this->active_notebook = new Notebook();

              $this->active_notebook->attachments()->attachUploadedFiles($this->logged_user);

              $this->active_notebook->setAttributes($notebook_data);
              $this->active_notebook->setProjectId($this->active_project->getId());
              $this->active_notebook->setCreatedBy($this->logged_user);
              $this->active_notebook->setState(STATE_VISIBLE);

              $this->active_notebook->save();
              
              $this->active_notebook->subscriptions()->set(array_unique(array_merge(
                (array) $this->logged_user->getId(),
                (array) $this->active_project->getLeaderId(),
                (array) $this->request->post('notify_users')
              )), false);

              DB::commit('Notebook created @ ' . __CLASS__);

              AngieApplication::notifications()
                ->notifyAbout('notebooks/new_notebook', $this->active_notebook, $this->logged_user)
                ->sendToSubscribers();

              if($this->request->isPageCall()) {
                $this->response->redirectToUrl($this->active_notebook->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_notebook, array(
                  'as' => 'notebook', 
                  'detailed' => true, 
                ));
              } // if
            } catch(Exception $e) {
              DB::rollback('Failed to create a new Notebook @ ' . __FILE__);

              if($this->request->isPageCall()) {
                $this->response->assign('errors', $e);
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
     * Edit notebook
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_notebook->isLoaded()) {
          if($this->active_notebook->canEdit($this->logged_user)) {
            $this->wireframe->actions->remove(array('new_notebook_page'));

            $notebook_data = $this->request->post('notebook', array(
              'name' => $this->active_notebook->getName(),
              'body' => $this->active_notebook->getBody(),
              'milestone_id' => $this->active_notebook->getMilestoneId(),
              'visibility' => $this->active_notebook->getVisibility(),
            ));
            $this->response->assign(array(
              'notebook_data' => $notebook_data,
            ));
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating a notebook @ ' . __CLASS__);
                
                $this->active_notebook->attachments()->attachUploadedFiles($this->logged_user);
                
                $this->active_notebook->setAttributes($notebook_data);
                $this->active_notebook->save();
                
                DB::commit('Notebook updated @ ' . __CLASS__);
                
                if($this->request->isPageCall()) {
                  $this->response->redirectToUrl($this->active_notebook->getViewUrl());
                } else {
                  $this->response->respondWithData($this->active_notebook, array(
                    'as' => 'notebook',
                    'detailed' => true,
                  ));
                } // if
              } catch(Exception $e) {
                DB::rollback('Failed to update notebook @ ' . __CLASS__);
                
                if($this->request->isPageCall()) {
                  $this->response->assign('errors', $e);
                } else {
                  $this->response->exception($e);
                } // if
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
     * Update notebooks position
     */
    function reorder() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $new_order = $this->request->post('new_order');
        if(empty($new_order)) {
          $this->response->badRequest();
        } //if
        $new_order = explode(",", $new_order);
        
        try {
          DB::beginWork('Updating notebooks position @ ' . __CLASS__);
          
          $position = 1;
          foreach($new_order as $notebook_id) {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET position = ? WHERE id = ? AND type = ?', $position, $notebook_id, 'Notebook');
            $position++;
          }//foreach
          
          DB::commit('Notebooks position updated @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to update notebooks position @ ' . __CLASS__);
          
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // reorder
  
  }
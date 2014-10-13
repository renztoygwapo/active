<?php

	// Extend notebooks controller  
	AngieApplication::useController('notebooks', NOTEBOOKS_MODULE);

  /**
   * Notebook pages controller
   *
   * @package activeCollab.modules.notebooks
   * @subpackage controllers
   */
  class NotebookPagesController extends NotebooksController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = NOTEBOOKS_MODULE;
    
    /**
     * Selected notebook page
     *
     * @var NotebookPage
     */
    protected $active_notebook_page;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
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
    protected $api_actions = array('index', 'view', 'add', 'edit', 'archive', 'unarchive');
    
    /**
     * Construct notebook pages controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'notebook_pages') {
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'project_notebook_page');
        $this->comments_delegate = $this->__delegate('comments', COMMENTS_FRAMEWORK_INJECT_INTO, 'project_notebook_page');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_notebook_page');
        $this->attachments_delegate = $this->__delegate('attachments', ATTACHMENTS_FRAMEWORK_INJECT_INTO, 'project_notebook_page');

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'project_notebook_page');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'project_notebook_page');
	      } // if
      } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $notebook_page_id = $this->request->get('notebook_page_id');
      if($notebook_page_id) {
        $this->active_notebook_page = NotebookPages::findById($notebook_page_id);
      } // if
      
      if($this->active_notebook_page instanceof NotebookPage) {
        if (!$this->active_notebook_page->isAccessible()) {
          $this->response->notFound();
        } // if

        $this->wireframe->breadcrumbs->add('notebook_page', $this->active_notebook_page->getName(), $this->active_notebook_page->getViewUrl());
      } else {
        $this->active_notebook_page = new NotebookPage();
        $this->active_notebook_page->setParent($this->active_notebook);
      } // if
      
      $this->smarty->assign('active_notebook_page', $this->active_notebook_page);
      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook_page
        ));
      } // if
      
      if($this->comments_delegate instanceof CommentsController) {
        $this->comments_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook_page
        ));
      } // if
      
      if($this->subscriptions_delegate instanceof SubscriptionsController) {
        $this->subscriptions_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook_page
        ));
      } // if
      
      if($this->attachments_delegate instanceof AttachmentsController) {
        $this->attachments_delegate->__setProperties(array(
          'active_object' => &$this->active_notebook_page
        ));
      } // if

	    if ($this->access_logs_delegate instanceof AccessLogsController) {
		    $this->access_logs_delegate->__setProperties(array(
			    'active_object' => &$this->active_notebook_page
		    ));
	    } // if

	    if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		    $this->history_of_changes_delegate->__setProperties(array(
			    'active_object' => &$this->active_notebook_page
		    ));
	    } // if
    } // __before
    
    /**
     * Show archived notebook pages (for mobile devices only)
     */
    function archive() {
      if($this->request->isMobileDevice()) {
      	$this->wireframe->actions->remove(array('new_notebook_page'));
      	
        $this->response->assign('notebook_pages', NotebookPages::findArchivedByNotebook($this->active_notebook));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Mass Edit
     */
    function mass_edit() {
    	if ($this->getControllerName() == 'notebook_pages') {
    		$this->mass_edit_objects = NotebookPages::findByIds($this->request->post('selected_item_ids'), STATE_ARCHIVED);
    	} // if
    	parent::mass_edit();
    } // mass_edit
    
		/**
		 * This is moved to the notebooks controler so we could load notebook pages 
		 * in objects list and use __forward action of Notebooks controller
		 */
    function view() {
      $notebook_page_id = $this->request->get('notebook_page_id');
      if($notebook_page_id) {
        $this->active_notebook_page = NotebookPages::findById($notebook_page_id);
      } // if
    	
      if($this->active_notebook_page instanceof NotebookPage) {
        $this->wireframe->breadcrumbs->add('notebook_page', $this->active_notebook_page->getName(), $this->active_notebook_page->getViewUrl());
      } else {
        $this->active_notebook_page = new NotebookPage();
        $this->active_notebook_page->setProject($this->active_project);
      } // if
      
      $this->smarty->assign('active_notebook_page', $this->active_notebook_page);
    	
      if (!$this->active_notebook_page->isLoaded()) {
      	$this->response->notFound();
      } // if
      
      if (!$this->active_notebook->canView($this->logged_user)) {
				$this->response->forbidden();
      } // if
      
	  $this->wireframe->setPageObject($this->active_notebook_page, $this->logged_user);
      $this->wireframe->print->enable();  
      
      // API call
      if($this->request->isApiCall()) {
      	$this->response->respondWithData($this->active_notebook_page, array(
        	'as' => 'notebook_page', 
          'detailed' => true, 
        ));
            
      	// Request made by phone
	  } elseif($this->request->isPhone()) {
        if($this->active_notebook_page->getState() == STATE_ARCHIVED) {
        	$this->wireframe->actions->remove(array('new_notebook_page'));
        } // if
        
        $this->wireframe->actions->remove(array('favorites_toggler'));
        
        $this->response->assign('subpages', $this->active_notebook_page->getSubpages());
			
      } elseif($this->request->isPrintCall()) {
         
        $this->response->assign('subpages', $this->active_notebook_page->getSubpages());
            
			// Regular web browser request
      } else {
      	
        if($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
					if($this->active_notebook->canAddPage($this->logged_user)) {
	        	$this->wireframe->actions->add('new_notebook_page', lang('New Page'), Router::assemble('project_notebook_pages_add', array('project_slug' => $this->active_project->getSlug(), 'notebook_id' => $this->active_notebook->getId())), array(
	          	'onclick' => new FlyoutFormCallback('notebook_page_created'),
	            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),          			
	    	    ));
	      	} // if
	        		
	      	$this->active_notebook_page->accessLog()->log($this->logged_user);
	            
	        $this->smarty->assign(array(
	        	'parent' => $this->active_notebook_page->getParent(),
	          'subpages' => $this->active_notebook_page->getSubpages(),
	          'versions' => $this->active_notebook_page->getVersions(),
	        ));
        	$this->render();
        } else {
        	parent::view();
        	parent::render(get_view_path('view', 'notebooks', NOTEBOOKS_MODULE));
				} // if
      } // if
    } // view
    
    /**
     * Create a new notebook page
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_notebook->canAddPage($this->logged_user)) {
        	$this->wireframe->actions->remove(array('new_notebook_page'));
        	
          $notebook_page_data = $this->request->post('notebook_page', array(
          	'add_notebook_page_url' => Router::assemble('project_notebook_pages_add', array('project_slug' => $this->active_project->getSlug(), 'notebook_id' => $this->active_notebook->getId())),
          	'notebook_id' => $this->active_notebook->getId(),
          	'parent_id' => $this->request->get('parent_id'),
          ));
          $this->response->assign('notebook_page_data', $notebook_page_data);
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating a new notebook page @ ' . __CLASS__);
              
              $parent = null;
              
              $parent = isset($notebook_page_data['parent_id']) && $notebook_page_data['parent_id'] ? NotebookPages::findById($notebook_page_data['parent_id']) : null;
              
              if(empty($parent)) {
                $parent = $this->active_notebook;
              } // if
              
              $this->active_notebook_page = new NotebookPage();
              
              $this->active_notebook_page->attachments()->attachUploadedFiles($this->logged_user);
              
              $this->active_notebook_page->setAttributes($notebook_page_data);
              $this->active_notebook_page->setParent($parent);
              $this->active_notebook_page->setCreatedBy($this->logged_user);
              $this->active_notebook_page->setUpdatedOn(DateTimeValue::now());
              $this->active_notebook_page->setUpdatedBy($this->logged_user);
              $this->active_notebook_page->setState(STATE_VISIBLE);
              
              $this->active_notebook_page->save();
              
              $this->active_notebook_page->subscriptions()->set(array_unique(array_merge(
                (array) $this->logged_user->getId(),
                (array) $this->active_project->getLeaderId(),
                (array) $this->request->post('notify_users')
              )), false);
              
              DB::commit('Notebook page created @ ' . __CLASS__);

              AngieApplication::notifications()
                ->notifyAbout('notebooks/new_notebook_page', $this->active_notebook_page, $this->logged_user)
                ->setNotebook($this->active_notebook)
                ->sendToSubscribers();
              
              if($this->request->isAsyncCall() || $this->request->isApiCall()) {
                $this->response->respondWithData($this->active_notebook_page, array(
                  'as' => 'notebook_page', 
                  'detailed' => true, 
                ));
              } else {
                $this->response->redirectToUrl($this->active_notebook_page->getViewUrl());
              } // if
            } catch(Exception $e) {
              DB::rollback('Failed to create a new notebook page @ ' . __CLASS__);
              
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
     * Show and process edit notebook page form
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_notebook_page->isLoaded()) {
          if($this->active_notebook_page->canEdit($this->logged_user)) {
            $notebook_page_data = $this->request->post('notebook_page', array(
              'name' => $this->active_notebook_page->getName(),
              'body' => $this->active_notebook_page->getBody(),
              'notebook_id' => $this->active_notebook->getId(),
              'parent_id' => strtolower($this->active_notebook_page->getParentType()) == 'notebookpage' ? $this->active_notebook_page->getParentId() : null,
              'is_minor_revision' => false
              
            ));
            $this->smarty->assign('notebook_page_data', $notebook_page_data);
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating a notebook page @ ' . __CLASS__);
                
                $parent = isset($notebook_page_data['parent_id']) && $notebook_page_data['parent_id'] ? NotebookPages::findById($notebook_page_data['parent_id']) : null;
                
                if(empty($parent)) {
                  $parent = $this->active_notebook;
                } // if
                
                $old_page_name = $this->active_notebook_page->getName();
                $old_page_body = $this->active_notebook_page->getBody();
                
                $this->active_notebook_page->attachments()->attachUploadedFiles($this->logged_user);
                
                $this->active_notebook_page->setAttributes($notebook_page_data);
                $this->active_notebook_page->setParent($parent);
                
                $new_version = null;
                $error = null;
                
                if((!array_var($notebook_page_data, 'is_minor_revision', false) && ($this->active_notebook_page->getName() != $old_page_name || $this->active_notebook_page->getBody() != $old_page_body))) {
                  $new_version = $this->active_notebook_page->createVersion($this->logged_user);
                } // if
                
                // If we had new version, it also made some updates to the page internal flags, so save is AFTER version creation
                $this->active_notebook_page->save();
               
                $this->active_notebook_page->subscriptions()->set(array_unique(array_merge(
                (array) $this->logged_user->getId(),
                (array) $this->active_project->getLeaderId(),
                (array) $this->request->post('notify_users')
              )), true);
              
              
                if($new_version instanceof NotebookPageVersion) {
                  $this->active_notebook_page->subscriptions()->subscribe($this->logged_user); // Subscribe person who create a new version

                  AngieApplication::notifications()
                    ->notifyAbout('notebooks/new_notebook_page_version', $this->active_notebook_page, $this->logged_user)
                    ->setNotebook($this->active_notebook)
                    ->setVersion($new_version)
                    ->sendToSubscribers();
                } // if
                
                DB::commit('Notebook page updated @ ' . __CLASS__);
                
                if($this->request->isAsyncCall() || $this->request->isApiCall()) {
                  $this->response->respondWithData($this->active_notebook_page, array(
                    'as' => 'notebook_page', 
                    'detailed' => true, 
                  ));
                } else {
                  $this->response->redirectToUrl($this->active_notebook_page->getViewUrl());
                } // if
              } catch(Exception $e) {
                DB::rollback('Failed to update notebook page @ ' . __CLASS__);
                
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
     * Revert to a specific version
     */
    function revert() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_notebook_page->isLoaded()) {
          if($this->active_notebook_page->canEdit($this->logged_user)) {
            try {
              $to_version = null;
            
              $to_version_num = (integer) $this->request->get('to');
              if($to_version_num) {
                $to_version = NotebookPageVersions::findByPageAndVersion($this->active_notebook_page, $to_version_num);
              } // if
              
              if($to_version instanceof NotebookPageVersion) {
                $this->active_notebook_page->revertToVersion($to_version, $this->logged_user);
                $this->response->respondWithData($this->active_notebook_page, array(
                  'as' => 'notebook_page', 
                  'detailed' => true, 
                ));
              } else {
                $this->response->notFound();
              } // if
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
    } // revert
    
    /**
     * Reorder notebook pages
     */
    function reorder() {
    	if (!$this->request->isAsyncCall()) {
    		$this->response->badRequest();
    	} // if
    	
    	if ($this->active_notebook->isNew()) {
    		$this->response->notFound();
    	} // if
    	
    	if (!$this->active_notebook->canEdit($this->logged_user)) {
    		$this->response->forbidden();
    	} // if
    	
			$reorder_data = $this->request->post('reorder');
    	
    	$this->smarty->assign(array(
    		'reorder_url' => Router::assemble('project_notebook_pages_reorder', array('project_slug' => $this->active_project->getSlug(), 'notebook_id' => $this->active_notebook->getId()))
    	));
    	
    	if ($this->request->isSubmitted()) {
				try {
					$available_notebook_ids = NotebookPages::getAllIdsByNotebook($this->active_notebook);
					
					if (is_foreachable($reorder_data)) {
						$position = 0;
						foreach ($reorder_data as $notebook_id => $parent_data) {
							DB::execute("UPDATE " . TABLE_PREFIX . "notebook_pages SET parent_id = ?, parent_type = ?, position = ? WHERE id = ? AND id IN (?)", $parent_data['parent_id'], $parent_data['parent_type'], $position, $notebook_id, $available_notebook_ids);
							$position ++;
						} // foreach
					} // if
					
    			$this->response->ok();
				} catch (Exception $e) {
					$this->response->exception($e);
				} // try
    	} // if
    } // reorder
    
    /**
     * Move page to another notebook
     */
    function move() {
    	if ($this->active_notebook_page->isNew()) {
    		$this->response->notFound();
    	} // if
    	
    	if (!$this->active_notebook_page->canEdit($this->logged_user)) {
    		$this->response->forbidden();
    	} // if
    	
    	if ($this->request->isSubmitted()) {
    		$destination_notebook = Notebooks::findbyId($this->request->post('notebook_id'));
    		
	    	if (!($destination_notebook instanceof Notebook)) {
	    		$this->response->notFound();
	    	} // if
	    	
	    	if ($destination_notebook->getProjectId() != $this->active_notebook_page->getNotebook()->getProjectId()) {
	    		$this->response->forbidden();
	    	} // if
	    	
	    	if (!$destination_notebook->canEdit($this->logged_user)) {
	    		$this->response->forbidden();
	    	} // if
	    	
	    	try {
	    		DB::beginWork('Moving a Notebook Page @ ' . __CLASS__);
	    		
	    		$this->active_notebook_page->moveToNotebook($destination_notebook, $this->logged_user);
	    		
	    		DB::commit('Notebook Page moved @ ' . __CLASS__);
	    		
	    		if($this->request->isPageCall()) {
            $this->response->redirectToUrl($this->active_notebook_page->getNotebook()->getViewUrl());
          } else {
            $this->response->respondWithData($this->active_notebook_page, array(
		    			'detailed' => true
		    		));
          } // if
	    	} catch (Exception $e) {
	    		DB::rollback('Failed to move a Notebook Page @ ' . __FILE__);
	    		
	    		if($this->request->isPageCall()) {
            $this->response->assign('errors', $e);
          } else {
            $this->response->exception($e);
          } // if
	    	} // if
    	} // if
    } // if
    
  }
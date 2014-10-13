<?php

  // Build on top of assets controller
  AngieApplication::useController('assets', FILES_MODULE);

  /**
   * Text documents controller
   *
   * @package activeCollab.modules.files
   * @subpackage controllers
   */
  class TextDocumentsController extends AssetsController {

    /**
     * Attachments delegate instance
     *
     * @var AttachmentsController
     */
    protected $attachments_delegate;

    /**
     * Construct TextDocuments controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'text_documents') {
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'project_assets_text_document');
        $this->comments_delegate = $this->__delegate('comments', COMMENTS_FRAMEWORK_INJECT_INTO, 'project_assets_text_document');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_assets_text_document');
				$this->reminders_delegate = $this->__delegate('reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'project_assets_text_document');
				$this->move_to_project_delegate = $this->__delegate('move_to_project', SYSTEM_MODULE, 'project_assets_text_document');
        $this->sharing_settings_delegate = $this->__delegate('sharing_settings', SYSTEM_MODULE, 'project_assets_text_document');
        $this->attachments_delegate = $this->__delegate('attachments', ATTACHMENTS_FRAMEWORK_INJECT_INTO, 'project_assets_text_document');

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'project_assets_text_document');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'project_assets_text_document');
	      } // if
      } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!$this->active_asset || $this->active_asset->isNew()) {
        $this->active_asset = new TextDocument();
        $this->active_asset->setProject($this->active_project);
        $this->response->assign('active_asset', $this->active_asset);
      } else if ($this->active_asset->isLoaded() && !($this->active_asset instanceof TextDocument)) {
        $this->response->notFound();
      } // if
      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_asset,
        ));
      } // if
      
      if($this->comments_delegate instanceof CommentsController) {
        $this->comments_delegate->__setProperties(array(
          'active_object' => &$this->active_asset, 
        ));
      } // if
        
      if($this->subscriptions_delegate instanceof SubscriptionsController) {
        $this->subscriptions_delegate->__setProperties(array(
          'active_object' => &$this->active_asset, 
        ));
      } // if
      
      if($this->reminders_delegate instanceof RemindersController) {
      	$this->reminders_delegate->__setProperties(array(
          'active_object' => &$this->active_asset,
        ));
      } // if

      if($this->sharing_settings_delegate instanceof SharingSettingsController) {
        $this->sharing_settings_delegate->__setProperties(array(
          'active_object' => &$this->active_asset,
        ));
      } // if

	    if ($this->access_logs_delegate instanceof AccessLogsController) {
		    $this->access_logs_delegate->__setProperties(array(
			    'active_object' => &$this->active_asset
		    ));
	    } // if

	    if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		    $this->history_of_changes_delegate->__setProperties(array(
			    'active_object' => &$this->active_asset
		    ));
	    } // if

      if($this->attachments_delegate instanceof AttachmentsController) {
        $this->attachments_delegate->__setProperties(array(
          'active_object' => &$this->active_asset,
        ));
      } // if

    } // __before
    
    /**
     * List all project text documents (API & phone requests only)
     */
    function index() {
    	// Phone call
    	if($this->request->isPhone()) {
    		if(ProjectAssets::canAdd($this->logged_user, $this->active_project)) {
      		$this->wireframe->actions->add('add_text_document', lang('New Text Document'), Router::assemble('project_assets_text_document_add', array('project_slug' => $this->active_project->getSlug())), array(
          	'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
          	'primary' => true
	        ));
    		} // if
        
    		$this->response->assign('text_documents', ProjectAssets::findByTypeAndProject($this->active_project, 'TextDocument', STATE_VISIBLE, $this->logged_user->getMinVisibility()));
    		
    	// Tablet device
    	} elseif($this->request->isTablet()) {
    		throw new NotImplementedError(__METHOD__);
    		
    	// API call
    	} elseif($this->request->isApiCall()) {
        $this->response->respondWithData(ProjectAssets::findByTypeAndProject($this->active_project, 'TextDocument', STATE_VISIBLE, $this->logged_user->getMinVisibility()), array(
          'as' => 'text_documents', 
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // index
    
    /**
     * forwards the action to the assets index action
     */
    function assets_list() {
    	parent::index();
    } // index_forward
    
    /**
     * Show archived text documents (mobile devices only)
     */
    function archive() {
      if($this->request->isMobileDevice()) {
        $this->response->assign('text_documents', ProjectAssets::findArchivedByTypeAndProject($this->active_project, 'TextDocument', STATE_ARCHIVED, $this->logged_user->getMinVisibility()));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
  	/**
  	 * View text document
  	 */
  	function view() {
  	  if($this->active_asset->isLoaded()) {
        if($this->active_asset->canView($this->logged_user)) {
          if($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_asset, array(
              'as' => 'text_document', 
              'detailed' => true, 
            ));
          } // if
          
          $this->wireframe->setPageObject($this->active_asset, $this->logged_user);
      
          // Phone request
          if($this->request->isPhone()) {
          	$this->wireframe->actions->remove(array('pin_unpin', 'favorites_toggler'));
          } elseif($this->request->isWebBrowser()) {
            if($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
            	$this->active_asset->accessLog()->log($this->logged_user);
              $this->render();
            } else {
              if ($this->active_asset->getState() == STATE_ARCHIVED) {
                parent::archive();
                parent::render(get_view_path('archive', 'assets', FILES_MODULE));
              } else {
                parent::index();
                parent::render(get_view_path('index', 'assets', FILES_MODULE));
              } // if
            } // if
          } // if
        } else {
        	$this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
  	} // view
  	    
    /**
     * Create new text document
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if(ProjectAssets::canAdd($this->logged_user, $this->active_project)) {
          $text_document_data = $this->request->post('text_document', array(
    	      'milestone_id' => $this->request->getId('milestone_id'), 
    	      'category_id' => $this->request->getId('category_id'), 
    	      'visibility' => $this->active_project->getDefaultVisibility()
          ));
          
          $this->response->assign(array(
            'text_document_data' => $text_document_data, 
            'add_text_document_url' => Router::assemble('project_assets_text_document_add', array('project_slug' => $this->active_project->getSlug())), 
          ));
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating new text document @ ' . __FILE__);
              
              $this->active_asset->setAttributes($text_document_data);
              $this->active_asset->setState(STATE_VISIBLE);
              
              $this->active_asset->save();
              
              DB::commit('New text document created @ ' . __FILE__);
              
              // set subscriptions
              $this->active_asset->subscriptions()->set(array_unique(array_merge(
                (array) $this->logged_user->getId(),
                (array) $this->active_project->getLeaderId(),
                (array) $this->request->post('notify_users', array())
              )), false);

              AngieApplication::notifications()
                ->notifyAbout('files/new_text_document', $this->active_asset, $this->logged_user)
                ->sendToSubscribers();
              
              if($this->request->isPageCall()) {
                $this->response->redirectToUrl($this->active_asset->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_asset, array(
                  'as' => 'text_document', 
                  'detailed' => true, 
                ));
              } // if
            } catch(Exception $e) {
              DB::rollback('Failed to create new text document @ ' . __FILE__);
            	
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
     * Update existing text document
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_asset->isLoaded()) {
          if($this->active_asset->canEdit($this->logged_user)) {
            $text_document_data = $this->request->post('text_document', array(
              'name' => $this->active_asset->getName(), 
              'body' => $this->active_asset->getBody(), 
              'category_id' => $this->active_asset->getCategoryId(), 
              'milestone_id' => $this->active_asset->getMilestoneId(),  
              'visibility' => $this->active_asset->getVisibility(),
              'create_new_version' => true,
            ));
            
            $this->response->assign(array(
              'text_document_data' => $text_document_data,
            ));
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating a text document @ ' . __FILE__);
      
                $create_new_version = array_var($text_document_data, 'create_new_version', true);
                
                // if we need to create new revision, we do it first
                if ($create_new_version && (array_var($text_document_data, 'name') != $this->active_asset->getName() || array_var($text_document_data, 'body') != $this->active_asset->getBody())) {
                  $name_updated = isset($text_document_data['name']) && $text_document_data['name'] && $text_document_data['name'] != $this->active_asset->getName();
                  $body_updated = !$name_updated && isset($text_document_data['body']) && $text_document_data['body'] && $text_document_data['body'] != HTML::cleanUpHtml($this->active_asset->getBody());
                  
                  if($name_updated || $body_updated) {
                    $old_version = $this->active_asset->versions()->create();
                  } else {
                    $create_new_version = false;
                  } // if
                } // if 
                          
               	$this->active_asset->setAttributes($text_document_data);
               	$this->active_asset->save();
                  
                $this->active_asset->subscriptions()->subscribe($this->logged_user);
                
                if($create_new_version && $old_version instanceof TextDocumentVersion) {
                  AngieApplication::notifications()
                    ->notifyAbout('files/new_text_document_version', $this->active_asset, $this->logged_user)
                    ->setVersion($old_version)
                    ->sendToSubscribers();
                } // if
                
                DB::commit('Text document updated @ ' . __FILE__);
                
                if($this->request->isPageCall()) {
                  $this->response->redirectToUrl($this->active_asset->getViewUrl());
                } else {
                  $this->response->respondWithData($this->active_asset, array(
                    'as' => 'text_document', 
                    'detailed' => true, 
                  ));
                } // if
              } catch(Exception $e) {
                DB::rollback('Failed to update text document @ ' . __FILE__);
                
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
     * Revert to a specific version
     */
    function revert() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_asset->isLoaded()) {
          if($this->active_asset->canEdit($this->logged_user)) {
            try {
              $to_version = null;

              $to_version_num = (integer) $this->request->get('to');
              if($to_version_num) {
                $to_version = $this->active_asset->versions()->getVersion($to_version_num);
              } // if

              if($to_version instanceof TextDocumentVersion) {
                $this->active_asset->versions()->revert($to_version);
                $this->response->respondWithData($this->active_asset, array(
                  'as' => 'text_document',
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
    } //revert

    /**
     * Compares 2 text document versions
     */
    function compare_versions() {

    } //compare_versions
    
  }
<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Documents controller
   *
   * @package activeCollab.modules.documents
   * @subpackage controllers
   */
  class DocumentsController extends BackendController {
    
    /**
     * Selected Document
     *
     * @var document
     */
    protected $active_document;
    
    /**
     * Categories delegate instance
     *
     * @var CategoriesController
     */
    protected $categories_delegate;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;

	  /**
	   * History of changes controller delegate
	   *
	   * @var HistoryOfChangesController
	   */
	  protected $history_of_changes_delegate;
    
    /**
     * Constructor
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      $this->categories_delegate = $this->__delegate('categories', CATEGORIES_FRAMEWORK_INJECT_INTO, 'document');
	    $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'document');
	    $this->subscriptions_delegate = $this->__delegate('subscriptions', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'document');

	    if(AngieApplication::isModuleLoaded('footprints')) {
		    $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'document');
	    } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(Documents::canUse($this->logged_user)) {
        $this->wireframe->tabs->clear();
        $this->wireframe->tabs->add('documents', lang('Documents'), Router::assemble('documents'), null, true);
        
        EventsManager::trigger('on_documents_tabs', array(&$this->wireframe->tabs, &$this->logged_user));
        
        $this->wireframe->breadcrumbs->add('documents', lang('Documents'), Router::assemble('documents'));
        $this->wireframe->setCurrentMenuItem('documents');
        
        $document_id = $this->request->getId('document_id');
        if($document_id) {
          $this->active_document = Documents::findById($document_id);
        } // if
        
        if($this->active_document instanceof Document) {
          if (!$this->active_document->isAccessible()) {
            $this->response->notFound();
          } // if

          if ($this->active_document->getState() == STATE_ARCHIVED) {
            $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('documents_archive'));
          } // if

          $this->wireframe->breadcrumbs->add('document', $this->active_document->getName(), $this->active_document->getViewUrl());
        } else {
          $this->active_document = new Document();
        } // if
        
        $this->response->assign('active_document', $this->active_document);
        
        if($this->categories_delegate instanceof CategoriesController) {
          $this->categories_delegate->__setProperties(array(
            'routing_context' => 'document',
            'category_class' => 'DocumentCategory',
            'active_object' => &$this->active_document
          ));
        } // if
        
        if($this->state_delegate instanceof StateController) {
          $this->state_delegate->__setProperties(array(
            'active_object' => &$this->active_document
          ));
        } // if

	      if($this->subscriptions_delegate instanceof SubscriptionsController) {
		      $this->subscriptions_delegate->__setProperties(array(
			      'active_object' => &$this->active_document,
		      ));
	      } // if

	      if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		      $this->history_of_changes_delegate->__setProperties(array(
			      'active_object' => &$this->active_document
		      ));
	      } // if
      } else {
        $this->response->forbidden();
      } // if
    } // __before
    
    /**
     * Index page action
     */
    function index() {
      
      // API call
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Documents::findAll(STATE_VISIBLE, $this->logged_user->getMinVisibility()), array(
          'as' => 'documents', 
        ));
        
      // Mass edit
      } elseif($this->request->isSubmitted()) {
        if (!$this->request->isAsyncCall()) {
          $this->response->badRequest();
        } // if

        $this->mass_edit(Documents::findByIds($this->request->post('selected_item_ids'), STATE_VISIBLE, $this->logged_user->getMinVisibility()));
        
      // Print interface
      } elseif($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        
        $page_title = lang('All documents');
     
        // find invoices
        $documents = Documents::findForPrint($group_by);
        
        // maps
        if ($group_by == 'category_id') {
          $map = Categories::getIdNameMap('','DocumentCategory');
          
          if(empty($map)) {
            $map = null;
          } // if
          
          $map[0] = lang('Unknown Category');
          $getter = 'getCategoryId';
          $page_title.= ' ' . lang('Grouped by Category'); 
          
        } else if ($group_by == 'first_letter') {
          $map = get_letter_map();
          $getter = 'getFirstLetter';
          $page_title.= ' ' . lang('Grouped by First Letter');
        } // if       
               
        $this->smarty->assignByRef('documents', $documents);
        $this->smarty->assignByRef('map', $map);
        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));
        
      // Regular web browser request
      } elseif($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();

        $can_add_documents = false;
        if (Documents::canManage($this->logged_user)) {
          $can_add_documents = true;
          $this->wireframe->actions->add('add_text_document', lang('New Text Document'), Router::assemble('documents_add_text'), array(
            'onclick' => new FlyoutFormCallback('document_created', array(
              'success_message' => lang('Text document has been created')
            )),
           'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
          ));
          
          $this->wireframe->actions->add('upload_document', lang('Upload File'), Router::assemble('documents_upload_file'), array(
            'onclick' => new FlyoutFormCallback('document_created', array(
              'success_message' => lang('File has been uploaded')
            )),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),            
          ));
        } // if
        
        $this->response->assign(array(
          'can_add_documents' => $can_add_documents,
          'can_manage_documents' => Documents::canManage($this->logged_user),
          'documents' => Documents::findForObjectsList($this->logged_user, STATE_VISIBLE),
          'letters' => get_letter_map(),
          'categories' => Categories::getIdNameMap(null, 'DocumentCategory'),
          'manage_categories_url' => $can_add_documents ? Router::assemble('document_categories') : null,
          'in_archive' => false
        ));
        
        // mass manager
        if ($this->logged_user->isAdministrator()) {
          $mass_manager = new MassManager($this->logged_user, $this->active_document);          
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
      } // if
    } // index

    /**
     * Documents archive
     */
    function archive() {

      // API call
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Documents::findAll(STATE_ARCHIVED, $this->logged_user->getMinVisibility()), array(
          'as' => 'documents',
        ));

        // Mass edit
      } elseif($this->request->isSubmitted()) {
        if (!$this->request->isAsyncCall()) {
          $this->response->badRequest();
        } // if

        $this->mass_edit(Documents::findByIds($this->request->post('selected_item_ids'), STATE_ARCHIVED, $this->logged_user->getMinVisibility()));

        // Print interface
      } elseif($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));

        $page_title = lang('All documents');

        // find invoices
        $documents = Documents::findForPrint($group_by);

        // maps
        if ($group_by == 'category_id') {
          $map = Categories::getIdNameMap('','DocumentCategory');

          if(empty($map)) {
            $map = null;
          } // if

          $map[0] = lang('Unknown Category');
          $getter = 'getCategoryId';
          $page_title.= ' ' . lang('Grouped by Category');

        } else if ($group_by == 'first_letter') {
          $map = get_letter_map();
          $getter = 'getFirstLetter';
          $page_title.= ' ' . lang('Grouped by First Letter');
        } // if

        $this->smarty->assignByRef('documents', $documents);
        $this->smarty->assignByRef('map', $map);
        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));

        // Regular web browser request
      } elseif($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();

        $can_add_documents = false;
        if (Documents::canManage($this->logged_user)) {
          $can_add_documents = true;
          $this->wireframe->actions->add('add_text_document', lang('New Text Document'), Router::assemble('documents_add_text'), array(
            'onclick' => new FlyoutFormCallback('document_created', array(
              'success_message' => lang('Text document has been created')
            )),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
          ));

          $this->wireframe->actions->add('upload_document', lang('Upload File'), Router::assemble('documents_upload_file'), array(
            'onclick' => new FlyoutFormCallback('document_created', array(
              'success_message' => lang('File has been uploaded')
            )),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
          ));
        } // if

        $this->response->assign(array(
          'can_add_documents' => $can_add_documents,
          'can_manage_documents' => Documents::canManage($this->logged_user),
          'documents' => Documents::findForObjectsList($this->logged_user, STATE_ARCHIVED),
          'letters' => get_letter_map(),
          'categories' => Categories::getIdNameMap(null, 'DocumentCategory'),
          'manage_categories_url' => $can_add_documents ? Router::assemble('document_categories') : null,
          'in_archive' => true
        ));

        // mass manager
        if ($this->logged_user->isAdministrator()) {
          $mass_manager = new MassManager($this->logged_user, $this->active_document);
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
      } // if
    } // archive
    
    /**
     * Mass edit
     */
    function mass_edit() {
      if ($this->getControllerName() == 'documents') {
        $this->mass_edit_objects = Documents::findByIds($this->request->post('selected_item_ids'), STATE_ARCHIVED, $this->logged_user->getMinVisibility());
      } // if
      
      parent::mass_edit();
    } // mass_edit
    
    /**
     * View document page action
     */
    function view() {
      if($this->active_document->isLoaded()) {
        if ($this->active_document->canView($this->logged_user)) {

          // Single or quick view
          if($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
            $this->wireframe->print->enable();

            if (Documents::canManage($this->logged_user)) {
              $this->wireframe->actions->add('add_text_document', lang('New Text Document'), Router::assemble('documents_add_text'), array(
                'onclick' => new FlyoutFormCallback('document_created', array(
                  'success_message' => lang('Text document has been created')
                )),
                'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
              ));

              $this->wireframe->actions->add('upload_document', lang('Upload File'), Router::assemble('documents_upload_file'), array(
                'onclick' => new FlyoutFormCallback('document_created', array(
                  'success_message' => lang('File has been uploaded')
                )),
                'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
              ));
            } // if

            $this->wireframe->setPageObject($this->active_document, $this->logged_user);
            
          // Mobile device
          } elseif($this->request->isMobileDevice()) {
            $this->wireframe->breadcrumbs->remove('documents');

          // Print
          } elseif($this->request->isPrintCall()) {
            // Just render view

          // Direct access
          } else {
            if ($this->active_document->getState() == STATE_ARCHIVED) {
              $this->__forward('archive', 'archive');
            } else {
              $this->__forward('index', 'index');
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
     * Download file action
     */
    function download() {
      if($this->active_document->isLoaded()) {
        if($this->active_document->canView($this->logged_user)) {
          if($this->active_document->getType() == 'file') {
            $this->response->respondWithFileDownload($this->active_document->getFilePath(), $this->active_document->getMimeType(), $this->active_document->getName(), $this->request->get('force'));
          } else {
            $this->response->badRequest();
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // download
    
    /**
     * Add text document page action
     */
    function add_text() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(Documents::canManage($this->logged_user)) {
          $document_data = $this->request->post('document', array(
            'category_id' => $this->request->get('category_id'), 
            'visibility' => VISIBILITY_NORMAL,
            'exclude_ids' => Documents::getUsersWithoutDocumentAccess()
          ));

          $this->response->assign(array(
            'document_data' => $document_data,
          ));
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating a new text document @ ' . __CLASS__);
              
              $this->active_document->setAttributes($document_data);
              $this->active_document->setType('text');
              $this->active_document->setState(STATE_VISIBLE);

              if(!$this->logged_user->canSeePrivate()) {
                $this->active_document->setVisibility(VISIBILITY_NORMAL);
              } // if
              
              $this->active_document->save();

              $this->active_document->subscriptions()->set(array_unique(array_merge(
                (array) $this->logged_user->getId(),
                (array) $this->request->post('notify_users')
              )), false);
        
              DB::commit('Text document created @ ' . __CLASS__);

              $notify_user_ids = $this->request->post('notify_users');

              if(is_array($notify_user_ids) && count($notify_user_ids)) {
                AngieApplication::notifications()
                  ->notifyAbout('documents/new_text_document_document', $this->active_document, $this->logged_user)
                  ->sendToUsers(Users::findByIds($notify_user_ids));
              } // if
              
              $this->response->respondWithData($this->active_document, array(
                'as' => 'document'
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to create text document @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
        } else {  
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add_text
    
    /**
     * Upload file document page action
     */
    function upload_file() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(Documents::canManage($this->logged_user)) {
          $file_data = $this->request->post('file', array(
            'category_id' => $this->request->get('category_id'), 
            'visibility' => VISIBILITY_NORMAL,
            'exclude_ids' => Documents::getUsersWithoutDocumentAccess()
          ));

          $this->response->assign(array(
            'file_data' => $file_data,
            'upload_url' => Router::assemble('documents_upload_file', array('async' => 1)),
          ));
          
          if($this->request->isSubmitted()) {
            try {
              if (FwDiskSpace::isUsageLimitReached() || !FwDiskSpace::has($_FILES['file']['tmp_name'])) {
                throw new Error(lang('Disk Quota Reached. Please consult your system administrator.'));
              } // if

              DB::beginWork('Uploading file @ ' . __CLASS__);
              
              $this->active_document->setAttributes($file_data);
              $this->active_document->setType('file');
              $this->active_document->setState(STATE_VISIBLE);
              $this->active_document->setBody(''); // Make sure that insert query does not break when MySQL is in strict mode
              
              if(isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                $destination_file = $this->active_document->download()->setContentFromUploadedFile($_FILES['file'], false);
              } // if

              if(!$this->logged_user->canSeePrivate()) {
                $this->active_document->setVisibility(VISIBILITY_NORMAL);
              } // if
              
              $this->active_document->save();

              $this->active_document->subscriptions()->set(array_unique(array_merge(
                (array) $this->logged_user->getId(),
                (array) $this->request->post('notify_users')
              )), false);
              
              DB::commit('File uploaded @ ' . __CLASS__);

              $notify_user_ids = $this->request->post('notify_users');

              if(is_array($notify_user_ids) && count($notify_user_ids)) {
                AngieApplication::notifications()
                  ->notifyAbout('documents/new_file_document', $this->active_document, $this->logged_user)
                  ->sendToUsers(Users::findByIds($notify_user_ids));
              } // if
              
              $this->response->respondWithData($this->active_document, array(
                'as' => 'document',
                'detailed' => true
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to upload file @ ' . __CLASS__);
              
              if(isset($destination_file)) {
                @unlink($destination_file);
              } // if
              
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // upload_file
    
    /**
     * Edit document page action
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_document->isLoaded()) {
          if($this->active_document->canEdit($this->logged_user)) {
            $document_data = $this->request->post('document', array(
              'name' => $this->active_document->getName(),
              'body' => $this->active_document->getBody(),
              'category_id' => $this->active_document->getCategoryId(),
              'visibility' => $this->active_document->getVisibility(),
            ));
            
            $this->response->assign(array(
              'document_data' => $document_data,
            ));
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating document @ ' . __CLASS__);
                
                $this->active_document->setAttributes($document_data);
                $this->active_document->save();
                
                DB::commit('Document updated @ ' . __CLASS__);
                
                $this->response->respondWithData($this->active_document, array(
                  'as' => 'document',
                  'detailed' => true
                ));
              } catch(Exception $e) {
                DB::rollback('Failed to update document @ ' . __CLASS__);
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
     * Pin document
     */
    function pin() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_document->isLoaded()) {
          if($this->active_document->canPinUnpin($this->logged_user)) {
            try {
              DB::beginWork('Pinning document @ ' . __CLASS__);
              
              $this->active_document->setIsPinned(1);
              $this->active_document->save();
              
              DB::commit('Document pinned @ ' . __CLASS__);
              
              $this->response->respondWithData($this->active_document, array(
                'as' => 'document',
                'detailed' => true
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to pin document @ ' . __CLASS__);
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
    } // pin
    
    /**
     * Unpin document
     */
    function unpin() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_document->isLoaded()) {
          if($this->active_document->canPinUnpin($this->logged_user)) {
            try {
              DB::beginWork('Unpinning document @ ' . __CLASS__);
              
              $this->active_document->setIsPinned(0);
              $this->active_document->save();
              
              DB::commit('Document unpinned @ ' . __CLASS__);
              
              $this->response->respondWithData($this->active_document, array(
                'as' => 'document',
                'detailed' => true
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to unpin document @ ' . __CLASS__);
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
    } // unpin
    
    /**
     * Delete document action
     */
    function delete() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_document->isLoaded()) {
          if($this->active_document->canDelete($this->logged_user)) {
            try {
              $this->active_document->delete();
              $this->response->respondWithData($this->active_document, array(
                'as' => 'document'
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
    
  }
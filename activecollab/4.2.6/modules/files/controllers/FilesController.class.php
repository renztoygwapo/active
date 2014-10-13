<?php

  // Build on top of assets controller
  AngieApplication::useController('assets', FILES_MODULE);

  /**
   * Project files controller
   *
   * @package activeCollab.modules.files
   * @subpackage controllers
   */
  class FilesController extends AssetsController {
  	
    /**
     * Construct Files controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'files') {
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'project_assets_file');
        $this->comments_delegate = $this->__delegate('comments', COMMENTS_FRAMEWORK_INJECT_INTO, 'project_assets_file');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_assets_file');
				$this->reminders_delegate = $this->__delegate('reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'project_assets_file');
				$this->move_to_project_delegate = $this->__delegate('move_to_project', SYSTEM_MODULE, 'project_assets_file');
        $this->sharing_settings_delegate = $this->__delegate('sharing_settings', SYSTEM_MODULE, 'project_assets_file');

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'project_assets_file');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'project_assets_file');
	      } // if
      } // if
  	} // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!($this->active_asset instanceof File)) {
        $this->active_asset = new File();
        $this->active_asset->setProject($this->active_project);
      } // if
      
      $this->smarty->assign('active_asset', $this->active_asset);

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
    } // __construct
    
    /**
     * List all project files (API & phone requests only)
     */
    function index() {
    	
    	// Phone call
    	if($this->request->isPhone()) {
        $can_add_files = ProjectAssets::canAdd($this->logged_user, $this->active_project);
    		if($can_add_files) {
      		$this->wireframe->actions->add('add_file', lang('New File'), "#", array(
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'primary' => true,
	        ));
    		} // if

        $this->response->assign(array(
          'files' => ProjectAssets::findByTypeAndProject($this->active_project, 'File', STATE_VISIBLE, $this->logged_user->getMinVisibility()),
          'can_add_files' => $can_add_files
        ));
    		
    	// Tablet device
    	} elseif($this->request->isTablet()) {
    		throw new NotImplementedError(__METHOD__);
    		
    	// API call
    	} elseif($this->request->isApiCall()) {
    	  $this->response->respondWithData(ProjectAssets::findByTypeAndProject($this->active_project, 'File', STATE_VISIBLE, $this->logged_user->getMinVisibility()), array('as' => 'files'));
      } else {
        $this->response->badRequest();
      } // if
    } // index
    
    /**
     * Show archived files (mobile devices only)
     */
    function archive() {
      if($this->request->isMobileDevice()) {
        $this->response->assign('files', ProjectAssets::findArchivedByTypeAndProject($this->active_project, 'File', STATE_ARCHIVED, $this->logged_user->getMinVisibility()));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Show file details
     */
    function view() {
    	
      if($this->active_asset->isLoaded()) {
        if($this->active_asset->canView($this->logged_user)) {
          $this->wireframe->setPageObject($this->active_asset, $this->logged_user);
          
          // API call
          if($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_asset, array(
              'as' => 'file', 
            	'detailed' => true, 
            ));
            
          // Phone request
          } elseif($this->request->isPhone()) {
            $this->wireframe->actions->remove(array('download', 'pin_unpin', 'favorites_toggler'));
          	
          // Web browser request
          } else if($this->request->isPrintCall()) {
            
          } else {
          	if($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
            	$this->active_asset->accessLog()->log($this->logged_user);
              $this->response->assign(array(
                'uploader_options' => array(
                  'runtimes'                  => FILE_UPLOADER_RUNTIMES,
                  'size_limit'                => get_max_upload_size(),
                  'flash_uploader_url'        => AngieApplication::getAssetUrl('plupload.flash.swf', FILE_UPLOADER_FRAMEWORK, 'flash'),
                  'silverlight_uploader_url'  => AngieApplication::getAssetUrl('plupload.silverlight.xap', FILE_UPLOADER_FRAMEWORK, 'silverlight'),
                )
              ));
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
     * Download file content
     */
    function download() {
      if($this->active_asset->isLoaded()) {
        if($this->active_asset->canView($this->logged_user)) {
          $this->active_asset->accessLog()->logDownload($this->logged_user);
          
          $disposition = $this->request->get('disposition');
          if($disposition == 'attachment') {
            $force = true;
          } else {
            $force = !($disposition == 'inline' || $this->active_asset->download()->isImage());
          } // if
          
          $this->active_asset->download()->send($force);
        } else {
        	$this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // download
    
    /**
     * Show and upload files form
     */
    function add() {
      if($this->request->isAsyncCall()) {
        if(ProjectAssets::canAdd($this->logged_user, $this->active_project)) {
          $file_data = $this->request->post('file', array(
            'milestone_id' => $this->request->get('milestone_id'),
     				'visibility' => $this->active_project->getDefaultVisibility(),
     			));
     			
     			if ($this->request->isSubmitted()) {
      			// associate files with attachments    			
      			try {
      				DB::beginWork('Upload of multiple files started @ ' . __CLASS__);
      				
      				$attachments = $this->request->post('attachments');
      				if (!is_foreachable($attachments)) {
      					throw new Exception(lang('No files uploaded'));
      				} // if
      				
      				$descriptions =$this->request->post('descriptions');
      				
      				$result = array();
      				foreach ($attachments as $index => $attachment_id) {
      					$attachment = Attachments::findById($attachment_id);
      					
      					if (!($attachment instanceof Attachment)) {
      						throw new Exception(lang('Could not find attachment with id :attachment_id', array('attachment_id' => $attachment_id)));
      					} // if
      					
      					$file = new File();
      					$file->setAttributes($file_data);
      					$file->setName($attachment->getName());
      					$file->setBody(array_var($descriptions, $index));
      					$file->setProject($this->active_project);
      					$file->setSize($attachment->getSize());
  						  $file->setLocation($attachment->getLocation());    					
      					$file->setMimeType($attachment->getMimeType());
      					$file->setState(STATE_VISIBLE);
      					$file->setVersionNum(1);
      					$file->save();
      					
      					$result[] = $file->describe($this->logged_user, true, true);

      				  $files[] = $file;
      				  
  		 					$file->subscriptions()->set(array_unique(array_merge(
  		            (array) $this->logged_user->getId(),
  		            (array) $this->active_project->getLeaderId(),
  		            (array) $this->request->post('notify_users')
  		          )));
  		          
  		          $subscribers = false;
  		          if(!is_array($subscribers)) {
        				  $subscribers = $file->subscriptions()->get();
    		        }//if
      				} // foreach
      				
      				
      				if(count($files) == 1) {
                AngieApplication::notifications()
                  ->notifyAbout('files/new_file', $file, $this->logged_user)
                  ->sendToSubscribers();
      				} else {
                AngieApplication::notifications()
                  ->notifyAbout('files/multiple_files_uploaded', null, $this->logged_user)
                  ->setFiles($files)
                  ->setProject($this->active_project)
                  ->sendToUsers($subscribers, true);
      				} // if
      				
      				DB::execute("DELETE FROM " . TABLE_PREFIX . "attachments WHERE id IN (?)", $attachments);
      				
      				DB::commit('Multiple file upload succeded @ ' . __CLASS__);
      				
      				$this->response->respondWithData($result, array(
      				  'as' => 'files', 
      				  'detailed' => true, 
      				));
      			} catch (Exception $e) {
      			  DB::rollback('Upload of multiple files failed @ ' . __CLASS__);
      				$this->response->exception($e);
      			} // try
     			} // if
        } else {
          $this->response->forbidden();
        } // if

        $uploader_options = array(
          'upload_url'          => Router::assemble('project_assets_file_upload_compatibility', array('project_slug' => $this->active_project->getSlug())),
          'delete_button_url'   => AngieApplication::getAssetUrl('icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK),
          'size_limit'          => get_max_upload_size()
        );

        $uploader_options = array_merge($uploader_options, array(
          'uploader_runtimes'           => FILE_UPLOADER_RUNTIMES,
          'flash_uploader_url'          => AngieApplication::getAssetUrl('plupload.flash.swf', FILE_UPLOADER_FRAMEWORK, 'flash'),
          'silverlight_uploader_url'    => AngieApplication::getAssetUrl('plupload.silverlight.xap', FILE_UPLOADER_FRAMEWORK, 'silverlight'),
          'upload_name'                 => 'file'
        ));

      	$this->smarty->assign(array(
          'file_data'           => $file_data,
      		'form_id'             => HTML::uniqueId('form'),
          'upload_url'          => Router::assemble('project_assets_files_add', array('project_slug' => $this->active_project->getSlug())),
          'uploader_options'    => $uploader_options
      	));    	 
      } else {
        $this->response->badRequest();
      } // if
    } // upload
    
    /**
     * Upload single file
     */
    function upload() {
      if(ProjectAssets::canAdd($this->logged_user, $this->active_project)) {
        if($this->request->isSubmitted()) {
          $file_data = $this->request->post('file');
    
          try {
            DB::beginWork('Saving file @ ' . __FILE__);

            if (FwDiskSpace::isUsageLimitReached() || !FwDiskSpace::has($_FILES['attachment']['tmp_name'])) {
              throw new Error(lang('Disk Quota Reached. Please consult your system administrator.'));
            } // if
            
            $this->active_asset->setAttributes($file_data);
            if($this->active_asset->getName() == '') {
              $this->active_asset->setName($file_data['name']);
            } // if
            
            $this->active_asset->setVersionNum(1);
            $this->active_asset->setState(STATE_VISIBLE);
            
            if(isset($_FILES['attachment']) && is_uploaded_file($_FILES['attachment']['tmp_name'])) {
              $destination_file = $this->active_asset->download()->setContentFromUploadedFile($_FILES['attachment']);
            } // if
            
            $this->active_asset->save();
            
   					$this->active_asset->subscriptions()->set(array_unique(array_merge(
              (array) $this->logged_user->getId(),
              (array) $this->active_project->getLeaderId(),
              (array) $this->request->post('notify_users')
            )));
            
            
            DB::commit('File saved @ ' . __CLASS__);

            AngieApplication::notifications()
              ->notifyAbout('files/new_file', $this->active_asset, $this->logged_user)
              ->sendToSubscribers();
                      
            if($this->request->isApiCall()) {
              $this->response->respondWithData($this->active_asset, array('as' => 'file'));
            } else {
              die('success'); // async
            } // if
            
          } catch(Exception $e) {
            DB::rollback('Failed to save file @ ' . __CLASS__);
            
            if(isset($destination_file)) {
              @unlink($destination_file);
            } // if
            
            $this->response->exception($e);
          } // try
        } else {
          if($this->request->isApiCall()) {
            $this->response->badRequest();
          } else {
            die('error - request is not POST request'); // async
          } // if
        } // if
      } else {
        $this->response->forbidden();
      } // if
    } // upload_single
    
    /**
     * Uploads the file in compatibility mode
     */
    function upload_compatibility() {
      $advanced_upload = $this->request->get('advanced_upload');

      if($this->request->isSubmitted()) {
        if(ProjectAssets::canAdd($this->logged_user, $this->active_project)) {
          $this->smarty->assign(array(
    				'form_id' => $this->request->get('form_id'),
    				'row_index' => $this->request->get('row_index')
    			));
        			
    			try {
    				DB::beginWork('Creating attachment');
        				
    				$uploaded_file = array_var($_FILES, 'file', null);
        				
    				if ($uploaded_file['error']) {
    					throw new Error(get_upload_error_message($uploaded_file['error']));
    				} // if
    				    				
    				if (!$uploaded_file) {
    					throw new Error(lang('File not uploaded correctly'));
    				} // if

            if (FwDiskSpace::isUsageLimitReached() || !FwDiskSpace::has($uploaded_file['size'])) {
              throw new Error(lang('Disk Quota Reached. Please consult your system administrator.'));
            } // if
    				    				
    				$new_name = AngieApplication::getAvailableUploadsFileName();
    				if (!move_uploaded_file($uploaded_file['tmp_name'], $new_name)) {
    					throw new Error(lang('Could not move uploaded file to uploads folder. Check folder permissions'));
    				} // if
    				
    				$attachment = new Attachment();
    				$attachment->setName(array_var($uploaded_file, 'name'));
    				$attachment->setSize(filesize($new_name));
    				$attachment->setLocation(basename($new_name));
    				$attachment->setMimeType(get_mime_type($new_name, $attachment->getName()));
    				$attachment->setCreatedBy($this->logged_user);
    				$attachment->setCreatedOn(new DateTimeValue());
    				$attachment->save();

            DB::commit('Attachment created');

            if ($advanced_upload) {
              $this->response->setContentType(BaseHttpResponse::PLAIN);
              echo JSON::encode($attachment, $this->logged_user, false, true);
              die();
            } else {
              $this->smarty->assign('attachment_id', $attachment->getId());
            } // if
    			} catch (Exception $e) {
    				DB::commit('Failed to create attachment');
    				    				
    				if ($new_name && is_file($new_name)) {
    					@unlink($new_name);
    				} // if

            if ($advanced_upload) {
              $this->response->respondWithData($e);
            } else {
              $this->smarty->assign('error_message', $e->getMessage());
            } // if
    			} // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // upload_compatibility
    
    /**
     * Preview the current file
     */
    function preview() {
    	$this->smarty->assign(array(
    		'render_large' => $this->request->get('large', false)
    	));
    } // preview
    
    /**
     * Edit existing file information
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_asset->isLoaded()) {
          if($this->active_asset->canEdit($this->logged_user)) {
            $file_data = $this->request->post('file', array(
              'name' => $this->active_asset->getName(),
              'body' => $this->active_asset->getBody(),
              'visibility' => $this->active_asset->getVisibility(),
              'category_id' => $this->active_asset->getCategoryId(),
              'milestone_id' => $this->active_asset->getMilestoneId(),
            ));
            
            $this->smarty->assign(array(
              'file_data' => $file_data,
            ));
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating file @ ' . __CLASS__);
              
                $this->active_asset->setAttributes($file_data);
                $this->active_asset->save();
                
                DB::commit('File updated @ ' . __CLASS__);
                
                if($this->request->isPageCall()) {
			            $this->response->redirectToUrl($this->active_asset->getViewUrl());
			          } else {
			            $this->response->respondWithData($this->active_asset, array(
			            	'as' => 'file', 
			              'detailed' => true, 
			            ));
			          } // if
              } catch(Exception $e) {
                DB::rollback('Failed to update file @ ' . __CLASS__);
                
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
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // edit
    
    /**
     * Refresh file details
     */
    function refresh_details() {
      if($this->request->isAsyncCall()) {
        if($this->active_asset->isLoaded()) {
          if($this->active_asset->canView($this->logged_user)) {
            AngieApplication::useHelper('filesize', GLOBALIZATION_FRAMEWORK, 'modifier');
            
            $this->response->respondWithData(array(
              'title' => $this->active_asset->getName() . ', ' . smarty_modifier_filesize($this->active_asset->getSize()), 
              'preview' => $this->active_asset->preview()->has() ? $this->active_asset->preview()->renderLarge() : false, 
            ), array('format' => BaseHttpResponse::JSON));
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // refresh_details
    
  }
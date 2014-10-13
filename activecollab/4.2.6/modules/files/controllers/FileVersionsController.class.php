<?php

  // Build on top of files controller
  AngieApplication::useController('files', FILES_MODULE);
  
  /**
   * File versions controller
   *
   * @package activeCollab.modules.files
   * @subpackage controllers
   */
  class FileVersionsController extends FilesController {
    
    /**
     * Selected file version
     *
     * @var FileVersion
     */
    protected $active_file_version;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_asset->isLoaded()) {
        if($this->active_asset->canView($this->logged_user)) {
          $version_num = (integer) $this->request->get('file_version_num');
          if($version_num) {
            $this->active_file_version = FileVersions::findByFileIdAndVersionNum($this->active_asset->getId(), $version_num);
          } // if
          
          if($this->active_file_version instanceof FileVersion) {
            if($this->active_asset->getId() != $this->active_file_version->getFileId()) {
              $this->response->notFound();
            } // if
          } else {
            $this->active_file_version = new FileVersion();
            $this->active_file_version->setFileId($this->active_asset->getId());
          } // if
          
          $this->response->assign('active_file_version', $this->active_file_version);
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // __construct
    
    /**
     * Download specific file verison
     */
    function view() {
      if($this->active_file_version->isLoaded()) {
        if($this->active_file_version->canView($this->logged_user)) {
          $disposition = $this->request->get('disposition');
          
          if($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_file_version, array(
              'as' => 'file_version', 
              'detailed' => true, 
            ));
          } else {
            $this->active_file_version->download()->send(true);
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view
    
    /**
     * Force download
     */
    function download() {
      if($this->active_file_version->isLoaded()) {
        if($this->active_file_version->canView($this->logged_user)) {
          $this->active_file_version->download()->send(true);
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // download
    
    /**
     * Create new file version
     */
    function add() {
      if($this->request->isSubmitted() && ($this->request->isApiCall() || $this->request->isAsyncCall())) {
        if($this->active_asset->canUploadNewVersions($this->logged_user)) {
          try {
      	    DB::beginWork('Adding new file version @ ' . __CLASS__);
        	    
      	    if (!isset($_FILES['new_file_version']) && !is_uploaded_file($_FILES['new_file_version']['tmp_name'])) {
    					throw new Exception(lang('File not uploaded'));
      	    } // if

            if (FwDiskSpace::isUsageLimitReached() || !FwDiskSpace::has($_FILES['new_file_version']['size'])) {
              throw new Error(lang('Disk Quota Reached. Please consult your system administrator.'));
            } // if
        	    
       	    $old_version = $this->active_asset->versions()->create();
       	    $this->active_asset->download()->setContentFromUploadedFile($_FILES['new_file_version']);

            // Subscriptions
            $this->active_asset->subscriptions()->subscribe($this->logged_user);
        	    
       	    DB::commit('New file version added @ ' . __CLASS__);

            AngieApplication::notifications()
              ->notifyAbout('files/new_file_version', $this->active_asset, $this->logged_user)
              ->setVersion($old_version)
              ->sendToSubscribers();
        	    
            if($this->request->isAsyncCall()) {
            	$result = $this->active_asset->describe($this->logged_user, true, true);
              $result['body'] = clean($result['body']);
            	echo JSON::encode($result, $this->logged_user);
            	die();
            } elseif($this->request->isApiCall()) {
              $this->response->respondWithData($this->active_asset, array(
                'as' => 'file', 
                'detailed' => true,  
              ));
            } // if
          } catch(Exception $e) {
            DB::rollback('Failed to add new file version @ ' . __CLASS__);
                	    
            if ($this->request->isAsyncCall()) {
            	die($e->getMessage());
            } else {
            	$this->response->exception($e);
            } // if
       	  } // try
        } else {
          if ($this->request->isAsyncCall()) {
     	      die('Forbidden');
     	    } else {
       	    $this->response->forbidden();
     	    } // if
        } // if
      } else {
        if ($this->request->isAsyncCall()) {
    	  	die('Bad request');
    	  } else {
    	  	$this->response->badRequest();
    	  } // if
      } // if
    } // add
    
    /**
     * Create new file version
     */
    function delete() {
      if($this->request->isSubmitted()) {
        if($this->active_file_version->isLoaded()) {
          if($this->active_file_version->canDelete($this->logged_user)) {
            try {
              $this->active_file_version->delete();
              $this->response->respondWithData($this->active_file_version, array(
                'as' => 'file_version', 
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
    
  }
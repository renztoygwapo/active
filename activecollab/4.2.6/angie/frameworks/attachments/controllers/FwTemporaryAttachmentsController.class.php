<?php

  // Build on top of application controller
  AngieApplication::useController('frontend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Temporal attachments controller implementation
   *
   * @package angie.frameworks.attachments
   * @subpackage controller
   */
  class FwTemporaryAttachmentsController extends FrontendController {
    
    /**
     * Active attachment instance
     *
     * @var Attachment
     */
    protected $active_attachment;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $attachment_id = $this->request->getId('attachment_id');
      if($attachment_id) {
        $this->active_attachment = Attachments::findById($attachment_id);
      } // if
      
      if(!($this->active_attachment instanceof Attachment)) {
        $this->active_attachment = new Attachment();
      } // if
      
      $this->response->assign('active_attachment', $this->active_attachment);
    } // __construct
    
    /**
     * Download temporal attachment
     */
    function view() {
      if($this->active_attachment->isLoaded()) {
        $this->active_attachment->send($this->request->get('disposition', 'attachment') == 'attachment', true);
      } else {
        $this->response->notFound();
      } // if
    } // view
    
    /**
     * Add temporary attachment
     */
    function add() {
    	$advanced_upload = $this->request->get('advanced_upload', false);
    	$editor_upload = $this->request->get('editor_upload', false);

      if (($this->request->isSubmitted() && $this->request->isAsyncCall()) || $editor_upload) {
        try {
          DB::beginWork('Saving temporary attachment @ ' . __CLASS__);
  
          $files_field_name = array_var($_GET, 'field_name', 'attachment');
          if (!isset($_FILES[$files_field_name]) || !is_array($_FILES[$files_field_name])) {
            throw new Error(lang('File is not correctly uploaded'));
          } // if
  
  				$upload = $_FILES[$files_field_name];
          if ($upload['error'] != UPLOAD_ERR_OK) {
          	throw new UploadError($upload['error']);
          } // if

          if (FwDiskSpace::isUsageLimitReached() || !FwDiskSpace::has($upload['size'])) {
            throw new Error(lang('Disk Quota Reached. Please consult your system administrator.'));
          } // if
            
          $destination_file = AngieApplication::getAvailableUploadsFileName();          
          if (!move_uploaded_file($upload['tmp_name'], $destination_file)) {
          	throw new Error(lang('Failed to write uploaded file to the :folder_name folder', array('folder_name' => dirname($destination_file))));
          } // if

          $file_size = filesize($destination_file);
            
          $this->active_attachment = new Attachment();
          $this->active_attachment->setName($upload['name']);
          $this->active_attachment->setSize($file_size);
          $this->active_attachment->setMimeType(get_mime_type($destination_file, $this->active_attachment->getName()));
          $this->active_attachment->setLocation(basename($destination_file));

          // set MD5 if file size is smaller than 10MB
          if ($file_size < (1024 * 1024 * 10)) {
            $this->active_attachment->setMd5(md5_file($destination_file));
          } // if

          $this->active_attachment->save();
            
          DB::commit('Temporary attachment saved @ ' . __CLASS__);
            
         	$this->response->setContentType(BaseHttpResponse::PLAIN);
          echo JSON::encode($this->active_attachment, $this->logged_user, false, true);
          die();
        } catch(Exception $e) {
          DB::rollback('Failed to save temporary attachment @ ' . __CLASS__);
          if ($advanced_upload) {
            $this->response->respondWithData($e);
          } else {
            $this->response->exception($e);
          } // if
        } // try
      } else {
      	$this->response->badRequest();
      } // if
    } // add
    
    /**
     * Update temp attachment
     */
    function edit() {
      $this->response->badRequest();
    } // edit
    
    /**
     * Delete existing temporal attachment
     */
    function delete() {
      $this->response->badRequest();
    } // delete
    
  }
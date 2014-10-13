<?php

  /**
   * Framework level avatar controller
   *
   * @package angie.frameworks.avatar
   * @subpackage controllers
   */
  abstract class FwAvatarController extends Controller {
    
    /**
     * Selected object
     *
     * @var IAvatar
     */
    protected $active_object;
    
    /**
     * Prepare controller
     */
    function __before() {
			$this->wireframe->breadcrumbs->add('avatar', lang('Avatar'), $this->active_object->avatar()->getViewUrl());
			$this->smarty->assign(array(
				'active_object' => $this->active_object
			));
    } // __before
    
  	/**
  	 * Avatar view action
  	 */
  	function avatar_view() {
  		$biggest_size = $this->active_object->avatar()->biggestSize();
			$avatar_exists = $this->active_object->avatar()->avatarExists($biggest_size);
			
			$this->smarty->assign(array(
        'gd_library_loaded' => extension_loaded('gd'),
				'widget_id'				  => 'fw_avatar_'.strtolower(get_class($this->active_object)).'_'.$this->active_object->getId().'_'.time(),
				'biggest_size'	  	=> $biggest_size,
				'avatar_exists'		  => $avatar_exists,
				'current_avatar'	  => $this->active_object->avatar()->getUrl($biggest_size),
				'default_avatar'	  => $this->active_object->avatar()->getDefaultUrl($biggest_size),
				'original_url'		  => $this->active_object->avatar()->getOriginalUrl(),
				'event_name'			  => $this->active_object->getUpdatedEventName()
			));
  	} // avatar_view
  	
  	/**
  	 * Upload the avatar
  	 */
  	function avatar_upload() {
  		if (!($this->request->isAsyncCall() && $this->request->isSubmitted())) {
  			$this->response->badRequest();
  		} // if
  		
  		if (!$this->active_object->avatar()->canUpload($this->logged_user)) {
  			$this->response->forbidden();
  		} // if
  		
  		$uploaded_file_path = array_var(array_var($_FILES, 'avatar'), 'tmp_name', null);
  		$uploaded_file_name = array_var(array_var($_FILES, 'avatar'), 'name', null);
  		
  		try {
	  		if (!$uploaded_file_path) {
	  			throw new Exception(lang('Image upload failed. Check PHP configuration'));	
	  		} // if
	  		
  			$temporary_file = move_uploaded_file_to_temp_directory($uploaded_file_path, $uploaded_file_name);	
  			if (!$temporary_file) {
  				throw new Exception(lang('Could not move uploaded image to temporary folder'));
  			} // if
  			
  			if (!folder_is_writable($this->active_object->avatar()->getAvatarsPath())) {
  				throw new Exception(lang('Destination folder is not writable'));
  			} // if
  			
  			$this->active_object->avatar()->set($temporary_file);

        // returning the result
        $this->response->setContentType(BaseHttpResponse::PLAIN);
        $describe = $this->active_object->describe($this->logged_user, true, true);

        // clean richtext fields
        $describe['body'] = clean($describe['body']);
        $describe['body_formatted'] = clean($describe['body_formatted']);
        $describe['overview'] = clean($describe['overview']);
        $describe['overview_formatted'] = clean($describe['overview_formatted']);

        echo JSON::encode($describe, $this->logged_user);
        die();

  		} catch (Exception $e) {
  			echo JSON::encode(array(
  				'error' => true,
  				'message' => $e->getMessage(),
  			));
  			die();
  		} // try/catch  		
  	} // avatar_upload
  	
  	/**
  	 * Crop avatar
  	 */
  	function avatar_edit() {
  		if (!($this->request->isAsyncCall() && $this->request->isSubmitted())) {
  			$this->response->badRequest();
  		} // if
  		
  		if (!$this->active_object->avatar()->canEdit($this->logged_user)) {
  			$this->response->forbidden();
  		} // if
  		
  		try {
  			$left_offset = (int) array_var($_POST, 'left_offset', 0);
  			$top_offset = (int) array_var($_POST, 'top_offset', 0);
  			
  			$this->active_object->avatar()->crop($left_offset, $top_offset);
  			
  			$this->response->respondWithData($this->active_object, array(
          'detailed' => true, 
        ));
  		} catch (Exception $e) {
  			$this->response->exception($e);
  		} // try / catch
  	} // avatar_edit
  	
  	/**
  	 * Rest avatar to default one, and remove current
  	 */
  	function avatar_remove() {
  		if ($this->request->isAsyncCall() && $this->request->isSubmitted()) {
  		  if ($this->active_object->avatar()->canEdit($this->logged_user)) {
  		    try {
      			$this->active_object->avatar()->remove();
		  			$this->response->respondWithData($this->active_object, array(
		          'detailed' => true, 
		        ));
      		} catch (Exception $e) {
      			$this->response->exception($e);
      		} // try
  		  } else {
    			$this->response->forbidden();
    		} // if
  		} else {
  			$this->response->badRequest();
  		} // if
  	} // avatar_remove
  	
  }
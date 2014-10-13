<?php

  /**
   * Shared object controller delegate
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class SharedObjectController extends Controller {
    
    /**
     * Shared object
     *
     * @var ISharing
     */
    protected $active_shared_object;
    
    /**
     * Execute before any action
     */
    function __before() {
      parent::__before();
      
      $sharing_context = $this->request->get('sharing_context');
      $sharing_code = $this->request->get('sharing_code');
      
      if($sharing_context && $sharing_code) {
        $this->active_shared_object = SharedObjectProfiles::findParentByContextAndCode($sharing_context, $sharing_code);
      } // if
      
      if($this->active_shared_object instanceof ISharing) {
        $this->wireframe->breadcrumbs->add('shared_object', $this->active_shared_object->getName(), $this->active_shared_object->getViewUrl());
      } else {
        $this->response->notFound();
      } // if
      
      $this->smarty->assign(array(
      	'active_shared_object' => $this->active_shared_object,
      	'sharing_code' => $sharing_code
      ));
    } // __before
  
    /**
     * Display shared object details
     */
    function view_shared_object() {
    	if ($this->active_shared_object->sharing()->isExpired() || ($this->active_shared_object instanceof IState && $this->active_shared_object->getState() <= STATE_TRASHED)) {
    		$this->response->notFound();
    	} // if

      // log access
      if ($this->active_shared_object instanceof IAccessLog) {
        if ($this->logged_user instanceof User) {
          $this->active_shared_object->accessLog()->log($this->logged_user);
        } else {
          $this->active_shared_object->accessLog()->logAnonymous();
        } // if
      } // if
    	
      if($this->active_shared_object->sharing()->supportsComments()) {
        $comment_data = $this->request->post('comment');
        $sharing_code = $this->request->get('sharing_code');
        $cookie_name = 'activecollab_public_task_' . $sharing_code; 
        $show_instructions = false;
        if (isset($_COOKIE[$cookie_name])) {
        	$show_instructions = true;
        } // if

        $this->smarty->assign(array(
        	'cookie_name' => $cookie_name,
        	'comment_data' => $comment_data,
        	'show_instructions' => $show_instructions
        ));

        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Posting a comment @ ' . __CLASS__);
            
            $errors = new ValidationErrors();
            
            if($this->logged_user instanceof User) {
              $by = $this->logged_user;
            } else {
              $by_name = trim(array_var($comment_data, 'created_by_name'));
              $by_email = trim(array_var($comment_data, 'created_by_email'));
              
              if(empty($by_name)) {
                $errors->addError(lang('Your name is required'), 'created_by_name');
              } // if
              
              if(empty($by_email)) {
                $errors->addError(lang('Please provide a valid email address'), 'created_by_email');
              } else {
                if(is_valid_email($by_email)) {
                  $by = Users::findByEmail($by_email, true);
                  
                  if(empty($by)) {
                    $by = new AnonymousUser($by_name, $by_email);
                  } // if
                } else {
                  $errors->addError(lang('Please provide a valid email address'), 'created_by_email');
                } // if
              } // if
            } // if

            $body = array_var($comment_data, 'body');
            //preserve formatting
            $body = nl2br_pre($body);
            if(trim(strip_tags($body)) == '') {
              $errors->addError(lang('Please insert comment text'), 'body');
            } // if
            
            if($errors->hasErrors()) {
              throw $errors;
            } // if

            $this->active_shared_object->comments()->submit($body, $by, array(
              'set_source' => Comment::SOURCE_SHARED_PAGE,
              'set_visibility' => VISIBILITY_PUBLIC,
              'reopen_if_completed' => $this->active_shared_object->sharing()->canReopenIfCompleted($by),
            	'comment_attributes' => $comment_data,
              'attach_uploaded_files' => $this->active_shared_object->sharing()->getSharingProfile()->getAdditionalProperty('attachments_enabled'),
            ));
            
            DB::commit('Comment posted @ ' . __CLASS__);
            
            $this->response->redirectToUrl($this->active_shared_object->sharing()->getUrl());
          } catch(Exception $e) {
            DB::rollback('Failed to post a comment @ ' . __CLASS__);
            $this->response->assign('errors', $e);
          } // try
          
        } // if
      } // if
    } // view_shared_object
    
    /**
     * Post a comment to a shared object
     */
    function comment_shared_object() {
      
    } // comment_shared_object
    
  }
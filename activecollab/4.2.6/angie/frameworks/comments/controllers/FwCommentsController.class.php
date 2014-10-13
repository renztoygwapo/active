<?php

  /**
   * Framework level comments controller delegate implemnetation
   *
   * @package angie.frameworks.comments
   * @subpackage comments
   */
  abstract class FwCommentsController extends Controller {
    
    /**
     * Active parent object
     *
     * @var IComments
     */
    protected $active_object;
    
    /**
     * Active comment
     * 
     * @var Comment
     */
    protected $active_comment;
    
    /**
     * API actions
     *
     * @var array
     */
    protected $api_actions = array('view', 'add', 'edit', 'delete');
    
    /**
     * State controller deleage
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Attachments controller delegate instance
     *
     * @var AttachmentsController
     */
    protected $attachments_delegate;
    
    /**
     * Construct new comments controller delegate
     *
     * @param mixed $parent
     * @param mixed $context
     */
    function __construct(Controller &$parent, $context = null) {
      parent::__construct($parent, $context);
      
      $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, "{$context}_comment");
      $this->attachments_delegate = $this->__delegate('attachments', ATTACHMENTS_FRAMEWORK_INJECT_INTO, "{$context}_comment");
    } // __construct
    
    /**
     * Execute before any action
     */
    function __before() {
      if($this->active_object instanceof IComments) {
        $breadcrumbs_context = get_class($this->active_object) . '_' . $this->active_object->getId();
        
        $this->wireframe->breadcrumbs->add("{$breadcrumbs_context}_comments", 'Comments', $this->active_object->comments()->getUrl());
        
        $comment_id = $this->request->getId('comment_id');
        if($comment_id) {
          $this->active_comment = Comments::findById($comment_id);
        } // if

        if($this->active_comment instanceof Comment) {
          if(!$this->active_comment->isParent($this->active_object)) {
            $this->response->notFound();
          } // if
          
          $this->wireframe->breadcrumbs->add("{$breadcrumbs_context}_comments_{$comment_id}", lang('Comment #:num', array('num' => $comment_id)), $this->active_comment->getViewUrl());
        } else {
          $this->active_comment = $this->active_object->comments()->newComment();
        } // if

        $this->smarty->assign('active_comment', $this->active_comment);

        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_comment, 
        ));
        
        $this->attachments_delegate->__setProperties(array(
          'active_object' => &$this->active_comment, 
        ));
      } else {
        $this->response->notFound();
      } // if
    } // __before
    
    /**
     * Show object comment
     */
    function comments() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData($this->active_object->comments()->get($this->logged_user), array(
          'as' => 'comments',
          'detailed' => true
        ));
      } elseif($this->request->isAsyncCall()) {
        $timestamp = (integer) $this->request->get('timestamp');
        
        if(empty($timestamp)) {
          $reference = DateTimeValue::now();
        } else {
          $reference = new DateTimeValue($timestamp);
        } // if
        
        $loaded_comment_ids = $this->request->get('loaded_comment_ids');
        if($loaded_comment_ids) {
          $loaded_comment_ids = explode(',', $loaded_comment_ids);
        } // if
        
        $this->response->respondWithData($this->active_object->comments()->getMore($this->logged_user, $loaded_comment_ids, $reference), array(
          'as' => 'comments', 
          'detailed' => true, 
        ));
      } else {
        if($this->request->isMobileDevice()) {
          $this->response->redirectToUrl($this->active_object->getViewUrl());
        } else {
          $this->response->badRequest();
        } // if
      } // if
    } // comments
    
    /**
     * View single comment
     */
    function view_comment() {
      if($this->active_comment->isLoaded()) {
        if($this->active_comment->canView($this->logged_user)) {
          if($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_comment, array(
              'as' => 'comment',
              'detailed' => true,
            ));
          } else {
            if ($this->active_comment->isAccessible()) {
              if($this->request->isPhone() || $this->request->isWebBrowser()) {
                $this->response->redirectToUrl($this->active_comment->getParent()->getViewUrl());
              } else if ($this->request->isQuickViewCall()) {
                $this->response->redirectToUrl(extend_url($this->active_comment->getRealViewUrl(), array('quick_view' => $this->request->isQuickViewCall())));
                $this->response->redirectToUrl($this->active_comment->getRealViewUrl());
              } // if
            } else {
              $this->response->notFound();
            } // if
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view_comment
    
    /**
     * Create new comment
     */
    function add_comment() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall() || $this->request->isMobileDevice())) {
        $comment_data = $this->request->post('comment');

        try {        	
          $comment_body = isset($comment_data['body']) && $comment_data['body'] ? trim($comment_data['body']) : null;
          
          if($this->logged_user instanceof IUser) {
            $comment_by = $this->logged_user;
          } else {
            $errors = new ValidationErrors();
            
            $by_name = isset($comment_data['created_by_name']) && $comment_data['created_by_name'] ? trim($comment_data['created_by_name']) : null;
            $by_email = isset($comment_data['created_by_email']) && $comment_data['created_by_email'] ? trim($comment_data['created_by_email']) : null;

            if(empty($by_name)) {
              $errors->addError(lang('Your name is required'), 'created_by_name');
            } // if
            
            if($by_email) {
              if(!is_valid_email($by_email)) {
                $errors->addError(lang('Valid email address is required'), 'created_by_email');
              } // if
            } else {
              $errors->addError(lang('Your email address is required'), 'created_by_email');
            } // if
            
            if(empty($comment_body)) {
              $errors->addError(lang('Your comment is required'), 'body');
            } // if
            
            if($errors->hasErrors()) {
              throw $errors;
            } else {
              $comment_by = new AnonymousUser($by_name, $by_email);
            } // if
          } // if
          
          if(!$this->active_object->comments()->canComment($comment_by)) {
            $this->response->forbidden();
          } // if

          $parent_data = $this->request->post('parent');
                    
          $this->active_comment = $this->active_object->comments()->submit($comment_body, $comment_by, array(
            'set_source' => $this->request->isApiCall() ? Comment::SOURCE_API : Comment::SOURCE_WEB,
          	'update_parent' => $parent_data, 
          	'comment_attributes' => $comment_data 
          ));

          if($this->request->isPageCall()) {
          	$this->flash->success('Comment has been posted');
            $this->response->redirectToUrl($this->active_object->getViewUrl());
          } else {
          	$this->response->respondWithData($this->active_comment, array(
	            'as' => 'comment', 
          	  'detailed' => true, 
	          ));
          } // if
        } catch(Exception $e) {
          DB::rollback('Failed to submit new comment @ ' . __CLASS__);
          
          if($this->request->isPageCall()) {
            $this->response->assign('errors', $e);
            $this->response->redirectToUrl($this->active_object->getViewUrl());
          } else {
            $this->response->exception($e);
          } // if
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // add_comment
    
    /**
     * Update an existing comment
     */
    function edit_comment() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_comment->isLoaded()) {
          if($this->active_comment->canEdit($this->logged_user)) {
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating comment @ ' . __CLASS__);
                
                $comment_data = $this->request->post('comment');
                
                $this->active_comment->setAttributes($comment_data);
                $this->active_comment->attachments()->attachUploadedFiles($this->logged_user);
                $this->active_comment->save();
                
                DB::commit('Comment updated @ ' . __CLASS__);
                
                $this->response->respondWithData($this->active_comment, array(
                  'as' => 'comment', 
                	'detailed' => true,  
                ));
              } catch(Exception $e) {
                DB::rollback('Failed to update comment @ ' . __CLASS__);
                
                $this->response->exception($e);
              } // try
            } else {
              if($this->request->isAsyncCall()) {
                $view = $this->smarty->createTemplate(get_view_path('_object_comment_form_row', null, COMMENTS_FRAMEWORK));
                $view->assign(array(
                  'comment_parent' => $this->active_object, 
                  'comment' => $this->active_comment, 
                  'comments_id' => HTML::uniqueId('edit_comment'), 
                  'user' => $this->logged_user, 
                	'comment_data' => array('body' => $this->active_comment->getBody()), 
                ));
                
                $this->renderText($view->fetch());
              } else {
                $this->response->badRequest();
              } // if
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
    } // edit_comment
    
    /**
     * Lock object for comments
     */
    function comments_lock() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted() || $this->request->isMobileDevice()) {
        try {
          $this->active_object->comments()->lock($this->logged_user);
          
          if($this->request->isPageCall()) {
          	$this->flash->success('Comments have been successfully locked');
            $this->response->redirectToUrl($this->active_object->getViewUrl());
          } else {
          	$this->response->respondWithData($this->active_object, array(
	            'as' => $this->active_object->getBaseTypeName(), 
	            'detailed' => true, 
	          ));
          } // if
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // comments_lock
    
    /**
     * Unlock object for comments
     */
    function comments_unlock() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted() || $this->request->isMobileDevice()) {
        try {
          $this->active_object->comments()->unlock($this->logged_user);
          
          if($this->request->isPageCall()) {
          	$this->flash->success('Comments have been successfully unlocked');
            $this->response->redirectToUrl($this->active_object->getViewUrl());
          } else {
          	$this->response->respondWithData($this->active_object, array(
	            'as' => $this->active_object->getBaseTypeName(), 
	            'detailed' => true, 
	          ));
          } // if
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // comments_unlock
    
    /**
     * Drop active comment
     */
    function delete_comment() {
      if(($this->request->isApiCall() || $this->request->isAsyncCall()) && $this->request->isSubmitted()) {
        if($this->active_comment->isLoaded()) {
          if($this->active_comment->canDelete($this->logged_user)) {
            try {
              $this->active_comment->delete();
              
              $this->response->respondWithData($this->active_comment, array(
                'as' => 'comment', 
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
    } // delete_comment
    
  }
<?php

	// Extend public controller
  AngieApplication::useController('frontend', SYSTEM_MODULE);

	/**
	 * Project requests public controller
	 *
	 * @package activeCollab.modules.system
	 * @subpackage controllers
	 */
	class ProjectRequestsPublicController extends FrontendController {
		
		/**
		 * Selected project request
		 *
		 * @var ProjectRequest
		 */
		protected $active_project_request;
		
		/**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(ConfigOptions::getValue('project_requests_enabled')) {
        $project_request_public_id = $this->request->get('project_request_public_id');
  			if($project_request_public_id) {
  				$this->active_project_request = ProjectRequests::findByPublicId($project_request_public_id);
  			} // if
  			
  			if($this->active_project_request instanceof ProjectRequest) {
  			  $this->wireframe->breadcrumbs->add('project_request', $this->active_project_request->getName(), $this->active_project_request->getPublicUrl());
  			} else {
  				$this->active_project_request = new ProjectRequest();
  			} // if
      } else {
        $this->response->notFound();
      } // if

			$this->smarty->assign('active_project_request', $this->active_project_request);
		} // __constructor
		
		/**
		 * Request a project
		 */
		function index() {
		  $this->smarty->assign(ConfigOptions::getValue(array(
				'project_requests_page_title',
				'project_requests_page_description',
				'project_requests_custom_fields', 
			)));

			$captcha_enabled = ProjectRequests::isCaptchaEnabled();
		  
		  $project_request_data = $this->request->post('project_request', array(
        'created_by_name' => $this->logged_user ? $this->logged_user->getDisplayName() : Authentication::getVisitorName(),
        'created_by_email' => $this->logged_user ? $this->logged_user->getEmail() : Authentication::getVisitorEmail(),
        'created_by_company_name' => $this->logged_user ? $this->logged_user->getCompany()->getName() : '',
        'created_by_company_address' => $this->logged_user ? ConfigOptions::getValueFor('office_address', $this->logged_user->getCompany()) : ''
      ));
		  
			$this->smarty->assign(array(
			  'project_request_data' => $project_request_data, 
			  'project_requests_captcha_enabled' => $captcha_enabled, 
			));

			if($this->request->isSubmitted()) {
				try {
					$errors = new ValidationErrors();

	        if($captcha_enabled) {
	          if(!Captcha::Validate($project_request_data['captcha'])) {
	            $errors->addError(lang('Code you entered is not valid'), 'captcha');
	          } // if
	        } // if

	        if($errors->hasErrors()) {
          	throw $errors;
          } // if
          
          DB::beginWork('Submiting a project request @ ' . __CLASS__);

          if($project_request_data['body']) {
            $project_request_data['body'] = '<p>' . nl2br($project_request_data['body'], false) . '</p>';
          } // if

          if ($this->logged_user instanceof User) {
            $project_request_data['created_by_id'] = $this->logged_user->getId();
            $project_request_data['created_by_company_id'] = $this->logged_user->getCompanyId();
          } elseif (($user = Users::findByEmail($project_request_data['created_by_email'], true)) instanceof User) {
            $project_request_data['created_by_id'] = $user->getId();
            $project_request_data['created_by_company_id'] = $user->getCompanyId();
          } // if

					$this->active_project_request->setAttributes($project_request_data);

					$this->active_project_request->setPublicId(make_string(32, 'abcdefghijklmnopqrstuvwxyz1234567890'));
					$this->active_project_request->save();
					
					$this->active_project_request->subscriptions()->subscribe($this->active_project_request->getCreatedBy());
					
					DB::commit('Project request submitted @ ' . __CLASS__);
					
					$this->active_project_request->notifyRepresentatives();
					$this->active_project_request->notifyClient();
					
					if(!($this->logged_user instanceof User)) {
					  Authentication::setVisitorName($project_request_data['created_by_name']);
					  Authentication::setVisitorEmail($project_request_data['created_by_email']);
					} // if

					$this->response->redirectToUrl(extend_url($this->active_project_request->getPublicUrl(), array("submitted" => "1")));
				} catch(Exception $e) {
				  DB::rollback('Failed to submit project request @ ' . __CLASS__);
					
					unset($project_request_data['captcha']);
					$this->smarty->assign('errors', $e);
				} // try
			} // if
		} // index
		
		/**
		 * View requested project
		 */
		function view() {
			if($this->active_project_request->isLoaded()) {
			  if($this->active_project_request->getStatus() == ProjectRequest::STATUS_CLOSED) {
			    if(empty($this->logged_user) || !ProjectRequests::canManage($this->logged_user)) {
			      $this->response->notFound();
			    } else {
			      $this->smarty->assign('project_request_expired', true);
			    } // if
			  } // if

        if ($this->request->get("submitted")) {
          $this->smarty->assign("request_submitted", true);
        } // if
			  
			  $comment_data = $this->request->post('comment');
			  $this->smarty->assign('comment_data', $comment_data);
			  
			  if($this->request->isSubmitted()) {
			    try {
			      $comment_body = isset($comment_data['body']) && $comment_data['body'] ? nl2br(trim($comment_data['body'])) : null;
          
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
                $comment_by = Users::findByEmail($by_email, true);
                
                if(empty($comment_by)) {
                  $comment_by = new AnonymousUser($by_name, $by_email);
                } // if
              } // if
            } // if
            
            if(!$this->active_project_request->comments()->canComment($comment_by)) {
              $this->response->forbidden();
            } // if
            
            $this->active_comment = $this->active_project_request->comments()->submit($comment_body, $comment_by, array(
              'set_source' => Comment::SOURCE_WEB, 
              'set_visibility' => VISIBILITY_PUBLIC,
            	'comment_attributes' => $comment_data 
            ));
            
            if($comment_by instanceof AnonymousUser) {
              Authentication::setVisitorName($comment_by->getName());
              Authentication::setVisitorEmail($comment_by->getEmail());
            } // if
			      
            $this->flash->success('Thank you for the comment');
            $this->response->redirectToUrl($this->active_project_request->getPublicUrl());
			    } catch(Exception $e) {
			      $this->smarty->assign('errors', $e);
			    } // try
			  } // if
			} else {
				$this->response->notFound();
			} // if
		} // view
		
	}
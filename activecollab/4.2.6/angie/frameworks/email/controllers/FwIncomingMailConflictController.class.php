<?php

  // Build on top of mailing controller
  AngieApplication::useController('email_admin', EMAIL_FRAMEWORK_INJECT_INTO);
  
  /**
   * Incoming mail conflict controller
   *
   */
  class FwIncomingMailConflictController extends EmailAdminController {

    /**
     * Active incoming mail
     * 
     * @var IncomingMail
     */
    private $active_mail;
    
     /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->active_mail = IncomingMails::findById($this->request->getId('incoming_mail_id'));
      if (!$this->active_mail instanceof IncomingMail) {
        $this->active_mail = new IncomingMail();
      } else {
        $this->wireframe->breadcrumbs->add('incoming_mail_conflicts', $this->active_mail->getSubject(), $this->active_mail->getImportUrl());
      } // if
      
      $this->smarty->assign(array(
        'active_mail' => $this->active_mail,
      ));
      
    } // __construct
    
    /*
     * Index
     */
    function index() {
      if($this->request->isMobileDevice()) {
    		$this->wireframe->breadcrumbs->remove(array('admin', 'email_admin'));
    		
    	} else {
    		$conflicts_per_page = 50;
	    
	    	if($this->request->get('paged_list')) {
	    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
	    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
	    		
	    		$this->response->respondWithData(IncomingMails::getSlice($conflicts_per_page, $exclude, $timestamp));
	    	} else {
	    	 
	    		$this->smarty->assign(array(
	    		  'conflicts' => IncomingMails::getSlice($conflicts_per_page), 
	    		  'conflicts_per_page' => $conflicts_per_page, 
	    		  'total_conflicts' => IncomingMails::countConflicts(),
	    		));
	    		 
	    	} // if
    	} // if
    } //index
    
    /**
     * Delete incoming mail
     */
    function delete() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        if (!$this->active_mail instanceof IncomingMail) {
          $this->response->notFound();
        } // if
        
        if($this->request->isSubmitted()) {
          try {
            $mail = $this->active_mail;          
            $this->active_mail->delete();
            
            $log = new IncomingMessageDeleteActivityLog();
            $log->log($mail->getMailbox(), $mail);
            
            $this->response->respondWithData($this->active_mail, array(
              'as' => 'incoming_mail',
              'detailed' => true, 
            ));
          } catch(Error $e) {
            $this->response->exception($e);
          }//try
        }//if
      } else {
        $this->response->badRequest();
      }//if
    }//delete
    
    /**
     * Import incoming mail into system
     * 
     */
    function conflict() {
      
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        $actions = new NamedList();
        $unavailable_actions = array();
        
        EventsManager::trigger('on_incoming_mail_actions', array(&$actions, $this->logged_user, &$unavailable_actions));

        $comment_action = $actions->get('add_new_comment');
        $tpl_params = array(
          'force' => true
        );
        if($comment_action instanceof IncomingMailAction) {
          $comment_action->setParams($tpl_params);
          if($this->active_mail->canAddCommentAction()) {
            //add comment action
            $comment_action->setPreSelected(true);
            if($actions->exists('Add Task')) {
              $actions->get('Add Task')->setPreSelected(false);
            }//if
          }//if
        } //if

        if(is_foreachable($actions)) {
          foreach ($actions as $action) {
            if($action->getTemplateName()) {
              $this->smarty->assign(array(
                'tpl_params' => $action->getParams()
              ));
              $action_forms[$action->getActionClassName()] = $this->smarty->fetch(get_view_path($action->getTemplateName(), $action->getTemplateFolder(), $action->getModuleName()));
            }//if
          }//foreach
        }//if
        
        $this->smarty->assign(array(
        	'action_forms' => $action_forms,
          'incoming_mail_actions' => $actions,
        	'unavailable_actions' => $unavailable_actions,
          'active_filter' => new IncomingMailFilter(),
          'to_url' => AngieApplication::getName() == 'activeCollab' ? Router::assemble('project_action_project_changed') : '',
        ));
        
        if($this->request->isSubmitted()) {
          $posted_data = $this->request->post('filter');
        
          //create action object
          $action_object = new $posted_data['action_name']();
         
          if(!($action_object instanceof IncomingMailAction)) {
            throw new InvalidInstanceError('action_object', $action_object, 'IncomingMailAction');
          }//if
         
          try {
            $created_object = $action_object->doActions($this->active_mail, $posted_data['action_parameters'], true);
           
            $succ_log = new IncomingMessageReceivedActivityLog();
            $succ_log->log($this->active_mail->getMailbox(), $action_object, $this->active_mail, null, $created_object);
            
            $this->active_mail->delete();
            
            $this->response->respondWithData($this->active_mail, array(
              'as' => 'incoming_mail',
              'detailed' => true, 
            ));
          } catch(Error $e) {
            $this->response->exception($e);
          }//try
        }//if
      } else {
        $this->response->badRequest();
      }//if
    }//conflict
    
    /**
     * Remove all conflicts
     * 
     */
    function remove_all_conflicts() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
       
        IncomingMails::deleteAll();
        
        $response = array(
          'ids' => 'all',
          'conflicts' => IncomingMails::countConflicts()
        );
        
        $this->response->respondWithData($response, array(
          'as' => 'incoming_mail_conflict_ids',
          'detailed' => true, 
        ));
      } else {
        $this->response->badRequest(); 
      }//if
    }//remove_all_conflicts
    
    /**
     * Remove selected conflicts
     * 
     */
    function remove_selected_conflicts() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $incoming_mail_conflict_ids = $this->request->post('incoming_mail_conflict_ids');
        if($incoming_mail_conflict_ids) {
          IncomingMails::deleteByIds($incoming_mail_conflict_ids);
        }//if
        
        $response = array(
          'ids' => $incoming_mail_conflict_ids,
          'conflicts' => IncomingMails::countConflicts()
        );
        
        $this->response->respondWithData($response, array(
          'as' => 'incoming_mail_conflict_ids',
          'detailed' => true, 
        ));
      } else {
        $this->response->badRequest(); 
      }//if
    }//remove_all_conflicts
    
    
    /**
     * Mass edit conflict resolution
     */
    function mass_conflict_resolution() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $incoming_mail_ids = $this->request->post('incoming_mail_ids');
        $action = $this->request->post('action');
        
        $incoming_mail_ids = explode(",",$incoming_mail_ids);
        
        if(is_foreachable($incoming_mail_ids) && $action) {
          switch ($action) {
            case 'delete':
              foreach ($incoming_mail_ids as $incoming_mail_id) {
                $incoming_mail = IncomingMails::findById($incoming_mail_id);
                if($incoming_mail instanceof IncomingMail) {
                  try {
                    $incoming_mail->delete();
                    $log = new IncomingMessageDeleteActivityLog();
                    $log->log($incoming_mail->getMailbox(), $incoming_mail);
                  } catch (Error $e) {
                    $this->response->exception($e);
                  } //try
                } //if
              }//foreach
              $this->response->ok();
              break;
          }//if
        }//if
        die();
      }//if
    }//mass_conflict_resolution
    
  } //FwIncomingMailConflictController
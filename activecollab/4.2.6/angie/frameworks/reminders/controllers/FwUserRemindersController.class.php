<?php

  // Build on top of users controller
  AngieApplication::useController('users', REMINDERS_FRAMEWORK_INJECT_INTO);

  /**
   * User reminders controller
   * 
   * @package angie.frameworks.reminders
   * @subpackage controllers
   */
  abstract class FwUserRemindersController extends Controller {
  
    /**
  	 * Parent object instance
  	 * 
  	 * @var User
  	 */
  	protected $active_object;
  
  	/**
  	 * Selected reminder instance
  	 * 
  	 * @var Reminder
  	 */
  	protected $active_reminder;
  	
  	/**
     * Prepare controller before action is being executed
     */
    function __before() {
    	if($this->active_object instanceof User && $this->active_object->isLoaded()) {
    	  if($this->request->isAsyncCall()) {
    	    $reminder_id = $this->request->getId('reminder_id');
          if($reminder_id) {
            $this->active_reminder = Reminders::findById($reminder_id);
          } // if
          
          if($this->active_reminder instanceof Reminder) {
            if(!$this->active_reminder->isParent($this->active_object)) {
              $this->response->operationFailed();
            } // if
          } // if
          
          $this->response->assign(array(
            'active_object' => $this->active_object, 
            'active_reminder' => $this->active_reminder, 
          ));
    	  } else {
    	    $this->response->badRequest();
    	  } // if
      } else {
        $this->response->notFound();
      } // if
    } // __before
    
    /**
     * List user reminders
     */
    function user_reminders() {
      $this->response->assign(array(
        'reminders' => Reminders::findActiveByUser($this->logged_user)
      ));
    } // user_reminders
    
  }
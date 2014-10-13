<?php

  /**
   * Framework level reminders controller implementation
   * 
   * @package angie.frameworks.reminders
   * @subpackage controllers
   */
  abstract class FwRemindersController extends Controller {
    
    /**
     * Parent object instance
     * 
     * @var IReminders
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
      if($this->active_object instanceof IReminders) {
        if($this->active_object->isNew()) {
          $this->response->notFound();
        } // if
        
        $reminder_id = $this->request->getId('reminder_id');
        if($reminder_id) {
          $this->active_reminder = Reminders::findById($reminder_id);
        } // if
        
        if($this->active_reminder instanceof Reminder) {
          if(!$this->active_reminder->isParent($this->active_object)) {
            $this->response->operationFailed();
          } // if
        } else {
          $this->active_reminder = $this->active_object->reminders()->newReminder();
        } // if
        
        $this->response->assign(array(
          'active_object' => $this->active_object, 
          'active_reminder' => $this->active_reminder, 
        ));
      } else {
        $this->response->notFound();
      } // if
    } // __before
    
    /**
     * List object reminders
     */
    function reminders() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData($this->active_object->reminders()->get(), array(
          'as' => 'reminders'
        ));
      } else {
        $reminders_per_load = 30;

        if($this->request->get('paged_list')) {
          $exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
          $timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;

          $this->response->respondWithData($this->active_object->reminders()->getSlice($reminders_per_load, $exclude, $timestamp));
        } else {
          $this->response->assign(array(
            'reminders' => $this->active_object->reminders()->getSlice($reminders_per_load),
            'reminders_per_load' => $reminders_per_load,
            'total_reminders' => $this->active_object->reminders()->count(),
          ));
        } // if
      } // if
    } // reminders
    
    /**
     * View reminder details
     */
    function view_reminder() {
      if($this->active_reminder->isNew()) {
        $this->response->notFound();
      } // if
      
      if(!$this->active_reminder->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if
    } // view_reminder
    
    /**
     * Create a new reminder
     */
    function add_reminder() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        $reminder_data = $this->request->post('reminder');
        if(!is_array($reminder_data)) {
          if($this->active_object instanceof IAssignees) {
            $send_to = Reminder::REMIND_ASSIGNEES;
          } elseif($this->active_object instanceof ISubscriptions) {
            $send_to = Reminder::REMIND_SUBSCRIBERS;
          } elseif($this->active_object instanceof IComments) {
            $send_to = Reminder::REMIND_COMMENTERS;
          } else {
            $send_to = Reminder::REMIND_SELF;
          } // if

          $send_on = new DateTimeValue();
          $send_on = $send_on->getForUser($this->logged_user);
          $send_on->advance(24*60*60);
          $send_on->setHour(9);
          $send_on->setMinute(0);

          $reminder_data = array(
            'send_to' => $send_to, 
            'send_on' => $send_on,
            'selected_users' => array()
          );
        } // if
        
        $this->response->assign('reminder_data', $reminder_data);
        
        if($this->request->isSubmitted()) {
          try {
            if($reminder_data['send_to'] != Reminder::REMIND_SELECTED) { 
              unset($reminder_data['selected_user_id']);
            } // if
            
            DB::beginWork('Creating a reminder @ ' . __CLASS__);
            
            $tmp = new DateTimeValue($reminder_data['send_on']);
            $reminder_data['send_on'] = $tmp->getForUserInGMT($this->logged_user);
        
            $this->active_reminder->setAttributes($reminder_data);
            $this->active_reminder->setParent($this->active_object);
            $this->active_reminder->setCreatedBy($this->logged_user);
            $this->active_reminder->save();
            
            DB::commit('Reminder created @ ' . __FILE__);

            if (AngieApplication::isOnDemand()) {
              OnDemand::runFrequentlyAfter($this->active_reminder->getSendOn());
            } // if
            
            $this->response->respondWithData($this->active_reminder, array(
              'as' => 'reminder', 
              'detailed' => true, 
            ));
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add_reminder
    
    /**
     * Nudge
     */
    function nudge_reminder() {
      if($this->request->isAsyncCall() || $this->request->isApiCall() || $this->request->isMobileDevice()) {
        $reminder_data = $this->request->post('reminder');
        if(!is_array($reminder_data)) {
          if($this->active_object instanceof IAssignees) {
            $send_to = Reminder::REMIND_ASSIGNEES;
          } elseif($this->active_object instanceof ISubscriptions) {
            $send_to = Reminder::REMIND_SUBSCRIBERS;
          } elseif($this->active_object instanceof IComments) {
            $send_to = Reminder::REMIND_COMMENTERS;
          } else {
            $send_to = Reminder::REMIND_SELF;
          } // if
          
          $reminder_data = array(
            'send_to' => $send_to,
            'selected_users' => array()
          );
        } // if
        
        $this->response->assign('reminder_data', $reminder_data);
        
        if($this->request->isSubmitted()) {
          try {
            if($reminder_data['send_to'] != Reminder::REMIND_SELECTED) {
              unset($reminder_data['selected_user_id']);
            } // if
            
            DB::beginWork('Creating a reminder @ ' . __CLASS__);
            
            $this->active_reminder->setAttributes($reminder_data);
            $this->active_reminder->setSendOn(DateTimeValue::now());
            $this->active_reminder->setParent($this->active_object);
            $this->active_reminder->setCreatedBy($this->logged_user);
            $this->active_reminder->save();
            
            $this->active_reminder->send(true);
            
            DB::commit('Reminder created @ ' . __FILE__);
            
            if($this->request->isPageCall()) {
              $this->flash->success('Reminder has been created');
              $this->response->redirectToUrl($this->active_object->getViewUrl());
            } else {
              $this->response->respondWithData($this->active_reminder, array(
                'as' => 'reminder', 
                'detailed' => true, 
              ));
            } // if
          } catch(Exception $e) {
            DB::rollback('Failed to create new reminder @ ' . __CLASS__);
            
            if($this->request->isPageCall()) {
              $this->smarty->assign('errors', $e);
            } else {
              $this->response->exception($e);
            } // if
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // nudge_reminder
    
    /**
     * Send reminder before it is automatically sent
     */
    function send_reminder() {
      if($this->request->isSubmitted() && ($this->request->isApiCall() || $this->request->isAsyncCall())) {
        if($this->active_reminder->isNew()) {
          $this->response->notFound();
        } // if
        
        if(!$this->active_reminder->canSend($this->logged_user)) {
          $this->response->forbidden();
        } // if
        
        try {
          $this->active_reminder->send();
          $this->response->respondWithData($this->active_reminder, array(
            'as' => 'reminder', 
            'detailed' => true, 
          ));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // send_reminder
    
    /**
     * Dismiss existing reminder
     */
    function dismiss_reminder() {
      if($this->request->isSubmitted() && ($this->request->isApiCall() || $this->request->isAsyncCall())) {
        if($this->active_reminder->isNew()) {
          $this->response->notFound();
        } // if

        if(!$this->active_reminder->canDismiss($this->logged_user, (boolean) $this->request->get('for_user'))) {
          $this->response->forbidden();
        } // if

        try {
          if ($this->request->get('for_user')) {
            $this->active_reminder->dismissForUser($this->logged_user);
          } else {
            $this->active_reminder->dismiss();
          } // if

          $this->response->respondWithData($this->active_reminder, array(
            'as' => 'reminder', 
            'detailed' => true, 
          ));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // dismiss_reminder
    
    /**
     * Dismiss existing reminder
     */
    function delete_reminder() {
      if($this->request->isSubmitted() && ($this->request->isApiCall() || $this->request->isAsyncCall())) {
        if($this->active_reminder->isNew()) {
          $this->response->notFound();
        } // if
        
        if(!$this->active_reminder->canDelete($this->logged_user)) {
          $this->response->forbidden();
        } // if
        
        try {
          $this->active_reminder->delete();
          $this->response->respondWithData($this->active_reminder, array(
            'as' => 'reminder', 
            'detailed' => true, 
          ));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // delete_reminder
    
  }
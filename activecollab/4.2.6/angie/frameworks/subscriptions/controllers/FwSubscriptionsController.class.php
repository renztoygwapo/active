<?php

  // Build on top of selected object controller
  AngieApplication::useController('selected_object', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Selected object subscriptions controller
   *
   * @package angie.framework.subscriptions
   * @subpackage controllers
   */
  abstract class FwSubscriptionsController extends Controller {
    
    /**
     * Selected object
     *
     * @var ISubscriptions
     */
    protected $active_object;
    
    /**
     * Selected user
     *
     * @var User
     */
    protected $active_user;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_object instanceof ISubscriptions && $this->active_object->isLoaded()) {
        $user_id = $this->request->get('user_id');
        if($user_id === null) {
          $this->active_user = $this->logged_user;
        } else {
          $user_id = (integer) $user_id;
          
          if($user_id) {
            $this->active_user = Users::findById($user_id);
          
            if(!($this->active_user instanceof User)) {
              $this->response->notFound(); // User not found!
            } // if
          } else {
            $this->active_user = new AnonymousUser($this->request->get('user_name'), $this->request->get('user_email'));
          } // if
        } // if
        
        $this->smarty->assign(array(
          'active_object' => $this->active_object, 
          'active_user' => $this->active_user, 
        ));
      } else {
        $this->response->notFound();
      } // if
    } // __before
    
    /**
     * Display object subscriptions
     */
    function manage_subscriptions() {
      if($this->request->isAsyncCall()) {
        if($this->active_object->subscriptions()->canView($this->logged_user)) {
          $available_user_ids = array();

          $available_users = $this->active_object->subscriptions()->getAvailableUsers($this->logged_user);

          if($available_users) {
            $grouped_subscribers = array();

            foreach($available_users as $user) {
              $group_name = $user->getGroupName();

              $available_user_ids[] = $user->getId();

              if(isset($grouped_subscribers[$group_name])) {
                $grouped_subscribers[$group_name][$user->getId()] = $user;
              } else {
                $grouped_subscribers[$group_name] = array($user->getId() => $user);
              } // if
            } // foreach
          } // if

          krsort($grouped_subscribers);

          // Add anonymous subscribers if they exist
          $rows = DB::execute('SELECT user_name, user_email FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_type = ? AND parent_id = ? AND user_id = 0', get_class($this->active_object), $this->active_object->getId());
          if(is_foreachable($rows)) {
            $other_subscribers_key = lang('Other Subscribers');
            
            $grouped_subscribers[$other_subscribers_key] = array();
            foreach($rows as $row) {
              $grouped_subscribers[$other_subscribers_key][] = new AnonymousUser($row['user_name'], $row['user_email']);
            } // foreach
          } // if
          
          // Done, lets render it
          $this->smarty->assign(array(
            'grouped_subscribers' => $grouped_subscribers,
            'can_be_managed' => $this->active_object instanceof IState ? $this->active_object->getState() == STATE_VISIBLE : true,
            'unsubscribe_all_link' => $this->active_object->subscriptions()->getUnsubscribeAllUrl()
          ));
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // manage_subscriptions
    
    /**
     * Subscribe selected user to selected object
     */
    function subscribe() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
	      if($this->active_object->subscriptions()->canSubscribe($this->logged_user)) {
          try {
            $this->active_object->subscriptions()->subscribe($this->active_user);

            $this->response->respondWithData($this->active_object, array(
              'as' => $this->active_object->getBaseTypeName(),
              'detailed' => true,
            ));
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
	        $this->response->forbidden();
	      } // if
      } else {
        $this->response->badRequest();
      } // if
    } // subscribe
    
    /**
     * Unsubscribe selected user from selected object
     */
    function unsubscribe() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
      	if($this->active_object->subscriptions()->canSubscribe($this->logged_user)) {
          try {
            $this->active_object->subscriptions()->unsubscribe($this->active_user);

            $this->response->respondWithData($this->active_object, array(
              'as' => $this->active_object->getBaseTypeName(),
              'detailed' => true,
            ));
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
	        $this->response->forbidden();
	      } // if
      } else {
        $this->response->badRequest();
      } // if
    } // unsubscribe

    /**
     * Unsubscribe all users from selected object
     */
    function unsubscribe_all() {
      if($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if($this->active_object->subscriptions()->canSubscribe($this->logged_user)) {
          try {

            $this->active_object->subscriptions()->unsubscribeAllUsers();
            $this->response->respondWithData($this->active_object, array(
              'as' => $this->active_object->getBaseTypeName(),
              'detailed' => true,
            ));
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // unsubscribe_all

  }
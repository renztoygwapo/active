<?php

  // Build on top backend controller
  AngieApplication::useController('backend', AUTHENTICATION_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level users controller implementation
   *
   * @package angie.frameworks.authentication
   * @subpackage controllers
   */
  abstract class FwUsersController extends BackendController {
    
    /**
     * Selected user account
     *
     * @var User
     */
    protected $active_user;

    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;

    /**
     * API client subscriptions delegate
     *
     * @var ApiClientSubscriptionsController
     */
    protected $api_client_subscriptions_delegate;

    /**
     * Avatar controller delegate
     *
     * @var UserAvatarController
     */
    protected $avatar_delegate;

    /**
     * Activity logs delegate
     *
     * @var ActivityLogsController
     */
    protected $activity_logs_delegate;

    /**
     * Home screen delegate
     *
     * @var HomescreenController
     */
    protected $homescreen_delegate;

    /**
     * Reminders delegate controller
     *
     * @var UserRemindersController
     */
    protected $reminders_delegate;

    /**
     * Construct users controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);

      if($this->getControllerName() == 'users') {
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'Auser');
        $this->api_client_subscriptions_delegate = $this->__delegate('api_client_subscriptions', AUTHENTICATION_FRAMEWORK_INJECT_INTO, 'user');
        $this->avatar_delegate = $this->__delegate('user_avatar', AVATAR_FRAMEWORK_INJECT_INTO, 'user');
        $this->homescreen_delegate = $this->__delegate('homescreen', HOMESCREENS_FRAMEWORK_INJECT_INTO, 'user');
        $this->activity_logs_delegate = $this->__delegate('activity_logs', ACTIVITY_LOGS_FRAMEWORK_INJECT_INTO, 'user');
        $this->reminders_delegate = $this->__delegate('user_reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'user');
      } // if
    } // __construct
    
    /**
     * Execute before any other action
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->tabs->clear();
      $this->wireframe->tabs->add('users', lang('Users'), Router::assemble('users'), null, true);
      
      EventsManager::trigger('on_users_tabs', array(&$this->wireframe->tabs, &$this->logged_user));
      
      $this->wireframe->setCurrentMenuItem('users');
      $this->wireframe->breadcrumbs->add('users', lang('Users'), Router::assemble('users'));
      
      $user_id = $this->request->getId('user_id');
      
      if($user_id) {
        $this->active_user = Users::findById($user_id);
      } // if
      
      if($this->active_user instanceof User) {
        $this->wireframe->breadcrumbs->add('user', $this->active_user->getDisplayName(true), $this->active_user->getViewUrl());
      } else {
        $this->active_user = Users::getUserInstance();
      } // if

      if($this->activity_logs_delegate instanceof ActivityLogsController) {
        $this->activity_logs_delegate->__setProperties(array(
          'show_activities_by' => &$this->active_user
        ));
      } // if
      
      $this->response->assign('active_user', $this->active_user);

      if($this->getControllerName() == 'users') {
        if($this->api_client_subscriptions_delegate instanceof ApiClientSubscriptionsController) {
          $this->api_client_subscriptions_delegate->__setProperties(array(
            'active_object' => &$this->active_user,
          ));
        } // if

        if($this->state_delegate instanceof StateController) {
          $this->state_delegate->__setProperties(array(
            'active_object' => &$this->active_user,
          ));
        } // if

        if($this->avatar_delegate instanceof UserAvatarController) {
          $this->avatar_delegate->__setProperties(array(
            'active_object' => &$this->active_user
          ));
        } // if

        if($this->homescreen_delegate instanceof HomescreenController) {
          $this->homescreen_delegate->__setProperties(array(
            'active_object' => &$this->active_user
          ));
        } // if

        if($this->activity_logs_delegate instanceof ActivityLogsController) {
          $this->activity_logs_delegate->__setProperties(array(
            'show_activities_by' => &$this->active_user
          ));
        } // if

        if($this->reminders_delegate instanceof UserRemindersController) {
          $this->reminders_delegate->__setProperties(array(
            'active_object' => &$this->active_user
          ));
        } // if
      } // if
    } // __before
    
    /**
     * Show users index page
     */
    function index() {
      $this->wireframe->list_mode->enable();
      
      if(Users::canAdd($this->logged_user)) {
        $this->wireframe->actions->add('new_user', lang('New User'), Router::assemble('users_add'), array(
          'onclick' => new FlyoutFormCallback('user_created'),
          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
          'primary' => true, 
        ));
      } // if
      
      $this->response->assign('users', Users::find());
    } // index
    
    /**
     * View an account of a single user
     */
    function view() {
      if($this->active_user->isLoaded()) {
        if($this->active_user->canView($this->logged_user)) {
          if($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_user, array(
              'as' => 'user', 
              'detailed' => true,
            ));
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view
    
    /**
     * Create a new user account
     */
    function add() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        if(Users::canAdd($this->logged_user)) {
          $user_data = $this->request->post('user');
          $this->response->assign('user_data', $user_data);
          
          if($this->request->isSubmitted()) {
            try {
              $password = array_var($user_data, 'password');
              $password_a = array_var($user_data, 'password_a');
              
              if(trim($password)) {
                if($password != $password_a) {
                  throw new ValidationErrors(array(
                    'passwords' => lang('Passwords do not match'), 
                  ));
                } // if
              } else {
                throw new ValidationErrors(array(
                  'password' => lang('Password is required')
                ));
              } // if
              
              $this->active_user->setAttributes($user_data);
              $this->active_user->save();
              
              $this->response->respondWithData($this->active_user, array(
                'as' => 'user', 
                'detailed' => true,
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add
    
    /**
     * Update existing user account
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canEdit($this->logged_user)) {
            $user_data = $this->request->post('user', array(
              'email' => $this->active_user->getEmail(), 
              'first_name' => $this->active_user->getFirstName(), 
              'last_name' => $this->active_user->getLastName(), 
            ));
            $this->response->assign('user_data', $user_data);
          
            if($this->request->isSubmitted()) {
              try {
                $this->active_user->setAttributes($user_data);
                $this->active_user->save();
                
                $this->response->respondWithData($this->active_user, array(
                  'as' => 'user', 
                  'detailed' => true,
                ));
              } catch(Exception $e) {
                $this->response->exception($e);
              } // try
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
    } // edit
    
    /**
     * Edit user password
     */
    function edit_password() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canChangePassword($this->logged_user)) {
            $user_data = $this->request->post('user');
            $this->response->assign('user_data', $user_data);
            
          	if($this->request->isSubmitted(true, $this->response)) {
          	  try {
          	    $errors = new ValidationErrors();
      	
              	$password = array_var($user_data, 'password');
              	$repeat_password = array_var($user_data, 'repeat_password');
              	
              	if(empty($password)) {
              		$errors->addError(lang('Password value is required'), 'password');
              	} // if
              	
              	if(empty($repeat_password)) {
              		$errors->addError(lang('Repeat Password value is required'), 'repeat_password');
              	} // if
              	
              	if(!$errors->hasErrors() && ($password !== $repeat_password)) {
              	  $errors->addError(lang('Inserted values does not match'));
              	} // if
              	
              	if($errors->hasErrors()) {
              	  throw $errors;
              	} // if
              	
              	$this->active_user->setPassword($user_data['password']);
            	  $this->active_user->save();
            	  
            	  if($this->request->isPageCall()) {
			            $this->response->redirectToUrl($this->active_user->getViewUrl());
			          } else {
			            $this->response->respondWithData($this->active_user, array(
			              'as' => 'user', 
			              'detailed' => true,
	    		      	));
			          } // if
          	  } catch(Exception $e) {
          	    AngieApplication::revertCsfrProtectionCode();
          	    $this->response->exception($e);
          	  } // try
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
    } // edit_password

    /**
     * Send welcome message
     */
    function send_welcome_message() {
      if($this->request->isAsyncCall()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canSendWelcomeMessage($this->logged_user)) {
            $welcome_message_data = $this->request->post('welcome_message', array(
              'message' => ConfigOptions::getValueFor('welcome_message', $this->active_user),
            ));
            $this->response->assign('welcome_message_data', $welcome_message_data);

            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Sending welcome message @ ' . __CLASS__);

                $welcome_message = trim(array_var($welcome_message_data, 'message'));
                if($welcome_message) {
                  ConfigOptions::setValueFor('welcome_message', $this->active_user, $welcome_message);
                } else {
                  ConfigOptions::removeValuesFor($this->active_user, 'welcome_message');
                } // if

                $password = Authentication::getPasswordPolicy()->generatePassword();
                $this->active_user->setPassword($password);
                $this->active_user->setInvitedOn(new DateTimeValue());

                $this->active_user->save();

                AngieApplication::notifications()
                  ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/welcome', $this->active_user, $this->logged_user)
                  ->setPassword($password)
                  ->setWelcomeMessage($welcome_message)
                  ->sendToUsers($this->active_user);

                DB::commit('Welcome message sent @ ' . __CLASS__);

                $this->response->respondWithData($this->active_user, array(
                  'as' => 'user',
                  'detailed' => true,
                ));
              } catch(Exception $e) {
                DB::rollback('Failed to send welcome message @ ' . __CLASS__);
                $this->response->exception($e);
              } // try
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
    } // send_welcome_message

    /**
     * Set user as invited
     */
    function set_as_invited() {
      if($this->request->isAsyncCall()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canSetAsInvited($this->logged_user)) {
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Setting user as invited @ ' . __CLASS__);

                $this->active_user->setInvitedOn(new DateTimeValue());
                $this->active_user->save();

                DB::commit('User set as invited @ ' . __CLASS__);

                $this->response->respondWithData($this->active_user, array(
                  'as' => 'user',
                  'detailed' => true,
                ));
              } catch(Exception $e) {
                DB::rollback('Failed to set user as invited @ ' . __CLASS__);
                $this->response->exception($e);
              } // try
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
    } // set_as_invited

    /**
     * Login as a selected user
     */
    function login_as() {
      if($this->active_user->isLoaded()) {
        if($this->active_user->canLoginAs($this->logged_user)) {
          if($this->request->isSubmitted()) {
            Authentication::getProvider()->logUserIn($this->active_user);

            // log login as
            $this->logged_user->securityLog()->log('login', $this->active_user);

            $this->response->respondWithData($this->active_user, array(
              'as' => 'user',
            ));
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // login_as

    /**
     * Export vCard
     */
    function export_vcard() {
      if($this->active_user->isLoaded()) {
        try{
          $this->active_user->toVCard();
          die();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->notFound();
      } // if
    } // export_vcard

    /**
     * Not found...
     */
    function delete() {
      $this->response->notFound();
    } // delete
    
//    /**
//     * Delete an account
//     */
//    function delete() {
//      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
//        if($this->active_user->isLoaded()) {
//          if($this->active_user->canDelete($this->logged_user)) {
//            try {
//              $this->active_user->delete();
//              $this->response->respondWithData($this->active_user, array(
//	              'as' => 'user',
//	              'detailed' => true,
//  		      	));
//            } catch(Exception $e) {
//              $this->response->exception($e);
//            } // try
//          } else {
//            $this->response->forbidden();
//          } // if
//        } else {
//          $this->response->notFound();
//        } // if
//      } else {
//        $this->response->badRequest();
//      } // if
//    } // delete
    
    
  }
<?php

  // Build on top of system module
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Authentication controller implementation
   *
   * @package angie.frameworks.authentication
   * @subpackage controllers
   */
  abstract class FwAuthenticationController extends BackendController {
    
    /**
     * Login is not required here...
     * 
     * @var boolean
     */
    protected $login_required = false;
    
    /**
     * Allow people to log in when system is in maintenance mode
     *
     * @var boolean
     */
    protected $restrict_access_in_maintenance_mode = false;
    
    /**
     * Execute before any other action
     */
    function __before() {
      parent::__before();
      
      if($this->request->isMobileDevice()) {
        $this->setLayout(array(
          'layout' =>'auth',
          'module' => ENVIRONMENT_FRAMEWORK
        ));
      } // if
    } // __before
    
    /**
     * Log user in
     */
    function login() {
      $prefered_intreface = AngieApplication::getPreferedInterface();
      
      $login_data = $this->request->post('login', array('interface' => $prefered_intreface));
      $interface = array_var($login_data, 'interface');

      $external_login = (boolean) $this->request->post('external_login');

      // ---------------------------------------------------
      //  Web browser interface
      // ---------------------------------------------------

      if($prefered_intreface == AngieApplication::INTERFACE_DEFAULT) {
        try {
          $user =& Authentication::getProvider()->authenticate(array(
            'email' => array_var($login_data, 'email'),
            'password' => array_var($login_data, 'password'),
            'remember' => (boolean) array_var($login_data, 'remember'),
            'interface' => array_var($login_data, 'interface'), 
          ));
          
          Globalization::setCurrentLocaleByUser($user); // We need to redefine locale settings to match logged user
                    
          $init_params = $this->wireframe->getInitParams($user);
          
          $redirect_to_url = array_var($login_data, 'redirect_to_url');
          if ($redirect_to_url && $external_login === false) {
            try {
              $request = Router::matchUrl($redirect_to_url);
              if ($request instanceof Request) {
                if (in_array($request->getMatchedRoute(), array('login', 'logout', 'forgot_password', 'reset_password'))) {
                  $redirect_to_url = '';  
                } // if
              }  // if
            } catch (Exception $e) {
              // noop
            } // try
          } // if
         
          // default redirect to route
          if (!$redirect_to_url) {
            $redirect_to_url = Router::assemble('homepage');
          } // if
         
          $init_params['redirect_to_url'] = $redirect_to_url;
          $init_params['interface'] = $interface;
                    
          if($user instanceof User) {
            if ($external_login === false) {
              $this->response->respondWithData($init_params, array(
                'format' => BaseHttpResponse::FORMAT_JSON,
              ));
            } else {
	            $this->response->redirectTo("homepage");
            } // if
          } else {
            $this->response->operationFailed();
          } // if
        } catch(Exception $e) {
          if ($external_login === false) {
            $this->response->exception($e);
          } else {
            $this->response->redirectToReferer(Router::assemble('login'));
          } // if
        } // try

      // ---------------------------------------------------
      //  Phone and Table
      // ---------------------------------------------------

      } elseif($prefered_intreface == AngieApplication::INTERFACE_PHONE || $prefered_intreface == AngieApplication::INTERFACE_TABLET) {
        $redirect_to = null; // Get page user wanted to visit based on GET params
        if($this->request->get('re_route')) {
          $params = array();
          foreach($this->request->getUrlParams() as $k => $v) {
            if(($k != 're_route') && str_starts_with($k, 're_')) {
              $params[substr($k, 3)] = $v;
            } // if
          } // if
          $redirect_to = Router::assemble($this->request->get('re_route'), $params);
        } else {
          $redirect_to = Router::assemble('homepage');
        } // if
        
        // If user is already logged in redirect him to page he wanted to visit
        if($this->logged_user instanceof User) {
          $this->response->redirectToUrl($redirect_to, true);
        } // if

        $this->smarty->assign('login_data', $login_data);
        
        if($this->request->isSubmitted()) {
          try {
            $errors = new ValidationErrors();
          
            $email = trim(array_var($login_data, 'email'));
            $password = array_var($login_data, 'password');
            
            if($email == '') {
              $errors->addError(lang('Email address is required'), 'email');
            } // if
            
            if(trim($password) == '') {
              $errors->addError(lang('Password is required'), 'password');
            } // if
            
            if($errors->hasErrors()) {
              throw $errors;
            } // if
            
            $user =& Authentication::getProvider()->authenticate(array(
              'email' => $email,
              'password' => $password,
              'remember' => (boolean) array_var($login_data, 'remember'),
              'interface' => array_var($login_data, 'interface'), 
            ));
            
            if($user instanceof User) {
              $this->response->redirectToUrl($redirect_to, true);
            } else {
              $errors->addError(lang('Failed to log you in with data you provided. Please try again'), 'login');
              throw $errors;
            } // if
          } catch(Exception $e) {
            $this->smarty->assign('errors', $e);
          } // try
        } // if

      // ---------------------------------------------------
      //  Other interfaces
      // ---------------------------------------------------

      } else {
        $this->response->badRequest();
      } // if
    } // login
    
    /**
     * Log user out
     */
    function logout() {
	    $force = (boolean) $this->request->get('force');

      // Async backend logout
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->logged_user instanceof User) {
          Authentication::getProvider()->logUserOut($force);
        } // if

        $on_logout_url = ConfigOptions::getValue('on_logout_url');
        $init_params = $this->wireframe->getInitParams();
        $init_params['on_logout_url'] = $on_logout_url;

        $this->response->respondWithData($init_params);

      // Device logout, with redirection
      } elseif($this->request->isPhone() || $this->request->isTablet()) {
        if($this->logged_user instanceof User) {
          Authentication::getProvider()->logUserOut($force);
        } // if

        $on_logout_url = ConfigOptions::getValue('on_logout_url');

        if(is_valid_url($on_logout_url)) {
          $this->response->redirectToUrl($on_logout_url);
        } else {
          $this->response->redirectTo('homepage');
        } // if

      // Not a browser or a phone?
      } else {
        $this->response->badRequest();
      } // if
    } // logout
    
    /**
     * Render and process forgot password form
     */
    function forgot_password() {
      $forgot_password_data = $this->request->post('forgot_password');
      $this->smarty->assign('forgot_password_data', $forgot_password_data);
      
      if($this->request->isSubmitted()) {
        try {
          $email = trim(array_var($forgot_password_data, 'email'));          
          if (empty($email)) {
            throw new Error(lang('Email address is required field'));
          } // if
          
          if (!is_valid_email($email)) {
            throw new Error(lang('Email address is not in valid format'));
          } // if
          
          $user = Users::findByEmail($email, true);
          if (!($user instanceof User) || !$user->isActive()) {
            throw new Error(lang('There is no user account that matches the e-mail address you entered'));
          } // if
          
          $user->setPasswordResetKey(make_string(13));
          $user->setPasswordResetOn(new DateTimeValue());
          $user->save();

          AngieApplication::notifications()
            ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/forgot_password', $user)
            ->sendToUsers($user, true);

          if ($this->request->isAsyncCall()) {
            $this->response->ok();
          } elseif($this->request->isPageCall()) {
            $this->response->redirectTo('login');
          } else {
            $this->smarty->assign(array(
              'success_message' => lang('We emailed reset password instructions at :email', array('email' => $user->getEmail())),
              'forgot_password_data' => null,
            ));
          } // if
        } catch (Exception $e) {
          if ($this->request->isAsyncCall()) {
            $this->response->exception($e);
          } else {
            $this->smarty->assign('errors', $e);
            $this->render();
          } // if
        } // try
      } // if
    } // forgot_password
    
    /**
     * Reset users password
     */
    function reset_password() {
      if($this->logged_user instanceof User) {
        $this->response->badRequest(array(
          'message' => lang('You are already logged in'),
        ));
      } // if

      $user_id = $this->request->getId('user_id');
      $code = trim($this->request->get('code'));
      
      if(empty($user_id) || empty($code)) {
        $this->response->operationFailed();
      } // if
      
      $user = null;
      if($user_id) {
        $user = Users::findById($user_id);
      } // if
      
      // Valid user and key
      if(!($user instanceof User) || !$user->isActive()) {
        $this->response->notFound();
      } // if
      
      if($user->getPasswordResetKey() != $code) {
        $this->response->notFound();
      } // if
      
      // Not expired
      $reset_on = $user->getPasswordResetOn();
      if($reset_on instanceof DateTimeValue) {
        if(($reset_on->getTimestamp() + 172800) < time()) {
          $this->response->notFound();
        } // if
      } else {
        $this->response->notFound();
      } // if
      
      $reset_data = $this->request->post('reset');
      $this->smarty->assign(array(
        'reset_data' => $reset_data,
        'user' => $user,
      ));
      
      if($this->request->isSubmitted()) {        
        try {
          $password = array_var($reset_data, 'password');
          $password_a = array_var($reset_data, 'password_a');
          
          $errors = new ValidationErrors();
          
          if(strlen_utf($password) < 3) {
            $errors->addError(lang('Minimum password length is 3 characters'), 'password');
          } // if
          
          if($password != $password_a) {
            $errors->addError(lang('Passwords do not match'), 'passwords');
          } // if

          if(AngieApplication::isOnDemand()) {
            //if is on demand and in suspended status, allow only account owner
            if(OnDemand::getAccountStatus()->getStatus() == OnDemand::STATUS_SUSPENDED_PAID || OnDemand::getAccountStatus()->getStatus() == OnDemand::STATUS_SUSPENDED_FREE) {
              if(!OnDemand::isAccountOwner($user)) {
                AngieApplication::notifications()
                  ->notifyAbout('on_demand/login_attempt')
                  ->setAttemptedBy($user)
                  ->sendToUsers(OnDemand::getAccountOwner());

                $errors->addError(AuthenticationError::ACCOUNT_SUSPENDED, 'password');
              } //if
            } //if

            if(OnDemand::getAccountStatus()->getStatus() == OnDemand::STATUS_PENDING_DELETION) {
              $errors->addError(AuthenticationError::ACCOUNT_PENDING_FOR_DELETION, 'password');
            } //if
          } //if

          if($errors->hasErrors()) {
            throw $errors;
          } // if
          
          $user->setPassword($password);
          $user->setPasswordResetKey(null);
          $user->setPasswordResetOn(null);
          $user->setLastActivityOn(date(DATETIME_MYSQL));
          $user->setLastLoginOn(date(DATETIME_MYSQL));
          $user->save();

          // log user in
          Authentication::getProvider()->logUserIn($user, array('interface' => AngieApplication::getPreferedInterface()));
          
          if($this->request->isAsyncCall()) {
            $this->response->respondWithData($this->wireframe->getInitParams($user));
          } else {
            $this->flash->success('Welcome back :name', array('name' => $user->getDisplayName()));
            $this->response->redirectTo('homepage');
          } // if
        } catch (Exception $e) {
          if ($this->request->isAsyncCall()) {
            $this->response->exception($e);
          } else {
            $this->smarty->assign('errors', $e);
          } // if
        } // try
      } // if
    } // reset_password
    
  }
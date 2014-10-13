<?php

  /**
   * Authentication manager
   *
   * @package angie.library.authentication
   */
  final class Authentication {
    
    /**
     * Selected provider
     *
     * @var AuthenticationProvider
     */
    private static $provider;
    
    /**
     * Return loaded authentication provider
     *
     * @return AuthenticationProvider
     */
    static function getProvider() {
      return self::$provider;
    } // getProvider
    
    /**
     * Include authentication provider
     *
     * @param string $provider_class
     * @param boolean $initialize
     * @param mixed $init_params
     * @throws InvalidParamError
     */
    static function useProvider($provider_class, $initialize = false, $init_params = null) {
      $custom_path = CUSTOM_PATH . "/auth_providers/$provider_class.class.php";
      if(is_file($custom_path)) {
        require_once $custom_path;
      } else {
        require_once AUTHENTICATION_FRAMEWORK_PATH . "/models/providers/$provider_class.class.php";
      } // if
      
      self::$provider = new $provider_class();
      if(self::$provider instanceof AuthenticationProvider) {
        if($initialize) {
          self::$provider->initialize($init_params);
        } // if
      } else {
        self::$provider = null;
        throw new InvalidParamError('provider_class', $provider_class, "'$provider_class' is not a valid authentication provider");
      } // if
    } // useProvider

    /**
     * Password policy instance
     *
     * @var PasswordPolicy
     */
    static private $password_policy = false;

    /**
     * Return password policy instance
     *
     * @return PasswordPolicy
     */
    static function getPasswordPolicy() {
      if(self::$password_policy === false) {
        if(AngieApplication::isModuleLoaded('password_policy')) {
          self::$password_policy = new ConfigurablePasswordPolicy();
        } else {
          self::$password_policy = new PasswordPolicy();
        } // if
      } // if

      return self::$password_policy;
    } // getPasswordPolicy

    /**
     * Saved API subscription
     *
     * @var ApiClientSubscription
     */
    static private $api_subscription;

    /**
     * Return used API subscription
     *
     * @return ApiClientSubscription
     */
    static function getApiSubscription() {
      return self::$api_subscription;
    } // getApiSubscription

    /**
     * Remember API subscription instance that's used to authenticate the user
     *
     * @param ApiClientSubscription $subscription
     */
    static function setApiSubscription(ApiClientSubscription $subscription) {
      self::$api_subscription = $subscription;
    } // setApiSubscription
    
    /**
     * Logged user instance
     *
     * @var IUser
     */
    static private $logged_user = false;
    
    /**
     * Return logged in user
     *
     * @return User
     */
    static function &getLoggedUser() {
      if(self::$logged_user === false) {
        $user = self::$provider instanceof AuthenticationProvider ? self::$provider->getUser() : null;
        return $user;
      } // if
      
      return self::$logged_user;
    } // getLoggedUser
    
    /**
     * Override authentication provider and force set logged user for this request
     * 
     * @param IUser $user
     * @return IUser
     * @throws InvalidInstanceError
     */
    static function setLoggedUser($user) {
      if($user instanceof IUser) {
        self::$logged_user = $user;
      } elseif($user === null) {
        self::$logged_user = false;
      } else {
        throw new InvalidInstanceError('user', $user, 'IUser');
      } // if
      
      return self::getLoggedUser();
    } // setLoggedUser
    
    /**
     * Return visitor name from cookie
     * 
     * @return string
     */
    static function getVisitorName() {
      return Cookies::getVariable(AngieApplication::getName() . '_visitor_name');
    } // getVisitorName
    
    /**
     * Remember visitor name
     * 
     * @param string $name
     * @return string
     */
    static function setVisitorName($name) {
      if(trim($name)) {
        Cookies::setVariable(AngieApplication::getName() . '_visitor_name', $name);
      } else {
        Cookies::unsetVariable(AngieApplication::getName() . '_visitor_name');
      } // if

      return $name;
    } // setVisitorName
    
    /**
     * Return vistor email address from cookie
     * 
     * @return string
     */
    static function getVisitorEmail() {
      return Cookies::getVariable(AngieApplication::getName() . '_visitor_email');
    } // getVisitorEmail
    
    /**
     * Set visitor email to cookie
     * 
     * @param string $email
     * @return string
     */
    static function setVisitorEmail($email) {
      if(trim($email) && is_valid_email($email)) {
        Cookies::setVariable(AngieApplication::getName() . '_visitor_email', $email);
      } else {
        Cookies::unsetVariable(AngieApplication::getName() . '_visitor_email');
      } // if

      return $email;
    } // setVisitorEmail
    
  }
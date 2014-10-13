<?php

  /**
   * Abstract authentication provider
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  class AuthenticationProvider {
    
    /**
     * Logged user
     * 
     * @var User
     */
    protected $user;
    
    /**
     * Initialize provider
     *
     * @param mixed $init_params
     * @return User
     */
    function initialize($init_params) {
    
    } // initialize
    
    /**
     * Authenticate with given credential agains authentication source
     *
     * @param array $credentials
     * @return User
     */
    function authenticate($credentials) {
    
    } // authenticate
    
    /**
     * Set logged user
     * 
     * This method is called after user is successfully authenticated. We can 
     * put functionality that remembers user in a cookie or session, updates 
     * flags and timestamps in users table and so on
     *
     * @param User $user
     * @param array $settings
     * @return User
     * @throws InvalidInstanceError
     */
    function &logUserIn(User $user, $settings = null) {
      if($user instanceof User) {
        $this->user = $user;
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
      } // if

      if(isset($settings['new_session']) && $settings['new_session']) {
        EventsManager::trigger('on_new_user_session', array(&$this->user));
      } // if
      
      return $this->user;
    } // logUserIn
    
    /**
     * Log user out
     */
    function logUserOut() {
      $this->user = null;
    } // logUserOut

    // ---------------------------------------------------
    //  Password encoding
    // ---------------------------------------------------

    /**
     * Returns true if $password is $user's password
     *
     * @param string $password
     * @param User $user
     * @return boolean
     */
    function isUsersPassword($password, User $user) {
      return $user->isCurrentPassword($password);
    } // isUsersPassword
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return logged user (if we have it)
     *
     * @return User
     */
    function &getUser() {
      return $this->user;
    } // getUser
    
  }
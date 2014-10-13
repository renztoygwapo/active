<?php

  /**
   * Basic authentication provider
   * 
   * Although basic authentication provider is part of the framework it makes 
   * some assumptions about how authentication should work:
   * 
   * - Users are data objects that use email and password pairs for 
   *   authentication
   * - There is per user session ID that is used to track sessions
   * - We keep session ID in $_COOKIE
   * - We can remember session in a cookie for 14 days
   * 
   * @package angie.library.authentication
   * @subpackage provider
   */
  class BasicAuthenticationProvider extends AuthenticationProvider {
    
    /**
     * Secret key used for session ID and passwords
     *
     * @var string
     */
    protected $secret_key;
    
    /**
     * Session ID variable name (in $_COOKIE)
     *
     * @var string
     */
    protected $session_id_var_name = 'sid';

    /**
     * Session timestamp var name
     *
     * @var string
     */
    protected $session_timestamp_var_name = 'timestamp';

    /**
     * ID of current session, set by logUserIn()
     *
     * @var integer
     */
    protected $session_id = null;

    /**
     * Initialize basic authentication
     *
     * Try to get user from cookie or session
     *
     * @param array $params
     * @throws InvalidParamError
     * @return User|null
     */
    function initialize($params) {
      $this->secret_key = array_var($params, 'secret_key');
      
      if(empty($this->secret_key)) {
        throw new InvalidParamError('params', $params, 'params[secret_key] value is required');
      } // if

      $hash_part = extension_loaded('hash') ? hash('sha256', $this->secret_key) : sha1($this->secret_key);

      $this->session_id_var_name = array_var($params, 'sid_prefix') . '_sid_' . substr($hash_part, 0, 10);
      $this->session_timestamp_var_name = array_var($params, 'sid_prefix') . '_timestamp';

      if(CLEAN_OLD_SESSION_ON_EACH_REQUEST) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'user_sessions WHERE expires_on < ?', date(DATETIME_MYSQL)); // Expire old sessions
      } // if
      
      $cookie_session_id = Cookies::getVariable($this->session_id_var_name);
      
      $settings = array(
        'remember' => false,
        'new_visit' => false,
      );
      
      if($cookie_session_id && strpos($cookie_session_id, '/') !== false) {
        list($session_id, $session_key, $session_time) = explode('/', $cookie_session_id);
        
        if((time() - USER_SESSION_LIFETIME) > strtotime($session_time)) {
          $settings['new_visit'] = true;
        } // if
        
        $user = Users::findBySessionId($session_id, $session_key);
        
        if($user instanceof User && $user->isActive()) {
          if(is_array($settings)) {
            $settings['existing_session_id'] = $session_id;
          } else {
            $settings = array('existing_session_id' => $session_id);
          } // if
          
          $this->logUserIn($user, $settings);
        } // if
      } // if
    } // init

	  /**
	   * Try to log user in with given credentials
	   *
	   * @param array $credentials
	   * @return User
	   * @throws FirewallError
	   * @throws AuthenticationError
	   */
	  function authenticate($credentials) {
      $user = Users::findByEmail(array_var($credentials, 'email'), true);

		  if (AngieApplication::firewall()->isEnabled()) {
			  if (!AngieApplication::firewall()->check(AngieApplication::getVisitorIp(), $user)) {
				  throw new FirewallError(FirewallError::TOO_MANY_ATTEMPTS);
			  } // if
		  } // if
      
      if($user instanceof User) {
        if($user->isActive()) {
          if($this->isUsersPassword(array_var($credentials, 'password'), $user)) {
            if(ConfigOptions::getValue('maintenance_enabled') && !$user->isAdministrator()) {
              throw new AuthenticationError(AuthenticationError::IN_MAINTENANCE_MODE);
            } // if

            if(AngieApplication::isOnDemand()) {
              //if is on demand and in suspended status, allow only account owner
              if(OnDemand::getAccountStatus()->getStatus() == OnDemand::STATUS_SUSPENDED_PAID || OnDemand::getAccountStatus()->getStatus() == OnDemand::STATUS_SUSPENDED_FREE) {
                if(!OnDemand::isAccountOwner($user)) {
                  AngieApplication::notifications()
                    ->notifyAbout('on_demand/login_attempt')
                    ->setAttemptedBy($user)
                    ->sendToUsers(OnDemand::getAccountOwner());

                  throw new AuthenticationError(AuthenticationError::ACCOUNT_SUSPENDED);
                } //if
              } //if

              if(OnDemand::getAccountStatus()->getStatus() == OnDemand::STATUS_PENDING_DELETION) {
                throw new AuthenticationError(AuthenticationError::ACCOUNT_PENDING_FOR_DELETION);
              } //if
            } //if

	          $user->securityLog()->log('login');

            return $this->logUserIn($user, array(
              'remember' => (boolean) array_var($credentials, 'remember', false),
              'interface' => array_var($credentials, 'interface'),
              'new_visit' => true,
            ));
          } else {
	          $user->securityLog()->log('failed');
            throw new AuthenticationError(AuthenticationError::INVALID_PASSWORD);
          } // if
        } else {
          throw new AuthenticationError(AuthenticationError::USER_NOT_ACTIVE);
        } // if
      } else {
	      SecurityLogs::logAttempt(array_var($credentials, 'email'));
        throw new AuthenticationError(AuthenticationError::USER_NOT_FOUND);
      } // if
    } // authenticate
    
    // ---------------------------------------------------
    //  Login / logout
    // ---------------------------------------------------
    
    /**
     * Log user in
     * 
     * This function will recognise following settings:
     * 
     * - remember - remember session ID in cookie for 14 days
     * - new_visit - mark this as new visit (set last visit on timestamp to 
     *   current time)
     * - silent - used for a quick and dirty authentication, usually for feeds
     * 
     * $session_id is ID of existing session
     *
     * @param User $user
     * @param array $settings
     * @return User
     * @throws Exception
     */
    function &logUserIn(User $user, $settings = null) {
      if(isset($settings['silent']) && $settings['silent']) {
        return parent::logUserIn($user);
      } else {
        $existing_session_id = isset($settings['existing_session_id']) && $settings['existing_session_id'] ? $settings['existing_session_id'] : null;
        
        try {
          DB::beginWork('Logging user in @ ' . __CLASS__);
        
          $users_table = TABLE_PREFIX . 'users';
          $user_sessions_table = TABLE_PREFIX . 'user_sessions';
          
          $remember = (boolean) array_var($settings, 'remember', false);
          $new_visit = (boolean) array_var($settings, 'new_visit', false);

          // Some initial data
          $session_id = null;
          $new_expires_on = $remember ? time() + 1209600 : time() + USER_SESSION_LIFETIME; // 30 minutes or 2 weeks?
          
          // Existing session
          if($existing_session_id) {
            $existing_session_data = DB::executeFirstRow("SELECT remember, session_key, interface FROM $user_sessions_table WHERE id = ?", $existing_session_id);
            
            if($existing_session_data && isset($existing_session_data['remember']) && isset($existing_session_data['session_key'])) {
              if($existing_session_data['remember']) {
                $new_expires_on = time() + 1209600;
              } // if
              
              $session_key = $existing_session_data['session_key'];
              
              DB::execute("UPDATE $user_sessions_table SET user_ip = ?, user_agent = ?, last_activity_on = UTC_TIMESTAMP(), expires_on = ?, visits = visits + 1 WHERE id = ?", AngieApplication::getVisitorIp(), substr_utf(AngieApplication::getVisitorUserAgent(), 0, 255), date(DATETIME_MYSQL, $new_expires_on), $existing_session_id);
              $session_id = $existing_session_id;
              
              AngieApplication::setPreferedInterface($existing_session_data['interface']);
            } // if
          } // if

          $new_session = false;
          
          // New session?
          if($session_id === null) {
          	AngieApplication::setPreferedInterface(array_var($settings, 'interface'));
          	
            do {
              $session_key = make_string(40);
            } while(DB::executeFirstCell("SELECT COUNT(id) AS 'row_count' FROM $user_sessions_table WHERE session_key = ?", $session_key) > 0);
            
            DB::execute("INSERT INTO $user_sessions_table (user_id, user_ip, user_agent, visits, remember, interface, created_on, last_activity_on, expires_on, session_key) VALUES (?, ?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?, ?, ?)", $user->getId(), AngieApplication::getVisitorIp(), AngieApplication::getVisitorUserAgent(), 1, (integer) $remember, AngieApplication::getPreferedInterface(), date(DATETIME_MYSQL), date(DATETIME_MYSQL, $new_expires_on), $session_key);
            $session_id = DB::lastInsertId();

            $new_session = true;
          } // if
          
          // Update last visit time
          if($new_visit) {
            DB::execute("UPDATE $users_table SET last_visit_on = last_login_on, last_login_on = ?, last_activity_on = ? WHERE id = ?", date(DATETIME_MYSQL), date(DATETIME_MYSQL), $user->getId());
          } else {
            DB::execute("UPDATE $users_table SET last_activity_on = ? WHERE id = ?", date(DATETIME_MYSQL), $user->getId());
          } // if
          
          DB::commit('User logged in @ ' . __CLASS__);
          
          $this->session_id = $session_id; // remember it, for logout
          
          Cookies::setVariable($this->session_id_var_name, "$session_id/$session_key/" . date(DATETIME_MYSQL));
          Cookies::setVariable($this->session_timestamp_var_name, time(), null, false);

          return parent::logUserIn($user, array(
            'new_session' => $new_session,
          ));
        } catch(Exception $e) {
          DB::rollback('Failed to log user in @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // logUserIn
    
    /**
     * Log user out
     *
     * @param boolean $force
     */
    function logUserOut($force = false) {
      DB::execute("DELETE FROM " . TABLE_PREFIX . 'user_sessions WHERE id = ?', $this->session_id);

      Cookies::unsetVariable($this->session_id_var_name);
      Cookies::unsetVariable($this->session_timestamp_var_name);

	    if ($force) {
		    $this->user->securityLog()->log('expired');
	    } else {
		    $this->user->securityLog()->log('logout');
	    } // if

      parent::logUserOut();
    } // logUserOut
    
    /**
     * Return session ID var name
     *
     * @return string
     */
    function getSessionIdVarName() {
      return $this->session_id_var_name;
    } // getSessionIdVarName

	  /**
	   * Return is this session_id active
	   *
	   * @param integer $session_id
	   * @return bool
	   */
	  function isSessionActive($session_id) {
		  return $this->session_id === $session_id ? true : false;
	  } // isSessionActive

	  /**
	   * Kill Sessions by id
	   *
	   * @param $session_id
	   * @return DbResult
	   */
	  static function killSessions($session_id) {
		  if (!is_array($session_id)) {
			  $session_id = array($session_id);
		  } // if
		  if (!$session_id) {
			  return false;
		  } // if
		  return DB::execute("DELETE FROM " . TABLE_PREFIX . 'user_sessions WHERE id IN (?)', $session_id);
	  } // killSessions

	  static function mapSessionIDtoIP() {
		  $result = array();
		  $sessions = DB::executeFirstColumn("SELECT id, user_ip FROM " . TABLE_PREFIX . "user_sessions WHERE expires_on > ?", DateTimeValue::now()->toMySQL());
		  if (is_foreachable($sessions)) {
			  foreach ($sessions as $session) {
				  $id = $session['id'];
				  $ip = $session['user_ip'];
				  $result[$id] = $ip;
			  } // foreach
		  } // if
		  return $result;
	  }

    /**
     * Return session timestamp var name
     *
     * @return string
     */
    function getSessionTimestampVarName() {
      return $this->session_timestamp_var_name;
    } // getSessionTimestampVarName
    
  }
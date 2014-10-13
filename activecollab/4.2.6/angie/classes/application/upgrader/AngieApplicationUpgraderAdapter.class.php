<?php

  /**
   * Angie application upgrade system
   *
   * @package angie.library.application
   * @subpackage upgrader
   */
  abstract class AngieApplicationUpgraderAdapter {
    
    // Validation statuses
    const VALIDATION_OK = 'ok';
    const VALIDATION_WARNING = 'warning';
    const VALIDATION_ERROR = 'error';
    
    /**
     * Return installer sections
     * 
     * @return array
     */
    function getSections() {
      return array(
        'login' => 'Log In', 
        'upgrade' => 'Upgrade Database', 
      );
    } // getSections
    
    /**
     * Render initial section content
     * 
     * @param string $name
     * @return string
     */
    function getSectionContent($name) {
      switch($name) {
            
        // Render login form
        case 'login':
          return "<p>Now that we have connected to database, all we need is to set up an administrator's account and configure some defaults. Please fill the details below and click on Install button to complete the installation</p>" . 
            $this->getLoginForm() . 
            '<script type="text/javascript">$("#application_upgrader").upgrader("validate", "login", validate_login_parameters);</script>';
            
        // List upgrade steps
        case 'upgrade':
          $current_version = $this->currentVersion();
          
          $available_scripts = $this->availableScripts($current_version);
          if($available_scripts) {
            $response = "<p>Your database version is $current_version and here are the steps that we need to execute so we can upgrade it to the latest version:</p>";

            $first = true;
            
            $response .= '<ul id="upgrader_actions_list">';
            foreach($available_scripts as $script) {
              $group = $script->getGroup();

              if($first) {
                $response .= '<li upgrade_group="' . $group . '" upgrade_action="startUpgrade" class="not_executed">Start upgrade</li>';
                $first = false;
              } // if
              
              if($script->getActions()) {
                foreach($script->getActions() as $action => $description) {
                  $response .= '<li upgrade_group="' . $group . '" upgrade_action="' . $action . '" class="not_executed">' . clean($description) . '</li>';
                } // foreach
              } // if
            } // foreach
            $response .= '</ul>';
          } else {
            $response = "<p>Good, your database version is $current_version and it's up to date!</p>";
          } // if
          
          return $response;
      } // switch
    } // getSectionContent
    
    /**
     * Handle given section
     * 
     * @param string $name
     * @param mixed $data
     * @param string $response
     * @return bool
     */
    function executeSection($name, $data, &$response) {
      switch($name) {
          
        // Log in
        case 'login':
          $login_params = $this->getLoginParams($data);
          
          if($this->validateBeforeUpgrade($login_params)) {
            $response = $this->printValidationLog();
            $response .= '<script type="text/javascript">$("#application_upgrader").upgrader("run", ' . var_export($login_params['email'], true) . ', ' . var_export($login_params['pass'], true) . ');</script>';
            return true;
          } else {
            $response = $this->printValidationLog();
            return false;
          } // if
          
      } // switch
      
      return false;
    } // executeSection
    
    // ---------------------------------------------------
    //  Actions
    // ---------------------------------------------------
    
    /**
     * Execute given action
     * 
     * @param string $group
     * @param string $action
     * @return boolean
     */
    function executeAction($group, $action) {
      $script = $this->getScriptByGroup($group);
      
      if($script instanceof AngieApplicationUpgradeScript) {
        return $script->$action();
      } else {
        return "Invalid group";
      } // if
    } // executeAction
    
    // ---------------------------------------------------
    //  Login
    // ---------------------------------------------------
    
    /**
     * Return login form
     * 
     * @param string $admin_email
     * @return string
     */
    private function getLoginForm($admin_email = '') {
      return '<form action="index.php" method=post>' . 
        '<p class="wrap_form_element"><label for="login_email_input">Your Email Address</label> <input type="text" name="login[email]" id="login_email_input" value="' . clean($admin_email) . '"></p>' . 
        '<p class="wrap_form_element"><label for="login_pass_input">Your Password</label> <input type="password" name="login[pass]" id="login_pass_input"></p>' . 
        '<p><button type="submit">Log In</button></p>' . 
      '</form>';
    } // getLoginForm
    
    /**
     * Return admin parameters
     * 
     * @param array $from
     * @return array
     */
    function getLoginParams($from) {
      $params = isset($from['login']) && is_array($from['login']) ? $from['login'] : array();
          
      if(!isset($params['email'])) {
        $params['email'] = '';
      } // if
      
      if(!isset($params['pass'])) {
        $params['pass'] = '';
      } // if
      
      return $params;
    } // getLoginParams
    
    /**
     * Validate login parameters and other conditions required for upgrade to work properly
     * 
     * @param array $params
     * @return boolean
     */
    function validateBeforeUpgrade($params) {
      $this->cleanUpValidationLog();

      if(version_compare(PHP_VERSION, '5.3.3', '<')) {
        $this->validationLogError('PHP version that is required to run the system is 5.3.3. You have ' . PHP_VERSION);
      } // if

      if(extension_loaded('phar')) {
        $this->validationLogOk('Phar extension is available');
      } else {
        $this->validationLogError('Required Phar PHP extension was not found. Please install it before continuing');
      } // if

      // Check memory limit
      $memory_limit = php_config_value_to_bytes(ini_get('memory_limit'));

      if($memory_limit == -1 || $memory_limit >=  67108864) {
        $formatted_memory_limit = $memory_limit == -1 ? "unlimited" : format_file_size($memory_limit);
        $this->validationLogOk('Your memory limit is: ' . $formatted_memory_limit);
      } else {
        $this->validationLogError('Your memory is too low to complete the upgrade. Minimal value is 64MB, and you have it set to ' . format_file_size($memory_limit));
      } // if

      // Check whether ROOT is writable
      if(folder_is_writable(ROOT)) {
        $this->validationLogOk('/activecollab folder is writable');
      } else {
        $this->validationLogError('/activecollab folder is not writable. Make it writable to continue');
      } // if

      // Check if public/assets folder is writable
      if (defined('PROTECT_ASSETS_FOLDER') && PROTECT_ASSETS_FOLDER) {
        $this->validationLogOk("Assets folder is protected, skipping writable check");
      } else {
        if(folder_is_writable(PUBLIC_PATH . '/assets')) {
          $this->validationLogOk('public/assets folder is writable');
        } else {
          $this->validationLogError('public/assets folder is not writable. Make it writable to continue');
        } // if
      } // if

      // Check versions.php
      if(file_is_writable(CONFIG_PATH . '/version.php')) {
        $this->validationLogOk('config/version.php is writable');
      } else {
        $this->validationLogError('config/version.php is not writable. Make it writable to continue');
      } // if

      // Validate login parameters
      $email = $params['email']; 
      $password = $params['pass'];
      
      if(php_sapi_name() !== 'cli' && $email && $password) {
        if(is_valid_email($email)) {
          $user_table_fields = DB::listTableFields(TABLE_PREFIX . 'users');

          // Version 4.0.0 or newer
          if(in_array('type', $user_table_fields)) {
            $user = DB::executeFirstRow('SELECT type, password FROM ' . TABLE_PREFIX . 'users WHERE email = ?', $email);

          // Versions older than 4.0.0
          } else {
            $user = DB::executeFirstRow('SELECT role_id, password FROM ' . TABLE_PREFIX . 'users WHERE email = ?', $email);
          } // if

          if(is_array($user)) {
            if($this->checkUserPassword($password, $user['password'])) {
              if($this->isUserAdministrator($user)) {
                $this->validationLogOk("User '$email' authenticated");
              } // if
            } else {
              $this->validationLogError("Invalid password");
            } // if
          } else {
            $this->validationLogError("User '$email' not found");
          } // if
        } else {
          $this->validationLogError("'$email' is not a valid email address");
        } // if
      } else {
        $this->validationLogError('Authentication data not provided');
      } // if
      
      return $this->everythingValid();
    } // validateBeforeUpgrade
    
    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------
    
    /**
     * Cached current version value
     *
     * @var string
     */
    private $current_version = false;
    
    /**
     * Return current installation version
     *
     * @return string
     */
    function currentVersion() {
      if($this->current_version === false) {
        $this->current_version = DB::executeFirstCell('SELECT version FROM ' . TABLE_PREFIX . 'update_history ORDER BY created_on DESC LIMIT 0, 1');
        if(empty($this->current_version)) {
          $this->current_version = '1.0';
        } // if
    	  
    	  // activeCollab 2.0.2 failed to record proper version into update 
    	  // history so we need to manually check if we have 2.0.2. This is done 
    	  // by checking if acx_attachments table exists (introduced in aC 2).
    	  if((version_compare($this->current_version, '2.0') < 0) && DB::tableExists(TABLE_PREFIX . 'attachments')) {
    	    $this->current_version = '2.0.2';
    	  } // if
      } // if
      return $this->current_version;
    } // currentVersion
    
    /**
     * Check if users password is OK. This function is aC version sensitive
     *
     * @param string $raw
     * @param string $from_database
     * @return boolean
     */
    private function checkUserPassword($raw, $from_database) {
      $application_key = defined('APPLICATION_UNIQUE_KEY') ? APPLICATION_UNIQUE_KEY : LICENSE_KEY;

      // activeCollab 1.0
      if(version_compare($this->currentVersion(), '1.1', '<')) {
        if ($raw == $from_database) {
        	return true;
        } else {
        	return sha1($application_key . $raw) == $from_database;
        } // if

      // activeCollab 1.1 to activeCollab 3.1.17
      } elseif(version_compare($this->currentVersion(), '3.1.17', '<')) {
        return sha1($application_key . $raw) == $from_database;

      // activeCollab 3.1.17+
      } else {
        if(strlen($from_database) == 40) {
          return sha1($application_key . $raw) == $from_database; // We have old, SHA1 encoded value
        } else {
          return base64_encode(pbkdf2($raw, $application_key, 1000, 40)) == $from_database; // New one, encoded with PBKDF2
        } // if
      } // if

    } // checkUserPassword
    
    /**
     * Check if user is administrator by his role ID
     *
     * @param array $user
     * @return boolean
     */
    private function isUserAdministrator($user) {

      // Version 4.0.0
      if(isset($user['type'])) {
        return $user['type'] == 'Administrator';

      // Versions older than 4.0.0
      } else {
        $role_id = (integer) $user['role_id'];

        // activeCollab < 1.1
        if(version_compare($this->currentVersion(), '1.1', '<') && DB::tableExists(TABLE_PREFIX . 'role_permissions')) {
          $role_permission = DB::executeFirstRow('SELECT value FROM ' . TABLE_PREFIX . 'role_permissions WHERE role_id = ? AND permission = ?', $role_id, 'admin_access');
          if($role_permission) {
            return (boolean) $role_permission['value'];
          } else {
            return false;
          } // if

          // Post 1.0
        } else {
          $role_permissions = DB::executeFirstRow('SELECT permissions FROM ' . TABLE_PREFIX . 'roles WHERE id = ?', $role_id);
          if(is_array($role_permissions) && isset($role_permissions['permissions']) && $role_permissions['permissions']) {
            $permissions = $role_permissions['permissions'] ? unserialize($role_permissions['permissions']) : array();

            return
              (isset($permissions['admin_access']) && $permissions['admin_access']) ||         // activeCollab 2.3.x or older
              (isset($permissions['has_admin_access']) && $permissions['has_admin_access']);   // activeCollab 3.0 +
          } else {
            return false;
          } // if
        } // if
      } // if
    } // isUserAdministrator
    
    // ---------------------------------------------------
    //  Script loader
    // ---------------------------------------------------
    
    /**
     * Return script by group
     *
     * @param string $group
     * @return UpgradeScript
     */
    function getScriptByGroup($group) {
    	$files = $this->getLatestVersionScripts();

    	if(is_foreachable($files)) {
    	  foreach($files as $file) {
    	    require_once $file;
    	    $basename = basename($file);
    	    
    	    $class_name = substr($basename, 0,  strpos($basename, '.'));
    	    
    	    $script = new $class_name($this);
    	    
    	    if($script->getGroup() == $group) {
    	      return $script;
    	    } // if
    	  } // foreach
    	} // if
    	
    	return null;
    } // getScript
    
    /**
     * Return list of upgrade scripts that are newer than $newer_than version
     *
     * @param string $current_version
     * @return AngieApplicationUpgradeScript[]
     */
    function availableScripts($current_version) {
      $files = $this->getLatestVersionScripts();
    	
    	$result = array();
    	if(is_foreachable($files)) {
    	  sort($files);
    	  
    	  foreach($files as $file) {
    	    require_once $file;
    	    $basename = basename($file);
    	    
    	    $class_name = substr($basename, 0,  strpos($basename, '.'));
    	    
    	    $script = new $class_name($this);

          if($script instanceof AngieApplicationUpgradeScript && !$script->isExecuted($current_version)) {
            $result[] = $script;
          } // if
    	  } // foreach
    	} // if
    	
    	return empty($result) ? null : $result;
    } // availableScripts
    
    /**
     * Return latest version that's available
     * 
     * @return string
     */
    private function getLatestVersion() {
      if (php_sapi_name() == 'cli' && isset($_SERVER['argv']) && is_foreachable($_SERVER['argv']) && array_key_exists(1, $_SERVER['argv'])) {
        return $_SERVER['argv'][1];
      } else {
        if(is_dir(ROOT . '/current')) {
          return 'current';
        } else {
          $latest_version = null;

          if($h = @opendir(ROOT)) {
            while(false !== ($version = readdir($h))) {
              if(substr($version, 0, 1) == '.') {
                continue;
              } // if

              if($this->isValidVersionNumber($version)) {
                if(empty($latest_version)) {
                  $latest_version = $version;
                } else {
                  if(version_compare($latest_version, $version, '<')) {
                    $latest_version = $version;
                  } // if
                } // if
              } // if
            } // while
          } // if

          return $latest_version;
        } // if
      } // if
    } // getLatestVersion
    
    /**
     * Return latest version scripts
     */
    private function getLatestVersionScripts() {
      $latest_version = $this->getLatestVersion();
      
      if($latest_version) {
        $latest_version_path = ROOT . "/$latest_version";
        
        return get_files("$latest_version_path/upgrade", 'php');
      } else {
        return array();
      } // if
    } // getLatestVersionScripts
    
    /**
     * Returns true if $version is a valid angie application version number
     * 
     * @param string $version
     * @return boolean
     */
    private function isValidVersionNumber($version) {
      if(strpos($version, '.') !== false) {
        $parts = explode('.', $version);
        
        if(count($parts) == 3) {
          foreach($parts as $part) {
            if(!is_numeric($part)) {
              return false;
            } // if
          } // foreach
          
          return true;
        } else {
          return false;
        } // if
      } else {
        return false;
      } // if
    } // isValidVersionNumber
    
    // ---------------------------------------------------
    //  Validation log
    // ---------------------------------------------------

    /**
     * Return validation log
     *
     * @return mixed
     */
    function getValidationLog() {
      return $this->validation_log;
    } // getValidationLog
    
    /**
     * Clean up validation log
     */
    function cleanUpValidationLog() {
      if(!empty($this->validation_log)) {
        $this->validation_log = array();
      } // if
    } // cleanUpValidationLog
    
    /**
     * Returns true if there are no errors in validation log
     * 
     * @return boolean
     */
    function everythingValid() {
      foreach($this->validation_log as $v) {
        if($v['status'] == self::VALIDATION_ERROR) {
          return false;
        } // if
      } // foreach
      
      return true;
    } // everythingValid
    
    /**
     * Log validation OK message
     * 
     * @param string $message
     */
    function validationLogOk($message) {
      $this->validation_log[] = array(
        'status' => self::VALIDATION_OK, 
        'message' => $message,
        'explanation' => null, 
      );
    } // validationLogOk
    
    /**
     * Log validation warning message
     * 
     * @param string $message
     * @param string $explanation
     */
    protected function validationLogWarning($message, $explanation = null) {
      $this->validation_log[] = array(
        'status' => self::VALIDATION_WARNING, 
        'message' => $message,
        'explanation' => $explanation, 
      );
    } // validationLogWarning
    
    /**
     * Log validation error message
     * 
     * @param string $message
     * @param string $explanation
     */
    protected function validationLogError($message, $explanation = null) {
      $this->validation_log[] = array(
        'status' => self::VALIDATION_ERROR, 
        'message' => $message,
        'explanation' => $explanation, 
      );
    } // validationLogError
    
    /**
     * Print validation log
     * 
     * @return string
     */
    function printValidationLog() {
      return php_sapi_name() == 'cli' ? $this->printValidationLogToConsole() : $this->printValidationLogToHtml();
    } // printValidationLog

    /**
     * @return string
     */
    protected function printValidationLogToHtml() {
      $response = '<ul class="validation_log">';

      foreach($this->validation_log as $log_entry) {
        switch($log_entry['status']) {
          case self::VALIDATION_ERROR:
            $class = 'error';
            $status = 'Error';
            break;
          case self::VALIDATION_WARNING:
            $class = 'warning';
            $status = 'Warning';
            break;
          default:
            $class = 'ok';
            $status = 'OK';
        } // switch

        $response .= '<li class="' . $class . '"><span class="status">' . $status . '</span> &mdash; <span class="message">' . clean($log_entry['message']) . '</span>';

        if($log_entry['explanation']) {
          $response .= '<span class="explanation">' . clean($log_entry['explanation']) . '</span>';
        } // if

        $response .= '</li>';
      } // foreach

      return "$response</ul>";
    } // printValidationLogToHtml

    /**
     * @return string
     */
    protected function printValidationLogToConsole() {
      $response = '';

      foreach($this->validation_log as $log_entry) {
        switch($log_entry['status']) {
          case self::VALIDATION_ERROR:
            $status = 'Error';
            break;
          case self::VALIDATION_WARNING:
            $status = 'Warning';
            break;
          default:
            $status = 'OK';
        } // switch

        $response .= $status . ': ' . $log_entry['message'];

        if($log_entry['explanation']) {
          $response .= ' (' . clean($log_entry['explanation']) . ')';
        } // if

        $response .= "\n";
      } // foreach

      return "$response\n";
    } // printValidationLogToConsole

  }
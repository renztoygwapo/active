<?php

  /**
   * Application installer adapter
   * 
   * @package angie.library.application
   */
  abstract class AngieApplicationInstallerAdapter {
    
    // Validation statuses
    const VALIDATION_OK = 'ok';
    const VALIDATION_WARNING = 'warning';
    const VALIDATION_ERROR = 'error';
    
    /**
     * Minimal PHP version
     *
     * @var string
     */
    protected $min_php_version = '5.3.3';

    /**
     * Minimal memory_limit value (in MB)
     *
     * @var integer
     */
    protected $min_memory = null;
    
    /**
     * Recommended PHP version
     *
     * @var string
     */
    protected $recommended_php_version = '5.5';
    
    /**
     * Minimal MySQL version
     *
     * @var string
     */
    protected $min_mysql_version = '5.0';
    
    /**
     * Default table prefix
     *
     * @var string
     */
    protected $default_table_prefix = 'app_';

    /**
     * Recommended MySQL version
     *
     * @var string
     */
    protected $recommended_mysql_version = '5.5';
    
    /**
     * List of PHP requestions that are required to be installed
     *
     * @var array
     */
    protected $required_php_extensions = array('mysql', 'pcre', 'tokenizer', 'ctype', 'session', 'json', 'xml', 'dom', 'phar');
    
    /**
     * List of PHP extensions that are recommended to be installed for some 
     * application features to work
     *
     * @var array
     */
    protected $recommended_php_extensions = array(
      'gd' => 'image manipulation', 
      'mbstring' => 'Unicode operations',
      'curl' => 'support various network tasks',
      'iconv' => 'characterset operations',
      'imap' => 'connect to POP3/IMAP mailboxes and read email messages',
      'zlib' => 'read and write gzip (.gz) compressed files',
    );
    
    /**
     * List of folder that will need to be writable for installed to be able to 
     * set up the application
     *
     * @var array
     */
    protected $writable_folders = array(
      'cache',
      'compile',
      'config',
      'logs',
      'thumbnails',
      'upload',
      'work',
    );

    /**
     * List of files that needs to be writable
     *
     * @var array
     */
    protected $writable_files = array(
      'config/version.php'
    );

    /**
     * Default installer mode is self-install
     *
     * @var bool
     */
    protected $is_self_install = true;

    // ---------------------------------------------------
    //  Sections
    // ---------------------------------------------------
    
    /**
     * Return installer sections
     * 
     * @return array
     */
    function getSections() {
      return array(
        'welcome' => 'Welcome', 
        'database' => 'Database Connection', 
        'admin' => 'Administrator', 
      );
    } // getSections
    
    /**
     * Render initial section content
     * 
     * @param string $name
     * @return string
     */
    function getSectionContent($name) {
      $application_name = AngieApplication::getName();
      
      switch($name) {
        
        // Render welcome message
        case 'welcome':
          return '<form action="index.php" method=post>' .  
            '<p>Welcome to ' . $application_name . ' Installer. This tool will help you set up the system easily and quickly, within minutes.</p>' . 
            '<p>First step is to check if your platform can run ' . $application_name . '. Click on the button below to run the tests.</p>' . 
            '<p><button type="submit">Validate</button></p>' . 
          '</form>';
          
        // Render database form
        case 'database':
          return '<p>Good, your platform can run ' . $application_name . '. Now lets connect to database. Please provide database host, username and password, as well as name of the database that you want to use for ' . $application_name . '</p>' . 
            $this->getDatabaseConnectionForm('localhost', '', '', '', $this->default_table_prefix) . 
            '<script type="text/javascript">$("#application_installer").installer("validate", "database", validate_database_parameters);</script>';
            
        // Render admin credentials form
        case 'admin':
          return "<p>Now that we have connected to database, all we need is to set up an administrator's account and configure some defaults. Please fill the details below and click on Install button to complete the installation</p>" . 
            $this->getAdminForm() . 
            '<script type="text/javascript">$("#application_installer").installer("validate", "admin", validate_admin_parameters);</script>';
            
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
        
        // Run environment tests
        case 'welcome':
          
          // Environment is valid
          if($this->validateEnvironment()) {
            $response = $this->printValidationLog();
            return true;
            
          // Environment is not valid
          } else {
            $response = '<form action="index.php" method="post">' . $this->printValidationLog() . '<p><button type="submit">Revalidate</button></p></form>';
            return false;
          } // if
          
        // Connect to database and validate database support
        case 'database':
          $database_params = $this->getDatabaseParams($_POST);
          
          if($this->validateDatabase($database_params)) {
            $response = $this->printValidationLog();
            return true;
          } else {
            $response = $this->printValidationLog();
            $response .= $this->getDatabaseConnectionForm($database_params['host'], $database_params['user'], $database_params['pass'], $database_params['name'], $database_params['prefix']);
            
            return false;
          } // if
          
        // Create administrators account
        case 'admin':
          $database_params = $this->getDatabaseParams($_POST);
          $admin_params = $this->getAdminParams($_POST);
          $license_params = $this->getLicenseParams($_POST);
          
          if($this->validateInstallation($database_params, $admin_params, $license_params)) {
            $response = $this->printValidationLog();
            return true;
          } else {
            $response = $this->printValidationLog();
            $response .= $this->getAdminForm($admin_params['email']);
            
            return false;
          } // if
          
      } // switch
      
      return false;
    } // executeSection
    
    /**
     * Return database connection form
     * 
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $name
     * @param string $prefix
     * @return string
     */
    private function getDatabaseConnectionForm($host = 'localhost', $user = '', $pass = '', $name = '', $prefix = '') {
      return '<form action="index.php" method=post>' . 
        '<p class="wrap_form_element"><label for="database_host_input">Host</label> <input type="text" name="database[host]" id="database_host_input" value="' . clean($host) . '"></p>' . 
        '<p class="wrap_form_element"><label for="database_user_input">Username</label> <input type="text" name="database[user]" id="database_user_input" value="' . clean($user) . '"></p>' . 
        '<p class="wrap_form_element"><label for="database_pass_input">Password</label> <input type="password" name="database[pass]" id="database_pass_input"></p>' . 
        '<p class="wrap_form_element"><label for="database_host_input">Database Name</label> <input type="text" name="database[name]" id="database_name_input" value="' . clean($name) . '"></p>' . 
        '<p class="wrap_form_element"><label for="database_prefix_input">Table Prefix</label> <input type="text" name="database[prefix]" id="database_prefix_input" value="' . clean($prefix) . '"></p>' . 
        '<p><button type="submit">Connect</button></p>' . 
      '</form>';
    } // getDatabaseConnectionForm
    
    /**
     * Return admin account form
     * 
     * @param string $admin_email
     * @return string
     */
    private function getAdminForm($admin_email = '') {
      return '<form action="index.php" method=post>' . 
        '<p class="wrap_form_element"><label for="admin_email_input">Your Email Address</label> <input type="text" name="admin[email]" id="admin_email_input" value="' . clean($admin_email) . '"></p>' . 
        '<p class="wrap_form_element"><label for="admin_pass_input">Your Password</label> <input type="password" name="admin[pass]" id="admin_pass_input"> <input type="checkbox" id="admin_reveal_password"> Reveal Password</p>' . 
        '<p class="wrap_form_element"><input type="checkbox" name="license[accepeted]" id="license_accepeted_input"> I Accept <a href="' . AngieApplication::getLicenseAgreementUrl() . '" tabindex="-1" target="_blank">' . AngieApplication::getName() . ' License Agreement</a></p>'.
        '<p class="wrap_form_element"><input type="checkbox" name="license[help_improve]" id="help_improve_input" checked> Help Us Improve ' . AngieApplication::getName() . ' by Sending Anonymous Usage Information</p>'.
        '<p><button type="submit">Install</button></p>' .
      '</form>
      <script type="text/javascript">
      	$("#admin_reveal_password").click(function() {
      		var old_password_input = $("#admin_pass_input");
      	
      		if(this.checked) {
      			var new_password_input_type = "text";
    			} else {
    				var new_password_input_type = "password";
    			} // if
    			
    			var new_password_input = $(\'<input type="\' + new_password_input_type + \'" name="admin[pass]" id="admin_pass_input">\').val(old_password_input.val());
    			
    			old_password_input.after(new_password_input).remove();
    			new_password_input.attr("id", "admin_pass_input");
    		});
      </script>';
    } // getAdminForm

    // ---------------------------------------------------
    //  General
    // ---------------------------------------------------
    
    /**
     * Return a list of modules that need to be installed
     * 
     * @return array
     */
    function getModulesToInstall() {
      $modules = array('system');

      $all_modules = get_folders(APPLICATION_PATH . '/modules');

      if($all_modules) {
        foreach($all_modules as $module_path) {
          $module_name = basename($module_path);

          if(!in_array($module_name, $modules)) {
            $modules[] = $module_name;
          } // if
        } // foreach
      } // if

      return $modules;
    } // getModulesToInstall

    /**
     * Returns true if this is self-installer (application that installs itself)
     *
     * @return boolean
     */
    function isSelfInstall() {
      return $this->is_self_install;
    } // isSelfInstall

    /**
     * Return configuration options array
     *
     * Supported additional params:
     *
     * - root_url - Used instead of internal getRootUrl() call
     * - use_unpacked_files - Forced USE_UNPAKCED_FILES configuration option value
     *
     * @param array $database_params
     * @param array $admin_params
     * @param array $licensing_params
     * @param array $additional_params
     * @return array
     */
    function getConfigOptions($database_params, $admin_params, $licensing_params, $additional_params = null) {
      $config_options = array(
        'ROOT' => ROOT,
        'ROOT_URL' => $this->getRootUrl(),
        'DB_HOST' => $database_params['host'],
        'DB_USER' => $database_params['user'],
        'DB_PASS' => $database_params['pass'],
        'DB_NAME' => $database_params['name'],
        'DB_CAN_TRANSACT' => $database_params['have_innodb'],
        'TABLE_PREFIX' => $database_params['prefix'],
        'ADMIN_EMAIL' => $admin_params['email'],
      );

      if(defined('APPLICATION_UNIQUE_KEY') && APPLICATION_UNIQUE_KEY) {
        $config_options['APPLICATION_UNIQUE_KEY'] = APPLICATION_UNIQUE_KEY;
      } // if

      if($this->isSelfInstall()) {
        if(!str_starts_with(__FILE__, 'phar://') && !array_key_exists('USE_UNPACKED_FILES', $config_options)) {
          $config_options['USE_UNPACKED_FILES'] = true;
        } // if

        if(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1')) {
          $config_options['COOKIE_DOMAIN'] = ''; // In case of localhost, fix COOKIE_DOMAIN
        } // if
      } // if

      if(is_array($additional_params) && isset($additional_params['force_config_options']) && is_array($additional_params['force_config_options'])) {
        foreach($additional_params['force_config_options'] as $k => $v) {
          $config_options[$k] = $v;
        } // foreach
      } // if

      return $config_options;
    } // getConfigOptions
    
    // ---------------------------------------------------
    //  Register requirements
    // ---------------------------------------------------
    
    /**
     * Set minimal PHP version
     * 
     * @param string $version
     */
    function setMinPHPVersion($version) {
      $this->min_php_version = $version;
    } // setMinPHPVersion
    
    /**
     * Set recommended PHP version
     * 
     * @param string $version
     */
    function setRecommendedPHPVersion($version) {
      $this->recommended_php_version = $version;
    } // setRecommendedPHPVersion

    /**
     * Set min memory value
     *
     * @param $min_memory
     */
    function setMinMemory($min_memory) {
      $this->min_memory = $min_memory;
    } // setMinMemory
    
    /**
     * Set minimal MySQL version
     * 
     * @param string $version
     */
    function setMinMySQLVersion($version) {
      $this->min_mysql_version = $version;
    } // setMinMySQLVersion
    
    /**
     * Set recommended MySQL version
     * 
     * @param string $version
     */
    function setRecommenderMySQLVersion($version) {
      $this->recommended_mysql_version = $version;
    } // setRecommenderMySQLVersion
    
    /**
     * Set default table prefix
     * 
     * @param string $prefix
     */
    function setDefaultTablePrefix($prefix) {
      $this->default_table_prefix = $prefix;
    } // setDefaultTablePrefix
    
    /**
     * Add a one or more of PHP extensions to list of required extensions
     * 
     * @param array $extension
     */
    function addRequiredPhpExtension($extension) {
      $to_add = (array) $extension;
      
      foreach($to_add as $v) {
        if(!in_array($v, $this->required_php_extensions)) {
          $this->required_php_extensions[] = $v;
        } // if
      } // foreach
    } // addRequiredPhpExtension
    
    /**
     * Add one or more recommended PHP extensions to the list
     * 
     * @param string $extension
     * @param string $why_recommended
     */
    function addRecommendedPhpExtension($extension, $why_recommended = null) {
      if(is_array($extension)) {
        $to_add = $extension;
      } else {
        $to_add = array($extension => $why_recommended);
      } // if
      
      foreach($to_add as $k => $v) {
        $this->recommended_php_extensions[$k] = $v;
      } // foreach
    } // addRecommendedPhpExtension
    
    /**
     * Add folder to the list of folder that will need to be writable
     * 
     * @param string $rel_path
     */
    function addWritableFolder($rel_path) {
      if(is_array($rel_path)) {
        foreach($rel_path as $k) {
          if(!in_array($k, $this->writable_folders)) {
            $this->writable_folders[] = $k;
          } // if
        } // foreach
      } else {
        if(!in_array($rel_path, $this->writable_folders)) {
          $this->writable_folders[] = $rel_path;
        } // if
      } // if
    } // addWritableFolder

    /**
     * Add file that needs to be writable
     *
     * @param string $rel_path
     */
    function addWritableFile($rel_path) {
      if(is_array($rel_path)) {
        foreach($rel_path as $k) {
          if(!in_array($k, $this->writable_files)) {
            $this->writable_files[] = $k;
          } // if
        } // foreach
      } else {
        if(!in_array($rel_path, $this->writable_files)) {
          $this->writable_files[] = $rel_path;
        } // if
      } // if
    } // addWritableFile
    
    // ---------------------------------------------------
    //  Validation
    // ---------------------------------------------------
    
    /**
     * Validation log
     *
     * @var array
     */
    protected $validation_log = array();
    
    /**
     * Validate environment installation
     * 
     * @return boolean
     */
    function validateEnvironment() {
      $this->cleanUpValidationLog();
      
      // Validate PHP version and Zend Engine compatibility
      $php_version = PHP_VERSION;
      
      if(version_compare($php_version, $this->min_php_version) == -1) {
        $this->validationLogError("Minimum PHP version required in order to run activeCollab is PHP $this->min_php_version. Your PHP version: $php_version");
      } elseif(version_compare(PHP_VERSION, '5.1') == -1) {
        $this->validationLogWarning("Your PHP version is $php_version. Recommended version is PHP $this->recommended_php_version or later");
      } else {
        $this->validationLogOk("Your PHP version is $php_version");
      } // if
      
      // Validate safe mode
      if(ini_get('safe_mode')) {
        $this->validationLogWarning('PHP safe mode is On', 'This feature has been DEPRECATED as of PHP 5.3.0. Relying on this feature is highly discouraged.');
      } else {
        $this->validationLogOk('PHP safe mode is turned Off');
      } // if
      
      // Validate Zend Engine 1 compatibility mode
      if(ini_get('zend.ze1_compatibility_mode')) {
        $this->validationLogError('zend.ze1_compatibility_mode is set to On', 'This feature has been DEPRECATED and REMOVED as of PHP 5.3.0.');
      } else {
        $this->validationLogOk('zend.ze1_compatibility_mode is turned Off');
      } // if

      // Check for eAccelerator
      if(extension_loaded('eAccelerator') && ini_get('eaccelerator.enable')) {
        $this->validationLogError('eAccelerator extension was found', 'System is not compatible with eAccelerator opcode cache. Please disable it for this folder or use APC instead');
      } // if

      // Check for XCache
      if(extension_loaded('XCache') && ini_get('xcache.cacher')) {
        $this->validationLogError('XCache extension was found', 'System is not compatible with XCache opcode cache. Please disable it for this folder or use APC instead');
      } // if

      // Check for zlib.output_compression
      if (extension_loaded('zlib') && !(ini_get('zlib.output_compression'))) {
        $this->validationLogWarning('zlib.output_compression is turned off. Please turn it on for faster server/browser communication');
      } // if

      // Check memory limit
      if($this->min_memory > 0) {
        $memory_limit = php_config_value_to_bytes(ini_get('memory_limit'));

        $formatted_memory_limit = $memory_limit == -1 ? 'unlimited' : format_file_size($memory_limit);

        if($memory_limit === -1 || $memory_limit >= ($this->min_memory * 1024 * 1024)) {
          $this->validationLogOk('Your memory limit is ' . $formatted_memory_limit);
        } else {
          $this->validationLogError('Your memory is too low to complete the installation. Minimal value is ' . $this->min_memory . 'MB, and you have it set to ' . $formatted_memory_limit);
        } // if
      } // if
      
      // Validate required PHP extensions
      foreach($this->required_php_extensions as $extension) {
        if(extension_loaded($extension)) {
          $this->validationLogOk("Required extension '$extension' found");
        } else {
          $this->validationLogError("Required extension '$extension' not found");
        } // if
      } // foreach
      
      // Validate recommended PHP extensions
      foreach($this->recommended_php_extensions as $extension => $explanation) {
        if(extension_loaded($extension)) {
          $this->validationLogOk("Recommended extension '$extension' found");
        } else {
          $this->validationLogWarning("Recommended extension '$extension' not found", "'$extension' is used for $explanation");
        } // if
      } // foreach
      
      // Validate folders
      if(is_array($this->writable_folders)) {
        foreach($this->writable_folders as $relative_folder_path) {
          $check_this = realpath(ROOT . "/../$relative_folder_path");
          
          if(is_dir($check_this) && folder_is_writable($check_this)) {
            $this->validationLogOk("/$relative_folder_path folder is writable");
          } else {
            $this->validationLogError("/$relative_folder_path folder is not writable");
          } // if
        } // foreach
      } // if

      // Validate files
      if(is_array($this->writable_files)) {
        foreach($this->writable_files as $relative_file_path) {
          $check_this = realpath(ROOT . "/../$relative_file_path");

          if(is_file($check_this) && file_is_writable($check_this)) {
            $this->validationLogOk("/$relative_file_path file is writable");
          } else {
            $this->validationLogError("/$relative_file_path file is not writable");
          } // if
        } // foreach
      } // if
      
      return $this->everythingValid();
    } // validateEnvironment
    
    /**
     * Return database parameters array
     * 
     * @param array $from
     * @return array
     */
    function getDatabaseParams($from) {
      $params = isset($from['database']) && is_array($from['database']) ? $from['database'] : array();
          
      if(!isset($params['host'])) {
        $params['host'] = '';
      } // if
      
      if(!isset($params['user'])) {
        $params['user'] = '';
      } // if
      
      if(!isset($params['pass'])) {
        $params['pass'] = '';
      } // if
      
      if(!isset($params['name'])) {
        $params['name'] = '';
      } // if
      
      if(!isset($params['prefix'])) {
        $params['prefix'] = '';
      } // if
      
      return $params;
    } // getDatabaseParams
    
    /**
     * Validate database connection parameters
     * 
     * @param array $database_params
     * @return boolean
     */
    function validateDatabase($database_params) {
      $this->cleanUpValidationLog();
      
      $database_host = $database_params['host']; 
      $database_user = $database_params['user'];
      $database_pass = $database_params['pass']; 
      $database_name = $database_params['name'];
      
      if($database_host && $database_user && $database_name) {
        $link = mysql_connect($database_host, $database_user, $database_pass);
        
        if($link) {
          $this->validationLogOk("Connected to database as $database_user@$database_host (using password: " . (empty($database_pass) ? 'No' : 'Yes') . ")");
          
          $mysql_version = mysql_get_server_info($link);
        
          if(version_compare($mysql_version, '5.0') >= 0) {
            $this->validationLogOk("MySQL version is $mysql_version");
            
            // Check if we have the database created
            if(mysql_select_db($database_name)) {
              $this->validationLogOk("Database '$database_name' selected");
            } else {
              $this->validationLogError("Failed to select '$database_name' database", "Make sure that database '$database_name' exists and that $database_user@$database_host can access it");
            } // if
            $have_inno = $this->checkHaveInno($link);
            
            if($have_inno) {
              $this->validationLogOk("InnoDB support available");
            } else {
              $this->validationLogWarning("InnoDB support not available", 'Although ' . AngieApplication::getName() . ' can use MyISAM storage engine InnoDB is HIGHLY recommended!');
            } // if
          } else {
            $this->validationLogError("MySQL5 or later is required. Your MySQL version is $mysql_version");
          } // if
        } else {
          $this->validationLogError('Failed to connect to database', "Failed to connect to database as $database_user@$database_host (using password: " . (empty($database_pass) ? 'No' : 'Yes') . ")");
        } // if
      } else {
        $this->validationLogError('Database connection parameters are not provided');
      } // if
      
      return $this->everythingValid();
    } // validateDatabase
    
    /**
     * Return admin parameters
     * 
     * @param array $from
     * @return array
     */
    function getAdminParams($from) {
      $params = isset($from['admin']) && is_array($from['admin']) ? $from['admin'] : array();
          
      if(!isset($params['email'])) {
        $params['email'] = '';
      } // if
      
      if(!isset($params['pass'])) {
        $params['pass'] = '';
      } // if
      
      return $params;
    } // getAdminParams
    
    /**
     * Return license parameters
     * 
     * @param array $from
     * @return array
     */
    function getLicenseParams($from) {
      $params = isset($from['license']) && is_array($from['license']) ? $from['license'] : array();
          
      if(!array_key_exists('accepted', $params)) {
        $params['accepted'] = false;
      } // if

      if(!array_key_exists('help_improve', $params)) {
        $params['help_improve'] = false;
      } // if
      
      return $params;
    } // getLicenseParams
    
    /**
     * Validate system installation
     * 
     * @param array $database_params
     * @param array $admin_params
     * @param array $licensing_params
     * @param array $additional_params
     * @return bool
     */
    function validateInstallation($database_params, $admin_params, $licensing_params, $additional_params = null) {
      $this->cleanUpValidationLog();
      
      $admin_email = array_var($admin_params, 'email', null, true);
      $admin_password = array_var($admin_params, 'pass', null, true);
      
      $license_accepted = $licensing_params['accepted'];
      
      // We have all the data
      if($admin_email && is_valid_email($admin_email) && $admin_password && $license_accepted) {
        $database_host = $database_params['host']; 
        $database_user = $database_params['user'];
        $database_pass = $database_params['pass']; 
        $database_name = $database_params['name'];
        $table_prefix = $database_params['prefix'];

        defined('TABLE_PREFIX') or define('TABLE_PREFIX', $table_prefix);
        
        // Lets connect to database
        try {
          DB::setConnection('default', new MySQLDBConnection($database_host, $database_user, $database_pass, $database_name, false, 'utf8'));
        } catch(Exception $e) {
          $this->validationLogError('Failed to connect to database');
          return false;
        } // try
        
        // InnoDB support
        $have_inno = $this->checkHaveInno();
        
        // Initialize and load model
        try {
          // triggered by tasks/install.php
          if (isset($additional_params) && isset($additional_params['force_config_options']) && isset($additional_params['force_config_options']['APPLICATION_MODULES'])) {
            $modules_to_install = explode(",", $additional_params['force_config_options']['APPLICATION_MODULES']);
          } elseif (defined(APPLICATION_MODULES)) { // config.empty.php
            $modules_to_install = explode(",", APPLICATION_MODULES);
          } else { // default
            $modules_to_install = $this->getModulesToInstall();
          } // if

          AngieApplicationModel::load(explode(',', APPLICATION_FRAMEWORKS), $modules_to_install);
          AngieApplicationModel::init();
          
          $this->validationLogOk(AngieApplication::getName() . ' tables have been created and initial data loaded');
        } catch(Exception $e) {
          $this->validationLogError('Failed to build model. Reason: ' . $e->getMessage());
          return false;
        } // try
        
        // Administrator
        try {
          $this->createAdministrator($admin_email, $admin_password, $admin_params);
          $this->validationLogOk("'$admin_email' administration account has been created");
        } catch(Exception $e) {
          $this->validationLogError('Failed to create administrator. Reason: ' . $e->getMessage());
          return false;
        } // try

        // Set help improve option
        $this->setConfigOption('help_improve_application', (boolean) $licensing_params['help_improve']);
        
        // Update history
        try {
          if(!defined('APPLICATION_VERSION')) {
            require_once CONFIG_PATH . '/version.php';
          } // if
          
          $this->registerVersion(APPLICATION_VERSION);
          $this->validationLogOk('Application version has been logged');
        } catch(Exception $e) {
          $this->validationLogError('Failed to log application version. Reason: ' . $e->getMessage());
          return false;
        } // try

        // Create configuration file. We reseted $admin_params values in previous step, so we are rebuilding the
        $this->createConfigFile(CONFIG_PATH . '/config.php', $this->getConfigOptions($database_params, array(
          'email' => $admin_email,
          'pass' => $admin_password,
        ), $licensing_params, $additional_params));
        $this->validationLogOk("Configuration file has been created");
        
      // Invalid input data
      } else {
        if(empty($admin_email)) {
          $this->validationLogError("Email address for administrator's account not provided");
        } elseif(!is_valid_email($admin_email)) {
          $this->validationLogError("Email address for administrator's account is not valid");
        } // if
        
        if(empty($admin_password)) {
          $this->validationLogError("Password for administratior's account not provided");
        } // if
        
        if(empty($license_accepted)) {
          $this->validationLogError(AngieApplication::getName() . " can't be used unless you accept the license agreement");
        } // if
      } // if
      
      return $this->everythingValid();
    } // validateInstallation

    /**
     * Returns true if MySQL supports InnoDB
     *
     * This function can use MySQL resource, or DB::execute
     *
     * @param resource $link
     * @return bool
     */
    function checkHaveInno($link = null) {

      // We can't use DB just yet
      if(is_resource($link)) {
        if($result = mysql_query('SHOW ENGINES', $link)) {
          while($engine = mysql_fetch_assoc($result)) {
            if(strtolower($engine['Engine']) == 'innodb' && in_array(strtolower($engine['Support']), array('yes', 'default'))) {
              return true;
            } // if
          } // while
        } // if

      // We have a connection so we can use DB
      } else {
        $engines = DB::execute('SHOW ENGINES');

        if($engines) {
          foreach($engines as $engine) {
            if(strtolower($engine['Engine']) == 'innodb' && in_array(strtolower($engine['Support']), array('yes', 'default'))) {
              return true;
            } // if
          } // foreach
        } // if
      } // if

      return false;
    } // checkHaveInno

    /**
     * Create administrator user account
     *
     * This function returns administrator's user ID
     *
     * @param string $email
     * @param string $password
     * @return integer
     */
    function createAdministrator($email, $password) {
      $users_table = TABLE_PREFIX . 'users';

      $admin_id = (integer) DB::executeFirstCell("SELECT id FROM $users_table WHERE type = 'Administrator'");

      // We already have an administrator, update the account
      if($admin_id) {
        DB::execute("UPDATE $users_table SET email = ?, password = ?, password_hashed_with = ? WHERE id = ?", $email, base64_encode(pbkdf2($password, APPLICATION_UNIQUE_KEY, 1000, 40)), 'pbkdf2', $admin_id);

        // Add a new user account
      } else {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'users (type, state, email, password, password_hashed_with, created_on, created_by_id) VALUES (?, ?, ?, ?, ?, ?, ?)', 'Administrator', 3, $email, base64_encode(pbkdf2($password, APPLICATION_UNIQUE_KEY, 1000, 40)), 'pbkdf2', DateTimeValue::now(), 1);
        $admin_id = DB::lastInsertId();
      } // if

      return $admin_id;
    } // createAdministrator

    /**
     * Set configuration option value
     *
     * @param string $name
     * @param mixed $value
     */
    function setConfigOption($name, $value) {
      DB::execute('UPDATE ' . TABLE_PREFIX . 'config_options SET value = ? WHERE name = ?', serialize($value), $name);
    } // setConfigOption
    
    /**
     * Register version number
     * 
     * @param string $version
     */
    function registerVersion($version) {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'update_history (version, created_on) VALUES (?, UTC_TIMESTAMP())', $version);
    } // registerVersion
    
    /**
     * Create configuration file
     * 
     * @param string $config_file_path
     * @param array $params
     * @return boolean
     */
    function createConfigFile($config_file_path, $params) {
      $lines = array(
        '<?php', 
        '', 
        '  /**', 
        '   * ' . AngieApplication::getName() . ' configuration file',
        '   *',
        '   * Automatically generated by installer script on ' . date(DATE_MYSQL),
        '   */',
        '',
      );
      
      foreach($params as $k => $v) {
        $lines[] = "  const $k = " . var_export($v, true) . ';';
      } // foreach
      
      $lines[] = '';
      $lines[] = '';
      $lines[] = "    defined('CONFIG_PATH') or define('CONFIG_PATH', dirname(__FILE__));";
      $lines[] = '';
      
      $lines[] = "  require_once CONFIG_PATH . '/version.php';";
      $lines[] = "  require_once CONFIG_PATH . '/license.php';";
      $lines[] = "  require_once CONFIG_PATH . '/defaults.php';";
      
      file_put_contents($config_file_path, implode("\r\n", $lines));
    } // createConfigFile
    
    /**
     * Return ROOT_URL value
     * 
     * @return string
     */
    function getRootUrl() {
      return rtrim(AngieApplication::getRequestSchema() . dirname($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']), '/');
    } // getRootUrl

    // ---------------------------------------------------
    //  Uninstallation
    // ---------------------------------------------------

    /**
     * Remove all database tables
     */
    function uninstall() {
      $indexes = Search::getIndices();
      if($indexes) {
        foreach($indexes as $index) {
          $index->tearDown();
          $this->validationLogOk('Search index dropped: ' . $index->getName());
        } // if
      } // if

      $rows = DB::execute('SELECT name FROM ' . TABLE_PREFIX . 'modules');

      if($rows) {
        $module_names = array();
        foreach($rows as $row) {
          $module_names[] = $row['name'];
        } // foreach

        AngieApplicationModel::load(explode(',', APPLICATION_FRAMEWORKS), $module_names);

        foreach(AngieApplicationModel::getTables() as $table) {
          DB::execute('DROP TABLE ' . TABLE_PREFIX . $table->getName());
          $this->validationLogOk('Table dropped: ' . $table->getName());
        } // foreach
      } // if
    } // uninstall
    
    // ---------------------------------------------------
    //  Log
    // ---------------------------------------------------
    
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
     * @param boolean $html
     * @return string
     */
    function printValidationLog($html = true) {
      return $html ? $this->printValidationLogToHtml() : $this->printValidationLogToConsole();
    } // printValidationLog

    /**
     * Print validation log to HTML
     *
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
     * Print validation log to CLI
     *
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
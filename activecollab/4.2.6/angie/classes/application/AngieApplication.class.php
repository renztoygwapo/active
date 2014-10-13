<?php

  /**
   * Angie appliction interface
   *
   * @package angie.library.application
   */
  final class AngieApplication {
    
    // Application modes
    const IN_DEVELOPMENT = 'in_development';
    const IN_DEBUG_MODE = 'in_debug_mode';
    const IN_PRODUCTION = 'in_production';

    // Api token variable name
    const API_TOKEN_VARIABLE_NAME = 'auth_api_token';
    
    /**
     * Application specific adapter instance
     *
     * @var AngieApplicationAdapter
     */
    static private $adapter;
    
    /**
     * Load and set application adapter
     */
    static function loadAdapter() {
      $application_adapter_class = APPLICATION_NAME;
      
      $application_adapter_file = APPLICATION_PATH . "/$application_adapter_class.class.php";
      if(is_file($application_adapter_file)) {
        require_once $application_adapter_file;
        
        $application_adapter = new $application_adapter_class();
        if($application_adapter instanceof AngieApplicationAdapter) {
          self::setAdapter($application_adapter);
        } else {
          throw new InvalidInstanceError('application_adapter', $application_adapter, 'AngieApplicationAdapter');
        } // if
      } else {
        throw new FileDnxError($application_adapter_file);
      } // if
    } // loadAdapter
    
    /**
     * Return adapter
     *
     * @return AngieApplicationAdapter
     */
    static function &getAdapter() {
    	return self::$adapter;
    } // getAdapter
    
    /**
     * Set adapter
     *
     * @param AngieApplicationAdapter $value
     */
    static function setAdapter(AngieApplicationAdapter $value) {
      self::$adapter = $value;
    } // setAdapter
    
    // ---------------------------------------------------
    //  Meta information
    // ---------------------------------------------------
    
    /**
     * Return application name
     *
     * @return string
     */
    static function getName() {
      return self::$adapter instanceof AngieApplicationAdapter ? self::$adapter->getName() : 'UnknownAngieApplication';
    } // getName
    
    /**
     * Return application name
     *
     * @return string
     */
    static function getUrl() {
      return self::$adapter instanceof AngieApplicationAdapter ? self::$adapter->getUrl() : 'http://www.a51dev.com';
    } // getName
    
    /**
     * Return application version
     *
     * @return string
     */
    static function getVersion() {
      return self::$adapter->getVersion();
    } // getVersion
    
    /**
     * Returns true if current version is stable
     *
     * @return string
     */
    static function isStable() {
      return self::$adapter->isStable();
    } // isStable
    
    /**
     * Return build number
     * 
     * @return string
     */
    static function getBuild() {
      return APPLICATION_BUILD == '%APPLICATION-BUILD%' ? 'DEV' : APPLICATION_BUILD;
    } // getBuild
    
    /**
     * Return application API version
     *
     * @return string
     */
    static function getApiVersion() {
      return self::$adapter->getApiVersion();
    } // getApiVersion
    
    /**
     * Return vendor name
     */
    static function getVendor() {
      return self::$adapter->getVendor();
    } // getVendor
    
    /**
     * Return license agreement URL
     * 
     * @return string
     */
    static function getLicenseAgreementUrl() {
      return self::$adapter->getLicenseAgreementUrl();
    } // getLicenseAgreementUrl

    /**
     * Return check for updates URL
     *
     * @return string
     */
    static function getCheckForUpdatesUrl() {
      $url = self::$adapter->getCheckForUpdatesUrl();

      if($url) {
        $url .= (defined('LICENSE_KEY') && LICENSE_KEY ? LICENSE_KEY : 'MISSING') . '/' . AngieApplication::getVersion() . '/';

        if(function_exists('gzdeflate')) {
          $stats = '';

          foreach(AngieApplication::getStats() as $k => $v) {
            $stats .= "$k:$v\n";
          } // foreach

          $url .= rtrim(strtr(base64_encode(gzdeflate(trim($stats), 9)), '+/', '-_'), '=');
        } else {
          $url = extend_url($url . 'raw', AngieApplication::getStats());
        } // if
      } // if

      return $url;
    } // getCheckForUpdatesUrl

    /**
     * Return download updates URL
     *
     * @return string
     */
    static function getDownloadUpdateUrl() {
      return self::$adapter->getDownloadUpdateUrl();
    } // getDownloadUpdateUrl

    /**
     * Array of cached stats
     *
     * @var array
     */
    static private $stats = false;

    /**
     * Return anonymous usage stats
     *
     * @return array|bool
     */
    static function getStats() {
      if(self::$stats === false) {
        $modules = AngieApplication::getEnabledModuleNames();

        unset($modules[array_search('system', $modules)]);

        self::$stats = array(
          'php' => PHP_VERSION,
          'sql' => DB::getConnection() instanceof DBConnection ? DB::getConnection()->getServerVersion() : 'unknown',
          'url' => ROOT_URL,
          'exts' => implode(',', get_loaded_extensions()),
          'mods' => implode(',', $modules),
        );

        if(ConfigOptions::getValue('help_improve_application')) {
          EventsManager::trigger('on_extra_stats', array(&self::$stats));
        } // if
      } // if

      return self::$stats;
    } // getStats

    /**
     * Cached use package value
     *
     * @var boolean
     */
    private static $use_package = null;

    /**
     * Returns true if we should use PHAR package instead of unpackaged files
     *
     * @return bool
     */
    static function usePackage() {
      if(self::$use_package === null) {
        self::$use_package = !(defined('USE_UNPACKED_FILES') && USE_UNPACKED_FILES);
      } // if

      return self::$use_package;
    } // usePackage
    
    // ---------------------------------------------------
    //  Bootstrapping
    // ---------------------------------------------------
    
    /**
     * Load system so it can properly handle HTTP request
     */
    static function bootstrapForHttpRequest() {
      if(!(self::$adapter instanceof AngieApplicationAdapter)) {
        self::loadAdapter();
      } // if
      
      self::initEnvironment();
      self::initHttpEnvironment();
      
      if(self::isInstalled()) {
        self::initCookies();
        self::initCache();
        self::initDatabaseConnection();
        self::initSmarty();
        self::initFrameworks();
	      // @todo test init firewall
	      // ------------------------
	      self::initFirewall();
	      // ------------------------
        self::initModules();
        self::initRouter();
        self::initEventsManager();
        self::initAuthentication();
        self::initGlobalization();
        self::initMailer();
        self::initLoadedWidgets();

        if(self::isFirstRun()) {
          self::onFirstRun();
        } // if
      } else {
        self::initInstaller();
      } // if
    } // bootstrapForHttpRequest

    /**
     * Bootstrap for upgrade
     */
    static function bootstrapForUpgrade() {
      if(!(self::$adapter instanceof AngieApplicationAdapter)) {
        self::loadAdapter();
      } // if

      self::initEnvironment();
      self::initHttpEnvironment();

      if(self::isInstalled()) {
        self::initDatabaseConnection();
      } // if

      self::includeLatestUpgradeClasses();

      AngieApplicationUpgrader::init();
    } // bootstrapForUpgrade

    /**
     * Include Latest Upgrade Classes
     */
    static function includeLatestUpgradeClasses() {

      // ---------------------------------------------------
      //  Get latest version number
      // ---------------------------------------------------

      $latest_version = null;

      if($h = @opendir(ROOT)) {
        while(false !== ($version = readdir($h))) {
          if(substr($version, 0, 1) == '.') {
            continue;
          } // if

          if(self::isValidVersionNumber($version)) {
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

      // ---------------------------------------------------
      //  Include upgrade utilities from the latest
      //  available version
      // ---------------------------------------------------

      /**
       * Require upgrader files from given path
       *
       * @param string $angie_path
       */
      $require_upgrader_files = function($angie_path) {
        require_once "$angie_path/classes/application/upgrader/AngieApplicationUpgrader.class.php";
        require_once "$angie_path/classes/application/upgrader/AngieApplicationUpgraderAdapter.class.php";
        require_once "$angie_path/classes/application/upgrader/AngieApplicationUpgradeScript.class.php";
        require_once "$angie_path/classes/application/upgrader/AngieApplicationUpgradeScript.class.php";
        require_once "$angie_path/classes/application/migrations/AngieModelMigration.class.php";
        require_once "$angie_path/classes/application/migrations/AngieModelMigrationDiscoverer.class.php";
      }; // require_upgrader_files

      if($latest_version) {
        $require_upgrader_files(ROOT . "/$latest_version/angie");
      } else {
        $require_upgrader_files(ANGIE_PATH);
      } // if
    } // includeLatestUpgradeClasses

    /**
     * Returns true if $version is a valid angie application version number
     *
     * @param string $version
     * @return boolean
     */
    static function isValidVersionNumber($version) {
      if(strpos($version, '.') !== false) {
        $parts = explode('.', $version);

        if(count($parts) == 3) {
          foreach($parts as $part) {
            if(!is_numeric($part)) {
              return false;
            } // if
          } // foreach

          return true;
        } // if
      } // if

      return false;
    } // isValidVersionNumber

    /**
     * Initialize system for API subscription creation
     */
    static function bootstrapForApiSubscription() {
      if(!(self::$adapter instanceof AngieApplicationAdapter)) {
        self::loadAdapter();
      } // if

      self::initEnvironment();
      self::initHttpEnvironment();

      if(self::isInstalled()) {
        self::initCache();
        self::initDatabaseConnection();
        self::initFrameworks();
        self::initModules();
        self::initRouter();
        self::initEventsManager();
      } else {
        self::initInstaller();
      } // if
    } // bootstrapForApiSubscription

    /**
     * Load system so it can properly handle CLI request (scheduled task etc)
     *
     * @param Output $output
     * @param boolean $init_router
     * @param boolean $init_events
     * @param boolean $init_mailer
     */
    static function bootstrapForCommandLineRequest($output = null, $init_router = false, $init_events = true, $init_mailer = false) {
      if(!(self::$adapter instanceof AngieApplicationAdapter)) {
        self::loadAdapter();
      } // if

      self::initEnvironment();
      self::initCache();
      self::initDatabaseConnection();
      self::initSmarty();
      self::initFrameworks();
      self::initModules();

      if($init_router) {
        self::initRouter();
      } // if

      if($init_events) {
        self::initEventsManager();
      } // if

      if($init_mailer) {
        self::initMailer();
      } // if
    } // bootstrapForCommandLineRequest

    /**
     * Bootstrap system to run automated tests
     *
     * @param Output $output
     */
    static function bootstrapForTest(Output $output) {
      if(!(self::$adapter instanceof AngieApplicationAdapter)) {
        self::loadAdapter();
      } // if

      self::initEnvironment();
      self::initCache(true);
      self::initDatabaseConnection();
      self::initSmarty();
      self::initModel('test');
      self::initFrameworks();
      self::initModules();
      self::initRouter();
      self::initEventsManager();
    } // bootstrapForTest

    /**
     * Initialize PHP environment
     */
    static function initEnvironment() {
      // CLI can set start the session earlier, let's avoid warnings
      if (version_compare(PHP_VERSION, "5.4", ">=")) {
        if (!(session_status() == PHP_SESSION_ACTIVE)) {
          session_start();
        } // if
      } else {
        if (session_id() == '') {
          session_start();
        } // if
      } // if

      set_include_path('');

      ini_set('magic_quotes_runtime', false); // don't break Smarty!

      error_reporting(E_ALL);

      if(self::isInProduction()) {
        ini_set('display_errors', 0);
      } else {
        ini_set('display_errors', 1);
      } // if

      register_shutdown_function(array('AngieApplication', 'shutdown'));
      set_error_handler(array('AngieApplication', 'handleError'));
      set_exception_handler(array('AngieApplication', 'handleFatalError'));
    } // initEnvironment

    /**
     * Extracted path info
     *
     * @var string
     */
    static private $path_info;

    /**
     * Extracted query string
     *
     * @var string
     */
    static private $query_string;

    /**
     * True if client can accept zipped content
     *
     * @var bool
     */
    static private $accepts_gzip = false;

    /**
     * Initialize HTTP environment
     */
    static function initHttpEnvironment() {
      if(function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
        array_stripslashes($_GET);
        array_stripslashes($_POST);
        array_stripslashes($_COOKIE);
      } // if

      if(defined('FORCE_QUERY_STRING') && FORCE_QUERY_STRING) {
        self::$path_info = array_var($_GET, 'path_info');

        // We are using query string to pass path info here. We need to get
        // original query string from REQUEST_URI
        if(PATH_INFO_THROUGH_QUERY_STRING && isset($_SERVER['QUERY_STRING'])) {
          self::$query_string = $_SERVER['QUERY_STRING'];
        } else {
          $request_uri = array_var($_SERVER, 'REQUEST_URI');
          if(($pos = strpos($request_uri, '?')) !== false) {
            self::$query_string = substr($request_uri, $pos + 1);
          } // if
        } // if
      } else {
        if(isset($_SERVER['PATH_INFO'])) {
          self::$path_info = $_SERVER['PATH_INFO'];
        } // if

        if(empty(self::$path_info) && isset($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO']) {
          self::$path_info = $_SERVER['ORIG_PATH_INFO'];
        } // if

        if(($pos = strpos(self::$path_info, 'index.php')) !== false) {
          self::$path_info = substr(self::$path_info, $pos + 10);
        } // if

        self::$query_string = array_var($_SERVER, 'QUERY_STRING');
      } // if

      self::$accepts_gzip = isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;
    } // initHttpEnvironment

    /**
     * Initialize cookie handling serivce
     */
    static function initCookies() {
      Cookies::init(COOKIE_PREFIX, COOKIE_PATH, COOKIE_DOMAIN, COOKIE_SECURE);
    } // initCookies

    /**
     * Initialize caching service
     *
     * @param boolean $clear
     */
    static function initCache($clear = false) {
      AngieApplication::cache()->setLifetime(CACHE_LIFETIME);

      if($clear) {
        AngieApplication::cache()->clear();
      } // if
    } // initCache

    /**
     * Initialize database connection
     */
    static function initDatabaseConnection() {
      try {
        DB::setConnection('default', new MySQLDBConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PERSIST, DB_CHARSET));
      } catch(Exception $e) {
        if(self::isInProduction()) {
          trigger_error('Failed to connect to database');
        } else {
          dump_error($e);
        } // if
      } // try
    } // initDatabaseConnection

    /**
     * Global smarty instance
     *
     * @var Smarty
     */
    static private $smarty;

    /**
     * Initialize Smarty service
     */
    static function initSmarty() {
      self::$smarty =& SmartyForAngie::getInstance();

      self::$smarty->setCompileDir(COMPILE_PATH);
      self::$smarty->setCacheDir(ENVIRONMENT_PATH . '/cache');
      self::$smarty->compile_check = true;
      self::$smarty->registerFilter('variable', 'clean'); // {$foo nofilter}
    } // initSmarty

    /**
     * Initialize application model
     *
     * @param string $environment
     */
    static function initModel($environment = null) {
      if(AngieApplicationModel::isEmpty()) {
        AngieApplicationModel::load(explode(',', APPLICATION_FRAMEWORKS), explode(',', APPLICATION_MODULES));
      } // if

      AngieApplicationModel::drop();
      AngieApplicationModel::init($environment);
    } // initModel

    /**
     * Array of loaded frameworks and modules
     *
     * @var array
     */
    static private $loaded_frameworks_and_modules = array();

    /**
     * Flag that is set to true when frameworks are initialized
     *
     * @var boolean
     */
    static private $frameworks_initialized = false;

    /**
     * Flag that is set when modules are initialized
     *
     * @var boolean
     */
    static private $modules_initialized = false;

    /**
     * Initialize application frameworks
     */
    static function initFrameworks() {
      $frameworks = self::getFrameworks();

      if(is_foreachable($frameworks)) {
        foreach($frameworks as $framework) {
          self::$loaded_frameworks_and_modules[$framework->getName()] = $framework; // Set as loaded before we call init.php

          $path = $framework->getPath();

          if(self::$smarty instanceof Smarty) {
            self::$smarty->addPluginsDir("$path/helpers");
          } // if

          require_once $path . '/init.php';
        } // foreach
      } // if

      self::$frameworks_initialized = true;
    } // initFrameworks

    /**
     * Array of modules that are blocked because of autoload errors
     *
     * @var array
     */
    static private $blocked_for_autoload_error = array();

    /**
     * Returns true if $module is blocked because of auto-load error
     *
     * @param AngieModule $module
     * @return bool
     */
    static function isBlockedForAutoloadError(AngieModule $module) {
      return in_array($module->getName(), self::$blocked_for_autoload_error);
    } // isBlockedForAutoloadError

    /**
     * Initialize installed application modules
     */
    static function initModules() {
      self::getEnabledModules();

      foreach(self::$enabled_modules as $enabled_module_key => &$module) {
        self::$loaded_frameworks_and_modules[$module->getName()] = $module; // Set as loaded before we call init.php

        $path = $module->getPath();

        try {
          require_once $path . '/init.php';
        } catch(SetForAutoloadError $e) {
          unset(self::$loaded_frameworks_and_modules[$module->getName()]);
          unset(self::$enabled_modules[$enabled_module_key]);

          self::$blocked_for_autoload_error[] = $module->getName();
        } catch(Exception $e) {
          throw $e;
        } // try

        if(self::$smarty instanceof Smarty) {
          self::$smarty->addPluginsDir($path . '/helpers');
        } // if
      } // foreach

      unset($module);

      self::$modules_initialized = true;
    } // initModules

    /**
     * Initialize route
     */
    static function initRouter() {
      Router::init(self::$frameworks, self::$enabled_modules);
    } // initRouter

    /**
     * Init events manager
     */
    static function initEventsManager() {
      EventsManager::init(self::$frameworks, self::$enabled_modules);
    } // initEventsManager

    /**
     * Initialize authentication service
     */
    static function initAuthentication() {
      if(self::getModule('authentication') instanceof AngieFramework) {
        Authentication::useProvider(AUTH_PROVIDER, false);

        $raw_token = false;
        if(FORCE_QUERY_STRING) {
          if(self::$query_string) {
            $query_string_params = parse_string(self::$query_string);
            if(isset($query_string_params[self::API_TOKEN_VARIABLE_NAME])) {
              $raw_token = $query_string_params[self::API_TOKEN_VARIABLE_NAME];
            } // if
          } // if
        } else {
          $raw_token = isset($_GET[self::API_TOKEN_VARIABLE_NAME]) ? $_GET[self::API_TOKEN_VARIABLE_NAME] : false;
        } // if

        // If request comes from api.php, token is required
        if(defined('ANGIE_API_CALL') && ANGIE_API_CALL && empty($raw_token)) {

          // Make sure that we give proper response to is alive requests
          if(isset($_GET['check_if_alive']) && $_GET['check_if_alive']) {
            header('HTTP/1.1 200 OK');
            header('Content-type: text/xml');

            print '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
            print '<api_is_alive>yes</api_is_alive>';

          // API requests but no token? Forbidden!
          } else {
            header('HTTP/1.1 403 Forbidden');
            print '<h1>HTTP/1.1 403 Forbidden</h1>';
          } // if

          die();
        } // if

        // Handle token based authentication
        if($raw_token !== false) {
          list($user_id, $token) = explode('-', $raw_token);

          $subscription = $user_id && $token ? ApiClientSubscriptions::findByToken($token) : null;

          if($subscription instanceof ApiClientSubscription && $subscription->getIsEnabled() && $subscription->getUser() instanceof User && ($subscription->getUser()->getId() == $user_id)) {
            if(!$subscription->getUser()->isApiUser() || ($subscription->getIsReadOnly() && count($_POST) > 0)) {
              header('HTTP/1.1 403 Forbidden');
              die('<h1>HTTP/1.1 403 Forbidden</h1>');
            } // if

            $subscription->setLastUsedOn(new DateTimeValue());
            $subscription->save();

            Authentication::getProvider()->logUserIn($subscription->getUser(), array(
              'silent' => true
            ));

            Authentication::setApiSubscription($subscription); // Remember subscription that is used to make this call
            defined('ANGIE_API_CALL') or define('ANGIE_API_CALL', true); // Make sure that we mark this request as made through the API
          } else {
	          // @todo security logs invalid token detected
	          FwSecurityLogs::countFailedToken();
            header('HTTP/1.1 403 Forbidden');
            die('<h1>HTTP/1.1 403 Forbidden</h1>');
          } // if
        } // if

        Authentication::getProvider()->initialize(array(
          'sid_prefix' => self::getName(),
          'secret_key' => self::$adapter->getUniqueKey(),
        ));
      } else {
        throw new Error('Authentication framework not loaded');
      } // if
    } // initAuthentication

    /**
     * Initialize globalization service
     */
    static function initGlobalization() {
      if(self::getModule('globalization') instanceof AngieFramework) {
        $logged_user =& Authentication::getLoggedUser();

        $language = Globalization::setCurrentLocaleByUser($logged_user);
				self::$smarty->assign('current_language', $language);
      } else {
        throw new Error('Globalization framework not loaded');
      } // if
    } // initGlobalization

    /**
     * Initialize mailer
     */
    static function initMailer() {
    	AngieApplication::mailer()->getAdapter(); // Load
    	AngieApplication::mailer()->getDefaultSender(); // Load
    	AngieApplication::mailer()->setDecorator(new OutgoingMessageDecorator());
    } // initMailer

    /**
     * Include core installer files
     */
    static function includeCoreInstallerFiles() {
      require_once ANGIE_PATH . '/classes/application/installer/AngieApplicationInstaller.class.php';
      require_once ANGIE_PATH . '/classes/application/installer/AngieApplicationInstallerAdapter.class.php';
    } // includeCoreInstallerFiles

    /**
     * Initialize installer
     *
     * @param string $adapter_class
     * @param string $adapter_class_path
     */
    static function initInstaller($adapter_class = null, $adapter_class_path = null) {
      AngieApplication::includeCoreInstallerFiles();
      AngieApplicationInstaller::init($adapter_class, $adapter_class_path);
    } // initInstaller

    /**
     * Initialize list of loaded widgets
     */
    static function initLoadedWidgets() {
      if(isset($_GET['loaded_widgets']) && $_GET['loaded_widgets']) {
        $loaded_widgets = explode(',', $_GET['loaded_widgets']);

        if(count($loaded_widgets)) {
          self::$loaded_widgets = $loaded_widgets;
        } // if
      } // if
    } // initLoadedWidgets

	  /**
	   * Initialize firewall
	   */
	  static function initFirewall() {
		  AngieApplication::firewall()->initialize();
	  } // initFirewall

    // ---------------------------------------------------
    //  Delegates
    // ---------------------------------------------------

    /**
     * Place where we'll keep delegate instances
     *
     * @var array
     */
    static private $delegate_instances = array();

    /**
     * Return a particular delegate
     *
     * @param $delegate
     * @return AngieDelegate
     * @throws InvalidParamError
     * @throws InvalidInstanceError
     */
    static private function &getDelegate($delegate) {
      if(!isset(self::$delegate_instances[$delegate]) || !(self::$delegate_instances[$delegate] instanceof AngieDelegate)) {
        $delegate_class = 'Angie' . Inflector::camelize($delegate) . 'Delegate';

        if(class_exists($delegate_class, true)) {
          $delegate_instance = new $delegate_class;
          if($delegate_instance instanceof AngieDelegate) {
            self::$delegate_instances[$delegate] = $delegate_instance;
          } else {
            throw new InvalidInstanceError('delegate_instance', $delegate_instance, 'AngieDelegate');
          } // if
        } else {
          throw new InvalidParamError('delegate', $delegate, "Delegate '$delegate' not found");
        } // if
      } // if

      return self::$delegate_instances[$delegate];
    } // getDelegate

    /**
     * Return behavior delegate instance
     *
     * @return AngieBehaviourDelegate
     */
    static function &behaviour() {
      return self::getDelegate('behaviour');
    } // &behaviour

    /**
     * Return cache delegate instance
     *
     * @return AngieCacheDelegate
     */
    static function &cache() {
      return self::getDelegate('cache');
    } // &cache

    /**
     * Return elastica delegate
     *
     * @return AngieElasticaDelegate
     */
    static function &elastica() {
      return self::getDelegate('elastica');
    } // elastica

    /**
     * Return experiments delegate
     *
     * @return AngieExperimentsDelegate
     */
    static function &experiments() {
      return self::getDelegate('experiments');
    } // experiments

    /**
     * Return incoming mail delegate instance
     *
     * @return AngieIncomingMailDelegate
     */
    static function &incomingMail() {
      return self::getDelegate('incoming_mail');
    } // &incomingMail

    /**
     * Return launcher delegate instance
     *
     * @return AngieLauncherDelegate
     */
    static function &launcher() {
      return self::getDelegate('launcher');
    } // &launcher

    /**
     * Return mailer delegate instance
     *
     * @return AngieMailerDelegate
     */
    static function &mailer() {
      return self::getDelegate('mailer');
    } // &mailer

    /**
     * Return describe delegate instance
     *
     * @return AngieDescribeDelegate
     */
    static function &describe() {
      return self::getDelegate('describe');
    } // &describe

    /**
     * Return notifications delegate instance
     *
     * @return AngieNotificationsDelegate
     */
    static function &notifications() {
      return self::getDelegate('notifications');
    } // &notifications

    /**
     * Return migration delegate
     *
     * @return AngieMigrationDelegate
     */
    static function &migration() {
      return self::getDelegate('migration');
    } // &migration

    /**
     * Return help delegate instance
     *
     * @return AngieHelpDelegate
     */
    static function &help() {
      return self::getDelegate('help');
    } // &firewall

	  /**
	   * Return firewall delegate instance
	   *
	   * @return AngieFirewallDelegate
	   */
	  static function &firewall() {
		  return self::getDelegate('firewall');
	  } // &firewall

    // ---------------------------------------------------
    //  First run
    // ---------------------------------------------------

    /**
     * Returns true if this is the first application run
     *
     * @return boolean
     */
    static private function isFirstRun() {
      $first_run_on = DB::executeFirstRow('SELECT value FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', 'first_run_on');

      if(is_array($first_run_on) && array_key_exists('value', $first_run_on)) {
        return empty($first_run_on['value']) || unserialize($first_run_on['value']) == null;
      } else {
        return false; // Old version, does not support first run on
      } // if
    } // isFirstRun

    /**
     * On first run initialization
     */
    static private function onFirstRun() {
      self::rebuildAssets();
      self::rebuildLocalization();

      if(self::isFrameworkLoaded('search')) {
        Search::initialize();

        if(self::isFrameworkLoaded('help')) {
          AngieApplication::help()->buildSearchIndex(Search::getIndex('help'));
        } // if
      } // if

      self::getAdapter()->onFirstRun();

      DB::execute('UPDATE ' . TABLE_PREFIX . 'config_options SET value = ? WHERE name = ?', serialize(DateTimeValue::now()->toMySQL()), 'first_run_on');
    } // onFirstRun

    // ---------------------------------------------------
    //  Application Mode
    // ---------------------------------------------------

    /**
     * Returns true if application is in development mode
     *
     * @return boolean
     */
    static function isInDevelopment() {
      return defined('APPLICATION_MODE') ? APPLICATION_MODE == self::IN_DEVELOPMENT : true;
    } // isInDevelopment

    /**
     * Returns true if application is in debug mode
     *
     * @return boolean
     */
    static function isInDebugMode() {
      return defined('APPLICATION_MODE') && APPLICATION_MODE == self::IN_DEBUG_MODE;
    } // isDebugging

    /**
     * Returns true if application is in production mode
     *
     * @return boolean
     */
    static function isInProduction() {
      return defined('APPLICATION_MODE') && APPLICATION_MODE == self::IN_PRODUCTION;
    } // isInProduction

    /**
     * Returns true if application is in on demand mode
     *
     * @return boolean
     */
    static function isOnDemand() {
      return defined('IS_ON_DEMAND') && IS_ON_DEMAND;
    } // isOnDemand

    // ---------------------------------------------------
    //  Request Handling
    // ---------------------------------------------------

    /**
     * Handle HTTP request
     */
    static function handleHttpRequest() {
      self::$adapter->handleHttpRequest(self::$path_info, self::$query_string);
    } // handleHttpRequest

    /**
     * Return path info
     *
     * @return string
     */
    static function getRequestPathInfo() {
      return self::$path_info;
    } // getRequestPathInfo

    /**
     * Return query string
     *
     * @return string
     */
    static function getRequestQueryString() {
      return self::$query_string;
    } // getRequestQueryString

    /**
     * Return user IP address
     *
     * @return string
     */
    static function getVisitorIp() {
	    return array_var($_SERVER, 'REMOTE_ADDR', '127.0.0.1');
    } // getVisitorIp

    /**
     * Return visitor's user agent string
     *
     * @return string
     */
    static function getVisitorUserAgent() {
      return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    } // getVisitorUserAgent

    /**
     * Return request schema (http:// or https://)
     *
     * @return string
     */
    static function getRequestSchema() {
      return ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || (isset($_SERVER['HTTP_X_REAL_PORT']) && $_SERVER['HTTP_X_REAL_PORT'] == 443)) ? 'https://' : 'http://';
    } // getRequestSchema

    // ---------------------------------------------------
    //  CSFR related methods
    // ---------------------------------------------------

    /**
     * Return variable name where CSFR protection code is stored
     *
     * @return string
     */
    static private function getCsfrSessionVariableName() {
      return 'csfr_protection_code_for_' . self::$adapter->getUniqueKey();
    } // getCsfrSessionVariableName

    /**
     * Return CSFR protection code
     *
     * @return string
     */
    static function getCsfrProtectionCode() {
      $session_id = self::getCsfrSessionVariableName();

      if(!isset($_SESSION[$session_id]) || empty($_SESSION[$session_id])) {
        $_SESSION[$session_id] = make_string(40);
      } // if

      return $_SESSION[$session_id];
    } // getCsfrProtectionCode

    /**
     * Saved CSFR protection code, in case that we need to revert it
     *
     * @var string
     */
    static private $saved_csfr_code;

    /**
     * Reset code after it's been validated and used to sign a single request
     */
    static function resetCsfrProtectionCode() {
      $session_id = self::getCsfrSessionVariableName();

      if(isset($_SESSION[$session_id])) {
        if(empty(self::$saved_csfr_code)) {
          self::$saved_csfr_code = $_SESSION[$session_id];
        } // if

        unset($_SESSION[$session_id]);
      } // if
    } // resetCsfrProtectionCode

    /**
     * This method is called if we need to revert code to allow user to
     * re-submit the same form without re-rendering it
     */
    static function revertCsfrProtectionCode() {
      $session_id = self::getCsfrSessionVariableName();

      if(self::$saved_csfr_code) {
        $_SESSION[$session_id] = self::$saved_csfr_code;
      } // if
    } // revertCsfrProtectionCode

    // ---------------------------------------------------
    //  Frameworks and modules
    // ---------------------------------------------------

    /**
     * Cached array of application frameworks
     *
     * @var array
     */
    static private $frameworks = false;

    /**
     * Return list of available application frameworks
     *
     * @return AngieFramework[]
     * @throws FileDnxError
     * @throws ClassNotImplementedError
     */
    static function getFrameworks() {
      if(self::$frameworks === false) {
        $framework_names = defined('APPLICATION_FRAMEWORKS') && APPLICATION_FRAMEWORKS ? explode(',', APPLICATION_FRAMEWORKS) : null;

        if(is_foreachable($framework_names)) {
          self::$frameworks = array();

          foreach($framework_names as $framework_name) {
            $framework_class = Inflector::camelize($framework_name) . 'Framework';

            $file = ANGIE_PATH . "/frameworks/$framework_name/$framework_class.class.php";
            if(is_file($file)) {
              require_once $file;

              $framework = new $framework_class();
              if($framework instanceof AngieFramework) {
                self::$frameworks[] = $framework;
              } else {
                throw new ClassNotImplementedError($framework_class, $file, "Framwork definition class not found at '$file'");
              } // if
            } else {
              throw new FileDnxError($file, "Framework definition class not found at '$file'");
            } // if
          } // foreach
        } else {
          self::$frameworks = null;
        } // if
      } // if

      return self::$frameworks;
    } // getFrameworks

    /**
     * Cached list of all modules
     *
     * @var array
     */
    static private $all_modules = false;

    /**
     * Return list of all modules in the system
     *
     * @return AngieModule[]
     */
    static function getAllModules() {
      if(self::$all_modules === false) {
        self::$all_modules = array_merge(self::getInstalledModules(), self::getAvailableModules());
      } //if
      
      return self::$all_modules;
    } // getAllModules

    /**
     * Cached list of installed modules
     *
     * @var array
     */
    static private $installed_modules = false;

    /**
     * Return list of installed application modules
     *
     * @return AngieModule[]
     * @throws FileDnxError
     * @throws ClassNotImplementedError
     */
    static function &getInstalledModules() {
      if(self::$installed_modules === false) {

        // Initialize system module
        require_once APPLICATION_PATH . "/modules/system/SystemModule.class.php";
        
        self::$installed_modules = array(
          'system' => new SystemModule(true)
        );

        // Load and initialize additional modules
        foreach(AngieApplication::getInstalledNonSystemModules() as $module_name => $module_is_enabled) {
          $module_class = Inflector::camelize($module_name) . 'Module';

          // Try to load main module
          $file = APPLICATION_PATH . "/modules/$module_name/$module_class.class.php";
          if(is_file($file)) {
            require_once $file;
          } else {

            // Try to load custom module
            $file = CUSTOM_PATH . "/modules/$module_name/$module_class.class.php";
            if(is_file($file)) {
              require_once $file;
            } else {
              throw new FileDnxError($file, "'$module_name' module definition file not found");
            } // if
          } // if

          $module = class_exists($module_class, false) ? new $module_class($module_is_enabled, true) : null;

          if($module instanceof AngieModule) {
            self::$installed_modules[] = $module;
          } else {
            throw new ClassNotImplementedError($module_class, $file, "'$module_name' module definition class not found");
          } // if
        } // foreach
      } // if

      return self::$installed_modules;
    } // getInstalledModules

    /**
     * Returns an array of installed non-system module
     *
     * Key is module name, and value is whether module is enabled or not
     *
     * @return array
     */
    static function getInstalledNonSystemModules() {
      return AngieApplication::cache()->get('installed_non_system_modues', function() {
        $rows = DB::execute('SELECT name, is_enabled FROM ' . TABLE_PREFIX . 'modules WHERE name != ? ORDER BY position', 'system', true);

        if($rows instanceof DBResult) {
          $result = array();

          foreach($rows as $row) {
            $result[$row['name']] = (boolean) $row['is_enabled'];
          } // foreach

          return $result;
        } // if

        return array();
      });
    } // getInstalledNonSystemModules

    /**
     * List of enabled modules
     *
     * @var array
     */
    static private $enabled_modules = false;

    /**
     * Return modules that are installed and enabled
     *
     * @return AngieModule[]
     */
    static function &getEnabledModules() {
      if(self::$enabled_modules === false) {
        self::$enabled_modules = array();

        if(self::$installed_modules === false) {
          self::getInstalledModules();
        } // if

        foreach(self::$installed_modules as &$module) {
          if($module->isEnabled() && self::isCompatibleModule($module)) {
            self::$enabled_modules[] = $module;
          } // if
        } // foreach
        unset($module);
      } // if

      return self::$enabled_modules;
    } // getEnabledModules

    /**
     * Cached array of enabled module names
     *
     * @var array
     */
    static private $enabled_module_names = false;

    /**
     * Return list of enabled module names
     *
     * @return array
     */
    static function getEnabledModuleNames() {
      if(self::$enabled_module_names === false) {
        self::$enabled_module_names = array();

        foreach(self::getEnabledModules() as $module) {
          self::$enabled_module_names[] = $module->getName();
        } // foreach
      } // if

      return self::$enabled_module_names;
    } // getEnabledModuleNames

    /**
     * Cached array of module names for collectors
     *
     * @var array
     */
    static private $module_names_for_collectors = false;

    /**
     * Get all enabled modules and frameworks
     *
     * @return array
     */
    static function getModuleNamesForCollectors() {
    	if (self::$module_names_for_collectors === false) {
    		self::$module_names_for_collectors = array();

        foreach(self::getEnabledModules() as $module) {
          self::$module_names_for_collectors[] = $module->getName() . '-' . $module->getVersion();
        } // foreach
    	} // if

    	return self::$module_names_for_collectors;
    } // getModuleNamesForCollectors

    /**
     * Cached list of available modules
     *
     * @var array
     */
    static private $available_modules = false;

    /**
     * Scan for modules in given path
     *
     * @param string $path
     * @param array $skip_modules
     * @return array
     */
    static function scanForModules($path, $skip_modules) {
      if (!$skip_modules) {
        $skip_modules = array();
      } // if

      $d = dir($path);
      if($d) {
      	$available_modules = array();

    		while(($entry = $d->read()) !== false) {
    		  if(str_starts_with($entry, '.') || !(is_dir(APPLICATION_PATH . '/modules/' . $entry) || is_dir(CUSTOM_PATH . '/modules/' . $entry)) || in_array($entry, $skip_modules)) {
    		    continue;
    		  } // if

  		    $module_class = Inflector::camelize($entry) . 'Module';

  		    if(is_file("$path/$entry/$module_class.class.php")) {
    		    require_once "$path/$entry/$module_class.class.php";
    		    $available_modules[] = new $module_class();
  		    } // if
    		} // while

    		if(count($available_modules) > 0) {
    		  return $available_modules;
    		} // if
      } // if

      return null;
    } // scanForModules

    /**
     * Return list of available modules that are not yet installed
     *
     * @return array
     */
    static function getAvailableModules() {
      if(self::$available_modules === false) {
        $module_names = DB::executeFirstColumn('SELECT name FROM ' . TABLE_PREFIX . 'modules');

        // look for modules in activecollab modules directory
        $system_modules_path = APPLICATION_PATH . '/modules';
        $system_modules = AngieApplication::scanForModules($system_modules_path, $module_names);

        // we need to populate $module_names to prevent users to name modules with names of system modules
        foreach ($system_modules as $system_module) {
        	$module_names[] = $system_module->getName();
        } // foreach

        $custom_modules_path = CUSTOM_PATH . '/modules';
        $custom_modules = AngieApplication::scanForModules($custom_modules_path, $module_names);

        self::$available_modules = array_merge((array) $system_modules, (array) $custom_modules);
      } // if

  		return self::$available_modules;
    } // getAvailableModules

    /**
     * Get Available Modules names
     *
     * @return array
     */
    static function getAvailableModulesNames() {
      $available_modules = self::getAvailableModules();
      $names = array();

      if (is_foreachable($available_modules)) {
        foreach ($available_modules as $available_module) {
          $names[] = $available_module->getName();
        } // foreach
      } // if

      return $names;
    } // getAvailableModulesNames

    /**
     * Available official modules
     *
     * @var array
     */
    static private $official_modules = false;

    /**
     * Get Available Official Modules
     *
     * @return array
     */
    static function getOfficialModules() {
      if (self::$official_modules === false) {
        self::$official_modules = AngieApplication::scanForModules(APPLICATION_PATH . '/modules', false);
      } // if

      return self::$official_modules;
    } // getAvailableOfficialModules

    /**
     * Get list of available official modules
     *
     * @return array
     */
    static function getOfficialModuleNames(){
      $available_modules = self::getOfficialModules();
      $names = array();

      if (is_foreachable($available_modules)) {
        foreach ($available_modules as $available_module) {
          $names[] = $available_module->getName();
        } // foreach
      } // if

      return $names;
    } // getAvailableOfficialModuleNames

    /**
     * Returns true if framework $name is loaded
     *
     * @param string $name
     * @return boolean
     */
    static function isFrameworkLoaded($name) {
      return isset(self::$loaded_frameworks_and_modules[$name]) && self::$loaded_frameworks_and_modules[$name] instanceof AngieFramework;
    } // isFrameworkLoaded

    /**
     * Returns true if $name module is loaded
     *
     * @param string $name
     * @return boolean
     */
    static function isModuleLoaded($name) {
      return isset(self::$loaded_frameworks_and_modules[$name]) && self::$loaded_frameworks_and_modules[$name] instanceof AngieModule;
    } // isModuleLoaded

    /**
     * Return module instance
     *
     * @param string $name
     * @param boolean $loaded_only
     * @return AngieFramework
     * @throws InvalidParamError
     */
    static function getModule($name, $loaded_only = true) {
      if(isset(self::$loaded_frameworks_and_modules[$name])) {
        return self::$loaded_frameworks_and_modules[$name];
      } // if

      if($loaded_only) {
        throw new InvalidParamError('name', $name, "Module '$name' is not loaded");
      } else {
        $module_class = Inflector::camelize($name) . 'Module';

        $system_module_file = APPLICATION_PATH . "/modules/$name/$module_class.class.php";
        $custom_module_file = CUSTOM_PATH . "/modules/$name/$module_class.class.php";

        if(is_file($system_module_file) || is_file($custom_module_file)) {
        	if(is_file($system_module_file)) {
        		require_once $system_module_file;
        	} else {
        		require_once $custom_module_file;
        	} // if

          if(class_exists($module_class)) {
            return new $module_class();
          } // if
        } // if

        throw new InvalidParamError('name', $name, "Module '$name' is not defined");
      } // if
    } // getModule

    /**
     * Return module signature
     *
     * @param AngieModule|string $module
     * @return string|null
     */
    static private function getModuleSignature($module) {
      $signature_file = $module instanceof AngieModule ?
        $module->getPath() . '/signature.php' :
        "{$module}/signature.php";

      if(is_file($signature_file)) {
        $signature = include $signature_file;
      } else {
        $signature = null;
      } // if

      return $signature;
    } // getModuleSignature

    /**
     * Returns true if module is compatible with this version of system based on module signature
     *
     * @param string $module
     * @return boolean
     */
    static function isCompatibleModule($module) {
      return self::getModuleSignature($module) === self::getAdapter()->getModuleSignature();
    } // isCompatibleModule

    /**
     * Return module compatibility link
     *
     * @param AngieModule $module
     * @param boolean $module_declared_internal
     * @return string
     */
    static function getCompatibilityLink(AngieModule $module, $module_declared_internal = false) {
      return self::getAdapter()->getCompatibilityLink($module, $module_declared_internal);
    } // getCompatibilityLink

    // ---------------------------------------------------
    //  File paths
    // ---------------------------------------------------

    /**
     * Find and include specific controller based on controller name
     *
     * @param string $controller_name
     * @param string $module_name
     * @return string
     * @throws InvalidParamError
     */
    static function useController($controller_name, $module_name = DEFAULT_MODULE) {
      if(isset(self::$loaded_frameworks_and_modules[$module_name])) {
        return self::$loaded_frameworks_and_modules[$module_name]->useController($controller_name);
      } else {
        throw new InvalidParamError('module_name', $module_name, "Module / framework '$module_name' not loaded");
      } // if
    } // useController

    /**
     * Use one or more models from a given module
     *
     * @param array $model_names
     * @param string $module_name
     * @throws InvalidParamError
     */
    static function useModel($model_names, $module_name = DEFAULT_MODULE) {
      if(isset(self::$loaded_frameworks_and_modules[$module_name])) {
        self::$loaded_frameworks_and_modules[$module_name]->useModel($model_names);
      } else {
        throw new InvalidParamError('module_name', $module_name, "Module / framework '$module_name' not loaded");
      } // if
    } // useModel

    /**
     * Use helper file
     *
     * @param string $helper_name
     * @param string $module_name
     * @param string $helper_type
     * @return string
     * @throws InvalidParamError
     */
    static function useHelper($helper_name, $module_name = DEFAULT_MODULE, $helper_type = 'function') {
      if(isset(self::$loaded_frameworks_and_modules[$module_name])) {
        return self::$loaded_frameworks_and_modules[$module_name]->useHelper($helper_name, $helper_type);
      } else {
        throw new InvalidParamError('module_name', $module_name, "Module / framework '$module_name' not loaded");
      } // if
    } // useHelper

    // ---------------------------------------------------
    //  Widgets
    // ---------------------------------------------------

    /**
     * Array of widgets that are used in the current request
     *
     * @var array
     */
    static private $used_widgets = array();

    /**
     * Use a widget
     *
     * @param string $widget_name
     * @param string $module_name
     */
    static function useWidget($widget_name, $module_name = DEFAULT_MODULE) {
      if(!isset(self::$used_widgets[$widget_name])) {
        self::$used_widgets[$widget_name] = self::getWidgetPath($widget_name, $module_name);
      } // if
    } // useWidget

    /**
     * Return array of widgets using in this request
     *
     * @return array
     */
    static function getUsedWidgets() {
      return self::$used_widgets;
    } // getUsedWidgets

    /**
     * Return widget code
     *
     * @param string $widget_name
     * @param string $widget_path
     * @return string
     */
    static function renderWidget($widget_name, $widget_path) {
      if(AngieApplication::isWidgetLoaded($widget_name)) {
        return null; // Already loaded
      } // if

      $result = '';

      $widget_loader = AngieApplication::getWidgetLoader($widget_path);

      $dependencies = $widget_loader->getDependencies();
      if($dependencies) {
        foreach($dependencies as $dependency) {
          if(strpos($dependency, '/') === false) {
            $dependency_widget_module = DEFAULT_MODULE;
            $dependency_widget = $dependency;
          } else {
            list($dependency_widget_module, $dependency_widget) = explode('/', $dependency);
          } // if

          if(str_ends_with($dependency_widget, '#optional')) {
            $dependency_widget = str_replace('#optional', '', $dependency_widget);

            $optional = true;
          } else {
            $optional = false;
          } // if

          if($optional && !AngieApplication::isModuleLoaded($dependency_widget_module)) {
            continue; // Widget is optional and module is not loaded
          } // if

          $result .= AngieApplication::renderWidget($dependency_widget, AngieApplication::getWidgetPath($dependency_widget, $dependency_widget_module));
          AngieApplication::markWidgetAsLoaded($dependency_widget);
        } // foreach
      } // if

      $result .= $widget_loader->render();
      AngieApplication::markWidgetAsLoaded($widget_name);

      return $result;
    } // renderWidget

    /**
     * Return widget loader for given widget path
     *
     * @param string $widget_path
     * @return AngieWidgetLoader|mixed
     * @throws InvalidInstanceError
     */
    static function getWidgetLoader($widget_path) {
      if(is_file("$widget_path/load.php")) {
        $widget_loader = include "$widget_path/load.php";

        if($widget_loader instanceof AngieWidgetLoader) {
          return $widget_loader;
        } // if
      } else {
        return new AngieWidgetLoader($widget_path);
      } // if

      throw new InvalidInstanceError('widget_loader', $widget_loader, 'AngieWidgetLoader');
    } // getWidgetLoader

    /**
     * Saved list of loaded widgets
     *
     * @var array
     */
    static private $loaded_widgets = array();

    /**
     * Mark widget as loaded, so we don't load it again
     *
     * @param string $widget
     */
    static function markWidgetAsLoaded($widget) {
      if(!in_array($widget, self::$loaded_widgets)) {
        self::$loaded_widgets[] = $widget;
      } // if
    } // markWidgetAsLoaded

    /**
     * Returns true if widget is marked as loaded, so we don't load it again
     *
     * @param string $widget
     * @return boolean
     */
    static function isWidgetLoaded($widget) {
      return in_array($widget, self::$loaded_widgets);
    } // isWidgetLoaded

    // ---------------------------------------------------
    //  Paths
    // ---------------------------------------------------

    /**
     * Return template path
     *
     * @param string $template
     * @param string $controller_name
     * @param string $module_name
     * @param string $interface
     * @return string
     * @throws InvalidParamError
     */
    static function getViewPath($template, $controller_name = null, $module_name = DEFAULT_MODULE, $interface = null) {
      if(isset(self::$loaded_frameworks_and_modules[$module_name])) {
       return self::$loaded_frameworks_and_modules[$module_name]->getViewPath($template, $controller_name, $interface);
      } // if

      throw new InvalidParamError('module_name', $module_name, "Module / framework '$module_name' not loaded");
    } // getViewPath

    /**
     * Return layout path
     *
     * @param string $layout
     * @param string $module_name
     * @return string
     * @throws InvalidParamError
     */
    static function getLayoutPath($layout, $module_name = DEFAULT_MODULE) {
      if(isset(self::$loaded_frameworks_and_modules[$module_name])) {
        return self::$loaded_frameworks_and_modules[$module_name]->getLayoutPath($layout);
      } // if

      throw new InvalidParamError('module_name', $module_name, "Module / framework '$module_name' not loaded");
    } // getLayoutPath

    /**
     * Return path where specific widget is defined
     *
     * @param string $widget
     * @param string $module_name
     * @return string
     * @throws InvalidParamError
     */
    static function getWidgetPath($widget, $module_name = DEFAULT_MODULE) {
      if(isset(self::$loaded_frameworks_and_modules[$module_name])) {
        return self::$loaded_frameworks_and_modules[$module_name]->getWidgetPath($widget);
      } // if

      throw new InvalidParamError('module_name', $module_name, "Module / framework '$module_name' not loaded");
    } // getWidgetPath

    /**
     * Return URL for a given proxy with given parameters
     *
     * @param string $proxy
     * @param string $module_name
     * @param mixed $params
     * @throws InvalidParamError
     */
    static function getProxyUrl($proxy, $module_name = DEFAULT_MODULE, $params = null) {
      if(isset(self::$loaded_frameworks_and_modules[$module_name])) {
        return self::$loaded_frameworks_and_modules[$module_name]->getProxyUrl($proxy, $params);
      } // if

      throw new InvalidParamError('module_name', $module_name, "Module / framework '$module_name' not loaded");
    } // getProxyUrl

    /**
     * Return email template path
     *
     * @param string $template
     * @param string $module_name
     * @return string
     * @throws InvalidParamError
     */
    static function getEmailTemplatePath($template, $module_name = DEFAULT_MODULE) {
      if(isset(self::$loaded_frameworks_and_modules[$module_name])) {
        return self::$loaded_frameworks_and_modules[$module_name]->getEmailTemplatePath($template);
      } // if

      throw new InvalidParamError('module_name', $module_name, "Module / framework '$module_name' not loaded");
    } // getEmailTemplatePath

    /**
     * Return handler file path based on event name
     *
     * @param string $callback_name
     * @param string $module_name
     * @return string
     * @throws InvalidParamError
     */
    static function getEventHandlerPath($callback_name, $module_name = DEFAULT_MODULE) {
      if(isset(self::$loaded_frameworks_and_modules[$module_name])) {
        return self::$loaded_frameworks_and_modules[$module_name]->getEventHandlerPath($callback_name);
      } // if

      throw new InvalidParamError('module_name', $module_name, "Module / framework '$module_name' not loaded");
    } // getEventHandlerPath

    /**
     * Cached array of module names that we extracted from controller paths
     *
     * @var array
     */
    static private $module_names_from_controller_paths = array();

    /**
     * Extract module name from controller path
     *
     * @param string $controller_path
     * @return string
     */
    static function getModuleNameFromControllerPath($controller_path) {
      if(!array_key_exists($controller_path, self::$module_names_from_controller_paths)) {
        if(str_starts_with($controller_path, 'phar://')) {
          $controller_path = substr($controller_path, 7);
        } // if

        $controller_path = str_replace('\\', '/', $controller_path);

        $parts = explode('/', $controller_path);
        if(count($parts) > 3) {
          self::$module_names_from_controller_paths[$controller_path] = $parts[count($parts) - 3];
        } else {
          self::$module_names_from_controller_paths[$controller_path] = ''; // Invalid controller path
        } // if
      } // if

      return self::$module_names_from_controller_paths[$controller_path];
    } // getModuleNameFromControllerPath

    /**
     * Cached array of controller names extracted from controller class names
     *
     * @var array
     */
    static private $controller_names = array();

    /**
     * Convert controller class name to controller name
     *
     * @param string $controller_class_name
     * @return string
     */
    static function getControllerNameFromControllerClassName($controller_class_name) {
      if(!array_key_exists($controller_class_name, self::$controller_names)) {
        self::$controller_names[$controller_class_name] = Inflector::underscore(substr($controller_class_name, 0, strlen($controller_class_name) - 10));
      } // if

      return self::$controller_names[$controller_class_name];
    } // getControllerNameFromControllerClassName

    /**
     * Return available file name in /uploads folder
     *
     * @return string
     */
    static function getAvailableUploadsFileName() {
      do {
        $filename = UPLOAD_PATH . '/' . make_string(10) . '-' . make_string(10) . '-' . make_string(10) . '-' . make_string(10);
      } while(is_file($filename));

      return $filename;
    } // getAvailableUploadsFileName

    /**
     * Return unique filename in work folder
     *
     * @param string $prefix
     * @param string $extension
     * @return string
     */
    static function getAvailableWorkFileName($prefix = null, $extension = null) {
      return self::getAvailableFileName(WORK_PATH, $prefix, $extension);
    } // getAvailableWorkFileName

    /**
     * Get Available file name in $folder
     *
     * @param string $folder
     * @param string $prefix
     * @param string $extension
     * @return string
     */
    static function getAvailableFileName($folder, $prefix = null, $extension = null) {
      if($prefix) {
        $prefix = "$prefix-";
      } // if

      if($extension) {
        $extension = ".$extension";
      } // if

      do {
        $filename = $folder . '/' . $prefix . make_string(10) . $extension;
      } while(is_file($filename));

      return $filename;
    } // getAvailableFileName

    // ---------------------------------------------------
    //  Localization
    // ---------------------------------------------------

    /**
     * Rebuild localization data
     *
     * @param array $frameworks
     * @param array $modules
     * @param string $version
     * @return boolean
     */
    static function rebuildLocalization($frameworks = null, $modules = null, $version = null) {
    	if ($frameworks === null) {
    		$frameworks = AngieApplication::getFrameworks();
    	} // if

    	if ($modules === null) {
    		// we have to reset installed modules cache
    		self::$installed_modules = false;
    		$modules = AngieApplication::getInstalledModules();
    	} // if

			$custom_path = CUSTOM_PATH;

      if($version) {
        if(AngieApplication::usePackage()) {
          $application_path = 'phar://' . ROOT . "/$version.phar";
        } else {
          $application_path = ROOT . '/' . $version;
        } // if

        $angie_path = "$application_path/angie";
      } else {
        $angie_path = ANGIE_PATH;
        $application_path = APPLICATION_PATH;
      } // if

    	$all_installed_modules = array();

    	if (is_foreachable($frameworks)) {
    		foreach ($frameworks as $framework) {
    			if ($framework instanceof AngieFramework) {
    				$all_installed_modules[$framework->getName()] = $framework->getPath() . '/resources';
    			} else {
    				$all_installed_modules[$framework] = "$angie_path/frameworks/$framework/resources";
    			} // if
    		} // foreach
    	} // if

    	if (is_foreachable($modules)) {
    		foreach ($modules as $module) {
    			if ($module instanceof AngieFramework) {
    				$all_installed_modules[$module->getName()] = $module->getPath() . '/resources';
    			} else {
	          if(is_dir("$application_path/modules/$module")) {
	            $all_installed_modules[$module] = "$application_path/modules/$module/resources";
	          } else {
	            $all_installed_modules[$module] = "$custom_path/modules/$module/resources";
	          } // if
    			} // if
    		} // foreach
    	} // if

    	$module_names = array_keys($all_installed_modules);

    	if (!is_foreachable($module_names)) {
    		return false;
    	} // if

    	try {
    	  $languages_table = TABLE_PREFIX . 'languages';
      	$translations_table = TABLE_PREFIX . 'language_phrase_translations';
      	$phrases_table = TABLE_PREFIX . 'language_phrases';

				DB::beginWork('Rebuilding localization');

				// delete all phrases which are not in list of active modules and frameworks
				DB::execute("TRUNCATE $phrases_table");

				$serverside_dictionary = null;
				$clientside_dictionary = null;
				$to_insert = null;

				foreach ($all_installed_modules as $module_name => $dictionary_directory) {
          $serverside_dictionary = array();
          $serverside_manual_dictionary = array();

          if (is_file($dictionary_directory . '/dictionary.serverside.php')) {
            $serverside_dictionary = @include($dictionary_directory . '/dictionary.serverside.php');
          } // if

          if (is_file($dictionary_directory . '/dictionary.serverside.manual.php')) {
            $serverside_manual_dictionary = @include($dictionary_directory . '/dictionary.serverside.manual.php');
          } // if

          $serverside_dictionary = array_unique(array_merge((array) $serverside_dictionary, (array) $serverside_manual_dictionary));
          if (is_foreachable($serverside_dictionary)) {
            $query = null;
            if (is_foreachable($serverside_dictionary)) {
              foreach ($serverside_dictionary as $phrase) {
                $query[]= DB::prepare('(md5(?), ?, ?, ?)', $phrase, $phrase, $module_name, 1);
              } // foreach
            } // if
            DB::execute("INSERT INTO $phrases_table (hash, phrase, module, is_serverside) VALUES " . implode(',', $query));
          } // if

          $clientside_dictionary = array();
          $clientside_manual_dictionary = array();

          if (is_file($dictionary_directory . '/dictionary.clientside.php')) {
            $clientside_dictionary = @include($dictionary_directory . '/dictionary.clientside.php');
          } // if

          if (is_file($dictionary_directory . '/dictionary.clientside.manual.php')) {
            $clientside_manual_dictionary = @include($dictionary_directory . '/dictionary.clientside.manual.php');
          } // if

          $clientside_dictionary = array_unique(array_merge((array) $clientside_dictionary, (array) $clientside_manual_dictionary));
					if (is_foreachable($clientside_dictionary)) {
						$query = null;


						if (is_foreachable($clientside_dictionary)) {
							DB::execute("UPDATE $phrases_table SET is_clientside = 1 WHERE module = ? AND phrase IN (?)", $module_name, $clientside_dictionary);

							$to_insert = array_diff($clientside_dictionary, $serverside_dictionary);
              if (is_foreachable($to_insert)) {
                foreach ($to_insert as $phrase) {
                  $query[]= DB::prepare('(md5(?), ?, ?, ?)', $phrase, $phrase, $module_name, 1);
                } // foreach
                DB::execute("INSERT INTO $phrases_table (hash, phrase, module, is_clientside) VALUES " . implode(',', $query));
              } // if
						} // if
					} // if
				} // foreach

				// Cleanup translations
				if(class_exists('Languages', false)) {
				  Languages::cleanUpUnusedTranslations();
				} else {
				  DB::execute("DELETE $translations_table.* FROM $translations_table LEFT JOIN $languages_table ON $translations_table.language_id = $languages_table.id WHERE $languages_table.id IS NULL"); // cleanup translations that belongs to non existing languages
        	DB::execute("DELETE $translations_table.* FROM $translations_table LEFT JOIN $phrases_table ON $translations_table.phrase_hash = $phrases_table.hash WHERE $phrases_table.phrase IS NULL"); // cleanup translations to non existing phrases in dictionary
				} // if

				DB::commit('Rebuilding localization succeeded');
        return true;
    	} catch (Exception $e) {
    		DB::rollback('Rebuilding localization failed');
    		return false;
    	} // try
    } // rebuildLocalization

    // ---------------------------------------------------
    //  Scheduled tasks
    // ---------------------------------------------------

    /**
     * Return true if scheduled tasks are running
     *
     * @return boolean
     */
    static function areScheduledTasksRunning() {
      return AngieApplication::isFrequentlyRunning() && AngieApplication::isHourlyRunning() && AngieApplication::isDailyRunning();
    } // areScheduledTasksRunning

    /**
     * Cached is frequently running value
     *
     * @var mixed
     */
    static private $is_frequently_running = null;

    /**
     * Returns true if frequently task is properly running
     *
     * @return boolean
     */
    static function isFrequentlyRunning() {
      if(self::$is_frequently_running === null) {
        self::$is_frequently_running = (ConfigOptions::getValue('last_frequently_activity', false) + 600) >= time(); // Frequently task executed in last 10 minutes
      } // if

      return self::$is_frequently_running;
    } // isFrequentlyRunning

    /**
     * Cached is hourly running value
     *
     * @var mixed
     */
    static private $is_hourly_running = null;

    /**
     * Returns true if hourly task is properly running
     *
     * @return boolean
     */
    static function isHourlyRunning() {
      if(self::$is_hourly_running === null) {
        self::$is_hourly_running = (ConfigOptions::getValue('last_hourly_activity', false) + 3600) >= time(); // Hourly task executed in last the hour
      } // if

      return self::$is_hourly_running;
    } // isHourlyRunning

    /**
     * Cached is daily running value
     *
     * @var mixed
     */
    static private $is_daily_running = null;

    /**
     * Returns true if daily task is properly running
     *
     * @return boolean
     */
    static function isDailyRunning() {
      if(self::$is_daily_running === null) {
        self::$is_daily_running = (ConfigOptions::getValue('last_daily_activity', false) + 86400) >= time(); // Daily task executed in last hour
      } // if

      return self::$is_daily_running;
    } // isDailyRunning

    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------

    /**
     * Get the url of specified asset
     *
     * @param string $name
     * @param string $asset_type
     * @param string $module
     * @param string $interface
     * @return string
     */
    static function getAssetUrl($name, $module = null, $asset_type = 'images', $interface = null) {
      if(empty($interface)) {
        $interface = self::INTERFACE_DEFAULT;
      } // if

      if($module) {
        return ASSETS_URL . "/$asset_type/$module/$interface/$name";
      } else {
        return ASSETS_URL . "/$asset_type/$interface/$name";
      } // if
    } // getAssetUrl

    /**
     * Return image URL
     *
     * @param string $name
     * @param string $module
     * @param string $interface
     * @return string
     */
    static function getImageUrl($name, $module = null, $interface = null) {
			return self::getAssetUrl($name, $module, 'images', $interface);
    } // getImageUrl

    /**
  	 * Return icon url of the file based on it's extension
  	 *
    * @param string $filename
 	  * @param string $size
 	  * @return string
 	  */
    static function getFileIconUrl($filename, $size) {
     	$extension = get_file_extension($filename);
      $possible_location = ENVIRONMENT_FRAMEWORK_PATH . "/assets/default/images/file-types/$size/$extension.png";

      if (!is_file($possible_location)) {
      	return AngieApplication::getImageUrl("file-types/$size/default.png", ENVIRONMENT_FRAMEWORK);
      } else {
      	return AngieApplication::getImageUrl("file-types/$size/$extension.png", ENVIRONMENT_FRAMEWORK);
      } // if
    } // get_file_icon_url

    /**
     * Return foundation image URL
     *
     * @param string $name
     * @return string
     */
    function getFoundationImageUrl($name) {
      return ASSETS_URL . "/images/foundation/$name";
    } // getFoundationImageUrl

    /**
     * Return brand image URL
     *
     * @param string $name
     * @param boolean $include_timestamp
     * @return string
     */
    static function getBrandImageUrl($name, $include_timestamp = false) {
      if(URL_BASE == ROOT_URL . '/') {
        $url = PUBLIC_AS_DOCUMENT_ROOT ? ROOT_URL . "/brand/$name" : ROOT_URL . "/public/brand/$name";
      } else {
        $url = ROOT_URL . "/brand/$name";
      } // if

      if($include_timestamp) {
        $url .= '?timestamp=' . @filemtime(PUBLIC_PATH . "/brand/$name");
      } // if

      return $url;
    } // getBrandImageUrl

    /**
     * Application brand image url
     *
     * @param String $name
     * @return String
     */
    static function getApplicationBrandImageUrl($name) {
      return AngieApplication::getImageUrl('application-branding/' . $name, 'system');
    } // getApplicationBrandImageUrl

    /**
     * Application Icon Url
     *
     * @param String $size
     * @return String
     */
    static function getApplicationIconUrl($size) {
      return AngieApplication::getApplicationBrandImageUrl('logo.' . $size + 'x' . $size . '.png');
    } // getApplicationIconUrl

    // ---------------------------------------------------
    //  Assets
    // ---------------------------------------------------

    /**
     * Return true if system is configured to protect assets folder
     *
     * @return bool
     */
    static function assetsProtected() {
      return defined('PROTECT_ASSETS_FOLDER') && PROTECT_ASSETS_FOLDER;
    } // assetsProtected

    /**
     * Rebuilds all assets and puts them into public assets dir
     *
     * If $frameworks and $modules are not provided, system will use loaded list
     * of frameworks and modules
     *
     * @param array $frameworks
     * @param array $modules
     * @param string $version
     * @return boolean
     */
    static function rebuildAssets($frameworks = null, $modules = null, $version = null) {
      if(self::assetsProtected()) {
        return true; // Assets are protected
      } // if

      if($version) {
        if(is_file(ROOT . "/{$version}.phar")) {
          $application_path = 'phar://' . APPLICATION_NAME . "-$version.phar"; // Give advantage to PHAR distribution
        } else {
          $application_path = ROOT . '/' . $version;
        } // if

        $angie_path = "$application_path/angie";
      } else {
        $angie_path = ANGIE_PATH;
        $application_path = APPLICATION_PATH;
      } // if

      $custom_path = CUSTOM_PATH;

      // ---------------------------------------------------
      //  Clean up and prepare
      // ---------------------------------------------------

      $supported_assets = array('images', 'flash', 'silverlight', 'fonts'); // Assets which will be rebuilt
      empty_dir(ASSETS_PATH, true); // Empty destination directory (ignore hidden files, like .gitignore)
      copy_dir("$angie_path/frameworks/environment/assets/foundation/images", ASSETS_PATH . '/images/foundation', array('.svn'), true); // Copy foundation images

      // ---------------------------------------------------
      //  Copy framework and module files
      // ---------------------------------------------------

      $to_move = $to_move_widgets = $to_move_help = array();

      if(empty($frameworks)) {
        $frameworks = AngieApplication::getFrameworks();
      } // if

      if(empty($modules)) {
        $modules = AngieApplication::getEnabledModules();
      } // if

      foreach($frameworks as $framework) {
        $framework_name = $framework instanceof AngieFramework ? $framework->getName() : $framework;

        $to_move[$framework_name] = "$angie_path/frameworks/$framework_name/assets";
        $to_move_widgets[$framework_name] = "$angie_path/frameworks/$framework_name/widgets";
        $to_move_help[$framework_name] = "$angie_path/frameworks/$framework_name/help";
      } // foreach

      foreach($modules as $module) {
        $module_name = $module instanceof AngieModule ? $module->getName() : $module;

        if(is_dir("$application_path/modules/$module_name")) {
          $to_move[$module_name] = "$application_path/modules/$module_name/assets";
          $to_move_widgets[$module_name] = "$application_path/modules/$module_name/widgets";
          $to_move_help[$module_name] = "$application_path/modules/$module_name/help";
        } else {
          $to_move[$module_name] = "$custom_path/modules/$module_name/assets";
          $to_move_widgets[$module_name] = "$custom_path/modules/$module_name/widgets";
          $to_move_help[$module_name] = "$custom_path/modules/$module_name/help";
        } // if
      } // foreach

      foreach($to_move as $name => $path) {
        $device_folders = get_folders($path);

        if($device_folders) {
          foreach($device_folders as $device_folder) {
          	foreach ($supported_assets as $asset_type) {
	            if (is_dir("$device_folder/$asset_type")) {
	              copy_dir("$device_folder/$asset_type", ASSETS_PATH . "/$asset_type/$name/" . basename($device_folder), array('.svn'), true);
	            } // if
          	} // foreach
          } // foreach
        } // if
      } // foreach

      foreach($to_move_widgets as $name => $path) {
        $widget_folders = get_folders($path);

        if($widget_folders) {
          foreach($widget_folders as $widget_folder) {
            if(is_dir("$widget_folder/images")) {
              copy_dir("$widget_folder/images", ASSETS_PATH . "/images/$name/widgets/" . basename($widget_folder), array('.svn'), true);
            } // if
          } // foreach
        } // if
      } // foreach

      foreach($to_move_help as $name => $path) {
        $book_folders = get_folders("$path/books");

        if($book_folders) {
          foreach($book_folders as $book_folder) {
            if(is_dir("$book_folder/images")) {
              copy_dir("$book_folder/images", ASSETS_PATH . "/images/$name/help/books/" . str_replace('_', '-', basename($book_folder)), array('.svn'), true);
            } // if
          } // foreach
        } // if

        $version_folders = get_folders("$path/whats_new");

        if($version_folders) {
          foreach($version_folders as $version_folder) {
            if(is_dir("$version_folder/images")) {
              copy_dir("$version_folder/images", ASSETS_PATH . "/images/$name/help/whats-new/" . str_replace('_', '-', basename($version_folder)), array('.svn'), true);
            } // if
          } // foreach
        } // if
      } // foreach

      return true;
    } // rebuildAssets

    /**
     * Rebuild module assets
     *
     * @param AngieModule
     * @return boolean
     */
    static function rebuildModuleAssets(AngieModule $module) {
      if(self::assetsProtected()) {
        return true; // Assets are protected
      } // if

      $supported_assets = array('images', 'flash', 'fonts');  // asseets which will be rebuilt

      $path = $module->getPath() . '/assets';

      if (is_dir($path)) {
        $name = $module->getName();

        $device_folders = get_folders($path);

        if ($device_folders) {
          foreach ($device_folders as $device_folder) {
          	foreach ($supported_assets as $asset_type) {
              if (is_dir("$device_folder/$asset_type")) {
                copy_dir("$device_folder/$asset_type", ASSETS_PATH . "/$asset_type/$name/" . basename($device_folder), array('.svn'), true);
              } // if
          	} // foreach
          } // foreach
        } // if
      } // if

      return true;
    } // rebuildModuleAssets

    /**
     * Clean module assets (when module is unistalled)
     *
     * @package AngieModule $module
     */
    static function cleanModuleAssets(AngieModule $module) {
      if(self::assetsProtected()) {
        return; // Assets are protected
      } // if

      $supported_assets = array('images', 'flash');  // asseets which will be rebuilt

      foreach ($supported_assets as $asset_type) {
        delete_dir(ASSETS_PATH . '/' . $asset_type . '/' . $module->getName());
    	} // foreach
    } // cleanModuleAssets

    /**
     * Remove logs
     */
    static function removeLogs() {
      return empty_dir(ENVIRONMENT_PATH . '/logs/', true);
    } // removeLogs



    // ---------------------------------------------------
    //  Compile
    // ---------------------------------------------------

    /**
     * Clear compiled scripts
     *
     * @param bool $templates_only
     */
    static function clearCompiledScripts($templates_only = false) {
      if(is_dir(COMPILE_PATH)) {
        $mask = $templates_only ? '*.tpl.php' : '*.php';

        foreach(glob(COMPILE_PATH . "/$mask") as $v){
          @unlink($v);
        } // foreach
      } // if
    } // clearCompiledScripts
    
    // ---------------------------------------------------
    //  Client related routines
    // ---------------------------------------------------

    /**
     * Returns true if client accepts GZip
     *
     * @return boolean
     */
    static function clientAcceptsGzip() {
      return self::$accepts_gzip;
    } // clientAcceptsGzip
    
    // Interface types
    const INTERFACE_DEFAULT = 'default';
    const INTERFACE_PRINTER = 'printer';
    const INTERFACE_PHONE = 'phone';
    const INTERFACE_TABLET = 'tablet';
    const INTERFACE_API = 'api';
    
    // Known clients
    const CLIENT_UNKNOWN = 'unknown';
    const CLIENT_IPHONE = 'iphone';
	  const CLIENT_IPOD_TOUCH = 'ipodtouch';
	  const CLIENT_IPAD = 'ipad';
	  const CLIENT_SAFARI = 'safari';
	  const CLIENT_FIREFOX = 'firefox';
	  const CLIENT_CAMINO = 'camino';
	  const CLIENT_OPERA = 'opera';
	  const CLIENT_IE = 'ie';
	  const CLIENT_NETSCAPE = 'netscape';
	  const CLIENT_KONQUEROR = 'konqueror';
	  const CLIENT_SYMBIAN = 'symbian';
	  const CLIENT_OPERA_MINI = 'opera_mini';
	  const CLIENT_OPERA_MOBILE = 'opera_mobile';
	  const CLIENT_ANDROID = 'android';
	  const CLIENT_BLACKBERRY = 'blackberry';
	  const CLIENT_MOBILE_IE = 'mobile_ie';
	  const CLIENT_PALM = 'palm';
	  
	  /**
	   * Cached device class
	   *
	   * @var string
	   */
	  private static $device_class = false;
    
	  /**
	   * Return device class based on user of client that's accessing the system
	   * 
	   * @return string
	   */
    static function getDeviceClass() {
    	if(self::$device_class === false) {
    	  if(FORCE_DEVICE_CLASS) {
    	    self::$device_class = FORCE_DEVICE_CLASS;
    	  } else {
    	    $known_device_classes = array(
      		  "/MSIE(.*)IEMobile/" => AngieApplication::CLIENT_MOBILE_IE,
  		      "/BlackBerry/" => AngieApplication::CLIENT_BLACKBERRY,
  		      "/Linux(.*)Android/" => AngieApplication::CLIENT_ANDROID,
  		      "/iPhone(.*)AppleWebKit(.*)KHTML(.*)Mobile/" => AngieApplication::CLIENT_IPHONE,  
  		      "/iPod(.*)AppleWebKit(.*)KHTML(.*)Mobile/" => AngieApplication::CLIENT_IPOD_TOUCH,
  		      "/SymbianOS(.*)AppleWebKit(.*)KHTML(.*)Safari/" => AngieApplication::CLIENT_SYMBIAN,
  		      "/(webOS|PalmOS|PalmSource)/" => AngieApplication::CLIENT_PALM,
  		      "/AppleWebKit(.*)KHTML(.*)Safari/" => AngieApplication::CLIENT_SAFARI,
  		      "/Gecko(.*)Firefox/" => AngieApplication::CLIENT_FIREFOX,
  		      "/Gecko(.*)Camino/" => AngieApplication::CLIENT_CAMINO,
  		      "/Gecko(.*)Netscape/" => AngieApplication::CLIENT_NETSCAPE,
  		      "/Opera(.*)Opera Mini/" => AngieApplication::CLIENT_OPERA_MINI,
  		      "/MSIE(.*)Windows NT(.*)SV1(.*)Opera/" => AngieApplication::CLIENT_OPERA_MOBILE,
  		      "/MSIE(.*)Windows CE(.*)Opera(.*)/" => AngieApplication::CLIENT_OPERA_MOBILE,
  		      "/MSIE(.*)Symbian OS(.*)Opera(.*)/" => AngieApplication::CLIENT_OPERA_MOBILE,
  		      "/Opera/" => AngieApplication::CLIENT_OPERA,
  		      "/compatible(.*)MSIE/" => AngieApplication::CLIENT_IE,
  		      "/compatible(.*)Konqueror/" => AngieApplication::CLIENT_KONQUEROR,
      		);
      		
      		$user_agent = array_var($_SERVER, 'HTTP_USER_AGENT');
      		
      		if($user_agent) {
  	    		foreach($known_device_classes as $pattern => $class) {
  	    			if(preg_match($pattern, $user_agent)) {
  	    				self::$device_class = $class;
  	    				break;
  	    			} // if
  	    		} // foreach
      		} // if
      		
      		if(empty(self::$device_class)) {
      			self::$device_class = AngieApplication::CLIENT_UNKNOWN;
      		} // if
    	  } // if
    	} // if
    	
    	return self::$device_class;
    } // getDeviceClass
    
    /**
     * Cached prefered interface value
     *
     * @var string
     */
    static private $prefered_interface = false;
    
    /**
     * This function will return prefered interface based on device class that's 
     * been used to access the system
     * 
     * @return string
     */
    static function getPreferedInterface() {
      if(self::$prefered_interface === false) {
        if(FORCE_INTERFACE) {
          self::$prefered_interface = FORCE_INTERFACE;
        } else {
          switch(AngieApplication::getDeviceClass()) {
        		case AngieApplication::CLIENT_IPHONE:
    		    case AngieApplication::CLIENT_IPOD_TOUCH:
    		    case AngieApplication::CLIENT_SYMBIAN:
    		    case AngieApplication::CLIENT_PALM:
    		    case AngieApplication::CLIENT_OPERA_MINI:
    		    case AngieApplication::CLIENT_ANDROID:
    		    case AngieApplication::CLIENT_BLACKBERRY:
    		    case AngieApplication::CLIENT_MOBILE_IE:
    		    case AngieApplication::CLIENT_OPERA_MOBILE:
    		    	self::$prefered_interface = AngieApplication::INTERFACE_PHONE;
    		    	break;
    		    case AngieApplication::CLIENT_IPAD:
    		    	self::$prefered_interface = AngieApplication::INTERFACE_TABLET;
    		    	break;
    		    default:
    		    	self::$prefered_interface = AngieApplication::INTERFACE_DEFAULT;
        	} // switch
        } // if
      } // if
      
      return self::$prefered_interface;
    } // getPreferedInterface
    
    /**
     * Set prefered interface
     * 
     * @param string $interface
     * @return string
     */
    static function setPreferedInterface($interface) {
      if($interface == AngieApplication::INTERFACE_PHONE || $interface == AngieApplication::INTERFACE_TABLET || $interface == AngieApplication::INTERFACE_PRINTER) {
        self::$prefered_interface = $interface;
      } else {
        self::$prefered_interface = AngieApplication::INTERFACE_DEFAULT;
      } // if
    } // setPreferedInterface
    
    // ---------------------------------------------------
    //  Installation
    // ---------------------------------------------------
    
    /**
     * Returns true if this application is installed
     *
     * @return boolean
     */
    static function isInstalled() {
      return defined('CONFIG_PATH') && is_file(CONFIG_PATH . '/config.php');
    } // isInstalled
    
    // ---------------------------------------------------
    //  Autoload
    // ---------------------------------------------------

    /**
     * Return true if $class_name exists
     *
     * This function can suppress auto-load exception, in cases where we need to check if class exists, but don't
     * want to throw exception and break the system
     *
     * @param string $class_name
     * @param bool $autoload
     * @param bool $supress_autoload_exception
     * @return bool
     * @throws AutoloadError|Exception
     */
    static function classExists($class_name, $autoload = true, $supress_autoload_exception = false) {
      try {
        return class_exists($class_name, $autoload);
      } catch(AutoloadError $e) {
        if($supress_autoload_exception) {
          return false;
        } // if

        throw $e;
      } catch(Exception $e) {
        throw $e;
      } // if
    } // classExists
    
    /**
     * Array of registered classes that autoloader uses
     * 
     * @var array
     */
    static private $autoload_classes = array();
    
    /**
     * Automatically load requested class
     * 
     * @param string $class
     * @throws AutoloadError
     */
    static function autoload($class) {
      $path = array_var(self::$autoload_classes, strtoupper($class));
      if($path && is_file($path)) {
        require_once $path;
      } else {
        //if(substr($class, 0, 7) == 'Smarty_') {
        if(stripos($class, 'smarty_') !== false) {
          return; // Ignore Smarty classes
        } // if
        
        throw new AutoloadError($class, self::$autoload_classes);
      } // if
    } // autoload
    
    /**
     * Register class to autoload array
     * 
     * $class can be an array of classes, where index is class name value is 
     * path to the file where class is defined
     * 
     * @param array|string $class
     * @param string $path
     * @throws SetForAutoloadError
     */
    static function setForAutoload($class, $path = null) {
      if(is_array($class)) {
        foreach($class as $k => $v) {
          $key = strtoupper($k);

          if(isset(self::$autoload_classes[$key]) && !AUTOLOAD_ALLOW_PATH_OVERRIDE) {
            throw new SetForAutoloadError($k, self::$autoload_classes);
          } else {
            self::$autoload_classes[$key] = $v;
          } // if
        } // if
      } else {
        $key = strtoupper($class);

        if(isset(self::$autoload_classes[$key]) && !AUTOLOAD_ALLOW_PATH_OVERRIDE) {
          throw new SetForAutoloadError($class, self::$autoload_classes);
        } else {
          self::$autoload_classes[$key] = $path;
        } // if
      } // if
    } // setForAutoload
    
    /**
     * Register a new auto loader
     * 
     * @param mixed $autoloader
     * @throws RegisterAutoloaderError
     */
    static function registerAutoloader($autoloader) {
      if(!spl_autoload_register($autoloader, true, true)) {
        require_once ANGIE_PATH . '/classes/errors/RegisterAutoloaderError.class.php';
        throw new RegisterAutoloaderError($autoloader);
      } // if
    } // registerAutoloader
    
    // ---------------------------------------------------
    //  PHP hooks
    // ---------------------------------------------------
    
    /**
     * Definition of how specific error types should be handled
     * 
     * @var array
     */
    static private $how_to_handle_error = array(
      E_ERROR => 'exception', 
      E_WARNING => 'log', 
      E_NOTICE => 'log', 
      E_STRICT => null, 
      E_PARSE => 'exception', 
      E_CORE_ERROR => 'exception', 
      E_CORE_WARNING => 'log', 
      E_COMPILE_ERROR => 'exception', 
      E_COMPILE_WARNING => 'log', 
      E_USER_ERROR => 'log', 
      E_USER_WARNING => 'log', 
      E_USER_NOTICE => 'log', 
      E_RECOVERABLE_ERROR => 'log', 
      E_ALL => 'log', 
    );
    
    /**
     * Convert PHP errors to exceptions
     * 
     * @param integer $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @throws ErrorException
     */
    static function handleError($errno, $errstr, $errfile, $errline) {
      if(AngieApplication::isInProduction()) {
        return; // supress errors while in production
      } // if

      if(isset(self::$how_to_handle_error[$errno]) && self::$how_to_handle_error[$errno]) {
        
        // Log error to error logs file
        if(self::$how_to_handle_error[$errno] == 'log') {
          $warnings_log_path = AngieApplication::getWarningsLogPath();

          if(file_exists($warnings_log_path)) {
            $handle = @fopen($warnings_log_path, 'a');
          } else {
            $handle = @fopen($warnings_log_path, 'w');
          } // if

          if($handle) {
            fwrite($handle, date(DATETIME_MYSQL) . ' ' . AngieApplication::getErrorType($errno) . " : $errstr (at $errfile on $errline line)\n");
            fclose($handle);
          } // if
          
        // Throw an exception
        } elseif(self::$how_to_handle_error[$errno] == 'exception') {
          throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        } // if
        
      } // if
    } // handleError
    
    /**
     * Return error type based on error number
     * 
     * @param integer $errno
     * @return string
     */
    static function getErrorType($errno) {
      switch($errno) {
        case E_ERROR:
          return 'Fatal Error';
        case E_WARNING:
          return 'Warning';
        case E_NOTICE:
          return 'Notice';
        case E_STRICT:
          return 'Strict Mode';
        case E_PARSE:
          return 'Syntax Error';
        case E_CORE_ERROR:
          return 'Core Error';
        case E_CORE_WARNING:
          return 'Core Warning';
        case E_COMPILE_ERROR:
          return 'Compile Error';
        case E_COMPILE_WARNING:
          return 'Compile Warning';
        case E_USER_ERROR:
          return 'User Error';
        case E_USER_WARNING:
          return 'User Warning';
        case E_USER_NOTICE:
          return 'User Notice';
        case E_RECOVERABLE_ERROR:
          return 'Recoverable Error';
      } // switch

      return 'Any';
    } // getErrorType
    
    /**
     * Handle fatal error
     *
     * @param Error $error
     */
    static function handleFatalError($error) {
      if(self::isInProduction()) {
        if($error instanceof RoutingError || $error instanceof RouteNotDefinedError) {
          header("HTTP/1.1 404 Not Found");
          print '<h1>Not Found</h1>';
          if($error instanceof RoutingError) {
            print '<p>Page "<em>' . clean($error->getParam('request_string')) . '</em>" not found.</p>';
          } else {
            print '<p>Route "<em>' . clean($error->getParam('name')) . '</em>" not mapped.</p>';
          } // if
          print '<p><a href="' . Router::assemble('homepage') . '">&laquo; Back to homepage</a></p>';
          die();
        } // if
        
        // Send email to administrator
        if(defined('ADMIN_EMAIL') && is_valid_email(ADMIN_EMAIL)) {
          $content = '<p>Hi,</p><p>' . self::getName() . ' setup at ' . clean(ROOT_URL) . ' experienced fatal error. Info:</p>';
          
          ob_start();
          dump_error($error, false);
          $content .= ob_get_clean();
          
          @mail(ADMIN_EMAIL, self::getName() . ' Crash Report', $content, "Content-Type: text/html; charset=utf-8");
        } // if
        
        // log...
        if(defined('ENVIRONMENT_PATH') && class_exists('Logger') && !AngieApplication::isInProduction()) {
          Logger::logToFile(ENVIRONMENT_PATH . '/logs/' . date('Y-m-d') . '.txt');
        } // if
      } else {
        dump_error($error);
      } // if
      
      $error_message = '<div style="text-align: left; background: white; color: red; padding: 7px 15px; border: 1px solid red; font: 12px Verdana; font-weight: normal;">';
      $error_message .= '<p>Fatal error: Application has failed to execute your request (reason: ' . clean(get_class($error)) . '). Information about this error has been logged and sent to administrator.</p>';
      if(is_valid_url(ROOT_URL)) {
        $error_message .= '<p><a href="' . ROOT_URL . '">&laquo; Back to homepage</a></p>';
      } // if
      $error_message .= '</div>';
      
      print $error_message;
      die();
    } // handleFatalError
    
    /**
     * Called on application shutdown
     */
    static function shutdown() {
      $do_log = !AngieApplication::isInProduction();

      if($do_log) {
        AngieApplication::logLastError();
      } // if
      
      try {
        EventsManager::trigger('on_shutdown');
        
        // Lets kill a transaction if we have something open
        if(DB::getConnection('default') instanceof DBConnection && DB::getConnection('default')->isConnected()) {
          DB::getConnection('default')->rollback();
        } // if

        if($do_log) {
          Logger::logToFile(ENVIRONMENT_PATH . '/logs/' . date('Y-m-d') . '.txt');
        } // if
      } catch(Exception $e) {
        if(self::isInProduction()) {
          trigger_error('Error detected on shutdown: ' . $e->getMessage());
        } else {
          dump_error($e);
        } // if
      } // try
    } // shutdown

    /**
     * Log last error message
     */
    static private function logLastError() {
      $last_error = error_get_last();

      if($last_error) {
        $handle = fopen(AngieApplication::getWarningsLogPath(), 'a');
        if($handle) {
          fwrite($handle, date(DATETIME_MYSQL) . ": $last_error[message] (at $last_error[file] on $last_error[line] line)\n");
          fclose($handle);
        } // if
      } // if
    } // logLastError

    /**
     * Return file name for warnings log
     *
     * @return string
     */
    static private function getWarningsLogPath() {
      return ENVIRONMENT_PATH . '/logs/' . (php_sapi_name() == 'cli' ? '_cli_warnings_log.txt' : '_warnings_log.txt');
    } // getWarningsLogPath
    
  }
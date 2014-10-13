<?php

  /**
   * Base angie application class
   *
   * This class implements most of the Angie application initialization routines. 
   * We figured that things were scattered to much in previous setup so we 
   * decided to move everything into one class that can be overriden if user 
   * finds need to change initialization process.
   * 
   * @package angie.library.application
   */
  abstract class AngieApplicationAdapter {
    
    /**
     * Return application name
     *
     * @return string
     */
    abstract function getName();
    
    /**
     * Return application URL
     * 
     * @return string
     */
    abstract function getUrl();
    
    /**
     * Return application version
     *
     * @return string
     */
    function getVersion() {
      return APPLICATION_VERSION;
    } // getVersion
    
    /**
     * Returns true if current application version is stable
     *
     * @return boolean
     */
    abstract function isStable();
    
    /**
     * Return vendor name
     * 
     * @return string
     */
    abstract function getVendor();
    
    /**
     * Return license agreement URL
     * 
     * @return string
     */
    abstract function getLicenseAgreementUrl();

    /**
     * Return check for updates URL
     *
     * @return string
     */
    abstract function getCheckForUpdatesUrl();

    /**
     * Return download update URL
     *
     * @return string
     */
    abstract function getDownloadUpdateUrl();
    
    /**
     * Return application API version
     *
     * @return string
     */
    abstract function getApiVersion();
    
    /**
     * Return something unique to this application setup
     */
    abstract function getUniqueKey();

    /**
     * Return module signature
     *
     * @return string
     */
    abstract function getModuleSignature();
    
    // ---------------------------------------------------
    //  On first run
    // ---------------------------------------------------
    
    /**
     * Do application specific intialization
     */
    function onFirstRun() {
      
    } // onFirstRun

    /**
     * Return module compatibility link
     *
     * @param AngieModule $module
     * @param boolean $module_declared_internal
     * @return string
     */
    function getCompatibilityLink(AngieModule $module, $module_declared_internal = false) {
      return '#';
    } // getCompatibilityLink

    /**
     * Test platform for optimal performance (capture some common platform misconfigurations)
     *
     * @return bool
     */
    function testPlatformForOptimalPerformance() {
      if(version_compare(PHP_VERSION, '5.4.0', '<')) {
        return false;
      } // if

      $cache_backend = AngieApplication::cache()->getBackendType();

      if(empty($cache_backend) || $cache_backend == AngieCacheDelegate::FILESYSTEM_BACKEND) {
        return false;
      } // if

      $opcache_enabled = ini_get('opcache.enable');
      $apc_enabled = extension_loaded('apc') && ini_get('apc.enabled');
      $wincache_enabled = extension_loaded('wincache') && ini_get('wincache.ocenabled');

      if(empty($opcache_enabled) && empty($apc_enabled) && empty($wincache_enabled)) {
        return false;
      } // if

      $apc_shared_memory_size = php_config_value_to_bytes(ini_get('apc.shm_size'));

      if($opcache_enabled && ini_get('opcache.memory_consumption') < 64) {
        return false; // Opcache enabled, but memory size lower than 64M
      } elseif($apc_enabled && $cache_backend == AngieCacheDelegate::APC_BACKEND && $apc_shared_memory_size < 134217728) {
        return false; // APC used for opcode cache and data cache, but cache size lower than 128M
      } elseif($apc_enabled && $apc_shared_memory_size < 67108864) {
        return false; // APC used for opcode cache only, but cache size lower than 64M
      } elseif($wincache_enabled && ini_get('wincache.ocachesize') < 64) {
        return false; // WinCache enabled, but memory size lower than 64
      } // if

      $old_versions = array();
      $folders = get_folders(ROOT);

      if(is_foreachable($folders)) {
        foreach($folders as $folder) {
          $folder_name = basename($folder);

          if(AngieApplication::isValidVersionNumber($folder_name) && $folder_name != APPLICATION_VERSION) {
            $old_versions[] = $folder_name;
          } // if
        } // foreach
      } // if

      if(count($old_versions) > 1) {
        return false;
      } // if

      if(version_compare(APPLICATION_VERSION, '5.0.0', '>=') && DB::getConnection() instanceof MySQLDBConnection && !DB::getConnection()->hasInnoDBSupport()) {
        return false;
      } // if

      return true;
    } // testPlatformForOptimalPerformance
    
    // ---------------------------------------------------
    //  Handlers
    // ---------------------------------------------------
    
    /**
     * Get and handle HTTP request
     * 
     * @param string $path_info
     * @param $query_string
     * @throws RoutingError
     * @throws ControllerDnxError
     */
    function handleHttpRequest($path_info, $query_string) {
      $request = Router::match($path_info, $query_string);
      
      $controller_name = $request->getController(); // we'll use this a lot
      
      AngieApplication::useController($controller_name, $request->getModule());
      
      $controller_class = Inflector::camelize($controller_name) . 'Controller';
      if(!class_exists($controller_class)) {
        throw new ControllerDnxError($controller_name);
      } // if
      
      $controller = new $controller_class($request);
      if($controller instanceof Controller) {
        $controller->__execute($request->getAction());
      } else {
        throw new ControllerDnxError($controller_name);
      } // if
    } // handleHttpRequest
    
  }
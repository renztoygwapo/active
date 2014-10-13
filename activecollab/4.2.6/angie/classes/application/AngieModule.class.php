<?php

  /**
   * Angie module definition
   *
   * @package angie.library.application
   */
  abstract class AngieModule extends AngieFramework {
    
    /**
     * Module version
     *
     * @var string
     */
    protected $version = '1.0';
    
    /**
     * Flag that indicates whether this module is enabled or not
     *
     * @var boolean
     */
    private $is_enabled = false;

    /**
     * Construct new angie module instance
     *
     * @param boolean $is_enabled
     * @param boolean $is_installed
     */
    function __construct($is_enabled = false, $is_installed = null) {
      $this->is_enabled = (boolean) $is_enabled;
      
      if($is_installed !== null) {
        $this->is_installed = (boolean) $is_installed;
      } // if
    } // __construct
    
    /**
     * Cached module path value
     *
     * @var string
     */
    private $path = false;
    
    /**
     * Return full framework path
     *
     * @return string
     */
    function getPath() {
      if($this->path === false) {
        $this->path = APPLICATION_PATH . '/modules/' . $this->name;
        
        if(!is_dir($this->path)) {
          $this->path = CUSTOM_PATH . '/modules/' . $this->name;
        } // if
      } // if
      
      return $this->path;
    } // getPath

    /**
     * Returns true is module is native and false if it is custom module
     *
     * @return bool
     */
    function isNative() {
      return is_dir(APPLICATION_PATH . '/modules/' . $this->name) ? true : false;
    } //isNative
    
    /**
     * Cached options instance
     *
     * @var NamedList
     */
    private $options = false;
    
    /**
     * Return list of module options
     *
     * @param User $user
     * @return NamedList
     */
    function getOptions(User $user) {
      if($this->options === false) {
        $this->options = new NamedList();
        
        if($this->isInstalled()) {
          if($this->canEnable($user)) {
            $this->options->add('enable_disable', array(
              'text' => 'Enable/Disable', 
              'url' => '#',  
              'onclick' => new AsyncTogglerCallback(array(
                'text' => lang('Disable'), 
                'url' => $this->getDisableUrl(), 
                'confirmation' => lang('Are you sure that you want to disable :module_name?', array("module_name" => $this->getDisplayName())),
                'success_message' => lang(':module_name has been disabled', array("module_name" => $this->getDisplayName())),
                'success_event' => 'module_updated',
              ), array(
                  'text' => lang('Enable'),
                  'url' => $this->getEnableUrl(),
                  'confirmation' => lang('Are you sure that you want to enable :module_name', array("module_name" => $this->getDisplayName())),
                  'success_message' => lang(':module_name has been enabled', array("module_name" => $this->getDisplayName())),
                  'success_event' => 'module_updated',
              ), $this->isEnabled()), 
            ));
          } // if
          
          if($this->canUninstall($user)) {
            $this->options->add('uninstall', array(
              'text' => lang('Uninstall'), 
              'url' => $this->getUninstallUrl(), 
              'onclick' => new AsyncLinkCallback(array(
                'confirmation' => lang('Are you sure that you want to uninstall :module_name?', array("module_name" => $this->getDisplayName())),
                'success_message' => lang(':module_name has been successfully uninstalled', array("module_name" => $this->getDisplayName())),
                'success_event' => 'module_deleted', 
              ))
            ));
          } // if
        } else {
          if($this->canInstall($user)) {
            $this->options->add('install', array(
              'text' => lang('Install'), 
              'url' => $this->getInstallUrl(), 
              'onclick' => new AsyncLinkCallback(array(
                'confirmation' => lang('Are you sure that you want to install :module_name?', array("module_name" => $this->getDisplayName())),
                'success_message' => lang('":module_name has been successfully installed', array("module_name" => $this->getDisplayName())),
                'success_event' => 'module_created', 
              ))
            ));
          } // if
        } // if
      } // if
      
      return $this->options;
    } // getOptions
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $urls = array('view' => $this->getViewUrl());
      $permissions = array();
      
      if($this->isInstalled()) {
        $urls['uninstall'] = $this->getUninstallUrl();
        $permissions['can_uninstall'] = $this->canUninstall($user);
      } else {
        $urls['install'] = $this->getInstallUrl();
        $permissions['can_install'] = $this->canInstall($user);
      } // if
      
      if($this->isEnabled()) {
        $urls['disable'] = $this->getDisableUrl();
        $permissions['can_disable'] = $this->canDisable($user);
      } else {
        $urls['enable'] = $this->getEnableUrl();
        $permissions['can_enable'] = $this->canEnable($user);
      } // if
      
      return array(
        'name' => $this->name,
        'icon' => $this->getIconUrl(), 
        'display_name' => $this->getDisplayName(), 
        'version' => $this->getVersion(), 
        'description' => $this->getDescription(), 
        'uninstall_message' => $this->getUninstallMessage(), 
        'is_installed' => $this->isInstalled(), 
        'is_enabled' => $this->isEnabled(), 
        'urls' => $urls,
        'permissions' => $permissions,  
      );
    } // describe
    
    // ---------------------------------------------------
    //  Install / Uninstall / Enable / Disable
    // ---------------------------------------------------
    
    /**
     * Returns true if this module installed
     *
     * @param boolean $use_cache
     * @return boolean
     */
    function isInstalled($use_cache = true) {
      if($this->name == 'system') {
        return true;
      } else {
        if($use_cache) {
          return array_key_exists($this->name, AngieApplication::getInstalledNonSystemModules());
        } else {
          return (boolean) DB::executeFirstCell('SELECT COUNT(name) FROM ' . TABLE_PREFIX . 'modules WHERE name = ?', $this->name);
        } // if
      } // if
    } // isInstalled
    
    /**
     * Returns true if this module is enabled
     *
     * @return boolean
     */
    function isEnabled() {
      return $this->isInstalled() && $this->is_enabled && !AngieApplication::isBlockedForAutoloadError($this);
    } // isEnabled
    
    /**
     * Install this module
     * 
     * $bulk is true when this module is installed as part of a larger module 
     * installation call (like system installation)
     *
     * @param integer $position
     * @param boolean $bulk
     */
    function install($position = null, $bulk = false) {
      parent::install();
      
      $modules_table = TABLE_PREFIX . 'modules';
      
      if($position < 1) {
        $position = DB::executeFirstCell("SELECT MAX(position) FROM $modules_table") + 1;
      } // if
      
      DB::execute("INSERT INTO $modules_table (name, is_enabled, position) VALUES (?, ?, ?)", $this->name, true, $position);

      foreach(AngieApplication::migration()->getScriptsInModule($this) as $migrations) {
        foreach($migrations as $migration) {
          $migration->setAsExecuted();
        } // foreach
      } // foreach
      
      AngieApplication::rebuildModuleAssets($this);
      
      if(!$bulk) {
        AngieApplication::cache()->clear();

        if (!AngieApplication::isOnDemand()) {
          AngieApplication::clearCompiledScripts();
        } // if
      } // if
    } // install

    /**
     * Execute after module has been installed through web interface
     *
     * @param User $user
     */
    function postInstall(User $user) {

    } // postInstall
    
    /**
     * Uninstall this module
     *
     * @throws Error
     * @throws Exception
     */
    function uninstall() {
      if($this->isInstalled()) {
        try {
          DB::beginWork('Uninstalling module @ ' . __CLASS__);
          
          ConfigOptions::deleteByModule($this->name);
          Notifications::deleteByModule($this);

          if(AngieApplication::isFrameworkLoaded('homescreens')) {
            HomescreenTabs::deleteByModule($this);
            HomescreenWidgets::deleteByModule($this);
          } // if

          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'modules WHERE name = ?', $this->name);

          foreach(AngieApplication::migration()->getScriptsInModule($this) as $migrations) {
            foreach($migrations as $migration) {
              $migration->setAsNotExecuted();
            } // foreach
          } // foreach
         
          parent::uninstall();

          AngieApplication::cache()->clear();

          if (!AngieApplication::isOnDemand()) {
            Router::cleanUpCache(true);
          } // if
          
          DB::commit('Module uninstalled @ ' . __CLASS__);

          if (!AngieApplication::isOnDemand()) {
            AngieApplication::cleanModuleAssets($this);
            AngieApplication::clearCompiledScripts();
          } // if

        } catch(Exception $e) {
          DB::rollback('Failed to uninstall module @ ' . __CLASS__);
          throw $e;
        } // try
      } else {
        throw new Error("Module $this->name is not installed");
      } // if
    } // uninstall
    
    /**
     * Enable this module
     */
    function enable() {
      if(!$this->isEnabled()) {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'modules SET is_enabled = ? WHERE name = ?', true, $this->getName());

        AngieApplication::cache()->clear();

        if (!AngieApplication::isOnDemand()) {
          AngieApplication::clearCompiledScripts();
        } // if
        
        $this->is_enabled = true;
      } // if
    } // enable
    
    /**
     * Disable this module
     */
    function disable() {
      if($this->name == SYSTEM_MODULE) {
        throw new Error('System module can not be disabled');
      } // if
      
      if($this->isEnabled()) {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'modules SET is_enabled = ? WHERE name = ?', false, $this->getName());

        Notifications::deleteByModule($this); // Make sure that we do not have left overs when module gets disabled

        AngieApplication::cache()->clear();

        if (!AngieApplication::isOnDemand()) {
          AngieApplication::clearCompiledScripts();
        } // if

        $this->is_enabled = false;
       } // if
    } // disable
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can install this module
     *
     * @param User $user
     * @return boolean
     */
    function canInstall(User $user) {
      if($this->isInstalled()) {
        return false; // Already installed
      } else {
        $log = array();
        return $user->isAdministrator() && $this->canBeInstalled($log);
      } // if
    } // canInstall
    
    /**
     * Can this module be installed or not
     *
     * @param array $log
     * @return boolean
     */
    function canBeInstalled(&$log) {
      return true;
    } // canBeInstalled
    
    /**
     * Returns true if this module can be uninstalled
     *
     * @param User $user
     * @return boolean
     */
    function canUninstall(User $user) {
      return $this->isInstalled() && $this->name != SYSTEM_MODULE && $user->isAdministrator();
    } // canUninstall
    
    /**
     * Returns true if $user can enable or disable this module
     *
     * @param User $user
     * @return boolean
     */
    function canEnable(User $user) {
      return $user->isAdministrator();
    } // canEnable
    
    /**
     * Returns true if $user can enable or disable this module
     *
     * @param User $user
     * @return boolean
     */
    function canDisable(User $user) {
      return $this->name != SYSTEM_MODULE && $user->isAdministrator();
    } // canDisable
    
    // ---------------------------------------------------
    //  Describe
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return Inflector::humanize($this->name);
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('No module description provided');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Are you sure that you want to uninstall :module_name?', array("module_name" => $this->getDisplayName()));
    } // getUninstallMessage
    
    /**
     * Return module version
     *
     * @return mixed
     */
    function getVersion() {
      return $this->version;
    } // getVersion

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return null;
    } // getObjectTypes
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return module icon URL
     */
    function getIconUrl() {
    	if ($this->isEnabled()) {
    		return AngieApplication::getImageUrl('module.png', $this->name);
    	} else {
    		return ROOT_URL . '/proxy.php?proxy=module_icon&module=environment&module_name=' . $this->name;
    	} // if
    } // getIconUrl
    
    /**
     * Return details URL
     *
     * @return string
     */
    function getViewUrl() {
      return Router::assemble('module_admin_module', array('module_name' => $this->name));
    } // getViewUrl
    
    /**
     * Return install module URL
     *
     * @return string
     */
    function getInstallUrl() {
      return Router::assemble('module_admin_module_install', array('module_name' => $this->name));
    } // getInstallUrl
    
    /**
     * Return uninstall module URL
     *
     * @return string
     */
    function getUninstallUrl() {
      return Router::assemble('module_admin_module_uninstall', array('module_name' => $this->name));
    } // getUninstallUrl
    
    /**
     * Return enable module URL
     *
     * @return string
     */
    function getEnableUrl() {
      return Router::assemble('module_admin_module_enable', array('module_name' => $this->name));
    } // getEnableUrl
    
    /**
     * Return disable module URL
     *
     * @return string
     */
    function getDisableUrl() {
      return Router::assemble('module_admin_module_disable', array('module_name' => $this->name));
    } // getDisableUrl
    
  }
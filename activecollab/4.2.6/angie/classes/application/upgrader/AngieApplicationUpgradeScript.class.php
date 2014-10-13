<?php

  /**
   * Foundation for all upgrade scripts
   * 
   * @package angie.library.application
   * @subpackage upgrader
   */
  abstract class AngieApplicationUpgradeScript {
  
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version;
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version;
    
    /**
     * Return upgrade actions
     *
     * @return array
     */
    function getActions() {
    	return null;
    } // getActions
    
    /**
     * Return from version
     *
     * @return float
     */
    function getFromVersion() {
    	return $this->from_version;
    } // getFromVersion
    
    /**
     * Return to version
     *
     * @return float
     */
    function getToVersion() {
    	return $this->to_version;
    } // getToVersion

    /**
     * Returns true if we should run this script
     *
     * @param string $current_version
     * @return boolean
     */
    function isExecuted($current_version) {
      return version_compare($this->getToVersion(), $current_version, '<=');
    } // isExecuted
    
    /**
     * Identify this uprade script
     *
     * @return string
     */
    function getGroup() {
    	return (string) $this->from_version . '-' . (string) $this->to_version;
    } // getGroup
    
    /**
     * Start upgrade by creating backup
     *
     * @return boolean
     */
    function startUpgrade() {
      $version_file = $this->getVersionFilePath();
      
      if(is_file($version_file)) {
        if(!is_writable($version_file)) {
          return 'Version file not writable';
        } // if
      } else {
        return 'Version file not found';        
      } // if
      
      $work_path = ENVIRONMENT_PATH . '/work';
      if(is_dir($work_path)) {
        if(is_writable($work_path)) {
          $tables = DB::listTables(TABLE_PREFIX);
          if(is_foreachable($tables)) {
            try {
              DB::exportToFile($tables, $work_path . '/database-backup-' . date('Y-m-d-H-i-s') . '.sql', true, true);
            } catch(Exception $e) {
              return $e->getMessage();
            } // try
            
            return true;
          } else {
            return 'There are no activeCollab tables in the database';
          } // if
        } else {
          return "Work folder not writable";
        } // if
      } else {
        return "Work folder not found. Expected location: $work_path";
      } // if
    } // startUpgrade

    /**
     * Schedule index rebuild
     *
     * @return boolean
     */
    function scheduleIndexesRebuild() {
      try {
        $config_options_table = TABLE_PREFIX . 'config_options';

        if(DB::executeFirstCell("SELECT COUNT(*) FROM $config_options_table WHERE name = 'require_index_rebuild'")) {
          DB::execute("UPDATE $config_options_table SET value = ? WHERE name = 'require_index_rebuild'", serialize(true));
        } else {
          DB::execute("INSERT INTO $config_options_table (name, module, value) VALUES ('require_index_rebuild', 'system', ?)", serialize(true));
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // scheduleIndexesRebuild
    
    /**
     * This action will write entry in upgrade history
     *
     * @return boolean
     */
    function endUpgrade() {
      try {
        $final_version = $this->getToVersion();
        
        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'update_history SET version = ?, created_on = UTC_TIMESTAMP()', array($final_version));
        file_put_contents($this->getVersionFilePath(), "<?php\n\n  define('APPLICATION_VERSION', " . var_export($final_version, true) . ");");

        // Clear opcode cache if we have APC installed and running. We are calling clear function twice, because of the
        // reports where people claim that cache does not get properly cleared without 'opcode' parameter
        if(extension_loaded('apc') && function_exists('apc_clear_cache')) {
          apc_clear_cache();
          apc_clear_cache('opcode');
        } // if

        // Get frameworks
        $frameworks = explode(',', APPLICATION_FRAMEWORKS);

        // Make sure that we also rebuild data from the new frameworks, if there are any
        foreach(get_folders(ROOT . "/$final_version/angie/frameworks") as $framework_path) {
          $framework_name = basename($framework_path);

          if(!in_array($framework_name, $frameworks)) {
            $frameworks[] = $framework_name;
          } // if
        } // foreach

        $modules = DB::executeFirstColumn('SELECT name FROM ' . TABLE_PREFIX . 'modules WHERE is_enabled = ?', true);
        
        AngieApplication::rebuildLocalization($frameworks, $modules, $final_version);
        AngieApplication::rebuildAssets($frameworks, $modules, $final_version);

        // ---------------------------------------------------
        //  Clear cache
        // ---------------------------------------------------

        if(function_exists('cache_clear')) {
          cache_clear(true); // activeCollab 3.2 or older
        } else {
          AngieApplication::cache()->clear(); // activeCollab 3.3 or up
        } // if

        // ---------------------------------------------------
        //  Clear compiled scripts
        // ---------------------------------------------------

        if(method_exists('AngieApplication', 'clearCompiledScripts')) {
          AngieApplication::clearCompiledScripts(); // activeCollab 3.3 or up
        } else {
          if(is_dir(COMPILE_PATH)) {
            foreach(glob(with_slash(COMPILE_PATH) . '*.php') as $v){
              @unlink($v);
            } // foreach
          } // if
        } // if

        // Truncate routing cache to avoid potential issues with broken links
        DB::execute("TRUNCATE TABLE " . TABLE_PREFIX . "routing_cache");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // endUpgrade
    
    // ---------------------------------------------------
    //  Utilities
    // ---------------------------------------------------

    /**
     * Cached array of isModuleInstalled() results
     *
     * @var array
     */
    private $is_module_installed = array();

    /**
     * Returns true if module is installed
     *
     * @param $module_name
     * @return boolean
     */
    function isModuleInstalled($module_name) {
      if(!array_key_exists($module_name, $this->is_module_installed)) {
        $this->is_module_installed[$module_name] = (boolean) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'modules WHERE name = ?', $module_name);
      } // if

      return $this->is_module_installed[$module_name];
    } // isModuleInstalled
    
    /**
     * Return path of the version file
     * 
     * @return string
     */
    function getVersionFilePath() {
      return CONFIG_PATH . '/version.php';
    } // getVersionFilePath

    /**
     * Return list of table fields
     *
     * @param string $table_name
     * @return array
     */
    function listTableFields($table_name) {
      return DB::listTableFields($table_name);
    } // listTableColumns

    /**
     * Return list of table indexes
     *
     * @param string $table_name
     * @return array
     */
    function listTableIndexes($table_name) {
      if(method_exists('DB', 'listTableIndexes')) {
        return DB::listTableIndexes($table_name);
      } else {
        $rows = DB::execute("SHOW INDEXES FROM $table_name");
        if(is_foreachable($rows)) {
          $indexes = array();

          foreach($rows as $row) {
            $key_name = $row['Key_name'];

            if(!in_array($key_name, $indexes)) {
              $indexes[] = $key_name;
            } // if
          } // foreach

          return $indexes;
        } // if

        return array();
      } // if
    } // listTableIndexes

    /**
     * Find the first administrator on the system
     *
     * @return array
     * @throws Exception
     */
    protected function getFirstAdministrator() {
      $users_table = TABLE_PREFIX . 'users';

      // activeCollab v4.0.0 or newer
      if(in_array('type', DB::listTableFields($users_table))) {
        $admin_user = DB::executeFirstRow("SELECT id, first_name, last_name, email FROM $users_table WHERE type = 'Administrator' ORDER BY id ASC");

      // activeCollab older than v4.0.0
      } else {
        $roles_table = TABLE_PREFIX . 'roles';

        // find roles with has_admin_access permission
        $admin_roles = array();
        $system_roles = DB::execute("SELECT id, permissions FROM $roles_table ORDER BY id ASC");
        if (is_foreachable($system_roles)) {
          foreach ($system_roles as $system_role) {
            $permissions = $system_role['permissions'] ? unserialize($system_role['permissions']) : array();

            if((isset($permissions['admin_access']) && $permissions['admin_access']) || (isset($permissions['has_admin_access']) && $permissions['has_admin_access'])) {
              $admin_roles[] = $system_role['id'];
            } // if
          }; // foreach
        } else  {
          throw new Exception('There are no system roles with admin_access or has_admin_access permission on the system');
        } // if

        // find first administrator
        $admin_user = DB::executeFirstRow("SELECT id, first_name, last_name, email FROM $users_table WHERE role_id IN (?) ORDER BY id ASC", $admin_roles);
      } // if

      if (!is_foreachable($admin_user)) {
        throw new Exception('There are no administrators on the system');
      } // if

      // extract required data from first administrator row
      $admin_user_id = array_var($admin_user, 'id');
      $admin_first_name = trim(array_var($admin_user, 'first_name'));
      $admin_last_name = trim(array_var($admin_user, 'last_name'));
      $admin_email_address = trim(array_var($admin_user, 'email'));

      $admin_display_name = 'Administrator';
      if ($admin_first_name && $admin_last_name) {
        $admin_display_name = $admin_first_name . ' ' . $admin_last_name;
      } else if ($admin_first_name) {
        $admin_display_name = $admin_first_name;
      } else if ($admin_last_name) {
        $admin_display_name = $admin_last_name;
      } // if

      return array($admin_user_id, $admin_display_name, $admin_email_address);
    } // getFirstAdministrator

    /**
     * Add a new configuration option value
     *
     * @param string $name
     * @param mixed $value
     * @param string $module
     */
    function addConfigOption($name, $value = null, $module = 'system') {
      $config_options_table = TABLE_PREFIX . 'config_options';

      if(DB::executeFirstCell("SELECT COUNT(name) FROM $config_options_table WHERE name = ?", $name)) {
        DB::execute("UPDATE $config_options_table SET value = ? WHERE name = ?", serialize($value), $name);
      } else {
        DB::execute("INSERT INTO $config_options_table (name, module, value) VALUES (?, ?, ?)", $name, $module, serialize($value));
      } // if
    } // addConfigOption

    /**
     * Return config option value
     *
     * @param string $name
     * @return mixed|null
     */
    function getConfigOptionValue($name) {
      $value = DB::executeFirstCell('SELECT value FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', $name);

      return $value ? unserialize($value) : null;
    } // getConfigOptionValue

    /**
     * Update configuration option
     *
     * @param string $name
     * @param mixed $value
     */
    function updateConfigOption($name, $value = null) {
      $config_options_table = TABLE_PREFIX . 'config_options';

      if(DB::executeFirstCell("SELECT COUNT(name) FROM $config_options_table WHERE name = ?", $name)) {
        DB::execute("UPDATE $config_options_table SET value = ? WHERE name = ?", serialize($value), $name);
      } else {
        $this->addConfigOption($name, $value);
      } // if
    } // updateConfigOption

    /**
     * Remove configuration option from the system
     *
     * @param string $name
     * @throws Exception
     */
    function removeConfigOption($name) {
      try {
        DB::beginWork('Removing configuration option @ ' . __CLASS__);

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_option_values WHERE name = ?', $name);
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', $name);

        DB::commit('Configuration option has been removed @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove configuration option @ ' . __CLASS__);
        throw $e;
      } // try
    } // removeConfigOption
    
  }
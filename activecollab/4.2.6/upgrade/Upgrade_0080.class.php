<?php

  /**
   * Update activeCollab 3.3.20 to activeCollab 4.0.0
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0080 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.20';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.0';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'scheduleIndexesRebuild' => 'Schedule index rebuild',
        'insertColorSchemesOptions' => 'Prepare color schema configuration',
        'mailToProject' => 'Add Mail To Project Interceptor',
        'createAnnouncementsTables' => 'Create Announcements tables',
        'initNotifications' => 'Initialize notification center',
        'updateHomescreens' => 'Update home screen settings',
        'upgradeRolesAndPermissions' => 'Update user roles and permissions',
        'cleanUpHomescreenConfig' => 'Clean up home screen configuration',
	      'createSecurityLogsTable' => 'Create Security Logs table',
	      'initFirewall' => 'Initialize firewall',
        'initProjectTemplates' => 'Initialize project templates',
        'insertOnDemandConfigOptions' => 'Add On Demand Config Options',
        'insertProjectSyncConfigOptions' => 'Add Project Synchronization Config Options',
        'cleanUpMassMailerNotifications' => 'Clean up Mass Mailer notifications',
        'updateTodoItems' => 'Update to do items',
        'updateCoreModules' => 'Update core modules',
      );
    } // getActions

    /**
     * Setup auto update
     *
     * @return bool|string
     */
    function insertColorSchemesOptions() {
      try {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('current_scheme', 'system', ?)", serialize('default'));
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('custom_schemes', 'system', ?)", serialize(null));
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // insertColorSchemesOptions

    /**
     * Setup auto update
     *
     * @return bool|string
     */
    function insertOnDemandConfigOptions() {
      try {
        if(DB::tableExists(TABLE_PREFIX . 'on_demand_statuses')) {
          DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('on_demand_payment_period', 'system', ?)", serialize('monthly'));
          DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('on_demand_plan_version', 'system', ?)", serialize(1));
        } //if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // insertOnDemandConfigOptions

    /**
     * Recalculate Invoices balance due
     *
     * @return boolean
     */
    function mailToProject() {
      try {
        $project_table = TABLE_PREFIX . 'projects';

        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('mail_to_project', 'system', 'i:0;')");
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('mail_to_project_default_action', 'system', ?)", serialize('task:'));
        DB::execute("ALTER TABLE $project_table ADD mail_to_project_code VARCHAR(10) NOT NULL DEFAULT 0 AFTER custom_field_3");

        $projects = DB::execute("SELECT id FROM $project_table");
        if(is_foreachable($projects)) {
          foreach($projects as $project) {
            do {
              $string = microtime();
              $m2pcode = substr(sha1($string), 0, 7);
            } while (DB::executeFirstCell("SELECT id FROM $project_table WHERE mail_to_project_code = ?", $m2pcode) != null);
            DB::execute("UPDATE $project_table SET mail_to_project_code = ? WHERE id = ?", $m2pcode, $project['id']);
          } //foreach
        } //if

        DB::execute("ALTER TABLE $project_table ADD UNIQUE INDEX (mail_to_project_code)");

      } catch (Exception $e) {
        return $e->getMessage();
      } // try
      return true;
    } // mailToProject

    /**
     * Create Announcements Tables
     *
     * @return bool|string
     */
    function createAnnouncementsTables() {
      try {
        $announcements_table = TABLE_PREFIX . 'announcements';
        $announcement_target_ids_table = TABLE_PREFIX . 'announcement_target_ids';
        $announcement_dismissals_table = TABLE_PREFIX . 'announcement_dismissals';

        $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

        // create announcements table
        if(!in_array($announcements_table, DB::listTables(TABLE_PREFIX))) {
          DB::execute("CREATE TABLE $announcements_table (
            id int(10) unsigned NOT NULL auto_increment,
            subject varchar(255) DEFAULT NULL,
            body longtext,
            body_type tinyint(3) unsigned NOT NULL DEFAULT 0,
            icon enum('announcement','bug','comment','event','idea','info','joke','news','question','star','warning','welcome') NOT NULL DEFAULT 'announcement',
            target_type varchar(50) DEFAULT NULL,
            expiration_type varchar(50) DEFAULT NULL,
            expires_on date DEFAULT NULL,
            created_on datetime DEFAULT NULL,
            created_by_id int(10) unsigned DEFAULT NULL,
            created_by_name varchar(100) DEFAULT NULL,
            created_by_email varchar(150) DEFAULT NULL,
            is_enabled tinyint(1) unsigned NOT NULL DEFAULT 0,
            position int(10) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        } // if

        // create announcement target IDs table
        if(!in_array($announcement_target_ids_table, DB::listTables(TABLE_PREFIX))) {
          DB::execute("CREATE TABLE $announcement_target_ids_table (
            id int(10) unsigned NOT NULL auto_increment,
            announcement_id int(10) unsigned NOT NULL DEFAULT 0,
            target_id varchar(50) NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        } // if

        // create announcement dismissals table
        if(!in_array($announcement_dismissals_table, DB::listTables(TABLE_PREFIX))) {
          DB::execute("CREATE TABLE $announcement_dismissals_table (
            id int(10) unsigned NOT NULL auto_increment,
            announcement_id int(10) unsigned NOT NULL DEFAULT 0,
            user_id int(10) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // createAnnouncementsTables

    /**
     * Initialize notifications model
     *
     * @return bool|string
     */
    function initNotifications() {
      try {
        $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "notifications (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'Notification',
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          sender_id int(10) unsigned NULL DEFAULT NULL,
          sender_name varchar(100)  DEFAULT NULL,
          sender_email varchar(150)  DEFAULT NULL,
          created_on datetime  DEFAULT NULL,
          raw_additional_properties longtext ,
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX parent (parent_type, parent_id),
          INDEX sender_id (sender_id),
          INDEX created_on (created_on)
        ) engine=$engine DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
        
        DB::execute("CREATE TABLE " . TABLE_PREFIX . "notification_recipients (
          id int unsigned NOT NULL auto_increment,
          notification_id int(5) unsigned NULL DEFAULT NULL,
          recipient_id int(10) unsigned NULL DEFAULT NULL,
          recipient_name varchar(100)  DEFAULT NULL,
          recipient_email varchar(150)  DEFAULT NULL,
          seen_on datetime  DEFAULT NULL,
          read_on datetime  DEFAULT NULL,
          PRIMARY KEY (id),
          INDEX recipient_id (recipient_id),
          UNIQUE notification_recipient (notification_id, recipient_email),
          INDEX seen_on (seen_on),
          INDEX read_on (read_on)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

        $this->addConfigOption('notifications_show_indicators', 2);
        $this->addConfigOption('email_notifications_enabled', true);
        $this->addConfigOption('who_can_override_channel_settings', array(
          'email' => array('Member', 'Manager')
        ));
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // initNotifications

    /**
     * Update home screens so all users have their home screen configuration
     *
     * @return bool|string
     */
    function updateHomescreens() {
      try {
        $homescreens_table = TABLE_PREFIX . 'homescreens';
        $homescreen_tabs_table = TABLE_PREFIX . 'homescreen_tabs';
        $homescreen_widgets_table = TABLE_PREFIX . 'homescreen_widgets';

        defined('STATE_TRASHED') or define('STATE_TRASHED', 1);

        /**
         * Create a home screen copy and attach it to a differnet user
         *
         * @param integer $homescreen_id
         * @param string $parent_type
         * @param integer $parent_id
         * @return integer
         */
        $copy_homescreen = function($homescreen_id, $parent_type, $parent_id) use ($homescreens_table, $homescreen_tabs_table, $homescreen_widgets_table) {
          DB::execute("INSERT INTO $homescreens_table (type, parent_type, parent_id) VALUES ('Homescreen', ?, ?)", $parent_type, $parent_id);

          $new_homescreen_id = DB::lastInsertId();

          $tabs = DB::execute("SELECT * FROM $homescreen_tabs_table WHERE homescreen_id = ?", $homescreen_id);
          if($tabs) {
            $tabs->setCasting(array(
              'id' => DBResult::CAST_INT,
            ));

            $tabs_map = array();

            foreach($tabs as $tab) {
              DB::execute("INSERT INTO $homescreen_tabs_table (type, homescreen_id, name, position, raw_additional_properties) VALUES (?, ?, ?, ?, ?)", $tab['type'], $new_homescreen_id, $tab['name'], $tab['position'], $tab['raw_additional_properties']);

              $tabs_map[$tab['id']] = DB::lastInsertId();
            } // foreach

            $widgets = DB::execute("SELECT * FROM $homescreen_widgets_table WHERE homescreen_tab_id IN (?)", array_keys($tabs_map));
            if($widgets) {
              $batch = new DBBatchInsert($homescreen_widgets_table, array('type', 'homescreen_tab_id', 'column_id', 'position', 'raw_additional_properties'));

              foreach($widgets as $widget) {
                $homescreen_tab_id = (integer) $widget['homescreen_tab_id'];

                $batch->insert($widget['type'], $tabs_map[$homescreen_tab_id], $widget['column_id'], $widget['position'], $widget['raw_additional_properties']);
              } // foreach

              $batch->done();
            } // if
          } // if
        }; // copy_homescreen

        DB::beginWork('Updating home screens @ ' . __CLASS__);

        $users_with_homescreens = DB::executeFirstColumn("SELECT DISTINCT parent_id FROM $homescreens_table WHERE parent_type = 'User'");

        if(empty($users_with_homescreens)) {
          $users_with_homescreens = array();
        } // if

        $role_homescreens = DB::execute("SELECT id, parent_id AS 'role_id' FROM $homescreens_table WHERE parent_type = 'Role'");
        if($role_homescreens) {
          $role_homescreens->setCasting(array(
            'id' => DBResult::CAST_INT,
            'role_id' => DBResult::CAST_INT,
          ));

          foreach($role_homescreens as $role_homescreen) {
            if(count($users_with_homescreens)) {
              $role_user_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE id NOT IN (?) AND role_id = ? AND state >= ?', $users_with_homescreens, $role_homescreen['role_id'], STATE_TRASHED);
            } else {
              $role_user_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE role_id = ? AND state >= ?', $role_homescreen['role_id'], STATE_TRASHED);
            } // if

            if($role_user_ids) {
              foreach($role_user_ids as $role_user_id) {
                $role_user_id = (integer) $role_user_id;

                $copy_homescreen($role_homescreen['id'], 'User', $role_user_id);
                $users_with_homescreens[] = $role_user_id; // This user now has a custom home screen
              } // foreach
            } // if
          } // foreach

          if(count($users_with_homescreens)) {
            $user_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE id NOT IN (?) AND state >= ?', $users_with_homescreens, STATE_TRASHED);
          } else {
            $user_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'users WHERE state >= ?', STATE_TRASHED);
          } // if

          if($user_ids) {
            $default_homescreen_id = (integer) DB::executeFirstCell("SELECT id FROM $homescreens_table WHERE (parent_type IS NULL OR parent_type = '') AND (parent_id IS NULL OR parent_id = '0')");

            if($default_homescreen_id) {
              foreach($user_ids as $user_id) {
                $copy_homescreen($default_homescreen_id, 'User', $user_id);
              } // foreach
            } // if
          } // if
        } // if

        DB::commit('Home screens updated @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to update home screens @ ' . __CLASS__);
        return $e->getMessage();
      } // try

      return true;
    } // updateHomescreens

    /**
     * Upgrade roles and permissions
     *
     * @return bool|string
     */
    function upgradeRolesAndPermissions() {
      try {
        $users_table = TABLE_PREFIX . 'users';
        $roles_table = TABLE_PREFIX . 'roles';

        $owner_company_id = DB::executeFirstCell('SELECT id FROM ' . TABLE_PREFIX . 'companies WHERE is_owner = ? ORDER BY id LIMIT 0, 1', true);

        if($owner_company_id) {
          list($admin_user_id, $admin_display_name, $admin_email_address) = $this->getFirstAdministrator();

          DB::execute("ALTER TABLE $users_table ADD type VARCHAR(50) NOT NULL DEFAULT 'Client' AFTER id");
          DB::execute("ALTER TABLE $users_table ADD INDEX (type)");
          DB::execute("ALTER TABLE $users_table ADD raw_additional_properties longtext null default null AFTER last_activity_on");

          try {
            DB::beginWork('Updating roles and permissions @ ' . __CLASS__);

            $roles = DB::execute("SELECT * FROM $roles_table");
            if($roles) {
              $roles->setCasting(array(
                'id' => DBResult::CAST_INT,
              ));

              $inactive_role_ids = array();

              // ---------------------------------------------------
              //  Third party modules
              // ---------------------------------------------------

              /**
               * Carry over permissions from Apps Magnet modules
               *
               * @param array $role_permissions
               * @param array $custom_permissions
               * @param boolean $hide_people_is_installed
               * @param boolean $planning_is_installed
               * @param boolean $reports_plus_is_installed
               * @param boolean $communications_is_installed
               */
              $apply_apps_magnet_permissions = function($role_permissions, &$custom_permissions, $hide_people_is_installed, $planning_is_installed, $reports_plus_is_installed, $communications_is_installed) {
                $to_check = array();

                if($hide_people_is_installed) {
                  $to_check[] = 'can_see_people';
                } // if

                if($planning_is_installed) {
                  $to_check[] = 'can_see_planning';
                  $to_check[] = 'can_update_planning';
                  $to_check[] = 'can_see_outline';
                  $to_check[] = 'can_see_timeline';
                  $to_check[] = 'can_see_cards';
                } // if

                if($reports_plus_is_installed) {
                  $to_check[] = 'can_use_reports';
                  $to_check[] = 'can_see_reports_people';
                  $to_check[] = 'can_see_reports_companies';
                  $to_check[] = 'can_see_time_reports';
                } // if

                if($communications_is_installed) {
                  $to_check[] = 'can_use_communications';
                  $to_check[] = 'can_show_communication_on_dashboard';
                } // if

                foreach($to_check as $permission) {
                  if(isset($role_permissions[$permission]) && $role_permissions[$permission]) {
                    $custom_permissions[] = $permission;
                  } // if
                } // foreach
              };

              $hide_people_is_installed = $this->isModuleInstalled('hide_people');
              $planning_is_installed = $this->isModuleInstalled('planning');
              $reports_plus_is_installed = $this->isModuleInstalled('reports_plus');
              $communications_is_installed = $this->isModuleInstalled('communications');

              foreach($roles as $role) {
                $role_id = $role['id'];
                $role_permissions = $role['permissions'] ? unserialize($role['permissions']) : null;

                if(!is_array($role_permissions)) {
                  $role_permissions = array();
                } // if

                if(isset($role_permissions['has_system_access']) && $role_permissions['has_system_access']) {
                  $is_admin = isset($role_permissions['has_admin_access']) && $role_permissions['has_admin_access'];

                  $can_manage_projects = isset($role_permissions['can_manage_projects']) && $role_permissions['can_manage_projects'];
                  $can_manage_project_requests = isset($role_permissions['can_manage_project_requests']) && $role_permissions['can_manage_project_requests'];
                  $can_manage_people = isset($role_permissions['can_manage_people']) && $role_permissions['can_manage_people'];
                  $can_manage_finances = isset($role_permissions['can_manage_finances']) && $role_permissions['can_manage_finances'];
                  $can_manage_quotes = isset($role_permissions['can_manage_quotes']) && $role_permissions['can_manage_quotes'];

                  $can_see_private_objects = isset($role_permissions['can_see_private_objects']) && $role_permissions['can_see_private_objects'];

                  if($can_see_private_objects && ($can_manage_projects || $can_manage_project_requests || $can_manage_people || $can_manage_finances || $can_manage_quotes)) {
                    $is_manager = true; // Ok, this is manager
                  } else {
                    $is_manager = $can_manage_projects; // This user used to be able to see private objects, and thefore keep it as employee
                  } // if

                  // Set all administrators
                  if($is_admin) {
                    $custom_permissions = array();

                    if($can_manage_finances) {
                      $custom_permissions[] = 'can_manage_finances';
                    } // if

                    if($can_manage_quotes) {
                      $custom_permissions[] = 'can_manage_quotes';
                    } // if

                    // Carry over AppsMagnet permissions if their modules are installed
                    $apply_apps_magnet_permissions($role_permissions, $custom_permissions, $hide_people_is_installed, $planning_is_installed, $reports_plus_is_installed, $communications_is_installed);

                    $this->updateByRole($role_id, 'Administrator', $custom_permissions);

                  // Managers
                  } elseif($is_manager) {
                    $custom_permissions = array();

                    if(isset($role_permissions['can_use_api']) && $role_permissions['can_use_api']) {
                      $custom_permissions[] = 'can_use_api';
                    } // if

                    if(isset($role_permissions['can_manage_trash']) && $role_permissions['can_manage_trash']) {
                      $custom_permissions[] = 'can_manage_trash';
                    } // if

                    if($can_manage_projects) {
                      $custom_permissions[] = 'can_manage_projects';
                    } // if

                    if($can_manage_project_requests) {
                      $custom_permissions[] = 'can_manage_project_requests';
                    } // if

                    if($can_manage_people) {
                      $custom_permissions[] = 'can_manage_people';
                    } // if

                    if($this->isModuleInstalled('invoicing')) {
                      if($can_manage_finances) {
                        $custom_permissions[] = 'can_manage_finances';
                      } // if

                      if($can_manage_quotes) {
                        $custom_permissions[] = 'can_manage_quotes';
                      } // if
                    } // if

                    if($this->isModuleInstalled('status') && (isset($role_permissions['can_use_status_updates']) && $role_permissions['can_use_status_updates'])) {
                      $custom_permissions[] = 'can_use_status_updates';
                    } // if

                    if($this->isModuleInstalled('documents')) {
                      $use_documents = isset($role_permissions['can_use_documents']) && $role_permissions['can_use_documents'];
                      $add_documents = isset($role_permissions['can_add_documents']) && $role_permissions['can_add_documents'];
                      $manage_documents = isset($role_permissions['can_manage_documents']) && $role_permissions['can_manage_documents'];

                      if($use_documents && ($add_documents || $manage_documents)) {
                        $custom_permissions[] = 'can_manage_documents';
                      } // if
                    } // if

                    // Carry over AppsMagnet permissions if their modules are installed
                    $apply_apps_magnet_permissions($role_permissions, $custom_permissions, $hide_people_is_installed, $planning_is_installed, $reports_plus_is_installed, $communications_is_installed);

                    $this->updateByRole($role_id, 'Manager', $custom_permissions);

                  // Members and Clients
                  } else {

                    // Member
                    if($can_see_private_objects) {
                      $custom_permissions = array();

                      if(isset($role_permissions['can_use_api']) && $role_permissions['can_use_api']) {
                        $custom_permissions[] = 'can_use_api';
                      } // if

                      if(isset($role_permissions['can_manage_trash']) && $role_permissions['can_manage_trash']) {
                        $custom_permissions[] = 'can_manage_trash';
                      } // if

                      if(isset($role_permissions['can_see_project_budgets']) && $role_permissions['can_see_project_budgets']) {
                        $custom_permissions[] = 'can_see_project_budgets';
                      } // if

                      if(isset($role_permissions['can_see_company_notes']) && $role_permissions['can_see_company_notes']) {
                        $custom_permissions[] = 'can_see_company_notes';
                      } // if

                      if(isset($role_permissions['can_add_project']) && $role_permissions['can_add_project']) {
                        $custom_permissions[] = 'can_add_projects';
                      } // if

                      if($this->isModuleInstalled('status') && (isset($role_permissions['can_use_status_updates']) && $role_permissions['can_use_status_updates'])) {
                        $custom_permissions[] = 'can_use_status_updates';
                      } // if

                      if($this->isModuleInstalled('documents') && isset($role_permissions['can_use_documents']) && $role_permissions['can_use_documents']) {
                        $custom_permissions[] = 'can_use_documents';
                      } // if

                      // Carry over AppsMagnet permissions if their modules are installed
                      $apply_apps_magnet_permissions($role_permissions, $custom_permissions, $hide_people_is_installed, $planning_is_installed, $reports_plus_is_installed, $communications_is_installed);

                      $this->updateByRole($role_id, 'Member', $custom_permissions);

                    // Client
                    } else {
                      $custom_permissions = array();

                      if(isset($role_permissions['can_use_api']) && $role_permissions['can_use_api']) {
                        $custom_permissions[] = 'can_use_api';
                      } // if

                      if(isset($role_permissions['can_manage_company_details']) && $role_permissions['can_manage_company_details']) {
                        $custom_permissions[] = 'can_request_projects';

                        if($this->isModuleInstalled('invoicing')) {
                          $custom_permissions[] = 'can_manage_client_finances';
                        } // if
                      } // if

                      if($this->isModuleInstalled('documents') && isset($role_permissions['can_use_documents']) && $role_permissions['can_use_documents']) {
                        $custom_permissions[] = 'can_use_documents';
                      } // if

                      // Carry over AppsMagnet permissions if their modules are installed
                      $apply_apps_magnet_permissions($role_permissions, $custom_permissions, $hide_people_is_installed, $planning_is_installed, $reports_plus_is_installed, $communications_is_installed);

                      $this->updateByRole($role_id, 'Client', $custom_permissions);
                    } // if
                  } // if

                  // Archive users that don't have system access
                } else {
                  $inactive_role_ids[] = $role_id;
                  defined('STATE_ARCHIVED') or define('STATE_ARCHIVED', 2);

                  DB::execute("UPDATE $users_table SET type = 'Member', original_state = state, state = ? WHERE role_id = ? AND company_id = ?", STATE_ARCHIVED, $role_id, $owner_company_id);
                  DB::execute("UPDATE $users_table SET type = 'Client', original_state = state, state = ? WHERE role_id = ? AND company_id != ?", STATE_ARCHIVED, $role_id, $owner_company_id);
                } // if
              } // foreach

              // Archive user with inactive role
              if(count($inactive_role_ids)) {
                $inactive_users = DB::execute("SELECT id, company_id, state FROM $users_table WHERE role_id IN (?)", $inactive_role_ids);

                if($inactive_users) {
                  $inactive_users->setCasting(array(
                    'id' => DBResult::CAST_INT,
                    'company_id' => DBResult::CAST_INT,
                    'state' => DBResult::CAST_INT,
                  ));

                  foreach($inactive_users as $inactive_user) {
                    $this->updateByUserId($inactive_user['id'], ($inactive_user['company_id'] == $owner_company_id ? 'Member' : 'Client'), $admin_user_id, $admin_display_name, $admin_email_address, null, STATE_ARCHIVED, $inactive_user['state']);
                  } // foreach
                } // if
              } // if
            } // if

            DB::commit('Roles and permissions updated @ ' . __CLASS__);
          } catch(Exception $e) {
            DB::rollback('Failed to update roles and permissions @ ' . __CLASS__);
            return $e->getMessage();
          } // try

          DB::execute("ALTER TABLE $users_table DROP role_id");
          DB::execute("ALTER TABLE $users_table DROP homescreen_id");
          DB::execute("DROP TABLE $roles_table");
        } else {
          throw new Exception('Failed to load owner company');
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // upgradeRolesAndPermissions

    /**
     * Update user account based on their existing role
     *
     * @param integer $role_id
     * @param string $type
     * @param array|null $custom_permissions
     */
    private function updateByRole($role_id, $type, $custom_permissions = null) {
      $additional_properties = array(
        'custom_permissions' => is_foreachable($custom_permissions) ? $custom_permissions : array(),
      );

      DB::execute("UPDATE " . TABLE_PREFIX . "users SET type = ?, raw_additional_properties = ? WHERE role_id = ?", $type, serialize($additional_properties), $role_id);
    } // updateByRole

    /**
     * Update individual user record
     *
     * @param integer $user_id
     * @param string $type
     * @param $admin_user_id
     * @param string $admin_display_name
     * @param string $admin_email_address
     * @param array $set_custom_permissions
     * @param integer $set_state
     * @param integer $from_state
     */
    private function updateByUserId($user_id, $type, $admin_user_id, $admin_display_name, $admin_email_address, $set_custom_permissions = null, $set_state = null, $from_state = null) {
      $users_table = TABLE_PREFIX . 'users';

      // Update type and custom permissions
      if($set_custom_permissions) {
        $additional_properties = array(
          'custom_permissions' => $set_custom_permissions,
        );
      } else {
        $additional_properties = array();
      } // if

      DB::execute("UPDATE $users_table SET type = ?, raw_additional_properties = ? WHERE id = ?", $type, serialize($additional_properties), $user_id);

      // Update user state if we new value is larger than the existing one, and track the change in modification log
      if($set_state !== null && $from_state !== null && $set_state > $from_state) {
        $modification_logs_table = TABLE_PREFIX . 'modification_logs';
        $modification_log_values_table = TABLE_PREFIX . 'modification_log_values';

        DB::execute("UPDATE $users_table SET state = ? WHERE id = ?", $set_state, $user_id);

        DB::execute("UPDATE $modification_logs_table SET parent_type = ? WHERE parent_type = 'User' AND parent_id = ?", $type, $user_id);
        DB::execute("INSERT INTO $modification_logs_table (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?,  UTC_TIMESTAMP(), ?, ?, ?)", $type, $user_id, $admin_user_id, $admin_display_name, $admin_email_address);
        DB::execute("INSERT INTO $modification_log_values_table (modification_id, field, value) VALUES (?, ?, ?)", DB::lastInsertId(), 'state', $set_state);
      } // if
    } // updateByUserId

    /**
     * Clean up home screen configuration
     *
     * @return bool|string
     */
    function cleanUpHomescreenConfig() {
      try {
        $this->addConfigOption('default_homescreen_tab_id');

        $homescreens_table = TABLE_PREFIX . 'homescreens';
        $tabs_table = TABLE_PREFIX . 'homescreen_tabs';
        $widgets_table = TABLE_PREFIX . 'homescreen_widgets';
        $config_values_tables = TABLE_PREFIX . 'config_option_values';

        // ---------------------------------------------------
        //  Clean up Non-User Home screens
        // ---------------------------------------------------

        try {
          DB::beginWork('Cleaning up home screen configuration @ ' . __CLASS__);

          $homescreen_ids = DB::executeFirstColumn("SELECT id FROM $homescreens_table WHERE parent_type IS NULL OR parent_type = 'Role'");

          if($homescreen_ids) {
            $homescreen_tabs = DB::executeFirstColumn("SELECT id FROM $tabs_table WHERE homescreen_id IN (?)", $homescreen_ids);

            if($homescreen_tabs) {
              DB::execute("DELETE FROM $widgets_table WHERE homescreen_tab_id IN (?)", $homescreen_tabs);
              DB::execute("DELETE FROM $tabs_table WHERE id IN (?)", $homescreen_tabs);
            } // if

            DB::execute("DELETE FROM $homescreens_table WHERE id IN (?)", $homescreen_ids);
          } // if

          DB::commit('Home screen configuration cleaned up @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to clean up home screen configuration @ ' . __CLASS__);
          return $e->getMessage();
        } // try

        // ---------------------------------------------------
        //  Update home screens
        // ---------------------------------------------------
        
        DB::execute("ALTER TABLE $tabs_table ADD user_id INT UNSIGNED NOT NULL DEFAULT '0' AFTER homescreen_id");

        try {
          DB::beginWork('Update homescreen tabs @ ' . __CLASS__);

          $homescreens = DB::execute("SELECT id, parent_id FROM $homescreens_table");
          if($homescreens) {
            $homescreens->setCasting(array(
              'id' => DBResult::CAST_INT,
              'parent_id' => DBResult::CAST_INT,
            ));

            foreach($homescreens as $homescreen) {
              DB::execute("UPDATE $tabs_table SET user_id = ? WHERE homescreen_id = ?", $homescreen['parent_id'], $homescreen['id']);
            } // foreach
          } // if

          // Delete orphaned tabs
          DB::execute("DELETE FROM $tabs_table WHERE user_id = '0'");

          // Make sure that first tab remains the first tab for users, after the upgrade
          $user_ids = DB::executeFirstColumn("SELECT DISTINCT user_id FROM $tabs_table");
          if($user_ids) {
            foreach($user_ids as $user_id) {
              $first_tab_id = (integer) DB::executeFirstCell("SELECT id FROM $tabs_table WHERE user_id = ? ORDER BY position LIMIT 0, 1", $user_id);

              if($first_tab_id) {
                DB::execute("INSERT INTO $config_values_tables (name, parent_type, parent_id, value) VALUES ('default_homescreen_tab_id', 'User', ?, ?)", $user_id, serialize($first_tab_id));
              } // if
            } // foreach
          } // if

          // Delete orphaned widgets
          $tab_ids = DB::executeFirstColumn("SELECT id FROM $tabs_table");
          if($tab_ids) {
            DB::execute("DELETE FROM $widgets_table WHERE homescreen_tab_id NOT IN (?)", $tab_ids);
          } // if

          DB::commit('Homescreen tabs updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update homescreen tabs @ ' . __CLASS__);
          throw $e;
        } // try

        // ---------------------------------------------------
        //  Finalize Table Transformations
        // ---------------------------------------------------

        DB::execute("ALTER TABLE $tabs_table DROP homescreen_id");
        DB::execute("ALTER TABLE $tabs_table ADD INDEX (user_id)");
        DB::execute("ALTER TABLE $widgets_table ADD INDEX (position)");
        DB::execute("ALTER TABLE $widgets_table ADD INDEX (homescreen_tab_id)");
        DB::execute("ALTER TABLE $widgets_table ADD INDEX (position)");
        DB::execute("DROP TABLE $homescreens_table");

      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // cleanUpHomescreenConfig

	  /**
	   * Create Security Logs table
	   *
	   * @return bool|string
	   */
	  function createSecurityLogsTable() {
		  try {
			  $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

			  DB::execute("CREATE TABLE " . TABLE_PREFIX . "security_logs (
				  id bigint unsigned NOT NULL auto_increment,
				  user_id int(10) unsigned NULL DEFAULT NULL,
				  user_name varchar(100)  DEFAULT NULL,
				  user_email varchar(150)  DEFAULT NULL,
				  login_as_id int(10) unsigned NULL DEFAULT NULL,
				  login_as_name varchar(100)  DEFAULT NULL,
				  login_as_email varchar(150)  DEFAULT NULL,
				  logout_by_id int(10) unsigned NULL DEFAULT NULL,
				  logout_by_name varchar(100)  DEFAULT NULL,
				  logout_by_email varchar(150)  DEFAULT NULL,
				  event enum('login', 'logout', 'expired', 'failed')  DEFAULT NULL,
				  event_on datetime  DEFAULT NULL,
				  user_ip varchar(45)  DEFAULT NULL,
				  user_agent text,
				  is_api tinyint(1) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (id),
				  INDEX user_id (user_id),
				  INDEX login_as_id (login_as_id),
				  INDEX logout_by_id (logout_by_id),
				  INDEX event_on (event_on)
				) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

			  DB::execute("CREATE TABLE " . TABLE_PREFIX . "api_token_logs (
				  id int unsigned NOT NULL auto_increment,
				  counts_on datetime  DEFAULT NULL,
				  total int(10) unsigned NOT NULL DEFAULT 0,
				  PRIMARY KEY (id),
				  INDEX counts_on (counts_on)
				) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
		  } catch(Exception $e) {
			  return $e->getMessage();
		  } // try

		  return true;
	  } // createSecurityLogsTable

	  /**
	   * Initialize Firewall
	   *
	   * @return bool|string
	   */
	  function initFirewall() {
		  try {
			  DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('firewall_enabled', 'environment', ?)", serialize(false));
			  DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('firewall_settings', 'environment', ?)", serialize(null));
			  DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('firewall_white_list', 'environment', ?)", serialize(null));
			  DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('firewall_black_list', 'environment', ?)", serialize(null));
			  DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('firewall_temp_list', 'environment', ?)", serialize(null));
		  } catch(Exception $e) {
			  return $e->getMessage();
		  } // try

		  return true;
	  } // initFirewall

    /**
     * Initialize project templates
     *
     * @return bool|string
     */
    function initProjectTemplates() {
      $icons_path = realpath(__DIR__ . '/..') . '/modules/system/assets/default/images/default-project-template-covers';

      copy("$icons_path/default.145x145.png", PUBLIC_PATH . '/template_covers/default.145x145.png');
      copy("$icons_path/default.145x145.png", PUBLIC_PATH . '/template_covers/default.300x300.png');

      try {
        $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "project_templates (
          id int unsigned NOT NULL auto_increment,
          name varchar(150)  DEFAULT NULL,
          category_id int(10) unsigned NULL DEFAULT NULL,
          company_id int(5) unsigned NOT NULL DEFAULT 0,
          created_on datetime  DEFAULT NULL,
          created_by_id int unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          updated_on datetime  DEFAULT NULL,
          updated_by_id int unsigned NULL DEFAULT NULL,
          updated_by_name varchar(100)  DEFAULT NULL,
          updated_by_email varchar(150)  DEFAULT NULL,
          custom_field_1 varchar(255)  DEFAULT NULL,
          custom_field_2 varchar(255)  DEFAULT NULL,
          custom_field_3 varchar(255)  DEFAULT NULL,
          position int(10) unsigned NULL DEFAULT NULL,
          PRIMARY KEY (id),
          INDEX created_on (created_on),
          INDEX category_id (category_id),
          INDEX company_id (company_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "project_object_templates (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'ProjectObjectTemplate',
          subtype varchar(50)  DEFAULT NULL,
          template_id int(10) unsigned NOT NULL DEFAULT 0,
          parent_id int(10) unsigned NULL DEFAULT NULL,
          value longtext ,
          position int(10) unsigned NULL DEFAULT NULL,
          file_size int(11) unsigned NULL DEFAULT NULL,
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX template_id (template_id),
          INDEX parent_id (parent_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // initProjectTemplates

    /**
     * Setup Project Synchronization
     *
     * @return bool|string
     */
    function insertProjectSyncConfigOptions() {
      try {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('project_sync_locked', 'system', ?)", serialize(false));
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('project_last_synced_on', 'system', ?)", serialize(null));
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('project_last_sync_locked_until', 'system', ?)", serialize(null));
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // insertProjectSyncConfigOptions

    /**
     * Clean up Mass Mailer notifications
     *
     * @return bool|string
     */
    function cleanUpMassMailerNotifications() {
      try {
        $notifications_table = TABLE_PREFIX . 'notifications';
        $notification_recipients_table = TABLE_PREFIX . 'notification_recipients';

        $notification_ids = DB::executeFirstColumn("SELECT id FROM $notifications_table WHERE type = 'MassNotification'");

        if($notification_ids) {
          try {
            DB::beginWork('Cleaning up Mass Mailer notifications @ ' . __CLASS__);

            $notification_recipient_ids = DB::executeFirstColumn("SELECT id FROM $notification_recipients_table WHERE notification_id IN (?)", $notification_ids);

            if($notification_recipient_ids) {
              DB::execute("DELETE FROM $notification_recipients_table WHERE id IN (?)", $notification_recipient_ids);
            } // if

            DB::execute("DELETE FROM $notifications_table WHERE id IN (?)", $notification_ids);

            DB::commit('Mass Mailer notifications cleaned up @ ' . __CLASS__);
          } catch(Exception $e) {
            DB::rollback('Failed to clean up Mass Mailer notifications @ ' . __CLASS__);
            return $e->getMessage();
          } // try
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // cleanUpMassMailerNotifications

    /**
     * Update to do items
     *
     * @return boolean
     */
    function updateTodoItems() {
      if(LICENSE_PACKAGE == 'smallbiz' && $this->isModuleInstalled('todo') && !$this->isModuleInstalled('tasks')) {
        try {
          DB::beginWork('Converting to do lists to tasks @ ' . __CLASS__);

          // Replace To Do module with Tasks module
          DB::execute('UPDATE ' . TABLE_PREFIX . "modules SET name = 'tasks' WHERE name = 'todo'");

          // ---------------------------------------------------
          //  Update project tabs
          // ---------------------------------------------------

          $project_tabs = $this->getConfigOptionValue('project_tabs');

          if(is_array($project_tabs)) {
            $k = array_search('todo_lists', $project_tabs);

            if($k !== false) {
              $project_tabs[$k] = 'tasks';
              $this->updateConfigOption('project_tabs', $project_tabs);
            } // if
          } // if

          $custom_project_tab_rows = DB::execute('SELECT * FROM ' . TABLE_PREFIX . "config_option_values WHERE name = 'project_tabs'");
          if($custom_project_tab_rows) {
            foreach($custom_project_tab_rows as $custom_project_tab_row) {
              $project_tabs = $custom_project_tab_row['value'] ? unserialize($custom_project_tab_row['value']) : null;

              if(is_array($project_tabs)) {
                $k = array_search('todo_lists', $project_tabs);

                if($k !== false) {
                  $project_tabs[$k] = 'tasks';

                  DB::execute('UPDATE ' . TABLE_PREFIX . "config_option_values SET value = ? WHERE name = 'project_tabs' AND parent_type = ? AND parent_id = ?", serialize($project_tabs), $custom_project_tab_row['parent_type'], $custom_project_tab_row['parent_id']);
                } // if
              } // if
            } // foreach
          } // if

          // ---------------------------------------------------
          //  Related tasks table
          // ---------------------------------------------------

          $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

          DB::execute("CREATE TABLE " . TABLE_PREFIX . "related_tasks (
            parent_task_id int(10) unsigned NOT NULL DEFAULT 0,
            related_task_id int(10) unsigned NOT NULL DEFAULT 0,
            note varchar(255)  DEFAULT NULL,
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            INDEX created_on (created_on),
            PRIMARY KEY (parent_task_id, related_task_id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

          // ---------------------------------------------------
          //  Task segments table
          // ---------------------------------------------------

          DB::execute("CREATE TABLE `" . TABLE_PREFIX . "task_segments` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(50) DEFAULT NULL,
            `raw_additional_properties` longtext,
            PRIMARY KEY (`id`)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

          // ---------------------------------------------------
          //  Public task forms table
          // ---------------------------------------------------
          
          DB::execute("CREATE TABLE `" . TABLE_PREFIX . "_public_task_forms` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `project_id` int(11) unsigned NOT NULL DEFAULT '0',
            `slug` varchar(50) NOT NULL,
            `name` varchar(100) DEFAULT NULL,
            `body` text,
            `is_enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
            `raw_additional_properties` longtext,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

          // ---------------------------------------------------
          //  Config options and custom fields for tasks module
          // ---------------------------------------------------

          DB::execute('UPDATE ' . TABLE_PREFIX . "config_options SET name = 'task_categories', module = 'tasks' WHERE name = 'todo_list_categories'");

          $this->addConfigOption('tasks_auto_reopen', true, 'tasks');
          $this->addConfigOption('tasks_auto_reopen_clients_only', true, 'tasks');
          $this->addConfigOption('tasks_public_submit_enabled', false, 'tasks');
          $this->addConfigOption('tasks_use_captcha', false, 'tasks');

          DB::execute('INSERT INTO ' . TABLE_PREFIX . "custom_fields (field_name, parent_type) VALUES ('custom_field_1', 'Task'), ('custom_field_2', 'Task'), ('custom_field_3', 'Task')");

          // ---------------------------------------------------
          //  Project roles and project users
          // ---------------------------------------------------

          $project_roles = DB::execute('SELECT id, permissions FROM ' . TABLE_PREFIX . 'project_roles');
          if($project_roles) {
            foreach($project_roles as $project_role) {
              $permissions = $project_role['permissions'] ? unserialize($project_role['permissions']) : array();

              if(is_array($permissions) && isset($permissions['todo_list'])) {
                $permissions['task'] = $permissions['todo_list'];
                unset($permissions['todo_list']);

                DB::execute('UPDATE ' . TABLE_PREFIX . 'project_roles SET permissions = ? WHERE id = ?', serialize($permissions), $project_role['id']);
              } // if
            } // if
          } // if

          $project_users = DB::execute('SELECT user_id, project_id, permissions FROM ' . TABLE_PREFIX . 'project_users WHERE role_id IS NULL OR role_id = ?', 0);
          if($project_users) {
            foreach($project_users as $project_user) {
              $permissions = $project_user['permissions'] ? unserialize($project_user['permissions']) : array();

              if(is_array($permissions) && isset($permissions['todo_list'])) {
                $permissions['task'] = $permissions['todo_list'];
                unset($permissions['todo_list']);

                DB::execute('UPDATE ' . TABLE_PREFIX . 'project_users SET permissions = ? WHERE user_id = ? AND project_id = ?', serialize($permissions), $project_user['user_id'], $project_user['project_id']);
              } // if
            } // if
          } // if

          // ---------------------------------------------------
          //  To do lists to Tasks
          // ---------------------------------------------------

          DB::execute('UPDATE ' . TABLE_PREFIX . "categories SET type = 'TaskCategory' WHERE type = 'TodoListCategory'");

          $project_objects_table = TABLE_PREFIX . 'project_objects';

          $todo_lists = DB::execute("SELECT id, project_id FROM $project_objects_table WHERE type = 'TodoList'");
          if($todo_lists) {
            $todo_lists->setCasting(array(
              'id' => DBResult::CAST_INT,
              'project_id' => DBResult::CAST_INT,
            ));

            $todo_list_ids = array();
            $counters = array();

            foreach($todo_lists as $todo_list) {
              $id = $todo_list['id'];
              $project_id = $todo_list['project_id'];

              if(isset($counters[$project_id])) {
                $counters[$project_id]++;
              } else {
                $counters[$project_id] = 1;
              } // if

              DB::execute("UPDATE $project_objects_table SET type = 'Task', module = 'tasks', integer_field_1 = ? WHERE id = ?", $counters[$project_id], $id);

              $todo_list_ids[] = $id;
            } // foreach

            DB::execute('UPDATE ' . TABLE_PREFIX . 'attachments SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)', 'Task', 'TodoList', $todo_list_ids);
            DB::execute('UPDATE ' . TABLE_PREFIX . 'code_snippets SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)', 'Task', 'TodoList', $todo_list_ids);
            DB::execute('UPDATE ' . TABLE_PREFIX . 'favorites SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)', 'Task', 'TodoList', $todo_list_ids);
            DB::execute('UPDATE ' . TABLE_PREFIX . 'modification_logs SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)', 'Task', 'TodoList', $todo_list_ids);
            DB::execute('UPDATE ' . TABLE_PREFIX . 'reminders SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)', 'Task', 'TodoList', $todo_list_ids);
            DB::execute('UPDATE ' . TABLE_PREFIX . 'subscriptions SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)', 'Task', 'TodoList', $todo_list_ids);
            DB::execute('UPDATE ' . TABLE_PREFIX . 'subtasks SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)', 'Task', 'TodoList', $todo_list_ids);
          } // if

          DB::commit('To do lists converted to tasks @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to convert to do lists to tasks @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // updateTodoItems

    /**
     * Update core modules
     *
     * @return bool|string
     */
    function updateCoreModules() {
      if(LICENSE_PACKAGE == 'smallbiz') {
        try {
          DB::beginWork('Updating modules @ ' . __CLASS__);

          $modules_table = TABLE_PREFIX . 'modules';
          $users_table = TABLE_PREFIX . 'users';
          $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

          // Set up calendar module
          if(!$this->isModuleInstalled('calendar')) {
            $next_position = $position = DB::executeFirstCell("SELECT MAX(position) FROM $modules_table") + 1;

            DB::execute("INSERT INTO $modules_table (name, is_enabled, position) VALUES ('calendar', '1', $next_position)");

            $project_tabs = $this->getConfigOptionValue('project_tabs');

            if(is_array($project_tabs)) {
              $project_tabs[] = 'calendar';
              $this->updateConfigOption('project_tabs', $project_tabs);
            } // if
          } // if

          // Set up Documents module
          if(!$this->isModuleInstalled('documents')) {
            $next_position = $position = DB::executeFirstCell("SELECT MAX(position) FROM $modules_table") + 1;

            DB::execute("INSERT INTO $modules_table (name, is_enabled, position) VALUES ('documents', '1', $next_position)");

            DB::execute("CREATE TABLE " . TABLE_PREFIX . "documents (
              id int unsigned NOT NULL auto_increment,
              category_id int(11) unsigned NULL DEFAULT NULL,
              type enum('text', 'file') NOT NULL DEFAULT 'text',
              name varchar(150)  DEFAULT NULL,
              body text ,
              size int(11) NULL DEFAULT NULL,
              mime_type varchar(255)  DEFAULT NULL,
              location varchar(50)  DEFAULT NULL,
              md5 varchar(32)  DEFAULT NULL,
              state tinyint(3) unsigned NOT NULL DEFAULT 0,
              original_state tinyint(3) unsigned NULL DEFAULT NULL,
              visibility tinyint(3) unsigned NOT NULL DEFAULT 0,
              original_visibility tinyint(3) unsigned NULL DEFAULT NULL,
              is_pinned tinyint(1) unsigned NOT NULL DEFAULT '0',
              created_on datetime  DEFAULT NULL,
              created_by_id int unsigned NULL DEFAULT NULL,
              created_by_name varchar(100)  DEFAULT NULL,
              created_by_email varchar(150)  DEFAULT NULL,
              PRIMARY KEY (id)
            ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

            DB::execute('INSERT INTO ' . TABLE_PREFIX . 'categories (type, name) VALUES (?, ?)', 'DocumentsCategory', 'General');
          } // if

          // Set up status module
          if(!$this->isModuleInstalled('status')) {
            $next_position = $position = DB::executeFirstCell("SELECT MAX(position) FROM $modules_table") + 1;

            DB::execute("INSERT INTO $modules_table (name, is_enabled, position) VALUES ('status', '1', $next_position)");

            DB::execute("CREATE TABLE " . TABLE_PREFIX . "status_updates (
              id int unsigned NOT NULL auto_increment,
              parent_id int(10) unsigned NULL DEFAULT NULL,
              message varchar(255) NOT NULL DEFAULT '',
              created_on datetime  DEFAULT NULL,
              created_by_id int unsigned NULL DEFAULT NULL,
              created_by_name varchar(100)  DEFAULT NULL,
              created_by_email varchar(150)  DEFAULT NULL,
              last_update_on datetime  DEFAULT NULL,
              PRIMARY KEY (id),
              INDEX created_on (created_on),
              INDEX last_update_on (last_update_on),
              INDEX parent_id (parent_id)
            ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

            $this->addConfigOption('status_update_last_visited');
          } // if

          // ---------------------------------------------------
          //  Update permissions
          // ---------------------------------------------------

          $users = DB::execute("SELECT id, type, raw_additional_properties FROM $users_table WHERE type IN ('Member', 'Manager')");
          if($users) {
            foreach($users as $user) {
              $properties = $user['raw_additional_properties'] ? unserialize($user['raw_additional_properties']) : array();

              if(isset($properties['custom_permissions']) && is_array($properties['custom_permissions'])) {
                $properties['custom_permissions'][] = 'can_use_status_updates';

                if($user['type'] == 'Member') {
                  $properties['custom_permissions'][] = 'can_use_documents';
                } else {
                  $properties['custom_permissions'][] = 'can_manage_documents';
                } // if

                DB::execute("UPDATE $users_table SET raw_additional_properties = ? WHERE id = ?", serialize($properties), $user['id']);
              } // if
            } // foreach
          } // if

          DB::commit('Modules updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update modules @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // updateCoreModules

  }
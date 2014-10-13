<?php

  /**
   * Update activeCollab 3.0.0 to activeCollab 3.0.1
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0024 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.0.0';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.0.1';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateHomescreens' => 'Update home screens',
        'updateModificationLog' => 'Update modification log', 
        'updateNotiifcationStyle' => 'Update notification style settings', 
      );
    } // getActions
    
    /**
     * Rename desktops to home screens
     * 
     * @return boolean
     */
    function updateHomescreens() {
      try {
        $homescreens_table = TABLE_PREFIX . 'homescreens';
        $homescreen_tabs_table = TABLE_PREFIX . 'homescreen_tabs';
        $homescreen_widgets_table = TABLE_PREFIX . 'homescreen_widgets';
        
        $roles_table = TABLE_PREFIX . 'roles';
        
        // Update construction
        DB::execute("RENAME TABLE " . TABLE_PREFIX . "desktop_sets TO $homescreens_table");
        DB::execute("ALTER TABLE $homescreens_table CHANGE type type VARCHAR(50) NOT NULL DEFAULT 'Homescreen'");
        DB::execute("UPDATE $homescreens_table SET type = 'Homescreen' WHERE type = 'DesktopSet'");
        
        DB::execute("RENAME TABLE " . TABLE_PREFIX . "desktops TO $homescreen_tabs_table");
        DB::execute("ALTER TABLE $homescreen_tabs_table CHANGE type type VARCHAR(50) NOT NULL DEFAULT 'HomescreenTab'");
        DB::execute("ALTER TABLE $homescreen_tabs_table CHANGE desktop_set_id homescreen_id INT(6) UNSIGNED NOT NULL DEFAULT '0'");
        
        DB::execute("RENAME TABLE " . TABLE_PREFIX . "desktop_widgets TO $homescreen_widgets_table");
        DB::execute("ALTER TABLE $homescreen_widgets_table CHANGE type type VARCHAR(50) NOT NULL DEFAULT 'HomescreenWidget'");
        DB::execute("ALTER TABLE $homescreen_widgets_table CHANGE desktop_id homescreen_tab_id INT(5) UNSIGNED NOT NULL DEFAULT '0'");
        
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "users CHANGE desktop_set_id homescreen_id INT(5) UNSIGNED NULL DEFAULT '0'");
        
        if(DB::loadTable($roles_table)->getColumn('desktop_set_id')) {
          DB::execute("ALTER TABLE $roles_table CHANGE desktop_set_id homescreen_id INT(5) UNSIGNED NULL DEFAULT NULL");
        } else {
          DB::execute("ALTER TABLE $roles_table ADD homescreen_id INT(5) UNSIGNED NULL DEFAULT NULL AFTER is_default");
        } // if
        
        // Update data
        try {
          DB::beginWork('Renaming desktops to homescreens @ ' . __CLASS__);
          
          $rename_homescreen_tabs = array(
            'CenterDesktop' => 'CenterHomescreenTab', 
            'SplitDesktop' => 'SplitHomescreenTab', 
            'RightDesktop' => 'RightHomescreenTab', 
            'LeftDesktop' => 'LeftHomescreenTab', 
          );
          
          foreach($rename_homescreen_tabs as $k => $v) {
            DB::execute("UPDATE $homescreen_tabs_table SET type = ? WHERE type = ?", $v, $k);
          } // foreach
          
          $rename_homescreen_widgets = array(
          
            // System
            'ProjectsDesktopWidget' => 'ProjectsHomescreenWidget', 
            'MyProjectsDesktopWidget' => 'MyProjectsHomescreenWidget', 
            'FavoriteProjectsDesktopWidget' => 'FavoriteProjectsHomescreenWidget', 
            'WelcomeDesktopWidget' => 'WelcomeHomescreenWidget',
          	'DayOverviewDesktopWidget' => 'DayOverviewHomescreenWidget', 
            'AssignmentsFilterDesktopWidget' => 'AssignmentsFilterHomescreenWidget', 
          
            // Tasks
            'TasksFilterDesktopWidget' => 'TasksFilterHomescreenWidget', 
            'MyTasksDesktopWidget' => 'MyTasksHomescreenWidget', 
            'DelegatedTasksDesktopWidget' => 'DelegatedTasksHomescreenWidget', 
            'UnassignedTasksDesktopWidget' => 'UnassignedTasksHomescreenWidget',
           
            // Discussions
            'MyDiscussionsDesktopWidget' => 'MyDiscussionsHomescreenWidget',

            // Frameworks
            'RecentActivitiesDesktopWidget' => 'RecentActivitiesHomescreenWidget', 
            'SystemNotificationsDesktopWidget' => 'SystemNotificationsHomescreenWidget', 
            'WhosOnlineDesktopWidget' => 'WhosOnlineHomescreenWidget', 
          );
          
          foreach($rename_homescreen_widgets as $k => $v) {
            DB::execute("UPDATE $homescreen_widgets_table SET type = ? WHERE type = ?", $v, $k);
          } // foreach
          
          DB::commit('Desktops renamed to homescreens @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to rename desktops to homescreens');
          throw $e;
        } // try
        
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateHomescreens
    
    /**
     * Clean up problems that are result of invalid due_on modification handling
     * 
     * @return boolean
     */
    function updateModificationLog() {
      try {
        $modification_logs_table = TABLE_PREFIX . 'modification_logs';
        $modification_log_values_table = TABLE_PREFIX . 'modification_log_values';
        
        DB::beginWork('Cleaning up modification logs @ ' . __CLASS__);
        
        DB::execute("DELETE FROM $modification_log_values_table WHERE field = ? AND (value = ? OR value IS NULL)", 'due_on', '1970-01-01 00:00:00');
        DB::execute("DELETE FROM $modification_logs_table WHERE NOT EXISTS (SELECT * FROM $modification_log_values_table WHERE $modification_log_values_table.modification_id = $modification_logs_table.id)");
        
        DB::commit('Modification log cleaned @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to clean modification log @ ' . __CLASS__);
        return $e->getMessage();
      } // try
      
      return true;
    } // updateModificationLog
    
    /**
     * Update notification style, as defind in email framework model
     * 
     * @return boolean
     */
    function updateNotiifcationStyle() {
      $options = array(
        'notification_header_style' => array(
          'text' => array(
            'color' => '#8a8a8a',
            'font-size' => '17px',
            'font-family' => 'Lucida Grande, Verdana, Arial, Helvetica, sans-serif',
          ),
    			'link' => array(
            'color' => '#950000',
            'font-size' => '17px',
            'font-family' => 'Lucida Grande, Verdana, Arial, Helvetica, sans-serif',
        )), 
        'notification_content_style' => array(
          'text' => array(
            'color' => '#000000',
            'font-size' => '13px',
            'font-family' => 'Lucida Grande, Verdana, Arial, Helvetica, sans-serif',
          ),
    			'link' => array(
            'color' => '#950000',
            'font-size' => '13px',
            'font-family' => 'Lucida Grande, Verdana, Arial, Helvetica, sans-serif',
          )
        ), 
        'notification_footer_style' => array(
          'text' => array(
            'color' => '#000000',
            'font-size' => '11px;',
            'font-family' => 'Lucida Grande, Verdana, Arial, Helvetica, sans-serif',
          ),
    			'link' => array(
            'color' => '#950000',
            'font-size' => '11px',
            'font-family' => 'Lucida Grande, Verdana, Arial, Helvetica, sans-serif',
          )
        )
      );
      
      try {
        DB::beginWork('Updating configuration options @ ' . __CLASS__);
        
        foreach($options as $k => $v) {
          DB::execute('INSERT INTO ' . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?)', $k, 'system', serialize($v));
        } // foreach
        
        DB::commit('Configuration options updated @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to update configuration options @ ' . __CLASS__);
        return $e->getMessage();
      } // try
      
      return true;
    } // updateNotiifcationStyle
  
  }
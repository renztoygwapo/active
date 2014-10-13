<?php

  /**
   * Update activeCollab 4.0.10 to activeCollab 4.0.11
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0087 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.10';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.11';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'newConfigOptions' => 'Set new configuration options',
        'dropOldBackupTables' => 'Drop old backup tables',
        'updateProjectObjectsSearchIndex' => 'Update project objects search index',
      );
    } // getActions

    /**
     * Set new config options
     *
     * @return bool|string
     */
    function newConfigOptions() {
      try {
        DB::beginWork('Setting config options @ ' . __CLASS__);

        $this->addConfigOption('identity_client_welcome_message', "Welcome to our project collaboration environment! You will find all your projects when you click on 'Projects' icon in the main navigation. To get back to this page, you can always click on 'Home Screen' menu item.");
        $this->addConfigOption('identity_logo_on_white', true);

        DB::commit('Config options set @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to set config options @ ' . __CLASS__);
        return $e->getMessage();
      } // try

      return true;
    } // newConfigOptions

    /**
     * Drop old backup tables
     *
     * @return bool|string
     */
    function dropOldBackupTables() {
      try {
        if(DB::tableExists(TABLE_PREFIX . 'content_backup')) {
          DB::dropTable(TABLE_PREFIX . 'content_backup');
        } // if

        if(DB::tableExists(TABLE_PREFIX . 'private_comments_backup')) {
          DB::dropTable(TABLE_PREFIX . 'private_comments_backup');
        } // if

        if(DB::tableExists(TABLE_PREFIX . 'tags_backup')) {
          DB::dropTable(TABLE_PREFIX . 'tags_backup');
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // dropOldBackupTables

    /**
     * Update project objects search index
     *
     * @return bool|string
     */
    function updateProjectObjectsSearchIndex() {
      try {
        DB::execute("DROP TABLE IF EXISTS " . TABLE_PREFIX . 'search_index_for_project_objects');
        DB::execute("CREATE TABLE " . TABLE_PREFIX . "search_index_for_project_objects (
          item_type varchar(50) NOT NULL default '',
          item_id int(10) unsigned NOT NULL default '0',
          item_context varchar(255) default NULL,
          project_id int(11) default NULL,
          project varchar(255) default NULL,
          milestone_id int(11) default NULL,
          milestone varchar(255) default NULL,
          category_id int(11) default NULL,
          category varchar(255) default NULL,
          visibility int(11) default NULL,
          name varchar(255) default NULL,
          body longtext,
          assignee_id int(11) default NULL,
          assignee varchar(255) default NULL,
          priority int(11) default NULL,
          due_on date default NULL,
          completed_on datetime default NULL,
          comments longtext,
          subtasks longtext,
          PRIMARY KEY  (item_type,item_id),
          KEY item_context (item_context),
          KEY project_id (project_id),
          KEY milestone_id (milestone_id),
          KEY category_id (category_id),
          KEY visibility (visibility),
          KEY assignee_id (assignee_id),
          KEY priority (priority),
          KEY due_on (due_on),
          KEY completed_on (completed_on),
          FULLTEXT KEY content (project,milestone,category,name,body,assignee,comments,subtasks)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateProjectObjectsSearchIndex

  }
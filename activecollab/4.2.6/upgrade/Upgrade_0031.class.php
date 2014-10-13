<?php

	/**
   * Update activeCollab 3.0.7 to activeCollab 3.0.9
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0031 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.0.7';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.0.9';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateSystemPermissions' => 'Update use feeds permission name', 
      	'fixNotebookPagesCollation' => 'Fix collation of notebook pages table',
      );
    } // getActions
    
    /**
     * Update use feeds permission name
     * 
     * @return string
     */
    function updateSystemPermissions() {
      $roles_table = TABLE_PREFIX . 'roles';
      
      try {
        DB::beginWork('Updating system permissions @ ' . __CLASS__);
        
        $roles = DB::execute("SELECT id, name, permissions FROM $roles_table");
        
        foreach($roles as $role) {
          $permissions = $role['permissions'] ? unserialize($role['permissions']) : array();
          
          if($role['name'] == 'Project Manager') {
            $permissions['can_see_project_budgets'] = true;
          } // if
          
          if(isset($permissions['can_use_feed'])) {
            if($permissions['can_use_feed']) {
              $permissions['can_use_feeds'] = true;
            } // if
            
            unset($permissions['can_use_feed']);
          } // if
          
          DB::execute("UPDATE $roles_table SET permissions = ? WHERE id = ?", serialize($permissions), $role['id']);
        } // foreach
        
        DB::commit('Updated system permissions @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to update system permissions @ ' . __CLASS__);
        return $e->getMessage();
      } // try
      
      return true;
    } // updateSystemPermissions
    
    /**
     * Fix collation of notebook pages table
     * 
     * @return boolean
     */
    function fixNotebookPagesCollation() {
      try {
        if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'notebooks'")) {
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_pages CHANGE parent_type parent_type VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_pages CHANGE name name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_pages CHANGE body body LONGTEXT  CHARACTER SET utf8 COLLATE utf8_general_ci NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_pages CHANGE created_by_name created_by_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_pages CHANGE created_by_email created_by_email VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_pages CHANGE updated_by_name updated_by_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_pages CHANGE updated_by_email updated_by_email VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_pages COLLATE = utf8_general_ci');
          
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_page_versions CHANGE name name VARCHAR(255)  CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_page_versions CHANGE body body LONGTEXT  CHARACTER SET utf8 COLLATE utf8_general_ci NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_page_versions CHANGE created_by_name created_by_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_page_versions CHANGE created_by_email created_by_email VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
          DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'notebook_page_versions COLLATE = utf8_general_ci');
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // fixNotebookPagesCollation

  }
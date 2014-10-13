<?php

	/**
   * Update activeCollab 3.0.3 to activeCollab 3.0.4
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0027 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.0.3';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.0.4';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array( 
      	'importLegacyTranslations' => 'Import translations from older aC versions',
    		'addIncomingMailStatus' => 'Alter Incoming Mail table',
    		'fixIpAddressColumn' => 'Update IP address columns',
        'fixParentKeyForObjectContexts' => 'Fix parent key for object contexts', 
        'visibilityCleanUpForComments' => 'Update visibility values for comments', 
        'visibilityCleanUpForSubtasks' => 'Update visibility values for subtasks', 
        'visibilityCleanUpForAttachments' => 'Update visibility values for attachments',
        'updateProjectObjectsTable' => 'Update ProjectObjects table',
      	'updateCommitProjectObjectsTable' => 'Update relation between commits and project objects',
        'archiveOldProjects' => 'Archive old projects',  
      );
    } // getActions
    
  
    /**
     * Add status to incoming mail table
     * 
     * @return boolean
     */
    function addIncomingMailStatus() {
      try {
        $incoming_mail_table = TABLE_PREFIX.'incoming_mails';
        if(DB::tableExists($incoming_mail_table)) {
          DB::execute("ALTER TABLE $incoming_mail_table DROP state");
          DB::execute("ALTER TABLE $incoming_mail_table ADD status VARCHAR(255) NULL DEFAULT NULL AFTER headers");
          DB::execute("ALTER TABLE $incoming_mail_table ADD parent_type VARCHAR(50) NULL DEFAULT NULL AFTER incoming_mailbox_id");
        }//if
      } catch (Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // addIncomingMailStatus
    
    /**
     * Import legacy translations
     * 
     * @return boolean
     */
    function importLegacyTranslations() {
			$languages_table = TABLE_PREFIX . 'languages';
  		$phrases_table = TABLE_PREFIX . 'language_phrases';
  		$translations_table = TABLE_PREFIX . 'language_phrase_translations';
  		
  		$localizations_dir = CUSTOM_PATH . '/localization';
  		$available_localizations = get_folders($localizations_dir);
  		if (!is_foreachable($available_localizations)) {
  			return true;
  		} // if
  		
  		$directories_pending_deletion = array();
  		$files_pending_deletion = array();
  		
  		try {
  			foreach ($available_localizations as $localization) {
  				$directories_pending_deletion[] = $localization;
  				
  				$info_file = $localization . '/info.php';
  				if (!is_file($info_file)) {
  					continue;
  				} // if
  				$files_pending_deletion[] = $info_file;
  				
  				$info = require($info_file);
  				$locale = array_var($info, 'code', null);
  				if (!$locale) {
  					continue;
  				} // if
  				
  				$language_id = DB::executeFirstCell("SELECT id FROM $languages_table WHERE locale = ? LIMIT 1", $locale);
  				if (!$language_id) {
						DB::execute("INSERT INTO $languages_table (name, locale) VALUES (?, ?)", array_var($info, 'name', $locale), $locale);
						$language_id = DB::executeFirstCell("SELECT last_insert_id() FROM $languages_table");
  				} // if
  				
  				$translations = array();  				
  				$files = get_files($localization, 'php', false);
  				foreach ($files as $file) {
  					$files_pending_deletion[] = $file;
  					$basename = basename($file);
						if (strpos($basename, 'module.') === 0) {
							$translations = array_merge($translations, (array) include($file));
						} // if
  				} // foreach
  				
		  		$query = array();
		  		foreach ($translations as $phrase => $translation) {
		  			if ($translation) {
			  			$query[] = DB::prepare('(?, md5(?), ?)', $language_id, $phrase, $translation);
		  			} // if
		  		} // foreach

          // save translations
		  		if (count($query)) {
            DB::execute('REPLACE INTO ' . TABLE_PREFIX .'language_phrase_translations (language_id, phrase_hash, translation) VALUES ' . implode(', ', $query));
		  			return false; // TODO (on error, return error message)
		  		} // if
  			} // foreach
	    	
	    	// cleanup translations that belongs to non existing languages
	    	DB::execute("DELETE $translations_table.* FROM $translations_table LEFT JOIN $languages_table ON $translations_table.language_id = $languages_table.id WHERE $languages_table.id IS NULL");

	    	// try to cleanup legacy files
	    	if (is_foreachable($files_pending_deletion)) {
	    		foreach ($files_pending_deletion as $file_pending_deletion) {
	    			@unlink($file_pending_deletion);
	    		} // foreach
	    	} // if
	    	
	    	// try to cleanup legacy directories
	    	if (is_foreachable($directories_pending_deletion)) {
	    		foreach ($directories_pending_deletion as $directory_pending_deletion) {
	    			@rmdir($directory_pending_deletion);
	    		}	// foreach
	    	} // if
  		} catch (Exception $e) {
  			return $e->getMessage();
  		} // try
    } // importLegacyTranslations
    
    /**
     * Fix IP address column
     * 
     * @return boolean
     */
    function fixIpAddressColumn() {
      try {
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "comments ADD ip_address VARCHAR(45) NULL DEFAULT NULL AFTER body");
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "user_sessions CHANGE user_ip user_ip VARCHAR(45) NULL DEFAULT NULL");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // fixIpAddressColumn
    
    /**
     * Fix parent key for object contexts table
     * 
     * @return boolean
     */
    function fixParentKeyForObjectContexts() {
      try {
        DB::execute("TRUNCATE TABLE " . TABLE_PREFIX . "object_contexts");
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "object_contexts DROP INDEX parent");
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "object_contexts ADD UNIQUE INDEX parent (parent_type, parent_id)");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // fixParentKeyForObjectContexts
    
    /**
     * Visibility cleanup for comments
     * 
     * @return boolean
     */
    function visibilityCleanUpForComments() {
      try {
        $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
        
        $comments_table = TABLE_PREFIX . 'comments';
        $private_comments_table = TABLE_PREFIX . 'private_comments_backup';
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        
        DB::execute("CREATE TABLE $private_comments_table (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'Comment',
          source varchar(50)  DEFAULT NULL,
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          body longtext ,
          state tinyint(3) unsigned NOT NULL DEFAULT 0,
          original_state tinyint(3) unsigned NULL DEFAULT NULL,
          created_on datetime  DEFAULT NULL,
          created_by_id int unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          updated_on datetime  DEFAULT NULL,
          updated_by_id int unsigned NULL DEFAULT NULL,
          updated_by_name varchar(100)  DEFAULT NULL,
          updated_by_email varchar(150)  DEFAULT NULL,
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX parent (parent_type, parent_id),
          INDEX created_on (created_on),
          INDEX created_by_id (created_by_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        
        // Remember private comments
        $private_comments = DB::execute("SELECT $comments_table.id, $comments_table.type, $comments_table.source, $comments_table.parent_type, $comments_table.parent_id, $comments_table.body, $comments_table.state, $comments_table.original_state, $comments_table.created_on, $comments_table.created_by_id, $comments_table.created_by_name, $comments_table.created_by_email, $comments_table.updated_on, $comments_table.updated_by_id, $comments_table.updated_by_name, $comments_table.updated_by_email FROM $comments_table, $project_objects_table WHERE $project_objects_table.type = $comments_table.parent_type AND $project_objects_table.id = $comments_table.parent_id AND $project_objects_table.visibility > $comments_table.visibility");
        
        // Drop fields before transaction
        DB::execute("ALTER TABLE $comments_table DROP visibility");
        DB::execute("ALTER TABLE $comments_table DROP original_visibility");
        
        // Now migrate comments and clean up comments table
        try {
          DB::beginWork('Moving private comments @ ' . __CLASS__);
          
          if($private_comments) {
            $private_comment_ids = array();
            
            $batch = DB::batchInsert($private_comments_table, array('id', 'type', 'source', 'parent_type', 'parent_id', 'body', 'state', 'original_state', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email', 'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email'));
            
            foreach($private_comments as $comment) {
              $private_comment_ids[] = (integer) $comment['id'];
              
              $batch->insertArray($comment);
            } // foreach
            
            $batch->done();
            
            DB::execute("DELETE FROM $comments_table WHERE id IN (?)", $private_comment_ids);
          } // if
          
          DB::commit('Private comments moved @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to move private comments @ ' . __CLASS__);
        } // try
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // visibilityCleanUpForComments
    
    /**
     * Visibility cleanup for subtasks
     * 
     * @return boolean
     */
    function visibilityCleanUpForSubtasks() {
      try {
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'subtasks DROP visibility');
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'subtasks DROP original_visibility');
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'subtasks DROP INDEX parent_type');
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // visibilityCleanUpForSubtasks
    
    /**
     * Visibility cleanup for attachments
     * 
     * @return boolean
     */
    function visibilityCleanUpForAttachments() {
      try {
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'attachments DROP visibility');
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'attachments DROP original_visibility');
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // visibilityCleanUpForAttachments

    /**
     * Moves data from parent_id to integer_field_1 for ProjectSourceRepository 
     * types
     *
     * @return boolean
     */
    function updateProjectObjectsTable() {
      $project_objects_table = TABLE_PREFIX . "project_objects";
      
      try {
        DB::execute("UPDATE $project_objects_table SET integer_field_1 = parent_id WHERE type = ?", 'ProjectSourceRepository');
        
        DB::execute("ALTER TABLE $project_objects_table DROP parent_type");
        DB::execute("ALTER TABLE $project_objects_table DROP parent_id");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateProjectObjectsTable

    /**
     * Update CommitProjectObjects table
     *
     * @return boolean
     */
    function updateCommitProjectObjectsTable() {
      $commit_project_objects_table = TABLE_PREFIX . "commit_project_objects";

      try {
        if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='source'")) {
          DB::execute("ALTER TABLE $commit_project_objects_table DROP PRIMARY KEY");
          DB::execute("ALTER TABLE $commit_project_objects_table ADD id INT(11) AUTO_INCREMENT PRIMARY KEY FIRST");
          DB::execute("ALTER TABLE $commit_project_objects_table CHANGE object_id parent_id int(11)");
          DB::execute("ALTER TABLE $commit_project_objects_table CHANGE object_type parent_type varchar(50)");
          DB::execute("CREATE INDEX parent ON $commit_project_objects_table (parent_id,parent_type)");
          DB::execute("UPDATE $commit_project_objects_table SET parent_type = 'ProjectObjectSubtask' WHERE parent_type = 'Task'");
          DB::execute("UPDATE $commit_project_objects_table SET parent_type = 'Task' WHERE parent_type = 'Ticket'");
        } // if
      } catch (Exception $e) {
        return $e->getMessage();
      } //try
      return true;
    } //updateCommitProjectObjects
    
    /**
     * Archive old projects
     * 
     * @return boolean
     */
    function archiveOldProjects() {
      try {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'projects SET state = ? WHERE state > ? AND (completed_on IS NOT NULL AND completed_on < ?)', 2, 2, new DateValue('-6 months')); // STATE_ARCHIVED = 2
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // archiveOldProjects

  }
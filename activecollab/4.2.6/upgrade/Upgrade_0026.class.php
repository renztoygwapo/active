<?php

	/**
   * Update activeCollab 3.0.2 to activeCollab 3.0.3
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0026 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.0.2';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.0.3';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'initObjectContextsTable' => 'Initialize object contexts cache',
        'updateQuotes' => 'Update quotes',  
        'updateMailingQueue' => 'Update mailing queue',
      	'updateAssetVersions' => 'Updating file and text document versions',
        'updateInvoiceDrafts' => 'Updating data for draft invoices', 
        'updateApiClientSubscriptions' => 'Update API client subscriptions table', 
        'updateProjectRequestCustomFields' => 'Fix custom project requests fields', 
        'updateLocalization' => 'Update localization storage', 
      );
    } // getActions
    
    /**
     * Initialize object contexts storage
     * 
     * @return boolean
     */
    function initObjectContextsTable() {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
      
      try {
        DB::execute("CREATE TABLE " . TABLE_PREFIX . "object_contexts (
          id int unsigned NOT NULL auto_increment,
          parent_type varchar(50) DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          context varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY (id),
          INDEX parent (parent_type, parent_id),
          UNIQUE context (context)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // initObjectContextsTable

    /**
     * Standardize value of company_id column in quotes table and add additional columns for 'sent to'
     *
     * @return boolean
     */
    function updateQuotes() {
      $quotes_table = TABLE_PREFIX.'quotes';
      
      try {
        if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='invoicing'")) {
          DB::execute("UPDATE $quotes_table SET company_id = 0 WHERE company_id IS NULL");
          DB::execute("UPDATE $quotes_table SET sent_to_id = 0 WHERE sent_to_id IS NULL");
          DB::execute("ALTER TABLE $quotes_table CHANGE sent_to_id sent_to_id INT(10) UNSIGNED NOT NULL DEFAULT '0'");
          DB::execute("ALTER TABLE $quotes_table ADD sent_to_name VARCHAR(100) NULL DEFAULT NULL AFTER sent_to_id");
          DB::execute("ALTER TABLE $quotes_table ADD sent_to_email VARCHAR(150) NULL DEFAULT NULL AFTER sent_to_name");
        } // if
      } catch (Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateQuotes
    
    /**
     * Update mailing queue
     * 
     * @return boolean
     */
    function updateMailingQueue() {
      try {
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "outgoing_messages ADD last_send_error VARCHAR(255) NULL DEFAULT NULL AFTER send_retries");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateMailingQueue
    
    /**
     * Update asset versions
     * 
		 * @return boolean
     */
    function updateAssetVersions() {
    	$project_objects_table = TABLE_PREFIX . 'project_objects';
    	try {
    		DB::execute("UPDATE $project_objects_table SET datetime_field_1 = updated_on, integer_field_3 = updated_by_id, text_field_1 = updated_by_name, text_field_2 = updated_by_email WHERE updated_on AND type = ?", 'File');
    		DB::execute("UPDATE $project_objects_table SET datetime_field_1 = updated_on, integer_field_2 = updated_by_id, varchar_field_1 = updated_by_name, varchar_field_2 = updated_by_email WHERE updated_on AND type = ?", 'TextDocument');    		
    	} catch (Exception $e) {
    		return $e->getMessage();
    	} // try
    	
    	return true;
    } // updateAssetVersions


    /**
     * Fix potential issues with draft invoices having due date set by previous v3 releases
     *
     * @return boolean
     */
    function updateInvoiceDrafts() {
      if($this->isModuleInstalled('invoicing')) {
        try {
          DB::execute("UPDATE ".TABLE_PREFIX."invoices SET due_on = NULL WHERE status = '0'");
        } catch (Exception $e) {
          return $e->getMessage();
        } // try
      } // if
      
      return true;
    } // updateInvoiceDrafts
    
    /**
     * Update API client subscriptions
     * 
     * @return boolean
     */
    function updateApiClientSubscriptions() {
      try {
        $subscriptions_table = TABLE_PREFIX . 'api_client_subscriptions';
        
        DB::execute("ALTER TABLE $subscriptions_table ADD is_read_only TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER is_enabled");
        DB::execute("UPDATE $subscriptions_table SET is_read_only = ?", false);
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateApiClientSubscriptions
    
    /**
     * Update project requests
     * 
     * @return boolean
     */
    function updateProjectRequestCustomFields() {
      try {
        $custom_fields = DB::executeFirstCell('SELECT value FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', 'project_requests_custom_fields');
        if($custom_fields) {
          $custom_fields = unserialize($custom_fields);
          
          if(isset($custom_fields['custom_field_6'])) {
            unset($custom_fields['custom_field_6']);
            
            DB::execute('UPDATE ' . TABLE_PREFIX . 'config_options SET value = ? WHERE name = ?', serialize($custom_fields), 'project_requests_custom_fields');
          } // if
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateProjectRequestCustomFields
    
    /**
     * Update localization
     * 
     * @return boolean
     */
    function updateLocalization() {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

      $phrases_table = TABLE_PREFIX . 'language_phrases';
      $translations_table = TABLE_PREFIX . 'language_phrase_translations';

      try {
        DB::execute("DROP TABLE IF EXISTS $phrases_table");
        DB::execute("DROP TABLE IF EXISTS $translations_table");
        
        DB::execute("CREATE TABLE $phrases_table (
          hash varchar(32)  DEFAULT NULL,
          phrase text,
          module varchar(50) NOT NULL DEFAULT '',
          is_serverside int(2) unsigned NULL DEFAULT NULL,
          is_clientside int(2) unsigned NULL DEFAULT NULL,
          UNIQUE module_phrase (hash, module)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        
        DB::execute("CREATE TABLE $translations_table (
          language_id int(11) unsigned NOT NULL DEFAULT 0,
          phrase_hash varchar(32)  DEFAULT NULL,
          translation text,
          UNIQUE language_phrase (phrase_hash, language_id),
          INDEX language_id (language_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateLocalization
    
  }
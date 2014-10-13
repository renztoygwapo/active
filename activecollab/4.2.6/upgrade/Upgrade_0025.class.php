<?php

	/**
   * Update activeCollab 3.0.1 to activeCollab 3.0.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0025 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.0.1';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.0.2';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateDateFormat' => 'Update date format values', 
        'updateActivityLogs' => 'Update activity logs storage', 
      	'updateRecurringProfiles' => 'Update recurring profiles',
        'updateQuotes' => 'Update quotes',
        'updateProjectRequests' => 'Update project requests',
        'updateFileVersionsTable' => 'Update file versions storage', 
        'updateTextDocumentVersionsTable' => 'Update text document versions storage', 
        'updateNotebookPagesTable' => 'Update notebook pages storage',
        'updatePaymentGatewayTable' => 'Update payment gateway table' 
      );
    } // getActions
    
    /**
     * Update date format values
     * 
     * @return boolean
     */
    function updateDateFormat() {
      try {
        DB::beginWork('Updating date format @ ' . __CLASS__);
        
        if(DIRECTORY_SEPARATOR != '\\') {
          $replace = array(
            "%Y/%m/%e" => "%Y/%m/%d", 
            "%m/%e/%Y" => "%m/%d/%Y",
          );
          
          $format_date = DB::executeFirstCell('SELECT value FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', 'format_date');
          if($format_date) {
            $format_date = unserialize($format_date);
            
            if(isset($replace[$format_date])) {
              DB::execute('UPDATE ' . TABLE_PREFIX . 'config_options SET value = ? WHERE name = ?', serialize($replace[$format_date]), 'format_date');
            } // if
          } // if
          
          foreach($replace as $k => $v) {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'config_option_values SET value = ? WHERE name = ? AND value = ?', serialize($v), 'format_date', serialize($k));
          } // foreach
        } // if
        
        DB::commit('Date format updated @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to update date format @ ' . __CLASS__);
        return $e->getMessage();
      } // try
      
      return true;
    } // updateDateFormat
    
    /**
     * Update payment gateways
     * 
     * @return boolean
     */
    function updatePaymentGatewayTable() {
      $payment_gateway_table = TABLE_PREFIX . 'payment_gateways';
      try {
        DB::execute("ALTER TABLE $payment_gateway_table ADD is_enabled tinyint(1) NOT NULL DEFAULT '0'");
      } catch (Exception $e) {
        return $e->getMessage();
      } //try
      return true;
    } // updatePaymentGatewayTable
    
    /**
     * Update activity logs
     * 
     * @return boolean
     */
    function updateActivityLogs() {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
      $activity_logs_table = TABLE_PREFIX . 'activity_logs';
      
      try {
        DB::execute("DROP TABLE $activity_logs_table");
        DB::execute("CREATE TABLE $activity_logs_table (
          id int unsigned NOT NULL auto_increment,
          subject_type varchar(50) NOT NULL DEFAULT '',
          subject_id int(5) unsigned NOT NULL DEFAULT 0,
          subject_context varchar(255) NOT NULL DEFAULT '',
          action varchar(100) NOT NULL DEFAULT '',
          target_type varchar(50)  DEFAULT NULL,
          target_id int(5) unsigned NULL DEFAULT NULL,
          created_on datetime  DEFAULT NULL,
          created_by_id int unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          comment text ,
          PRIMARY KEY (id),
          INDEX created_on (created_on),
          INDEX subject (subject_type, subject_id),
          INDEX subject_context (subject_context),
          INDEX action (action),
          INDEX target (target_type, target_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateActivityLogs
    
    /**
     * Upgrade recurring profile module
     * 
     * @return boolean
     */
    function updateRecurringProfiles() {
      $recurring_profiles_table = TABLE_PREFIX . 'recurring_profiles';
      $request_approval_table = TABLE_PREFIX . 'recurring_approval_requests';
      
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute("ALTER TABLE $recurring_profiles_table ADD auto_issue tinyint(1) NOT NULL DEFAULT '0' AFTER occurrences");
          DB::execute("ALTER TABLE $recurring_profiles_table DROP request_approval");
          
          DB::execute('DROP TABLE IF EXISTS ' . DB::escapeTableName($request_approval_table));
        }//if
        
      } catch (Exception $e) {
        return $e->getMessage();
      } //try
      
    }//updateRecurringProfiles

    /**
     * Update Quotes table structure
     * 
     * @return boolean
     */
    function updateQuotes() {
      $quotes_table = TABLE_PREFIX . "quotes";
      
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute("ALTER TABLE $quotes_table ADD company_name VARCHAR(150) NULL DEFAULT NULL AFTER company_id");
          DB::execute("ALTER TABLE $quotes_table ADD public_id VARCHAR(32) NOT NULL DEFAULT '' AFTER id");
          DB::execute("ALTER TABLE $quotes_table ADD private_note TEXT NULL DEFAULT NULL AFTER note");
          DB::execute("ALTER TABLE $quotes_table ADD recipient_id INT(10) NOT NULL DEFAULT '0' AFTER sent_by_email");
          DB::execute("ALTER TABLE $quotes_table ADD recipient_name VARCHAR(100) NULL DEFAULT NULL AFTER recipient_id");
          DB::execute("ALTER TABLE $quotes_table ADD recipient_email VARCHAR(150) NULL DEFAULT NULL AFTER recipient_name");

          $quotes = DB::execute("SELECT id, public_id, created_on FROM $quotes_table");
          if ($quotes) {
            foreach($quotes as $quote) {
              if (empty($quote['public_id'])) {
                DB::execute("UPDATE $quotes_table SET public_id = ? WHERE id = ?", md5($quote['id'].$quote['created_on']), $quote['id']);
              } // if
            } // foreach
          } // if
        } // if
      } catch (Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateQuotes

    /**
     * Update Project Requests
     * 
     * @return boolean
     */
    function updateProjectRequests() {
      $project_requests_table = TABLE_PREFIX . 'project_requests';
      try {
        DB::execute("ALTER TABLE $project_requests_table ADD created_by_company_address VARCHAR(150) NULL DEFAULT NULL AFTER created_by_company_name");
      } catch (Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateProjectRequests
    
    /**
     * Update file versions table
     * 
     * @return boolean
     */
    function updateFileVersionsTable() {
      if ($this->isModuleInstalled('files')) {
        $file_versions_table = TABLE_PREFIX . 'file_versions';

        try {
          DB::execute("ALTER TABLE $file_versions_table DROP PRIMARY KEY");
          DB::execute("ALTER TABLE $file_versions_table ADD id INT UNSIGNED NOT NULL DEFAULT '0' FIRST");

          $rows = DB::execute("SELECT file_id, version_num FROM $file_versions_table ORDER BY created_on");
          if($rows) {
            $counter = 1;

            foreach($rows as $row) {
              DB::execute("UPDATE $file_versions_table SET id = ? WHERE file_id = ? AND version_num = ?", $counter++, $row['file_id'], $row['version_num']);
            } // foreach
          } // if

          DB::execute("ALTER TABLE $file_versions_table CHANGE id id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");
          DB::execute("ALTER TABLE $file_versions_table ADD INDEX file_version (file_id, version_num)");

        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } //if
      
      return true;
    } // updateFileVersionsTable
    
    /**
     * Update text document versions table
     * 
     * @return boolean
     */
    function updateTextDocumentVersionsTable() {
      if ($this->isModuleInstalled('files')) {
        $text_document_versions = TABLE_PREFIX . 'text_document_versions';

        try {
          DB::execute("ALTER TABLE $text_document_versions DROP PRIMARY KEY");
          DB::execute("ALTER TABLE $text_document_versions ADD id INT UNSIGNED NOT NULL DEFAULT '0' FIRST");

          $rows = DB::execute("SELECT text_document_id, version_num FROM $text_document_versions ORDER BY created_on");
          if($rows) {
            $counter = 1;

            foreach($rows as $row) {
              DB::execute("UPDATE $text_document_versions SET id = ? WHERE text_document_id = ? AND version_num = ?", $counter++, $row['text_document_id'], $row['version_num']);
            } // foreach
          } // if

          DB::execute("ALTER TABLE $text_document_versions CHANGE id id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");
          DB::execute("ALTER TABLE $text_document_versions ADD INDEX text_document_version (text_document_id, version_num)");
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } //if
      
      return true;
    } // updateTextDocumentVersionsTable
    
    /**
     * Update notebook pages table
     * 
     * @return boolean
     */
    function updateNotebookPagesTable() {
      $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';
      
      try {
        if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'notebooks'")) {
          DB::execute("ALTER TABLE $notebook_pages_table ADD last_version_on DATETIME NULL DEFAULT NULL AFTER updated_by_email");
          DB::execute("ALTER TABLE $notebook_pages_table ADD last_version_by_id INT UNSIGNED NULL DEFAULT NULL AFTER last_version_on");
          DB::execute("ALTER TABLE $notebook_pages_table ADD last_version_by_name VARCHAR(100) NULL DEFAULT NULL AFTER last_version_by_id");
          DB::execute("ALTER TABLE $notebook_pages_table ADD last_version_by_email VARCHAR(150) NULL DEFAULT NULL AFTER last_version_by_name;");

          $notebook_pages = DB::execute("SELECT id FROM $notebook_pages_table");
          if($notebook_pages_table) {
            foreach($notebook_pages as $notebook_page) {
              $last_page_version = DB::executeFirstRow('SELECT created_on, created_by_id, created_by_name, created_by_email FROM ' . TABLE_PREFIX . 'notebook_page_versions WHERE notebook_page_id = ? ORDER BY version DESC LIMIT 0, 1', $notebook_page['id']);
              if($last_page_version) {
                DB::execute("UPDATE $notebook_pages_table SET last_version_on = ?, last_version_by_id = ?, last_version_by_name = ?, last_version_by_email = ? WHERE id = ?", $last_page_version['created_on'], $last_page_version['created_by_id'], $last_page_version['created_by_name'], $last_page_version['created_by_email'], $notebook_page['id']);
              } // if
            } // foreach
          } // if
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateNotebookPagesTable
    
  }
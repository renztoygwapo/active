<?php

  /**
   * Update activeCollab 2.3 to activeCollab 2.3.1
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0019 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '2.3';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '2.3.1';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateUserAgentField' => 'Update user agent field', 
    	  'updateStatusTable' => 'Update status updates table',
    	  'updateInvoicingModule' => 'Update invoicing module',
    	  'updateSourceConfigOptions' => 'Update Source module configuration options', 
    	  'updateMimeType' => 'Update MIME type field length', 
    	  'removeResolutionField' => 'Remove resolution field', 
    	);
    } // getActions
    
    /**
     * Update user agent field type
     *
     * @return boolean
     */
    function updateUserAgentField() {
      DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'user_sessions CHANGE user_agent user_agent text');
      
      return true;
    } // updateUserAgentField
    
    /**
     * Update Source module's configuration
     *
     * @return boolean
     */
    function updateConfigOptions() {
      if(array_var(DB::executeFirstRow("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . "modules WHERE name = 'source'"), 'row_count') == 1) {
        DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, type, value) VALUES ('source_svn_use_output_redirect', 'source', 'system', 'b:0;'), ('source_svn_trust_server_cert', 'source', 'system', 'b:0;')");
      } // if
      
      return true;
    } // updateConfigOptions
    
    /**
     * Update status table
     *
     * @return boolean
     */
    function updateStatusTable() {
      $status_updates_table = TABLE_PREFIX . 'status_updates';
      
      if(in_array($status_updates_table, DB::listTables(TABLE_PREFIX))) {
        $updates = array(
          "ALTER TABLE $status_updates_table CHANGE created_on created_on DATETIME DEFAULT NULL",
          "ALTER TABLE $status_updates_table ADD parent_id INT UNSIGNED NULL DEFAULT NULL AFTER id",
          "ALTER TABLE $status_updates_table ADD INDEX (parent_id)",
          "ALTER TABLE $status_updates_table ADD last_update_on DATETIME NULL DEFAULT NULL AFTER created_on",
          "ALTER TABLE $status_updates_table ADD INDEX (last_update_on)",
          "UPDATE $status_updates_table SET last_update_on = created_on",
        );
        
        foreach($updates as $update) {
      	  $updated = DB::execute($update);
      	  if(is_error($updated)) {
      	    return $updated->getMessage();
      	  } // if
      	} // foreach
      } // if
      
      return true;
    } // updateStatusTable
    
    /**
     * Update invoicing module
     * 
     * @return boolean
     */
    function updateInvoicingModule() {
      $invoices_table = TABLE_PREFIX . 'invoices';
      $companies_table = TABLE_PREFIX . 'companies';
      
      // if module is not installed, no need to upgrade it
      if(in_array($invoices_table, DB::listTables(TABLE_PREFIX))) {
        $updates = array(
          "ALTER TABLE $invoices_table ADD company_name varchar(50) NOT NULL AFTER number", 
          "ALTER TABLE $invoices_table CHANGE closed_by_email closed_by_email varchar(150) NULL DEFAULT NULL",
        );
        
        foreach($updates as $update) {
          $update_table = DB::execute($update);
          if(is_error($update_table)) {
            return $update_table->getMessage();
          } // if
        } // foreach
        
        // first update table by adding missing field
        
        
        // retrive all invoices with missing cached value of company name
        $invoices = DB::execute("SELECT id,company_id FROM $invoices_table");
        if (is_foreachable($invoices)) {
          $company_ids = array();
          // extract companies that should be retrieved because their names
          foreach ($invoices as $invoice) {
            if (!in_array($invoice['company_id'], $company_ids)) {
          	 $company_ids[] = $invoice['company_id'];
            } // if
          } // foreach
          
          // retrieve those companies
          $raw_companies = DB::execute("SELECT id,name FROM $companies_table WHERE id IN (?)", $company_ids);
          $companies = array();
          // format their names so they can easiliy accessed
          if (is_foreachable($raw_companies)) {
            foreach ($raw_companies as $raw_company) {
              $companies[$raw_company['id']] = $raw_company['name'];
            } // foreach
          } // if
          
          // finally upload every invoice with missing company name
          foreach ($invoices as $invoice) {
            $result = DB::execute("UPDATE $invoices_table SET company_name = ? WHERE id= ?", array_var($companies, $invoice['company_id'], 'Unknown Company'), $invoice['id']);
          	if (is_error($result)) {
          		return $result->getMessage();
          	} // if
          } // if
        } // if
        
        // update fields. we need to use DECIMAL instead of double for money fields
        $result = DB::execute("ALTER TABLE ".TABLE_PREFIX."invoice_item_templates CHANGE quantity quantity DECIMAL(13,3) UNSIGNED NOT NULL DEFAULT '1', CHANGE unit_cost unit_cost DECIMAL(13,3) NOT NULL DEFAULT '0.00'");
      	if (is_error($result)) {
      		return $result->getMessage();
      	} // if
        $result = DB::execute("ALTER TABLE ".TABLE_PREFIX."invoice_payments CHANGE amount amount DECIMAL(13,3) NOT NULL");
      	if (is_error($result)) {
      		return $result->getMessage();
      	} // if
        $result = DB::execute("ALTER TABLE ".TABLE_PREFIX."tax_rates CHANGE percentage percentage DECIMAL(6,3) NOT NULL");
      	if (is_error($result)) {
      		return $result->getMessage();
      	} // if
        $result = DB::execute("ALTER TABLE ".TABLE_PREFIX."invoice_items CHANGE quantity quantity DECIMAL(13,3) UNSIGNED NOT NULL DEFAULT '1.00', CHANGE unit_cost unit_cost DECIMAL(13,3) NOT NULL DEFAULT '0.00'");
      	if (is_error($result)) {
      		return $result->getMessage();
      	} // if
        $result = DB::execute("ALTER TABLE ".TABLE_PREFIX."currencies CHANGE default_rate default_rate DECIMAL(13,3) UNSIGNED NOT NULL");
      	if (is_error($result)) {
      		return $result->getMessage();
      	} // if
      } // if
      
      return true;
    } // updateInvoicingModule
    
    /**
     * Fix fields withot default value
     * 
     * @return boolean
     */
    function updateIncomingMailModule() {
      $incoming_mail_activity_logs_table = TABLE_PREFIX . 'incoming_mail_activity_logs';
      
      if(in_array($incoming_mail_activity_logs_table, DB::listTables(TABLE_PREFIX))) {
        $result = DB::execute("ALTER TABLE $incoming_mail_activity_logs_table 
          CHANGE mailbox_id mailbox_id smallint(5) unsigned default NULL,
          CHANGE state state tinyint(3) unsigned NOT NULL default '0',
          CHANGE response response varchar(255) default NULL,
          CHANGE sender sender varchar(255) default NULL,
          CHANGE subject subject varchar(255) default NULL,
          CHANGE incoming_mail_id incoming_mail_id int(10) unsigned default NULL,
          CHANGE project_object_id project_object_id int(10) unsigned default NULL,
          CHANGE created_on created_on datetime default NULL");
        
      	if(is_error($result)) {
      		return $result->getMessage();
      	} // if
      } // if
    	
    	return true;
    } // updateIncomingMailModule
    
    /**
     * Update config options
     *
     * @param void
     * @return boolean
     */
    function updateSourceConfigOptions() {
      if(array_var(DB::executeFirstRow("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . "modules WHERE name = 'source'"), 'row_count') == 1) {
        DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, type, value) VALUES ('source_svn_use_output_redirect', 'source', 'system', 'b:0;')");
        DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, type, value) VALUES ('source_svn_trust_server_cert', 'source', 'system', 'b:0;')");
      } // if
      return true;
    } // updateSourceConfigOptions
    
    /**
     * Update length of MIME type fields
     *
     * @return boolean
     */
    function updateMimeType() {
      DB::execute("alter table " . TABLE_PREFIX . "attachments change mime_type mime_type varchar(255) not null default 'application/octet-stream'");
      
      if(in_array(TABLE_PREFIX . 'documents', DB::listTables(TABLE_PREFIX))) {
        DB::execute("alter table " . TABLE_PREFIX . "documents change mime_type mime_type varchar(255) null default null");
      } // if
      
      return true;
    } // updateMimeType
    
    /**
     * Remove resolution field
     *
     * @return boolean
     */
    function removeResolutionField() {
      if(in_array('resolution', DB::listTableFields(TABLE_PREFIX . 'project_objects'))) {
        DB::execute("alter table " . TABLE_PREFIX . "project_objects drop resolution");
      } // if
      
      return true;
    } // removeResolutionField
    
  }
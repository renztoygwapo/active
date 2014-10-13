<?php

  /**
   * Update activeCollab 2.3.1 to activeCollab 2.3.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0020 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '2.3.1';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '2.3.2';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateExistingTables' => 'Update existing tables'
    	);
    } // getActions
    
    /**
     * Update length of MIME type fields
     *
     * @return boolean
     */
    function updateExistingTables() {
      $existing_tables = DB::listTables(TABLE_PREFIX);
      
      DB::execute("alter table " . TABLE_PREFIX . "attachments change mime_type mime_type varchar(255) not null default 'application/octet-stream'");
      DB::execute('alter table ' . TABLE_PREFIX . 'project_objects change text_field_1 text_field_1 longtext');
      DB::execute('alter table ' . TABLE_PREFIX . 'project_objects change text_field_2 text_field_2 longtext');
      
      if(DB::tableExists(TABLE_PREFIX . 'documents')) {
        DB::execute("alter table " . TABLE_PREFIX . "documents change mime_type mime_type varchar(255) null default null");
      } // if
      
      if(DB::tableExists(TABLE_PREFIX . 'incoming_mailboxes')) {
        DB::execute('alter table ' . TABLE_PREFIX . 'incoming_mailboxes change id id int unsigned not null auto_increment');
      } // if
      
      return true;
    } // updateExistingTables
    
  }
<?php

  /**
   * Update activeCollab 2.0 to activeCollab 2.0.1
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0008 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '2.0';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '2.0.1';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateConfigOptions' => 'Create new and update existing configuration options',
    	  'updateAttachmentParentType' => 'Update attachments parent type value', 
    	);
    } // getActions
    
    /**
     * Insert configuration options
     *
     * @param void
     * @return boolean
     */
    function updateConfigOptions() {
      $config_options_table = TABLE_PREFIX . 'config_options';
      if(array_var(DB::executeFirstRow("SELECT COUNT(*) AS 'row_count' FROM $config_options_table WHERE name = 'email_splitter_translations'"), 'row_count') < 1) {
        DB::execute("INSERT INTO $config_options_table (name, module, type, value) VALUES ('email_splitter_translations', 'system', 'system', 'a:0:{}');");
      } // if
      return true;
    } // updateConfigOptions
    
    /**
     * Update attachments for setups where file type is not properly set
     *
     * @param void
     * @return boolean
     */
    function updateAttachmentParentType() {
      $attachments_table = TABLE_PREFIX . 'attachments';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      
      $rows = DB::execute("SELECT DISTINCT $project_objects_table.id, $project_objects_table.type FROM $project_objects_table, $attachments_table WHERE $project_objects_table.id = $attachments_table.parent_id AND $attachments_table.parent_type IS NULL");
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $attachment_type = strtolower($row['type']) == 'file' ? 'file_revision' : 'attachment';
          DB::execute("UPDATE $attachments_table SET parent_type = ?, attachment_type = ? WHERE parent_id = ?", $row['type'], $attachment_type, $row['id']);
        } // foreach
      } // if
      
      return true;
    } // updateAttachmentParentType
    
  }
<?php

  /**
   * Update activeCollab 2.1.1 to activeCollab 2.1.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0013 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '2.1.1';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '2.1.2';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateAttachmentParentType' => 'Update attachment parent types',
    	);
    } // getActions
    
    /**
     * Update parent type for old first discussion comments to Discussion
     *
     * @param void
     * @return boolean
     */
    function updateAttachmentParentType() {
      $rows = DB::execute('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ?', 'Discussion');
      if(is_foreachable($rows)) {
        $discussion_ids = array();
        foreach($rows as $row) {
          $discussion_ids[] = (integer) $row['id'];
        } // foreach
        
        DB::execute('UPDATE ' . TABLE_PREFIX . 'attachments SET parent_type = ? WHERE parent_id IN (?)', 'Discussion', $discussion_ids);
      } // if
      
      return true;
    } // updateExistingTables
    
  }
<?php

	/**
   * Update activeCollab 3.0.6 to activeCollab 3.0.7
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0030 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.0.6';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.0.7';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateRecurringProfiles' => 'Update recurring profiles recipient fields',
        'updateModificationLog' => 'Update modification log table', 
      );
    } // getActions

    /**
     * Upgrade recurring profile module
     * 
     * @return boolean
     */
    function updateRecurringProfiles() {
      $recurring_profiles_table = TABLE_PREFIX . 'recurring_profiles';
      
      try {
        if($this->isModuleInstalled('invoicing')) {
          $fields = DB::listTableFields($recurring_profiles_table);
          
          if(!in_array('recipient_id',$fields)) {
            DB::execute("ALTER TABLE $recurring_profiles_table ADD recipient_id INT(10) NOT NULL DEFAULT '0' AFTER visibility");
          }//if
          if(!in_array('recipient_name',$fields)) {
            DB::execute("ALTER TABLE $recurring_profiles_table ADD recipient_name VARCHAR(100) NULL DEFAULT NULL AFTER recipient_id");
          }//if
          if(!in_array('recipient_email',$fields)) {
            DB::execute("ALTER TABLE $recurring_profiles_table ADD recipient_email VARCHAR(150) NULL DEFAULT NULL AFTER recipient_name");
          }//if
        }//if
        
      } catch (Exception $e) {
        return $e->getMessage();
      } //try
      
      return true;
    }//updateRecurringProfiles

    /**
     * Update modification log table
     * 
     * @return boolean
     */
    function updateModificationLog() {
      try {
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'modification_logs DROP type');
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
      
      return true;
    } // updateModificationLog

  }
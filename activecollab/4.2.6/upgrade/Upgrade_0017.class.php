<?php

  /**
   * Update activeCollab 2.2.1 to activeCollab 2.2.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0017 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '2.2.1';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '2.2.2';
    
    /**
     * Return script actions
     *
     * @param void
     * @return array
     */
    function getActions() {
    	return array(
    	  'updateConfigOptions' => 'Update configuration options',
    	);
    } // getActions
    
    /**
     * Update parent type for old first discussion comments to Discussion
     *
     * @param void
     * @return boolean
     */
    function updateConfigOptions() {
      if(array_var(DB::executeFirstRow("SELECT COUNT(*) AS 'row_count' FROM " . TABLE_PREFIX . "modules WHERE name = 'source'"), 'row_count') == 1) {
        DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, type, value) VALUES ('source_svn_config_dir', 'source', 'system', 'N;')");
      } // if
      return true;
    } // updateConfigOptions
    
  }
<?php

  /**
   * Make sure that we have everyone with 2.2.2 on activeCollab 2.3
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0018 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '2.2.2';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '2.3';
    
    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
    	return array(
    	  'emptyAction' => 'Skip 2.2.2 to 2.3 version logging step',
    	);
    } // getActions
    
    /**
     * Empty action
     * 
     * @return boolean
     */
    function emptyAction() {
      return true;
    } // emptyAction
    
  }
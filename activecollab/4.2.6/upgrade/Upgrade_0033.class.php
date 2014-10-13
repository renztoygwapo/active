<?php

	/**
   * Update activeCollab 3.1.0 to activeCollab 3.1.1
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0033 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.0';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.1';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'cleanMailingActivityLog' => 'Clean up mailing activity log',
      );
    } // getActions

    /**
     * Clean up mailing activity log
     *
     * @return bool|string
     */
    function cleanMailingActivityLog() {
      try {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'mailing_activity_logs WHERE created_on < ?', DateValue::makeFromString('-30 days'));
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // cleanMailingActivityLog

  }
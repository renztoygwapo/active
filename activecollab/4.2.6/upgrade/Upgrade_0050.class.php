<?php

  /**
   * Update activeCollab 3.2.0 to activeCollab 3.2.1
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0050 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.2.0';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.1';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'upgradeOutgoingMessages' => 'Upgrade outgoing messages table',
      );
    } // getActions

    /**
     * Upgrade outgoing messages table
     *
     * @return bool|string
     */
    function upgradeOutgoingMessages() {
      try {
        $outgoing_messages_table = TABLE_PREFIX . 'outgoing_messages';

        if(!in_array('code', $this->listTableFields($outgoing_messages_table))) {
          DB::execute("ALTER TABLE $outgoing_messages_table ADD code VARCHAR(25) NULL DEFAULT NULL AFTER context_id");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // if
      return true;
    } // upgradeOutgoingMessages

  }
<?php

  /**
   * Update activeCollab 4.0.0 to activeCollab 4.0.4
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0081 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.0';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.4';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'insertNotificationsPopupConfigOptions' => 'Insert notifications popup config options'
      );
    } // getActions

    /**
     * Setup auto update
     *
     * @return bool|string
     */
    function insertNotificationsPopupConfigOptions() {
      try {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('popup_show_only_unread', 'notifications', ?)", serialize(false));
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // insertNotificationsPopupConfigOptions

  }
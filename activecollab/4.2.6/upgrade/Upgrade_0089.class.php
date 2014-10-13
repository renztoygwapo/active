<?php


  /**
   * Update activeCollab 4.0.12 to activeCollab 4.0.13
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0089 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.12';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.13';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'removeSubscribersAndAssigneesFromTrashedUsers' => 'Remove subscription and assignments from trashed users',
      );
    } // getActions

    function removeSubscribersAndAssigneesFromTrashedUsers() {
      $subscriptions_table = TABLE_PREFIX . 'subscriptions';
      $assignments_table = TABLE_PREFIX . 'assignments';
      $users_table = TABLE_PREFIX . 'users';

      defined('STATE_DELETED') or define('STATE_DELETED', 0);

      DB::executeFirstColumn("DELETE FROM $subscriptions_table WHERE user_id IN (SELECT id FROM $users_table WHERE state = ?)", STATE_DELETED);
      DB::executeFirstColumn("DELETE FROM $assignments_table WHERE user_id IN (SELECT id FROM $users_table WHERE state = ?)", STATE_DELETED);
    } //removeSubscribersAndAssigneesFromTrashedUsers

  }

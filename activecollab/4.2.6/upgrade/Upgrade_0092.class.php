<?php

  /**
   * Update activeCollab 4.0.15 to activeCollab 4.0.16
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0092 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.15';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.16';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return null;
    } // getActions

  }
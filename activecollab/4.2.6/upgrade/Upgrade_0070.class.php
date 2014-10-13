<?php

  /**
   * Update activeCollab 3.3.6 to activeCollab 3.3.7
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0070 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.6';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.7';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return null;
    } // getActions

  }
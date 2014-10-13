<?php

  /**
   * Update activeCollab 3.2.13 to activeCollab 3.2.14
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0062 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.2.13';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.14';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return null;
    } // getActions

  }
<?php

  /**
   * Update activeCollab 4.0.8 to activeCollab 4.0.9
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0085 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.8';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.9';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return null;
    } // getActions

  }
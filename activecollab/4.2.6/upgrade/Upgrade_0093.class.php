<?php

  /**
   * Update activeCollab 4.0.16 to activeCollab 4.0.17
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0093 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.16';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.17';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return null;
    } // getActions

  }
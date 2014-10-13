<?php

  /**
   * Update activeCollab 3.2.11 to activeCollab 3.2.12
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0060 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.2.11';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.12';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'sourceUpdateTypeBugfix' => 'Bugfix for update type in Source module',
      );
    } // getActions

    /**
     * Bugfix for update type in Source module
     *
     * @return bool|string
     */
    function sourceUpdateTypeBugfix() {
      if ($this->isModuleInstalled('source')) {
        $source_repositories_table = TABLE_PREFIX . 'source_repositories';
        try {
          DB::execute("UPDATE $source_repositories_table SET update_type = 3 WHERE (update_type <> 1 AND update_type <> 2)");
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } //if

      return true;
    } // sourceUpdateTypeBugfix

  }

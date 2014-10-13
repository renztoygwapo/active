<?php

  /**
   * Update activeCollab 3.1.6 to activeCollab 3.1.7
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0039 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.7';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.8';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateConfigOptions' => 'Update configuration options',
        'updateNumberGenerator' => 'Update invoicing number generator',
      );
    } // getActions

    /**
     * Update configuration options
     *
     * @return bool|string
     */
    function updateConfigOptions() {
      try {
        DB::execute('UPDATE ' . TABLE_PREFIX . "config_options SET value = 'b:1;' WHERE name = 'clients_can_delegate_to_employees'");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateConfigOptions

    /**
     * Update number generator
     *
     * @return bool
     */
    function updateNumberGenerator() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES ('invoicing_number_counter_padding', 'invoicing', 'N;')");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateNumberGenerator

  }
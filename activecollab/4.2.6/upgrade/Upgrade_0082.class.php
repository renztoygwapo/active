<?php

  /**
   * Update activeCollab 4.0.4 to activeCollab 4.0.5
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0082 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.4';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.5';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'addProxyConfigOptions' => 'Adding proxy config options'
      );
    } // getActions

    /**
     * Add proxy config options
     *
     * @return boolean
     */
    function addProxyConfigOptions() {
      try {
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('network_proxy_enabled', 'environment', ?)", serialize(false));
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('network_proxy_protocol', 'environment', ?)", serialize('http'));
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('network_proxy_address', 'environment', ?)", serialize(''));
        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('network_proxy_port', 'environment', ?)", serialize(''));
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // addProxyConfigOptions

  }
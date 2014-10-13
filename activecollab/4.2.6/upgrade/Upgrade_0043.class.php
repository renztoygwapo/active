<?php

  /**
   * Update activeCollab 3.1.11 to activeCollab 3.1.12
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0043 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.11';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.12';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateCompanyFields' => 'Update company fields',
        'updateVersionCheckingOptions' => 'Load default version information',
        'updateInvoicingModel' => 'Update invoicing settings',
      );
    } // getActions

    /**
     * Update company fields
     *
     * @return bool
     */
    function updateCompanyFields() {
      try {
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "companies ADD note VARCHAR(255) NULL DEFAULT NULL AFTER name");
      } catch (Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateCompanyFields

    /**
     * Update version checking configuration options
     *
     * @return bool|string
     */
    function updateVersionCheckingOptions() {
      try {
        $branding_removed = defined('LICENSE_COPYRIGHT_REMOVED') && LICENSE_COPYRIGHT_REMOVED;
        $license_expires = defined('LICENSE_EXPIRES') && LICENSE_EXPIRES ? LICENSE_EXPIRES : date('Y-m-d');
        $license_package = defined('LICENSE_PACKAGE') && LICENSE_PACKAGE == 'corporate' ? 'corporate' : 'smallbiz';
        $latest_version = APPLICATION_VERSION;

        DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES
          ('help_improve_application', 'system', 'b:1;'),
          ('license_copyright_removed', 'system', ?),
          ('license_expires', 'system', ?),
          ('license_package', 'system', ?),
          ('license_details_updated_on', 'system', 'N;'),
          ('latest_version', 'system', ?),
          ('latest_available_version', 'system', ?),
          ('remove_branding_url', 'system', 'N;'),
          ('renew_support_url', 'system', 'N;'),
          ('upgrade_to_corporate_url', 'system', 'N;');", serialize($branding_removed), serialize($license_expires), serialize($license_package), $latest_version, $latest_version);
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateVersionCheckingOptions

    /**
     * Update invoicing model settings
     *
     * @return bool|string
     */
    function updateInvoicingModel() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES ('invoicing_default_due', 'invoicing', 'i:15;')");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateInvoicingModel

  }
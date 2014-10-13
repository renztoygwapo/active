<?php

  /**
   * Update activeCollab 4.0.9 to activeCollab 4.0.10
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0086 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.9';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.10';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'cleanUpConfigOptions' => 'Clean up old configuration options',
        'increaseQuoteNameLength' => 'Increasing quote name field size',
      );
    } // getActions

    /**
     * Clean up old configuration options
     *
     * @return bool|string
     */
    function cleanUpConfigOptions() {
      try {
        $this->removeConfigOption('project_templates_category');
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // cleanUpConfigOptions

    /**
     * Increase Quoet Name Length
     *
     * @return boolean
     */
    function increaseQuoteNameLength() {
      if($this->isModuleInstalled('invoicing')) {
        try {
          $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';

          if(in_array($invoice_objects_table, DB::listTables(TABLE_PREFIX))) {
            DB::execute("ALTER TABLE $invoice_objects_table CHANGE name name VARCHAR(255) NULL DEFAULT NULL;");
          } // if
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // increaseQuoteNameLength

  }
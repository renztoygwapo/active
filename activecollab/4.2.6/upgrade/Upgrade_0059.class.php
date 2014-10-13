<?php

  /**
   * Update activeCollab 3.2.10 to activeCollab 3.2.11
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0059 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.2.10';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.11';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'insertRawAdditionalPropertiesRepositories' => 'Add additional properties to repositories model',
      );
    } // getActions

    /**
     * Insert Raw Additional Properties to source_repositories table
     *
     * @return bool|string
     */
    function insertRawAdditionalPropertiesRepositories() {
      if ($this->isModuleInstalled('source')) {
        $source_repositories_table = TABLE_PREFIX . 'source_repositories';
        try {
          $raw_additional_properties_column = DB::execute("SHOW COLUMNS FROM $source_repositories_table WHERE Field = 'raw_additional_properties'");
          if (!$raw_additional_properties_column) {
            DB::execute("ALTER TABLE $source_repositories_table ADD raw_additional_properties longtext null default null after graph");
          } //if
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } //if

      return true;
    } // insertRawAdditionalPropertiesRepositories

  }
<?php

  /**
   * Update activeCollab 3.2.1 to activeCollab 3.2.3
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0051 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.2.1';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.3';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateTaxRateTable' => 'Upgrade tax rate table',
        'upgradeSourceUsers' => 'Upgrade source users table',
        'removeDeprecatedFonts' => 'Update invoice designer settings',
      );
    } // getActions

    /**
     * Upgrade tax rate table
     *
     * @return bool|string
     */
    function updateTaxRateTable() {
      try {
        $tax_rates_table = TABLE_PREFIX . 'tax_rates';

        if(in_array($tax_rates_table, DB::listTables($tax_rates_table)) && !in_array('is_default', $this->listTableFields($tax_rates_table))) {
          DB::execute("ALTER TABLE $tax_rates_table ADD is_default TINYINT(1) NULL DEFAULT NULL AFTER percentage");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // if
      return true;
    } // updateTaxRateTable

    /**
     * Update source users table
     *
     * @return bool|string
     */
    function upgradeSourceUsers() {
      try {
        if(AngieApplication::isModuleLoaded('source')) {
          $source_users_table = TABLE_PREFIX . 'source_users';

          DB::execute("ALTER TABLE $source_users_table ADD id INT UNSIGNED NOT NULL FIRST");

          $rows = DB::execute("SELECT repository_id, repository_user FROM $source_users_table");

          if($rows) {
            $counter = 1;

            foreach($rows as $row) {
              DB::execute("UPDATE $source_users_table SET id = ? WHERE repository_id =? AND repository_user = ?", $counter++, $row['repository_id'], $row['repository_user']);
            } // foreach
          } // if

          DB::execute("ALTER TABLE $source_users_table DROP PRIMARY KEY");
          DB::execute("ALTER TABLE $source_users_table ADD PRIMARY KEY (id)");
          DB::execute("ALTER TABLE $source_users_table CHANGE id id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
          DB::execute("ALTER TABLE $source_users_table ADD UNIQUE INDEX repository_user (repository_id, repository_user)");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // upgradeSourceUsers

    /**
     * Remove deprecated Fonts
     *
     * @return boolean
     */
    function removeDeprecatedFonts() {
      try {
        $config_option_name = 'invoice_template'; // config option name

        $raw_value = DB::executeFirstCell('SELECT value FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', $config_option_name);

        if($raw_value) {
          $invoice_template = unserialize($raw_value);

          if(is_array($invoice_template)) {
            $affected_properties = array(
              'header_font',
              'client_details_font',
              'invoice_details_font',
              'items_font',
              'note_font',
              'footer_font'
            );

            $replacements = array(
              'freesans' => 'dejavusans',
              'freesansb' => 'dejavusansb',
              'freesansbi' => 'dejavusansbi',
              'freesansi' => 'dejavusansi',
              'freeserif' => 'dejavuserif',
              'freeserifb' => 'dejavuserifb',
              'freeserifbi' => 'dejavuserifbi',
              'freeserifi' => 'dejavuserifi'
            );

            foreach($affected_properties as $affected_property) {
              $affected_font = isset($invoice_template[$affected_property]) ? $invoice_template[$affected_property] : null;

              if($affected_font && array_key_exists($affected_font, $replacements)) {
                $invoice_template[$affected_property] = $replacements[$affected_font];
              } // if
            } // foreach

            // update config option
            DB::execute('UPDATE ' . TABLE_PREFIX . 'config_options SET value = ? WHERE name = ?', serialize($invoice_template), $config_option_name);
          } // if
        } // if
      } catch (Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // removeDeprecatedFonts

  }
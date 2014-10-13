<?php

  /**
   * Update activeCollab 3.1.5 to activeCollab 3.1.6
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0038 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.5';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.7';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'fixSourceBody' => 'Fixes bug that did not allow repositories from AC2 to edit/delete',
        'updateDaysOffNameIndex' => 'Update days off name index',
      );
    } // getActions

    /**
     * Fix bug that did not allow repositories from AC2 to edit/delete, and also add new config option for trust server certificate option
     *
     * @return bool|string
     */
    function fixSourceBody() {
      try {
        if($this->isModuleInstalled('source')) {
          $rows = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE module = 'source' AND body IS NULL AND text_field_1 IS NOT NULL");
          foreach ($rows as $row) {
            DB::execute("UPDATE " . TABLE_PREFIX . "project_objects SET body = ? WHERE id = ?", $row['text_field_1'], $row['id']);
          } //foreach

          if(DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', 'source_svn_trust_server_cert') < 1) {
            DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES ('source_svn_trust_server_cert', 'source', ?)", serialize(false));
          } // if
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // fixSourceBody

    /**
     * Update days off name index
     *
     * @return boolean
     */
    function updateDaysOffNameIndex() {
      try {
        $day_offs_table = TABLE_PREFIX . 'day_offs';
        $indexes = $this->listTableIndexes($day_offs_table);

        if(in_array('name', $indexes)) {
          DB::execute("ALTER TABLE $day_offs_table DROP INDEX name");
        } // if

        if(in_array('day_off_name', $indexes)) {
          DB::execute("ALTER TABLE $day_offs_table DROP INDEX day_off_name");
        } // if

        DB::execute("ALTER TABLE $day_offs_table ADD UNIQUE INDEX day_off_name (name, event_date)");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateDaysOffNameIndex

  }
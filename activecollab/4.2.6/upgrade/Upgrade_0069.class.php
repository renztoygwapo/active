<?php

  /**
   * Update activeCollab 3.3.5 to activeCollab 3.3.6
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0069 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.5';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.6';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'fixProjectBasedOnQuote' => 'Fixing Projects based on Quote',
      );
    } // getActions

    /**
     * Fixing Projects based on Quotes
     *
     * @return bool|string
     */
    function fixProjectBasedOnQuote() {

      $project_table = TABLE_PREFIX . 'projects';

      if($this->isModuleInstalled('invoicing') && !in_array('migrated', DB::listTableFields($project_table))) {
        try {
          $old_quotes_table = TABLE_PREFIX . 'backup_quotes';
          $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
          if(DB::tableExists($old_quotes_table)) {
            DB::execute("ALTER TABLE $project_table ADD already_migrated tinyint(1) DEFAULT 0 AFTER id"); // add migrated column for projects table
            $old_quotes = DB::execute("SELECT id, name,created_on FROM $old_quotes_table");
            if(is_foreachable($old_quotes)) {
              foreach($old_quotes as $old_quote) {
                //update project based on quotes
                $quote_id = DB::executeFirstCell("SELECT id FROM $invoice_objects_table WHERE type= ? AND name= ? and created_on= ?", 'Quote', $old_quote['name'], $old_quote['created_on']);
                if($quote_id) {
                  DB::execute("UPDATE $project_table SET already_migrated = ?, based_on_id = ? WHERE based_on_id = ? AND based_on_type = ? AND already_migrated < ?", 1, $quote_id, $old_quote['id'], 'Quote', 1);
                }//if
              }//foreach
            }//if
            DB::execute("ALTER TABLE $project_table DROP already_migrated");
          }//if

        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } //if

      if(in_array('migrated', DB::listTableFields($project_table))) {
        DB::execute("ALTER TABLE $project_table DROP migrated");
      } ///if

      return true;
    } // fixProjectBasedOnQuote

  } //Upgrade_0069
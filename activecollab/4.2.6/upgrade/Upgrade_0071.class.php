<?php

  /**
   * Update activeCollab 3.3.7 to activeCollab 3.3.8
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0071 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.7';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.8';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'migrateQuoteComments' => 'Migrate Quote Comments',
      );
    } // getActions

    /**
     * Migrate Quote Comments
     *
     * @return boolean
     */
    function migrateQuoteComments() {
      if($this->isModuleInstalled('invoicing')) {
        try {
          // get the table names
          $old_quotes_table = TABLE_PREFIX . 'backup_quotes';
          $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
          $config_table = TABLE_PREFIX . "config_options";
          $comments_table = TABLE_PREFIX . "comments";

          // if we previously updated to version that has done migration correctly
          $quote_comments_migrated = DB::executeFirstCell("SELECT value FROM $config_table WHERE name='quote_comments_migrated'");
          if($quote_comments_migrated) {
            DB::execute("DELETE FROM $config_table WHERE name='quote_comments_migrated'");
            return true;
          } // if

          // check if backup table exists
          if (!DB::tableExists($old_quotes_table)) {
            return true;
          } // if

          $old_quotes = DB::execute("SELECT id, name, created_on FROM $old_quotes_table");
          if (!is_foreachable($old_quotes)) {
            return true;
          } // if

          // add temporary field for migration purposes
          DB::execute("ALTER TABLE $comments_table ADD migrated tinyint(1) DEFAULT 0 AFTER id");

          foreach ($old_quotes as $old_quote) {
            $new_quote_id = DB::executeFirstCell("SELECT id FROM $invoice_objects_table WHERE type=? AND name=? and created_on=?", 'Quote', $old_quote['name'], $old_quote['created_on']);
            if ($new_quote_id) {
              DB::execute("UPDATE $comments_table SET migrated = ?, parent_id = ? WHERE parent_id = ? AND parent_type = ? AND migrated < ?", 1, $new_quote_id, $old_quote['id'], 'Quote', 1);
            } // if
          } // foreach

          // drop migrated field
          DB::execute("ALTER TABLE $comments_table DROP migrated");
        } catch (Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // migrateQuoteComments

  }
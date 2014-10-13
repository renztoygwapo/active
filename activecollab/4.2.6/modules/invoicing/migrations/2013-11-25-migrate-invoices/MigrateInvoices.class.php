<?php

  /**
   * Migrate invoices - add hash field
   *
   * Class MigrateInvoices
   *
   * @package modules.invoicing
   * @sub-package migrations
   */
  class MigrateInvoices extends AngieModelMigration {

    function up() {
      $invoice_object_table = $this->useTableForAlter('invoice_objects');
      $invoice_object_table->addColumn(DBStringColumn::create('hash', 50));
      $this->doneUsingTables('invoice_objects');

      $invoices = $this->execute('SELECT id FROM ' .  TABLE_PREFIX . 'invoice_objects WHERE type IN (?)', array('Invoice', 'Quote'));
      if(is_foreachable($invoices)) {
        foreach($invoices as $invoice) {
          do {
            $string = microtime();
            $hash = substr(sha1($string), 0, 20);
          } while ($this->executeFirstCell('SELECT id FROM ' .  TABLE_PREFIX . 'invoice_objects WHERE hash = ?', $hash) != null);
          $this->execute('UPDATE ' .  TABLE_PREFIX . 'invoice_objects SET hash = ? WHERE id = ?', $hash, $invoice['id']);
        } //foreach
      } //if
    } //up

    /**
     * Migrate down
     */
    function down() {
      $invoice_object_table = $this->useTableForAlter('invoice_objects');
      $invoice_object_table->dropColumn('hash');
      $this->doneUsingTables('invoice_objects');
    } // down

  } //MigrateInvoices
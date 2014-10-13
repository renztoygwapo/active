<?php

  /**
   * Update activeCollab 3.3.11 to activeCollab 3.3.12
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0075 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.11';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.12';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateInvoiceNoteTemplatesTable' => 'Update invoice note templates table',
      );
    } // getActions

    /**
     * Update invoice note templates table field default values
     *
     * @return bool|string
     */
    function updateInvoiceNoteTemplatesTable() {
      if($this->isModuleInstalled('invoicing')) {
        try {
          $invoice_note_templates_table = TABLE_PREFIX . 'invoice_note_templates';

          if(in_array($invoice_note_templates_table, DB::listTables(TABLE_PREFIX))) {
            DB::execute("ALTER TABLE $invoice_note_templates_table CHANGE position position int(10) unsigned NOT NULL DEFAULT 0");
          } // if
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // updateInvoiceNoteTemplatesTable

  }
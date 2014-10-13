<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Invoicing module model definition
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
	class InvoicingModuleModel extends ActiveCollabModuleModel {
    
    /**
     * Construct invoicing module model definition
     *
     * @param InvoicingModule $parent
     */
		function __construct(InvoicingModule $parent) {
      parent::__construct($parent);

      $this->addModel(DB::createTable('invoice_objects')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create('InvoiceObject'),
        DBIntegerColumn::create('company_id', 5, 0)->setUnsigned(true),
        DBStringColumn::create('company_name', 150),
        DBTextColumn::create('company_address'),
        DBIntegerColumn::create('currency_id', 4, 0),
        DBIntegerColumn::create('language_id', 3, 0),
        DBIntegerColumn::create('project_id', 5)->setUnsigned(true),
        DBNameColumn::create(255),
        DBMoneyColumn::create('subtotal', 0),
        DBMoneyColumn::create('tax', 0),
        DBMoneyColumn::create('total', 0),
        DBMoneyColumn::create('balance_due', 0),
        DBMoneyColumn::create('paid_amount', 0),
        DBTextColumn::create('note'),
        DBStringColumn::create('private_note', 255),
        DBIntegerColumn::create('status', 4, '0'),
        DBStringColumn::create('based_on_type', 50),
        DBIntegerColumn::create('based_on_id', 10)->setUnsigned(true),
        DBIntegerColumn::create('allow_payments', 3)->setSize(DBColumn::TINY),
        DBBoolColumn::create('second_tax_is_enabled', false),
        DBBoolColumn::create('second_tax_is_compound', false),
        DBStateColumn::create(),
        DBVisibilityColumn::create(),
        DBActionOnByColumn::create('created', true),
        DBIntegerColumn::create('recipient_id', 10, '0')->setUnsigned(true),
        DBStringColumn::create('recipient_name', 100),
        DBStringColumn::create('recipient_email', 150),
        DBActionOnByColumn::create('sent', true),
        DBDateTimeColumn::create('reminder_sent_on'),
        DBActionOnByColumn::create('closed', true),
        DBStringColumn::create('varchar_field_1', 255),
        DBStringColumn::create('varchar_field_2', 255),
        DBStringColumn::create('varchar_field_3', 255),
        DBStringColumn::create('varchar_field_4', 255),
        DBIntegerColumn::create('integer_field_1', 11),
        DBIntegerColumn::create('integer_field_2', 11),
        DBIntegerColumn::create('integer_field_3', 11),
        DBDateColumn::create('date_field_1'),
        DBDateColumn::create('date_field_2'),
        DBDateColumn::create('date_field_3'),
        DBDateTimeColumn::create('datetime_field_1'),
        DBStringColumn::create('hash', 50),
      ))->addIndices(array(
        DBIndex::create('company_id'),
        DBIndex::create('project_id'),
        DBIndex::create('total'),
//        DBIndex::create('number'), ??
//        DBIndex::create('issued_on'), ??
//        DBIndex::create('due_on'), ??
      )))->setTypeFromField('type');

      $this->addModel(DB::createTable('invoice_object_items')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create('InvoiceObjectItem'),
        DBParentColumn::create(),
        DBIntegerColumn::create('first_tax_rate_id', 3, '0')->setUnsigned(true),
        DBIntegerColumn::create('second_tax_rate_id', 3, '0')->setUnsigned(true),
        DBTextColumn::create('description'),
        DBDecimalColumn::create('quantity', 13, 3, 1)->setUnsigned(true),
        DBMoneyColumn::create('unit_cost', 0),
        DBMoneyColumn::create('subtotal', 0),
        DBMoneyColumn::create('first_tax', 0),
        DBMoneyColumn::create('second_tax', 0),
        DBMoneyColumn::create('total', 0),
        DBBoolColumn::create('second_tax_is_enabled', false),
        DBBoolColumn::create('second_tax_is_compound', false),
        DBIntegerColumn::create('position', 11),
      ))->addIndices(array(
        DBIndex::create('parent_id', DBIndex::KEY, array('parent_id', 'parent_type', 'position')),
      )))->setTypeFromField('type');
      
      $this->addModel(DB::createTable('invoice_item_templates')->addColumns(array(
        DBIdColumn::create(), 
        DBIntegerColumn::create('first_tax_rate_id', 3, '0')->setUnsigned(true),
        DBIntegerColumn::create('second_tax_rate_id', 3, '0')->setUnsigned(true),
        DBTextColumn::create('description'),

        DBDEcimalColumn::create('quantity', 13, 3, 1)->setUnsigned(true),
        DBMoneyColumn::create('unit_cost', 0),
        DBIntegerColumn::create('position', 11, 0), 
      )))->setOrderBy('ISNULL(position) DESC, position');
      
      $this->addModel(DB::createTable('invoice_note_templates')->addColumns(array(
        DBIdColumn::create(), 
        DBNameColumn::create(150, true), 
        DBTextColumn::create('content'), 
        DBIntegerColumn::create('position', 10, 0)->setUnsigned(true),
        DBBoolColumn::create('is_default', false)
      )))->setOrderBy('ISNULL(position) DESC, position');
      
      $this->addTable(DB::createTable('invoice_related_records')->addColumns(array(
        DBIntegerColumn::create('invoice_id', 5)->setUnsigned(true), 
        DBIntegerColumn::create('item_id', 10)->setUnsigned(true),
        DBParentColumn::create(true), 
      ))->addIndices(array(
        DBIndexPrimary::create(array('invoice_id', 'item_id', 'parent_type', 'parent_id')),
      )));

      $this->addModel(DB::createTable('tax_rates')->addColumns(array(
        DBIdColumn::create(),
        DBNameColumn::create(50, true),
        DBDecimalColumn::create('percentage', 6, 3),
        DBBoolColumn::create('is_default', false)
      )))->setOrderBy('name');
    } // __construct
    
    /**
     * Load initial module data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('prefered_currency');

      $this->addConfigOption('on_invoice_based_on', 'keep_records_as_separate_invoice_items');
      $this->addConfigOption('description_format_grouped_by_task');
      $this->addConfigOption('description_format_grouped_by_project');
      $this->addConfigOption('description_format_grouped_by_job_type');
      $this->addConfigOption('description_format_separate_items');
      $this->addConfigOption('first_record_summary_transformation', 'prefix_with_colon');
      $this->addConfigOption('second_record_summary_transformation');

      $this->addConfigOption('invoicing_number_pattern', ':invoice_in_year/:current_year');
      $this->addConfigOption('invoicing_number_date_counters');
      $this->addConfigOption('invoicing_number_counter_padding');

      $this->addConfigOption('invoice_template');

      $this->addConfigOption('print_invoices_as');
      $this->addConfigOption('print_proforma_invoices_as');

      $this->addConfigOption('invoicing_default_due', 15);

      $this->addConfigOption('invoice_second_tax_is_enabled', false);
      $this->addConfigOption('invoice_second_tax_is_compound', false);

      $this->addConfigOption('invoice_notify_on_payment', 1);
      $this->addConfigOption('invoice_notify_on_cancel', 1);
      $this->addConfigOption('invoice_notify_financial_managers', 2);
      $this->addConfigOption('invoice_notify_financial_manager_ids', 0);

      // Invoice Overdue Reminders
      $this->addConfigOption('invoice_overdue_reminders_enabled', false);
      $this->addConfigOption('invoice_overdue_reminders_send_first', 7);
      $this->addConfigOption('invoice_overdue_reminders_send_every', 7);
      $this->addConfigOption('invoice_overdue_reminders_first_message', 'We would like to remind you that the following invoice has been overdue. Please send your payment promptly. Thank you.');
      $this->addConfigOption('invoice_overdue_reminders_escalation_enabled', false);
      $this->addConfigOption('invoice_overdue_reminders_escalation_messages', array(
        array('send_escalated' => 14, 'escalated_message' => null)
      ));
      $this->addConfigOption('invoice_overdue_reminders_dont_send_to');

      // Job types
      $job_types_table = TABLE_PREFIX . 'job_types';
      
      if(DB::tableExists($job_types_table) && DB::executeFirstCell("SELECT COUNT(id) FROM $job_types_table") == 0) {
        $this->loadTableData('job_types', array(
          array(
            'name' => 'General',
            'default_hourly_rate' => 100,
            'is_default' => true,
            'is_active' => true,
          )
        ));
      } // if
      
      // Tax rates
      $tax_rates_table = TABLE_PREFIX . 'tax_rates';
      
      if(DB::tableExists($tax_rates_table) && DB::executeFirstCell("SELECT COUNT(id) FROM $tax_rates_table WHERE name='VAT'") == 0) {
        $this->loadTableData('tax_rates', array(
          array(
            'name' => 'VAT', 
            'percentage' => 17.5, 
          )
        ));
      }//if
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }
<?php

/**
 * Update activeCollab 3.2.14 to activeCollab 3.3.0
 *
 * @package activeCollab.upgrade
 * @subpackage scripts
 */
class Upgrade_0063 extends AngieApplicationUpgradeScript {

  /**
   * Initial system version
   *
   * @var string
   */
  protected $from_version = '3.2.14';

  /**
   * Final system version
   *
   * @var string
   */
  protected $to_version = '3.3.0';

  /**
   * Return script actions
   *
   * @return array
   */
  function getActions() {
    $result = array(
      'setupAutoUpdate' => 'Setting up auto update',
      'addDiskSpaceConfigOptions' => 'Add config options needed by Disk Space tool',
      'removePermanentlyDeletedFiles' => 'Remove permanently deleted files',
      'initDataFilters' => 'Initialize storage for filters and reports',
      'moveAssignmentFilters' => 'Move existing assignment filters the new storage',
      'moveTrackingReports' => 'Move existing tracking reports to the new storage',
      'updatePaymentMethodOptions' => 'Upgrade payment method option',
      'removeVisualEditorOption' => 'Remove visual editor configuration option',
      'updateNumberFormatting' => 'Upgrade number formatting',
      'addWorkweekConfigOptions' => 'Add config options required by Workweek settings',
      'upgradeDecimalColumns' => 'Upgrade decimal fields',
      'updateUsersTable' => 'Updating users table',
      'updateSearchOptions' => 'Update search index options',
    );

    if($this->isModuleInstalled('invoicing')) {
      $result = array_merge($result, array(
        'upgradeInvoicing' => 'Upgrade invoicing',
        'backupInvoicingTables' => 'Backing up existing invoicing tables',
        'createInvoicingTables' => 'Creating new invoicing tables',
        'migrateInvoices' => 'Migrating Invoices',
        'migrateQuotes' => 'Migrating Quotes',
        'migrateRecurringProfiles' => 'Migrating Recurring Profiles',
        'recalculatingPaymentTotals' => 'Recalculating Payment Totals',
        'updateInvoiceObjectsTable' => 'Updating Invoice Objects table',
        'addInvoiceOverdueRemindersConfigOptions' => 'Add Invoice overdue reminders config options',
      ));
    } // if

    return $result;
  } // getActions

  /**
   * Setup auto update
   *
   * @return bool|string
   */
  function setupAutoUpdate() {
    try {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('new_modules_available', 'system', 'b:0;')");
      DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES ('update_archive_url', 'system', 'N;')");
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('update_download_progress', 'system', 'i:0;')");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // setupAutoUpdate

  /**
   * Upgrade source branching
   *
   * @return bool|string
   */
  function updateSourceBranching() {
    if ($this->isModuleInstalled('source')) {
      try {
        $source_commits_table = TABLE_PREFIX . 'source_commits';
        $commit_project_objects_table = TABLE_PREFIX . 'commit_project_objects';
        $source_repositories_table = TABLE_PREFIX . 'source_repositories';

        DB::execute("ALTER TABLE $source_commits_table ADD branch_name VARCHAR(255) DEFAULT '' AFTER commited_by_email");
        DB::execute("ALTER TABLE $commit_project_objects_table ADD branch_name VARCHAR(255) DEFAULT '' AFTER revision");

        // Add default branch for existing data

        DB::execute("UPDATE $source_commits_table SET branch_name = 'master' WHERE type = 'GitCommit'");
        DB::execute("UPDATE $source_commits_table SET branch_name = 'default' WHERE type = 'MercurialCommit'");

        $commit_project_objects = DB::execute("
            SELECT $commit_project_objects_table.id, $source_repositories_table.type
            FROM $commit_project_objects_table, $source_repositories_table
            WHERE $commit_project_objects_table.repository_id = $source_repositories_table.id"
        );

        if (is_foreachable($commit_project_objects)) {
          foreach ($commit_project_objects as $commit_project_object) {
            if ($commit_project_object['type'] == 'GitRepository') {
              DB::execute("UPDATE $commit_project_objects_table SET branch_name = 'master' WHERE id = ?", $commit_project_object['id']);
            } //if
            if ($commit_project_object['type'] == 'MercurialRepository') {
              DB::execute("UPDATE $commit_project_objects_table SET branch_name = 'default' WHERE id = ?", $commit_project_object['id']);
            } //if
          } //foreach
        } //if

        DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('default_source_branch', 'source', 'N;')");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
    } //if
    return true;
  } // updateSourceBranching

  /**
   * Initialize related tasks table
   *
   * @return bool|string
   */
  function addDiskSpaceConfigOptions() {
    try {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES
          ('disk_space_limit', 'system', 'i:0;'),
          ('disk_space_email_notifications', 'system', 'b:1;'),
          ('disk_space_low_space_threshold', 'system', 's:2:\"90\";')");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // addDiskSpaceConfigOptions

  /**
   * Remove permanently deleted files
   *
   * @return bool|string
   */
  function removePermanentlyDeletedFiles() {
    try {
      DB::beginWork('Start removing permanently deleted files');
      $files_to_delete = array();

      defined('STATE_DELETED') or define('STATE_DELETED', 0); // Make sure that we have STATE_DELETED constant defined

      if ($this->isModuleInstalled('files')) {
        // find file ids which are deleted
        $file_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND state = ?', 'File', STATE_DELETED);
        // add their locations to cumulative list
        $files_to_delete = array_merge($files_to_delete, (array) DB::executeFirstColumn('SELECT varchar_field_2 FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND id IN (?)', 'File', $file_ids));
        // add file versions locations to the list
        $files_to_delete = array_merge($files_to_delete, (array) DB::executeFirstColumn('SELECT location FROM ' . TABLE_PREFIX . 'file_versions WHERE file_id IN (?)', $file_ids));
        // delete file versions
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'file_versions WHERE file_id IN (?)', $file_ids);
      } // if

      // add attachment locations to the list
      $files_to_delete = array_merge($files_to_delete, (array) DB::executeFirstColumn('SELECT location FROM ' . TABLE_PREFIX . 'attachments WHERE state = ?', STATE_DELETED));

      // delete all files related to previously deleted objects
      if (is_foreachable($files_to_delete)) {
        foreach ($files_to_delete as $file_to_delete) {
          $full_path_to_delete = UPLOAD_PATH . '/' . $file_to_delete;
          if (is_file($full_path_to_delete)) {
            @unlink($full_path_to_delete);
          } // if
        } // foreach
      } // if

      DB::commit('Successfully removed permanently deleted files');
    } catch (Exception $e) {
      DB::rollback('Failed to remove permanently deleted files');
      return $e->getMessage();
    } // if

    return true;
  } // removePermanentlyDeletedFiles

  /**
   * Initialize storage for data filters
   *
   * @return bool|string
   */
  function initDataFilters() {
    try {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "data_filters (
          id int unsigned NOT NULL auto_increment,
          type varchar(150) NOT NULL DEFAULT 'DataFilter',
          name varchar(50)  DEFAULT NULL,
          raw_additional_properties longtext ,
          created_on datetime  DEFAULT NULL,
          created_by_id int(10) unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          is_private tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX created_by_id (created_by_id),
          INDEX name (name)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // initDataFilters

  /**
   * Move existing filters and reports to the new storage
   *
   * @return bool|string
   */
  function moveAssignmentFilters() {
    try {
      $assignment_filters_table = TABLE_PREFIX . 'assignment_filters';
      $data_filters_table = TABLE_PREFIX . 'data_filters';
      $homescreen_tabs_table = TABLE_PREFIX . 'homescreen_tabs';

      DB::beginWork('Move assignment filters to the new storage @ ' . __CLASS__);

      $assignment_filters = DB::execute("SELECT * FROM $assignment_filters_table");
      if($assignment_filters) {
        $assignment_filter_tabs_map = $this->getTabsThatUseAssignmentFilters();

        foreach($assignment_filters as $filter) {
          $old_assignment_filter_id = (integer) $filter['id'];

          DB::execute("INSERT INTO $data_filters_table (type, name, raw_additional_properties, created_on, created_by_id, created_by_name, created_by_email, is_private) VALUES ('AssignmentFilter', ?, ?, ?, ?, ?, ?, ?)", $filter['name'], $filter['raw_additional_properties'], $filter['created_on'], $filter['created_by_id'], $filter['created_by_name'], $filter['created_by_email'], $filter['is_private']);

          $new_assignmnet_filter_id = DB::lastInsertId();

          if(isset($assignment_filter_tabs_map[$old_assignment_filter_id]) && count($assignment_filter_tabs_map[$old_assignment_filter_id])) {
            DB::execute("UPDATE $homescreen_tabs_table SET raw_additional_properties = ? WHERE id IN (?)", serialize(array('assignment_filter_id' => $new_assignmnet_filter_id)), $assignment_filter_tabs_map[$old_assignment_filter_id]);
          } // if
        } // foreach
      } // if

      DB::commit('Assignment filters moved to the new storage @ ' . __CLASS__);
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    try {
      $table_list = DB::listTables(TABLE_PREFIX);

      if(in_array($assignment_filters_table, $table_list)) {
        DB::execute("DROP TABLE $assignment_filters_table");
      } // if

      if(in_array(TABLE_PREFIX . 'milestone_filters', $table_list)) {
        DB::execute('DROP TABLE ' . TABLE_PREFIX . 'milestone_filters');
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // moveAssignmentFilters

  /**
   * Return list of filter ID-s (keys) and tabs that use them (value for a given key)
   *
   * @return array
   */
  private function getTabsThatUseAssignmentFilters() {
    $result = array();

    $assignment_filter_tabs = DB::execute("SELECT id, raw_additional_properties FROM " . TABLE_PREFIX . "homescreen_tabs WHERE type = 'AssignmentFiltersHomescreenTab'");
    if($assignment_filter_tabs) {
      foreach($assignment_filter_tabs as $assignment_filter_tab) {
        $tab_properties = $assignment_filter_tab['raw_additional_properties'] ? unserialize($assignment_filter_tab['raw_additional_properties']) : null;

        $tab_filter_id = $tab_properties && isset($tab_properties['assignment_filter_id']) && $tab_properties['assignment_filter_id'] ? (integer) $tab_properties['assignment_filter_id'] : 0;

        if($tab_filter_id) {
          if(array_key_exists($tab_filter_id, $result)) {
            $result[$tab_filter_id][] = (integer) $assignment_filter_tab['id'];
          } else {
            $result[$tab_filter_id] = array((integer) $assignment_filter_tab['id']);
          } // if
        } // if
      } // foreach
    } // if

    return $result;
  } // getTabsThatUseAssignmentFilters

  /**
   * Move tracking reports to the new storage
   *
   * @return bool
   */
  function moveTrackingReports() {
    if ($this->isModuleInstalled('tracking')) {
      try {
        $tracking_reports_table = TABLE_PREFIX . 'tracking_reports';
        $data_filters_table = TABLE_PREFIX . 'data_filters';

        DB::beginWork('Moving tracking reports to the new storage @ ' . __CLASS__);

        $tracking_reports = DB::execute("SELECT * FROM $tracking_reports_table");
        if($tracking_reports) {
          list($admin_user_id, $admin_display_name, $admin_email_address) = $this->getFirstAdministrator();

          foreach($tracking_reports as $report) {
            DB::execute("INSERT INTO $data_filters_table (type, name, raw_additional_properties, created_on, created_by_id, created_by_name, created_by_email, is_private) VALUES ('TrackingReport', ?, ?, NOW(), ?, ?, ?, '0')", $report['name'], $report['raw_additional_properties'], $admin_user_id, $admin_display_name, $admin_email_address);
          } // foreach
        } // if

        DB::commit('Tracking reports moved to the new storage @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to move tracking reports to the new storage @ ' . __CLASS__);
        return $e->getMessage();
      } // try

      try {
        DB::execute("DROP TABLE $tracking_reports_table");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
    } //if

    return true;
  } // moveTrackingReports

  /**
   * Upgrade payment method
   *
   * @return bool|string
   */
  function updatePaymentMethodOptions() {
    try {
      $payments_table = TABLE_PREFIX . 'payments';

      if (!in_array('method', $this->listTableFields($payments_table))) {
        DB::execute("ALTER TABLE $payments_table ADD method VARCHAR(100) DEFAULT '' AFTER comment");
      } // if

      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('payment_methods_common', 'payments', ?)", serialize(array('Bank Deposit','Check','Cash','Credit','Debit')));
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('payment_methods_credit_card', 'payments', ?)", serialize(array('Credit Card','Credit Card (Visa)','Credit Card (Mastercard)','Credit Card (Discover)','Credit Card (American Express)','Credit Card (Diners)')));
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('payment_methods_online', 'payments', ?)", serialize(array('Online Payment', 'Online Payment (PayPal)', 'Online Payment (Authorize)')));

    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updatePaymentMethodOptions

  /**
   * Remove visual editor option values
   *
   * @return bool|string
   */
  function removeVisualEditorOption() {
    try {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . "config_option_values WHERE name = 'visual_editor'");
      DB::execute('DELETE FROM ' . TABLE_PREFIX . "config_options WHERE name = 'visual_editor'");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // removeVisualEditorOption

  /**
   * Upgrade currencies
   *
   * @return bool|string
   */
  function updateNumberFormatting() {
    try {
      $currencies_table = TABLE_PREFIX  . 'currencies';
      $languages_table = TABLE_PREFIX  . 'languages';
      $table_list = DB::listTables(TABLE_PREFIX);

      // check if currencies table exists
      if(in_array($currencies_table, $table_list)) {
        $currencies_table_fields = $this->listTableFields($currencies_table); // list fields in currencies table

        if(!in_array('decimal_spaces', $currencies_table_fields)) {
          DB::execute("ALTER TABLE $currencies_table ADD decimal_spaces TINYINT(1) UNSIGNED NULL DEFAULT ? AFTER code", 2); // add decimal spaces field
        } // if

        if(!in_array('decimal_rounding', $currencies_table_fields)) {
          DB::execute("ALTER TABLE $currencies_table ADD decimal_rounding DECIMAL(4, 3) UNSIGNED NOT NULL DEFAULT 0 AFTER decimal_spaces"); // add decimal rouding field
        } // if
      } // if

      if(in_array($languages_table, $table_list)) {
        // list fields in languages table
        $language_table_fields = $this->listTableFields($languages_table);

        // add decimal separator field
        if (!in_array('decimal_separator', $language_table_fields)) {
          DB::execute("ALTER TABLE $languages_table ADD decimal_separator VARCHAR(1) NULL DEFAULT ? AFTER locale", '.');
        } // if

        // add thousands separator field
        if (!in_array('thousands_separator', $language_table_fields)) {
          DB::execute("ALTER TABLE $languages_table ADD thousands_separator VARCHAR(1) NULL DEFAULT ? AFTER decimal_separator", ',');
        } // if
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // if
    return true;
  } // updateNumberFormatting

  /**
   * Upgrade decimal columns
   *
   * @return bool|string
   */
  function upgradeDecimalColumns() {
    try {
      $installed_tables = DB::listTables(TABLE_PREFIX);
      if (!is_foreachable($installed_tables)) {
        return true;
      } // if

      if ($this->isModuleInstalled('invoicing')) {
        $quantity_fields_to_update = array(
          'invoice_items',
          'invoice_item_templates',
          'quote_items',
          'recurring_profile_items'
        );
        foreach ($quantity_fields_to_update as $table_with_quantity_field) {
          $current_table = TABLE_PREFIX . $table_with_quantity_field;
          if (in_array($current_table, $installed_tables)) {
            DB::execute('ALTER TABLE ' . $current_table . ' CHANGE quantity quantity DECIMAL(13,3) UNSIGNED NOT NULL DEFAULT "1.000"');
          } // if
        } // foreach
      } //if

      // set negative budget to 0 in projects table
      DB::execute("UPDATE " . TABLE_PREFIX . "projects SET budget = 0.000 WHERE budget < 0");


      $update_money_fields = array(
        array('table' => 'payments', 'field' => 'amount'),
        array('table' => 'projects', 'field' => 'budget', 'unsigned' => true),
      );

      if ($this->isModuleInstalled('invoicing')) {
        $update_money_fields[] = array('table' => 'invoice_items', 'field' => 'unit_cost');
        $update_money_fields[] = array('table' => 'invoice_item_templates', 'field' => 'unit_cost');
        $update_money_fields[] = array('table' => 'quote_items', 'field' => 'unit_cost');
        $update_money_fields[] = array('table' => 'recurring_profile_items', 'field' => 'unit_cost');
      } //if

      if ($this->isModuleInstalled('tracking')) {
        $update_money_fields[] = array('table' => 'expenses', 'field' => 'value', 'unsigned' => true);
        $update_money_fields[] = array('table' => 'project_hourly_rates', 'field' => 'hourly_rate', 'unsigned' => true);
        $update_money_fields[] = array('table' => 'job_types', 'field' => 'default_hourly_rate');
      } //if

      foreach ($update_money_fields as $money_field) {
        $current_table = TABLE_PREFIX . array_var($money_field, 'table', true);
        $current_field = array_var($money_field, 'field', true);
        $is_unsigned = array_var($money_field, 'unsigned', false);
        DB::execute("UPDATE $current_table SET $current_field = 0.000 WHERE $current_field IS NULL");
        if (in_array($current_table, $installed_tables)) {
          if ($is_unsigned) {
            DB::execute('ALTER TABLE ' . $current_table . ' CHANGE ' . $current_field . ' ' . $current_field . ' DECIMAL(13,3) UNSIGNED NOT NULL DEFAULT "0.000"');
          } else {
            DB::execute('ALTER TABLE ' . $current_table . ' CHANGE ' . $current_field .  ' ' . $current_field . ' DECIMAL(13,3) NOT NULL DEFAULT "0.000"');
          } // if
        } // if
      } // foreach

    } catch(Exception $e) {
      return $e->getMessage();
    } // if
    return true;
  } // upgradeDecimalColumns

  /**
   * Add workweek settings config options
   *
   * @return bool|string
   */
  function addWorkweekConfigOptions() {
    try {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('effective_work_hours', 'globalization', 'i:40;')");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // addWorkweekConfigOptions

  /**
   * Backup invoicing tables
   *
   * @return bool|string
   */
  function backupInvoicingTables() {
    try {
      // rename invoices table
      DB::execute('RENAME TABLE ' . TABLE_PREFIX . 'invoices TO ' . TABLE_PREFIX . 'backup_invoices');
      // rename invoice_items table
      DB::execute('RENAME TABLE ' . TABLE_PREFIX . 'invoice_items TO ' . TABLE_PREFIX . 'backup_invoice_items');
      // rename recurring_profiles table
      DB::execute('RENAME TABLE ' . TABLE_PREFIX . 'recurring_profiles TO ' . TABLE_PREFIX . 'backup_recurring_profiles');
      // rename recurring_profile_items table
      DB::execute('RENAME TABLE ' . TABLE_PREFIX . 'recurring_profile_items TO ' . TABLE_PREFIX . 'backup_recurring_profile_items');
      // rename quotes table
      DB::execute('RENAME TABLE ' . TABLE_PREFIX . 'quotes TO ' . TABLE_PREFIX . 'backup_quotes');
      // rename quotes table
      DB::execute('RENAME TABLE ' . TABLE_PREFIX . 'quote_items TO ' . TABLE_PREFIX . 'backup_quote_items');
      // clone payments table
      DB::execute('RENAME TABLE ' . TABLE_PREFIX . 'payments TO ' . TABLE_PREFIX . 'backup_payments');
      DB::execute('CREATE TABLE ' . TABLE_PREFIX . 'payments LIKE ' . TABLE_PREFIX . 'backup_payments');
      DB::execute('INSERT ' . TABLE_PREFIX . 'payments SELECT * FROM ' . TABLE_PREFIX . 'backup_payments');
      // clone invoice_related_records table
      DB::execute('RENAME TABLE ' . TABLE_PREFIX . 'invoice_related_records TO ' . TABLE_PREFIX . 'backup_invoice_related_records');
      DB::execute('CREATE TABLE ' . TABLE_PREFIX . 'invoice_related_records LIKE ' . TABLE_PREFIX . 'backup_invoice_related_records');
      DB::execute('INSERT ' . TABLE_PREFIX . 'invoice_related_records SELECT * FROM ' . TABLE_PREFIX . 'backup_invoice_related_records');
    } catch (Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // backupInvoicingTables

  /**
   * Upgrade invoicing module
   *
   * @return bool|string
   */
  function upgradeInvoicing() {
    try {
      $existing_tables = DB::listTables(TABLE_PREFIX);

      $invoice_item_templates_table = TABLE_PREFIX . 'invoice_item_templates';
      $invoice_note_templates_table = TABLE_PREFIX . 'invoice_note_templates';

      // update invoice item templates
      if (in_array($invoice_item_templates_table, $existing_tables)) {
        // rename tax_rate_id to first_tax_rate_id
        DB::execute("ALTER TABLE $invoice_item_templates_table CHANGE tax_rate_id first_tax_rate_id tinyint(3) unsigned NOT NULL DEFAULT '0'");
        // add second_tax_rate_id
        DB::execute("ALTER TABLE $invoice_item_templates_table ADD second_tax_rate_id tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER first_tax_rate_id");
      } // if

      // update invoice note templates table
      if (in_array($invoice_note_templates_table, $existing_tables) && !in_array('is_default', $this->listTableFields($invoice_note_templates_table))) {
        DB::execute("ALTER TABLE $invoice_note_templates_table ADD is_default TINYINT(1) NULL DEFAULT NULL AFTER content");
      } // if

      // added invoice_second_tax_is_enabled config option
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES (?, ?, ?)", 'invoice_second_tax_is_enabled', 'invoicing', 'b:0;');

      // added invoice_second_tax_is_compound config option
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES (?, ?, ?)", 'invoice_second_tax_is_compound', 'invoicing', 'b:0;');
    } catch(Exception $e) {
      return $e->getMessage();
    } // if
    return true;
  } // upgradeInvoicing

  /**
   * Generate Insert Query
   *
   * @param $table
   * @param $map
   * @param bool $skip_insert_query
   * @return string
   */
  function generateInsertQuery($table, $map, $skip_insert_query = false) {
    $columns = array_keys($map);
    $values = array_values($map);

    $query = '(' . implode(', ', $columns) . ') VALUES (' . implode(', ', array_fill(0, count($columns), '?')) . ')';
    if (!$skip_insert_query) {
      $query = 'INSERT INTO ' . $table . ' ' . $query;
    } // if

    return DB::getConnection()->prepare($query, $values);
  } // if

  /**
   * Create Invoicing Tables
   */
  function createInvoicingTables() {
    try {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

      $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
      $invoice_object_items_table = TABLE_PREFIX . 'invoice_object_items';

      // create invoice objects table
      DB::execute("CREATE TABLE $invoice_objects_table (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'InvoiceObject',
          company_id int(5) unsigned NOT NULL DEFAULT 0,
          company_name varchar(150)  DEFAULT NULL,
          company_address text ,
          currency_id int(4) NOT NULL DEFAULT 0,
          language_id int(3) NOT NULL DEFAULT 0,
          project_id int(5) unsigned NULL DEFAULT NULL,
          name varchar(150)  DEFAULT NULL,
          subtotal decimal(13, 3) NOT NULL DEFAULT 0,
          tax decimal(13, 3) NOT NULL DEFAULT 0,
          total decimal(13, 3) NOT NULL DEFAULT 0,
          balance_due decimal(13, 3) NOT NULL DEFAULT 0,
          paid_amount decimal(13, 3) NOT NULL DEFAULT 0,
          note text ,
          private_note varchar(255)  DEFAULT NULL,
          status int(4) NOT NULL DEFAULT 0,
          based_on_type varchar(50)  DEFAULT NULL,
          based_on_id int(10) unsigned NULL DEFAULT NULL,
          allow_payments tinyint(3) NULL DEFAULT NULL,
          second_tax_is_enabled tinyint(1) unsigned NOT NULL DEFAULT '0',
          second_tax_is_compound tinyint(1) unsigned NOT NULL DEFAULT '0',
          state tinyint(3) unsigned NOT NULL DEFAULT 0,
          original_state tinyint(3) unsigned NULL DEFAULT NULL,
          visibility tinyint(3) unsigned NOT NULL DEFAULT 0,
          original_visibility tinyint(3) unsigned NULL DEFAULT NULL,
          created_on datetime  DEFAULT NULL,
          created_by_id int unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          recipient_id int(10) unsigned NOT NULL DEFAULT 0,
          recipient_name varchar(100)  DEFAULT NULL,
          recipient_email varchar(150)  DEFAULT NULL,
          sent_on datetime  DEFAULT NULL,
          sent_by_id int unsigned NULL DEFAULT NULL,
          sent_by_name varchar(100)  DEFAULT NULL,
          sent_by_email varchar(150)  DEFAULT NULL,
          closed_on datetime  DEFAULT NULL,
          closed_by_id int unsigned NULL DEFAULT NULL,
          closed_by_name varchar(100)  DEFAULT NULL,
          closed_by_email varchar(150)  DEFAULT NULL,
          varchar_field_1 varchar(255)  DEFAULT NULL,
          varchar_field_2 varchar(255)  DEFAULT NULL,
          varchar_field_3 varchar(255)  DEFAULT NULL,
          varchar_field_4 varchar(255)  DEFAULT NULL,
          integer_field_1 int(11) NULL DEFAULT NULL,
          integer_field_2 int(11) NULL DEFAULT NULL,
          integer_field_3 int(11) NULL DEFAULT NULL,
          date_field_1 date  DEFAULT NULL,
          date_field_2 date  DEFAULT NULL,
          date_field_3 date  DEFAULT NULL,
          datetime_field_1 datetime  DEFAULT NULL,
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX created_on (created_on),
          INDEX sent_on (sent_on),
          INDEX closed_on (closed_on),
          INDEX company_id (company_id),
          INDEX project_id (project_id),
          INDEX total (total)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

      // create invoice object items table
      DB::execute("CREATE TABLE $invoice_object_items_table (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'InvoiceObjectItem',
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          first_tax_rate_id int(3) unsigned NOT NULL DEFAULT 0,
          second_tax_rate_id int(3) unsigned NOT NULL DEFAULT 0,
          description varchar(255)  DEFAULT NULL,
          quantity decimal(13, 3) unsigned NOT NULL DEFAULT 1,
          unit_cost decimal(13, 3) NOT NULL DEFAULT 0,
          subtotal decimal(13, 3) NOT NULL DEFAULT 0,
          first_tax decimal(13, 3) NOT NULL DEFAULT 0,
          second_tax decimal(13, 3) NOT NULL DEFAULT 0,
          total decimal(13, 3) NOT NULL DEFAULT 0,
          second_tax_is_enabled tinyint(1) unsigned NOT NULL DEFAULT '0',
          second_tax_is_compound tinyint(1) unsigned NOT NULL DEFAULT '0',
          position int(11) NULL DEFAULT NULL,
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX parent (parent_type, parent_id),
          INDEX parent_id (parent_id, parent_type, position)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
    } catch (Exception $e) {
      die($e->getMessage());
    } // try

    return true;
  } // createInvoicingTables

  /**
   * Cached invoice totals
   *
   * @var bool
   */
  private $invoice_totals = false;

  /**
   * taxes ID/rate map
   *
   * @var bool
   */
  private $taxes = false;


  /**
   * Recalculate Item
   *
   * @param $item
   * @param $parent_type
   * @param integer $parent_id
   * @return array
   */
  function recalculateItem($item, $parent_type, $parent_id) {
    $parent_type = strtolower($parent_type);

    if ($this->invoice_totals === false) {
      $this->invoice_totals = array();
    } // if

    if (!array_key_exists($parent_type, $this->invoice_totals)) {
      $this->invoice_totals[$parent_type] = array();
    } // if

    if (!array_key_exists($parent_id, $this->invoice_totals[$parent_type])) {
      $this->invoice_totals[$parent_type][$parent_id] = array(
        'subtotal'  => 0,
        'tax'       => 0,
        'total'     => 0
      );
    } // if

    if ($this->taxes === false) {
      $taxes = DB::execute("SELECT id, percentage FROM " . TABLE_PREFIX . "tax_rates");
      $this->taxes = array();
      if (is_foreachable($taxes)) {
        foreach ($taxes as $tax) {
          $this->taxes[$tax['id']] = $tax['percentage'] / 100;
        } // foreach
      } // if
    } // if

    // item subtotal
    $item_subtotal = array_var($item, 'quantity', 0) * array_var($item, 'unit_cost', 0);

    // item tax
    $item_tax = 0;
    $item_tax_rate_id = array_var($item, 'tax_rate_id', 0);
    if ($item_tax_rate_id && array_key_exists($item_tax_rate_id, $this->taxes)) {
      $item_tax = $item_subtotal * $this->taxes[$item_tax_rate_id];
    } // if

    // item total
    $item_total = $item_subtotal + $item_tax;

    // cache parent sum
    $this->invoice_totals[$parent_type][$parent_id]['subtotal'] += $item_subtotal;
    $this->invoice_totals[$parent_type][$parent_id]['tax'] += $item_tax;
    $this->invoice_totals[$parent_type][$parent_id]['total'] += $item_total;

    return array($item_subtotal, $item_tax, $item_total);
  } // recalculateItem

  /**
   * Migrate Invoices
   *
   * @return bool|string
   */
  function migrateInvoices() {
    try {
      // new tables
      $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
      $invoice_object_items_table = TABLE_PREFIX . 'invoice_object_items';

      // old tables
      $invoices_table = TABLE_PREFIX . 'backup_invoices';
      $invoice_items_table = TABLE_PREFIX . 'backup_invoice_items';
      $payments_table = TABLE_PREFIX . 'payments';
      $invoice_related_records_table = TABLE_PREFIX . 'invoice_related_records';

      // default language_id
      $default_language_id = (integer) @unserialize(DB::executeFirstCell("SELECT value FROM " . TABLE_PREFIX . "config_options WHERE name = 'language' AND module = 'system'"));

      // check if we have invoices
      $invoices = DB::execute('SELECT * FROM ' . $invoices_table);
      $old_invoice_ids = DB::executeFirstColumn('SELECT id FROM ' . $invoices_table);
      if (!is_foreachable($invoices)) {
        return true;
      } // if

      // starting transaction
      DB::beginWork('Starting Invoice migration');

      // remove orphaned items
      DB::execute("DELETE FROM $payments_table WHERE parent_id NOT IN (?) AND parent_type = ?", $old_invoice_ids, 'Invoice');
      DB::execute("DELETE FROM $invoice_related_records_table WHERE invoice_id NOT IN (?)", $old_invoice_ids);

      // create temporary column for old id
      DB::execute("ALTER TABLE $invoice_objects_table ADD old_id int unsigned DEFAULT NULL AFTER id");
      DB::execute("ALTER TABLE $invoice_object_items_table ADD old_id int unsigned DEFAULT NULL AFTER id");
      DB::execute("ALTER TABLE $invoice_object_items_table ADD migrated int(5) unsigned NOT NULL DEFAULT ? AFTER old_id", 0);

      // migrate invoice items
      $invoice_items = DB::execute('SELECT * FROM ' . $invoice_items_table . ' WHERE invoice_id IN (?)', $old_invoice_ids);
      if (is_foreachable($invoice_items)) {
        $invoice_items_batch = new DBBatchInsert($invoice_object_items_table, array(
          'old_id',
          'type',
          'parent_type',
          'parent_id',
          'first_tax_rate_id',
          'description',
          'quantity',
          'unit_cost',
          'position',
          'subtotal',
          'first_tax',
          'total'
        ));

        foreach ($invoice_items as $invoice_item) {
          list($item_subtotal, $item_tax, $item_total) = $this->recalculateItem($invoice_item, 'Invoice', array_var($invoice_item, 'invoice_id', 0));

          $invoice_items_batch->insertArray(array(
            array_var($invoice_item, 'id'),
            'InvoiceItem',
            'Invoice',
            array_var($invoice_item, 'invoice_id', 0),
            array_var($invoice_item, 'tax_rate_id', 0),
            array_var($invoice_item, 'description', ''),
            array_var($invoice_item, 'quantity', 1),
            array_var($invoice_item, 'unit_cost', 0),
            array_var($invoice_item, 'position', 0),
            $item_subtotal,
            $item_tax,
            $item_total
          ));
        } // if

        $invoice_items_batch->done();
      } // if

      // create invoices batch
      $invoices_batch = new DBBatchInsert($invoice_objects_table, array(
        'old_id',
        'type',
        'based_on_type',
        'based_on_id',
        'company_id',
        'project_id',
        'currency_id',
        'language_id',
        'varchar_field_1', // number
        'company_address',
        'varchar_field_2', // purchase_order_number
        'private_note',
        'note',
        'status',
        'date_field_2', // issued_on
        'integer_field_1', // issued_by_id
        'varchar_field_3', // issued_by_name
        'varchar_field_4', // issued_by_email
        'integer_field_2', // issued_to_id
        'date_field_1', // due_on
        'closed_on',
        'closed_by_id',
        'closed_by_name',
        'closed_by_email',
        'created_on',
        'created_by_id',
        'created_by_name',
        'created_by_email',
        'allow_payments',
        'subtotal',
        'tax',
        'total'
      ));

      // loop through invoices and batch insert
      foreach ($invoices as $invoice) {
        $old_id = array_var($invoice, 'id', null);

        $invoices_batch->insertArray(array(
          $old_id,
          'Invoice',
          array_var($invoice, 'based_on_type', null),
          array_var($invoice, 'based_on_id', null),
          (int) array_var($invoice, 'company_id', null),
          (int) array_var($invoice, 'project_id', null),
          (int) array_var($invoice, 'currency_id', null),
          (int) array_var($invoice, 'language_id', 0) ? (int) array_var($invoice, 'language_id', 0) : $default_language_id,
          array_var($invoice, 'number', null),
          array_var($invoice, 'company_address', null),
          array_var($invoice, 'purchase_order_number', null),
          array_var($invoice, 'comment', null),
          array_var($invoice, 'note', null),
          array_var($invoice, 'status', null),
          array_var($invoice, 'issued_on', null),
          array_var($invoice, 'issued_by_id', null),
          array_var($invoice, 'issued_by_name', null),
          array_var($invoice, 'issued_by_email', null),
          array_var($invoice, 'issued_to_id', null),
          array_var($invoice, 'due_on', null),
          array_var($invoice, 'closed_on', null),
          array_var($invoice, 'closed_by_id', null),
          array_var($invoice, 'closed_by_name', null),
          array_var($invoice, 'closed_by_email', null),
          array_var($invoice, 'created_on', null),
          array_var($invoice, 'created_by_id', null),
          array_var($invoice, 'created_by_name', null),
          array_var($invoice, 'created_by_email', null),
          array_var($invoice, 'allow_payments', null),
          array_key_exists($old_id, $this->invoice_totals['invoice']) ? $this->invoice_totals['invoice'][$old_id]['subtotal'] : 0,
          array_key_exists($old_id, $this->invoice_totals['invoice']) ? $this->invoice_totals['invoice'][$old_id]['tax'] : 0,
          array_key_exists($old_id, $this->invoice_totals['invoice']) ? $this->invoice_totals['invoice'][$old_id]['total'] : 0
        ));
      } // foreach

      // finalize batch insert
      $invoices_batch->done();

      DB::execute("ALTER TABLE $payments_table ADD migrated tinyint(1) DEFAULT 0 AFTER id"); // add migrated column for payments table
      DB::execute("ALTER TABLE $invoice_related_records_table ADD migrated tinyint(1) DEFAULT 0 AFTER invoice_id"); // add migrated column for related records table

      $invoices_id_map = DB::execute("SELECT id, old_id FROM $invoice_objects_table"); // update parent_id
      if (is_foreachable($invoices_id_map)) {
        foreach ($invoices_id_map as $invoices_id_map_item) {
          // update invoice items relation
          DB::execute("UPDATE $invoice_object_items_table SET migrated = ?, parent_id = ? WHERE parent_id = ? AND migrated < ?", 1, $invoices_id_map_item['id'], $invoices_id_map_item['old_id'], 1);
          // update payments relation
          DB::execute("UPDATE $payments_table SET migrated = ?, parent_id = ? WHERE parent_id = ? AND parent_type = ? AND migrated < ?", 1,  $invoices_id_map_item['id'], $invoices_id_map_item['old_id'], 'Invoice', 1);
          // update invoice related objects
          DB::execute("UPDATE $invoice_related_records_table SET migrated = ?, invoice_id = ? WHERE invoice_id = ? AND migrated < ?", 1, $invoices_id_map_item['id'], $invoices_id_map_item['old_id'], 1);
        } // if
      } // if

      // update related records item_id
      DB::execute("UPDATE $invoice_related_records_table SET migrated = ?", 0); // reset migrated state
      $invoice_items_id_map = DB::execute('SELECT id, old_id FROM ' . $invoice_object_items_table . ' WHERE parent_type = ?', 'Invoice');
      if (is_foreachable($invoice_items_id_map)) {
        foreach ($invoice_items_id_map as $invoice_items_id_map_item) {
          DB::execute("UPDATE $invoice_related_records_table SET migrated = ?, item_id = ? WHERE item_id = ? AND migrated < ?", 1 , $invoice_items_id_map_item['id'], $invoice_items_id_map_item['old_id'], 1);
        } // foreach
      } // if

      // dropping unnecessary fields
      DB::execute("ALTER TABLE $invoice_objects_table DROP old_id");
      DB::execute("ALTER TABLE $invoice_object_items_table DROP old_id");
      DB::execute("ALTER TABLE $invoice_object_items_table DROP migrated");
      DB::execute("ALTER TABLE $payments_table DROP migrated");
      DB::execute("ALTER TABLE $invoice_related_records_table DROP migrated");

      DB::commit('Invoice migrate successfully');
    } catch (Exception $e) {
      DB::rollback('Failed to migrate invoices');
      die($e->getMessage());
    } // try
    return true;
  } // migrateInvoices

  /**
   * Migrate Quotes
   */
  function migrateQuotes() {
    try {
      // new tables
      $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
      $invoice_object_items_table = TABLE_PREFIX . 'invoice_object_items';

      // old tables
      $quotes_table = TABLE_PREFIX . 'backup_quotes';
      $quote_items_table = TABLE_PREFIX . 'backup_quote_items';
      $comments_table = TABLE_PREFIX . 'comments';

      // default language id
      $default_language_id = (integer) @unserialize(DB::executeFirstCell("SELECT value FROM " . TABLE_PREFIX . "config_options WHERE name = 'language' AND module = 'system'"));

      // check if we have invoices
      $quotes = DB::execute('SELECT * FROM ' . $quotes_table);
      $old_quote_ids = DB::executeFirstColumn('SELECT id FROM ' . $quotes_table);
      if (!is_foreachable($quotes)) {
        return true;
      } // if

      // starting transaction
      DB::beginWork('Starting Quote migration');

      // create temporary column for old id
      DB::execute("ALTER TABLE $invoice_objects_table ADD old_id int unsigned DEFAULT NULL AFTER id");
      DB::execute("ALTER TABLE $invoice_object_items_table ADD old_id int unsigned DEFAULT NULL AFTER id");
      DB::execute("ALTER TABLE $invoice_object_items_table ADD migrated int(5) unsigned NOT NULL DEFAULT ? AFTER old_id", 0);

      // migrate quote items
      $quote_items = DB::execute('SELECT * FROM ' . $quote_items_table . ' WHERE quote_id IN (?)', $old_quote_ids);
      if (is_foreachable($quote_items)) {
        $quote_items_batch = new DBBatchInsert($invoice_object_items_table, array(
          'old_id',
          'type',
          'parent_type',
          'parent_id',
          'first_tax_rate_id',
          'description',
          'quantity',
          'unit_cost',
          'position',
          'subtotal',
          'first_tax',
          'total'
        ));

        foreach ($quote_items as $quote_item) {
          list($item_subtotal, $item_tax, $item_total) = $this->recalculateItem($quote_item, 'Quote', array_var($quote_item, 'quote_id', 0));

          $quote_items_batch->insertArray(array(
            array_var($quote_item, 'id'),
            'QuoteItem',
            'Quote',
            array_var($quote_item, 'quote_id', 0),
            array_var($quote_item, 'tax_rate_id', 0),
            array_var($quote_item, 'description', ''),
            array_var($quote_item, 'quantity', 1),
            array_var($quote_item, 'unit_cost', 0),
            array_var($quote_item, 'position', 0),
            $item_subtotal,
            $item_tax,
            $item_total
          ));
        } // foreach

        $quote_items_batch->done();
      } // if

      // create quotes batch
      $quotes_batch = new DBBatchInsert($invoice_objects_table, array(
        'old_id',
        'type',
        'based_on_type',
        'based_on_id',
        'project_id',
        'currency_id',
        'language_id',
        'company_id',
        'company_name',
        'company_address',
        'name',
        'private_note',
        'note',
        'status',
        'integer_field_1', // is_locked
        'varchar_field_1', // public_id
        'closed_on',
        'closed_by_id',
        'closed_by_name',
        'closed_by_email',
        'created_on',
        'created_by_id',
        'created_by_name',
        'created_by_email',
        'sent_on',
        'sent_by_id',
        'sent_by_name',
        'sent_by_email',
        'integer_field_2', // sent_to_id
        'varchar_field_2', // sent_to_name
        'varchar_field_3', // sent_to_email
        'recipient_id',
        'recipient_name',
        'recipient_email',
        'datetime_field_1', // last comment on
        'subtotal',
        'tax',
        'total'
      ));

      foreach ($quotes as $quote) {
        $old_id = array_var($quote, 'id', null);

        $quotes_batch->insertArray(array(
          $old_id,
          'Quote',
          array_var($quote, 'based_on_type', null),
          array_var($quote, 'based_on_id', null),
          (int) array_var($quote, 'project_id', null),
          (int) array_var($quote, 'currency_id', null),
          (int) array_var($quote, 'language_id', 0) ? (int) array_var($quote, 'language_id', 0) : $default_language_id,
          (int) array_var($quote, 'company_id', null),
          array_var($quote, 'company_name', null),
          array_var($quote, 'company_address', null),
          array_var($quote, 'name', null),
          array_var($quote, 'private_note', null),
          array_var($quote, 'note', null),
          array_var($quote, 'status', null),
          array_var($quote, 'is_locked', null),
          array_var($quote, 'public_id', null),
          array_var($quote, 'closed_on', null),
          array_var($quote, 'closed_by_id', null),
          array_var($quote, 'closed_by_name', null),
          array_var($quote, 'closed_by_email', null),
          array_var($quote, 'created_on', null),
          array_var($quote, 'created_by_id', null),
          array_var($quote, 'created_by_name', null),
          array_var($quote, 'created_by_email', null),
          array_var($quote, 'sent_on', null),
          array_var($quote, 'sent_by_id', null),
          array_var($quote, 'sent_by_name', null),
          array_var($quote, 'sent_by_email', null),
          array_var($quote, 'sent_to_id', null),
          array_var($quote, 'sent_to_name', null),
          array_var($quote, 'sent_to_email', null),
          array_var($quote, 'recipient_id', null),
          array_var($quote, 'recipient_name', null),
          array_var($quote, 'recipient_email', null),
          array_var($quote, 'last_comment_on', null),
          array_key_exists($old_id, $this->invoice_totals['quote']) ? $this->invoice_totals['quote'][$old_id]['subtotal'] : 0,
          array_key_exists($old_id, $this->invoice_totals['quote']) ? $this->invoice_totals['quote'][$old_id]['tax'] : 0,
          array_key_exists($old_id, $this->invoice_totals['quote']) ? $this->invoice_totals['quote'][$old_id]['total'] : 0
        ));
      } // foreach

      $quotes_batch->done();

      $project_table = TABLE_PREFIX . 'projects';

      DB::execute("ALTER TABLE $invoice_objects_table ADD migrated tinyint(1) DEFAULT 0 AFTER old_id"); // add migrated column for invoice objects table
      DB::execute("ALTER TABLE $comments_table ADD migrated tinyint(1) DEFAULT 0 AFTER id"); // add migrated column for comments table
      DB::execute("ALTER TABLE $project_table ADD migrated tinyint(1) DEFAULT 0 AFTER id"); // add migrated column for projects table

      // update quote id's
      $quotes_id_map = DB::execute("SELECT id, old_id FROM $invoice_objects_table WHERE type = ?", 'Quote'); // update parent_id
      if (is_foreachable($quotes_id_map)) {
        foreach ($quotes_id_map as $quotes_id_map_item) {
          // update invoice objects
          DB::execute("UPDATE $invoice_objects_table SET migrated = ?, based_on_id = ? WHERE based_on_id = ? AND based_on_type = ? AND migrated < ?", 1, $quotes_id_map_item['id'], $quotes_id_map_item['old_id'], 'Quote', 1);
          // update invoice items relation
          DB::execute("UPDATE $invoice_object_items_table SET migrated = ?, parent_id = ? WHERE parent_id = ? AND parent_type = ? AND migrated < ?", 1, $quotes_id_map_item['id'], $quotes_id_map_item['old_id'], 'Quote', 1);
          // update project based on quotes
          DB::execute("UPDATE $project_table SET migrated = ?, based_on_id = ? WHERE based_on_id = ? AND based_on_type = ? AND migrated < ?", 1, $quotes_id_map_item['id'], $quotes_id_map_item['old_id'], 'Quote', 1);
          // update comments table and update references to old quotes
          DB::execute("UPDATE $comments_table SET migrated = ?, parent_id = ? WHERE parent_id = ? AND parent_type = ? AND migrated < ?", 1, $quotes_id_map_item['id'], $quotes_id_map_item['old_id'], 'Quote', 1);
        } // if
      } // if

      // dropping unnecessary fields
      DB::execute("ALTER TABLE $invoice_objects_table DROP old_id");
      DB::execute("ALTER TABLE $invoice_objects_table DROP migrated");
      DB::execute("ALTER TABLE $invoice_object_items_table DROP old_id");
      DB::execute("ALTER TABLE $invoice_object_items_table DROP migrated");
      DB::execute("ALTER TABLE $comments_table DROP migrated");

      // insert config option which tells future upgrade scripts that quote comments are migrated
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES ('quote_comments_migrated', 'system', ?);", serialize(true));

      DB::commit('Quotes migrated successfully');
    } catch (Exception $e) {
      DB::rollback('Failed to migrate quotes');

      return $e->getMessage();
    } // try

    return true;
  } // migrateQuotes

  /**
   * Migrate Recurring Profiles
   */
  function migrateRecurringProfiles() {
    try {
      // new tables
      $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
      $invoice_object_items_table = TABLE_PREFIX . 'invoice_object_items';

      // old tables
      $recurring_profiles_table = TABLE_PREFIX . 'backup_recurring_profiles';
      $recurring_profile_items_table = TABLE_PREFIX . 'backup_recurring_profile_items';

      // default language_id
      $default_language_id = (integer) @unserialize(DB::executeFirstCell("SELECT value FROM " . TABLE_PREFIX . "config_options WHERE name = 'language' AND module = 'system'"));

      // check if we have invoices
      $recurring_profiles = DB::execute('SELECT * FROM ' . $recurring_profiles_table);
      $old_recurring_profile_ids = DB::execute('SELECT id FROM ' . $recurring_profiles_table);
      if (!is_foreachable($recurring_profiles)) {
        return true;
      } // if

      // starting transaction
      DB::beginWork('Starting Recurring Profiles migration');

      // create temporary column for old id
      DB::execute("ALTER TABLE $invoice_objects_table ADD old_id int unsigned DEFAULT NULL AFTER id");
      DB::execute("ALTER TABLE $invoice_object_items_table ADD old_id int unsigned DEFAULT NULL AFTER id");
      DB::execute("ALTER TABLE $invoice_object_items_table ADD migrated int(5) unsigned NOT NULL DEFAULT ? AFTER old_id", 0);

      // migrate recurring profile items
      $recurring_profile_items = DB::execute('SELECT * FROM ' . $recurring_profile_items_table . ' WHERE recurring_profile_id IN (?)', $old_recurring_profile_ids);
      if (is_foreachable($recurring_profile_items)) {
        $recurring_profile_items_batch = new DBBatchInsert($invoice_object_items_table, array(
          'old_id',
          'type',
          'parent_type',
          'parent_id',
          'first_tax_rate_id',
          'description',
          'quantity',
          'unit_cost',
          'position',
          'subtotal',
          'first_tax',
          'total'
        ));

        foreach ($recurring_profile_items as $recurring_profile_item) {
          list($item_subtotal, $item_tax, $item_total) = $this->recalculateItem($recurring_profile_item, 'RecurringProfile', array_var($recurring_profile_item, 'recurring_profile_id', 0));

          $recurring_profile_items_batch->insertArray(array(
            array_var($recurring_profile_item, 'id'),
            'RecurringProfileItem',
            'RecurringProfile',
            array_var($recurring_profile_item, 'recurring_profile_id', 0),
            array_var($recurring_profile_item, 'tax_rate_id', 0),
            array_var($recurring_profile_item, 'description', ''),
            array_var($recurring_profile_item, 'quantity', 1),
            array_var($recurring_profile_item, 'unit_cost', 0),
            array_var($recurring_profile_item, 'position', 0),
            $item_subtotal,
            $item_tax,
            $item_total
          ));
        } // foreach

        $recurring_profile_items_batch->done();
      } // if

      // create quotes batch
      $recurring_profiles_batch = new DBBatchInsert($invoice_objects_table, array(
        'old_id',
        'type',
        'project_id',
        'currency_id',
        'language_id',
        'company_id',
        'company_address',
        'name',
        'private_note',
        'note',
        'date_field_3', // start_on
        'varchar_field_1', // frequency
        'varchar_field_2', // occurrences
        'integer_field_1', // auto_issue
        'integer_field_2', // invoice_due_after
        'integer_field_3', // triggered_number
        'date_field_1', // last_triggered_on
        'date_field_2', // next_trigger_on
        'allow_payments',
        'state',
        'original_state',
        'visibility',
        'created_on',
        'created_by_id',
        'created_by_name',
        'created_by_email',
        'recipient_id',
        'recipient_name',
        'recipient_email',
        'subtotal',
        'tax',
        'total'
      ));

      foreach ($recurring_profiles as $recurring_profile) {
        $old_id = array_var($recurring_profile, 'id', null);

        $recurring_profiles_batch->insertArray(array(
          $old_id,
          'RecurringProfile',
          (int) array_var($recurring_profile, 'project_id', null),
          (int) array_var($recurring_profile, 'currency_id', null),
          (int) array_var($recurring_profile, 'language_id', 0) ? (int) array_var($recurring_profile, 'language_id', 0) : $default_language_id,
          (int) array_var($recurring_profile, 'company_id', null),
          array_required_var($recurring_profile, 'company_address', null),
          array_required_var($recurring_profile, 'name', null),
          array_required_var($recurring_profile, 'our_comment', null),
          array_required_var($recurring_profile, 'note', null),
          array_required_var($recurring_profile, 'start_on', null),
          array_required_var($recurring_profile, 'frequency', null),
          array_required_var($recurring_profile, 'occurrences', null),
          array_required_var($recurring_profile, 'auto_issue', null),
          array_required_var($recurring_profile, 'invoice_due_after', null),
          array_required_var($recurring_profile, 'triggered_number', null),
          array_required_var($recurring_profile, 'last_triggered_on', null),
          array_required_var($recurring_profile, 'next_trigger_on', null),
          array_required_var($recurring_profile, 'allow_payments', null),
          (int) array_required_var($recurring_profile, 'state', null),
          (int) array_required_var($recurring_profile, 'original_state', null),
          (int) array_required_var($recurring_profile, 'visibility', 0),
          array_required_var($recurring_profile, 'created_on', null),
          array_required_var($recurring_profile, 'created_by_id', null),
          array_required_var($recurring_profile, 'created_by_name', null),
          array_required_var($recurring_profile, 'created_by_email', null),
          array_required_var($recurring_profile, 'recipient_id', null),
          array_required_var($recurring_profile, 'recipient_name', null),
          array_required_var($recurring_profile, 'recipient_email', null),
          array_key_exists($old_id, $this->invoice_totals['recurringprofile']) ? $this->invoice_totals['recurringprofile'][$old_id]['subtotal'] : 0,
          array_key_exists($old_id, $this->invoice_totals['recurringprofile']) ? $this->invoice_totals['recurringprofile'][$old_id]['tax'] : 0,
          array_key_exists($old_id, $this->invoice_totals['recurringprofile']) ? $this->invoice_totals['recurringprofile'][$old_id]['total'] : 0
        ));
      } // foreach

      $recurring_profiles_batch->done();

//         update recurring profile id's
      $recurring_profiles_id_map = DB::execute("SELECT id, old_id FROM $invoice_objects_table WHERE type = ?", 'RecurringProfile'); // update parent_id
      if (is_foreachable($recurring_profiles_id_map)) {
        foreach ($recurring_profiles_id_map as $recurring_profiles_id_map_item) {
          // update invoice items relation
          DB::execute("UPDATE $invoice_object_items_table SET migrated = ?, parent_id = ? WHERE parent_id = ? AND parent_type = ? AND migrated < ?", 1, $recurring_profiles_id_map_item['id'], $recurring_profiles_id_map_item['old_id'], 'RecurringProfile', 1);
          //update invoice object with new recurring profiles id
          DB::execute("UPDATE $invoice_objects_table SET based_on_id = ? WHERE based_on_id = ? AND based_on_type = ? AND type = ?", $recurring_profiles_id_map_item['id'], $recurring_profiles_id_map_item['old_id'], 'RecurringProfile', 'Invoice');
        } // if
      } // if

      // dropping unnecessary fields
      DB::execute("ALTER TABLE $invoice_objects_table DROP old_id");
      DB::execute("ALTER TABLE $invoice_object_items_table DROP old_id");
      DB::execute("ALTER TABLE $invoice_object_items_table DROP migrated");
      DB::commit('Recurring Profiles migrated successfully');
    } catch (Exception $e) {
      DB::rollback('Failed to migrate Recurring Profiles');
      die($e->getMessage());
    } // try
    return true;
  } // migrateRecurringProfiles

  /**
   * Recalculating Payment Totals
   */
  function recalculatingPaymentTotals() {
    $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
    $payments_table = TABLE_PREFIX . 'payments';

    // set balance due to invoice total
    DB::execute('UPDATE ' . $invoice_objects_table . ' SET balance_due = total');

    $total_payments = DB::execute('SELECT parent_id AS invoice_id, SUM(amount) AS paid_amount FROM ' . $payments_table . ' WHERE parent_type = ? AND status = ? GROUP BY parent_id ORDER BY parent_id ASC', 'Invoice', 'Paid');
    if ($total_payments) {
      foreach ($total_payments as $total_payment) {
        $invoice_id = $total_payment['invoice_id'];
        $paid_amount = $total_payment['paid_amount'];
        $invoice_total = DB::executeFirstCell('SELECT total FROM ' . $invoice_objects_table . ' WHERE id = ? AND type = ?', $invoice_id, 'Invoice');
        $balance_due = $invoice_total - $paid_amount;
        DB::execute('UPDATE ' . $invoice_objects_table . ' SET balance_due = ?, paid_amount = ? WHERE id = ? AND type = ?', $balance_due, $paid_amount, $invoice_id, 'Invoice');
      } // foreach
    } // if
  } // recalculatingPaymentTotals

  /**
   * Upgrade users table
   *
   * @return bool|string
   */
  function updateUsersTable() {
    try {
      DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'users ADD invited_on DATETIME NULL DEFAULT NULL AFTER updated_by_email');
    } catch(Exception $e) {
      return $e->getMessage();
    } // if
    return true;
  } // updateUsersTable

  /**
   * Update search options
   *
   * @return bool|string
   */
  function updateSearchOptions() {
    try {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES
          ('elastic_search_hosts', 'system', ?);", serialize('localhost:9200'));
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateSearchOptions

  /**
   * Updating Invoice Objects table
   *
   * @return bool|string
   */
  function updateInvoiceObjectsTable() {
    try {
      DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'invoice_objects ADD reminder_sent_on DATETIME NULL DEFAULT NULL AFTER sent_by_email');
    } catch(Exception $e) {
      return $e->getMessage();
    } // if

    return true;
  } // updateInvoiceObjectsTable

  /**
   * Add Invoice overdue reminders config options
   *
   * @return bool|string
   */
  function addInvoiceOverdueRemindersConfigOptions() {
    try {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_options (name, module, value) VALUES
          ('invoice_overdue_reminders_enabled', 'invoicing', 'b:0;'),
          ('invoice_overdue_reminders_send_first', 'invoicing', 'i:7;'),
          ('invoice_overdue_reminders_send_every', 'invoicing', 'i:7;'),
          ('invoice_overdue_reminders_first_message', 'invoicing', ?),
          ('invoice_overdue_reminders_escalation_enabled', 'invoicing', 'b:0;'),
          ('invoice_overdue_reminders_escalation_messages', 'invoicing', 'a:1:{i:0;a:2:{s:14:\"send_escalated\";i:14;s:17:\"escalated_message\";N;}}'),
          ('invoice_overdue_reminders_dont_send_to', 'invoicing', 'N;')",
        serialize('We would like to remind you that the following invoice has been overdue. Please send your payment promptly. Thank you.') // invoice_overdue_reminders_first_message
      );
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // addInvoiceOverdueRemindersConfigOptions

}
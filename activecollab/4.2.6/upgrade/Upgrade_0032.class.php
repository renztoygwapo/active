<?php

	/**
   * Update activeCollab 3.0.9 to activeCollab 3.1.0
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0032 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.0.9';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.0';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'fixMissedSteps' => 'Complete quotes and recurring profiles upgrade',
      );
    } // getActions

    /**
     * Complete missed invoicing steps
     *
     * @return bool
     */
    function fixMissedSteps() {
      try {
        if($this->isModuleInstalled('invoicing')) {

          // UpgradeScript_0026::updateInvoiceDrafts
          DB::execute("UPDATE " . TABLE_PREFIX . "invoices SET due_on = NULL WHERE status = '0'");

          // Fix quotes
          $quotes_table = TABLE_PREFIX . "quotes";
          $quotes_table_fields = DB::listTableFields($quotes_table);

          if(!in_array('company_name', $quotes_table_fields) && !in_array('public_id', $quotes_table_fields) && !in_array('private_note', $quotes_table_fields) && !in_array('recipient_id', $quotes_table_fields) && !in_array('recipient_name', $quotes_table_fields) && !in_array('recipient_email', $quotes_table_fields)) {
            DB::execute("ALTER TABLE $quotes_table ADD company_name VARCHAR(150) NULL DEFAULT NULL AFTER company_id");
            DB::execute("ALTER TABLE $quotes_table ADD public_id VARCHAR(32) NOT NULL DEFAULT '' AFTER id");
            DB::execute("ALTER TABLE $quotes_table ADD private_note TEXT NULL DEFAULT NULL AFTER note");
            DB::execute("ALTER TABLE $quotes_table ADD recipient_id INT(10) NOT NULL DEFAULT '0' AFTER sent_by_email");
            DB::execute("ALTER TABLE $quotes_table ADD recipient_name VARCHAR(100) NULL DEFAULT NULL AFTER recipient_id");
            DB::execute("ALTER TABLE $quotes_table ADD recipient_email VARCHAR(150) NULL DEFAULT NULL AFTER recipient_name");

            $quotes = DB::execute("SELECT id, public_id, created_on FROM $quotes_table");
            if ($quotes) {
              foreach($quotes as $quote) {
                if (empty($quote['public_id'])) {
                  DB::execute("UPDATE $quotes_table SET public_id = ? WHERE id = ?", md5($quote['id'] . $quote['created_on']), $quote['id']);
                } // if
              } // foreach
            } // if
          } // if

          // Fix recurring profiles
          $recurring_profiles_table = TABLE_PREFIX . 'recurring_profiles';

          $recurring_profile_fields = DB::listTableFields($recurring_profiles_table);

          if(in_array('request_approval', $recurring_profile_fields)) {
            DB::execute("ALTER TABLE $recurring_profiles_table DROP request_approval");
          } // if

          if(!in_array('auto_issue', $recurring_profile_fields)) {
            DB::execute("ALTER TABLE $recurring_profiles_table ADD auto_issue tinyint(1) NOT NULL DEFAULT '0' AFTER occurrences");
          } // if

          if(!in_array('recipient_id', $recurring_profile_fields)) {
            DB::execute("ALTER TABLE $recurring_profiles_table ADD recipient_id INT(10) NOT NULL DEFAULT '0' AFTER visibility");
          } // if

          if(!in_array('recipient_name', $recurring_profile_fields)) {
            DB::execute("ALTER TABLE $recurring_profiles_table ADD recipient_name VARCHAR(100) NULL DEFAULT NULL AFTER recipient_id");
          } // if

          if(!in_array('recipient_email', $recurring_profile_fields)) {
            DB::execute("ALTER TABLE $recurring_profiles_table ADD recipient_email VARCHAR(150) NULL DEFAULT NULL AFTER recipient_name");
          } // if

          if(DB::tableExists(TABLE_PREFIX . 'recurring_approval_requests')) {
            DB::execute('DROP TABLE IF EXISTS ' . DB::escapeTableName(TABLE_PREFIX . 'recurring_approval_requests'));
          } // if
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // fixMissedSteps

  }
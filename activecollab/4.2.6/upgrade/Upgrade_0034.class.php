<?php

	/**
   * Update activeCollab 3.1.1 to activeCollab 3.1.2
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0034 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.1';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.2';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'fixSlugs' => 'Fix project slugs',
        'fixQuoteItemsTable' => 'Create quote items table if it is missing',
        'fixRelatedRecordsTable' => 'Create related invoice record table if it is missing',
        'dropReservedLanguage' => 'Fix en_US.UTF-8',
      );
    } // getActions

    /**
     * Fix project slugs
     *
     * @return bool|string
     */
    function fixSlugs() {
      $projects_table = TABLE_PREFIX . 'projects';

      try {
        $rows = DB::execute("SELECT id, slug FROM $projects_table");

        if($rows) {

          /**
           * Slugify function, because we may not have new inflector loaded
           *
           * @param string $string
           * @param string $space
           * @return string
           */
          $slugify = function($string, $space = '-') {
            if (function_exists('iconv')) {
              $string = @iconv('UTF-8', 'ASCII//TRANSLIT', $string);
            } // if

            $string = preg_replace("/[^a-zA-Z0-9 -]/", '', $string);
            $string = strtolower($string);
            $string = str_replace(" ", $space, $string);

            while(strpos($string, '--') !== false) {
              $string = str_replace('--', '-', $string);
            } // while

            return trim($string);
          };

          try {
            DB::beginWork('Updating slugs @ ' . __CLASS__);

            foreach($rows as $row) {
              $old_slug = $row['slug'];
              $new_slug = $slugify($old_slug);

              if($old_slug != $new_slug) {
                DB::execute("UPDATE $projects_table SET slug = ? WHERE id = ?", $new_slug, $row['id']);
              } // if
            } // foreach

            DB::commit('Slugs updated @ ' . __CLASS__);
          } catch(Exception $e) {
            DB::rollback('Failed to update slugs @ ' . __CLASS__);
            throw $e;
          } // try
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
    } // fixSlugs

    /**
     * Create quote items table if it is missing
     *
     * @return bool|string
     */
    function fixQuoteItemsTable() {
      try {
        $tables = DB::listTables(TABLE_PREFIX);

        if(in_array(TABLE_PREFIX . 'quotes', $tables) && !in_array(TABLE_PREFIX . 'quote_items', $tables)) {
          $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

          DB::execute("CREATE TABLE " . TABLE_PREFIX . "quote_items (
            id int unsigned NOT NULL auto_increment,
            quote_id int(5) unsigned NOT NULL DEFAULT 0,
            position int(11) NOT NULL DEFAULT 0,
            tax_rate_id int(3) unsigned NOT NULL DEFAULT 0,
            description varchar(255) NOT NULL DEFAULT '',
            quantity decimal(12, 2) unsigned NOT NULL DEFAULT 1,
            unit_cost decimal(12, 3) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX quote_id (quote_id),
            INDEX position (position)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // fixQuoteItemsTable

    /**
     * Create related records if invoicing module is installed
     *
     * @return bool|string
     */
    function fixRelatedRecordsTable() {
      try {
        if($this->isModuleInstalled('invoicing')) {
          $related_items_table = TABLE_PREFIX . 'invoice_related_records';

          // If table exists, update keys
          if(in_array($related_items_table, DB::listTables(TABLE_PREFIX))) {
            $indexes = array();

            $rows = DB::execute("SHOW INDEXES FROM $related_items_table");
            if(is_foreachable($rows)) {
              foreach($rows as $row) {
                $key_name = $row['Key_name'];

                if(!in_array($key_name, $indexes)) {
                  $indexes[] = $key_name;
                } // if
              } // foreach
            } // if

            if(in_array('PRIMARY', $indexes)) {
              DB::execute("ALTER TABLE $related_items_table DROP PRIMARY KEY");
            } // if

            DB::execute("ALTER TABLE $related_items_table ADD PRIMARY KEY (invoice_id, item_id, parent_type, parent_id)");

            if(!in_array('parent', $indexes)) {
              DB::execute("ALTER TABLE $related_items_table ADD INDEX parent (parent_type, parent_id)");
            } // if

          // Create new table
          } else {
            $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

            DB::execute("CREATE TABLE $related_items_table (
              invoice_id int(5) unsigned NULL DEFAULT NULL,
              item_id int(10) unsigned NULL DEFAULT NULL,
              parent_type varchar(50)  DEFAULT NULL,
              parent_id int unsigned NULL DEFAULT NULL,
              INDEX parent (parent_type, parent_id),
              PRIMARY KEY (invoice_id, item_id, parent_type, parent_id)
            ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
          } // if
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // fixRelatedRecordsTable

    /**
     * Drops the custom language that is using en_US.UTF-8 as a locale because that locale is reserved by the system
     *
     * @return bool|string
     */
    function dropReservedLanguage() {
      try {
        // find en_US.UTF-8 language
        $built_in_language = DB::executeFirstCell('SELECT id FROM ' . TABLE_PREFIX . 'languages WHERE locale = ?', 'en_US.UTF-8');
        if ($built_in_language) {
          try {
            DB::beginWork('Removing built in language @ ' . __CLASS__);

            // delete all translations for this language
            DB::execute('DELETE FROM ' . TABLE_PREFIX . 'language_phrase_translations WHERE language_id = ?', $built_in_language);
            // this will reset language for all users who selected the faux built-in language
            DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_option_values WHERE name = ? AND parent_type = ? AND value = ?', 'language', 'user', serialize($built_in_language));
            // this will reset the global config option
            DB::execute('UPDATE ' . TABLE_PREFIX . 'config_options SET value = ? WHERE name = ? AND value = ?', serialize(null), 'language', serialize($built_in_language));
            // remove the language itself from the database
            DB::execute('DELETE FROM ' . TABLE_PREFIX . 'languages WHERE id = ?', $built_in_language);

            DB::commit('Build in language removed @ ' . __CLASS__);
          } catch(Exception $e) {
            DB::rollback('Failed to remove built in language @ ' . __CLASS__);
            return $e->getMessage();
          } // try
        } // if
      } catch (Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // dropReservedLanguage

  }
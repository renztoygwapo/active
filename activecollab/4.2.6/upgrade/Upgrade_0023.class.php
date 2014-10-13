<?php

/**
 * Update activeCollab 2.3.9 to activeCollab 3.0.0
 *
 * @package activeCollab.upgrade
 * @subpackage scripts
 */
class Upgrade_0023 extends AngieApplicationUpgradeScript {

  /**
   * Initial system version
   *
   * @var string
   */
  protected $from_version = '2.3.11';

  /**
   * Final system version
   *
   * @var string
   */
  protected $to_version = '3.0.0';

  /**
   * Construct upgrade script
   */
  function __construct() {
    $email_separators = $this->getEmailSeparators();
    $this->strip_reply_callback = function ($matches) use ($email_separators) {
      foreach ($email_separators as $email_separator) {
        if (strpos_utf($matches[1], $email_separator) !== false) {
          return '';
        } // if
      } // foreach
      return $matches[0];
    };
  } // __construct

  /**
   * Cached email sepatators
   *
   * @var array
   */
  private $email_separators = false;

  /**
   * Return array of email separators and it's translations
   *
   * @return array
   */
  private function getEmailSeparators() {
    if ($this->email_separators === false) {
      $default_separator = '-- REPLY ABOVE THIS LINE --';
      $this->email_separators = array($default_separator);

      $available_localizations = get_folders(CUSTOM_PATH . '/localization');
      if (is_foreachable($available_localizations)) {
        foreach ($available_localizations as $localization) {
          $translation_file = $localization . '/module.system.php';
          if (is_file($translation_file)) {
            $translations = include ($translation_file);
            if (isset($translations[$default_separator])) {
              $this->email_separators[] = $translations[$default_separator];
            } // if
          } // if
        } // foreach
      } // if
    } // if

    return $this->email_separators;
  } // getEmailSeparators

  /**
   * Callback function for stripping email reply
   *
   * @var bool
   */
  private $strip_reply_callback = false;

  /**
   * Regexp query for matching blockquotes
   *
   * @var string
   */
  private $blockquote_regexp = '/\<blockquote[^>]*?\>(.*?)\<\/blockquote\>/is';

  /**
   * Convert richtext to markdown hybrid
   *
   * @param String $richtext
   */
  private function updateHtmlContent($richtext) {
    if(strlen_utf($richtext) > 10000) {
      return $richtext;
    } // if

    $richtext = trim($richtext);

    if($richtext) {
      $email_separators = $this->getEmailSeparators();

      // first try to remove blockquotes with separators
      foreach ($email_separators as $email_separator) {
        if (strpos_utf($richtext, $email_separator) !== false) {
          $richtext = trim(preg_replace_callback($this->blockquote_regexp, $this->strip_reply_callback, $richtext));
          break;
        } // if
      } // foreach

      // then do the 'hard' removing of replies
      foreach ($email_separators as $email_separator) {
        if (strpos_utf($richtext, $email_separator) !== false) {
          $richtext = trim(preg_replace_callback($this->blockquote_regexp, $this->strip_reply_callback, $richtext));
          break;
        } // if
      } // foreach

      // then do the 'hard' removing of replies
      // Clean-up text bellow -- REPLY ABOVE THIS LINE --
      $reply_above_pos = strpos_utf($richtext, '-- REPLY ABOVE THIS LINE --');
      if($reply_above_pos) {
        $richtext = substr_utf($richtext, 0, $reply_above_pos); // Strip everything after "-- REPLY ABOVE THIS LINE --"
      } // if

      // Convert inline attachments to placeholders
      if (preg_match_all('/<img [^>]*src=["|\'][^"|\']+attachments\/([^"]+)\?[^"|\']+["|\'][^>]*>/is', $richtext, $matches)) {
        foreach ($matches[0] as $match_id => $match) {
          $attachment_id = $matches[1][$match_id];
          if ($attachment_id) {
            $richtext = str_replace($match, '<img object-id="' . $attachment_id . '" image-type="attachment">', $richtext);
          } // if
        } // foreach
      } // if

      // Newline to <br> for plain text entries, before we load DOM
      $richtext = nl2br($richtext);

      // Remove all inline styles
      $dom = SimpleHTMLDOMForAngie::getInstance($richtext);
      if($dom) {
        $elements = $dom->find('*[style]');
        if(is_foreachable($elements)) {
          foreach($elements as $element) {
            if($element->style == 'text-decoration:line-through;') {
              $element->outertext = '<del>' . $element->plaintext . '</del>';
            } else {
              $element->removeAttribute('style');
            } // if
          } // foreach
        } // if

        // Remove Apple style class SPAN-s
        $elements = $dom->find('span[class=Apple-style-span]');
        if(is_foreachable($elements)) {
          foreach($elements as $element) {
            $element->outertext = $element->plaintext;
          } // foreach
        } // if

        $richtext = (string) $dom;
      } // if
    } // if

    return empty($richtext) ? null : $richtext;
  } // updateHtmlContent

  /**
   * Return script actions
   *
   * @return array
   */
  function getActions() {
    return array(
      'updateUpdateHistoryTable' => 'Update upgrade history storage',
      'updateModules' => 'Update module definitions',
      'updateProjectObjectsParentType' => 'Update parent type values',
      'updateConfigOptions' => 'Update configuration options storage',
      'updateSystemModuleConfigOptions' => 'Update system configuration',
      'updateRoles' => 'Update roles',
      'updateLanguages' => 'Update localization data',
      'updateSearch' => 'Update search',
      'updateCompanies' => 'Update company information',
      'updateUsers' => 'Update user accounts',
      'updateUserSessions' => 'Add interface support to user sessions',
      'updateApiSubscriptions' => 'Update API subscriptions',
      'updateAssignmentFilters' => 'Update assignment filters',
      'prepareActivityLogs' => 'Prepare activity logs for upgrade',
      'prepareCategoriesTable' => 'Prepare categories for upgrade',
      'prepareModificationLog' => 'Prepare modification log for upgrade',
      'prepareAssignments' => 'Prepare assignments for upgrade',
      'cleanUpOrphanedSubscriptions' => "Clean up orphaned subscriptions",
      'prepareSubscriptions' => 'Prepare subscriptions for upgrade',
      'prepareLabels' => 'Prepare labels for upgrade',
      'prepareFavorites' => 'Prepare favorites storage',
      'updateProjectGroups' => 'Convert project groups to project categories',
      'updateProjectLabels' => 'Update project status labels',
      'updateProjects' => 'Update projects',
      'updateProjectObjects' => 'Update project objects storage',
      'updateStateAndVisibility' => 'Update state and visibility values',
      'updateAttachments' => 'Update attachments storage',
      'updateSubtasks' => 'Update ticket, checklist and page subtasks',
      'updateComments' => 'Update comments storage',
      'updateMilestones' => 'Update project milestones',
      'updateTicketChanges' => 'Move ticket changes to modification log',
      'updateTickets' => 'Convert tickets to tasks',
      'updateChecklists' => 'Convert checklists to to do lists (if used)',
      'updatePageCategories' => 'Convert pages to notebooks',
      'updatePages' => 'Update pages',
      'updateFiles' => 'Update project files',
      'updateDiscussions' => 'Update project discussions',
      'updateTracking' => 'Update time tracking module',
      'updateTimeRecords' => 'Move time records to the new storage',

      // We need complete project objects table and updated type field

      'updateProjectPermissionNames' => 'Update project permission names',
      'updateFavorites' => 'Update pinned projects and starred objects',
      'updateReminders' => 'Update reminders',
      'backupTags' => 'Back up tag data',
      'setUpPayments' => 'Set up payment processing system',
      'updateInvoicing' => 'Update invoicing module',
      'updateInvoicingPayments' => 'Register existing payments with the new payment handling system',
      'updateDocuments' => 'Update documents',
      'updateDocumentHashes' => 'Update document hashes',
      'updatePublicSubmit' => 'Upgrade public submit module',
      'updateSourceModule' => 'Upgrade source module',
      'finalizeModuleUpgrade' => 'Finalize module upgrade',
      'rebuildLocalizationIndex' => 'Rebuild localization index',
      'prepareBodyBackup' => 'Prepare content backup storage',
      'upgradeProjectSummaries' => 'Update project summaries',
      'updateDocumentsContent' => 'Update content of global documents',
      'updateOpenTaskDescriptions' => 'Update open task descriptions',
      'updateCompletedTaskDescriptions' => 'Update completed task descriptions',
      'updateOpenTaskComments' => 'Update open task comments',
      'updateCompletedTaskComments' => 'Update completed task comments',
      'updateDiscussionDescriptions' => 'Update discussion descriptions',
      'updateDiscussionComments' => 'Update discussion comments',
      'updateNotebookPagesComments' => 'Update notebook pages comments',
      'updateFileDescriptions' => 'Update file descriptions',
      'updateFileComments' => 'Update file comments',
      'updateFirstLevelPageContent' => 'Update first level pages',
      'updateFirstLevelPageVersions' => 'Update first level page versions',
      'updateFirstLevelPageComments' => 'Update first level page comments',
      'updateSubpagesContent' => 'Update subpages',
      'updateSubpageVersions' => 'Update subpage versions',
      'updateSubpageComments' => 'Update subpage comments',
    );
  } // getActions

  // ---------------------------------------------------
  //  Refactored
  // ---------------------------------------------------

  /**
   * Update update_history table
   *
   * @return boolean
   */
  function updateUpdateHistoryTable() {
    try {
      $update_history_table = TABLE_PREFIX . 'update_history';

      DB::execute("ALTER TABLE $update_history_table CHANGE created_on created_on DATETIME NULL");
      DB::execute("ALTER TABLE $update_history_table ADD INDEX (created_on)");

      $versions = DB::execute("SELECT version, COUNT(id) AS 'row_count' FROM $update_history_table GROUP BY version");
      if($versions) {
        foreach($versions as $version) {
          $duplicates = (integer) $version['row_count'] - 1;

          if($duplicates > 0) {
            DB::execute("DELETE FROM $update_history_table WHERE version = ? ORDER BY created_on DESC LIMIT $duplicates", $version['version']);
          } // if
        } // foreach
      } // if

      DB::execute("ALTER TABLE $update_history_table ADD UNIQUE INDEX (version)");

      return true;
    } catch(Exception $e) {
      return $e->getMessage();
    } // try
  } // updateUpdateHistoryTable

  /**
   * Update modules table
   *
   * @return boolean
   */
  function updateModules() {
    $modules_table = TABLE_PREFIX . 'modules';

    try {
      DB::execute("ALTER TABLE $modules_table ADD is_enabled tinyint(1) NOT NULL DEFAULT '0' AFTER position");
      DB::execute("ALTER TABLE $modules_table DROP is_system");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateModules

  /**
   * Update project objects parent type value where it is missing
   *
   * @return boolean
   */
  function updateProjectObjectsParentType() {
    try {
      DB::beginWork('Updating project objects parent type @ ' . __CLASS__);

      $project_objects_table = TABLE_PREFIX . 'project_objects';

      $rows = DB::execute("SELECT parent_id FROM $project_objects_table WHERE parent_id > '0' AND (parent_type IS NULL OR parent_type = '')");
      if($rows) {
        $parent_ids = array();

        foreach($rows as $row) {
          $parent_ids[] = (integer) $row['parent_id'];
        } // foreach

        $rows = DB::execute("SELECT id, type FROM $project_objects_table WHERE id IN (?)", $parent_ids);
        if($rows) {
          $type_ids_map = array();

          foreach($rows as $row) {
            $type = $row['type'];

            if(isset($type_ids_map[$type])) {
              $type_ids_map[$type][] = $row['id'];
            } else {
              $type_ids_map[$type] = array($row['id']);
            } // if
          } // foreach

          foreach($type_ids_map as $type => $ids) {
            DB::execute("UPDATE $project_objects_table SET parent_type = ? WHERE parent_id IN (?)", $type, $ids);
          } // foreach
        } // if
      } // if

      DB::commit('Parent type values have been updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update parent type @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateProjectObjectsParentType

  /**
   * Update config options
   *
   * @return boolean
   */
  function updateConfigOptions() {
    try {
      $config_options_table = TABLE_PREFIX . 'config_options';
      $config_option_values_table = TABLE_PREFIX . 'config_option_values';

      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

      DB::execute("ALTER TABLE $config_options_table DROP type");
      DB::execute("CREATE TABLE $config_option_values_table (
          name varchar(50) not null default '',
          parent_type varchar(50) not null default '',
          parent_id int(10) unsigned not null default 0,
          value text,
          primary key (name, parent_type, parent_id),
          key parent (parent_type,parent_id)
        ) engine=$engine DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

      // Move configuration options to the new table
      $config_option_tables = array(
        TABLE_PREFIX . 'company_config_options' => 'Company',
        TABLE_PREFIX . 'project_config_options' => 'Project',
        TABLE_PREFIX . 'user_config_options' => 'User',
      );

      $all_tables = DB::listTables(TABLE_PREFIX);

      foreach($config_option_tables as $table_name => $parent_type) {
        if(in_array($table_name, $all_tables)) {
          $rows = DB::execute("SELECT * FROM $table_name");
          if($rows) {
            $to_insert = array();
            foreach($rows as $row) {
              $to_insert[] = '('  . DB::prepare("?, '$parent_type', ?, ?", $row['name'], (integer) array_shift($row), $row['value']) . ')';
            } // foreach
            DB::execute("INSERT INTO $config_option_values_table VALUES " . implode(', ', $to_insert));
          } // if

          DB::execute('DROP TABLE IF EXISTS ' . DB::escapeTableName($table_name));
        } // if
      } // foreach

      return true;
    } catch(Exception $e) {
      return $e->getMessage();
    } // try
  } // updateConfigOptions

  /**
   * Update system module configuration options
   *
   * @return boolean
   */
  function updateSystemModuleConfigOptions() {
    try {
      $config_options_table = TABLE_PREFIX . 'config_options';
      $currencies_table = TABLE_PREFIX . 'currencies';
      $tax_rates_table = TABLE_PREFIX . 'tax_rates';

      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

      // Globalization tables and settings
      DB::execute("CREATE TABLE " . TABLE_PREFIX . "day_offs (
          id int unsigned NOT NULL auto_increment,
          name varchar(100)  DEFAULT NULL,
          event_date date  DEFAULT NULL,
          repeat_yearly tinyint(1) unsigned not null default '0' NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          UNIQUE name (name)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      if(DB::tableExists($currencies_table)) {
        DB::execute("ALTER TABLE $currencies_table DROP default_rate");
      } else {
        DB::execute("CREATE TABLE " . TABLE_PREFIX . "currencies (
            id int unsigned NOT NULL auto_increment,
            name varchar(50)  DEFAULT NULL,
            code varchar(3)  DEFAULT NULL,
            is_default tinyint(1) unsigned not null default '0' NOT NULL DEFAULT '0',
            PRIMARY KEY (id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("INSERT INTO $currencies_table (name, code, is_default) VALUES (?, ?, ?), (?, ?, ?), (?, ?, ?), (?, ?, ?)",
          'Euro', 'EUR', false,
          'US Dollar', 'USD', true,
          'British Pound', 'GBP', false,
          'Japanese Yen', 'JPY', false
        );
      } // if

      if (DB::tableExists($tax_rates_table)) {
        $tax_rates = DB::execute("SELECT id, name FROM $tax_rates_table");

        if($tax_rates) {
          foreach($tax_rates as $tax_rate) {
            $original_tax_rate_name = $tax_rate['name'];

            $tax_rate_name = $original_tax_rate_name;
            $counter = 1;

            while(DB::executeFirstCell("SELECT COUNT(id) FROM $tax_rates_table WHERE name = ? AND id != ?", $tax_rate_name, $tax_rate['id'])) {
              $tax_rate_name = "$original_tax_rate_name $counter";
              $counter++;
            } // while

            if($tax_rate_name != $original_tax_rate_name) {
              DB::execute("UPDATE $tax_rates_table SET name = ? WHERE id = ?", $tax_rate_name, $tax_rate['id']);
            } // if
          } // foreach
        } else {
          DB::execute("INSERT INTO $tax_rates_table (name, percentage) VALUES (?, ?)", 'VAT', 17.50);
        } // if

        DB::execute("ALTER TABLE $tax_rates_table ADD UNIQUE INDEX (name)");
      } else {
        DB::execute("CREATE TABLE " . TABLE_PREFIX . "tax_rates (
					  id int(10) unsigned NOT NULL AUTO_INCREMENT,
					  name varchar(50) DEFAULT NULL,
					  percentage decimal(6,3) DEFAULT '0.000',
					  PRIMARY KEY (id),
					  UNIQUE KEY name (name)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("INSERT INTO $tax_rates_table (name, percentage) VALUES (?, ?)", 'VAT', 17.50);
      } //if

      // Desktop sets
      DB::execute("CREATE TABLE " . TABLE_PREFIX . "desktop_sets (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'DesktopSet',
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX parent (parent_type, parent_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "desktops (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'Desktop',
          desktop_set_id int(6) unsigned NOT NULL DEFAULT 0,
          name varchar(50)  DEFAULT NULL,
          position int(5) unsigned NOT NULL DEFAULT 0,
          raw_additional_properties longtext,
          PRIMARY KEY (id),
          INDEX type (type)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "desktop_widgets (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'DesktopWidget',
          desktop_id int(5) unsigned NOT NULL DEFAULT 0,
          column_id int(3) unsigned NOT NULL DEFAULT 0,
          position int(5) unsigned NOT NULL DEFAULT 0,
          raw_additional_properties longtext,
          PRIMARY KEY (id),
          INDEX type (type)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      // Define default desktop set
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "desktop_sets (type, parent_type, parent_id) VALUES ('DesktopSet', NULL, NULL)");
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "desktops (type, desktop_set_id, name, position) VALUES ('SplitDesktop', 1, 'Welcome', 1)");
      DB::execute('INSERT INTO ' . TABLE_PREFIX . "desktop_widgets (type, desktop_id, column_id, position) VALUES 
          ('RecentActivitiesDesktopWidget', 1, 1, 1), 
          ('SystemNotificationsDesktopWidget', 1, 2, 1), 
          ('FavoriteProjectsDesktopWidget', 1, 2, 2), 
          ('WhosOnlineDesktopWidget', 1, 2, 3)
        ");

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "code_snippets (
          id int unsigned NOT NULL auto_increment,
          parent_id int(11) unsigned NULL DEFAULT NULL,
          parent_type int(30) NULL DEFAULT NULL,
          syntax varchar(50)  DEFAULT NULL,
          body text,
          created_on datetime  DEFAULT NULL,
          created_by_id int unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          PRIMARY KEY (id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      // ---------------------------------------------------
      //  Update emailing
      // ---------------------------------------------------

      $incoming_mailboxes_table = TABLE_PREFIX . 'incoming_mailboxes';
      $incoming_mail_filters_table = TABLE_PREFIX . 'incoming_mail_filters';
      $incoming_mails_table = TABLE_PREFIX . 'incoming_mails';

      DB::execute('DROP TABLE ' . TABLE_PREFIX . 'email_templates, ' . TABLE_PREFIX . 'email_template_translations');

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "mailing_activity_logs (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'MailingActivityLog',
          direction enum('in', 'out') NOT NULL DEFAULT 'out',
          from_id int(10) unsigned NULL DEFAULT NULL,
          from_name varchar(100)  DEFAULT NULL,
          from_email varchar(150)  DEFAULT NULL,
          to_id int(10) unsigned NULL DEFAULT NULL,
          to_name varchar(100)  DEFAULT NULL,
          to_email varchar(150)  DEFAULT NULL,
          created_on datetime  DEFAULT NULL,
          raw_additional_properties longtext,
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX created_on (created_on)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      // Mailing
      DB::execute("CREATE TABLE " . TABLE_PREFIX . "outgoing_messages (
          id int unsigned NOT NULL auto_increment,
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          sender_id int(10) unsigned NULL DEFAULT NULL,
          sender_name varchar(100)  DEFAULT NULL,
          sender_email varchar(150)  DEFAULT NULL,
          recipient_id int(10) unsigned NOT NULL DEFAULT '0',
          recipient_name varchar(100)  DEFAULT NULL,
          recipient_email varchar(150)  DEFAULT NULL,
          subject varchar(255)  DEFAULT NULL,
          body longtext,
          context_id varchar(50)  DEFAULT NULL,
          mailing_method varchar(15) NOT NULL DEFAULT 'in_background',
          created_on datetime  DEFAULT NULL,
          send_retries int(5) unsigned NOT NULL DEFAULT 0,
          PRIMARY KEY (id),
          INDEX parent (parent_type, parent_id),
          INDEX recipient_id (recipient_id),
          INDEX created_on (created_on),
          INDEX recipient_email (recipient_email)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute("CREATE TABLE $incoming_mail_filters_table (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'IncomingMailFilter',
          name varchar(100)  DEFAULT NULL,
          description text,
          subject text,
          body text,
          priority varchar(200)  DEFAULT NULL,
          attachments varchar(200)  DEFAULT NULL,
          sender longtext,
          mailbox_id text,
          action_name varchar(100)  DEFAULT NULL,
          action_parameters longtext,
          position int(10) NOT NULL DEFAULT 0,
          is_enabled tinyint(1) unsigned NOT NULL DEFAULT '0',
          is_default tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          INDEX type (type), 
          INDEX name (name)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      // Update incoming mailboxes module
      if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . 'modules WHERE name = ?', 'incoming_mail') > 0) {

        // Prepare filters based on mailbox settings
        $mailboxes = DB::execute("SELECT * FROM $incoming_mailboxes_table");
        if($mailboxes) {
          foreach($mailboxes as $mailbox) {
            $mailbox_id = array(
              (integer) $mailbox['id'],
            );
            $name = "$mailbox[username]@$mailbox[host]";

            $action_type = $mailbox['object_type'] == 'discussion' ? 'IncomingMailDiscussionAction' : 'IncomingMailTaskAction';

            $action_parameters = array('project_id' => (integer) $mailbox['project_id']);

            if($mailbox['accept_anonymous']) {
              $action_parameters['allow_for_everyone'] = 'allow_for_people_who_can';
            } elseif($mailbox['accept_all_registered']) {
              $action_parameters['allow_for_everyone'] = 'allow_for_everyone';
            } // if

            DB::execute("INSERT INTO $incoming_mail_filters_table (name, mailbox_id, action_name, action_parameters, is_enabled) VALUES (?, ?, ?, ?, ?)", $name, serialize($mailbox_id), $action_type, serialize($action_parameters), true);
          } // foreach
        } // if

        DB::execute("ALTER TABLE " . TABLE_PREFIX . "incoming_mail_attachments ADD type VARCHAR(50) NOT NULL DEFAULT 'IncomingMailAttachment' AFTER id");

        DB::execute("ALTER TABLE $incoming_mailboxes_table CHANGE id id INT UNSIGNED NOT NULL AUTO_INCREMENT");
        DB::execute("ALTER TABLE $incoming_mailboxes_table CHANGE type server_type ENUM('POP3','IMAP') NOT NULL DEFAULT 'POP3'");
        DB::execute("ALTER TABLE $incoming_mailboxes_table ADD type VARCHAR(50) NOT NULL DEFAULT 'IncomingMailbox' AFTER id");
        DB::execute("ALTER TABLE $incoming_mailboxes_table DROP project_id");
        DB::execute("ALTER TABLE $incoming_mailboxes_table DROP object_type");
//          DB::execute("ALTER TABLE $incoming_mailboxes_table ADD name VARCHAR(100) NULL DEFAULT NULL AFTER id;");
//          DB::execute("ALTER TABLE $incoming_mailboxes_table ADD email VARCHAR(150) NULL DEFAULT NULL AFTER name");
//          DB::execute("ALTER TABLE $incoming_mailboxes_table DROP from_name");
//          DB::execute("ALTER TABLE $incoming_mailboxes_table DROP from_email");
        DB::execute("ALTER TABLE $incoming_mailboxes_table CHANGE from_name name VARCHAR(100) NULL DEFAULT NULL AFTER id");
        DB::execute("ALTER TABLE $incoming_mailboxes_table CHANGE from_email email VARCHAR(150) NULL DEFAULT NULL AFTER name");
        DB::execute("ALTER TABLE $incoming_mailboxes_table CHANGE enabled is_enabled TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
        DB::execute("ALTER TABLE $incoming_mailboxes_table ADD failure_attempts TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER is_enabled");
        DB::execute("ALTER TABLE $incoming_mailboxes_table DROP accept_all_registered");
        DB::execute("ALTER TABLE $incoming_mailboxes_table DROP accept_anonymous");

        DB::execute("ALTER TABLE $incoming_mails_table ADD type VARCHAR(50) NOT NULL DEFAULT 'IncomingMail'  AFTER id");
        DB::execute("ALTER TABLE $incoming_mails_table MODIFY COLUMN incoming_mailbox_id INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER type");
        DB::execute("ALTER TABLE $incoming_mails_table CHANGE incoming_mailbox_id incoming_mailbox_id SMALLINT(10) UNSIGNED NOT NULL DEFAULT '0'");
        DB::execute("ALTER TABLE $incoming_mails_table ADD is_replay_to_notification TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
        DB::execute("ALTER TABLE $incoming_mails_table DROP project_id");
        DB::execute("ALTER TABLE $incoming_mails_table ADD to_email TEXT NULL AFTER body");
        DB::execute("ALTER TABLE $incoming_mails_table ADD cc_to TEXT NULL AFTER to_email");
        DB::execute("ALTER TABLE $incoming_mails_table ADD bcc_to TEXT NULL AFTER cc_to");
        DB::execute("ALTER TABLE $incoming_mails_table ADD reply_to TEXT NULL AFTER bcc_to");
        DB::execute("ALTER TABLE $incoming_mails_table ADD priority VARCHAR(200) NULL DEFAULT NULL AFTER reply_to");
        DB::execute("ALTER TABLE $incoming_mails_table ADD additional_data LONGTEXT NULL AFTER priority");
        DB::execute("ALTER TABLE $incoming_mails_table DROP object_type");
        DB::execute("ALTER TABLE $incoming_mails_table MODIFY COLUMN created_on DATETIME DEFAULT NULL AFTER state");

        DB::execute('DROP TABLE ' . TABLE_PREFIX . 'incoming_mail_activity_logs');

        // Install incoming mailboxes module
      } else {
        DB::execute("CREATE TABLE " . TABLE_PREFIX . "incoming_mail_attachments (
            id int unsigned NOT NULL auto_increment,
            type varchar(50) NOT NULL DEFAULT 'IncomingMailAttachment',
            mail_id int(10) unsigned NULL DEFAULT NULL,
            temporary_filename varchar(255)  DEFAULT NULL,
            original_filename varchar(255)  DEFAULT NULL,
            content_type varchar(255)  DEFAULT NULL,
            file_size int(10) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            INDEX type (type)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE $incoming_mailboxes_table (
            id int unsigned NOT NULL auto_increment,
            name varchar(100)  DEFAULT NULL,
            email varchar(100)  DEFAULT NULL,
            mailbox varchar(100)  DEFAULT NULL,
            username varchar(50)  DEFAULT NULL,
            password varchar(50)  DEFAULT NULL,
            host varchar(255)  DEFAULT NULL,
            server_type enum('POP3', 'IMAP') NOT NULL DEFAULT 'POP3',
            type varchar(50) NOT NULL DEFAULT 'ApplicationObject',
            port int(10) unsigned NULL DEFAULT NULL,
            security enum('NONE', 'TLS', 'SSL') NOT NULL DEFAULT 'NONE',
            last_status int(3) unsigned NOT NULL DEFAULT '0',
            is_enabled tinyint(1) unsigned NOT NULL DEFAULT '0',
            failure_attempts int(3) NOT NULL DEFAULT '0',
            PRIMARY KEY (id),
            INDEX type (type)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE $incoming_mails_table (
            id int unsigned NOT NULL auto_increment,
            type varchar(50) NOT NULL DEFAULT 'IncomingMail',
            incoming_mailbox_id int(10) unsigned NULL DEFAULT NULL,
            parent_id int(10) NULL DEFAULT NULL,
            is_replay_to_notification tinyint(1) unsigned NOT NULL DEFAULT '0',
            subject varchar(255)  DEFAULT NULL,
            body text,
            to_email text,
            cc_to text,
            bcc_to text,
            reply_to text,
            priority varchar(200)  DEFAULT NULL,
            additional_data longtext,
            headers longtext,
            state tinyint(3) unsigned NOT NULL DEFAULT '0',
            original_state tinyint(3) unsigned NULL DEFAULT NULL,
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX type (type),
            INDEX incoming_mailbox_id (incoming_mailbox_id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
      } // if

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "access_logs (
          id bigint unsigned NOT NULL auto_increment,
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          accessed_by_id int(10) unsigned NULL DEFAULT NULL,
          accessed_by_name varchar(100)  DEFAULT NULL,
          accessed_by_email varchar(150)  DEFAULT NULL,
          accessed_on datetime  DEFAULT NULL,
          ip_address varchar(50)  DEFAULT NULL,
          is_download tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          INDEX parent (parent_type, parent_id),
          INDEX accessed_by_id (accessed_by_id),
          INDEX accessed_on (accessed_on)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "access_logs_archive (
          id bigint unsigned NOT NULL auto_increment,
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          accessed_by_id int(10) unsigned NULL DEFAULT NULL,
          accessed_by_name varchar(100)  DEFAULT NULL,
          accessed_by_email varchar(150)  DEFAULT NULL,
          accessed_on datetime  DEFAULT NULL,
          ip_address varchar(50)  DEFAULT NULL,
          is_download tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          INDEX parent (parent_type, parent_id),
          INDEX accessed_by_id (accessed_by_id),
          INDEX accessed_on (accessed_on)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "routing_cache (
          id int unsigned NOT NULL auto_increment,
          path_info varchar(255) DEFAULT NULL,
          name varchar(255) DEFAULT NULL,
          content text,
          last_accessed_on datetime  DEFAULT NULL,
          PRIMARY KEY (id),
          UNIQUE path_info (path_info)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "shared_object_profiles (
          id int unsigned NOT NULL auto_increment,
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int(10) unsigned NOT NULL DEFAULT '0',
          sharing_context varchar(50) NOT NULL,
          sharing_code varchar(100) NOT NULL,
          expires_on date  DEFAULT NULL,
          raw_additional_properties longtext,
          created_on datetime  DEFAULT NULL,
          created_by_id int unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          is_discoverable tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          UNIQUE sharing (sharing_context, sharing_code),
          UNIQUE parent (parent_type, parent_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      if(defined('LICENSE_PACKAGE') && LICENSE_PACKAGE == 'corporate') {
        if($this->isModuleInstalled('checklists')) {
          $project_tabs = array('outline', 'milestones', 'tasks', 'todo_lists', 'discussions', 'files', 'notebooks', 'time', 'source', 'calendar');
        } else {
          $project_tabs = array('outline', 'milestones', 'tasks', 'discussions', 'files', 'notebooks', 'time', 'source', 'calendar');
        } // if
      } else {
        $project_tabs = array('outline', 'milestones', 'todo_lists', 'discussions', 'files');
      } // if

      // Options
      $add_options = array(
        'first_run_on' => DB::executeFirstCell('SELECT MIN(created_on) FROM ' . TABLE_PREFIX . 'update_history'),
        'identity_name' => 'Projects',
        'project_tabs' => $project_tabs,
        'default_project_object_visibility' => 1,
        'first_milestone_starts_on' => null,
        'project_requests_enabled' => false,
        'project_requests_page_title' => 'Request a Project',
        'project_requests_page_description' => 'Please tell us more about your project',
        'project_requests_custom_fields' => array(
          'custom_field_1' => array('enabled' => true, 'name' => 'Budget'),
          'custom_field_2' => array('enabled' => true, 'name' => 'Time Frame'),
          'custom_field_3' => array('enabled' => false, 'name' => ''),
          'custom_field_4' => array('enabled' => false, 'name' => ''),
          'custom_field_5' => array('enabled' => false, 'name' => ''),
        ),
        'project_requests_captcha_enabled' => false,
        'project_requests_notify_user_ids' => null,
        'time_workdays' => array(1, 2, 3, 4, 5),
        'skip_days_off_when_rescheduling' => true,

        // Email
        'mailing_method' => 'instantly',
        'mailing_method_override' => false,

        // Text processing
        'whitelisted_tags' => array(
          'environment' => array(
            'a' => array('href', 'title', 'class', 'object-id', 'object-class'),
            'div' => array('class', 'placeholder-type', 'placeholder-object-id', 'placeholder-extra'),
            'img' => array('src', 'alt', 'title', 'class')
          ),
          'visual_editor' => array('img' => array('image-type', 'object-id', 'class')),
        ),
      );

      $to_insert = array();
      foreach($add_options as $k => $v) {
        $to_insert[] = DB::prepare("(?, 'system', ?)", $k, serialize($v));
      } // foreach

      DB::execute("INSERT INTO $config_options_table (name, module, value) VALUES " . implode(', ', $to_insert));
      DB::execute("DELETE FROM $config_options_table WHERE name IN (?)", array(
        'show_welcome_message',
        'projects_use_client_icons',
      ));

      return true;
    } catch(Exception $e) {
      return $e->getMessage();
    } // try
  } // updateSystemModuleConfigOptions

  /**
   * Update roles table
   */
  function updateRoles() {
    try {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

      $project_roles_table = TABLE_PREFIX . 'project_roles';
      $roles_table = TABLE_PREFIX . 'roles';
      $project_users_table = TABLE_PREFIX . 'project_users';
      $config_options_table = TABLE_PREFIX . 'config_options';

      DB::beginWork('Updating roles @ ' . __CLASS__);

      DB::execute("CREATE TABLE $project_roles_table (
          id smallint(3) unsigned NOT NULL auto_increment,
          name varchar(50) NOT NULL default '',
          permissions text,
          is_default tinyint(1) unsigned default NULL,
          PRIMARY KEY (id),
          UNIQUE KEY name (name)
        ) ENGINE=$engine DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

      $rows = DB::execute("SELECT * FROM $roles_table WHERE type = ?", 'project');
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $project_roles_table (name, permissions) VALUES (?, ?)", $row['name'], $row['permissions']);
          DB::execute("UPDATE $project_users_table SET role_id = ? WHERE role_id = ?", DB::lastInsertId(), $row['id']);
        } // foreach

        DB::execute("UPDATE $project_roles_table SET is_default = '1' LIMIT 1");
        DB::execute("DELETE FROM $roles_table WHERE type = ?", 'project');
      } // if

      // Update roles table
      DB::execute("ALTER TABLE $roles_table DROP COLUMN type");
      DB::execute("ALTER TABLE $roles_table MODIFY COLUMN permissions text DEFAULT NULL AFTER name");
      DB::execute("ALTER TABLE $roles_table ADD is_default tinyint(1) UNSIGNED NULL DEFAULT NULL AFTER permissions");

      // Update default role ID
      $default_role_id = (integer) DB::executeFirstCell('SELECT value FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', 'default_role');
      if($default_role_id) {
        DB::execute("UPDATE $roles_table SET is_default = ? WHERE id = ?", true, (integer) unserialize($default_role_id));
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', 'default_role');
      } // if

      // Rename system permissions
      $rename_system_permissions = array(
        'system_access' => 'has_system_access',
        'admin_access' => 'has_admin_access',
        'project_management' => 'can_manage_projects',
        'people_management' => 'can_manage_people',
        'add_project' => 'can_add_project',
        'manage_company_details' => 'can_manage_company_details',
        'manage_trash' => 'can_manage_trash',
        'manage_assignment_filters' => 'can_manage_assignment_filters',
      );

      $rows = DB::execute("SELECT id, permissions FROM $roles_table");
      foreach($rows as $row) {
        $permissions = $row['permissions'] ? unserialize($row['permissions']) : array();

        foreach($rename_system_permissions as $k => $v) {
          if(isset($permissions[$k])) {
            $permissions[$v] = $permissions[$k];
            unset($permissions[$k]);
          } // if
        } // foreach

        if(array_var($permissions, 'has_admin_access') || array_var($permissions, 'can_manage_projects') || array_var($permissions, 'can_manage_people')) {
          $permissions['can_use_api'] = true;
          $permissions['can_use_feeds'] = true;
        } // if

        DB::execute("UPDATE $roles_table SET permissions = ? WHERE id = ?", serialize($permissions), $row['id']);
      } // foreach

      // ---------------------------------------------------
      //  Set default role
      // ---------------------------------------------------

      $default_role_id = DB::executeFirstCell("SELECT value FROM $config_options_table WHERE name = 'default_role'");
      if($default_role_id) {
        $default_role_id = unserialize($default_role_id);
      } // if

      if($default_role_id) {
        DB::execute("UPDATE $roles_table SET is_default = ? WHERE id = ?", true, $default_role_id);
      } // if

      if(DB::executeFirstCell("SELECT COUNT(id) FROM $roles_table WHERE is_default = ?", true) == 0) {
        DB::execute("UPDATE $roles_table SET is_default = ? LIMIT 1", true);
      } // if

      DB::execute("DELETE FROM $config_options_table WHERE name = 'default_role'");

      // ---------------------------------------------------
      //  Update role names
      // ---------------------------------------------------

      DB::execute("UPDATE $roles_table SET name = 'Employee' WHERE name = 'Member'");
      DB::execute("UPDATE $roles_table SET name = 'Client Company Employee' WHERE name = 'Client Company Member'");

      DB::commit('Roles updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update roles @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateRoles

  /**
   * Update languages
   *
   * @return boolean
   */
  function updateLanguages() {
    try {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
      $languages_table = TABLE_PREFIX . 'languages';

      DB::execute("ALTER TABLE $languages_table ADD last_updated_on DATETIME NULL DEFAULT NULL AFTER locale");

      if(DB::executeFirstCell("SELECT COUNT(id) FROM $languages_table WHERE locale = ?", 'en_US.UTF-8') == 0) {
        DB::execute("INSERT INTO $languages_table (name, locale, last_updated_on) VALUES (?, ?, UTC_TIMESTAMP())", 'English (United States)', 'en_US.UTF-8');
      } // if

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "language_phrases (
          id int unsigned NOT NULL auto_increment,
          phrase text,
          PRIMARY KEY (id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "language_phrase_translations (
          language_id int(11) unsigned NULL DEFAULT NULL,
          phrase_id int(11) unsigned NULL DEFAULT NULL,
          translation text,
          PRIMARY KEY (language_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute("UPDATE $languages_table SET last_updated_on = UTC_TIMESTAMP()");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateLanguages

  /**
   * Update search indices
   */
  function updateSearch() {
    try {
      DB::execute("DROP TABLE " . TABLE_PREFIX . "search_index");
      DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES ('search_initialized_on', 'system', ?), ('search_provider', 'system', ?)", serialize(null), serialize('MySqlSearchProvider'));
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateSearch

  /**
   * Update company information
   *
   * @return boolean
   */
  function updateCompanies() {
    $companies_table = TABLE_PREFIX . 'companies';

    try {
      DB::execute("ALTER TABLE $companies_table ADD created_by_id smallint(5) unsigned not null default '0' after created_on");
      DB::execute("ALTER TABLE $companies_table ADD created_by_name varchar(100) null after created_by_id");
      DB::execute("ALTER TABLE $companies_table ADD created_by_email VARCHAR(100) null after created_by_name");
      DB::execute("ALTER TABLE $companies_table ADD updated_by_id smallint(5) unsigned not null default '0' after updated_on");
      DB::execute("ALTER TABLE $companies_table ADD updated_by_name varchar(100) null after updated_by_id");
      DB::execute("ALTER TABLE $companies_table ADD updated_by_email VARCHAR(100) null after updated_by_name");
      DB::execute("ALTER TABLE $companies_table ADD state tinyint unsigned not null default '0' after id");
      DB::execute("ALTER TABLE $companies_table ADD original_state tinyint unsigned default null after state");

      list($admin_user_id, $admin_display_name, $admin_email_address) = $this->getFirstAdministrator();

      try {
        DB::execute("UPDATE $companies_table SET created_by_id = ?, created_by_name = ?, created_by_email = ?", $admin_user_id, $admin_display_name, $admin_email_address);
        DB::execute("UPDATE $companies_table SET updated_by_id = ?, updated_by_name = ?, updated_by_email = ? WHERE updated_on", $admin_user_id, $admin_display_name, $admin_email_address);

        DB::execute("UPDATE $companies_table SET state = ? WHERE is_archived = ?", 3, false);
        DB::execute("UPDATE $companies_table SET state = ?, original_state = ? WHERE is_archived = ?", 2, 3, true);

        DB::execute("ALTER TABLE $companies_table DROP is_archived");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateCompanies

  /**
   * Update user information
   *
   * @return boolean
   */
  function updateUsers() {
    $roles_table = TABLE_PREFIX . 'roles';
    $users_table = TABLE_PREFIX . 'users';

    try {
      DB::execute("ALTER TABLE $users_table ADD created_by_id smallint(5) unsigned not null default '0' AFTER created_on");
      DB::execute("ALTER TABLE $users_table ADD created_by_name varchar(100) null AFTER created_by_id");
      DB::execute("ALTER TABLE $users_table ADD created_by_email VARCHAR(100) null AFTER created_by_name");
      DB::execute("ALTER TABLE $users_table ADD updated_by_id smallint(5) unsigned not null default '0' AFTER updated_on");
      DB::execute("ALTER TABLE $users_table ADD updated_by_name varchar(100) null AFTER updated_by_id");
      DB::execute("ALTER TABLE $users_table ADD updated_by_email VARCHAR(100) null AFTER updated_by_name");
      DB::execute("ALTER TABLE $users_table ADD desktop_set_id int(5) unsigned NULL DEFAULT 0 AFTER company_id");
      DB::execute("ALTER TABLE $users_table ADD INDEX (role_id)");
      DB::execute("ALTER TABLE $users_table ADD INDEX (last_activity_on)");

      list($admin_user_id, $admin_display_name, $admin_email_address) = $this->getFirstAdministrator();

      DB::execute("UPDATE $users_table SET created_by_id = ?, created_by_name = ?, created_by_email = ?", $admin_user_id, $admin_display_name, $admin_email_address);
      DB::execute("UPDATE $users_table SET updated_by_id = ?, updated_by_name = ?, updated_by_email = ? where updated_on", $admin_user_id, $admin_display_name, $admin_email_address);

      DB::execute("ALTER TABLE $users_table ADD state tinyint unsigned not null default '0' after id");
      DB::execute("ALTER TABLE $users_table ADD original_state tinyint unsigned default null after state");

      DB::execute("UPDATE $users_table SET state = ?", 3);
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateUsers

  /**
   * Update user sessions
   *
   * @return boolean
   */
  function updateUserSessions() {
    try {
      DB::execute("ALTER TABLE " . TABLE_PREFIX . "user_sessions ADD interface enum('default', 'phone', 'tablet') NOT NULL DEFAULT 'default'");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateUserSessions

  /**
   * Initialize API subscriptions storage
   *
   * @return boolean
   */
  function updateApiSubscriptions() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      $subscriptions_table = TABLE_PREFIX . 'api_client_subscriptions';
      $users_table = TABLE_PREFIX . 'users';

      DB::execute("CREATE TABLE $subscriptions_table (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'ApiClientSubscription',
          user_id int(10) unsigned NOT NULL DEFAULT '0',
          token varchar(40)  DEFAULT NULL,
          client_name varchar(100)  DEFAULT NULL,
          client_vendor varchar(100)  DEFAULT NULL,
          created_on datetime  DEFAULT NULL,
          last_used_on datetime  DEFAULT NULL,
          is_enabled tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          INDEX type (type),
          UNIQUE token (token)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::beginWork('Moving token data to new API subscriptions table @ ' . __CLASS__);

      $rows = DB::execute("SELECT id, token, created_on FROM $users_table");
      if($rows) {
        foreach($rows as $row) {
          if($row['token']) {
            DB::execute("INSERT INTO $subscriptions_table (type, user_id, token, client_name, client_vendor, created_on, is_enabled) VALUES ('ApiClientSubscription', ?, ?, ?, ?, ?, '1')", $row['id'], $row['token'], 'activeCollab Legacy Token', 'A51', $row['created_on']);
          } // if
        } // foreach
      } // if

      DB::commit('Token data moved to new API subscriptions table @ ' . __CLASS__);

      DB::execute("ALTER TABLE $users_table DROP token");
    } catch(Exception $e) {
      DB::rollback('Failed to move token data to new API subscriptions table @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateApiSubscriptions

  /**
   * Update assignment filters
   *
   * @return boolean
   */
  function updateAssignmentFilters() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      $assignment_filters_table = TABLE_PREFIX . 'assignment_filters';

      DB::execute("CREATE TABLE {$assignment_filters_table}_2 (
          id int unsigned NOT NULL auto_increment,
          name varchar(50)  DEFAULT NULL,
          raw_additional_properties longtext,
          created_on datetime  DEFAULT NULL,
          created_by_id int(10) unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          is_private tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          INDEX created_by_id (created_by_id),
          INDEX name (name)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      $rows = DB::execute("SELECT * FROM $assignment_filters_table");
      if($rows) {
        $created_by_ids = $created_by = array();

        foreach($rows as $row) {
          $created_by_id = (integer) $row['created_by_id'];

          if($created_by_id && !in_array($created_by_id, $created_by_ids)) {
            $created_by_ids[] = $created_by_id;
          } // if
        } // foreach

        if(count($created_by_ids)) {
          $user_rows = DB::execute('SELECT id, first_name, last_name, email FROM ' . TABLE_PREFIX . 'users WHERE id IN (?)', $created_by_ids);

          if($user_rows) {
            foreach($user_rows as $user_row) {
              $created_by_id = (integer) $user_row['id'];

              $created_by[$created_by_id] = array(
                'email' => $user_row['email']
              );

              if($user_row['first_name'] && $user_row['last_name']) {
                $created_by[$created_by_id]['name'] = $user_row['first_name'] . ' ' . $user_row['last_name'];
              } elseif($user_row['first_name']) {
                $created_by[$created_by_id]['name'] = $user_row['first_name'];
              } elseif($user_row['last_name']) {
                $created_by[$created_by_id]['name'] = $user_row['last_name'];
              } else {
                $created_by[$created_by_id]['name'] = substr($user_row['email'], 0, strpos($user_row['email'], '@'));
              } // if
            } // foreach
          } // if
        } // if

        foreach($rows as $row) {
          $name = isset($row['group_name']) && $row['group_name'] ? "$row[group_name] / $row[name]" : $row['name'];

          $attributes = array(
            'user_filter' => $row['user_filter'],
            'project_filter' => $row['project_filter'],
            'due_on_filter' => $row['date_filter'],
            'created_on_filter' => $row['created_on_filter'],
            'completed_on_filter' => $row['completed_on_filter'],
          );

          if($attributes['completed_on_filter'] == 'any')

            if($row['user_filter'] == 'company' || $row['user_filter'] == 'company_responsible') {
              $attributes['company_id'] = isset($row['user_filter_data']) && $row['user_filter_data'] ? unserialize($row['user_filter_data']) : 0;
            } elseif($row['user_filter'] == 'company' || $row['user_filter'] == 'selected_responsible') {
              $attributes['selected_users'] = isset($row['user_filter_data']) && $row['user_filter_data'] ? unserialize($row['user_filter_data']) : array();
            } // if

          if($row['project_filter'] == 'selected') {
            $attributes['project_ids'] = isset($row['project_filter_data']) && $row['project_filter_data'] ? unserialize($row['project_filter_data']) : array();
          } // if

          if($row['date_filter'] == 'selected_date') {
            $attributes['due_on'] = $row['date_from'];
          } elseif($row['date_filter'] == 'selected_range') {
            $attributes['due_from'] = $row['date_from'];
            $attributes['due_to'] = $row['date_from'];
          } // if

          if($row['created_on_filter'] == 'selected_date') {
            $attributes['created_on'] = $row['created_on_from'];
          } elseif($row['created_on_filter'] == 'selected_range') {
            $attributes['created_from'] = $row['created_on_from'];
            $attributes['created_to'] = $row['created_on_to'];
          } // if

          if($row['completed_on_filter'] == 'selected_date') {
            $attributes['completed_on'] = $row['completed_on_from'];
          } elseif($row['completed_on_filter'] == 'selected_range') {
            $attributes['completed_from'] = $row['completed_on_from'];
            $attributes['completed_to'] = $row['completed_on_to'];
          } elseif($row['completed_on_filter'] == 'all'){
            if($row['status_filter'] == 'active') {
              $attributes['completed_on_filter'] = 'open';
            } elseif($row['status_filter'] == 'completed') {
              $attributes['completed_on_filter'] = 'completed';
            } // if
          } // if

          if(str_starts_with($row['order_by'], 'due_on')) {
            $attributes['group_by'] = 'due_on';
          } elseif(str_starts_with($row['order_by'], 'created_on')) {
            $attributes['group_by'] = 'created_on';
          } else {
            $attributes['group_by'] = 'dont';
          } // if

          if($row['tickets_only']) {
            $attributes['include_subtasks'] = false;
          } else {
            $attributes['include_subtasks'] = true;
          } // if

          $created_by_id = (integer) $row['created_by_id'];

          if($created_by[$created_by_id]) {
            $created_by_name = $created_by[$created_by_id]['name'];
            $created_by_email = $created_by[$created_by_id]['email'];
          } else {
            $created_by_id = 0;
            $created_by_name = '';
            $created_by_email = 'unknown@activecollab.com';
          } // if

          DB::execute("INSERT INTO {$assignment_filters_table}_2 (name, raw_additional_properties, created_on, created_by_id, created_by_name, created_by_email, is_private) VALUES (?, ?, NOW(), ?, ?, ?, ?)", $name, serialize($attributes), $created_by_id, $created_by_name, $created_by_email, $row['is_private']);
        } // foreach

        DB::execute("DROP TABLE $assignment_filters_table");
        DB::execute("RENAME TABLE {$assignment_filters_table}_2 TO $assignment_filters_table");
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateAssignmentFilters

  /**
   * Update activity logs
   *
   * @return boolean
   */
  function prepareActivityLogs() {
    $activity_logs_table = TABLE_PREFIX . 'activity_logs';
    $project_objects_table = TABLE_PREFIX . 'project_objects';

    try {
      DB::execute("ALTER TABLE $activity_logs_table CHANGE object_id parent_id int(11) unsigned not null default '0'");
      DB::execute("ALTER TABLE $activity_logs_table DROP project_id");
      DB::execute("ALTER TABLE $activity_logs_table DROP comment");
      DB::execute("ALTER TABLE $activity_logs_table ADD parent_type varchar(50) not null default '' after type");
      DB::execute("ALTER TABLE $activity_logs_table ADD raw_additional_properties longtext null default null after created_by_email");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    try {
      DB::beginWork('Updating parent_type field value for activity logs');

      // We don't need this in activity log... Irrelevant
      DB::execute("DELETE FROM $activity_logs_table WHERE type IN (?)", array(
        'CommentsLockedActivityLog',
        'CommentsUnlockedActivityLog',
        'ObjectUpdatedActivityLog',
        'TicketStatusUpdatedActivityLog', // @TODO
      ));

      $parent_ids = DB::executeFirstColumn("SELECT DISTINCT parent_id FROM $activity_logs_table");
      if(is_foreachable($parent_ids)) {
        $project_object_rows = DB::execute("SELECT id, type FROM $project_objects_table WHERE id IN (?)", $parent_ids);

        if(is_foreachable($project_object_rows)) {
          $type_map = array();
          foreach($project_object_rows as $row) {
            if(!isset($type_map[$row['type']])) {
              $type_map[$row['type']] = array();
            } // if

            $type_map[$row['type']][] = (integer) $row['id'];
          } // foreach

          foreach($type_map as $type => $ids) {
            DB::execute("UPDATE $activity_logs_table SET parent_type = ? WHERE parent_id IN (?)", $type, $ids);
          } // foreach
        } // if
      } // if

      // Clean up
      DB::execute("DELETE FROM $activity_logs_table WHERE parent_type = ''");

      DB::commit('Parent type value for activity logs updated');
    } catch(Exception $e) {
      DB::rollback('Failed to update parent type activity logs');
      return $e->getMessage();
    } // try

    try {
      DB::execute("ALTER TABLE $activity_logs_table ADD INDEX (parent_type, parent_id)");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // prepareActivityLogs

  /**
   * Prepare categories table that'll be used by other category upgrade steps
   *
   * @return boolean
   */
  function prepareCategoriesTable() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "categories (
          id int(11) unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL default 'Category',
          parent_type varchar(50) default NULL,
          parent_id int(10) unsigned default NULL,
          name varchar(100) NOT NULL default '',
          created_on datetime default NULL,
          created_by_id int(10) unsigned NOT NULL default '0',
          created_by_name varchar(100) default NULL,
          created_by_email varchar(150) default NULL,
          PRIMARY KEY (id),
          KEY parent (parent_type,parent_id),
          KEY type (type)
        ) ENGINE=$engine DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

      DB::execute("ALTER TABLE $project_objects_table ADD category_id int UNSIGNED NULL DEFAULT NULL AFTER parent_type");
      DB::execute("ALTER TABLE $project_objects_table ADD INDEX category_id (category_id)");

      // Category creation should not be displayed in activity log
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'activity_logs WHERE type = ? AND parent_type = ?', 'ObjectCreatedActivityLog', 'category');
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // prepareCategoriesTable

  /**
   * Prepare modification log
   *
   * @return boolean
   */
  function prepareModificationLog() {
    try {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

      DB::execute("CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "modification_logs (
          id bigint(20) unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL default 'ApplicationObjectModification',
          parent_type varchar(50) NOT NULL default '',
          parent_id int(11) unsigned NOT NULL default '0',
          created_on datetime default NULL,
          created_by_id int(10) unsigned NOT NULL default '0',
          created_by_name varchar(100) default NULL,
          created_by_email varchar(150) default NULL,
          is_first tinyint(1) NOT NULL default '0',
          PRIMARY KEY (id),
          KEY parent_type (parent_type,parent_id),
          KEY created_on (created_on)
        ) ENGINE=$engine DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

      DB::execute("CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "modification_log_values (
          modification_id bigint(20) unsigned NOT NULL default '0',
          field varchar(50) NOT NULL default '',
          value longtext,
          PRIMARY KEY (modification_id,field)
        ) ENGINE=$engine DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // prepareModificationLog

  /**
   * Update assignment data
   *
   * @return boolean
   */
  function prepareAssignments() {
    try {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $assignments_table = TABLE_PREFIX . 'assignments';

      DB::execute("ALTER TABLE $assignments_table ADD parent_type VARCHAR(50) NOT NULL FIRST");
      DB::execute("ALTER TABLE $assignments_table CHANGE object_id parent_id INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER parent_type");
      DB::execute("ALTER TABLE $project_objects_table ADD assignee_id int(10) unsigned NULL DEFAULT NULL AFTER parent_id");
      DB::execute("ALTER TABLE $project_objects_table ADD delegated_by_id int(10) unsigned NULL DEFAULT NULL AFTER assignee_id");
      DB::execute("ALTER TABLE $project_objects_table ADD INDEX (assignee_id)");
      DB::execute("ALTER TABLE $project_objects_table ADD INDEX (delegated_by_id)");

      DB::beginWork('Updating assignment data @ ' . __CLASS__);

      $rows = DB::execute("SELECT DISTINCT $project_objects_table.id, $project_objects_table.type FROM $project_objects_table, $assignments_table WHERE $project_objects_table.id = $assignments_table.parent_id");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("UPDATE $assignments_table SET parent_type = ? WHERE parent_id = ?", $row['type'], $row['id']);
        } // foreach
      } // if

      // Clean up records that don't have parent type set (there's no corresponding project object)
      DB::execute("DELETE FROM $assignments_table WHERE parent_type = ''");

      // Move assignee ID to project_objects table
      $rows = DB::execute("SELECT user_id, parent_id FROM $assignments_table WHERE is_owner = ?", true);
      if($rows) {
        foreach($rows as $row) {
          DB::execute("UPDATE $project_objects_table SET assignee_id = ? WHERE id = ?", $row['user_id'], $row['parent_id']);
        } // foreach

        DB::execute("DELETE FROM $assignments_table WHERE is_owner = ?", true);
      } // if

      DB::execute("UPDATE $project_objects_table SET delegated_by_id = created_by_id WHERE assignee_id IS NOT NULL AND assignee_id != created_by_id");

      DB::commit('Assignment data updated @ ' . __CLASS__);

      DB::execute("ALTER TABLE $assignments_table DROP is_owner");

      // Recreate indexes (we needed them to speed up type update)
      DB::execute("ALTER TABLE $assignments_table DROP INDEX object_id");
      DB::execute("ALTER TABLE $assignments_table DROP PRIMARY KEY");

      DB::execute("ALTER TABLE $assignments_table ADD PRIMARY KEY (parent_type, parent_id, user_id)");
      DB::execute("ALTER TABLE $assignments_table ADD INDEX (user_id)");
    } catch(Exception $e) {
      DB::rollback('Failed to update assignment data @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // prepareAssignments

  /**
   * Clean up any possible subscription entries for users that do not exist in the system any more
   */
  function cleanUpOrphanedSubscriptions() {
    $existing_user_ids = DB::executeFirstColumn("SELECT id FROM " . TABLE_PREFIX . "users");
    if (is_foreachable($existing_user_ids)) {
      try {
        DB::execute("DELETE FROM " . TABLE_PREFIX . "subscriptions WHERE user_id NOT IN (?)", $existing_user_ids);
      } catch (Exception $e) {
        return $e->getMessage();
      } // try
    } // if - actually, else should die('your aC is WRONG!')

    return true;
  } // cleanUpOrphanedSubscriptions

  /**
   * Prepare subscriptions
   *
   * @return boolean
   */
  function prepareSubscriptions() {
    $subscriptions_table = TABLE_PREFIX . 'subscriptions';
    $users_table = TABLE_PREFIX . 'users';
    $project_objects_table = TABLE_PREFIX . 'project_objects';

    // Lets drop primary key and add new fields
    $transformations = array(
      "ALTER TABLE $subscriptions_table DROP PRIMARY KEY",
      "ALTER TABLE $subscriptions_table ADD id INT UNSIGNED NOT NULL DEFAULT '0' FIRST",
      "ALTER TABLE $subscriptions_table ADD parent_type VARCHAR(50) NOT NULL DEFAULT '' AFTER id",
      "ALTER TABLE $subscriptions_table CHANGE user_id user_id INT(10) UNSIGNED NOT NULL DEFAULT '0'",
      "ALTER TABLE $subscriptions_table CHANGE parent_id parent_id INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER parent_type",
      "ALTER TABLE $subscriptions_table ADD user_name VARCHAR(100) NULL DEFAULT NULL AFTER user_id",
      "ALTER TABLE $subscriptions_table ADD user_email VARCHAR(150) NULL DEFAULT NULL AFTER user_name",
      "ALTER TABLE $subscriptions_table ADD subscribed_on DATETIME NULL DEFAULT NULL AFTER user_email",
      "ALTER TABLE $subscriptions_table ADD code VARCHAR(10) NOT NULL DEFAULT '' AFTER subscribed_on",
      "ALTER TABLE $subscriptions_table ADD INDEX (user_id)",
      "ALTER TABLE $subscriptions_table ADD INDEX (parent_id)",
    );

    foreach($transformations as $transformation) {
      DB::execute($transformation);
    } // foreach

    try {
      DB::beginWork('Updating subscriptions table data');

      // Populate user fields
      $rows = DB::execute("SELECT DISTINCT $users_table.id, $users_table.first_name, $users_table.last_name, $users_table.email FROM $users_table, $subscriptions_table WHERE $users_table.id = $subscriptions_table.user_id");
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $first_name = trim($row['first_name']) && trim($row['last_name']) ? trim($row['first_name']) . ' ' . trim($row['last_name']) : $row['email'];
          DB::execute("UPDATE $subscriptions_table SET user_name = ?, user_email = ? WHERE user_id = ?", $first_name, $row['email'], $row['id']);
        } // foreach
      } // if

      // Populate parent_type field
      $rows = DB::execute("SELECT $project_objects_table.id, $project_objects_table.type FROM $project_objects_table, $subscriptions_table WHERE $project_objects_table.id = $subscriptions_table.parent_id");
      if(is_foreachable($rows)) {
        $by_type = array();

        foreach($rows as $row) {
          $type = $row['type'];

          if($type == '') {
            continue;
          } // if

          if(isset($by_type[$type])) {
            $by_type[$type][] = $row['id'];
          } else {
            $by_type[$type] = array($row['id']);
          } // if
        } // foreach

        if(count($by_type)) {
          foreach($by_type as $type => $ids) {
            $smaller_arrays = array_chunk($ids, 1000);
            foreach ($smaller_arrays as $value) {
              DB::execute("UPDATE $subscriptions_table SET parent_type = ? WHERE parent_id IN (?)", $type, $value);
            } //foreach
          } // foreach
        } // if
      } // if

      DB::execute("DELETE FROM $subscriptions_table WHERE parent_type = ''");

      $rows = DB::execute("SELECT user_id, parent_id FROM $subscriptions_table");
      if(is_foreachable($rows)) {
        $counter = 1;
        foreach($rows as $row) {
          DB::execute("UPDATE $subscriptions_table SET id = ?, code = ? WHERE user_id = ? AND parent_id = ?", $counter, make_string(10), $row['user_id'], $row['parent_id']);
          $counter++;
        } // foreach
      } // if

      DB::execute("UPDATE $subscriptions_table SET subscribed_on = UTC_TIMESTAMP()");

      DB::commit('Subscriptions table data update');
    } catch(Exception $e) {
      DB::rollback('Failed to update subscriptions table data');

      throw $e;
    } // try

    // Drop temp index
    DB::execute("ALTER TABLE $subscriptions_table DROP INDEX parent_id");

    // Add unique index that ensures that there are no duplicate subscriptions
    DB::execute("ALTER TABLE $subscriptions_table ADD PRIMARY KEY (id)");
    DB::execute("ALTER TABLE $subscriptions_table CHANGE id id INT UNSIGNED NOT NULL AUTO_INCREMENT");
    DB::execute("ALTER TABLE $subscriptions_table ADD INDEX parent(parent_type, parent_id)");
    DB::execute("ALTER TABLE $subscriptions_table ADD UNIQUE INDEX user_subscribed(user_email, parent_type, parent_id)");
    DB::execute("ALTER TABLE $subscriptions_table ADD INDEX (subscribed_on)");

    return true;
  } // prepareSubscriptions

  /**
   * Prepare labels storage
   *
   * @return boolean
   */
  function prepareLabels() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      $labels_table = TABLE_PREFIX . 'labels';

      DB::execute("CREATE TABLE $labels_table (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'Label',
          name varchar(10)  DEFAULT NULL,
          is_default tinyint(1) unsigned not null default '0' NOT NULL DEFAULT '0',
          raw_additional_properties longtext,
          PRIMARY KEY (id),
          INDEX type (type),
          UNIQUE name (name, type)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      $white = '#FFFFFF';
      $black = '#000000';
      $red = '#FF0000';
      $green = '#00A651';
      $blue = '#0000FF';
      $yellow = '#FFFF00';
      $orange = '#F26522';
      $grey = '#ACACAC';

      $labels_by_type = array(
        'AssignmentLabel' => array(
          array('NEW', $black, $yellow),
          array('CONFIRMED', $white, $orange),
          array('WORKS4ME', $white, $green),
          array('DUPLICATE', $white, $green),
          array('WONTFIX', $white, $green),
          array('ASSIGNED', $white, $red),
          array('BLOCKED', $black, $grey),
          array('INPROGRESS', $black, $yellow),
          array('FIXED', $white, $blue),
          array('REOPENED', $white, $red),
          array('VERIFIED', $white, $green),
        ),
        'ProjectLabel' => array(
          array('NEW', $black, $yellow),
          array('INPROGRESS', $white, $green),
          array('CANCELED', $white, $red),
          array('PAUSED', $white, $blue),
        )
      );

      foreach($labels_by_type as $type => $labels) {
        foreach($labels as $label) {
          list($label_name, $fg_color, $bg_color) = $label;

          DB::execute("INSERT INTO $labels_table (type, name, raw_additional_properties) VALUES (?, ?, ?)", $type, $label_name, serialize(array('fg_color' => $fg_color, 'bg_color' => $bg_color)));
        } // foreach
      } // foreach

      DB::execute("UPDATE $labels_table SET is_default = ? WHERE type = ? AND name = ?", true, 'AssignmentLabel', 'NEW');
      DB::execute("UPDATE $labels_table SET is_default = ? WHERE type = ? AND name = ?", true, 'ProjectLabel', 'NEW');
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // prepareLabels

  /**
   * Prepare favorites storage
   *
   * @return boolean
   */
  function prepareFavorites() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      DB::execute("CREATE TABLE " . TABLE_PREFIX . "favorites (
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          user_id int(10) unsigned NULL DEFAULT NULL,
          UNIQUE favorite_object (parent_type, parent_id, user_id),
          INDEX user_id (user_id)
        ) ENGINE=$engine DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // prepareFavorites

  /**
   * Update project groups
   *
   * @return boolean
   */
  function updateProjectGroups() {
    $projects_table = TABLE_PREFIX . 'projects';
    $project_groups_table = TABLE_PREFIX . 'project_groups';
    $categories_table = TABLE_PREFIX . 'categories';
    $config_options_table = TABLE_PREFIX . 'config_options';

    try {
      $templates_group_id = DB::executeFirstCell("SELECT value FROM $config_options_table WHERE name = ?", 'project_templates_group');
      $templates_group_id = $templates_group_id ? (integer) unserialize($templates_group_id) : 0;

      $templates_category_id = 0;

      DB::execute("ALTER TABLE $projects_table ADD category_id int(10) unsigned null default null AFTER group_id");

      $rows = DB::execute("SELECT id, name FROM $project_groups_table");
      if($rows) {
        list($admin_user_id, $admin_display_name, $admin_email_address) = $this->getFirstAdministrator();

        foreach($rows as $row) {
          DB::execute("INSERT INTO $categories_table (type, name, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, UTC_TIMESTAMP(), ?, ?, ?)", 'ProjectCategory', $row['name'], $admin_user_id, $admin_display_name, $admin_email_address);

          $category_id = DB::lastInsertId();

          DB::execute("UPDATE $projects_table SET category_id = ? WHERE group_id = ?", $category_id, $row['id']);

          if($row['id'] == $templates_group_id) {
            $templates_category_id = $category_id;
          } // if
        } // foreach
      } // if

      DB::execute("UPDATE $config_options_table SET name = ?, value = ? WHERE name = ?", 'project_templates_category', serialize($templates_category_id), 'project_templates_group');
      DB::execute("ALTER TABLE $projects_table DROP group_id");
      DB::execute("ALTER TABLE $projects_table ADD INDEX (company_id)");
      DB::execute("DROP TABLE $project_groups_table");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateProjectGroups

  /**
   * Update project labels
   */
  function updateProjectLabels() {
    $projects_table = TABLE_PREFIX . 'projects';
    $labels_table = TABLE_PREFIX . 'labels';

    try {
      DB::execute("ALTER TABLE $projects_table ADD label_id smallint unsigned null default null AFTER category_id");

      $paused_label_id = DB::executeFirstCell("SELECT id FROM $labels_table WHERE type = ? AND name = ?", 'ProjectLabel', 'PAUSED');
      $canceled_label_id = DB::executeFirstCell("SELECT id FROM $labels_table WHERE type = ? AND name = ?", 'ProjectLabel', 'CANCELED');

      $rows = DB::execute("SELECT id, status FROM $projects_table");
      if($rows) {
        foreach($rows as $row) {
          $label_id = null;
          if($row['status'] == 'paused') {
            $label_id = $paused_label_id;
          } elseif($row['status'] == 'canceled') {
            $label_id = $canceled_label_id;
          } // if

          DB::execute("UPDATE $projects_table SET label_id = ? WHERE id = ?", $label_id, $row['id']);
        } // foreach
      } // if

      DB::execute("ALTER TABLE $projects_table DROP status");
      DB::execute("ALTER TABLE $projects_table ADD INDEX (label_id)");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateProjectLabels

  /**
   * Update projects table
   *
   * @return boolean
   */
  function updateProjects() {
    $projects_table = TABLE_PREFIX . 'projects';
    $project_groups_table = TABLE_PREFIX . 'project_groups';
    $categories_table = TABLE_PREFIX . 'categories';

    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      DB::execute("ALTER TABLE $projects_table ADD slug varchar(50) not null default '' AFTER id");
      DB::execute("ALTER TABLE $projects_table ADD template_id INT(10) unsigned null default null AFTER slug");
      DB::execute("ALTER TABLE $projects_table ADD based_on_type varchar(50) DEFAULT NULL AFTER template_id");
      DB::execute("ALTER TABLE $projects_table ADD based_on_id int(10) unsigned NULL DEFAULT 0 AFTER based_on_type");
      DB::execute("ALTER TABLE $projects_table ADD currency_id smallint unsigned null default null AFTER label_id");
      DB::execute("ALTER TABLE $projects_table ADD budget decimal(12, 2) unsigned  DEFAULT 0 AFTER currency_id");
      DB::execute("ALTER TABLE $projects_table ADD state tinyint unsigned not null default '0' AFTER label_id");
      DB::execute("ALTER TABLE $projects_table ADD original_state tinyint unsigned null default null AFTER state");
      DB::execute("ALTER TABLE $projects_table CHANGE leader_id leader_id int(10) unsigned not null default 0");
      DB::execute("ALTER TABLE $projects_table ADD updated_by_id int unsigned NULL DEFAULT 0 AFTER updated_on");
      DB::execute("ALTER TABLE $projects_table ADD updated_by_name varchar(100)  DEFAULT NULL AFTER updated_by_id");
      DB::execute("ALTER TABLE $projects_table ADD updated_by_email varchar(150)  DEFAULT NULL AFTER updated_by_name");
      DB::execute("ALTER TABLE $projects_table DROP open_tasks_count");
      DB::execute("ALTER TABLE $projects_table DROP total_tasks_count");
      DB::execute("ALTER TABLE $projects_table DROP starts_on");
      DB::execute("ALTER TABLE $projects_table DROP type");
      DB::execute("ALTER TABLE $projects_table ADD INDEX (leader_id)");
      DB::execute("ALTER TABLE $projects_table ADD INDEX (category_id)");

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "project_requests (
          id int unsigned NOT NULL auto_increment,
          public_id varchar(32) NOT NULL,
          name varchar(150)  DEFAULT NULL,
          body text,
          status int(4) NOT NULL DEFAULT '0',
          created_on datetime  DEFAULT NULL,
          created_by_id int unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          created_by_company_name varchar(100) NOT NULL,
          custom_field_1 text,
          custom_field_2 text,
          custom_field_3 text,
          custom_field_4 text,
          custom_field_5 text,
          is_locked tinyint(1) unsigned NOT NULL DEFAULT '0',
          taken_by_id int(10) unsigned NULL DEFAULT NULL,
          taken_by_name varchar(100)  DEFAULT NULL,
          taken_by_email varchar(150)  DEFAULT NULL,
          closed_on datetime  DEFAULT NULL,
          closed_by_id int unsigned NULL DEFAULT NULL,
          closed_by_name varchar(100)  DEFAULT NULL,
          closed_by_email varchar(150)  DEFAULT NULL,
          last_comment_on datetime  DEFAULT NULL,
          PRIMARY KEY (id),
          INDEX taken_by_id (taken_by_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try


    try {
      DB::beginWork('Updating projects @ ' . __CLASS__);

      // Client ID
      $owner_company_id = DB::executeFirstCell('SELECT id FROM ' . TABLE_PREFIX . 'companies ORDER BY is_owner DESC LIMIT 0, 1');
      $company_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'companies');

      DB::execute("UPDATE $projects_table SET company_id = ? WHERE company_id NOT IN (?)", $owner_company_id, $company_ids);

      // Default visibility
      $rows = DB::execute("SELECT id FROM $projects_table WHERE default_visibility < '1'");
      if($rows) {
        foreach($rows as $row) {
          DB::execute('INSERT INTO ' . TABLE_PREFIX . "config_option_values (name, parent_type, parent_id, value) VALUES ('default_project_object_visibility', 'Project', ?, ?)", $row['id'], 'i:0;');
        } // foreach
      } // if

      // Update projects
      $rows = DB::execute("SELECT id, name FROM $projects_table");
      if(is_foreachable($rows)) {
        foreach($rows as $row) {

          // Prepare slug
          $slug = trim(Inflector::slug($row['name']), '-');

          $strlen = strlen_utf($slug);
          if($strlen && is_numeric($slug)) {
            $slug = 'project-' . $row['id'];
          } elseif($strlen > 40) {
            $slug = trim(substr_utf($slug, 0, 40), '-');
          } elseif($strlen < 1) {
            $slug = 'project-' . $row['id'];
          } // if

          $original_slug = $slug;
          $counter = 1;

          while(DB::executeFirstCell("SELECT COUNT(id) FROM $projects_table WHERE slug = ?", $slug) > 0) {
            $slug = $original_slug . '-' . $counter++;
          } // while

          DB::execute("UPDATE $projects_table SET slug = ?, state = ? WHERE id = ?", $slug, 3, $row['id']);
        } // foreach
      } // if

      DB::commit('Projects updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update projects @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    try {
      DB::execute("ALTER TABLE $projects_table DROP default_visibility");
      DB::execute("ALTER TABLE $projects_table ADD UNIQUE (slug)");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateProjects

  /**
   * Update project objects table
   *
   * @return boolean
   */
  function updateProjectObjects() {
    try {
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      DB::execute("ALTER TABLE $project_objects_table ADD label_id SMALLINT UNSIGNED NULL DEFAULT NULL AFTER category_id");
      DB::execute("ALTER TABLE $project_objects_table ADD INDEX (label_id)");
      DB::execute("ALTER TABLE $project_objects_table DROP has_time");
      DB::execute("ALTER TABLE $project_objects_table DROP comments_count");
      DB::execute("ALTER TABLE $project_objects_table ADD varchar_field_3 VARCHAR(255)  NULL  DEFAULT NULL  AFTER varchar_field_2");
      DB::execute("ALTER TABLE $project_objects_table ADD integer_field_3 INT(11)  NULL  DEFAULT NULL  AFTER integer_field_2");
      DB::execute("ALTER TABLE $project_objects_table ADD float_field_3 FLOAT  NULL  DEFAULT NULL  AFTER float_field_2");
      DB::execute("ALTER TABLE $project_objects_table ADD text_field_3 LONGTEXT  NULL  AFTER text_field_2");
      DB::execute("ALTER TABLE $project_objects_table ADD date_field_3 DATE  NULL  DEFAULT NULL  AFTER date_field_2");
      DB::execute("ALTER TABLE $project_objects_table ADD datetime_field_3 DATETIME  NULL  DEFAULT NULL  AFTER datetime_field_2");
      DB::execute("ALTER TABLE $project_objects_table ADD boolean_field_3 TINYINT(1)  UNSIGNED  NULL  DEFAULT NULL  AFTER boolean_field_2");

      DB::execute('DROP TABLE ' . TABLE_PREFIX . 'project_object_views');
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateProjectObjects

  /**
   * Update project objects table
   *
   * @return boolean
   */
  function updateStateAndVisibility() {
    $project_objects_table = TABLE_PREFIX . 'project_objects';
    $activity_logs_table = TABLE_PREFIX . 'activity_logs';

    try {
      DB::execute("ALTER TABLE $project_objects_table CHANGE state state tinyint(3) unsigned not null default '0'");
      DB::execute("ALTER TABLE $project_objects_table ADD original_state tinyint(3) unsigned null default null after state");
      //DB::execute("ALTER TABLE $project_objects_table CHANGE visibility visibility tinyint(3) UNSIGNED NOT NULL DEFAULT '0'");
      DB::execute("ALTER TABLE $project_objects_table CHANGE visibility visibility tinyint(3) NOT NULL DEFAULT '0'"); // Keep the field as UNSIGNED, so confidentiality module can continue to work
      DB::execute("ALTER TABLE $project_objects_table ADD original_visibility tinyint UNSIGNED NULL DEFAULT NULL AFTER visibility");

      DB::execute("UPDATE $project_objects_table SET original_state = ? WHERE state = ?", 3, 1); // Set original state to visible for trashed objects
      //DB::execute("UPDATE $project_objects_table SET visibility = ? WHERE visibility IS NOT NULL AND visibility < ?", 0, 0); // Reset all invalid visibility values

      DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type = ?", 'MovedToTrashActivityLog', 'ObjectTrashedActivityLog');
      DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type = ?", 'RestoredFromTrashActivityLog', 'ObjectRestoredActivityLog');
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateStateAndVisibility

  /**
   * Make sure that attachments are up to date
   *
   * @return boolean
   */
  function updateAttachments() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      $attachments_table = TABLE_PREFIX . 'attachments';
      $file_versions_table = TABLE_PREFIX . 'file_versions';
      $text_document_versions_table = TABLE_PREFIX . 'text_document_versions';

      if ($this->isModuleInstalled('files')) {
        DB::execute("CREATE TABLE $file_versions_table (
            file_id int(10) unsigned NOT NULL DEFAULT '0',
            version_num int(5) unsigned NOT NULL DEFAULT '0',
            name varchar(255)  DEFAULT NULL,
            mime_type varchar(255) NOT NULL DEFAULT 'application/octet-stream',
            size int(10) unsigned NOT NULL DEFAULT 0,
            location varchar(50)  DEFAULT NULL,
            md5 varchar(32)  DEFAULT NULL,
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            PRIMARY KEY (file_id, version_num)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE $text_document_versions_table (
            text_document_id int(10) unsigned NOT NULL DEFAULT '0',
            version_num int(5) unsigned NOT NULL DEFAULT '0',
            name varchar(255)  DEFAULT NULL,
            body longtext,
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            PRIMARY KEY (text_document_id, version_num)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
      } //if

      DB::execute("ALTER TABLE $attachments_table ADD type varchar(50) not null default 'Attachment' AFTER id");
      DB::execute("ALTER TABLE $attachments_table CHANGE parent_type parent_type varchar(50) null default null AFTER type");
      DB::execute("ALTER TABLE $attachments_table ADD state tinyint unsigned not null default '0' AFTER parent_id");
      DB::execute("ALTER TABLE $attachments_table ADD original_state tinyint(3) unsigned null default null AFTER state");
      DB::execute("ALTER TABLE $attachments_table ADD visibility tinyint unsigned not null default '0' AFTER parent_id");
      DB::execute("ALTER TABLE $attachments_table ADD original_visibility tinyint(3) unsigned null default null AFTER visibility");
      DB::execute("ALTER TABLE $attachments_table ADD raw_additional_properties longtext null default null AFTER created_by_email");
      DB::execute("ALTER TABLE $attachments_table CHANGE mime_type mime_type varchar(255) not null default 'application/octet-stream'");
      DB::execute("ALTER TABLE $attachments_table ADD md5 VARCHAR(32) NULL DEFAULT NULL AFTER location");

      DB::beginWork('Update attachments @ ' . __CLASS__);

      $parent_ids = DB::executeFirstColumn("SELECT DISTINCT parent_id FROM $attachments_table WHERE attachment_type = 'file_revision'");

      if($parent_ids) {
        foreach($parent_ids as $parent_id) {
          $counter = 0;

          $rows = DB::execute("SELECT id, name, mime_type, size, location, created_on, created_by_id, created_by_name, created_by_email FROM $attachments_table WHERE attachment_type = 'file_revision' AND parent_id = ? ORDER BY created_on", $parent_id);
          if($rows) {
            foreach($rows as $row) {
              $counter++;

              $mime_type = empty($row['mime_type']) ? 'application/octet-stream' : $row['mime_type'];

              if($counter == $rows->count()) {
                DB::execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET integer_field_1 = ?, integer_field_2 = ?, varchar_field_1 = ?, varchar_field_2 = ?, datetime_field_1 = ? WHERE type = ? AND id = ?', $rows->count(), $row['size'], $mime_type, $row['location'], $row['created_on'], 'File', $parent_id);
              } else {
                DB::execute("INSERT INTO $file_versions_table (file_id, version_num, name, mime_type, size, location, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", $parent_id, $counter, $row['name'], $mime_type, $row['size'], $row['location'], $row['created_on'], $row['created_by_id'], $row['created_by_name'], $row['created_by_email']);
                DB::execute('UPDATE ' . TABLE_PREFIX . 'activity_logs SET type = ?, parent_type = ?, raw_additional_properties = ? WHERE type = ? AND parent_id = ?', 'FileVersionUploadedActivityLog', 'File', serialize(array('version_num' => $counter)), 'NewFileVersionActivityLog', $parent_id);
              } // if
            } // if
          } // if
        } // foreach

        DB::execute("DELETE FROM $attachments_table WHERE attachment_type = 'file_revision'");
      } // if

      DB::execute("ALTER TABLE $attachments_table DROP attachment_type");

      // Update state field values
      $rows = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE state = ?', 1); // Old trashed items
      if($rows) {
        DB::execute("UPDATE $attachments_table SET state = ?, original_state = ? WHERE parent_id IN (?)", 1, 3, $rows);
      } // if

      DB::execute("UPDATE $attachments_table SET state = ? WHERE state = ?", 3, 0);

      DB::commit('Attachments updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update attachments @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateAttachments

  /**
   * Updates substasks data
   *
   * @return boolean
   */
  function updateSubtasks() {
    try {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

      $subtasks_table = TABLE_PREFIX . 'subtasks';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $activity_logs_table = TABLE_PREFIX . 'activity_logs';
      $subscriptions_table = TABLE_PREFIX . 'subscriptions';

      DB::execute("CREATE TABLE $subtasks_table (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'Subtask',
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          label_id int(5) unsigned NULL DEFAULT NULL,
          assignee_id int(10) unsigned NULL DEFAULT NULL,
          delegated_by_id int(10) unsigned NULL DEFAULT NULL,
          priority int(4) NULL DEFAULT NULL,
          body longtext,
          due_on date  DEFAULT NULL,
          state tinyint(3) unsigned NOT NULL DEFAULT '0',
          original_state tinyint(3) unsigned NULL DEFAULT NULL,
          visibility tinyint(3) unsigned NOT NULL DEFAULT '0',
          original_visibility tinyint(3) unsigned NULL DEFAULT NULL,
          created_on datetime  DEFAULT NULL,
          created_by_id int unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          completed_on datetime  DEFAULT NULL,
          completed_by_id int unsigned NULL DEFAULT NULL,
          completed_by_name varchar(100)  DEFAULT NULL,
          completed_by_email varchar(150)  DEFAULT NULL,
          position int(10) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX parent (parent_type, parent_id),
          INDEX parent_type (parent_type, parent_id),
          INDEX created_on (created_on),
          INDEX position (position),
          INDEX completed_on (completed_on),
          INDEX due_on (due_on),
          INDEX assignee_id (assignee_id),
          INDEX delegated_by_id (delegated_by_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::beginWork('Moving data from project objects table to subtasks table');

      $rows = DB::execute("SELECT id, parent_type, parent_id, assignee_id, delegated_by_id, state, visibility, priority, body, due_on, created_on, created_by_id, created_by_name, created_by_email, completed_on, completed_by_id, completed_by_name, completed_by_email, position FROM $project_objects_table WHERE type = 'Task'");
      if($rows) {
        $tracking_is_installed = (boolean) DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'timetracking'");

        $new_data = array();

        foreach($rows as $row) {
          if(empty($row['parent_type'])) {
            continue;
          } // if

          if($row['state'] > 1) {
            $state = 3; // Visible
            $original_state = null;
          } else {
            $state = 1; // Trashed
            $original_state = 3;
          } // if

          // override: tasks added to pages do not exist any more
          if (strtolower($row['parent_type']) == "page") {
            $state = 0;
            $original_state = 3;
          } // if

          DB::execute("INSERT INTO $subtasks_table (type, parent_type, parent_id, assignee_id, delegated_by_id, state, original_state, priority, body, due_on, visibility, original_visibility, created_on, created_by_id, created_by_name, created_by_email, completed_on, completed_by_id, completed_by_name, completed_by_email, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'ProjectObjectSubtask', $row['parent_type'], (integer) $row['parent_id'], $row['assignee_id'], $row['delegated_by_id'], $state, $original_state, (integer) $row['priority'], $row['body'], $row['due_on'], (integer) $row['visibility'], (integer) $row['visibility'], $row['created_on'], (integer) $row['created_by_id'], $row['created_by_name'], $row['created_by_email'], $row['completed_on'], (integer) $row['completed_by_id'], $row['completed_by_name'], $row['completed_by_email'], (integer) $row['position']
          );

          $new_data[(integer) $row['id']] = array(
            'subtask_id' => DB::lastInsertId(),
            'parent_type' => $row['parent_type'],
            'parent_id' => (integer) $row['parent_id'],
          );
        } // foreach

        foreach($new_data as $id => $data) {
          DB::execute("UPDATE $subscriptions_table SET parent_type = 'ProjectObjectSubtask', parent_id = ? WHERE parent_type = 'Task' AND parent_id = ?", $data['subtask_id'], $id);

          DB::execute("UPDATE $activity_logs_table SET type = ?, parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE type IN (?) AND parent_type = ? AND parent_id = ?", 'SubtaskCreatedActivityLog', $data['parent_type'], $data['parent_id'], serialize(array('subtask_id' => $data['subtask_id'])), array('NewTaskActivityLog', 'ObjectCreatedActivityLog'), 'Task', $id);
          DB::execute("UPDATE $activity_logs_table SET type = ?, parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE type = ? AND parent_type = ? AND parent_id = ?", 'SubtaskCompletedActivityLog', $data['parent_type'], $data['parent_id'], serialize(array('subtask_id' => $data['subtask_id'])), 'TaskCompletedActivityLog', 'Task', $id);
          DB::execute("UPDATE $activity_logs_table SET type = ?, parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE type = ? AND parent_type = ? AND parent_id = ?", 'SubtaskReopenedActivityLog', $data['parent_type'], $data['parent_id'], serialize(array('subtask_id' => $data['subtask_id'])), 'TaskReopenedActivityLog', 'Task', $id);

          if($tracking_is_installed) {
            DB::execute("UPDATE $project_objects_table SET parent_type = 'ProjectObjectSubtask', parent_id = ? WHERE type = 'TimeRecord' AND parent_type = 'Task' AND parent_id = ?", $data['subtask_id'], $id);
          } // if
        } // foreach

        DB::execute("DELETE FROM $project_objects_table WHERE type = ?", 'Task');
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ?', 'Task');
      } // if

      DB::commit('Moved data from project objects table to subtasks table');
    } catch(Exception $e) {
      DB::rollback('Failed to move data from project objects table to subtasks table');
      return $e->getMessage();
    } // try

    return true;
  } // updateSubtasks

  /**
   * Create comments table and migrate the data from project objects table
   *
   * @return boolean
   */
  function updateComments() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      $comments_table = TABLE_PREFIX . 'comments';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $activity_logs_table = TABLE_PREFIX . 'activity_logs';

      DB::execute("CREATE TABLE $comments_table (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'Comment',
          source varchar(50)  DEFAULT NULL,
          parent_type varchar(50)  DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          body longtext,
          state tinyint(3) unsigned NOT NULL DEFAULT '0',
          original_state tinyint(3) unsigned NULL DEFAULT NULL,
          visibility tinyint(3) unsigned NOT NULL DEFAULT '0',
          original_visibility tinyint(3) unsigned NULL DEFAULT NULL,
          created_on datetime  DEFAULT NULL,
          created_by_id int unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          updated_on datetime  DEFAULT NULL,
          updated_by_id int unsigned NULL DEFAULT NULL,
          updated_by_name varchar(100)  DEFAULT NULL,
          updated_by_email varchar(150)  DEFAULT NULL,
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX parent (parent_type, parent_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::beginWork('Migrate comments from project objects table');

      $new_data = array();

      $comments = DB::execute("SELECT id, source, parent_type, parent_id, body, state, visibility, created_on, created_by_id, created_by_name, created_by_email FROM $project_objects_table WHERE type = ?", 'Comment');
      if(is_foreachable($comments)) {
        foreach($comments as $comment) {
          if($comment['state'] > 1) {
            $state = 3; // Visible
            $original_state = null;
          } else {
            $state = 1; // Trashed
            $original_state = 3;
          } // if

          DB::execute("INSERT INTO $comments_table (source, parent_type, parent_id, body, state, original_state, visibility, original_visibility, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            $comment['source'], $comment['parent_type'], $comment['parent_id'], $comment['body'], $state, $original_state, $comment['visibility'], null, $comment['created_on'], $comment['created_by_id'], $comment['created_by_name'], $comment['created_by_email']
          );

          $new_data[(integer) $comment['id']] = array(
            'comment_id'  => DB::lastInsertId(),
            'parent_type' => $comment['parent_type'],
            'parent_id' => $comment['parent_id'],
          );
        } // foreach

        foreach($new_data as $id => $data) {
          DB::execute('UPDATE ' . TABLE_PREFIX . 'attachments SET parent_id = ? WHERE parent_type = ? AND parent_id = ?', $data['comment_id'], 'Comment', $id);
          DB::execute("UPDATE $activity_logs_table SET type = ?, parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE type IN (?) AND parent_type = ? AND parent_id = ?", 'CommentCreatedActivityLog', $data['parent_type'], $data['parent_id'], serialize(array('comment_id' => $data['comment_id'])), array('NewCommentActivityLog', 'ObjectCreatedActivityLog'), 'Comment', $id);
          DB::execute("UPDATE $activity_logs_table SET parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE parent_type = ? AND parent_id = ?", $data['parent_type'], $data['parent_id'], serialize(array('comment_id' => $data['comment_id'])), 'Comment', $id);
        } // foreach

        DB::execute("DELETE FROM $project_objects_table WHERE type = ?", 'Comment');
      } // if

      DB::commit('Comments migrated from project objects table');
    } catch(Exception $e) {
      DB::rollback('Failed to migrate comments from project objects table');
      return $e->getMessage();
    } // try

    return true;
  } // updateComments

  /**
   * Update milestones
   *
   * @return boolean
   */
  function updateMilestones() {
    try {
      $activity_logs_table = TABLE_PREFIX . 'activity_logs';

      DB::execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET type = ? WHERE type = ?', 'Milestone', 'milestone');
      DB::execute('UPDATE ' . TABLE_PREFIX . 'assignments SET parent_type = ? WHERE parent_type = ?', 'Milestone', 'milestone');

      // Update activity logs
      DB::execute("UPDATE $activity_logs_table SET parent_type = ? WHERE parent_type = ?", 'Milestone', 'milestone');

      DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type IN (?) AND parent_type = ?", 'MilestoneCreatedActivityLog', array('NewTaskActivityLog', 'ObjectCreatedActivityLog'), 'Milestone');
      DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type = ? AND parent_type = ?", 'AssignmentCompletedActivityLog', 'TaskCompletedActivityLog', 'Milestone');
      DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type = ? AND parent_type = ?", 'AssignmentReopenedActivityLog', 'TaskReopenedActivityLog', 'Milestone');
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateMilestones

  /**
   * Move ticket changes to modification log
   *
   * @return boolean
   */
  function updateTicketChanges() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='tickets'")) {
        DB::beginWork('Updating ticket categories and changes @ ' . __CLASS__);

        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $categories_table = TABLE_PREFIX . 'categories';
        $ticket_changes_table = TABLE_PREFIX . 'ticket_changes';

        // Move ticket categories from project objects table to categories 
        // table and then drop them. Remember the map for modification log 
        // update
        $categories_map = array();

        $categories = DB::execute("SELECT id, project_id, name, created_on, created_by_id, created_by_name, created_by_email FROM $project_objects_table WHERE module = ? AND type = ?", 'tickets', 'Category');
        if(is_foreachable($categories)) {
          $ids = array();

          foreach($categories as $category) {
            $old_category_id = (integer) $category['id'];

            DB::execute("INSERT INTO $categories_table (type, parent_type, parent_id, name, created_on, created_by_id, created_by_name, created_by_email) VALUES ('TaskCategory', 'Project', ?, ?, ?, ?, ?, ?)", $category['project_id'], $category['name'], $category['created_on'], $category['created_by_id'], $category['created_by_name'], $category['created_by_email']);
            $categories_map[$old_category_id] = DB::lastInsertId();

            DB::execute("UPDATE $project_objects_table SET parent_type = ?, parent_id = ?, category_id = ? WHERE type = ? AND parent_id = ?", null, null, $categories_map[$old_category_id], 'Ticket', $old_category_id);
          } // foreach

          // Drop old category records from project_objects table
          if(count($categories_map)) {
            DB::execute("DELETE FROM $project_objects_table WHERE id IN (?)", array_keys($categories_map));
          } // if
        } // if

        // Update modification log
        $tickets = DB::execute("SELECT id, project_id, milestone_id, category_id, parent_id, name, body, priority, due_on, completed_on, state, visibility, created_on, created_by_id, created_by_name, created_by_email FROM $project_objects_table WHERE type = ?", 'Ticket');
        if(is_foreachable($tickets)) {
          $modification_logs_table = TABLE_PREFIX . 'modification_logs';
          $modification_log_values_table = TABLE_PREFIX . 'modification_log_values';

          foreach($tickets as $ticket) {
            $changes = DB::execute("SELECT changes, created_on, created_by_id, created_by_name, created_by_email FROM $ticket_changes_table WHERE ticket_id = ? ORDER BY created_on DESC", $ticket['id']);

            // We have changes recorded for this ticket
            if(is_foreachable($changes)) {
              $modified_fields = array(); // Lets remember fields that we have modified

              foreach($changes as $change) {
                $field_changes = unserialize($change['changes']);

                if(is_array($field_changes)) {

                  // Clean up planning / ticket+ change entries
                  if(array_key_exists('assignees', $field_changes)) {
                    unset($field_changes['assignees']);
                  } // if

                  if(array_key_exists('workflow_status', $field_changes)) {
                    unset($field_changes['workflow_status']);
                  } // if

                  if(array_key_exists('estimate', $field_changes)) {
                    unset($field_changes['estimate']);
                  } // if

                  // If we have changes, insert an entry
                  if(count($changes) > 0) {
                    DB::execute("INSERT INTO $modification_logs_table (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email, is_first) VALUES (?, ?, ?, ?, ?, ?, ?)", 'Task', $ticket['id'], $change['created_on'], $change['created_by_id'], $change['created_by_name'], $change['created_by_email'], false);

                    $modification_log_id = DB::lastInsertId();

                    $to_insert = array();

                    foreach($field_changes as $field => $v) {
                      if(is_array($v) && isset($v[0]) && isset($v[1])) {
                        list($old_value, $new_value) = $v;

                        $modified_fields[$field] = $old_value;
                        if($field == 'parent_id') {
                          $to_insert[] = DB::prepare("($modification_log_id, 'category_id', ?)", array_var($categories_map, $new_value));
                        } elseif($field == 'owner') {
                          $to_insert[] = DB::prepare("($modification_log_id, 'assignee_id', ?)", $new_value);
                        } else {
                          $to_insert[] = DB::prepare("($modification_log_id, ?, ?)", $field, (string) $new_value);
                        } // if
                      } // if
                    } // foreach

                    if(count($to_insert)) {
                      DB::execute("INSERT INTO $modification_log_values_table (modification_id, field, value) VALUES " . implode(', ', $to_insert));
                    } // if
                  } // if
                } // if
              } // foreach

              // Lets create initial modification log
              DB::execute("INSERT INTO $modification_logs_table (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email, is_first) VALUES (?, ?, ?, ?, ?, ?, ?)", 'Task', $ticket['id'], $ticket['created_on'], $ticket['created_by_id'], $ticket['created_by_name'], $ticket['created_by_email'], true);

              $modification_log_id = DB::lastInsertId();

              $to_insert = array();

              $track_fields = array('project_id', 'milestone_id', 'category_id', 'name', 'body', 'priority', 'due_on', 'completed_on', 'state', 'visibility');
              foreach($track_fields as $track_field) {
                $value = isset($modified_fields[$track_field]) ? $modified_fields[$track_field] : $ticket[$track_field];
                if($value) {
                  if($track_field == 'parent_id') {
                    $to_insert[] = DB::prepare("($modification_log_id, ?, ?)", 'category_id', array_var($categories_map, $value));
                  } else {
                    $to_insert[] = DB::prepare("($modification_log_id, ?, ?)", $track_field, $value);
                  } // if
                } // if
              } // foreach

              if(count($to_insert)) {
                DB::execute("INSERT INTO $modification_log_values_table (modification_id, field, value) VALUES " . implode(', ', $to_insert));
              } // if

              // There are no changes for this ticket, create just initial modification
            } else {
              DB::execute("INSERT INTO $modification_logs_table (type, parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email, is_first) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 'TaskModificationLog', 'Task', $ticket['id'], $ticket['created_on'], $ticket['created_by_id'], $ticket['created_by_name'], $ticket['created_by_email'], true);

              $modification_log_id = DB::lastInsertId();

              $to_insert = array();
              foreach($ticket as $k => $v) {
                if($k == 'id' || $k == 'created_on' || $k == 'created_by_id' || $k == 'created_by_name' || $k == 'created_by_email') {
                  continue;
                } // if

                if($v) {
                  if($k == 'parent_id') {
                    $to_insert[] = DB::prepare('(?, ?, ?)', $modification_log_id, 'category_id', array_var($categories_map, $v));
                  } else {
                    $to_insert[] = DB::prepare('(?, ?, ?)', $modification_log_id, $k, $v);
                  } // if
                } // if
              } // foreach

              if(count($to_insert)) {
                DB::execute("INSERT INTO $modification_log_values_table (modification_id, field, value) VALUES " . implode(', ', $to_insert));
              } // if
            } // if
          } // foreach
        } // if

        DB::commit('Ticket categories and changes updated @ ' . __CLASS__);

        DB::execute("DROP TABLE $ticket_changes_table");
      } // if
    } catch(Exception $e) {
      DB::rollback('Failed to update ticket categories and changes @ ' . __CLASS__);

      return $e->getMessage();
    } // try

    return true;
  } // updateTicketChanges

  /**
   * Update tickets
   *
   * @return boolean
   */
  function updateTickets() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='tickets'")) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $activity_logs_table = TABLE_PREFIX . 'activity_logs';
        $comments_table = TABLE_PREFIX . 'comments';
        $attachments_table = TABLE_PREFIX . 'attachments';

        // Update fields and references
        DB::execute('UPDATE ' . TABLE_PREFIX . 'config_options SET name = ?, module = ? WHERE name = ?', 'task_categories', 'tasks', 'ticket_categories');
        DB::execute('UPDATE ' . TABLE_PREFIX . 'modules SET name = ? WHERE name = ?', 'tasks', 'tickets');
        DB::execute("UPDATE $project_objects_table SET module = ?, type = ? WHERE type = ?", 'tasks', 'Task', 'Ticket');
        DB::execute("UPDATE $project_objects_table SET parent_type = ? WHERE parent_type = ?", 'Task', 'Ticket');
        DB::execute('UPDATE ' . TABLE_PREFIX . 'subscriptions SET parent_type = ? WHERE parent_type = ?', 'Task', 'Ticket');
        DB::execute('UPDATE ' . TABLE_PREFIX . 'assignments SET parent_type = ? WHERE parent_type = ?', 'Task', 'Ticket');
        DB::execute('UPDATE ' . TABLE_PREFIX . 'subtasks SET parent_type = ? WHERE parent_type = ?', 'Task', 'Ticket');
        DB::execute("UPDATE $attachments_table SET parent_type = ? WHERE parent_type = ?", 'Task', 'Ticket');

        $comment_ids = DB::execute("SELECT id FROM $comments_table WHERE parent_type = 'Ticket'");
        if($comment_ids) {
          DB::execute("UPDATE $comments_table SET type = ?, parent_type = ? WHERE id IN (?)", 'TaskComment', 'Task', $comment_ids);
          DB::execute("UPDATE $attachments_table SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)", 'TaskComment', 'Comment', $comment_ids);
        } // if

        DB::execute("UPDATE $activity_logs_table SET parent_type = ? WHERE parent_type = ?", 'Task', 'Ticket');
        DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type IN (?) AND parent_type = ?", 'TaskCreatedActivityLog', array('NewTaskActivityLog', 'ObjectCreatedActivityLog'), 'Task');
        DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type = ? AND parent_type = ?", 'AssignmentCompletedActivityLog', 'TaskCompletedActivityLog', 'Task');
        DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type = ? AND parent_type = ?", 'AssignmentReopenedActivityLog', 'TaskReopenedActivityLog', 'Task');

        // Configuration options
        DB::execute("INSERT INTO " . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?), (?, ?, ?)',
          'tasks_auto_reopen', 'tasks', serialize(true),
          'tasks_auto_reopen_clients_only', 'tasks', serialize(true)
        );
      } // if
    } catch (Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateTickets

  /**
   * Update checklists
   *
   * @return boolean
   */
  function updateChecklists() {
    try {
      $modules_table = TABLE_PREFIX . 'modules';

      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM $modules_table WHERE name='checklists'")) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $activity_logs_table = TABLE_PREFIX . 'activity_logs';

        // Late fix for situation when there exists a module named 'todo'
        if(DB::executeFirstCell("SELECT COUNT(name) FROM $modules_table WHERE name = 'todo'") > 0) {
          DB::execute("DELETE FROM $modules_table WHERE name = 'todo'");
        } // if

        // Update references
        DB::execute("UPDATE $modules_table SET name = ? WHERE name = ?", 'todo', 'checklists');
        DB::execute("UPDATE $project_objects_table SET module = ?, type = ? WHERE type = ?", 'todo', 'TodoList', 'Checklist');
        DB::execute("UPDATE $project_objects_table SET parent_type = ? WHERE parent_type = ?", 'TodoList', 'Checklist');
        DB::execute('UPDATE ' . TABLE_PREFIX . 'subscriptions SET parent_type = ? WHERE parent_type = ?', 'TodoList', 'Checklist');
        DB::execute('UPDATE ' . TABLE_PREFIX . 'attachments SET parent_type = ? WHERE parent_type = ?', 'TodoList', 'Checklist');
        DB::execute('UPDATE ' . TABLE_PREFIX . 'subtasks SET parent_type = ? WHERE parent_type = ?', 'TodoList', 'Checklist');

        DB::execute("UPDATE $activity_logs_table SET parent_type = ? WHERE parent_type = ?", 'TodoList', 'Checklist');
        DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type IN (?) AND parent_type = ?", 'TodoListCreatedActivityLog', array('NewTaskActivityLog', 'ObjectCreatedActivityLog'), 'TodoList');
        DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type = ? AND parent_type = ?", 'AssignmentCompletedActivityLog', 'TaskCompletedActivityLog', 'TodoList');
        DB::execute("UPDATE $activity_logs_table SET type = ? WHERE type = ? AND parent_type = ?", 'AssignmentReopenedActivityLog', 'TaskReopenedActivityLog', 'TodoList');

        // Update categories
        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?)', 'todo_list_categories', 'todo', serialize(array('General')));

        $projects = DB::execute('SELECT id, created_on, created_by_id, created_by_name, created_by_email FROM ' . TABLE_PREFIX . 'projects');
        if($projects) {
          foreach($projects as $project) {
            DB::execute('INSERT INTO ' . TABLE_PREFIX . 'categories (type, parent_type, parent_id, name, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', 'TodoListCategory', 'Project', $project['id'], 'General', $project['created_on'], $project['created_by_id'], $project['created_by_name'], $project['created_by_email']);
            DB::execute("UPDATE $project_objects_table SET category_id = ? WHERE type = ? AND project_id = ?", DB::lastInsertId(), 'TodoList', $project['id']);
          } // foreach
        } // if
      } // if
    } catch (Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateChecklists

  /**
   * Update page categories
   *
   * @return boolean
   */
  function updatePageCategories() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'pages'")) {
        $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

        $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';
        $notebook_page_versions_table = TABLE_PREFIX . 'notebook_page_versions';
        $activity_logs_table = TABLE_PREFIX . 'activity_logs';

        DB::execute("CREATE TABLE $notebook_pages_table (
            id int(10) unsigned NOT NULL auto_increment,
            parent_type varchar(50) default NULL,
            parent_id int(10) unsigned default '0',
            name varchar(255) default NULL,
            body longtext,
            state tinyint(3) unsigned NOT NULL default '0',
            original_state tinyint(3) unsigned default '0',
            visibility tinyint(3) unsigned NOT NULL default '0',
            original_visibility tinyint(3) unsigned default '0',
            is_locked tinyint(1) unsigned NOT NULL DEFAULT '0',
            created_on datetime default NULL,
            created_by_id int(10) unsigned default '0',
            created_by_name varchar(100) default NULL,
            created_by_email varchar(150) default NULL,
            updated_on datetime default NULL,
            updated_by_id int(10) unsigned default '0',
            updated_by_name varchar(100) default NULL,
            updated_by_email varchar(150) default NULL,
            position int(10) unsigned default '0',
            version int(5) unsigned NOT NULL default '0',
            PRIMARY KEY (id),
            KEY parent (parent_type,parent_id)
          ) ENGINE=$engine DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

        DB::execute("CREATE TABLE $notebook_page_versions_table (
            id int(10) unsigned NOT NULL auto_increment,
            notebook_page_id int(10) unsigned NOT NULL default '0',
            version int(5) unsigned NOT NULL default '0',
            name varchar(255) default NULL,
            body longtext,
            created_on datetime default NULL,
            created_by_id int(10) unsigned default '0',
            created_by_name varchar(100) default NULL,
            created_by_email varchar(150) default NULL,
            PRIMARY KEY (id),
            UNIQUE KEY notebook_page_version (notebook_page_id,version)
          ) ENGINE=$engine DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

        // Move the data
        DB::beginWork('Updating page categories @ ' . __CLASS__);

        DB::execute('UPDATE ' . TABLE_PREFIX . 'modules SET name = ? WHERE name = ?', 'notebooks', 'pages');

        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $page_versions_table = TABLE_PREFIX . 'page_versions';
        $attachments_table = TABLE_PREFIX . 'attachments';
        $subscriptions_table = TABLE_PREFIX . 'subscriptions';

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', 'pages_categories');

        // find all orphaned pages (pages with deleted categories) and set their parent_id to null
        DB::execute("UPDATE $project_objects_table SET parent_id = NULL WHERE type = ? AND module = ? and parent_id = ?", 'Page', 'pages', 0);

        // create category for uncategorized pages and set them in these categories
        $grouped_uncategorized_pages_by_project = DB::execute("SELECT * FROM $project_objects_table WHERE type=? AND module=? and parent_id is NULL GROUP BY project_id", 'Page', 'pages');

        if($grouped_uncategorized_pages_by_project) {
          foreach ($grouped_uncategorized_pages_by_project as $page) {
            DB::execute("INSERT INTO $project_objects_table (type, module, project_id, name, state, visibility, created_on, created_by_id, created_by_name, created_by_email) VALUES (?,?,?,?,?,?,?,?,?,?)", 'Category', 'pages', $page['project_id'], 'Uncategorized pages', 3, 1, date(DATETIME_MYSQL), $page['created_by_id'], $page['created_by_name'], $page['created_by_email']);
            $category_id = DB::lastInsertId();
            DB::execute("UPDATE $project_objects_table SET parent_id = ? WHERE type = ? AND module = ? and parent_id is NULL and project_id = ?", $category_id, 'Page', 'pages', $page['project_id']);
          } //foreach
        } // if

        $page_categories = DB::execute("SELECT id, created_on, created_by_id, created_by_name, created_by_email FROM $project_objects_table WHERE type = ? AND module = ?", 'Category', 'pages');
        if(is_foreachable($page_categories)) {
          $page_category_ids = array();

          // Re-create notebook created activity logs
          foreach($page_categories as $page_category) {
            $page_category_ids[] = (integer) $page_category['id'];

            DB::execute("INSERT INTO $activity_logs_table (type, parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, ?, ?, ?, ?)", 'NotebookCreatedActivityLog', 'Notebook', $page_category['id'], $page_category['created_on'], $page_category['created_by_id'], $page_category['created_by_name'], $page_category['created_by_email']);
          } // foreach

          // Page categories are notebooks now
          DB::execute("UPDATE $project_objects_table SET type = ?, module = ?, varchar_field_1 = ? WHERE id IN (?)", 'Notebook', 'notebooks', null, $page_category_ids);
          DB::execute("UPDATE $project_objects_table SET parent_type = 'Notebook' WHERE parent_id IN (?)", $page_category_ids);
        } // if

        DB::commit('Page categories upgraded @ ' . __CLASS__);
      } // if
    } catch(Exception $e) {
      DB::rollback('Failed to upgrade page categories @ ' . __CLASS__);

      return $e->getMessage();
    } // try

    return true;
  } // updatePageCategories

  /**
   * Update pages (move them from project objects table to new table)
   *
   * @return boolean
   */
  function updatePages() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'notebooks'")) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';

        DB::beginWork('Updating pages @ ' . __CLASS__);

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'activity_logs WHERE type = ?', 'NewPageVersionActivityLog'); // We'll rebuild this

        $notebook_ids = DB::execute("SELECT id FROM $project_objects_table WHERE type = 'Notebook'");

        if($notebook_ids) {
          foreach($notebook_ids as $notebook_id) {
            $this->doUpdatePages($notebook_id, 'Notebook', $notebook_id);
          } // foreach
        } // if

        DB::execute('UPDATE ' . TABLE_PREFIX . 'comments SET type = ? WHERE parent_type = ?', 'NotebookPageComment', 'NotebookPage'); // Make sure that comments have proper class
        DB::execute("DELETE FROM $project_objects_table WHERE type = 'Page'"); // Cleanup

        DB::commit('Pages updated @ ' . __CLASS__);

        DB::execute('DROP TABLE ' . TABLE_PREFIX . 'page_versions');
      } // if
    } catch(Exception $e) {
      DB::rollback('Failed to updated pages @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updatePages

  /**
   * Update pages for a given parent
   *
   * @param integer $notebook_id
   * @param string $parent_type
   * @param integer $parent_id
   */
  private function doUpdatePages($notebook_id, $parent_type, $parent_id) {
    $project_objects_table = TABLE_PREFIX . 'project_objects';
    $page_versions_table = TABLE_PREFIX . 'page_versions';

    $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';
    $notebook_page_versions_table = TABLE_PREFIX . 'notebook_page_versions';
    $activity_logs_table = TABLE_PREFIX . 'activity_logs';
    $comments_table = TABLE_PREFIX . 'comments';
    $attachments_table = TABLE_PREFIX . 'attachments';
    $starred_objects_table = TABLE_PREFIX . 'starred_objects';

    $pages = DB::execute("SELECT id, name, body, tags, state, visibility, created_on, created_by_id, created_by_name, created_by_email, updated_on, updated_by_id, updated_by_name, updated_by_email, is_locked, integer_field_1, boolean_field_1, position FROM $project_objects_table WHERE type = ? AND module = ? AND parent_type = ? AND parent_id = ?", 'Page', 'pages', $parent_type, $parent_id);

    if(is_foreachable($pages)) {
      $pages_map = array();

      foreach($pages as $page) {
        $old_page_id = (integer) $page['id'];
        $page_version = $page['integer_field_1'] === null ? 1 : $page['integer_field_1'];

        // Mark notebook private if it contains private pages
        if($page['visibility'] == 0) {
          DB::execute("UPDATE $project_objects_table SET visibility = ? WHERE id = ?", 0, $notebook_id);
        } // if

        DB::execute("INSERT INTO $notebook_pages_table (parent_type, parent_id, name, body, state, visibility, is_locked, created_on, created_by_id, created_by_name, created_by_email, updated_on, updated_by_id, updated_by_name, updated_by_email, position, version) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
          $parent_type, $parent_id, $page['name'], $page['body'], $page['state'], $page['visibility'], (boolean) $page['is_locked'], $page['created_on'], $page['created_by_id'], $page['created_by_name'], $page['created_by_email'], $page['updated_on'], $page['updated_by_id'], $page['updated_by_name'], $page['updated_by_email'], $page['position'], $page_version);

        $notebook_page_id = DB::lastInsertId();

        $pages_map[$old_page_id] = $notebook_page_id;

        // Update child references
        DB::execute("UPDATE $project_objects_table SET parent_type = ?, parent_id = ? WHERE type = ? AND parent_id = ?", 'NotebookPage', $notebook_page_id, 'Page', $old_page_id);

        // Update resources
        DB::execute("UPDATE $activity_logs_table SET type = ?, parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE type = ? AND parent_type = ? AND parent_id = ?", 'NotebookPageCreatedActivityLog', 'Notebook', $notebook_id, serialize(array('notebook_page_id' => $notebook_page_id)), 'ObjectCreatedActivityLog', 'Page', $old_page_id);

        $comment_activity_logs = DB::execute("SELECT id, raw_additional_properties FROM $activity_logs_table WHERE type = ? AND parent_type = ? AND parent_id = ?", 'CommentCreatedActivityLog', 'Page', $old_page_id);
        if($comment_activity_logs) {
          foreach($comment_activity_logs as $comment_activity_log) {
            $properties = $comment_activity_log['raw_additional_properties'] ? unserialize($comment_activity_log['raw_additional_properties']) : null;

            if(is_array($properties)) {
              $properties['notebook_page_id'] = $notebook_page_id;
            } else {
              $properties = array('notebook_page_id' => $notebook_page_id);
            } // if

            DB::execute("UPDATE $activity_logs_table SET type = ?, parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE id = ?", 'NotebookPageCommentCreatedActivityLog', 'Notebook', $notebook_id, serialize($properties), $comment_activity_log['id']);
          } // foreach
        } // if

        DB::execute("UPDATE $activity_logs_table SET parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE parent_type = ? AND parent_id = ?", 'Notebook', $notebook_id, serialize(array('notebook_page_id' => $notebook_page_id)), 'Page', $old_page_id);
        DB::execute("UPDATE " . TABLE_PREFIX . "subscriptions SET parent_type = ?, parent_id = ? WHERE parent_type = ? AND parent_id = ?", 'NotebookPage', $notebook_page_id, 'Page', $old_page_id);
        DB::execute("UPDATE $attachments_table SET parent_type = ?, parent_id = ? WHERE parent_type = ? AND parent_id = ?", 'NotebookPage', $notebook_page_id, 'Page', $old_page_id);

        $comment_ids = DB::execute("SELECT id FROM $comments_table WHERE parent_type = 'Page' AND parent_id = '$old_page_id'");
        if($comment_ids) {
          DB::execute("UPDATE $comments_table SET type = ?, parent_type = ?, parent_id = ? WHERE id IN (?)", 'NotebookPageComment', 'NotebookPage', $notebook_page_id, $comment_ids);
          DB::execute("UPDATE $attachments_table SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)", 'NotebookPageComment', 'Comment', $comment_ids);
        } // if

        $stars = DB::executeFirstColumn("SELECT user_id FROM $starred_objects_table WHERE object_id = ?", $old_page_id);
        if($stars) {
          $to_insert = array();

          foreach($stars as $user_id) {
            $to_insert[] = "('NotebookPage', '$notebook_page_id', '$user_id')";
          } // foreach

          DB::execute('INSERT INTO ' . TABLE_PREFIX . 'favorites (parent_type, parent_id, user_id) VALUES ' . implode(', ', $to_insert));
          DB::execute("DELETE FROM $starred_objects_table WHERE object_id = ?", $old_page_id);
        } // if
      } // foreach

      // Clean up moved pages
      DB::execute("DELETE FROM $project_objects_table WHERE id IN (?)", array_keys($pages_map));

      // Update page versions
      $page_versions = DB::execute("SELECT * FROM $page_versions_table WHERE page_id IN (?)", array_keys($pages_map));
      if(is_foreachable($page_versions)) {
        foreach($page_versions as $page_version) {
          $old_id = $page_version['page_id'];

          if(isset($pages_map[$old_id])) {
            DB::execute("INSERT INTO $notebook_page_versions_table (notebook_page_id, version, name, body, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", $pages_map[$old_id], $page_version['version'], $page_version['name'], $page_version['body'], $page_version['created_on'], $page_version['created_by_id'], $page_version['created_by_name'], $page_version['created_by_email']);
            DB::execute("INSERT INTO $activity_logs_table (type, parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email, raw_additional_properties) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 'NotebookPageVersionCreatedActivityLog', 'Notebook', $notebook_id, $page_version['created_on'], $page_version['created_by_id'], $page_version['created_by_name'], $page_version['created_by_email'], serialize(array(
              'notebook_page_id' => $pages_map[$old_id],
              'notebook_page_version_id' => DB::lastInsertId(),
              'version_num' => $page_version['version'] + 1,
            )));
          } // if
        } // foreach
      } // if

      // Move subpages
      foreach($pages_map as $old_page_id => $new_page_id) {
        $this->doUpdatePages($notebook_id, 'NotebookPage', $new_page_id);
      } // foreach
    } // if
  } // doUpdatePages

  /**
   * Update files
   *
   * @return boolean
   */
  function updateFiles() {
    $project_objects_table = TABLE_PREFIX . 'project_objects';
    $categories_table = TABLE_PREFIX . 'categories';
    $config_options_table = TABLE_PREFIX . 'config_options';
    $comments_table = TABLE_PREFIX . 'comments';
    $attachments_table = TABLE_PREFIX . 'attachments';

    try {
      DB::beginWork('Updating files @ ' . __CLASS__);

      $categories = DB::execute("SELECT id, project_id, name, module, created_on, created_by_id, created_by_name, created_by_email FROM $project_objects_table WHERE type = ? AND module = ?", 'Category', 'files');
      if(is_foreachable($categories)) {
        $ids = array();

        foreach($categories as $category) {
          $old_category_id = (integer) $category['id'];

          DB::execute("INSERT INTO $categories_table (type, parent_type, parent_id, name, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 'AssetCategory', 'Project', $category['project_id'], $category['name'], $category['created_on'], $category['created_by_id'], $category['created_by_name'], $category['created_by_email']);
          DB::execute("UPDATE $project_objects_table SET parent_type = ?, parent_id = ?, category_id = ? WHERE type = ? AND parent_id = ?", null, null, DB::lastInsertId(), 'File', $old_category_id);

          $ids[] = $old_category_id; // Set this category to be deleted from project objects table
        } // foreach

        if(count($ids)) {
          DB::execute("DELETE FROM $project_objects_table WHERE id IN (?)", $ids);
        } // if
      } // if

      DB::execute("UPDATE $config_options_table SET name = 'asset_categories' WHERE name = 'file_categories'");
      DB::execute('UPDATE ' . TABLE_PREFIX . 'activity_logs SET type = ? WHERE type = ?', 'FileUploadedActivityLog', 'NewFileActivityLog');

      $comment_ids = DB::execute("SELECT id FROM $comments_table WHERE parent_type = 'File'");
      if($comment_ids) {
        DB::execute("UPDATE $comments_table SET type = ? WHERE id IN (?)", 'AssetComment', $comment_ids);
        DB::execute("UPDATE $attachments_table SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)", 'AssetComment', 'Comment', $comment_ids);
      } // if

      DB::commit('Files updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update files @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateFiles

  /**
   * Update dicussions
   *
   * @return boolean
   */
  function updateDiscussions() {
    $project_objects_table = TABLE_PREFIX . 'project_objects';
    $categories_table = TABLE_PREFIX . 'categories';
    $comments_table = TABLE_PREFIX . 'comments';
    $attachments_table = TABLE_PREFIX . 'attachments';

    try {
      DB::beginWork('Updating discussions @ ' . __CLASS__);

      $categories = DB::execute("SELECT id, project_id, name, module, created_on, created_by_id, created_by_name, created_by_email FROM $project_objects_table WHERE type = ? AND module = ?", 'Category', 'discussions');
      if(is_foreachable($categories)) {
        $ids = array();

        foreach($categories as $category) {
          $old_category_id = (integer) $category['id'];

          DB::execute("INSERT INTO $categories_table (type, parent_type, parent_id, name, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 'DiscussionCategory', 'Project', $category['project_id'], $category['name'], $category['created_on'], $category['created_by_id'], $category['created_by_name'], $category['created_by_email']);
          DB::execute("UPDATE $project_objects_table SET parent_type = ?, parent_id = ?, category_id = ? WHERE type = ? AND parent_id = ?", null, null, DB::lastInsertId(), 'Discussion', $old_category_id);

          $ids[] = $old_category_id; // Set this category to be deleted from project objects table
        } // foreach

        if(count($ids)) {
          DB::execute("DELETE FROM $project_objects_table WHERE id IN (?)", $ids);
        } // if
      } // if

      DB::execute('UPDATE ' . TABLE_PREFIX . 'activity_logs SET type = ? WHERE type = ? AND parent_type = ?', 'DiscussionStartedActivityLog', 'ObjectCreatedActivityLog', 'Discussion');

      $comment_ids = DB::execute("SELECT id FROM $comments_table WHERE parent_type = 'Discussion'");
      if($comment_ids) {
        DB::execute("UPDATE $comments_table SET type = ? WHERE id IN (?)", 'DiscussionComment', $comment_ids);
        DB::execute("UPDATE $attachments_table SET parent_type = ? WHERE parent_type = ? AND parent_id IN (?)", 'DiscussionComment', 'Comment', $comment_ids);
      } // if

      DB::commit('Discussions updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update discussions @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateDiscussions

  /**
   * Update tracking module details and data
   *
   * @return boolean
   */
  function updateTracking() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'timetracking'")) {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'modules SET name = ? WHERE name = ?', 'tracking', 'timetracking');

        $time_records_table = TABLE_PREFIX . 'time_records';
        $tracking_reports_table = TABLE_PREFIX . 'tracking_reports';
        $time_reports_table = TABLE_PREFIX . 'time_reports';
        $tracking_reports_table = TABLE_PREFIX . 'tracking_reports';
        $project_roles_table = TABLE_PREFIX . 'project_roles';
        $project_users_table = TABLE_PREFIX . 'project_users';

        DB::execute("CREATE TABLE $time_records_table (
            id int unsigned NOT NULL auto_increment,
            parent_type varchar(50)  DEFAULT NULL,
            parent_id int unsigned NULL DEFAULT NULL,
            job_type_id int(5) unsigned NOT NULL DEFAULT 0,
            state tinyint(3) unsigned NOT NULL DEFAULT '0',
            original_state tinyint(3) unsigned NULL DEFAULT NULL,
            record_date date  DEFAULT NULL,
            value decimal(12, 2)  DEFAULT 0,
            user_id int(10) unsigned NULL DEFAULT NULL,
            user_name varchar(100)  DEFAULT NULL,
            user_email varchar(150)  DEFAULT NULL,
            summary text,
            billable_status int(3) unsigned NOT NULL DEFAULT 0,
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX parent (parent_type, parent_id),
            INDEX user_id (user_id),
            INDEX job_type_id (job_type_id),
            INDEX record_date (record_date)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "job_types (
            id int unsigned NOT NULL auto_increment,
            name varchar(100)  DEFAULT NULL,
            default_hourly_rate decimal(12, 2) NOT NULL DEFAULT 0,
            is_default tinyint(1) unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY (id),
            UNIQUE name (name)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'job_types (id, name, default_hourly_rate, is_default) VALUES (?, ?, ?, ?)', 1, 'General', 100, true);

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "project_hourly_rates (
            project_id int(10) unsigned NULL DEFAULT NULL,
            job_type_id int(5) unsigned NULL DEFAULT NULL,
            hourly_rate decimal(12, 2) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (project_id, job_type_id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "expenses (
            id int unsigned NOT NULL auto_increment,
            parent_type varchar(50)  DEFAULT NULL,
            parent_id int unsigned NULL DEFAULT NULL,
            category_id int(5) unsigned NOT NULL DEFAULT 0,
            state tinyint(3) unsigned NOT NULL DEFAULT '0',
            original_state tinyint(3) unsigned NULL DEFAULT NULL,
            record_date date  DEFAULT NULL,
            value decimal(12, 2) unsigned NOT NULL DEFAULT 0,
            user_id int(10) unsigned NULL DEFAULT NULL,
            user_name varchar(100)  DEFAULT NULL,
            user_email varchar(150)  DEFAULT NULL,
            summary text,
            billable_status int(3) unsigned NOT NULL DEFAULT '0',
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX parent (parent_type, parent_id),
            INDEX user_id (user_id),
            INDEX category_id (category_id),
            INDEX record_date (record_date)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "expense_categories (
            id int unsigned NOT NULL auto_increment,
            name varchar(100)  DEFAULT NULL,
            is_default tinyint(1) unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY (id),
            UNIQUE name (name)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'expense_categories (id, name, is_default) VALUES (?, ?, ?)', 1, 'General', true);

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "estimates (
            id int unsigned NOT NULL auto_increment,
            parent_type varchar(50) DEFAULT NULL,
            parent_id int unsigned NULL DEFAULT NULL,
            job_type_id int(5) unsigned NOT NULL DEFAULT 0,
            value decimal(12, 2) unsigned  DEFAULT 0,
            comment text,
            created_on datetime DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100) DEFAULT NULL,
            created_by_email varchar(150) DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX parent (parent_type, parent_id),
            INDEX created_on (created_on)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE $tracking_reports_table (
            id int unsigned NOT NULL auto_increment,
            name varchar(50)  DEFAULT NULL,
            raw_additional_properties longtext,
            PRIMARY KEY (id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        // Migrate time reports to new table
        $rows = DB::execute("SELECT * FROM $time_reports_table");
        if($rows) {
          foreach($rows as $row) {
            $attributes = array();

            // User filter
            $attributes['user_filter'] = $row['user_filter'];

            if($attributes['user_filter'] == 'company') {
              $attributes['company_id'] = $row['user_filter_data'] ? unserialize($row['user_filter_data']) : 0;
            } elseif($attributes['user_filter'] == 'selected') {
              $attributes['selected_users'] = $row['user_filter_data'] ? unserialize($row['user_filter_data']) : null;
            } // if

            // Billable status
            $attributes['billable_status_filter'] = $row['billable_filter'];

            // Date filter
            $attributes['date_filter'] = $row['date_filter'];

            if($attributes['date_filter'] == 'selected_date') {
              $attributes['date_filter_on'] = $row['date_from'];
            } elseif($attributes['date_filter'] == 'selected_range') {
              $attributes['date_filter_from'] = $row['date_from'];
              $attributes['date_filter_to'] = $row['date_to'];
            } // if

            if($row['sum_by_user']) {
              $attributes['summarize'] = 'by_user';
            } // if

            DB::execute("INSERT INTO $tracking_reports_table (name, raw_additional_properties) VALUES (?, ?)", $row['name'], serialize($attributes));
          } // foreach
        } // if

        DB::execute("DROP TABLE $time_reports_table");
      } // if

      return true;
    } catch(Exception $e) {
      return $e->getMessage();
    } // try
  } // updateTracking

  /**
   * Update time reports
   *
   * @return boolean
   */
  function updateTimeRecords() {
    try {
      if($this->isModuleInstalled('tracking')) {
        $time_records_table = TABLE_PREFIX . 'time_records';
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        $activity_logs_table = TABLE_PREFIX . 'activity_logs';

        // Prepare invoicing related items table if invoicing is installed
        $invoicing_installed = (boolean) DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'invoicing'");

        if($invoicing_installed) {
          $invoice_time_records_table = TABLE_PREFIX . 'invoice_time_records';

          DB::execute("ALTER TABLE $invoice_time_records_table DROP PRIMARY KEY");
          DB::execute("ALTER TABLE $invoice_time_records_table CHANGE time_record_id parent_id INT(10) UNSIGNED NOT NULL DEFAULT '0';");
          DB::execute("ALTER TABLE $invoice_time_records_table ADD parent_type VARCHAR(50) NOT NULL DEFAULT '' AFTER item_id");

          DB::execute("UPDATE $invoice_time_records_table SET parent_type = 'TimeRecord'");
        } // if

        DB::beginWork('Moving time records @ ' . __CLASS__);

        $rows = DB::execute("SELECT id, project_id, parent_type, parent_id, state, original_state, body, created_on, created_by_id, created_by_name, created_by_email, date_field_1 AS 'record_date', float_field_1 AS 'value', integer_field_1 AS 'user_id', varchar_field_1 AS 'user_name', varchar_field_2 AS 'user_email', integer_field_2 AS 'billable_status' FROM $project_objects_table WHERE type = ?", 'TimeRecord');
        if($rows) {
          $to_delete = array(); // remember ID-s so we can delete time records from project objects table

          foreach($rows as $row) {
            if($row['parent_type'] && $row['parent_id']) {
              $parent_type = $row['parent_type'];
              $parent_id = $row['parent_id'];
            } else {
              $parent_type = 'Project';
              $parent_id = $row['project_id'];
            } // if

            $billable_status = (integer) $row['billable_status'];
            if($billable_status < 0 || $billable_status > 3) {
              $billable_status = 0;
            } // if

            DB::execute("INSERT INTO $time_records_table (parent_type, parent_id, job_type_id, state, original_state, record_date, value, user_id, user_name, user_email, summary, billable_status, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", $parent_type, $parent_id, 1, $row['state'], $row['original_state'], $row['record_date'], $row['value'], $row['user_id'], $row['user_name'], $row['user_email'], ($row['body'] ? $row['body'] : null), $billable_status, $row['created_on'], $row['created_by_id'], $row['created_by_name'], $row['created_by_email']);

            $time_record_id = DB::lastInsertId();

            DB::execute("UPDATE $activity_logs_table SET type = ?, parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE type = ? AND parent_type = ? AND parent_id = ?", 'TimeRecordCreatedActivityLog', $parent_type, $parent_id, serialize(array('time_record_id' => $time_record_id)), 'TimeAddedActivityLog', 'TimeRecord', $row['id']);
            DB::execute("UPDATE $activity_logs_table SET parent_type = ?, parent_id = ?, raw_additional_properties = ? WHERE parent_type = ? AND parent_id = ?", $parent_type, $parent_id, serialize(array('time_record_id' => $time_record_id)), 'TimeRecord', $row['id']);
            if($invoicing_installed) {
              DB::execute("UPDATE $invoice_time_records_table SET parent_id = ? WHERE parent_id = ?", $time_record_id, $row['id']);
            } // if

            $to_delete[] = $row['id'];
          } // foreach

          DB::execute("DELETE FROM $project_objects_table WHERE id IN (?)", $to_delete);
        } // if

        DB::commit('Time reports mode @ ' . __CLASS__);

        // Now that data is up to date, add primary key and rename table
        if($invoicing_installed) {
          DB::execute("ALTER TABLE $invoice_time_records_table ADD PRIMARY KEY (invoice_id, parent_type, parent_id)");
          DB::execute("RENAME TABLE $invoice_time_records_table TO " . TABLE_PREFIX . "invoice_related_records");
        } // if
      } // if
    } catch(Exception $e) {
      DB::rollback('Failed to move time records @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateTimeRecords

  // We need to have type field values up to date in project objects table 
  // before this line

  /**
   * Update project role keys
   *
   * @return string
   */
  function updateProjectPermissionNames() {
    $project_roles_table = TABLE_PREFIX . 'project_roles';
    $project_users_table = TABLE_PREFIX . 'project_users';

    try {
      DB::beginWork('Updating project role keys @ ' . __CLASS__);

      $rename = array(
        'ticket' => 'task',
        'checklist' => 'todo_list',
        'page' => 'notebook',
        'timerecord' => 'tracking',
      );

      $rows = DB::execute("SELECT id, permissions FROM $project_roles_table");
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $permissions = $row['permissions'] ? unserialize($row['permissions']) : array();

          foreach($rename as $k => $v) {
            if(isset($permissions[$k])) {
              $permissions[$v] = $permissions[$k];
              unset($permissions[$k]);
            } // if
          } // foreach

          DB::execute("UPDATE $project_roles_table SET permissions = ? WHERE id = ?", serialize($permissions), $row['id']);
        } // foreach
      } // if

      $rows = DB::execute("SELECT user_id, project_id, permissions FROM $project_users_table");
      if(is_foreachable($rows)) {
        foreach($rows as $row) {
          $permissions = $row['permissions'] ? unserialize($row['permissions']) : null;

          if(is_array($permissions)) {
            foreach($rename as $k => $v) {
              if(isset($permissions[$k])) {
                $permissions[$v] = $permissions[$k];
                unset($permissions[$k]);
              } // if
            } // foreach
          } // if

          DB::execute("UPDATE $project_users_table SET permissions = ? WHERE user_id = ? AND project_id = ?", serialize($permissions), $row['user_id'], $row['project_id']);
        } // foreach
      } // if

      DB::commit('Project role keys updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update project role keys @ ' . __CLASS__);

      return $e->getMessage();
    } // try

    return true;
  } // updateProjectPermissionNames

  /**
   * Move pinned projects and starred objects data to favorites table
   *
   * @return boolean
   */
  function updateFavorites() {
    try {
      $favorites_table = TABLE_PREFIX . 'favorites';
      $starred_objects_table = TABLE_PREFIX . 'starred_objects';
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      // Starred objects
      $rows = DB::execute("SELECT DISTINCT $project_objects_table.type AS 'parent_type', $project_objects_table.id AS 'parent_id', $starred_objects_table.user_id AS 'user_id' FROM $project_objects_table, $starred_objects_table WHERE $project_objects_table.id = $starred_objects_table.object_id");

      if($rows) {
        $to_insert = array();

        foreach($rows as $row) {
          $to_insert[] = DB::prepare('(?, ?, ?)', $row['parent_type'], $row['parent_id'], $row['user_id']);
        } // foreach

        DB::execute("INSERT INTO $favorites_table (parent_type, parent_id, user_id) VALUES " . implode(', ', $to_insert));
      } // if

      // Pinned projects to favorites
      $rows = DB::execute('SELECT project_id, user_id FROM ' . TABLE_PREFIX . 'pinned_projects');
      if($rows) {
        $to_insert = array();

        foreach($rows as $row) {
          $to_insert[] = DB::prepare("('Project', ?, ?)", $row['project_id'], $row['user_id']);
        } // foreach

        DB::execute("INSERT INTO $favorites_table (parent_type, parent_id, user_id) VALUES " . implode(', ', $to_insert));
      } // if

      // Drop old tables
      DB::execute("DROP TABLE $starred_objects_table");
      DB::execute('DROP TABLE ' . TABLE_PREFIX . 'pinned_projects');

      return true;
    } catch(Exception $e) {
      return $e->getMessage();
    } // try
  } // updateFavorites

  /**
   * Update reminders
   *
   * @return boolean
   */
  function updateReminders() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      $reminders_table = TABLE_PREFIX . 'reminders';
      $reminder_users_table = TABLE_PREFIX . 'reminder_users';
      $users_table = TABLE_PREFIX . 'users';
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      // Update existing reminders table
      DB::execute("ALTER TABLE $reminders_table ADD parent_type VARCHAR(50)  NULL  DEFAULT NULL AFTER id");
      DB::execute("ALTER TABLE $reminders_table CHANGE object_id parent_id INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER parent_type");
      DB::execute("ALTER TABLE $reminders_table ADD send_to varchar(15) NOT NULL DEFAULT 'self' AFTER parent_id");
      DB::execute("ALTER TABLE $reminders_table ADD send_on datetime DEFAULT NULL AFTER send_to");
      DB::execute("ALTER TABLE $reminders_table ADD sent_on datetime DEFAULT NULL AFTER send_on");
      DB::execute("ALTER TABLE $reminders_table ADD selected_user_id int(10) unsigned NULL DEFAULT NULL AFTER comment");
      DB::execute("ALTER TABLE $reminders_table CHANGE created_on created_on datetime NULL DEFAULT NULL");
      DB::execute("ALTER TABLE $reminders_table ADD dismissed_on datetime DEFAULT NULL AFTER created_on");
      DB::execute("ALTER TABLE $reminders_table ADD INDEX (created_by_id);");

      // Create reminder users table
      DB::execute("CREATE TABLE $reminder_users_table (
          reminder_id int(10) unsigned NULL DEFAULT NULL,
          user_id int(10) unsigned NULL DEFAULT NULL,
          user_name varchar(100) DEFAULT NULL,
          user_email varchar(150) DEFAULT NULL,
          dismissed_on datetime  DEFAULT NULL,
          INDEX user_id (user_id),
          PRIMARY KEY (reminder_id, user_email)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      // Temp index, so we can faster locate objects by parent_id during upgrade
      DB::execute("ALTER TABLE $reminders_table ADD INDEX (parent_id)");

      DB::beginWork('Update reminders @ ' . __CLASS__);

      // Get users map
      $rows = DB::execute("SELECT DISTINCT $users_table.id, $users_table.first_name, $users_table.last_name, $users_table.email FROM $users_table, $reminders_table WHERE $users_table.id = $reminders_table.user_id");
      if($rows) {
        $reminded_users = array();

        foreach($rows as $row) {
          $reminded_users[$row['id']] = array();

          if($row['first_name'] && $row['last_name']) {
            $reminded_users[$row['id']]['display_name'] = "$row[first_name] $row[last_name]";
          } elseif($row['first_name']) {
            $reminded_users[$row['id']]['display_name'] = $row['first_name'];
          } else {
            $reminded_users[$row['id']]['display_name'] = $row['email'];
          } // if

          $reminded_users[$row['id']]['email'] = $row['email'];
        } // foreach
      } else {
        $reminded_users = null;
      } // if

      // Get type map
      $rows = DB::execute("SELECT DISTINCT $project_objects_table.id, $project_objects_table.type FROM $project_objects_table, $reminders_table WHERE $project_objects_table.id = $reminders_table.parent_id");
      if($rows) {
        $parent_types = array();

        foreach($rows as $row) {
          $parent_types[$row['id']] = $row['type'];
        } // foreach
      } else {
        $parent_types = null;
      } // if

      $rows = DB::execute("SELECT id, user_id, parent_id, created_on FROM $reminders_table");
      if($rows) {
        $to_delete = array();

        foreach($rows as $row) {
          $parent_type = isset($parent_types[$row['parent_id']]) ? $parent_types[$row['parent_id']] : null;
          $user_details = isset($reminded_users[$row['user_id']]) ? $reminded_users[$row['user_id']] : null;

          if($parent_type && $user_details) {
            DB::execute("INSERT INTO $reminder_users_table (reminder_id, user_id, user_name, user_email) VALUES (?, ?, ?, ?)", $row['id'], $row['user_id'], $user_details['display_name'], $user_details['email']);
            DB::execute("UPDATE $reminders_table SET parent_type = ?, send_to = ?, send_on = ?, sent_on = ?, selected_user_id = ? WHERE id = ?", $parent_type, 'selected', $row['created_on'], $row['created_on'], $row['user_id'], $row['id']);
          } else {
            $to_delete[] = (integer) $row['id'];
          } // if
        } // foreach

        if(count($to_delete)) {
          DB::execute("DELETE FROM $reminders_table WHERE id IN (?)", $to_delete);
        } // if
      } // if

      DB::commit('Reminders updated @ ' . __CLASS__);

      // Now that we have full data, create parent index
      DB::execute("ALTER TABLE $reminders_table ADD INDEX parent (parent_type, parent_id)");

      // Drop temp index
      DB::execute("ALTER TABLE $reminders_table DROP INDEX parent_id");
    } catch(Exception $e) {
      DB::rollback('Failed to update reminders @ ' . __CLASS__);

      return $e->getMessage();
    } // try

    return true;
  } // updateReminders

  /**
   * Update tags
   *
   * @return boolean
   */
  function backupTags() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      $tags_table = TABLE_PREFIX . 'tags_backup';
      $project_objects_table = TABLE_PREFIX . 'project_objects';

      DB::execute("CREATE TABLE $tags_table (
          parent_type varchar(50) NOT NULL default '',
          parent_id int(10) unsigned NOT NULL default '0',
          tags varchar(255) default NULL,
          PRIMARY KEY  (parent_type,parent_id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      $rows = DB::execute('SELECT id, type, tags FROM ' . TABLE_PREFIX . "project_objects WHERE tags IS NOT NULL AND tags <> ''");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $tags_table (parent_type, parent_id, tags) VALUES (?, ?, ?)", $row['type'], $row['id'], $row['tags']);
        } // foreach
      } // if

      DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'project_objects DROP tags');
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // backupTags

  // ---------------------------------------------------
  //  Invoicing
  // ---------------------------------------------------

  /**
   * Set up environment for handling payments
   *
   * @return boolean
   */
  function setUpPayments() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      DB::execute("CREATE TABLE " . TABLE_PREFIX . "payment_gateways (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'ApplicationObject',
          raw_additional_properties longtext,
          is_default tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (id),
          INDEX type (type)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "payments (
          id int unsigned NOT NULL auto_increment,
          type varchar(50) NOT NULL DEFAULT 'ApplicationObject',
          parent_type varchar(50) DEFAULT NULL,
          parent_id int unsigned NULL DEFAULT NULL,
          amount decimal(12, 3) DEFAULT 0,
          currency_id int(5) NULL DEFAULT NULL,
          gateway_type varchar(50) DEFAULT NULL,
          gateway_id int(10) unsigned NULL DEFAULT NULL,
          status enum('Paid', 'Pending', 'Deleted', 'Canceled') DEFAULT NULL,
          reason enum('Fraud', 'Refund', 'Other')  DEFAULT NULL,
          reason_text text,
          created_by_id int(10) unsigned NULL DEFAULT NULL,
          created_by_name varchar(100)  DEFAULT NULL,
          created_by_email varchar(150)  DEFAULT NULL,
          created_on datetime  DEFAULT NULL,
          paid_on date  DEFAULT NULL,
          comment text,
          raw_additional_properties longtext,
          PRIMARY KEY (id),
          INDEX type (type),
          INDEX parent (parent_type, parent_id),
          INDEX created_by_id (created_by_id),
          INDEX currency_id (currency_id),
          INDEX status (status),
          INDEX created_on (created_on),
          INDEX paid_on (paid_on)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?)', 'allow_payments', 'payments', serialize(false));
      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?)', 'allow_payments_for_invoice', 'payments', serialize(true));
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // setUpPayments

  /**
   * Upgrade invoicing module
   *
   * @return boolean
   */
  function updateInvoicing() {
    $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='invoicing'")) {
        $invoices_table = TABLE_PREFIX . 'invoices';

        DB::execute("ALTER TABLE $invoices_table ADD based_on_type VARCHAR(50) NULL DEFAULT NULL AFTER id");
        DB::execute("ALTER TABLE $invoices_table ADD based_on_id INT  UNSIGNED NULL DEFAULT NULL AFTER based_on_type");
        DB::execute("ALTER TABLE $invoices_table DROP company_name");
        DB::execute("ALTER TABLE $invoices_table ADD allow_payments TINYINT(3) NOT NULL DEFAULT '0' AFTER created_by_email");
        DB::execute("ALTER TABLE $invoices_table ADD INDEX (issued_on)");
        DB::execute("ALTER TABLE $invoices_table ADD INDEX (due_on)");
        DB::execute("ALTER TABLE $invoices_table ADD INDEX (closed_on)");

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "recurring_profiles (
            id int unsigned NOT NULL auto_increment,
            company_id int(11) NULL DEFAULT NULL,
            company_address text,
            currency_id int(11) NULL DEFAULT NULL,
            language_id int(11) NULL DEFAULT NULL,
            name varchar(150)  DEFAULT NULL,
            note text,
            our_comment text,
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            start_on date  DEFAULT NULL,
            frequency varchar(150)  DEFAULT NULL,
            occurrences varchar(100)  DEFAULT NULL,
            request_approval tinyint(1) unsigned NOT NULL DEFAULT '0',
            allow_payments varchar(100)  DEFAULT NULL,
            project_id int(10) NULL DEFAULT NULL,
            state tinyint(3) NULL DEFAULT NULL,
            original_state tinyint(3) NULL DEFAULT NULL,
            triggered_number int(11) NULL DEFAULT NULL,
            last_triggered_on date  DEFAULT NULL,
            next_trigger_on date  DEFAULT NULL,
            visibility int NULL DEFAULT NULL,
            PRIMARY KEY (id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "recurring_profile_items (
            id int unsigned NOT NULL auto_increment,
            recurring_profile_id int(11) NULL DEFAULT NULL,
            position int(5) NULL DEFAULT NULL,
            tax_rate_id int(11) NULL DEFAULT NULL,
            description varchar(255) DEFAULT NULL,
            quantity int(11) NULL DEFAULT NULL,
            unit_cost decimal(12, 2) DEFAULT 0,
            PRIMARY KEY (id),
            INDEX recurring_profile_id (recurring_profile_id),
            INDEX position (position)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "recurring_approval_requests (
            id int unsigned NOT NULL auto_increment,
            recurring_profile_id int(11) NULL DEFAULT NULL,
            notified_on date  DEFAULT NULL,
            resolution varchar(100)  DEFAULT NULL,
            archive_it tinyint(1) unsigned NOT NULL DEFAULT '0',
            resolved_on datetime  DEFAULT NULL,
            resolved_by_id int unsigned NULL DEFAULT NULL,
            resolved_by_name varchar(100)  DEFAULT NULL,
            resolved_by_email varchar(150)  DEFAULT NULL,
            PRIMARY KEY (id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE " . TABLE_PREFIX . "quotes (
            id int unsigned NOT NULL auto_increment,
            based_on_type varchar(50)  DEFAULT NULL,
            based_on_id int(10) unsigned NULL DEFAULT NULL,
            company_id int(5) unsigned NOT NULL DEFAULT '0',
            company_address text,
            currency_id int(4) unsigned NOT NULL DEFAULT '0',
            language_id int(3) unsigned NOT NULL DEFAULT '0',
            name varchar(150)  DEFAULT NULL,
            note text,
            status int(4) NOT NULL DEFAULT '0',
            created_on datetime  DEFAULT NULL,
            created_by_id int unsigned NULL DEFAULT NULL,
            created_by_name varchar(100)  DEFAULT NULL,
            created_by_email varchar(150)  DEFAULT NULL,
            sent_on datetime  DEFAULT NULL,
            sent_by_id int unsigned NULL DEFAULT NULL,
            sent_by_name varchar(100)  DEFAULT NULL,
            sent_by_email varchar(150)  DEFAULT NULL,
            sent_to_id int(11) NULL DEFAULT NULL,
            closed_on datetime  DEFAULT NULL,
            closed_by_id int unsigned NULL DEFAULT NULL,
            closed_by_name varchar(100)  DEFAULT NULL,
            closed_by_email varchar(150)  DEFAULT NULL,
            is_locked tinyint(1) unsigned NOT NULL DEFAULT '0',
            last_comment_on datetime  DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX based_on_id (based_on_id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        
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

        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?)',
          'on_invoice_based_on', 'invoicing', serialize('sum_all')
        );

        // Retrieve old config options
        $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "config_options WHERE name='invoicing_company_details' OR name='invoicing_company_name' OR name='invoicing_pdf_background_color' OR name='invoicing_pdf_border_color' OR name='invoicing_pdf_header_text_color' OR name='invoicing_pdf_page_text_color' OR name='invoicing_pdf_paper_format' OR name='invoicing_pdf_paper_orientation'");

        if ($result instanceof DBResult) {
          $result = $result->toArray();

          $new_config_option = array(
            'print_logo' => true,
            'print_company_details' => true,
            'print_header_border' => true,
            'print_table_border' => true,
            'print_footer_border' => true,
            'header_border_color' => '#ccc',
            'footer_border_color' => '#ccc'
          );

          foreach ($result as $config_option) {
            switch ($config_option['name']) {
              case 'invoicing_company_details':
                $new_config_option['company_details'] = unserialize($config_option['value']);
                break;

              case 'invoicing_company_name':
                $new_config_option['company_name'] = unserialize($config_option['value']);
                break;

              case 'invoicing_pdf_border_color':
                $new_config_option['items_border_color'] = '#' . unserialize($config_option['value']);
                $new_config_option['print_items_border'] = true;
                break;

              case 'invoicing_pdf_header_text_color':
                $new_config_option['header_text_color'] = '#' . unserialize($config_option['value']);
                $new_config_option['footer_text_color'] = '#' . unserialize($config_option['value']);
                break;

              case 'invoicing_pdf_page_text_color':
                $new_config_option['client_details_text_color'] = '#' . unserialize($config_option['value']);
                $new_config_option['invoice_details_text_color'] = '#' . unserialize($config_option['value']);
                $new_config_option['items_text_color'] = '#' . unserialize($config_option['value']);
                $new_config_option['note_text_color'] = '#' . unserialize($config_option['value']);
                break;

              case 'invoicing_pdf_paper_format':
                $paper_format = unserialize($config_option['value']);
                if (!in_array($paper_format, array(Globalization::PAPER_FORMAT_A4, Globalization::PAPER_FORMAT_LETTER))) {
                  $paper_format = Globalization::PAPER_FORMAT_A4;
                } // if
                $new_config_option['paper_size'] = $paper_format;
                break;
            } //switch
          } // foreach

          // insert new config option
          $result = DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES
              ('invoice_template', 'invoicing', '".serialize($new_config_option)."')"
          );

          // delete old config options
          DB::execute("DELETE FROM " . TABLE_PREFIX . "config_options WHERE name='invoicing_company_details' OR name='invoicing_company_name' OR name='invoicing_pdf_background_color' OR name='invoicing_pdf_border_color' OR name='invoicing_pdf_header_text_color' OR name='invoicing_pdf_page_text_color' OR name='invoicing_pdf_paper_format' OR name='invoicing_pdf_paper_orientation'");
        } // if
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateInvoicing

  /**
   * Move recorded invoice payments to the new storage
   *
   * @return boolean
   */
  function updateInvoicingPayments() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='invoicing'")) {
        try {
          $invoices_table = TABLE_PREFIX . 'invoices';
          $invoice_payments_table = TABLE_PREFIX . 'invoice_payments';
          $payments_table = TABLE_PREFIX . 'payments';

          DB::beginWork('Moving payments @ ' . __CLASS__);

          $rows = DB::execute("SELECT id, currency_id FROM $invoices_table");
          if($rows) {
            $currencies_map = array();

            foreach($rows as $row) {
              $currencies_map[(integer) $row['id']] = (integer) $row['currency_id'];
            } // foreach
          } else {
            $currencies_map = null;
          } // if

          if(is_foreachable($currencies_map)) {
            $rows = DB::execute("SELECT * FROM $invoice_payments_table ORDER BY created_on");
            if($rows) {
              foreach($rows as $row) {
                $currency_id = isset($currencies_map[$row['invoice_id']]) ? (integer) $currencies_map[$row['invoice_id']] : 0;

                DB::execute("INSERT INTO $payments_table (type, parent_type, parent_id, amount, currency_id, gateway_type, comment, status, created_by_id, created_by_name, created_by_email, created_on, paid_on) VALUES ('CustomPayment', 'Invoice', ?, ?, ?, 'CustomPaymentGateway', ?, 'Paid', ?, ?, ?, ?, ?)", $row['invoice_id'], $row['amount'], $currency_id, $row['comment'], $row['created_by_id'], $row['created_by_name'], $row['created_by_email'], $row['created_on'], $row['created_on']);
              } // foreach
            } // if
          } // if

          DB::commit('Payments moved @ ' . __CLASS__);

          DB::execute("DROP TABLE $invoice_payments_table");
        } catch(Exception $e) {
          DB::rollback('Failed to move payments @ ' . __CLASS__);
          return $e->getMessage();
        } // try

      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateInvoicingPayments

  // ---------------------------------------------------
  //  Third party and additional modules
  // ---------------------------------------------------

  /**
   * Update documents
   *
   * @return boolean
   */
  function updateDocuments() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='documents'")) {
        $documents_table = TABLE_PREFIX . 'documents';
        $document_categories_table = TABLE_PREFIX . 'document_categories';

        try {
          DB::execute("ALTER TABLE $documents_table CHANGE category_id category_id int(11) unsigned null default null");
          DB::execute("ALTER TABLE $documents_table ADD size INT UNSIGNED NULL DEFAULT NULL AFTER body");
          DB::execute("ALTER TABLE $documents_table CHANGE mime_type mime_type VARCHAR(255) NULL DEFAULT NULL");
          DB::execute("ALTER TABLE $documents_table ADD location VARCHAR(50) NULL DEFAULT NULL AFTER mime_type");
          DB::execute("ALTER TABLE $documents_table ADD md5 VARCHAR(32) NULL DEFAULT NULL AFTER location");
          DB::execute("ALTER TABLE $documents_table ADD state tinyint unsigned not null default '0' after md5");
          DB::execute("ALTER TABLE $documents_table ADD original_state tinyint(3) unsigned null default null after state");
          DB::execute("ALTER TABLE $documents_table ADD original_visibility tinyint unsigned null default null after visibility");
          DB::execute("ALTER TABLE $documents_table CHANGE created_on created_on DATETIME NULL DEFAULT NULL");

          // Update data
          DB::beginWork('Updating documents @ ' . __CLASS__);

          DB::execute("UPDATE $documents_table SET state = ?", 3); // Set state to visible for existing documents
          DB::execute("UPDATE $documents_table SET location = body, body = '' WHERE type = 'file'"); // Move file location to location column

          $categories = DB::execute("SELECT id, name FROM $document_categories_table");
          if(is_foreachable($categories)) {
            list($admin_user_id, $admin_display_name, $admin_email_address) = $this->getFirstAdministrator();

            foreach($categories as $category) {
              DB::execute("INSERT INTO " . TABLE_PREFIX . "categories (type, name, created_on, created_by_id, created_by_name, created_by_email) VALUES (?, ?, UTC_TIMESTAMP(), ?, ?, ?)", 'DocumentCategory', $category['name'], $admin_user_id, $admin_display_name, $admin_email_address);
              DB::execute("UPDATE $documents_table SET category_id = ? WHERE category_id = ?", DB::lastInsertId(), $category['id']);
            } // foreach
          } // if

          DB::commit('Documents updated @ ' . __CLASS__);

          DB::execute("DROP TABLE $document_categories_table");
        } catch (Exception $e) {
          DB::rollback('Failed to update documents @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if
    } catch (Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateDocuments

  /**
   * Update document hashes
   *
   * @return boolean
   */
  function updateDocumentHashes() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='documents'")) {
        try {
          $documents_table = TABLE_PREFIX . 'documents';

          $documents = DB::execute("SELECT id, location FROM $documents_table WHERE type = 'file'");
          if($documents) {
            DB::beginWork('Updating document hashes @ ' . __CLASS__);

            foreach($documents as $document) {
              $file_path = UPLOAD_PATH . "/$document[location]";

              if(is_file($file_path)) {
                $hash = md5_file($file_path);

                if($hash) {
                  DB::execute("UPDATE $documents_table SET md5 = ? WHERE id = ?", $hash, $document['id']);
                } // if
              } // if
            } // foreach

            DB::commit('Document hashes updated @ ' . __CLASS__);
          } // if
        } catch(Exception $e) {
          DB::rollback('Failed to update document hashes @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateDocumentHashes

  /**
   * Upgrade public submit
   *
   * @return boolean
   */
  function updatePublicSubmit() {
    try {
      $modules_table = TABLE_PREFIX . 'modules';
      $config_options_table = TABLE_PREFIX . 'config_options';
      $task_forms_table = TABLE_PREFIX . 'public_task_forms';

      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM $modules_table WHERE name='public_submit'")) {
        $public_submit_enabled = DB::executeFirstCell("SELECT value FROM $config_options_table WHERE name = 'public_submit_enabled'");

        if($public_submit_enabled) {
          $public_submit_enabled = (boolean) unserialize($public_submit_enabled);
        } else {
          $public_submit_enabled = false;
        } // if

        $captcha_enabled = DB::executeFirstCell("SELECT value FROM $config_options_table WHERE name = 'public_submit_enable_captcha'");

        if($captcha_enabled) {
          $captcha_enabled = (boolean) unserialize($captcha_enabled);
        } else {
          $captcha_enabled = false;
        } // if

        $public_submit_project = DB::executeFirstCell("SELECT value FROM $config_options_table WHERE name = 'public_submit_default_project'");

        if($public_submit_project) {
          $captcha_enabled = (integer) unserialize($public_submit_project);
        } else {
          $public_submit_project = false;
        } // if

        DB::execute("DELETE FROM $modules_table WHERE name = 'public_submit'");
        DB::execute("DELETE FROM $config_options_table WHERE module = 'public_submit'");
      } else {
        $public_submit_enabled = false;
        $captcha_enabled = false;
        $public_submit_project = 0;
      } // if

      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM $modules_table WHERE name='tasks'")) {
        $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

        DB::execute("CREATE TABLE $task_forms_table (
            id int unsigned NOT NULL auto_increment,
            project_id int(11) unsigned NOT NULL DEFAULT '0',
            slug varchar(50) NOT NULL,
            name varchar(100)  DEFAULT NULL,
            body text,
            is_enabled tinyint(1) unsigned NOT NULL DEFAULT '1',
            raw_additional_properties longtext,
            PRIMARY KEY (id),
            UNIQUE slug (slug)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("INSERT INTO $config_options_table (name, module, value) VALUES (?, ?, ?)", 'tasks_public_submit_enabled', 'tasks', serialize($public_submit_enabled));
        DB::execute("INSERT INTO $config_options_table (name, module, value) VALUES (?, ?, ?)", 'tasks_use_captcha', 'tasks', serialize($captcha_enabled));

        if($public_submit_project && DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'projects WHERE id = ?', $public_submit_project) > 0) {
          DB::execute("INSERT INTO $task_forms_table (project_id, slug, name, is_enabled) VALUES (?, ?, ?, ?)", $public_submit_project, 'public-submit', 'Submit a Request', true);
        } // if
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updatePublicSubmit

  /**
   * Upgrade Source module
   *
   * @return boolean
   */
  function updateSourceModule() {
    try {
      $commits_table = TABLE_PREFIX . 'source_commits';
      $paths_table = TABLE_PREFIX . 'source_paths';
      $repositories_table = TABLE_PREFIX . 'source_repositories';
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $source_users_table = TABLE_PREFIX . 'source_users';
      $activity_logs_table = TABLE_PREFIX . 'activity_logs';

      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='source'")) {
        DB::execute("CREATE TABLE $commits_table (
            id int(10) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) DEFAULT NULL,
            type varchar(25) DEFAULT NULL,
            revision_number int(11) unsigned DEFAULT '0',
            repository_id int(11) unsigned DEFAULT '0',
            message_title text,
            message_body text,
            authored_on datetime DEFAULT NULL,
            authored_by_name varchar(100) DEFAULT NULL,
            authored_by_email varchar(100) DEFAULT NULL,
            commited_on datetime DEFAULT NULL,
            commited_by_name varchar(100) DEFAULT NULL,
            commited_by_email varchar(100) DEFAULT NULL,
            diff text,
            PRIMARY KEY (id),
            KEY repository_id (repository_id),
            KEY commited_on (commited_on)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE $paths_table (
            id int(10) unsigned NOT NULL AUTO_INCREMENT,
            commit_id int(11) unsigned DEFAULT '0',
            is_dir tinyint(1) unsigned NOT NULL DEFAULT '0',
            path varchar(255) DEFAULT NULL,
            action varchar(1) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY commit_id (commit_id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        DB::execute("CREATE TABLE $repositories_table (
            id int(10) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) DEFAULT NULL,
            type varchar(25) DEFAULT NULL,
            created_on datetime DEFAULT NULL,
            created_by_id int(10) unsigned DEFAULT '0',
            created_by_name varchar(100) DEFAULT NULL,
            created_by_email varchar(150) DEFAULT NULL,
            updated_on datetime DEFAULT NULL,
            updated_by_id int(10) unsigned DEFAULT '0',
            updated_by_name varchar(100) DEFAULT NULL,
            updated_by_email varchar(150) DEFAULT NULL,
            repository_path_url varchar(255) DEFAULT NULL,
            username varchar(255) DEFAULT NULL,
            password varchar(255) DEFAULT NULL,
            update_type int(3) DEFAULT '0',
            graph text,
            PRIMARY KEY (id)
          ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $repositories_link = array();
        $repositories = DB::execute("SELECT * FROM $project_objects_table WHERE type = 'Repository'");
        if (is_foreachable($repositories)) {
          foreach ($repositories as $repository) {
            //insert new source repository
            DB::execute("INSERT INTO $repositories_table (name,type,created_on,created_by_id,created_by_name,created_by_email,updated_on,updated_by_id,updated_by_name,updated_by_email,
                repository_path_url,username,password,update_type,graph) VALUES (?, 'SvnRepository', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
              $repository['name'],
              $repository['created_on'],
              $repository['created_by_id'],
              $repository['created_by_name'],
              $repository['created_by_email'],
              $repository['updated_on'],
              $repository['updated_by_id'],
              $repository['updated_by_name'],
              $repository['updated_by_email'],
              $repository['text_field_1'],
              $repository['varchar_field_1'],
              $repository['varchar_field_2'],
              $repository['integer_field_2'],
              $repository['text_field_2']
            );

            // Update project object repository
            $last_insert_id = DB::lastInsertId();
            DB::execute("UPDATE $project_objects_table SET type = 'ProjectSourceRepository', parent_id = ? WHERE id = ?", $last_insert_id, $repository['id']);

            // Update source user links
            DB::execute("UPDATE $source_users_table SET repository_id = ? WHERE repository_id = ?", $last_insert_id, $repository['id']);
          } //foreach
        } //if


        if (extension_loaded('svn')) {
          $svn_type = 'extension';
        } elseif (extension_loaded('xml') && function_exists('xml_parser_create')) {
          $svn_type = 'exec';
        } else {
          $svn_type = 'none';
        } // if

        // Update configuration options
        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?), (?, ?, ?)',
          'source_svn_type', 'source', serialize($svn_type),
          'source_mercurial_path', 'source', serialize('/usr/bin/')
        );
      } //if
      // Delete all commits from project object
      DB::execute("DELETE FROM $project_objects_table WHERE type = 'Commit'");

      // Delete all activity logs
      DB::execute("DELETE FROM $activity_logs_table WHERE type = 'RepositoryUpdateActivityLog'");

      //delete unnecessary configuration options
      DB::execute("DELETE FROM " . TABLE_PREFIX . "config_options WHERE name = 'source_svn_trust_server_cert' OR name = 'source_svn_use_output_redirect'");

    } catch (Exception $e) {
      return $e->getMessage();
    } //try

    return true;
  } //updateSourceModule

  /**
   * Finalize module upgrade
   *
   * @return boolean
   */
  function finalizeModuleUpgrade() {
    $modules_table = TABLE_PREFIX . 'modules';
    $config_options_table = TABLE_PREFIX . 'config_options';

    try {

      // Remove merged modules
      DB::execute("DELETE FROM $modules_table WHERE name IN (?)", array('incoming_mail', 'milestones', 'mobile_access', 'resources'));

      // Update incoming mail related settings
      DB::execute("UPDATE $config_options_table SET module = ? WHERE module = ?", 'system', 'incoming_mail');

      // Uninstall backup module
      DB::execute("DELETE FROM $modules_table WHERE name = ?", 'backup');
      DB::execute("DELETE FROM $config_options_table WHERE module = ?", 'backup');

      // Enable only first party modules
      DB::execute("DELETE FROM $modules_table WHERE name NOT IN (?)", array('system', 'discussions', 'milestones', 'files', 'todo', 'calendar', 'notebooks', 'tasks', 'tracking', 'project_exporter', 'status', 'documents', 'source', 'invoicing'));
      DB::execute("UPDATE $modules_table SET is_enabled = ?", true);
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // finalizeModuleUpgrade

  /**
   * Rebuild localization index
   *
   * @return boolean
   */
  function rebuildLocalizationIndex() {
    return true; // @TODO When we have index build for localization, implement this
  } // rebuildLocalizationIndex

  /**
   * Prepare storage engine for content backup
   *
   * @return boolean
   */
  function prepareBodyBackup() {
    try {
      $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

      DB::execute("CREATE TABLE " . TABLE_PREFIX . "content_backup (
          id int(11) unsigned NOT NULL auto_increment,
          parent_type varchar(50) default NULL,
          parent_id int(10) unsigned default NULL,
          body longtext,
          PRIMARY KEY  (id)
        ) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // prepareBodyBackup

  /**
   * Update project summaries
   *
   * @return boolean
   */
  function upgradeProjectSummaries() {
    try {
      $projects_table = TABLE_PREFIX . 'projects';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating project summaries @ ' . __CLASS__);

      $rows = DB::execute("SELECT id, overview FROM $projects_table");
      if($rows) {
        foreach($rows as $row) {
          if($row['overview']) {
            DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('Project', ?, ?)", $row['id'], $row['overview']);
            DB::execute("UPDATE $projects_table SET overview = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['overview']));
          } // if
        } // foreach
      } // if

      DB::commit('Project summaries updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update project summaries @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // upgradeProjectSummaries

  /**
   * Update global documents content
   *
   * @return boolean
   */
  function updateDocumentsContent() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='documents'")) {
        try {
          $documents_table = TABLE_PREFIX . 'documents';
          $content_backup_table = TABLE_PREFIX . 'content_backup';

          DB::beginWork('Updating documents content @ ' . __CLASS__);

          $rows = DB::execute("SELECT id, body FROM $documents_table WHERE type = 'text'");
          if($rows) {
            foreach($rows as $row) {
              if($row['body']) {
                DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('Document', ?, ?)", $row['id'], $row['body']);
                DB::execute("UPDATE $documents_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
              } // if
            } // foreach
          } // if

          DB::commit('Documents content updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update documents content @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateDocumentsContent

  /**
   * Update open task descriptions
   *
   * @return boolean
   */
  function updateOpenTaskDescriptions() {
    try {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating task descriptions @ ' . __CLASS__);

      $rows = DB::execute("SELECT id, body FROM $project_objects_table WHERE type = 'Task' AND completed_on IS NULL AND body != '' AND body IS NOT NULL");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('Task', ?, ?)", $row['id'], $row['body']);
          DB::execute("UPDATE $project_objects_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
        } // foreach
      } // if

      DB::commit('Task descriptions updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to upgrade task descriptions @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateOpenTaskDescriptions

  /**
   * Update completed task descriptions
   *
   * @return boolean
   */
  function updateCompletedTaskDescriptions() {
    try {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating task descriptions @ ' . __CLASS__);

      $rows = DB::execute("SELECT id, body FROM $project_objects_table WHERE type = 'Task' AND completed_on IS NOT NULL AND body != '' AND body IS NOT NULL");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('Task', ?, ?)", $row['id'], $row['body']);
          DB::execute("UPDATE $project_objects_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
        } // foreach
      } // if

      DB::commit('Task descriptions updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to upgrade task descriptions @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateCompletedTaskDescriptions

  /**
   * Update open task comments
   *
   * @return boolean
   */
  function updateOpenTaskComments() {
    try {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $comments_table = TABLE_PREFIX . 'comments';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating open task comments @ ' . __CLASS__);

      $rows = DB::execute("SELECT $comments_table.id, $comments_table.body FROM $comments_table, $project_objects_table WHERE $project_objects_table.type = $comments_table.parent_type AND $project_objects_table.id = $comments_table.parent_id AND $project_objects_table.type = 'Task' AND $project_objects_table.completed_on IS NULL");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('TaskComment', ?, ?)", $row['id'], $row['body']);
          DB::execute("UPDATE $comments_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
        } // foreach
      } // if

      DB::commit('Open task comments updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update open task comments @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateOpenTaskComments

  /**
   * Update completed task comments
   *
   * @return boolean
   */
  function updateCompletedTaskComments() {
    try {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $comments_table = TABLE_PREFIX . 'comments';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating completed task comments @ ' . __CLASS__);

      $rows = DB::execute("SELECT $comments_table.id, $comments_table.body FROM $comments_table, $project_objects_table WHERE $project_objects_table.type = $comments_table.parent_type AND $project_objects_table.id = $comments_table.parent_id AND $project_objects_table.type = 'Task' AND $project_objects_table.completed_on IS NOT NULL");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('TaskComment', ?, ?)", $row['id'], $row['body']);
          DB::execute("UPDATE $comments_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
        } // foreach
      } // if

      DB::commit('Completed task comments updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update completed task comments @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateCompletedTaskComments

  /**
   * Update discussion descriptions
   *
   * @return boolean
   */
  function updateDiscussionDescriptions() {
    try {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating discussion descriptions @ ' . __CLASS__);

      $rows = DB::execute("SELECT id, body FROM $project_objects_table WHERE type = 'Discussion' AND body != '' AND body IS NOT NULL");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('Discussion', ?, ?)", $row['id'], $row['body']);
          DB::execute("UPDATE $project_objects_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
        } // foreach
      } // if

      DB::commit('Discussion descriptions updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to upgrade discussion descriptions @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateDiscussionDescriptions

  /**
   * Update discussion comments
   *
   * @return boolean
   */
  function updateDiscussionComments() {
    try {
      $comments_table = TABLE_PREFIX . 'comments';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating discussion comments @ ' . __CLASS__);

      $rows = DB::execute("SELECT id, body FROM $comments_table WHERE parent_type = 'Discussion'");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('DiscussionComment', ?, ?)", $row['id'], $row['body']);
          DB::execute("UPDATE $comments_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
        } // foreach
      } // if

      DB::commit('Discussion comments updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update discussion comments @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateDiscussionComments

  /**
   * Update discussion comments
   *
   * @return boolean
   */
  function updateNotebookPagesComments() {
    try {
      $comments_table = TABLE_PREFIX . 'comments';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating notebook pages comments @ ' . __CLASS__);

      $rows = DB::execute("SELECT id, body FROM $comments_table WHERE parent_type = 'NotebookPage'");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('NotebookPageComment', ?, ?)", $row['id'], $row['body']);
          DB::execute("UPDATE $comments_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
        } // foreach
      } // if

      DB::commit('Notebook pages comments updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update notebook pages comments @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateNotebookPagesComments

  /**
   * Update file descriptions
   *
   * @return boolean
   */
  function updateFileDescriptions() {
    try {
      $project_objects_table = TABLE_PREFIX . 'project_objects';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating file descriptions @ ' . __CLASS__);

      $rows = DB::execute("SELECT id, body FROM $project_objects_table WHERE type = 'File' AND body != '' AND body IS NOT NULL");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('File', ?, ?)", $row['id'], $row['body']);
          DB::execute("UPDATE $project_objects_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
        } // foreach
      } // if

      DB::commit('File descriptions updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to upgrade file descriptions @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateFileDescriptions

  /**
   * Update file comments
   *
   * @return boolean
   */
  function updateFileComments() {
    try {
      $comments_table = TABLE_PREFIX . 'comments';
      $content_backup_table = TABLE_PREFIX . 'content_backup';

      DB::beginWork('Updating file comments @ ' . __CLASS__);

      $rows = DB::execute("SELECT id, body FROM $comments_table WHERE parent_type = 'File'");
      if($rows) {
        foreach($rows as $row) {
          DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('AssetComment', ?, ?)", $row['id'], $row['body']);
          DB::execute("UPDATE $comments_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
        } // foreach
      } // if

      DB::commit('File comments updated @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update file comments @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateFileComments

  /**
   * Update file descriptions
   *
   * @return boolean
   */
  function updateFirstLevelPageContent() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='notebooks'")) {
        try {
          $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';
          $content_backup_table = TABLE_PREFIX . 'content_backup';

          DB::beginWork('Updating first level notebook pages @ ' . __CLASS__);

          $rows = DB::execute("SELECT id, body FROM $notebook_pages_table WHERE parent_type = 'Notebook' AND body != '' AND body IS NOT NULL");
          if($rows) {
            foreach($rows as $row) {
              DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('NotebookPage', ?, ?)", $row['id'], $row['body']);
              DB::execute("UPDATE $notebook_pages_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
            } // foreach
          } // if

          DB::commit('First level notebook pages updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update first level notebook pages @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateFirstLevelPageContent

  /**
   * Update file descriptions
   *
   * @return boolean
   */
  function updateFirstLevelPageVersions() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='notebooks'")) {
        try {
          $pages_table = TABLE_PREFIX . 'notebook_pages';
          $page_versions_table = TABLE_PREFIX . 'notebook_page_versions';
          $content_backup_table = TABLE_PREFIX . 'content_backup';

          DB::beginWork('Updating first level notebook page versions @ ' . __CLASS__);

          $rows = DB::execute("SELECT $page_versions_table.id, $page_versions_table.body FROM $pages_table, $page_versions_table WHERE $pages_table.id = $page_versions_table.notebook_page_id AND $pages_table.parent_type = 'Notebook'");
          if($rows) {
            foreach($rows as $row) {
              DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('NotebookPageVersion', ?, ?)", $row['id'], $row['body']);
              DB::execute("UPDATE $page_versions_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
            } // foreach
          } // if

          DB::commit('First level notebook page versions updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update first level notebook page versions @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateFirstLevelPageVersions

  /**
   * Update first level page comments
   *
   * @return boolean
   */
  function updateFirstLevelPageComments() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='notebooks'")) {
        try {
          $pages_table = TABLE_PREFIX . 'notebook_pages';
          $comments_table = TABLE_PREFIX . 'comments';
          $content_backup_table = TABLE_PREFIX . 'content_backup';

          DB::beginWork('Updating first level notebook page comments @ ' . __CLASS__);

          $rows = DB::execute("SELECT $comments_table.id, $comments_table.body FROM $pages_table, $comments_table WHERE $pages_table.parent_type = 'Noteobok' AND $pages_table.id = $comments_table.parent_id AND $comments_table.parent_type = 'NotebookPage'");
          if($rows) {
            foreach($rows as $row) {
              DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('NotebookPageComment', ?, ?)", $row['id'], $row['body']);
              DB::execute("UPDATE $comments_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
            } // foreach
          } // if

          DB::commit('First level notebook page comments updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update first level notebook page comments  @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateFirstLevelPageComments

  /**
   * Update subpages content
   *
   * @return boolean
   */
  function updateSubpagesContent() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='notebooks'")) {
        try {
          $notebook_pages_table = TABLE_PREFIX . 'notebook_pages';
          $content_backup_table = TABLE_PREFIX . 'content_backup';

          DB::beginWork('Updating subpages @ ' . __CLASS__);

          $rows = DB::execute("SELECT id, body FROM $notebook_pages_table WHERE parent_type = 'NotebookPage' AND body != '' AND body IS NOT NULL");
          if($rows) {
            foreach($rows as $row) {
              DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('NotebookPage', ?, ?)", $row['id'], $row['body']);
              DB::execute("UPDATE $notebook_pages_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
            } // foreach
          } // if

          DB::commit('Subpages updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update subpages @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateSubpagesContent

  /**
   * Update file descriptions
   *
   * @return boolean
   */
  function updateSubpageVersions() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='notebooks'")) {
        try {
          $pages_table = TABLE_PREFIX . 'notebook_pages';
          $page_versions_table = TABLE_PREFIX . 'notebook_page_versions';
          $content_backup_table = TABLE_PREFIX . 'content_backup';

          DB::beginWork('Updating subpage versions @ ' . __CLASS__);

          $rows = DB::execute("SELECT $page_versions_table.id, $page_versions_table.body FROM $pages_table, $page_versions_table WHERE $pages_table.id = $page_versions_table.notebook_page_id AND $pages_table.parent_type = 'NotebookPage'");
          if($rows) {
            foreach($rows as $row) {
              DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('NotebookPageVersion', ?, ?)", $row['id'], $row['body']);
              DB::execute("UPDATE $page_versions_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
            } // foreach
          } // if

          DB::commit('Subpage versions updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update subpage versions @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateSubpageVersions

  /**
   * Update first level page comments
   *
   * @return boolean
   */
  function updateSubpageComments() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) AS count FROM " . TABLE_PREFIX . "modules WHERE name='notebooks'")) {
        try {
          $pages_table = TABLE_PREFIX . 'notebook_pages';
          $comments_table = TABLE_PREFIX . 'comments';
          $content_backup_table = TABLE_PREFIX . 'content_backup';

          DB::beginWork('Updating subpage comments @ ' . __CLASS__);

          $rows = DB::execute("SELECT $comments_table.id, $comments_table.body FROM $pages_table, $comments_table WHERE $pages_table.parent_type = 'NoteobokPage' AND $pages_table.id = $comments_table.parent_id AND $comments_table.parent_type = 'NotebookPage'");
          if($rows) {
            foreach($rows as $row) {
              DB::execute("INSERT INTO $content_backup_table (parent_type, parent_id, body) VALUES ('NotebookPageComment', ?, ?)", $row['id'], $row['body']);
              DB::execute("UPDATE $comments_table SET body = ? WHERE id = '$row[id]'", $this->updateHtmlContent($row['body']));
            } // foreach
          } // if

          DB::commit('Subpage comments updated @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to update subpage comments @ ' . __CLASS__);
          return $e->getMessage();
        } // try
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateSubpageComments

}
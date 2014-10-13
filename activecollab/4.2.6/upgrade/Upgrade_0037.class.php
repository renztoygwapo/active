<?php

	/**
   * Update activeCollab 3.1.4 to activeCollab 3.1.5
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0037 extends AngieApplicationUpgradeScript {
    
    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.1.4';
    
    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.1.5';

    /**
     * Return date when activeCollab 3 was installed or upgraded
     *
     * @return mixed
     */
    private function getVersion3InstallationDate() {
      return DB::executeFirstCell('SELECT created_on FROM ' . TABLE_PREFIX . "update_history WHERE version LIKE '3.%' ORDER BY version DESC LIMIT 0, 1");
    } // getVersion3InstallationDate

    /**
     * Clean up HTML from a given table
     *
     * @param string $table_name
     * @param string $body_field
     * @param string $created_by_field
     * @param string $updated_by_field
     * @param string $required_module
     * @return bool|string
     */
    private function cleanUpHtml($table_name, $body_field = 'body', $created_by_field = 'created_by', $updated_by_field = null, $required_module = null) {
      if($required_module && !$this->isModuleInstalled($required_module)) {
        return true;
      } // if

      $since = $this->getVersion3InstallationDate();

      if($since) {
        try {
          if($created_by_field && $updated_by_field) {
            $rows = DB::execute("SELECT id, $body_field FROM $table_name WHERE $created_by_field >= ? OR $updated_by_field >= ?", $since, $since);
          } elseif($created_by_field) {
            $rows = DB::execute("SELECT id, $body_field FROM $table_name WHERE $created_by_field >= ?", $since);
          } else {
            $rows = DB::execute("SELECT id, $body_field FROM $table_name");
          } // if

          if($rows) {
            try {
              DB::beginWork('Cleaning up HTML @ ' . __CLASS__);

              foreach($rows as $row) {
                DB::execute("UPDATE $table_name SET $body_field = ? WHERE id = ?", HTML::cleanUpHtml($row[$body_field]), $row['id']);
              } // foreach

              DB::commit('HTML cleaned up @ ' . __CLASS__);
            } catch(Exception $e) {
              DB::rollback('Failed to clean up HTML @ ' . __CLASS__);
              return $e->getMessage();
            } // try
          } // if

          return true;
        } catch(Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // cleanUpHtml

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      if($this->getVersion3InstallationDate()) {
        $actions = array(
          'cleanUpDocumentBodies' => 'Clean up documents',
          'cleanUpCommentBodies' => 'Clean up comments',
          'cleanUpNotebookPageBodies' => 'Clean up notebook pages',
          'cleanUpNotebookPageVersionBodies' => 'Clean up notebook page versions',
          'cleanUpProjectSummaries' => 'Clean up project summaries',
          'cleanUpProjectRequestSummaries' => 'Clean up project request summaries',
          'cleanUpProjectObjectBodies' => 'Clean up project objects',
          'cleanUpTextDocumentVersionBodies' => 'Clean up text document versions',
        );
      } else {
        $actions = array();
      };

      $actions['extendDocumentsNameField'] = 'Extends the name field of documents';
      $actions['updateUserAndCompanyUniqueKeys'] = 'Update user and company keys';
      $actions['updateConfigOptions'] = 'Update configuration options';
      $actions['changeRecurringProfileOccurrencesType'] = 'Change recurring profile occurrence field type';

      return $actions;
    } // getActions

    /**
     * Clean up document bodies
     *
     * @return bool|string
     */
    function cleanUpDocumentBodies() {
      return $this->cleanUpHtml(TABLE_PREFIX . 'documents', 'body', 'created_on', null, 'documents');
    } // cleanUpDocumentBodies

    /**
     * Clean up document bodies
     *
     * @return bool|string
     */
    function cleanUpCommentBodies() {
      return $this->cleanUpHtml(TABLE_PREFIX . 'comments', 'body', 'created_on', 'updated_on');
    } // cleanUpCommentBodies

    /**
     * Clean up document bodies
     *
     * @return bool|string
     */
    function cleanUpNotebookPageBodies() {
      return $this->cleanUpHtml(TABLE_PREFIX . 'notebook_pages', 'body', 'created_on', 'updated_on', 'notebooks');
    } // cleanUpNotebookPageBodies

    /**
     * Clean up document bodies
     *
     * @return bool|string
     */
    function cleanUpNotebookPageVersionBodies() {
      return $this->cleanUpHtml(TABLE_PREFIX . 'notebook_page_versions', 'body', 'created_on', null, 'notebooks');
    } // cleanUpNotebookPageVersionBodies

    /**
     * Clean up document bodies
     *
     * @return bool|string
     */
    function cleanUpProjectSummaries() {
      return $this->cleanUpHtml(TABLE_PREFIX . 'projects', 'overview', 'created_on', 'updated_on');
    } // cleanUpProjectSummaries

    /**
     * Clean up document bodies
     *
     * @return bool|string
     */
    function cleanUpProjectRequestSummaries() {
      return $this->cleanUpHtml(TABLE_PREFIX . 'project_requests', 'body', 'created_on');
    } // cleanUpProjectRequestSummaries

    /**
     * Clean up document bodies
     *
     * @return bool|string
     */
    function cleanUpProjectObjectBodies() {
      return $this->cleanUpHtml(TABLE_PREFIX . 'project_objects', 'body', 'created_on', 'updated_on');
    } // cleanUpProjectObjectBodies

    /**
     * Clean up document bodies
     *
     * @return bool|string
     */
    function cleanUpTextDocumentVersionBodies() {
      return $this->cleanUpHtml(TABLE_PREFIX . 'text_document_versions', 'body', 'created_on', null, 'files');
    } // cleanUpTextDocumentVersionBodies

    /**
     * Extends the name field of documents
     *
     * @return bool|string
     */
    function extendDocumentsNameField() {
      try {
        if($this->isModuleInstalled('documents')) {
          DB::execute("ALTER TABLE " . TABLE_PREFIX . "documents CHANGE name name VARCHAR(150) NULL DEFAULT NULL");
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // extendDocumentsNameField

    /**
     * Update user and company unique strings
     *
     * @return bool|string
     */
    function updateUserAndCompanyUniqueKeys() {
      try {
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "users DROP INDEX email");
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "users ADD INDEX (email)");
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "companies ADD INDEX (name)");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateUserAndCompanyUniqueKeys

    /**
     * Update configuration options
     *
     * @return bool|string
     */
    function updateConfigOptions() {
      try {
        if(DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', 'clients_can_delegate_to_employees') < 1) {
          DB::execute("INSERT INTO " . TABLE_PREFIX . "config_options (name, module, value) VALUES ('clients_can_delegate_to_employees', 'system', 'b:1;')");
        } //if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateConfigOptions
    
    /**
     * Change occurrence field type to int
     *
     * @return bool|string
     */
    function changeRecurringProfileOccurrencesType() {
      try {
        $table = TABLE_PREFIX . "recurring_profiles";
        if($this->isModuleInstalled('invoicing')) {
          DB::execute("ALTER TABLE $table CHANGE occurrences occurrences INT(10) NULL DEFAULT NULL");
        }//if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // changeRecurringProfileOccurrencesType

  }
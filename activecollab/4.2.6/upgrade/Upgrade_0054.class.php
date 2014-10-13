<?php

  /**
   * Update activeCollab 3.2.5 to activeCollab 3.2.6
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0054 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.2.5';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.6';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateSourceBranching' => 'Upgrade source branching',
      );
    } // getActions

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

          if (!in_array('branch_name', $this->listTableFields($source_commits_table))) {
            DB::execute("ALTER TABLE $source_commits_table ADD branch_name VARCHAR(255) DEFAULT '' AFTER commited_by_email");
            DB::execute("ALTER TABLE $source_commits_table ADD INDEX (branch_name)");
          } // if

          if (!in_array('branch_name', $this->listTableFields($commit_project_objects_table))) {
            DB::execute("ALTER TABLE $commit_project_objects_table ADD branch_name VARCHAR(255) DEFAULT '' AFTER revision");
          } // if

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

  }
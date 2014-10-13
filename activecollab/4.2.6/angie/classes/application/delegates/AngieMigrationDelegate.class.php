<?php

  require_once ANGIE_PATH . '/classes/application/migrations/AngieModelMigration.class.php';
  require_once ANGIE_PATH . '/classes/application/migrations/AngieModelMigrationDiscoverer.class.php';
  require_once ANGIE_PATH . '/classes/application/migrations/MigrationDnxError.class.php';

  /**
   * Angie migration delegate
   *
   * @package angie.library.application
   * @subpackage delegates
   */
  class AngieMigrationDelegate extends AngieDelegate {

    /**
     * Migrate the database up
     *
     * @param boolean $dry_run
     * @param Output $output
     * @return array
     */
    function up($dry_run = false, $output = null) {
      $batch = array();

      foreach($this->getScripts() as $migrations) {
        foreach($migrations as $migration) {
          if($migration instanceof AngieModelMigration) {
            $this->executeMigrationUp($migration, $batch, $dry_run, $output);
          } // if
        } // foreach
      } // foreach

      return $batch;
    } // up

    /**
     * Move down
     *
     * @param string $timestamp
     * @param bool $dry_run
     * @param null $output
     * @return array
     */
    function down($timestamp = null, $dry_run = false, $output = null) {
      if($timestamp) {
        $executed_migrations = DB::execute('SELECT migration, changeset_timestamp, changeset_name FROM ' . TABLE_PREFIX . 'executed_model_migrations WHERE changeset_timestamp >= ? ORDER BY id DESC', $timestamp);
      } else {
        $executed_migrations = DB::execute('SELECT migration, changeset_timestamp, changeset_name FROM ' . TABLE_PREFIX . 'executed_model_migrations ORDER BY id DESC');
      } // if

      $batch = array();

      if($executed_migrations) {
        foreach($executed_migrations as $executed_migration_details) {
          $executed_migration = $this->getScript($executed_migration_details['changeset_timestamp'] . '-' . $executed_migration_details['changeset_name'], $executed_migration_details['migration']);

          if($executed_migration instanceof AngieModelMigration) {
            $executed_migration->setDryRun($dry_run);
            $executed_migration->setOutput($output);

            $executed_migration->down();

            $executed_migration->setAsNotExecuted();
            $batch[] = get_class($executed_migration);
          } //if
        } // foreach
      } // if

      return $batch;
    } // down

    /**
     * Trigger one migration up
     *
     * @param AngieModelMigration $migration
     * @param array $batch
     * @param bool $dry_run
     * @param Output $output
     * @throws MigrationDnxError
     * @throws Exception
     */
    private function executeMigrationUp(AngieModelMigration $migration, array &$batch, $dry_run = false, $output = null) {
      $migration_name = get_class($migration);

      if(in_array($migration_name, $batch)) {
        return;
      } // if

      $changeset = $migration->getChangeset();

      if($migration->isExecuted()) {
        if($output instanceof Output) {
          $output->printMessage("Script '$migration_name' from '$changeset' change-set has been skipped (already executed)");
        } // if

        return;
      } // if

      $execute_after_migrations = $migration->getExecuteAfter();

      if(is_foreachable($execute_after_migrations)) {
        if($output instanceof Output) {
          $output->printMessage("Migration '$migration_name' needs to be executed after these migrations: " . implode(', ', $execute_after_migrations));
        } // if

        foreach($execute_after_migrations as $execute_after_migration_name) {
          $execute_after_migration = $this->getScript($changeset, $execute_after_migration_name);

          if($execute_after_migration instanceof AngieModelMigration) {
            $this->executeMigrationUp($execute_after_migration, $batch, $dry_run, $output);
          } else {
            throw new MigrationDnxError($execute_after_migration_name, $changeset);
          } // if
        } // foreach
      } // if

      $migration->setDryRun($dry_run);
      $migration->setOutput($output);

      if($output instanceof Output) {
        $output->printMessage("Ready to execute '$migration_name'");
      } // if

      try {
        $migration->up();
        $migration->setAsExecuted();

        $batch[] = $migration_name;

        if($output instanceof Output) {
          $output->printMessage("Migration '$migration_name' has been set as executed");
        } // if
      } catch(Exception $e) {
        $migration->cleanUpUsedTableCopies();
        throw $e;
      } // if
    } // executeMigrationUp

    /**
     * Loaded list of migration scripts
     *
     * @var array
     */
    private $scripts = false;

    /**
     * Return a list of migration scripts
     *
     * @return AngieModelMigration[]
     */
    function getScripts() {
      if($this->scripts === false) {
        $this->scripts = AngieModelMigrationDiscoverer::discover(); // Discover migration scripts for currently installed version
      } // if

      return $this->scripts;
    } // getScripts

    /**
     * Return scripts form a given module
     *
     * @param AngieModule $module
     * @return AngieModelMigration[]
     */
    function getScriptsInModule(AngieModule $module) {
      return AngieModelMigrationDiscoverer::discoverFromPaths(array($module->getPath() . '/migrations'));
    } // getScriptsInModule

    /**
     * Return a particular script
     *
     * @param string $changeset
     * @param string $script
     * @return AngieModelMigration
     */
    function getScript($changeset, $script) {
      if($this->scripts === false) {
        $this->getScripts();
      } // if

      return isset($this->scripts[$changeset]) && isset($this->scripts[$changeset][$script]) && $this->scripts[$changeset][$script] instanceof AngieModelMigration ? $this->scripts[$changeset][$script] : null;
    } // getScript

    /**
     * Return time stamp from a given change-set name
     *
     * @param string $name
     * @return string|bool
     */
    function getChangesetTimestamp($name) {
      $matches = array();

      if(preg_match('/^(\d{4})-(\d{2})-(\d{2})-(.*)$/', $name, $matches)) {
        return "$matches[1]-$matches[2]-$matches[3]";
      } // if

      return false;
    } // getChangesetTimestamp

  }
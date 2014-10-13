<?php

  /**
   * Update activeCollab 4.1.0 to the latest stable version
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0096 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.1.0';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.2.6';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      $result = array();

      foreach($this->getMigrationScripts() as $script) {
        if(!$script->isExecuted()) {
          $result[get_class($script)] = $script->getDescription();
        } // if
      } // foreach

      $result['endUpgrade'] = 'Finish upgrade';

      return $result;
    } // getActions

    /**
     * Call a method
     *
     * @param string $name
     * @param mixed $arguments
     * @return string
     */
    function __call($name, $arguments) {
      if(str_starts_with($name, 'Migrate')) {
        $this->getMigrationScripts();

        if(isset($this->migrations[$name]) && $this->migrations[$name] instanceof AngieModelMigration) {
          if($this->migrations[$name]->isExecuted()) {
            return true;
          } // if

          try {
            $this->migrations[$name]->up();
            $this->migrations[$name]->setAsExecuted();
          } catch(Exception $e) {
            return $e->getMessage();
          } // try

          return true;
        } else {
          return "'$name' is not a valid migration'";
        } // if
      } else {
        return "'$name' is not a valid migration'";
      } // if
    } // __call

    /**
     * Migrations list
     *
     * @var AngieModelMigration[]
     */
    private $migrations = false;

    /**
     * Return migration scripts
     *
     * @return AngieModelMigration[]
     */
    function getMigrationScripts() {
      if($this->migrations === false) {

        // Include discoverer in case we are upgrading from an older release which is not yet aware of migrations subsystem
        if(!class_exists('AngieModelMigration', false) || !class_exists('AngieModelMigrationDiscoverer', false)) {
          require_once ROOT . '/' . $this->getToVersion() . '/angie/classes/application/migrations/AngieModelMigration.class.php';
          require_once ROOT . '/' . $this->getToVersion() . '/angie/classes/application/migrations/AngieModelMigrationDiscoverer.class.php';
        } // if

        // Make sure that we have a execution log table available before we start loading migrations
        if(!DB::tableExists(TABLE_PREFIX . 'executed_model_migrations')) {
          $engine = defined('DB_CAN_TRANSACT') && DB_CAN_TRANSACT ? 'InnoDB' : 'MyISAM';

          DB::execute("CREATE TABLE " . TABLE_PREFIX . "executed_model_migrations (
            id smallint(5) unsigned NOT NULL auto_increment,
            migration varchar(255) NOT NULL default '',
            changeset_timestamp date default NULL,
            changeset_name varchar(255) default NULL,
            executed_on datetime default NULL,
            PRIMARY KEY (id),
            UNIQUE KEY migration (migration),
            KEY executed_on (executed_on)
          ) ENGINE=$engine DEFAULT CHARSET=utf8;");
        } // if

        // Get all defined migration scripts
        $all_scripts = array();

        foreach(AngieModelMigrationDiscoverer::discover($this->getToVersion()) as $migrations) {
          foreach($migrations as $migration) {
            $all_scripts[] = $migration;
          } // foreach
        } // foreach

        // Sort and add to the list in order of exexution
        $this->migrations = array();

        foreach($all_scripts as $script) {
          $this->addToMigrationsList($script, $all_scripts);
        } // foreach
      } // if

      return $this->migrations;
    } // getMigrationScripts

    /**
     * Add to the list of migrations that need to be executed
     *
     * @param AngieModelMigration $script
     * @param AngieModelMigration[] $all_scripts
     */
    private function addToMigrationsList(AngieModelMigration $script, $all_scripts) {
      $execute_after_script_names = $script->getExecuteAfter();

      if($execute_after_script_names) {
        foreach($execute_after_script_names as $execute_after_script_name) {
          if(isset($all_scripts[$execute_after_script_name]) && $all_scripts[$execute_after_script_name] instanceof AngieModelMigration) {
            $this->addToMigrationsList($all_scripts[$execute_after_script_name], $all_scripts);
          } // if
        } // foreach
      } // if

      if(!isset($this->migrations[get_class($script)])) {
        $this->migrations[get_class($script)] = $script;
      } // if
    } // addToMigrationsList

  }
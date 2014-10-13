<?php

  require_once ANGIE_PATH . '/classes/application/migrations/AngieModelMigration.class.php';

  /**
   * Migrate test implementation
   */
  class MigrateTest extends AngieModelMigration {

    /**
     * Construct the migration
     */
    function __construct() {
      $this->setCopyUsedTables(true);
    } // __construct
    
    function up() {
      $copy = $this->useTable('test_table');

      DB::execute("INSERT INTO $copy (name) VALUES ('Test Migration')");

      $this->doneUsingTables('test_table');
    } // up
    
  }

  /**
   * Test migrations
   *
   * @package
   * @subpackage
   */
  class TestMigrations extends AngieModelTestCase {

    private $test_table = 'test_table';
    private $test_table_with_prefix;

    /**
     * Construct the test case
     *
     * @param bool $label
     */
    function __construct($label = false) {
      parent::__construct($label);

      $this->test_table_with_prefix = TABLE_PREFIX . $this->test_table;
    } // __construct

    /**
     * Set up test case
     */
    function setUp() {
      parent::setUp();

      DB::execute("CREATE TABLE $this->test_table_with_prefix (
        id int(10) unsigned NOT NULL auto_increment,
        name varchar(100) NOT NULL,
        PRIMARY KEY  (id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    } // setUp

    /**
     * Tear down test case
     */
    function tearDown() {
      DB::execute("DROP TABLE $this->test_table_with_prefix");

      parent::tearDown();
    } // tearDown

    /**
     * Test use template copy creation
     */
    function testUseTable() {
      $tables = DB::listTables(TABLE_PREFIX);

      $this->assertTrue(in_array($this->test_table_with_prefix, $tables));

      $migration = new MigrateTest();

      $copy_table_name = $migration->useTable($this->test_table);

      $this->assertEqual($copy_table_name, $this->test_table_with_prefix . '_migrate_test');
      $this->assertTrue(in_array($copy_table_name, DB::listTables(TABLE_PREFIX)));

      $migration = new MigrateTest();
      $migration->setCopyUsedTables(false);

      $copy_table_name = $migration->useTable($this->test_table);

      $this->assertEqual($copy_table_name, $this->test_table_with_prefix);
    } // testUseTable

    /**
     * Test simple migration
     */
    function testSampleMigration() {
      $this->assertEqual(0, (integer) DB::executeFirstCell("SELECT COUNT(*) FROM $this->test_table_with_prefix"));

      $migration = new MigrateTest();
      $this->assertEqual($migration->getUsedTables(), array());
      $migration->up();
      $this->assertEqual($migration->getUsedTables(), array());

      $this->assertEqual(1, (integer) DB::executeFirstCell("SELECT COUNT(*) FROM $this->test_table_with_prefix"));
    } // testSampleMigration
    
  }
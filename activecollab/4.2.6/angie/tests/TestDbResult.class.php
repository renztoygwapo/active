<?php

  /**
   * Database result test case
   */
  class TestDbResult extends UnitTestCase {
    
    /**
     * Result set
     *
     * @var DBResult
     */
    private $result;
    
    /**
     * Constructor
     */
    function __construct() {
      parent::__construct('Test database result');
    } // __construct
    
    /**
     * Create table for test data
     */
    function setUp() {
      $test_data_objects_table = TABLE_PREFIX . 'test_data_objects';
      
      DB::execute("CREATE TABLE $test_data_objects_table (
        id int(10) unsigned NOT NULL auto_increment,
        name varchar(100) NOT NULL,
        description text,
        type enum('Task','Milestone','Message','File') NOT NULL default 'File',
        created_on datetime NULL,
        updated_on datetime NULL,
        PRIMARY KEY  (id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
      
      DB::getConnection('default')->execute("INSERT INTO $test_data_objects_table (id, name) VALUES (1, 'Belgrade'), (2, 'Novi Sad'), (3, 'Sombor'), (4, 'Subotica')");
      
      $this->result = DB::getConnection('default')->execute("SELECT id, name FROM $test_data_objects_table ORDER BY id");
      
      $this->assertIsA($this->result, 'DBResult');
      $this->assertEqual($this->result->count(), 4);
    } // setUp
    
    /**
     * Tear down after test function has been executed
     */
    function tearDown() {
      DB::execute("DROP TABLE IF EXISTS " . TABLE_PREFIX . "test_data_objects");
    } // tearDown
    
    function testIterator() {
      // Lets do two iteration over the result...
      $i = 0;
      do {
        $iteration = 0;
        foreach($this->result as $row) {
          switch($iteration) {
            case 0:
              $this->assertEqual($row['id'], 1);
              $this->assertEqual($row['name'], 'Belgrade');
              break;
            case 1:
              $this->assertEqual($row['id'], 2);
              $this->assertEqual($row['name'], 'Novi Sad');
              break;
            case 2:
              $this->assertEqual($row['id'], 3);
              $this->assertEqual($row['name'], 'Sombor');
              break;
            case 3:
              $this->assertEqual($row['id'], 4);
              $this->assertEqual($row['name'], 'Subotica');
              break;
            default:
              $this->assertTrue(false, 'This key should never be available');
          }
          
          $iteration++;
        } // foerach
        
        $i++;
      } while($i < 2);
      
      // Seek specific row
      $row = $this->result->getRowAt(2);
      
      $this->assertEqual($row['id'], 3);
      $this->assertEqual($row['name'], 'Sombor');
    } // testIterator
    
    function testArrayAccess() {
      $this->assertEqual($this->result[0]['id'], 1);
      $this->assertEqual($this->result[0]['name'], 'Belgrade');
      
      $this->assertEqual($this->result[1]['id'], 2);
      $this->assertEqual($this->result[1]['name'], 'Novi Sad');
      
      $this->assertEqual($this->result[2]['id'], 3);
      $this->assertEqual($this->result[2]['name'], 'Sombor');
      
      $this->assertEqual($this->result[3]['id'], 4);
      $this->assertEqual($this->result[3]['name'], 'Subotica');
    } // testArrayAccess
    
    function testEscaping() {
      $this->assertEqual(DB::getConnection('default')->prepare('id IN (?)', array($this->result)), "id IN ('1', '2', '3', '4')");
    } // testEscaping
    
  }

?>
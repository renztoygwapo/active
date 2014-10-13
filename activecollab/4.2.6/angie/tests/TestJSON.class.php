<?php

  class ErrorForJSONEncodingTest extends Error {
  
    /**
     * Construct error
     * @param mixed $param1
     * @param mixed $param2
     * @param string $message
     */
    function __construct($param1, $param2, $message) {
      parent::__construct($message, array(
        'param1' => $param1, 
        'param2' => $param2, 
      ));
    } // __construct
    
  }

  class TestJSON extends UnitTestCase {
    
    /**
     * Prepare
     */
    function setUp() {
      Authentication::setLoggedUser(Users::findById(1));
    } // setUp
    
    /**
     * Tear down
     */
    function tearDown() {
      Authentication::setLoggedUser(null);
    } // tearDown
    
    /**
     * Test ecoding
     */
    function testEncodeNativeTypes() {
      $this->assertEqual(JSON::encode(1), '1');
      $this->assertEqual(JSON::encode('2'), '"2"');
      $this->assertEqual(JSON::encode(true), 'true');
      $this->assertEqual(JSON::encode(false), 'false');
      $this->assertEqual(JSON::encode(null), 'null');
      $this->assertEqual(JSON::encode(array('2' => 18, '3' => 'Test')), '{"2":18,"3":"Test"}');
    } // testEncode
    
    /**
     * Make sure that array elements go through JSON::encode() as well
     */
    function testArrayEncoding() {
      $array = array(
        'first' => new DateValue(), 
        'second' => new DateTimeValue(), 
      );
      
      $from_json = JSON::decode(JSON::encode($array));
      
      $this->assertIsA($from_json, 'array');
      $this->assertEqual(count($from_json), 2);

      // Make sure that we get the same value as we had every element encoded
      // through JSON::encode()
      $this->assertEqual($from_json['first'], JSON::decode(JSON::encode($array['first'])));
      $this->assertEqual($from_json['second'], JSON::decode(JSON::encode($array['second'])));
    } // testArrayEncoding
    
    /**
     * Test error encoding
     */
    function testEncodeError() {
      $error = new ErrorForJSONEncodingTest('First', 2, 'Test error message');
      
      $this->assertIsA($error, 'Exception');
      
      $recoded = JSON::decode(JSON::encode($error, Authentication::getLoggedUser()));
      
      $this->assertTrue(is_array($recoded));
      $this->assertEqual($recoded['type'], 'ErrorForJSONEncodingTest', 'Property has expected value');
      $this->assertEqual($recoded['message'], 'Test error message', 'Property has expected value');
      $this->assertEqual($recoded['file'], __FILE__, 'Property has expected value');
      $this->assertEqual($recoded['line'], 72, 'Property has expected value');
      $this->assertEqual($recoded['param1'], 'First', 'Property has expected value');
      $this->assertEqual($recoded['param2'], 2, 'Property has expected value');
    } // testEncodeError
    
    /**
     * Test encoding of DateValue objects
     */
    function testEncodeDateValue() {
      $date = new DateValue();
      
      $this->assertIsA($date, 'IDescribe');
      $this->assertIsA($date, 'IJSON');
      
      $date_json = JSON::encode($date);
      
      $this->assertEqual($date_json, $date->toJSON(Authentication::getLoggedUser()));
      
      $date_from_json = JSON::decode($date_json);
      
      $this->assertTrue(isset($date_from_json['class']));
      $this->assertEqual($date_from_json['class'], get_class($date));
      
      $this->assertTrue(isset($date_from_json['timestamp']));
      $this->assertEqual($date_from_json['timestamp'], $date->getTimestamp());
      
      $this->assertTrue(isset($date_from_json['mysql']));
      $this->assertEqual($date_from_json['mysql'], $date->toMySQL());
      
      $this->assertTrue(isset($date_from_json['formatted_time']));
      $this->assertEqual($date_from_json['formatted_time'], '00:00');
      
      $this->assertTrue(isset($date_from_json['formatted_time_gmt']));
      $this->assertEqual($date_from_json['formatted_time_gmt'], '00:00');
      
      $this->assertEqual(JSON::encode($date), $date->toJSON(Authentication::getLoggedUser()));
    } // testEncodeDateValue
    
    /**
     * Test encoding of date and time value
     */
    function testEncodeDateTimeValue() {
      $date = new DateTimeValue();
      
      $this->assertIsA($date, 'IDescribe');
      $this->assertIsA($date, 'IJSON');
      
      $date_json = JSON::encode($date);
      
      $this->assertEqual($date_json, $date->toJSON(Authentication::getLoggedUser()));
      
      $date_from_json = JSON::decode($date_json);
      
      $this->assertTrue(isset($date_from_json['class']));
      $this->assertEqual($date_from_json['class'], get_class($date));
      
      $this->assertTrue(isset($date_from_json['timestamp']));
      $this->assertEqual($date_from_json['timestamp'], $date->getTimestamp());
      
      $this->assertTrue(isset($date_from_json['mysql']));
      $this->assertEqual($date_from_json['mysql'], $date->toMySQL());
      
      $this->assertTrue(isset($date_from_json['formatted_time']));
      $this->assertEqual($date_from_json['formatted_time'], $date->formatTimeForUser(Authentication::getLoggedUser()));
      
      $this->assertTrue(isset($date_from_json['formatted_time_gmt']));
      $this->assertEqual($date_from_json['formatted_time_gmt'], $date->formatTimeForUser(Authentication::getLoggedUser(), 0));
      
      $this->assertEqual(JSON::encode($date), $date->toJSON(Authentication::getLoggedUser()));
    } // testEncodeDateTimeValue
    
    /**
     * Test encoding of DB result
     */
    function testEncodeDbResult() {
      $test_table = TABLE_PREFIX . 'test_db_result_to_json';
      
      // Prepare
      DB::execute("CREATE TABLE $test_table (
        id int(11) NOT NULL auto_increment,
        string_field varchar(255) default NULL,
        int_field int(11) default NULL,
        other float default NULL,
        PRIMARY KEY  (id)
      ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;");
      
      $this->assertTrue(in_array($test_table, DB::listTables(TABLE_PREFIX)));
      
      DB::execute("INSERT INTO $test_table (id,string_field,int_field,other) VALUES (1,'A',5,3.2), (2,'Another record',18,NULL);");
      
      // Do the test
      $rows = DB::execute("SELECT * FROM $test_table");
      
      $this->assertIsA($rows, 'DbResult');
      $this->assertEqual($rows->count(), 2);
      
      $this->assertEqual(JSON::decode(JSON::encode($rows, Authentication::getLoggedUser())), array(
        '0' => array(
          'id' => 1, 
          'string_field' => 'A', 
          'int_field' => 5, 
          'other' => 3.2, 
        ), 
      	'1' => array(
          'id' => 2, 
          'string_field' => 'Another record', 
          'int_field' => 18, 
          'other' => null, 
        )
      ));
     
      // Clean up
      DB::execute("DROP TABLE $test_table");
      
      $this->assertFalse(in_array($test_table, DB::listTables(TABLE_PREFIX)));
    } // testEncodeDbResult
    
    /**
     * Test JSON decoding
     */
    function testDecode() {
      $this->assertEqual(JSON::decode('null'), null);
      
      $invalid_json = 'Invalid JSON';
      
      $this->expectException(new JSONDecodeError($invalid_json, JSON_ERROR_SYNTAX));
      $this->assertEqual(JSON::decode($invalid_json), null);
    } // testDecode
    
  }
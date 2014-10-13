<?php

  require_once ANGIE_PATH . '/tests/testdataobject/BaseTestDataObject.class.php';
  require_once ANGIE_PATH . '/tests/testdataobject/BaseTestDataObjects.class.php';
  require_once ANGIE_PATH . '/tests/testdataobject/TestDataObject.class.php';
  require_once ANGIE_PATH . '/tests/testdataobject/TestDataObjects.class.php';

  class TestDataObjectCase extends UnitTestCase {
    
    /**
     * Create table for test data
     */
    function setUp() {
      DB::execute("CREATE TABLE " . TABLE_PREFIX . "test_data_objects (
        id int(10) unsigned NOT NULL auto_increment,
        name varchar(100) NOT NULL,
        description text,
        type enum('TestDataObject', 'Task','Milestone','Message','File') NOT NULL default 'File',
        created_on datetime NULL,
        updated_on datetime NULL,
        PRIMARY KEY  (id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    } // setUp
    
    /**
     * Tear down after test function has been executed
     */
    function tearDown() {
      DB::execute("DROP TABLE IF EXISTS " . TABLE_PREFIX . "test_data_objects");
    } // tearDown
    
    function testIs() {
      $object1 = new TestDataObject();
      $object2 = new TestDataObject();
      
      $this->assertTrue($object1->is($object2), 'Blank objects of the same class');
      
      $object1->setName('Object 1');
      $object2->setName('Object 1');
      
      $this->assertTrue($object1->is($object1), 'Same object instance');
      $this->assertTrue($object1->is($object2), 'Same property values');
      
      
      $object2->setName('Object 2');
      $this->assertFalse($object1->is($object2), 'No longer same values');
      
      // Lets save them
      $object1->save();
      $this->assertTrue($object1->isLoaded(), 'Object 1 saved');
      
      $object2->save();
      $this->assertTrue($object2->isLoaded(), 'Object 2 saved');
      
      $this->assertFalse($object1->is($object2), 'Different object, different properties');
      
      $object2->setName('Object 1');
      $this->assertFalse($object1->is($object2), 'Same properties, but different objects (based on ID)');
      
      $object1_from_db = TestDataObjects::findById($object1->getId());
      $this->assertIsA($object1_from_db, 'TestDataObject');
      $this->assertTrue($object1_from_db->isLoaded());
      
      $this->assertTrue($object1_from_db->is($object1), 'Different object in memory, but same from database (based on ID)');
    } // testIs
  
    /**
     * Test if internal flags are correctly set
     */
    function testFlags() {
      $test_object = new TestDataObject();
      
      $this->assertTrue($test_object->isNew());
      $this->assertFalse($test_object->isLoaded());
      
      $test_object->setName('Name');
      $test_object->save();
      
      $this->assertFalse($test_object->isNew());
      $this->assertTrue($test_object->isLoaded());
      
      $test_object->delete();
      
      $this->assertTrue($test_object->isNew());
      $this->assertFalse($test_object->isLoaded());
    } // testFlags
    
    /**
     * Test field mapping
     */
    function testMapping() {
      $test_object = new TestDataObject();
      $test_object->setFieldValue('real_name', 'Our value');
      $this->assertEqual($test_object->getName(), 'Our value');
      $this->assertEqual($test_object->getFieldValue('real_name'), 'Our value');
      $this->assertTrue($test_object->isModifiedField('name'));
      
      $new_object = new TestDataObject();
      $new_object->setAttributes($var = array(
        'real_name' => 'Ilija Studen',
        'biography' => 'Comming soon!',
      ));
      
      $this->assertEqual($new_object->getName(), 'Ilija Studen');
      $this->assertEqual($new_object->getFieldValue('real_name'), 'Ilija Studen');
      $this->assertTrue($new_object->isModifiedField('name'));
      $this->assertEqual($new_object->getDescription(), 'Comming soon!');
      $this->assertEqual($new_object->getFieldValue('description'), 'Comming soon!');
      $this->assertTrue($new_object->isModifiedField('description'));
    } // testMapping
    
    /**
     * Test if loading is properly done
     */
    function testLoading() {
      DB::execute("INSERT INTO " . TABLE_PREFIX . 'test_data_objects (name, description) VALUES (?, ?)', 'Object name', 'Object description');
      $insert_id = DB::lastInsertId();
      
      $object = new TestDataObject($insert_id);
      $this->assertTrue($object->isLoaded());
      $this->assertEqual($object->getId(), $insert_id);
      $this->assertEqual($object->getName(), 'Object name');
      $this->assertEqual($object->getDescription(), 'Object description');
    } // testLoading
    
    /**
     * Test hydration loading
     */
    function testHydration() {
      DB::execute("INSERT INTO " . TABLE_PREFIX . 'test_data_objects (name, description) VALUES (?, ?), (?, ?), (?, ?)', 'Object #1', 'Description #1', 'Object #2', 'Description #2', 'Object #3', 'TestDataObject');
      
      // Multirows...
      $result = DB::getConnection()->execute("SELECT * FROM " . TABLE_PREFIX . 'test_data_objects', null, DB::LOAD_ALL_ROWS, DB::RETURN_OBJECT_BY_CLASS, 'TestDataObject');
      
      $this->assertIsA($result, 'MySQLDBResult');
      $this->assertEqual($result->count(), 3);
      
      foreach($result as $v) {
        $this->assertIsA($v, 'TestDataObject');
      } // if
      
      // One row, based on class name
      $result = DB::getConnection()->execute("SELECT * FROM " . TABLE_PREFIX . 'test_data_objects ORDER BY name', null, DB::LOAD_FIRST_ROW, DB::RETURN_OBJECT_BY_CLASS, 'TestDataObject');
      
      $this->assertIsA($result, 'TestDataObject');
      $this->assertTrue($result->isLoaded());
      $this->assertEqual($result->getName(), 'Object #1');
      
      // One row, based on field value
      $result = DB::getConnection()->execute("SELECT * FROM " . TABLE_PREFIX . 'test_data_objects WHERE name = ?', array('Object #3'), DB::LOAD_FIRST_ROW, DB::RETURN_OBJECT_BY_FIELD, 'description');
      
      $this->assertIsA($result, 'TestDataObject');
      $this->assertTrue($result->isLoaded());
      $this->assertEqual($result->getName(), 'Object #3');
      $this->assertEqual($result->getDescription(), 'TestDataObject');
    } // testHydration
    
    /**
     * Test inserting into database
     */
    function testInserting() {
      $test_object = new TestDataObject();
      $test_object->setName('First name');
      $test_object->setDescription('Description');
      $test_object->save();
      
      $id = $test_object->getId();
      
      $this->assertTrue($id > 0);
      
      $test_object->delete();
      $test_object->save();
      
      $this->assertEqual($test_object->getId(), $id, 'ID should be preserved');
      
      // Now lets test refreshing
      $second_test_object = TestDataObjects::find(array(
        'conditions' => array('id = ?', $id),
        'one' => true,
      ));
      
      $this->assertIsA($second_test_object, 'TestDataObject');
      $this->assertEqual($second_test_object->getName(), $test_object->getName());
      $this->assertEqual($second_test_object->getDescription(), $test_object->getDescription());
    } // testInserting
    
    /**
     * Test insert and update
     */
    function testSaving() {
      $test_object = new TestDataObject();
      
      $this->assertFalse($test_object->isModified());
      
      $test_object->setName('First name');
      $this->assertTrue($test_object->isModified());
      $this->assertTrue($test_object->isModifiedField('name'));

      $test_object->save();
      
      $this->assertFalse($test_object->isModified());
      $this->assertFalse($test_object->isModifiedField('name'));
      $this->assertEqual($test_object->getName(), 'First name');
      
      $test_object->setName('First name');
      $this->assertFalse($test_object->isModified(), 'Name did not change');
      $this->assertFalse($test_object->isModifiedField('name'), 'Name did not change');
      $this->assertEqual($test_object->getName(), 'First name');
      
      $test_object->setName('Second name');
      $this->assertTrue($test_object->isModified(), 'Name changed');
      $this->assertTrue($test_object->isModifiedField('name'), 'Name changed');
      $this->assertEqual($test_object->getName(), 'Second name');
    } // testSaving
    
    function testOldValues() {
      // New object...
      $test_object = new TestDataObject();
      $test_object->setName('Somebody someone');
      
      $this->assertEqual($test_object->getOldFieldValue('name'), null);
      
      $test_object->setName('New name');
      $this->assertEqual($test_object->getOldFieldValue('name'), 'Somebody someone');
      
      $test_object->save();
      $this->assertEqual($test_object->getOldFieldValue('name'), null);
      
      // Existing object
      $test_object = TestDataObjects::findById(1);
      $this->assertEqual($test_object->getOldFieldValue('name'), null);
      
      $test_object->setName('New name'); // current value, should not be saved
      $this->assertEqual($test_object->getOldFieldValue('name'), null);
      
      $test_object->setName('Something something');
      $this->assertEqual($test_object->getOldFieldValue('name'), 'New name');
      
      $test_object->save();
      $this->assertEqual($test_object->getOldFieldValue('name'), null);
    } // testOldValues
    
    function testUpdateId() {
      $test_object = new TestDataObject();
      $test_object->setName('My name');
      $test_object->save();
      
      $this->assertTrue($test_object->isLoaded());
      
      $old_id = $test_object->getId();
      $this->assertEqual($old_id, 1);
      
      $test_object->setId(12);
      $save = $test_object->save();
      
      $this->assertEqual($test_object->getId(), 12);
      
      $new_object = TestDataObjects::findById(12);
      $this->assertIsA($new_object, 'TestDataObject');
      $this->assertEqual($new_object->getId(), 12);
    } // testUpdateId
    
    function testValidation() {
      try {
        $test_object = new TestDataObject();
        $test_object->save();
        
        $this->assertTrue($test_object->isNew());
      } catch(Exception $e) {
        $this->assertIsA($e, 'ValidationErrors');
      } // try
      
      // No exception expected
      $test_object->setName('Unique name');
      $test_object->save();
      
      try {
        $second_test_object = new TestDataObject();
        $second_test_object->setName('Unique name');
        
        $second_test_object->save();
        
        $this->assertTrue($second_test_object->isNew());
      } catch(Exception $e) {
        $this->assertIsA($e, 'ValidationErrors');
      } // try
      
      // No exception expected
      $second_test_object->setName('New, better name');
      $second_test_object->save();
    } // testValidation
  
  }
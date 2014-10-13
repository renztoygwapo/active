<?php

  require_once ANGIE_PATH . '/tests/testdataobject/BaseTestDataObject.class.php';
  require_once ANGIE_PATH . '/tests/testdataobject/BaseTestDataObjects.class.php';
  require_once ANGIE_PATH . '/tests/testdataobject/TestDataObject.class.php';
  require_once ANGIE_PATH . '/tests/testdataobject/TestDataObjects.class.php';

  /**
   * Test data object caching
   *
   * @package angie.tests
   */
  class TestDataObjectCaching extends UnitTestCase {

    /**
     * Set up environment for testing
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
     * Tear down testing environment
     */
    function tearDown() {
      DB::execute("DROP TABLE IF EXISTS " . TABLE_PREFIX . "test_data_objects");
      AngieApplication::cache()->clear();
    } // tearDown

    /**
     * Test if cache is updated when object gets updated
     */
    function testUpdate() {
      $object = new TestDataObject();
      $object->setName('Name');
      $object->setType('File');
      $object->save();

      $this->assertEqual($object->getCacheKey(), array('models', 'test_data_objects', 1));

      $this->assertFalse(AngieApplication::cache()->isCached($object->getCacheKey()), 'Cache is not created on insert, only on load');

      // Load object
      $loaded_object = TestDataObjects::findById($object->getId());

      $this->assertEqual($loaded_object->getCacheKey(), array('models', 'test_data_objects', 1), 'Cache ID is the same');
      $this->assertTrue(AngieApplication::cache()->isCached($loaded_object->getCacheKey()), 'Cache should be created when object is loaded from the database');

      $this->assertEqual(AngieApplication::cache()->get($loaded_object->getCacheKey()), array(
        'id' => '1',
        'name' => 'Name',
        'description' => null,
        'type' => 'File',
        'created_on' => $loaded_object->getCreatedOn()->toMySQL(),
        'updated_on' => null,
      ), 'Valid data is cached');

      // Update object
      $loaded_object->setName('New name');
      $loaded_object->save();

      $this->assertFalse(AngieApplication::cache()->isCached($object->getCacheKey()), 'Cache has been removed on update');

      // Delete object
      $loaded_again_object = TestDataObjects::findById($object->getId());

      $this->assertEqual($loaded_again_object->getCacheKey(), array('models', 'test_data_objects', 1), 'Cache ID is the same');
      $this->assertTrue(AngieApplication::cache()->isCached($loaded_again_object->getCacheKey()), 'Cache should be created when object is loaded from the database');

      $loaded_again_object->delete();

      $this->assertFalse(AngieApplication::cache()->isCached($object->getCacheKey()), 'Cache is removed when object is deleted');
    } // testUpdate
  
  }
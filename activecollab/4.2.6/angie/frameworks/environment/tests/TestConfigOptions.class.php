<?php
  
  class TestConfigOptionsObject1 implements IConfigContext {
    
    /**
     * Publically available, for testing
     *
     * @var integer
     */
    public $id = 1;
    
    /**
     * Return ID that we have set in $id field
     *
     * @return integer
     */
    function getId() {
      return $this->id;
    } // getId

    /**
     * Return model name
     *
     * @param boolean $underscore
     * @return string
     */
    function getModelName($underscore = false) {
      return $underscore ? 'test_config_objects' : 'TestConfigObjects';
    } // getModelName

  }
  
  class TestConfigOptionsObject2 implements IConfigContext {
    
    /**
     * Publically available, for testing
     *
     * @var integer
     */
    public $id = 1;
    
    /**
     * Return ID that we have set in $id field
     *
     * @return integer
     */
    function getId() {
      return $this->id;
    } // getId

    /**
     * Return model name
     *
     * @param boolean $underscore
     * @return string
     */
    function getModelName($underscore = false) {
      return $underscore ? 'test_config_objects' : 'TestConfigObjects';
    } // getModelName
    
  }

  /**
   * Test config options behavior
   * 
   * @package angie.frameworks.environment
   * @subpackage tests
   */
  class TestConfigOptions extends AngieModelTestCase {
    
    function testAddRemove() {
      $original_count = (integer) DB::executeFirstCell('SELECT COUNT(*) FROM  ' . TABLE_PREFIX . 'config_options');
      
      ConfigOptions::addOption('Option 1', 'Module 1', 12);
      ConfigOptions::addOption('Option 2', 'Module 2', null);
      ConfigOptions::addOption('Option 3', 'Module 1', 'something');
      
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM  ' . TABLE_PREFIX . 'config_options'), $original_count + 3);
      $this->assertTrue(ConfigOptions::exists('Option 1', false));
      $this->assertTrue(ConfigOptions::exists('Option 2', false));
      $this->assertTrue(ConfigOptions::exists('Option 3', false));
      
      ConfigOptions::removeOption('Option 2');
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM  ' . TABLE_PREFIX . 'config_options'), $original_count + 2);
      $this->assertTrue(ConfigOptions::exists('Option 1', false));
      $this->assertFalse(ConfigOptions::exists('Option 2', false));
      $this->assertTrue(ConfigOptions::exists('Option 3', false));
      
      ConfigOptions::deleteByModule('Module 1');
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM  ' . TABLE_PREFIX . 'config_options'), $original_count);
      $this->assertFalse(ConfigOptions::exists('Option 1', false));
      $this->assertFalse(ConfigOptions::exists('Option 3', false));
    } // testAddRemove

    /**
     * Test get set
     */
    function testGetSet() {
      $original_count = (integer) DB::executeFirstCell('SELECT COUNT(*) FROM  ' . TABLE_PREFIX . 'config_options');
      
      ConfigOptions::addOption('Option 1', 'Module 1');
      ConfigOptions::addOption('Option 2', 'Module 1');
      ConfigOptions::addOption('Option 3', 'Module 1');
      
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'config_options'), $original_count + 3);
      
      $this->assertTrue(ConfigOptions::exists('Option 1', false));
      $this->assertTrue(ConfigOptions::exists('Option 2', false));
      $this->assertTrue(ConfigOptions::exists('Option 3', false));
      
      $this->assertEqual(ConfigOptions::setValue('Option 1', 'Value 1'), 'Value 1');
      $this->assertEqual(ConfigOptions::setValue(array(
        'Option 2' => 'Value 2',
        'Option 3' => 'Value 3', 
      )), array(
        'Option 2' => 'Value 2',
        'Option 3' => 'Value 3', 
      ));
      
      $this->assertEqual(ConfigOptions::getValue('Option 1'), 'Value 1');
      $this->assertEqual(ConfigOptions::getValue('Option 2'), 'Value 2');
      $this->assertEqual(ConfigOptions::getValue('Option 3'), 'Value 3');
      
      $this->assertEqual(ConfigOptions::getValue(array('Option 3', 'Option 1', 'Option 2')), array(
        'Option 3' => 'Value 3', 
        'Option 1' => 'Value 1', 
        'Option 2' => 'Value 2',
      ));
      
      ConfigOptions::deleteByModule('Module 1');
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'config_options'), $original_count);
    } // testSet

    /**
     * Test get set for
     */
    function testGetSetRemoveFor() {
      $original_count = (integer) DB::executeFirstCell('SELECT COUNT(*) FROM  ' . TABLE_PREFIX . 'config_options');
      
      $config_option_values_table = TABLE_PREFIX . 'config_option_values';
      
      // Prepare
      ConfigOptions::addOption('Option 1', 'system', 1983);
      ConfigOptions::addOption('Option 2', 'system', 'Google.com');
      ConfigOptions::addOption('Option 3', 'system', array('Belgrade', 'Zagreb', 'Ljubljana', 'Sarajevo', 'Podgorica', 'Skoplje'));
      
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'config_options'), $original_count + 3);
      
      $this->assertTrue(ConfigOptions::exists('Option 1', false));
      $this->assertTrue(ConfigOptions::exists('Option 2', false));
      $this->assertTrue(ConfigOptions::exists('Option 3', false));
      
      $object1 = new TestConfigOptionsObject1();
      $object1->id = 1;
      
      $object2 = new TestConfigOptionsObject1();
      $object2->id = 2;
      
      $object3 = new TestConfigOptionsObject2();
      $object3->id = 1;
      
      // Test mixing
      $this->assertEqual(ConfigOptions::setValueFor(array(
        'Option 1' => 'Value 1',
        'Option 3' => 'Value 3', 
        'Option 2' => 'Value 2',
      ), $object1), array(
        'Option 1' => 'Value 1',
        'Option 3' => 'Value 3', 
        'Option 2' => 'Value 2',
      ));
      
      $this->assertEqual(ConfigOptions::setValueFor(array(
        'Option 1' => 'Value 1 for second object',
        'Option 3' => 'Value 3',
      ), $object2), array(
        'Option 1' => 'Value 1 for second object',
        'Option 3' => 'Value 3',
      ));
      
      $this->assertEqual(ConfigOptions::setValueFor(array(
        'Option 1' => 1,
      ), $object3), array(
        'Option 1' => 1,
      ));
      
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $config_option_values_table"), 6);
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $config_option_values_table WHERE parent_type = ?", 'TestConfigOptionsObject1'), 5);
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $config_option_values_table WHERE parent_type = ?", 'TestConfigOptionsObject2'), 1);
      
      ConfigOptions::removeValuesFor($object1, array(
        'Option 1', 
        'Option 3'
      ));
      
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $config_option_values_table WHERE parent_type = ?", 'TestConfigOptionsObject1'), 3);
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $config_option_values_table WHERE parent_type = ? AND parent_id = ?", 'TestConfigOptionsObject1', 1), 1);
      
      ConfigOptions::removeValuesFor($object2);
      
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $config_option_values_table WHERE parent_type = ?", 'TestConfigOptionsObject1'), 1);
      
      // Test set
      $this->assertEqual(ConfigOptions::getValue('Option 2'), 'Google.com');
      ConfigOptions::setValueFor('Option 2', $object1, 'Yahoo.com');
      
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $config_option_values_table WHERE name = ? AND parent_type = ? AND parent_id = ?", 'Option 2', 'TestConfigOptionsObject1', 1), 1);
      $this->assertEqual(DB::executeFirstCell("SELECT value FROM $config_option_values_table WHERE name = ? AND parent_type = ? AND parent_id = ?", 'Option 2', 'TestConfigOptionsObject1', 1), serialize('Yahoo.com'));
      
      $this->assertEqual(ConfigOptions::getValueFor('Option 2', $object1, false), 'Yahoo.com');
      
      ConfigOptions::removeValuesFor($object1, 'Option 2');
      $this->assertEqual(ConfigOptions::getValueFor('Option 2', $object1, false), 'Google.com');
    } // testGetSetRemoveFor

    /**
     * Test configuration options for user model
     */
    function testUserModel() {
      $options_table = TABLE_PREFIX . 'config_options';

      $administrator = Users::findById(1);

      ConfigOptions::addOption('testing_user_model');

      $this->assertTrue($administrator->isLoaded());
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(name) FROM $options_table WHERE name = 'testing_user_model'"), 1);

      ConfigOptions::setValueFor('testing_user_model', $administrator, 123);

      $this->assertEqual(ConfigOptions::getValueFor('testing_user_model', $administrator, false), 123);

      $row = DB::executeFirstRow("SELECT * FROM " . TABLE_PREFIX . "config_option_values WHERE name = 'testing_user_model'");

      $this->assertTrue(is_array($row));
      $this->assertTrue($row['parent_type'], 'User');
      $this->assertTrue($row['parent_id'], 1);
    } // testUserModel
    
  }
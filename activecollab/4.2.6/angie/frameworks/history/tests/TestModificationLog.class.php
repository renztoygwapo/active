<?php

  /**
   * Test modification log
   */
  class TestModificationLog extends AngieModelTestCase {

    /**
     * Set up test case
     */
    function setUp() {
      parent::setUp();
      
      // We need this because system requires logged in user in order to track 
      // modification log (required for $by)
      Authentication::setLoggedUser(Users::findById(1));
    } // setUp

    /**
     * Tear down
     */
    function tearDown() {
      parent::tearDown();
      
      Authentication::setLoggedUser(null);
    } // tearDown

    /**
     * Test writing to log
     */
    function testWritingToLog() {
      $in_activecollab = class_exists('ActiveCollab');
      
      $logs_table = TABLE_PREFIX . 'modification_logs';
      $values_table = TABLE_PREFIX . 'modification_log_values';
      
      $total_modification_log_entries = (integer) DB::executeFirstCell("SELECT COUNT(id) FROM $logs_table");
      
      $user = new Administrator();
      
      $attributes = array(
        'email' => 'test@activecollab.com', 
        'first_name' => 'Test', 
        'last_name' => 'User',
        'password' => 'guano apes', 
      );
      
      if($in_activecollab) {
        $attributes['company_id'] = 1;
      } // if
      
      $user->setAttributes($attributes);
      //$user->setCreatedBy(Users::findById(1)); // We need this for modification to be saved
      $user->save();
      
      $this->assertTrue($user->isLoaded(), 'Saved to database');
      
      // ---------------------------------------------------
      //  Creation
      // ---------------------------------------------------
      
      $log_entries = DB::execute("SELECT * FROM $logs_table WHERE parent_type = ? AND parent_id = ?", 'Administrator', $user->getId());
      
      $this->assertIsA($log_entries, 'DBResult', 'Valid result');
      $this->assertEqual($log_entries->count(), 1, 'One entry in modification log');
      
      $creation_log_entry = $log_entries[0];
      
      $this->assertTrue(is_array($creation_log_entry), 'Valid first record');
      $this->assertTrue($creation_log_entry['is_first'], 'First record created');
      
      $fields = DB::executeFirstColumn("SELECT field FROM $values_table WHERE modification_id = ?", $creation_log_entry['id']);
      
      $this->assertTrue(is_array($fields));
      
      $this->assertTrue(in_array('email', $fields));
      $this->assertTrue(in_array('first_name', $fields));
      $this->assertTrue(in_array('last_name', $fields));
      $this->assertTrue(in_array('password', $fields));
      
      if($in_activecollab) {
        $this->assertTrue(in_array('company_id', $fields));
      } // if
      
      // ---------------------------------------------------
      //  Update
      // ---------------------------------------------------
      
      Authentication::setLoggedUser(Users::findById(1));
      
      $user->setFirstName('Second');
      $user->setLastName('User'); // Not new value!
      $user->setEmail('whoa@activecollab.com');
      $user->save();
      
      $this->assertEqual(DB::executeFirstCell("SELECT COUNT(id) FROM $logs_table WHERE parent_type = ? AND parent_id = ?", get_class($user), $user->getId()), 2, 'Two entries for this user in modification log');
      
      $update_log_entry = DB::executeFirstRow("SELECT * FROM $logs_table WHERE parent_type = ? AND parent_id = ? AND id != ?", get_class($user), $user->getId(), $creation_log_entry['id']);
      
      $this->assertTrue(is_array($update_log_entry), 'Valid update log entry');
      $this->assertFalse($update_log_entry['is_first'], 'Update log entry is not set as first');
      
      $fields = DB::executeFirstColumn("SELECT field FROM $values_table WHERE modification_id = ?", $update_log_entry['id']);
      
      $this->assertTrue(is_array($fields));
      $this->assertEqual(count($fields), 2, 'Two fields for update log entry');
      
      $this->assertTrue(in_array('first_name', $fields), 'First name update logged');
      $this->assertTrue(in_array('email', $fields), 'Email update logged');
      
      // ---------------------------------------------------
      //  Modification log clean up
      // ---------------------------------------------------
      
      $user->forceDelete();
      
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(id) FROM $logs_table"), $total_modification_log_entries, 'Same number of modification logs as before the test');
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $values_table WHERE modification_id = ?", $creation_log_entry['id']), 0, 'Values for creation log entry removed');
      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM $values_table WHERE modification_id = ?", $update_log_entry['id']), 0, 'Values for update log entry removed');
    } // testWritingToLog
    
  }
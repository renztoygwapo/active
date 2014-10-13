<?php

  class TestTrackingModificationLog extends AngieModelTestCase {
    
    /**
     * Application project
     *
     * @var Project
     */
    private $application_project;
    
    function setUp() {
      parent::setUp();
      
      $this->application_project = new Project();
      $this->application_project->setAttributes(array(
        'name' => 'Application', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->application_project->setState(STATE_VISIBLE);
      $this->application_project->save();
    } // setUp
    
    function tearDown() {
      parent::tearDown();
      
      $this->application_project = null;
    } // tearDown
    
    function testInitialization() {
      $this->assertTrue($this->application_project->isLoaded(), 'Application project is loaded');
      $this->assertEqual($this->application_project->getState(), STATE_VISIBLE, 'Project state for application project');
    } // testInitialization
    
    function testTimeRecordFields() {
      $time_record = $this->application_project->tracking()->logTime(50, Users::findById(1), JobTypes::findById(1), DateValue::now());
      
      $this->assertIsA($time_record, 'TimeRecord', 'Logged time');
      $this->assertIsA($time_record, 'IHistory', 'Time record implements IHistory');
      $this->assertIsA($time_record->history(), 'IHistoryImplementation', 'Time record implements history');
      
      $fields = $time_record->history()->getTrackedFields();
      
      $this->assertTrue(in_array('value', $fields));
      $this->assertTrue(in_array('user_id', $fields));
      $this->assertTrue(in_array('user_name', $fields));
      $this->assertTrue(in_array('user_email', $fields));
      $this->assertTrue(in_array('job_type_id', $fields));
      $this->assertTrue(in_array('record_date', $fields));
      $this->assertTrue(in_array('billable_status', $fields));
      $this->assertTrue(in_array('state', $fields));
    } // testTimeRecordFields
    
    function testExpenseFields() {
      $expense = $this->application_project->tracking()->logExpense(50, Users::findById(1), ExpenseCategories::findById(1), DateValue::now());
      
      $this->assertIsA($expense, 'Expense', 'Logged expense');
      $this->assertIsA($expense, 'IHistory', 'Expense implements IHistory');
      $this->assertIsA($expense->history(), 'IHistoryImplementation', 'Expense implements history'); 
      
      $fields = $expense->history()->getTrackedFields();
      
      $this->assertTrue(in_array('value', $fields));
      $this->assertTrue(in_array('user_id', $fields));
      $this->assertTrue(in_array('user_name', $fields));
      $this->assertTrue(in_array('user_email', $fields));
      $this->assertTrue(in_array('category_id', $fields));
      $this->assertTrue(in_array('record_date', $fields));
      $this->assertTrue(in_array('billable_status', $fields));
      $this->assertTrue(in_array('state', $fields));
    } // testExpenseFields
    
  }
<?php

  /**
   * Test project budget calculator
   */
  class TestProjectBudgetCalculator extends AngieModelTestCase {

    /**
     * Administrator
     *
     * @var Administrator
     */
    private $logged_user;

    /**
     * First job type
     *
     * @var JobType
     */
    private $first_job_type;

    /**
     * Second job type
     *
     * @var JobType
     */
    private $second_job_type;

    /**
     * Expense category
     *
     * @var ExpenseCategory
     */
    private $expense_category;

    /**
     * Active project
     *
     * @var Project
     */
    private $active_project;

    /**
     * Active task
     *
     * @var Task
     */
    private $active_task;

    function setUp() {
      parent::setUp();
      
      $this->logged_user = Users::findById(1);
      
      $this->first_job_type = JobTypes::findById(1);
      
      $this->second_job_type = new JobType();
      $this->second_job_type->setAttributes(array(
        'name' => 'Some Work', 
        'default_hourly_rate' => 200,
        'is_active' => true,
      ));
      $this->second_job_type->save();
      
      $this->expense_category = ExpenseCategories::findById(1);
      
      $this->active_project = new Project();
      $this->active_project->setAttributes(array(
        'name' => 'Test project', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->active_project->setState(STATE_VISIBLE);
      $this->active_project->save();
      
      $this->active_task = new Task();
      $this->active_task->setName('Test task');
      $this->active_task->setProject($this->active_project);
      $this->active_task->setCreatedBy($this->logged_user);
      $this->active_task->setState(STATE_VISIBLE);
      $this->active_task->setVisibility(VISIBILITY_NORMAL);
      $this->active_task->save();
    } // setUp
    
    function testInitialization() {
      $this->assertTrue($this->active_project->isLoaded(), 'Project created');
      $this->assertTrue($this->first_job_type->isLoaded(), 'First job type loaded');
      $this->assertEqual($this->first_job_type->getDefaultHourlyRate(), 100, 'Valid value for default hourly rate');
      $this->assertTrue($this->second_job_type->isLoaded(), 'Second job type is loaded');
      $this->assertEqual($this->second_job_type->getDefaultHourlyRate(), 200, 'Valid value for default hourly rate');
      $this->assertTrue($this->expense_category->isLoaded(), 'Expense category is loaded');
      $this->assertTrue($this->active_task->isLoaded(), 'Task created');
      $this->assertEqual($this->active_task->getProjectId(), $this->active_project->getId(), 'Task belongs to a project');
    } // testInitialization
    
    function testTimeLog() {
      $this->active_project->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $this->active_task->tracking()->logTime(2, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      
      $this->assertEqual(TimeRecords::count(), 2, 'Three records added to the database');
      
      $records = $this->active_project->tracking()->getTimeRecords($this->logged_user);
      $this->assertIsA($records, 'DBResult', 'Valid time records result');
      $this->assertIsA($records->getRowAt(0), 'TimeRecord', 'Valid time record in result');
      $this->assertEqual($records->getRowAt(0)->getValue(), 1, 'Valid record value');
      $this->assertEqual($records->getRowAt(0)->getUserId(), $this->logged_user->getId(), 'Valid record user');
      $this->assertEqual($records->getRowAt(0)->getRecordDate()->getTimestamp(), DateValue::makeFromString('yesterday')->getTimestamp(), 'Valid record date');
      $this->assertEqual($records->getRowAt(0)->getJobTypeId(), $this->first_job_type->getId(), 'Valid job type');
      
      $records = $this->active_task->tracking()->getTimeRecords($this->logged_user);
      $this->assertIsA($records, 'DBResult', 'Valid time records result');
      $this->assertIsA($records->getRowAt(0), 'TimeRecord', 'Valid time record in result');
      $this->assertEqual($records->getRowAt(0)->getValue(), 2, 'Valid record value');
      $this->assertEqual($records->getRowAt(0)->getUserId(), $this->logged_user->getId(), 'Valid record user');
      $this->assertEqual($records->getRowAt(0)->getRecordDate()->getTimestamp(), DateValue::makeFromString('today')->getTimestamp(), 'Valid record date');
      $this->assertEqual($records->getRowAt(0)->getJobTypeId(), $this->first_job_type->getId(), 'Valid job type');
    } // testTimeLog
    
    function testExpenseLog() {
      $this->active_project->tracking()->logExpense(100, $this->logged_user, $this->expense_category, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $this->active_task->tracking()->logExpense(200, $this->logged_user, $this->expense_category, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      
      $records = $this->active_project->tracking()->getExpenses($this->logged_user);
      $this->assertIsA($records, 'DBResult', 'Valid time records result');
      $this->assertIsA($records->getRowAt(0), 'Expense', 'Valid expense in result');
      $this->assertEqual($records->getRowAt(0)->getValue(), 100, 'Valid expense value');
      $this->assertEqual($records->getRowAt(0)->getUserId(), $this->logged_user->getId(), 'Valid expense user');
      $this->assertEqual($records->getRowAt(0)->getRecordDate()->getTimestamp(), DateValue::makeFromString('yesterday')->getTimestamp(), 'Valid expense date');
      $this->assertEqual($records->getRowAt(0)->getCategoryId(), $this->expense_category->getId(), 'Valid exepnse category');
      
      $records = $this->active_task->tracking()->getExpenses($this->logged_user);
      $this->assertIsA($records, 'DBResult', 'Valid expenses result');
      $this->assertIsA($records->getRowAt(0), 'Expense', 'Valid expense in result');
      $this->assertEqual($records->getRowAt(0)->getValue(), 200, 'Valid expense value');
      $this->assertEqual($records->getRowAt(0)->getUserId(), $this->logged_user->getId(), 'Valid expense user');
      $this->assertEqual($records->getRowAt(0)->getRecordDate()->getTimestamp(), DateValue::makeFromString('today')->getTimestamp(), 'Valid expense date');
      $this->assertEqual($records->getRowAt(0)->getCategoryId(), $this->expense_category->getId(), 'Valid exepnse category');
    } // testExpenseLog
    
    function testSumTime() {
      $this->active_project->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $this->active_task->tracking()->logTime(2, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      
      $to_trash = $this->active_task->tracking()->logTime(8, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $to_trash->state()->trash();
      
      $this->assertEqual($to_trash->getState(), STATE_TRASHED, 'Time record trashed');
      
      $this->assertEqual($this->active_project->tracking()->sumTime($this->logged_user, true), 3, '6 hours tracked for project');
      $this->assertEqual($this->active_project->tracking()->sumTime($this->logged_user, false), 1, '1 hour tracked for project directly');
      $this->assertEqual($this->active_task->tracking()->sumTime($this->logged_user, false), 2, '2 hours tracked for task directly');
    } // testSumTime
    
    function testSumExpenses() {
      $this->active_project->tracking()->logExpense(100, $this->logged_user, $this->expense_category, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $this->active_task->tracking()->logExpense(200, $this->logged_user, $this->expense_category, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      
      $to_trash = $this->active_task->tracking()->logExpense(800, $this->logged_user, $this->expense_category, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $to_trash->state()->trash();
      
      $this->assertEqual($to_trash->getState(), STATE_TRASHED, 'Expense trashed');
      
      $this->assertEqual($this->active_project->tracking()->sumExpenses($this->logged_user, true), 300, '600$ tracked for project');
      $this->assertEqual($this->active_project->tracking()->sumExpenses($this->logged_user, false), 100, '100$ tracked for project directly');
      $this->assertEqual($this->active_task->tracking()->sumExpenses($this->logged_user, false), 200, '200$ tracked for task directly');
    } // testSumExpenses
    
    function testCostSoFar() {
      
      // Time
      $this->active_project->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $this->active_task->tracking()->logTime(2, $this->logged_user, $this->second_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      
      $to_trash = $this->active_task->tracking()->logTime(8, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $to_trash->state()->trash();
      
      $this->assertEqual($to_trash->getState(), STATE_TRASHED, 'Time record trashed');
      
      // Expenses
      $this->active_project->tracking()->logExpense(100, $this->logged_user, $this->expense_category, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $this->active_task->tracking()->logExpense(200, $this->logged_user, $this->expense_category, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      
      $to_trash = $this->active_task->tracking()->logExpense(800, $this->logged_user, $this->expense_category, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $to_trash->state()->trash();
      
      $this->assertEqual($to_trash->getState(), STATE_TRASHED, 'Expense trashed');
      
      // (1 x 100 + 2 x 200) + (100 + 200) = 800
      $this->assertEqual(TrackingObjects::sumCostByProject($this->logged_user, $this->active_project), 800, 'Cost is properly calculated');
      
      // Custom hourly rate
      $this->second_job_type->setCustomHourlyRateFor($this->active_project, 150);
      
      $this->assertEqual($this->second_job_type->getCustomHourlyRateFor($this->active_project), 150, 'Custom hourly rate for active project is set');
      
      // (1 x 100 + 2 x 150) + (100 + 200 + 300) = 1450
      $this->assertEqual(TrackingObjects::sumCostByProject($this->logged_user, $this->active_project), 700, 'Cost is properly calculated');
    } // testCostSoFar
    
  }
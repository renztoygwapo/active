<?php

  /**
   * Test tracking reports
   */
  class TestTrackingReports extends AngieModelTestCase {
    
    /**
     * Logged user
     *
     * @var User
     */
    private $logged_user;
    
    /**
     * Second user
     *
     * @var User
     */
    private $second_user;
    
    /**
     * First job type instance
     *
     * @var JobType
     */
    private $first_job_type;
    
    /**
     * Second job type instance
     *
     * @var JobType
     */
    private $second_job_type;
    
    /**
     * First expense category
     *
     * @var ExpenseCategory
     */
    private $first_expense_category;
    
    /**
     * Second expense category
     *
     * @var ExpenseCategory
     */
    private $second_expense_category;
    
    /**
     * Application project
     *
     * @var Project
     */
    private $application_project;
    
    /**
     * Application project task
     *
     * @var Task
     */
    private $application_project_task;
    
    /**
     * Website project
     *
     * @var Project
     */
    private $website_project;
    
    /**
     * Website project task
     *
     * @var Task
     */
    private $website_project_task;
  
    function setUp() {
      parent::setUp();
      
      $this->logged_user = Users::findById(1);
      
      $this->second_user = new Administrator();
      $this->second_user->setAttributes(array(
        'email' => 'second-user@test.com', 
        'company_id' => 1, 
        'password' => 'test', 
        'role_id' => 1, 
      ));
      $this->second_user->setState(STATE_VISIBLE);
      $this->second_user->save();
      
      $this->first_job_type = JobTypes::findById(1);
      
      $this->second_job_type = new JobType();
      $this->second_job_type->setAttributes(array(
        'name' => 'Some Work', 
        'default_hourly_rate' => 200, 
      ));
      $this->second_job_type->save();
      
      $this->first_expense_category = ExpenseCategories::findById(1);
      
      $this->second_expense_category = new ExpenseCategory();
      $this->second_expense_category->setAttributes(array(
        'name' => 'Second Expense Category', 
      ));
      $this->second_expense_category->save();
      
      $this->application_project = new Project();
      $this->application_project->setAttributes(array(
        'name' => 'Application', 
        'leader_id' => 1,
        'company_id' => 1,
      ));
      $this->application_project->setState(STATE_VISIBLE);
      $this->application_project->save();
      
      $this->application_project_task = new Task();
      $this->application_project_task->setName('Test task');
      $this->application_project_task->setProject($this->application_project);
      $this->application_project_task->setCreatedBy($this->logged_user);
      $this->application_project_task->setState(STATE_VISIBLE);
      $this->application_project_task->setVisibility(VISIBILITY_NORMAL);
      $this->application_project_task->save();
      
      $this->website_project = new Project();
      $this->website_project->setAttributes(array(
        'name' => 'Website', 
        'leader_id' => 1, 
        'company_id' => 1,
      ));
      $this->website_project->setState(STATE_VISIBLE);
      $this->website_project->save();
      
      $this->website_project_task = new Task();
      $this->website_project_task->setName('Test task');
      $this->website_project_task->setProject($this->website_project);
      $this->website_project_task->setCreatedBy($this->logged_user);
      $this->website_project_task->setState(STATE_VISIBLE);
      $this->website_project_task->setVisibility(VISIBILITY_NORMAL);
      $this->website_project_task->save();
    } // setUp
    
    function tearDown() {
      parent::tearDown();
      
      $this->logged_user = null;
      $this->second_user = null;
      $this->first_job_type = null;
      $this->second_job_type = null;
      $this->first_expense_category = null;
      $this->second_expense_category = null;
      $this->application_project = null;
      $this->application_project_task = null;
      $this->website_project = null;
      $this->website_project_task = null;
    } // tearDown
    
    function testInitialization() {
      $this->assertTrue($this->logged_user->isLoaded(), 'Logged user loaded');
      $this->assertTrue($this->second_user->isLoaded(), 'Second user loaded');
      
      $this->assertTrue($this->first_job_type->isLoaded(), 'First job type is loaded');
      $this->assertTrue($this->second_job_type->isLoaded(), 'Second job type is loaded');
      $this->assertTrue($this->first_expense_category->isLoaded(), 'First expense category is loaded');
      $this->assertTrue($this->second_expense_category->isLoaded(), 'Second expense category is loaded');
      
      $this->assertTrue($this->application_project->isLoaded(), 'Application project is loaded');
      $this->assertEqual($this->application_project->getState(), STATE_VISIBLE, 'Project state for application project');
      $this->assertTrue($this->application_project_task->isLoaded(), 'Application project task is loaded');
      $this->assertEqual($this->application_project_task->getProjectId(), $this->application_project->getId(), 'Application project task belongs to website project');
      
      $this->assertTrue($this->website_project->isLoaded(), 'Website project is loaded');
      $this->assertEqual($this->website_project->getState(), STATE_VISIBLE, 'Project state for website project');
      $this->assertTrue($this->website_project_task->isLoaded(), 'Website project task is loaded');
      $this->assertEqual($this->website_project_task->getProjectId(), $this->website_project->getId(), 'Website project task belongs to website project');
    } // testInitialization
    
    function testSimpleReport() {
      $this->application_project->tracking()->logTime(5, $this->second_user, $this->first_job_type, DateValue::now());
      $this->application_project->tracking()->logTime(8, $this->second_user, $this->first_job_type, DateValue::makeFromString('yesterday'));
      
      $this->application_project->tracking()->logExpense(300, $this->second_user, $this->first_expense_category, DateValue::makeFromString('yesterday'));
      $this->application_project->tracking()->logExpense(100, $this->second_user, $this->second_expense_category, DateValue::makeFromString('today'));
      
      $to_trash = $this->application_project->tracking()->logTime(0.5, $this->second_user, $this->first_job_type, DateValue::makeFromString('yesterday'));
      $to_trash->state()->trash();
      
      $this->assertEqual($to_trash->getState(), STATE_TRASHED, 'One time record trashed');
      
      $this->assertEqual(TimeRecords::count(), 3, 'Three time records logged');
      $this->assertEqual(Expenses::count(), 2, 'Two expenses logged');
      
      $report = new TrackingReport();
      
      // Run the default report (returns all records)
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 4, 'Four records returned');
      
      // Filter only time records
      $report->setTypeFilter(TrackingReport::TYPE_FILTER_TIME);
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 2, 'Two records returned');
      
      foreach($result[0]['records'] as $record) {
        $this->assertEqual($record['type'], 'TimeRecord', 'Time record found');
      } // foreach
      
      // Filter only expenses
      $report->setTypeFilter(TrackingReport::TYPE_FILTER_EXPENSES);
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 2, 'Two records returned');
      
      foreach($result[0]['records'] as $record) {
        $this->assertEqual($record['type'], 'Expense', 'Expense found');
      } // foreach
    } // testSimpleReport
    
    /**
     * Test billable status filter
     */
    function testBillableStatusFilter() {
      $not_billable_hours = $this->application_project->tracking()->logTime(3, $this->second_user, $this->first_job_type, DateValue::now(), BILLABLE_STATUS_NOT_BILLABLE);
      $five_hours = $this->application_project->tracking()->logTime(5, $this->second_user, $this->first_job_type, DateValue::now(), BILLABLE_STATUS_BILLABLE);
      $eight_hours = $this->application_project->tracking()->logTime(8, $this->second_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_PENDING_PAYMENT);
      
      $not_billable_expenses = $this->application_project->tracking()->logExpense(800, $this->second_user, $this->first_expense_category, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_NOT_BILLABLE);
      $three_hundred_expense = $this->application_project->tracking()->logExpense(300, $this->second_user, $this->first_expense_category, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $one_hundred_expense = $this->application_project->tracking()->logExpense(100, $this->second_user, $this->second_expense_category, DateValue::makeFromString('today'), BILLABLE_STATUS_PAID);
      
      $to_trash = $this->application_project->tracking()->logTime(0.5, $this->second_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $to_trash->state()->trash();
      
      $this->assertEqual($to_trash->getState(), STATE_TRASHED, 'One time record trashed');
      
      $this->assertEqual(TimeRecords::count(), 4, 'Three time records logged');
      $this->assertEqual(Expenses::count(), 3, 'Two expenses logged');
      
      $report = new TrackingReport();
      
      // ---------------------------------------------------
      //  Search for any status
      // ---------------------------------------------------
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 6, 'Six records returned');
      
      // ---------------------------------------------------
      //  Search for not billable records
      // ---------------------------------------------------
      
      $report->setBillableStatusFilter(TrackingReport::BILLABLE_FILTER_NOT_BILLABLE);
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 2, 'Two billable records returned');
      
      foreach($result[0]['records'] as $record) {
        $this->assertEqual($record['billable_status'], BILLABLE_STATUS_NOT_BILLABLE, 'Billable records');
        $this->assertTrue(($record['type'] == 'TimeRecord' && $record['id'] == $not_billable_hours->getId()) || ($record['type'] == 'Expense' && $record['id'] == $not_billable_expenses->getId()), 'Valid type and ID');
      } // foreach
      
      // ---------------------------------------------------
      //  Search for billable records
      // ---------------------------------------------------
      
      $report->setBillableStatusFilter(TrackingReport::BILLABLE_FILTER_BILLABLE);
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 2, 'Two billable records returned');
      
      foreach($result[0]['records'] as $record) {
        $this->assertEqual($record['billable_status'], BILLABLE_STATUS_BILLABLE, 'Billable records');
        $this->assertTrue(($record['type'] == 'TimeRecord' && $record['id'] == $five_hours->getId()) || ($record['type'] == 'Expense' && $record['id'] == $three_hundred_expense->getId()), 'Valid type and ID');
      } // foreach
      
      // ---------------------------------------------------
      //  Search for pending payment records
      // ---------------------------------------------------
      
      $report->setBillableStatusFilter(TrackingReport::BILLABLE_FILTER_PENDING_PAYMENT);
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 1, 'One record pending payment');
      
      foreach($result[0]['records'] as $record) {
        $this->assertEqual($record['billable_status'], BILLABLE_STATUS_PENDING_PAYMENT, 'Billable records');
        $this->assertTrue($record['type'] == 'TimeRecord' && $record['id'] == $eight_hours->getId(), 'Valid type and ID');
      } // foreach
      
      // ---------------------------------------------------
      //  Search for paid records
      // ---------------------------------------------------
      
      $report->setBillableStatusFilter(TrackingReport::BILLABLE_FILTER_BILLABLE_PAID);
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 1, 'One record paid');
      
      foreach($result[0]['records'] as $record) {
        $this->assertEqual($record['billable_status'], BILLABLE_STATUS_PAID, 'Billable records');
        $this->assertTrue($record['type'] == 'Expense' && $record['id'] == $one_hundred_expense->getId(), 'Valid type and ID');
      } // foreach
      
      // ---------------------------------------------------
      //  Search for not paid records
      // ---------------------------------------------------
      
      $report->setBillableStatusFilter(TrackingReport::BILLABLE_FILTER_BILLABLE_NOT_PAID);
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 3, 'Three billable records not yet paid');
      
      foreach($result[0]['records'] as $record) {
        $this->assertTrue($record['billable_status'] == BILLABLE_STATUS_BILLABLE || $record['billable_status'] == BILLABLE_STATUS_PENDING_PAYMENT, 'Billable records');
        $this->assertTrue(
          ($record['type'] == 'TimeRecord' && $record['id'] == $five_hours->getId()) || 
          ($record['type'] == 'TimeRecord' && $record['id'] == $eight_hours->getId()) || 
          ($record['type'] == 'Expense' && $record['id'] == $three_hundred_expense->getId()), 
        'Valid type and ID');
      } // foreach
      
    } // testBillableStatusFilter
    
    /**
     * Test grouping of expenses by currency
     */
    function testExpenseCurrency() {
      $dollar = Currencies::findByCode('USD');
      $euro = Currencies::findByCode('EUR');
      
      $this->assertTrue($dollar->isLoaded(), 'Dollar loaded');
      $this->assertTrue($dollar->getIsDefault(), 'Dollar is default currency');
      $this->assertTrue($euro->isLoaded(), 'Euro loaded');
      
      $this->assertNotEqual($dollar->getId(), $euro->getId(), 'Currencies::findByCode() works well');
      
      $this->assertEqual($this->application_project->getCurrency()->getId(), $dollar->getId(), 'Application project uses defualt currency');
      $this->assertEqual($this->website_project->getCurrency()->getId(), $dollar->getId(), 'Website project uses defualt currency');
      
      $this->website_project->setCurrency($euro, true);
      
      $this->assertEqual($this->website_project->getCurrency()->getId(), $euro->getId(), 'Website project now uses EURO');
      
      $application_project_record = $this->application_project->tracking()->logExpense(100, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      $application_project_task_record = $this->application_project_task->tracking()->logExpense(200, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      $website_project_record = $this->website_project->tracking()->logExpense(300, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      $website_project_task_record = $this->website_project_task->tracking()->logExpense(400, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      
      $report = new TrackingReport();
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report result');
      $this->assertEqual(count($result[0]['records']), 4, 'Four records returned');
      
      foreach($result[0]['records'] as $record) {
        if($record['id'] == $application_project_record->getId() || $record['id'] == $application_project_task_record->getId()) {
          $this->assertEqual($record['project_id'], $this->application_project->getId(), 'Record is in valid project');
          $this->assertEqual($record['project_name'], $this->application_project->getName(), 'Project name is properly loaded');
          $this->assertEqual($record['currency_id'], $dollar->getId(), 'Record has a good currency loaded');
        } elseif($record['id'] == $website_project_record->getId() || $record['id'] == $website_project_task_record->getId()) {
          $this->assertEqual($record['project_id'], $this->website_project->getId(), 'Record is in valid project');
          $this->assertEqual($record['project_name'], $this->website_project->getName(), 'Project name is properly loaded');
          $this->assertEqual($record['currency_id'], $euro->getId(), 'Record has a good currency loaded');
        } else {
          $this->fail('Unknown record ID');
        } // if
      } // if
    } // testExpenseCurrency
    
    /**
     * Load and test currencies
     * 
     * @return array
     */
    private function getCurrencies() {
      $dollar = Currencies::findByCode('USD');
      $euro = Currencies::findByCode('EUR');
      $yen = Currencies::findByCode('JPY');
      $pound = Currencies::findByCode('GBP');
      
      $this->assertTrue($dollar->isLoaded() && $dollar->getIsDefault(), 'Dollar is loaded and set as default currency');
      $this->assertTrue($euro->isLoaded(), 'Euro loaded');
      $this->assertTrue($yen->isLoaded(), 'Yen loaded');
      $this->assertTrue($pound->isLoaded(), 'Pound loaded');
      
      return array($dollar, $euro, $yen, $pound);
    } // getCurrencies
    
    /**
     * Test filter by project
     */
    function testFilterByProject() {
      $this->application_project->complete()->complete($this->logged_user);
      $this->assertTrue($this->application_project->complete()->isCompleted(), 'Application project is completed');
      
      $this->application_project->tracking()->logTime(5, $this->second_user, $this->first_job_type, DateValue::now());
      $this->application_project->tracking()->logTime(8, $this->second_user, $this->first_job_type, DateValue::makeFromString('yesterday'));
      
      $this->application_project->tracking()->logExpense(300, $this->second_user, $this->first_expense_category, DateValue::makeFromString('yesterday'));
      $this->application_project->tracking()->logExpense(100, $this->second_user, $this->second_expense_category, DateValue::makeFromString('today'));
      
      $to_trash = $this->application_project->tracking()->logTime(0.5, $this->second_user, $this->first_job_type, DateValue::makeFromString('yesterday'));
      $to_trash->state()->trash();
      
      $this->assertEqual($to_trash->getState(), STATE_TRASHED, 'One time record trashed');
      
      $this->website_project->tracking()->logTime(12, $this->second_user, $this->first_job_type, DateValue::now());
      $this->website_project->tracking()->logExpense(1315, $this->second_user, $this->second_expense_category, DateValue::now());
      
      $this->assertEqual(TimeRecords::count(), 4, 'Three time records logged');
      $this->assertEqual(Expenses::count(), 3, 'Two expenses logged');
      
      // ---------------------------------------------------
      //  Data from active projects
      // ---------------------------------------------------
      
      $report = new TrackingReport();
      $report->setProjectFilter(TrackingReport::PROJECT_FILTER_ACTIVE);
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 2, 'Two records returned');
      
      foreach($result[0]['records'] as $record) {
        $this->assertEqual($record['project_id'], $this->website_project->getId(), 'All records belong to website project');
      } // foreach
      
      // ---------------------------------------------------
      //  Data from completed projects
      // ---------------------------------------------------
      
      $report = new TrackingReport();
      $report->setProjectFilter(TrackingReport::PROJECT_FILTER_COMPLETED);
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result) && is_array($result[0]['records']), 'Valid report response');
      $this->assertEqual(count($result[0]['records']), 4, 'Four records returned');
      
      foreach($result[0]['records'] as $record) {
        $this->assertEqual($record['project_id'], $this->application_project->getId(), 'All records belong to application project');
      } // foreach
    } // testFilterByProject
    
    /**
     * Test summarized but not grouped
     */
    function testSummarizedButNotGrouped() {
      list($dollar, $euro, $yen, $pound) = $this->getCurrencies();
      
      $this->website_project->setCurrency($euro, true);
      
      $this->assertEqual($this->application_project->getCurrency()->getId(), $dollar->getId(), 'Application project uses defualt currency');
      $this->assertEqual($this->website_project->getCurrency()->getId(), $euro->getId(), 'Website project uses EURO');
      
      $this->application_project->tracking()->logTime(1, $this->second_user, $this->first_job_type, DateValue::now());
      $this->application_project->tracking()->logExpense(100, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      
      $this->application_project_task->tracking()->logTime(2, $this->second_user, $this->first_job_type, DateValue::now());
      $this->application_project_task->tracking()->logExpense(200, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      
      $this->website_project->tracking()->logTime(3, $this->second_user, $this->first_job_type, DateValue::now());
      $this->website_project->tracking()->logExpense(300, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      
      $this->website_project_task->tracking()->logTime(4, $this->second_user, $this->first_job_type, DateValue::now());
      $this->website_project_task->tracking()->logExpense(400, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      
      $report = new TrackingReport();
      
      $report->setSumByUser(true);
      $report->setGroupBy(TrackingReport::DONT_GROUP);
      
      $result = $report->run($this->logged_user);
      
      $user_key = $this->second_user->getEmail();
      
      $this->assertTrue(is_array($result) && is_array($result['all']) && is_array($result['all']['records']), 'Report result is valid');
      $this->assertTrue(isset($result['all']['records'][$user_key]) && is_array($result['all']['records'][$user_key]), 'Data for $second_user loaded');
      $this->assertTrue(isset($result['all']['records'][$user_key]['time']), 'Time property is set');
      $this->assertEqual($result['all']['records'][$user_key]['time'], 10, 'Proper time is loaded');
      
      $this->assertEqual($result['all']['records'][$user_key]['expenses_for_' . $dollar->getId()], 300, 'Proper dollar expenses loaded');
      $this->assertEqual($result['all']['records'][$user_key]['expenses_for_' . $euro->getId()], 700, 'Proper euro expenses loaded');
      $this->assertEqual($result['all']['records'][$user_key]['expenses_for_' . $yen->getId()], 0, 'Proper yen expenses loaded');
      $this->assertEqual($result['all']['records'][$user_key]['expenses_for_' . $pound->getId()], 0, 'Proper pound expenses loaded');
    } // testSummarizedButNotGrouped
    
    function testSummarizedGroupByDate() {
      list($dollar, $euro, $yen, $pound) = $this->getCurrencies();
      
      $this->website_project->setCurrency($euro, true);
      
      $this->assertEqual($this->application_project->getCurrency()->getId(), $dollar->getId(), 'Application project uses defualt currency');
      $this->assertEqual($this->website_project->getCurrency()->getId(), $euro->getId(), 'Website project uses EURO');
      
      $yesterday = DateValue::makeFromString('yesterday');
      $today = DateValue::makeFromString('today');
      
      $this->application_project->tracking()->logTime(1, $this->second_user, $this->first_job_type, $yesterday);
      $this->application_project->tracking()->logExpense(100, $this->second_user, $this->first_expense_category, $yesterday);
      
      $this->application_project_task->tracking()->logTime(2, $this->second_user, $this->first_job_type, $yesterday);
      $this->application_project_task->tracking()->logExpense(200, $this->second_user, $this->first_expense_category, $yesterday);
      
      $this->website_project->tracking()->logTime(3, $this->second_user, $this->first_job_type, $today);
      $this->website_project->tracking()->logExpense(300, $this->second_user, $this->first_expense_category, $today);
      
      $this->website_project_task->tracking()->logTime(4, $this->second_user, $this->first_job_type, $today);
      $this->website_project_task->tracking()->logExpense(400, $this->second_user, $this->first_expense_category, $today);
      
      $report = new TrackingReport();
      
      $report->setSumByUser(true);
      $report->setGroupBy(TrackingReport::GROUP_BY_DATE);
      
      $yesterday_key = $yesterday->toMySQL();
      $today_key = $today->toMySQL();
      $user_key = $this->second_user->getEmail();
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result), 'Result is an array');
      $this->assertEqual(count($result), 2, 'Two results, for two days');
      
      $this->assertTrue(isset($result[$yesterday_key]), 'Yesterday key set');
      $this->assertEqual(count($result[$yesterday_key]['records']), 1, 'One record for one user');
      $this->assertTrue(isset($result[$yesterday_key]['records'][$user_key]['time']), 'Time is set');
      $this->assertEqual($result[$yesterday_key]['records'][$user_key]['time'], 3, 'Proper result for yesterday time');
      
      $this->assertEqual($result[$yesterday_key]['records'][$user_key]['expenses_for_' . $dollar->getId()], 300, 'Proper dollar expenses loaded for yesterday');
      $this->assertEqual($result[$yesterday_key]['records'][$user_key]['expenses_for_' . $euro->getId()], 0, 'Proper euro expenses loaded for yesterday');
      $this->assertEqual($result[$yesterday_key]['records'][$user_key]['expenses_for_' . $yen->getId()], 0, 'Proper yen expenses loaded for yesterday');
      $this->assertEqual($result[$yesterday_key]['records'][$user_key]['expenses_for_' . $pound->getId()], 0, 'Proper pound expenses loaded for yesterday');
      
      $this->assertTrue(isset($result[$today_key]), 'Today key set');
      $this->assertEqual(count($result[$today_key]['records']), 1, 'One record for one user');
      $this->assertTrue(isset($result[$today_key]['records'][$user_key]['time']), 'Time is set');
      $this->assertEqual($result[$today_key]['records'][$user_key]['time'], 7, 'Proper result for today time');
      
      $this->assertEqual($result[$today_key]['records'][$user_key]['expenses_for_' . $dollar->getId()], 0, 'Proper dollar expenses loaded for today');
      $this->assertEqual($result[$today_key]['records'][$user_key]['expenses_for_' . $euro->getId()], 700, 'Proper euro expenses loaded for today');
      $this->assertEqual($result[$today_key]['records'][$user_key]['expenses_for_' . $yen->getId()], 0, 'Proper yen expenses loaded for today');
      $this->assertEqual($result[$today_key]['records'][$user_key]['expenses_for_' . $pound->getId()], 0, 'Proper pound expenses loaded for today');
    } // testSummarizedGroupByDate
    
    function testSummarizedGroupByProjectAndProjectClient() {
      $client_1 = new Company();
      $client_1->setName('Client 1');
      $client_1->setState(STATE_VISIBLE);
      $client_1->save();
      
      $client_2 = new Company();
      $client_2->setName('Client 2');
      $client_2->setState(STATE_VISIBLE);
      $client_2->save();
      
      $this->application_project->setCompanyId($client_1->getId());
      $this->application_project->save();
      $this->assertEqual($this->application_project->getCompanyId(), $client_1->getId(), 'Client 1 is client for application project');
      
      $this->website_project->setCompanyId($client_2->getId());
      $this->website_project->save();
      $this->assertEqual($this->website_project->getCompanyId(), $client_2->getId(), 'Client 2 is client for website project');
      
      list($dollar, $euro, $yen, $pound) = $this->getCurrencies();
      
      $this->website_project->setCurrency($euro, true);
      
      $this->assertEqual($this->application_project->getCurrency()->getId(), $dollar->getId(), 'Application project uses defualt currency');
      $this->assertEqual($this->website_project->getCurrency()->getId(), $euro->getId(), 'Website project uses EURO');
      
      $yesterday = DateValue::makeFromString('yesterday');
      $today = DateValue::makeFromString('today');
      
      $this->application_project->tracking()->logTime(1, $this->second_user, $this->first_job_type, $yesterday);
      $this->application_project->tracking()->logExpense(100, $this->second_user, $this->first_expense_category, $yesterday);
      
      $this->application_project_task->tracking()->logTime(2, $this->second_user, $this->first_job_type, $yesterday);
      $this->application_project_task->tracking()->logExpense(200, $this->second_user, $this->first_expense_category, $yesterday);
      
      $this->website_project->tracking()->logTime(3, $this->second_user, $this->first_job_type, $today);
      $this->website_project->tracking()->logExpense(300, $this->second_user, $this->first_expense_category, $today);
      
      $this->website_project_task->tracking()->logTime(4, $this->second_user, $this->first_job_type, $today);
      $this->website_project_task->tracking()->logExpense(400, $this->second_user, $this->first_expense_category, $today);
      
      $user_key = $this->second_user->getEmail();
      
      $report = new TrackingReport();
      
      $report->setSumByUser(true);
      
      // ---------------------------------------------------
      //  Group by project
      // ---------------------------------------------------
      
      $report->setGroupBy(TrackingReport::GROUP_BY_PROJECT);
      
      $application_project_key = $this->application_project->getId();
      $website_project_key = $this->website_project->getId();
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result), 'Result is an array');
      $this->assertEqual(count($result), 2, 'Two results, for two days');
      
      $this->assertTrue(isset($result[$application_project_key]), 'Application project key set');
      $this->assertEqual(count($result[$application_project_key]['records']), 1, 'One record for one user');
      $this->assertTrue(isset($result[$application_project_key]['records'][$user_key]['time']), 'Time is set');
      $this->assertEqual($result[$application_project_key]['records'][$user_key]['time'], 3, 'Proper result for Application project time');
      
      $this->assertEqual($result[$application_project_key]['records'][$user_key]['expenses_for_' . $dollar->getId()], 300, 'Proper dollar expenses loaded for client 1');
      $this->assertEqual($result[$application_project_key]['records'][$user_key]['expenses_for_' . $euro->getId()], 0, 'Proper euro expenses loaded for client 1');
      $this->assertEqual($result[$application_project_key]['records'][$user_key]['expenses_for_' . $yen->getId()], 0, 'Proper yen expenses loaded for client 1');
      $this->assertEqual($result[$application_project_key]['records'][$user_key]['expenses_for_' . $pound->getId()], 0, 'Proper pound expenses loaded for client 1');
      
      $this->assertTrue(isset($result[$website_project_key]), 'Website project key set');
      $this->assertEqual(count($result[$website_project_key]['records']), 1, 'One record for one user');
      $this->assertTrue(isset($result[$website_project_key]['records'][$user_key]['time']), 'Time is set');
      $this->assertEqual($result[$website_project_key]['records'][$user_key]['time'], 7, 'Proper result for website project time');
      
      $this->assertEqual($result[$website_project_key]['records'][$user_key]['expenses_for_' . $dollar->getId()], 0, 'Proper dollar expenses loaded for client 2');
      $this->assertEqual($result[$website_project_key]['records'][$user_key]['expenses_for_' . $euro->getId()], 700, 'Proper euro expenses loaded for client 2');
      $this->assertEqual($result[$website_project_key]['records'][$user_key]['expenses_for_' . $yen->getId()], 0, 'Proper yen expenses loaded for client 2');
      $this->assertEqual($result[$website_project_key]['records'][$user_key]['expenses_for_' . $pound->getId()], 0, 'Proper pound expenses loaded for client 2');
      
      // ---------------------------------------------------
      //  Group by project client
      // ---------------------------------------------------
      
      $report->setGroupBy(TrackingReport::GROUP_BY_PROJECT_CLIENT);
      
      $client_1_key = $client_1->getId();
      $client_2_key = $client_2->getId();
      
      $result = $report->run($this->logged_user);
      
      $this->assertTrue(is_array($result), 'Result is an array');
      $this->assertEqual(count($result), 2, 'Two results, for two days');
      
      $this->assertTrue(isset($result[$client_1_key]), 'Client 1 key set');
      $this->assertEqual(count($result[$client_1_key]['records']), 1, 'One record for one user');
      $this->assertTrue(isset($result[$client_1_key]['records'][$user_key]['time']), 'Time is set');
      $this->assertEqual($result[$client_1_key]['records'][$user_key]['time'], 3, 'Proper result for client 1 time');
      
      $this->assertEqual($result[$client_1_key]['records'][$user_key]['expenses_for_' . $dollar->getId()], 300, 'Proper dollar expenses loaded for client 1');
      $this->assertEqual($result[$client_1_key]['records'][$user_key]['expenses_for_' . $euro->getId()], 0, 'Proper euro expenses loaded for client 1');
      $this->assertEqual($result[$client_1_key]['records'][$user_key]['expenses_for_' . $yen->getId()], 0, 'Proper yen expenses loaded for client 1');
      $this->assertEqual($result[$client_1_key]['records'][$user_key]['expenses_for_' . $pound->getId()], 0, 'Proper pound expenses loaded for client 1');
      
      $this->assertTrue(isset($result[$client_2_key]), 'Client 2 key set');
      $this->assertEqual(count($result[$client_2_key]['records']), 1, 'One record for one user');
      $this->assertTrue(isset($result[$client_2_key]['records'][$user_key]['time']), 'Time is set');
      $this->assertEqual($result[$client_2_key]['records'][$user_key]['time'], 7, 'Proper result for client 2 time');
      
      $this->assertEqual($result[$client_2_key]['records'][$user_key]['expenses_for_' . $dollar->getId()], 0, 'Proper dollar expenses loaded for client 2');
      $this->assertEqual($result[$client_2_key]['records'][$user_key]['expenses_for_' . $euro->getId()], 700, 'Proper euro expenses loaded for client 2');
      $this->assertEqual($result[$client_2_key]['records'][$user_key]['expenses_for_' . $yen->getId()], 0, 'Proper yen expenses loaded for client 2');
      $this->assertEqual($result[$client_2_key]['records'][$user_key]['expenses_for_' . $pound->getId()], 0, 'Proper pound expenses loaded for client 2');
    } // testSummarizedGroupByProjectAndProjectClient
    
    function testExpensesSumarizedByUser() {
      return;
      
      $this->assertEqual(Currencies::count(), 4, 'Four currencies defined in database');
      
      list($dollar, $euro, $yen, $pound) = $this->getCurrencies();
      
      $this->website_project->setCurrency($euro, true);
      
      $this->assertEqual($this->application_project->getCurrency()->getId(), $dollar->getId(), 'Application project uses defualt currency');
      $this->assertEqual($this->website_project->getCurrency()->getId(), $euro->getId(), 'Website project uses');
      
      $application_project_record = $this->application_project->tracking()->logExpense(100, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      $application_project_task_record = $this->application_project_task->tracking()->logExpense(200, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      $website_project_record = $this->website_project->tracking()->logExpense(300, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      $website_project_task_record = $this->website_project_task->tracking()->logExpense(400, $this->second_user, $this->first_expense_category, DateTimeValue::now());
      
      $report = new TrackingReport();
      $report->setSumByUser(true);
      
      $result = $report->run($this->logged_user);
      
      $dollar_key = 'expenses_for_' . $dollar->getId();
      $euro_key = 'expenses_for_' . $euro->getId();
      $yen_key = 'expenses_for_' . $yen->getId();
      $pound_key = 'expenses_for_' . $pound->getId();
      
      $user_key = $this->second_user->getEmail();
      
      $this->assertTrue(is_array($result) && is_array($result['all']) && is_array($result['all']['records']), 'Valid report result');
      $this->assertEqual(count($result['all']['records']), 1, 'Four records returned, for four currencies that we have in the database');
      $this->assertTrue(isset($result['all']['records'][$user_key]), 'Data for user loaded');
      
      $this->assertTrue(isset($result['all']['records'][$user_key][$dollar_key]), 'Value loaded for dollar');
      $this->assertEqual($result['all']['records'][$user_key][$dollar_key], 300, 'Valid expenses for dollar');
      
      $this->assertTrue(isset($result['all']['records'][$user_key][$euro_key]), 'Value loaded for euro');
      $this->assertEqual($result['all']['records'][$user_key][$euro_key], 700, 'Valid expenses for euro');
      
      $this->assertTrue(isset($result['all']['records'][$user_key][$yen_key]), 'Value loaded for yen');
      $this->assertEqual($result['all']['records'][$user_key][$euro_key], 0, 'Valid expenses for yen');
      
      $this->assertTrue(isset($result['all']['records'][$user_key][$pound_key]), 'Value loaded for pound');
      $this->assertEqual($result['all']['records'][$user_key][$euro_key], 0, 'Valid expenses for pound');
    } // testExpensesSumarizedByUser
    
  }
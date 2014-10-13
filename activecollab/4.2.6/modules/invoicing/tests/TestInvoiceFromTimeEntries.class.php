<?php

  /**
   * Test invoice form time entires
   *
   * @package activeCollab.modules.invoicing
   * @subpackage tests
   */
  class TestInvoiceFromTimeEntries extends AngieModelTestCase {

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
     * First project that we will use for testing
     *
     * @var Project
     */
    private $first_project;

    /**
     * Second project
     *
     * @var Project
     */
    private $second_project;

    /**
     * First task in first project
     *
     * @var Task
     */
    private $first_projects_first_task;

    /**
     * Second task in first project
     *
     * @var Task
     */
    private $first_projects_second_task;

    /**
     * First task in second project
     *
     * @var Task
     */
    private $second_projects_first_task;

    /**
     * Second task in second project
     *
     * @var Task
     */
    private $second_projects_second_task;

    /**
     * Set up test enviornment
     */
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

      $this->first_project = new Project();
      $this->first_project->setAttributes(array(
        'name' => 'First Project',
        'leader_id' => 1,
        'company_id' => 1,
      ));
      $this->first_project->setState(STATE_VISIBLE);
      $this->first_project->save();

      $this->second_project = new Project();
      $this->second_project->setAttributes(array(
        'name' => 'Second Project',
        'leader_id' => 1,
        'company_id' => 1,
      ));
      $this->second_project->setState(STATE_VISIBLE);
      $this->second_project->save();

      $this->first_projects_first_task = new Task();
      $this->first_projects_first_task->setName('First Task');
      $this->first_projects_first_task->setProject($this->first_project);
      $this->first_projects_first_task->setCreatedBy($this->logged_user);
      $this->first_projects_first_task->setState(STATE_VISIBLE);
      $this->first_projects_first_task->setVisibility(VISIBILITY_NORMAL);
      $this->first_projects_first_task->save();

      $this->first_projects_second_task = new Task();
      $this->first_projects_second_task->setName('Second Task');
      $this->first_projects_second_task->setProject($this->first_project);
      $this->first_projects_second_task->setCreatedBy($this->logged_user);
      $this->first_projects_second_task->setState(STATE_VISIBLE);
      $this->first_projects_second_task->setVisibility(VISIBILITY_NORMAL);
      $this->first_projects_second_task->save();

      $this->second_projects_first_task = new Task();
      $this->second_projects_first_task->setName('First Task');
      $this->second_projects_first_task->setProject($this->second_project);
      $this->second_projects_first_task->setCreatedBy($this->logged_user);
      $this->second_projects_first_task->setState(STATE_VISIBLE);
      $this->second_projects_first_task->setVisibility(VISIBILITY_NORMAL);
      $this->second_projects_first_task->save();

      $this->second_projects_second_task = new Task();
      $this->second_projects_second_task->setName('Second Task');
      $this->second_projects_second_task->setProject($this->second_project);
      $this->second_projects_second_task->setCreatedBy($this->logged_user);
      $this->second_projects_second_task->setState(STATE_VISIBLE);
      $this->second_projects_second_task->setVisibility(VISIBILITY_NORMAL);
      $this->second_projects_second_task->save();
    } // setUp

    /**
     * Tear down after test execution
     */
    function tearDown() {
      $this->logged_user = null;
      $this->first_job_type = null;
      $this->second_job_type = null;
      $this->first_project = null;
      $this->second_project = null;
      $this->first_projects_first_task = null;
      $this->first_projects_second_task = null;
      $this->second_projects_first_task = null;
      $this->second_projects_second_task = null;

      parent::tearDown();
    } // tearDown

    /**
     * Test initialisation
     */
    function testInitialization() {
      $this->assertTrue($this->first_project->isLoaded(), 'First project created');
      $this->assertTrue($this->second_project->isLoaded(), 'Second project created');

      $this->assertTrue($this->first_job_type->isLoaded(), 'First job type loaded');
      $this->assertEqual($this->first_job_type->getDefaultHourlyRate(), 100, 'Valid value for default hourly rate');

      $this->assertTrue($this->second_job_type->isLoaded(), 'Second job type is loaded');
      $this->assertEqual($this->second_job_type->getDefaultHourlyRate(), 200, 'Valid value for default hourly rate');

      $this->assertTrue($this->first_projects_first_task->isLoaded(), 'Task created');
      $this->assertEqual($this->first_projects_first_task->getProjectId(), $this->first_project->getId(), 'Task belongs to a project');
      $this->assertEqual($this->first_projects_first_task->getTaskId(), 1, 'Task ID is correct');
      $this->assertEqual($this->first_projects_second_task->getProjectId(), $this->first_project->getId(), 'Task belongs to a project');
      $this->assertEqual($this->first_projects_second_task->getTaskId(), 2, 'Task ID is correct');

      $this->assertTrue($this->second_projects_first_task->isLoaded(), 'Task created');
      $this->assertEqual($this->second_projects_first_task->getProjectId(), $this->second_project->getId(), 'Task belongs to a project');
      $this->assertEqual($this->second_projects_first_task->getTaskId(), 1, 'Task ID is correct');
      $this->assertEqual($this->second_projects_second_task->getProjectId(), $this->second_project->getId(), 'Task belongs to a project');
      $this->assertEqual($this->second_projects_second_task->getTaskId(), 2, 'Task ID is correct');
    } // testInitialization

    /**
     * Test invoice items when we have multiple records with same job ID, and all project are using same hourly rates
     */
    function testGroupByJobTypeWithSameHourlyRates() {
      $time_record_1 = $this->first_projects_first_task->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $time_record_2 = $this->first_projects_first_task->tracking()->logTime(5, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);

      $time_record_3 = $this->second_projects_first_task->tracking()->logTime(2, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $time_record_4 = $this->second_projects_first_task->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);

      $this->assertIsA($time_record_1, 'TimeRecord');
      $this->assertTrue($time_record_1->isLoaded());
      $this->assertIsA($time_record_2, 'TimeRecord');
      $this->assertTrue($time_record_2->isLoaded());
      $this->assertIsA($time_record_3, 'TimeRecord');
      $this->assertTrue($time_record_3->isLoaded());
      $this->assertIsA($time_record_4, 'TimeRecord');
      $this->assertTrue($time_record_4->isLoaded());

      $report = new TrackingReport();

      $invoice = $report->invoice()->create($this->logged_user->getCompany(), 'Magic Lane 123', array(
        'sum_by' => Invoice::INVOICE_SETTINGS_SUM_ALL_BY_JOB_TYPE,
      ), $this->logged_user);

      $this->assertIsA($invoice, 'Invoice');
      $this->assertTrue($invoice->isLoaded());

      $items = $invoice->getItems();

      if($items instanceof DBResult) {
        $items = $items->toArray();
      } else {
        $this->fail('We did not get items result');
      } // if

      $this->assertTrue(is_array($items));
      $this->assertEqual(count($items), 1);

      // Test if first item is correct
      $this->assertEqual($items[0]->getDescription(), $this->first_job_type->getName());
      $this->assertEqual($items[0]->getQuantity(), 9);
      $this->assertEqual($items[0]->getUnitCost(), $this->first_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[0]->getTimeRecordIds(), array($time_record_1->getId(), $time_record_2->getId(), $time_record_3->getId(), $time_record_4->getId()));
    } // testGroupByJobTypeWithSameHourlyRates

    /**
     * Test invoice items when we have multiple records with same job ID, and all project are using same hourly rates
     */
    function testGroupByJobTypeWithPerProjectCustomHourlyRates() {
      $this->first_job_type->setCustomHourlyRateFor($this->second_project, 1000);

      $time_record_1 = $this->first_projects_first_task->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $time_record_2 = $this->first_projects_first_task->tracking()->logTime(5, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);

      $time_record_3 = $this->second_projects_first_task->tracking()->logTime(2, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $time_record_4 = $this->second_projects_first_task->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);

      $this->assertIsA($time_record_1, 'TimeRecord');
      $this->assertTrue($time_record_1->isLoaded());
      $this->assertIsA($time_record_2, 'TimeRecord');
      $this->assertTrue($time_record_2->isLoaded());
      $this->assertIsA($time_record_3, 'TimeRecord');
      $this->assertTrue($time_record_3->isLoaded());
      $this->assertIsA($time_record_4, 'TimeRecord');
      $this->assertTrue($time_record_4->isLoaded());

      $report = new TrackingReport();

      $invoice = $report->invoice()->create($this->logged_user->getCompany(), 'Magic Lane 123', array(
        'sum_by' => Invoice::INVOICE_SETTINGS_SUM_ALL_BY_JOB_TYPE,
      ), $this->logged_user);

      $this->assertIsA($invoice, 'Invoice');
      $this->assertTrue($invoice->isLoaded());

      $items = $invoice->getItems();

      if($items instanceof DBResult) {
        $items = $items->toArray();
      } else {
        $this->fail('We did not get items result');
      } // if

      $this->assertTrue(is_array($items));
      $this->assertEqual(count($items), 1);

      // Test if first item is correct
      $this->assertEqual($items[0]->getDescription(), $this->first_job_type->getName());
      $this->assertEqual($items[0]->getQuantity(), 1);
      $this->assertEqual($items[0]->getUnitCost(), 6 * $this->first_job_type->getDefaultHourlyRate() + 3 * 1000);
      $this->assertEqual($items[0]->getTimeRecordIds(), array($time_record_1->getId(), $time_record_2->getId(), $time_record_3->getId(), $time_record_4->getId()));
    } // testGroupByJobTypeWithPerProjectCustomHourlyRates

    /**
     * Test invoice items when we have multiple records with same job ID, and all project are using same hourly rates
     */
    function testGroupByProject() {
      $time_record_1 = $this->first_project->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $time_record_2 = $this->first_projects_first_task->tracking()->logTime(5, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);

      $time_record_3 = $this->second_project->tracking()->logTime(2, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $time_record_4 = $this->second_projects_first_task->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);

      $this->assertIsA($time_record_1, 'TimeRecord');
      $this->assertTrue($time_record_1->isLoaded());
      $this->assertIsA($time_record_2, 'TimeRecord');
      $this->assertTrue($time_record_2->isLoaded());
      $this->assertIsA($time_record_3, 'TimeRecord');
      $this->assertTrue($time_record_3->isLoaded());
      $this->assertIsA($time_record_4, 'TimeRecord');
      $this->assertTrue($time_record_4->isLoaded());

      $report = new TrackingReport();

      $invoice = $report->invoice()->create($this->logged_user->getCompany(), 'Magic Lane 123', array(
        'sum_by' => Invoice::INVOICE_SETTINGS_SUM_ALL_BY_PROJECT,
      ), $this->logged_user);

      $this->assertIsA($invoice, 'Invoice');
      $this->assertTrue($invoice->isLoaded());

      $items = $invoice->getItems();

      if($items instanceof DBResult) {
        $items = $items->toArray();
      } else {
        $this->fail('We did not get items result');
      } // if

      $this->assertTrue(is_array($items));
      $this->assertEqual(count($items), 2);

      // Test if first item is correct
      $this->assertEqual($items[0]->getDescription(), 'Project First Project');
      $this->assertEqual($items[0]->getQuantity(), 6);
      $this->assertEqual($items[0]->getUnitCost(), $this->first_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[0]->getTimeRecordIds(), array($time_record_1->getId(), $time_record_2->getId()));

      $this->assertEqual($items[1]->getDescription(), 'Project Second Project');
      $this->assertEqual($items[1]->getQuantity(), 3);
      $this->assertEqual($items[1]->getUnitCost(), $this->first_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[1]->getTimeRecordIds(), array($time_record_3->getId(), $time_record_4->getId()));
    } // testGroupByProject

    /**
     * Test invoice items when we have multiple records with same job ID, and all project are using same hourly rates
     */
    function testGroupByProjectWithDifferentJobType() {
      $time_record_1 = $this->first_project->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $time_record_2 = $this->first_projects_first_task->tracking()->logTime(5, $this->logged_user, $this->second_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);

      $time_record_3 = $this->second_project->tracking()->logTime(2, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $time_record_4 = $this->second_projects_first_task->tracking()->logTime(1, $this->logged_user, $this->second_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);

      $this->assertIsA($time_record_1, 'TimeRecord');
      $this->assertTrue($time_record_1->isLoaded());
      $this->assertIsA($time_record_2, 'TimeRecord');
      $this->assertTrue($time_record_2->isLoaded());
      $this->assertIsA($time_record_3, 'TimeRecord');
      $this->assertTrue($time_record_3->isLoaded());
      $this->assertIsA($time_record_4, 'TimeRecord');
      $this->assertTrue($time_record_4->isLoaded());

      $report = new TrackingReport();

      $invoice = $report->invoice()->create($this->logged_user->getCompany(), 'Magic Lane 123', array(
        'sum_by' => Invoice::INVOICE_SETTINGS_SUM_ALL_BY_PROJECT,
      ), $this->logged_user);

      $this->assertIsA($invoice, 'Invoice');
      $this->assertTrue($invoice->isLoaded());

      $items = $invoice->getItems();

      if($items instanceof DBResult) {
        $items = $items->toArray();
      } else {
        $this->fail('We did not get items result');
      } // if

      $this->assertTrue(is_array($items));
      $this->assertEqual(count($items), 2);

      // Test if first item is correct
      $this->assertEqual($items[0]->getDescription(), 'Project First Project');
      $this->assertEqual($items[0]->getQuantity(), 1);
      $this->assertEqual($items[0]->getUnitCost(), 1 * $this->first_job_type->getDefaultHourlyRate() + 5 * $this->second_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[0]->getTimeRecordIds(), array($time_record_1->getId(), $time_record_2->getId()));

      $this->assertEqual($items[1]->getDescription(), 'Project Second Project');
      $this->assertEqual($items[1]->getQuantity(), 1);
      $this->assertEqual($items[1]->getUnitCost(), 2 * $this->first_job_type->getDefaultHourlyRate() + 1 * $this->second_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[1]->getTimeRecordIds(), array($time_record_3->getId(), $time_record_4->getId()));
    } // testGroupByProjectWithDifferentJobType

    /**
     * Test invoice items when we have multiple records with same job ID
     */
    function testGroupByTaskWithRecordsAttachedToTasksAndProjects() {
      $time_record_1 = $this->first_project->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $time_record_2 = $this->first_project->tracking()->logTime(5, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);

      $time_record_3 = $this->first_projects_second_task->tracking()->logTime(2, $this->logged_user, $this->second_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $time_record_4 = $this->first_projects_second_task->tracking()->logTime(1, $this->logged_user, $this->second_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);

      $this->assertIsA($time_record_1, 'TimeRecord');
      $this->assertTrue($time_record_1->isLoaded());
      $this->assertIsA($time_record_2, 'TimeRecord');
      $this->assertTrue($time_record_2->isLoaded());
      $this->assertIsA($time_record_3, 'TimeRecord');
      $this->assertTrue($time_record_3->isLoaded());
      $this->assertIsA($time_record_4, 'TimeRecord');
      $this->assertTrue($time_record_4->isLoaded());

      $report = new TrackingReport();

      $invoice = $report->invoice()->create($this->logged_user->getCompany(), 'Magic Lane 123', array(
        'sum_by' => Invoice::INVOICE_SETTINGS_SUM_ALL_BY_TASK,
      ), $this->logged_user);

      $this->assertIsA($invoice, 'Invoice');
      $this->assertTrue($invoice->isLoaded());

      $items = $invoice->getItems();

      if($items instanceof DBResult) {
        $items = $items->toArray();
      } else {
        $this->fail('We did not get items result');
      } // if

      $this->assertTrue(is_array($items));
      $this->assertEqual(count($items), 2);

      // Test if first item is correct
      $this->assertEqual($items[0]->getDescription(), 'Project First Project');
      $this->assertEqual($items[0]->getQuantity(), 6);
      $this->assertEqual($items[0]->getUnitCost(), $this->first_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[0]->getTimeRecordIds(), array($time_record_1->getId(), $time_record_2->getId()));

      // Test if second item is correct
      $this->assertEqual($items[1]->getDescription(), 'Task #2: Second Task (First Project)');
      $this->assertEqual($items[1]->getQuantity(), 3);
      $this->assertEqual($items[1]->getUnitCost(), $this->second_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[1]->getTimeRecordIds(), array($time_record_3->getId(), $time_record_4->getId()));
    } // testGroupByTaskWithRecordsAttachedToTasksAndProjects

    /**
     * Test invoice items when we have multiple records with same job ID
     */
    function testGroupByTaskWithSameJobId() {
      $time_record_1 = $this->first_projects_first_task->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $time_record_2 = $this->first_projects_first_task->tracking()->logTime(5, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);

      $time_record_3 = $this->first_projects_second_task->tracking()->logTime(2, $this->logged_user, $this->second_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $time_record_4 = $this->first_projects_second_task->tracking()->logTime(1, $this->logged_user, $this->second_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);

      $this->assertIsA($time_record_1, 'TimeRecord');
      $this->assertTrue($time_record_1->isLoaded());
      $this->assertIsA($time_record_2, 'TimeRecord');
      $this->assertTrue($time_record_2->isLoaded());
      $this->assertIsA($time_record_3, 'TimeRecord');
      $this->assertTrue($time_record_3->isLoaded());
      $this->assertIsA($time_record_4, 'TimeRecord');
      $this->assertTrue($time_record_4->isLoaded());

      $report = new TrackingReport();

      $invoice = $report->invoice()->create($this->logged_user->getCompany(), 'Magic Lane 123', array(
        'sum_by' => Invoice::INVOICE_SETTINGS_SUM_ALL_BY_TASK,
      ), $this->logged_user);

      $this->assertIsA($invoice, 'Invoice');
      $this->assertTrue($invoice->isLoaded());

      $items = $invoice->getItems();

      if($items instanceof DBResult) {
        $items = $items->toArray();
      } else {
        $this->fail('We did not get items result');
      } // if

      $this->assertTrue(is_array($items));
      $this->assertEqual(count($items), 2);

      // Test if first item is correct
      $this->assertEqual($items[0]->getDescription(), 'Task #1: First Task (First Project)');
      $this->assertEqual($items[0]->getQuantity(), 6);
      $this->assertEqual($items[0]->getUnitCost(), $this->first_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[0]->getTimeRecordIds(), array($time_record_1->getId(), $time_record_2->getId()));

      // Test if second item is correct
      $this->assertEqual($items[1]->getDescription(), 'Task #2: Second Task (First Project)');
      $this->assertEqual($items[1]->getQuantity(), 3);
      $this->assertEqual($items[1]->getUnitCost(), $this->second_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[1]->getTimeRecordIds(), array($time_record_3->getId(), $time_record_4->getId()));
    } // testGroupByTaskWithSameJobId

    /**
     * Test if we have proper items when time records on the same task have different job types
     */
    function testGroupByTaskWithDifferentJobIds() {
      $time_record_1 = $this->first_projects_first_task->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);
      $time_record_2 = $this->first_projects_first_task->tracking()->logTime(5, $this->logged_user, $this->second_job_type, DateValue::makeFromString('yesterday'), BILLABLE_STATUS_BILLABLE);

      $time_record_3 = $this->first_projects_second_task->tracking()->logTime(2, $this->logged_user, $this->second_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);
      $time_record_4 = $this->first_projects_second_task->tracking()->logTime(1, $this->logged_user, $this->first_job_type, DateValue::makeFromString('today'), BILLABLE_STATUS_BILLABLE);

      $this->assertIsA($time_record_1, 'TimeRecord');
      $this->assertTrue($time_record_1->isLoaded());
      $this->assertIsA($time_record_2, 'TimeRecord');
      $this->assertTrue($time_record_2->isLoaded());
      $this->assertIsA($time_record_3, 'TimeRecord');
      $this->assertTrue($time_record_3->isLoaded());
      $this->assertIsA($time_record_4, 'TimeRecord');
      $this->assertTrue($time_record_4->isLoaded());

      $report = new TrackingReport();

      $invoice = $report->invoice()->create($this->logged_user->getCompany(), 'Magic Lane 123', array(
        'sum_by' => Invoice::INVOICE_SETTINGS_SUM_ALL_BY_TASK,
      ), $this->logged_user);

      $this->assertIsA($invoice, 'Invoice');
      $this->assertTrue($invoice->isLoaded());

      $items = $invoice->getItems();

      if($items instanceof DBResult) {
        $items = $items->toArray();
      } else {
        $this->fail('We did not get items result');
      } // if

      $this->assertTrue(is_array($items));
      $this->assertEqual(count($items), 2);

      // Test if first item is correct
      $this->assertEqual($items[0]->getDescription(), 'Task #1: First Task (First Project)');
      $this->assertEqual($items[0]->getQuantity(), 1);
      $this->assertEqual($items[0]->getUnitCost(), 1 * $this->first_job_type->getDefaultHourlyRate() + 5 * $this->second_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[0]->getTimeRecordIds(), array($time_record_1->getId(), $time_record_2->getId()));

      // Test if second item is correct
      $this->assertEqual($items[1]->getDescription(), 'Task #2: Second Task (First Project)');
      $this->assertEqual($items[1]->getQuantity(), 1);
      $this->assertEqual($items[1]->getUnitCost(), 2 * $this->second_job_type->getDefaultHourlyRate() + 1 * $this->first_job_type->getDefaultHourlyRate());
      $this->assertEqual($items[1]->getTimeRecordIds(), array($time_record_3->getId(), $time_record_4->getId()));
    } // testGroupByTaskWithDifferentJobIds

  }
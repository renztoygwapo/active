<?php

  /**
   * Test task attachments
   *
   * @package activeCollab.modules.tasks
   * @subpackage tests
   */
  class TestTaskAttachments extends AngieModelTestCase {

    /**
     * Logged user
     *
     * @var User
     */
    private $logged_user;

    /**
     * @var Project
     */
    private $active_project;

    /**
     * @var Task
     */
    private $active_task;

    /**
     * @var string
     */
    private $test_file_path;

    /**
     * Construct the test case
     *
     * @param bool $label
     */
    function __construct($label = false) {
      parent::__construct($label);
      
      $this->test_file_path = ATTACHMENTS_FRAMEWORK_PATH . '/tests/resources/test.jpg';
    } // __construct

    /**
     * Set up test environment
     */
    function setUp() {
      parent::setUp();
      
      $this->logged_user = Users::findById(1);
      
      $this->active_project = new Project();
      $this->active_project->setAttributes(array(
        'name' => 'Application', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->active_project->save();
      
      $this->active_task = new Task();
      $this->active_task->setName('Task 1');
      $this->active_task->setProject($this->active_project);
      $this->active_task->setCreatedBy($this->logged_user);
      $this->active_task->setState(STATE_VISIBLE);
      $this->active_task->setVisibility(VISIBILITY_NORMAL);
      $this->active_task->save();
    } // setUp

    /**
     * Tear down test environment
     */
    function tearDown() {
      $this->logged_user = null;
      $this->active_project = null;
      $this->active_task = null;
    } // tearDown

    /**
     * Test initialization
     */
    function testInitialization() {
      $this->assertTrue($this->logged_user->isLoaded(), 'Test user loaded');
      $this->assertTrue($this->active_project->isLoaded(), 'Test project created');
      $this->assertTrue($this->active_task->isLoaded(), 'Test task created');
    } // testInitialization

    /**
     * Test attachment process
     */
    function testAttachFiles() {
      $this->assertEqual($this->active_task->attachments()->count($this->logged_user), 0, 'No attachments yet');
      
      $this->active_task->attachments()->attachFile($this->test_file_path, basename($this->test_file_path), 'image/jpeg', $this->logged_user, true);
      
      $this->assertEqual($this->active_task->attachments()->count($this->logged_user), 1, 'One file attached');
      
      $attachment = Attachments::findById(1);
      
      $this->assertTrue($attachment->isLoaded(), 'Attachment was found');
      $this->assertTrue($this->active_task->is($attachment->getParent()), 'Attachment parent is active task');
      $this->assertEqual($this->active_task->getState(), $attachment->getState(), 'State is inherited from parent');
      
      $this->assertEqual($attachment->getName(), 'test.jpg', 'File name is OK');
      $this->assertEqual($attachment->getMimeType(), 'image/jpeg', 'MIME type is correct');
      $this->assertTrue($attachment->getCreatedBy()->is($this->logged_user), 'Correct user is set as uploader');
      $this->assertEqual($attachment->getMd5(), md5_file($this->test_file_path), 'MD5 is OK');
      
      $attachment->delete();
      
      $this->assertEqual($this->active_task->attachments()->count($this->logged_user), 0, 'No more attachments');
    } // testAttachFiles
    
  }
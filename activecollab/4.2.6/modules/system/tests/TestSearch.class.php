<?php

  /**
   * Test search
   *
   * @package activeCollab.modules.system
   * @subpackage tests
   */
  class TestSearch extends AngieModelTestCase {

    /**
     * Administrator
     *
     * @var Administrator
     */
    private $logged_user;

    /**
     * Client company
     *
     * @var Company
     */
    private $client_company;

    /**
     * Client user
     *
     * @var Client
     */
    private $client;

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
      $this->logged_user->setFirstName('Nikola');
      $this->logged_user->setLastName('Tesla');
      $this->logged_user->save();

      $this->client_company = new Company();
      $this->client_company->setName('Client Company');
      $this->client_company->setState(STATE_VISIBLE);
      $this->client_company->save();

      $this->client = new Client();
      $this->client->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => $this->client_company->getId(),
        'password' => 'test',
      ));
      $this->client->setState(STATE_VISIBLE);
      $this->client->save();

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
      $this->first_projects_first_task->setName('Prvi zadatak');
      $this->first_projects_first_task->setProject($this->first_project);
      $this->first_projects_first_task->setCreatedBy($this->logged_user);
      $this->first_projects_first_task->setState(STATE_VISIBLE);
      $this->first_projects_first_task->setVisibility(VISIBILITY_NORMAL);
      $this->first_projects_first_task->save();

      $this->first_projects_second_task = new Task();
      $this->first_projects_second_task->setName('Drugi zadatak');
      $this->first_projects_second_task->setProject($this->first_project);
      $this->first_projects_second_task->setCreatedBy($this->logged_user);
      $this->first_projects_second_task->setState(STATE_VISIBLE);
      $this->first_projects_second_task->setVisibility(VISIBILITY_NORMAL);
      $this->first_projects_second_task->save();

      $this->second_projects_first_task = new Task();
      $this->second_projects_first_task->setName('Prvi zadatak');
      $this->second_projects_first_task->setProject($this->second_project);
      $this->second_projects_first_task->setCreatedBy($this->logged_user);
      $this->second_projects_first_task->setState(STATE_VISIBLE);
      $this->second_projects_first_task->setVisibility(VISIBILITY_NORMAL);
      $this->second_projects_first_task->save();

      $this->second_projects_second_task = new Task();
      $this->second_projects_second_task->setName('Drugi zadatak');
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
      $this->client_company = null;
      $this->client = null;
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
      $this->assertTrue($this->client_company->isLoaded());
      $this->assertTrue($this->client->isLoaded());
      $this->assertIsA($this->client, 'Client');

      $this->assertTrue($this->client->canView($this->logged_user));
      $this->assertFalse($this->logged_user->canView($this->client));

      $this->assertTrue($this->first_project->isLoaded(), 'First project created');
      $this->assertTrue($this->second_project->isLoaded(), 'Second project created');

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
     * Test search users
     */
    function testSearchUsers() {
      $users_search_index = Search::getIndex('users');

      $this->assertTrue($users_search_index->isInitialized());

      $result = $users_search_index->query($this->logged_user, 'Nikola');

      $this->assertTrue(is_array($result));
      $this->assertEqual(count($result), 1);
      $this->assertEqual($result[0]['name'], 'Nikola Tesla');

      $result = $users_search_index->query($this->client, 'Nikola');

      $this->assertTrue(is_array($result));
      $this->assertEqual(count($result), 0);

      $this->first_project->users()->add($this->logged_user);
      $this->first_project->users()->add($this->client);

      $this->assertTrue($this->logged_user->canView($this->client));

      $result = $users_search_index->query($this->client, 'Nikola');

      $this->assertTrue(is_array($result));
      $this->assertEqual(count($result), 1);
      $this->assertEqual($result[0]['name'], 'Nikola Tesla');
    } // testSearchUsers

    /**
     * Administrator search project objects
     */
    function testAdministratorSearchProjectObjects() {
      $project_objects_seach_index = Search::getIndex('project_objects');

      $result = $project_objects_seach_index->query($this->logged_user, 'Prvi zadatak');

      $this->assertTrue(is_array($result));
      $this->assertEqual(count($result), 2);

      $this->assertEqual($result[0]['id'], $this->second_projects_first_task->getId());
      $this->assertEqual($result[1]['id'], $this->first_projects_first_task->getId());

      $result = $project_objects_seach_index->query($this->logged_user, 'Prvi zadatak', array(new SearchCriterion('project_id', SearchCriterion::IS, $this->first_project->getId())));

      $this->assertTrue(is_array($result));
      $this->assertEqual(count($result), 1);

      $this->assertEqual($result[0]['id'], $this->first_projects_first_task->getId());
    } // testAdministratorSearchProjectObjects

    /**
     * Client search project objects
     */
    function testClientSearchProjectObjects() {
      $project_objects_seach_index = Search::getIndex('project_objects');

      // ---------------------------------------------------
      //  No access to project
      // ---------------------------------------------------

      $this->assertFalse($this->first_projects_first_task->canView($this->client));
      $result = $project_objects_seach_index->query($this->client, 'Prvi zadatak');

      $this->assertTrue(is_array($result));
      $this->assertEqual(count($result), 0);

      // ---------------------------------------------------
      //  Access to project, normal task
      // ---------------------------------------------------

      $this->first_project->users()->add($this->client, null, array(
        'task' => ProjectRole::PERMISSION_ACCESS
      ));

      $this->assertTrue($this->first_projects_first_task->canView($this->client, true));

      $result = $project_objects_seach_index->query($this->client, 'Prvi zadatak');

      $this->assertTrue(is_array($result));
      $this->assertEqual(count($result), 1);

      $this->assertEqual($result[0]['id'], $this->first_projects_first_task->getId());

      // ---------------------------------------------------
      //  Access to project, private task
      // ---------------------------------------------------

      $this->first_projects_first_task->setVisibility(VISIBILITY_PRIVATE);
      $this->first_projects_first_task->save();

      $this->assertFalse($this->first_projects_first_task->canView($this->client));
      $result = $project_objects_seach_index->query($this->client, 'Prvi zadatak');

      $this->assertTrue(is_array($result));
      $this->assertEqual(count($result), 0);
    } // testClientSearchProjectObjects

  }
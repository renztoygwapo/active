<?php

  /**
   * Test assignment filters
   * 
   * @package activeCollab.modules.system
   * @subpackage tests
   */
  class TestAssignmentFilters extends AngieModelTestCase {
    
    /**
     * Logged user
     *
     * @var User
     */
    private $logged_user;
    
    /**
     * Application project
     *
     * @var Project
     */
    private $application_project;
    
    /**
     * Spec application milestone
     *
     * @var Milestone
     */
    private $application_spec_milestone;
    
    /**
     * Build application milestone
     *
     * @var Milestone
     */
    private $application_build_milestone;
    
    /**
     * Test application milestone
     *
     * @var Milestone
     */
    private $application_test_milestone;
    
    /**
     * Deploy application milestone
     *
     * @var Milestone
     */
    private $application_deploy_milestone;
    
    /**
     * Website project
     *
     * @var Project
     */
    private $website_project;
    
    /**
     * Wireframe website milestone
     *
     * @var Milestone
     */
    private $website_wireframe_milestone;
    
    /**
     * Design website milestone
     *
     * @var Milestone
     */
    private $website_design_milestone;
    
    /**
     * Build website milestone
     *
     * @var Milestone
     */
    private $website_build_milestone;
    
    /**
     * Deploy website milestone
     *
     * @var Milestone
     */
    private $website_deploy_milestone;
  
    /**
     * Set up environment for tests
     */
    function setUp() {
      parent::setUp();
      
      $this->logged_user = Users::findById(1);

      ConfigOptions::removeValuesFor($this->logged_user, array('time_timezone', 'time_dst'));

      get_system_gmt_offset(true);
      get_user_gmt_offset($this->logged_user, false);
      
      $this->application_project = new Project();
      $this->application_project->setAttributes(array(
        'name' => 'Application', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->application_project->setState(STATE_VISIBLE);
      $this->application_project->save();
      
      $milestones = array(
        'application_spec_milestone' => 'Spec', 
        'application_build_milestone' => 'Build', 
        'application_test_milestone' => 'Test', 
        'application_deploy_milestone' => 'Deploy', 
      );
      
      foreach($milestones as $milestone_var => $milestone_name) {
        $this->$milestone_var = new Milestone();
        $this->$milestone_var->setName($milestone_name);
        $this->$milestone_var->setProject($this->application_project);
        $this->$milestone_var->setCreatedBy($this->logged_user);
        $this->$milestone_var->setState(STATE_VISIBLE);
        $this->$milestone_var->save();
      } // foreach
      
      $this->website_project = new Project();
      $this->website_project->setAttributes(array(
        'name' => 'Website', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->website_project->setState(STATE_VISIBLE);
      $this->website_project->save();
      
      $milestones = array(
        'website_wireframe_milestone' => 'Wireframe', 
        'website_design_milestone' => 'Design', 
        'website_build_milestone' => 'Build', 
        'website_deploy_milestone' => 'Deploy', 
      );
      
      foreach($milestones as $milestone_var => $milestone_name) {
        $this->$milestone_var = new Milestone();
        $this->$milestone_var->setName($milestone_name);
        $this->$milestone_var->setProject($this->website_project);
        $this->$milestone_var->setCreatedBy($this->logged_user);
        $this->$milestone_var->setState(STATE_VISIBLE);
        $this->$milestone_var->save();
      } // foreach
    } // setUp

    /**
     * Test if we have properly configured test environment
     */
    function testSetUp() {
      $this->assertEqual(get_user_gmt_offset($this->logged_user, false), 0);
      $this->assertFalse(ConfigOptions::getValueFor('time_dst', $this->logged_user));

      $this->assertTrue($this->logged_user->isLoaded(), 'We have user loaded');

      $this->assertTrue($this->application_project->isLoaded(), 'Application project created');
      $this->assertTrue($this->application_spec_milestone->isLoaded(), 'Application spec milestone created');
      $this->assertTrue($this->application_build_milestone->isLoaded(), 'Application build milestone created');
      $this->assertTrue($this->application_test_milestone->isLoaded(), 'Application test milestone created');
      $this->assertTrue($this->application_deploy_milestone->isLoaded(), 'Application deploy milestone created');

      $this->assertTrue($this->website_project->isLoaded(), 'Website project created');
      $this->assertTrue($this->website_wireframe_milestone->isLoaded(), 'Website wireframe milestone created');
      $this->assertTrue($this->website_design_milestone->isLoaded(), 'Website design milestone created');
      $this->assertTrue($this->website_build_milestone->isLoaded(), 'Website deploy milestone created');
      $this->assertTrue($this->website_deploy_milestone->isLoaded(), 'Website deploy milestone created');
    } // testSetUp

    /**
     * Test if all assignments are loaded for administrators
     */
    function testAllForAdmins() {
      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task2 = new Task();
      $task2->setName('Task 2');
      $task2->setProject($this->website_project);
      $task2->setCreatedBy($this->logged_user);
      $task2->setState(STATE_VISIBLE);
      $task2->save();

      $filter = new AssignmentFilter();

      // ---------------------------------------------------
      //  None, no involvement
      // ---------------------------------------------------

      $this->expectException(new DataFilterConditionsError('project_filter', Projects::PROJECT_FILTER_ANY, null, 'There are no projects in the database that current user can see'));
      $filter->run($this->logged_user);

      // ---------------------------------------------------
      //  All for admins
      // ---------------------------------------------------

      $filter->setIncludeAllProjects(true);

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results));
      $this->assertTrue(isset($results['all']));
      $this->assertEqual(count($results['all']), 2);
    } // testAllForAdmins

    /**
     * Test if milestones filter is working properly
     */
    function testMilestonesFilter() {
      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setMilestone($this->application_spec_milestone);
      $task1->setCreatedBy($this->logged_user);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task2 = new Task();
      $task2->setName('Task 2');
      $task2->setProject($this->website_project);
      $task2->setMilestone($this->application_build_milestone);
      $task2->setCreatedBy($this->logged_user);
      $task2->setState(STATE_VISIBLE);
      $task2->save();

      $task3 = new Task();
      $task3->setName('Task 3');
      $task3->setProject($this->application_project);
      $task3->setCreatedBy($this->logged_user);
      $task3->setState(STATE_VISIBLE);
      $task3->save();

      $this->assertTrue($task1->isLoaded(), 'Task 1 saved');
      $this->assertEqual($task1->getMilestoneId(), $this->application_spec_milestone->getId(), 'Proper milestone for task 1');
      $this->assertTrue($task2->isLoaded(), 'Task 2 saved');
      $this->assertEqual($task2->getMilestoneId(), $this->application_build_milestone->getId(), 'Proper milestone for task 2');
      $this->assertTrue($task3->isLoaded(), 'Task 3 saved');
      $this->assertEqual($task3->getMilestoneId(), null, 'No milestone for task 3');

      $this->assertNotEqual($task1->getMilestoneId(), $task2->getMilestoneId(), 'Make sure that we have different milestones set for the two tasks');

      $filter = new AssignmentFilter();
      $filter->setIncludeAllProjects(true);

      // ---------------------------------------------------
      //  One Milestone
      // ---------------------------------------------------

      $filter->filterByMilestoneNames('Spec');

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results));
      $this->assertTrue(isset($results['all']));
      $this->assertEqual(count($results['all']['assignments']), 1);
      $this->assertNotNull($results['all']['assignments'][$task1->getId()]);

      // ---------------------------------------------------
      //  Multiple Milestones
      // ---------------------------------------------------

      $filter->filterByMilestoneNames('Spec, Build');

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results));
      $this->assertTrue(isset($results['all']));
      $this->assertEqual(count($results['all']['assignments']), 2);

      $this->assertNotNull($results['all']['assignments'][$task1->getId()]);
      $this->assertNotNull($results['all']['assignments'][$task2->getId()]);
    } // testMilestonesFilter

    /**
     * Test created by filter
     */
    function testCreatedBy() {
      $second_company = new Company();
      $second_company->setName('Second company');
      $second_company->setState(STATE_VISIBLE);
      $second_company->save();

      $this->assertTrue($second_company->isLoaded(), 'Second company created');

      $second_user = new Administrator();
      $second_user->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => $second_company->getId(),
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded(), 'Second user created');
      $this->assertTrue($second_user->getCompany()->is($second_company), 'Company set for second user');

      $third_user = new Administrator();
      $third_user->setAttributes(array(
        'email' => 'third-user@test.com',
        'company_id' => $second_company->getId(),
        'password' => 'test',
      ));
      $third_user->setState(STATE_VISIBLE);
      $third_user->save();

      $this->assertTrue($third_user->isLoaded(), 'Third user created');
      $this->assertTrue($third_user->getCompany()->is($second_company), 'Company set for third user');

      $this->application_project->users()->add($this->logged_user);
      $this->application_project->users()->add($second_user);
      $this->application_project->users()->add($third_user);

      $this->assertTrue($this->application_project->users()->isMember($this->logged_user, false));
      $this->assertTrue($this->application_project->users()->isMember($second_user, false));
      $this->assertTrue($this->application_project->users()->isMember($third_user, false));

      $this->website_project->users()->add($this->logged_user);
      $this->website_project->users()->add($second_user, null, array(
        'task' => ProjectRole::PERMISSION_MANAGE,
      ));

      $this->website_project->users()->add($third_user, null, array(
        'task' => ProjectRole::PERMISSION_ACCESS,
      ));

      $this->assertTrue($this->website_project->users()->isMember($this->logged_user, false), 'First user is a member of a project');
      $this->assertTrue($this->website_project->users()->isMember($second_user, false), 'Second user is a member of a project');
      $this->assertTrue($this->website_project->users()->isMember($third_user, false), 'Third user is a member of a project');

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task2 = new Task();
      $task2->setName('Task 2');
      $task2->setProject($this->website_project);
      $task2->setCreatedBy($second_user);
      $task2->setState(STATE_VISIBLE);
      $task2->save();

      $task3 = new Task();
      $task3->setName('Task 3');
      $task3->setProject($this->application_project);
      $task3->setCreatedBy($second_user);
      $task3->setState(STATE_VISIBLE);
      $task3->save();

      $task4 = new Task();
      $task4->setName('Task 4');
      $task4->setProject($this->application_project);
      $task4->setCreatedBy($third_user);
      $task4->setState(STATE_VISIBLE);
      $task4->save();

      $this->assertTrue($task1->getCreatedBy()->is($this->logged_user), 'Task 1 created by logged user');
      $this->assertTrue($task2->getCreatedBy()->is($second_user), 'Task 2 created by second user');
      $this->assertTrue($task3->getCreatedBy()->is($second_user), 'Task 3 created by second user');
      $this->assertTrue($task4->getCreatedBy()->is($third_user), 'Task 4 created by third user');

      // ---------------------------------------------------
      //  All tasks
      // ---------------------------------------------------

      $filter = new AssignmentFilter();

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results) && isset($results['all']) && $results['all']['assignments'], 'Valid result');
      $this->assertEqual(count($results['all']['assignments']), 4, 'Four tasks in the database');

      // ---------------------------------------------------
      //  Created by logged user
      // ---------------------------------------------------

      $filter = new AssignmentFilter();
      $filter->filterCreatedByUsers(array($this->logged_user->getId()));

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results) && isset($results['all']) && $results['all']['assignments'], 'Valid result');
      $this->assertEqual(count($results['all']['assignments']), 1, 'One task created by logged user');

      $this->assertTrue(isset($results['all']['assignments'][$task1->getId()]), 'Task 1 is in the result');
      $this->assertEqual(isset($results['all']['assignments'][$task1->getId()]['created_by_id']), $this->logged_user->getId(), 'Proper created_by_id value');

      // ---------------------------------------------------
      //  Created by second user
      // ---------------------------------------------------

      $filter = new AssignmentFilter();
      $filter->filterCreatedByUsers(array($second_user->getId()));

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results) && isset($results['all']) && $results['all']['assignments'], 'Valid result');
      $this->assertEqual(count($results['all']['assignments']), 2, 'Two tasks created by second user');

      $this->assertTrue(isset($results['all']['assignments'][$task2->getId()]), 'Task 2 is in the result');
      $this->assertEqual(isset($results['all']['assignments'][$task2->getId()]['created_by_id']), $this->logged_user->getId(), 'Proper created_by_id value');

      $this->assertTrue(isset($results['all']['assignments'][$task3->getId()]), 'Task 3 is in the result');
      $this->assertEqual(isset($results['all']['assignments'][$task3->getId()]['created_by_id']), $this->logged_user->getId(), 'Proper created_by_id value');

      // ---------------------------------------------------
      //  Created by members of owner company
      // ---------------------------------------------------

      $filter = new AssignmentFilter();
      $filter->filterCreatedByCompany(1);

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results) && isset($results['all']) && $results['all']['assignments'], 'Valid result');
      $this->assertEqual(count($results['all']['assignments']), 1, 'One task created by members of owner company');

      $this->assertTrue(isset($results['all']['assignments'][$task1->getId()]), 'Task 1 is in the result');
      $this->assertEqual(isset($results['all']['assignments'][$task1->getId()]['created_by_id']), $this->logged_user->getId(), 'Proper created_by_id value');

      // ---------------------------------------------------
      //  Created by members of second company
      // ---------------------------------------------------

      $filter = new AssignmentFilter();
      $filter->filterCreatedByCompany($second_company->getId());

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results) && isset($results['all']) && $results['all']['assignments'], 'Valid result');
      $this->assertEqual(count($results['all']['assignments']), 3, 'Three tasks created by members of second company');

      $this->assertTrue(isset($results['all']['assignments'][$task2->getId()]), 'Task 2 is in the result');
      $this->assertEqual(isset($results['all']['assignments'][$task2->getId()]['created_by_id']), $this->logged_user->getId(), 'Proper created_by_id value');

      $this->assertTrue(isset($results['all']['assignments'][$task3->getId()]), 'Task 3 is in the result');
      $this->assertEqual(isset($results['all']['assignments'][$task3->getId()]['created_by_id']), $this->logged_user->getId(), 'Proper created_by_id value');

      $this->assertTrue(isset($results['all']['assignments'][$task4->getId()]), 'Task 4 is in the result');
      $this->assertEqual(isset($results['all']['assignments'][$task4->getId()]['created_by_id']), $this->logged_user->getId(), 'Proper created_by_id value');
    } // testCreatedBy

    /**
     * Test project client filter
     */
    function testProjectClientFilter() {
      $second_company = new Company();
      $second_company->setName('Second company');
      $second_company->setState(STATE_VISIBLE);
      $second_company->save();

      $this->assertTrue($second_company->isLoaded(), 'Second company created');

      $this->website_project->setCompanyId($second_company->getId());
      $this->website_project->save();

      $this->assertTrue($this->website_project->getCompany()->is($second_company), 'Second company set as client for website project');

      $second_user = new Administrator();
      $second_user->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => $second_company->getId(),
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded(), 'Second user created');
      $this->assertTrue($second_user->getCompany()->is($second_company), 'Company set for second user');

      $this->application_project->users()->add($this->logged_user);
      $this->application_project->users()->add($second_user);

      $this->assertTrue($this->application_project->users()->isMember($this->logged_user, false));
      $this->assertTrue($this->application_project->users()->isMember($second_user, false));

      $this->website_project->users()->add($this->logged_user);
      $this->website_project->users()->add($second_user, null, array(
        'task' => ProjectRole::PERMISSION_MANAGE,
      ));

      $this->assertTrue($this->website_project->users()->isMember($this->logged_user, false), 'First user is a member of a project');
      $this->assertTrue($this->website_project->users()->isMember($second_user, false), 'Second user is a member of a project');

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task2 = new Task();
      $task2->setName('Task 2');
      $task2->setProject($this->website_project);
      $task2->setCreatedBy($second_user);
      $task2->setState(STATE_VISIBLE);
      $task2->save();

      $task3 = new Task();
      $task3->setName('Task 3');
      $task3->setProject($this->application_project);
      $task3->setCreatedBy($second_user);
      $task3->setState(STATE_VISIBLE);
      $task3->save();

      $task4 = new Task();
      $task4->setName('Task 4');
      $task4->setProject($this->website_project);
      $task4->setCreatedBy($second_user);
      $task4->setState(STATE_VISIBLE);
      $task4->save();

      $this->assertTrue($task1->getCreatedBy()->is($this->logged_user), 'Task 1 created by logged user');
      $this->assertTrue($task2->getCreatedBy()->is($second_user), 'Task 2 created by second user');
      $this->assertTrue($task3->getCreatedBy()->is($second_user), 'Task 3 created by second user');
      $this->assertTrue($task4->getCreatedBy()->is($second_user), 'Task 4 created by second user');

      $filter = new AssignmentFilter();
      $filter->filterByProjectClient($second_company);

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results) && isset($results['all']) && $results['all']['assignments'], 'Valid result');
      $this->assertEqual(count($results['all']['assignments']), 2, 'Two tasks in website project');

      $this->assertTrue(isset($results['all']['assignments'][$task2->getId()]), 'Task 2 is in the result');
      $this->assertTrue(isset($results['all']['assignments'][$task4->getId()]), 'Task 4 is in the result');
    } // testProjectClientFilter

    /**
     * Test assigned to filter
     */
    function testAssignedTo() {
      $second_user = new Administrator();
      $second_user->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded());

      $this->application_project->users()->add($this->logged_user);
      $this->application_project->users()->add($second_user);

      $this->assertTrue($this->application_project->users()->isMember($this->logged_user, false));
      $this->assertTrue($this->application_project->users()->isMember($second_user, false));

      $this->website_project->users()->add($this->logged_user);
      $this->website_project->users()->add($second_user, null, array(
        'task' => ProjectRole::PERMISSION_MANAGE,
      ));

      $this->assertTrue($this->website_project->users()->isMember($this->logged_user, false));
      $this->assertTrue($this->website_project->users()->isMember($second_user, false));

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task2 = new Task();
      $task2->setName('Task 2');
      $task2->setProject($this->website_project);
      $task2->setCreatedBy($this->logged_user);
      $task2->setState(STATE_VISIBLE);
      $task2->save();

      $task3 = new Task();
      $task3->setName('Task 3');
      $task3->setProject($this->application_project);
      $task3->setCreatedBy($this->logged_user);
      $task3->setState(STATE_VISIBLE);
      $task3->save();

      // ---------------------------------------------------
      //  Not a single task
      // ---------------------------------------------------

      $filter = new AssignmentFilter();
      $filter->filterByUsers(array($this->logged_user->getId()));

      $results = $filter->run($this->logged_user);
      $this->assertNull($results);

      // ---------------------------------------------------
      //  Now, we have assignees
      // ---------------------------------------------------

      $task3->assignees()->setAssignee($this->logged_user, $this->logged_user);
      $task3->assignees()->setOtherAssignees(array($second_user));

      $this->assertTrue($task3->assignees()->isAssignee($this->logged_user));
      $this->assertTrue($task3->assignees()->isAssignee($second_user));

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results));
      $this->assertTrue(isset($results['all']));
      $this->assertEqual(count($results['all']['assignments']), 1);

      // ---------------------------------------------------
      //  Via Other Assignee
      // ---------------------------------------------------

      $filter->filterByUsers(array($second_user->getId()), true);
      $this->assertEqual($filter->getUserFilter(), AssignmentFilter::USER_FILTER_SELECTED_RESPONSIBLE);

      $results = $filter->run($this->logged_user);

      $this->assertNull($results);

      $filter->filterByUsers(array($second_user->getId()));
      $this->assertEqual($filter->getUserFilter(), AssignmentFilter::USER_FILTER_SELECTED);

      $results = $filter->run($this->logged_user);

      $this->assertTrue(is_array($results));
      $this->assertTrue(isset($results['all']));
      $this->assertEqual(count($results['all']['assignments']), 1);
    } // testAssignedTo

    /**
     * Test bug #621 of activeCollab project
     *
     * http://afiveone.activecollab.net/projects/activecollab/tasks/621
     */
    function testJoinWithAssignments() {
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'assignments'), 0);

      $project_manager = new Manager();
      $project_manager->setSystemPermission('can_manage_projects', true);
      $project_manager->setAttributes(array(
        'email' => 'project-manager@test.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $project_manager->setState(STATE_VISIBLE);
      $project_manager->save();

      $this->assertTrue($project_manager->isLoaded(), 'User saved');
      $this->assertTrue($project_manager->isProjectManager(), 'User is project manager');

      $this->application_project->users()->add($this->logged_user);
      $this->application_project->users()->add($project_manager);

      $this->assertTrue($this->application_project->users()->isMember($this->logged_user, false), 'Administrator added to the project');
      $this->assertTrue($this->application_project->users()->isMember($project_manager, false), 'Project manager added to the project');

      $this->assertNull($this->application_project->getUserAssignments($this->logged_user), 'No assignments yet');

      $task = new Task();
      $task->setName('Task 1');
      $task->setProject($this->application_project);
      $task->setCreatedBy($this->logged_user);
      $task->setState(STATE_VISIBLE);
      $task->assignees()->setAssignee($this->logged_user, $this->logged_user, false);
      $task->save();

      $this->assertTrue($task->isLoaded(), 'Task has been created');
      $this->assertEqual($task->getAssigneeId(), $this->logged_user->getId(), 'Proper user is assigned');

      $assignments = $this->application_project->getUserAssignments($this->logged_user);

      $this->assertTrue(is_array($assignments));
      $this->assertTrue(isset($assignments['not-set']));
      $this->assertEqual(count($assignments['not-set']['assignments']), 1);

      $task->assignees()->setOtherAssignees(array(
        $project_manager->getId()
      ));

      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id = ? AND user_id = ?', 'Task', $task->getId(), $project_manager->getId()), 1, 'Now we have project manager assigned to the task');

      $assignments = $this->application_project->getUserAssignments($this->logged_user);

      $this->assertTrue(is_array($assignments));
      $this->assertTrue(isset($assignments['not-set']));
      $this->assertEqual(count($assignments['not-set']['assignments']), 1);
    } // testJoinWithAssignments

    /**
     * Test unassigned filter
     */
    function testUnassigned() {
      $second_user = new Manager();
      $second_user->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded(), 'Second user created');

      $this->application_project->users()->add($this->logged_user);
      $this->website_project->users()->add($this->logged_user);

      $this->application_project->users()->add($second_user, null, array(
        'task' => ProjectRole::PERMISSION_ACCESS,
      ));

      $this->website_project->users()->add($second_user, null, array(
        'task' => ProjectRole::PERMISSION_ACCESS,
      ));

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setAssigneeId(0);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task2 = new Task();
      $task2->setName('Task 2');
      $task2->setProject($this->website_project);
      $task2->setCreatedBy($this->logged_user);
      $task2->setAssigneeId(0);
      $task2->setState(STATE_VISIBLE);
      $task2->save();

      $task3 = new Task();
      $task3->setName('Task 3');
      $task3->setProject($this->application_project);
      $task3->setCreatedBy($this->logged_user);
      $task3->setState(STATE_VISIBLE);
      $task3->assignees()->setAssignee($second_user);
      $task3->save();

      // ---------------------------------------------------
      //  By user
      // ---------------------------------------------------

      $filter = new AssignmentFilter();
      $filter->setIncludeSubtasks(true);
      $filter->setIncludeAllProjects(true);
      $filter->filterByUsers(array($this->logged_user->getId()));

      $results = $filter->run($this->logged_user);
      $this->assertNull($results);

      // ---------------------------------------------------
      //  All unassigned
      // ---------------------------------------------------

      $filter->setUserFilter(AssignmentFilter::USER_FILTER_NOT_ASSIGNED);

      $results = $filter->run($second_user);

      $this->assertTrue(is_array($results) && isset($results['all']), 'Filter returned valid result');
      $this->assertEqual(count($results['all']['assignments']), 2, 'Filter found 2 assignments that fit the criteria');

      // ---------------------------------------------------
      //  Unassigned from a project
      // ---------------------------------------------------

      $filter->filterByProjects(array($this->website_project->getId()));
      $filter->setUserFilter(AssignmentFilter::USER_FILTER_NOT_ASSIGNED);

      $results = $filter->run($second_user);

      $this->assertTrue(is_array($results) && isset($results['all']), 'Valid filter result');
      $this->assertEqual(count($results['all']['assignments']), 1, 'Filter found 1 assignment that matches the criteria');

      $filter->filterByProjects(array($this->application_project->getId()));

      $results = $filter->run($second_user);

      $this->assertTrue(is_array($results) && isset($results['all']), 'Valid filter result');
      $this->assertEqual(count($results['all']['assignments']), 1, 'Filter found 1 assignment that matches the criteria');
    } // testUnassigned

    /**
     * Test date filters
     */
    function testDateFilters() {
      $date_5_days_ago = DateTimeValue::makeFromString('-5 days');
      $date_15_days_ago = DateTimeValue::makeFromString('-15 days');

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setCreatedOn($date_5_days_ago);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task1 = DataObjectPool::get('Task', $task1->getId(), true);

      if($task1 instanceof Task) {
        $this->assertTrue($task1->getCreatedOn()->isSameDay($date_5_days_ago));
      } else {
        $this->fail('Task #1 is not properly saved');
      } // if

      $task2 = new Task();
      $task2->setName('Task 2');
      $task2->setProject($this->website_project);
      $task2->setCreatedBy($this->logged_user);
      $task2->setCreatedOn($date_15_days_ago);
      $task2->setState(STATE_VISIBLE);
      $task2->save();

      $task2 = DataObjectPool::get('Task', $task2->getId(), true);

      if($task2 instanceof Task) {
        $this->assertTrue($task2->getCreatedOn()->isSameDay($date_15_days_ago));
      } else {
        $this->fail('Task #2 is not properly saved');
      } // if

      $filter = new AssignmentFilter();
      $filter->setIncludeAllProjects(true);
      $filter->createdOnDate($date_15_days_ago);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'Only one assignment returned');

      $filter = new AssignmentFilter();
      $filter->setIncludeAllProjects(true);
      $filter->createdInRange($date_15_days_ago, DateTimeValue::now());

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 2, 'Two assignments returned');

      $task1->complete()->complete($this->logged_user);

      $filter = new AssignmentFilter();
      $filter->setIncludeAllProjects(true);
      $filter->setCompletedOnFilter(DataFilter::DATE_FILTER_TODAY);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'Only one assignments returned');
    } // testDateFilters

    /**
     * Test due date not set filter
     */
    function testDueDateNotSet() {
      $second_user = new Manager();
      $second_user->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded(), 'Second user created');

      $this->application_project->users()->add($this->logged_user);
      $this->website_project->users()->add($this->logged_user);

      $this->application_project->users()->add($second_user, null, array(
        'task' => ProjectRole::PERMISSION_ACCESS,
      ));

      $this->website_project->users()->add($second_user, null, array(
        'task' => ProjectRole::PERMISSION_ACCESS,
      ));

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task2 = new Task();
      $task2->setName('Task 2');
      $task2->setProject($this->website_project);
      $task2->setCreatedBy($this->logged_user);
      $task2->setState(STATE_VISIBLE);
      $task2->save();

      $task3 = new Task();
      $task3->setName('Task 3');
      $task3->setProject($this->application_project);
      $task3->setCreatedBy($this->logged_user);
      $task3->setState(STATE_VISIBLE);
      $task3->setDueOn(DateValue::now());
      $task3->save();

      $filter = new AssignmentFilter();
      $filter->setDueOnFilter(AssignmentFilter::DATE_FILTER_IS_NOT_SET);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 2, 'Two assignments returned');
    } // testDueDateNotSet

    /**
     * Test completed before and after date
     */
    function testCompletedBeforeAndAfter() {
      $this->application_project->users()->add($this->logged_user);

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task1->complete()->complete($this->logged_user);

      $today = new DateValue();
      $tomorrow = DateValue::makeFromString('+1 day');
      $yesterday = DateValue::makeFromString('-1 day');

      $filter = new AssignmentFilter();
      $filter->completedOnDate($today);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'One assignments returned');

      $filter = new AssignmentFilter();
      $filter->completedBeforeDate($tomorrow);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'One assignments returned');

      $filter = new AssignmentFilter();
      $filter->completedAfterDate($yesterday);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'One assignments returned');
    } // testCompletedBeforeAndAfter

    /**
     * Test completed on that is user time zone sensitive
     */
    function testCompletedOnWithDifferentTimeZone() {
      $this->application_project->users()->add($this->logged_user);

      $created_on = DateTimeValue::makeFromString('2013-12-29 23:10:00');
      $completed_on = DateTimeValue::makeFromString('2013-12-29 23:15:00');

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setDueOn(DateValue::makeFromString('2013-12-31'));
      $task1->setCreatedOn($created_on);
      $task1->setCreatedBy($this->logged_user);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task1->complete()->complete($this->logged_user);
      $task1->setCompletedOn($completed_on);
      $task1->save();

      $task1 = Tasks::findById($task1->getId());
      if($task1 instanceof Task) {
        $this->assertEqual($task1->getCreatedOn()->toMySQL(), $created_on->toMySQL());
        $this->assertEqual($task1->getCompletedOn()->toMySQL(), $completed_on->toMySQL());
      } else {
        $this->fail('Task not properly reloaded');
      } // if

      // ---------------------------------------------------
      //  Run with GMT 0
      // ---------------------------------------------------

      $filter = new AssignmentFilter();
      $filter->completedOnDate(DateValue::makeFromString('2013-12-29'));

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'One assignments returned');

      ConfigOptions::setValueFor(array(
        'time_timezone' => 3600, // GMT+1
        'time_dst' => true,
      ), $this->logged_user);

      $this->assertEqual(get_user_gmt_offset($this->logged_user, true), 7200);
      $this->assertEqual($completed_on->formatForUser($this->logged_user), 'Dec 30. 2013 01:15 AM');

      // Nothing the day before?
      $filter = new AssignmentFilter();
      $filter->completedOnDate(DateValue::makeFromString('2013-12-29'));

      $result = $filter->run($this->logged_user);

      $this->assertNull($result);

      // Created before 30th (should miss 29th)
      $filter = new AssignmentFilter();
      $filter->createdBeforeDate(DateValue::makeFromString('2013-12-30'));

      $result = $filter->run($this->logged_user);

      $this->assertNull($result);

      // Created between 20 and 29 (should miss 29th)
      $filter = new AssignmentFilter();
      $filter->createdInRange(DateValue::makeFromString('2013-12-20'), DateValue::makeFromString('2013-12-29'));

      $result = $filter->run($this->logged_user);

      $this->assertNull($result);

      // Found for the next day, due to time difference
      $filter = new AssignmentFilter();
      $filter->completedOnDate(DateValue::makeFromString('2013-12-30'));

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'One assignments returned');

      // Created before 31st
      $filter = new AssignmentFilter();
      $filter->createdBeforeDate(DateValue::makeFromString('2013-12-31'));

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'One assignments returned');

      // Created on 30th or 31st
      $filter = new AssignmentFilter();
      $filter->createdInRange(DateValue::makeFromString('2013-12-30'), DateValue::makeFromString('2013-12-31'));

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'One assignments returned');
    } // testCompletedOnWithDifferentTimeZone

    /**
     * Test time zone aware today and yesterday filter
     */
    function testTimeZoneAwareTodayAndYesterdayFilter() {
      $this->application_project->users()->add($this->logged_user);

      $yesterday = DateTimeValue::makeFromString(DateValue::makeFromString('-2 days')->toMySQL() . ' 23:10:00'); // Will become yesterday when we alter time zone
      $today = DateTimeValue::makeFromString(DateValue::makeFromString('-1 day')->toMySQL() . ' 23:15:00'); // Will become today when we alter time zone

      $this->assertTrue($today->isYesterday(0));

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setDueOn(DateValue::makeFromString('2013-12-31'));
      $task1->setCreatedOn($yesterday);
      $task1->setCreatedBy($this->logged_user);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task1->complete()->complete($this->logged_user);
      $task1->setCompletedOn($today);
      $task1->save();

      $task1 = Tasks::findById($task1->getId());
      if($task1 instanceof Task) {
        $this->assertEqual($task1->getCreatedOn()->toMySQL(), $yesterday->toMySQL());
        $this->assertEqual($task1->getCompletedOn()->toMySQL(), $today->toMySQL());
      } else {
        $this->fail('Task not properly reloaded');
      } // if

      ConfigOptions::setValueFor(array(
        'time_timezone' => 3600, // GMT+1
        'time_dst' => true,
      ), $this->logged_user);

      $this->assertEqual(get_user_gmt_offset($this->logged_user, true), 7200);

      // Created yesteday
      $filter = new AssignmentFilter();
      $filter->setCreatedOnFilter(DataFilter::DATE_FILTER_YESTERDAY);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'One assignments returned');

      // Completed today
      $filter = new AssignmentFilter();
      $filter->setCompletedOnFilter(DataFilter::DATE_FILTER_TODAY);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'One assignments returned');
    } // testTimeZoneAwareTodayAndYesterdayFilter

    /**
     * Test age filter
     */
    function testAgeFilter() {
      $date_5_days_ago = DateTimeValue::makeFromString('-5 days');
      $date_50_days_ago = DateTimeValue::makeFromString('-50 days');

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setCreatedOn($date_5_days_ago);
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $task2 = new Task();
      $task2->setName('Task 2');
      $task2->setProject($this->website_project);
      $task2->setCreatedBy($this->logged_user);
      $task2->setCreatedOn($date_50_days_ago);
      $task2->setState(STATE_VISIBLE);
      $task2->save();

      $task1_age = (integer) DB::executeFirstCell('SELECT DATEDIFF(UTC_TIMESTAMP(), created_on) FROM ' . TABLE_PREFIX . 'project_objects WHERE id = ?', $task1->getId());
      $task2_age = (integer) DB::executeFirstCell('SELECT DATEDIFF(UTC_TIMESTAMP(), created_on) FROM ' . TABLE_PREFIX . 'project_objects WHERE id = ?', $task2->getId());

      $this->assertEqual($task1_age, 5);
      $this->assertEqual($task2_age, 50);

      $filter = new AssignmentFilter();
      $filter->setIncludeAllProjects(true);
      $filter->createdAge(5);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'Only one assignment should have age 5');
      $this->assertTrue(is_array($result['all']['assignments'][$task1->getId()]));
      $this->assertEqual($result['all']['assignments'][$task1->getId()]['age'], 5);

      $filter = new AssignmentFilter();
      $filter->setIncludeAllProjects(true);
      $filter->createdAge(4, DataFilter::DATE_FILTER_AGE_IS_MORE_THAN);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 2, 'Two assignments should have age larger than 4 days');
      $this->assertTrue(is_array($result['all']['assignments'][$task1->getId()]));
      $this->assertTrue(is_array($result['all']['assignments'][$task2->getId()]));
      $this->assertEqual($result['all']['assignments'][$task2->getId()]['age'], 50);

      $filter = new AssignmentFilter();
      $filter->setIncludeAllProjects(true);
      $filter->createdAge(50, DataFilter::DATE_FILTER_AGE_IS_LESS_THAN);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'Only one assignment should have age that is less than 50 days');
      $this->assertTrue(is_array($result['all']['assignments'][$task1->getId()]));
    } // testAgeFilter

    /**
     * Test querying other assignees
     */
    function testOtherAssignees() {
      $second_user = new Administrator();
      $second_user->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded());

      $task1 = new Task();
      $task1->setName('Task 1');
      $task1->setProject($this->application_project);
      $task1->setCreatedBy($this->logged_user);
      $task1->setAssigneeId($this->logged_user->getId());
      $task1->setState(STATE_VISIBLE);
      $task1->save();

      $this->assertTrue($task1->isLoaded());

      $task1->assignees()->setOtherAssignees(array(
        $second_user->getId(),
      ));

      $this->assertEqual((integer) DB::executeFirstColumn("SELECT COUNT(*) FROM " . TABLE_PREFIX . 'assignments WHERE parent_type = ? AND parent_id = ?', get_class($task1), $task1->getId()), 1);

      $filter = new AssignmentFilter();
      $filter->setIncludeAllProjects(true);
      $filter->setIncludeOtherAssignees(true);

      $result = $filter->run($this->logged_user);

      $this->assertTrue(is_array($result) && is_array($result['all']['assignments']), 'Valid filter result');
      $this->assertEqual(count($result['all']['assignments']), 1, 'We have only one assignment');
      $this->assertEqual($result['all']['assignments'][$task1->getId()]['other_assignees'], array($second_user->getId()));
    } // testOtherAssignees
    
  }
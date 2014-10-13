<?php

  /**
   * Test system modification log
   *
   * @package activeCollab.modules.system
   * @subpackage tests
   */
  class TestSystemModificationLog extends AngieModelTestCase {

    /**
     * Test company fields
     */
    function testCompanyFields() {
      $company = new Company();
      
      $this->assertIsA($company, 'Company', 'Company instance');
      $this->assertIsA($company->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($company->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $company->history()->getTrackedFields();
      
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('state', $fields));
    } // testCompanyFields

    /**
     * Test user fields
     */
    function testUserFields() {
      $user = Users::getUserInstance();
      
      $this->assertIsA($user, 'User', 'User instance');
      $this->assertIsA($user->history(), 'IHistoryImplementation', 'Valid history helper instance');
      
      $fields = $user->history()->getTrackedFields();
      
      $this->assertTrue(in_array('state', $fields), 'state field found');
      $this->assertTrue(in_array('first_name', $fields), 'first_name field found');
      $this->assertTrue(in_array('last_name', $fields), 'last_name field found');
      $this->assertTrue(in_array('email', $fields), 'email field found');
      $this->assertTrue(in_array('password', $fields), 'password field found');
      $this->assertTrue(in_array('company_id', $fields), 'company_id field found');
    } // testUserFields

    /**
     * Test project fields
     */
    function testProjectFields() {
      $project = new Project();
      
      $this->assertIsA($project, 'Project', 'Project instance');
      $this->assertIsA($project->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($project->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $project->history()->getTrackedFields();
      
      $this->assertTrue(in_array('slug', $fields));
      $this->assertTrue(in_array('company_id', $fields));
      $this->assertTrue(in_array('currency_id', $fields));
      $this->assertTrue(in_array('budget', $fields));
      $this->assertTrue(in_array('leader_id', $fields));
      $this->assertTrue(in_array('overview', $fields));
    } // testProjectFields

    /**
     * test project request fields
     */
    function testProjectRequestFields() {
      $project_request = new ProjectRequest();
      
      $this->assertIsA($project_request, 'ProjectRequest', 'Project request instance');
      $this->assertIsA($project_request->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($project_request->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $project_request->history()->getTrackedFields();
      
      $this->assertTrue(in_array('status', $fields));
      $this->assertTrue(in_array('created_by_company_name', $fields));
      $this->assertTrue(in_array('created_by_company_address', $fields));
      $this->assertTrue(in_array('custom_field_1', $fields));
      $this->assertTrue(in_array('custom_field_2', $fields));
      $this->assertTrue(in_array('custom_field_3', $fields));
      $this->assertTrue(in_array('custom_field_4', $fields));
      $this->assertTrue(in_array('custom_field_5', $fields));
      $this->assertTrue(in_array('is_locked', $fields));
      $this->assertTrue(in_array('taken_by_id', $fields));
      $this->assertTrue(in_array('name', $fields));
    } // testProjectRequestFields

    /**
     * Test milestone fields
     */
    function testMilestoneFields() {
      $milestone = new Milestone();
      
      $this->assertIsA($milestone, 'Milestone', 'Milestone instance');
      $this->assertIsA($milestone->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($milestone->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $milestone->history()->getTrackedFields();
      
      $this->assertTrue(in_array('project_id', $fields));
      $this->assertTrue(!in_array('milestone_id', $fields), 'milestone_id is not tracked for milestones');
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('body', $fields));
      $this->assertTrue(in_array('date_field_1', $fields));
      $this->assertTrue(in_array('state', $fields));
      $this->assertTrue(in_array('visibility', $fields));
      $this->assertTrue(in_array('due_on', $fields));
      $this->assertTrue(in_array('assignee_id', $fields));
      $this->assertTrue(in_array('priority', $fields));
    } // testMilestoneFields
    
  }
<?php

  /**
   * Test job type
   * 
   * @package activeCollab.modules.tracking
   * @subpackage tests
   */
  class TestJobTypes extends AngieModelTestCase {

    /**
     * Test default job type
     */
    function testDefaultJobType() {
      $job_type = JobTypes::findById(1);
      
      $this->assertTrue($job_type->isLoaded(), 'One job type is defined');
      $this->assertEqual($job_type->getName(), 'General', 'Name of the default job type is general');
    } // testDefaultJobType

    /**
     * Test delete permissions
     */
    function testCanDelete() {
      $user = Users::findById(1);

      if($user instanceof User) {
        $this->assertTrue($user->isLoaded(), 'Administrator exists');
        $this->assertTrue($user->isAdministrator(), 'Administrator has proper permissions');
      } else {
        $this->fail('User not loaded');
      } // if
      
      $default_job_type = JobTypes::findById(1);
      $this->assertTrue($default_job_type->isLoaded(), 'Job type is loaded');
      
      $this->assertFalse($default_job_type->canDelete($user));
      
      $second_job_type = new JobType();
      $second_job_type->setAttributes(array(
        'name' => 'Second Job Type', 
        'default_hourly_rate' => 100,
      ));
      $second_job_type->setIsActive(true);
      $second_job_type->save();
      
      $this->assertTrue($second_job_type->isLoaded());
      $this->assertTrue($second_job_type->canDelete($user));
      
      $project = new Project();
      $project->setAttributes(array(
        'name' => 'Testing Job Type Can Delete', 
        'company_id' => 1, 
        'leader_id' => 1, 
      ));
      $project->setCreatedBy($user);
      
      $project->save();
      
      $this->assertTrue($project->isLoaded(), 'Make sure that our test project is created');
      
      $task = new Task();
      $task->setAttributes(array(
        'name' => 'Test task', 
        'project_id' => $project->getId(), 
      ));
      $task->setCreatedBy($user);
      
      $task->save();
      $this->assertTrue($task->isLoaded(), 'Make sure that our test task is created');
      
      // ---------------------------------------------------
      //  Check Job Type Reserved by Estimate
      // ---------------------------------------------------
      
      $estimate = $task->tracking()->setEstimate(125, $second_job_type, null, $user, false, true);

      $this->assertIsA($estimate, 'Estimate', 'We have a valid result from setEstimate() method');
      $this->assertTrue($estimate->isLoaded(), 'Estimate is saved to the database');

      $this->assertEqual($estimate->getValue(), 125);
      $this->assertEqual($estimate->getJobTypeId(), $second_job_type->getId());
      
      $this->assertFalse($second_job_type->canDelete($user), "Can't delete because there's an estimate that uses this job type");
      
      $estimate->forceDelete();
      $this->assertTrue($second_job_type->canDelete($user), "No longer in use by the estimate");
      
      // ---------------------------------------------------
      //  Check Job Type Reserved by Time Record
      // ---------------------------------------------------
      
      $time_record = new TimeRecord();
      $time_record->setUser($user);
      $time_record->setCreatedBy($user);
      $time_record->setParent($project);
      $time_record->setJobType($second_job_type);
      $time_record->setRecordDate(DateValue::now());
      $time_record->setValue(10);
      $time_record->save();
      
      $this->assertTrue($time_record->isLoaded(), 'Time record is saved to the database');
      
      $this->assertFalse($second_job_type->canDelete($user), "Can't delete because there's a time record that uses this job type");
      
      $time_record->forceDelete();
      $this->assertTrue($second_job_type->canDelete($user), "No longer in use by the time record");
    } // testCanDelete
    
  }
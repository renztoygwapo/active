<?php

  /**
   * Test project object search
   *
   * @package activeCollab.modules.system
   * @subpackage tests
   */
  class TestProjectObjectSearch extends AngieModelTestCase {

    /**
     * Logged user
     *
     * @var User
     */
    private $logged_user;

    /**
     * Active project
     *
     * @var Project
     */
    private $project;

    /**
     * Active milestone
     *
     * @var Milestone
     */
    private $milestone;

    /**
     * Active task
     *
     * @var Task
     */
    private $task;

    /**
     * First subtask
     *
     * @var Subtask
     */
    private $first_subtask;

    /**
     * Second subtask
     *
     * @var Subtask
     */
    private $second_subtask;

    /**
     * First comment
     *
     * @var Comment
     */
    private $first_comment;

    /**
     * Second comment
     *
     * @var Comment
     */
    private $second_comment;

    /**
     * Project objects search index
     *
     * @var SearchIndex
     */
    private $project_objects_index;

    /**
     * Set up test environment
     */
    function setUp() {
      parent::setUp();

      Search::initialize(true);

      $this->project_objects_index = Search::getIndex('project_objects');

      $this->logged_user = Users::findById(1);

      $this->project = new Project();
      $this->project->setAttributes(array(
        'name' => 'Test Project',
        'leader_id' => 1,
        'company_id' => 1,
      ));
      $this->project->save();

      $this->milestone = new Milestone();
      $this->milestone->setAttributes(array(
        'name' => 'Test Subject',
      ));
      $this->milestone->setProject($this->project);
      $this->milestone->setCreatedBy($this->logged_user);
      $this->milestone->setState(STATE_VISIBLE);
      $this->milestone->save();

      $this->task = new Task();
      $this->task->setName('Test task');
      $this->task->setProject($this->project);
      $this->task->setMilestone($this->milestone);
      $this->task->setCreatedBy($this->logged_user);
      $this->task->setState(STATE_VISIBLE);
      $this->task->setVisibility(VISIBILITY_NORMAL);
      $this->task->save();

      $this->first_subtask = $this->task->subtasks()->newSubtask();

      $this->first_subtask->setAttributes(array(
        'body' => 'Who should take care of this subtask? Goran, of course',
      ));
      $this->first_subtask->setCreatedBy($this->logged_user);
      $this->first_subtask->setState(STATE_VISIBLE);
      $this->first_subtask->save();

      $this->second_subtask = $this->task->subtasks()->newSubtask();

      $this->second_subtask->setAttributes(array(
        'body' => 'And this one? Oliver should take care of it',
      ));
      $this->second_subtask->setCreatedBy($this->logged_user);
      $this->second_subtask->setState(STATE_VISIBLE);
      $this->second_subtask->save();

      $this->first_comment = $this->task->comments()->newComment();
      $this->first_comment->setBody('Yellowstone National Park is a national park located primarily in the U.S. state of Wyoming');
      $this->first_comment->setState(STATE_VISIBLE);
      $this->first_comment->save();

      $this->second_comment = $this->task->comments()->newComment();
      $this->second_comment->setBody('Tara is a mountain located in western Serbia');
      $this->second_comment->setState(STATE_VISIBLE);
      $this->second_comment->save();
    } // setUp

    /**
     * Tear down
     */
    function tearDown() {
      $this->logged_user = null;
      $this->project = null;
      $this->milestone = null;
      $this->task = null;

      parent::tearDown();
    } // tearDown

    /**
     * Test if everything is properly initalised
     */
    function testInitialization() {
      $this->assertIsA($this->project_objects_index, 'SearchIndex');
      
      $this->assertTrue($this->logged_user->isLoaded(), 'Logged user loaded');
      $this->assertTrue($this->project->isLoaded(), 'Active project is created');

      $this->assertTrue($this->milestone->isLoaded(), 'Test milestone is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->milestone), ApplicationObjects::getContext($this->project) . '/milestones/' . $this->milestone->getId(), 'Test milestone context is OK');

      $this->assertTrue($this->task->isLoaded(), 'Test task is created');
      $this->assertEqual(ApplicationObjects::getRememberedContext($this->task), ApplicationObjects::getContext($this->project) . '/tasks/normal/' . $this->task->getId(), 'Test task context is OK');
      $this->assertEqual($this->task->getMilestoneId(), $this->milestone->getId(), 'Task milestone is set');

      $this->assertEqual($this->task->subtasks()->count($this->logged_user, false), 2);
      $this->assertIsA($this->task->subtasks()->get($this->logged_user)->getRowAt(0), 'Subtask');

      $this->assertEqual($this->task->comments()->count($this->logged_user, false), 2);
      $this->assertIsA($this->task->comments()->get($this->logged_user)->getRowAt(0), 'Comment');
    } // testInitialization

    /**
     * Test if we have proper index that is created by the model
     */
    function testIndexCreatedByModel() {
      $task_index_record = DB::executeFirstRow('SELECT * FROM ' . TABLE_PREFIX . "search_index_for_project_objects WHERE item_type = 'Task' AND item_id = ?", $this->task->getId());

      $this->assertTrue(is_array($task_index_record));
      $this->assertEqual($this->first_subtask->getBody() . ' ' . $this->second_subtask->getBody(), $task_index_record['subtasks']);
      $this->assertEqual($this->first_comment->getBody() . ' ' . $this->second_comment->getBody(), $task_index_record['comments']);
    } // testIndexCreatedByModel

    /**
     * Test rebuild index
     */
    function testRebuildIndex() {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'search_index_for_project_objects');

      ProjectObjects::rebuildProjectSearchIndex($this->project, $this->project_objects_index);

      $task_index_record = DB::executeFirstRow('SELECT * FROM ' . TABLE_PREFIX . "search_index_for_project_objects WHERE item_type = 'Task' AND item_id = ?", $this->task->getId());

      $this->assertTrue(is_array($task_index_record));
      $this->assertEqual($this->first_subtask->getBody() . ' ' . $this->second_subtask->getBody(), $task_index_record['subtasks']);
      $this->assertEqual($this->first_comment->getBody() . ' ' . $this->second_comment->getBody(), $task_index_record['comments']);
    } // testRebuildIndex

    /**
     * Test search for subtask
     */
    function testSearchForSubtask() {
      $result = Search::query($this->logged_user, $this->project_objects_index, 'Goran');

      $this->assertTrue(is_array($result) && count($result) == 1);
      $this->assertEqual($result[0]['type'], 'Task');
      $this->assertEqual($result[0]['id'], $this->task->getId());
    } // testSearchForSubtask

    /**
     * Test search for subtask
     */
    function testSearchForComment() {
      $result = Search::query($this->logged_user, $this->project_objects_index, 'Yellowstone');

      $this->assertTrue(is_array($result) && count($result) == 1);
      $this->assertEqual($result[0]['type'], 'Task');
      $this->assertEqual($result[0]['id'], $this->task->getId());
    } // testSearchForComment

    function testNotebookPageIndex() {
      $this->assertTrue(AngieApplication::isModuleLoaded('notebooks'));

      $notebook = ProjectObjects::create('Notebook', array(
        'name' => 'Test notebook',
        'project_id' => $this->project->getId(),
      ));
      $notebook->setState(STATE_VISIBLE);
      $notebook->save();

      $notebook_page = NotebookPages::create(array(
        'parent_type' => 'Notebook',
        'parent_id' => $notebook->getId(),
        'name' => 'Test notebook page',
        'body' => 'Test notebook page body',
      ));
      $notebook_page->setState(STATE_VISIBLE);
      $notebook_page->save();

      $comment = $notebook_page->comments()->newComment();
      $comment->setBody('Misar is a town in the municipality of Sabac, Serbia. According to the 2002 census, the town has a population of 2217 people');
      $comment->setState(STATE_VISIBLE);
      $comment->save();

      // Test if we have the info in the index
      $notebook_page_index_record = DB::executeFirstRow('SELECT * FROM ' . TABLE_PREFIX . "search_index_for_project_objects WHERE item_type = 'NotebookPage' AND item_id = ?", $notebook_page->getId());

      $this->assertTrue(is_array($notebook_page_index_record));
      $this->assertEqual($comment->getBody(), $notebook_page_index_record['comments']);

      // Search
      $result = Search::query($this->logged_user, $this->project_objects_index, 'municipality');

      $this->assertTrue(is_array($result) && count($result) == 1);
      $this->assertEqual($result[0]['type'], 'NotebookPage');
      $this->assertEqual($result[0]['id'], $notebook_page->getId());

      // Rebuild
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'search_index_for_project_objects');
      ProjectObjects::rebuildProjectSearchIndex($this->project, $this->project_objects_index);

      $notebook_page_index_record_updated = DB::executeFirstRow('SELECT * FROM ' . TABLE_PREFIX . "search_index_for_project_objects WHERE item_type = 'NotebookPage' AND item_id = ?", $notebook_page->getId());

      $this->assertTrue(is_array($notebook_page_index_record_updated));
      $this->assertEqual($notebook_page_index_record, $notebook_page_index_record_updated);

      // Search
      $result = Search::query($this->logged_user, $this->project_objects_index, 'municipality');

      $this->assertTrue(is_array($result) && count($result) == 1);
      $this->assertEqual($result[0]['type'], 'NotebookPage');
      $this->assertEqual($result[0]['id'], $notebook_page->getId());
    } // testNotebookPageIndex

  }
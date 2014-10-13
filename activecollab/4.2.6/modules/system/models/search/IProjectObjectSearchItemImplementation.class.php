<?php

  /**
   * Project object search item implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectObjectSearchItemImplementation extends ISearchItemImplementation {
  
    /**
     * Return list of indices that index parent object
     * 
     * Result is an array where key is the index name, while value is list of 
     * fields that's watched for changes
     * 
     * @return array
     */
    function getIndices() {
      $project_object_index_fields = array('project_id', 'project', 'milestone_id', 'visibility', 'name', 'body');
      
      if($this->object instanceof ICategory) {
        $project_object_index_fields[] = 'category_id';
      } // if
      
      if($this->object instanceof IComplete) {
        $project_object_index_fields[] = 'priority';
        $project_object_index_fields[] = 'due_on';
        $project_object_index_fields[] = 'completed_on';
      } // if
      
      if($this->object instanceof IAssignees) {
        $project_object_index_fields[] = 'assignee_id';
      } // if
      
      return array(
        'project_objects' => $project_object_index_fields, 
        'names' => array('name', 'body'),
      );
    } // getIndices
    
    /**
     * Return additional properties for a given index
     * 
     * @param SearchIndex $index
     * @return mixed
     * @throws InvalidInstanceError
     */
    function getAdditional(SearchIndex $index) {
      
      // Additional fields for primary projects index
      if($index instanceof ProjectObjectsSearchIndex) {
        $result = array(
          'project_id' => $this->object->getProjectId(), 
          'project' => $this->object->getProject() instanceof Project ? $this->object->getProject()->getName() : null, 
          'milestone_id' => $this->object->getMilestoneId(), 
          'milestone' => $this->object->getMilestone() instanceof Milestone ? $this->object->getMilestone()->getName() : null, 
          'visibility' => $this->object->getVisibility(), 
          'name' => $this->object->getName() ? $this->object->getName() : null, 
          'body' => $this->object->getBody() ? $this->object->getBody() : null, 
        );
        
        if($this->object instanceof ICategory) {
          $result['category_id'] = $this->object->getCategoryId();
          $result['category'] = $this->object->category()->get() instanceof Category ? $this->object->category()->get()->getName() : null;
        } // if
        
        if($this->object instanceof IComplete) {
          $result['priority'] = $this->object->getPriority();
          $result['due_on'] = $this->object->getDueOn();
          $result['completed_on'] = $this->object->getCompletedOn();
        } // if
        
        if($this->object instanceof IAssignees) {
          $result['assignee_id'] = $this->object->getAssigneeId();
          $result['assignee'] = $this->object->assignees()->getAssignee() instanceof User ? $this->object->assignees()->getAssignee()->getDisplayName() : null;
        } // if

        $result['comments'] = $this->object instanceof IComments ? $this->getCommentsForSearch() : null;
        $result['subtasks'] = $this->object instanceof ISubtasks ? $this->getSubtasksForSearch() : null;
        
        return $result;
        
      // Additional properties for names index
      } elseif($index instanceof NamesSearchIndex) {
        return array(
          'name' => $this->object->getName(),
          'body' => $this->object->getBody() ? $this->object->getBody() : null,
          'visibility' => $this->object->getVisibility(),
          'comments' => $this->object instanceof IComments ? $this->getCommentsForSearch(STATE_VISIBLE) : null,
          'subtasks' => $this->object instanceof ISubtasks ? $this->getSubtasksForSearch(STATE_VISIBLE) : null,
        );
        
      // Invalid index type
      } else {
        throw new InvalidInstanceError('index', $index, array('ProjectObjectsSearchIndex', 'NamesSearchIndex'));
      } // if
      
    } // getAdditional

    /**
     * Return comments prepared for search index
     *
     * @param int $min_state
     * @return null|string
     */
    function getCommentsForSearch($min_state = STATE_ARCHIVED) {
      $rows = DB::execute('SELECT body FROM ' . TABLE_PREFIX . 'comments WHERE parent_type = ? AND parent_id = ? AND state >= ? ORDER BY created_on', get_class($this->object), $this->object->getId(), $min_state);

      if($rows) {
        $result = '';

        foreach($rows as $row) {
          $result .= $row['body'] . ' ';
        } // foreach

        return $result ? trim($result) : null;
      } else {
        return null;
      } // if
    } // getCommentsForSearch

    /**
     * Return subtasks prepared for search index
     *
     * @param int $min_state
     * @return null|string
     */
    function getSubtasksForSearch($min_state = STATE_ARCHIVED) {
      $rows = DB::execute('SELECT body FROM ' . TABLE_PREFIX . 'subtasks WHERE parent_type = ? AND parent_id = ? AND state >= ? ORDER BY created_on', get_class($this->object), $this->object->getId(), $min_state);

      if($rows) {
        $result = '';

        foreach($rows as $row) {
          $result .= $row['body'] . ' ';
        } // foreach

        return $result ? trim($result) : null;
      } else {
        return null;
      } // if
    } // getSubtasksForSearch
    
  }
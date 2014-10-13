<?php

  /**
   * Notebook page search item implementation
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class INotebookPageSearchItemImplementation extends ISearchItemImplementation {
  
    /**
     * Return list of indices that index parent object
     * 
     * Result is an array where key is the index name, while value is list of 
     * fields that's watched for changes
     * 
     * @return array
     */
    function getIndices() {
      return array(
        'project_objects' => array('parent_type', 'parent_id', 'name', 'body'), 
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
      $notebook = $this->object->getNotebook();

      if($index instanceof ProjectObjectsSearchIndex) {
        $result = array(   
          'name' => $this->object->getName(), 
          'body' => $this->object->getBody() ? $this->object->getBody() : null,
        );
        
        if($notebook instanceof Notebook) {
          $result['project_id'] = $notebook->getProjectId();
          $result['project'] = $notebook->getProject() instanceof Project ? $notebook->getProject()->getName() : null;
          $result['milestone_id'] = $notebook->getMilestoneId() ? $notebook->getMilestoneId() : null;
          $result['milestone'] = $notebook->getMilestone() instanceof Milestone ? $notebook->getMilestone()->getName() : null;
        } else {
          $result['context'] = 'unknown';
        } // if

        $result['comments'] = $this->getCommentsForSearch();
        $result['subtasks'] = '';
        
        return $result;
      } elseif($index instanceof NamesSearchIndex) {
        return array(
          'name' => $this->object->getName(),
          'body' => $this->object->getBody() ? $this->object->getBody() : null,
          'comments' => $this->getCommentsForSearch(STATE_VISIBLE),
          'visibility' => $notebook instanceof Notebook ? $notebook->getVisibility() : null,
        );
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
    
  }
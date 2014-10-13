<?php

  /**
   * Task search item implementation
   * 
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class ITaskSearchItemImplementation extends IProjectObjectSearchItemImplementation {
  
    /**
     * Return additional properties for a given index
     * 
     * @param SearchIndex $index
     * @return mixed
     */
    function getAdditional(SearchIndex $index) {
      if($index instanceof NamesSearchIndex) {
        return array(
          'name' => $this->object->getName(), 
          'short_name' => '#' . $this->object->getTaskId(),
          'body' => $this->object->getBody(),
          'visibility' => $this->object->getVisibility(),
          'comments' => $this->object instanceof IComments ? $this->getCommentsForSearch() : null,
          'subtasks' => $this->object instanceof ISubtasks ? $this->getSubtasksForSearch() : null,
        );
      } else {
        return parent::getAdditional($index);
      } // if
    } // getAdditional

    /**
     * Describe parent object to be used in search result
     *
     * @param IUser $user
     * @return array
     */
    function describeForSearch(IUser $user) {
      $result = parent::describeForSearch($user);

      $result['short_name'] = '#' . $this->object->getTaskId();

      return $result;
    } // describeForSearch
    
  }
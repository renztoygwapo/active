<?php

  /**
   * Source search item implementation
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class ISourceSearchItemImplementation extends ISearchItemImplementation {
  
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
        'source' => array('repository_id', 'message_body'), 
      );
    } // getIndices
    
    /**
     * Return additional properties for a given index
     * 
     * @param SearchIndex $index
     * @return mixed
     */
    function getAdditional(SearchIndex $index) {
      if($index instanceof SourceSearchIndex) {
        return array(
          'repository_id' => $this->object->getRepositoryId(), 
          'repository' => $this->object->getRepository() instanceof SourceRepository ? $this->object->getRepository()->getName() : null, 
          'body' => stripslashes($this->object->getMessageBody()),
        );
      } else {
        throw new InvalidInstanceError('index', $index, 'SourceSearchIndex');
      } // if
    } // getAdditional

    /**
     * Descript for search result
     *
     * @param IUser $user
     * @return array
     */
    function describeForSearch(IUser $user) {
      $repository = $this->object->getSourceRepository();

      if($repository instanceof SourceRepository) {
        $project_repositories = ProjectSourceRepositories::findByParent($repository);

        if($project_repositories) {
          $project_repository = first($project_repositories);

          if($project_repository instanceof ProjectSourceRepository) {
            return array(
              'id' => $this->object->getId(),
              'type' => get_class($this->object),
              'type_underscore' => $this->object->getBaseTypeName(),
              'verbose_type' => $this->object->getVerboseType(),
              'name' => $this->object->getVerboseName(),
              'permalink' => $this->object->getViewUrl($project_repository->getProjectId(), $project_repository->getId()),
              'short_name' => $repository->getName(),
            );
          } // if
        } // if
      } // if

      return null;
    } // describeForSearch
    
  }
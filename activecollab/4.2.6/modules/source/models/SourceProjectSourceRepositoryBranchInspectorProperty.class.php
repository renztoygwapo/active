<?php

  /**
   * SourceCommit commited by property defintiion class
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class SourceProjectSourceRepositoryBranchInspectorProperty extends InspectorProperty {
  	
  	/**
  	 * Field Name
  	 * 
  	 * @var string
  	 */
  	var $field_name;

    /**
     * Project Repository
     *
     * @var ProjectSourceRepository
     */
    private $project_source_repository;

    /**
  	 * Constructor
  	 * 
  	 * @param ProjectSourceRepository $object
  	 * @param string $field_name
  	 */
  	function __construct(ProjectSourceRepository $object, $field_name) {
  		$this->field_name = $field_name;
      $this->project_source_repository = $object;
  	}
    
    /**
     * Function which will render the property
     * 
     * @return string
     */
    function render() {
      return '(function (field, object, client_interface) { App.Inspector.Properties.ProjectSourceRepositoryBranchName.apply(field, [object, client_interface, "' . $this->field_name . '", "'.$this->project_source_repository->getChangeBranchUrl().'"]) })';
    } // render    
  } // SourceCommitCommitedByInspectorProperty
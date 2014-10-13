<?php

  /**
   * Project object subtask implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectObjectSubtask extends Subtask {
    
    /**
     * Parent project
     *
     * @var Project
     */
    private $project = false;
    
    /**
     * Return parent project
     *
     * @return Project
     */
    function getProject() {
      if($this->project === false) {
        $this->project = $this->getParent()->getProject();
      } // if
      
      return $this->project;
    } // getProject

    /**
     * Parent object's project ID
     *
     * @var integer
     */
    private $project_id = false;

    /**
     * Return parent object's project id
     *
     * @return integer
     */
    function getProjectId() {
      if ($this->project_id === false) {
        $this->project_id = $this->getParent()->getProjectId();
      } // if

      return $this->project_id;
    } // getProjectId

    // ---------------------------------------------------
    //  Interface impelementations / overrides
    // ---------------------------------------------------
    
    /**
     * Labels implementation instance
     *
     * @var IAssignmentLabelImplementation
     */
    private $label = false;
    
    /**
     * Return labels implementation
     *
     * @return IAssignmentLabelImplementation
     */
    function label() {
      if($this->label === false) {
        $this->label = new IAssignmentLabelImplementation($this);
      } // if
      
      return $this->label;
    } // label
    
    /**
     * Assignees helper implementation
     *
     * @var IProjectObjectSubtaskAssigneesImplementation
     */
    private $assignees = false;
    
    /**
     * Return assignees helper implementation
     *
     * @return IProjectObjectSubtaskAssigneesImplementation
     */
    function assignees() {
      if($this->assignees === false) {
        $this->assignees = new IProjectObjectSubtaskAssigneesImplementation($this);
      } // if
      
      return $this->assignees;
    } // assignees
    
  }
<?php

  /**
   * Project object history renderer
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectObjectHistoryRenderer extends HistoryRenderer {
    
    /**
     * Render single field value
     *
     * @param IUser $user
     * @param string $field
     * @param mixed $value
     * @param mixed $old_value
     * @return string
     */
    protected function renderField(IUser $user, $field, $value, $old_value) {
      
      // Project ID
      if($field == 'project_id') {
        if($value) {
          if($old_value) {
            return lang('Moved from <b>:old_value</b> project to <b>:new_value</b> project', array(
              'old_value' => $this->getProjectInfo($user, $old_value), 
              'new_value' => $this->getProjectInfo($user, $value), 
            ));
          } else {
            return lang('Moved to <b>:new_value</b> project', array(
              'new_value' => $this->getProjectInfo($user, $value), 
            ));
          } // if
        } else {
          if($old_value) {
            return lang('Project set to empty value'); // This would be an error actually
          } // if
        } // if
      } // if
      
      // Milestone ID
      if($field == 'milestone_id') {
        if($value) {
          if($old_value) {
            return lang('Milestone changed from <b>:old_value</b> to <b>:new_value</b>', array(
              'old_value' => $this->getMilestoneInfo($user, $old_value), 
              'new_value' => $this->getMilestoneInfo($user, $value), 
            ));
          } else {
            return lang('Milestone set to <b>:new_value</b>', array(
              'new_value' => $this->getMilestoneInfo($user, $value), 
            ));
          } // if
        } else {
          if($old_value) {
            return lang(':object_type removed from milestone <b>:old_value</b>', array('object_type' => $this->object->getVerboseType(), 'old_value' => $this->getMilestoneInfo($user, $old_value)));
          } // if
        } // if
      } // if
      
      return parent::renderField($user, $field, $value, $old_value);
    } // renderField
    
    /**
     * Map of priject IDs and names
     *
     * @var array
     */
    private $projects_map = false;
    
    /**
     * Return project info based on project ID
     *
     * @param IUser $user
     * @param integer $project_id
     * @return string
     */
    function getProjectInfo(IUser $user, $project_id) {
      if($this->projects_map === false) {
        $this->getProjectsMap($user);
      } // if
      
      return isset($this->projects_map[$project_id]) ? $this->projects_map[$project_id] : lang('Unknown Project');
    } // getProjectInfo
    
    /**
     * Cached array of milestone IDs and names
     *
     * @var array
     */
    private $milestones_map = false;
    
    /**
     * Return milestone info based on milestone ID
     *
     * @param IUser $user
     * @param integer $milestone_id
     * @return string
     */
    function getMilestoneInfo(IUser $user, $milestone_id) {
      if($this->milestones_map === false) {
        $milestone_ids = array();
        $available_projects = $this->getProjectsMap($user);

        if (is_foreachable($available_projects)) {
          $milestone_ids = DB::executeFirstColumn("SELECT id FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Milestone' AND project_id IN (?)", array_keys($available_projects));
        } // if

        $this->milestones_map = is_foreachable($milestone_ids) ? Milestones::getIdNameMap($milestone_ids, STATE_ARCHIVED) : null;

      } // if
      
      return $this->milestones_map && isset($this->milestones_map[$milestone_id]) ? $this->milestones_map[$milestone_id] : lang('Unknown Milestone');
    } // getMilestoneInfo

    /**
     * @param IUser $user
     * @return array|null
     */
    private function getProjectsMap(IUser $user) {
      if ($this->projects_map === false) {
        $this->projects_map = $user instanceof User ? Projects::getIdNameMap($user, STATE_ARCHIVED, null, null, true) : null;
      } // if

      return $this->projects_map;
    } // getProjectsMap


  }
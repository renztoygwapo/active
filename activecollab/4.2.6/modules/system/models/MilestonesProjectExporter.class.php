<?php

  class MilestonesProjectExporter extends ProjectExporter {

    /**
     * active module
     *
     * @var string
     */
    protected $active_module = SYSTEM_MODULE;

    /**
     * Relative path where exported files will be stored
     * 
     * @var String
     */
    protected $relative_path = 'milestones';
        
    /**
     * Export the milestones
     * 
     * @param void
     * @return null
     */
    public function export() {
      parent::export();

      $installed_modules = AngieApplication::getInstalledModules();
      $milestone_submodules = array(
        'tasks' => false,
        'todo'  => false,
        'discussions' => false,
        'notebooks' => false
      ); //array

      foreach ($installed_modules as $module) {
        if (array_key_exists($module->getName(), $milestone_submodules)) {
          $milestone_submodules[$module->getName()] = true;
        } //if
      } //foreach

      if ($this->section == 'milestones') {
        $milestones_count = Milestones::countByProject($this->project,STATE_ARCHIVED, $this->getObjectsVisibility());
        $per_query = 500;
        $loops = ceil($milestones_count / $per_query);

        // create single milestone page for every milestone in the project
        $current_iteration = 0;

        while ($current_iteration < $loops) {
          $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Milestone' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_iteration * $per_query . ", $per_query", $this->project->getId(), STATE_ARCHIVED, $this->getObjectsVisibility());
          if ($result instanceof DBResult) {
            foreach ($result as $row) {
              $milestone = new Milestone();
              $milestone->loadFromRow($row);
              $this->smarty->assignByRef('milestone', $milestone);
              $this->smarty->assignByRef('milestone_submodules', $milestone_submodules);
              $this->renderTemplate('milestone', $this->getDestinationPath('milestone_' . $milestone->getId() . '.html'));
              $this->smarty->clearAssign('milestone');
              unset($row);
            } //foreach
          } //if
          
          set_time_limit(30);
          $current_iteration++;
        } // while
          
        // render milestone index page
        $this->renderTemplate('milestones_index', $this->getDestinationPath('index.html'));
      } // if
    
      return true;
    } // export
  } // MilestonesProjectExporter
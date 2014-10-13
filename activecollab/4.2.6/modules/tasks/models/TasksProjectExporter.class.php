<?php

  /**
   * Tasks project exporter
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class TasksProjectExporter extends ProjectExporter {
  	
  	/**
  	 * active module
  	 * 
  	 * @var string
  	 */
  	protected $active_module = TASKS_MODULE;
  	
    /**
     * Relative path where exported files will be stored
     * 
     * @var String
     */
    protected $relative_path = 'tasks';
        
    /**
     * Export the tasks
     * 
     * @param void
     * @return null
     */
    public function export() {
      parent::export();
      if ($this->section == 'tasks') {
	      $tasks_count = Tasks::countByProject($this->project, null, STATE_ARCHIVED, $this->getObjectsVisibility());
	      $per_query = 500;
	      $loops = ceil($tasks_count / $per_query);
	      
	      // create single task page for every task in the project
	      $current_iteration = 0;
	      while ($current_iteration < $loops) {
	      	$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Task' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_iteration * $per_query . ", $per_query", $this->project->getId(), STATE_ARCHIVED, $this->getObjectsVisibility());
	      	if ($result instanceof DBResult) {
	      		foreach ($result as $row) {
	      			$task = new Task();
		    			$task->loadFromRow($row);
		    			$this->smarty->assignByRef('task', $task);
		    			$this->renderTemplate('task', $this->getDestinationPath('task_' . $task->getTaskId() . '.html'));
		    			$this->smarty->clearAssign('task');
		    			unset($row);
	      		} //foreach
	      	} //if
  			set_time_limit(30);
  			$current_iteration++;
	      } // while
          
        $categories = Categories::findBy($this->project, 'TaskCategory');
        $categories_for_helper = Categories::findBy($this->project, 'TaskCategory');
        $this->smarty->assignByRef('categories', $categories_for_helper);
				
	      // render task index page     
	      $this->renderTemplate('tasks_index', $this->getDestinationPath('index.html'));
	
	      // export categories
	      if (is_foreachable($categories)) {
	      	foreach ($categories as $category) {
            $this->smarty->assignByRef('category', $category);
            $this->renderTemplate('tasks_index', $this->getDestinationPath('category_' . $category->getId() . '.html'));
            $this->smarty->clearAssign('category');
	      	} // foreach      	
	      } // if				
      } // if
    
	  return true;
    } // export
    
  } // TasksProjectExporter
<?php

  /**
   * Discussions exporter
   * 
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class DiscussionsProjectExporter extends ProjectExporter {
  	
  	/**
  	 * active module
  	 * 
  	 * @var string
  	 */
  	protected $active_module = DISCUSSIONS_MODULE;
  	
    /**
     * Relative path where exported files will be stored
     * 
     * @var String
     */
    protected $relative_path = 'discussions';
        
    /**
     * Export the discussions
     */
    public function export() {
      parent::export();
      
      if ($this->section == 'discussions') {
	      $discussions_count = Discussions::countByProject($this->project, null, STATE_ARCHIVED, $this->getObjectsVisibility());
	      $per_query = 500;
	      $loops = ceil($discussions_count / $per_query);
	      
	      // create single discussion page for every discussion in the project
	      $current_iteration = 0;
	      while ($current_iteration < $loops) {
	      	$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Discussion' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_iteration * $per_query . ", $per_query", $this->project->getId(), STATE_ARCHIVED, $this->getObjectsVisibility());
	      	
	      	if ($result instanceof DBResult) {
	    		  foreach ($result as $row) {
		    			$discussion = new Discussion();
		    			$discussion->loadFromRow($row);
		    			$this->smarty->assignByRef('discussion', $discussion);
		    			$this->renderTemplate('discussion', $this->getDestinationPath('discussion_' . $discussion->getId() . '.html'));
		    			$this->smarty->clearAssign('discussion');
		    			unset($row);
	    		  } // foreach
	        } //if
  			set_time_limit(30);
  			$current_iteration++;
	    } // while
          
		  $categories = Categories::findBy($this->project, 'DiscussionCategory');
		  $categories_for_helper = Categories::findBy($this->project, 'DiscussionCategory');
		  $this->smarty->assignByRef('categories', $categories_for_helper);
				
	      // render discussion index page     
	      $this->renderTemplate('discussions_index', $this->getDestinationPath('index.html'));
	
	      // export categories
	      if (is_foreachable($categories)) {
	      	foreach ($categories as $category) {
    		  $this->smarty->assignByRef('category', $category);
    		  $this->renderTemplate('discussions_index', $this->getDestinationPath('category_' . $category->getId() . '.html'));
    		  $this->smarty->clearAssign('category');
	      	} // foreach      	
	      } // if				
      } // if
    
      return true;
    } // export
    
  } // ProjectExporter
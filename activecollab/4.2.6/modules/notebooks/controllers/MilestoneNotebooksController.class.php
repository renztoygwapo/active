<?php

  // Extend milestones controller
  AngieApplication::useController('milestones', SYSTEM_MODULE);

  /**
   * Milestone notebooks controller implementation
   *
   * @package activeCollab.modules.notebooks
   * @subpackage controllers
   */
  class MilestoneNotebooksController extends MilestonesController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_milestone->isNew()) {
        $this->response->notFound();
      } // if
      
      $add_notebook_url = false;
      if(Notebooks::canAdd($this->logged_user, $this->active_project)) {
        $add_notebook_url = Router::assemble('project_notebooks_add', array(
          'project_slug' => $this->active_project->getSlug(), 
          'milestone_id' => $this->active_milestone->getId()
        ));
        
        $this->wireframe->actions->add('new_notebook', lang('New Notebook'), $add_notebook_url);
      } // if
      
      $this->smarty->assign('add_notebook_url', $add_notebook_url);
    } // __construct
    
    /**
     * Show milestone notebooks
     */
    function index() {
    	// Serve request made with web browser
      if($this->request->isWebBrowser()) {
      	$items_per_page = 30;
	      
	    	$this->response->assign('more_results_url', Router::assemble('milestone_notebooks', array(
	    		'project_slug' => $this->active_project->getSlug(), 
	    	  'milestone_id' =>$this->active_milestone->getId())
	    	));
	    	
	    	if($this->request->get('paged_list')) {
	    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
	    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
	    		$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'Notebook' AND state >= ? AND visibility >= ? AND id NOT IN (?) AND created_on < ? ORDER BY ISNULL(completed_on) DESC, priority DESC, created_on DESC LIMIT $items_per_page", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility(), $exclude, date(DATETIME_MYSQL, $timestamp));
	    		$this->response->respondWithData(Notebooks::getDescribedNotebookArray($result, $this->active_project, $this->logged_user, $items_per_page));
	    	} else {
	    		$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'Notebook' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(completed_on) DESC, priority DESC, created_on DESC", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility());
		      $notebooks = Notebooks::getDescribedNotebookArray($result, $this->active_project, $this->logged_user, $items_per_page);
		      $this->response->assign(array(
		      	'notebooks' => $notebooks,
		      	'items_per_page'  => $items_per_page,
		      	'total_items' => ($result instanceof DBResult) ? $result->count() : 0,
		      	'milestone_id' => $this->active_milestone->getId()
		      ));
	    	} //if
	    	
      // Server request made with mobile device
      } elseif($this->request->isMobileDevice()) {
      	$this->response->assign(array(
          'notebooks' => DB::execute("SELECT id, name FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = ? AND state = ? AND visibility >= ? ORDER BY ISNULL(position) ASC, position ASC", $this->active_milestone->getId(), 'Notebook', STATE_VISIBLE, $this->logged_user->getMinVisibility()),
        	'notebook_url' => Router::assemble('project_notebook', array('project_slug' => $this->active_project->getSlug(), 'notebook_id' => '--NOTEBOOKID--')),
        ));

      // API call
      } elseif($this->request->isApiCall()) {
        $this->response->respondWithData(Notebooks::findByMilestone($this->active_milestone), array(
          'as' => 'notebooks'
        ));
      } // if
    } // index
    
  } //MilestoneNotebooksController
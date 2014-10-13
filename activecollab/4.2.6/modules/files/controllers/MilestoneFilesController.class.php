<?php

  // Extend milestones controller
  AngieApplication::useController('milestones', SYSTEM_MODULE);

  /**
   * Milestone files controller implementation
   *
   * @package activeCollab.modules.files
   * @subpackage controllers
   */
  class MilestoneFilesController extends MilestonesController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_milestone->isNew()) {
        $this->response->notFound();
      } // if
      
      $add_file_url = false;
      if(ProjectAssets::canAdd($this->logged_user, $this->active_project)) {
        $add_file_url = Router::assemble('project_assets_files_add', array(
          'project_slug' => $this->active_project->getSlug(), 
          'milestone_id' => $this->active_milestone->getId()
        ));
        
        $this->wireframe->actions->add('new_file', lang('New File'), $add_file_url);
      } // if
      
      $this->smarty->assign('add_file_url', $add_file_url);
    } // __construct
    
    /**
     * Show milestone files
     */
    function index() {
    	// Serve request made with web browser
      if($this->request->isWebBrowser()) {
      	$items_per_page = 30;
	      
	    	$this->response->assign('more_results_url', Router::assemble('milestone_files', array(
	    		'project_slug' => $this->active_project->getSlug(), 
	    	  'milestone_id' =>$this->active_milestone->getId())
	    	));
	    	
	    	if($this->request->get('paged_list')) {
	    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
	    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
	    		$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'File' AND state >= ? AND visibility >= ? AND id NOT IN (?) AND created_on < ? ORDER BY ISNULL(completed_on) DESC, priority DESC, created_on DESC LIMIT $items_per_page", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility(), $exclude, date(DATETIME_MYSQL, $timestamp));
	    		$this->response->respondWithData(ProjectAssets::getDescribedFileArray($result, $this->active_project, $this->logged_user, $items_per_page));
	    	} else {
	    		$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'File' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(completed_on) DESC, priority DESC, created_on DESC", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility());
		      $files = ProjectAssets::getDescribedFileArray($result, $this->active_project, $this->logged_user, $items_per_page);
		      $this->response->assign(array(
		      	'files' => $files,
		      	'items_per_page'  => $items_per_page,
		      	'total_items' => ($result instanceof DBResult) ? $result->count() : 0,
		      	'milestone_id' => $this->active_milestone->getId()
		      ));
	    	} //if
	    	
      // Server request made with mobile device
      } elseif($this->request->isMobileDevice()) {
      	$this->response->assign(array(
          'files' => DB::execute("SELECT id, name FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = ? AND state >= ? AND visibility >= ? ORDER BY ISNULL(completed_on) DESC, priority DESC, created_on DESC", $this->active_milestone->getId(), 'File', STATE_VISIBLE, $this->logged_user->getMinVisibility()),
        	'file_url' => Router::assemble('project_assets_file', array('project_slug' => $this->active_project->getSlug(), 'asset_id' => '--ASSETID--')),
        ));

      // API call
      } elseif($this->request->isApiCall()) {
        $this->response->respondWithData(ProjectAssets::findActiveByMilestoneAndType($this->active_milestone, 'File', $this->logged_user), array(
          'as' => 'files'
        ));
      } // if
    } // index
    
  } //MilestoneFilesController
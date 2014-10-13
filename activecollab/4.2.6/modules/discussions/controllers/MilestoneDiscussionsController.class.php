<?php

  // Extend milestones controller
  AngieApplication::useController('milestones', SYSTEM_MODULE);

  /**
   * Milestone discussions controller implementation
   *
   * @package activeCollab.modules.discussions
   * @subpackage controllers
   */
  class MilestoneDiscussionsController extends MilestonesController {
  	
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_milestone->isNew()) {
        $this->response->notFound();
      } // if
      
      
      $add_discussion_url = false;
      if (Discussions::canAdd($this->logged_user, $this->active_project)) {
        $add_discussion_url = Router::assemble('project_discussions_add', array(
          'project_slug' => $this->active_project->getSlug(),
          'milestone_id' => $this->active_milestone->getId()
        ));

        $this->wireframe->actions->add('new_discussion', lang('New Discussion'), $add_discussion_url,  array('icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),));
      } // if

      $this->smarty->assign('add_discussion_url', $add_discussion_url);
    } // __construct
    
    /**
     * Show milestone discussions
     */
    function index() {
    	// Serve request made with web browser
      if($this->request->isWebBrowser()) {
      	$items_per_page = 30;
      	
	    	$this->response->assign('more_results_url', Router::assemble('milestone_discussions', array(
	    		'project_slug' => $this->active_project->getSlug(), 
	    	  'milestone_id' =>$this->active_milestone->getId())
	    	));
	    	if($this->request->get('paged_list')) {
	    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
	    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
	    		$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'Discussion' AND state >= ? AND visibility >= ? AND id NOT IN (?) AND created_on < ? ORDER BY boolean_field_1 DESC, datetime_field_1 DESC LIMIT $items_per_page", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility(), $exclude, date(DATETIME_MYSQL, $timestamp));
	    		$this->response->respondWithData(Discussions::getDescribedDiscussionArray($result, $this->active_project, $this->logged_user, $items_per_page));
	    	} else {
		      $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'Discussion' AND state >= ? AND visibility >= ?  ORDER BY boolean_field_1 DESC, datetime_field_1 DESC", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility());
		      $discussions = Discussions::getDescribedDiscussionArray($result, $this->active_project, $this->logged_user, $items_per_page);
		      $this->response->assign(array(
		      	'discussions' => $discussions,
		      	'items_per_page'  => $items_per_page,
		      	'total_items' => ($result instanceof DBResult) ? $result->count() : 0,
		      	'milestone_id' => $this->active_milestone->getId()
		      ));
	    	} //if
	    	
      // Server request made with mobile device
      } elseif($this->request->isMobileDevice()) {
      	$this->response->assign(array(
          'discussions' => DB::execute("SELECT id, name, category_id, milestone_id FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Discussion' AND milestone_id = ? AND state >= ? AND visibility >= ? ORDER BY boolean_field_1 DESC, datetime_field_1 DESC", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility()),
        	'discussion_url' => Router::assemble('project_discussion', array('project_slug' => $this->active_project->getSlug(), 'discussion_id' => '--DISCUSSIONID--')), 
        ));

      // API call
      } elseif($this->request->isApiCall()) {
        $this->response->respondWithData(Discussions::findActiveByMilestone($this->active_milestone, $this->logged_user), array(
          'as' => 'discussions'
        ));
      } // if
    } // index
    
  } //MilestoneDiscussionsController
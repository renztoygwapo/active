<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Projects controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectsController extends BackendController {
    
    /**
     * Methods that are available through API
     *
     * @var array
     */
    protected $api_actions = array('index');
    
    /**
     * Categories delegate controller instance
     *
     * @var CategoriesController
     */
    protected $categories_delegate;
    
    /**
     * Active project
     *
     * @var Project
     */
    protected $active_project;
    
    /**
     * Construct projects controller
     *
     * @param Request $parent
     * @param string $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);

      if($this->getControllerName() == 'projects') {
        $this->categories_delegate = $this->__delegate('categories', CATEGORIES_FRAMEWORK_INJECT_INTO, 'project');
      } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
       
      $this->active_project = new Project();

      if($this->request->isWebBrowser()) {
        $this->wireframe->tabs->clear();
        $this->wireframe->tabs->add('projects', lang('Projects'), Router::assemble('projects'), null, true);
        
        EventsManager::trigger('on_projects_tabs', array(&$this->wireframe->tabs, &$this->logged_user));
        
        $this->wireframe->setCurrentMenuItem('projects');
      } // if

      if($this->getControllerName() == 'projects' || $this->getControllerName() == 'project') {
        $this->wireframe->breadcrumbs->add('projects', lang('Projects'), Router::assemble('projects'));
      } // if
      
      $this->response->assign('active_project', $this->active_project);
      
      if($this->categories_delegate instanceof CategoriesController) {
        $this->categories_delegate->__setProperties(array(
          'routing_context' => 'project', 
          'category_class' => 'ProjectCategory',
					'active_object' => &$this->active_project
        ));
      } // if
    } // __construct
    
    /**
     * Display main projects page
     */
    function index() {
      
      // API call
      if ($this->request->isApiCall()) {
        $this->response->respondWithData(Projects::findByUser($this->logged_user, true, array('state >= ?', STATE_VISIBLE)), array(
          'as' => 'projects'
        ));
        
      // Phone call
      } elseif ($this->request->isPhone()) {
        list($favorite_projects, $other_projects) = Projects::findForPhone($this->logged_user, true, array('state >= ? AND completed_on IS NULL', STATE_VISIBLE));
        
        $this->response->assign(array(
          'favorite_projects' => $favorite_projects, 
          'other_projects' => $other_projects
        ));
        
      // Printer
      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        $filter_by = $this->request->get('filter_by', null);
        
        // page title
        $filter_by_completion = array_var($filter_by, 'is_completed', null); 
        if ($filter_by_completion === '1') {
        	$page_title = lang('Completed Projects');
        } else if ($filter_by_completion === '0') {
			    $page_title = lang('Active Projects');
        } else {
        	$page_title = lang('All Projects');
        } // if
        
        // Maps
        if ($group_by == 'category_id') {
        	$map = Categories::getIdNameMap(null, 'ProjectCategory');
        	
        	if(empty($map)) {
        	  $map = array();
        	} // if
        	
        	$map[0] = lang('Uncategorized');
        	$getter = 'getCategoryId';
        	$page_title .= ', ' . lang('Grouped by Category');
        } elseif ($group_by == 'company_id') {
            $map = Companies::getidNameMap();
        	$map[0] = lang('Uncategorized');
        	$getter = 'getCompanyId';
        	$page_title .= ', ' . lang('Grouped by Company');
        } elseif ($group_by == 'label_id') {
          
          $map = Labels::getIdNameMap('ProjectLabel');
          
          if(empty($map)) {
            $map = array();
          } // if
          
        	$map[0] = lang('No Label');
        	$getter = 'getLabelId';
        	$page_title .= ', ' . lang('Grouped by Label');
        } // if

        // Find projects
        $projects = Projects::findForPrint($this->logged_user, STATE_VISIBLE, $group_by, $filter_by);
        
        // Use this to sort objects by map array
        $project_list = group_by_mapped($map, $projects, $getter);
        
        $this->response->assignByRef('projects', $project_list);
        $this->response->assignByRef('map', $map);
        $this->response->assign(array(
        	'page_title' => $page_title,
        	'getter' => $getter
        ));
        
      // Web browser
      } else {
        if(Projects::canAdd($this->logged_user)) {
          $this->wireframe->actions->add('new_project', lang('New Project'), Router::assemble('projects_add'), array(
            'onclick' => new FlyoutFormCallback('project_created'),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'primary' => true
          ));
        } // if

        $this->wireframe->list_mode->enable();
        $companies = Companies::getIdNameMap();
        unset($companies[$this->owner_company->getId()]);
        
        // page title
        $this->response->assign(array(
          'can_add_project' => Projects::canAdd($this->logged_user),
          'projects' => Projects::findforObjectsList($this->logged_user),
          'categories' => Categories::getIdNameMap(null, 'ProjectCategory'),
          'companies' => $companies,
          'labels' => Labels::findByType('ProjectLabel'),
          'manage_categories_url' => $this->logged_user->isProjectManager() ? Router::assemble('project_categories') : null,
          'in_archive' => false,
          'print_url' => Router::assemble('projects', array('print' => 1))
        ));
        
        // mass manager
        if ($this->logged_user->isProjectManager()) {
        	$mass_manager = new MassManager($this->logged_user, new Project());        	
        	$this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
      } // if
      
    } // index
    /**
     * Mass edit
     */
    function mass_edit() {
    	if ($this->getControllerName() == 'projects') {
    		$this->mass_edit_objects = Projects::findByIds($this->request->post('selected_item_ids'));
    	} // if
    	
    	parent::mass_edit();
    } // mass_edit
    
    /**
     * Show projects archive page
     */
    function archive() {
      if ($this->request->isWebBrowser()) {
        $this->wireframe->print->enable();
        $this->wireframe->list_mode->enable();
        $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('projects_archive'));

        $companies = Companies::getIdNameMap();
        unset($companies[$this->owner_company->getId()]);

        // page title
        $this->response->assign(array(
          'is_archive'            => true,
          'can_add_project'       => Projects::canAdd($this->logged_user),
          'projects'              => Projects::findforObjectsList($this->logged_user, STATE_ARCHIVED),
          'categories'            => Categories::getIdNameMap(null, 'ProjectCategory'),
          'companies'             => $companies,
          'labels'                => Labels::findByType('ProjectLabel'),
          'manage_categories_url' => $this->logged_user->isProjectManager() ? Router::assemble('project_categories') : null,
          'in_archive'            => true,
          'print_url'             => Router::assemble('projects_archive', array('print' => 1))
        ));

        // mass manager
        if ($this->logged_user->isProjectManager()) {
          $this->active_project->setState(STATE_ARCHIVED);
          $mass_manager = new MassManager($this->logged_user, new Project());
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if

      } else if ($this->request->isPrintCall()) {

        $group_by = strtolower($this->request->get('group_by', null));
        // page title
        $page_title = lang('Archived Projects');
        // Maps
        if ($group_by == 'category_id') {
          $map = Categories::getIdNameMap(null, 'ProjectCategory');

          if(empty($map)) {
            $map = array();
          } // if

          $map[0] = lang('Uncategorized');
          $getter = 'getCategoryId';
          $page_title .= ', ' . lang('Grouped by Category');
        } elseif ($group_by == 'company_id') {
          $map = Companies::getidNameMap();
          $map[0] = lang('Uncategorized');
          $getter = 'getCompanyId';
          $page_title .= ', ' . lang('Grouped by Company');
        } elseif ($group_by == 'label_id') {

          $map = Labels::getIdNameMap('ProjectLabel');

          if(empty($map)) {
            $map = array();
          } // if

          $map[0] = lang('No Label');
          $getter = 'getLabelId';
          $page_title .= ', ' . lang('Grouped by Label');
        } // if

        // Find projects
        $projects = Projects::findForPrint($this->logged_user, STATE_ARCHIVED, $group_by);

        // Use this to sort objects by map array
        $project_list = group_by_mapped($map, $projects, $getter);

        $this->response->assignByRef('projects', $project_list);
        $this->response->assignByRef('map', $map);
        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));



      } else if($this->request->isApiCall()) {
        $this->response->respondWithData(Projects::findByUser($this->logged_user, true, array('state = ?', STATE_ARCHIVED)), array('as' => 'projects'));
      } elseif($this->request->isPhone()) {
      	$this->response->assign('projects', Projects::findCompletedByUser($this->logged_user, true));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Show project labels (API only)
     */
    function labels() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Labels::findByType('ProjectLabel'), array(
          'as' => 'labels', 
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // labels

    /**
     * Which projects to synchronize
     */
    function what_to_sync() {
      if($this->request->isApiCall()) {
        $last_sync = null;
        if($this->request->get('last_sync')) {
          $last_sync = (integer) $this->request->get('last_sync');
        } // if

        $project_ids = Projects::findIdsByLastSync($this->logged_user, $last_sync);

        $this->response->respondWithData($project_ids, array('format' => 'json'));
      } else {
        $this->response->badRequest();
      } // if
    } // what_to_sync
    
  }
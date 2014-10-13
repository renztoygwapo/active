<?php

  // Build on top of project controller
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Project assets controller
   *
   * @package activeCollab.modules.files
   * @subpackage controllers
   */
  class AssetsController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = FILES_MODULE;
    
    /**
     * Selected asset
     *
     * @var ProjectAsset
     */
    protected $active_asset;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Categories delegate instance
     *
     * @var CategoriesController
     */
    protected $categories_delegate;
    
    /**
     * Comments delegate instance
     *
     * @var CommentsController
     */
    protected $comments_delegate;
    
    /**
     * Subscriptions controller delegate
     *
     * @var SubscriptionsController
     */
    protected $subscriptions_delegate;
    
    /**
     * Complete controller delegate
     * 
     * @var CompleteController
     */
    protected $complete_delegate;
    
    /**
     * Reminders controller instance
     * 
     * @var RemindersController
     */
    protected $reminders_delegate;
    
    /**
     * Sharing delegate
     *
     * @var SharingSettingsController
     */
    protected $sharing_settings_delegate;
    
    /**
     * Move to project delegate controller
     *
     * @var MoveToProjectController
     */
    protected $move_to_project_delegate;

	  /**
	   * Access log controller delegate
	   *
	   * @var AccessLogController
	   */
	  protected $access_logs_delegate;

	  /**
	   * History of changes controller delegate
	   *
	   * @var HistoryOfChangesController
	   */
	  protected $history_of_changes_delegate;
    
    /**
     * Construct assets controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'assets') {

        $this->categories_delegate = $this->__delegate('categories', CATEGORIES_FRAMEWORK_INJECT_INTO, 'project_asset');
      } else {
        $this->wireframe->print->enable();
      } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if (!ProjectAssets::canAccess($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
      
      $this->wireframe->breadcrumbs->add('project_assets', lang('Files'), Router::assemble('project_assets', array('project_slug' => $this->active_project->getSlug())));
      $this->wireframe->tabs->setCurrentTab('files');
      
      if($this->request->isPageCall() || $this->request->isSingleCall() || $this->request->isMobileDevice()) {
        if(ProjectAssets::canAdd($this->logged_user, $this->active_project)) {
          $project_slug = $this->active_project->getSlug();
          
          if($this->request->isPhone()) {
          	$options = new NamedList(array(
	            'upload_files' => array(
	            	'text' => lang('Files'),
	              'url' => Router::assemble('project_assets_files', array('project_slug' => $project_slug)),
	          		'icon' => AngieApplication::getImageUrl('icons/96x96/files.png', FILES_MODULE, AngieApplication::INTERFACE_PHONE)
	            ),
	            'new_text_document' => array(
	            	'text' => lang('Text Documents'),
	              'url' => Router::assemble('project_assets_text_documents', array('project_slug' => $project_slug)),
	            	'icon' => AngieApplication::getImageUrl('icons/96x96/text-documents.png', FILES_MODULE, AngieApplication::INTERFACE_PHONE)
	            )
	          ));
          } else {
          	$options = new NamedList(array(
	            'upload_files' => array(
	              'url' => Router::assemble('project_assets_files_add', array('project_slug' => $project_slug)),
	              'text' => lang('Upload Files'),
	          		'onclick' => new FlyoutFileFormCallback('multiple_assets_created'),
          			'icon' => AngieApplication::getImageUrl('icons/12x12/new-version.png', FILES_MODULE)
	            ),
	            'new_text_document' => array(
	              'url' => Router::assemble('project_assets_text_document_add', array('project_slug' => $project_slug)),
	              'text' => lang('Text Document'),
	            	'onclick' => new FlyoutFormCallback('asset_created'),
          			'icon' => AngieApplication::getImageUrl('icons/12x12/text-document.png', FILES_MODULE)
	            )
	          ));
          } // if
          
          EventsManager::trigger('on_project_assets_new_options', array(&$options, &$this->active_project, &$this->logged_user));
          
          if($this->request->isPhone()) {
	          $this->smarty->assign('subitems', $options);
          } else {
          	$this->wireframe->actions->add('new_asset', lang('New Asset'), '#', array(
	            'subitems' => $options,
              'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),	            
	          ));

            $this->response->assign(array(
              'can_add_assets' => ProjectAssets::canAdd($this->logged_user, $this->active_project),
              'can_manage_assets' => ProjectAssets::canManage($this->logged_user, $this->active_project),
              'project_assets_files_add' => Router::assemble('project_assets_files_add', array('project_slug' => $project_slug)),
              'project_assets_text_document_add' => Router::assemble('project_assets_text_document_add', array('project_slug' => $project_slug)),
            ));
          } // if
        } // if
      } // if

      $asset_id = $this->request->getId('asset_id');
      if($asset_id) {
        $this->active_asset = ProjectAssets::findById($asset_id);      	
      } // if
      
      if($this->active_asset instanceof ProjectAsset) {
        if (!$this->active_asset->isAccessible()) {
          $this->response->notFound();
        } // if

        if ($this->active_asset->getState() == STATE_ARCHIVED) {
          $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_assets_archive', array('project_slug' => $this->active_project->getSlug())));
        } // if
        $this->wireframe->breadcrumbs->add('project_asset', $this->active_asset->getName(), $this->active_asset->getViewUrl());
      } else {
        $this->active_asset = new ProjectAsset();
        $this->active_asset->setProject($this->active_project);
      } // if
      
      $this->smarty->assign('active_asset', $this->active_asset);
      
      if($this->categories_delegate instanceof CategoriesController) {
        $this->categories_delegate->__setProperties(array(
          'categories_context' => &$this->active_project, 
          'routing_context' => 'project_asset', 
          'routing_context_params' => array('project_slug' => $this->active_project->getSlug()), 
          'category_class' => 'AssetCategory',
        	'active_object' => &$this->active_asset
        ));
      } //if
      
      if($this->move_to_project_delegate instanceof MoveToProjectController) {
      	$this->move_to_project_delegate->__setProperties(array(
          'active_project' => &$this->active_project,
          'active_object' => &$this->active_asset,
        ));
      } // if
    } // __before
    
    /**
     * Show main assets page
     */
    function index() {
      
    	// Phone request
      if($this->request->isPhone()) {
      	$this->wireframe->actions->add('add_file', lang('New File'), "#", array(
          'icon' => AngieApplication::getImageUrl('icons/navbar/add-file.png', FILES_MODULE, AngieApplication::INTERFACE_PHONE)
        ));
        $this->wireframe->actions->add('add_text_document', lang('New Text Document'), Router::assemble('project_assets_text_document_add', array('project_slug' => $this->active_project->getSlug())), array(
          'icon' => AngieApplication::getImageUrl('icons/navbar/add-text-document.png', FILES_MODULE, AngieApplication::INTERFACE_PHONE)
        ));
        
        $recent_assets_num = 5;
        
        $this->response->assign('assets', ProjectAssets::findRecentByTypeAndProject($this->active_project, ProjectAssets::getAssetTypes(), $recent_assets_num, STATE_VISIBLE, $this->logged_user->getMinVisibility()));
      	
      // Tablet device
    	} elseif($this->request->isTablet()) {
    		throw new NotImplementedError(__METHOD__);
      
      // API call
      } elseif($this->request->isApiCall()) {
        $this->response->respondWithData(ProjectAssets::findByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility()), array(
          'as' => 'assets', 
        ));
        
        // printer
      } else if ($this->request->isPrintCall()) {
        
        $group_by = strtolower($this->request->get('group_by', ''));
        $filter_by = $this->request->get('filter_by', null);
        
        // page title
        $filter_by_completion = array_var($filter_by, 'is_archived', null); 
        if ($filter_by_completion === '0') {
        	$page_title = lang('Active Files in :project_name Project', array('project_name' => $this->active_project->getName()));
        } else if ($filter_by_completion === '1') {
					$page_title = lang('Archived Files in :project_name Project', array('project_name' => $this->active_project->getName()));        	
        } else {
        	$page_title = lang('All Files in :project_name Project', array('project_name' => $this->active_project->getName()));
        } // if
        
        // maps
        $map = array();
        
        switch ($group_by) {
          case 'milestone_id':
            $map = Milestones::getIdNameMap($this->active_project);
            $map[0] = lang('Unknown Milestone');
            
          	$getter = 'getMilestoneId';
          	$page_title.= ' ' . lang('Grouped by Milestone'); 
            break;
          case 'category_id':
            $map = Categories::getidNameMap($this->active_project, 'AssetCategory');
            $map[0] = lang('Uncategorized');
            
          	$getter = 'getCategoryId';
          	$page_title.= ' ' . lang('Grouped by Category');
            break;
         case 'first_letter':
            $map = get_letter_map();
            $getter = 'getFirstLetter';
          	$page_title.= ' ' . lang('Grouped by First Letter');
            break;
        }//switch
        
        // find tasks
        $assets = ProjectAssets::findForPrint($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility(), $group_by, $filter_by);
        
        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map,$assets,$getter);
        
        $this->response->assignByRef('assets', $print_list);
        $this->response->assignByRef('map', $map);
        
        $this->response->assign(array(
			    'page_title' => $page_title,
        	'getter' => $getter
        ));
      
      // Display regular assets
      } else {
        $this->wireframe->list_mode->enable();
        
        $this->smarty->assign(array(
        	'assets' => ProjectAssets::findForObjectsList($this->active_project, $this->logged_user),
          'letters' => get_letter_map(), 
          'milestones' => Milestones::getIdNameMap($this->active_project), 
          'categories' => Categories::getIdNameMap($this->active_project, 'AssetCategory'),
          'created_on_dates' => ProjectAssets::getCreatedOnDatesMap($this->active_project, $this->logged_user),
          'updated_on_dates' => ProjectAssets::getUpdatedOnDatesMap($this->active_project, $this->logged_user),
        	'types'	=> ProjectAssets::getTypeNameMap(),
        	'types_detailed' => ProjectAssets::getAssetTypesDetailed(), 
          'manage_categories_url' => $this->active_project->availableCategories()->canManage($this->logged_user, 'FileCategory') ? Router::assemble('project_asset_categories', array('project_slug' => $this->active_project->getSlug())) : null,
          'in_archive' => false,
          'print_url' => Router::assemble('project_assets', array('print' => 1, 'project_slug' => $this->active_project->getSlug()))
        ));
        
        // mass manager
        if (ProjectAssets::canManage($this->logged_user, $this->active_project)) {
        	$mass_manager = new MassManager($this->logged_user, $this->active_asset);        	
        	$this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
      } // if
      
    } // index
    
    /**
     * Show archived discussions (mobile devices only)
     */
    function archive() {
      if($this->request->isMobileDevice()) {
        $this->response->assign('assets', ProjectAssets::findArchivedByTypeAndProject($this->active_project, ProjectAssets::getAssetTypes(), STATE_ARCHIVED, $this->logged_user->getMinVisibility()));

      } else if ($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();
        $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_assets_archive', array('project_slug' => $this->active_project->getSlug())));

        $this->smarty->assign(array(
          'assets' => ProjectAssets::findForObjectsList($this->active_project, $this->logged_user, STATE_ARCHIVED),
          'letters' => get_letter_map(),
          'milestones' => Milestones::getIdNameMap($this->active_project),
          'categories' => Categories::getIdNameMap($this->active_project, 'AssetCategory'),
          'types'	=> ProjectAssets::getTypeNameMap(),
          'types_detailed' => ProjectAssets::getAssetTypesDetailed(),
          'in_archive' => true,
          'print_url' => Router::assemble('project_assets_archive', array('print' => 1, 'project_slug' => $this->active_project->getSlug()))
        ));

        // mass manager
        if (ProjectAssets::canManage($this->logged_user, $this->active_project)) {
          $this->active_asset->setState(STATE_ARCHIVED);
          $mass_manager = new MassManager($this->logged_user, $this->active_asset);
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
        // printer
      } else if ($this->request->isPrintCall()) {

        $group_by = strtolower($this->request->get('group_by', ''));
        $filter_by = $this->request->get('filter_by', null);

        $page_title = lang('Archived Files in :project_name Project', array('project_name' => $this->active_project->getName()));

        // maps
        $map = array();

        switch ($group_by) {
          case 'milestone_id':
            $map = Milestones::getIdNameMap($this->active_project);
            $map[0] = lang('Unknown Milestone');

            $getter = 'getMilestoneId';
            $page_title.= ' ' . lang('Grouped by Milestone');
            break;
          case 'category_id':
            $map = Categories::getidNameMap($this->active_project, 'AssetCategory');
            $map[0] = lang('Uncategorized');

            $getter = 'getCategoryId';
            $page_title.= ' ' . lang('Grouped by Category');
            break;
          case 'first_letter':
            $map = get_letter_map();
            $getter = 'getFirstLetter';
            $page_title.= ' ' . lang('Grouped by First Letter');
            break;
        }//switch

        // find tasks
        $assets = ProjectAssets::findForPrint($this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility(), $group_by, $filter_by);

        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map,$assets,$getter);

        $this->response->assignByRef('assets', $print_list);
        $this->response->assignByRef('map', $map);

        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Mass Edit action
     */
    function mass_edit() {
    	if ($this->getControllerName() == 'assets') {
    		$this->mass_edit_objects = ProjectAssets::findByIds($this->request->post('selected_item_ids'), STATE_ARCHIVED, $this->logged_user->getMinVisibility());
    	} // if
    	parent::mass_edit();
    } // mass_edit
    
  }
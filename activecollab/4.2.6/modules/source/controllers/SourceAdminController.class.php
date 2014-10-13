<?php

  // We need admin controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);
  
  /**
   * Manages source settings
   * 
   * @package activeCollab.modules.source
   * @subpackage controllers
   */
  class SourceAdminController extends AdminController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      require_once(SOURCE_MODULE_PATH.'/engines/subversion.class.php');
      require_once(SOURCE_MODULE_PATH.'/engines/subversionExec.class.php');
      require_once(SOURCE_MODULE_PATH.'/engines/git.class.php');
      require_once(SOURCE_MODULE_PATH.'/engines/mercurial.class.php');
      
      $this->wireframe->breadcrumbs->add('source_admin', lang('Source'),Router::assemble('admin_source'));
    } // __construct
    
    /**
     * Control panel for source module
     */
    function index() {
    	
      $this->wireframe->actions->add('add_svn_repository', lang('New Subversion Repository'), Router::assemble('admin_source_svn_repositories_add'), array(
      	'onclick' => new FlyoutFormCallback('repository_created', array('width' => 'narrow')),
      	'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface())      	
      ));
      
      $this->wireframe->actions->add('add_git_repository', lang('New Git Repository'), Router::assemble('admin_source_git_repositories_add'), array(
      	'onclick' => new FlyoutFormCallback('repository_created', array('width' => 'narrow')),
      	'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface())      	
      ));
      
      $this->wireframe->actions->add('add_mercurial_repository', lang('New Mercurial Repository'), Router::assemble('admin_source_mercurial_repositories_add'), array(
      	'onclick' => new FlyoutFormCallback('repository_created', array('width' => 'narrow')),
      	'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface())      	
      ));
      
      $source_data = array(
        'svn_path'                  => ConfigOptions::getValue('source_svn_path'),
        'svn_config_dir'            => ConfigOptions::getValue('source_svn_config_dir'),
        'svn_type'                  => ConfigOptions::getValue('source_svn_type'),
        'svn_trust_server_cert'     => ConfigOptions::getValue('source_svn_trust_server_cert'),
        'mercurial_path'            => ConfigOptions::getValue('source_mercurial_path'),
      );
      
      if ($source_data['svn_type'] !== "exec") {
        $source_data['svn_path'] = "-";
        $source_data['svn_config_dir'] = "-";
        $source_data['svn_trust_server_cert'] = "-";
      } // if
      
      $repositories_per_page = 100;
      $source_repositories = SourceRepositories::getSlice($repositories_per_page);
      
      if($this->request->get('paged_list')) {
        $exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
        $timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
        $this->response->respondWithData(SourceRepositories::getSlice($repositories_per_page, $exclude, $timestamp));
      } else {
      	$this->smarty->assign(array(
      	  'source_data' => $source_data,
      	  'repositories' => SourceRepositories::getSlice($repositories_per_page), 
      	  'repositories_per_page' => $repositories_per_page, 
      	  'total_repositories' => SourceRepositories::count(), 
        ));
      } // if
    } // index
  } // SourceAdminController
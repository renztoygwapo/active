<?php

  // Build on top of admin controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Repsite administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controller
   */
  class RepsiteAdminController extends AdminController {
    
    /**
     * Manage rep site
     */

    protected $active_repsite;
    
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('repsite_admin', lang('Manage Repsite'), Router::assemble('repsite_admin'));
      //EventsManager::trigger('on_people_tabs', array(&$this->wireframe->tabs, &$this->logged_user));
      
     
      if(get_class($this) == 'RepsiteAdminController') {
      
          $this->wireframe->actions->add('repsite_admin_add_new_page', lang('Add New Page'), Router::assemble('repsite_admin_add_new_page'), array(
            'onclick' => new FlyoutFormCallback('people_invited', array(
              'success_message' => lang('<span id="success_added">Added page</span>'),
              'width' => 1000,
            )),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'primary' => true
          ));

          $this->wireframe->actions->add('repsite_domainrepsite_domain', lang('Edit Repsite Domain'), Router::assemble('repsite_admin_edit_repsite_domain'), array(
            'onclick' => new FlyoutFormCallback('repsite_domain', array(
              'success_message' => lang('Repsite domain changed'),
              'width' => 600,
            )),
            'icon' => AngieApplication::getImageUrl('layout/button-edit.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'primary' => true
          ));
      } // if

      

      $repsite_page_id = $this->request->getId('page_id');
      if($repsite_page_id) {
        $this->active_repsite = RepsitePages::findById($repsite_page_id);

      } // if

      if($this->active_repsite instanceof RepsitePage) {

      } else {
        $this->active_repsite = new RepsitePage();
      } // if

    } // __construct


    function index() {

      $repsite_pages_per_page = 20;

      JSON::encode(RepsitePages::getSlice($repsite_pages_per_page));

      $this->smarty->assign(array(
        'repsite_pages' => RepsitePages::getSlice($repsite_pages_per_page), 
        'repsite_pages_per_page' => $repsite_pages_per_page, 
        'total_repsite_pages' => RepsitePages::count(), 
      ));
      $pages = RepsitePages::getRepsitePagesList();
      $pagelists = array();
      if(is_foreachable($pages)) {
        foreach ($pages as $page) {
          $pagelists[] = array( 'id' => lang($page['id']),
                                'name' => strlen($page['name']) > 24 ? trim(substr($page['name'],0,24)).'...' : $page['name'], 
                                'page_url' => strlen($page['page_url']) > 24 ? trim(substr($page['page_url'],0,24)).'...' : $page['page_url'], 
                                'page_html' => strlen($page['page_html']) > 24 ? trim(substr($page['page_html'],0,24)).'...' : $page['page_html'],
                                'delete_url' => Router::assemble('repsite_admin_delete_page', array('page_id' => $page['id'])),
                                'edit_url' => Router::assemble('repsite_admin_edit_page', array('page_id' => $page['id']))
                              );
        }    
      }

      $rep_site_domain = ConfigOptions::getValue('rep_site_domain');

      $this->response->assign('rep_site_domain', $rep_site_domain);
      $this->response->assign('repsite_pages', $pagelists);
       $this->flash->success('Comments have been successfully unlocked');
    } // index

    function get_page() {
      if($this->request->isApiCall()) {
        $result = array();
        if ($this->request->isSubmitted()) {
          $page_name = urldecode($this->request->post('page_name'));
          $byName = RepsitePages::findByName($page_name);
          //$this->active_repsite = RepsitePages::findById($byName['id']);

        }       
        
        $result = array(
            'name' => $byName->getName(),
            'page_html' => $byName->getPageHtml()
          ); 
        

        $this->response->respondWithData($result, array(
          'as' => 'page',
        ));
      }
    }

    function add_new_page() { 
      if($this->request->isApiCall() || $this->request->isAsyncCall()) {

        /*$this->response->respondWithData($this->active_company->getUsers(), array(
          'as' => 'users',
        ));*/
       
        if($this->request->isSubmitted()) {
          $page_data = $this->request->post('data');
          
          $page_data['page_url'] = RepsitePages::pageNameURLencode($page_data['name']);

          try {
            DB::beginWork('Adding new repsite page @ ' . __CLASS__);



            $this->active_repsite->setAttributes($page_data);
            $this->active_repsite->save();

            DB::commit('Page added @ ' . __CLASS__);

            $this->response->respondWithData($this->active_repsite, array('as' => 'repsite_admin', 'detailed' => true));

            

          } catch(Exception $e){
             DB::rollback('Failed to add PAGE @ ' . __CLASS__);
             if($this->request->isPageCall()) {
                $this->response->redirectToReferer();
              } else {
                $this->response->exception($e);
              } // if
          } // try
        }

      } else {
        $this->response->badRequest();
      } // if

    }

    function delete() {
      try {
        $this->active_repsite->delete();
        $this->response->redirectToUrl($this->active_repsite->getViewUrl());
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try
    }
    
    function edit() {
      $this->wireframe->tabs->clear();
      $this->wireframe->tabs->add('repsite', lang('Manage Repsite'), Router::assemble('repsite_admin'), null, true);
      

      if($this->request->isPageCall()) { 
        

        //var_dump($this->active_repsite->getid()); exit();
        $page_data = $this->request->post('data', array(
              'id' => $this->active_repsite->getId(),
              'name' => $this->active_repsite->getName(),
              'page_url' => $this->active_repsite->getPageUrl(),
              'page_html' => ($this->active_repsite->getPageHtml()),
            ));

        $this->response->assign(array(
              'page_data' => $page_data,
            ));

        if($this->request->isSubmitted()) {

          $page_data['page_url'] = RepsitePages::pageNameURLencode($page_data['name']);

          try {
              //  var_dump($page_data); exit();

                $this->active_repsite->setAttributes($page_data);
                $this->active_repsite->save();

                clean_menu_projects_and_quick_add_cache();
                
                $this->response->redirectToUrl($this->active_repsite->getViewUrl());
              } catch(Exception $e) {
                $this->response->exception($e);
              } // try
        } else {

        }
      } else {
        $this->response->badRequest();
      } // if


    }

    function edit_repsite_domain() {
      if($this->request->isApiCall() || $this->request->isAsyncCall()) {
        $config_opt = $this->request->post('config_opt', ConfigOptions::getValue(array('rep_site_domain')));
        /*$this->response->respondWithData($this->active_company->getUsers(), array(
          'as' => 'users',
        ));*/
        $this->response->assign(array(
          'config_opt' => $config_opt,
        ));
        if($this->request->isSubmitted()) {

          try {
            if(empty($config_opt['rep_site_domain'])) {
              $config_opt['rep_site_domain'] = null;
            } // if
          
            ConfigOptions::setValue('rep_site_domain', trim(array_var($config_opt , 'rep_site_domain')));
            $this->response->respondWithData($this->active_repsite, array('as' => 'repsite_domain', 'detailed' => true));

          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        }

      } else {
        $this->response->badRequest();
      } // if
    }

  }
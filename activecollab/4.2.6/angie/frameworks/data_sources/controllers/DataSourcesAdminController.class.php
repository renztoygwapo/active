<?php

  // We need admin controller
  AngieApplication::useController('admin');

  /**
   * Importer admin controller
   *
   * @package activeCollab.modules.importer
   * @subpackage controllers
   */
  class DataSourcesAdminController extends AdminController  {

    /**
     * @var DataSource
     */
    var $active_data_source;

    /**
     * Constructor method
     *
     * @param string $request
     */
    function __construct($request) {
      parent::__construct($request);

    } // __construct

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      $data_source_id = $this->request->get('data_source_id');
      if($data_source_id) {
        $this->active_data_source = DataSources::findById($data_source_id);
      } //if

      $this->wireframe->actions->add('data_source_admin_add', lang('New Data Source'), Router::assemble('data_source_add'), array(
        'onclick' => new FlyoutFormCallback(array('success_event'=>'data_source_created','width' => 1050)),
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
      ));

      $this->response->assign(array(
        'active_data_source' => $this->active_data_source
      ));
    } // __construct


    /**
     * Index Page
     *
     * @param void
     * @return null
     */
    function index() {
      $this->wireframe->breadcrumbs->add('data_sources', lang('Data Sources'), Router::assemble('data_sources'));

      $sources_per_page = 20;

      if($this->request->get('paged_list')) {
        $exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
        $timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;

        $this->response->respondWithData(DataSources::getSlice($sources_per_page, $exclude, $timestamp));
      } else {

        $this->response->assign(array(
          'data_sources' => DataSources::getSlice($sources_per_page),
          'data_sources_per_page' => $sources_per_page,
          'total_sources' => DataSources::count(),
        ));
      } // if
    } // index

    /**
     * View data source details
     */
    function view() {
      $this->wireframe->breadcrumbs->add('data_source', $this->active_data_source->getName(),$this->active_data_source->getViewUrl());
    } //view

    /**
     * Add Data Source
     *
     * @param void
     * @return null
     */
    function add() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        $source_data = $this->request->post('data_source', array(
          'import_settings' => Basecamp::IMPORT_SETTINGS_TODO_LIST_AS_TASK_CATEGORY,
        ));
        $this->smarty->assign(array(
          'source_data' => $source_data,
        ));
        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Add new data source @ ' . __CLASS__);

            $data_source_type = $source_data['type'];
            $this->active_data_source = new $data_source_type();
            $this->active_data_source->setAdditionalProperties(array_var($source_data, 'additional_properties'));
            $this->active_data_source->setAttributes($source_data);

            $this->active_data_source->save();
            DB::commit('New data source added @ ' . __CLASS__);
            $this->response->respondWithData($this->active_data_source, array('as' => 'data_source'));
          } catch(Error $e) {
            DB::rollback('Failed to add new data source @ ' . __CLASS__);
            $this->response->exception($e);
          }//try
        }//if
      } else {
        $this->response->badRequest();
      }//if
    } // add

    /**
     * Edit data source
     */
    function edit() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        if(!$this->active_data_source instanceof DataSource) {
          $this->response->notFound();
        } //if

        $source_data = $this->request->post('data_source', array(
            'name' => $this->active_data_source->getName(),
            'account_id' => $this->active_data_source->getAdditionalProperty('account_id') ? $this->active_data_source->getAccountId() : null,
            'password' => $this->active_data_source->getPassword(),
            'username' => $this->active_data_source->getUsername(),
            'import_settings' => $this->active_data_source->getAdditionalProperty('import_settings') ? $this->active_data_source->getImportSettings() : null,
            'import_users_in_company' => $this->active_data_source->getAdditionalProperty('import_users_in_company') ? $this->active_data_source->getAdditionalProperty('import_users_in_company') : null,
            'import_users_with_role' => $this->active_data_source->getAdditionalProperty('import_users_with_role') ? $this->active_data_source->getAdditionalProperty('import_users_with_role') : null,
          ));

        $this->smarty->assign(array(
          'source_data' => $source_data,
        ));

        if($this->request->isSubmitted()) {
          try {

            DB::beginWork('Edit data source @ ' . __CLASS__);

            $this->active_data_source->setAdditionalProperties(array_var($source_data,'additional_properties'));
            $this->active_data_source->setAttributes($source_data);
            $this->active_data_source->save();

            DB::commit('Data source edited @ ' . __CLASS__);

            $this->response->respondWithData($this->active_data_source, array(
              'as' => 'data_source',
              'detailed' => true,
            ));
          } catch (Error $e) {
            DB::rollback('Failed to edit data source @ ' . __CLASS__);
            $this->response->exception($e);
          }//try
        } //if
      } else {
        $this->response->badRequest();
      }//if
    } //edit

    /**
     * Delete data source
     */
    function delete() {
      if ($this->request->isSubmitted() && ($this->request->isAsyncCall() || $this->request->isApiCall())) {
        if ($this->active_data_source->isNew()) {
          $this->response->notFound();
        } // if

        try {
          $this->active_data_source->delete();
          $this->response->respondWithData($this->active_data_source, array('as' => 'data_source'));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } //if
    } //delete

    /**
     * Test Connection to data source
     *
     * @return mixed
     */
    function test_connection() {
      if($this->request->isAsyncCall()) {
        try {
          $source_type = $this->request->post('source_type');
          $data_source = $this->request->post('data_source');

          $this->active_data_source = new $source_type();
          if(!$this->active_data_source instanceof DataSource || !$this->active_data_source->canTestConnection()) {
            $this->response->notFound();
          } //if
          $test = $this->active_data_source->testConnection($data_source);

          $this->response->respondWithData($test);
        } catch(Error $e) {
          $this->response->exception($e);
        } //try
      } else {
        $this->response->badRequest();
      } //if
    } //test_connection


  } //DataSourcesAdminController
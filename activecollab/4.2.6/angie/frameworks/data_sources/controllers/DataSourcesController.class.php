<?php
  AngieApplication::useController('backend', SYSTEM_MODULE);

  /**
   * Class DataSourcesController
   */
  class DataSourcesController extends BackendController {

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

      $this->response->assign(array(
        'active_data_source' => $this->active_data_source
      ));
    } // __construct

    /**
     * Import from source
     *
     */
    function import() {
      if(!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } //if
      $this->response->assign(array(
        'validate_url' => $this->active_data_source->getVa
      ));

      if($this->request->isSubmitted()) {
        try {
          $params = $this->request->post('params');

          $this->active_data_source->import($params);
        } catch (Error $e) {
          $this->response->exception($e);
        } //try
      } //if

    } //import

    /**
     * Check if project/users can be imported
     */
    function validate_import() {
      if(!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } //if
      $this->response->assign(array(
        'validate_url' => $this->active_data_source->getVa
      ));

      if($this->request->isSubmitted()) {
        try {
          $params = $this->request->post('params');
          $validate = $this->active_data_source->validate_import($params);
          $this->response->respondWithData($validate);
        } catch (Error $e) {
          $this->response->exception($e);
        } //try
      } //if
    } //validate

  } //DataSourcesController
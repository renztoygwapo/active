<?php

  // Build on top of reports controller
  AngieApplication::useController('reports', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Data filters controller
   *
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwDataFiltersController extends ReportsController {

    /**
     * Selected data filter
     *
     * @var DataFilter
     */
    protected $active_data_filter;

    /**
     * Return filter class
     *
     * @return string
     */
    abstract function getFilterType();

    /**
     * Return new filter instance
     *
     * @return DataFilter
     */
    function getFilterInstance() {
      $filter_class = $this->getFilterType();
      return new $filter_class;
    } // getFilterInstance

    /**
     * Return filter ID variable name
     *
     * @return mixed
     */
    abstract function getFilterIdVariableName();

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      $data_filter_id = $this->request->getId($this->getFilterIdVariableName());
      if($data_filter_id) {
        $this->active_data_filter = DataFilters::findById($data_filter_id);

        if(!($this->active_data_filter instanceof DataFilter) || get_class($this->active_data_filter) != $this->getFilterType()) {
          $this->response->notFound();
        } // if
      } else {
        $this->active_data_filter = $this->getFilterInstance();
      } // if

      $this->response->assign('active_data_filter', $this->active_data_filter);
    } // __construct

    /**
     * Show tracking report form and options
     */
    function index() {
      $this->response->assign('saved_filters', DataFilters::findByUser($this->getFilterType(), $this->logged_user));
    } // index

    /**
     * View selected filter
     */
    function view() {
      if ($this->request->isApiCall()) {
        if ($this->active_data_filter->isLoaded()) {
          $this->response->respondWithData($this->active_data_filter, array('as' => 'data_filter'));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // view

    /**
     * Run a given report
     */
    function run() {
      if($this->request->isPrintCall() || ($this->request->isWebBrowser() && $this->request->isAsyncCall())) {
        $filter = $this->getFilterInstance();

        // Set filter parameters, and make sure that values are valid (throw Bad Request on param error)
        try {
          $filter->setAttributes($this->request->get('filter'));
        } catch(InvalidParamError $e) {
          $this->response->operationFailed(array(
            'message' => $e->getMessage(),
          ));
        } // try

        $results = null;

        try {
          $results = $filter->run($this->logged_user);
        } catch(DataFilterConditionsError $e) {
          $results = null;
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try

        if($this->request->isPrintCall()) {
          $this->response->assign(array(
            'filter' => $filter,
            'filter_name' => $filter->getName() ? $filter->getName() : lang('Custom'),
            'result' => $results,
          ));

          $additional_print_data = array();

          $filter->getAdditionalPrintData($additional_print_data);

          if(count($additional_print_data)) {
            $this->response->assign($additional_print_data);
          } // if
        } else {
          if($results) {
            $filter->resultToMap($results); // Optimize for mapping
            $this->response->respondWithMap($results);
          } else {
            $this->response->ok();
          }//if
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // run

    /**
     * Export result as CSV
     */
    function export() {
      if($this->request->isApiCall() || $this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if

      $filter = $this->getFilterInstance();

      // Set filter parameters, and make sure that values are valid (throw Bad Request on param error)
      try {
        $filter->setAttributes($this->request->get('filter'));
      } catch(InvalidParamError $e) {
        $this->response->operationFailed(array(
          'message' => $e->getMessage(),
        ));
      } // try

      $export_format = $this->request->get('export_format');

      $temp_file_path = null;

      try {
        $temp_file_path = $filter->runForExport($this->logged_user, array(
          'export_format' => $this->request->get('export_format'),
        ));
      } catch(DataFilterConditionsError $e) {
        $temp_file_path = null;
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try

      $export_file_extension = $export_format === DataFilter::EXPORT_EXCEL ? 'xlsx' : 'csv';
      $export_file_mime_type = $export_format === DataFilter::EXPORT_EXCEL ? BaseHttpResponse::EXCEL : BaseHttpResponse::CSV;

      if($temp_file_path) {
        $this->response->respondWithFileDownload($temp_file_path, $export_file_mime_type, $filter->getExportFileName() . '.' . $export_file_extension);
      } else {
        $this->response->respondWithContentDownload('', $export_file_mime_type, $filter->getExportFileName() . '.' . $export_file_extension);
      } // if
    } // export

    /**
     * Create new filter
     */
    function add() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if(DataFilters::canAdd($this->getFilterType(), $this->logged_user)) {
          try {
            $this->active_data_filter = $this->getFilterInstance();

            // Set filter parameters, and make sure that values are valid (throw Bad Request on param error)
            try {
              $this->active_data_filter->setAttributes($this->request->post('filter'));
            } catch(InvalidParamError $e) {
              $this->response->operationFailed(array(
                'message' => $e->getMessage(),
              ));
            } // try

            $this->active_data_filter->save();

            $this->response->respondWithData($this->active_data_filter, array('as' => 'data_filter'));
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add

    /**
     * Update an existing filter
     */
    function edit() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->active_data_filter->isLoaded()) {
          if($this->active_data_filter->canEdit($this->logged_user)) {
            try {

              // Set filter parameters, and make sure that values are valid (throw Bad Request on param error)
              try {
                $this->active_data_filter->setAttributes($this->request->post('filter'));
              } catch(InvalidParamError $e) {
                $this->response->operationFailed(array(
                  'message' => $e->getMessage(),
                ));
              } // try

              $this->active_data_filter->save();

              $this->response->respondWithData($this->active_data_filter, array('as' => 'data_filter'));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // edit

    /**
     * Drop an existing filter
     */
    function delete() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->active_data_filter->isLoaded()) {
          if($this->active_data_filter->canDelete($this->logged_user)) {
            try {
              $this->active_data_filter->delete();
              $this->response->respondWithData($this->active_data_filter, array('as' => 'data_filter'));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete

  }
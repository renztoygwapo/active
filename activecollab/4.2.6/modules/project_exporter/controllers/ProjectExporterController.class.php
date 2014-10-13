<?php

  // We need projects controller
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Project Exporter controller
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage controllers
   */
  class ProjectExporterController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = PROJECT_EXPORTER_MODULE;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!$this->logged_user->isAdministrator() && !$this->active_project->isLeader($this->logged_user) && !$this->logged_user->isProjectManager()) {
        $this->response->forbidden();
      } // if
      
      $this->wireframe->hidePrintButton();
    } // __before
    
    /**
     * Index - main page for project exporter
     */
    function index() {
    	$exporters = array();
    	$project_tabs = $this->active_project->getTabs($this->logged_user);
    	EventsManager::trigger('on_project_export', array(&$exporters, $this->active_project, $project_tabs->keys()));
    	
    	$this->smarty->assign(array(
    		'exporters' => $exporters,
    	    'export_exists' => (ProjectExporter::exportExists($this->active_project,PROJECT_EXPORTER_WORK_PATH)) ? Router::assemble('project_exporter_download_export',array('project_slug' => $this->active_project->getSlug())) : false
    	));
    } // index
    
    /**
     * Export project section
     */
    function export() {
    	$sections = trim($this->request->get('sections'));
    	$sections = explode(',', $sections);

  		$exporter_id = trim($this->request->get('exporter_id'));
  		if (!$exporter_id) {
  		  $this->response->notFound();
  		} // if
		
    	$exporters = array();
    	$project_tabs = $this->active_project->getTabs($this->logged_user);
    	EventsManager::trigger('on_project_export', array(&$exporters, $this->active_project, $project_tabs->keys()));
    	
    	if (!is_foreachable($exporters)) {
    	  $this->response->notFound();
    	} // if
    	
    	if (!array_key_exists($exporter_id, $exporters)) {
    	  $this->response->notFound();
    	} // if
    	
    	// find all exporters that will be used
    	$selected_exporters = array();
    	foreach ($exporters as $key => $exporter) {
    	  if (in_array($key, $sections)) {
    	    $selected_exporters[$key] = $exporter;
    	  } // if
    	} // foreach
    	
    	// Current exporter
    	$exporter = $exporters[$exporter_id];
    	$exporter_model = $exporter['exporter'];
    
    	try {
          $exporter = new $exporter_model($exporter_id, $selected_exporters);
          $exporter->setProject($this->active_project);
          $exporter->setLoggedUser($this->logged_user);
          $exporter->setObjectsVisibility($this->request->get('visibility', $this->active_project->getDefaultVisibility()));
          $exporter->setBasePath(PROJECT_EXPORTER_WORK_PATH);
          $exporter->export();
    	
  		  $this->response->respondWithData($exporter, array('as' => 'exporter'));
    	} catch (AutoloadError $e) {
    	  $this->response->exception(new Error(lang('Selected exporter not found')));
    	} catch (Exception $e) {
    	  $exporter->cleanup();
    	  $this->response->exception($e);
    	} // try
    } // export
    
    
    /**
     * Finalize project export (compressing and cleanup)
     */
    function finalize() {
    	$compress = $this->request->get('compress');
    	try {
    	  $exporter = new ProjectExporter('finalize');
    	  $exporter->setProject($this->active_project);
    	  $exporter->setLoggedUser($this->logged_user);
    	  $exporter->setObjectsVisibility($this->request->get('visibility', $this->active_project->getDefaultVisibility()));
    	  $exporter->setBasePath(PROJECT_EXPORTER_WORK_PATH);
  		  $finalize_data = $exporter->finalize($compress);
  
  		  $this->response->respondWithData($finalize_data, array('as' => 'finalize_data'));
    	} catch (AutoloadError $e) {
    	  $this->response->exception(new Error(lang('Finalization failed')));
    	} catch (Exception $e) {
    	  $exporter->cleanup();
    	  $this->response->exception($e);
    	} // try
    } // finish
    
    /**
     * Download exported data
     */
    function download() {
      $exporter = new ProjectExporter();
      $exporter->setProject($this->active_project);
      $exporter->setLoggedUser($this->logged_user);
      $exporter->setObjectsVisibility($this->request->get('visibility', $this->active_project->getDefaultVisibility()));
      $exporter->setBasePath(PROJECT_EXPORTER_WORK_PATH);
      $filename = $exporter->getCompressedPath();
      
      if (is_file($filename)) {
        $this->response->respondWithFileDownload($filename, 'application/zip', basename($filename));
      } else {
        $this->response->notFound();
      } // if
    } // download
     
  }
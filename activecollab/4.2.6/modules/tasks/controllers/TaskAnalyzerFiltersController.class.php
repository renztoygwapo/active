<?php

  // Build on top of data filters controller
  AngieApplication::useController('data_filters', SYSTEM_MODULE);

  /**
   * Task analyzers filter controller
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  abstract class TaskAnalyzerFiltersController extends DataFiltersController {

    /**
     * Load common data used by task analyzers
     */
    function index() {
      parent::index();

      $this->smarty->assign(array(
        'task_segments' => TaskSegments::getIdNameMap(),
        'companies' => Companies::getIdNameMap(null, STATE_VISIBLE),
        'projects' => Projects::getIdNameMap($this->logged_user, STATE_ARCHIVED, null, null, true),
        'active_projects' => Projects::getIdNameMap($this->logged_user, STATE_VISIBLE, null, null, true), // We need this so we can group projects in the picker
        'project_categories' => Categories::getIdNameMap(null, 'ProjectCategory'),
      ));
    } // index

  }
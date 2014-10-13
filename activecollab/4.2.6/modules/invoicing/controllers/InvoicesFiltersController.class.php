<?php

  // Build on top of data filters controller
  AngieApplication::useController('data_filters', REPORTS_FRAMEWORK_INJECT_INTO);

  /**
   * Detailed invoices filter controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  abstract class InvoicesFiltersController extends DataFiltersController {

    /**
     * Show payment report form and options
     */
    function index() {
      parent::index();

      $this->response->assign(array(
        'users' => Users::getForSelect($this->logged_user),
        'companies' => Companies::getIdNameMap(null, STATE_VISIBLE),
        'projects' => Projects::getIdNameMap($this->logged_user, STATE_ARCHIVED, null, null, true),
        'active_projects' => Projects::getIdNameMap($this->logged_user, STATE_VISIBLE, null, null, true), // We need this, so we can group projects in projects picker
        'project_categories' => Categories::getIdNameMap(null, 'ProjectCategory'),
        'currencies' => Currencies::getIdDetailsMap(),
      ));
    } // index

  }
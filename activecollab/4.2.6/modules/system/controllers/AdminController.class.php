<?php

  // Exted framework administration controller
  AngieApplication::useController('fw_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Base administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AdminController extends FwAdminController {

    /**
     * Show administration index page
     */
    function index() {
      $this->wireframe->javascriptAssign(array(
        'check_application_version_url' => AngieApplication::getCheckForUpdatesUrl(),
      ));

      parent::index();

      $this->setView(array(
        'view' => 'index',
        'controller' => 'fw_admin',
        'module' => ENVIRONMENT_FRAMEWORK,
      ));
    } // index
  }
<?php

  // Extend company profile
  AngieApplication::useController('companies', SYSTEM_MODULE);

  /**
   * Company project requests controller implementation
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class CompanyProjectRequestsController extends CompaniesController {

    /**
     * Selected project request
     *
     * @var ProjectRequest
     */
    protected $active_project_request;

    /**
     * Construct company project requests controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
    } // __construct

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if($this->active_company->isLoaded()) {
        if($this->active_company->canView($this->logged_user)) {
          if($this->logged_user->isAdministrator() || ($this->logged_user instanceof Client && $this->logged_user->canManageCompanyFinances())) {

          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // __construct

    /**
     * Show company project requests
     */
    function index() {
      $this->response->assign(array(
        'project_requests' => ProjectRequests::findByCompany($this->active_company)
      ));
    } // index

  }
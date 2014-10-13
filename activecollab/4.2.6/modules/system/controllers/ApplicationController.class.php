<?php

  // Extend FwApplication controller
  AngieApplication::useController('fw_application', ENVIRONMENT_FRAMEWORK);

  /**
   * Main activeCollab application controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  abstract class ApplicationController extends FwApplicationController {
  	
    /**
     * Owner company
     * 
     * Instance of account owner company. Script will break if owner company does 
     * not exist
     *
     * @var Company
     */
    protected $owner_company;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      // Load and init owner company
      $this->owner_company = Companies::findOwnerCompany();
      
      if($this->owner_company instanceof Company) {
        AngieApplication::cache()->set('owner_company', $this->owner_company);
      } else {
        $this->response->operationFailed(array(
          'message' => 'Owner company is not defined', 
        ));
      } // if
      
      $this->response->assign(array(
        'owner_company' => $this->owner_company,
      	'prefered_interface' => AngieApplication::getPreferedInterface(),
      ));
    } // __before

    /**
     * Return default layout
     */
    function getDefaultLayout() {
      return 'backend';
    } // getDefaultLayout
  
  }
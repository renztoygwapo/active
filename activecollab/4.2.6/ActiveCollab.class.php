<?php

  /**
   * ActiveCollab application class
   * 
   * @package activeCollab
   */
  class ActiveCollab extends AngieApplicationAdapter {
    
    /**
     * Return application name
     *
     * @return string
     */
    function getName() {
      return 'activeCollab';
    } // getName
    
    /**
     * Return application URL
     * 
     * @return string
     */
    function getUrl() {
      return 'http://www.activecollab.com';
    } // getUrl
    
    /**
     * Returns true if current application version is stable
     *
     * @return boolean
     */
    function isStable() {
      return false;
    } // isStable
    
    /**
     * Return vendor name
     * 
     * @return string
     */
    function getVendor() {
      return 'A51';
    } // getVendor
    
    /**
     * Return license agreement URL
     * 
     * @return string
     */
    function getLicenseAgreementUrl() {
      return 'http://www.activecollab.com/docs/manuals/licensing/license-agreement';
    } // getLicenseAgreementUrl

    /**
     * Return check for updates URL
     *
     * @return string
     */
    function getCheckForUpdatesUrl() {
      return AngieApplication::getRequestSchema() . 'www.activecollab.com/l/';
    } // getCheckForUpdatesUrl

    /**
     * Return download update URL
     *
     * @return string
     */
    function getDownloadUpdateUrl() {
      return AngieApplication::getRequestSchema() . 'www.activecollab.com/remote/download-activecollab' . '?me=' . APPLICATION_VERSION;
    } // getDownloadUpdateUrl
    
    /**
     * Return application API version
     *
     * @return string
     */
    function getApiVersion() {
      return '3.1.16';
    } // getApiVersion

    /**
     * Return module signature
     *
     * @return string
     */
    function getModuleSignature() {
      return '2D4xeWJpEI7p6uqf6nTNLHYweculoPe7133n8fH6';
    } // getModuleSignature
    
    /**
     * Return something that makes this application instance unique
     *
     * @return string
     */
    function getUniqueKey() {
      return APPLICATION_UNIQUE_KEY;
    } // getUniqueKey
    
    /**
     * Returns true if branding removal is part of the license
     * 
     * @return boolean
     */
    function getBrandingRemoved() {
      $config_option = ConfigOptions::getValue('license_copyright_removed');
      return isset($config_option) ? $config_option : LICENSE_COPYRIGHT_REMOVED;
    } // getBrandingRemoved

    /**
     * Return module compatibility link
     *
     * @param AngieModule $module
     * @param boolean $module_declared_internal
     * @return string
     */
    function getCompatibilityLink(AngieModule $module, $module_declared_internal = false) {
      return 'https://www.activecollab.com/module-compatibility?name=' . clean($module->getName()) . '&version=' . clean($module->getVersion()) . '&appv=' . clean($this->getVersion()) . '&internal=' . ($module_declared_internal ? 1 : 0);
    } // getCompatibilityLink
    
    // ---------------------------------------------------
    //  First run
    // ---------------------------------------------------
    
    /**
     * Do operations that require entire system to be loaded, but are executed 
     * on the first system run
     */
    function onFirstRun() {
      if(AngieApplication::isModuleLoaded('invoicing')) {
        $admin = Users::findById(1);

        if($admin instanceof Administrator) {
          $admin->setSystemPermissions(array('can_manage_finances', 'can_manage_quotes'));
          $admin->save();
        } // if
      } // if
    } // onFirstRun
    
  }
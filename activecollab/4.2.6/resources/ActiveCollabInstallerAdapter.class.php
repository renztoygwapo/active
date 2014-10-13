<?php

  /**
   * activeCollab installer adapter
   * 
   * @package activeCollab.resources
   */
  class ActiveCollabInstallerAdapter extends AngieApplicationInstallerAdapter {
  
    /**
     * Construct installer adapter
     */
    function __construct() {
      $this->setMinPHPVersion('5.3.3');
      $this->setMinMemory(64);
      $this->setMinMySQLVersion('5.0');
      $this->setDefaultTablePrefix('acx_');
      
      $this->addRecommendedPhpExtension('svn', 'faster interaction with SVN repositories');
      
      $this->addWritableFolder(array(
        'activecollab',
        'public/assets',
        'public/avatars',
        'public/brand',
        'public/logos',
        'public/notebook_covers',
        'public/projects_icons',
      ));
    } // __construct
    
    /**
     * Return installer sections
     * 
     * @return array
     */
    function getSections() {
      return array_merge(parent::getSections(), array(
        'finish' => 'Finish', 
      ));
    } // getSections
    
    /**
     * Render initial section content
     * 
     * @param string $name
     * @return string
     */
    function getSectionContent($name) {
      switch($name) {
        case 'finish':
          return '<p>Done, you have successfully installed activeCollab!</p>' . 
          '<p><button type="button" id="application_installer_done">Log in Now!</button></p>' . 
          '<script type="text/javascript">
            $("#application_installer_done").click(function() { window.location.reload(); $(this).prop("disabled", true) });
          </script>';
        default:
          return parent::getSectionContent($name);
      } // switch
    } // getSectionContent
    
    /**
     * Return a list of modules that need to be installed
     * 
     * @return array
     */
    function getModulesToInstall() {
      $modules = parent::getModulesToInstall();

      // Turn off to do in favor of tasks module
      $todo = array_search('todo', $modules);
      $tasks = array_search('tasks', $modules);
      
      if($todo !== false && $tasks !== false) {
        unset($modules[$todo]);
      } // if

      // Make sure that invoicing module is installed after tracking module
      $invoicing = array_search('invoicing', $modules);
      $tracking = array_search('tracking', $modules);

      if($invoicing !== false && $tracking !== false && $invoicing < $tracking) {
        unset($modules[$invoicing]);
        $modules[] = 'invoicing';
      } // if
      
      return $modules;
    } // getModulesToInstall
    
    /**
     * Return prepared admin params
     * 
     * @param array $from
     * @return array
     */
    function getAdminParams($from) {
      $params = parent::getAdminParams($from);
      
      if(!isset($params['company_name'])) {
        $params['company_name'] = '';
      } // if
      
      return $params;
    } // getAdminParams
    
    /**
     * Create administrator user account
     * 
     * This function returns administrator's user ID
     * 
     * @param string $email
     * @param string $password
     * @param mixed $other_params
     * @return integer
     */
    function createAdministrator($email, $password, $other_params = null) {
      $admin_id = parent::createAdministrator($email, $password, $other_params);
      
      if(isset($other_params['company_name'])) {
        $company_name = trim($other_params['company_name']);
        
        if($company_name) {
          DB::execute('UPDATE ' . TABLE_PREFIX . 'companies SET name = ? WHERE id = ?', $company_name, 1);
        } // if
      } // if
      
      return $admin_id;
    } // createAdministrator
    
  }
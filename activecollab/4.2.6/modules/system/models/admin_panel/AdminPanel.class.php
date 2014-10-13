<?php

  /**
   * Application administration panel
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  final class AdminPanel extends FwAdminPanel {
  
    /**
     * Construct administration panel
     *
     * @param User $user
     */
    function __construct(User $user) {
      parent::__construct($user);
      
      $this->defineRow('projects', new ToolsAdminPanelRow(lang('Projects')), array('before' => 'tools'));
      $this->defineRow('other', new ToolsAdminPanelRow(lang('Other')), array('after' => 'tools'));
      $this->defineRow('invoicing', new ToolsAdminPanelRow(lang('Invoicing')), array('after' => 'other'));
    } // __construct
    
    /**
     * Add a tool to the list of other tools
     * 
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param mixed $options
     */
    function addToProjects($name, $title, $url, $icon_url, $options = null) {
      $this->addTo('projects', $name, $title, $url, $icon_url, $options);
    } // addToProjects
    
    /**
     * Add a tool to the list of other tools
     * 
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param mixed $options
     */
    function addToOther($name, $title, $url, $icon_url, $options = null) {
      $this->addTo('other', $name, $title, $url, $icon_url, $options);
    } // addToOther
    
    /**
     * Add a tool to the list of invoicing tools
     * 
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $icon_url
     * @param mixed $options
     */
    function addToInvoicing($name, $title, $url, $icon_url, $options = null) {
      $this->addTo('invoicing', $name, $title, $url, $icon_url, $options);
    } // addToInvoicing

    // ---------------------------------------------------
    //  Rows
    // ---------------------------------------------------

    /**
     * Return system information row
     *
     * @return IAdminPanelRow
     */
    protected function getSystemInformationRow() {
      if(AngieApplication::isOnDemand()) {
        if (OnDemand::isAccountOwner($this->user)) {
          return OnDemand::getSystemInformationRow();
        } // if
      } else {
        return new SystemInfoAdminPanelRow();
      } // if
    } // getSystemInformationRow
    
  }
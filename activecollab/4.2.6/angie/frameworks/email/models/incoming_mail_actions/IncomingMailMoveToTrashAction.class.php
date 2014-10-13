<?php
  
  /**
   * Incoming mail action - Move to trash
   * 
   * @package angie.framework.email
   * @subpackage models.incoming_mail_actions
   */
  class IncomingMailMoveToTrashAction extends IncomingMailAction {
   
    /**
     * Constructor
     */ 
    function __construct() {
      $this->setActionClassName(__CLASS__);
      $this->setSettings();
    }//__construct
    
    /**
     * Set settings as name, descriptions..
     */
    public function setSettings() {
      $this->setName(lang('Move to trash'));
      $this->setDescription(lang('Move incoming mail to trash.'));
      $this->setModuleName(EMAIL_FRAMEWORK);
    }//setSettings
    
    /**
     * Do actions over incoming email
     *
     * @params $incoming_mail 
     * @params $additional_settings
     */
    public function doActions(IncomingMail $incoming_email, $additional_settings = false, $force = false) {
      $this->checkActionsParameters($incoming_email,$additional_settings);
      
      return $incoming_email->delete();
    }//doActions
    
  }

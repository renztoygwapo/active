<?php
  
  /**
   * Incoming mail action - Ignore
   * 
   * @package angie.framework.email
   * @subpackage models.incoming_mail_actions
   *
   */
  class IncomingMailIgnoreAction extends IncomingMailAction {
    
    /**
     * Required additional settings needed for actions
     * 
     * @var array
     */
     protected $required_additional_settings = array();
    
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
      $this->setName(lang('Ignore'));
      $this->setDescription(lang('Ignore incoming mail.'));
      //$this->setTemplateName('incoming_mail_ignore_action');
      $this->setModuleName(EMAIL_FRAMEWORK);
    } // setSettings
    
    /**
     * Do actions over incoming email
     *
     * @params $incoming_mail 
     * @params $additional_settings
     */
    public function doActions(IncomingMail $incoming_email, $additional_settings = false, $force = false) {
      $this->checkActionsParameters($incoming_email,$additional_settings);
      
      return true;
    } // doActions
    
  }
<?php
  /**
   * Incoming mail action abstract class
   * 
   * @package angie.framework.email
   * @subpackage modules
   *
   */
  abstract class IncomingMailAction {

    /**
     * Additional smarty template params
     *
     * @var array
     */
    protected $action_tpl_params;

    /**
     * Action class name
     * 
     * @var string
     */
    protected $action_class_name;
    
    /**
     * Action name
     * 
     * @var string
     */
    protected $name;
    
    /**
     * Action description
     * 
     * @var string
     */
    protected $description;
  
    /**
     * Module name
     * 
     * @var string
     */
    protected $module_name;
    
    /**
     * Path to template 
     * 
     * @var string
     */
    protected $template_folder = "incoming_mail_actions";
      
    /**
     * Action template name
     * 
     * @var string
     */
    protected $template_name;
    
    /**
     * Additional settings from action form that will be done with matched incoming mail
     * 
     * @var mixed
     */
    protected $additional_settings = false;

    /**
     * Required additional settings needed for actions
     * 
     * @var array
     */
    protected $required_additional_settings;

    /**
     * Can action be used in new filter page
     * 
     * @var $can_use boolean
     */
    protected $can_use = true;
    
    /**
     * Is pre-selected in action form
     * 
     * @var boolean
     */
    protected $pre_selected = false;
    
    /**
     * Constructor
     */
    function __construct() {
      
    } //__construct

    /**
     * Set additional smarty tpl params
     *
     * @param $params
     */
    public function setParams($params) {
      $this->action_tpl_params = $params;
    } //setParams

    /**
     * Get additional smarty tpl params
     *
     * @return array
     */
    public function getParams() {
      return $this->action_tpl_params;
    } //setParams

    /**
     * Check additional settings existance
     */
    function validateAdditionalSettings() {
      if($this->additional_settings) {
        foreach($this->required_additional_settings as $key => $field) {
          if(!$this->additional_settings[$field]) {
            throw new Error('Validation error');
          }//if
        }//foreach
        return true;
      } //if 
    }//validateAdditionalSettings

    /**
     * Check parameters before action
     *
     * @param $incoming_email
     * @param $additional_settings
     * @return bool
     * @throws InvalidParamError
     * @throws InvalidInstanceError
     */
    function checkActionsParameters($incoming_email, $additional_settings) {
      if(!($incoming_email instanceof IncomingMail)) {
        throw new InvalidInstanceError('incoming_email', $incoming_email, 'IncomingMail');
      } // if
      
      if(!is_array($additional_settings) && $additional_settings) {
        throw new InvalidParamError('additional_settings', $additional_settings, 'Additional settings is not array');
      } // if

      $this->additional_settings =  $additional_settings;
      $this->validateAdditionalSettings();

      return true;
    } //if
    
    /*
     * Return CC and BCC users
     * 
     * @return array
     */
    function getUsersToSubscribe(IncomingMail $incoming_email, $additional_settings) {
      
      $subscribe_users = array();
      
       //add subscribers from filter action
      if($additional_settings['subscribers']) {
        if(is_foreachable($additional_settings['subscribers'])) {
          foreach ($additional_settings['subscribers'] as $k => $id) {
            $subscribe_users[] = Users::findById($id); 
          }//foreach
        }//if
      }//if
      
      //add subscribers from filter action
      if($additional_settings['notify_users']) {
        if(is_foreachable($additional_settings['notify_users'])) {
          foreach ($additional_settings['notify_users'] as $k => $id) {
            $subscribe_users[] = Users::findById($id); 
          }//foreach
        }//if
      }//if
      
      //subsribe users from cc
      if($incoming_email->getCcTo() && $additional_settings['notify_sender']) {
        $cc_to = $incoming_email->getCcTo();
        foreach($cc_to as $key => $cc_user) {
          $is_mailbox = IncomingMailboxes::findByEmail($cc_user['email']);
          //check to see if mailbox email is added to cc
          if(!$is_mailbox instanceof IncomingMailbox) {
            $cc_to_user = Users::findByEmail($cc_user['email'], true);
            if($cc_to_user instanceof IUser) {
              $subscribe_users[] = $cc_to_user; 
            } else {
              $subscribe_users[] = new AnonymousUser($cc_user['name'] ? $cc_user['name'] : $cc_user['email'] ,$cc_user['email']);
            }//if
          }//if
        }//foreach
      }//if
      
      //subsribe users from bcc
      if($incoming_email->getBccTo()){
        $bcc_to = $incoming_email->getBccTo();
        foreach($bcc_to as $key => $bcc_user) {
          $bcc_to_user = Users::findByEmail($bcc_user['email'], true);
          if($bcc_to_user instanceof User) {
            $subscribe_users[] = $bcc_to_user; 
          } else {
            $subscribe_users[] = new AnonymousUser($bcc_user['name'] ? $bcc_user['name'] : $bcc_user['email'] ,$bcc_user['email']);
          }//if
        }//foreach
      }//if
      
      return $subscribe_users;
      
    }//getConversationUsers

//==========================Abstract methods ============================//  
    
    /**
     * Set fileds
     */
    abstract function setSettings();
    
    /**
     * Do actions over incoming email
     * 
     * @params $incoming_mail 
     * @params $additional_settings
     * 
     */
    abstract function doActions(IncomingMail $incoming_email, $additional_settings = false, $force = false);
    
//==========================End Abstract methods ============================//  
//==========================Getters and setters ============================//
    /**
     * Get name
     */
    function getName() {
      return $this->name;
    } //getName
    
    /**
     * Set name
     * 
     * @param $value
     */
    function setName($value) {
      return $this->name = $value;
    } //setName
    
    /**
     * Get description
     */
    function getDescription() {
      return $this->description;
    } //getDescription
    
    /**
     * Set description 
     * 
     * @param $value
     */
    function setDescription($value) {
      return $this->description = $value;
    } //setDescription
    
    /**
     * Get template name
     */
    function getTemplateName() {
      return $this->template_name;
    } //getTemplateName
    
    /**
     * Set template name
     *  
     * @param $value
     */
    function setTemplateName($value) {
      return $this->template_name = $value;
    } //setTemplateName
    
    /**
     * Get template folder
     */
    function getTemplateFolder() {
      return $this->template_folder;
    } //getTemplateFolder
    
    /**
     * Set template folder
     *  
     * @param $value
     */
    function setTemplateFolder($value) {
      return $this->template_folder = $value;
    } //setTemplateFolder

    /**
     * Get module_name
     */
    function getModuleName() {
      return $this->module_name;
    } //getTemplateFolder
    
    /**
     * Set module_name
     *  
     * @param $value
     */
    function setModuleName($value) {
      return $this->module_name = $value;
    } //setModuleName
    
    
     /**
     * Get additional_settings
     */
    function getAdditionalSettings() {
      return $this->additional_settings;
    } //getAdditionalSettings
    
    /**
     * Set additional_settings
     *  
     * @param $value
     */
    function setAdditionalSettings($value) {
      return $this->additional_settings = $value;
    } //setAdditionalSettings
    
     /**
     * Get action_class_name
     */
    function getActionClassName() {
      return $this->action_class_name;
    } //getActionClassName
    
    /**
     * Set action_class_name
     *  
     * @param $value
     */
    function setActionClassName($value) {
      return $this->action_class_name = $value;
    } //setActionClassName
    
    /**
     * Get can_use
     */
    function getCanUse() {
      return $this->can_use;
    } //getCanUse
    
    /**
     * Set can_use
     *  
     * @param $value
     */
    function setCanUse($value) {
      return $this->can_use = $value;
    } //setCanUse
    
    /**
     * Get $pre_selected
     */
    function getPreSelected() {
      return $this->pre_selected;
    } //getPreSelected
    
    /**
     * Set $pre_selected
     *  
     * @param $value
     */
    function setPreSelected($value) {
      return $this->pre_selected = $value;
    } //setPreSelected
    
//==========================END Getters and setters ============================//   

  }//IncomingMailAction
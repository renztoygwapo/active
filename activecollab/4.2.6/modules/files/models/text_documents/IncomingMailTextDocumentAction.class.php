<?php
  
  /**
   * Incoming mail action - Add new text document
   * 
   * @package modules.files
   * @subpackage models
   */
  class IncomingMailTextDocumentAction extends IncomingMailProjectObjectAction {
    
    /**
     * Required additional settings needed for actions
     * 
     * @var array
     */
     protected $required_additional_settings = array('project_id');
    
    /*
     * Constructor
     */
    function __construct() {
      $this->setActionClassName(__CLASS__);
      $this->setSettings();
    }//__construct

    /**
     * Render project elements into filter form
     *
     */
    function renderProjectElements(IUser $user, Project $project, IncomingMailFilter $filter = null) {
      if($filter instanceof IncomingMailFilter) {
        //set initial values from this array
        $this->action_parameters = $filter->getActionParameters();
      }//if
      $object = new TextDocument();
      $object->setProject($project);
      $this->addCategorySelect($user, 'AssetCategory', $project);
      $this->addNotifyElement($user,$object);
      $this->addMilestoneSelect($user, $project);
      return $this->elements;
    }//renderProjectElements
    
    /**
     * Set settings as name, descriptions..
     */
    public function setSettings() {
      $this->setName(lang('Add New Text Document'));
      $this->setDescription(lang('Add new text document to specific project.'));
      $this->setTemplateName('incoming_mail_add_text_document_action');
      $this->setCanUse(true);
      $this->setModuleName(FILES_MODULE);
      $this->setPreSelected(false);
    }//setSettings
    
    /**
     * Do actions over incoming email
     *
     * @params $incoming_mail 
     * @params array $additional_settings
     * 
     */
    public function doActions(IncomingMail $incoming_email, $additional_settings = false, $force = false) {
      //check all parameters
      $this->checkActionsParameters($incoming_email,$additional_settings);
      
      //========= put custom actions here ==============//
   
      $project_id = $additional_settings['project_id'];
      $project = Projects::findById($project_id);
     
      if(!$project instanceof Project || $project->getState() == STATE_DELETED) {
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_PROJECT_DOES_NOT_EXISTS);
      }//if
      
       //get users from cc and bcc and users from filter subscribers,notify_people
      $subscribe_users = $this->getUsersToSubscribe($incoming_email,$additional_settings);
      //subscribe leader
      $subscribe_users[] = $project->getLeader();
      
      //get sender
      if(!$incoming_email->getCreatedById() == 0) {
        $sender = Users::findById($incoming_email->getCreatedById());
      } else {
        //if anonymous user creates document
        $sender = new AnonymousUser($incoming_email->getCreatedByName(),$incoming_email->getCreatedByEmail());
      }//if
      
      //is create as option chosen set specific user else set sender as creator
      $create_as = $additional_settings['create_as'];
      
      if($create_as == IncomingMailFilter::CREATE_AS_SPECIFIC_USER) {
        $create_as_user_id = $additional_settings['create_as_user'];
        $create_as_user = Users::findById($create_as_user_id);
        $subscribe_users[] = $create_as_user;  
      } else {
        $create_as_user = $sender; 
      }//if
      
      $object = new TextDocument();
      
      //check to see if user can add task to project
      $allow_for_everyone = $additional_settings['allow_for_everyone'];
      if($allow_for_everyone == IncomingMailFilter::ALLOW_FOR_PEOPLE_WHO_CAN) {
        if(!TextDocuments::canAdd($sender,$project) ) {
          throw new Error(IncomingMessageImportErrorActivityLog::ERROR_USER_CANNOT_CREATE_OBJECT);
        } //if
      } //if
      
      //set basic values
      $object->setProjectId($project->getId());
      $object->setCreatedBy($create_as_user);
      $object->setCreatedOn($incoming_email->getCreatedOn());
      $object->setVisibility($project->getDefaultVisibility());
      $object->setState(STATE_VISIBLE);
      $object->setName(substr($incoming_email->getSubject(),0,150));
      $object->setBody($incoming_email->getBody());
      $object->setMilestoneId($additional_settings['milestone_id']);

      //Set additional settings 
      $object->setCategoryId($additional_settings['category_id']);

      //attach files from mail to task
      $this->attachFilesToProjectObject($incoming_email, $object);

      //save object
      $object->save();
      
      //notify sender and subscribe him
      $notify_sender = $additional_settings['notify_sender'];
      if($notify_sender) {

        $subscribe_users[] = $sender;
        
        $additional = array(
        	'exclude' => $sender
        );
        AngieApplication::notifications()
          ->notifyAbout(EMAIL_FRAMEWORK_INJECT_INTO . '/notify_email_sender', $object)
          ->sendToUsers($sender);
      }//if

      $object->subscriptions()->set($subscribe_users, true);
      
      // notify all subscribers (except sender)
      if($object->subscriptions()->hasSubscribers()) {
        AngieApplication::notifications()
          ->notifyAbout('files/new_text_document', $object, $sender)
          ->sendToSubscribers();
      }//if
      
      return $object;
  
      //=========== end ================================//
    }//doActions
  
  }
<?php
  
  /**
   * Incoming mail action - Add Discussion
   * 
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class IncomingMailDiscussionAction extends IncomingMailProjectObjectAction {
    
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
     * Render project elements into action form
     * 
     */
    function renderProjectElements(IUser $user, Project $project, IncomingMailFilter $filter = null) {
      if($filter instanceof IncomingMailFilter) {
        //set initial values from this array
        $this->action_parameters = $filter->getActionParameters();
      }//if
      $object = new Discussion();
      $object->setProject($project);
      $this->addCategorySelect($user,'DiscussionCategory',$project);
      $this->addNotifyElement($user,$object);
      $this->addMilestoneSelect($user, $project);
      return $this->elements;
    }//renderProjectElements
    
    
    
    /**
     * Set settings as name, descriptions..
     */
    public function setSettings() {
      $this->setName(lang('Add New Discussion'));
      $this->setDescription(lang('Add new discussion to specific project.'));
      $this->setTemplateName('incoming_mail_add_discussion_action');
      $this->setCanUse(true);
      $this->setModuleName(DISCUSSIONS_MODULE);
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
   
      $project_id = $additional_settings['project_id'];
      $project = Projects::findById($project_id);
     
      if(!$project instanceof Project || $project->getState() == STATE_DELETED) {
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_PROJECT_DOES_NOT_EXISTS);
      }//if
      
      //get users from cc and bcc and users from filter subscribers,notify_people
      $subscribe_users = $this->getUsersToSubscribe($incoming_email,$additional_settings);
      $subscribe_users[] = $project->getLeader();
      
      //get sender
      if(!$incoming_email->getCreatedById() == 0) {
        $sender = Users::findById($incoming_email->getCreatedById());
      } else {
        //if anonymous user creates task
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
   
      $discussion = new Discussion();
      
      //check to see if user can add task to project
      $allow_for_everyone = $additional_settings['allow_for_everyone'];
      if($allow_for_everyone == IncomingMailFilter::ALLOW_FOR_PEOPLE_WHO_CAN) {
          if(!Discussions::canAdd($sender,$project) ) {
            throw new Error(IncomingMessageImportErrorActivityLog::ERROR_USER_CANNOT_CREATE_OBJECT);
          } //if
        } //if
      
      //set basic values
      $discussion->setProjectId($project->getId());
      $discussion->setCreatedBy($create_as_user);
      $discussion->setCreatedOn($incoming_email->getCreatedOn());
      $discussion->setVisibility($project->getDefaultVisibility());
      $discussion->setState(STATE_VISIBLE);
      $discussion->setSource(OBJECT_SOURCE_EMAIL);
      $discussion->setName(substr($incoming_email->getSubject(),0,150));
      $discussion->setBody($incoming_email->getBody());
      $discussion->setMilestoneId($additional_settings['milestone_id']);  
        
      //Set additional settings 
      $discussion->setCategoryId($additional_settings['category_id']);
           
      //attach files from mail to task
      $this->attachFilesToProjectObject($incoming_email, $discussion);

      //save object
      $discussion->save();
      
      //notify sender and subscribe him
      $notify_sender = $additional_settings['notify_sender'];
      if($notify_sender) {
        
        $create_public_page = $additional_settings['create_public_page'];
        if($create_public_page) {
          $share_parameters = array(
            'comments_enabled' => true,
            'comment_reopens' => true
          );
          $discussion->sharing()->share($create_as_user, $share_parameters, false);
        }//if
         
        $subscribe_users[] = $sender;
        
        $additional = array(
        	'exclude' => $sender
        );

        AngieApplication::notifications()
          ->notifyAbout(EMAIL_FRAMEWORK_INJECT_INTO . '/notify_email_sender', $discussion)
          ->sendToUsers($sender);
      }//if
      
      $discussion->subscriptions()->set($subscribe_users, true);
      
      if($discussion->subscriptions()->hasSubscribers()) {
        AngieApplication::notifications()
          ->notifyAbout('discussions/new_discussion', $discussion, $sender)
          ->sendToSubscribers();
      }//if
      
      return $discussion;
    }//doActions
  
  }

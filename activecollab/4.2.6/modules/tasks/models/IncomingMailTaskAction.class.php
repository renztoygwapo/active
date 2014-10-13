<?php
  
  /**
   * Incoming mail action - Add task
   * 
   * @package angie.framework.email
   * @subpackage models.incoming_mail_actions
   */
  class IncomingMailTaskAction extends IncomingMailProjectObjectAction {
    
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
      
      $object = new Task();
      $object->setProject($project);
      $this->addCategorySelect($user, 'TaskCategory', $project);
      $this->addLabelSelect($user, 'AssignmentLabel');
      $this->addAssigneeElement($user, $object);
      $this->addMilestoneSelect($user, $project);
      return $this->elements;
    }//renderProjectElements
    
    /**
     * Set settings as name, descriptions..
     */
    public function setSettings() {
      $this->setName(lang('Add New Task'));
      $this->setDescription(lang('Add new task to specific project.'));
      $this->setTemplateName('incoming_mail_add_task_action');
      $this->setCanUse(true);
      $this->setModuleName(TASKS_MODULE);
      $this->setPreSelected(true);
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
      
      $task = new Task();
      
      //check to see if user can add task to project
      $allow_for_everyone = $additional_settings['allow_for_everyone'];
      if($allow_for_everyone == IncomingMailFilter::ALLOW_FOR_PEOPLE_WHO_CAN) {
        if(!Tasks::canAdd($sender,$project) ) {
          throw new Error(IncomingMessageImportErrorActivityLog::ERROR_USER_CANNOT_CREATE_OBJECT);
        } //if
      } //if
      
      //set basic values
      $task->setProjectId($project->getId());
      $task->setCreatedBy($create_as_user);
      $task->setCreatedOn($incoming_email->getCreatedOn());
      $task->setVisibility($project->getDefaultVisibility());
      $task->setState(STATE_VISIBLE);
      $task->setSource(OBJECT_SOURCE_EMAIL);
      $task->setName(substr($incoming_email->getSubject(),0,150));
      $task->setBody($incoming_email->getBody());
      $task->setMilestoneId($additional_settings['milestone_id']);

      //set due on
      if($additional_settings['due_on'] == IncomingMailFilter::DUE_ON_MESSAGE_RECEIVED) {
        $due_on = $incoming_email->getCreatedOn();
      } elseif ($additional_settings['due_on'] == IncomingMailFilter::DUE_ON_NEXT_BUSSINESS_DAY) {
        $due_on = $incoming_email->getCreatedOn()->advance(86400, false);
        while ($due_on->isWorkday() === false) : $due_on->advance(86400, true); endwhile;
      }//if
      $task->setDueOn($due_on);

      //do we use message priority or users custom
      $use_message_priority = $additional_settings['use_message_priority'];
      if($use_message_priority == IncomingMailFilter::USE_MESSAGE_PRIORITY) {
        $priority = $incoming_email->getPriority() == IncomingMailFilter::IM_FILTER_IMPORTANT ? PRIORITY_HIGHEST : PRIORITY_NORMAL;
      } else {
        $priority = $additional_settings['priority']; 
      } //if
      $task->setPriority($priority);
      
      //Set additional settings 
      $task->setCategoryId($additional_settings['category_id']);
      
      
      //responsible person
      if($additional_settings['assignee_id']) {
        $assignee = Users::findById($additional_settings['assignee_id']);
        $task->assignees()->setAssignee($assignee);
        if($task->assignees()->getSupportsMultipleAssignees() && $additional_settings['other_assignees']) {
          $task->assignees()->setOtherAssignees($additional_settings['other_assignees']);
        }//if
      }//if
      
      $task->setLabelId($additional_settings['label_id']);
      
      //attach files from mail to task
      $this->attachFilesToProjectObject($incoming_email, $task);

      //save object
      $task->save();
      
      //notify sender and subscribe him
      $notify_sender = $additional_settings['notify_sender'];
      if($notify_sender) {
        
        $create_public_page = $additional_settings['create_public_page'];
        if($create_public_page) {
          $share_parameters = array(
            'comments_enabled' => true,
            'comment_reopens' => true
          );
          $task->sharing()->share($create_as_user, $share_parameters, false);
        }//if
         
        $subscribe_users[] = $sender;
        
        $additional = array(
        	'exclude' => $sender
        );

        AngieApplication::notifications()
          ->notifyAbout(EMAIL_FRAMEWORK_INJECT_INTO . '/notify_email_sender', $task)
          ->sendToUsers($sender);
      }//if
      
      $task->subscriptions()->set($subscribe_users, true);
      
      // notify all subscribers (except sender)
      if($task->subscriptions()->hasSubscribers()) {
        AngieApplication::notifications()
          ->notifyAbout('tasks/new_task', $task, $sender)
          ->sendToSubscribers();
      }//if
      
      return $task;
  
      //=========== end ================================//
    }//doActions
  
  }
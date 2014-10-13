<?php

  /**
   * Project object action
   *
   * @package angie.framework.angie.email
   * @subpackage models
   */
  abstract class IncomingMailProjectObjectAction extends IncomingMailAction {
    
    /**
     * Action form elements
     * 
     * @var string
     */
    protected $elements;
    
    /**
     * If edit mode and filter_id is sent, take pre-defined action parameters and set the initial value for elements
     * 
     * @var $action_parameters array
     */
    protected $action_parameters;
  
    /**
     * Render action project elements
     * 
     */
    abstract function renderProjectElements(IUser $user, Project $project, IncomingMailFilter $filter = null);


    /**
     * Add object select to elements
     *
     * @param IUser $user
     * @param Project $project
     */
    function addObjectByTypeSelect(IUser $user, Project $project) {

      AngieApplication::useHelper('select_object_by_type', EMAIL_FRAMEWORK);

      $params = array(
        'project' => $project,
        'user'  => $user,
        'label' => lang('On a'),
        'type_value' => array_var($this->action_parameters,'type_name',IncomingMailCommentAction::ADD_ON_TASK,true),
        'type_name' => 'filter[action_parameters][type_name]',
        'object_id' => array_var($this->action_parameters,'object_id',null,true),
        'name' => 'filter[action_parameters][object_id]',
      );
      $object_select = smarty_function_select_object_by_type($params);
      $this->elements .=  $object_select;

    }//addCategorySelect

    /**
     * Add category select to elements
     *
     * @param IUser $user
     * @param $category_type
     * @param Project $project
     */
    function addCategorySelect(IUser $user, $category_type, Project $project) {

        $category_params = array(
          'parent' => $project,
          'user' => $user,
          'value' => array_var($this->action_parameters,'category_id',null,true),
          'label' => 'In Category',
          'type' => $category_type,
          'name' => 'filter[action_parameters][category_id]',
          'id' => 'category_id',
          'can_create_new' => false
        );
        AngieApplication::useHelper('select_category',CATEGORIES_FRAMEWORK);
        //category
        $category_select = smarty_function_select_category($category_params);
        $this->elements .= $category_select;

      }//addCategorySelect

    /**
     * Add label drop down
     *
     * @param IUser $user
     * @param $label_type
     */
    function addLabelSelect(IUser $user, $label_type) {

        $label_params = array(
          'user' => $user,
          'value' => array_var($this->action_parameters,'label_id',null,true),
          'label' => 'Label',
          'type' => $label_type,
          'name' => 'filter[action_parameters][label_id]',
          'can_create_new' => false
        );

        AngieApplication::useHelper('select_label',LABELS_FRAMEWORK);
        //label
        $label_select = smarty_function_select_label($label_params);
        $this->elements .= $label_select;

      }//addLabelSelect

    /**
     * Add assignee element
     *
     * @param IUser $user
     * @param IAssignees $object
     */
    function addAssigneeElement(IUser $user, IAssignees $object) {

        AngieApplication::useHelper('select_assignees',ASSIGNEES_FRAMEWORK);

        $assignees_params = array(
          'object' => $object,
          'user' => $user,
          'value' => array_var($this->action_parameters,'assignee_id',null,true),
          'label' => 'Assignee',
          'choose_responsible' => true,
          'choose_subscribers' => true,
          'inline' => true,
          'name' => 'filter[action_parameters]',
          'can_create_new' => false,
          'id' => 'assignee_id',
          'other_assignees' => array_var($this->action_parameters,'other_assignees',null,true),
          'subscribers' => array_var($this->action_parameters,'subscribers',null,true)
        );

        AngieApplication::useHelper('label',ENVIRONMENT_FRAMEWORK,'block');
        $assignee_category_select .= smarty_block_label(array('for' => 'assignee_id'),'Assignees');

        $assignee_category_select .= smarty_function_select_assignees($assignees_params);

        $this->elements .= $assignee_category_select;

      }//addAssigneeElement

    /**
     * Add notify people element
     *
     * @param IUser $user
     * @param ISubscriptions $object
     *
     */
    function addNotifyElement(IUser $user, ISubscriptions $object) {
      $notify_params = array(
        'object' => $object,
        'user' => $user,
        'value' => array_var($this->action_parameters,'notify_users',null,true),
        'label' => 'Notify People',
        'inline' => true,
        'name' => 'filter[action_parameters][notify_users]',
      );

      AngieApplication::useHelper('select_subscribers',SUBSCRIPTIONS_FRAMEWORK);
      //label
      $notify_select = smarty_function_select_subscribers($notify_params);
      $this->elements .= $notify_select;
    }//addLabelSelect

    /**
     * Add select milestone drop down
     *
     * @param IUser $user
     * @param Project $project
     */
    function addMilestoneSelect(IUser $user, Project $project) {
      $milestone_params = array(
        'project' => $project,
        'user' => $user,
        'value' => array_var($this->action_parameters,'milestone_id',null,true),
        'label' => 'Milestone',
        'name' => 'filter[action_parameters][milestone_id]',
      );

      AngieApplication::useHelper('select_milestone',SYSTEM_MODULE);
      //label
      $milestone_select = smarty_function_select_milestone($milestone_params);
      $this->elements .= $milestone_select;

    }//addMilestoneSelect

    /**
     * Attach files from incoming mail to $project_object
     *
     * @param IncomingMail $incoming_mail
     * @param ProjectObject $project_object
     * @throws Error
     * @throws InvalidInstanceError
     */
    function attachFilesToProjectObject(&$incoming_mail, &$project_object) {
      if(!DiskSpace::canImportEmailBasedOnDiskLimitation($incoming_mail)) {
        throw new Error(IncomingMessageImportErrorActivityLog::ERROR_DISK_QUOTA_REACHED);
      } //if

      $attachments = $incoming_mail->getAttachments();
      $formated_attachments = array();
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
          $formated_attachments[] = array(
            'path' => INCOMING_MAIL_ATTACHMENTS_FOLDER.'/'.$attachment->getTemporaryFilename(),
            'filename' => $attachment->getOriginalFilename(),
            'type' => strtolower($attachment->getContentType()),
          );
        } // foreach
        if(!$project_object instanceof IAttachments) {
          throw new InvalidInstanceError('project_object', $project_object, 'IAttachments');
        }//if
        $project_object->attachments()->attachFromArray($formated_attachments);
      } // if

    } // attachFilesToProjectObject

  }
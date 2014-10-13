<?php

  // Build on top of public tasks controller
  AngieApplication::useController('public_tasks', TASKS_MODULE);

  /**
   * Public submit task forms controller
   * 
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class PublicTaskFormsController extends PublicTasksController {

    /**
     * Active public task form
     *
     * @var PublicTaskForm
     */
    var $active_public_task_form;

    /**
     * Is captcha enabled
     *
     * @var Boolean
     */
    var $captcha_enabled = false;

    /**
     * Are uploads enabled
     *
     * @var bool
     */
    var $uploads_enabled = false;

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      $form_slug = $this->request->get('public_task_form_slug');
      $this->active_public_task_form = PublicTaskForms::findBySlug($form_slug);

      if ($this->getControllerName() == 'public_task_forms' && !ConfigOptions::getValue('tasks_public_submit_enabled')) {
        $this->response->forbidden();
      } // if

      if ($this->active_public_task_form instanceof PublicTaskForm) {
        $this->captcha_enabled = (boolean) ConfigOptions::getValue('tasks_use_captcha');
        $this->uploads_enabled = $this->active_public_task_form->getAdditionalProperty('attachments_enabled', false);

        $this->smarty->assign(array(
          'active_public_task_form' => $this->active_public_task_form,
          'captcha_enabled' => $this->captcha_enabled,
          'uploads_enabled' => $this->uploads_enabled
        ));
      } // if
    } // function

    /**
     * Submit form
     */
    function submit() {
      if($this->active_public_task_form instanceof PublicTaskForm && $this->active_public_task_form->isLoaded()) {
        if($this->active_public_task_form->getIsEnabled()) {
          $task_data = $this->request->post('task', array(
            'created_by_name' => $this->logged_user ? $this->logged_user->getDisplayName() : Authentication::getVisitorName(),
            'created_by_email' => $this->logged_user ? $this->logged_user->getEmail() : Authentication::getVisitorEmail()
          ));

          $this->smarty->assign(array(
            'task_data' => $task_data
          ));

          if ($this->request->isSubmitted()) {
            try {
              DB::beginWork('public task adding @ ' . __CLASS__);

              if ($this->captcha_enabled) {
                if (!Captcha::Validate($task_data['captcha'])) {
                  $errors = new ValidationErrors();
                  $errors->addError(lang('Code you entered is not valid'), 'captcha');
                  throw $errors;
                } // if
              } // if

              $created_by = new AnonymousUser(array_var($task_data, 'created_by_name'), array_var($task_data, 'created_by_email'));

              // keep formatting for body of the task
              if ($task_data['body']) {
                $task_data['body'] = nl2br_pre($task_data['body']);
              } //if

              $property_sharing = $this->active_public_task_form->getAdditionalProperty('sharing', true);

              // create task
              $task = new Task();
              $task->setAttributes($task_data);
              $task->setVisibility($property_sharing ? VISIBILITY_PUBLIC : VISIBILITY_NORMAL);
              $task->setState(STATE_VISIBLE);
              $task->setPriority(PRIORITY_NORMAL);
              $task->setCreatedOn(new DateTimeValue());
              $task->setCreatedBy($created_by);
              $task->setProjectId($this->active_public_task_form->getProjectId());

              $default_label = Labels::findDefault('AssignmentLabel');
              if($default_label instanceof AssignmentLabel) {
                $task->setLabelId($default_label->getId());
              } // if

              if ($this->uploads_enabled) {
                $task->attachments()->attachUploadedFiles($created_by);
              } // if

              $task->save();

              // clone all form subscribers to object that has just created
              $this->active_public_task_form->subscriptions()->cloneTo($task);

              // subscribe the project leader of project linked to the public task form
              $task->subscriptions()->subscribe($this->active_public_task_form->getProject()->getLeader());

              // load sharing properties

              $property_expire_after = $this->active_public_task_form->getAdditionalProperty('expire_after', 7) ;
              $property_comments_enabled = $this->active_public_task_form->getAdditionalProperty('comments_enabled', true);
              $property_attachments_enabled = $this->active_public_task_form->getAdditionalProperty('attachments_enabled', true);
              $property_reopen_on_comment = $this->active_public_task_form->getAdditionalProperty('reopen_on_comment', true);

              if (in_array($property_sharing, array(1, 2))) {
                // parse sharing properties and prepare to share the object
                $sharing_additional = array(
                  'expires' => 0,
                  'comments_enabled' => $property_comments_enabled,
                  'attachments_enabled' => $property_comments_enabled && $property_attachments_enabled,
                  'comment_reopens' =>  $property_comments_enabled && $property_reopen_on_comment
                );

                if ($property_sharing == 2) {
                  $sharing_additional['expires'] = 1;
                  $sharing_additional['expires_on'] = $task->getCreatedOn()->advance($property_expire_after * 60 * 60 * 24, false)->getTimestamp();
                } // if

                // share the object
                $task->sharing()->share($created_by, $sharing_additional);

                // set the cookie
                setcookie('activecollab_public_task_' . $task->sharing()->getSharingProfile()->getSharingCode(), true);
              } // if

              DB::commit('public task added @ ' . __CLASS__);

              // Notify all subscribers (except creator)
              AngieApplication::notifications()
                ->notifyAbout('tasks/new_task_from_form_for_staff', $task, $created_by)
                ->setForm($this->active_public_task_form)
                ->sendToSubscribers();

              // Subscribe the creator of the task
              if($this->active_public_task_form->getAdditionalProperty('subscribe_author')) {
                $task->subscriptions()->subscribe($created_by);
              } // if

              // Notify user who created the task about successful task creation
              AngieApplication::notifications()
                ->notifyAbout('tasks/new_task_from_form_for_author', $task)
                ->sendToUsers($created_by);

              if ($property_sharing) {
                $this->response->redirectToUrl($task->sharing()->getUrl());
              } else {
                $this->response->redirectTo('public_task_form_success');
              } // if
            } catch (Exception $e) {
              DB::commit('failed to add public task @ ' . __CLASS__);

              unset($task_data['captcha']);
              $this->smarty->assign('errors', $e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // submit
  
    /**
     * Success message after submission
     */
    function success() {

    } // success

  }
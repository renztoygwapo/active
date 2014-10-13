<?php

// Build on top of administration controller
AngieApplication::useController('admin', SYSTEM_MODULE);

/**
 * Tasks administration controller
 *
 * @package activeCollab.modules.tasks
 * @subpackage controllers
 */
class TasksAdminController extends AdminController {

  /**
   * Execute before action
   */
  function __before() {
    parent::__before();

    $this->wireframe->breadcrumbs->add('tasks_admin', lang('Tasks'), Router::assemble('tasks_admin'));
  } // __before

  /**
   * Show index page
   */
  function index() {
    $forms_per_page = 3;

    if($this->request->get('paged_list')) {
      $exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
      $timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;

      $this->response->respondWithData(PublicTaskForms::getSlice($forms_per_page, $exclude, $timestamp));
    } else {
      if(PublicTaskForms::canAdd($this->logged_user)) {
        $this->wireframe->actions->add('add_public_task_form', lang('New Public Form'), Router::assemble('public_task_forms_add'), array(
          'onclick' => new FlyoutFormCallback('public_task_form_created'),
          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
        ));
      } // if

      $custom_fields = array();

      foreach(CustomFields::getEnabledCustomFieldsByType('Task') as $field_name => $details) {
        $custom_fields[] = $details['label'] ? $details['label'] : CustomFields::getSafeFieldLabel($field_name);
      } // foreach

      $this->response->assign(array(
        'tasks_auto_reopen' => (boolean) ConfigOptions::getValue('tasks_auto_reopen'),
        'tasks_auto_reopen_clients_only' => (boolean) ConfigOptions::getValue('tasks_auto_reopen_clients_only'),
        'tasks_public_submit_enabled' => (boolean) ConfigOptions::getValue('tasks_public_submit_enabled'),
        'tasks_use_captcha' => (boolean) ConfigOptions::getValue('tasks_use_captcha'),
        'task_custom_fields' => $custom_fields,

        'forms' => PublicTaskForms::getSlice($forms_per_page),
        'forms_per_page' => $forms_per_page,
        'total_forms' => PublicTaskForms::count(),
      ));
    } // if
  } // index

  /**
   * Show and process settings form
   */
  function settings() {
    if($this->request->isAsyncCall()) {
      $this->response->assign(array(
        'tasks_auto_reopen' => (boolean) ConfigOptions::getValue('tasks_auto_reopen'),
        'tasks_auto_reopen_clients_only' => (boolean) ConfigOptions::getValue('tasks_auto_reopen_clients_only'),
        'tasks_public_submit_enabled' => (boolean) ConfigOptions::getValue('tasks_public_submit_enabled'),
        'tasks_use_captcha' => (boolean) ConfigOptions::getValue('tasks_use_captcha'),
      ));

      if($this->request->isSubmitted()) {
        $settings_data = $this->request->post('settings');

        $tasks_auto_reopen = (boolean) array_var($settings_data, 'tasks_auto_reopen');
        $tasks_auto_reopen_clients_only = $tasks_auto_reopen && array_var($settings_data, 'tasks_auto_reopen_clients_only');

        $tasks_public_submit_enabled = (boolean) array_var($settings_data, 'tasks_public_submit_enabled');
        $tasks_use_captcha = $tasks_public_submit_enabled && array_var($settings_data, 'tasks_use_captcha');

        try {
          DB::beginWork('Updating settings @ ' . __CLASS__);

          ConfigOptions::setValue('tasks_auto_reopen', $tasks_auto_reopen);
          ConfigOptions::setValue('tasks_auto_reopen_clients_only', $tasks_auto_reopen_clients_only);
          ConfigOptions::setValue('tasks_public_submit_enabled', $tasks_public_submit_enabled);
          ConfigOptions::setValue('tasks_use_captcha', $tasks_use_captcha);

          CustomFields::setCustomFieldsByType('Task', $settings_data['custom_fields']);

          DB::commit('Settings updated @ ' . __CLASS__);

          $custom_fields = array();

          foreach(CustomFields::getEnabledCustomFieldsByType('Task') as $field_name => $details) {
            $custom_fields[] = $details['label'] ? $details['label'] : CustomFields::getSafeFieldLabel($field_name);
          } // foreach

          $this->response->respondWithData(array(
            'tasks_auto_reopen' => $tasks_auto_reopen,
            'tasks_auto_reopen_clients_only' => $tasks_auto_reopen_clients_only,
            'tasks_public_submit_enabled' => $tasks_public_submit_enabled,
            'tasks_use_captcha' => $tasks_use_captcha,
            'task_custom_fields' => $custom_fields,
          ), array('as' => 'settings'));
        } catch(Exception $e) {
          DB::rollback('Failed to update settings @ ' . __CLASS__);
          $this->response->exception($e);
        } // try

      } // if
    } else {
      $this->response->badRequest();
    } // if
  } // settings

  function resolve_duplicate_ids() {
    $this->response->assign('actions', array(
      Router::assemble('tasks_admin_do_resolve_duplicate_id') => lang('Task IDs')
    ));
  } // resolve_duplicate_ids

  /**
   * Resolves duplicate Task IDs giving them new ID
   */
  function do_resolve_duplicate_ids() {
    if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
      try {
        Tasks::resolveDuplicateTaskIds();
        $this->response->ok();
      } catch(Exception $e) {
        $this->response->exception($e);
      } // try
    } else {
      $this->response->badRequest();
    } // if
  } // resolve_duplicate_ids
}
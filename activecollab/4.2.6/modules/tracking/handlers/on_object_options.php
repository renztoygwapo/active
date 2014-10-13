<?php

  /**
   * Tracking module on_object_options event handler
   *
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */
  
  /**
   * Handle on project options event
   *
   * @param ApplicationObject $object
   * @param User $user
   * @param NamedList $options
   * @param string $interface
   */
  function tracking_handle_on_object_options(&$object, &$user, &$options, $interface) {
    if($object instanceof Project) {
      if($object->canManageBudget($user)) {
        $options->add('project_budget', array(
          'url' => Router::assemble('project_budget', array('project_slug' => $object->getSlug())),
          'text' => lang('Budget Report'),  
        ));
      } // if
      
      if(JobTypes::canManageProjectHourlyRates($user, $object)) {
        $options->add('project_hourly_rates', array(
          'url' => Router::assemble('project_hourly_rates', array('project_slug' => $object->getSlug())),
          'text' => lang('Hourly Rates')
        ));
      } // if
    } // if
  } // tracking_handle_on_object_options
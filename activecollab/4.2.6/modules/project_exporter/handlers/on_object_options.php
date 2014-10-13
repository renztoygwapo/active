<?php

  /**
   * Project Exporter module on_object_options event handler
   *
   * @package activeCollab.modules.project_exporter
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
  function project_exporter_handle_on_object_options(&$object, &$user, &$options, $interface) {
    if($object instanceof Project && ($user->isProjectManager() || $object->isLeader($user))) {
      $options->add('export_project', array(
        'url' => Router::assemble('project_exporter', array('project_slug' => $object->getSlug())),
        'text' => lang('Export Project'),
      	'onclick' => new FlyoutCallback('project_exported')
      ));
    } //
  } // project_exporter_handle_on_object_options
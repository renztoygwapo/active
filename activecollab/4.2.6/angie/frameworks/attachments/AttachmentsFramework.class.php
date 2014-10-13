<?php

  /**
   * Attachment framework definition
   *
   * @package angie.frameworks.attachments
   */
  class AttachmentsFramework extends AngieFramework {
    
    /**
     * Framework name
     *
     * @var string
     */
    protected $name = 'attachments';
    
    /**
     * Define routes for this framework
     */
    function defineRoutes() {
      Router::map('temporary_attachment_add', 'attachments/temporary/add', array('controller' => 'temporary_attachments', 'action' => 'add', 'module' => ATTACHMENTS_FRAMEWORK_INJECT_INTO));

      // @todo solution when attachment has no parent
      Router::map("attachment", "/attachments/:attachment_id", array('controller' => 'temporary_attachments', 'action' => "view", 'module' => ATTACHMENTS_FRAMEWORK_INJECT_INTO), array('attachment_id' => Router::MATCH_ID));
      Router::map("attachment_download", "/attachments/:attachment_id/download", array('controller' => 'temporary_attachments', 'action' => "view", 'module' => ATTACHMENTS_FRAMEWORK_INJECT_INTO), array('attachment_id' => Router::MATCH_ID));
      Router::map("attachment_edit", "/attachments/:attachment_id/edit", array('controller' => 'temporary_attachments', 'action' => "edit", 'module' => ATTACHMENTS_FRAMEWORK_INJECT_INTO), array('attachment_id' => Router::MATCH_ID));
      Router::map("attachment_delete", "/attachments/:attachment_id/delete", array('controller' => 'temporary_attachments', 'action' => "delete", 'module' => ATTACHMENTS_FRAMEWORK_INJECT_INTO), array('attachment_id' => Router::MATCH_ID));

      Router::map("disk_space_remove_temporary_attachments", "/admin/disk-space/tools/remove-temporary-attachments", array('controller' => 'attachments_disk_space_admin', 'action' => 'remove_temporary_attachments', 'module' => ATTACHMENTS_FRAMEWORK_INJECT_INTO));
      
      AngieApplication::getModule('environment')->defineStateRoutesFor('attachment', "/attachments/:attachment_id", 'temporary_attachments', ATTACHMENTS_FRAMEWORK_INJECT_INTO, array('attachment_id' => Router::MATCH_ID));
    } // defineRoutes
    
    /**
     * Define attachment routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineAttachmentsRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $attachment_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('attachment_id' => Router::MATCH_ID)) : array('attachment_id' => Router::MATCH_ID);
      
      Router::map("{$context}_attachments", "$context_path/attachments", array('controller' => $controller_name, 'action' => "{$context}_attachments", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_attachments_add", "$context_path/attachments/add", array('controller' => $controller_name, 'action' => "{$context}_add_attachment", 'module' => $module_name), $context_requirements);
      
      Router::map("{$context}_attachment", "$context_path/attachments/:attachment_id", array('controller' => $controller_name, 'action' => "{$context}_view_attachment", 'module' => $module_name), $attachment_requirements);
      Router::map("{$context}_attachment_edit", "$context_path/attachments/:attachment_id/edit", array('controller' => $controller_name, 'action' => "{$context}_edit_attachment", 'module' => $module_name), $attachment_requirements);
      Router::map("{$context}_attachment_delete", "$context_path/attachments/:attachment_id/delete", array('controller' => $controller_name, 'action' => "{$context}_delete_attachment", 'module' => $module_name), $attachment_requirements);
      
      AngieApplication::getModule('download')->defineDownloadRoutesFor("{$context}_attachment", "$context_path/attachments/:attachment_id", $controller_name, $module_name, $attachment_requirements);
      AngieApplication::getModule('preview')->definePreviewRoutesFor("{$context}_attachment", "$context_path/attachments/:attachment_id", $controller_name, $module_name, $attachment_requirements);
      AngieApplication::getModule('environment')->defineStateRoutesFor("{$context}_attachment", "$context_path/attachments/:attachment_id", $controller_name, $module_name, $attachment_requirements);
    } // defineAttachmentRoutesFor
    
    /**
     * Define event listeners
     */
    function defineHandlers() {
      EventsManager::listen('on_used_disk_space', 'on_used_disk_space', null); // NULL added to fix awkward PHP 5.3.9 and 5.3.10 issues on IIS7 (confirmed and discussed in activecollab/#642)
      EventsManager::listen('on_daily', 'on_daily', null); // NULL added to fix awkward PHP 5.3.9 and 5.3.10 issues on IIS7 (confirmed and discussed in activecollab/#642)
    } // defineHandlers
    
  }
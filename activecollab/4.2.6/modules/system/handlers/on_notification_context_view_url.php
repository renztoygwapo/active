<?php

  /**
   * on_notification_context_view_url event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle context view URL event
   *
   * @param IUser $user
   * @param mixed $context
   * @param string $context_view_url
   */
  function system_handle_on_notification_context_view_url(&$user, &$context, &$context_view_url) {
    if($user instanceof AnonymousUser) {
      if($context instanceof ProjectRequest) {
        $context_view_url = $context->getPublicUrl();
      } elseif($context instanceof ISharing && $context->sharing()->isShared() && !$context->sharing()->isExpired()) {
        $context_view_url = $context->sharing()->getUrl();
      } else {
        $context_view_url = null;
      } // if
    } // if
  } // system_handle_on_notification_context_view_url
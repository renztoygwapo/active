<?php

  /**
   * notification_attachments_table helper implementation
   *
   * @package angie.frameworks.email
   * @subpackage helpers
   */

  /**
   * Render notification new comment made
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notification_attachments_table($params, &$smarty) {
    $object = array_required_var($params, 'object', false, 'ApplicationObject');
    $recipient = array_required_var($params, 'recipient', false, 'IUser');

    if($object instanceof IAttachments) {
      $sharing_object = $object instanceof FwComment ? $object->getParent() : $object;
      if ($sharing_object instanceof ProjectRequest) {
        $object_is_shared = $sharing_object->getStatus() !== ProjectRequest::STATUS_CLOSED;
      } elseif ($sharing_object instanceof Quote) {
        $object_is_shared = !$sharing_object->isPublicPageExpired();
      } else {
        $object_is_shared = $sharing_object instanceof ISharing && $sharing_object->sharing()->isShared() && !$sharing_object->sharing()->isExpired();
      } // if
      $view_method = $object_is_shared ? "getPublicViewUrl" : "getViewUrl";

      $attachments = $object->attachments()->get($recipient);

      if($attachments) {
        $content = "<table width='100%' id='attachment'><tbody><tr><td style='padding-bottom:15px;'>";

        $link_style = AngieApplication::mailer()->getDecorator()->getLinkStyle();
        foreach($attachments as $attachment) {
          $content .= "<img src='" . AngieApplication::getImageUrl('icons/12x12/manage-attachments.png', ATTACHMENTS_FRAMEWORK) . "'><a href='" . clean($attachment->$view_method(true)) . "' style='margin-left: 5px; font-size: 11px; " . $link_style . "'>" . clean($attachment->getName()) . "</a><span style='margin-left:5px; font-size:9px; color:#91918D;'>" . format_file_size($attachment->getSize()) . "</span><br/>";
        } // foreach

        return "$content</td></tr></tbody></table>";
      } // if
    } // if
  } // smarty_function_notification_attachments_table
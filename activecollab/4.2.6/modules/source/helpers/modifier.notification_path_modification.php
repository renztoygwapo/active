<?php

  /**
   * Show path modifier flag
   *
   * @package activeCollab.modules.source
   * @subpackage helpers
   */

  /**
   * Return formatted path modification flag
   *
   * @param string $action
   * @param Language $language
   * @return string
*/
  function smarty_modifier_notification_path_modification($action, $language = null) {
    switch ($action) {
      case SOURCE_MODULE_STATE_MODIFIED:
        $span_color = '#D6E1FA';
        break;
      case SOURCE_MODULE_STATE_DELETED:
        $span_color = '#F7DDE8';
        break;
      case SOURCE_MODULE_STATE_ADDED:
        $span_color = '#D8FBD4';
        break;
      default:
        $span_color = '#F2DA00';
        break;
    } // switch

    return '<span style="color: #515151; border-radius: 10px; background-color: ' . $span_color . ' ; width:40px; display: block; padding: 2px 8px; font-size: 9px; margin-bottom: 2px; text-align: center;">' . source_module_get_state_label($action, $language) . '</span>';
  } // smarty_modifier_notification_path_modification
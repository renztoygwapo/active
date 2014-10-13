<?php

  /**
   * notification_wrap_body helper implementation
   *
   * @package angie.frameworks.email
   * @subpackage helpers
   */

  /**
   * Wrap context object body
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_notification_wrap_body($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if

    if($content) {
      return '<div style="padding: 5px 10px">' . HTML::toRichText($content, 'notification') . '</div>';
    } // if
  } // smarty_block_notification_wrap_body
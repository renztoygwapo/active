<?php

  /**
   * Wrap notification core block
   *
   * @package angie.frameworks.angie
   * @subpackage helpers
   */

  /**
   * Render main notification block wrapper
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return mixed
   */
  function smarty_block_notification_wrapper($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if

    $context = array_var($params, 'context', null, false);
    $sender = array_required_var($params, 'sender', false, 'IUser');
    $recipient = array_required_var($params, 'recipient', false, 'IUser');
    $language = $recipient->getLanguage();

    $inspect = $context && array_var($params, 'inspect', true);

    $title = isset($params['title']) && $params['title'] ? lang($params['title'], null, true, $language) : lang('Notification', null, true, $language);
    
    if(strpos($title, ':') !== false && $context instanceof ApplicationObject) {
      $title = lang($title, array(
        'name' => $context->getName(),
        'type' => $context->getVerboseType(false, $language),
        'type_lowercase' => $context->getVerboseType(true, $language),
      ), true, $language);
    } // if

    // Open content wrapper
    $result = '<table width="654" cellpadding="0" cellspacing="0" border="0" align="center" id="mainTable" style="width: 654px; margin: 0 auto; border-spacing: 0; border-collapse:separate; border:1px solid #d0d2c9; border-radius: 20px; text-align:left;">';
    
    $result.= '<tr><td id="content" style="background-color:#ffffff; padding: 25px; border-top-left-radius: 20px; border-top-right-radius: 20px;"><table border="0" cellpadding="0" cellspacing="0" style="font-family: Lucida Grande, Verdana, Arial, Helvetica, sans-serif; font-size:12px;">';

    // Greeting and title
    $result .= '<tr><td><table style="font-family: Lucida Grande, Verdana, Arial, Helvetica, sans-serif;"><tr><td width="600" id="greetings">';

    if(array_var($params, 'greet', true)) {
      $result .= '<div style="padding-bottom:15px;">' . lang('Hi :name', array(
        'name' => $recipient->getFirstName(true),
      ), true, $language) . ',</div>';
    } // if

    $result .= '<div style="font-size:28px; line-height:28px; padding-bottom:15px;">' . clean($title) . '</div>';
    $result .= '</td>';

    // Logo
    AngieApplication::useHelper('notification_identity', EMAIL_FRAMEWORK);

    $result .= '<td width="100" style="text-align: center; width: 100px; padding-bottom:15px;" class="branding">' . smarty_function_notification_identity($params, $smarty) . '</td>';
    $result .= '</tr></table></td></tr>';

    // Body block
    $result .= '<tr><td colspan="2">' . $content . '</td></tr>';

    // Context related information, if present
    if($context) {
    	$result .= '<tr><td><table width="100%"><tr><td>';
    	
      if($context instanceof IAttachments) {
        AngieApplication::useHelper('notification_attachments_table', EMAIL_FRAMEWORK);

        $result .= smarty_function_notification_attachments_table(array(
          'object' => $context,
          'recipient' => $recipient,
        ), $smarty);
      } // if

      // Open in Browser link
      if(array_var($params, 'open_in_browser', true) && isset($params['context_view_url']) && $params['context_view_url']) {
        $result .= '<tr><td style="text-align: center; padding:10px 5px;" id="openInBrowser" align="center"><a href="' . clean($params['context_view_url']) . '" style="color: #4b4b4b; text-decoration: none; text-align:center; padding: 3px 10px; background-color: #eeeeed; border-color: #dedede; border-width: 1px; border-radius: 20px; border-style: solid; font-size:11px; font-family: Lucida Grande, Verdana, Arial, Helvetica, sans-serif;">' . lang('Open this :type in Your Web Browser', array(
          'type' => $context->getVerboseType(true, $language),
        ), true, $language) . '</a></td></tr>';
      } // if
      
      $result .= '</td></tr></table></td></tr>';
    } // if

    $result .= '</table></td></tr>'; // Close content wrapper

    // Inspector
    if($inspect) {
      AngieApplication::useHelper('notification_inspector', EMAIL_FRAMEWORK);
      
      $result .= '<tr><td height="1" style="height:1px; line-height:1px; border-bottom:1px solid #d7d8cf; background:#fff;"></td></tr>';
      $result .= '<tr><td><table cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:separate; padding: 15px; -webkit-border-radius: 20px; -moz-border-radius: 20px; border-radius: 20px;" id="inspector"><tr>' . smarty_function_notification_inspector(array(
        'recipient' => $recipient,
        'context' => $context,
        'context_view_url' => array_var($params, 'context_view_url'),
        'sender' => $sender,
      ), $smarty) . '</tr></table></td></tr>';

    // Just close the block
    } else {
      $result .= '<tr><td id="content" style="background-color:#ffffff; padding: 25px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;"></td></tr>';
    } // if

    // Close wrapper and done
    return "$result</table>";
  } // smarty_block_notification_wrapper
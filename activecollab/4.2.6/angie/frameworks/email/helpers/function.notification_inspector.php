<?php

  /**
   * Render inspector table for email notifications
   *
   * @package angie.frameworks.email
   * @subpackage helpers
   */

  /**
   * Render notification inspector
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notification_inspector($params, &$smarty) {
    $context = array_required_var($params, 'context', true, 'ApplicationObject');
    $context_view_url = array_var($params, 'context_view_url');
    $recipient = array_required_var($params, 'recipient', true, 'IUser');
    $language = $recipient->getLanguage();

    $action = lang('Created by', null, true, $language);
    $action_by = $context->getCreatedBy();

    $link_to_context = $context_view_url ?
      array($context_view_url, $context->getName()) : // Link
      $context->getName(); // Just the name

    $properties = new NamedList(array(
      'name' => array(
        'label' => $context->getVerboseType(false, $language),
        'value' => array($link_to_context),
      )
    ));

    if($context instanceof ICategory && $context->category()->get() instanceof Category) {
      $properties->add('category', array(
        'label' => lang('Category', null, null, $language),
        'value' => array($context->category()->get()->getName()),
      ));
    } // if

    EventsManager::trigger('on_notification_inspector', array(&$context, &$recipient, &$properties, &$action, &$action_by));

    if($context instanceof IAssignees) {
      if($context->assignees()->isResponsible($recipient)) {
        $properties->add('responsibility', array(
          'label' => lang('Responsibility', null, null, $language),
          'value' => array(lang('You are responsible for this :type', array(
            'type' => $context->getVerboseType(true, $language),
          ), null, $language)),
        ));
      } elseif($context->assignees()->isAssignee($recipient)) {
        $properties->add('responsibility', array(
          'label' => lang('Responsibility', null, null, $language),
          'value' => array(lang('You are assigned to this :type', array(
            'type' => $context->getVerboseType(true, $language),
          ), null, $language)),
        ));
      } elseif($context->assignees()->getAssignee() instanceof User) {
        $properties->add('responsibility', array(
          'label' => lang('Responsibility', null, null, $language),
          'value' => array(lang(':responsible_name is responsible.', array(
            'responsible_name' => $context->assignees()->getAssignee()->getDisplayName(true)
          ), true, $language)),
        ));
      } // if
    } // if

    // Open inspector table
    $result = '<td style="text-align: left; width:624px;"><table cellpadding="0" cellspacing="0" border="0" align="left" style="font-family: Lucida Grande, Verdana, Arial, Helvetica, sans-serif; font-size:12px; background:' . AngieApplication::mailer()->getDecorator()->getBackgroundColor() . '; line-height:16px;">';

    $counter = 0;
    foreach($properties as $k => $v) {
      $result .= '<tr>';

      $result .= '<td style="width: 80px; vertical-align: top; padding: 3px 2px 4px 4px;">' . clean($v['label']) . ':</td>';

      if(is_array($v['value'])) {
        $links = array();

        foreach($v['value'] as $link) {
          if (is_string($link)) {
            $links[] = clean($link);
          } else if (is_array($link) && is_string($link[0]) && is_string($link[1])) {
            $links[] = '<a href="' . clean($link[0]) . '" style="' . AngieApplication::mailer()->getDecorator()->getLinkStyle() . '">' . clean($link[1]) . '</a>';
          } // if
        } // foreach

        $links = implode(' - ', $links);
      } else {
        $links = '<b>' . clean($v['value']) . '</b>';
      } // if

      $result .= '<td style="vertical-align: top; padding: 2px; width:auto !important;">' . $links . '</td>';
      $result .= '</tr>';

      $counter++;
    } // foreach
    
    $result .= '</table></td>';
    
    // Add creation info as well
    if($action_by instanceof IUser) {
    	$result .= '<td><table cellpadding="0" cellspacing="0" border="0" align="center" id="createdBy" style="width:150px; font-family: Lucida Grande, Verdana, Arial, Helvetica, sans-serif; font-size:12px;"><tr>';
    	
      $result .= '<td align="right" style="margin: 5px; width:100px;" width="100">' . clean($action) . '<br> <a href="' . clean($action_by->getViewUrl()) . '" style="padding-top:5px; ' . AngieApplication::mailer()->getDecorator()->getLinkStyle() . '">' . clean($action_by->getDisplayName(true)) . '</a></td>';
      $result .= '<td style="padding: 5px 10px;" class="avatar"><img src="' . clean($action_by->avatar()->getUrl(40)) . '" alt=""></td>';
      
      $result .= '</tr></table></td>';
    } // if

    return $result;
  } // smarty_function_notification_inspector
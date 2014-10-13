<?php

  /**
   * User reminders helper
   *
   * @package angie.frameworks.reminders
   * @subpackage helpers
   */

  /**
   * Render user reminders widget content
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_user_reminders($params, &$smarty) {
    $user = array_required_var($params, 'user', false, 'User');

    $reminders = Reminders::findActiveByUser($user, true);
    $id = HTML::uniqueId('user_reminders');

    $result = '<ul class="user_reminders" id="' . $id . '">';
    if (is_foreachable($reminders)) {
      AngieApplication::useHelper('ago', GLOBALIZATION_FRAMEWORK, 'modifier');

      foreach ($reminders as $reminder) {
        $result.= '<li class="reminder" reminder_id="' . $reminder->getId() . '">';
        $result.= '<span class="reminder_avatar"><a href="' . $reminder->getCreatedBy()->getViewUrl() . '"><img src="' . $reminder->getCreatedBy()->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG) . '" /></a></span>';
        $result.= '<span class="reminder_author">' . clean($reminder->getCreatedBy()->getDisplayName(true)) . ($reminder->getSentOn() ? smarty_modifier_ago($reminder->getSentOn()) : '') . '</span>';
        $result.= '<span class="reminder_related_object">' . object_link($reminder->getParent(), 40, array('class' => 'quick_view_item')) . '</span>';
        if ($reminder->getComment()) {
          $result.= '<span class="reminder_comment">' . nl2br($reminder->getComment()) . '</span>';
        } // if
        $result.= '<a href="' . $reminder->getDismissUrl(true) . '" class="reminder_dismiss"><img src="' . AngieApplication::getImageUrl('icons/12x12/dismiss.png', REMINDERS_FRAMEWORK) . '" /></a>';
        $result.= '</li>';
      } // foreach
    } // if

    AngieApplication::useWidget('user_reminders', REMINDERS_FRAMEWORK);

    $result.= '</ul><script type="text/javascript">$("#' . $id . '").userReminders()</script>';

    return $result;
  } // smarty_function_user_reminders
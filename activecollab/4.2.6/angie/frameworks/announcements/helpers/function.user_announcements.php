<?php

  /**
   * User assignments widget helper
   *
   * @package angie.frameworks.announcements
   * @subpackage helpers
   */

  /**
   * Render user announcements
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_user_announcements($params, &$smarty) {
    $user = array_required_var($params, 'user', false, 'User');

    $announcements = Announcements::findActiveByUser($user);
    $id = HTML::uniqueId('user_announcements');

    $result = '<ul class="user_announcements" id="' . $id . '">';
    if(is_foreachable($announcements)) {
      foreach($announcements as $announcement) {
        $result .= '<li class="announcement" announcement_id="' . $announcement->getId() . '">';
        if($announcement->canDismiss($user) && $announcement->getExpirationType() == FwAnnouncement::ANNOUNCE_EXPIRATION_TYPE_UNTIL_DISMISSED) {
          $result .= '<a href="' . $announcement->getDismissUrl() . '" class="announcement_dismiss"><img src="' . AngieApplication::getImageUrl('icons/12x12/dismiss.png', ANNOUNCEMENTS_FRAMEWORK) . '" /></a>';
        } // if
        $result .=   '<span class="announcement_icon" title="' . ucfirst($announcement->getIcon()) . '"><img src="' . $announcement->getLargeIconUrl() . '" /></span>';
        $result .=   '<span class="announcement_subject">' . clean($announcement->getSubject()) . '</span>';
        $result .=   '<span class="announcement_body">' . nl2br($announcement->getBody()) . '</span>';
        $result .= '</li>';
      } // foreach
    } // if

    AngieApplication::useWidget('user_announcements', ANNOUNCEMENTS_FRAMEWORK);

    $result .= '</ul><script type="text/javascript">$("#' . $id . '").userAnnouncements()</script>';

    return $result;
  } // smarty_function_user_announcements
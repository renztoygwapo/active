<?php

  /**
   * select_announcement_icon helper implementation
   * 
   * @package angie.frameworks.announcements
   * @subpackage helpers
   */

  /**
   * Render announcement icon selection options
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_announcement_icon($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', FwAnnouncement::ANNOUNCE_ANNOUNCEMENT, true);

    $possibilities = array(
      FwAnnouncement::ANNOUNCE_ANNOUNCEMENT => lang('Announcement'),
      FwAnnouncement::ANNOUNCE_BUG => lang('Bug'),
      FwAnnouncement::ANNOUNCE_COMMENT => lang('Comment'),
      FwAnnouncement::ANNOUNCE_EVENT => lang('Event'),
      FwAnnouncement::ANNOUNCE_IDEA => lang('Idea'),
      FwAnnouncement::ANNOUNCE_INFO => lang('Info'),
      FwAnnouncement::ANNOUNCE_JOKE => lang('Joke'),
      FwAnnouncement::ANNOUNCE_NEWS => lang('News'),
      FwAnnouncement::ANNOUNCE_QUESTION => lang('Question'),
      FwAnnouncement::ANNOUNCE_STAR => lang('Star'),
      FwAnnouncement::ANNOUNCE_WARNING => lang('Warning'),
      FwAnnouncement::ANNOUNCE_WELCOME => lang('Welcome')
    );
    
    return HTML::radioGroupFromPossibilities($name, $possibilities, $value, $params);
  } // smarty_function_select_announcement_icon
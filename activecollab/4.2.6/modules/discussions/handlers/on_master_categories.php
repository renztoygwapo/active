<?php

  /**
   * on_master_categories handler definition
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on_master_categories event
   *
   * @param array $categories
   */
  function discussions_handle_on_master_categories(&$categories) {
  	$categories[] = array(
  	  'name' => 'discussion_categories',
  	  'label' => lang('Discussion Categories'),
  	  'value' => ConfigOptions::getValue('discussion_categories'),
  	  'type' => 'DiscussionCategory', 
  	);
  } // discussions_handle_on_master_categories
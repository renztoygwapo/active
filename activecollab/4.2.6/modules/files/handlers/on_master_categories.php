<?php

  /**
   * on_master_categories handler definition
   *
   * @package activeCollab.modules.files
   * @subpackage handlers
   */

  /**
   * Handle on_master_categories event
   *
   * @param array $categories
   */
  function files_handle_on_master_categories(&$categories) {
  	$categories[] = array(
  	  'name' => 'asset_categories',
  	  'label' => lang('File Categories'),
  	  'value' => ConfigOptions::getValue('asset_categories'),
  	  'type' => 'AssetCategory', 
  	);
  } // files_handle_on_master_categories
<?php

  /**
   * select_asset_category helper implementation
   * 
   * @package activeCollab.modules.files
   * @subpackage helpers
   */

  /**
   * Render select asset category helper
   *
   * @param unknown_type $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_asset_category($params, &$smarty) {
    AngieApplication::useHelper('select_category', CATEGORIES_FRAMEWORK);
    
    $parent = array_var($params, 'parent');
    if(!($parent instanceof Project)) {
      throw new InvalidInstanceError('parent', $parent, '$parent is expected to be Project instance');
    } // if
    
    $user = array_var($params, 'user');
    if(!($user instanceof User)) {
      throw new InvalidInstanceError('user', $user, '$user is expected to be User instance');
    } // if
    
    if(array_var($params, 'can_create_new', true) && $parent->availableCategories()->canManage($user, 'AssetCategory')) {
      $params['add_url'] = Router::assemble('project_asset_categories_add', array('project_slug' => $parent->getSlug()));
    } // if
    
    $params['type'] = 'AssetCategory';
    
    return smarty_function_select_category($params, $smarty);
  } // smarty_function_select_asset_category

?>
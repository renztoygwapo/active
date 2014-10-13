<?php

	/**
   * Select Document Category helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select Document Category helper
   * 
   * Params:
   * 
   * - Standard select box attributes
   * - value - ID of selected role
   * - optional - Wether value is optional or not
   * - can_create_new - Can the user create new category or not, default is true
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_document_category($params, &$smarty) {
    AngieApplication::useHelper('select_category', CATEGORIES_FRAMEWORK);
    
    $user = array_required_var($params, 'user', true, 'IUser');
    
    if(array_var($params, 'can_create_new', true) && Documents::canManage($user)) {
      $params['add_url'] = Router::assemble('document_categories_add');
    } // if
    
    $params['type'] = 'DocumentCategory';
    
    return smarty_function_select_category($params, $smarty);
  } // smarty_function_select_document_category
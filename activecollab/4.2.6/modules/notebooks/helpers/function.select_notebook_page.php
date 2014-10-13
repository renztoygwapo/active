<?php

  /**
   * select_notebook helper
   *
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */
  
  /**
   * Render select notebook control
   * 
   * Parameters:
   * 
   * - project - Parent project
   * - value - ID of selected notebook
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_notebook_page($params, &$smarty) {
    $project = array_required_var($params, 'project', true, 'Project');
    $user = array_required_var($params, 'user', true, 'IUser');
    $notebook = array_var($params, 'notebook', true, 'Notebook');
    
    $notebook_pages = NotebookPages::findByNotebook($notebook);
    
    $name = array_var($params, 'name', null, true);
    $value = array_var($params, 'value', null, true);
    $skip = array_var($params, 'skip', null, true);
    
    $options = array();
    
    if($notebook_pages) {
      foreach($notebook_pages as $notebook_page) {
        smarty_function_select_notebook_page_populate_options($notebook_page, $value, $user, $skip, $options, '- ');
      } // foreach
    } // if
    
    $result = array_var($params, 'optional', true, true) ? 
      HTML::optionalSelect($name, $options, $params, lang('None')) : 
      HTML::select($name, $options, $params);
    
    return $result;
  } // smarty_function_select_notebook_page
  
  /**
   * Populate options array with options recursivly
   *
   * @param NotebookPage $notebook_page
   * @param integer $value
   * @param User $user
   * @param NotebookPage $skip
   * @param array $options
   * @param string $indent
   */
  function smarty_function_select_notebook_page_populate_options($notebook_page, $value, $user, $skip, &$options, $indent = '') {
    if($skip instanceof NotebookPage && $skip->getId() == $notebook_page->getId()) {
      return;
    } // if
    
    $options[] = HTML::optionForSelect($indent.$notebook_page->getName(), $notebook_page->getId(), $notebook_page->getId() == $value);
    
    $subpages = $notebook_page->getSubpages();
    if(is_foreachable($subpages)) {
      foreach($subpages as $subpage) {
        smarty_function_select_notebook_page_populate_options($subpage, $value, $user, $skip, $options, $indent . '- ');
      } // foreach
    } // if
  } // smarty_function_select_notebook_populate_options
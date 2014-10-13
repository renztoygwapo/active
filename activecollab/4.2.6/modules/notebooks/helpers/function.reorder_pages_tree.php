<?php

  /**
   * Render reorder pages tree
   * 
   * Params:
   * 
   * - pages
   * - user
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_reorder_pages_tree($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'User');
    $notebook = array_required_var($params, 'notebook', true, 'Notebook');
    
    $widget_id = HTML::uniqueId('reorder_pages');

    AngieApplication::useWidget('reorder_notebook_pages', NOTEBOOKS_MODULE);
    $notebook_pages = NotebookPages::findByNotebook($notebook);
    if(is_foreachable($notebook_pages)) {
      $result = "<div class='reorder_pages_tree_wrapper' id='" . $widget_id . "'><ul>\n";
      $result .= '<li><span class="notebook_pages_parent"  object_id="'.$notebook->getId().'" object_type="' . get_class($notebook) . '">' . clean($notebook->getName()) . '</span><ul>';
      foreach($notebook_pages as $notebook_page) {
        $result .= _pages_reorder_tree_render_cell($notebook_page, $user, $smarty);
      } // foreach
      return "$result\n</ul></li></ul></div><script type='text/javascript'>$('#" . $widget_id . "').reorderNotebookPages()</script>";
    } // if
  } // smarty_function_pages_tree
  
  /**
   * Render single page tree cell
   *
   * @param Page $notebook_page
   * @param User $user
   * @param Smarty $smarty
   * @param string $indent
   * @return string
   */
  function _pages_reorder_tree_render_cell($notebook_page, $user, &$smarty, $indent = '') {
    $smarty->assign('_notebook_page',$notebook_page);    
    $notebook_subpages = $notebook_page->getSubpages();
    $result.= '<li><span object_id="'.$notebook_page->getId().'" object_type="' . get_class($notebook_page) . '">' . clean($notebook_page->getName()) . '</span>';
    $result.= '<ul>';
    if(is_foreachable($notebook_subpages)) {
      foreach($notebook_subpages as $notebook_subpage) {
        $result .= _pages_reorder_tree_render_cell($notebook_subpage, $user, $smarty, $indent . '&middot;&middot;');
      } // foreach
    } // if
    $result.= '</ul>';
    $result.= '</li>';
    return $result;
  } // _pages_tree_render_cell
  
  function _pages_reorder_tree_render_notebook($notebook_page) {
  } // _pages_reorder_tree_render_notebook
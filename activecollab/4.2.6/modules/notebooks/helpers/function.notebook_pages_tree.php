<?php

  /**
   * notebook_pages_tree helper implementation
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */

  /**
   * Render notebook pages tree
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notebook_pages_tree($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'User');
    $notebook_pages = array_var($params, 'notebook_pages');
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if(is_foreachable($notebook_pages)) {
    	
    	// Default interface
	    if($interface == AngieApplication::INTERFACE_DEFAULT) {
	      $result = "<table class=\"pages_tree\">\n";
	      foreach($notebook_pages as $notebook_page) {
	        $result .= _notebook_pages_tree_render_cell($notebook_page, $user, $smarty);
	      } // foreach
	      return "$result\n</table>";
	      
	    // Phone interface
	    } elseif($interface == AngieApplication::INTERFACE_PHONE) {
	    	$result = "";
	      foreach($notebook_pages as $notebook_page) {
	        $result .= _notebook_pages_tree_render_cell($notebook_page, $user, $smarty);
	      } // foreach
	      return $result;
	    } elseif($interface == AngieApplication::INTERFACE_PRINTER) {
	        $result .= '<h2>' . lang('Pages') . '</h2>';
	    	$result .= "<table class=\"pages_tree common\">\n";
	    	$result .= "<tr>
	    		<th class='star'>" . lang('Favorite') . "</th>
	    		<th class='name'>" . lang('Name') . "</th>
	    		<th class='version'>" . lang('Version') . "</th>
	    		<th class='ago'>" . lang('History') . "</th>
	    	</tr>";
	    	foreach($notebook_pages as $notebook_page) {
	        $result .= _notebook_pages_tree_render_cell($notebook_page, $user, $smarty);
	      } // foreach
	      return "$result\n</table>";
	    } // if 
    } // if
  } // smarty_function_notebook_pages_tree
  
  /**
   * Render single notebook page tree cell
   *
   * @param NotebookPage $notebook_page
   * @param User $user
   * @param Smarty $smarty
   * @param string $indent
   * @return string
   */
  function _notebook_pages_tree_render_cell($notebook_page, $user, &$smarty, $indent = '') {
    $smarty->assign(array(
      '_notebook_page' => $notebook_page,
      '_indent' => $indent,
    ));
    
    $result = $smarty->fetch(get_view_path('_notebook_page_tree_row', 'notebook_pages', NOTEBOOKS_MODULE));
    
    $subpages = $notebook_page->getSubpages();
    if(is_foreachable($subpages)) {
      foreach($subpages as $subpage) {
        $result .= _notebook_pages_tree_render_cell($subpage, $user, $smarty, $indent . '&middot;&middot;');
      } // foreach
    } // if
    return $result;
  } // _notebook_pages_tree_render_cell
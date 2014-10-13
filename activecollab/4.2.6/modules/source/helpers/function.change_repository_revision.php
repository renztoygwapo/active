<?php

/**
 * change_repository_revision helper
 *
 * @package activeCollab.modules.source
 * @subpackage helpers
 */


/**
 * Renders a change repository revision
 *
 * @param array $params
 * @param Smarty $smarty
 * @return rendered image
 */
function smarty_function_change_repository_revision($params, &$smarty) {
	static $ids = array();
	
	$id = array_var($params, 'id');
	if(empty($id)) {
		$counter = 1;
		do {
			$id = 'change_revision_' . $counter++;
		} while(in_array($id, $ids));
	} // if
	$ids[] = $id;
	
	$repository = array_var($params, 'repository');
    
	$smarty->assign(array(
		'_change_repository_revision_url' => array_var($params, 'url'),
		'_change_repository_revision_test_url' => array_var($params, 'test_url'),
		'_change_repository_revision_id' => $id,
		'_change_repository_revision_repository_type' => $repository->getType(), 
	));
    
	return $smarty->fetch(get_view_path('_change_repository_revision', null, SOURCE_MODULE));
} // smarty_function_change_repository_revision
<?php

/**
 * change_repository_revision_select helper
 *
 * @package activeCollab.modules.source
 * @subpackage helpers
 */


/**
 * Renders a change repository revision select
 *
 * @param array $params
 * @param Smarty $smarty
 * @return rendered image
 */
function smarty_function_change_repository_revision_select($params, &$smarty) {
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
	
	$path = array_var($params, 'path');
	$commits = array();
	$source_paths = SourcePaths::findSourcePathsForPath($path);
    
    foreach ($source_paths as $key => $source_path) {
      $commit= SourceCommits::findById($source_path->getCommitId());
      if ($commit->getRepositoryId() === $repository->getId()) {
        $commits[] = $commit;
      }//if
    } //foreach
    $commits = SourceCommits::sortCommitsByDate($commits);
	
	$smarty->assign(array(
		'_change_repository_revision_select_url' => array_var($params, 'url'),
		'_change_repository_revision_select_id' => $id,
	    '_change_repository_revision_select_commits' => $commits
	));
    
	return $smarty->fetch(get_view_path('_change_repository_revision_select', null, SOURCE_MODULE));
} // smarty_function_change_repository_revision_select
<?php

  /**
   * Source version control module on_frequently event handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * Frequently update of repositories
   */
  function source_handle_on_frequently() {
    require_once(ANGIE_PATH.'/classes/xml/xml2array.php');
    
    $source_repositories = SourceRepositories::findByUpdateType(REPOSITORY_UPDATE_FREQUENTLY);
    
    if($source_repositories) {
      $results = "";
      foreach ($source_repositories as $source_repository) {
        if ($source_repository instanceof SourceRepository) {
          $project_source_repositories = ProjectSourceRepositories::findByParent($source_repository);
          
          // don't update repositories which are not added to any project
          
          if (is_foreachable($project_source_repositories)) {
            
            //load and get engines
            if (($error = $source_repository->loadEngine()) !== true) {
              return($error);
            } // if
            if (!$repository_engine = $source_repository->getEngine()) {
              return lang('Failed to load repository engine class');
            } // if

            if (is_error($repository_engine->error)) {
              $results .= lang('Error connecting to repository ') . ' ' . $source_repository->getName() . ': ' . $repository_engine->error->getMessage();
              continue;
            } //if

            $branches = $source_repository->hasBranches() ? $repository_engine->getBranches() : Array('');


            foreach ($branches as $branch) {
              $repository_engine->active_branch = $branch;
              $last_commit = $source_repository->getLastCommit($branch);

              $latest_revision = $last_commit instanceof SourceCommit ? $last_commit->getRevisionNumber() : ($repository_engine->getZeroRevision() - 1);
              $head_revision = $repository_engine->getHeadRevision();

              if (!$head_revision) {
                $results .= lang('Connection to ":name" source repository failed. Please contact repository server administrator for assistance', array(
                  'name' => $source_repository->getName()
                ));
                continue;
              } //if

              if (!is_null($repository_engine->error) || ($latest_revision == $head_revision)) {
                continue;
              } //if

              $revision_from = $latest_revision+1;
              $revision_to = $revision_from + $repository_engine->getModuleLogsPerRequest() - 1;
              if ($revision_to >= $head_revision) {
                $revision_to = $head_revision;
              } //if
              $logs = $repository_engine->getLogs($revision_from,$revision_to);
              if (!is_null($repository_engine->error)) {
                continue;
              } //if
              $source_repository->update($logs['data'], $branch);

              $total_commits = $logs['total'] - $logs['skipped_commits'];
              $branch_string = $branch ? ' '.lang('Branch'). ': '.$branch : '';
              $results .= $source_repository->getName(). $branch_string . ' ('.$total_commits.' '. lang('new commits')   . '); \n';

              if ($total_commits > 0) {
                  foreach ($project_source_repositories as $project_source_repository) {
                    /**
                     * @var ProjectSourceRepository $project_source_repository
                     */
                    if ($total_commits <= MAX_UPDATED_COMMITS_TO_SEND_DETAILED_NOTIFICATIONS) {
                      $project_source_repository->detailed_notifications = true;
                    } //if

                    $project_source_repository->last_update_commits_count = $total_commits;
                    $project_source_repository->source_repository = $source_repository;
                    $project_source_repository->active_branch = $branch;
                    ProjectSourceRepositories::sendCommitNotificationsToSubscribers($project_source_repository);

                    $project_source_repository->createActivityLog();
                  } //foreach
              } //if
            } //foreach
          } //if  
        } //if
      } // foreach
      
      return empty($results) ? lang('No repositories for frequently update') : lang('Updated repositories: \n') . $results; 
    } else {
      return lang('No repositories for frequently update');
    } // if
  } // source_handle_on_frequently
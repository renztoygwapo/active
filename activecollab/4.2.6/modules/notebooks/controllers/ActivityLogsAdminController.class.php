<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs_admin', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Activity logs controller
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage controllers
   */
  class ActivityLogsAdminController extends FwActivityLogsAdminController {
    
    /**
     * Rebuild notebook entries
     */
    function rebuild_notebooks() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          ActivityLogs::rebuildProjectObjectActivityLogs(array('Notebook'), 'notebooks', array('Notebook' => 'notebook/created'));
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_notebooks
    
    /**
     * Rebuild activity logs of notebook pages
     */
    function rebuild_notbook_pages() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating notebook page activity logs @ ' . __CLASS__);
          
          $notebooks = DB::execute('SELECT id, visibility, project_id FROM ' . TABLE_PREFIX . 'project_objects WHERE type = ? AND state >= ?', 'Notebook', STATE_ARCHIVED);
          if($notebooks) {
            $notebooks->setCasting(array(
              'id' => DBResult::CAST_INT, 
              'visibility' => DBResult::CAST_INT, 
              'project_id' => DBResult::CAST_INT, 
            ));
            
            $batch = DB::batchInsert(TABLE_PREFIX . 'activity_logs', array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
            
            foreach($notebooks as $notebook) {
              $notebook_page_ids = NotebookPages::getAllIdsByNotebook($notebook['id']);
              
              $visibility = $notebook['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
              
              if($notebook_page_ids) {
                $update_older_verisons = array();
                
                $notebook_pages = DB::execute('SELECT id, created_on, created_by_id, created_by_name, created_by_email, last_version_on, last_version_by_id, last_version_by_name, last_version_by_email, version FROM ' . TABLE_PREFIX . 'notebook_pages WHERE id IN (?)', $notebook_page_ids);
                foreach($notebook_pages as $notebook_page) {
                  $batch->insert('NotebookPage', $notebook_page['id'], "projects:projects/$notebook[project_id]/notebooks/$visibility/$notebook[id]/pages/$notebook_page[id]", 'notebook_page/created', 'Notebook', $notebook['id'], $notebook_page['created_on'], $notebook_page['created_by_id'], $notebook_page['created_by_name'], $notebook_page['created_by_email']);
                  
                  ActivityLogs::rebuildStateChangeActivityLogs('NotebookPage', $notebook_page['id'], "projects:projects/$notebook[project_id]/notebooks/$visibility/$notebook[id]/pages/$notebook_page[id]", 'Notebook', $notebook['id'], 'notebook_page');
                  
                  $comments = DB::execute('SELECT id, type, created_on, created_by_id, created_by_name, created_by_email FROM ' . TABLE_PREFIX . "comments WHERE parent_type = ? AND parent_id = ? AND state >= ?", 'NotebookPage', $notebook_page['id'], STATE_ARCHIVED);
              
                  if($comments) {
                    foreach($comments as $comment) {
                      $batch->insert($comment['type'], $comment['id'], "projects:projects/$notebook[project_id]/notebooks/$notebook[id]/pages/$notebook_page[id]/comments/$comment[id]", 'comment/created', 'NotebookPage', $notebook_page['id'], $comment['created_on'], $comment['created_by_id'], $comment['created_by_name'], $comment['created_by_email']);
                    } // foreach
                  } // if
                  
                  if($notebook_page['last_version_on']) {
                    $batch->insert('NotebookPage', $notebook_page['id'], "projects:projects/$notebook[project_id]/notebooks/$visibility/$notebook[id]/pages/$notebook_page[id]/versions/$notebook_page[version]", 'notebook_page/new_version', 'Notebook', $notebook['id'], $notebook_page['last_version_on'], $notebook_page['last_version_by_id'], $notebook_page['last_version_by_name'], $notebook_page['last_version_by_email']);
                  } // if
                  
                  if($notebook_page['version'] > 2) {
                    $update_older_verisons[] = $notebook_page['id'];
                  } // if
                } // foreach
                
                if(count($update_older_verisons)) {
                  $notebook_page_versions = DB::execute('SELECT id, notebook_page_id, version, created_on, created_by_id, created_by_name, created_by_email FROM ' . TABLE_PREFIX . 'notebook_page_versions WHERE notebook_page_id IN (?) AND version > 1', $update_older_verisons);
                  
                  if($notebook_page_versions) {
                    foreach($notebook_page_versions as $notebook_page_version) {
                      $batch->insert('NotebookPageVersion', $notebook_page_version['id'], "projects:projects/$notebook[project_id]/notebooks/$visibility/$notebook[id]/pages/$notebook_page[id]/versions/$notebook_page_version[version]", 'notebook_page_version/created', 'NotebookPage', $notebook_page_version['notebook_page_id'], $notebook_page_version['created_on'], $notebook_page_version['created_by_id'], $notebook_page_version['created_by_name'], $notebook_page_version['created_by_email']);
                    } // foreach
                  } // if
                } // if
              } // if
            } // foreach
            
            $batch->done();
          } // if
          
          DB::commit('Updated notebook page activity logs @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to update notebook page activity logs @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_notbook_pages
    
  }
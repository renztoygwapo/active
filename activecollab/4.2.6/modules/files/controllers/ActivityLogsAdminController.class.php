<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs_admin', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Activity logs controller
   * 
   * @package activeCollab.modules.files
   * @subpackage controllers
   */
  class ActivityLogsAdminController extends FwActivityLogsAdminController {
    
    /**
     * Rebuild files entries
     */
    function rebuild_files() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          ActivityLogs::rebuildProjectObjectActivityLogs(array('File', 'TextDocument'), 'files', array(
          	'File' => 'file/created', 
          	'TextDocument' => 'text_document/created',
          ), null, null, true);
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_tasks
    
    /**
     * Rebuild file versions log
     */
    function rebuild_file_versions() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        
        try {
          DB::beginWork('Rebuilding file version logs @ ' . __CLASS__);
          
          $files = DB::execute("SELECT id, project_id, visibility, integer_field_1 AS version_num, datetime_field_1 AS last_version_on, integer_field_3 AS last_version_by_id, text_field_1 AS last_version_by_name, text_field_2 AS last_version_by_email FROM $project_objects_table WHERE type = ? AND state >= ? AND integer_field_1 > ?", 'File', STATE_ARCHIVED, 1);
          if($files) {
            $files->setCasting(array(
              'id' => DBResult::CAST_INT, 
              'project_id' => DBResult::CAST_INT, 
              'visibility' => DBResult::CAST_INT, 
              'version_num' => DBResult::CAST_INT, 
              'last_version_by_id' => DBResult::CAST_INT, 
            ));
            
            $batch = DB::batchInsert(TABLE_PREFIX . 'activity_logs', array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
            
            $update_older_verisons = array();
            
            foreach($files as $file) {
              $visibility = $file['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
              
              $batch->insert('File', $file['id'], "projects:projects/$file[project_id]/files/$visibility/$file[id]/versions/$file[version_num]", 'file/new_version', 'Project', $file['project_id'], $file['last_version_on'], $file['last_version_by_id'], $file['last_version_by_name'], $file['last_version_by_email']);
              
              if($file['version_num'] > 2) {
                $update_older_verisons[] = $file['id'];
              } // if
            } // foreach
            
            if(count($update_older_verisons)) {
              $file_versions_table = TABLE_PREFIX . 'file_versions';
              
              $file_versions = DB::execute("SELECT $project_objects_table.project_id, $project_objects_table.visibility, $file_versions_table.id, $file_versions_table.file_id, $file_versions_table.version_num, $file_versions_table.created_on, $file_versions_table.created_by_id, $file_versions_table.created_by_name, $file_versions_table.created_by_email FROM $file_versions_table, $project_objects_table WHERE $file_versions_table.file_id = $project_objects_table.id AND $project_objects_table.type = 'File' AND $project_objects_table.id IN (?)", $update_older_verisons);
              
              if($file_versions) {
                foreach($file_versions as $file_version) {
                  $visibility = $file_version['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
                  
                  $batch->insert('File', $file_version['file_id'], "projects:projects/$file_version[project_id]/files/$visibility/$file_version[file_id]/versions/$file_version[version_num]", 'file/new_version', 'Project', $file_version['project_id'], $file_version['created_on'], $file_version['created_by_id'], $file_version['created_by_name'], $file_version['created_by_email']);
                } // foreach
              } // if
            } // if

            $batch->done();
          } // if
          
          DB::commit('File version logs @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to rebuild file version logs @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_file_versions
    
    /**
     * Rebuild text document versions
     */
    function rebuild_text_document_versions() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $project_objects_table = TABLE_PREFIX . 'project_objects';
        
        try {
          DB::beginWork('Rebuilding text document version logs @ ' . __CLASS__);
          
          $text_documents = DB::execute("SELECT id, project_id, visibility, integer_field_1 AS version_num, datetime_field_1 AS last_version_on, integer_field_2 AS last_version_by_id, varchar_field_1 AS last_version_by_name, varchar_field_2 AS last_version_by_email FROM $project_objects_table WHERE type = ? AND state >= ? AND integer_field_1 > ?", 'TextDocument', STATE_ARCHIVED, 1);
          if($text_documents) {
            $text_documents->setCasting(array(
              'id' => DBResult::CAST_INT, 
              'project_id' => DBResult::CAST_INT, 
              'visibility' => DBResult::CAST_INT, 
              'version_num' => DBResult::CAST_INT, 
              'last_version_by_id' => DBResult::CAST_INT, 
            ));
            
            $batch = DB::batchInsert(TABLE_PREFIX . 'activity_logs', array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
            
            $update_older_verisons = array();
            
            foreach($text_documents as $text_document) {
              $visibility = $text_document['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
              
              $batch->insert('TextDocument', $text_document['id'], "projects:projects/$text_document[project_id]/files/$visibility/$text_document[id]/versions/$text_document[version_num]", 'text_document/new_version', 'Project', $text_document['project_id'], $text_document['last_version_on'], $text_document['last_version_by_id'], $text_document['last_version_by_name'], $text_document['last_version_by_email']);
              
              if($text_document['version_num'] > 2) {
                $update_older_verisons[] = $text_document['id'];
              } // if
            } // foreach
            
            if(count($update_older_verisons)) {
              $text_document_versions_table = TABLE_PREFIX . 'text_document_versions';
              
              $text_document_versions = DB::execute("SELECT $project_objects_table.project_id, $project_objects_table.visibility, $text_document_versions_table.id, $text_document_versions_table.text_document_id, $text_document_versions_table.version_num, $text_document_versions_table.created_on, $text_document_versions_table.created_by_id, $text_document_versions_table.created_by_name, $text_document_versions_table.created_by_email FROM $text_document_versions_table, $project_objects_table WHERE $text_document_versions_table.text_document_id = $project_objects_table.id AND $project_objects_table.type = 'TextDocument' AND $project_objects_table.id IN (?)", $update_older_verisons);
              
              if($text_document_versions) {
                foreach($text_document_versions as $text_document_version) {
                  $visibility = $text_document_version['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
                  
                  $batch->insert('TextDocument', $text_document_version['text_document_id'], "projects:projects/$text_document_version[project_id]/files/$visibility/$text_document_version[text_document_id]/versions/$text_document_version[version_num]", 'text_document/new_version', 'Project', $text_document_version['project_id'], $text_document_version['created_on'], $text_document_version['created_by_id'], $text_document_version['created_by_name'], $text_document_version['created_by_email']);
                } // foreach
              } // if
            } // if

            $batch->done();
          } // if
          
          DB::commit('Text document version logs @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to rebuild text document version logs @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_text_document_versions
    
  }
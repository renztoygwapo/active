<?php

/**
 * Application level disk space implementation
 *
 * @package activeCollab.modules.system
 * @subpackage models
 */
class DiskSpace extends FwDiskSpace {

  /**
   * Return disk space usage by project ID
   *
   * @param integer $project_id
   * @return integer
   */
  public static function getUsageByProjectId($project_id) {
    $project_disk_space_usage = 0;

    $project_objects_table = TABLE_PREFIX . 'project_objects';
    $file_versions_table = TABLE_PREFIX . 'file_versions';

    $file_ids = DB::executeFirstColumn("SELECT id FROM $project_objects_table WHERE type = 'File' AND project_id = ?", $project_id);

    if($file_ids) {
      $project_disk_space_usage += DB::executeFirstCell("SELECT SUM(integer_field_2) FROM $project_objects_table WHERE id IN (?)", $file_ids);
      $project_disk_space_usage += DB::executeFirstCell("SELECT SUM(size) FROM $file_versions_table WHERE file_id IN (?)", $file_ids);
    } // if

    $rows = DB::execute("SELECT id, type FROM $project_objects_table WHERE project_id = ?", $project_id);

    if($rows) {
      $parents = array();

      foreach($rows as $row) {
        if(isset($parents[$row['type']])) {
          $parents[$row['type']][] = (integer) $row['id'];
        } else {
          $parents[$row['type']] = array((integer) $row['id']);
        } // if
      } // foreach

      if(isset($parents['Notebook']) && is_foreachable($parents['Notebook'])) {
        foreach($parents['Notebook'] as $notebook_id) {
          $notebook_page_ids = NotebookPages::getAllIdsByNotebook($notebook_id);

          if(is_foreachable($notebook_page_ids)) {
            foreach($notebook_page_ids as $notebook_page_id) {
              if(array_key_exists('NotebookPage', $parents)) {
                $parents['NotebookPage'][] = (integer) $notebook_page_id;
              } else {
                $parents['NotebookPage'] = array((integer) $notebook_page_id);
              } // if
            } // foreach
          } // if
        } // foreach
      } // if

      Attachments::getDiscSpaceUsageByParents($parents, $project_disk_space_usage);
      Comments::getAttachmentDiscSpaceUsageByParents($parents, $project_disk_space_usage);
    } // if

    return $project_disk_space_usage;
  } // getUsageByProjectId

}
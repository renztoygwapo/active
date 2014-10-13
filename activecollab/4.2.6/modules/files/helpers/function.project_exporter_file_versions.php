<?php

/**
 * project_exporter_file_versions helper
 *
 * @package activeCollab.modules.files
 * @subpackage helpers
 */

/**
 * Renders file versions table for project exporter
 *
 * Parameters:
 *
 * - file - instance of File
 *
 * @param array $params
 * @param Smarty $template
 * @return string
 */

function smarty_function_project_exporter_file_versions($params, $template) {
  /**
   * @var File $file
   */
  $file = array_required_var($params,'file',true,'File');
  AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');



  $file_name = $template->tpl_vars['exporter']->value->storeFile($file->getName(), UPLOAD_PATH . '/' . $file->getLocation(), false);
  $return = '<h3>'.lang('Latest Version').'</h3>';
  $return .= '<table class="common_table"><tr>';

  $return .= '<td class="column_id">#' . (FileVersions::countByFile($file) + 1) . '</td>';
  $return .= '<td class="column_name">' . '<a href="' . $file_name . '" target="_blank">' . clean($file->getName()) . '</a></td>';
  $return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $file->getCreatedById(), 'name' => $file->getCreatedByName(), 'email' => $file->getCreatedByEmail()), $template) .'</td>';
  $return .= '<td class="column_date">' . smarty_modifier_date($file->getLastVersionOn()) . '</td>';
  $return .= '<td class="column_options">' . '<a href="' . $file_name . '" target="_blank">' . lang('Download') . '</a></td>';
  $return .= '</tr></table>';

  $file_versions = $file->versions()->get();

  if (is_foreachable($file_versions)) {
    $return .= '<h3>'.lang('Previous Versions').'</h3>';
    $return .= '<table class="common_table">';
    foreach ($file_versions as $file_version) {

      $file_name = $template->tpl_vars['exporter']->value->storeFile($file_version->getName(), UPLOAD_PATH . '/' . $file_version->getLocation(), false);

      $return .= '<tr>';
      /**
       * @var FileVersion $file_version
       */
      $return .= '<td class="column_id">#' . ($file_version->getVersionNum()) . '</td>';
      $return .= '<td class="column_name">' . '<a href="../_uploaded_files/' . $file_name . '">' . clean($file_version->getName()) . '</a></td>';
      $return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $file_version->getCreatedById(), 'name' => $file_version->getCreatedByName(), 'email' => $file_version->getCreatedByEmail()), $template) .'</td>';
      $return .= '<td class="column_date">' . smarty_modifier_date($file_version->getCreatedOn()) . '</td>';
      $return .= '<td class="column_options">' . '<a href="../_uploaded_files/' . $file_name . '">' . lang('Download') . '</a></td>';
      $return .= '</tr>';
    } //foreach
    $return .= '</table>';
  } //if


  return $return;

} // smarty_function_project_exporter_task_list
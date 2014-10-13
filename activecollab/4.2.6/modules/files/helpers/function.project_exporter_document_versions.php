<?php

/**
 * project_exporter_document_versions helper
 *
 * @package activeCollab.modules.files
 * @subpackage helpers
 */

/**
 * Renders document versions table for project exporter
 *
 * Parameters:
 *
 * - document - instance of TextDocument
 *
 * @param array $params
 * @param Smarty $template
 * @return string
 */

function smarty_function_project_exporter_document_versions($params, $template) {
  /**
   * @var TextDocument $text_document
   */
  $text_document = array_required_var($params,'text_document',true,'TextDocument');
  AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');



  $return = '<h3>'.lang('Latest Version').'</h3>';
  $return .= '<table class="common_table"><tr>';

  $return .= '<td class="column_id">#' . (TextDocumentVersions::countByTextDocument($text_document) + 1) . '</td>';
  $return .= '<td class="column_name">' . clean($text_document->getName()) . '</td>';
  $return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $text_document->getCreatedById(), 'name' => $text_document->getCreatedByName(), 'email' => $text_document->getCreatedByEmail()), $template) .'</td>';
  $return .= '<td class="column_date">' . smarty_modifier_date($text_document->getLastVersionOn()) . '</td>';
  $return .= '</tr></table>';

  $text_document_versions = $text_document->versions()->get();

  if (is_foreachable($text_document_versions)) {
    $return .= '<h3>'.lang('Previous Versions').'</h3>';
    $return .= '<table class="common_table">';
    foreach ($text_document_versions as $text_document_version) {

      $return .= '<tr>';
      /**
       * @var TextDocumentVersion $text_document_version
       */
      $permalink = $template->tpl_vars['url_prefix']->value . 'files/file_' . $text_document->getId() . '_document_version_' . $text_document_version->getVersionNum() . '.html';
      $return .= '<td class="column_id">#' . ($text_document_version->getVersionNum()) . '</td>';
      $return .= '<td class="column_name">' . '<a href="' . $permalink . '">' . clean($text_document_version->getName()) . '</a></td>';
      $return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $text_document_version->getCreatedById(), 'name' => $text_document_version->getCreatedByName(), 'email' => $text_document_version->getCreatedByEmail()), $template) .'</td>';
      $return .= '<td class="column_date">' . smarty_modifier_date($text_document_version->getCreatedOn()) . '</td>';
      $return .= '</tr>';
    } //foreach
    $return .= '</table>';
  } //if


  return $return;

} // smarty_function_project_exporter_task_list
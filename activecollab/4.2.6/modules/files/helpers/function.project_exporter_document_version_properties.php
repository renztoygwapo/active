<?php

/**
 * function.project_exporter_document_version_properties helper
 *
 * @package activeCollab.modules.files
 * @subpackage helpers
 */

/**
 * Renders a text document version properties
 *
 * Parameters:
 *
 * - TextDocumentVersion - text document version of which properties are shown
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */

function smarty_function_project_exporter_document_version_properties($params, $template) {
  AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');

  $return ='';

  $text_document_version = array_var($params, 'object');
  if (!($text_document_version instanceof TextDocumentVersion)) {
    throw new InvalidInstanceError('text_document_version', $text_document_version, 'TextDocumentVersion');
  } // if
  /**
   * @var TextDocumentVersion $text_document_version
   */

  $return.= '<dl class="properties">';
  // created by
  if ($text_document_version->getCreatedByEmail() || $text_document_version->getCreatedByName() || $text_document_version->getCreatedById()) {
    $return.= '<dt>' . lang('Created by') . ':</dt>';
    $return.= '<dd>' . smarty_function_project_exporter_user_link(array('id' => $text_document_version->getCreatedById(), 'name' => $text_document_version->getCreatedByName(), 'email' => $text_document_version->getCreatedByEmail()), $template) . '</dd>';
  } // if

  // created on
  if ($text_document_version->getCreatedOn() instanceof DateValue) {
    $return.= '<dt>' . lang('Created on') . ':</dt>';
    $return.= '<dd>' . smarty_modifier_date($text_document_version->getCreatedOn()) . '</dd>';
  } // if

  //current version
  $return.= '<dt>' . lang('Current version') . ':</dt>';
  $return.= '<dd><a href="file_'. $text_document_version->getTextDocumentId() .'.html">' . $text_document_version->getTextDocument()->getName() . '</a></dd>';

  $return.= '<dt>' . lang('Body') . ':</dt>';
  $return.= '<dd><div class="body content">' . HTML::toRichText($text_document_version->getBody()) . '</div></dd>';

  $return.= '</dl>';
  return $return;
} // smarty_function_project_exporter_document_version_properties
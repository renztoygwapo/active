<?php

  /**
   * document_versions helper implementation
   *
   * @package activeCollab.modules.files
   * @subpackage helpers
   */

  /**
   * Render documents versions table
   * 
   * Parameters:
   * 
   * - document - Selected text document
   * - user - User who is viewing the page
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_text_document_versions($params, &$smarty) {
    static $ids = array();
        
    $document = array_var($params, 'document');
    if(!($document instanceof TextDocument)) {
      throw new InvalidInstanceError('document', $document, 'TextDocument');
    } // if
        
    $user = array_var($params, 'user');
    if(!($user instanceof User)) {
      throw new InvalidInstanceError('user', $user, 'User');
    } // if
      
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter = 1;
      do {
        $id = 'text_document_versions_' . $counter++;
      } while(in_array($id, $ids));
    } // if
    $ids[] = $id;
    
    $smarty->assign(array(
      '_document_version_num' => $document->getVersionNum(), 
      '_document_versions' => $document->versions()->get(), 
      '_document_versions_document' => $document, 
      '_document_versions_user' => $user, 
      '_document_versions_id' => $id,
      '_compare_versions_url' => Router::assemble('compare_versions'),
    ));
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    return $smarty->fetch(get_view_path('_text_document_versions', 'text_documents', FILES_MODULE, $interface));
  } // smarty_function_text_document_versions
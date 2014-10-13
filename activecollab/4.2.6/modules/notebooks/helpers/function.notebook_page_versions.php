<?php

  /**
   * notebook_page_versions helper implementation
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */

  /**
   * Render notebook page versions list
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_notebook_page_versions($params, &$smarty) {
    $page = array_required_var($params, 'page', null, 'NotebookPage');
    $user = array_required_var($params, 'user', null, 'User');
    /**
     * @var NotebookPage $page
     */

    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('notebook_page_versions');
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      $settings = array(
  			'object' => array(
      		'id' => $page->getId(),
      		'class' => get_class($page),
      		'listen' => $page->getUpdatedEventName(),
      		'name' => $page->getName(),
      		'body' =>  $page->getBody(),
      		'body_formatted' => HTML::toRichText($page->getBody()),  
      		'updated_by' => $page->getUpdatedBy(),
      		'updated_on' => $page->getUpdatedOn(),
      		'revision_num' => $page->getVersion()
      	),
      	
        'compare_versions_url' => Router::assemble('compare_versions'), 
        'revert_to_version_url' => $page->getRevertUrl('--REVERT-TO-VERSION--'),
        'can_manage' => $page->canEdit($user),
      );

      $current_request = $smarty->getVariable('request')->value;
      $settings['event_scope'] = $current_request->getEventScope();


      foreach ($page->getVersions() as $version) {
        /**
         * @var NotebookPageVersion $version
         */
        $settings['revisions'][] = array(
          'version' => $version->getVersion(), 
          'name' => $version->getName(), 
          'body' => HTML::toRichText($version->getBody()),
        	'created_by' => array(
        		'display_name' => $version->getCreatedBy() instanceof IUser ? $version->getCreatedBy()->getDisplayName(true) : lang('Unknown User')
        	), 
        	'created_on' => $version->getCreatedOn(), 
          'urls' => array(
        		'preview' => $version->getPreviewUrl(),
        		'delete' => $settings['can_manage'] ? $version->getDeleteUrl() : '#'
        	),
        );
      } // foreach

      AngieApplication::useWidget('notebook_page_versions', NOTEBOOKS_MODULE);
      return '<div id="' . $id . '"></div><script type="text/javascript">$("#' . $id . '").notebookPageVersions("init", ' . JSON::encode($settings) . ');</script>';
    
    //printer interface
    } else if($interface == AngieApplication::INTERFACE_PRINTER) {
      $by_user = $page->getUpdatedBy() instanceof IUser ? $page->getUpdatedBy()->getDisplayName(true) : lang('Unknown User');
        
      $result = '
      <h2>' . lang('Page Versions') . '</h2>
      <table class="notebook_page_versions common">';
      
       $result.= '<tr>
        	<td class="version_num">' . lang('#:version_num by :user on :date', array('version_num' => $page->getVersion(), 'user' => $by_user, 'date' => $page->getUpdatedOn()->formatForUser($user,0))) . '<span class="latest">' . lang('Latest') . '</span></td>
        </tr>';
       
      foreach ($page->getVersions() as $version) {
        $by_user = $version->getCreatedBy() instanceof IUser ? $version->getCreatedBy()->getDisplayName(true) : lang('Unknown User');
        
        $result.= '<tr>
        	<td class="version_num">' . lang('#:version_num by :user on :date', array('version_num' => $version->getVersion(), 'user' => $by_user, 'date' => $version->getCreatedOn()->formatForUser($user,0))) . '</td>
        </tr>';
      } // foreach
      $result .= '</table>';
      
      return $result;
    }//if
    
    
    
  } // smarty_function_notebook_page_versions
<?php

  /**
   * Documents class
   * 
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class Documents extends BaseDocuments {
    
    /**
     * Returns true if $user can use documents section
     * 
     * @param User $user
     * @return boolean
     */
    static function canUse(User $user) {
      if($user->isAdministrator()) {
        return true;
      } elseif($user->isManager()) {
        return $user->getSystemPermission('can_manage_documents');
      } else {
        return $user->getSystemPermission('can_use_documents');
      } // if
    } // canUse

    /**do
     * Returns true if $user can manage global documents
     *
     * @param User $user
     * @return boolean
     */
    static function canManage(User $user) {
      return $user->isAdministrator() || ($user->isManager() && $user->getSystemPermission('can_manage_documents'));
    } // canManage

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    
    /**
     * Return all documents
     *
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findAll($min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Documents::find(array(
        'conditions' => array('state >= ? AND visibility >= ?', $min_state, $min_visibility), 
        'order' => 'name', 
      ));
    } // findAll
    
    /**
     * Return documents by IDs
     *
     * @param array $ids
     * @param integer $min_state
     * @param integer $min_visibility
     * @return DBResult
     */
    static function findByIds($ids, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Documents::find(array(
        'conditions' => array('id IN (?) AND state >= ? AND visibility >= ?', $ids, $min_state, $min_visibility), 
        'order' => 'name', 
      ));
    } // findByIds
  	
  	/**
     * Return all documents that belong to a category
     *
     * @param DocumentCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findByCategory(DocumentCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Documents::find(array(
        'conditions' => array('category_id = ? AND state >= ? AND visibility >= ?', $category->getId(), $min_state, $min_visibility),
        'order' => 'name', 
      ));
    } // findByCategory
    
    /**
     * Return number of documents by category
     * 
     * @param DocumentCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return integer
     */
    static function countByCategory(DocumentCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return Documents::count(array('category_id = ? AND state >= ? AND visibility >= ?', $category->getId(), $min_state, $min_visibility));
    } // countByCategory

    /**
     * Find for objects list
     *
     * @param User $user
     * @param int $state
     * @return array
     */
    static function findForObjectsList(User $user, $state = STATE_VISIBLE) {
      $documents_url = Router::assemble('document', array('document_id' => '--DOCUMENTID--'));

      $documents = DB::execute("SELECT id, name, LOWER(SUBSTRING(name, 1, 1)) AS first_letter, category_id, state, visibility FROM " . TABLE_PREFIX . "documents WHERE visibility >= ? AND state = ? ORDER BY is_pinned DESC, name", $user->getMinVisibility(), $state);
      
      $result = array();
      foreach ($documents as $document) {
      	$result[] = array(
      		'id' => $document['id'],
      		'name' => $document['name'],
      		'first_letter' => Inflector::transliterate($document['first_letter']),
      		'category_id' => $document['category_id'],
      		'permalink' => str_replace('--DOCUMENTID--', $document['id'], $documents_url),
      		'is_archived' => $document['state'] == STATE_ARCHIVED ? 1 : 0,
          'is_favorite' => Favorites::isFavorite(array('Document', $document['id']), $user),
          'visibility' => $document['visibility']
      	);
      } // foreach
			
      return $result;
	  } // findForObjectsList
	
	  /**
     * Find documents for printing by grouping and filtering criteria
     * 
     * @param string $group_by
     * @param array $filter_by
     * @return DBResult
     */
    static function findForPrint($group_by = null, $filter_by = null) {
      
      if (!in_array($group_by, array('category_id'))) {
      	$group_by = null;
      } // if
      
      // do find documents
      $documents = self::find(array(
      	'order' => $group_by ? $group_by : 'id ASC' 
      ));
    	
    	return $documents;
    } // findForPrint
    
    /**
     * Get trashed map
     * 
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
      return array(
        'document' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'documents WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED)
      );
    } // getTrashedMap
    
    /**
     * Find trashed projects
     * 
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
      $trashed_documents = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'documents WHERE state = ? ORDER BY created_on DESC', STATE_TRASHED);
      if (!is_foreachable($trashed_documents)) {
        return null;
      } // if

      $view_url = Router::assemble('document', array('document_id' => '--DOCUMENT-ID--'));

      $items = array();
      foreach ($trashed_documents as $document) {
        $items[] = array(
          'id'            => $document['id'],
          'name'          => $document['name'],
          'type'          => 'Document',
          'permalink'      => str_replace('--DOCUMENT-ID--', $document['id'], $view_url),
          'can_be_parent' => false,
        );
      } // foreach

      return $items;
    } // findTrashed
    
    /**
     * Delete trashed projects
     */
    static function deleteTrashed() {
      $documents = Documents::find(array(
        'conditions' => array('state = ?', STATE_TRASHED)
      ));

      if (is_foreachable($documents)) {
        foreach ($documents as $document) {
          $document->state()->delete();
        } // foreach
      } // if

      return true;
    } // deleteTrashed

    /**
     * Return contexts by user
     *
     * @param User $user
     * @param array $contexts
     * @param array $ignore_contexts
     */
    static function getContextsByUser(User $user, &$contexts, &$ignore_contexts) {
      if($user instanceof User) {
        if (Documents::canUse($user)) {
          if ($user->canSeePrivate()) {
            $contexts[] = "documents:documents/%";
          } else {
            $contexts[] = "documents:documents/normal/%";
          } // if
        } // if
      } // if
    } // getContextsByUser

    /**
     * Get list of uses with access to the documents section
     *
     * @return array
     */
    static function getUsersWithoutDocumentAccess() {
      return Users::findIdsByType(null, STATE_VISIBLE, null, function($id, $type, $custom_permissions, $state) {
        if($type == 'Administrator') {
          return false;
        } else {
          return !in_array('can_use_documents', $custom_permissions) && !in_array('can_manage_documents', $custom_permissions);
        } // if
      });
    } // getUsersWithAccess

  }
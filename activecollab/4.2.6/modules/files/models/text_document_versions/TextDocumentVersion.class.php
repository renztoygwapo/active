<?php

  /**
   * TextDocumentVersion class
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class TextDocumentVersion extends BaseTextDocumentVersion implements IObjectContext {
  	    
    /**
     * Cached parent text document instance
     *
     * @var TextDocument
     */
    private $text_document = false;
    
    /**
     * Return parent text document
     *
     * @return TextDocument
     */
    function getTextDocument() {
      if($this->text_document === false) {
        $this->text_document = ProjectObjects::findById($this->getTextDocumentId());
      } // if
      
      return $this->text_document;
    } // getTextDocument
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return $this->getTextDocument()->getObjectContextDomain();
    } // getContextDomain
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return $this->getTextDocument()->getObjectContextPath() . '/versions/' . $this->getVersionNum();
    } // getContextPath
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can see this file version
     *
     * @param User $user
     * @return bolean
     */
    function canView(User $user) {
    	return $this->getTextDocument()->canView($user);
    } // canView
    
    /**
     * Returns true if $user can delete this file version
     *
     * @param IUser $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $this->getTextDocument()->canDelete($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  URLS
    // ---------------------------------------------------
    
    /**
     * Return view url
     * 
     * @return string
     */
    function getViewUrl() {
    	return Router::assemble('project_assets_text_document_version', array(
    		'project_slug' => $this->getTextDocument()->getProject()->getSlug(),
    		'asset_id' => $this->getTextDocumentId(),
    		'version_num' => $this->getVersionNum()
    	));
    } // getViewUrl
    
    /**
     * Return delete url
     * 
     * @return string
     */
    function getDeleteUrl() {
    	return Router::assemble('project_assets_text_document_version_delete', array(
    		'project_slug' => $this->getTextDocument()->getProject()->getSlug(),
    		'asset_id' => $this->getTextDocumentId(),
    		'version_num' => $this->getVersionNum()
    	));
    } // getDeleteUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	if ($this->canDelete($user)) {
	      $options->add('text_document_version_delete', array(
	        'url' => $this->getDeleteUrl(),
	        'text' => lang('Delete'),
	        'onclick' => new AsyncLinkCallback('text_document_version_deleted'),
	      ), true);
    	} // if
    } // prepareOptionsFor
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
    	$result = parent::describe($user, $detailed, $for_interface);

			$result['version'] = $this->getVersionNum();
      $result['body'] = $this->getBody();
			$result['urls']['delete'] = $this->getDeleteUrl();
			$result['urls']['view'] = $this->getViewUrl();

			if(empty($result['created_by'])) {
			  $result['created_by'] = $this->getCreatedBy() instanceof IUser ? $this->getCreatedBy()->describe($user, false, $for_interface) : null;
			} // if

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describe($user, $detailed);

      $result['version'] = $this->getVersionNum();
      $result['body'] = $this->getBody();
      $result['urls']['delete'] = $this->getDeleteUrl();
      $result['urls']['view'] = $this->getViewUrl();

      if(empty($result['created_by'])) {
        $result['created_by'] = $this->getCreatedBy() instanceof IUser ? $this->getCreatedBy()->describeForApi($user) : null;
      } // if

      return $result;
    } // describeForApi
    
  }
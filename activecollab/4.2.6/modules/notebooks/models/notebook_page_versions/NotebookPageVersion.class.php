<?php

  /**
   * NotebookPageVersion class
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class NotebookPageVersion extends BaseNotebookPageVersion {
    
    /**
     * List of rich text fields
     *
     * @var array
     */
    protected $rich_text_fields = array('body');
    
    /**
     * Cached notebook page instance
     *
     * @var NotebookPage
     */
    private $notebook_page = false;
    
    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? 
        lang('page version', null, true, $language) : 
        lang('Page Version', null, true, $language);
    } // getVerboseType
    
    /**
     * Return notebook page
     *
     * @return NotebookPage
     */
    function getNotebookPage() {
      if($this->notebook_page === false) {
        $this->notebook_page = $this->getNotebookPageId() ? NotebookPages::findById($this->getNotebookPageId()) : null;
      } // if
      
      return $this->notebook_page;
    } // getNotebookPage
  
    /**
     * Set parent notebook page
     *
     * @param NotebookPage $notebook_page
     */
    function setNotebookPage($notebook_page) {
      if($notebook_page instanceof NotebookPage) {
        $this->setNotebookPageId($notebook_page->getId());
        $this->notebook_page = $notebook_page;
      } else {
        throw new InvalidInstanceError('notebook_page', $notebook_page, 'NotebookPage');
      } // if
    } // setNotebookPage
    
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
      
      $result['notebook_page_id'] = $this->getNotebookPageId();
      $result['version'] = $this->getVersion();
      $result['created_by'] = $this->getCreatedBy();
      
      $result['urls']['view'] = $this->getViewUrl();
      $result['urls']['preview'] = $this->getPreviewUrl();
      $result['urls']['delete'] = $this->getDeleteUrl();
      
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
      $result = parent::describeForApi($user, $detailed);

      $result['notebook_page_id'] = $this->getNotebookPageId();
      $result['version'] = $this->getVersion();
      $result['created_by'] = $this->getCreatedBy() instanceof IUser ? $this->getCreatedBy()->describeForApi($user) : null;

      $result['urls']['view'] = $this->getViewUrl();
      $result['urls']['preview'] = $this->getPreviewUrl();
      $result['urls']['delete'] = $this->getDeleteUrl();

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      if($this->getNotebookPage() instanceof NotebookPage) {
        return $this->getNotebookPage()->getObjectContextPath() . '/versions/' . $this->getId();
      } else {
        throw new InvalidInstanceError('notebook_page', $this->getNotebookPage(), 'NotebookPage');
      } // if
    } // getContextPath
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Return true if $user can view this page version
     * 
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $this->getNotebookPage()->canView($user);
    } // canView
    
    /**
     * Page versions can't be updated
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return false;
    } // canEdit
    
    /**
     * Returns true if $user can delete this version
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $this->getNotebookPage()->canEdit($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view URL
     *
     * @return string
     */
    function getViewUrl() {
      return $this->getNotebookPage() instanceof NotebookPage ? $this->getNotebookPage()->getCompareVersionsUrl($this) : '#';
    } // getViewUrl

    /**
     * Return preview version URL
     *
     * @return string
     */
    function getPreviewUrl() {
      return $this->getNotebookPage() instanceof NotebookPage ? Router::assemble('project_notebook_page_version', array(
        'project_slug' => $this->getNotebookPage()->getProject()->getSlug(),
        'notebook_id' => $this->getNotebookPage()->getNotebook()->getId(),
        'notebook_page_id' => $this->getNotebookPage()->getId(),
        'version' => $this->getVersion()
      )) : '#';
    } // getPreviewUrl
    
    /**
     * Return delete version URL
     *
     * @return string
     */
    function getDeleteUrl() {
      return $this->getNotebookPage() instanceof NotebookPage ? Router::assemble('project_notebook_page_version_delete', array(
      	'project_slug' => $this->getNotebookPage()->getProject()->getSlug(),
      	'notebook_id' => $this->getNotebookPage()->getNotebook()->getId(),
      	'notebook_page_id' => $this->getNotebookPage()->getId(),
      	'version' => $this->getVersion()
      )) : '#';
    } // getDeleteUrl
    
  }
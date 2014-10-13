<?php

  /**
   * FileVersion class
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class FileVersion extends BaseFileVersion implements IPreview, IDownload, IObjectContext, IRoutingContext, IReadOnly {
    
    /**
     * Cached parent file instance
     *
     * @var File
     */
    private $file = false;
    
    /**
     * Return parent file
     *
     * @return File
     */
    function getFile() {
      if($this->file === false) {
        $this->file = ProjectObjects::findById($this->getFileId());
      } // if
      
      return $this->file;
    } // getFile
    
    /**
     * Return MD5 hash of this file version
     */
    function getMd5() {
      $md5 = parent::getMd5();
      
      if(empty($md5)) {
        $path = $this->download()->getPath();
        if(is_file($path)) {
          $md5 = md5_file($path);
          
          if($md5 && $this->isLoaded()) {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'file_versions SET md5 = ? WHERE file_id = ? AND version_num = ?', $md5, $this->getFileId(), $this->getVersionNum());
            AngieApplication::cache()->removeByObject($this);
          } // if
        } // if
      } // if
      
      return $md5;
    } // getMd5
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'project_assets_file_version';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array(
        'project_slug' => $this->getFile()->getProject()->getSlug(),
        'asset_id' => $this->getFileId(),
        'file_version_num' => $this->getVersionNum(),
      );
    } // getRoutingContextParams
    
    /**
     * Return object domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return $this->getFile()->getObjectContextDomain();
    } // getContextDomain
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return $this->getFile()->getObjectContextPath() . '/versions/' . $this->getVersionNum();
    } // getContextPath
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * UserAvatar implementation instance for this object
     *
     * @var IFilePreviewImplementation
     */
  	private $preview;
    
    /**
     * Return subtasks implementation for this object
     *
     * @return IUserAvatarImplementation
     */
    function preview() {
      if(empty($this->preview)) {
        $this->preview = new IFilePreviewImplementation($this);
      } // if
      
      return $this->preview;
    } // preview
    
    /**
     * Cached file version download instance
     *
     * @var IFileVersionDownloadImplementation
     */
    private $download = false;
  
    /**
     * Return file version download helper
     *
     * @return IFileVersionDownloadImplementation
     */
    function download() {
      if($this->download === false) {
        $this->download = new IFileVersionDownloadImplementation($this);
      } // if
      
      return $this->download;
    } // download
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can see this file version
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $this->getFile()->canView($user);
    } // canView
    
    /**
     * Returns true if $user can delete this file version
     *
     * @param IUser $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $this->getFile()->canDelete($user);
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return view file version URL
     *
     * @param boolean $force
     * @return string
     */
    function getViewUrl($force = false) {
      if($force) {
        return extend_url(parent::getViewUrl(), array(
          'disposition' => 'attachment',
        ));
      } else {
        return parent::getViewUrl();
      } // if
    } // getViewUrl
    
    /**
     * Return download URL
     * 
     * @return string
     */
    function getDownloadUrl() {
      return Router::assemble('project_assets_file_version_download', array(
        'project_slug' => $this->getFile()->getProject()->getSlug(), 
        'asset_id' => $this->getFileId(), 
        'file_version_num' => $this->getVersionNum(), 
      ));
    } // getDownloadUrl
    
    /**
     * Return delete file version URL
     *
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble('project_assets_file_version_delete', array(
        'project_slug' => $this->getFile()->getProject()->getSlug(), 
        'asset_id' => $this->getFileId(), 
        'file_version_num' => $this->getVersionNum(), 
      ));
    } // getDeleteUrl

    /**
     * Delete this file version
     *
     * @param bool $delete_file
     * @return bool|void
     * @throws Exception
     */
    function delete($delete_file = true) {
      try {
        $location = $this->getLocation(); // remember the location

        parent::delete(); // perform deletion from database

        // delete file
        if ($delete_file) {
          @unlink(UPLOAD_PATH . '/' . $location);
        } // if
      } catch (Exception $e) {
        throw $e;
      } // try
    } // delete
    
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
    	
    	if ($detailed) {
    	  $result['file'] = $this->getFile();
    	} // if
    	
			$result['version'] = $this->getVersionNum();
			$result['mime_type'] = $this->getMimeType();
			$result['size'] = $this->getSize();
			$result['md5'] = $this->getMd5();
			
			$result['urls']['view'] = $this->getViewUrl();
			$result['urls']['download'] = $this->getDownloadUrl();
			$result['urls']['delete'] = $this->getDeleteUrl();

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      $result['version'] = $this->getVersionNum();
      $result['mime_type'] = $this->getMimeType();
      $result['size'] = $this->getSize();
      $result['md5'] = $this->getMd5();

      $result['urls']['view'] = $this->getViewUrl();
      $result['urls']['download'] = $this->getDownloadUrl();
      $result['urls']['delete'] = $this->getDeleteUrl();

      return $result;
    } // describeForApi
    
  }
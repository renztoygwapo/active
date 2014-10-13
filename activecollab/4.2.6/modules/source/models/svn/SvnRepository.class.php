<?php

  /**
   * Subversion repository implementation
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class SvnRepository extends SourceRepository implements IRoutingContext {

    /**
     * Construct a SVN new repository
     */
    function __construct() {
      parent::__construct();
    } //__construct
    
    /**
     * Return revision number based on revision name
     * 
     * @param string $revision_name
     * @return integer
     */
    function getRevisionNumber($revision_name) {
      return $revision_name;
    } // getRevisionNumber
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'admin_source_svn_repository';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('source_repository_id' => $this->getId());
    } // getRoutingContextParams

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
      $result['project_count'] = count($this->getProjects());
      
      $result['urls']['usage'] = $this->getUsageUrl();
      
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
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    /**
     * Return source repository project usage URL
     * 
     * @return string
     */
    function getUsageUrl() {
    	return Router::assemble("admin_source_svn_repository_usage",array("source_repository_id" => $this->getId()));
    } //getUsageUrl
    
    /**
     * Use specific version control engine
     *
     * @return boolean or string
     */
    function loadEngine() {
      $svn_type = ConfigOptions::getValue('source_svn_type');
      
  	  if ($svn_type == 'extension') {
  	    require_once SOURCE_MODULE_PATH . "/engines/subversion.class.php";
  	    return true;
  	  } elseif ($svn_type == 'exec') {
  	    require_once SOURCE_MODULE_PATH . "/engines/subversionExec.class.php";
  	    if (SvnExecRepositoryEngine::executableExists(ConfigOptions::getValue('source_svn_path')) === true) {
  	      return true;
  	    } else {
  	      return lang('SVN executable path is invalid');
  	    } //if
  	  } //if
  	  return lang('Failed to load repository engine class');
    } //loadEngine
    
    /**
     * Get information about latest commit(s). Count is the number of the commmits
     *
     * @param string $branch_name
     * @param int $count
     * @param boolean $always_array
     *
     * @return SvnCommit or array of SvnCommit
     */
    function getLastCommit($branch_name, $count = 1, $always_array = false) {
    	$count = intval($count);
    	if ($count == 1) {
    		if ($always_array) {
    			return SourceCommits::find(array(
		      	'conditions'  => array('repository_id = ? AND `type` = ?', $this->getId(), 'SvnCommit'),
		        'order'       => 'commited_on DESC',
		        'limit'         => 1
		      ));
    		} else {
			  	return SourceCommits::find(array(
		      	'conditions'  => array('repository_id = ? AND `type` = ?', $this->getId(), 'SvnCommit'),
		       	'order'       => 'commited_on DESC',
		       	'one'         => true
		      ));
    		} //if
    	} elseif ($count > 1) {
    		return SourceCommits::find(array(
      		'conditions'  => array('repository_id = ? AND `type` = ?', $this->getId(), 'SvnCommit'),
        	'order'       => 'commited_on DESC',
        	'limit'         => $count
      	));
    	} else {
    		return false;
    	} //if
    } //getLastCommit


    /**
     * Returns SourceCommit by revision
     *
     * @param int $revision
     * @param string $branch_name
     *
     * @return SourceCommit
     */
    function getCommitByRevision($revision, $branch_name) {
    	return BaseSourceCommits::find(array(
          'conditions'  => array('revision_number = ? AND repository_id = ? AND type = ? AND branch_name = ?', $revision, $this->getId(), 'SvnCommit', $branch_name),
          'one'         => true
        ));
    }//getCommitByRevision
    
    /**
     * Returns object of repository engine
     *
     * @return Object of engine
     */
    function getEngine($project_id = null,$project_object_repository = null) {
      $svn_type = ConfigOptions::getValue('source_svn_type');
  	  if ($svn_type == 'extension') {
  	    return ($project_object_repository) ? new SvnRepositoryEngine($project_object_repository,$project_id) : new SvnRepositoryEngine($this,$project_id);
  	  } elseif ($svn_type == 'exec') {
  	    return ($project_object_repository) ? new SvnExecRepositoryEngine($project_object_repository,$project_id) : new SvnExecRepositoryEngine($this,$project_id);
  	  } //if
    } //getEngine
    
    /**
     * Tests if connection parameters for this repository are valid
     *
     * @return boolean
     */
    function testRepositoryConnection() {
  	  $svn_type = ConfigOptions::getValue('source_svn_type');
  	  if ($svn_type == 'extension') {
  	    return SvnRepositoryEngine::testRepositoryConnection($this);
  	  } elseif ($svn_type == 'exec') {
  	    return SvnExecRepositoryEngine::testRepositoryConnection($this);
  	  } //if
    } //testRepositoryConnection
    
    /**
     * Tests if engines can be used
     *
     * @return boolean
     */
    function engineIsUsable() {
      $svn_type = ConfigOptions::getValue('source_svn_type');
      if ($svn_type == 'extension') {
        return SvnRepositoryEngine::isUsable() === true ? true : false;
      } elseif ($svn_type == 'exec') {
        return SvnExecRepositoryEngine::isUsable() === true ? true : false;
      } //if
    } //engineIsUsable
    
    /**
     * Return new instance of SvnCommit
     * 
     * @return SvnCommit
     */
    function getNewCommit() {
      return new SvnCommit();
    } //getNewCommit
    
    /**
     * Returns the name of the repository commit
     * 
     * @return string
     */
    function getCommitName() {
      return "SvnCommit";
    } //getCommitName

    /**
     * Returns the name of the repository in real language
     *
     * @return string
     */
    function getVerboseName() {
      return lang('Subversion');
    } //getVerboseName

    /**
     * Returns the name of icon file representing the repository
     *
     * @return string
     */
    function getIconFileName() {
      return 'subversion-icon.png';
    } //getIconFileName

    /**
     * Returns true if repository has branches
     *
     * @return bool
     */
    function hasBranches() {
      return false;
    } //hasBranches

    /**
     * Returns the name of the default branch or false
     *
     * @return string or FALSE
     */
    function getDefaultBranch() {
      return '';
    } //getDefaultBranch
  
  }
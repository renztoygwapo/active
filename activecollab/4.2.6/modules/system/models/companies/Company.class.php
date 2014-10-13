<?php

  /**
   * Company class
   *
   * @package activeCollab.modules.system
   * @subpackage model
   */
  class Company extends BaseCompany implements IRoutingContext, IConfigContext, IUsersContext, IHistory, IState, ISearchItem, IObjectContext, IAvatar {
    
    /**
     * Protected company fields
     *
     * @var array
     */
    protected $protect = array('note', 'is_owner');
    
    /**
     * Return object context domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return 'people';
    } // getObjectContextDomain
    
    /**
     * Return object context path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return 'companies/' . $this->getId();
    } // getObjectContextPath
    
    /**
     * Cached inspector instance
     * 
     * @var ICompanyInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return ICompanyInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new ICompanyInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector
    
    /**
     * UserCompany implementation instance for this object
     *
     * @var ICompanyAvatarImplementation
     */
    private $avatar;
    
    /**
     * Return avatar implementation for this object
     *
     * @return ICompanyAvatarImplementation
     */
    function avatar() {
      if(empty($this->avatar)) {
        $this->avatar = new ICompanyAvatarImplementation($this);
      } // if
      
      return $this->avatar;
    } // avatar
    
    /**
     * Return users that belongs to $this company
     *
     * @param array $ids
     * @return DBResult
     */
    function getUsers($ids = array()) {
      return Users::findByCompany($this, $ids);
    } // getUsers
    
    /**
     * Return archived users that belongs to $this company
     *
     * @param array $ids
     * @return DBResult
     */
    function getArchivedUsers() {
      return Users::findArchivedByCompany($this);
    } // getUsers
    
    /**
     * Return number of users in company
     *
     * @return integer
     */
    function getUsersCount() {
      $company_id = $this->getId();
      return Users::count("company_id LIKE $company_id");
    } // getUsersCount
    
    /**
     * Return number of archived users in company
     *
     * @return integer
     */
    function getArchivedUsersCount() {
      return Users::count("company_id LIKE " . $this->getId() . " AND state = " . STATE_ARCHIVED);
    } // getArchivedUsersCount
    
    /**
     * Return number of active projects company participating in
     *
     * @return integer
     */
    function getProjectsCount() {
      return Users::count("company_id LIKE " . $this->getId() . " AND state = " . STATE_ARCHIVED);
    } // getProjectsCount
    
    /**
     * Return config option value
     *
     * @param string $name
     * @return mixed
     */
    function getConfigValue($name) {
      return ConfigOptions::getValueFor($name, $this);
    } // getConfigValue
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canEdit($user)) {
        $options->add('edit', array(
          'text' => lang('Update Details'),
          'url'  => $this->getEditUrl(),
          'icon' => AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK),
          'important' => true,
          'onclick'  => new FlyoutFormCallback('company_updated', array(
            'success_message' => lang('Company details updated'),
            'width' => 600
          ))
        ));
      } // if
      
      if(Users::canAdd($user, $this)) {
        $options->add('add_user', array(
          'text' => lang('New User'),
          'url'  => $this->getAddUserUrl(),
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? '' : AngieApplication::getImageUrl('icons/navbar/add_user.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
          'important' => true,
          'onclick'  => new FlyoutFormCallback('user_created', array(
            'success_message' => lang('New user account has been created'), 
          ))
        ), true);
      } // if
      
      if($this->canContact($user)) {
        $options->add('export_vcard', array(
          'text' => lang('Export vCard'),
          'url'  => $this->getExportVcardUrl(),
          'onclick' => new FlyoutFormCallback('export_vcard', array('width' => 'narrow'))
        ), true);
      } // if
      
      parent::prepareOptionsFor($user, $options, $interface);
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
      
      $result = array_merge($result, array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'created_on' => $this->getCreatedOn(),
        'permalink' => $this->getViewUrl(),
        'office_address' => $this->getConfigValue('office_address'),
        'office_phone' => $this->getConfigValue('office_phone'),
        'office_fax' => $this->getConfigValue('office_fax'),
        'office_homepage' => $this->getConfigValue('office_homepage'),
      ));

      if(Companies::canSeeNotes($user)) {
        $result['note'] = $this->getNote();
      } // if

      if($detailed) {
        $min_state = Trash::canAccess($user) ? $this->getState() : STATE_ARCHIVED;
        $company_users = $this->users()->get($user, $min_state);

        if($company_users) {
          $result['users'] = array();
          foreach($company_users as $company_user) {
            $result['users'][] = $company_user->describe($user, false, $for_interface);
          } // foreach
        } else {
          $result['users'] = null;
        } // if
      } // if
      
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

      $result = array_merge($result, array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'created_on' => $this->getCreatedOn(),
        'permalink' => $this->getViewUrl(),
        'office_address' => (string) $this->getConfigValue('office_address'),
        'office_phone' => (string) $this->getConfigValue('office_phone'),
        'office_fax' => (string) $this->getConfigValue('office_fax'),
        'office_homepage' => (string) $this->getConfigValue('office_homepage'),
        'is_owner' => $this->isOwner(), 
      ));

      if(Companies::canSeeNotes($user)) {
        $result['note'] = $this->getNote();
      } // if

      if($detailed) {
        $min_state = Trash::canAccess($user) ? $this->getState() : STATE_ARCHIVED;
        $company_users = $this->users()->get($user, $min_state);

        if($company_users) {
          $result['users'] = array();
          foreach($company_users as $company_user) {
            $result['users'][] = $company_user->describeForApi($user, false);
          } // foreach
        } else {
          $result['users'] = null;
        } // if
      } // if

      return $result;
    } // describeForApi
    
    /**
     * Return vCard content that represents this company
     * 
     * @param boolean $include_users
     * @param boolean $force_download
     * @param boolean $export_to_file
     * @return string
     * @throws Exception
     */
    function toVCard($include_users = false, $force_download = true, $export_to_file = false) {
      $vcard_content = '';
      
      $vcard = File_IMC::build('vCard');

      $vcard->setName('', $this->getName());
      $vcard->setFormattedName($this->getName());
      $vcard->addAddress('', '', preg_split("[\n]", $this->getConfigValue('office_address')));
      
      if($this->getConfigValue('office_phone')) {
        $vcard->set('TEL', $this->getConfigValue('office_phone'), 0);
        $vcard->addParam('TYPE', 'WORK', 'TEL', 0);
        $vcard->addParam('TYPE', 'VOICE', 'TEL', 0);
      } // if
      
      if($this->getConfigValue('office_fax')) {
        $vcard->set('TEL', $this->getConfigValue('office_fax'), 1);
        $vcard->addParam('TYPE', 'WORK', 'TEL', 1);
        $vcard->addParam('TYPE', 'FAX', 'TEL', 1);
      } // if
      
      $vcard->setUrl($this->getConfigValue('office_homepage'));
      $vcard->addOrganization($this->getName());
      
      $logo_url = $this->avatar()->getUrl(ICompanyAvatarImplementation::SIZE_PHOTO);
      $logo = strtok(basename($logo_url), '?');
      $type = strtoupper(substr(strrchr($logo, '.'), 1));
      
      if($logo != 'default.256x256.png') {
        $vcard->setPhoto(base64_encode(file_get_contents($logo_url)));
        $vcard->addParam('TYPE', $type == 'JPG' ? 'JPEG' : $type);
        $vcard->addParam('ENCODING', 'b');
      } // if
  
      if($this->getUpdatedOn()) {
        $vcard->setRevision(date('Ymd\THis\Z', strtotime($this->getUpdatedOn())));
      } // if
      
      $vcard_content .= $vcard->fetch() . "\n";
      
      if($include_users && is_foreachable($users = $this->getUsers())) {
        foreach($users as $user) {
          if($user instanceof User) {
            $vcard_content .= $user->toVCard(false, false, $this);
          } // if
        } // foreach
      } // if
      
      if($force_download) {
        header('Content-Type: text/x-vcard; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->getName() . '.vcf"');
  
        print $vcard_content;
        die();
      } elseif($export_to_file) {
        $file_path = EXPORT_DIR_PATH . '/' . $this->getName() . '.vcf';
        $file_handle = fopen($file_path, 'w+');
        
        if(!fwrite($file_handle, $vcard_content)) {
          throw new Exception(lang('Could not write company :name vCard into temporary vCard :file file', array('vcard' => $this->getName(), 'file' => $file_path)));
        } // if
        fclose($file_handle);
        @chmod($file_path, 0777);
      } else {
        return $vcard_content;
      } // if
    } // toVCard
    
    /**
     * Returns true if this company is owner
     *
     * @return boolean
     */
    function isOwner() {
      return $this->getIsOwner();
    } // isOwner
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'people_company';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('company_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Cached users helper implementation
     *
     * @var ICompanyUsersContextImplementation
     */
    private $users = false;
    
    /**
     * Return company users helper
     *
     * @return ICompanyUsersContextImplementation
     */
    function users() {
      if($this->users === false) {
        $this->users = new ICompanyUsersContextImplementation($this);
      } // if
      
      return $this->users;
    } // users
    
    /**
     * Instance of history helper
     *
     * @var IHistoryImplementation
     */
    private $history = false;
    
    /**
     * Return modification log helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      if($this->history === false) {
        $this->history = new IHistoryImplementation($this, array('name', 'note'));
      } // if
      
      return $this->history;
    } // history
    
    /**
     * State helper instance
     *
     * @var ICompanyStateImplementation
     */
    private $state = false;
    
    /**
     * Return state helper instance
     *
     * @return ICompanyStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new ICompanyStateImplementation($this);
      } // if
      
      return $this->state;
    } // state
    
    /**
     * Cached search helper implementation
     *
     * @var ICompanySearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper implementation
     * 
     * @return ICompanySearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new ICompanySearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can see this company
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      if($this->isOwner()) {
        return true;
      } // if
      
      return in_array($this->getId(), $user->visibleCompanyIds());
    } // canView
    
    /**
     * Returns true if $user can see and use contact information of this company
     * 
     * @param User $user
     * @return boolean
     */
    function canContact(User $user) {
      return $this->users()->isMember($user) || $user->canSeeContactDetails();
    } // canContact

    /**
     * Returns true if $user can see note value for this company
     *
     * @param User $user
     * @return boolean
     */
    function canSeeNote(User $user) {
      return Companies::canSeeNotes($user);
    } // canSeeNote
    
    /**
     * Can this user update company information
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isPeopleManager();
    } // canEdit
    
    /**
     * Can $user delete this company
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      if($this->isOwner() || $user->getCompanyId() == $this->getId()) {
        return false;  // Owner company cannot be deleted. Also, user cannot delete company he belongs to
      } // if
      
      $users = $this->users()->get($user);
      if($users) {
        foreach($users as $user) {
          if($user->isLastAdministrator()) {
            return false; // Can't delete company that has last administrator
          } // if
        } // foreach
      } // if
      
      return $user->isPeopleManager();
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Get export vCard URL
     *
     * @return string
     */
    function getExportVcardUrl() {
      return Router::assemble('people_company_export_vcard', array('company_id' => $this->getId()));
    } // getExportVcardUrl
    
    /**
     * Return company users URL
     *
     * @return string
     */
    function getUsersUrl() {
      return Router::assemble('people_company_users', array('company_id' => $this->getId()));
    } // getUsersUrl
    
    /**
     * Get Edit Logo URL
     *
     * @return string
     */
    function getAddUserUrl(){
      return Router::assemble('people_company_user_add', array('company_id' => $this->getId()));
    } // getAddUserUrl
    
    /**
     * Return URL of company projects page
     *
     * @return string
     */
    function getProjectsUrl() {
      return Router::assemble('people_company_projects', array('company_id' => $this->getId()));
    } // getProjectsUrl

    /**
     * Return URL of company project requests page
     *
     * @return string
     */
    function getProjectRequestsUrl() {
      return Router::assemble('people_company_project_requests', array('company_id' => $this->getId()));
    } // getProjectRequestsUrl
    
    /**
     * Return URL of company archived users page
     *
     * @return string
     */
    function getArchivedUsersUrl() {
      return Router::assemble('people_company_users_archive', array('company_id' => $this->getId()));
    } // getArchivedUsersUrl
    
    /**
     * Company projects archive
     *
     * @param integer $page
     * @return string
     */
    function getProjectsArchiveUrl($page = null) {
      $params =  array('company_id' => $this->getId());
      if($page) {
        $params['page'] = $page;
      } // if
      return Router::assemble('people_company_projects_archive', $params);
    } // getProjectsArchiveUrl
    
    // ---------------------------------------------------
    //  SYSTEM
    // ---------------------------------------------------
    
    /**
     * Validate homepage url
     *
     * @param string $homepage_url
     * @return string
     * @throws ValidationErrors
     */
    function validateHomepage($homepage_url) {
      $errors = new ValidationErrors();
      if (!empty($homepage_url)) {
        $homepage_url = valid_url_protocol($homepage_url);
        if(!is_valid_url($homepage_url)) {
          $errors->addError(lang('Homepage URL is not valid'));
        } // if
      } //if
      if ($errors->hasErrors()) {
        throw $errors;
      } else {
        return $homepage_url;
      } //if
    } //validateHomepage
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if($this->isNew()) {
          $in_use = Companies::isCompanyNameInUse($this->getName());
        } else {
          $in_use = Companies::isCompanyNameInUse($this->getName(), $this->getId());
        } // if

        if($in_use) {
          $errors->addError(lang('Company name needs to be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Company name is required'), 'name');
      } // if
      
      if($this->getIsOwner() && $this->getState() < STATE_VISIBLE) {
        $errors->addError(lang("Owner company can't be archived, trashed or deleted"));
      } // if
    } // validate
    
    /**
     * Clear cache on save
     *
     * @return boolean
     */
    function save() {
      $name_changed = $this->isModifiedField('name');
      
      parent::save();
      
      if($name_changed) {
        AngieApplication::cache()->remove('companies_id_name_map'); // remove ID - name map from cache
        AngieApplication::cache()->remove('companies_basic_info'); // remove company basic info
      } // if
      
      return true;
    } // save
    
    /**
     * Delete this company from database
     *
     * @return boolean
     */
    function delete() {
      try {
        DB::beginWork('Deleting company @ ' . __CLASS__);
      
        parent::delete();

        AngieApplication::cache()->remove('companies_id_name_map'); // remove ID - name map from cache
        AngieApplication::cache()->remove('companies_basic_info'); // remove company basic info - name map from cache
        
        $users = Users::findByCompany($this);

        if(is_foreachable($users)) {
          foreach($users as $user) {
            $user->delete();
          } // foreach
        } // if
        
        // Reset company ID for projects
        DB::execute('UPDATE ' . TABLE_PREFIX . "projects SET company_id = '0' WHERE company_id = ?", $this->getId());
        $this->avatar()->remove();
        DB::commit('Company deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete company @ ' . __CLASS__);
        throw $e;
      } // try
      
      return true;
    } // delete
        
  }
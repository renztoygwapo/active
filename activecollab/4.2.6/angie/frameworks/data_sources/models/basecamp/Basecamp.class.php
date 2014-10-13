<?php

 /**
  * Class Basecamp - New API
  *
  * @package angie.frameworks.data_sources
  * @subpackage basecamp
  */
  class Basecamp extends BasecampSource implements IDescribe {

    const ACTION_IMPORT_USERS = 'import_users';
    const ACTION_IMPORT_PROJECTS = 'import_projects';
    const ACTION_IMPORT_PROJECT = 'import_project';

    const IMPORT_SETTINGS_TODO_LIST_AS_TASK_CATEGORY = 'import_todo_list_as_task_category';
    const IMPORT_SETTINGS_TODO_LIST_AS_TASK = 'import_todo_list_as_task';

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
      $result['icon'] = $this->getIconUrl();

      $result['urls']['import'] = $this->getImportUrl();

      return $result;
    } // describe

   /**
    * Return data source name
    *
    * @return mixed
    */
    function getDataSourceName() {
      return lang('Basecamp');
    } //getDataSourceName

   /**
    * Set params
    *
    * @param $params
    */
    public function setParams($params) {
      $this->setAccountId(array_var($params,'account_id', null));
      $this->setUsername(array_var($params,'username', null));
      $this->setPassword(array_var($params,'password', null));
    } //setParams

   /**
    * Return Icon Url
    *
    * @return mixed|string
    */
    public function getIconUrl() {
      return AngieApplication::getImageUrl('icons/basecamp/32x32/icon.png', DATA_SOURCES_FRAMEWORK);
    } //getIconUrl

   /**
    * Test connection to basecamp
    *
    * @param $params
    * @return mixed
    */
    public function testConnection($params = null) {
      if($params) {
        $this->setParams($params);
        if(!$this->getAccountId()) {
          throw new Error('Please enter Basecamp account ID.');
        } //if
        if(!$this->getUsername()) {
          throw new Error('Please enter Basecamp account username.');
        } //if
        if(!$this->getPassword()) {
          throw new Error('Please enter Basecamp account password.');
        } //if
      } //if
      try {
        $user_data = $this->getMe();
        return lang('Successfully connected to :name\'s basecamp account', array('name' => $user_data->name));
      } catch (Error $e) {
        throw new Error('Basecamp connection error, please check your parameters');
      } //try

    } //test_connection

   /**
    * Actually do import
    *
    * @param $params
    * @return mixed|void
    */
    public function import($params) {
      parent::import($params);

      $action = array_var($params, 'action');
      switch($action) {
       case BASECAMP::ACTION_IMPORT_USERS:
         return $this->importUsers();
         break;
       case BASECAMP::ACTION_IMPORT_PROJECTS:
         break;
       case BASECAMP::ACTION_IMPORT_PROJECT:
         return $this->importProject($params['project_id']);
         break;
       default:
         throw new Error('Action missing');
       break;
      } //switch
    } //import

    /**
     * Validate import
     *
     * @param $params
     * @return mixed
     */
    public function validate_import($params) {
      $action = array_var($params, 'action');
      switch($action) {
        case 'import_project':
          $project_id = array_var($params, 'project_id');

          if(AngieApplication::isOnDemand()) {
            list($total, $admins, $non_admins) = $this->countMembersOnProject($project_id);
            $import_as_role = $this->getAdditionalProperty('import_users_with_role');
            $total_member_to_import = strtolower($import_as_role['type']) != 'client' ? $total : $admins;
            if(!OnDemand::canAddUsersBasedOnCurrentPlan('admins', $total_member_to_import)) {
              //user doesn't have user slots for importing this project
              return array(
                'is_valid' => false,
                'message' => lang('This project cannot be imported because it requires :num team members but you already have :used of maximum :max team members. If you need additional member slots, please upgrade to a larger plan.', array(
                  'num' => $total_member_to_import,
                  'used' => OnDemand::countActiveUsers(),
                  'max' => OnDemand::getCurrentPlan()->getUsersUsageLimit()
                )),
              );
            }//if
          } //if
          break;
        case 'import_users':
          if(AngieApplication::isOnDemand()) {
            list($total, $admins, $non_admins) = $this->countMemebersOnAccount();
            $import_as_role = $this->getAdditionalProperty('import_users_with_role');
            $total_member_to_import = strtolower($import_as_role['type']) != 'client' ? $total : $admins;
            if(!OnDemand::canAddUsersBasedOnCurrentPlan('admins', $total_member_to_import)) {
              //user doesn't have user slots for importing this project
              return array(
                'is_valid' => false,
                'message' => lang('Users cannot be imported because your plan allows :max team members.', array(
                  'max' => OnDemand::getCurrentPlan()->getUsersUsageLimit()
                )),
              );
            }//if
          } //if
          break;
      } //switch
      return true;
    } //validate_import

    /**
     * Count Memebers on Account
     *
     * @return array
     */
    private function countMemebersOnAccount() {
      $users = $this->getUsers();
      return $this->countMemebers($users);
    } //countMemebersOnAccount

    /**
     * Count how many members will be imported from project
     *
     * @param $project_id
     * @return array
     */
    private function countMembersOnProject($project_id) {
      $accesses = $this->getProjectPeopleByProjectId($project_id);
      return $this->countMemebers($accesses);
    } //countMembersOnProject

    /**
     * Count people on bc account which will be imported as Members
     *
     * @param $bc_people_list
     * @param $exclude_existing
     * @return mixed
     */
    private function countMemebers($bc_people_list, $exclude_existing = true) {
      $admins = 0;
      $not_admins = 0;
      $total = 0;
      if($bc_people_list && is_foreachable($bc_people_list)) {
        foreach($bc_people_list as $user) {

          if($exclude_existing) {
            //check if user already in activecollab / don't count him
            $user_exists = Users::findByEmail($user->email_address, true);
            if($user_exists instanceof User) {
              continue;
            }//if
          } //if

          if($user->admin == true) {
            $admins++;
          } else {
            $not_admins++;
          }//if
          $total++;
        } //foreach
      } //if
      return array($total, $admins, $not_admins);
    } //countMemebers

   /**
    * Import Users from basecamp
    *
    */
    private function importUsers() {
      $users = $this->getUsers();
      if(is_foreachable($users)) {
       foreach($users as $bc_user) {
         $this->importUser($bc_user);
       } //foreach
      } //if
    } //importUsers

   /**
    * Import user
    *
    * @param $bc_user
    * @return User $user
    */
    private function importUser($bc_user) {
      $logged_user = Authentication::getLoggedUser();
      $bc_user_email = $bc_user->email_address;

      $user = Users::findByEmail($bc_user_email, true);

      $full_name = $bc_user->name;
      $full_name_array = explode(" ", $full_name);

      if(!$user instanceof User) {
      //new user
       $user = $bc_user->admin == true ? new Administrator() : $this->getUserType($bc_user);

        $custom_permissions = $this->getUserCustomPermissions($bc_user);
        if(is_foreachable($custom_permissions)) {
          $user->setSystemPermissions($custom_permissions);
        } // if

       $attributes = array(
         'email' => $bc_user_email,
         'first_name' => $bc_user_email == $full_name ? null : $full_name_array[0],
         'last_name' => $bc_user_email == $full_name ? null : $full_name_array[1],
         'company_id' => $this->getUserCompany($bc_user)->getId(),
         'state'  => STATE_VISIBLE
       );

       $user->setCreatedOn($bc_user->created_at);
       $password = Authentication::getPasswordPolicy()->generatePassword();
       $user->setPassword($password);
       $is_new = true;

      } else {
       //existing user - change if empty
       $attributes = array();
       if(!$user->getFirstName()) {
         $attributes['first_name'] = $bc_user_email == $full_name ? null : $full_name_array[0];
       } //if
       if(!$user->getLastName()) {
         $attributes['last_name'] = $bc_user_email == $full_name ? null : $full_name_array[1];
       } //if
      } //if

      $user->setAttributes($attributes);
      $user->save();

      //import avatar if doesn't exist
      if(!$user->avatar()->avatarExists()) {
       $temporary_file = $this->downloadAttachment($bc_user->avatar_url);
       $user->avatar()->set($temporary_file);
       @unlink($temporary_file);
      } //if

      if($is_new) {
       DataSourceMappings::add($this, $user, null, $bc_user->id, DataSourceMappings::BASECAMP_EXTERNAL_TYPE_USER);
      } //if

      return $user;
    } //importUser

    /**
     * Import project by Id
     *
     * @param $bc_project_id
     */
    public function importProject($bc_project_id) {

      $project = DataSourceMappings::findObjectByExternalAndSource(DataSourceMappings::BASECAMP_EXTERNAL_TYPE_PROJECT, $bc_project_id, $this);
      if(!$project instanceof Project) {
        //isn't already imported
        $bc_project = $this->getProjectById($bc_project_id);
        if($bc_project) {
          //get creator
          $creator = $this->getBasecampUser($bc_project->creator->id);

          $created_project = ProjectCreator::create($bc_project->name, array(
            'leader' => $creator,
            'overview' => $bc_project->description ? $bc_project->description : lang('Description not provided'),
            'company' => $this->getUserCompany($creator)
          ));

          $created_project->setCreatedOn($bc_project->created_at);
          $created_project->setCreatedBy($creator);
          $created_project->save();

          if($bc_project->starred == true) {
            Favorites::addToFavorites($created_project, $creator);
          } //if

          //accesses - add people to this project
          $this->addPeopleToProject($created_project, $bc_project);

          //add to do lists
          $this->addToDoListsToProject($created_project, $bc_project);

          $this->addDiscussionsToProjects($created_project, $bc_project);

          $this->addFilesToProjects($created_project, $bc_project);

          $this->addTextDocumentsToProjects($created_project, $bc_project);

          if($bc_project->archived == true) {
            $completed_on = new DateTimeValue($bc_project->updated_at);
            $this->completeObject($created_project, $creator, $completed_on, true);
            $created_project->state()->archive();
          } //if

          DataSourceMappings::add($this, $created_project, $created_project, $bc_project->id, DataSourceMappings::BASECAMP_EXTERNAL_TYPE_PROJECT);
        } //if
      } //if

    } //importProject

    /**
     * Add/import people to project
     *
     * @param Project $project
     * @param $bc_project_data
     */
    private function addPeopleToProject(Project $project, $bc_project_data) {
      $accesses = $bc_project_data->accesses;

      if($accesses->count > 0) {
        $bc_people_to_add = $this->getProjectPeopleByProjectId($bc_project_data->id);
        if(is_foreachable($bc_people_to_add)) {

          foreach($bc_people_to_add as $bc_user) {
            $user = $this->getBasecampUser($bc_user->id);

            //add user to project
            $project->users()->add($user);
          } //foreach
        } //if
      }//if
    } //addPeopleToProject

    /**
     * Add TO-DO LISTS to created project
     *
     * @param Project $project
     * @param $bc_project_data
     */
    private function addToDoListsToProject(Project $project, $bc_project_data) {
      $lists = $bc_project_data->todolists;

      if($lists->remaining_count > 0) {
        $to_do_lists = $this->getToDoListsByProjectId($bc_project_data->id);
        if(is_foreachable($to_do_lists)) {
          foreach($to_do_lists as $list) {
            $this->addToDoListToProjectByUrl($project, $list->url);
          } //foreach
        } //if
      }//if

      if($lists->completed_count > 0) {
        //completed list
        $completed_to_do_lists = $this->getToDoListsByProjectId($bc_project_data->id, true);
        if(is_foreachable($completed_to_do_lists)) {
          foreach($completed_to_do_lists as $list) {
            $this->addToDoListToProjectByUrl($project, $list->url);
          } //foreach
        } //if
      }//if
    } //addToDoListsToProject

    /**
     * Add TO Do list as Task to project
     *
     * @param Project $project
     * @param $to_do_list_url
     */
    private function addToDoListToProjectByUrl(Project $project, $to_do_list_url) {
      //request full to-do list data
      $to_do_list = $this->requestUrl($to_do_list_url);

      $creator = $this->getBasecampUser($to_do_list->creator->id);

      switch($this->getImportSettings()) {

        case Basecamp::IMPORT_SETTINGS_TODO_LIST_AS_TASK:
          $object = new Task();

          $attributes = array(
            'name' => $this->maxLength($to_do_list->name),
            'body' => $to_do_list->description ? $to_do_list->description : lang('Body not provided'),
            'visibility' => $project->getDefaultVisibility()
          );
          $object->setCreatedOn($to_do_list->created_at);

          $object->setAttributes($attributes);
          $object->setProject($project);
          $object->setState(STATE_VISIBLE);

          $object->setCreatedBy($creator);
          $object->save();

          //add todos list comments to this task
          $this->addComments($object, $to_do_list->comments);

          if($to_do_list->completed == true) {
            $completer = $this->getBasecampUser($to_do_list->completer->id);
            $completed_on = new DateTimeValue($to_do_list->completed_at);
            $this->completeObject($object, $completer, $completed_on);
          } //if
          break;

        case Basecamp::IMPORT_SETTINGS_TODO_LIST_AS_TASK_CATEGORY:
          $object = new TaskCategory();
          $object->setParent($project);
          $object->setName($this->maxLength($to_do_list->name));
          $object->save();

          //create discussion with todos lists comments
          if(is_foreachable($to_do_list->comments)) {
            $discussion = new Discussion();
            $name = lang('Comments from ":name" ToDo List', array('name' => $to_do_list->name));
            $attributes = array(
              'name' => $this->maxLength($name),
              'body' => $to_do_list->description ? $to_do_list->description : lang('Body not provided'),
              'visibility' => $project->getDefaultVisibility()
            );
            $discussion->setCreatedOn($to_do_list->created_at);
            $discussion->setAttributes($attributes);
            $discussion->setProject($project);
            $discussion->setState(STATE_VISIBLE);
            $discussion->setCreatedBy($creator);

            $discussion->save();

            //add comments to discussion
            $this->addComments($discussion, $to_do_list->comments);
          } //if

          break;
      } //switch

      //add todos to newly created object
      $this->addTodosToObject($object, $to_do_list->todos);

      DataSourceMappings::add($this, $object, $project, $to_do_list->id, DataSourceMappings::BASECAMP_EXTERNAL_TYPE_TODO_LIST);

    } //addToDoListToProject

    /**
     * Add todos to object
     */
    public function addTodosToObject(ApplicationObject $object, $todos) {
      if(is_foreachable($todos->remaining)) {
        foreach($todos->remaining as $todo_block) {
          $this->addToDoToObjectByUrl($object, $todo_block->url);
        } //foreach
      } //if
      if(is_foreachable($todos->completed)) {
        foreach($todos->completed as $todo_block) {
          //get full todos data
          $this->addToDoToObjectByUrl($object, $todo_block->url);
        } //foreach
      } //if
    } //addTodosToObject

    /**
     * Add ToDos by Url to specific object
     *
     * @param ApplicationObject $parent
     * @param $todo_url
     */
    public function addToDoToObjectByUrl(ApplicationObject $parent, $todo_url) {
      //get full TODOs data
      $todo = $this->requestUrl($todo_url);

      if($parent instanceof Task) {
        $object = $parent->subtasks()->newSubtask();
        $project = $parent->getProject();

        $attributes = array(
          'body' => $todo->content ? $todo->content : lang('Body not provided'),
        );
      } elseif($parent instanceof TaskCategory) {
        $project = $parent->getParent();
        $object = new Task();

        $attributes = array(
          'name' => $this->maxLength($todo->content),
          'body' => $todo->content ? $todo->content : lang('Body not provided'),
          'visibility' => $project->getDefaultVisibility(),
          'category_id' => $parent->getId()
        );
        $object->setProject($project);
      } //if

      $creator = $this->getBasecampUser($todo->creator->id);

      $object->setCreatedOn($todo->created_at);
      $object->setCreatedBy($creator);
      $object->setAttributes($attributes);
      $object->setState(STATE_VISIBLE);
      $object->setDueOn($todo->due_at);

      if($todo->assignee) {
        $assignee = $this->getBasecampUser($todo->assignee->id);
        $object->setAssigneeId($assignee->getId());
      } //if

      $object->save();

      if($parent instanceof Task) {
        //add todos list comments to this task
        $this->addComments($parent, $todo->comments);
      } elseif($parent instanceof TaskCategory) {
        $this->addComments($object, $todo->comments);
      } //if

      if($todo->completed == true) {
        $completer = $this->getBasecampUser($todo->completer->id);
        $completed_on = new DateTimeValue($todo->completed_at);
        $this->completeObject($object, $completer, $completed_on);
      } //if

      DataSourceMappings::add($this, $object, $project, $todo->id, DataSourceMappings::BASECAMP_EXTERNAL_TYPE_TODO);
    } //addToDoToObjectByUrl


    /**
     * Complete object
     *
     * @param ApplicationObject $object
     * @param IUser $by
     * @param DateTimeValue $on
     * @param boolean $gag_modification_log
     */
    private function completeObject(ApplicationObject $object, IUser $by, DateTimeValue $on = null, $gag_modification_log = false) {
      if(!$on instanceof DateTimeValue) {
        $on = DateTimeValue::now();
      } //if
      switch(strtolower(get_class($object))) {
        case 'task':
          $object_table_name = TABLE_PREFIX . 'project_objects';
          break;
        case 'projectobjectsubtask':
          $object_table_name = TABLE_PREFIX . 'subtasks';
          break;
        case 'project':
          $object_table_name = TABLE_PREFIX . 'projects';
          break;
      } //switch

      DB::execute("UPDATE $object_table_name SET completed_on = ?, completed_by_id = ?, completed_by_name = ?, completed_by_email = ? WHERE id = ?", $on->toMySQL(), $by->getId(), $by->getName(), $by->getEmail(), $object->getId());
      if($object instanceof IHistory && !$gag_modification_log) {
        $log_table = TABLE_PREFIX . 'modification_logs';
        $log_values_table = TABLE_PREFIX . 'modification_log_values';

        DB::execute("INSERT INTO $log_table (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email, is_first) VALUES (?, ?, ?, ?, ?, ?, ?)", get_class($object), $object->getId(), $on->toMySQL(), $by->getId(), $by->getName(), $by->getEmail(), 0);
        $log_id = DB::lastInsertId();
        DB::execute("INSERT INTO $log_values_table (modification_id, field, value) VALUES (?, ?, ?)", $log_id, 'completed_on', $on->toMySQL());
      } //if
    } //completeObject

    /**
     * Add discussions/messages to project
     *
     * @param Project $project
     * @param $bc_project
     */
    public function addDiscussionsToProjects(Project $project, $bc_project) {
      $discussions = $this->getDiscussionsByProjectId($bc_project->id);
      if(is_foreachable($discussions)) {
        foreach($discussions as $discussion_block) {

          //get full message data
          $discussion_data = $this->getDiscussionByUrl($discussion_block->topicable->url);

          $new_discussion = new Discussion();
          $attributes = array(
            'name' => $this->maxLength($discussion_data->subject),
            'body' => $discussion_data->content ? $discussion_data->content : lang('Body not provided'),
            'visibility' => $project->getDefaultVisibility()
          );
          $creator = $this->getBasecampUser($discussion_data->creator->id);

          $new_discussion->setCreatedBy($creator);
          $new_discussion->setCreatedOn($discussion_data->created_at);
          $new_discussion->setAttributes($attributes);
          $new_discussion->setProject($project);
          $new_discussion->setState(STATE_VISIBLE);
          $new_discussion->save();

          //add comments
          $this->addComments($new_discussion, $discussion_data->comments);
          //add attachments
          $this->addAttachments($new_discussion, $discussion_data->attachments);
          //subscribe users
          $this->addSubsribers($new_discussion, $discussion_data->subscribers);

          DataSourceMappings::add($this, $new_discussion, $project, $discussion_data->id, DataSourceMappings::BASECAMP_EXTERNAL_TYPE_DISCUSSION);
        } //foreach
      } //if
    }//addDiscussionsToProjects

    /**
     * Add files/uploads to project
     *
     * @param Project $project
     * @param $bc_project
     */
    public function addFilesToProjects(Project $project, $bc_project) {
      $uploads = $this->getUploadsByProjectId($bc_project->id);
      if(is_foreachable($uploads)) {
        foreach($uploads as $upload_block) {

          //get full message data
          $upload_data = $this->getUploadByUrl($upload_block->attachable->url);

          if(is_foreachable($upload_data->attachments)) {
            $attachment_block = $upload_data->attachments[0];

            $creator = $this->getBasecampUser($attachment_block->creator->id);

            $path = $this->downloadAttachment($attachment_block->url);

            $new_file = new File();
            $attributes = array(
              'name' => $attachment_block->name ? $this->maxLength($attachment_block->name) : 'file_name',
              'body' => $attachment_block->content ? $attachment_block->content : lang('Body not provided'),
              'visibility' => $project->getDefaultVisibility()
            );
            $new_file->setCreatedOn($attachment_block->created_at);
            $new_file->setCreatedBy($creator);
            $new_file->setVersionNum(1);
            $new_file->setAttributes($attributes);
            $new_file->setProject($project);
            $new_file->setState(STATE_VISIBLE);
            $new_file->setSize($attachment_block->byte_size);

            $file_name_hash = md5($path);
            $new_file->setLocation($file_name_hash);
            $new_file->setMimeType($attachment_block->content_type);

            copy($path, UPLOAD_PATH . "/" . $file_name_hash);
            $new_file->setMd5(md5_file(UPLOAD_PATH . "/" . $file_name_hash));

            $new_file->save();

            //add comments
            $this->addComments($new_file, $upload_data->comments);

            //subscribe users
            $this->addSubsribers($new_file, $upload_data->subscribers);

            DataSourceMappings::add($this, $new_file, $project, $upload_data->id, DataSourceMappings::BASECAMP_EXTERNAL_TYPE_FILE);
            @unlink($path);

          } //if
        } //foreach
      } //if
    }//addFilesToProjects

    /**
     * Add text documents to project
     *
     * @param Project $project
     * @param $bc_project
     */
    public function addTextDocumentsToProjects(Project $project, $bc_project) {
      $text_documents = $this->getTextDocumentsByProjectId($bc_project->id);

      if(is_foreachable($text_documents)) {
        foreach($text_documents as $document_block) {

          //get full message data
          $text_document_data = $this->getTextDocumentByUrl($document_block->url);

          $creator = $this->getBasecampUser($text_document_data->last_updater->id);

          $new_text_document = new TextDocument();
          $attributes = array(
            'name' => $this->maxLength($text_document_data->title),
            'body' => $text_document_data->content ? $text_document_data->content : lang('Body not provided'),
            'visibility' => $project->getDefaultVisibility(),
          );
          $new_text_document->setCreatedOn($text_document_data->created_at);
          $new_text_document->setCreatedBy($creator);
          $new_text_document->setAttributes($attributes);
          $new_text_document->setProject($project);
          $new_text_document->setState(STATE_VISIBLE);
          $new_text_document->save();

          //add comments
          $this->addComments($new_text_document, $text_document_data->comments);
           //subscribe users
          $this->addSubsribers($new_text_document, $text_document_data->subscribers);

          DataSourceMappings::add($this, $new_text_document, $project, $text_document_data->id, DataSourceMappings::BASECAMP_EXTERNAL_TYPE_TEXT_DOCUMENT);

        } //foreach
      } //if
    }//addFilesToProjects

    /******************** Common methods ****************************/

    /**
     * Subscribe users from "subscribers" bc block to parent
     *
     * @param ISubscriptions $parent
     * @param $bc_subscribers_block
     */
    private function addSubsribers(ISubscriptions $parent, $bc_subscribers_block) {
      if(is_foreachable($bc_subscribers_block) && $parent instanceof ISubscriptions) {
        foreach($bc_subscribers_block as $bc_subscriber) {
          $subscribers[] = $this->getBasecampUser($bc_subscriber->id);
        } //foreach
        $parent->subscriptions()->set($subscribers);
      } //if
    } //addSubsribers

    /**
     * Add attachments from basecamp to project object
     *
     * @param IAttachments $parent
     * @param $bc_attachments_block
     */
    private function addAttachments(IAttachments $parent, $bc_attachments_block) {

      if(is_foreachable($bc_attachments_block)) {
        foreach($bc_attachments_block as $bc_attachment) {
          //download attachment
          $path = $this->downloadAttachment($bc_attachment->url);

          $formated_attachments[] = array(
            'path' => $path,
            'filename' => $bc_attachment->name,
            'type' => $bc_attachment->content_type,
          );
        } //foreach
        $parent->attachments()->attachFromArray($formated_attachments, true);

        //unlink tmp files
        if(is_foreachable($formated_attachments)) {
          foreach($formated_attachments as $attachment) {
            @unlink($attachment['path']);
          } //foreach
        } //if
      } //if
    } //addAttachments

    /**
     * Add coments to object
     *
     * @param IComments $parent
     * @param $bc_comments
     */
    private function addComments(IComments $parent, $bc_comments) {

      if(is_foreachable($bc_comments)) {
        foreach($bc_comments as $bc_comment) {
          $user = $this->getBasecampUser($bc_comment->creator->id);
          $additional_params = array();

          if(is_foreachable($bc_comment->attachments)) {
            foreach($bc_comment->attachments as $bc_attachment) {
              //download attachment
              $path = $this->downloadAttachment($bc_attachment->url);

              $formated_attachments[] = array(
                'path' => $path,
                'filename' => $bc_attachment->name,
                'type' => $bc_attachment->content_type,
              );
            } //foreach
            $additional_params['attach_files'] = $formated_attachments;
          } //if

          $additional_params['created_on'] = $bc_comment->created_at;

          $comment = $parent->comments()->submit($bc_comment->content, $user, $additional_params);

          //unlink tmp files
          if(is_foreachable($formated_attachments)) {
            foreach($formated_attachments as $attachment) {
              @unlink($attachment['path']);
            } //foreach
          } //if

          $project = $parent->getProject();

          DataSourceMappings::add($this, $comment, $project, $bc_comment->id, DataSourceMappings::BASECAMP_EXTERNAL_TYPE_COMMENT);

        } //foreach
      } //if
    } //addComment

    /**
     * Check to see if we imported creator already, if not import him
     *
     * @param $bc_user_id
     * @return IUser
     */
    private function getBasecampUser($bc_user_id) {
      if($bc_user_id instanceof Object) {
        $bc_user_id = $bc_user_id->id;
      } //if
      $creator = DataSourceMappings::findObjectByExternalAndSource(DataSourceMappings::BASECAMP_EXTERNAL_TYPE_USER, $bc_user_id, $this);

      //if creator isn't imported - import him from BC
      if(!$creator instanceof User) {
        try {
          $import_bc_user = $this->getUserById($bc_user_id);
          $creator = $this->importUser($import_bc_user);
        } catch(Exception $e) {

          //in case that it is example project created bt basecamp team
          $creator = Authentication::getLoggedUser();
        }//try

      } //if
      return $creator;
    } //getBasecampUser

    /**
     * Substract string to $length
     *
     * @param $string
     * @param int $length
     * @return string
     */
    private function maxLength($string, $length = 150) {
      return substr(trim($string), 0, $length);
    } //maxLength

    /************** Requests ********************/

    /**
    * Return user type based on permissions on basecamp
    *
    * @param $bc_user
    * @return User
    */
    private function getUserType($bc_user) {
      $user_role = $this->getAdditionalProperty('import_users_with_role');
      return Users::getUserInstance($user_role['type'], true);
    } //getUserType

    /**
     * Return user permissions
     *
     * @param $bc_user
     * @return array
     */
    private function getUserCustomPermissions($bc_user) {
      $user_role = $this->getAdditionalProperty('import_users_with_role');
      return $user_role['custom_permissions'];
    } //getUserCustomPermissions

   /**
    * Return company for bc user
    *
    * @param $bc_user
    * @return Company
    */
    private function getUserCompany($bc_user) {
      $company_id = $this->getAdditionalProperty('import_users_in_company');
      return Companies::findById($company_id);
    } //getUserCompany

    /****************** Attachments ***************/

    /**
     * Return all text documents by project id
     *
     * @param $project_id
     * @return mixed
     */
    private function getTextDocumentsByProjectId($project_id) {
      return $this->makeRequest('/projects/' . $project_id . '/documents');
    } //getTextDocumentsByProjectId

    /**
     * Return full text document data from basecamp by Url
     *
     * @param $url
     * @return mixed
     */
    private function getTextDocumentByUrl($url) {
      return $this->requestUrl($url);
    } //getTextDocumentByUrl

    /**
     * Return all uploads that have "attachable" type "Upload"
     * @param $project_id
     */
    private function getUploadsByProjectId($project_id) {
      $topics = $this->getAttachmentsGroupedByType($project_id);
      return $topics['Upload'];
    } //getUploads

    /**
     * Return full upload data from basecamp by Url
     *
     * @param $url
     * @return mixed
     */
    private function getUploadByUrl($url) {
      return $this->requestUrl($url);
    } //getUploadByUrl

    /**
     * Return attachments grouped by type
     *
     * @param $project_id
     * @return array
     */
    private function getAttachmentsGroupedByType($project_id) {
      $grouped_attachments = array();
      $page = 1;
      do {
        $attachments = $this->getAttachmentsByProjectId($project_id, $page);
        foreach($attachments as $attachment) {
          $grouped_attachments[$attachment->attachable->type][] = $attachment;
        } //foreach
        $page++;

      } while ($this->getAttachmentsByProjectId($project_id, $page));

      return $grouped_attachments;
    } //getTopicsGroupedByType

    /**
     * Return all attachments
     *
     * @param $project_id
     * @param $page
     * @return mixed
     */
    private function getAttachmentsByProjectId($project_id, $page = 1) {
      $params = array(
        'page' => $page
      );
      return $this->makeRequest('/projects/' . $project_id . '/attachments', $params);
    }//getAttachments

    /****************** Topics ***************/

    /**
     * Return all topics that have "topicable" type "Message" - discussions
     * @param $project_id
     */
    private function getDiscussionsByProjectId($project_id) {
      $topics = $this->getTopicsGroupedByType($project_id);
      return $topics['Message'];
    } //getDiscussions

    /**
     * Return full discussion/message data from basecamp by Url
     *
     * @param $url
     * @return mixed
     */
    private function getDiscussionByUrl($url) {
      return $this->requestUrl($url);
    } //getDiscussionByUrl

    /**
     * Return topics grouped by type
     *
     * @param $project_id
     * @return array
     */
    private function getTopicsGroupedByType($project_id) {
      $grouped_topics = array();
      $page = 1;
      do {
        $topics = $this->getTopicsByProjectId($project_id, $page);
        foreach($topics as $topic) {
          $grouped_topics[$topic->topicable->type][] = $topic;
        } //foreach
        $page++;

      } while ($this->getTopicsByProjectId($project_id, $page));

      return $grouped_topics;
    } //getTopicsGroupedByType

    /**
     * Return all topics
     *
     * @param $project_id
     * @param $page
     * @return mixed
     */
    private function getTopicsByProjectId($project_id, $page = 1) {
      $params = array(
        'page' => $page
      );
      return $this->makeRequest('/projects/' . $project_id . '/topics', $params);
    }//getTopicsByProjectId

    /**
    * Return user details from basecamp
    *
    * @param $user_id
    * @return mixed
    */
    public function getUserById($user_id) {
      return $this->makeRequest('/people/' . $user_id);
    } //getUser

   /**
    * Return me from basecamp
    *
    * @return mixed
    */
    public function getMe() {
      return $this->makeRequest('/people/me');
    } //getMe

   /**
    * Return all users from basecamp account
    *
    * @return mixed
    */
    public function getUsers() {
      return $this->makeRequest('/people');
    } //getUsers

    /**
    * Return people on this project
    *
    * @param $project_id
    * @return mixed
    */
    public function getProjectPeopleByProjectId($project_id) {
      return $this->makeRequest('/projects/' . $project_id . '/accesses');
    } //getProjectPeopleByProjectId

    /**
     * Return all TO-DO lists by project_id
     *
     * @param $project_id
     * @param $completed
     * @return mixed
     */
    public function getToDoListsByProjectId($project_id, $completed = false) {
      if($completed) {
        return $this->makeRequest('/projects/' . $project_id . '/todolists/completed');
      } //if
      return $this->makeRequest('/projects/' . $project_id . '/todolists');
    } //getToDoListsByProjectId

    /**
     * Return To Do list by id
     *
     * @param $project_id
     * @param $todo_list_id
     * @return mixed
     */
    public function getToDoListById($project_id, $todo_list_id) {
      return $this->makeRequest('/projects/' . $project_id . '/todolists/' . $todo_list_id);
    } //getToDoListById

    /**
     * Basecamp projects
     *
     * @var
     */
    private $projects = null;

   /**
    * Return all projects from basecamp account
    *
    * @return mixed
    */
    public function getProjects() {
      if($this->projects == null) {
        $this->projects = $this->makeRequest('/projects');
      } //if
      return $this->projects;
    } //getProjects

    /**
     * Basecamp archived projects
     *
     * @var
     */
    private $archived_projects = null;

    /**
     * Return all archived projects from basecamp account
     *
     * @return mixed
     */
    public function getArchivedProjects() {
      if($this->archived_projects == null) {
        $this->archived_projects = $this->makeRequest('/projects/archived');
      } //if
      return $this->archived_projects;
    } //getArchivedProjects

    /**
     * Return project data from basecamp by project_id
     *
     * @param $project_id
     * @return mixed
     */
    public function getProjectById($project_id) {
      return $this->makeRequest('/projects/' . $project_id);
    } //getProjectById

   /**
    * Return all projects from basecamp account
    *
    * @return mixed
    */
    public function getCompanies() {
      throw new NotImplementedError('getCompanies');
    } //getUsers

    /**
     * Return number of already imported projects
     *
     * @return int
     */
    public function countImportedProjects() {
      return DB::executeFirstCell('SELECT count(id) FROM ' . TABLE_PREFIX . 'data_source_mappings WHERE external_type = ? AND source_type = ? AND source_id = ?', DataSourceMappings::BASECAMP_EXTERNAL_TYPE_PROJECT, get_class($this), $this->getId());
    } //countImportedProjects

    /**
     * Return projects from basecamp that can be imported
     *
     * @return mixed
     */
    public function getProjectsForImport() {
      $active_project = $this->getProjects();
      $archived_project = $this->getArchivedProjects();
      $all_bc_projects = array_merge($active_project, $archived_project);
      if(is_foreachable($all_bc_projects)) {
        foreach($all_bc_projects as $bc_project) {
          $imported = DataSourceMappings::findObjectByExternalAndSource(DataSourceMappings::BASECAMP_EXTERNAL_TYPE_PROJECT, $bc_project->id, $this);
          if(!$imported instanceof Project) {
            $temp[] = $bc_project;
          } //if
        } //foreach
      } //if
      return $temp;
    } //getProjectsForImport

    /**
     * Return users from basecamp that can be imported
     *
     * @return mixed
     */
    public function getUsersForImport() {
      $all_bc_users = $this->getUsers();
      if(is_foreachable($all_bc_users)) {
        foreach($all_bc_users as $bc_user) {
          $user = Users::findByEmail($bc_user->email_address, true);
          if(!$user instanceof User) {
            $temp[] = $user;
          } //if
        } //foreach
      } //if
      return $temp;
    } //getUsersForImport

   /**
    * Render data source Option
    *
    * @param IUser $user
    * @return mixed|void
    */
    public function renderOptions(IUser $user) {
      $smarty =& SmartyForAngie::getInstance();
      return $smarty->fetch(get_view_path('forms/basecamp/_basecamp_admin_form', 'data_sources_admin', DATA_SOURCES_FRAMEWORK));
    } //renderOptions

   /**
    * Render source import form
    *
    */
    public function renderImportForm() {
      $smarty =& SmartyForAngie::getInstance();
      try {
        $smarty->assign(array(
          '_users' => $this->getUsersForImport(),
          '_projects' => $this->getProjectsForImport(),
          '_projects_num' => count($this->getProjects()) + count($this->getArchivedProjects()),
          '_imported_project_num' => $this->countImportedProjects(),
          '_validate_url' => $this->getValidateUrl()
        ));
        return $smarty->display(get_view_path('import_forms/basecamp/_import_form', 'data_sources', DATA_SOURCES_FRAMEWORK));
      } catch (Error $e) {
        $smarty->assign(array(
          '_error' => $e,
        ));
        return $smarty->display(get_view_path('import_forms/_import_form_error', 'data_sources', DATA_SOURCES_FRAMEWORK));
      } //try

    } //renderImportForm

  } //Basecamp

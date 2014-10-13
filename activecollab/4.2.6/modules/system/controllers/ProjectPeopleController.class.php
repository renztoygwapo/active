<?php

  // Use project controller
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Project people controller
   * 
   * This controller implements project people and permission related pages and 
   * actions
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectPeopleController extends ProjectController {
    
    /**
     * Actions available as API methods
     *
     * @var array
     */
    protected $api_actions = array('index', 'add_people', 'user_permissions', 'remove_user');
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_project->isNew()) {
        $this->response->notFound();
      } // if
      
      $this->wireframe->breadcrumbs->add('project_people', lang('People'), $this->active_project->getPeopleUrl());
    } // __construct
    
    /**
     * Show people page
     */
    function index() {
      // API
      if($this->request->isApiCall()) {
        $this->response->respondWithData($this->active_project->users(), array(
          'as' => 'project_users', 
          'detailed' => true, 
        ));
        
      // Request made via mobile device
      } elseif($this->request->isPhone()) {
      	$this->response->assign(array(
          'formatted_project_users' => Projects::findForPhoneProjectUsers($this->logged_user, $this->active_project),
          'companies' => Companies::getIdNameMap($this->logged_user->visibleCompanyIds())
        ));
        
      // Regular interface
      } else {
        $can_manage = $this->active_project->canManagePeople($this->logged_user);
        
        if($can_manage) {
          $this->wireframe->actions->add('add_project_people', lang('Add People'), $this->active_project->getAddPeopleUrl(), array(
            'onclick' => new FlyoutFormCallback('project_people_created', array(
              'width' => 640,
              'focus_first_field' => false,
            )),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),             
          ));
        } // if
        
        $this->response->assign(array(
          'can_manage' => $can_manage,
          'can_see_contact_details' => $this->logged_user->canSeeContactDetails(),
          'project_users' => Users::groupByCompany($this->active_project->users()->get($this->logged_user)),
          'companies' => Companies::getIdNameMap($this->logged_user->visibleCompanyIds()), 
        ));
      } // if
    } // index
    
    /**
     * Add people to the project
     */
    function add_people() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_project->canManagePeople($this->logged_user)) {
          $exclude_users = array();
          $project_users = $this->active_project->users()->get($this->logged_user);
          if(is_foreachable($project_users)) {
            $exclude_users = objects_array_extract($project_users, 'getId');
          } // if

          if ($this->logged_user->isOwner() || $this->logged_user->isAdministrator() || $this->logged_user->isProjectManager()) {
						$is_empty_select = is_null(Users::getForSelect($this->logged_user, $exclude_users));
          } else {
          	$is_empty_select = is_null($this->logged_user->getCompany()->users()->getForSelect($this->logged_user, $exclude_users));
          } //if
          $this->response->assign(array(
            'exclude_users' => $exclude_users,
          	'is_empty_select' => $is_empty_select,
            'default_project_role_id' => ProjectRoles::getDefaultId(), 
          ));
          
          if($this->request->isSubmitted()) {
            $user_ids = $this->request->post('users');

            $userManagedByIds = Users::getUsersManagedById($user_ids);
            if(!empty($userManagedByIds)) {
              $user_ids = array_merge($user_ids, $userManagedByIds);
              $user_ids = array_unique($user_ids);
            }
     
            $response = array();
            
            if($user_ids) {
              $users = Users::findByIds($user_ids);
            
              $project_permissions = $this->request->post('project_permissions');
              
              $role = null;
              $role_id = (integer) array_var($project_permissions, 'role_id');
              
              if($role_id) {
                $role = ProjectRoles::findById($role_id);
              } // if
              
              if($role instanceof ProjectRole) {
                $permissions = null;
              } else {
                $permissions = array_var($project_permissions, 'permissions');
                if(!is_array($permissions)) {
                  $permissions = null;
                } // if
              } // if
              
              if(is_foreachable($users)) {
                try {
                  DB::beginWork('Adding users to project @ ' . __CLASS__);
                  
                  foreach($users as $user) {
                    $this->active_project->users()->add($user, $role, $permissions, true);

                    $response[] = array(
                      'id'            => $user->getId(),
                      'company_id'    => $user->getCompanyId(),
	                    'company_name'  => $user->getCompanyName(),
                      'display_name'  => $user->getDisplayName(),
	                    'avatar_url'    => $user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)
                    );
                  } // foreach
                  
                  DB::commit('Users added to project @ ' . __CLASS__);

                  AngieApplication::cache()->removeByModel('users');
                  AngieApplication::cache()->removeByModel('projects');
                } catch(Exception $e) {
                  DB::rollback('Failed to add people to project @ ' . __CLASS__);
                  $this->response->exception($e);
                } // try
              } // if
            } // if

            $this->response->respondWithData($response);
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add_people
    
    /**
     * Show and process user permissions page
     */
    function user_permissions() {
      if ($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        $user_id = $this->request->getId('user_id');
        $user = $user_id ? Users::findById($user_id) : null;
        
        if ($user instanceof User) {
          $row = DB::executeFirstRow('SELECT role_id, permissions FROM ' . TABLE_PREFIX . 'project_users WHERE user_id = ? AND project_id = ?', $user->getId(), $this->active_project->getId());
          if(empty($row)) {
            $this->response->notFound();
          } // if
          
          if ($user->canChangeProjectPermissions($this->logged_user, $this->active_project)) {
            if ($row['role_id']) {
              $role_id = $row['role_id'];
              $permissions = null;
            } else {
              $role_id = 0;
              $permissions = $row['permissions'] ? unserialize($row['permissions']) : null;
            } // if

            $this->response->assign(array(
              'active_user' => $user,
              'role_id' => $role_id,
              'permissions' => $permissions, 
            ));
            
            if ($this->request->isSubmitted()) {
              try {
                if (!$user->isProjectManager() && !$this->active_project->isLeader($user)) {
                  $project_permissions = $this->request->post('project_permissions');
                  $role_id = (integer) array_var($project_permissions, 'role_id');

                  $role = $role_id ? ProjectRoles::findById($role_id) : null;

                  if ($role instanceof ProjectRole) {
                    $permissions = null;
                  } else {
                    $role = null;

                    $permissions = array_var($project_permissions, 'permissions');
                    if(!is_array($permissions)) {
                      $permissions = null;
                    } // if
                  } // if
                  
                  $this->active_project->users()->update($user, $role, $permissions);
                } // if

                clean_menu_projects_and_quick_add_cache($user);

                $this->response->ok();
              } catch(Exception $e) {
                $this->response->exception($e);
              } // try
            } // if
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // user_permission
    
    /**
     * Remove user from this project
     */
    function remove_user() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        $user_id = $this->request->getId('user_id');
        $user = $user_id ? Users::findById($user_id) : null;

        if($user instanceof User) {
          if($user->canRemoveFromProject($this->logged_user, $this->active_project)) {
            $this->setView(AngieApplication::getViewPath('remove_or_replace_user', 'project_people', SYSTEM_MODULE));

            $remove_or_replace_data = $this->request->post('remove_or_replace', array(
              'operation' => 'remove',
            ));

            $this->response->assign(array(
              'initial_form_action' => $this->active_project->getRemoveUserUrl($user),
              'user_can_be_removed' => $user->canRemoveFromProject($this->logged_user, $this->active_project),
              'remove_or_replace_data' => $remove_or_replace_data,
              'active_user' => $user,
              'open_responsibilities' => $this->active_project->users()->countResponsibilities($user, true),
            ));

            if($this->request->isSubmitted()) {
              try {
                $this->active_project->users()->remove($user, $this->logged_user);

                clean_menu_projects_and_quick_add_cache($user);

                $this->response->ok();
              } catch(Exception $e) {
                $this->response->exception($e);
              } // try
            } // if
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // remove_user
    
    /**
     * Replace one project user with another person
     */
    function replace_user() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_project->canManagePeople($this->logged_user)) {
          $user_id = $this->request->getId('user_id');
          $user = $user_id ? Users::findById($user_id) : null;
          
          if($user instanceof User) {
            if($user->canReplaceOnProject($this->logged_user, $this->active_project)) {
              $this->setView(AngieApplication::getViewPath('remove_or_replace_user', 'project_people', SYSTEM_MODULE));

              $remove_or_replace_data = $this->request->post('remove_or_replace', array(
                'operation' => 'replace',
                'send_notification' => true,
              ));
  
              $this->response->assign(array(
                'initial_form_action' => $this->active_project->getReplaceUserUrl($user),
                'user_can_be_removed' => $user->canRemoveFromProject($this->logged_user, $this->active_project),
                'remove_or_replace_data' => $remove_or_replace_data,
                'active_user' => $user,
                'open_responsibilities' => $this->active_project->users()->countResponsibilities($user, true),
              ));
              
              if($this->request->isSubmitted()) {
                try {
                  $replace_with_id = (integer) array_var($remove_or_replace_data, 'replace_with_id');
                  $replace_with = $replace_with_id ? Users::findById($replace_with_id) : null;
  
                  if($replace_with instanceof User) {
                    $this->active_project->users()->replace($user, $replace_with, $this->logged_user);

                    clean_menu_projects_and_quick_add_cache($user);
                    clean_menu_projects_and_quick_add_cache($replace_with);

                    if($remove_or_replace_data['send_notification']) {
                      AngieApplication::notifications()
                        ->notifyAbout('system/replaced_project_user_with', $this->active_project)
                        ->setReplacedUser($replace_with)
                        ->sendToUsers($user);

                      AngieApplication::notifications()
                        ->notifyAbout('system/replacing_project_user', $this->active_project)
                        ->setReplacingUser($user)
                        ->sendToUsers($replace_with);
                    } // if
  
                    $this->response->ok();
                  } else {
                    throw new ValidationErrors(array(
                      'replace_with_id' => lang('Required'),
                    ));
                  } // if
                } catch(Exception $e) {
                  $this->response->exception($e);
                } // try
              } // if
            } else {
              $this->response->forbidden();
            } // if
          } else {
            $this->response->notFound();
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // replace_user
    
  }
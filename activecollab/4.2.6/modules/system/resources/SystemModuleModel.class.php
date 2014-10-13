<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * System module model definition
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class SystemModuleModel extends ActiveCollabModuleModel {

    /**
     * Construct system module model definition
     *
     * @param SystemModule $parent
     */
    function __construct(SystemModule $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('companies')->addColumns(array(
        DBIdColumn::create(), 
        DBStateColumn::create(), 
        DBNameColumn::create(100),
        DBStringColumn::create('note'),
        DBActionOnByColumn::create('created'), 
        DBActionOnByColumn::create('updated'), 
        DBBoolColumn::create('is_owner', false), 
      ))->addIndices(array(
        DBIndex::create('name'),
      )));
      
      $this->addModel(DB::createTable('projects')->addColumns(array(
        DBIdColumn::create(),
        DBStringColumn::create('slug', 50), 
        DBIntegerColumn::create('template_id', 10)->setUnsigned(true),
        DBStringColumn::create('based_on_type', 50), 
        DBIntegerColumn::create('based_on_id', 10)->setUnsigned(true), 
        DBIntegerColumn::create('company_id', 5, '0')->setUnsigned(true), 
        DBIntegerColumn::create('category_id', 10)->setUnsigned(true), 
        DBIntegerColumn::create('label_id', 5)->setUnsigned(true),
        DBIntegerColumn::create('currency_id', 5)->setUnsigned(true),
        DBMoneyColumn::create('budget')->setUnsigned(true),  
        DBStateColumn::create(), 
        DBNameColumn::create(150), 
        DBUserColumn::create('leader'), 
        DBTextColumn::create('overview'),
        DBActionOnByColumn::create('completed', true), 
        DBActionOnByColumn::create('created', true), 
        DBActionOnByColumn::create('updated'),
        DBStringColumn::create('custom_field_1'),
        DBStringColumn::create('custom_field_2'),
        DBStringColumn::create('custom_field_3'),
        DBStringColumn::create('mail_to_project_code', 10),
      ))->addIndices(array(
        DBIndex::create('slug', DBIndex::UNIQUE, 'slug'), 
        DBIndex::create('mail_to_project_code', DBIndex::UNIQUE, 'mail_to_project_code'),
        DBIndex::create('company_id'),
        DBIndex::create('category_id'),
        DBIndex::create('label_id'), 
      )))->setOrderBy('ISNULL(completed_on) DESC, name');
      
      $this->addModel(DB::createTable('project_roles')->addColumns(array(
        DBIdColumn::create(DBColumn::SMALL), 
        DBNameColumn::create(50), 
        DBTextColumn::create('permissions'), 
        DBBoolColumn::create('is_default'), 
      ))->addIndices(array(
        DBIndex::create('name', DBIndex::UNIQUE, 'name'), 
      )))->setOrderBy('name');
      
      $this->addTable(DB::createTable('project_users')->addColumns(array(
        DBIntegerColumn::create('user_id', 5, '0')->setUnsigned(true), 
        DBIntegerColumn::create('project_id', 10, '0')->setUnsigned(true), 
        DBTextColumn::create('permissions'), 
        DBIntegerColumn::create('role_id', 3, '0')->setUnsigned(true), 
      ))->addIndices(array(
        DBIndexPrimary::create(array('user_id', 'project_id')), 
      )));
      
      $this->addModel(DB::createTable('project_requests')->addColumns(array(
			  DBIdColumn::create(), 
			  DBStringColumn::create('public_id', 32, ''), 
			  DBNameColumn::create(150), 
			  DBTextColumn::create('body'), 
			  DBIntegerColumn::create('status', 4, '0'), 
			  DBActionOnByColumn::create('created'),
        DBIntegerColumn::create('created_by_company_id', 10),
			  DBStringColumn::create('created_by_company_name', 100, ''),
			  DBStringColumn::create('created_by_company_address', 150, ''),
			  DBTextColumn::create('custom_field_1'), 
			  DBTextColumn::create('custom_field_2'), 
			  DBTextColumn::create('custom_field_3'), 
			  DBTextColumn::create('custom_field_4'), 
			  DBTextColumn::create('custom_field_5'),
			  DBBoolColumn::create('is_locked', false),
			  DBUserColumn::create('taken_by'),
			  DBActionOnByColumn::create('closed'),
			  DBDateTimeColumn::create('last_comment_on'),
			)));

      $this->addModel(DB::createTable('project_objects')->addColumns(array(
        DBIdColumn::create(),
        DBTypeColumn::create('ProjectObject'), 
        DBStringColumn::create('source', 50),
        DBStringColumn::create('module', 30, 'system'), 
        DBIntegerColumn::create('project_id', 10, '0')->setUnsigned(true), 
        DBIntegerColumn::create('milestone_id', 10)->setUnsigned(true), 
        DBIntegerColumn::create('category_id', 10)->setUnsigned(true), 
        DBIntegerColumn::create('label_id', 5)->setUnsigned(true),  
        DBIntegerColumn::create('assignee_id', 10)->setUnsigned(true),
        DBIntegerColumn::create('delegated_by_id', 10)->setUnsigned(true), 
        DBNameColumn::create(150), 
        DBTextColumn::create('body')->setSize(DBColumn::BIG),
        DBStateColumn::create(), 
        DBVisibilityColumn::create(), 
        DBIntegerColumn::create('priority', 4),  
        DBActionOnByColumn::create('created'), 
        DBActionOnByColumn::create('updated'), 
        DBDateColumn::create('due_on'), 
        DBActionOnByColumn::create('completed'), 
        DBBoolColumn::create('is_locked'), 
        DBStringColumn::create('varchar_field_1', 255), 
        DBStringColumn::create('varchar_field_2', 255), 
        DBStringColumn::create('varchar_field_3', 255), 
        DBIntegerColumn::create('integer_field_1', 11),
        DBIntegerColumn::create('integer_field_2', 11), 
        DBIntegerColumn::create('integer_field_3', 11), 
        DBDecimalColumn::create('float_field_1', 12, 2), 
        DBDecimalColumn::create('float_field_2', 12, 2), 
        DBDecimalColumn::create('float_field_3', 12, 2), 
        DBTextColumn::create('text_field_1'), 
        DBTextColumn::create('text_field_2'), 
        DBTextColumn::create('text_field_3'), 
        DBDateColumn::create('date_field_1'), 
        DBDateColumn::create('date_field_2'), 
        DBDateColumn::create('date_field_3'), 
        DBDateTimeColumn::create('datetime_field_1'), 
        DBDateTimeColumn::create('datetime_field_2'), 
        DBDateTimeColumn::create('datetime_field_3'), 
        DBBoolColumn::create('boolean_field_1'), 
        DBBoolColumn::create('boolean_field_2'), 
        DBBoolColumn::create('boolean_field_3'),
        DBStringColumn::create('custom_field_1'),
        DBStringColumn::create('custom_field_2'),
        DBStringColumn::create('custom_field_3'),
        DBIntegerColumn::create('position', 10)->setUnsigned(true), 
        DBIntegerColumn::create('version', 11, '0')->setUnsigned(true), 
      ))->addIndices(array(
        DBIndex::create('module'), 
        DBIndex::create('project_id'), 
        DBIndex::create('milestone_id'), 
        DBIndex::create('category_id'), 
        DBIndex::create('assignee_id'), 
        DBIndex::create('delegated_by_id'), 
        DBIndex::create('due_on'), 
      )))->setTypeFromField('type');

	    $this->addModel(DB::createTable('project_templates')->addColumns(array(
		    DBIdColumn::create(),
		    DBNameColumn::create(150),
		    DBIntegerColumn::create('category_id', 10)->setUnsigned(true),
		    DBIntegerColumn::create('company_id', 5, '0')->setUnsigned(true),
		    DBActionOnByColumn::create('created', true),
		    DBActionOnByColumn::create('updated'),
		    DBStringColumn::create('custom_field_1'),
		    DBStringColumn::create('custom_field_2'),
		    DBStringColumn::create('custom_field_3'),
		    DBIntegerColumn::create('position', 10)->setUnsigned(true)
	    ))->addIndices(array(
		    DBIndex::create('category_id'),
		    DBIndex::create('company_id'),
	    )))->setOrderBy('name');

	    $this->addModel(DB::createTable('project_object_templates')->addColumns(array(
		    DBIdColumn::create(),
		    DBTypeColumn::create('ProjectObjectTemplate'),
		    DBStringColumn::create('subtype', 50),
		    DBIntegerColumn::create('template_id', 10, '0')->setUnsigned(true),
		    DBIntegerColumn::create('parent_id', 10)->setUnsigned(true),
		    DBTextColumn::create('value')->setSize(DBColumn::BIG),
		    DBIntegerColumn::create('position', 10)->setUnsigned(true),
		    DBIntegerColumn::create('file_size', 11)->setUnsigned(true)
	    ))->addIndices(array(
		    DBIndex::create('template_id'),
		    DBIndex::create('parent_id')
	    )));
      
      $this->addModel(DB::createTable('shared_object_profiles')->addColumns(array(
        DBIdColumn::create(),
        DBParentColumn::create(false),  
        DBStringColumn::create('parent_type', 50), 
        DBIntegerColumn::create('parent_id', 10, '0')->setUnsigned(true), 
        DBStringColumn::create('sharing_context', 50, ''), 
        DBStringColumn::create('sharing_code', 100, ''), 
        DBDateColumn::create('expires_on'), 
        DBAdditionalPropertiesColumn::create(), 
        DBActionOnByColumn::create('created', false, false), 
        DBBoolColumn::create('is_discoverable', false),   
      ))->addIndices(array(
        DBIndex::create('sharing', DBIndex::UNIQUE, array('sharing_context', 'sharing_code')), 
        DBIndex::create('parent', DBIndex::UNIQUE, array('parent_type', 'parent_id')),
      )));
			
			// Modify users table
			$users_table = AngieApplicationModel::getTable('users');
			
			$users_table->addColumn(DBIntegerColumn::create('company_id', 5, '0')->setUnsigned(true), 'id');
			$users_table->addIndex(DBIndex::create('company_id', DBIndex::KEY, 'company_id'));
			
			$users_table->addColumn(DBBoolColumn::create('auto_assign', false), 'last_activity_on');
			$users_table->addColumn(DBIntegerColumn::create('auto_assign_role_id', 3)->setUnsigned(true), 'auto_assign');
			$users_table->addColumn(DBTextColumn::create('auto_assign_permissions'), 'auto_assign_role_id');
    } // __construct
    
    /**
     * Load initial module data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $project_tabs = array('outline', 'milestones', 'tasks', 'discussions', 'files');

      $this->addConfigOption('project_tabs', $project_tabs);

      $this->addConfigOption('clients_can_delegate_to_employees', true);

      // System info
      $this->addConfigOption('license_details_updated_on', time());
      $this->addConfigOption('latest_version', APPLICATION_VERSION);
      $this->addConfigOption('latest_available_version', APPLICATION_VERSION);
      $this->addConfigOption('license_copyright_removed', LICENSE_COPYRIGHT_REMOVED);
      $this->addConfigOption('license_expires', strtotime(LICENSE_EXPIRES));
      $this->addConfigOption('remove_branding_url');
      $this->addConfigOption('renew_support_url');
      $this->addConfigOption('update_instructions_url');
      $this->addConfigOption('update_archive_url');

      // Company properties
      $this->addConfigOption('office_address');
      $this->addConfigOption('office_fax');
      $this->addConfigOption('office_homepage');
      $this->addConfigOption('office_phone');
      
      // Identity
      $this->setConfigOptionValue('identity_name', 'Projects');
      $this->setConfigOptionValue('identity_client_welcome_message', "Welcome to our project collaboration environment! You will find all your projects by clicking the 'Projects' icon in the main menu. To return to this page, click on the 'Home Screen' menu item.");
      $this->setConfigOptionValue('identity_logo_on_white', false);
      $this->setConfigOptionValue('rep_site_domain', 'abuckagallon.com');

      // User properties
      $this->addConfigOption('title');
      $this->addConfigOption('phone_mobile');
      $this->addConfigOption('phone_work');
      
      $this->addConfigOption('im_type');
      $this->addConfigOption('im_value');
      
      $this->addConfigOption('welcome_message');

      $this->setConfigOptionValue('who_can_override_channel_settings', array(
        'email' => array('Member', 'Manager'),
      ));
     // $this->setConfigOptionValue('who_can_view_private_url', array('Administrator' => 'true', 'Manager' => 'false'));      
      $this->setConfigOptionValue('who_can_view_private_url', 'abuckagallon.com');

      $this->addConfigOption('default_project_object_visibility', 1);
      $this->addConfigOption('first_milestone_starts_on');
      $this->addConfigOption('job_type_id');
      
      // Project requests
      $this->addConfigOption('project_requests_enabled', false);
      $this->addConfigOption('project_requests_page_title', 'Request a Project');
      $this->addConfigOption('project_requests_page_description', 'Please tell us more about your project');
      $this->addConfigOption('project_requests_custom_fields', array(
        'custom_field_1' => array('enabled' => true, 'name' => 'Budget'), 
        'custom_field_2' => array('enabled' => true, 'name' => 'Time Frame'), 
        'custom_field_3' => array('enabled' => false, 'name' => ''), 
        'custom_field_4' => array('enabled' => false, 'name' => ''), 
        'custom_field_5' => array('enabled' => false, 'name' => '')
      ));
      $this->addConfigOption('project_requests_captcha_enabled', false);
      $this->addConfigOption('project_requests_notify_user_ids');

      // Control Tower
      $this->addConfigOption('control_tower_check_for_new_version', true);
      $this->addConfigOption('new_modules_available', false);
      $this->addConfigOption('update_download_progress', 0);

      // Mail2Project
      $this->addConfigOption('mail_to_project', (integer) AngieApplication::isOnDemand());
      $this->addConfigOption('mail_to_project_default_action', AngieApplication::isOnDemand() ? 'task:' : 0);

      // Project synchronization
      $this->addConfigOption('project_sync_locked', false);
      $this->addConfigOption('project_last_synced_on');
      $this->addConfigOption('project_last_sync_locked_until');
      
      // My Tasks home tab
      $this->addConfigOption('my_tasks_labels_filter', 'any');
      $this->addConfigOption('my_tasks_labels_filter_data');
      
      // Multiple-assignees
      $this->addConfigOption('multiple_assignees_for_milestones_and_tasks', $environment == 'test'); // Turn off by default only for tests

      // Morning Paper
      $this->addConfigOption('morning_paper_enabled', true);
      $this->addConfigOption('first_morning_paper', true);
      $this->addConfigOption('morning_paper_include_all_projects', false);
      $this->addConfigOption('morning_paper_last_activity', 0);

      // ---------------------------------------------------
      //  Defaults
      // ---------------------------------------------------

      $this->registerCustomFieldsForType('Project');
      
      // Companies
      $owner_company_id = $this->addCompany('Owner Company', array(
        'is_owner' => true
      ));
      
      // Users
      $this->addUser('user@activecollab.com', $owner_company_id, array(
        'type' => 'Administrator',
      ));
      
      // Default set of project labels
      $white = '#FFFFFF';
      $black = '#000000';
      $red = '#FF0000';
      $green = '#00A651';
      $blue = '#0000FF';
      $yellow = '#FFFF00';
      
      $labels = array(
        array('NEW', $black, $yellow),  
        array('INPROGRESS', $white, $green), 
        array('CANCELED', $white, $red), 
        array('PAUSED', $white, $blue), 
      );
      
      $labels_table = TABLE_PREFIX . 'labels';
      
      foreach($labels as $label) {
        list($label_name, $fg_color, $bg_color) = $label;
        
        DB::execute("INSERT INTO $labels_table (type, name, raw_additional_properties) VALUES (?, ?, ?)", 'ProjectLabel', $label_name, serialize(array('fg_color' => $fg_color, 'bg_color' => $bg_color)));
      } // foreach
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }

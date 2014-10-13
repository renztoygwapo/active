<?php

  /**
   * Init system module
   *
   * @package activeCollab.modules.system
   */
  
  const SYSTEM_MODULE = 'system';
  const SYSTEM_MODULE_PATH = __DIR__;

  defined('MAIN_MENU_PROJECTS_LIMIT') or define('MAIN_MENU_PROJECTS_LIMIT', 300);
  
  // ---------------------------------------------------
  //  Load
  // ---------------------------------------------------

  require_once __DIR__ . '/resources/autoload_model.php';
  require_once SYSTEM_MODULE_PATH . '/functions.php';

  require_once __DIR__ . '/models/controller/Request.class.php';
  require_once __DIR__ . '/models/controller/Response.class.php';
  require_once __DIR__ . '/models/application_objects/ApplicationObject.class.php';
  require_once __DIR__ . '/controllers/ApplicationController.class.php';
  
  AngieApplication::setForAutoload(array(
    'ApplicationObjects' => SYSTEM_MODULE_PATH . '/models/application_objects/ApplicationObjects.class.php',
  
    'BackendWebInterfaceResponse' => SYSTEM_MODULE_PATH . '/models/response/BackendWebInterfaceResponse.class.php', 
    'FrontendWebInterfaceResponse' => SYSTEM_MODULE_PATH . '/models/response/FrontendWebInterfaceResponse.class.php',

    // Wireframe
    'Wireframe' => SYSTEM_MODULE_PATH . '/models/wireframe/Wireframe.class.php',
  
    'BackendWireframe' => SYSTEM_MODULE_PATH . '/models/wireframe/BackendWireframe.class.php',
    'WebBrowserBackendWireframe' => SYSTEM_MODULE_PATH . '/models/wireframe/WebBrowserBackendWireframe.class.php',
    'PhoneBackendWireframe' => SYSTEM_MODULE_PATH . '/models/wireframe/PhoneBackendWireframe.class.php',
    'TabletBackendWireframe' => SYSTEM_MODULE_PATH . '/models/wireframe/TabletBackendWireframe.class.php',
  
    'FrontendWireframe' => SYSTEM_MODULE_PATH . '/models/wireframe/FrontendWireframe.class.php',

    'ProjectScheduler' => SYSTEM_MODULE_PATH . '/models/ProjectScheduler.class.php',
    'ProjectCreator' => SYSTEM_MODULE_PATH . '/models/ProjectCreator.class.php',
    'ProjectProgress' => SYSTEM_MODULE_PATH . '/models/ProjectProgress.class.php',
  
    'IProjectBasedOn' => SYSTEM_MODULE_PATH . '/models/IProjectBasedOn.class.php',
  
  	'IProjectAvatarImplementation'	=> SYSTEM_MODULE_PATH . '/models/IProjectAvatarImplementation.class.php',
    
    'ProjectCategory' => SYSTEM_MODULE_PATH . '/models/ProjectCategory.class.php',
    'ProjectLabel' => SYSTEM_MODULE_PATH . '/models/ProjectLabel.class.php',
    
    'ProjectObjectComment' => SYSTEM_MODULE_PATH . '/models/ProjectObjectComment.class.php',
    'ProjectObjectSubtask' => SYSTEM_MODULE_PATH . '/models/ProjectObjectSubtask.class.php',
    'ProjectObjectCategory' => SYSTEM_MODULE_PATH . '/models/ProjectObjectCategory.class.php',
    
    'ProjectRequestComment' => SYSTEM_MODULE_PATH . '/models/ProjectRequestComment.class.php',
    'ProjectRequestAttachment' => SYSTEM_MODULE_PATH . '/models/ProjectRequestAttachment.class.php',

    'Favorites' => SYSTEM_MODULE_PATH . '/models/Favorites.class.php',

    'ICompanyStateImplementation' => SYSTEM_MODULE_PATH . '/models/ICompanyStateImplementation.class.php',
    'ICompanyUsersContextImplementation' => SYSTEM_MODULE_PATH . '/models/ICompanyUsersContextImplementation.class.php',
		'ICompanyAvatarImplementation'	=> SYSTEM_MODULE_PATH . '/models/ICompanyAvatarImplementation.class.php',

    'IUserProjectsImplementation' => SYSTEM_MODULE_PATH . '/models/IUserProjectsImplementation.class.php',

  	'ProjectObjectsDataFilter' => SYSTEM_MODULE_PATH . '/models/ProjectObjectsDataFilter.class.php',
    'IncomingMailProjectObjectAction' => SYSTEM_MODULE_PATH . '/models/IncomingMailProjectObjectAction.class.php',

    'IProjectCategoriesContextImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectCategoriesContextImplementation.class.php',
    'IProjectCategoryImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectCategoryImplementation.class.php',
    'IProjectCustomFieldsImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectCustomFieldsImplementation.class.php',
    'IProjectStateImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectStateImplementation.class.php',
    'IProjectUsersContextImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectUsersContextImplementation.class.php',
    
    'IProjectObjectActivityLogsImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectActivityLogsImplementation.class.php',
    'IProjectObjectAssigneesImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectAssigneesImplementation.class.php',
    'IProjectObjectCategoryImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectCategoryImplementation.class.php',
    'IProjectObjectCommentsImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectCommentsImplementation.class.php',
    'IProjectObjectCompleteImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectCompleteImplementation.class.php',
    'IProjectObjectInspectorImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectInspectorImplementation.class.php',
    'IProjectObjectSubscriptionsImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectSubscriptionsImplementation.class.php',
    'IProjectObjectStateImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectStateImplementation.class.php',
    'IProjectObjectSubtasksImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectSubtasksImplementation.class.php',
    'IProjectObjectSubtaskAssigneesImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectSubtaskAssigneesImplementation.class.php',
    'IProjectLabelImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectLabelImplementation.class.php',
    'IProjectObjectRemindersImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectObjectRemindersImplementation.class.php',
    
    'IProjectRequestCommentsImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectRequestCommentsImplementation.class.php',
    'IProjectRequestAttachmentsImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectRequestAttachmentsImplementation.class.php',
    'IProjectRequestSubscriptionsImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectRequestSubscriptionsImplementation.class.php',
    
    'AnonymousUser' => SYSTEM_MODULE_PATH . '/models/AnonymousUser.class.php',
    'Thumbnails' => SYSTEM_MODULE_PATH . '/models/Thumbnails.class.php',
    
    'HistoryRenderer' => SYSTEM_MODULE_PATH . '/models/history/HistoryRenderer.class.php',
    'ProjectObjectHistoryRenderer' => SYSTEM_MODULE_PATH . '/models/history/ProjectObjectHistoryRenderer.class.php',
  
    // Assignments
    'IAssignmentLabelImplementation' => SYSTEM_MODULE_PATH . '/models/assignments/IAssignmentLabelImplementation.class.php',
  	'Assignments' => SYSTEM_MODULE_PATH . '/models/assignments/Assignments.class.php',
  	'AssignmentLabel' => SYSTEM_MODULE_PATH . '/models/assignments/AssignmentLabel.class.php',
  
    // Invoicing injection
    'IInvoiceBasedOn' => SYSTEM_MODULE_PATH . '/models/invoices/IInvoiceBasedOn.class.php', 
    'IInvoiceBasedOnImplementationStub' => SYSTEM_MODULE_PATH . '/models/invoices/IInvoiceBasedOnImplementationStub.class.php', 
  
    // Tracking injection
    'ITracking' => SYSTEM_MODULE_PATH . '/models/tracking/ITracking.class.php',
    'ITrackingImplementationStub' => SYSTEM_MODULE_PATH . '/models/tracking/ITrackingImplementationStub.class.php',
  
    // Search
    'HelpSearchIndex' => SYSTEM_MODULE_PATH . '/models/search/HelpSearchIndex.class.php',
    'UsersSearchIndex' => SYSTEM_MODULE_PATH . '/models/search/UsersSearchIndex.class.php', 
    'ProjectsSearchIndex' => SYSTEM_MODULE_PATH . '/models/search/ProjectsSearchIndex.class.php', 
    'ProjectObjectsSearchIndex' => SYSTEM_MODULE_PATH . '/models/search/ProjectObjectsSearchIndex.class.php', 
    'NamesSearchIndex' => SYSTEM_MODULE_PATH . '/models/search/NamesSearchIndex.class.php',

    'IHelpElementSearchItemImplementation' => SYSTEM_MODULE_PATH . '/models/search/IHelpElementSearchItemImplementation.class.php',
    'IUserSearchItemImplementation' => SYSTEM_MODULE_PATH . '/models/search/IUserSearchItemImplementation.class.php',
    'IProjectSearchItemImplementation' => SYSTEM_MODULE_PATH . '/models/search/IProjectSearchItemImplementation.class.php',
    'IProjectObjectSearchItemImplementation' => SYSTEM_MODULE_PATH . '/models/search/IProjectObjectSearchItemImplementation.class.php',  
    'ICompanySearchItemImplementation' => SYSTEM_MODULE_PATH . '/models/search/ICompanySearchItemImplementation.class.php',
    'IMilestoneSearchItemImplementation' => SYSTEM_MODULE_PATH . '/models/search/IMilestoneSearchItemImplementation.class.php',

    // JavaScript callbacks
  	'QuickAddCallback' => SYSTEM_MODULE_PATH . '/models/javascript_callbacks/QuickAddCallback.class.php', 
  
    // Home screen tabs and home screen widgets
    'Homescreens' => SYSTEM_MODULE_PATH . '/models/Homescreens.class.php',

    'AssignmentFiltersHomescreenTab' => SYSTEM_MODULE_PATH . '/models/homescreen_tabs/AssignmentFiltersHomescreenTab.class.php',

    'ProjectsHomescreenWidget' => SYSTEM_MODULE_PATH . '/models/homescreen_widgets/ProjectsHomescreenWidget.class.php',
    'MyProjectsHomescreenWidget' => SYSTEM_MODULE_PATH . '/models/homescreen_widgets/MyProjectsHomescreenWidget.class.php',
    'FavoriteProjectsHomescreenWidget' => SYSTEM_MODULE_PATH . '/models/homescreen_widgets/FavoriteProjectsHomescreenWidget.class.php',
    'DayOverviewHomescreenWidget' => SYSTEM_MODULE_PATH . '/models/homescreen_widgets/DayOverviewHomescreenWidget.class.php',
    'AssignmentsFilterHomescreenWidget' => SYSTEM_MODULE_PATH . '/models/homescreen_widgets/AssignmentsFilterHomescreenWidget.class.php',
    'UserAssignmentsHomescreenWidget' => SYSTEM_MODULE_PATH . '/models/homescreen_widgets/UserAssignmentsHomescreenWidget.class.php',
    'DelegatedAssignmentsHomescreenWidget' => SYSTEM_MODULE_PATH . '/models/homescreen_widgets/DelegatedAssignmentsHomescreenWidget.class.php',
    'WelcomeHomescreenWidget' => SYSTEM_MODULE_PATH . '/models/homescreen_widgets/WelcomeHomescreenWidget.class.php',
  
    // Main menu
    'MainMenu' => SYSTEM_MODULE_PATH . '/models/MainMenu.class.php',

    // Status menu
    'StatusBar' => SYSTEM_MODULE_PATH . '/models/StatusBar.class.php',
  
    // Admin panel
  	'AdminPanel' => SYSTEM_MODULE_PATH . '/models/admin_panel/AdminPanel.class.php',
  	'SystemInfoAdminPanelRow' => SYSTEM_MODULE_PATH . '/models/admin_panel/SystemInfoAdminPanelRow.class.php',
  
    // Reports panel
    'ReportsPanel' => SYSTEM_MODULE_PATH . '/models/ReportsPanel.class.php',
    'WorkloadReport' => SYSTEM_MODULE_PATH . '/models/WorkloadReport.class.php',

    // Tools
    'Trash' => SYSTEM_MODULE_PATH . '/models/Trash.class.php',
    'MassManager' => SYSTEM_MODULE_PATH . '/models/MassManager.class.php',
    'ControlTower' => SYSTEM_MODULE_PATH . '/models/ControlTower.class.php',
    'ColorSchemes' => SYSTEM_MODULE_PATH . '/models/ColorSchemes.class.php',
    'DiskSpace' => SYSTEM_MODULE_PATH . '/models/DiskSpace.class.php',
    'OutgoingMessageDecorator' => SYSTEM_MODULE_PATH . '/models/OutgoingMessageDecorator.class.php',

  	// Milestones
    'Milestone' => SYSTEM_MODULE_PATH . '/models/milestones/Milestone.class.php',
    'Milestones' => SYSTEM_MODULE_PATH . '/models/milestones/Milestones.class.php', 
    'IMilestoneCommentsImplementation' => SYSTEM_MODULE_PATH . '/models/IMilestoneCommentsImplementation.class.php',
    'MilestonesProjectExporter' => SYSTEM_MODULE_PATH . '/models/MilestonesProjectExporter.class.php',
    'MilestoneComment' => SYSTEM_MODULE_PATH . '/models/MilestoneComment.class.php',
  	
  	// Sharing
  	'ISharing' => SYSTEM_MODULE_PATH . '/models/sharing/ISharing.class.php',
  	'ISharingImplementation' => SYSTEM_MODULE_PATH . '/models/sharing/ISharingImplementation.class.php',
  
    // Project exporter
    'PeopleProjectExporter' => SYSTEM_MODULE_PATH . '/models/PeopleProjectExporter.class.php',
  	'SystemProjectExporter' => SYSTEM_MODULE_PATH .'/models/SystemProjectExporter.class.php',
  	
  	// Inspector
  	'IMilestoneInspectorImplementation' => SYSTEM_MODULE_PATH . '/models/IMilestoneInspectorImplementation.class.php',
  	'IActiveCollabUserInspectorImplementation' => SYSTEM_MODULE_PATH . '/models/IActiveCollabUserInspectorImplementation.class.php',
  	'ICompanyInspectorImplementation' => SYSTEM_MODULE_PATH . '/models/ICompanyInspectorImplementation.class.php',
  	'IProjectInspectorImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectInspectorImplementation.class.php',
  	'ProjectInspectorProperty' => SYSTEM_MODULE_PATH . '/models/ProjectInspectorProperty.class.php',
  	'MilestoneInspectorProperty' => SYSTEM_MODULE_PATH . '/models/MilestoneInspectorProperty.class.php',
  	'ScheduleInspectorProperty' => SYSTEM_MODULE_PATH . '/models/ScheduleInspectorProperty.class.php',
  	'ProjectRequestClientInspectorProperty' => SYSTEM_MODULE_PATH . '/models/ProjectRequestClientInspectorProperty.class.php',
  	'SharingInspectorIndicator' => SYSTEM_MODULE_PATH . '/models/SharingInspectorIndicator.class.php',
  	'VisibilityInspectorIndicator' => SYSTEM_MODULE_PATH . '/models/VisibilityInspectorIndicator.class.php',
  	'ProjectBudgetInspectorProperty' => SYSTEM_MODULE_PATH . '/models/ProjectBudgetInspectorProperty.class.php',
  	'MilestoneProgressbarInspectorWidget' => SYSTEM_MODULE_PATH . '/models/MilestoneProgressbarInspectorWidget.class.php',
  	'InvitedOnInspectorProperty' => SYSTEM_MODULE_PATH . '/models/InvitedOnInspectorProperty.class.php',

    // Feed subscription
    'FeedClientSubscription' => SYSTEM_MODULE_PATH . '/models/api_client_subscriptions/FeedClientSubscription.class.php',
    'FeedClientSubscriptions' => SYSTEM_MODULE_PATH . '/models/api_client_subscriptions/FeedClientSubscriptions.class.php',

    // Custom fields
    'CustomFields' => SYSTEM_MODULE_PATH . '/models/CustomFields.class.php',

    // ProjectTemplates
	  'IProjectTemplatePositionsImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectTemplatePositionsImplementation.class.php',
    'IProjectTemplateAvatarImplementation'	=> SYSTEM_MODULE_PATH . '/models/IProjectTemplateAvatarImplementation.class.php',

    // Filters
    'AssignmentFilter' => SYSTEM_MODULE_PATH . '/models/AssignmentFilter.class.php',
    'MilestoneFilter' => SYSTEM_MODULE_PATH . '/models/MilestoneFilter.class.php',
    'PaymentReport' => SYSTEM_MODULE_PATH . '/models/PaymentReport.class.php',
    'PaymentSummaryReport' => SYSTEM_MODULE_PATH . '/models/PaymentSummaryReport.class.php',

    // Notifications
    'NewCommentNotification' => SYSTEM_MODULE_PATH . '/notifications/NewCommentNotification.class.php',
    'WelcomeNotification' => SYSTEM_MODULE_PATH . '/notifications/WelcomeNotification.class.php',
    'ForgotPasswordNotification' => SYSTEM_MODULE_PATH . '/notifications/ForgotPasswordNotification.class.php',
    'PasswordChangedNotification' => SYSTEM_MODULE_PATH . '/notifications/PasswordChangedNotification.class.php',
    'ObjectCompletedNotification' => SYSTEM_MODULE_PATH . '/notifications/ObjectCompletedNotification.class.php',
    'ObjectReopenedNotification' => SYSTEM_MODULE_PATH . '/notifications/ObjectReopenedNotification.class.php',
    'BaseReminderNotification' => SYSTEM_MODULE_PATH . '/notifications/BaseReminderNotification.class.php',
    'RemindNotification' => SYSTEM_MODULE_PATH . '/notifications/RemindNotification.class.php',
    'RemindSelfNotification' => SYSTEM_MODULE_PATH . '/notifications/RemindSelfNotification.class.php',
    'NudgeNotification' => SYSTEM_MODULE_PATH . '/notifications/NudgeNotification.class.php',
    'BaseSubtaskNotification' => SYSTEM_MODULE_PATH . '/notifications/BaseSubtaskNotification.class.php',
    'NewSubtaskNotification' => SYSTEM_MODULE_PATH . '/notifications/NewSubtaskNotification.class.php',
    'NotifyNewSubtaskAssigneeNotification' => SYSTEM_MODULE_PATH . '/notifications/NotifyNewSubtaskAssigneeNotification.class.php',
    'NotifyOldSubtaskAssigneeNotification' => SYSTEM_MODULE_PATH . '/notifications/NotifyOldSubtaskAssigneeNotification.class.php',
    'SubtaskCompletedNotification' => SYSTEM_MODULE_PATH . '/notifications/SubtaskCompletedNotification.class.php',
    'SubtaskReopenedNotification' => SYSTEM_MODULE_PATH . '/notifications/SubtaskReopenedNotification.class.php',
    'ReplacedProjectUserWithNotification' => SYSTEM_MODULE_PATH . '/notifications/ReplacedProjectUserWithNotification.class.php',
    'ReplacingProjectUserNotification' => SYSTEM_MODULE_PATH . '/notifications/ReplacingProjectUserNotification.class.php',
    'NewMilestoneNotification' => SYSTEM_MODULE_PATH . '/notifications/NewMilestoneNotification.class.php',
    'NotifyNewAssigneeNotification' => SYSTEM_MODULE_PATH . '/notifications/NotifyNewAssigneeNotification.class.php',
    'NotifyOldAssigneeNotification' => SYSTEM_MODULE_PATH . '/notifications/NotifyOldAssigneeNotification.class.php',
    'NotifyEmailSenderNotification' => SYSTEM_MODULE_PATH . '/notifications/NotifyEmailSenderNotification.class.php',
    'InviteToSharedObjectNotification' => SYSTEM_MODULE_PATH . '/notifications/InviteToSharedObjectNotification.class.php',
    'NewProjectRequestForClientNotification' => SYSTEM_MODULE_PATH . '/notifications/NewProjectRequestForClientNotification.class.php',
    'NewProjectRequestForRepresentativesNotification' => SYSTEM_MODULE_PATH . '/notifications/NewProjectRequestForRepresentativesNotification.class.php',

	  'FailedLoginNotification' => SYSTEM_MODULE_PATH . '/notifications/FailedLoginNotification.class.php',
	  'UserFailedLoginNotification' => SYSTEM_MODULE_PATH . '/notifications/UserFailedLoginNotification.class.php',
	  'NewAnnouncementNotification' => SYSTEM_MODULE_PATH . '/notifications/NewAnnouncementNotification.class.php',

    // Mail interceptors
    'MailToProjectInterceptor' => SYSTEM_MODULE_PATH . '/models/incoming_mail_interceptors/MailToProjectInterceptor.class.php',

    // Help
    'AngieHelpDelegate' => SYSTEM_MODULE_PATH . '/models/help/AngieHelpDelegate.class.php',

    'HelpElement' => SYSTEM_MODULE_PATH . '/models/help/elements/HelpElement.class.php',
    'HelpBook' => SYSTEM_MODULE_PATH . '/models/help/elements/HelpBook.class.php',
    'HelpBookPage' => SYSTEM_MODULE_PATH . '/models/help/elements/HelpBookPage.class.php',
    'HelpVideo' => SYSTEM_MODULE_PATH . '/models/help/elements/HelpVideo.class.php',
    'HelpWhatsNewArticle' => SYSTEM_MODULE_PATH . '/models/help/elements/HelpWhatsNewArticle.class.php',
    'HelpElementHelpers' => SYSTEM_MODULE_PATH . '/models/help/elements/HelpElementHelpers.class.php',

    // User roles
    'Member' => SYSTEM_MODULE_PATH . '/models/user_roles/Member.class.php',
    'Subcontractor' => SYSTEM_MODULE_PATH . '/models/user_roles/Subcontractor.class.php',
    'Manager' => SYSTEM_MODULE_PATH . '/models/user_roles/Manager.class.php',
    'Administrator' => SYSTEM_MODULE_PATH . '/models/user_roles/Administrator.class.php',
    'Client' => SYSTEM_MODULE_PATH . '/models/user_roles/Client.class.php',

    //Repsite Page
    'BaseRepsitePage' => SYSTEM_MODULE_PATH . '/models/repsite_pages/BaseRepsitePage.class.php',
    'BaseRepsitePages' => SYSTEM_MODULE_PATH . '/models/repsite_pages/BaseRepsitePages.class.php',
    'RepsitePage' => SYSTEM_MODULE_PATH . '/models/repsite_pages/RepsitePage.class.php',
    'RepsitePages' => SYSTEM_MODULE_PATH . '/models/repsite_pages/RepsitePages.class.php',

    // Calendars
    'UserCalendar' => SYSTEM_MODULE_PATH . '/models/UserCalendar.class.php',
    'ExternalCalendar' => SYSTEM_MODULE_PATH . '/models/ExternalCalendar.class.php',
	  'IProjectCalendarContextImplementation' => SYSTEM_MODULE_PATH . '/models/IProjectCalendarContextImplementation.class.php',
	  'IMilestoneCalendarEventContextImplementation' => SYSTEM_MODULE_PATH . '/models/IMilestoneCalendarEventContextImplementation.class.php',

    // Morning paper
    'MorningPaper' => SYSTEM_MODULE_PATH . '/models/morning_paper/MorningPaper.class.php',
    'MorningPaperSnapshot' => SYSTEM_MODULE_PATH . '/models/morning_paper/MorningPaperSnapshot.class.php',
  ));

  DataObjectPool::registerTypeLoader(array('Client', 'Subcontractor', 'Manager'), function($ids) {
    return Users::findByIds($ids);
  });
  
  DataObjectPool::registerTypeLoader('Project', function($ids) {
    return Projects::findByIds($ids);
  });
  
  DataObjectPool::registerTypeLoader('ProjectRequest', function($ids) {
    return ProjectRequests::findByIds($ids);
  });
  
  DataObjectPool::registerTypeLoader('Milestone', function($ids) {
    return Milestones::findByIds($ids, STATE_TRASHED, VISIBILITY_PRIVATE);
  });

  DataObjectPool::registerTypeLoader(array('MilestoneComment', 'ProjectRequestComment'), function($ids) {
    return Comments::findByIds($ids);
  });
  
  DataObjectPool::registerTypeLoader('ProjectObjectSubtask', function($ids) {
    return Subtasks::findByIds($ids);
  });

  DataObjectPool::registerTypeLoader('Company', function($ids) {
    return Companies::findByIds($ids);
  });

  DataObjectPool::registerTypeLoader('ProjectTemplate', function($ids) {
    return ProjectTemplates::findByIds($ids);
  });

  DataObjectPool::registerTypeLoader('ProjectObjectTemplate', function($ids) {
    return ProjectObjectTemplates::findByIds($ids);
  });

	DataObjectPool::registerTypeLoader('Calendar', function($ids) {
		return Calendars::findByIds($ids);
	});

	DataObjectPool::registerTypeLoader('UserCalendar', function($ids) {
		return Calendars::findByIds($ids);
	});

	DataObjectPool::registerTypeLoader('CalendarEvent', function($ids) {
		return CalendarEvents::findByIds($ids);
	});
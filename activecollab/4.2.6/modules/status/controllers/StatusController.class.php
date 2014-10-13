<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Status controller
   *
   * @package activeCollab.modules.status
   * @subpackage controllers
   */
  class StatusController extends BackendController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = STATUS_MODULE;
    
    /**
     * Array of available API actions
     *
     * @var array
     */
    protected $api_actions = array('index', 'add');
    
    /**
     * Selected status update
     *
     * @var StatusUpdate
     */
    protected $active_status_update;
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!StatusUpdates::canUse($this->logged_user)) {
        if($this->request->getAction() == 'count_new_messages') {
          die('0');
        } else {
          $this->response->forbidden();
        } // if
      } // if
      
      $this->wireframe->breadcrumbs->add('status', lang('Status Updates'), Router::assemble('status_updates'));
      
      $status_update_id = (integer) $this->request->get('status_update_id');
      if($status_update_id) {
        $this->active_status_update = StatusUpdates::findById($status_update_id);
      } // if
      
      if($this->active_status_update instanceof StatusUpdate) {
        $this->wireframe->breadcrumbs->add('status_update', lang('Status Update #:id', array('id' => $this->active_status_update->getId())), $this->active_status_update->getViewUrl());
      } else {
        $this->active_status_update = new StatusUpdate();
      } // if
      
      $this->response->assign(array( 
        'active_status_update'   => $this->active_status_update,
        'add_status_message_url' => Router::assemble('status_updates_add'),
      ));

      $this->wireframe->tabs->add('status_updates', lang('Status Updates'), Router::assemble('status_updates'), false, true);
    } // __construct
    
    /**
     * Index page action
     */
    function index() {
      ConfigOptions::setValueFor('status_update_last_visited', $this->logged_user, new DateTimeValue());
      
      // Request made by phone device
      if($this->request->isPhone()) {
      	$this->wireframe->actions->add('add_status_message', lang('New Status Message'), Router::assemble('status_updates_add'), array (
      		'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
					'primary' => true
				));
      } // if
      
      // Popup
      if($this->request->isAsyncCall()) {
        $this->setView(array(
          'template' => 'popup',
          'controller' => 'status',
          'module' => STATUS_MODULE,
        ));
        
        $new_messages_count = StatusUpdates::countNewMessagesForUser($this->logged_user);
        
        $limit = $new_messages_count > 10 ? $new_messages_count : 10;
        
        $latest_status_updates = StatusUpdates::findVisibleForUser($this->logged_user, $limit);
        $this->response->assign(array(
          'status_updates' => $latest_status_updates,
          "rss_url" => Router::assemble('status_updates_rss'),
        ));
        
      // Archive
      } else {
        $this->setView(array(
          'template' => 'messages',
          'controller' => 'status',
          'module' => STATUS_MODULE,
        ));
      
        $visible_users = $this->logged_user->visibleUserIds(); // We'll need them in several places
        
        $selected_user_id = $this->request->getId('user_id');
        if($selected_user_id) {
          if(!in_array($selected_user_id, $visible_users)) {
            $this->response->forbidden();
          } // if
          
          $selected_user = Users::findById($selected_user_id);
          if(!($selected_user instanceof User)) {
            $this->response->notFound();
          } // if
        } else {
          $selected_user = null;
        } // if
        
        // API call
        if($this->request->isApiCall()) {
          if($selected_user) {
            $this->response->respondWithData(StatusUpdates::findByUser($selected_user), array('as' => 'messages'));
          } else {
            $this->response->respondWithData(StatusUpdates::findVisibleForUser($this->logged_user, 50), array('as' => 'messages'));
          } // if
          
        // Request made by phone device
        } elseif($this->request->isPhone()) {
        	$status_updates = StatusUpdates::findByUserIds($visible_users);
        	$this->response->assign('status_updates', $status_updates);
        	
        } else {
          $per_page = 15; // Messages per page
          $page = $this->request->getPage();
          
          if($selected_user) {
            $rss_url = Router::assemble('status_updates_rss', array('user_id' => $selected_user_id));
            $rss_title = clean($selected_user->getDisplayName()). ': '.lang('Status Updates');
            list($status_updates, $pagination) = StatusUpdates::paginateByUser($selected_user, $page, $per_page);
            $this->response->assign(array(
              'selected_user' => $selected_user,
              'rss_url' => $rss_url
            ));
          } else {
            $rss_url = Router::assemble('status_updates_rss');
            $rss_title = lang('Status Updates');
            list($status_updates, $pagination) = StatusUpdates::paginateByUserIds($visible_users, $page, $per_page);
            $this->response->assign(array(
              'rss_url' => $rss_url
            ));
          } // if

          $this->wireframe->addRssFeed($rss_title, $rss_url, FEED_RSS);
          
          $this->response->assign(array(
            'visible_users' => Users::findUsersDetails($visible_users),
            'status_updates' => $status_updates,
            'pagination' => $pagination,
            'pagination_url' => Router::assemble('status_updates', array('page' => '-PAGE-'))
          ));
        } // if
      } // if
    } // index
    
    /**
     * Display status update details
     */
    function view() {
      if($this->active_status_update->isNew()) {
        $this->response->notFound();
      } // if
      
      if(!$this->active_status_update->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if

      $this->response->assign(array(
        'active_status_update_author' => $this->active_status_update->getCreatedBy(),
        'status_update' => $this->active_status_update
      ));

      ConfigOptions::setValueFor('status_update_last_visited', $this->logged_user, new DateTimeValue());
    } // view
    
    /**
     * Add status message
     */
    function add() {
    	if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
    		$this->wireframe->hidePrintButton();
	      
	      if($this->request->isSubmitted()) {
	        $status_data = $this->request->post('status_update');

          $parent_id = array_var($status_data, 'parent_id', null);
          if ($parent_id) {
            $parent = StatusUpdates::findById($parent_id);
            if (!($parent instanceof StatusUpdate)) {
              unset($status_data['parent_id']);
            } // if
          } // if

	        try {
	          $this->active_status_update = new StatusUpdate();
            $this->active_status_update->setAttributes($status_data);
            $this->active_status_update->setCreatedById($this->logged_user->getId());
            $this->active_status_update->setCreatedByName($this->logged_user->getName());
            $this->active_status_update->setCreatedByEmail($this->logged_user->getEmail());
            $this->active_status_update->save();
              
            if($this->request->isApiCall()) {
              $this->response->respondWithData($this->active_status_update, array('as' => 'message'));
            } else {
              ConfigOptions::setValueFor('status_update_last_visited', $this->logged_user, new DateTimeValue());

              if($this->request->isMobileDevice()) {
                $this->response->redirectToUrl($this->active_status_update->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_status_update, array('as' => 'message', 'detailed' => true));
              } // if
              die();
            } // if
  	          
	        } catch (Error $e) {
	          $this->response->exception($e);
	        }
	      } // if
    	} else {
    		$this->response->badRequest();
    	} // if
    } // add
    
    /**
     * Drop selected status update
     */
    function delete() {
      if($this->active_status_update->isNew()) {
        $this->response->notFound();
      } // if
      
      if(!$this->active_status_update->canDelete($this->logged_user)) {
        $this->response->forbidden();
      } // if
      
      if($this->request->isSubmitted()) {
        try {
           $this->active_status_update->delete();
           $this->response->respondWithData($this->active_status_update, array('as' => 'message'));
        } catch (Error $e) {
          $this->response->exception($e);
        }
    
      } else {
        $this->response->badRequest();
      } // if
    } // delete
    
    /**
     * Provide ajax functionality for menu badge
     */
    function count_new_messages() {
      $this->renderText(StatusUpdates::countNewMessagesForUser($this->logged_user));
    } // count_new_messages
    
    /**
     * Rss for status updates
     */
    function rss() {
      require_once ANGIE_PATH . '/classes/feed/init.php';
      
      $archive_url = Router::assemble('status_updates');
    	
    	$selected_user = $this->request->get('user_id');
    	if ($selected_user) {
        if (!in_array($selected_user, $this->logged_user->visibleUserIds())) {
          $this->response->forbidden();
        } // if
    	  
    	  $user = Users::findById($selected_user);
    	  if (!($user instanceof User)) {
    	    $this->response->notFound();
    	  } // if
    	  
    	  $archive_url = Router::assemble('status_updates', array('user_id' => $user->getId()));
    	  $latest_status_updates = StatusUpdates::findByUser($user, 20);
    	  $feed = new Feed(lang(":display_name's Status Updates", array('display_name' => $user->getDisplayName())), $archive_url);
    	} else {
      	$latest_status_updates = StatusUpdates::findVisibleForUser($this->logged_user, 20);
      	$feed = new Feed(lang('Status Updates'), $archive_url);
    	} // if
    	
    	if(is_foreachable($latest_status_updates)) {
    	  foreach($latest_status_updates as $status_update) {
    	    $this->response->assign(array(
    	      'status_update' => $status_update,
    	    ));
    	    
    	    $item = new FeedItem(str_excerpt($status_update->getMessage(), 50), $status_update->getViewUrl(), $this->smarty->fetch(get_view_path('feed_item', 'status', STATUS_MODULE)), $status_update->getLastUpdateOn());
    	    $item->setId($status_update->getId());
    	    $feed->addItem($item);
    	  } // foreach
    	} // if
    	
      print render_rss_feed($feed);
      die();
    } // rss
    
  }
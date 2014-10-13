<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Announcements administration controller implementation
   *
   * @package angie.frameworks.announcements
   * @subpackage controller
   */
  abstract class FwAnnouncementsAdminController extends AdminController {
    
    /**
     * Selected announcement instance
     *
     * @var Announcement
     */
    protected $active_announcement;
    
    /**
     * Execute before any of the controller actions
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->breadcrumbs->add('announcements_admin', lang('Announcements'), Router::assemble('admin_announcements'));
      
      $announcement_id = $this->request->getId('announcement_id');
      if($announcement_id) {
        $this->active_announcement = Announcements::findById($announcement_id);
      } // if
      
      if(!($this->active_announcement instanceof Announcement)) {
        $this->active_announcement = new Announcement();
      } // if
      
      $this->response->assign('active_announcement', $this->active_announcement);
    } // __before
    
    /**
     * Display announcements administration
     */
    function index() {
      $this->wireframe->actions->add('new_announcement', lang('New Announcement'), Router::assemble('admin_announcements_add'), array(
        'onclick' => new FlyoutFormCallback(array(
          'success_event' => 'announcement_created',
          'width' => 565
        )),
        'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
      ));

      $announcements_per_page = 50;

      if($this->request->get('paged_list')) {
        $exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
        $timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;

        $this->response->respondWithData(Announcements::getSlice($announcements_per_page, $exclude, $timestamp));
      } else {
        $this->smarty->assign(array(
          'announcements' => Announcements::getSlice($announcements_per_page),
          'announcements_per_page' => $announcements_per_page,
          'total_announcements' => Announcements::count(),
        ));
      } // if
    } // index
    
    /**
     * View announcement
     */
    function view() {
    } // view
    
    /**
     * Add announcement
     */
    function add() {
      if($this->request->isAsyncCall()) {
        $announcement_data = $this->request->post('announcement');
        $this->response->assign('announcement_data', $announcement_data);

        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Creating an announcement @ ' . __CLASS__);

            $this->active_announcement->setAttributes($announcement_data);

            $body = array_var($announcement_data, 'body');

            // HTML allowed
            if((boolean) array_var($announcement_data, 'body_type', false)) {
              $body = HTML::cleanUpHtml(nl2br($body));
            } // if

            $this->active_announcement->setBody($body);

            $show_to = array_var($announcement_data, 'show_to');
            $target_type = array_var($show_to, 'target_type');
            $target_ids = array_var($show_to, $target_type);

            $this->active_announcement->setTargetType($target_type);

            $expiration = array_var($announcement_data, 'expiration');
            $expiration_type = array_var($expiration, 'type');

            $this->active_announcement->setExpirationType($expiration_type);

            if($expiration_type == FwAnnouncement::ANNOUNCE_EXPIRATION_TYPE_ON_DAY) {
              $this->active_announcement->setExpiresOn(new DateTimeValue(array_var($expiration, 'date')));
            } // if

            $this->active_announcement->setIsEnabled(true);

            $this->active_announcement->save();
            $this->active_announcement->setTargetIds($target_ids);

            // Notify via email
            if(isset($announcement_data['notify_via_email']) && $announcement_data['notify_via_email']) {
              $recipients = FwAnnouncements::findRecipientsByTarget($target_type, $target_ids);

              if($recipients) {
                AngieApplication::notifications()
                  ->notifyAbout(ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO . '/new_announcement', null, $this->logged_user)
                  ->setSubject(array_var($announcement_data, 'subject'))
                  ->setBody($body)
                  ->sendToUsers($recipients);
              } // if
            } // if

            DB::commit('Announcement created @ ' . __CLASS__);

            $this->response->respondWithData($this->active_announcement, array(
              'as' => 'announcement',
              'detailed' => true
            ));
          } catch(Exception $e) {
            DB::rollback('Failed to create new announcement @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add

    /**
     * Reorder announcements
     */
    function reorder() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $announcements = $this->request->post('announcements');

        if(is_foreachable($announcements)) {
          try {
            DB::beginWork('Reordering announcements @ ' . __CLASS__);

            foreach($announcements as $announcement_id => $position) {
              $announcement = $announcement_id ? Announcements::findById($announcement_id) : null;

              if($announcement instanceof Announcement) {
                $announcement->setPosition($position);
                $announcement->save();
              } // if
            } // foreach

            DB::commit('Announcements reordered @ ' . __CLASS__);
          } catch(Exception $e) {
            DB::rollback('Failed to reorder announcements @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
        } // if

        $this->response->ok();
      } else {
        $this->response->badRequest();
      } // if
    } // reorder
    
    /**
     * Update an existing announcement
     */
    function edit() {
      if($this->request->isAsyncCall()) {
        if($this->active_announcement->isLoaded()) {
          if($this->active_announcement->canEdit($this->logged_user)) {
            $announcement_data = $this->request->post('announcement', array(
              'subject' => $this->active_announcement->getSubject(),
              'body' => $this->active_announcement->getBody(),
              'body_type' => $this->active_announcement->getBodyType(),
              'icon' => $this->active_announcement->getIcon(),
              'show_to' => $this->active_announcement->getShowTo(),
              'expiration' => $this->active_announcement->getExpiration()
            ));

            $this->response->assign('announcement_data', $announcement_data);

            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating announcement @ ' . __CLASS__);

                $this->active_announcement->setAttributes($announcement_data);

                $body = array_var($announcement_data, 'body');

                // HTML allowed
                if((boolean) array_var($announcement_data, 'body_type', false)) {
                  $body = HTML::cleanUpHtml(nl2br($body));
                } // if

                $this->active_announcement->setBody($body);

                $show_to = array_var($announcement_data, 'show_to');
                $target_type = array_var($show_to, 'target_type');
                $target_ids = array_var($show_to, $target_type);

                $this->active_announcement->setTargetType($target_type);

                $expiration = array_var($announcement_data, 'expiration');
                $expiration_type = array_var($expiration, 'type');

                $this->active_announcement->setExpirationType($expiration_type);

                if($expiration_type == FwAnnouncement::ANNOUNCE_EXPIRATION_TYPE_ON_DAY) {
                  $this->active_announcement->setExpiresOn(new DateTimeValue(array_var($expiration, 'date')));
                } else {
                  $this->active_announcement->setExpiresOn(null);
                } // if

                $this->active_announcement->save($target_ids);

                // Notify via email
                if(isset($announcement_data['notify_via_email']) && $announcement_data['notify_via_email']) {
                  $recipients = FwAnnouncements::findRecipientsByTarget($target_type, $target_ids);

                  if($recipients) {
                    AngieApplication::notifications()
                      ->notifyAbout(ANNOUNCEMENTS_FRAMEWORK_INJECT_INTO . '/new_announcement', null, $this->logged_user)
                      ->setSubject(array_var($announcement_data, 'subject'))
                      ->setBody($body)
                      ->sendToUsers($recipients);
                  } // if
                } // if

                DB::commit("Announcement updated @ " . __CLASS__);

                $this->response->respondWithData($this->active_announcement, array(
                  'as' => 'announcement',
                  'detailed' => true
                ));
              } catch(Exception $e) {
                DB::rollback("Failed to update announcement @ " . __CLASS__);
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
    } // edit

    /**
     * Enable specific announcement
     */
    function enable() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->active_announcement->isLoaded()) {
          if($this->active_announcement->canChangeStatus($this->logged_user)) {
            try {
              DB::beginWork("Enabling announcement @ " . __CLASS__);

              $this->active_announcement->setIsEnabled(true);
              $this->active_announcement->save();

              DB::commit("Announcement enabled @ " . __CLASS__);

              $this->response->respondWithData($this->active_announcement, array(
                'as' => 'announcement',
                'detailed' => true
              ));
            } catch(Exception $e) {
              DB::rollback("Failed to enable announcement @ " . __CLASS__);
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // enable

    /**
     * Disable announcement
     */
    function disable() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if($this->active_announcement->isLoaded()) {
          if($this->active_announcement->canChangeStatus($this->logged_user)) {
            try {
              DB::beginWork("Disabling announcement @ " . __CLASS__);

              $this->active_announcement->setIsEnabled(false);
              $this->active_announcement->save();

              DB::commit("Announcement disabled @ " . __CLASS__);

              $this->response->respondWithData($this->active_announcement, array(
                'as' => 'announcement',
                'detailed' => true
              ));
            } catch(Exception $e) {
              DB::rollback("Failed to disable announcement @ " . __CLASS__);
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // disable
    
    /**
     * Delete selected announcement
     */
    function delete() {
      if($this->request->isAsyncCall()) {
        if($this->active_announcement->isLoaded()) {
          if($this->active_announcement->canDelete($this->logged_user)) {
            try {
              $this->active_announcement->delete();
              $this->response->respondWithData($this->active_announcement, array(
                'as' => 'announcement',
                'detailed' => true
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete
    
  }
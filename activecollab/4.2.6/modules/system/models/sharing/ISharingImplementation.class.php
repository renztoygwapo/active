<?php

  /**
   * Sharing helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage sharing
   */
  abstract class ISharingImplementation {
  
    /**
     * Parent object
     *
     * @var ISharing|ISubscriptions||IRoutingContext|ApplicationObject
     */
    protected $object;
    
    /**
     * Construct object sharing helper
     * 
     * @param ISharing $object
     * @throws InvalidInstanceError
     */
    function __construct(ISharing $object) {
      if($object instanceof ISharing) {
        $this->object = $object;
      } else {
        throw new InvalidInstanceError('object', $object, 'ISharing');
      } // if
    } // __construct
    
    // ---------------------------------------------------
    //  Operations
    // ---------------------------------------------------
    
    /**
     * Returns true if parent object is shared
     * 
     * @return boolean
     */
    function isShared() {
      return $this->getSharingProfile() instanceof SharedObjectProfile;
    } // isShared
    
    /**
     * Returns true if sharing has expired
     * 
     * @return boolean
     */
    function isExpired() {
      if($this->isShared()) {
        $expire_on = $this->getSharingProfile()->getExpiresOn(); // expiration date is not set so this object hasn't expired

        if ($expire_on instanceof DateValue) {
          $expire_on->advance(86400, true); // advance date for one day

          return ($expire_on->getTimestamp() < time());
        } else {
          return false;
        } // if
      } else {
        return true;
      } // if
    } // isExpired
    
    /**
     * Mark parent object as shared
     * 
     * @param IUser $by
     * @param array $additional
     * @throws Exception
     */
    function share(IUser $by, $additional = null) {
      if(empty($additional)) {
        $additional = array();
      } // if
      
      try {
        if($this->getSharingProfile() instanceof SharedObjectProfile) {
          $profile = $this->getSharingProfile();
        } else {
          $profile = new SharedObjectProfile();
          $profile->setParent($this->object);
        } // if
        
        DB::beginWork('Sharing object @ ' . __CLASS__);
      
        $profile->setSharingContext($this->getSharingContext());
        $profile->setSharingCode(isset($additional['code']) && $additional['code'] ? $additional['code'] : $this->generateSharingCode());
        $profile->setExpiresOn(isset($additional['expires_on']) && $additional['expires_on'] ? new DateTimeValue($additional['expires_on']) : null);
        
        if($this->supportsComments()) {
          $profile->setAdditionalProperty('comments_enabled', array_key_exists('comments_enabled', $additional) && $additional['comments_enabled']);
          $profile->setAdditionalProperty('attachments_enabled', array_key_exists('attachments_enabled', $additional) && $additional['attachments_enabled']);

          if($this->object instanceof IComplete) {
            $profile->setAdditionalProperty('comment_reopens', array_key_exists('comment_reopens', $additional) && $additional['comment_reopens']);
          } // if
        } // if
        
        $profile->save();

        // set visibility to public and save the previous one
        $this->object->setOriginalVisibility($this->object->getVisibility());
        $this->object->setVisibility(VISIBILITY_PUBLIC);
        $this->object->save();
        
        DB::commit('Object shared @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to share object @ ' . __CLASS__);
        throw $e;
      } // try
    } // share
    
    /**
     * Unshare parent object
     */
    function unshare(IUser $by, $unsubscribe_unregistered = false) {
      if($this->getSharingProfile() instanceof SharedObjectProfile) {
        $this->deleteSharingProfile();

        if ($unsubscribe_unregistered && $this->object instanceof ISubscriptions) {
          $subscribers = $this->object->subscriptions()->get();
          if (is_foreachable($subscribers)) {
            foreach ($subscribers as $subscriber) {
              if ($subscriber instanceof AnonymousUser) {
                $this->object->subscriptions()->unsubscribe($subscriber);
              } // if
            } // foreach
          } // if
        } // if

        // Revert the visibility
        if($this->object->getOriginalVisibility() !== null) {
          $this->object->setVisibility($this->object->getOriginalVisibility());
          $this->object->setOriginalVisibility(null);
          $this->object->save();
        } // if

        // clear cached shared profile
        $this->sharing_profile = null;
      } // if
    } // unshare

    /**
     * Invite $users to see this object
     *
     * @param Array $users
     * @param IUser $by
     * @return bool
     * @throws InvalidParamError
     */
    function invite($users, IUser $by) {
      if ($this->isShared() && $this->object instanceof ISubscriptions && is_foreachable($users)) {
        $to_invite = array();

        foreach ($users as $user) {
          if ($user instanceof IUser) {
            $to_invite[] = $user;
          } elseif (is_valid_email($user)) {
            $to_invite[] = new AnonymousUser(null, $user);
          } else {
            continue; // Ignore invalid addresses
          } // if
        } // foreach

        if (count($to_invite)) {
          $this->object->subscriptions()->set($to_invite, false); // subscribe invited users

          AngieApplication::notifications()
            ->notifyAbout('system/invite_to_shared_object', $this->object, $by)
            ->sendToUsers($to_invite);
        } // if

        return true;
      } else {
        return false;
      } // if
    } // invite
    
    // ---------------------------------------------------
    //  Definition
    // ---------------------------------------------------
    
    /**
     * Return sharing context
     * 
     * @return string
     */
    abstract function getSharingContext();
    
    /**
     * Generate default sharing code
     * 
     * @return string
     */
    function generateSharingCode() {
      return SharedObjectProfiles::getUniqueCodeForContext($this->getSharingContext());
    } // generateSharingCode
    
    /**
     * Cached sharing profile instance
     *
     * @var SharedObjectProfile::
     */
    private $sharing_profile = null;
    
    /**
     * Return sharing profile for parent object
     * 
     * @return SharedObjectProfile
     */
    function getSharingProfile() {
      if($this->sharing_profile === null) {
        $this->sharing_profile = SharedObjectProfiles::findByParent($this->object);
      } // if
      return $this->sharing_profile;
    } // getSharingProfile

    /**
     * Deletes sharing profile from database and unsets $this->sharing_profile property
     *
     * @return boolean - whether the object has been deleted
     */
    private function deleteSharingProfile() {
      if ($this->getSharingProfile() instanceof SharedObjectProfile) {
        $this->getSharingProfile()->delete();
        $this->sharing_profile = null;
        return true;
      } else {
        return false;
      } //if
    } // deleteSharingProfile
    
    /**
     * Returns true if the public page needs to be displayed as a discussion
     * 
     * @return boolean
     */
    function displayAsDiscussion() {
      return false;
    } // displayAsDiscussion
    
    // ---------------------------------------------------
    //  Shared data
    // ---------------------------------------------------
    
    /**
     * Return shared properties
     * 
     * @return NamedList
     */
    function getSharedProperties() {
      return array();
    } // getSharedProperties
    
    /**
     * Returns true if this implementation has body text to display
     * 
     * @return boolean
     */
    function hasSharedBody() {
      return false;
    } // hasSharedBody
    
    /**
     * Return prepared shared body
     * 
     * @return string
     */
    function getSharedBody() {
      return '';
    } // getSharedBody
    
    // ---------------------------------------------------
    //  Comments
    // ---------------------------------------------------
    
    /**
     * Returns true if comments support is enabled in sharing
     * 
     * @return boolean
     */
    function supportsComments() {
      return $this->object instanceof IComments;
    } // supportsComments

    /**
     * Returns true if attachments are enabled
     *
     * @return boolean
     */
    function supportsAttachments() {
      return ($this->object instanceof IAttachments);
    } // supportsAttachments

    /**
     * Are comments enabled
     *
     * @return boolean
     */
    function areCommentsEnabled() {
      return $this->supportsComments() && $this->getSharingProfile()->getAdditionalProperty('comments_enabled');
    } // areCommentsEnabled

    /**
     * Are attachments enabled
     *
     * @return boolean
     */
    function areAttachmentsEnabled() {
      return $this->areCommentsEnabled() && $this->supportsAttachments() && $this->getSharingProfile()->getAdditionalProperty('attachments_enabled');
    } // areAttachmentsEnabled

    /**
     * Returns true if parent object can be reopened
     * 
     * @return boolean
     */
    function supportsReopenIfCompleted() {
      return $this->supportsComments() && $this->object instanceof IComplete;
    } // supportsReopenIfCompleted
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can change sharing settings for parent object
     * 
     * @param IUser $user
     * @return boolean
     */
    function canChangeSettings(IUser $user) {
      return $this->object->canEdit($user);
    } // canChangeSettings
    
    /**
     * Returns true if $user can post a comment through sharing interface
     * 
     * Since this function is frequently called through public interface, $user 
     * value is optional
     * 
     * @param User $user
     * @return boolean
     */
    function canComment($user = null) {
      return $this->supportsComments() && $this->getSharingProfile()->getAdditionalProperty('comments_enabled');
    } // canComment
    
    /**
     * Returns true if $user (optional) can reopen parent object if it's already 
     * marked as completed
     * 
     * @param IUser $user
     * @return boolean
     */
    function canReopenIfCompleted($user = null) {
      return $this->supportsReopenIfCompleted() && $this->getSharingProfile()->getAdditionalProperty('comment_reopens');
    } // canReopenIfCompleted
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return shared object URL
     * 
     * @return string
     * @throws InvalidInstanceError
     */
    function getUrl() {
      if($this->getSharingProfile() instanceof SharedObjectProfile) {
        return Router::assemble('shared_object', array(
          'sharing_context' => $this->getSharingProfile()->getSharingContext(), 
          'sharing_code' => $this->getSharingProfile()->getSharingCode(),
        ));
      } else {
        throw new InvalidInstanceError('sharing_profile', $this->getSharingProfile(), 'SharedObjectProfile');
      } // if
    } // getUrl
    
    /**
     * Return URL proposal, based on $code value
     * 
     * @param string $code
     * @return string
     */
    function getUrlProposal($code) {
      return Router::assemble('shared_object', array(
        'sharing_context' => $this->getSharingContext(), 
        'sharing_code' => $code,
      ));
    } // getUrlProposal
    
    /**
     * Return sharing settings URL
     * 
     * @return string
     */
    function getSettingsUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_sharing_settings', $this->object->getRoutingContextParams());
    } // getSettingsUrl
    
    /**
     * Describe sharing of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
    	if ($this->isShared()) {
				$result['urls']['sharing_settings'] = $this->getSettingsUrl();
				$result['urls']['sharing_public'] = $this->getUrl();
				$result['sharing'] = array(
					'expires' => $this->isShared() ? $this->getSharingProfile()->getExpiresOn() : null,
					'supports_comments' => $this->supportsComments(),
          'supports_attachments' => $this->supportsAttachments(),
          'comments_enabled' => $this->areCommentsEnabled(),
          'attachments_enabled' => $this->areAttachmentsEnabled(),
          'reopen_on_new_comment' => $this->areCommentsEnabled() && $this->canReopenIfCompleted()
				);
    	} // if
    } // describe

    /**
     * Describe sharing of the parent object for $user
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      if ($this->isShared()) {
        $result['urls']['sharing_settings'] = $this->getSettingsUrl();
        $result['urls']['sharing_public'] = $this->getUrl();
        $result['sharing'] = array(
          'expires' => $this->isShared() ? $this->getSharingProfile()->getExpiresOn() : null,
          'supports_comments' => $this->supportsComments(),
          'supports_attachments' => $this->supportsAttachments(),
          'comments_enabled' => $this->areCommentsEnabled(),
          'attachments_enabled' => $this->areAttachmentsEnabled(),
          'reopen_on_new_comment' => $this->areCommentsEnabled() && $this->canReopenIfCompleted()
        );
      } // if
    } // describeForApi
    
  }
<?php

  /**
   * Application level help delegate implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class AngieHelpDelegate extends FwAngieHelpDelegate {

    /**
     * Return video groups
     *
     * @return NamedList
     */
    function getVideoGroups() {
      $groups = parent::getVideoGroups();

      $groups->add('invoicing', lang('Invoicing'));
      $groups->add('advanced', lang('Advanced Topics'));

      return $groups;
    } // getVideoGroups

    /**
     * Hide help from clients
     *
     * @param User $user
     * @return bool
     */
    function isHelpUser(User $user) {
      return !($user instanceof Client);
    } // isHelpUser

    /**
     * Return contact options that are available to $user
     *
     * @param User $user
     * @return NamedList
     */
    function getContactOptions(User $user) {
      $options = new NamedList(array(
        'chat' => array(
          'text' => lang('Chat Now'),
          'url' => 'https://www.activecollab.com/contact.html',
          'onclick' => new InitLiveChatCallback(),
        ),
        'twitter' => array(
          'text' => lang('Ask @:twitter_account', array('twitter_account' => 'activecollab')),
          'url' => 'mailto:support@activecollab.com',
          'onclick' => new TwitterIntentCallback(TwitterIntentCallback::INTENT_TWEET, array(
            'text' => '@activecollab ',
          )),
        ),
        'phone' => array(
          'text' => lang('Call :phone_number', array('phone_number' => '1-888-422-6260')),
          'description' => lang('Toll-free for US and Canada'),
        ),
      ));

      EventsManager::trigger('on_contact_options', array(&$options, &$user));

      return $options;
    } // getContactOptions

  }
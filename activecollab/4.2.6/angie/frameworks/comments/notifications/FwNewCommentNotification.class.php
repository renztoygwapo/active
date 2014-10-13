<?php

  /**
   * Framework level new comment notification instance
   *
   * @package angie.frameworks.comments
   * @subpackage models
   */
  abstract class FwNewCommentNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      $lang_vars = array(
        'author_name' => $this->getSender() instanceof IUser ? $this->getSender()->getDisplayName(true) : lang('Unknown User'),
      );

      $parent = $this->getParent();
      if($parent instanceof ApplicationObject) {
        $lang_vars['parent_name'] = $parent->getName();
        $lang_vars['parent_type'] = $parent->getVerboseType(true);
      } else {
        $lang_vars['parent_name'] = null;
        $lang_vars['parent_type'] = null;
      } // if

      if($this->isUserMentioned($user)) {
        return lang(':author_name mentioned you in a comment on ":parent_name" :parent_type', $lang_vars);
      } else {
        return lang(':author_name commented on ":parent_name" :parent_type', $lang_vars);
      } // if
    } // getMessage

    /**
     * Return message for web interface
     *
     * @param IUser $user
     * @return string
     */
    function getMessageForWebInterface(IUser $user) {
      $sender = $this->getSender();
      $parent = $this->getParent();

      if($sender instanceof IUser && $parent instanceof ApplicationObject) {
        if($this->isUserMentioned($user)) {
          return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> mentioned you in a comment on <a href=":parent_url" class="quick_view_item">":parent_name"</a> :parent_type', array(
            'author_url' => $this->getSender()->getViewUrl(),
            'author_name' => $this->getSender()->getDisplayName(true),
            'author_link_classes' => $this->getSender() instanceof User ? 'quick_view_item' : null,
            'parent_name' => $parent->getName(),
            'parent_type' => $parent->getVerboseType(true),
            'parent_url' => $parent->getViewUrl(true),
          ));
        } else {
          return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> commented on <a href=":parent_url" class="quick_view_item">":parent_name"</a> :parent_type', array(
            'author_url' => $this->getSender()->getViewUrl(),
            'author_name' => $this->getSender()->getDisplayName(true),
            'author_link_classes' => $this->getSender() instanceof User ? 'quick_view_item' : null,
            'parent_name' => $parent->getName(),
            'parent_type' => $parent->getVerboseType(true),
            'parent_url' => $parent->getViewUrl(true),
          ));
        } // if
      } else {
        return parent::getMessageForWebInterface($user);
      } // if
    } // getMessageForWebInterface

    /**
     * In case of new comment, collect mentions from the comment, not the parent
     *
     * @return bool
     */
    protected function getMentionsFromParent() {
      return false;
    } // getMentionsFromParent

    /**
     * Return parent comment
     *
     * @return Comment
     */
    function getComment() {
      return DataObjectPool::get($this->getAdditionalProperty('comment_type'), $this->getAdditionalProperty('comment_id'));
    } // getComment

    /**
     * Set a parent comment
     *
     * @param Comment $comment
     * @return NewCommentNotification
     */
    function &setComment(Comment $comment) {
      $this->setAdditionalProperty('comment_type', get_class($comment));
      $this->setAdditionalProperty('comment_id', $comment->getId());

      if(is_foreachable($comment->getNewMentions())) {
        $this->setMentionedUsers($comment->getNewMentions());
      } // if

      return $this;
    } // setComment

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'comment' => $this->getComment(),
      );
    } // getAdditionalTemplateVars

  }
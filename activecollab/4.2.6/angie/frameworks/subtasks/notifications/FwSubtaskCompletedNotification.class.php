<?php

  /**
   * Subtask completed notification
   *
   * @package angie.frameworks.subtasks
   * @subpackage notifications
   */
  abstract class FwSubtaskCompletedNotification extends BaseSubtaskNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("':name' subtask completed", array(
        'name' => $this->getSubtask() instanceof Subtask ? $this->getSubtask()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return message for web interface
     *
     * @param IUser $user
     * @return string
     */
    function getMessageForWebInterface(IUser $user) {
      $sender = $this->getSender();
      $subtask = $this->getSubtask();

      if($sender instanceof IUser && $subtask instanceof Subtask) {
        return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> has completed <a href=":subtask_url" class="quick_view_item">":subtask_name"</a> subtask', array(
          'author_url' => $sender->getViewUrl(),
          'author_name' => $sender->getDisplayName(true),
          'author_link_classes' => $this->getSender() instanceof User ? 'quick_view_item' : null,
          'subtask_name' => $subtask->getName(),
          'subtask_url' => $subtask->getViewUrl(),
        ));
      } else {
        return parent::getMessageForWebInterface($user);
      } // if
    } // getMessageForWebInterface

  }
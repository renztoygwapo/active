<?php

  /**
   * New subtask notification
   *
   * @package angie.frameworks.subtasks
   * @subpackage notifications
   */
  abstract class FwNewSubtaskNotification extends BaseSubtaskNotification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("':name' subtask created", array(
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
      $parent = $this->getParent();
      $subtask = $this->getSubtask();

      if($sender instanceof IUser && $parent instanceof ApplicationObject && $subtask instanceof Subtask) {
        return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> has added <a href=":subtask_url" class="quick_view_item">":subtask_name"</a> subtask to <a href=":parent_url" class="quick_view_item">":parent_name"</a> :parent_type', array(
          'author_url' => $sender->getViewUrl(),
          'author_name' => $sender->getDisplayName(true),
          'author_link_classes' => $this->getSender() instanceof User ? 'quick_view_item' : null,
          'subtask_name' => $subtask->getName(),
          'subtask_url' => $subtask->getViewUrl(), 
          'parent_name' => $parent->getName(),
          'parent_url' => $parent->getViewUrl(),
          'parent_type' => $parent->getVerboseType(true, $user->getLanguage()),
        ));
      } else {
        return parent::getMessageForWebInterface($user);
      } // if
    } // getMessageForWebInterface

  }
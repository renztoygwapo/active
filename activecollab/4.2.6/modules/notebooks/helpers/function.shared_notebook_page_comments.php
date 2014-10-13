<?php

  /**
   * Shared object comments helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Display shared object comments
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   * @throws InvalidParamError
   * @throws InvalidInstanceError
   */
  function smarty_function_shared_notebook_page_comments($params, &$smarty) {
    $object = array_required_var($params, 'object', true);
    $user = array_var($params, 'user', null, true); // User is optional, since this helper can be used through public interface

    if($object instanceof NotebookPage) {
      $notebook = $object->getNotebook();

      if($notebook instanceof Notebook) {
        $object_as_comment = false;
        $errors = array_var($params, 'errors', null, true);
        $comment_data = array_var($params, 'comment_data', array(), true);

        if (!($user instanceof User)) {
          $user = new AnonymousUser('', 'anonymous@anonymous.com');
        } // if

        if(isset($params['class']) && $params['class']) {
          $params['class'] .= ' object_comments';
        } else {
          $params['class'] = 'object_comments';
        } // if

        $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);

        $result = HTML::openTag('div', $params);

        $comments = $object->comments()->getPublic();
        $body = trim($object->getBody());

        // Default web interface
        if($interface == AngieApplication::INTERFACE_DEFAULT) {
          if($notebook->sharing()->canComment($user)) {
            $template = $smarty->createTemplate(get_view_path('_shared_object_comment_form', 'shared_object', SYSTEM_MODULE));
            $template->assign(array(
              'object' => $object,
              'user' => $user,
              'comment_data' => $comment_data,
              'attachments_supported' => $notebook->sharing()->getSharingProfile()->getAdditionalProperty('attachments_enabled'),
              'errors' => $errors, 
              'add_comment_url' => $notebook->sharing()->getPageUrl($object),
            ));

            $result .= $template->fetch();
          } // if

          if($comments || $object_as_comment) {
            $template = $smarty->createTemplate(get_view_path('_shared_object_comment', 'shared_object', SYSTEM_MODULE));

            if($comments) {
              foreach($comments as  $comment) {
                $template->assign(array(
                  'created_by' => $comment->getCreatedBy(),
                  'created_on' => $comment->getCreatedOn(),
                  'updated_on' => $comment->getUpdatedOn(),
                  'body' => $comment->getBody(),
                  'attachments' => $comment->attachments()->getPublic(),
                ));

                $result .= $template->fetch();
              } // foreach
            } // if

            if($object_as_comment && (!empty($body) || $object->attachments()->getPublic())) {
              $template->assign(array(
                'created_by' => $object->getCreatedBy(),
                'created_on' => $object->getCreatedOn(),
                'updated_on' => $object->getUpdatedOn(),
                'body' => $body,
                'attachments' => $object->attachments()->getPublic(),
              ));

              $result .= $template->fetch();
            } // if
          } else {
            $result .= '<p class="empty_page">' . lang('No comments yet') . '</p>';
          } // if

          // Phone interface
        } elseif($interface == AngieApplication::INTERFACE_PHONE) {
          if($comments || $object_as_comment) {
            AngieApplication::useHelper('image_url', ENVIRONMENT_FRAMEWORK);

            $result .= '<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="p">
              <li data-role="list-divider"><img src="' . smarty_function_image_url(array(
                'name' => 'icons/listviews/navigate.png',
                'module' => COMMENTS_FRAMEWORK,
                'interface' => AngieApplication::INTERFACE_PHONE
            ), $smarty) . '" class="divider_icon" alt="">' . lang('Comments') . '</li>';

            $template = $smarty->createTemplate(get_view_path('_shared_object_comment', 'shared_object', SYSTEM_MODULE));

            if($comments) {
              foreach($comments as  $comment) {
                $template->assign(array(
                  'created_by' => $comment->getCreatedBy(),
                  'created_on' => $comment->getCreatedOn(),
                  'updated_on' => $comment->getUpdatedOn(),
                  'body' => $comment->getBody(),
                  'attachments' => $comment->attachments()->getPublic(),
                ));

                $result .= $template->fetch();
              } // foreach
            } // if

            if($object_as_comment && !empty($body)) {
              $template->assign(array(
                'created_by' => $object->getCreatedBy(),
                'created_on' => $object->getCreatedOn(),
                'updated_on' => $object->getUpdatedOn(),
                'body' => $body,
                'attachments' => $object->attachments()->getPublic(),
              ));

              $result .= $template->fetch();
            } // if

            $result .= '</ul>';
          } // if

          if($notebook->sharing()->canComment($user)) {
            $template = $smarty->createTemplate(get_view_path('_shared_object_comment_form', 'shared_object', SYSTEM_MODULE));
            $template->assign(array(
              'object' => $object,
              'user' => $user,
              'comment_data' => $comment_data,
              'attachments_supported' => $notebook->sharing()->getSharingProfile()->getAdditionalProperty('attachments_enabled'),
              'errors' => $errors
            ));

            $result .= $template->fetch();
          } // if
        } // if

        return $result . '</div>';
      } else {
        throw new InvalidInstanceError('notebook', $notebook, 'Notebook');
      } // if
    } else {
      throw new InvalidParamError('object', $object, 'NotebookPage');
    } // if
  } // smarty_function_shared_notebook_page_comments
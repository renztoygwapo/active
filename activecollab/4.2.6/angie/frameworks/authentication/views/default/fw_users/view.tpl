{add_bread_crumb}Profile{/add_bread_crumb}
{use_widget name=profile_card module=$smarty.const.AUTHENTICATION_FRAMEWORK}

{object object=$active_user user=$logged_user}
  <div class="wireframe_content_wrapper">
    {inline_tabs object=$active_user}
  </div>
{/object}

<script type="text/javascript">
  App.Wireframe.Events.bind('user_updated.{$request->getEventScope()}', function (event, user) {
    if(user['class'] == 'User' && user.id == '{$active_user->getId()}') {
    	// update avatar on the profile page
      $('#user_page_' + user.id + ' #select_user_icon .properties_icon').attr('src', user.avatar.photo);

      // if user is changing their own avatar, update the image at the bottom left corner
      if ('{$active_user->getId()}' == '{$logged_user->getId()}') {
        $('#menu_item_profile img').attr('src', user.avatar.large);
        $('#menu_item_profile span.label').html(user.display_name);
      } // if
    } // if
  });
</script>
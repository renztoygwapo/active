{title}Reset Your Password{/title}
{add_bread_crumb}Reset Your Password{/add_bread_crumb}

<div id="require_index_rebuild" class="homescreen_block_message">
  <table>
    <tr>
      <td>
        <p class="homescreen_block_message_title">{lang}Sorry, Your Current Password has Expired{/lang}</p>
        <p class="homescreen_block_message_aid">{lang}Please, click on the button bellow to change your password and return to your home screen{/lang}</p>
        <p class="homescreen_block_message_button">{link href=$logged_user->getEditPasswordUrl() mode=flyout_form title="Change Password" flyout_width=500 success_event='user_password_reseted' class=link_button_alternative}Change Password Now!{/link}</p>
      </td>
    </tr>
  </table>
</div>

<script type="text/javascript">
  App.Wireframe.Events.bind('user_password_reseted.content', function(event, user) {
    App.Wireframe.Content.reload();
  });
</script>
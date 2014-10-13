<div id="user_role_users">
{if $users}
  <div id="user_role_users_listing">
    <table class="common" cellspacing="0">
      <tr>
        <th class="icon"></th>
        <th class="name">{lang}User{/lang}</th>
        <th></th>
      </tr>
      {foreach $users as $user}
      <tr class="{if $user->getState() == $smarty.const.STATE_ARCHIVED}archived_user{else}visible_user{/if}">
        <td class="icon"><img src="{$user->avatar()->getUrl($smarty.const.IUserAvatarImplementation::SIZE_SMALL)}"></td>
        <td class="name"><a href="{$user->getViewUrl()}" class="quick_view_item">{$user->getDisplayName()}</a></td>
        <td class="right">
          {if $user->getState() == $smarty.const.STATE_ARCHIVED}
            <span class="details">{lang}Archived{/lang}</span>
          {/if}
        </td>
      </tr>
      {/foreach}
    </table>
  </div>
  <p style="text-align: center">{button id=show_hide_archived_users}Hide Archived Users{/button}</p>
{else}
  <p class="empty_page">{lang}Empty{/lang}</p>
{/if}
</div>

<script type="text/javascript">
  $('#user_role_users').each(function() {
    var wrapper = $(this);

    wrapper.find('#show_hide_archived_users').click(function() {
      var button = $(this);
      var user_listing = wrapper.find('#user_role_users_listing');

      if(user_listing.is('.hide_archived_users')) {
        user_listing.removeClass('hide_archived_users');
        button.text(App.lang('Hide Archived Users'));
      } else {
        user_listing.addClass('hide_archived_users');
        button.text(App.lang('Show Archived Users'));
      } // if
    });
  });
</script>
{title}Users{/title}
{add_bread_crumb}Users{/add_bread_crumb}

<div id="company_users">
{if is_foreachable($users)}
  <table class="company_users">
    <tr>
      <th class="icon"></th>
      <th class="name">{lang}Person{/lang}</th>
      <th class="last_activity">{lang}Last Seen{/lang}</th>
    </tr>
  {foreach from=$users item=user}
    <tr class="{cycle values='odd,even'}">
      <td class="icon"><img src="{$user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)}" alt="" /></td>
      <td class="name">
        {user_link user=$user}
        {if $user->getConfigValue('title')}<span class="details block">{$user->getConfigValue('title')}</span>{/if}
      </td>
      <td class="last_activity details">{if $logged_user->getId() != $user->getId()}{$user->getLastActivityOn()|ago nofilter}{/if}</td>
    </tr>
  {/foreach}
  </table>
{else}
  <p class="empty_page"><span class="inner">{lang}There are no users in this company{/lang}{if $add_user_url}.<br />{lang add_url=$add_user_url}Would you like to <a href=":add_url">create one</a>?{/lang}{/if}</span></p>
{/if}
</div>
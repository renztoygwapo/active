<div class="user_list">
{if is_foreachable($_users)}
    <h2>{$_label}</h2>
    <table class="common company_users" cellspacing="0">
          <thead>
            <tr>
              <th class="name" colspan="2">{lang}User{/lang}</th>
              {if $_archived}
              <th class="archived">{lang}Archived On{/lang}</th>
              {/if}
            </tr>
          </thead>
          <tbody>
          {foreach from=$_users item=object}
            <tr>
              <td class="avatar"><img src="{$object->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)}" /></td>
              <td class="user_name">{$object->getDisplayName()}</td>
              {if $_archived}
              	<td class="user_archived">{$object->getUpdatedOn()|datetime}</td>
              {/if}
            </tr>
          {/foreach}
          </tbody>
        </table>
  {/if}  
</div>
{title}{$page_title}{/title}

{if is_foreachable($companies)}
	{foreach from=$companies key=map_name item=object_list name=print_object}
		<table class="people_table common" cellspacing="0">
      <thead>
        <tr>
          <th colspan="5">{$map_name}</th>
        </tr>
      </thead>
      <tbody>
      {if is_foreachable($object_list)}
        {foreach from=$object_list item=object}
        <tr>
          <td class="icon" align="left"><img src="{$object->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)}"/></td>
          <td class="people_id" align="left">#{$object->getId()}</td>
          <td class="name">{$object->getName()}</td>
        </tr>
        {/foreach}
      {else}
        <tr>
          <td class="empty_list">{lang}There are no users in this company.{/lang}</td>
        </tr>
      {/if}
		  </tbody>
	  </table>
	{/foreach}
{else}
	<p>{lang}There are no companies that match this criteria{/lang}</p>	
{/if}
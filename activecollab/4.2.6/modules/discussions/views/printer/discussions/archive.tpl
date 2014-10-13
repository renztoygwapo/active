{title}{$page_title}{/title}

{if is_foreachable($discussions)}
	
	{foreach from=$discussions key=map_name item=object_list name=print_object}
		<table class="discussion_table common" cellspacing="0">
          <thead>
            <tr>
              <th colspan="5">
              	{$map_name}
        	</th>
            </tr>
          </thead>
          <tbody>
		  {foreach from=$object_list item=object}	
		  <tr>
		  	<td class="icon" align="left">
  		  	  {if $object->isRead($logged_user)}
                {image name="icons/16x16/discussion-read-pinned.png" module="discussions"}
              {else}
                {image name="icons/16x16/discussion-unread.png" module="discussions"}
              {/if}
            </td>
		    <td class="discussion_id" align="left">#{$object->getId()}</td>
		    <td class="name">{$object->getName()}</td>
		  </tr>    
		  {/foreach}
		 </tbody>
	  </table>
	{/foreach}
{else}
	<p>{lang}There are no Discussions that match this criteria{/lang}</p>	
{/if}
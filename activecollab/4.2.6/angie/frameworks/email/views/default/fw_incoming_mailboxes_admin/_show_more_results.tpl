{if is_foreachable($activity_history)}
  {foreach from=$activity_history key=date item=activities}
  	<thead>
  	  <tr>
      	<td colspan="5">{$date|date}</td>
      </tr> 
    </thead>   
      {foreach from=$activities item=activity}
      <tr class="{cycle values='odd,even'}">
        <td class="time">{$activity->getCreatedOn()|time}</td>
        <td class="activity_log_{$activity->getState()}">
          {if $activity->getState()}
            <img src="{image_url name="layout/bits/indicator-ok.png" module=$smary.const.ENVIRONMENT_FRAMEWORK}" />
          {else}
            <img src="{image_url name="layout/bits/indicator-error.png" module=$smary.const.ENVIRONMENT_FRAMEWORK}" />
          {/if}
          {$activity->getResponse()}</td>
        <td class="sender">{$activity->getSender()}</td>
        <td class="subject">{$activity->getSubject()|excerpt:30}</td>
        <td class="options">
          {if $activity->getState()}
          	{if $activity->getResultingObjectUrl()}
     				<a href="{$activity->getResultingObjectUrl()}"><img src="{image_url name="icons/16x16/magnifier.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" title="{lang}View{/lang}" /></a>
     			{/if}
     		{else}
     			<a href="{$activity->getIncomingMail()->getImportUrl()}"><img src="{image_url name='icons/16x16/proceed.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}}" title="{lang}Resolve{/lang}" /></a>
     		{/if}
        </td>
      </tr>
    {/foreach}
  {/foreach}
{/if}
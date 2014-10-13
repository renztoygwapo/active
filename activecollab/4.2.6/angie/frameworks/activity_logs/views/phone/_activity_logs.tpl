{foreach $_activity_logs as $date => $activities}
  {if is_foreachable($activities)}
  	<li data-role="list-divider">{$date|date}</li>
    {foreach from=$activities item=activity}
    	<li><a href="{$activity->getViewUrl()}">
    		<img class="ui-li-icon" src="{$activity->getIconUrl()}" alt=""/>
				<h3>{$activity->renderHead($_activity_logs_context, $_activity_logs_interface) nofilter}</h3>
    		<p>
    			{if $request->getController() != 'users' && $request->getAction() != 'view'}
	    			{$activity->getCreatedBy()->getDisplayName(true)},
	    		{/if}
    			{$date|date_format:"%b"} {$date|date_format:"%e"} {$activity->renderTime($_activity_logs_context) nofilter}
    		</p>
    	</a></li>
    {/foreach}
  {/if}
{/foreach}
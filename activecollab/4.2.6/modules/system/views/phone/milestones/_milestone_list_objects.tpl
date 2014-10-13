{if is_foreachable($_objects)}
	<div id="list_objects">
		<ul data-role="listview" data-inset="true">
			<li data-role="list-divider">{$_title}</li>
			{foreach $_objects as $_object}
				{assign_var name=object_name}
			    {if $_type == 'tasks'}
			    	{lang task_id=$_object->getTaskId() task_name=$_object->getName()}#:task_id :task_name{/lang}
			    {else}
			      {$_object->getName()}
			    {/if}
			  {/assign_var}
			  
				{if $_object instanceof IComplete && $_object->complete()->isCompleted()}
					<li><a href="{$_object->getViewUrl()}"><strike>{$object_name}</strike></a></li>
				{else}
	      	<li><a href="{$_object->getViewUrl()}">{$object_name}</a></li>
	      {/if}
			{/foreach}
		</ul>
	</div>
{/if}
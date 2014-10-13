{title}Public Tasks{/title}

<div id="public_tasks_page">
	{form id="check_on_a_request"}
		{wrap field=code}
		  {text_field name="request[code]" label="Request Code"}
		{/wrap}
	  
	  {wrap_buttons}
	    {submit}Check{/submit}
	  {/wrap_buttons}
	{/form}
	
	{if $task_forms}
	<div id="fill_public_form">
		<p><strong>{lang}Submit a new request{/lang}</strong></p>
		<ul>
		{foreach $task_forms as $task_form}
		  <li><a href="{$task_form->getPublicUrl()}">{$task_form->getName()}</a>
		{/foreach}
		</ul>
	</div>
	{/if}
</div>
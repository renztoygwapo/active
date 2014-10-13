{title}Quick Add{/title}

<div id="quick_add_items" class="ui-grid-b">
	{if is_foreachable($quick_add_data.items)}
		<div class="main_group">
			{foreach from=$quick_add_data.items item=quick_add_item key=item_id name=item}
				{if $quick_add_item.group == 'main'}
					{assign_var name=iteration}{$smarty.foreach.item.iteration}{/assign_var}
			  	<a href="#" item_id="{$item_id}" item_url="{$quick_add_item.url}" class="ui-block-{if $iteration % 3 == 1}a{elseif $iteration % 3 == 2}b{elseif $iteration % 3 == 0}c{/if}"><img src="{$quick_add_item.icon}" alt="" /><span>{$quick_add_item.text}</span></a>
				{else}
					{append var=project_group_item_keys value=$item_id}
					{append var=project_group_item_vals value=$quick_add_item}
				{/if}
			{/foreach}
			{assign var=project_group_items value=$project_group_item_keys|@array_combine:$project_group_item_vals}
		</div>
	{/if}
	
	{if is_foreachable($project_group_items)}
		<div class="project_group">
			{foreach from=$project_group_items item=project_group_item key=item_id name=item}
				{assign_var name=iteration}{$smarty.foreach.item.iteration}{/assign_var}
			  <a href="#" item_id="{$item_id}" item_url="{$project_group_item.url}" class="ui-block-{if $iteration % 3 == 1}a{elseif $iteration % 3 == 2}b{elseif $iteration % 3 == 0}c{/if}"><img src="{$project_group_item.icon}" alt="" /><span>{$project_group_item.text}</span></a>
			{/foreach}
		</div>
	{/if}
</div>

<script type="text/javascript">
  App.Wireframe.AddBackButton.init();

	$(document).ready(function() {
		App.Wireframe.QuickAdd.init({$quick_add_data|json nofilter});
	});
</script>
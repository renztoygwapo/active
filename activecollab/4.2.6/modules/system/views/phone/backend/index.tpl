{title}Welcome{/title}
{add_bread_crumb}Home Screen{/add_bread_crumb}

<div class="homescreen">
	<div id="global_search">
		<form action="{Router::assemble('quick_backend_search')}" method="post" class="quick_search">
			<input type="search" placeholder="Search" name="q" />
		</form>
	</div>
	
	<div id="homescreen_items" class="ui-grid-b">
		{if is_foreachable($homescreen_items)}
			{foreach from=$homescreen_items item=homescreen_item name=item}
				{assign_var name=iteration}{$smarty.foreach.item.iteration}{/assign_var}
			  <a href="{$homescreen_item.url}" class="ui-block-{if $iteration % 3 == 1}a{elseif $iteration % 3 == 2}b{elseif $iteration % 3 == 0}c{/if}"><img src="{$homescreen_item.icon}" alt="" /><span>{$homescreen_item.text}</span></a>
			{/foreach}
		{/if}
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('.homescreen').closest('div[data-role="page"]').css('background-color', '#E8E8E8');
		
		{if AngieApplication::isModuleLoaded('tracking')}
	    App.Wireframe.QuickTracking.init('ui-navbar', {$quick_tracking_data|json nofilter});
	  {/if}
	  
	  App.Wireframe.Logout.init({$logout_url|json nofilter});
	});
</script>
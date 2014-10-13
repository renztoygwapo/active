{title}Quick Search{/title}
{add_bread_crumb}Search Results{/add_bread_crumb}

<div class="quick_search_results">
	<div id="global_search">
		<form action="{Router::assemble('quick_backend_search')}" method="post" class="quick_search">
			<input type="search" placeholder="Search" name="q" value="{$search_for}" />
		</form>
	</div>

	<ul data-role="listview" data-dividertheme="j" data-theme="j">
		{if is_foreachable($search_results)}
			{foreach $search_results as $search_result}
				<li><a href="{$search_result.permalink}">{$search_result.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}Search returned no results{/lang}</li>
		{/if}
	</ul>
</div>

<script type="text/javascript">
	App.Wireframe.AddBackButton.init();
</script>
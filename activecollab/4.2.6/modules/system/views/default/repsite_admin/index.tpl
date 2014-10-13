{title}Manage Repsite{/title}
{add_bread_crumb}All Repsite Pages{/add_bread_crumb}
{use_widget name="repsite_admin_page" module="system"}
<!-- 
use_widget name="paged_objects_list" module="environment"

<div id="repsite_pages_admin"></div>
-->

<div id="repsite_pages_lists">
	{if $repsite_pages}
		<table class="common" cellspacing="0">
			<tr>
				<th class="icon"></th>
				<th>ID</th>
				<th>Page Name</th>
				<th>Page Url</th>
				<th>Page HTML</th>
				<th></th>
			</tr>
			{foreach $repsite_pages as $page}
				<tr>
					<td class="icon"></td> 
					<td>{$page.id}</td>
					<td>{$page.name}</td>
					<td>{$rep_site_domain}/page.php?path_info={$page.page_url}</td>
					<td>{$page.page_html}</td>
					<td>
						<a class="delete_repsite_page" title="Delete Repsite Page" href="{$page.delete_url}"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Delete') + '" /></a>
						<a class="edit_repsite_page" title="Edit Repsite Page" href="{$page.edit_url}"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Delete') + '" /></a>
					</td>
				</tr>
			{/foreach}
		</table>
	{else}
	  <p class="empty_page">{lang}Empty{/lang}</p>
	{/if}
    
</div>

<script type="text/javascript">
	
</script>
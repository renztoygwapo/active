<div class="projects_timeline">
	{if is_foreachable($projects)}
		<div class="project_overview_box">
			<div class="project_overview_box_title">
				<h2>{$_headline}</h2>
			</div>
			<div class="project_overview_box_content">
				<div class="project_overview_box_content_inner">
					<table class="common" cellspacing="0">
						<thead>
						<tr>
							<th class="project">{lang}Project{/lang}</th>
							<th class="leader">{lang}Leader{/lang}</th>
							<th class="start">{lang}Start On{/lang}</th>
							<th class="due">{lang}Due On{/lang}</th>
						</tr>
						</thead>
						<tbody>
						{foreach from=$projects item=project}
							<tr class="">
								<td class="project">{$project.name}</td>
								<td class="leader">
									{if isset($project.leader)}
										{$project.leader}
									{else}
										---
									{/if}
								</td>
								<td class="due">{if $project.start_on instanceof DateValue}{$project.start_on|date:0}{else}{lang}No Start Date{/lang}{/if}</td>
								<td class="due">{project_due project_id=$project.id due_on=$project.due_on}</td> <!--due object=$object-->
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	{/if}
</div>
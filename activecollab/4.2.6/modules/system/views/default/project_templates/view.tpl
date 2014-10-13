{title}{$active_template->getName() nofilter}{/title}

<div id="template_home_left">
	<div class="template_home_container">
		<div class="box">
			<div class="box-title">{lang}Positions on the project template{/lang}</div>
			<div class="box-body">
				<div class="data positions"></div>
				<div class="footer">
					<a href="{assemble route=project_object_template_add template_id=$active_template->getId() object_type=position}" class="add position">{lang}Add New{/lang}</a>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="box-title">{lang}Attached Files{/lang}</div>
			<div class="box-body">
				<div class="data files">
					<!--<ul id="files_table"></ul>-->
				</div>
				<div class="footer">
					<a href="{assemble route=project_template_file_add template_id=$active_template->getId()}" class="add file_upload">{lang}Upload Files{/lang}</a>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="box-title">{lang}Task Categories{/lang}</div>
			<div class="box-body">
				<div class="data task_categories"></div>
				<div class="footer">
					<a href="{assemble route=project_object_template_add template_id=$active_template->getId() object_type=category category_type=task}" class="add task category">{lang}Add New{/lang}</a>
				</div>
			</div>
			<div class="data"></div>
		</div>
		<div class="box">
			<div class="box-title">{lang}Discussion Categories{/lang}</div>
			<div class="box-body">
				<div class="data discussion_categories"></div>
				<div class="footer">
					<a href="{assemble route=project_object_template_add template_id=$active_template->getId() object_type=category category_type=discussion}" class="add discussion category">{lang}Add New{/lang}</a>
				</div>
			</div>
		</div>
		<div class="box">
			<div class="box-title">{lang}File Categories{/lang}</div>
			<div class="box-body">
				<div class="data file_categories"></div>
				<div class="footer">
					<a href="{assemble route=project_object_template_add template_id=$active_template->getId() object_type=category category_type=file}" class="add file category">{lang}Add New{/lang}</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="template_home_right">
	<div id="template_outline"></div>
</div>

{include file=get_view_path('_initialize_sidebar', 'project_templates', $smarty.const.SYSTEM_MODULE)}

<script type="text/javascript">
	$('#template_outline').templateOutline({
		'initial_object'        : {$active_template|json nofilter},
		'default_visibility'    : {$default_visibility|json nofilter},
		'initial_subobjects'    : {$initial_subobjects nofilter},
		'subobjects_url'        : '{$subobjects_url}',
		'reorder_url'           : '{$reorder_url}',
		'users'                 : {$users|json nofilter},
		'labels'                : {$labels_map|json nofilter},
		'default_labels'        : {$default_labels|json nofilter},
		'categories'            : {$categories_map|json nofilter},
		'milestones'            : {$milestones_map|json nofilter},
		'users_map'             : {$users_map|json nofilter},
		'companies_map'         : {$companies_map|json nofilter},
		'job_types_map'         : {$job_types_map|json nofilter},
		'visual_editor'         : {$visual_editor|json nofilter},
		'add_urls'              : {$add_urls|json nofilter},
		'mass_edit_urls'        : {$mass_edit_urls|json nofilter},
		'permissions'           : {$permissions|json nofilter},
		'shortcuts_url'         : {$shortcuts_url|json nofilter},
		'unclassified_label'    : {$unclassified_label|json nofilter},
		'default_billable_status' : {$default_billable_status|json nofilter}
	});
</script>

{if !$request->isQuickViewCall()}
	<script type="text/javascript">

		// template created
		App.Wireframe.Events.bind('template_created.content', function (event, template) {
			App.Wireframe.Content.setFromUrl(template['urls']['view']);
		});

		App.Wireframe.Events.bind('template_deleted.content', function (event, template) {
			App.Wireframe.Content.setFromUrl('{assemble route=project_templates}');
		});

		App.Wireframe.Events.bind('template_edited.content', function (event, template) {
			App.Wireframe.PageTitle.set(template.name);
		});

	</script>
{/if}
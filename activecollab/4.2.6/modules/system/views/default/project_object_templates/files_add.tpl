{use_widget name="file_upload_form" module="files"}

<script type="text/javascript">
	App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div id="upload_files" class="multi_upload_files">
	{form action=$upload_url method=post class="big_form" id="{$form_id}"}
		<div class="big_form_wrapper one_form_sidebar">
			<div class="main_form_column">
				<div class="upload_form_subform">
					<table class="common multiupload_table" cellspacing="0">
						<thead>
						<tr>
							<th class="input"><strong>{lang}File{/lang}</strong></th>
							<th class="description" colspan="2">{lang}Description{/lang}</th>
						</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>

			<div class="form_sidebar form_second_sidebar">
				{wrap field=category_id}
				{label for=fileCategory}Category{/label}
				{select_file_template_category name='file[category_id]' value=$object_data.id id=fileCategory parent=$active_template user=$logged_user success_event="file_category_created"}
				{/wrap}
			</div>
		</div>

	{wrap_buttons}
	{submit}Save{/submit}
	{/wrap_buttons}
	{/form}

	<script type="text/javascript">
		$('#{$form_id}').fileUploadForm({$uploader_options|json nofilter});
	</script>
</div>
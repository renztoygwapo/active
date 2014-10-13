{title}Import File{/title}
{add_bread_crumb}Import File{/add_bread_crumb}

<div id="import_file">
	{form action=Router::assemble('calendars_import_file')}
		{wrap_fields}
			{wrap field=feed_url}
				{file_field name="file" label="Choose file"}
			{/wrap}
		{/wrap_fields}

		{wrap_buttons}
			{submit}Import{/submit}
		{/wrap_buttons}
	{/form}
</div>
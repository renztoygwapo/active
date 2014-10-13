{title}Import Feed{/title}
{add_bread_crumb}Import Feed{/add_bread_crumb}

<div id="import_feed">
	{form action=Router::assemble('calendars_import_feed')}
		{wrap_fields}
			{wrap field=feed_url}
				{text_field name='feed[url]' value=$feed_data.link label='Feed Link' required=true}
			{/wrap}
		{/wrap_fields}

		{wrap_buttons}
			{submit}Import{/submit}
		{/wrap_buttons}
	{/form}
</div>
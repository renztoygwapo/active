{wrap field=calendar_name}
  {text_field name='calendar[name]' value=$calendar_data.name label='Name' required=true}
{/wrap}

{wrap field=calendar_color}
  {select_calendar_color name='calendar[color]' value=$calendar_data.color label='Color'}
{/wrap}

{wrap field=calendar_share_type class="calendar_sharing"}
	{select_share_type name='calendar[share_type]' user_ids=$calendar_data.user_ids value=$calendar_data.share_type user=$logged_user label='Who can See this Calendar?'}
{/wrap}

{wrap field=calendar_share_can_add_events}
  {yes_no name='calendar[share_can_add_events]' value=$calendar_data.share_can_add_events label='Allow Everyone who can See this Calendar to Add Events'}
{/wrap}

<script type="text/javascript">
	$('.calendar_sharing').each(function() {
		var wrapper = $(this);

		var select_share_type = wrapper.find('select[name="calendar[share_type]"]');

		select_share_type.on('change', function() {
			var selected_users_wrapper = wrapper.find('div.selected_users_wrapper');
			if ($(this).val() == "{Calendar::SHARE_WITH_SELECTED_USERS}") {
				selected_users_wrapper.show();
			} else {
				selected_users_wrapper.hide().find('input:checkbox').attr('checked', false);
			} // if
		});
	});
</script>
<div id="calendar_event_form">
	{wrap field=calendar_event_name}
		{text_field name='calendar_event[name]' value=$calendar_event_data.name label='Name' required=true}
	{/wrap}

	{wrap field=calendar_event_parent_id}
		{select_calendar name='calendar_event[parent_id]' value=$calendar_event_data.parent_id label='Calendar' user=$selected_user required=true}
	{/wrap}

	{wrap field=all_day_event}

	{/wrap}

	{wrap field=calendar_event_starts_on class="calendar_event_starts_on"}
		<div class="col-left">
			{select_date name='calendar_event[starts_on]' value=$calendar_event_data.starts_on skip_days_off=false label='Starts' class='starts_on'}
		</div>
		<div class="col-right">
			{select_calendar_event_time name="calendar_event[starts_on_time]" value=$calendar_event_data.starts_on_time label='Time' class="calendar_event_starts_on_time" user=$logged_user}
		</div>
	{/wrap}

	{wrap field=calendar_event_ends_on class="calendar_event_ends_on"}
		{select_date name='calendar_event[ends_on]' value=$calendar_event_data.ends_on skip_days_off=false label='Ends' class='ends_on'}
	{/wrap}

	{wrap field=calendar_event_repeat_event class="calendar_event_repeat"}
		{select_repeat_option name='calendar_event' value=$calendar_event_data.repeat_event label='Repeat' class="repeat_event" user=$logged_user repeat_event_option=$calendar_event_data.repeat_event_option repeat_until=$calendar_event_data.repeat_until starts_on=$calendar_event_data.starts_on}
	{/wrap}
</div>

<script type="text/javascript">
	$('div#calendar_event_form').each(function() {
		var wrapper = $(this);

		var select = wrapper.find('select.select_calendar');
		var parent_form = select.parents('form').first();
		var submit_button = parent_form.find('button[type="submit"]');

		// disable submit button if there is no options to select
		if (!select.children().length) {
			select.attr('required', false);
			select.hide();
			submit_button.attr('disabled', true);
		} // if

		var checkbox = wrapper.find('input.all_day_event');
		var select_time = wrapper.find('select.calendar_event_starts_on_time');
		var starts_on = wrapper.find('input.starts_on');
		var ends_on = wrapper.find('input.ends_on');

		if (checkbox.is(':checked')) {
			select_time.hide();
		} // if

		wrapper.on('change', 'select.repeat_event', function() {
			var repeat_until = $(this).val();
			if (repeat_until != "{CalendarEvent::DONT_REPEAT}") {
				wrapper.find('ul.repeat_until').show();
			} else {
				wrapper.find('ul.repeat_until').hide();
			} // if

			var text = App.lang('days');
			if (repeat_until == "{CalendarEvent::REPEAT_YEARLY}") {
				text = App.lang('years');
			} else if (repeat_until == "{CalendarEvent::REPEAT_MONTHLY}") {
				text = App.lang('months');
			} else if (repeat_until == "{CalendarEvent::REPEAT_WEEKLY}") {
				text = App.lang('weeks');
			} // if

			wrapper.find('span.repeat_until_period_text').text(text);
		});

		wrapper.on('change', 'input.starts_on, input.ends_on', function() {
			var date_picker = $(this);
			var starts_on_date = new Date(starts_on.val());
			var ends_on_date = new Date(ends_on.val());

			// set ends_on date
			if (starts_on_date > ends_on_date) {
				if (date_picker.is('.starts_on')) {
					ends_on.val(starts_on.val());
				} else {
					starts_on.val(ends_on.val());
				} // if
			} // if

			// different dates
			if (starts_on_date != ends_on_date) {
				select_time.hide();
				checkbox.prop('checked', true);
			} // if
		});

		wrapper.on('click', 'input.all_day_event', function(event) {
			var checkbox = $(this);

			if (checkbox.is(':checked')) {
				select_time.hide();
			} else {
				ends_on.val(starts_on.val());
				select_time.show();
			} // if
		});

		wrapper.on('click', 'a.add_new_calendar', function(event) {
			var url = $(this).attr('href');

			App.Delegates.flyoutFormClick.apply(this, [event, {
				'width'             : '350',
				'title'             : App.lang('New Calendar'),
				'href'              : url,
				'success_event'     : 'calendar_created'
			}]);

			return false;
		});

		/**
		 * On calendar created
		 */
		App.Wireframe.Events.bind('calendar_created.content', function(event, response) {
			// find my option groups
			var my_optgroup = select.find('optgroup.mine');

			// prepare new option
			var option = $('<option value="' + response['id'] + '">' + response['name'] + '</option>');

			// if exist then append new option and return else create new group
			if (my_optgroup.length) {
				my_optgroup.append(option);
			} else {
				var optgroup = $('<optgroup class="mine" label="' + App.lang('My Calendars') + '"></optgroup>');
				optgroup.append(option);
				select.prepend(optgroup);
			} // if

			select.val(response['id']);
			select.trigger('change');
			select.attr('required', true);
			submit_button.attr('disabled', false);
			select.show();
		});
	});
</script>
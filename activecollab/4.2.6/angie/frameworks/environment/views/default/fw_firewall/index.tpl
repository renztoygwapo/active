{title}Firewall{/title}
{add_bread_crumb}Firewall{/add_bread_crumb}

<div id="firewall">
	{form action=Router::assemble('firewall')}
		<div class="content_stack_wrapper">
			<div class="content_stack_element">
				<div class="content_stack_element_info">
					<h3>{lang}Enabled{/lang}</h3>
				</div>
				<div class="content_stack_element_body">
					<div class="col">
						{wrap field=enabled}
						{yes_no name='firewall[enabled]' value=$firewall_data.enabled}
						{/wrap}
					</div>
				</div>
			</div>

			<div class="content_stack_element default_or_specified_behavior">
				<div class="content_stack_element_info">
					<div class="content_stack_optional">{checkbox name="firewall[custom_settings]" class="turn_on" for_id="subject" label="Specify" value=1 checked=$firewall_data.custom_settings}</div>
					<h3>{lang}Settings{/lang}</h3>
				</div>
				<div class="content_stack_element_body">
					<div class="default_behavior">
						<p>{lang}Define settings or default values will be used{/lang}.</p>
					</div>

					<div class="specified_behavior">
						<div class="col">
							{wrap field=max_attempts}
							  {number_field name="firewall[max_attempts]" value=$firewall_data.max_attempts label='Max Login Attempts'}
							{/wrap}
						</div>

						<div class="col">
							{wrap field=block_time}
							  {number_field name="firewall[block_time]" value=$firewall_data.block_time label='Block Time (minutes)'}
							{/wrap}
						</div>

						<div class="clear"></div>

						<div class="col">
							{wrap field=alert_user_on}
							  {number_field name="firewall[alert_user_on]" value=$firewall_data.alert_user_on label='Alert User After # Failed Logins'}
							{/wrap}
						</div>

						<div class="col">
							{wrap field=alert_admin_on}
							  {number_field name="firewall[alert_admin_on]" value=$firewall_data.alert_admin_on label='Alert Admins After # Failed Logins'}
							{/wrap}
						</div>
					</div>
				</div>
			</div>

			<div class="content_stack_element default_or_specified_behavior">
				<div class="content_stack_element_info">
					<div class="content_stack_optional">{checkbox name="firewall[white_list_enabled]" class="turn_on" for_id="subject" label="Specify" value=1 checked=$firewall_data.white_list_enabled}</div>
					<h3>{lang}Allow From{/lang}</h3>
				</div>
				<div class="content_stack_element_body">
					<div class="default_behavior">
						<p>{lang}Define the address to which you want to allow access to this application{/lang}.</p>
					</div>

					<div class="specified_behavior">
						{wrap field=white_list}
						  {textarea_field name="firewall[white_list]"}{$firewall_data.white_list nofilter}{/textarea_field}
              <p>{lang}Please provide a list of IP addresses that you want to allow (each on a new line){/lang}</p>
						{/wrap}
					</div>
				</div>
			</div>

			<div class="content_stack_element default_or_specified_behavior">
				<div class="content_stack_element_info">
					<div class="content_stack_optional">{checkbox name="firewall[black_list_enabled]" class="turn_on" for_id="subject" label="Specify" value=1 checked=$firewall_data.black_list_enabled}</div>
					<h3>{lang}Deny From{/lang}</h3>
				</div>
				<div class="content_stack_element_body">
					<div class="default_behavior">
						<p>{lang}Define the address that you want to deny access to this application{/lang}.</p>
					</div>

					<div class="specified_behavior">
						{wrap field=black_list}
						  {textarea_field name="firewall[black_list]"}{$firewall_data.black_list nofilter}{/textarea_field}
              <p>{lang}Please provide a list of IP addresses that you want to block (each on a new line){/lang}</p>
						{/wrap}
					</div>
				</div>
			</div>

			<div class="content_stack_element default_or_specified_behavior">
				<div class="content_stack_element_info">
					<h3>{lang}Temporarily Blocked{/lang}</h3>
				</div>
				<div class="content_stack_element_body">
					<div class="default_behavior">
						{if $temp_rules}
						<label class="main_label" for=temp_rules>{lang}Select which rule you want to remove{/lang}.</label>
						<ul id="temp_rules">
							{foreach $temp_rules as $object}
								<li>{checkbox name="firewall[temp_rule_ids][]" class="test" for_id="subject" label=$object.text value=$object.id}</li>
							{/foreach}
						</ul>
						{else}
							<p>{lang}Empty list{/lang}</p>
						{/if}
					</div>
				</div>
			</div>
		</div>

		{wrap_buttons}
		{submit}Save Config{/submit}
		{/wrap_buttons}
	{/form}
</div>

<script type="text/javascript">
	$('#firewall').each(function() {
		var wrapper = $(this);

		wrapper.find('div.default_or_specified_behavior').each(function() {
			var section_wrapper = $(this);

			var turn_on = section_wrapper.find('input.turn_on');

			if (turn_on.is(":checked")) {
				section_wrapper.find('div.default_behavior').hide();
				section_wrapper.find('div.specified_behavior').show();
			} // if

			turn_on.click(function() {
				if(this.checked) {
					section_wrapper.find('div.default_behavior').hide();
					section_wrapper.find('div.specified_behavior').slideDown(function() {
						var first_textarea = section_wrapper.find('textarea:first');

						if(first_textarea.length) {
							first_textarea.focus();
						} // if
					});
				} else {
					section_wrapper.find('div.specified_behavior').slideUp(function() {
						section_wrapper.find('div.default_behavior').show();
					});
				} // if
			});
		});
	});
</script>
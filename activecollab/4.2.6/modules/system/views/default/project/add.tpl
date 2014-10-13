{title}New Project{/title}
{add_bread_crumb}New project{/add_bread_crumb}

<div id="add_project_form">
  {form action=Router::assemble('projects_add') class=big_form}
    {include file=get_view_path('_project_form', 'project', 'system')}

  {if AngieApplication::behaviour()->isTrackingEnabled()}
    <input type="hidden" name="_intent_id" value="{AngieApplication::behaviour()->recordIntent('project_created')}">
  {/if}
    
    {wrap_buttons}
      {submit}Create Project{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#add_project_form').each(function() {
    var wrapper = $(this);
    
    var template_picker = wrapper.find('div.select_project_template select');
    var first_milestone_starts_on_wrapper = wrapper.find('div.first_milestone_starts_on');
	  var template_positions_container = wrapper.find('div.template_positions_container');
	  var template_position = template_positions_container.children('div:first').clone();

	  var positions_url = '{assemble route=project_template_min_data template_id="--TEMPLATE-ID--"}';

    var selected_template_id = template_picker.val() == '' ? 0 : parseInt(template_picker.val());

    if(selected_template_id < 1) {
	    first_milestone_starts_on_wrapper.hide();
	    template_positions_container.hide();
    } // if

    template_picker.change(function() {
	    first_milestone_starts_on_wrapper.hide();
	    template_positions_container.empty().hide();
	    first_milestone_starts_on_wrapper.find('input').attr('enabled', false).attr('required', false);

      var selected_template_id = template_picker.val() == '' ? 0 : parseInt(template_picker.val());

      if(selected_template_id > 0) {
	      $.ajax({
					'url' : positions_url.replace("--TEMPLATE-ID--", template_picker.val()),
		      'success' : function(response) {
			      var is_scheduled;
			      var positions;

			      // check positions
			      if (typeof response['positions'] != 'undefined') {
				      positions = response['positions'];
			      } else {
			        positions = null;
			      } // if

			      // check is scheduled
			      if (typeof response['is_scheduled'] != 'undefined') {
				      is_scheduled = response['is_scheduled'];
			      } else {
				      is_scheduled = false;
			      } // if

			      if (is_scheduled) {
				      first_milestone_starts_on_wrapper.find('input').attr('enabled', true).attr('required', true);
				      first_milestone_starts_on_wrapper.slideDown('fast');
			      } // if

			      if (positions && positions.length) {
				      $.each(positions, function(index, position) {
				        var object = template_position.clone();
					      object.children('label:first').text(position.name);
					      object.children('select:first').attr('name', 'project[project_template_positions]['+position.id+']');

					      if (position.assigned) {
						      object.children('select:first').val(position.assigned.id);
					      } // if

					      template_positions_container.append(object);
				      });

				      template_positions_container.slideDown('fast');
			      } // if
		      }
	      });
      } else {
	      //first_milestone_starts_on_wrapper.slideUp('fast');
	      //template_positions_container.empty().slideUp('fast');
      } // if
    });

	  var based_on_quote_wrapper = wrapper.find('.based_on_quote');
	  based_on_quote_wrapper.find('input:radio').on('click', function() {
		  var show_template_picker = parseInt($(this).val());
		  if (show_template_picker) {
			  template_picker.parent().hide();
			  template_picker.attr('enabled', false);
			  first_milestone_starts_on_wrapper.hide();
			  template_positions_container.empty().hide();
			  first_milestone_starts_on_wrapper.find('input').attr('enabled', false).attr('required', false);
		  } else {
			  template_picker.val(0).parent().show();
			  template_picker.attr('enabled', true);
			  first_milestone_starts_on_wrapper.find('input').attr('enabled', true).attr('required', true);
		  } // if
	  });
  });
</script>
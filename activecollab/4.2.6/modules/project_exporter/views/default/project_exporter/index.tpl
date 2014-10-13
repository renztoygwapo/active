{title}Project Exporter{/title}
{add_bread_crumb}Export Project{/add_bread_crumb}
<div id="project_exporter_container">


  {if $export_exists}
    <div id="download_link_block" class="download_link_block_top" style="display: block;">
      {lang}Previous project archive already exists. You can download it using following link:{/lang}<br />
      <div id="download_link">
        <a href="{$export_exists}" target="_blank">{$export_exists}</a>
      </div>
    </div>
  {/if}


  {form action="" method="post" class="project_exporter_modules"}
    <div class="fields_wrapper">
	    {wrap field='exporters'}
	      {label}Choose which project sections to export{/label}
		    <table class="common" cellspacing="0">
		      <tbody>
		      {foreach from=$exporters key=exporter_id item=exporter}
		        <tr>
		          <td class="checkbox"><input type="checkbox" name="selected_exporters[]" value="{$exporter_id}" id="export_{$exporter_id}" section_exporter_name='{$exporter.name}' section_exporter_url="{assemble route='project_exporter_section_exporter' exporter_id=$exporter_id project_slug=$active_project->getSlug()}" checked="checked" {if $exporter.mandatory} disabled="disabled"{/if} /></td>
		          <td>{label for="export_$exporter_id" main_label=false}{$exporter.name nofilter}{/label}</td>
		        </tr>
		      {/foreach}
		        <tr>
		          <td class="checkbox"><input type="checkbox" name="" value="finalize" id="export_finalize" section_exporter_name='{lang}Finalize{/lang}' section_exporter_url="{assemble route='project_exporter_finalize_export' project_slug=$active_project->getSlug()}" disabled="disabled" checked="checked" /></td>
		          <td>{label for="export_finalize" main_label=false}{lang}Finalize export{/lang}{/label}</td>
		        </tr>
		      </tbody>
		    </table>
	    {/wrap}
	    
	    {wrap field='visibility'}
	      {label for='visibility'}Export{/label}
	      <div><label><input type="radio" name='visibility' value="1" {if $active_project->getDefaultVisibility() == $smarty.const.VISIBILITY_PRIVATE}checked="checked"{/if} /> {lang}Only the data clients can see{/lang}</label></div>
	      <div><label><input type="radio" name='visibility' value="0" {if $active_project->getDefaultVisibility() == $smarty.const.VISIBILITY_NORMAL}checked="checked"{/if} /> {lang}All project data, including data marked as private{/lang}</label></div>
	    {/wrap}
    </div>
       
    {wrap_buttons}
      {button class="default"}Export Project{/button}
      {if !AngieApplication::isOnDemand()}
        {checkbox_field id='compress' name='compress' value=true label="Compress exported project"}
     {/if}
     {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  var is_on_demand = {AngieApplication::isOnDemand()|json};
{literal}
  var wrapper = $('#project_exporter_container');
  var form = wrapper.find('form:first');
  var sections_table = form.find('table.common');
  var mandatory_checkboxes = sections_table.find('input[type=checkbox]:disabled');
  var export_table;
  var sections_query;
  var visibility;

  /**
   * Perform export
   * 
   * @param string row_id
   * @return null
   */
  var perform_export_section = function (row) {
    var next_row = row.next('tr');

    var indicator = row.find('td.checkbox img').attr('src', App.Wireframe.Utils.indicatorUrl());

    $.ajax({
      'url' : App.extendUrl(row.attr('export_url'), {
        'sections' : sections_query,
        'visibility' : visibility,
        'compress' : compress
      }),
      'success' : function (response) {
        if (response.warnings) {
          var field = row.find('td.export_log');
          
          for (var key in response.warnings) {
            if (response.warnings.hasOwnProperty(key)) {
              field.append(response.warnings[key] + '<br />');
            } // if
          } //  if

          indicator.attr('src', App.Wireframe.Utils.indicatorUrl('warning'));
        } else {
          indicator.attr('src', App.Wireframe.Utils.indicatorUrl('ok'));
        } // if
        
        if (next_row.length) {
          perform_export_section(next_row);
        } else {
          var output_div = $('<div class="project_export_path_block" style="display: block;"></div>');
          if (response.compress == 0) {
            output_div.append('Exported project is located:<br /><div class="download_link"><strong>' + response.info_path + '</strong></div>');
          } else {
            output_div.append('<span class="download_link"><a href="' + response.info_path + '" target="_blank">' + App.lang('Download project archive') + '</a></span>');
          } //if
          $('<div class="fields_wrapper"></div>').append(output_div).insertAfter(export_table); 
        }// if
      },
      'error' : function (response, response_text) {
        indicator.attr('src', App.Wireframe.Utils.indicatorUrl('error'));
        var unknown_error = App.lang('Unknown error occurred');
        if (response.responseText[0] == '{') {
          var response_obj;
          eval('response_obj = ' + response.responseText);
          var error_message = response_obj.message ? response_obj.message : unknown_error;
          App.Wireframe.Flash.error(error_message);
          row.find('td.export_log').html(error_message);          
        } else {
          App.Wireframe.Flash.error(unknown_error);
          row.find('td.export_log').html(unknown_error);
        } // if
      }
    });
  }; // perform_export_section;

  // on form submit
  form.find('button').click(function() {
    form.hide();
    visibility = form.find('input[type=radio][name=visibility]:checked').attr('value');
    if(is_on_demand) {
      compress = 1;
    } else {
      compress = (form.find('input[type=checkbox][name=compress]:checked').attr('value') === undefined) ? 0 : 1;
    } //if
    export_table = $('<table class="common selected_exporters" cellspacing="0"></table>');
       
    sections_query = new Array();
    sections_table.find('input[type=checkbox]:checked').each(function () {
      var checkbox = $(this);
      var image = $('<img src="' + App.Wireframe.Utils.indicatorUrl('pending') + '" alt="pending" />');
      export_table.append('<tr export_url="' + checkbox.attr('section_exporter_url') + '" id="export_' + checkbox.attr('value') + '"><td class="checkbox"><img src="' + App.Wireframe.Utils.indicatorUrl('pending') + '" alt="pending" /></td><td class="name">' + checkbox.attr('section_exporter_name') + '</td><td class="export_log"></td></tr>');
      sections_query.push(checkbox.attr('value'));
    });
//    export_table.insertAfter(form);
    sections_query = sections_query.join(',');

    $('<div class="fields_wrapper"></div>').append(export_table).insertAfter(form); 
    perform_export_section(export_table.find('tr:first'));
    return false;
  });

{/literal}
</script>
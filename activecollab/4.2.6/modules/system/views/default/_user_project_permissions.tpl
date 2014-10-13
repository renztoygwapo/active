<table class="select_user_project_permissions" id="{$id}">
{foreach $project_roles as $project_role_id => $project_role_name}
  <tr>
    <td class="radio"><input type="radio" name="{$name}[{$role_id_field}]" value="{$project_role_id}" id="{$id}_role_{$project_role_id}" class="inline input_radio" {if $role_id == $project_role_id}checked="checked"{/if} /></td>
    <td class="label"><label for="{$id}_role_{$project_role_id}">{$project_role_name}</label></td>
  </tr>
{/foreach}
  <tr>
    <td class="radio"><input type="radio" name="{$name}[{$role_id_field}]" value="0" id="{$id}_role_0" class="inline input_radio" {if $role_id == 0}checked="checked"{/if} /></td>
    <td class="label">
      <label for="{$id}_role_0">{lang}Custom Permissions ...{/lang}</label>
      
      <div class="custom_permissions" {if $role_id > 0}style="display: none"{/if}>
        {select_project_permissions name="{$name}[{$permissions_field}]" value=$permissions}
      </div>
    </td>
  </tr>
</table>
<script type="text/javascript">
  $('#{$id}').each(function() {
    var wrapper = $(this);

    // Hide radio button/label if there are no project roles defined
    if(wrapper.find('tr td.radio').length == 1) {
    	wrapper.find('tr td.radio').hide();
    	wrapper.find('tr td.label label:first').hide();
    } // if
    
    // Show/hide custom permissions
    wrapper.find('td.radio input').click(function() {
      if($(this).attr('value') == '0') {
        wrapper.find('td div.custom_permissions').slideDown();
      } else {
        wrapper.find('td div.custom_permissions').slideUp();
      } // if
    });
  });
</script>
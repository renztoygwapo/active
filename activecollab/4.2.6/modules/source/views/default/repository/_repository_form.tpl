  <div class="fields_wrapper">
		{wrap field=type}
		  {label for=repositoryType}Repository Type{/label}
		  {select_repository_type disabled=$disable_url_and_type name='repository[type]' id=repositoryType data=$types selected=$repository_data.repositorytype}
		  <p class="aid">{lang}Please choose the repository type you wish to connect on.{/lang}</p>
		{/wrap}
		
		{wrap field=name}
		  {label for=repositoryName required=yes}Name{/label}
		  {text_field name='repository[name]' value=$repository_data.name id=repositoryName class='title required' maxlength="150"}
		{/wrap}
		
		{wrap field=url}
		  {label for=repositoryUrl required=yes}Repository URL or directory{/label}
		  {text_field name='repository[repository_path_url]' disabled=$disable_url_and_type value=$repository_data.repository_path_url id=repositoryUrl class='title required'}
		  <p class="aid">{lang}Please enter the root path to the repository.{/lang}</p>
		{/wrap}

    <div id="sourceAuthenticateWrapper">
      <div class="col">
      {wrap field=username}
        {label for=repositoryUsername}Username{/label}
        {text_field name='repository[username]' style='width:250px' value=$repository_data.username id=repositoryUsername}
      {/wrap}
      </div>

      <div class="col">
      {wrap field=password}
        {label for=repositoryPassword}Password{/label}
        {password_field name='repository[password]' value=$repository_data.password id=repositoryPassword}
      {/wrap}
      </div>
    </div>
		<div class="clear"></div>
		
		{wrap field=type}
		  {label for=repositoryUpdateType}Commit History Update Type{/label}
		  {select_repository_update_type name='repository[update_type]' id=repositoryUpdateType data=$update_types selected=$repository_data.updatetype}
		{/wrap}
			
		{if $logged_user->canSeePrivate()}
		  {wrap field=visibility}
		    {label for=repositoryVisibility}Visibility{/label}
		    {select_visibility name='repository[visibility]' value=$repository_data.visibility object=$project_object_repository}
		  {/wrap}
		{else}
		  <input type="hidden" name="repository[visibility]" value="1"/>
		{/if}
  </div>
  
  {wrap_buttons}
    <div class="test_connection">
      <input type="hidden" value="{$repository_test_connection_url}" id="repository_test_connection_url" />
      <button type="button"><span><span>{lang}Test Connection{/lang}</span></span></button>
      <img src="{image_url name="layout/bits/indicator-loading-normal.gif" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt='' />    
    </div>
    
    <div class="submit_repository">
	    {if $project_object_repository->isNew()}
	      {submit}Add Repository{/submit}
	    {else}
	      {submit}Save Changes{/submit}
	    {/if}
    </div>
  {/wrap_buttons}

<script type="text/javascript">
	var set_form = function () {
		var type = $('#repositoryType').val();
		if (type == 'SvnRepository') {
			$('.col').show();
		} else {
			$('.col').hide();
		}
	};

	$(document).ready (function () {
		set_form();
		$('#repositoryType').change(function () {
			set_form();
		});
	});
</script>
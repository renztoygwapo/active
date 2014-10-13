<div class="fields_wrapper">
  {wrap field=name}
    {text_field name='repository[name]' value=$repository_data.name id=repositoryName class=title label=Name required=yes}
  {/wrap}
  
  {wrap field=url}
    {text_field name='repository[repository_path_url]' disabled=$disable_url value=$repository_data.repository_path_url id=repositoryUrl class=title label='Repository Path' required=yes}
    <p class="aid">{lang}Please enter the root path to the repository{/lang}.</p>
  {/wrap}

  {wrap field=type}
    {label for=repositoryUpdateType}{lang}Commit History Update Type{/lang}{/label}
    {select_repository_update_type name='repository[update_type]' id=repositoryUpdateType data=source_module_update_types() selected=$repository_data.updatetype}
  {/wrap}
</div>

{wrap_buttons}
    <div class="test_connection" id="test_connection">
      <input type="hidden" value="{$repository_test_connection_url}" id="repository_test_connection_url" />
      <button type="button" id="test_connection_button" class="default"><span><span>{lang}Test Connection{/lang}</span></span></button>
      <img id="test_connection_loading_img" src="{image_url name="layout/bits/indicator-loading-normal.gif" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt='' />    
    </div>
    <div class="submit_repository">
      {submit}{if $form_mode != 'edit'}Add Repository{else}Save Changes{/if}{/submit}
    </div>
{/wrap_buttons}

{literal}
<script type="text/javascript">

$(document).ready (function () {
	$('#test_connection_loading_img').hide();
	$('.submit_repository').hide();

	$('#test_connection_button').click(function (event) {
		var test_connection_url = $('#repository_test_connection_url').val();
		var repository_url = $('#repositoryUrl').val();
		$('#test_connection_loading_img').show();

		$.get(test_connection_url,{url: repository_url, engine: "MercurialRepository", async : true},
		function(data){
			$('#test_connection_loading_img').hide();
			if (jQuery.trim(data) == 'ok') {
				$("#test_connection").hide();
				$('.submit_repository').show();
				App.Wireframe.Flash.success(App.lang("Connection Established"));
			} else {
			 	App.Wireframe.Flash.error(App.lang(data));
			}
		});
	});
  
    //if some field is changed we need to put form in edit mode
	$(".fields_wrapper").find('input, select, textarea').bind('change keypress', function () {
      if (!$("#test_connection").is(':visible')) {
        $("#test_connection").show();
        $('.submit_repository').hide();
      };
    });
});
</script>
{/literal}
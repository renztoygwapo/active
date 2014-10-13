<div id="homescreen_tab_{$homescreen_tab->getId()}" class="homescreen_tab_manager with_options">
  {form action=$homescreen_tab->getEditUrl()}
    {wrap_fields}{$options nofilter}{/wrap_fields}
    
    {wrap_buttons}
    	{submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#homescreen_tab_{$homescreen_tab->getId()} form:first').each(function() {
    var form = $(this);

    form.submit(function () {
      form.formUtils('start_processing');

      $.ajax({
        'url' : form.attr('action'),
        'type' : 'post', 
        'data' : form.serialize(), 
        'success' : function (response) {
          App.Wireframe.Flash.success('Tab options have been updated');
        },
        'error' : function (response) {
          App.Wireframe.Flash.error('Failed to update tab options of the selected tab. Please try again later');
        },
        'complete' : function () {
          form.formUtils('stop_processing'); // when ajax completes request, no matter whether submissions succeedded or failed
        }
      });
      
      return false;
    });
  });
</script>
{add_bread_crumb}Profile{/add_bread_crumb}

{object object=$active_company user=$logged_user}
  <div class="wireframe_content_wrapper" id="company_inline_tabs">
    {inline_tabs object=$active_company}
  </div>
{/object}

<script type="text/javascript">
  App.Wireframe.Events.bind('company_updated.{$request->getEventScope()}', function(event, company) {
    if (company['class'] == 'Company' && company.id == '{$active_company->getId()}') {
      var wrapper = $('#company_page_' + company.id);
      var logo_image = wrapper.find('#select_company_icon .properties_icon');
      logo_image.attr('src', company.avatar.photo);
    } // if
  });

  App.Wireframe.Events.bind('company_deleted.{$request->getEventScope()}', function (event, company) {
    $('#page_title_actions #page_action_add_user').hide();
  });
</script>
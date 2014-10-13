App.Inspector.Properties.ProjectRequestClient = function (object, client_interface) {
  var wrapper = $(this);
  var check_string, wrapper_content;

  if (typeof object.created_by_company == 'object') {
    check_string = object.created_by_company.id + App.clean(object.created_by.email);
  } else {
    check_string = App.clean(object.created_by_company) + App.clean(object.created_by.display_name) + App.clean(object.created_by.email);
  } // if
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);

  if (typeof object.created_by_company == 'object' && object.created_by_company['class'] == 'Company') {
    wrapper_content = App.lang('<a href=":client_permalink">:client_name</a> from <a href=":client_company_permalink">:client_company_name</a>', {
      'client_permalink' : object.created_by.permalink,
      'client_name' : object.created_by.display_name,
      'client_company_permalink' : object.created_by_company.permalink,
      'client_company_name' : object.created_by_company.name
    });
  } else {
    wrapper_content = App.lang('<a href="mailto::client_email">:client_name</a> from :client_company', {
      'client_email' : object.created_by.email,
      'client_name' : object.created_by.display_name,
      'client_company' : object.created_by_company
    });

    wrapper_content += ' <span class="save_client"><a href=""><img src="' + App.Wireframe.Utils.imageUrl('icons/16x16/save_client.png', 'system') + '" alt="' + App.lang('Add Client to People') + '"/></a></span>';
  } // if

  wrapper.empty().append(wrapper_content);
  
}; // ProjectRequestClient

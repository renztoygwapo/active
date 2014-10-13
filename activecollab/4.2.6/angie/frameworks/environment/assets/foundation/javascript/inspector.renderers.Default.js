/**
 * Default renderer for object inspector
 */
App.Inspector.Renderers.Default = function (wrapper_dom, client_interface) {
  var wrapper = $(wrapper_dom);
  
  wrapper_dom.insp_data.wireframe_head = $('<div class="head"></div>').appendTo(wrapper);
  
  wrapper_dom.insp_data.bars_wrapper = $('<div class="bars"></div>').appendTo(wrapper_dom.insp_data.wireframe_head);
  
  wrapper_dom.insp_data.inspector_table = $('<table cellspacing="0" class="inspector_table"></table>').appendTo(wrapper_dom.insp_data.wireframe_head);
  wrapper_dom.insp_data.inspector_table_row = $('<tr></tr>').appendTo(wrapper_dom.insp_data.inspector_table);

  wrapper_dom.insp_data.properties = $('<td class="properties"></td>').appendTo(wrapper_dom.insp_data.inspector_table_row);
  wrapper_dom.insp_data.indicators_wrapper = $('<td class="indicators"></td>').appendTo(wrapper_dom.insp_data.inspector_table_row);
  wrapper_dom.insp_data.indicators = $('<ul></ul>').appendTo(wrapper_dom.insp_data.indicators_wrapper);
  wrapper_dom.insp_data.actions = $('<ul class="actions"></ul>').appendTo(wrapper_dom.insp_data.wireframe_head);
  wrapper_dom.insp_data.widgets = wrapper_dom.insp_data.inspector_table_row;
  
  // body
  if (wrapper_dom.insp_data.settings.supports_body) {
    var object_wrapper = wrapper.parents('.object_wrapper:first');
    var object_content = object_wrapper.find('div.object_content');
    
    if (!object_content.length) {
      object_content = $('<div class="object_content"></div>').appendTo(object_wrapper);
    } // if
    
    if (!object_content.find('.object_body_content').length) {
      object_content.prepend('<div class="wireframe_content_wrapper"><div class="object_body object_body_default "><div class="object_body_content formatted_content"></div></div></div>');
    } // if    
  } // if
}; // Default renderer
/**
 * Permalink title widget indicator handler
 */
App.Inspector.TitlebarWidgets.Permalink = function (object, client_interface) {
  var wrapper = $(this);

  var permalink = App.Inspector.Utils.getFieldValue('urls.view', object);

  wrapper.append('<a href="' + permalink + '"><img src="' + App.Wireframe.Utils.imageUrl('icons/16x16/proceed.png', 'environment') + '" title="' + App.lang('View Item') + '"/></a>');
};
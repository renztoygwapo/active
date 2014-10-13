/**
 * Money Field property
 */
App.Inspector.Properties.MoneyField = function (object, client_interface, content_field, currency_field, additional) {
  if (!additional) {
    additional = {};
  } // if
  
  additional['modifier'] = ['App.moneyFormat', App.Inspector.Utils.getFieldValue(currency_field, object)];
  
  App.Inspector.Properties.SimpleField.apply(this, [object, client_interface, content_field, additional]);
}; // MoneyFIeld
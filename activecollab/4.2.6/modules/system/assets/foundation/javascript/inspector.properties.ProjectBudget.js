/**
 * project budget
 */
App.Inspector.Properties.ProjectBudget = function (object, client_interface) {
  var wrapper = $(this);
  var wrapper_parent = $(this).parents('li:first');
  
  var budget = object.budget;
  var currency = object.currency;
  var currency_code = object.currency_code;
  var current_cost = object.cost_summarized;
  
  var check_string = budget + '_' + currency_code;
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper.empty();
    
  if (!budget) {
    wrapper_parent.hide();
  } // if
  
  var property = $('<span class="project_budget"><span class="amount">' + App.moneyFormat(budget, currency, null, true) + '</span></span>');
  wrapper.append(property);
 
  var percentage = budget > 0 ? Math.ceil((current_cost * 100) / budget) : 0;

  var message = App.lang(':percent%', {
    'percent' : percentage
  });
  
  if (percentage > 100) {
    var message = App.lang(':percent% over', {
      'percent' : (percentage - 100)
    });
    property.addClass('cost_over_budget');
  } else if (percentage >= 90) {
    property.addClass('cost_close_to_budget');
  } else {
    property.addClass('cost_ok');
  } // if
  
  property.attr('title', message).append(' (' + message + ')');
};
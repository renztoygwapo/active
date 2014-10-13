/**
 * Reinxed odd / even rows based on selector
 *
 * Settings:
 * - selector - string used to select rows that need to be reindexed
 */
jQuery.fn.oddEven = function(settings) {
  var settings = jQuery.extend({
    selector : 'tr'
  }, settings);
  
  return this.each(function() {
    var counter = 1;
    $(this).find(settings.selector).each(function() {
      $(this).removeClass('odd').removeClass('even').addClass((counter % 2 ? 'odd' : 'even'));
      counter++;
    });
  });
};
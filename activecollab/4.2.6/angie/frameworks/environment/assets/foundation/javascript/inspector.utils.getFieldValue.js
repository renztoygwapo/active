/**
 * Return field value of some object with support of . in names
 * 
 * @param String field_name
 * @param Object object
 * @return mixed
 */
App.Inspector.Utils.getFieldValue = function (field_name, object) {
  var result;

  if (!field_name) {
    return false;
  } // if
  
  if (field_name.indexOf('.') == -1) {
    result = object[$.trim(field_name)];
  } else {
    field_name = field_name.split('.');
    result = object;
    $.each(field_name, function (index, result_step) {
      if (!result) {
        result = '';
        return false;
      } // if
      result = result[$.trim(result_step)];
    });
  } // if
  
  return result;
};
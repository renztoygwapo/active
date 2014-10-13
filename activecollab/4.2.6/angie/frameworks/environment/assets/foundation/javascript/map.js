/**
 * Map definition
 *
 * @param Array data
 * @param boolean mutable
 */
App.Map = function(data, mutable) {
  if(jQuery.isArray(data) && data.length) {
    this.data = data;
  } else {
    this.data = [];
  } // if

  this.key_index_map = {}; // Cache key - index pairs

  if(typeof(mutable) == 'undefined') {
    this.mutable = true;
  } else {
    this.mutable = mutable ? true : false; // Cast to bool
  } // if

  this.length = this.data.length; // Initial length value
};

/**
 * Return true if this map is mutable
 *
 * @return boolean
 */
App.Map.prototype.isMutable = function() {
  return this.mutable;
}; // isMutable

/**
 * Return position of a given key
 *
 * If index is not found, system will return -1
 *
 * @param String key
 * @return integer
 */
App.Map.prototype.indexOfKey = function(key) {
  if(typeof(this.key_index_map[key]) == 'undefined') {
    var index_for_key = -1;

    // Loop through all records and find matching key
    jQuery.each(this.data, function(index, value) {
      if(value.__k == key) {
        index_for_key = index;
        return false;
      } // if
    });

    this.key_index_map[key] = index_for_key;
  } // if

  return this.key_index_map[key];
}; // indexOfKey

/**
 * Return index of a given value
 *
 * If value is not found, system will return -1
 *
 * @param String value
 * @return integer
 */
App.Map.prototype.search = function(value) {

}; // search

/**
 * Iterate over elements in the map
 *
 * @param Function callback
 */
App.Map.prototype.each = function(callback) {
  jQuery.each(this.data, function(index, value) {
    var result = callback(value.__k, value.__v);

    if(typeof(result) != 'undefined' && result === false) {
      return false;
    } // if
  });
}; // each

/**
 * Returns ture if key is found in the map
 *
 * @param String key
 * @return boolean
 */
App.Map.prototype.isset = function(key) {
  return this.indexOfKey(key) != -1;
}; // isset

/**
 * Return value at given key
 *
 * @param String key
 * @return mixed
 */
App.Map.prototype.get = function(key) {
  var index = this.indexOfKey(key);

  if(index == -1) {
    throw 'Key "' + key + '" not found in map';
  } else {
    return this.data[index]['__v'];
  } // if
}; // get

/**
 * Set value at given key
 *
 * @param String key
 * @param mixed value
 */
App.Map.prototype.set = function(key, value) {
  if(this.isMutable()) {
    var index = this.indexOfKey(key);

    if(index == -1) {
      this.data.push({
        '__k' : key, '__v' : value
      });

      this.key_index_map[key] = this.data.length - 1; // Remember new position
      this.length = this.data.length; // Update length
    } else {
      this.data[index]['__v'] = value;
    } // if

    return value;
  } else {
    throw 'Map is imputable';
  } // if
}; // set

/**
 * Remove specific element from a map
 *
 * @param String key
 */
App.Map.prototype.remove = function(key) {
  if(this.isMutable()) {
    var index = this.indexOfKey(key);

    if(index == -1) {
      throw 'Key "' + key + '" not found in map';
    } else {
      this.data.remove(index);
    } // if

    this.key_index_map = {}; // Reset index cache, we'll need to rebuild it
    this.length = this.data.length; // Update lenght
  } else {
    throw 'Map is imputable';
  } // if
}; // remove
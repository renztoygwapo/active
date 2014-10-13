// if History is loaded
if (History) {
  if (typeof History.initHtml4 !== 'undefined') {
    // backup the original html4 getHashByState
    History.originalGetHashByState = History.getHashByState;
    
    /**
     * We have to hack hash by state so we can remove those annoying prefixed characters
     * 
     * @param mixed passedState
     * @return string
     */
    History.getHashByState = function (passedState) {
      var hash = History.originalGetHashByState(passedState);
      
      if (hash[0] == '#') {
        hash = hash.substring(1);
      } // if
    
      if (hash[0] == '.') {
        hash = hash.substring(1);
      } // if
    
      if (hash[0] == '/') {
        hash = hash.substring(1);
      } // if
      
      return hash;
    }; // History.getHashByState
    
    /**
     * We want to to slightly modify this function so it uses only a small part of the suid so it does not look so complicated
     * Gets a ID for a State
     * 
     * @param {State} newState
     * @return {String} id
     */
    History.getIdByState = function(newState){

      // Fetch ID
      var id = History.extractId(newState.url),
        str;
      
      if ( !id ) {
        // Find ID via State String
        str = History.getStateString(newState);
        if ( typeof History.stateToId[str] !== 'undefined' ) {
          id = History.stateToId[str];
        }
        else if ( typeof History.store.stateToId[str] !== 'undefined' ) {
          id = History.store.stateToId[str];
        }
        else {
          // Generate a new ID
          while ( true ) {
            // we really don't need no huge suid, so we will use timestamp rounded to tenth of the second
            // and only last 8 numbers of that rounded timestamp as this ensures that maximum 'distance'
            // between first and last record in storage will be three months, which is i think more than enough.
            // if this is not the case, we will expand it
            id = Math.round((new Date()).getTime() / 100);
            id = parseInt((id + '').substring((id + '').length - 8));
            
            if ( typeof History.idToState[id] === 'undefined' && typeof History.store.idToState[id] === 'undefined' ) {
              break;
            }
          }

          // Apply the new State to the ID
          History.stateToId[str] = id;
          History.idToState[id] = newState;
        }
      }

      // Return ID
      return id;
    };
        
  } // if
} // if
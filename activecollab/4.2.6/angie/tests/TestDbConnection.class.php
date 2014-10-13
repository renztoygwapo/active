<?php

  /**
   * Test database connection
   */
  class TestDbConnection extends UnitTestCase {
    
    /**
     * Construct
     */
    function __construct() {
    	parent::__construct('Test database connection');
    } // __construct
    
    /**
     * Test database escape functionality
     */
    function testEscaping() {
    	$to_escape_string = 'string';
    	$to_escape_integer = 12;
    	$to_escape_date = new DateValue('5 December 2007');
    	$to_escape_datetime = new DateTimeValue('5 December 2007 18:57:32');
    	$to_escape_array = array(12, 13, 14, 200, 'string');
    	
    	$this->assertEqual(DB::getConnection('default')->escape($to_escape_string), "'string'");
    	$this->assertEqual(DB::getConnection('default')->escape($to_escape_integer), "'12'");
    	$this->assertEqual(DB::getConnection('default')->escape($to_escape_date), "'2007-12-05'");
    	$this->assertEqual(DB::getConnection('default')->escape($to_escape_datetime), "'2007-12-05 18:57:32'");
    	$this->assertEqual(DB::getConnection('default')->escape($to_escape_array), "'12', '13', '14', '200', 'string'");
    	
    	$this->assertEqual(DB::getConnection('default')->prepare('? AND ?', array('first', 'second')), "'first' AND 'second'");
    	$this->assertEqual(DB::getConnection('default')->prepare('? AND ?', array('fi?st', 'second')), "'fi?st' AND 'second'");
    	$this->assertEqual(DB::getConnection('default')->prepare('?? AND ?', array('fi?st', 'se?ond', 'third')), "'fi?st''se?ond' AND 'third'");
    	
    	$prepared = DB::getConnection('default')->prepare('UPDATE ac_search_index SET content = ? WHERE object_id = ? AND type = ?', array(
    	  "E, isto moze da se sredi za, recimo, dodavanje ticketa. Primjer:\n\n<ol><li>Kliknes na neku od kategorija</li><li>Kliknes na Add new ticket</li><li>U formularu je pre-selektovana je kategorija u kojoj si bio maloprije</li></ol>\nPretpostavljam da ne treba puno da se cacka oko toga, ali ja nikako da potjeram aC kod sebe... svukao sam sve iz repository-ja, sredio bazu, konfigurisao putanje itd, i dobijem:\n\n<span class=\"quote\">System configuration option 'format_datetime' does not exist</span>\nAny hints? :( 123\n\n", 
    	  3002, 
    	  'ProjectObject'
    	));
    	
    	$this->assertEqual($prepared, 'UPDATE ac_search_index SET content = \'E, isto moze da se sredi za, recimo, dodavanje ticketa. Primjer:\n\n<ol><li>Kliknes na neku od kategorija</li><li>Kliknes na Add new ticket</li><li>U formularu je pre-selektovana je kategorija u kojoj si bio maloprije</li></ol>\nPretpostavljam da ne treba puno da se cacka oko toga, ali ja nikako da potjeram aC kod sebe... svukao sam sve iz repository-ja, sredio bazu, konfigurisao putanje itd, i dobijem:\n\n<span class=\"quote\">System configuration option \\\'format_datetime\\\' does not exist</span>\nAny hints? :( 123\n\n\' WHERE object_id = \'3002\' AND type = \'ProjectObject\'');
    } // testEscaping
    
  }

?>
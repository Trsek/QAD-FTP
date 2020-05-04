<?php
	# db filename
	define('DB_NAME',      "php/data/data.sqlite");
	define('WEB_DISABLE_RESET', "sekerak.eu");

    define('INTEGER', 'INTEGER');
    define('DATETIME', 'DATETIME');
    define('DATE', 'DATE');
    define('TIME', 'TIME');

	date_default_timezone_set('Europe/Prague');
	
	# the fields of db
	$db_fields = null;
	$db_fields['backflush'] = array( 'id', 'Sign', 'TransactionID', 'ItemNumber', 'Quantity', 'Location', 'Hardcode', 'Sign2', 'TransactionID2', 'WIP', 'Version', 'ProductionDate', 'PHPDateTime', 'Filename', 'RAWData');
	$db_fields['scrap']     = array( 'id', 'Sign', 'TransactionID', 'ItemNumber', 'Quantity', 'WIP1', 'WIP2', 'MES', 'Wipe', 'Version', 'PHPDateTime', 'Filename', 'RAWData');
	
	$db_fields_cast['backflush'] = array( 'TransactionID' => INTEGER, 'ItemNumber' => INTEGER, 'Quantity' => INTEGER, 'TransactionID2' => INTEGER );
	$db_fields_cast['scrap']     = array( 'TransactionID' => INTEGER, 'ItemNumber' => INTEGER, 'Quantity' => INTEGER );
	
	$db_time_stamp = array('ProductionDate','SaveDateTime');
	$db_graph      = array('TransactionID', 'ItemNumber', 'Quantity');
	
	# convert LORA date to human format
	function modify_lora_date($time)
	{
		$cdate = strtotime($time);
		# miliseconds unix time
		if( $cdate == false ) {
			$militime = DateTime::createFromFormat('U.u', $time/1000);
			$militime->setTimezone(new DateTimeZone(date_default_timezone_get())); 
			return $militime->format("Y.m.d H:i:s.u");
		}
		return Date("Y.m.d H:i:s", $cdate);
	}
	
	# copy default to release
	function checkOrInitDB()
	{
	    if( !file_exists(DB_NAME))
	    {
	        rename(DB_NAME . '.default', DB_NAME);
	    }
	}

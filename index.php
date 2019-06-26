<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

if(IsSet($_REQUEST["XDEBUG_SESSION_START"]))
{
//	$_REQUEST['tbl']='scrap';
//	$_GET['start']='0';
//	$_GET['sort']='TransactionID';
//	$_REQUEST['reset']='';
//	$_REQUEST['graph']='Quantity';
//	$_REQUEST['graphw']='';
//  $_GET['f']='scrap';
//  $_GET['f']='TransactionID';
//  $_GET['op']='%3D';
//  $_GET['search']='2';
}

session_start();
require_once("php/config.php");
require_once("php/db/mte/mte.php");
	
# autorefresh
if( isset($_REQUEST['autorefresh'])) {
	header("Refresh: 7; URL=". $_SERVER['REQUEST_URI']);
}

require_once("php/import.php");
$tabledit = new MySQLtabledit();

# need a reset db
if((( $_REQUEST['s'] == "reset") || isset($_REQUEST['reset'])) && ($_SERVER['SERVER_NAME'] != WEB_DISABLE_RESET))
	require_once ("php/reset.php");

# pocet zaznamov na jednej strane
if( isset($_REQUEST['count']))
	$tabledit->num_rows_list_view = $_REQUEST['count'];

# tbl define
$tbl = empty($_REQUEST['tbl'])? "backflush": $_REQUEST['tbl']; 

# the fields you want to see in "list view"
$tabledit->fields_in_list_view = $db_fields[$tbl];
	
echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
	<html>
	<head>
	<meta HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>
	<title>QAD tables</title>
	</head>
	<body>
	";


# database settings:
$tabledit->database_connect_quick(DB_NAME, $tbl);
$tabledit->primary_key = "id";
$tabledit->fields_required = array("id");
$tabledit->chart_column = $db_graph;
$tabledit->field_cast = $db_fields_cast[$tbl];
$tabledit->insert_button("#", "backflush", "tbl=backflush");
$tabledit->insert_button("#", "scrap", "tbl=scrap");
$tabledit->insert_button("#", "reset db", "reset");
$tabledit->do_it( basename(__FILE__));
$tabledit->database_disconnect();

# bude graf
if( !empty($_REQUEST['graph']))
	echo "<div align='center'><img src='php/graph.php?tbl=".$tbl."&column=".$_REQUEST['graph']."&graphw=".urlencode($_REQUEST['graphw'])."'></div>";

# autorefresh
echo '<form method="GET" action="?"><div align="right">
	<input type="checkbox" name="autorefresh" '. (!empty($_REQUEST['autorefresh'])? 'checked':'') .' onchange="this.form.submit()">autorefresh
	</div></form>';

# connection settings
echo "<br><div align='center'>This server IP is: ". $_SERVER['SERVER_ADDR'] .':'. $_SERVER['SERVER_PORT'] ."</div>";
echo "
	<div align='center'>Software by Zdeno Sekerak (c) 2019</div>
	</body>
	</html>"
	;

?>

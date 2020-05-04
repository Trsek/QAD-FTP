<?php
// volanie napriamo http://localhost:8113/graph.php?tbl=epe&column=tm
if(IsSet($_REQUEST["XDEBUG_SESSION_START"]))
{
	$_REQUEST['tbl']='scrap';
	$_REQUEST['column']='Quantity';
//	$_REQUEST['graphw']="WHERE ProductionDate like '%2019.05.11%'";
}

require_once ("config.php");
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_line.php');
require_once ('jpgraph/jpgraph_date.php');
require_once ('jpgraph/jpgraph_bar.php');

$column = $_REQUEST['column'];
$table = $_REQUEST['tbl'];
$where = stripslashes($_REQUEST['graphw']);

$db = new PDO('sqlite:../'. DB_NAME);
$values = $db->query("SELECT PHPDateTime, $column FROM $table $where ORDER BY PHPDateTime");

$xdata = array();
$ydata = array();
if ($values != null) {
	foreach ($values as $rij) {
		if( is_numeric($rij[$column])) {
			$xdata[] = $rij['PHPDateTime'];
			$ydata[] = $rij[$column];
		}
	}
}
$db = null;

// Create a graph instance
$graph = new Graph(1200,600);
//$graph->SetScale('intint');
//$graph->xaxis->title->Set("Graph $column of table $table");
//$graph->yaxis->title->Set($column);

$graph->SetScale('datlin');
$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->SetTickLabels($xdata);
$graph->yaxis->title->Set($column);

// Create the linear plot
$lineplot=new LinePlot($ydata);
$graph->Add($lineplot);
$graph->Stroke();

?>

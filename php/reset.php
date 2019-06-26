<?php
require_once("php/config.php");
require_once("php/db/mte/mte.php");


# stored zip files
$zip = new ZipArchive();
$zip->open(dirname(DB_NAME) ."/". Date("Ymd") .".zip", ZIPARCHIVE::CREATE);

# xml files
if ($handle = opendir(dirname(DB_NAME)))
{
	while (false !== ($file = readdir($handle)))
	{
		if( pathinfo($file, PATHINFO_EXTENSION) == "xml")
		{
			$filename = dirname(DB_NAME) .'/' .$file;
			if( is_file($filename)) {
				$zip->addFile($filename, basename($filename));
				$files[] = $filename;
			}
		}
	}
	closedir($handle);
}
# SQL file too?
if( count($files))
	$zip->addFile(DB_NAME, basename(DB_NAME));
	
# make it
$zip->close();


# remove SQL file
unlink(DB_NAME);

# clear xml files
if( count($files)) {
	foreach ($files as $filename)
		unlink($filename);
}


# open the database
$db = new PDO('sqlite:'. DB_NAME);

# create the database tables
foreach ($db_fields as $db_tbl_name => $db_tbl_column)
{
	$sql = "CREATE TABLE ". $db_tbl_name ." (id INTEGER PRIMARY KEY";
	foreach($db_tbl_column as $column)
	{
		if( $column != 'id')
		$sql .= ",". $column ." TEXT";
	}
	$sql .= ")";
	$db->exec($sql);
}
$db = null;

?>
<?php
require_once("php/config.php");
require_once("php/db/mte/mte.php");

if ($handle = opendir('.'))
{    
    while (false !== ($file = readdir($handle)))
    {    
        if( pathinfo($file, PATHINFO_EXTENSION) == "txt")
        {
            $txt[] = $file;
        }
    }    
    closedir($handle);
}

if( !empty($txt))
{
    $count_update = 0;
    $count_bad = 0;

    # database settings:
    $tabledit = new MySQLtabledit();
    
    # put to db
    foreach ($txt as $file)
    {
        try {
            # parse array
            foreach(file($file) as $row)
            {
                # parse at ;
                $column = str_getcsv("0;". $row, ";");

                # tbl name
                $_REQUEST['cm'] = (substr($column[1], 0, 1) == 'S')? 'scrap': 'backflush';
                $_REQUEST['tbl'] = $_REQUEST['cm'];

                # add to post
                $_POST = null;
                $i = 0;
                foreach ($db_fields[$_REQUEST['cm']] as $key)
                {
                    if(( $key == "Sign")
                    || ( $key == "Sign2"))
                    {
                        $prefix = substr($column[$i], 0, 1);
                        $column[$i] = substr($column[$i], 1);
                        array_splice( $column, $i, 0, $prefix );
                    }
                    
                	if( $key != 'id')
                	    $_POST[$key] = $column[$i];
                	$i++;
                }
                # maintenance info
                $_POST['PHPDateTime'] = date("Y.m.d G:i", time());
                $_POST['Filename'] = $file;
                $_POST['RAWData'] = $row;
                
                # store it
                $_POST['mte_new_rec'] = "new";
                $tabledit->database_connect_quick(DB_NAME, $_REQUEST['cm']);
                $tabledit->primary_key = "id";
                $tabledit->save_rec_directly();
            }
            $count_update++;
            //unlink($file);
            rename($file, 'history/' .$file);
        } catch (Exception $e) {
            $count_bad++;
        }
    }
        
    $tabledit->database_disconnect();
    
    echo "Updated SQL of $count_update CSV file <br>";
    if( $count_bad )
        echo "Mistake in '$count_bad' CSV's.<br>";
}
?>
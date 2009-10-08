<?php
	session_start();
    
    $dbhost = "localhost";
    $dbname = "carumba";
    $dbuser = "webcarumba";
    $dbpassw = "6Fasj6FQ7d";
    
    $root_path = "";
        
    @ $db = mysql_connect($dbhost, $dbuser, $dbpassw);
    if (!$db)
    {
      echo "Error: Could not connect to database.";
      echo mysql_error();
      exit;
    }
    mysql_select_db($dbname);
    
    $query_1="SET NAMES 'cp1251'";
    $result = mysql_query($query_1);
    if (!$result)
    {
       echo "проблема с кодировкой";
    }

?>
<?
    $mp = GetCfg("mysql.port");
    $hst = GetCfg("mysql.host");
    
    if ($mp)
        $hst .= ":$mp";

    $dblink = mysql_pconnect(
        $hst,
        GetCfg("mysql.username"), 
        GetCfg("mysql.password"));
    
    if ($dblink)
    {
       if (!mysql_select_db(GetCfg("mysql.dbname"), $dblink))
           trigger_error("Could not select db - " . mysql_error(), PM_WARNING);
       
       SetCfg("dblink", $dblink);
    }
    else
        trigger_error("Could not connect to mysql - " . mysql_error(), PM_WARNING);

    mysql_query("SET NAMES 'cp1251'");
    mysql_query("SET CHARACTER SET cp1251");

    function prepareVar($value)
    {
        if (get_magic_quotes_gpc()) 
            $value = stripslashes($value);

        if (!is_numeric($value)) 
            $value = "'" . mysql_real_escape_string($value, GetCfg("dblink")) . "'";
        
        return $value;
    }

?>

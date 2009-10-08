<?php
    error_reporting('E_ERROR');

	include ("./stat/cnt.php");
	
    include ("./service/init.inc.php");
    include ("./service/functions.php");   
    
    if (!isset($_GET["idpage"])||($_GET["idpage"] == ''))
    {
        $_GET["idpage"] = 'main';
    }
               
    $query_1="SELECT sid FROM `pm_structure` WHERE tplID = 7 and URLName='".(mysql_escape_string($_GET["idpage"]))."'";
    $result = mysql_query($query_1);
    if (!$result) {echo "<br>Ошибка доступа к данным!<br>";};
    $num = mysql_num_rows($result);
    if ($num == 0)
    {
       $_GET["idpage"] = 'main';
    }
    
    $query_1="select * from `pm_structure` where tplID = 7 and URLName='".(mysql_escape_string($_GET["idpage"]))."'";
    $result = mysql_query($query_1);
    if (!$result) {echo "<br>Ошибка обработки1.<br>";};
    $num = mysql_num_rows($result);
    if ($num == 0)
    {
        echo "<br>Ошибка обработки2.<br>";
        exit;
    }
    $page = mysql_fetch_array($result);

    if ($page["ShortTitle"] == '')
    {
         $page["ShortTitle"] = "Noname";
    }

    if ($page["URLName"] == 'catalog')
    {
        $page["sID"] = 3;
    }
    
    if (isset($_GET["idproduct"]))
    {
        $pageID = $_GET["idproduct"];
    } elseif (isset($_GET["idcatalog"]))
    {
        $query_1="select sID from `pm_structure` where DataType = 'Category'
                AND `isHidden` =0
                AND isDeleted =0 
                AND URLName='".(mysql_escape_string($_GET["idcatalog"]))."'";
        $result = mysql_query($query_1);
        if (!$result) {echo "<br>Ошибка обработки1.<br>";};
        $num = mysql_num_rows($result);
        if ($num > 0)
        {
          $cat = mysql_fetch_array($result);
          $pageID = $cat["sID"];
        }
    } else
    {
        $pageID = $page["sID"];
    }
    $q = 'SELECT Title, MetaDesc, MetaKeywords
            FROM pm_structure WHERE sID="'.$pageID.'" AND isDeleted=0 AND isVersionOfParent=0 LIMIT 1';
    $qr = mysql_query($q);

    $res = mysql_fetch_assoc($qr);
        
    $title = $res["Title"];
    $descr = $res["MetaDesc"];
    $keys = $res["MetaKeywords"];
    
    if (isset($_GET["idproduct"]))
    {
        $descr = $keys = $title;
    }
    
    include ("./service/db_data.php");
    include('./service/header.php');
    
    if (($page["Content"] == '')&&(file_exists('./service/'.$page["URLName"].'.php')))
    {
        include('./service/'.$page["URLName"].'.php');
    } else
    {
        include('./service/text.php');   
    }
        
    include('./service/footer.php');
?>
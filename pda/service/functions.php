<?php
    function getUrlByID($idpage, $idcatalog='', $idgroup=0, $idproduct=0)
    { 
       $query_1="SELECT * FROM `pm_structure` WHERE tplID = 7 and URLName='".$idpage."'";
       $result = mysql_query($query_1);
       if (!$result) {echo "<br>Ошибка обработки1.<br>";};
       $num = mysql_num_rows($result);
       if ($num == 0)
       {
            $page["URLName"] = 'main';
       } else
       {
            $page = mysql_fetch_array($result);
       }
       $add = "";
       if ($idcatalog != '') 
       {
          $add .= $idcatalog."/"; 
       }
       if ($idgroup > 0) 
       {
          $add .= $idgroup."/group/"; 
       }
       if ($idproduct > 0) 
       {
          $add .= $idproduct."/product/"; 
       }
       
       return "/".$page["URLName"]."/".$add;   
    }
?>
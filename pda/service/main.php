<?php
    $query_1="select sID from `pm_structure` where tplID = 7 and pms_sID=1";
    $result = mysql_query($query_1);
    if (!$result) {echo "<br>Ошибка обработки1.<br>";};
    $num = mysql_num_rows($result);
    if ($num > 0)
    {
        $main_page = mysql_fetch_array($result);
    
        $query_1="select * from `pm_structure` where tplID = 7 and isHidden=0 and pms_sID='".$main_page["sID"]."'";
        $result = mysql_query($query_1);
        if (!$result) {echo "<br>Ошибка обработки1.<br>";};
        $num = mysql_num_rows($result);
        for ($i=0;$i<$num;$i++)
        {
            $main = mysql_fetch_array($result);
            ?><a href="<?php echo getUrlByID($main["URLName"]); ?>"><?php echo $main["ShortTitle"]; ?></a><br /><?php 
            if ($i < $num - 1)
                ?><img src="<?php echo $root_path;?>/img/polk_.gif" alt=""><br /><?php
        }
    }
    
?>    
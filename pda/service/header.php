<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
<title><?php echo $title; ?></title>         
<meta name="Description" content="<?php echo $descr; ?>" />
<meta name="Keywords" content="<?php echo $keys; ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<style type="text/css">
form, input {
    margin:0;
}
form.srch {
    margin-bottom:7px;
}
form.logform {
    margin-top:10px;
    margin-bottom:3px; 
}
body {
    font-family: Arial, sans-serif;
    font-size:16px;
    margin:0;
}
input.search {
    font-family: Arial, sans-serif;
    font-size:16px;
    margin:0;
    width:135px;
}
input.kol {
    font-family: Arial, sans-serif;
    font-size:16px;
    margin:0;
    width:40px;
}
input.loginp {
    font-family: Arial, sans-serif;
    font-size:16px;
    margin:0;
    width:75px;
}
input.but {
    width:33px;
}
input.fast {
    width:130px;
}
textarea.tfast {
    width:190px;
}
div.cart {
    padding-top: 20px;      
    padding-right: 8px;
}
.reg {
    font-family: Arial, sans-serif;
    font-size:14px;
}
.prod {
    font-family: Arial, sans-serif;
    font-size:14px;
    margin-top:5px;    
}

input {
    font-family: Arial, sans-serif;
    font-size:16px;
    margin:0;
}
div.incart
{
  vertical-align: middle;
  height: 37px;  
}
.small {
    font-size:10px;
}
.small1 {
    font-size:8px;
}
div.pda {
    width:225px;
}
div.d1 {
    height:10px;
    font-size:8px;
}
div.login {
    font-size:14px;
}
div.content {
    margin-left:12px;
    width:213px;
}
div.polk {
    width:200px;
    height:8px;
    background-image: url('<?php echo $root_path;?>/img/polk.gif');
    background-repeat:repeat-x;
    margin:0;
    font-size:8px;
}
div.bot {
    width:225px;
    height:84px;
    background-image: url('<?php echo $root_path;?>/img/bot.gif');
    background-repeat:no-repeat;
}
div.page_head {
    width:225px;
    height:58px;
    background-image: url('<?php echo $root_path;?>/img/page_head.gif');
    background-repeat:no-repeat;
    vertical-align: middle;
}
div.copy {
    font-size:14px;
    color:#666666;
    margin-top:5px;
}
.t_bonus { color: #009966; font-weight: bold;}
.t_sale  { color: #D1153D; font-weight: bold;}
.t_salepr  { color: #D1153D; font-weight: normal;}
.t_old  { color: #000; text-decoration: line-through; font-weight: normal;}


</style>
</head>
<body bgcolor="#FFFFFF"><div class="pda">
<?php 
      if ($page["pms_sID"] == 1)
      {
        ?><img src="<?php echo $root_path;?>/img/main_head.gif" alt=""><div class="content"><?php
      } else 
      {
            $qr = mysql_query("SELECT SUM(accCount) as cnt FROM pm_as_parts p 
                               LEFT JOIN pm_structure s ON (p.sID = s.sID) 
                               LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
                               LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                               WHERE c.sessionID='$sessionID'");

            if (!$qr)
                trigger_error("Error getting cart items - " . mysql_error(), PM_FATAL);

            $goodsCount =  mysql_fetch_assoc($qr);
            if ($goodsCount["cnt"] > 0) $gcnt = $goodsCount["cnt"]; else $gcnt = 0;
        ?><div class="page_head" align="right"><div class='cart'><a href="<?php echo $root_path;?>/cart/">Корзина (<?php echo $gcnt; ?>)</a></div></div><div class="content"><a href="<?php echo $root_path;?>/index.php">Вернуться в главное меню</a><br /><br class="small" /><?php
      }
      if (isset($_REQUEST["searchtext"])) $searchval=$_REQUEST["searchtext"]; else $searchval='поиск';
      
?><form class="srch" action="<?php echo $root_path;?>/search/" method="post"><input type="text" name="searchtext" size="15" value="<?php echo $searchval; ?>" class="search"> <input type="submit" value="Найти"></form>
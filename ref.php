<?php

$dblink = mysql_connect("localhost","webcarumba","6Fasj6FQ7d");
mysql_select_db("carumba", $dblink);
mysql_query("SET NAMES 'cp1251'");
mysql_query("SET CHARACTER SET cp1251");
			
$sID = mysql_escape_string($_GET["sID"]);

//error_reporting("E_ALL");
    
include ("./_pm/class.phpmailer.php");    

        function getPathByPageID($id, $hideDefault)
        {

            $q = "SELECT URLName, pms_sID FROM pm_structure WHERE sID = '$id'";
            $qr = mysql_query($q);

            list ($urlName, $pms_sID) = mysql_fetch_row($qr);

            if ($pms_sID == NULL)
            {
                return ($urlName) ? "/$urlName" : "";
            }
            else
                return getPathByPageID($pms_sID, false) . ($urlName ? "/$urlName" : "/$id");
        }
   


echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="ru">
<head>
<title>Письмо другу</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<link href="/css/print.css" rel="stylesheet" type="text/css" />
</head>
<body><div class="bac">
<br/>
<div class="podbor" style="margin-left:20px;">';

$view = 1;

if (($_POST["send"] == 1) & ($_POST["email2"] != '') & ($_POST["email1"] != '') & ($_POST["name1"]!='') & ($_POST["name2"]!='') & ($_POST["comment"]!=''))
{
    $URL = getPathByPageID($_POST["sID"],true);
    
    $query = "SELECT Title FROM pm_structure WHERE pm_structure.sID='".$sID."'";
    $result= mysql_query($query);
    if(mysql_num_rows($result)) {
        $row = mysql_fetch_assoc($result);
    }    
                        $subj = "Описание \"" . $row["Title"] . "\" в интернет-магазине Карумба.Ру";
                        $body = "
Привет, ". $_POST["name2"] ."!<br /><br />
 
В интернет-магазине автозапчастей Карумба.Ру <a href='http://www.carumba.ru'>http://www.carumba.ru</a> есть описание на <strong>\"" . $row["Title"] . "\"</strong>.
Ссылка для просмотра подробного описания <a href='http://www.carumba.ru" . $URL . "'>http://www.carumba.ru" . $URL . "</a> <br /><br />
Комментарий: " . $_POST["comment"] .  "<br /><br /> 
Данный товар можно купить с доставкой по Санкт-Петербургу и всей России!<br />
Это письмо сгенерированно системой \"ПОСЛАТЬ ССЫЛКУ ДРУГУ\" на сайте <a href='http://www.carumba.ru'>http://www.carumba.ru</a>.<br /><br /> 
 
-- <br />
С уважением,<br />
 ". $_POST["name1"] ."<br />
 ". $_POST["email1"] ."<br /><br /> ";
//echo $body;
                        $mail = new PHPMailer();

                        $mail->IsSMTP(); // set mailer to use SMTP
                        $mail->Host = "localhost";  // specify main and backup server
                        $mail->SMTPAuth = true;     // turn on SMTP authentication
                        $mail->Username = "robot@carumba.ru";  // SMTP username
                        $mail->Password = "Vifi2Ht6b"; // SMTP password

                        $mail->From = "sales@carumba.ru";
                        $mail->FromName = "Карумба.Ру";

                        $mail->WordWrap = 50;                                 // set word wrap to 50 characters
                        $mail->IsHTML(true);                                  // set email format to HTML
                        $mail->Subject = $subj;
                        $mail->Body = $body;
                        $mail->AddAddress('sales@carumba.ru');
                        $mail->AddAddress($_POST["email2"]);
                        
        if(!$mail->Send())
        {
            echo 'Ошибка: ' . $mail->ErrorInfo;
        } else {
            echo "<br><br>Письмо отправлено!<br>";        
        }
                        
        
                        
                        $view = 0;
} 

if ($view == 1)
{
    
if ($_POST["send"] == 1)
{
   echo "Все поля обязательны для заполнения!<br/><br/>";
}    
$tpl = '
<form action="" method="post" id="refForm">
<input type="hidden" name="send" value="1">
<input type="hidden" name="sID" value="'.$sID.'">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="reg">
  <tr>
      <td class="leftup">Ваше имя:</td>
      <td class="rightup"><input class="input03" class="input03" name="name1" value="'.$_POST["name1"].'" /></td>
  </tr>
  <tr class="both">
      <td class="leftmid">Ваш e-mail:</td>
      <td class="rightmid"><input class="input03" class="input03" name="email1" value="'.$_POST["email1"].'" /></td>
  </tr>
  <tr class="both">
      <td class="leftmid">Имя друга:</td>
      <td class="rightmid"><input class="input03" class="input03" name="name2" value="'.$_POST["name2"].'" /></td>
  </tr>
  <tr class="both">
      <td class="leftmid">E-mail друга:</td>
      <td class="rightmid"><input class="input03" class="input03" name="email2" value="'.$_POST["email2"].'" /></td>
  </tr>
  <tr class="both">
      <td class="leftmid">Комментарий:</td>
      <td class="rightmid"><textarea class="input05" name="comment">'.$_POST["comment"].'</textarea></td>
  </tr> 
</table>
    <br />
      <input type="image" src="/images/send_fr.gif" />
  </form>';

echo $tpl;
}
echo '</div></div>
</body>
</html>';

    mysql_close($dblink);   
?>

<?php


    echo "<b>Ваша корзина</b> (".$gcnt." товаров):<br /><br /><form method='post' action='".$root_path."/cart/'><input type='hidden' name='recalc' value='1'>";
    echo $page["Content"];
    $qr = mysql_query("SELECT salePrice, accCount, ptPercent, pt.ptID, ShortTitle, p.sID, p.accID, s.pms_sID FROM pm_as_parts p 
                       LEFT JOIN pm_structure s ON (p.sID = s.sID) 
                       LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
                       LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                       WHERE c.sessionID='$sessionID'");

    $goodsCount = mysql_num_rows($qr);    
    
    if ($goodsCount > 0)
    {
        $cardSum = 0;
        $curSum = 0;
        $sum = 0;

        $total = 0;
        
       $isCardInCart  = 0; 
       for ($i = 0; $i < $goodsCount; $i++) 
       {
          $r = mysql_fetch_array($qr);
          
          $qr1 = mysql_query("SELECT sID, URLName 
                             FROM pm_structure 
                             WHERE sID=".$r["pms_sID"]);
          $rp = mysql_fetch_array($qr1);
           
          echo ($i+1).") <a href=\"".$root_path."/catalog/".$rp["URLName"]."/".$r["sID"]."/product/\">".$r["ShortTitle"]."</a><br />";
                    switch($r['ptID']){
                        case 1:
                            if($userData['cardID'] || $isCardInCart){
                                $curPrice = round($r["salePrice"] - ($r["salePrice"] * 5 / 100));
                            }else {
                                $curPrice = $r['salePrice'];
                            }
                            $cardPrice = round($r["salePrice"] - ($r["salePrice"] * 5 / 100));
                            break;
                        case 2:
                            if($userData['cardID'] || $isCardInCart){
                                $curPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            }else {
                                $curPrice = $r['salePrice'];
                            }
                            $cardPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            break;
                        case 3:
                            if($userData['cardID'] || $isCardInCart){
                                $curPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            }else {
                                $curPrice = $r['salePrice'];
                            }
                            $cardPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            break;
                        case 4:
                            if($userData['cardID'] || $isCardInCart){
                                $curPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            }else {
                                $curPrice = $r['salePrice'];
                            }
                            $cardPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            break;
                        case 5:
                            $curPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            $cardPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            break;
                        case 6:
                            $curPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            $cardPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            break;
                        case 7:
                            $curPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            $cardPrice = round($r["salePrice"] - ($r["salePrice"] * $r["ptPercent"] / 100));
                            break;
                        default:
                            $curPrice = $r['salePrice'];
                            $cardPrice = $r['salePrice'];
                            break;
                    }

                    $curPrice *= $r["accCount"];
                    $cardPrice *= $r["accCount"];

                    $price = $r['salePrice']*$r["accCount"];

                    $cardSum += $cardPrice ;
                    $curSum += $curPrice ;
                    echo "<b>".$cardPrice."</b> / ".$curPrice." руб.<br />
                    Кол-во: <input size='3' class='kol' value='".$r["accCount"]."' name='kol_".$r['accID']."'> &nbsp;Удалить: <input type='checkbox' value='1' name='del_".$r['accID']."'><br /><img src='".$root_path."/img/polk_.gif' alt=''><br />";
                    $sum += $price;
                    $total += $r['accCount'];
       }
       echo "<br />Сумма заказа: ".$curSum." руб.<br />Сумма <a href='".$root_path."/club/'>клубная</a>: <b>".$cardSum."</b> руб.<br />
       <input type='image' src='".$root_path."/img/recalc.gif' alt=''><br /><img src='".$root_path."/img/polk_.gif' alt=''></form>
       ";
        if ($userID > 1)
        {
           echo "<br />
              <b>Заказать:</b><br /><form method='post' action='".$root_path."/cart/'><input type='hidden' name='send_order' value='1'>
              <textarea class='tfast' rows='6' name='comment'>Ваш комментарий</textarea><br />
              <input type='image' src='".$root_path."/img/sendorder.gif' alt=''></form>
           ";             
        } else
        {
           echo "<br />
              <b>Быстрый заказ:</b><br /><form method='post' action='".$root_path."/cart/'><input type='hidden' name='send_order' value='1'>
              <table cellpadding=0 cellspacing=0>
              <tr><td height='25'><b>Имя:*</b></td><td><input type='text' class='fast' name='name'></td></tr>
              <tr><td height='25'><b>Тел.:*</b></td><td><input type='text' class='fast' name='phone'></td></tr>
              <tr><td height='25'><b>Адрес:</b></td><td><input type='text' class='fast' name='adress'></td></tr>
              <tr><td height='25'><b>E-mail: &nbsp;</b></td><td><input type='text' class='fast' name='email'></td></tr></table>
              <textarea class='tfast' rows='6' name='comment'>Ваш комментарий</textarea><br />
              <input type='image' src='".$root_path."/img/sendorder.gif' alt=''></form>
           "; 
        }
    } else
    {
       if (isset($order_confirm) && ($order_confirm == 1))
       {
          echo "Ваш заказ успешно обработан и ему присвоен номер <b>".$orderID."</b>.";
       } else
       { 
          echo "Корзина пуста!"; 
       }
    }
    
?>
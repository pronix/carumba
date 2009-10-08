<?php
    
    
    include ("./service/class.authenticationmgr.php");
    include ("./service/class.phpmailer.php");
    
    $auth = new AuthenticationManager();
    
        function generateNewPass()
        {

            $pass = "";
            $length = rand(6, 12);
            for ($i = 0; $i < $length; $i++) {
                $val = array();
                $val[] = rand(48, 57);
                $val[] = rand(65, 90);
                $val[] = rand(97, 122);
                $num = rand(0, 2);
                $pass .= chr($val[$num]);
            }
            //echo $pass;
            return $pass;
        }
        function setNewPass($row) 
        {
            $new_pass = generateNewPass();
            $update = "UPDATE pm_users SET Password = MD5('".$new_pass."') WHERE Login = '".$row['Login']."' && Email = '".$row['Email']."'";
            mysql_query($update);
            return $new_pass;
        }
    
    
    if (isset($_GET["logout"]) && ($_GET["logout"] == 1))
    {
       $auth->endSession(); 
    }
    
    if (isset($_POST["login"]) && isset($_POST["psw"]))
    {
        $auth->authenticate($_POST["login"], $_POST["psw"]);
    }
    
    $sessionID = $auth->getSessionID();
    $userID = $auth->getUserID();
    $userData = $auth->getUserData($userID,'');

    if (isset($_GET["incart"])&&($_GET["incart"] > 0))
    {
            settype($_GET["incart"],"integer");
            
            $sessionID = $auth->getSessionID();
			if (isset($sessionID) & $sessionID != '')
			{
            	$qr = mysql_query("SELECT cartID FROM pm_as_cart WHERE sessionID='" . $sessionID . "' AND accID=" . $_GET["incart"]);
            	if ($qr)
            	{
                	if (mysql_num_rows($qr) == 0)
                	{
                    	$qr = mysql_query("INSERT INTO pm_as_cart (sessionID, accID, accCount, addDate) VALUES('$sessionID', ".$_GET["incart"].", 1, NOW())");
                	}
                	else
                	{
                    	list($cartID) = mysql_fetch_row($qr);
                    	$qr = mysql_query("UPDATE pm_as_cart SET accCount = accCount + 1, addDate=NOW() WHERE cartID=$cartID");
                	}
            	}
            }
    }
    
    if (isset($_POST["send_order"]) && ($_POST["send_order"] == 1))
    {
            $userName = $_POST["name"];
            $userPhone = $_POST["phone"];
            $userEmail = $_POST["email"];
            $userAdress = $_POST["adress"];
            $userComment = $_POST["comment"];
            
            //$isCardInCart = Cart::isItemInCart(GetCfg('Club.GoodID'));
            $cardStID = 1;
            if($userData['cardID']) {
                $cardStID = 4;
            }
            
                    
                $query = "SELECT p.sID, p.accID, p.ptID, ShortTitle, salePrice, accCount, ptPercent, accPlantID, p.smallPicture, s.MetaDesc, s.pms_sID, pc.PicturePath FROM pm_as_parts p 
                                   LEFT JOIN pm_structure s ON (p.sID = s.sID) 
                                   LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
                                   LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                                   LEFT JOIN pm_as_categories pc ON (s.pms_sID = pc.sID)
                                   WHERE c.sessionID='".$sessionID."'";
                $result = mysql_query($query);
                if(mysql_num_rows($result)) {
                    

                    $orderQuery = "INSERT INTO pm_order (userID, stDate, cardStID, stID, comment, pda) VALUES ('".$userData['userID']."', '".date("Y-m-d H:i")."', '".$cardStID."', '1', '".$userComment."', 'pda')";
                    //echo $orderQuery.'<br>';
                    mysql_query($orderQuery);

                    $orderQuery = "SELECT orderID FROM pm_order WHERE userID = '".$userID."' && stID = '1' ORDER BY orderID desc";
                    //echo $orderQuery.'<br>';
                    $orderResult = mysql_query($orderQuery);
                    $row = mysql_fetch_assoc($orderResult);
                    $orderID = $row['orderID'];
                    
                    $orderStatusQuery = "INSERT INTO pm_order_status_date (orderID, stID, stDate) VALUES ('".$orderID."', '1', '".date("Y-m-d H:i")."')";
                    //echo $orderStatusQuery.'<br>';
                    mysql_query($orderStatusQuery);
                    
                    if($userID == 1) {
                        $quickQuery = "INSERT INTO pm_order_quick (orderID, userName, userPhone, userEmail, userAdress) VALUES ('".$orderID."', '".$userName."', '".$userPhone."', '".$userEmail."', '".$userAdress."')";
                        //echo $quickQuery.'<br>';
                        mysql_query($quickQuery);
                    }
                    $isCardInCart = 0;
                    while($cartRow = mysql_fetch_assoc($result)) {
                        switch($cartRow['ptID']){
                            case 1:
                                if($userData['cardID'] || $isCardInCart){
                                    $curPrice = round($cartRow["salePrice"] - ($cartRow["salePrice"] * 5 / 100));
                                }else {
                                    $curPrice = $cartRow['salePrice'];
                                }
                                $cardPrice = round($cartRow["salePrice"] - ($cartRow["salePrice"] * 5 / 100));
                                break;
                            case 2:
                                if($userData['cardID'] || $isCardInCart){
                                    $curPrice = round($cartRow["salePrice"] - ($cartRow["salePrice"] * $cartRow["ptPercent"] / 100));
                                }else {
                                    $curPrice = $cartRow['salePrice'];
                                }
                                break;
                            case 3:
                                if($userData['cardID'] || $isCardInCart){
                                    $curPrice = round($cartRow["salePrice"] - ($cartRow["salePrice"] * $cartRow["ptPercent"] / 100));
                                }else {
                                    $curPrice = $cartRow['salePrice'];
                                }
                                break;
                            case 4:
                                if($userData['cardID'] || $isCardInCart){
                                    $curPrice = round($cartRow["salePrice"] - ($cartRow["salePrice"] * $cartRow["ptPercent"] / 100));
                                }else {
                                    $curPrice = $cartRow['salePrice'];
                                }
                                break;
                            case 5:
                                $curPrice = round($cartRow["salePrice"] - ($cartRow["salePrice"] * $cartRow["ptPercent"] / 100));
                                break;
                            case 6:
                                $curPrice = round($cartRow["salePrice"] - ($cartRow["salePrice"] * $cartRow["ptPercent"] / 100));
                                break;
                            case 7:
                                $curPrice = round($cartRow["salePrice"] - ($cartRow["salePrice"] * $cartRow["ptPercent"] / 100));
                                break;
                            default:
                                $curPrice = $cartRow['salePrice'];
                                break;
                        }

                        $curPrice *= $cartRow["accCount"];

                        $partQuery = "INSERT INTO pm_order_parts (orderID, accID, accCount, price) VALUES ('".$orderID."', '".$cartRow['accID']."', '".$cartRow['accCount']."', '".$curPrice."')";
                        //echo $partQuery.'<br>';
                        mysql_query($partQuery);
                    }

                    mysql_query("DELETE FROM pm_as_cart WHERE sessionID='".$sessionID."'");

                    //send email order
                    
                    $order_confirm = 1;
                }    
    }
    
    if (isset($_POST['recalc']) && ($_POST['recalc']== 1))
    {
        $qr = mysql_query("SELECT salePrice, accCount, ptPercent, pt.ptID, ShortTitle, p.sID, p.accID FROM pm_as_parts p 
                       LEFT JOIN pm_structure s ON (p.sID = s.sID) 
                       LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
                       LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                       WHERE c.sessionID='$sessionID'");

        $goodsCount1 = mysql_num_rows($qr);    
    
        for ($i = 0; $i < $goodsCount1; $i++) 
        {
          $r1 = mysql_fetch_array($qr); 
        
          if (isset($_POST["del_".$r1["accID"]]) || $_POST["kol_".$r1["accID"]] <= 0)
            mysql_query("DELETE FROM pm_as_cart WHERE sessionID='$sessionID' AND accID=".$r1["accID"]);
          else
            mysql_query("UPDATE pm_as_cart SET accCount=".$_POST["kol_".$r1["accID"]]." WHERE sessionID='$sessionID' AND accID=".$r1["accID"]);
        }
    }    
?>
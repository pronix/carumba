<?

include_once('mod.Catalogue.php');

class Good{
	
	var $itemData;

	function Good($itemData = array()){
		$this->itemData = $itemData;
	}

	function setItemData($var, $val){
		$this->itemData[$var] = $val;
	}

	function getItemData($var, $default = ''){
		return isset($this->itemData[$var]) ? $this->itemData[$var] : $default;
	}

}

    class Cart extends AbstractModule
    
    {
    	var $itemData = array();
    	var $goods = array();
    	var $userData = array();

    	function getItemData($key, $default = ''){
    		return isset($this->itemData[$key]) ? $this->itemData[$key] : $default;
    	}

    	function setItemData($key, $value){
    		$this->itemData[$key] = $value;
    	}

        function Cart()
        {
//            $this->desc = "Adds goods from Catalogue to cart";
            $this->publicFunctions = array("getContent", "getSubItemType", "getItemType", "getItemDesc", 
            "getSpecificDataForEditing", "getSpecificBlockDesc", "updateSpecificData",
            "cartBlock", "tocart");
            $this->cmdFunctions = array("recalc" => "recalcCart", "buycart" => "buyCart");
        }

        function getSpecificDataForEditing($args)
        {
            return array();
        }

        function updateSpecificData($args)
        {
            return true; 
            //can possibly check for existing cart and if present return false;
        }

        function getSpecificBlockDesc($args)
        {
            return "";
        }

        function getItemDesc($args)
        {
            $DataType = $args[0];
            
            switch ($DataType)
            {
                case "Cart":
                    return "";
            }
            
            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Cart":
                    return array("корзина заказа", "корзину заказа", "корзины заказа");
            }
            
            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Article":
                    return array("Cart" => "корзину заказа");
                case "Cart":
                    return array();
            }
            
            return array();
        }
        
        function cartBlock($args)
        {
            global $templatesMgr, $authenticationMgr, $structureMgr;

            if (!isset($args["TEMPLATE"]) || ($args["TEMPLATE"] == ""))
                trigger_error("Template for cartBlock must be specified", PM_FATAL);

            $blocks = $templatesMgr->getValidTags(
                $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Cart/" . $args["TEMPLATE"]),
                array("container", "orderlink", "goodsnumber", "price", "cardprice", "goods")
                );

            $sessionID = $authenticationMgr->getSessionID();
			$userData = $authenticationMgr->getUserData($authenticationMgr->getUserID(),'');
            $tpl = $blocks["container"];
            $orderlink = $blocks["orderlink"];
            $goodsnumber = $blocks["goodsnumber"];
			$goods = $blocks["goods"];

            /*
            $qr = mysql_query("SELECT ShortTitle, salePrice FROM pm_as_parts p 
                               LEFT JOIN pm_structure s ON (p.sID = s.sID) 
                               LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
                               WHERE c.sessionID='$sessionID'");

            if (!$qr)
                trigger_error("Error getting cart items - " . mysql_error(), PM_FATAL);

            $goodsCount = mysql_num_rows($qr);
            if ($goodsCount == 0)
            {
                $orderlink = "";
            }
            else
            {
                //goods list from $qr
            }
            */

            $qr = mysql_query("SELECT salePrice, accCount, ptPercent, pt.ptID, ShortTitle, p.sID FROM pm_as_parts p 
							   LEFT JOIN pm_structure s ON (p.sID = s.sID) 
                               LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
                               LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                               WHERE c.sessionID='$sessionID'");

            if (!$qr)
                trigger_error("Error getting cart items - " . mysql_error(), PM_FATAL);

            $goodsCount = mysql_num_rows($qr);
            $cardSum = 0;
            $curSum = 0;
            $sum = 0;
			
			$goods_row ="";

            $total = 0;

            if (mysql_num_rows($qr) > 0)
            {
                //goods list from $qr
				$i = 1;
                $isCardInCart = Cart::isItemInCart(GetCfg('Club.GoodID'));
				$goodsArray = array();
				while (false !== ($r = mysql_fetch_assoc($qr)))
                {
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
					//echo "$curSum in Block<br>";
                    $sum += $price ;
                    $total += $r['accCount'];
					
					 $goodsArray[] = $i.' | <a href="'.$structureMgr->getPathByPageID($r['sID'], false).'">'.$r['ShortTitle'].'</a>'; 
					$i++;
                }
				if(count($goodsArray))
					$goods_row = '<br />'.implode('<br /><br />', $goodsArray).'<br />';
            }

            $goodsnumber = str_replace("%number%", $total, $goodsnumber);
            $blocks["price"] = str_replace("%sum%", $curSum, $blocks["price"]);
            $blocks["cardprice"] = str_replace("%cardsum%", $cardSum, $blocks["cardprice"]);
			$blocks["goods"] = str_replace("%goods_row%", $goods_row, $blocks["goods"]);
            $tpl = str_replace("%price%", $blocks["price"], $tpl);
			$tpl = str_replace("%goods%", $blocks["goods"], $tpl);
            $tpl = str_replace("%cardprice%", $blocks["cardprice"], $tpl);
            $tpl = str_replace("%orderlink%", $orderlink, $tpl);
            $tpl = str_replace("%goodsnumber%", $goodsnumber, $tpl);

            return $tpl;
        }

        function getItems(){
            global $authenticationMgr;
            $sessionID = $authenticationMgr->getSessionID();
            $qr = mysql_query("SELECT * FROM pm_as_cart WHERE sessionID='" . $sessionID . "'");
            $items = array();
            while($row = mysql_fetch_assoc($qr)){
            	$items[$row['accID']] = $row;
            }
            return $items;
        }

        function isItemInCart($accID){
        	$items = Cart::getItems();
        	return isset($items[$accID]);
        }

        function tocart($args)
        {
            global $authenticationMgr;
            $accID = _post("goodID");
            if (!$accID)
                trigger_error("Empty goodID to put in cart", PM_WARNING);

            $return = getenv("HTTP_REFERER");

            $sessionID = $authenticationMgr->getSessionID();

            if ($sessionID == "")
                trigger_error("sessionID empty", PM_WARNING);

            $qr = mysql_query("SELECT cartID FROM pm_as_cart WHERE sessionID='" . $sessionID . "' AND accID=" . $accID);
            if (!$qr)
            {
                trigger_error("Error getting cartID from pm_as_cart" . mysql_error(), PM_WARNING);
            }
            else
            {
                if (mysql_num_rows($qr) == 0)
                {
                    $qr = mysql_query("INSERT INTO pm_as_cart (sessionID, accID, accCount, addDate) VALUES('$sessionID', $accID, 1, NOW())");
                    if (!$qr)
                        trigger_error("Error adding good into cart - " . mysql_error(), PM_WARNING);
                }
                else
                {
                    list($cartID) = mysql_fetch_row($qr);
                    $qr = mysql_query("UPDATE pm_as_cart SET accCount = accCount + 1, addDate=NOW() WHERE cartID=$cartID");
                    if (!$qr)
                        trigger_error("Error updating accCount in cart for cartID=$cartID - " . mysql_error(), PM_WARNING);
                }
            }

            header("Location: $return");
            exit(0);
        }

        function recalcCart()
        {
            global $structureMgr, $authenticationMgr;
            
            $sessionID = $authenticationMgr->getSessionID();
            
            $cnt = _postByPattern("/gc\d+/");
            $del = _postByPattern("/del\d+/");

            foreach ($cnt as $key => $gcount)
            {
                $gcount = safe_numeric($gcount);
                if (preg_match("/gc(\d+)/", $key, $match))
                {
                    if (isset($del["del" . $match[1]]) || $gcount <= 0)
                        mysql_query("DELETE FROM pm_as_cart WHERE sessionID='$sessionID' AND accID=$match[1]");
                    else
                        mysql_query("UPDATE pm_as_cart SET accCount=$gcount WHERE sessionID='$sessionID' AND accID=$match[1]");
					//echo "DELETE FROM pm_as_cart WHERE sessionID='$sessionID' AND accID=$match[1]";
                    if (mysql_error())
                        trigger_error(mysql_error(), PM_FATAL);
                }
            }

            header("Status: 302 Moved");
            header("Location: " . getenv("HTTP_REFERER"));
            exit(0);
        }

		function buyCart()
        {
            global $structureMgr, $authenticationMgr;
            
            $sessionID = $authenticationMgr->getSessionID();
            
			$userName = _post("name");
			$userPhone = _post("phone");
			$userEmail = _post("email");
			$userAdress = _post("adress");
			$userComment = _post("comment");
			
			$userData = $authenticationMgr->getUserData($authenticationMgr->getUserID(),'');
				
			$isCardInCart = Cart::isItemInCart(GetCfg('Club.GoodID'));
			$cardStID = 1;
			if($userData['cardID']) {
				$cardStID = 4;
			} elseif ($isCardInCart) {
				$cardStID = 2;
			} else {
				$cardStID = 1;
			}

			$msg = "";

			if($userData['userID'] == 1 && !$userName) {
				$msg = 1;
			}
			if($userData['userID'] == 1 && !$userPhone) {
				$msg = 2;
			}
			
			if(!$msg) {
				//echo 'Zer good';
				$query = "SELECT p.sID, p.accID, p.ptID, ShortTitle, salePrice, accCount, ptPercent, accPlantID, p.smallPicture, s.MetaDesc, s.pms_sID, pc.PicturePath FROM pm_as_parts p 
								   LEFT JOIN pm_structure s ON (p.sID = s.sID) 
								   LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
								   LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
								   LEFT JOIN pm_as_categories pc ON (s.pms_sID = pc.sID)
								   WHERE c.sessionID='".$sessionID."'";
				$result = mysql_query($query);
				if(mysql_num_rows($result)) {
					

					$user = ($userData['cardID']? $userData['cardID'] : $authenticationMgr->getUserID());
					$orderQuery = "INSERT INTO pm_order (userID, stDate, cardStID, stID, comment) VALUES ('".$userData['userID']."', '".date("Y-m-d H:i")."', '".$cardStID."', '1', '".$userComment."')";
					//echo $orderQuery.'<br>';
					mysql_query($orderQuery);

					$orderQuery = "SELECT orderID FROM pm_order WHERE userID = '".$authenticationMgr->getUserID()."' && stID = '1' ORDER BY orderID desc";
					//echo $orderQuery.'<br>';
					$orderResult = mysql_query($orderQuery);
					$row = mysql_fetch_assoc($orderResult);
					$orderID = $row['orderID'];
					
					$orderStatusQuery = "INSERT INTO pm_order_status_date (orderID, stID, stDate) VALUES ('".$orderID."', '1', '".date("Y-m-d H:i")."')";
					//echo $orderStatusQuery.'<br>';
					mysql_query($orderStatusQuery);
					
					if($authenticationMgr->getUserID() == 1) {
						$quickQuery = "INSERT INTO pm_order_quick (orderID, userName, userPhone, userEmail, userAdress) VALUES ('".$orderID."', '".$userName."', '".$userPhone."', '".$userEmail."', '".$userAdress."')";
						//echo $quickQuery.'<br>';
						mysql_query($quickQuery);
					}

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

					$this->sendNotifyEmail($orderID, $userData['userID'], $userEmail);
				}
				///////////////////////////////////////////
				header("Status: 302 Moved");
				header("Location: /carumba/cart?orderID=".$orderID);
				exit(0);
			} else {
				header("Status: 302 Moved");
				header("Location: /carumba/cart?msg=".$msg);
				exit(0);
			}
        }

        function getPriceClass($ptID){
        	$ret = 't_ordpr';
        	switch(true){
        		case $ptID == 0 : $ret = ''; break;
	       		case $ptID >= 2 && $ptID <= 4 : $ret = 't_bonus'; break;
        		case $ptID >= 5 && $ptID <= 7: $ret = 't_salepr';break;
        		case $ptID == 8 : $ret = '';break;
        	}
        	return $ret;
		}

		function sendNotifyEmail($orderID, $userID, $userEmail)
		{
			global $templatesMgr, $authenticationMgr;

			$tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/message.html");

			$subject = "Ваш заказ на carumba.ru";
			$body = "Ваш заказ успешно обработан и ему присвоен номер ".$orderID.".\nЗаказанные Вами товары: ";

			$mail = new PHPMailer();

			//$mail->IsSMTP(); // set mailer to use SMTP
			$mail->Host = "mail.lenera.ru;mail.lenera.ru";  // specify main and backup server
			$mail->SMTPAuth = true;     // turn on SMTP authentication
			$mail->Username = "rob-caru";  // SMTP username
			$mail->Password = "Vifi2Ht6b"; // SMTP password

			$mail->From = "robot@carumba.ru";
			$mail->FromName = "Carumba.ru";
			
			$mail->WordWrap = 50;                                 // set word wrap to 50 characters
			$mail->IsHTML(true);                                  // set email format to HTML
			
			$mail->Subject = $subject;
			
			$bonusBody = $this->getOrderList($orderID);
		
			$tpl = str_replace("%text%", $body, $tpl);
			$tpl = str_replace("%bonus%", $bonusBody, $tpl);
			$tpl = str_replace("%sale%", "", $tpl);
			$mail->Body = $tpl;
			//echo $tpl;
			$userData = $authenticationMgr->getUserData($authenticationMgr->getUserID(),'');
			if($userData['userID'] != 1) {
				$email['test'] = 'test';
				$email['email'] = $userData['Email'];
				$email['name'] = 'Уважаемый, '.$userData['FirstName'].' '.$userData['LastName'];
			} elseif($userData['userID'] == 1 && $userEmail) {
				$email['email'] = $userEmail;
				$email['test'] = 'test';
				$email['name'] = 'Уважаемый пользователь';
			}
			//print_r($email);
			
			if(count(trim($email['email']))) {
				$mail->AddAddress($email['email'], $email['name']);
				if(!@$mail->Send())
				{
				   trigger_error("Message could not be sent.Mailer Error: " . $mail->ErrorInfo, PM_WARNING);
				}
				$mail->ClearAddresses();
			}

		}

		function getOrderList($orderID)
		{
			global $structureMgr;

			$items = $this->getOrderItems($orderID);

			$content = "";
			foreach($items as $item) {
				$URL = $structureMgr->getPathByPageID($item['sID'], false);
				$URL = "http://carumba.ru".$URL;
				if ($item["ptPercent"] == 0)
					$firstPrice = round($item["salePrice"] - ($item["salePrice"] * 5 / 100));
				else
					$firstPrice = round($item["salePrice"] - ($item["salePrice"] * $item["ptPercent"] / 100));
				
				if ($catItem["smallPicture"] == NULL)
				{
					if (file_exists(GetCfg("ROOT") . $item["PicturePath"] . "/" . $item["sID"] . ".gif"))
						$item["smallPicture"] = $item["PicturePath"] . "/" . $item["sID"] . ".gif";
					else
					if ($item["logotype"] == NULL)
						$item["smallPicture"] = "/products/empty.gif";
					else
						$item["smallPicture"] = $item["logotype"];
				}
				
				if($item["ptPercent"]) {
					$content .= "<p><a href='".$URL."'><img src='http://carumba.ru".$item["smallPicture"]."' width=70 height=70 border=0 /></a><br /><a  href=\"".$URL."\">".$item["ShortTitle"]."</a><br><strong>". $firstPrice."</strong> / ".$item["salePrice"]." руб. (- <strong>".$item["ptPercent"]."</strong>%)</p>";
				} else {
					$content .= "<p><a href='".$URL."'><img src='http://carumba.ru".$item["smallPicture"]."' width=70 height=70 border=0 /></a><br /><a  href=\"".$URL."\">".$item["ShortTitle"]."</a><br><strong>". $firstPrice."</strong> руб. </p>";
				}
			}
			$content .= "<p>&nbsp;</p>";
			
			return $content;
		}
		
		function getOrderItems($orderID)
		{
			$query = "SELECT 
					pm_as_parts.accID, pm_as_parts.sID, ShortTitle, deliveryCode, accPlantName, logotype, smallPicture, pm_as_parts.tplID, salePrice, 
					MustUseCompatibility, PicturePath, DescriptionTemplate, ptPercent, pm_as_parts.accPlantID  
					FROM pm_order_parts
					LEFT JOIN pm_as_parts ON pm_order_parts.accID = pm_as_parts.accID
					LEFT JOIN pm_as_producer ON pm_as_parts.accPlantID = pm_as_producer.accPlantID
					LEFT JOIN pm_structure ON pm_as_parts.sID = pm_structure.sID
					LEFT JOIN pm_as_categories ON pm_structure.pms_sID = pm_as_categories.sID
					LEFT JOIN pm_as_pricetypes ON pm_as_pricetypes.ptID = pm_as_parts.ptID
					WHERE pm_order_parts.orderID = '".$orderID."'
				";
			echo $query;
			$result = mysql_query($query);
			if (!$result)
				trigger_error("Invaid query. " . mysql_error(), PM_FATAL);

			if (mysql_num_rows($result) == 0)
				trigger_error("Empty result for $query", PM_WARNING);

			$query = "SELECT FOUND_ROWS() as itemsCount";
			$res = mysql_query($query);
			$row = mysql_fetch_assoc($res);


			$catItems = array();

			while($item = mysql_fetch_assoc($result)) {
				if ($item["MustUseCompatibility"])
				{
					$item["Compatibility"] = "";
					$query2 = "SELECT atc.carID, carModel, carName FROM pm_as_acc_to_cars atc LEFT JOIN pm_as_cars c ON (c.carID = atc.carID) 
					WHERE accID=" . $item["accID"];
					$result2 = mysql_query($query2);

					if (!$result2)
						trigger_error("Error retrieving car model links [$query2] - " . mysql_error(), PM_FATAL);
					
					while (false !== (list($carID, $carModel, $carName) = mysql_fetch_row($result2)))
					{
						if ($item["Compatibility"])
							$item["Compatibility"] .= ", ";

						$item["Compatibility"] .= "$carModel";
						if ($carName)
							$item["Compatibility"] .= " $carName";
					}
				}
				$catItems[] = $item;
			}

			return $catItems;
		}

		function getUserData($userID){
			$email = Array();

			$query = "SELECT Email, FirstName, LastName, SurName FROM pm_users WHERE length(Email)<>0 && cardID=0 && subscribe = 1 && userID='".$userID."' ";
			$result = mysql_query($query);
			if(mysql_num_rows($result)) {
				$row = mysql_fetch_assoc($result);
				$email['name'] = $row['FirstName'].' '.$row['LastName'];
				$email['email'] = $row['Email'];
			}
			//echo $query.'<br>';

			return $email;
		}


        function getContent($args)
        {
            global $structureMgr, $templatesMgr, $authenticationMgr;

			$userId = $authenticationMgr->getUserID();
			$userData = $authenticationMgr->getUserData($userId,'');
			$userName = $userData['FirstName']." ".$userData['LastName'];

			$this->setItemData('userData',$userData);
			$this->setItemData('userName',$userName);

            $blocks = $templatesMgr->getValidTags(
                $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Cart/cart.xml"),
                array("container", "goods", "cart", "emptycart", "order", "quickorder", "login", "comment")
                );

            $sessionID = $authenticationMgr->getSessionID();

            $tpl = $blocks["container"];
            $goodsline = $blocks["goods"];

            $qr = mysql_query("SELECT p.sID, p.accID, p.ptID, ShortTitle, salePrice, accCount, ptPercent, p.accPlantID, logotype, p.smallPicture, s.MetaDesc, s.pms_sID, pc.PicturePath FROM pm_as_parts p 
                               LEFT JOIN pm_structure s ON (p.sID = s.sID) 
							   LEFT JOIN pm_as_producer ap ON (ap.accPlantID = p.accPlantID)
                               LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
                               LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                               LEFT JOIN pm_as_categories pc ON (s.pms_sID = pc.sID)
                               WHERE c.sessionID='$sessionID'");
            if (!$qr)
                trigger_error("Error getting cart items - \n". mysql_error(), PM_FATAL);
			
            $goodsCount = mysql_num_rows($qr);
            $goods = "";
            $cardSum = 0;
            $curSum = 0;
            $sum = 0;

            $total = 0;

            if (mysql_num_rows($qr) > 0)
            {

            	$cat = new Catalogue();
                //goods list from $qr
                $goodsArray = array();
                while (false !== ($r = mysql_fetch_assoc($qr)))
                {
					/*
						if ($catItem["ptPercent"] == 0)
							$firstPrice = "<strong>" . round($catItem["salePrice"] - ($catItem["salePrice"] * 5 / 100)) . "</strong>";
						else
							$firstPrice = "<strong><font class=\"".$typeClass."\">" . 
										  round($catItem["salePrice"] - ($catItem["salePrice"] * $catItem["ptPercent"] / 100)) . 
										  "</font></strong>";
					*/
                    $gl = $goodsline;
                    $URL = $structureMgr->getPathByPageID($r['sID'], false);
                    $isCardInCart = Cart::isItemInCart(GetCfg('Club.GoodID'));
					$isClubMember = (isset($userData['cardID']) && $userData['cardID']!='0') || $isCardInCart;

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

                    $price = $r['salePrice']*$r["accCount"]; //must calc as needed
                                       
					

					$priceCells = '<td class="gray"><p> <a href="%good_link%" class="bluem" target="_blank">%good_name%</a></p></td>
                            <td><strong>%card_price%</strong></td>
                            <td>%cur_price%</td>';
					if($isClubMember){
						$priceCells = '<td>%cur_price%</td>';
					}

					
                    $gl = str_replace("%good_name%", $r['ShortTitle'], $gl);
                    $gl = str_replace("%good_link%", $URL, $gl);
                    $gl = str_replace("%card_price%", $cardPrice . " * " . $r["accCount"], $gl);
                    $gl = str_replace("%cur_price%", $curPrice . " * " . $r["accCount"], $gl);
                    $gl = str_replace("%price%", $price . " * " . $r["accCount"], $gl);
                    $gl = str_replace("%good_count%", $r['accCount'], $gl);
                    $gl = str_replace("%goodID%", $r['accID'], $gl);
                    
                    $cardSum += $cardPrice ;
                    $curSum += $curPrice ;
                    $sum += $price;
                    $total += $r['accCount'];

					//echo "$curSum in Cart<br>";

					if (file_exists(GetCfg("ROOT") . $r["PicturePath"] . "/" . $r["sID"] . ".gif"))
						$r["smallPicture"] = $r["PicturePath"] . "/" . $r["sID"] . ".gif";
					else
					if ($r["logotype"] == NULL)
						$r["smallPicture"] = "/products/empty.gif";
					else
						$r["smallPicture"] = $r["logotype"];

                    $good = new Good(array('goodID'=>$r['sID'],'good_name'=>$r['ShortTitle'],
                    'PicturePath'=>$r['PicturePath'],
					'good_link'=>$URL,'card_price'=>$cardPrice,'smallPicture'=>$r["smallPicture"],
					'cur_price'=>$curPrice, 'price'=>$price, 'good_count'=>$r['accCount']));

					foreach($r as $var=>$val){
						$good->setItemData($var,$val);
					}

					$good->setItemData('priceClass', $this->getPriceClass($r['ptID']));
					$good->setItemData('ptID', $r['ptID']);
					$good->setItemData('ptPercent', $r['ptPercent']);

					$good->setItemData('props',$props = $cat->getCatItemProperties($r['sID'], "CatItem", $structureMgr->getParentPageID($r['sID'])));

                    $this->goods[$r['sID']] = $good;

       				$imgRes  = mysql_query("SELECT accPlantName, logotype FROM pm_as_producer WHERE accPlantID = '{$r["accPlantID"]}'");
       				if(mysql_num_rows($imgRes)>0){
       					$imgRow = mysql_fetch_assoc($imgRes);
       					$this->goods[$r['sID']]->setItemData('accPlantName',$imgRow['accPlantName']);
       					$this->goods[$r['sID']]->setItemData('logotype',$imgRow['logotype']);
       				}



                    $goods .= $gl;
                }
                $tcart = $blocks["cart"];
            }
            else
            {
                $tcart = $blocks["emptycart"];
            }


            if ($total > 0)
            {
                $order = $blocks["order"];
                $qorder = $blocks["quickorder"];

                if ($authenticationMgr->getUserID() == 1)
                {
                    $blocks["login"] = str_replace("%currentpath%", getenv("REQUEST_URI"), $blocks["login"]);
                    $order = str_replace("%right%", $blocks["login"], $order);
                    $order = str_replace("%left%", $qorder, $order);
                }
                else
                {
                    $order = str_replace("%right%", $blocks["comment"], $order);
                    $order = str_replace("%left%", $qorder, $order);
					//echo $order;
                }
            }
            else
            {
                $order = "";
            }

			$ret = '';

			$isCardInCart = Cart::isItemInCart(GetCfg('Club.GoodID'));
			$isClubMember = (isset($userData['cardID']) && $userData['cardID']!='0') || $isCardInCart;
			$this->setItemData('isCardInCart', $isCardInCart);
			$this->setItemData('isClubMember', $isClubMember);
			$this->setItemData('userID', $userId);
			$this->userData = $userData;

				$carName = 'марка автомобиля не определена';
				if($userId>1 && $isClubMember && $userData['carID']!=0){
					$carRow = mysql_fetch_assoc(mysql_query("SELECT * FROM pm_as_cars WHERE carID='{$userData['carID']}'"));
					$carName = "ВАЗ ".$carRow['carModel'].(isset($carRow['carName']) && $carRow['carName']!='' ? "({$carRow['carName']})" : '');
				}

				$this->setItemData('carName',$carName);

			if($userId==1){
				$ret.='<p><strong>Внимание!:</strong></p>
						<p>Для вашего удобства мы предлагаем вам:</p>
						<p><a href="/registration" class="levm"><img src="/images/arr_gray2.gif" width="7" height="9" border="0" align="absmiddle"/>Зарегестрироваться</a><br/>
						<a href="/main/club" class="levm"><img src="/images/arr_gray2.gif" width="7" height="9" border="0" align="absmiddle"/>Стать членом клуба</a></p>';
			}elseif($userId>1 && !$isClubMember){
				$ret.='<p><strong>Внимание:</strong></p>
						<p>Для вашего удобства мы предлагаем вам:</p>
						<a href="/main/club" class="levm"><img src="/images/arr_gray2.gif" width="7" height="9" border="0" align="absmiddle"/>Стать членом клуба</a></p>';
			}elseif($userId>1 && $isClubMember){

				$carName = 'марка автомобиля не определена';
				if($userData['carID']!=0){
					$carRow = mysql_fetch_assoc(mysql_query("SELECT * FROM pm_as_cars WHERE carID='{$userData['carID']}'"));
					$carName = "ВАЗ ".$carRow['carModel'].(isset($carRow['carName']) && $carRow['carName']!='' ? "({$carRow['carName']})" : '');
				}
				$ret.='<p><strong>Здравствуйте!</strong></p>
						<p>'.$userName.'</p>
						<p>№ Вашей карты: '.($userData['cardID']=='0'?'Будет присвоен после оплаты.':$userData['cardID']).'</p>
						<p>'.$carName.'</p>
						<p>
							<a href="#" class="levm"><img src="/images/arr_gray2.gif" width="7" height="9" border="0" align="absmiddle"/>Изменить анкету </a> 
						<a href="#" class="levm"><img src="/images/arr_gray2.gif" width="7" height="9" border="0" align="absmiddle"/>Выход (log out)</a></p>
						';
			}

			$club = $ret;


            $this->setItemData('cart',$tcart);
            $this->setItemData('cardsum',$cardSum);
            $this->setItemData('cursum',$curSum);
            $this->setItemData('sum',$sum);
            $this->setItemData('total', $total);
            $this->setItemData('goods', $goods);
            $this->setItemData('order', $order);
            $this->setItemData('club', $club);

            $dost = new Template('blockdost',$this);
            $this->setItemData('blockdost', $dost->getContent());

            if($userId==1){
            	$header = new Template('header','Быстрый заказ');
            	$of = new Template('orderQuick');
            	$orderForm = $header->getContent().$of->getContent();
            }else{
            	$header = new Template('header','Контактная информация');
            	$of = new Template('orderUser', $this->getItemData('userData'));
            	$orderForm = $header->getContent().$of->getContent();
            }

            $this->setItemData('orderForm',$orderForm);

            $cartTpl = new Template('cart',$this);
            $tpl = $cartTpl->getContent();
			$msg = _get("msg");
			if($msg == 1) {
				$msg = '<div class="podbor">Заполните пожалуйства поле &quot;Ваше имя&quot;</div>';
			} elseif ($msg == 2) {
				$msg = '<div class="podbor">Заполните пожалуйства поле &quot;Ваше имя&quot;</div>';
			}
            return $msg.$tpl;
        }
    }
?>

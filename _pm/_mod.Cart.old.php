<?
    class Cart extends AbstractModule
    {
        function Cart()
        {
//            $this->desc = "Adds goods from Catalogue to cart";
            $this->publicFunctions = array("getContent", "getSubItemType", "getItemType", "getItemDesc", 
            "getSpecificDataForEditing", "getSpecificBlockDesc", "updateSpecificData",
            "cartBlock", "tocart");
            $this->cmdFunctions = array("recalc" => "recalcCart");
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
            global $templatesMgr, $authenticationMgr;

            if (!isset($args["TEMPLATE"]) || ($args["TEMPLATE"] == ""))
                trigger_error("Template for cartBlock must be specified", PM_FATAL);

            $blocks = $templatesMgr->getValidTags(
                $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Cart/" . $args["TEMPLATE"]),
                array("container", "orderlink", "goodsnumber", "price", "cardprice")
                );

            $sessionID = $authenticationMgr->getSessionID();

            $tpl = $blocks["container"];
            $orderlink = $blocks["orderlink"];
            $goodsnumber = $blocks["goodsnumber"];

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

            $qr = mysql_query("SELECT salePrice, accCount, ptPercent FROM pm_as_parts p 
                               LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
                               LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                               WHERE c.sessionID='$sessionID'");

            if (!$qr)
                trigger_error("Error getting cart items - " . mysql_error(), PM_FATAL);

            $goodsCount = mysql_num_rows($qr);
            $cardSum = 0;
            $curSum = 0;
            $sum = 0;

            $total = 0;

            if (mysql_num_rows($qr) > 0)
            {
                //goods list from $qr
                while (false !== ($r = mysql_fetch_assoc($qr)))
                {
                    $cardPrice = round($r['salePrice'] - $r['salePrice'] * 5 / 100) ;
                    if ($r['ptPercent'] > 0)
                        $curPrice = round($r['salePrice'] - $r['salePrice'] * $r["prPercent"] / 100);
                    else
                        $curPrice = $r['salePrice'];

                    $price = $r['salePrice']; //must calc as needed
                    
                    $cardSum += $cardPrice * $r["accCount"];
                    $curSum += $curPrice * $r["accCount"];
                    $sum += $price * $r["accCount"];
                    $total += $r['accCount'];
                }
            }




            $goodsnumber = str_replace("%number%", $total, $goodsnumber);
            $blocks["price"] = str_replace("%sum%", $curSum, $blocks["price"]);
            $blocks["cardprice"] = str_replace("%cardsum%", $cardSum, $blocks["cardprice"]);
            
            $tpl = str_replace("%price%", $blocks["price"], $tpl);
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
                    if (mysql_error())
                        trigger_error(mysql_error(), PM_FATAL);
                }
            }

            header("Status: 302 Moved");
            header("Location: " . getenv("HTTP_REFERER"));
            exit(0);
        }

        function getContent($args)
        {
            global $structureMgr, $templatesMgr, $authenticationMgr;

			$userId = $authenticationMgr->getUserID();
			$userData = $authenticationMgr->getUserData($userId,'');
			$userName = $userData['FirstName']." ".$userData['LastName'];

            $blocks = $templatesMgr->getValidTags(
                $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Cart/cart.xml"),
                array("container", "goods", "cart", "emptycart", "order", "quickorder", "login", "comment")
                );

            $sessionID = $authenticationMgr->getSessionID();

            $tpl = $blocks["container"];
            $goodsline = $blocks["goods"];

            $qr = mysql_query("SELECT p.sID, p.accID, ShortTitle, salePrice, accCount, ptPercent FROM pm_as_parts p 
                               LEFT JOIN pm_structure s ON (p.sID = s.sID) 
                               LEFT JOIN pm_as_cart c ON (c.accID = p.accID)
                               LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                               WHERE c.sessionID='$sessionID'");

            if (!$qr)
                trigger_error("Error getting cart items - " . mysql_error(), PM_FATAL);

            $goodsCount = mysql_num_rows($qr);
            $goods = "";
            $cardSum = 0;
            $curSum = 0;
            $sum = 0;

            $total = 0;

            if (mysql_num_rows($qr) > 0)
            {
                //goods list from $qr
                while (false !== ($r = mysql_fetch_assoc($qr)))
                {
                    $gl = $goodsline;
                    $URL = $structureMgr->getPathByPageID($r['sID'], false);
                    
                    $cardPrice = round($r['salePrice'] - $r['salePrice'] * 5 / 100) ;
                    if ($r['ptPercent'] > 0)
                        $curPrice = round($r['salePrice'] - $r['salePrice'] * $r["prPercent"] / 100);
                    else
                        $curPrice = $r['salePrice'];

                    $price = $r['salePrice']; //must calc as needed
                    if($userId > 1  && ($userData['cardID']!='0' || $this->isItemInCart(GetCfg('Club.GoodID')))){
//                    	$price = $cardPrice;
                    	$curPrice = $cardPrice;
                    }
                    

                    $gl = str_replace("%good_name%", $r['ShortTitle'], $gl);
                    $gl = str_replace("%good_link%", $URL, $gl);
                    $gl = str_replace("%card_price%", $cardPrice . " * " . $r["accCount"], $gl);
                    $gl = str_replace("%cur_price%", $curPrice . " * " . $r["accCount"], $gl);
                    $gl = str_replace("%price%", $price . " * " . $r["accCount"], $gl);
                    $gl = str_replace("%good_count%", $r['accCount'], $gl);
                    $gl = str_replace("%goodID%", $r['accID'], $gl);
                    
                    $cardSum += $cardPrice * $r["accCount"];
                    $curSum += $curPrice * $r["accCount"];
                    $sum += $price * $r["accCount"];
                    $total += $r['accCount'];

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
                }
            }
            else
            {
                $order = "";
            }

			$ret = '';

			$isCardInCart = Cart::isItemInCart(GetCfg('Club.GoodID'));
			$isClubMember = (isset($userData['cardID']) && $userData['cardID']!='0') || $isCardInCart;


			if($userId==1){
				$ret.='<p><strong>Внимание:</strong></p>
						<p>Для вашего удобства предлагаем Вам:</p>
						<p><a href="/registration" class="levm"><img src="/images/arr_gray2.gif" width="7" height="9" border="0" align="absmiddle"/>Зарегестрироваться</a><br/>
						<a href="/main/club" class="levm"><img src="/images/arr_gray2.gif" width="7" height="9" border="0" align="absmiddle"/>Стать членом клуба</a></p>';
			}elseif($userId>1 && !$isClubMember){
				$ret.='<p><strong>Внимание:</strong></p>
						<p>Для вашего удобства предлагаем Вам:</p>
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



#			$tpl .=	"<h1>SD ADDON</h1>";
            $tpl = str_replace("%cart%", $tcart, $tpl);
            $tpl = str_replace("%cardsum%", $cardSum, $tpl);
            $tpl = str_replace("%cursum%", $curSum, $tpl);
            $tpl = str_replace("%sum%", $sum, $tpl);
            $tpl = str_replace("%total%", $total, $tpl);
            $tpl = str_replace("%goods%", $goods, $tpl);
            $tpl = str_replace("%order%", $order, $tpl);
			$tpl = str_replace("%club%", $club, $tpl);

            return $tpl;
        }
    }
?>


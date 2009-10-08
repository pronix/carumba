<?

class OrdersHandler{
	
	var $countOrders = 0;
	
	function getContent($status = 1, $page = 1)
	{
		global $templatesMgr, $startFrom;
		
		$pager = '';
		$content = '';

		$tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/orders.html');
		
		$tpl = str_replace('%ordertype%', 'orders', $tpl);

		$perPage = 20; // Количство записей на странице вывода
		$start = ($page - 1) * $perPage;
		// была отредактирована ледующая строка, а также переменная startFrom добавлена в глобальные
		// Вообще говоря, не понятно, откуда эта $startFrom берется, но в ней хранится количество записей 
		// для текущего вида заказа
		// Тем более не понятно, для чего нужна $endAt
        //$endAt = $startFrom['countOrders'] + $perPage - 1;
		$orders = $this->getOrders($status, $start, $perPage);
		//print_r($orders);
		
		//$cnt = $this->itemsCount;
		$cnt = $this->countOrders;
		//if ($endAt >= $cnt)
		//	$endAt = $cnt - 1;

		$pagesCount = ceil($cnt / $perPage);
		//print 'pagesCount='.$pagesCount;
		
		$URL = '/carorders?cmd=orders&status='.$status;
		if ($pagesCount < $pNum)
		{
			trigger_error("Invalid pageNumber [$pNum of $pagesCount] - possibly hacking or serious change in DB", PM_ERROR);
		}
		else
		{
			
			$pagerTpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/orders_pager.html');
			$purePager = '';

			for ($i=1; $i <= $pagesCount; $i++)
			{
				if ($i > 1)
				{
					$purePager .= ' - ';
					$u = $URL . '&page=' . $i;
				}
				else
				   $u = $URL;

				if ($filter)
					$u .= '?' . $filter;

				if ($i == $pNum)
				{
					$purePager .= $i;
				}
				else
				{
					$purePager .= "<a href=\"$u\" class=\"levm\">" . $i . '</a>';
				}
			}
			if($purePager) {
				$pager = str_replace('%pages%', 'Страницы: '.$purePager, $pagerTpl);
			} else {
				$pager = str_replace('%pages%', '&nbsp;', $pagerTpl);
			}
			

		}
		//print_r($orders);
		$i=0;
		foreach($orders as $order) {
			$style = ($i % 2? Array('bgcolor'=>'#FFFFFF') : Array('bgcolor'=>'#F6F6F6'));
			$content .= $this->getOrderRow($order);
			if($i>0 && $i % 4 == 0 && count($orders)-1!=$i){
				$content.= '</table>
<br>
	<table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#C6C6C6" class="tbl">
  <!-- legend begin -->
  <tr bgcolor="#DBDBDB"> 
    <td><strong>№ заказа</strong></td>
    <td><strong> № п-ля</strong></td>
    <td><strong>Инф. п-ля</strong></td>
    <td bgcolor="#DBDBDB"><strong>Перечень заказа</strong></td>
	<td><strong>Сумма покупки</strong></td>
    <td><strong>Дата статуса</strong></td>
    <td><strong>Дата поставки</strong></td>
    <td><strong>Предоплата</strong></td>
    <td><strong>Ответ на заказ</strong></td>
    <td><strong>Статус карты</strong></td>
    <td><strong>Статус заказа</strong></td>
  </tr>
   <!-- legend end -->
';
			}
			$i++;
		}
		$tpl = str_replace('%content%', $content, $tpl);
		$tpl = str_replace('%pager%', $pager, $tpl);
		$tpl = str_replace('%stID%', $status, $tpl);
		$statuses = $this->getOrderStatuses();
		//print_r($statuses);
		foreach($statuses as $orderStatus) {
			$str = '<td width="14%">'.($orderStatus['stID'] == $status ? '<strong>'.$orderStatus['name'].'</strong>' :'<a href="/carorders?cmd=orders&status='.$orderStatus['stID'].'">'.$orderStatus['name'].'</a>').'<br>('.$orderStatus['num'].')</td>';
			//echo $orderStatus['stID'].'<br>';
			$tpl = str_replace('%orderCount'.$orderStatus['stID'].'%', $str, $tpl);
		}

		return $tpl;

	}
	
	function generateCardID()
	{
		$query = 'SELECT card.cardID FROM `pm_users_cards` card LEFT JOIN pm_users us ON card.cardID = us.cardID WHERE ISNULL(us.userID) ORDER BY cardID LIMIT 1';
		//echo $query."<hr>";
		$result = mysql_query($query);
		$row = mysql_fetch_assoc($result);
		$cardID = $row['cardID'];

		return $cardID;
	}

	function updateOrders()
	{
        //print '<br>updateOrders';
		global $authenticationMgr;

		$cardStIDs = _postByPattern('/cardStID\d+/');
		$stIDs = _postByPattern('/stID\d+/');
		$status = _post('status');
		
		if(count($cardStIDs))
		{
			
			foreach($cardStIDs as $key=>$value) {
				$orderID = str_replace('cardStID','',$key);
				
				$order = $this->getOrder($orderID);
				$userData = $authenticationMgr->getUserData($order['userID'],'');
				if($order['cardStID'] != $value) {
					$query = "UPDATE pm_order SET cardStID='".$value."' WHERE userID = '".$userData['userID']."'";
					mysql_query($query);

					if($value == 3 && !$userData['cardID']) {
						$cardID = $this->generateCardID();

						$query = "UPDATE pm_users SET cardID='".$cardID."' WHERE userID = '".$userData['userID']."'";
						mysql_query($query);
						//echo $query;
					}elseif($value == 1) {
						$query = "UPDATE pm_users SET cardID='0' WHERE userID = '".$userData['userID']."'";
						mysql_query($query);
					}
				}
			}
		}

		if(count($stIDs))
		{
			foreach($stIDs as $key=>$value) {
				$orderID = str_replace('stID','',$key);
				$dateNow = date('Y-m-d H:i');
				$order = $this->getOrder($orderID);

				if($value != $order['stID']) {
					$query = "UPDATE pm_order SET stID='".$value."', stDate = '".$dateNow."' WHERE orderID = '".$orderID."'";
					mysql_query($query);

					$orderStatusQuery = "INSERT INTO pm_order_status_date (orderID, stID, stDate) VALUES ('".$orderID."', '".$value."', '".$dateNow."')";
					mysql_query($orderStatusQuery);
				}
			}
		} 

		header('location: /carorders?cmd=orders&status='.$status);
	}
	
	function sendReply()
	{
		global $authenticationMgr;

		$orderID = _post('orderID');
		$reply = _post('reply');
		
		$order = $this->getOrder($orderID);
		$userData = $authenticationMgr->getUserData($order['userID'],'');

		$mail = new PHPMailer();

		//$mail->IsSMTP(); // set mailer to use SMTP
		//$mail->Host = 'mail.lenera.ru;mail.lenera.ru';  // specify main and backup server
		//$mail->SMTPAuth = true;     // turn on SMTP authentication
		//$mail->Username = 'rob-caru';  // SMTP username
		//$mail->Password = 'Vifi2Ht6b'; // SMTP password

		$mail->From = 'robot@carumba.ru';
		$mail->FromName = 'Carumba.ru';

		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		$mail->IsHTML(true);                                  // set email format to HTML
		$mail->Subject = 'Ответ на заказ carumba.ru';
		$mail->Body = $reply;
		$mail->AddAddress($userData['Email'], $userData['LastName'].' '.$userData['FirstName']);
		if(!$mail->Send())
		{
		   trigger_error('Message could not be sent.Mailer Error: ' . $mail->ErrorInfo, PM_FATAL);
		} else {
			$query = "INSERT INTO pm_order_message (orderID, message, dateOfMes) VALUES ('".$orderID."', '".$reply."', '".date("Y-m-d H:i:s")."')";
			mysql_query($query);
		}
		header('location: /carorders?cmd=orders&act=viewReplies&orderID='.$orderID);
	}

	function viewReplies()
	{
		global $authenticationMgr, $templatesMgr;

		$orderID = _get('orderID');

		$order = $this->getOrder($orderID);
		$userData = $authenticationMgr->getUserData($order['userID'],'');

		
		$tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/_answ_read.html');
		$tpl = str_replace('%ordertype%', 'orders', $tpl);
		$content = '';

		$query = "SELECT  message, dateOfMes FROM pm_order_message WHERE orderID = '".$orderID."'";
		$result = mysql_query($query);
		if(mysql_num_rows($result)) {
			$i = 0;
			while($reply = mysql_fetch_assoc($result)) {
				$content .= '<tr bgcolor="'.($i % 2 ? "#FFFFFF" : "#F6F6F6").'"> 
					<td><p>'.$reply['dateOfMes'].'</p></td>
					<td>'.$reply['message'].'</td>
				  </tr>';
				$i++;
			}
		}
		$tpl = str_replace('%content%', $content, $tpl);
		$tpl = str_replace('%orderID%', $orderID, $tpl);
		$tpl = ($userData['cardID'] ? str_replace('%userID%', '<strong><font color="#009966">'.$userData['cardID'].'</font></strong>', $tpl) : str_replace("%userID%", $userData['userID'], $tpl));
		
		return $tpl;
	}

	function setDeliveryDate()
	{
		$orderID = _post('orderID');
		$day = _post('day');
		$month = _post('month');
		$year = _post('year');
		
		$query = "UPDATE pm_order SET deliveryDate='".date("Y-m-d", mktime(0, 0, 0, $month, $day, $year))."' WHERE orderID = '".$orderID."'";
		mysql_query($query);

		header('location: /carorders?cmd=orders&act=delivery&orderID='.$orderID);
	}

	function setPrePayment()
	{
		$orderID = _post('orderID');
		$prePayment = _post('prePayment');
		
		$query = "UPDATE pm_order SET prePayment='".$prePayment."' WHERE orderID = '".$orderID."'";
		mysql_query($query);
		
		//echo $query;

		header('location: /carorders?cmd=orders&act=prepayment&orderID='.$orderID);
	}

	function setComment()
	{
		$orderID = _post('orderID');
		$comment = _post('message');
		
		$query = "UPDATE pm_order SET comment='".$comment."' WHERE orderID = '".$orderID."'";
		mysql_query($query);
		
		//echo $query;

		header('location: /carorders?cmd=orders&act=order&orderID='.$orderID);
	}

	function recalcOrders()
	{
		global $authenticationMgr;

		$orderID = _post('orderID');
		
		$order = $this->getOrder($orderID);
		$userData = $authenticationMgr->getUserData($order['userID'],'');

		$cnt = _postByPattern('/gc\d+/');
        $del = _postByPattern('/del\d+/');


		foreach ($cnt as $key => $gcount)
		{
			$gcount = safe_numeric($gcount);

			if (preg_match('/gc(\d+)/', $key, $match))
			{
				$query = "SELECT pm_as_parts.ptID, salePrice, ptPercent FROM pm_as_parts LEFT JOIN pm_as_pricetypes ON (pm_as_parts.ptID = pm_as_pricetypes.ptID) WHERE accID = '".$match[1]."'";
				//echo $query.'<br>';
				$result = mysql_query($query);
				$cartRow = mysql_fetch_assoc($result);
				//print_r($cartRow);
				switch($cartRow['ptID']){
					case 1:
						if($userData['cardID']){
							$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * 5 / 100));
						}else {
							$curPrice = $cartRow['salePrice'];
						}
						$cardPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * 5 / 100));
						break;
					case 2:
						if($userData['cardID']){
							$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
						}else {
							$curPrice = $cartRow['salePrice'];
						}
						break;
					case 3:
						if($userData['cardID']){
							$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
						}else {
							$curPrice = $cartRow['salePrice'];
						}
						break;
					case 4:
						if($userData['cardID']){
							$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
						}else {
							$curPrice = $cartRow['salePrice'];
						}
						break;
					case 5:
						$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
						break;
					case 6:
						$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
						break;
					case 7:
						$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
						break;
					default:
						$curPrice = $cartRow['salePrice'];
						break;
				}

				$curPrice *= $gcount;

				if (isset($del['del' . $match[1]]) || $gcount <= 0)
					mysql_query("DELETE FROM pm_order_parts WHERE orderID='$orderID' AND accID=$match[1]");
				else
					mysql_query("UPDATE pm_order_parts SET accCount='".$gcount."', price='".$curPrice."' WHERE orderID='".$orderID."' && accID=$match[1]");
				if (mysql_error())
					trigger_error(mysql_error(), PM_FATAL);
			}
		}

		header('location: /carorders?cmd=orders&act=order&orderID='.$orderID);
	}

	function addPart()
	{
		global $authenticationMgr;

		$orderID = _post('orderID');
		$accID =  _post('accID');
		$accCount =  _post('accCount');
		$order = $this->getOrder($orderID);
		$userData = $authenticationMgr->getUserData($order['userID'],'');

		$query = "SELECT pm_as_parts.accID, pm_as_parts.ptID, salePrice, ptPercent FROM pm_as_parts LEFT JOIN pm_as_pricetypes ON (pm_as_parts.ptID = pm_as_pricetypes.ptID) WHERE pm_as_parts.sID = '".$accID."'";
		//echo $query.'<br>';
		$result = mysql_query($query);
		$cartRow = mysql_fetch_assoc($result);
		//print_r($cartRow);
		switch($cartRow['ptID']){
			case 1:
				if($userData['cardID']){
					$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * 5 / 100));
				}else {
					$curPrice = $cartRow['salePrice'];
				}
				$cardPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * 5 / 100));
				break;
			case 2:
				if($userData['cardID']){
					$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
				}else {
					$curPrice = $cartRow['salePrice'];
				}
				break;
			case 3:
				if($userData['cardID']){
					$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
				}else {
					$curPrice = $cartRow['salePrice'];
				}
				break;
			case 4:
				if($userData['cardID']){
					$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
				}else {
					$curPrice = $cartRow['salePrice'];
				}
				break;
			case 5:
				$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
				break;
			case 6:
				$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
				break;
			case 7:
				$curPrice = round($cartRow['salePrice'] - ($cartRow['salePrice'] * $cartRow['ptPercent'] / 100));
				break;
			default:
				$curPrice = $cartRow['salePrice'];
				break;
		}

		$curPrice *= $accCount;

		$partQuery = "INSERT INTO pm_order_parts (orderID, accID, accCount, price) VALUES ('".$orderID."', '".$cartRow['accID']."', '".$accCount."', '".$curPrice."')";
		//echo $partQuery.'<br>';
		mysql_query($partQuery);

		header('location: /carorders?cmd=orders&act=order&orderID='.$orderID);
	}

	function getOrderRow($order, $style = '')
	{
		global $authenticationMgr, $templatesMgr;
		$order = $this->getOrder($order['orderID']);
		$userData = $authenticationMgr->getUserData($order['userID'],'');
		
		if(!$userData['cardID']) {
			$query = "UPDATE pm_order SET cardStID='0' WHERE userID = '".$userData['userID']."'";
			mysql_query($query);
		}

		if(!$style)
			$style = Array('bgcolor'=>'#FFFFFF');

		$userID = ($userData['cardID'] ? '<strong>'.$order['userID'].' <br>(<font color="#009966">'.$userData['cardID'].'</font>)</strong>' : $order['userID']);
		$orderIDCol = ($userData['cardID'] ? '<strong><font color="#009966">'.$order['orderID'].'</font></strong>' : $order['orderID']);
		$content = '
		<tr bgcolor="'.$style['bgcolor'].'"> 
		<td bgcolor="'.$style['bgcolor'].'" '.($order['wait'] ? 'class="today"' : "" ).' >'.$orderIDCol.'</td>
		<td>'.$userID.'<br> </td>
		<td> <a style="cursor: pointer" onClick="openPopupWindow(\'/carorders?cmd=orders&act=anket&orderID='.$order['orderID'].'\', 600, 500); return false;">Анкета</a></td>
		<td bgcolor="'.$style['bgcolor'].'"><p> <a style="cursor: pointer" onClick="openPopupWindow(\'/carorders?cmd=orders&act=order&orderID='.$order['orderID'].'\', 600, 500); return false">Посмотреть</a><br>
			'.$this->getCountParts($order).' товара(ов) </p></td>
		<td bgcolor="'.$style['bgcolor'].'">'.$this->getOrderPrice($order['orderID']).'</td>
		<td> <a style="cursor: pointer" onClick="openPopupWindow(\'/carorders?cmd=orders&act=data&orderID='.$order['orderID'].'\', 600, 500); return false">История дат</a> <br>
		  '.$order['stDate'].'</td>
		<td '.($order['wait'] ? 'class="today"' : "" ).'> <a style="cursor: pointer" onClick="openPopupWindow(\'/carorders?cmd=orders&act=delivery&orderID='.$order['orderID'].'\', 600, 500); return false">'.($order['deliveryDate'] == "0000-00-00" ? 'Назначить' : 'Изменить').'</a><br>
		  '.$order['deliveryDate'].'</td>
		<td><p> <a style="cursor: pointer" onClick="openPopupWindow(\'/carorders?cmd=orders&act=prepayment&orderID='.$order['orderID'].'\', 600, 500); return false">Изменить</a><br>
			'.$order['prePayment'].' руб. </p></td>
		<td><a style="cursor: pointer" onClick="openPopupWindow(\'/carorders?cmd=orders&act=send&orderID='.$order['orderID'].'\', 600, 500); return false">Послать</a><br>
			'.($order['numOfMes'] ? '<a style="cursor: pointer" onClick="openPopupWindow(\'/carorders?cmd=orders&act=viewReplies&orderID='.$order['orderID'].'\', 600, 500); return false">Читать</a>' : '').'
		</td>
		<td><select name="cardStID'.$order['orderID'].'" onChange="updateCardSt('.$order['userID'].'); return false;" class="widesel" '.($userData['userID'] == 1 ? 'disabled' : '').'>
			'.$this->getCardStatusClassifier($order['cardStID']).'
		  </select></td>
		<td><select name="stID'.$order['orderID'].'" class="widesel">
			'.$this->getOrderStatusClassifier($order['stID']).'
		  </select></td>
	  </tr>
		';
		
		return $content;
	}

	function getPrepayment($orderID)
	{
		global $authenticationMgr, $templatesMgr;
		$order = $this->getOrder($orderID);

		$tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/_pre.htm');
		$tpl = str_replace('%ordertype%', 'orders', $tpl);
		
		$tpl = str_replace('%orderID%', $orderID, $tpl);
		$tpl = str_replace('%prePayment%', $order['prePayment'], $tpl);

		return $tpl;
	}

	function getSend($orderID)
	{
		global $authenticationMgr, $templatesMgr;
		$order = $this->getOrder($orderID);
		$userData = $authenticationMgr->getUserData($order['userID'],'');

		$tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/_answ.html');
		$tpl = str_replace('%ordertype%', 'orders', $tpl);
		
		$tpl = str_replace('%orderID%', $orderID, $tpl);
		$tpl = ($userData['cardID'] ? str_replace('%userID%', '<strong>'.$userData['userID'].' (<font color="#009966">'.$userData['cardID'].'</font>)</strong>', $tpl) : str_replace('%userID%', $userData['userID'], $tpl));

		return $tpl;
	}

	function getDeliveryDate($orderID)
	{
		global $authenticationMgr, $templatesMgr;
		$order = $this->getOrder($orderID);
		if($order['deliveryDate'] == '0000-00-00') {
			$order['deliveryDate'] = date('Y-m-d');
		}
		$dateParams = explode('-', $order['deliveryDate']);
		
		$tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/_data_postavki.html');
		$tpl = str_replace('%ordertype%', 'orders', $tpl);
		
		$tpl = str_replace('%orderID%', $orderID, $tpl);
		$tpl = str_replace('%day%', $dateParams[2], $tpl);
		$tpl = str_replace('%month%', $dateParams[1], $tpl);
		$tpl = str_replace('%year%', $dateParams[0], $tpl);

		return $tpl;
	}

	function getDataHistory($orderID)
	{
		global $authenticationMgr, $templatesMgr;

		$order = $this->getOrder($orderID);

		$tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/_data.html');
		$tpl = str_replace('%ordertype%', 'orders', $tpl);
		
		$tpl = str_replace('%orderID%', $orderID, $tpl);

		$query = "SELECT IF(stDate, DATE_FORMAT(stDate,'%Y-%m-%d %H:%i:%s'), '-') as stDate, name FROM pm_order_status LEFT JOIN pm_order_status_date ON pm_order_status_date.orderID ='".$orderID."' && pm_order_status_date.stID = pm_order_status.stID";
		//echo $query;
		$history = '';
		$result = mysql_query($query);
		if(mysql_num_rows($result)) {
			$i=0;
			while($his = mysql_fetch_assoc($result)) {
				$history .= '  <tr bgcolor="'.($i % 2 ? "#FFFFFF" : "#F6F6F6").'"> 
								<td align="right">'.$his['name'].'</td>
								<td>'.$his['stDate'].'</td>
							  </tr>
							';
				$i++;
			}
		}


		$tpl = str_replace('%history%', $history, $tpl);

		return $tpl;
	}
	
	function getLookOrder($orderID)
	{
		global $authenticationMgr, $templatesMgr, $structureMgr;

		$order = $this->getOrder($orderID);
		$userData = $authenticationMgr->getUserData($order['userID'],'');
		$parts = $this->getParts($orderID);
		$tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/_look_order.html');
		$tpl = str_replace('%ordertype%', 'orders', $tpl);
		
		$tpl = ($userData['cardID'] ? str_replace('%userID%', '<strong>'.$userData['userID'].' (<font color="#009966">'.$userData['cardID'].'</font>)</strong>', $tpl) : str_replace("%userID%", $userData['userID'], $tpl));
		$tpl = ($userData['cardID'] ? str_replace("%userIDTitle%", $userData['userID'].' '.$userData['cardID'], $tpl) : str_replace('%userIDTitle%', $userData['userID'], $tpl));
		$tpl = str_replace('%orderID%', $orderID, $tpl);
		$tpl = str_replace('%message%', $order['comment'], $tpl);
		
		$partsrow = '';
		$i = 0;
		$num = 0;
		$sum = 0;
		//print_r($parts);
		foreach($parts as $part) {
			$partsrow .= '<tr bgcolor="'.($i % 2 ? '#FFFFFF' : '#F6F6F6').'"> 
						<td><a href="'.$structureMgr->getPathByPageID($part['sID'], false).'">'.$part['ShortTitle'].'</a></td>
						<td>'.$part['price'].'</td>
						<td> <input name="gc'.$part['accID'].'" class="input02" value="'.$part['accCount'].'" type="text"></td>
						<td align="center"><input type="checkbox" name="del'.$part['accID'].'" value="1"></td>
					  </tr>';
			$num += $part['accCount'];
			$sum += $part['price'];
			$i++;
		}

		$tpl = str_replace('%partsrow%', $partsrow, $tpl);
		$tpl = str_replace('%num%', $num, $tpl);
		$tpl = str_replace('%sum%', $sum, $tpl);

		return $tpl;
	}

	function getAnket($orderID)
	{
		global $authenticationMgr, $templatesMgr;

		$order = $this->getOrder($orderID);
		if($order['userID'] == 1) {
			$query = "SELECT orderID, userName, userPhone, userEmail, userAdress FROM pm_order_quick WHERE orderID = '".$orderID."'";
			$result = mysql_query($query);
			$row = mysql_fetch_assoc($result);

			$userData['LastName'] = $row['userName'];
			$userData['phone'] = $row['userPhone'];
			$userData['Email'] = $row['userEmail'];
			$userData['address'] = $row['userAdress'];
		} else {
			$userData = $authenticationMgr->getUserData($order['userID'],'');
		}

		$tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/orders/_anketa.html');
		$tpl = str_replace('%ordertype%', 'orders', $tpl);
		//print_r($userData);
		//echo $userData['cardID'].' ---- '.$userData['userID'];
		$tpl = ($userData['cardID'] ? str_replace('%userID%', '<strong>'.$userData['userID'].' (<font color="#009966">'.$userData['cardID'].'</font>)</strong>', $tpl) : str_replace('%userID%', $userData['userID'], $tpl));
		$tpl = ($userData['cardID'] ? str_replace('%userIDTitle%', $userData['userID'].' '.$userData['cardID'], $tpl) : str_replace('%userIDTitle%', $userData['userID'], $tpl));
		$tpl = str_replace('%email%', $userData['Email'], $tpl);
		$tpl = str_replace('%lastName%', $userData['LastName'], $tpl);
		$tpl = str_replace('%firstName%', $userData['FirstName'], $tpl);
		$tpl = str_replace('%secondName%', $userData['SurName'], $tpl);
		$tpl = str_replace('%phone%', $userData['phone'], $tpl);
		$tpl = str_replace('%region%', $userData['region'], $tpl);
		$tpl = str_replace('%city%', $userData['city'], $tpl);
		$tpl = str_replace('%adress%', $userData['address'], $tpl);
		$tpl = str_replace('%car%', $userData['plantName'].' '. $userData['carModel'], $tpl);

		return $tpl;

	}
	
	function getOrderStatusClassifier($stID)
	{
		$content = '';
		$statuses = $this->getOrderStatuses();
		foreach($statuses as $status) {
			$content .= '<option value="'.$status['stID'].'" '.($status['stID'] == $stID ? 'selected' : "").'>'.$status['name'].'</option>'."\n";
		}

		return $content;

	}

	function getCardStatusClassifier($cardStID)
	{
		$content = '';
		$statuses = $this->getCardStatuses();
		foreach($statuses as $status) {
			$content .= '<option value="'.$status['cardStID'].'" '.($status['cardStID'] == $cardStID ? "selected" : "").'>'.$status['name'].'</option>'."\n";
		}

		return $content;
	}
	
	function getOrderStatuses()
	{
		$statuses = Array();
		$query = 'SELECT COUNT(pm_order.stID) as num, pm_order_status.stID, pm_order_status.name 
		              FROM pm_order_status 
		              LEFT JOIN pm_order ON pm_order.stID = pm_order_status.stID 
		              GROUP BY pm_order_status.stID';
		$result = mysql_query($query);
		if(mysql_num_rows($result)) {
			while($row = mysql_fetch_assoc($result)) {
				$statuses[] = $row;	
			}
		}

		return $statuses;
	}

	function getCardStatuses()
	{
		$statuses = Array();
		$query = 'SELECT cardStID, name FROM pm_order_card_st';
		$result = mysql_query($query);
		if(mysql_num_rows($result)) {
			while($row = mysql_fetch_assoc($result)) {
				$statuses[] = $row;	
			}
		}

		return $statuses;
	}

	function getCountParts($order)
	{
		$num = 0;
		foreach($order['parts'] as $part)
			$num += $part['accCount'];
		
		return $num;
	}

	function getParts($orderID)
	{
		$parts = Array();

		$partQuery = "SELECT * FROM pm_order_parts, pm_as_parts 
		LEFT JOIN pm_structure ON (pm_as_parts.sID = pm_structure.sID) 
		WHERE pm_as_parts.accID = pm_order_parts.accID && orderID = '".$orderID."'";
		$partResult = mysql_query($partQuery);
		if(mysql_num_rows($partResult)) {
			while($part = mysql_fetch_assoc($partResult)) {
				$parts[] = $part;
			}
		}
		return $parts;
	}

	function getOrders($orderStatus, $start, $count)
	{
		$orders = Array();
		$query = "SELECT SQL_CALC_FOUND_ROWS IF('".date("Y-m-d H:i:00")."' = deliveryDate, 1, 0) as wait, pm_order.orderID,userID, stDate, deliveryDate, prePayment, comment, cardStID, stID, count(pm_order_message.message) as numOfMes 
		FROM pm_order 
		LEFT JOIN pm_order_message ON pm_order_message.orderID = pm_order.orderID
		WHERE stID = '".$orderStatus."' 
		GROUP BY pm_order.orderID
		ORDER BY stDate desc LIMIT ".$start.",".$count."";
		$result = mysql_query($query);
		
		$query = 'SELECT FOUND_ROWS() as countOrders';
		$res = mysql_query($query);
		$row = mysql_fetch_assoc($res);
		$this->countOrders = $row['countOrders'];

		if(mysql_num_rows($result)) {
			while($row = mysql_fetch_assoc($result)) {
				$partQuery = "SELECT * FROM pm_order_parts WHERE orderID = '".$row['orderID']."'";
				$partResult = mysql_query($partQuery);
				if(mysql_num_rows($partResult)) {
					while($parts = mysql_fetch_assoc($partResult)) {
						$row['parts'][] = $parts;
					}
				}
				$orders[] = $row;
			}
		}

		return $orders;
	}

	function getOrder($orderID)
	{
		$orders = Array();
		$query = "SELECT pm_order.orderID, userID, stDate, deliveryDate, prePayment, cardStID, stID, comment, count(pm_order_message.message) as numOfMes FROM pm_order
		LEFT JOIN pm_order_message ON pm_order_message.orderID = pm_order.orderID
		WHERE pm_order.orderID = '".$orderID."' GROUP BY pm_order.orderID";
		//echo $query;
		$result = mysql_query($query);
		
		$query = 'SELECT FOUND_ROWS() as countOrders';
		$res = mysql_query($query);
		$row = mysql_fetch_assoc($res);
		$this->countOrders = $row['countOrders'];

		if(mysql_num_rows($result)) {
			$row = mysql_fetch_assoc($result);
			$partQuery = "SELECT * FROM pm_order_parts WHERE orderID = '".$row['orderID']."'";
			$partResult = mysql_query($partQuery);
			if(mysql_num_rows($partResult)) {
				while($parts = mysql_fetch_assoc($partResult)) {
					$row['parts'][] = $parts;
				}
			}
			$order = $row;
			
		}

		return $order;
	}

	function getOrderPrice($orderID) {
		$query = "SELECT SUM(price) as orderPrice FROM pm_order_parts WHERE orderID='".$orderID."'";
		$result= mysql_query($query);
		$row = mysql_fetch_assoc($result);
		if(!$row['orderPrice'])
			$row['orderPrice'] = 0;
		return $row['orderPrice'];
	}




}
?>
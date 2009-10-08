<?
require("class.phpmailer.php");

class MailsAdminHandler{

	function getContent()
	{
		global $templatesMgr;

		$tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/mail.html");

		return $tpl;

	}

	function sendToRSS()
	{
	}

	function sendToMail()
	{
		global $templatesMgr;

		$tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/message.html");

		$club = _post("club");
		$reged = _post("reged");
		$sales = _post("sales");
		$bonus = _post("bonus");
		
		$subject = _post("subject");
		$body = _post("body");
		$body = str_replace("\n","<br>",$body);

		$mail = new PHPMailer();

		$mail->IsSMTP(); // set mailer to use SMTP
		$mail->Host = "localhost";  // specify main and backup server
		$mail->SMTPAuth = true;     // turn on SMTP authentication
		$mail->Username = "robot@carumba.ru";  // SMTP username
		$mail->Password = "Vifi2Ht6b"; // SMTP password

		$mail->From = "robot@carumba.ru";
		$mail->FromName = "Carumba.ru";
		
		$mails = Array();
		if($club) {
			$userMails = $this->getClubMailList();
			if(count($userMails)) {
				foreach($userMails as $userMail=>$name)
					$mails[$userMail] = $name;
			}
		}
		if($reged) {
			$userMails = $this->getRegedMailList();
			if(count($userMails)) {
				foreach($userMails as $userMail=>$name)
					$mails[$userMail] = $name;
			}
		}
		
		//echo count($mails);
		if(count($mails)) {
			//foreach($mails as $email=>$name) {
			//	$mail->AddAddress($email, $name);
			//}
			//print_r($mails);
			$mail->WordWrap = 50;                                 // set word wrap to 50 characters
			$mail->IsHTML(true);                                  // set email format to HTML
			if(!$subject) {
				$mail->Subject = "Предложение о распродажах на carumba.ru";
			} else {
				$mail->Subject = $subject;
			}
			
			$bonusBody = "";
			if($bonus)
				$bonusBody.= $this->getBonusList();
			
			$saleBody = "";
			if($sales)
				$saleBody.= $this->getSaleList();
			
			$tpl = str_replace("%text%", $body, $tpl);
			$tpl = str_replace("%bonus%", $bonusBody, $tpl);
			$tpl = str_replace("%sale%", $saleBody, $tpl);
			$mail->Body = $tpl;
			//echo $mail->Body ;
			//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
			
			
			foreach($mails as $email=>$name) {
				$mail->AddAddress($email, $name);
				if(!$mail->Send())
				{
				   trigger_error("Message could not be sent.Mailer Error: " . $mail->ErrorInfo, PM_FATAL);
				}
				$mail->ClearAddresses();
			}
			
			header("location: /admin?cmd=mails");
		}
	}

	function getRegedMailList(){
		$mails = Array();

		$query = "SELECT Email, FirstName, LastName, SurName FROM pm_users WHERE length(Email)<>0 && cardID=0 && subscribe = 1 ";
		$result = mysql_query($query);
		if(mysql_num_rows($result)) {
			while($row = mysql_fetch_assoc($result)) {
				$mails[$row['Email']] = $row['FirstName'].' '.$row['LastName'];
			}
		}
		//echo $query.'<br>';

		return $mails;
	}

	function getClubMailList(){
		$mails = Array();

		$query = "SELECT Email, FirstName, LastName, SurName FROM pm_users WHERE length(Email)<>0 && cardID<>0 && subscribe = 1 ";
		$result = mysql_query($query);
		if(mysql_num_rows($result)) {
			while($row = mysql_fetch_assoc($result)) {
				$mails[$row['Email']] = $row['FirstName'].' '.$row['LastName'];
			}
		}
		//echo $query.'<br>';
		return $mails;
	}

	function getSaleList()
	{
		global $structureMgr;

		$content = "";

		$items = $this->getSaleItems();


		$content .= "<p><strong>Распродажа:</strong></p>";
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

			$content .= "<p><a href='".$URL."'><img src='http://carumba.ru".$item["logotype"]."' width=70 height=70 border=0></a><br><a  href=\"".$URL."\">".$item["ShortTitle"]."</a><br>". $firstPrice." / ".$item["salePrice"]." руб. (- <strong>".$item["ptPercent"]."</strong>%)</p>";
		}
		$content .= "<p>&nbsp;</p>";
		
		return $content;
	}

	function getBonusList()
	{
		global $structureMgr;


		$items = $this->getBonusItems();

		$content = "<p><strong>Спецпредложение для членов автоклуба: </strong></p>";
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

			$content .= "<p><a href='".$URL."'><img src='http://carumba.ru".$item["smallPicture"]."' width=70 height=70 border=0></a><br><a  href=\"".$URL."\">".$item["ShortTitle"]."</a><br><strong>". $firstPrice."</strong> / ".$item["salePrice"]." руб. (- <strong>".$item["ptPercent"]."</strong>%)</p>";
		}
		$content .= "<p>&nbsp;</p>";
		
		return $content;
	}

	function getSaleItems()
	{
		$query = "SELECT 
				accID, p.sID, ShortTitle, deliveryCode, accPlantName, logotype, smallPicture, p.tplID, salePrice, 
				MustUseCompatibility, PicturePath, DescriptionTemplate, ptPercent  
				FROM `pm_as_parts` p
				LEFT JOIN pm_as_producer ap ON (ap.accPlantID = p.accPlantID)
				LEFT JOIN pm_structure s ON (p.sID = s.sID)
				LEFT JOIN pm_as_categories ac ON (s.pms_sID = ac.sID)
				LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
				WHERE pt.ptID = 5 || pt.ptID = 6 || pt.ptID = 7
			";
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

	function getBonusItems()
	{
		$query = "SELECT 
				accID, p.sID, ShortTitle, deliveryCode, accPlantName, logotype, smallPicture, p.tplID, salePrice, 
				MustUseCompatibility, PicturePath, DescriptionTemplate, ptPercent  
				FROM `pm_as_parts` p
				LEFT JOIN pm_as_producer ap ON (ap.accPlantID = p.accPlantID)
				LEFT JOIN pm_structure s ON (p.sID = s.sID)
				LEFT JOIN pm_as_categories ac ON (s.pms_sID = ac.sID)
				LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
				WHERE pt.ptID = 5 || pt.ptID = 6 || pt.ptID = 7
			";
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


}
?>

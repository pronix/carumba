<?
    class Register extends AbstractModule
    {
        function Register()
        {
            $this->publicFunctions = array("getContent", "getSubItemType", "getItemType", "getItemDesc", 
            "getSpecificDataForEditing", "getSpecificBlockDesc", "updateSpecificData", "updateAdditionalColumns");
            $this->cmdFunctions = array();
        }

        function updateSpecificData($args)
        {
            global $structureMgr;
            $sData = array();
            $qSet = "";
            

            if ($args[0] != -1)
            {
                $md = $structureMgr->getMetaData($args[0]);
            }
            else
            {
                trigger_error("pageID must be specified", PM_WARNING);
                return false;
            }

            return true;
        }
        
        function updateAdditionalColumns($args)
        {
            return true;
        }
        
        function getSpecificDataForEditing($args)
        {
            return array();
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
                case "RegForm":
                    return "Форма регистрации";
                case "LostPassword":
                    return "Восстановление пароля";
                case "VIN":
                    return "Заказ по VIN";
            }
            
            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "RegForm":
                    return array("форма регистрации", "форму регистрации", "формы регистрации");
                case "LostPassword":
                    return array("форма восстановления пароля", "форму восстановления пароля", "формы восстановления пароля");
                case "VIN":
                    return array("форма заказа по VIN", "форму заказа по VIN", "формы заказа по VIN");
            }
            
            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case "Article":
                    return array("RegForm" => "форму регистрации", "LostPassword" => "форму восстановления пароля", "VIN" => "форму заказа по VIN");
                case "RegForm":
                    return array();
                case "LostPassword":
                    return array();
            }
            
            return array();
        }
        
        function getContent($args)
        {
            global $structureMgr;
            $md = $structureMgr->getMetaData($args[0]);

            switch ($md["DataType"])
            {
                case "RegForm": 
                    return $this->getRegForm($args[0]);
                case "LostPassword":
                    return $this->getLostPasswordForm($args[0]);
                case "VIN":
                    return $this->getVINForm($args[0]);
                default:
                    trigger_error("Unknown datatype: " . $md["DataType"], PM_FATAL);
            }
        }

        function getRegForm($pageID)
        {
            global $structureMgr, $templatesMgr, $authenticationMgr;
            
            $doRegister = 0;
            $msg = "";

            if ($authenticationMgr->getUserID() > 1)
            {
//                return "Зарегистрированные пользователи даже попасть сюда не должны!";
                header("Status: 302 Moved");
                header("Location: /");
                exit();
            }
			$msg .= _get("msg");
            if (_post("action") == "doRegister")
            {
                if (_post("login") != "")
                {
                    $ud = $authenticationMgr->getUserData(-1, _post("login"));
					echo "<!--";
					print_r($ud);
					
					$usersWithSameEmail = $authenticationMgr->getUserData(-1, 0,_post("email"));
					
					print_r($usersWithSameEmail);

					echo ' -->';

                    if (!count($ud) && !count($usersWithSameEmail))
                    {
                        $vals = array("psw", "firstname", "lastname", "surname", "email", "phone", "address", 
                        "car", "carType");
                        $names = array("Пароль", "Имя", "Фамилия", "Отчество", "E-mail", "Контактный телефон", "Адрес доставки", 
                        "Ваш автомобиль", "Марка автомобиля");

                        for ($i=0; $i < count($vals);$i++)
                        {
                            if ($vals[$i] != "carType")
                            {
                                if (_post($vals[$i]) == "")
                                    $msg .= "Заполните, пожалуйста, поле `$names[$i]`.<br />";
                            }
                            else
                            {
                                $pcar = _post("car");
                                if ((($pcar == "0") || ($pcar > 15 && $pcar < 24)) && (_post($vals[$i]) == ""))
                                    $msg .= "Заполните, пожалуйста, поле `$names[$i]`.<br />";
                                
                            }
                        }

                        if (_post("psw") != _post("psw2"))
                            $msg .= "Пароли не совпадают.";


                        if (!$msg)
                        {
                            $q = 
                            "INSERT INTO pm_users (login,`password`,FirstName,LastName,SurName,Email,sex,phone,region,city,address,carID,carType,subscribe) 
                            VALUES (
                            " . prepareVar(_post("login")) . ",
                            MD5(" . prepareVar(_post("psw")) . "),
                            " . prepareVar(_post("firstname")) . ",
                            " . prepareVar(_post("lastname")) . ",
                            " . prepareVar(_post("surname")) . ",
                            " . prepareVar(_post("email")) . ",
                            " . prepareVar(_post("sex")) . ",
                            " . prepareVar(_post("phone")) . ",
                            " . prepareVar(_post("region")) . ",
                            " . prepareVar(_post("city")) . ",
                            " . prepareVar(_post("address")) . ",
                            " . prepareVar(_post("car")) . ",
                            " . prepareVar(_post("carType")) . ",
                            " . prepareVar(_post("subscribe")) . "
                            )";


                            $qr = mysql_query($q);

                            if ($qr)
                            {
                                $msg = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regmsg.txt");
                                $subj = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regmailsubj.txt");
                                $body = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regmail.txt");

                                $body = str_replace("%login%", _post("login"), $body);
                                $body = str_replace("%psw%", _post("psw"), $body);
                                $body = str_replace("%firstname%", _post("firstname"), $body);
                                $body = str_replace("%lastname%", _post("lastname"), $body);
                                $body = str_replace("%car_type%", _post("carType"), $body);
                                
                                //mail(_post("email"), $subj, $body, "From: info@carumba.ru\r\n");
                                $mail = new PHPMailer();

                                $mail->IsSMTP(); // set mailer to use SMTP
                                $mail->Host = "localhost";  // specify main and backup server
                                $mail->SMTPAuth = true;     // turn on SMTP authentication
                                $mail->Username = "robot@carumba.ru";  // SMTP username
                                $mail->Password = "Vifi2Ht6b"; // SMTP password

                                $mail->From = "robot@carumba.ru";
                                $mail->FromName = "Carumba.ru";
            
                                $mail->WordWrap = 50;                                 // set word wrap to 50 characters
                                $mail->IsHTML(true);                                  // set email format to HTML
            
                                $mail->Subject = $subj;
        
                                $mail->Body = $body;   
                                
                                $mail->AddAddress(_post("email"));
                                if(!@$mail->Send())
                                {
                                    trigger_error("Message could not be sent.Mailer Error: " . $mail->ErrorInfo, PM_WARNING);
                                }
                                $mail->ClearAddresses();                                
        
                                $doRegister = 1;
                            }
                            else
                            {
                                $msg = mysql_error();
                            }
                        }
                    }
					elseif(count($usersWithSameEmail))
                    {
                        $msg .= "Уже существует пользователь с email `" .  _post("email") . "`.<br />";
                    }
                    else
                    {
                        $msg .= "Уже существует пользователь с логином `" .  _post("login") . "`.<br />";
                    }
                }
                else
                {
                    $msg .= "Заполните, пожалуйста, поле &quot;Логин&quot;.<br />";
                }
            }
            
            if ($doRegister == 0)
            {
                //$js = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "reg.js");
                $form = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regform.html");
                
                if ($msg)
                    $msg = "<div class=\"podbor\" style=\"color: red; text-align: center\">$msg</div>";
                
                $form = str_replace("%MSG%", $msg, $form);
                $form = str_replace("%login%",  _post("login") , $form);
                $form = str_replace("%psw%", _post("psw") , $form);
                $form = str_replace("%psw2%", _post("psw2") , $form);
                $form = str_replace("%firstname%", _post("firstname"), $form);
                $form = str_replace("%lastname%",  _post("lastname") , $form);
                $form = str_replace("%surname%", _post("surname") , $form);
				if(_get("email")){
					$form = str_replace("%email%", _get("email") , $form);
				}else {
					$form = str_replace("%email%", _post("email") , $form);
				}
                $form = str_replace("%phone%", _post("phone") , $form);
                $form = str_replace("%address%",  _post("address"), $form);
                $form = str_replace("%car_type%", _post("carType"), $form);
                $form = str_replace("%city%", _post("city"), $form);

                return $form;
            }
            else
            {
                return "<div class=\"podbor\">".$msg."</div>";
            }

        }


        function getLostPasswordForm($pageID)
        {
			global $structureMgr, $templatesMgr, $authenticationMgr;
			if (_post("action") == "getPass" && (_post("email") || _post("login")))
            {
				if(_post("login")) {
					$query = "SELECT Login, Email, FirstName, LastName FROM pm_users WHERE Login = '"._post("login")."'";
					$result = mysql_query($query);
					if(mysql_num_rows($result)) {
						$row = mysql_fetch_assoc($result);
                        $this->setNewPass($row);      
					} else   {
						return "<div class=\"podbor\">Не существует регистрации с указанным логином или e-mail адресов</div>";
					}
				} elseif(_post("email")) {
					$query = "SELECT Login, Email,  FROM pm_users WHERE Email = '"._post("email")."'";
					$result = mysql_query($query);
					if(mysql_num_rows($result)) {
						$row = mysql_fetch_assoc($result);
                        $this->setNewPass($row);      
					} else   {
						return "<div class=\"podbor\">Не существует регистрации с указанным логином или e-mail адресов</div>";
					}
				} else {
					return "<div class=\"podbor\">Не существует регистрации с указанным логином или e-mail адресов</div>";
				}
				return "<div class=\"podbor\">На указанный Вами e-mail при регистрации высланы Ваши логин и пароль</div>";
			} else {
				$rem = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/remember.html");
				return $rem;
			}
            return "LOST PASSWORD";
        }
		
		function setNewPass($row) 
		{
			global $templatesMgr;
			$new_pass = $this->generateNewPass();
			
			$msg = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regmsg.txt");
			$subj = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regmailsubj.txt");
			$body = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regmail.txt");
			
			$update = "UPDATE pm_users SET Password = MD5('".$new_pass."') WHERE Login = '".$row['Login']."' && Email = '".$row['Email']."'";
			mysql_query($update);


			$body = str_replace("%login%", $row['Login'], $body);
			$body = str_replace("%psw%", $new_pass, $body);
			$body = str_replace("%firstname%", $row['FirstName'], $body);
			$body = str_replace("%lastname%", $row['LastName'], $body);
			//mail($row['Email'], "Ваш новый пароль на carumba.ru", $body, "From: info@carumba.ru\r\n");
            
                                $mail = new PHPMailer();

                                $mail->IsSMTP(); // set mailer to use SMTP
                                $mail->Host = "localhost";  // specify main and backup server
                                $mail->SMTPAuth = true;     // turn on SMTP authentication
                                $mail->Username = "robot@carumba.ru";  // SMTP username
                                $mail->Password = "Vifi2Ht6b"; // SMTP password

                                $mail->From = "robot@carumba.ru";
                                $mail->FromName = "Carumba.ru";
            
                                $mail->WordWrap = 50;                                 // set word wrap to 50 characters
                                $mail->IsHTML(true);                                  // set email format to HTML
            
                                $mail->Subject = "Ваш новый пароль на carumba.ru";
        
                                $mail->Body = $body;   
                                
                                $mail->AddAddress($row['Email']);
                                if(!@$mail->Send())
                                {
                                    trigger_error("Message could not be sent.Mailer Error: " . $mail->ErrorInfo, PM_WARNING);
                                }
                                $mail->ClearAddresses();            
		}

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


        function getVINForm($pageID)
        {
            global $structureMgr, $templatesMgr, $authenticationMgr;
            
            $md = $structureMgr->getMetaData($pageID);
            
            $doRegister = 0;
            $msg = "";

            $UID = $authenticationMgr->getUserID();
            if ($UID > 1) //regged only
            {
                if (_post("action") == "doVIN")
                {
                    $ud = $authenticationMgr->getUserData($UID, "");
                    $usr = $ud["FirstName"] . " " . $ud["LastName"] . " ($ud[Login])";

					if(strlen(_post("VIN"))!= 17) {
						$msg = "Длина поля VIN должна быть 17 знаков.";
					}
					
					if(!$msg) {
						$body = str_replace("%user%", $usr, $body);
						$body = str_replace("%brand%", _post("brand"), $body);
						$body = str_replace("%model%", _post("model"), $body);
						$body = str_replace("%wheel%", _post("wheel"), $body);
						$body = str_replace("%year%", _post("year"), $body);
						$body = str_replace("%VIN%", _post("VIN"), $body);
						$body = str_replace("%engine%", _post("engine"), $body);
						$body = str_replace("%kuzov%", _post("kuzov"), $body);
						$body = str_replace("%kpp%", _post("kpp"), $body);
						$body = str_replace("%privod%", _post("privod"), $body);
						$body = str_replace("%abs%", _post("abs"), $body);
						$body = str_replace("%condition%", _post("condition"), $body);
						$body = str_replace("%parts_list%", _post("parts_list"), $body);
						$body = str_replace("%original%", _post("original"), $body);
						$body = str_replace("%nonoriginal%", _post("nonoriginal"), $body);

						$body = str_replace("%accID_1%", _post("accID_1"), $body);
						$body = str_replace("%accName_1%", _post("accName_1"), $body);
						$body = str_replace("%accID_2%", _post("accID_2"), $body);
						$body = str_replace("%accName_2%", _post("accName_2"), $body);
						$body = str_replace("%accID_3%", _post("accID_3"), $body);
						$body = str_replace("%accName_3%", _post("accName_3"), $body);

						//mail("info@carumba.ru", $subj, $body, "From: info@carumba.ru\r\n");
                        
                                $mail = new PHPMailer();

                                $mail->IsSMTP(); // set mailer to use SMTP
                                $mail->Host = "localhost";  // specify main and backup server
                                $mail->SMTPAuth = true;     // turn on SMTP authentication
                                $mail->Username = "robot@carumba.ru";  // SMTP username
                                $mail->Password = "Vifi2Ht6b"; // SMTP password

                                $mail->From = "robot@carumba.ru";
                                $mail->FromName = "Carumba.ru";
            
                                $mail->WordWrap = 50;                                 // set word wrap to 50 characters
                                $mail->IsHTML(true);                                  // set email format to HTML
            
                                $mail->Subject = $subj;
        
                                $mail->Body = $body;   
                                
                                $mail->AddAddress("info@carumba.ru");
                                if(!@$mail->Send())
                                {
                                    trigger_error("Message could not be sent.Mailer Error: " . $mail->ErrorInfo, PM_WARNING);
                                }
                                $mail->ClearAddresses();                        
						
						if ($ud['cardID']) {
							$cardStID = 4;
						} else {
							$cardStID = 1;
						}
						
						if ($md["URLName"] == "vin-order")
						{
							$orderType = 0;		
						} elseif ($md["URLName"] == "vin-order-gem")
						{
							$orderType = 1;
						} 
						

						$orderQuery = "INSERT INTO pm_vinorder (userID, stDate, cardStID, stID, comment, orderType) VALUES ('".$ud['userID']."', '".date("Y-m-d H:i")."', '".$cardStID."', '1', '',".$orderType.")";
						//echo $orderQuery.'<br />';
						mysql_query($orderQuery);
						
						$orderQuery = "SELECT orderID FROM pm_vinorder WHERE userID = '".$authenticationMgr->getUserID()."' && stID = '1' ORDER BY orderID desc";
						//echo $orderQuery.'<br />';
						$orderResult = mysql_query($orderQuery);
						$row = mysql_fetch_assoc($orderResult);
						$orderID = $row['orderID'];
					
						$orderStatusQuery = "INSERT INTO pm_vinorder_status_date (orderID, stID, stDate) VALUES ('".$orderID."', '1', '".date("Y-m-d H:i")."')";
						//echo $orderStatusQuery.'<br />';
						mysql_query($orderStatusQuery);

						for($i = 1 ; $i < 4; $i++) {
							$orderpartsDetails = "INSERT INTO pm_vinorder_parts_details (orderID, accID, accName) VALUES ('".$orderID."', '"._post("accID_".$i)."', '"._post("accName_".$i)."')";
							//echo $orderStatusQuery.'<br />';
							mysql_query($orderpartsDetails);
						}
						
						$partQuery = "insert into `pm_vinorder_parts` (orderID, carName, carModel, rul, year, vin, vengine, cuzov, kpp, privod, abs, `condition`, `original`, `other`, wanted) values ('".$orderID."', '"._post("brand")."', '"._post("model")."', '"._post("wheel")."', '"._post("year")."', '"._post("VIN")."', '"._post("engine")."', '". _post("kuzov")."', '"._post("kpp")."', '"._post("privod")."', '"._post("abs")."', '"._post("condition")."', '"._post("original")."', '"._post("nonoriginal")."', '"._post("parts_list")."')";
						//echo $partQuery.'<br />';
						mysql_query($partQuery);
						if ($md["URLName"] == "vin-order")
						{
							header("location: /catalogue/vin-order/?orderID=".$orderID);	
						} elseif ($md["URLName"] == "vin-order-gem")
						{
							header("location: /catalogue/vin-order-gem/?orderID=".$orderID);
						} 
						
						
					} else {
						
						$message = "<div class=\"podbor\">".$msg."</div>";
						
						if ($md["URLName"] == "vin-order")
						{
							$vinorderTpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Catalogue/vinorder.html");		
						} elseif ($md["URLName"] == "vin-order-gem")
						{
							$vinorderTpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Catalogue/vinorder_gem.html");		
						} 
						
						$vinorderTpl = str_replace("%user%", $usr, $vinorderTpl);
						$vinorderTpl = str_replace("%brand%", _post("brand"), $vinorderTpl);
						$vinorderTpl = str_replace("%model%", _post("model"), $vinorderTpl);
						$vinorderTpl = str_replace("%wheel%", _post("wheel"), $vinorderTpl);
						$vinorderTpl = str_replace("%year%", _post("year"), $vinorderTpl);
						$vinorderTpl = str_replace("%VIN%", _post("VIN"), $vinorderTpl);
						$vinorderTpl = str_replace("%engine%", _post("engine"), $vinorderTpl);
						$vinorderTpl = str_replace("%kuzov%", _post("kuzov"), $vinorderTpl);
						$vinorderTpl = str_replace("%kpp%", _post("kpp"), $vinorderTpl);
						$vinorderTpl = str_replace("%privod%", _post("privod"), $vinorderTpl);
						$vinorderTpl = str_replace("%abs%", _post("abs"), $vinorderTpl);
						$vinorderTpl = str_replace("%condition%", _post("condition"), $vinorderTpl);
						$vinorderTpl = str_replace("%parts_list%", _post("parts_list"), $vinorderTpl);
						$vinorderTpl = str_replace("%original%", _post("original"), $vinorderTpl);
						$vinorderTpl = str_replace("%nonoriginal%", _post("nonoriginal"), $vinorderTpl);

						$vinorderTpl = str_replace("%accID_1%", _post("accID_1"), $vinorderTpl);
						$vinorderTpl = str_replace("%accName_1%", _post("accName_1"), $vinorderTpl);
						$vinorderTpl = str_replace("%accID_2%", _post("accID_2"), $vinorderTpl);
						$vinorderTpl = str_replace("%accName_2%", _post("accName_2"), $vinorderTpl);
						$vinorderTpl = str_replace("%accID_3%", _post("accID_3"), $vinorderTpl);
						$vinorderTpl = str_replace("%accName_3%", _post("accName_3"), $vinorderTpl);

						$vinorderTpl = str_replace("%send%","<input type=\"image\" src=\"/images/vin.gif\"><br />",$vinorderTpl);
						return $message.$vinorderTpl;
					}
                    //return $msg;

                }
                else {
					$message = "";
					if(_get('orderID')) {
						//echo 'epta?';
						$message = "<div class=\"podbor\">Ваш заказ успешно обработан. Номер заказа ."._get('orderID')."</div>";
					}
					if ($md["URLName"] == "vin-order")
					{
						$vinorderTpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Catalogue/vinorder.html");		
					} elseif ($md["URLName"] == "vin-order-gem")
					{
						$vinorderTpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Catalogue/vinorder_gem.html");		
					} 
				
					$vinorderTpl = str_replace("%user%", $usr, $vinorderTpl);
					$vinorderTpl = str_replace("%brand%", _post("brand"), $vinorderTpl);
					$vinorderTpl = str_replace("%model%", _post("model"), $vinorderTpl);
					$vinorderTpl = str_replace("%wheel%", _post("wheel"), $vinorderTpl);
					$vinorderTpl = str_replace("%year%", _post("year"), $vinorderTpl);
					$vinorderTpl = str_replace("%VIN%", _post("VIN"), $vinorderTpl);
					$vinorderTpl = str_replace("%engine%", _post("engine"), $vinorderTpl);
					$vinorderTpl = str_replace("%kuzov%", _post("kuzov"), $vinorderTpl);
					$vinorderTpl = str_replace("%kpp%", _post("kpp"), $vinorderTpl);
					$vinorderTpl = str_replace("%privod%", _post("privod"), $vinorderTpl);
					$vinorderTpl = str_replace("%abs%", _post("abs"), $vinorderTpl);
					$vinorderTpl = str_replace("%condition%", _post("condition"), $vinorderTpl);
					$vinorderTpl = str_replace("%parts_list%", _post("parts_list"), $vinorderTpl);
					$vinorderTpl = str_replace("%original%", _post("original"), $vinorderTpl);
					$vinorderTpl = str_replace("%nonoriginal%", _post("nonoriginal"), $vinorderTpl);

					$vinorderTpl = str_replace("%accID_1%", _post("accID_1"), $vinorderTpl);
					$vinorderTpl = str_replace("%accName_1%", _post("accName_1"), $vinorderTpl);
					$vinorderTpl = str_replace("%accID_2%", _post("accID_2"), $vinorderTpl);
					$vinorderTpl = str_replace("%accName_2%", _post("accName_2"), $vinorderTpl);
					$vinorderTpl = str_replace("%accID_3%", _post("accID_3"), $vinorderTpl);
					$vinorderTpl = str_replace("%accName_3%", _post("accName_3"), $vinorderTpl);

					$vinorderTpl = str_replace("%send%","<input type=\"image\" src=\"/images/vin.gif\"><br />",$vinorderTpl);
					return $message.$vinorderTpl;
				}
            }
            else
            {
				if ($md["URLName"] == "vin-order")
				{
					$vinorderTpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Catalogue/vinorder.html");		
				} elseif ($md["URLName"] == "vin-order-gem")
				{
					$vinorderTpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Catalogue/vinorder_gem.html");		
				} 
				
				$noregister = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/regisneed.html");
				$vinorderTpl = str_replace("%send%","",$vinorderTpl);
                return $noregister.$vinorderTpl;
            }

        }


    }
?>



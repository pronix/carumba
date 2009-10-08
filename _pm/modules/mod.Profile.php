<?
    class Profile extends AbstractModule
    {
        function Profile()
        {
            $this->publicFunctions = array("getContent", "subscribeBlock", "getSubItemType", "getItemType", "getItemDesc", 
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
                case "ProfileForm":
                    return "Форма редактирования профайла";
            }
            
            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "ProfileForm":
                    return array("форма редактирования профайла", "фрму редактирования профайла", "Формы редактирования профайла");
            }
            
            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case "Article":
                    return array("ProfileForm" => "Форма редактирования профайла");
                case "ProfileForm":
                    return array();
            }
            
            return array();
        }
        
        function getContent($args)
        {
            global $structureMgr;
            $md = $structureMgr->getMetaData($args[0]);

			return $this->getRegForm($args[0]);
        }

        function getRegForm($pageID)
        {
            global $structureMgr, $templatesMgr, $authenticationMgr;
            
            $doRegister = 0;
            $msg = "";

            if ($authenticationMgr->getUserID() <= 1)
            {
//                return "Незарегистрированные пользователи даже попасть сюда не должны!";
                header("Status: 302 Moved");
				$msg = "Для подписки на рассылку предложений по распродаже, нужна регистрация";
                header("Location: /registration?msg=".$msg."&amp;email="._post("email"));
                exit();
            }

            if (_post("action") == "doUpdate")
            {
                if (_post("login") != "")
                {
                    $ud = Array();// $authenticationMgr->getUserData(-1, _post("login"));

                    if (count($ud) == 0)
                    {
                        $vals = array("firstname", "lastname", "surname", "email", "phone", "address", 
                        "carID", "carType");
                        $names = array("Имя", "Фамилия", "Отчество", "E-mail", "Контактный телефон", "Адрес доставки", 
                        "Ваш автомобиль", "Марка автомобиля");

                        for ($i=0; $i < count($vals);$i++)
                        {
                            if ($vals[$i] != "carType")
                            {
                                if (_post($vals[$i]) == "")
                                    $msg .= "Заполните, пожалуйста, поле `$names[$i]`.<br>";
                            }
                            else
                            {
                                $pcar = _post("carID");
                                if ((($pcar == "0") || ($pcar > 15 && $pcar < 24)) && (_post($vals[$i]) == ""))
                                    $msg .= "Заполните, пожалуйста, поле `$names[$i]`.<br>";
                                
                            }
                        }

                        if (_post("psw") != _post("psw2") && _post("psw"))
                            $msg .= "Пароли не совпадают.";
						

                        if (!$msg)
                        {
							$updateInfo = Array();
							if(_post("login")) 
								$updateInfo[] = "login = " . prepareVar(_post("login")) ;
							
							if(_post("psw")) 
								$updateInfo[] = "`password` = MD5(" . prepareVar(_post("psw")).")";
							
							if(_post("firstname"))
								$updateInfo[] = "FirstName = " . prepareVar(_post("firstname"));
							if(_post("lastname"))
								$updateInfo[] = "LastName = " . prepareVar(_post("lastname"));
							if(_post("surname"))
								$updateInfo[] = "SurName = " . prepareVar(_post("surname"));
							if(_post("email"))
								$updateInfo[] = "Email = " . prepareVar(_post("email"));
							if(_post("sex"))
								$updateInfo[] = "sex = " . prepareVar(_post("sex"));
							if(_post("phone"))
								$updateInfo[] = "phone = " . prepareVar(_post("phone"));
							if(_post("region"))
								$updateInfo[] = "region = " . prepareVar(_post("region"));
							if(_post("city"))
								$updateInfo[] = "city = " . prepareVar(_post("city"));
							if(_post("address"))
								$updateInfo[] = "address = " . prepareVar(_post("address"));
							if(_post("carID"))
								$updateInfo[] = "carID = " . prepareVar(_post("carID"));
							if(_post("carType"))
								$updateInfo[] = "carType = " . prepareVar(_post("carType"));
							if(_post("subscribe"))
								$updateInfo[] = "subscribe = " . prepareVar(_post("subscribe"));
							
							if(count($updateInfo)) {
								$q = 
								"UPDATE pm_users SET
								".implode(",", $updateInfo)."
								WHERE userID ='".$authenticationMgr->getUserID()."'";
								//echo $q;
								$qr = mysql_query($q);
							}

                            if ($qr)
                            {
                                $msg = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regmsg.txt");
                                $subj = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regmailsubj.txt");
                                $body = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "regmail.txt");

                                $body = str_replace("%login%", _post("login"), $body);
                                $body = str_replace("%psw%", _post("psw"), $body);
                                $body = str_replace("%firstname%", _post("firstname"), $body);
                                $body = str_replace("%lastname%", _post("lastname"), $body);
                                $body = str_replace("%carType%", _post("carType"), $body);
                                
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
        
                                $doUpdate = 1;
                            }
                            else
                            {
                                $msg = mysql_error();
                            }
                        }
                    }
                    else
                    {
                        $msg .= "Уже существует пользователь с логином `" .  _post("login") . "`.<br>";
                    }
                }
                else
                {
                    $msg .= "Заполните, пожалуйста, поле &amp;quot;Логин&amp;quot;.<br>";
                }
            }
            
            if ($doUpdate == 0)
            {
                //$js = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "reg.js");
                $form = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "profile.html");
                
                if ($msg)
                    $msg = "<div class=\"podbor\" style=\"color: red; text-align: center\">$msg<br><br></div>";
                
				$user = $this->getUserData($authenticationMgr->getUserID());
				

                $form = str_replace("%MSG%", $msg, $form);
                $form = str_replace("%login%", (_post("login")?_post("login"):$user['Login'] ) , $form);
                $form = str_replace("%psw%",str_replace("\"", "&amp;quot;", (_post("psw")?_post("psw"):""  ))  , $form);
                $form = str_replace("%psw2%", str_replace("\"", "&amp;quot;", (_post("psw2")?_post("psw2"):""  ))  , $form);
                $form = str_replace("%firstname%", str_replace("\"", "&amp;quot;", (_post("firstname")?_post("firstname"):$user['FirstName']  ))  , $form);
                $form = str_replace("%lastname%", str_replace("\"", "&amp;quot;", (_post("lastname")?_post("lastname"):$user['LastName']  ))  , $form);
                $form = str_replace("%surname%", str_replace("\"", "&amp;quot;", (_post("surname")?_post("surname"):$user['SurName']  ))  , $form);
                $form = str_replace("%email%", str_replace("\"", "&amp;quot;", (_post("email")?_post("email"):$user['Email']  ))  , $form);
                $form = str_replace("%phone%", str_replace("\"", "&amp;quot;", (_post("phone")?_post("phone"):$user['phone']  ))  , $form);
                $form = str_replace("%address%", str_replace("\"", "&amp;quot;", (_post("address")?_post("address"):$user['address']  )), $form);
                $form = str_replace("%city%", str_replace("\"", "&amp;quot;", (_post("city")?_post("city"):$user['city']  )), $form);
                $form = str_replace("%carType%", str_replace("\"", "&amp;quot;", (_post("carType")?_post("carType"):$user['carType']  )), $form);
				$form = str_replace("%subscribe%", ((_post("subscribe")?_post("subscribe"):$user['subscribe'])==1 ?"checked":"" ), $form);
				if((_post("sex")?_post("sex"):$user['sex'])== 'm') {
					$male = "checked";
					$female = "";
				} else {
					$male = "";
					$female = "checked";
				}
				$form = str_replace("%male%", $male, $form);
				$form = str_replace("%female%", $female, $form);
				
				$form = str_replace("%sel_".$user['carID']."%", "selected=\"selected\"", $form);
				for($i = 0; $i< 24; $i++) {
					$form = str_replace("%sel_".$i."%", "", $form);
				}

                return $form;
            }
            else
            {
                return $msg;
            }

        }
		
		function getUserData($userID) 
		{
			$user = Array();
			$query = "SELECT `userID`, `Login`, `Password`, `FirstName`, `LastName`, `SurName`, `Email`, `cardID`, `BirthDate`, `LockDate`, `SessionTimeout`, `MustChangePsw`, `NextPswChangeDate`, `DiskQuota`, `uDeleted`, `LoginDate`, `sex`, `phone`, `region`, `city`, `address`, `carID`, `carType`, `subscribe` FROM `pm_users` WHERE userID = '".$userID."'";
			$result = mysql_query($query);
			$user = mysql_fetch_assoc($result);

			return $user;
		}

        function subscribeBlock()
        {
			global $authenticationMgr;

            $tpl = "
				<div class=\"blok\">
				<form action=\"/profile/\" method=\"post\">
				<div>
				<p>%msg%</p>
				<input name=\"subscribe\" type=\"hidden\" value=\"%subscribe%\" />
				<input name=\"email\" type=\"text\" class=\"input01\" value=\"%email%\" onblur=\"writeVal(this, '%email%'); return false;\" onfocus=\"clearVal(this, '%email%'); return false;\" /><input type=\"image\" src=\"/images/butt_ok.gif\" class=\"ok_butt\" alt=\"ok\"  />
				</div>
				</form>
				</div>
				";
			$user = $this->getUserData($authenticationMgr->getUserID());
			
			if($user['subscribe'] && $user['userID']>1) {
				$tpl = str_replace('%msg%', 'Отписаться от e-mail рассылку предложений по <strong>распродаже</strong>:&nbsp;', $tpl);
				$tpl = str_replace('%subscribe%', 0, $tpl);
				$tpl = str_replace('%email%', $vote['email'], $tpl);		
			} else {
				$tpl = str_replace('%msg%', 'Подписаться на e-mail рассылку предложений по <strong>распродаже</strong>:&nbsp;', $tpl);
				$tpl = str_replace('%subscribe%', 1, $tpl);
				$tpl = str_replace('%email%', (!empty($vote['email'])?$vote['email']:'E-mail'), $tpl);
			}
			return $tpl;
        }


    }
?>

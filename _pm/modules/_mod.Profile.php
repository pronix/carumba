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
                header("Location: /registration?msg=".$msg."&email="._post("email"));
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
                        "car", "car_type");
                        $names = array("Имя", "Фамилия", "Отчество", "E-mail", "Контактный телефон", "Адрес доставки", 
                        "Ваш автомобиль", "Марка автомобиля");

                        for ($i=0; $i < count($vals);$i++)
                        {
                            if ($vals[$i] != "car_type")
                            {
                                if (_post($vals[$i]) == "")
                                    $msg .= "Заполните, пожалуйста, поле `$names[$i]`.<br>";
                            }
                            else
                            {
                                $pcar = _post("car");
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
                                $body = str_replace("%car_type%", _post("car_type"), $body);
                                
                                mail(_post("email"), $subj, $body, "From: info@carumba.ru\r\n");
        
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
                    $msg .= "Заполните, пожалуйста, поле &quot;Логин&quot;.<br>";
                }
            }
            
            if ($doUpdate == 0)
            {
                $js = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Register/" . "reg.js");
                $form = <<<FORM
<TABLE class="" cellSpacing=0 cellPadding=0 width="100%" border=0>
<TBODY>
<TR>
<TD class="" width=8><IMG height=8 src="/images/pix.gif" width=8></TD>
<TD class="" vAlign=top>
<TABLE class="" cellSpacing=1 cellPadding=5 width="100%" bgColor=#dcdde0 border=0>
<TBODY>
<TR bgColor=#ffffff>
<TD class="" vAlign=top width="50%">
<TABLE class="" cellSpacing=0 cellPadding=10 width="100%" border=0>
<TBODY>
<TR vAlign=top>
<TD class="" width=65><IMG height=85 alt="Полезная информация" src="/images/minime.gif" width=65 align=absMiddle border=0></TD>
<TD class="">
<P><STRONG>Внимание:</STRONG></P>
<UL>
<LI>После отправки анкеты, на Ваш e-mail будет отослано письмо</LI>
<LI>Указанная вами марка автомобиля поможет улучшить качество получаемой Вами информации о распродаже</LI>
<LI>Все поля данной анкеты обязательны для заполнения </LI></UL></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD>
<TD class="" width=8><IMG height=8 src="/images/pix.gif" width=8></TD></TR></TBODY></TABLE><BR>%MSG% 
<TABLE class="" cellSpacing=0 cellPadding=0 width="100%" border=0>
<FORM onsubmit="return checkReg();" method=post><INPUT type=hidden value=doUpdate name=action> 
<TBODY>
<TR>
<TD class="" width=8><IMG height=8 src="/images/pix.gif" width=8></TD>
<TD class="" vAlign=top>
<TABLE class="" cellSpacing=0 cellPadding=0 width="100%" bgColor=#f2f2f2 border=0>
<TBODY>
<TR>
<TD class="" width=10 height=10><IMG height=10 src="/images/t_01.gif" width=10></TD>
<TD class="" background=/images/tb_01.gif height=10><IMG height=10 src="/images/pix.gif" width=10></TD>
<TD class="" width=10 height=10><IMG height=10 src="/images/t_02.gif" width=10></TD></TR>
<TR>
<TD class="" width=10 background=/images/tb_02.gif><IMG height=10 src="/images/pix.gif" width=10></TD>
<TD class="" vAlign=top>
<TABLE class="" cellSpacing=1 cellPadding=5 bgColor=#cccccc border=0>
<TBODY>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Логин:</TD>
<TD class=""><INPUT class=input03 value=%login% name=login></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Пароль:</TD>
<TD class=""><INPUT class=input03 type=password value=%psw% name=psw></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Пароль повторно:</TD>
<TD class=""><INPUT class=input03 type=password value=%psw2% name=psw2></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>E-mail:</TD>
<TD class=""><INPUT class=input03 value=%email% name=email></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Фамилия:</TD>
<TD class=""><INPUT class=input03 value=%lastname% name=lastname></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Имя:</TD>
<TD class=""><INPUT class=input03 value=%firstname% name=firstname></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Отчество:</TD>
<TD class=""><INPUT class=input03 value=%surname% name=surname></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Ваш пол:</TD>
<TD class=""><INPUT type=radio %male% value=m name=sex> Мужской <INPUT type=radio %female% value=f name=sex> Женский </TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Контактный телефон:</TD>
<TD class=""><INPUT class=input03 value=%phone% name=phone></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Регион:</TD>
<TD class=""><SELECT class=input03 name=region> <OPTION value=Санкт-Петербург selected>Санкт-Петербург</OPTION></SELECT></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Город:</TD>
<TD class=""><SELECT class=input03 name=city> <OPTION value=Санкт-Петербург selected>Санкт-Петербург</OPTION></SELECT></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Адрес доставки:</TD>
<TD class=""><TEXTAREA class=input05 name=address>%address%</TEXTAREA></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Ваш автомобиль:</TD>
<TD class="" bgColor=#f2f2f2>
<SELECT class=input03 name=car> 
<OPTION value="">--------------
<OPTION value=1>ВАЗ 2101</OPTION> 
<OPTION value=2>ВАЗ 2102</OPTION> 
<OPTION value=3>ВАЗ 2103</OPTION> 
<OPTION value=4>ВАЗ 2104</OPTION> 
<OPTION value=5>ВАЗ 2105</OPTION> 
<OPTION value=6>ВАЗ 2106</OPTION> 
<OPTION value=7>ВАЗ 2107</OPTION> 
<OPTION value=8>ВАЗ 2108</OPTION> 
<OPTION value=9>ВАЗ 2109</OPTION> 
<OPTION value=10>ВАЗ 21099</OPTION> 
<OPTION value=11>ВАЗ 2110</OPTION> 
<OPTION value=12>ВАЗ 2112</OPTION>
<OPTION value=13>Ока</OPTION> 
<OPTION value=14>Нива</OPTION>
<OPTION value="">--------------
<OPTION value=16>Honda</OPTION>
<OPTION value=17>Infiniti</OPTION>
<OPTION value=18>Lexus</OPTION>
<OPTION value=19>Mazda</OPTION>
<OPTION value=20>Mitsubishi</OPTION>
<OPTION value=21>Nissan</OPTION>
<OPTION value=22>Subaru</OPTION>
<OPTION value=23>Toyota</OPTION>
<OPTION value="">--------------</OPTION>
<OPTION value=0>Другой</OPTION>
</SELECT> 
</TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Модель автомобиля<br>(заполняется, если указана иномарка либо Ваш автомобиль не входит в список):</TD>
<TD class="" bgColor=#f2f2f2><input type="text" class="input03" name="car_type" value="%car_type%"></TD></TR>
<TR bgColor=#f2f2f2>
<TD class="" align=right width=160>Подписаться на e-mail рассылку предложений по распродаже:</TD>
<TD class=""><INPUT type=checkbox %subscribe% value=1 name=subscribe></TD></TR></TBODY></TABLE><BR>
<TABLE class="" height=15 cellSpacing=0 cellPadding=0 width=116 border=0>
<TBODY>
<TR>
<TD class="" vAlign=top bgColor="#676971" height=15><INPUT type=image src="images/reg.gif"></TD></TR></TBODY></TABLE><BR></TD>
<TD class="" width=10 background=/images/tb_03.gif><IMG height=10 src="/images/pix.gif" width=10></TD></TR>
<TR>
<TD class="" width=10 height=10><IMG height=10 src="/images/t_03.gif" width=10></TD>
<TD class="" background=/images/tb_04.gif height=10><IMG height=10 src="/images/pix.gif" width=10></TD>
<TD class="" width=10 height=10><IMG height=10 src="/images/t_04.gif" width=10></TD></TR></TBODY></TABLE></TD>
<TD class="" width=8><IMG height=8 src="/images/pix.gif" width=8></TD></TR></TBODY></FORM></TABLE>
FORM;
                
                if ($msg)
                    $msg = "<div style=\"color: red; text-align: center\">$msg<br><br></div>";
                
				$user = $this->getUserData($authenticationMgr->getUserID());
				

                $form = str_replace("%MSG%", $msg, $form);
                $form = str_replace("%login%", "\"" . str_replace("\"", "&quot;", (_post("login")?_post("login"):$user['Login']  )   ) . "\"" , $form);
                $form = str_replace("%psw%", "\"" . str_replace("\"", "&quot;", (_post("psw")?_post("psw"):""  )) . "\"" , $form);
                $form = str_replace("%psw2%", "\"" . str_replace("\"", "&quot;", (_post("psw2")?_post("psw2"):""  )) . "\"" , $form);
                $form = str_replace("%firstname%", "\"" . str_replace("\"", "&quot;", (_post("firstname")?_post("firstname"):$user['FirstName']  )) . "\"" , $form);
                $form = str_replace("%lastname%", "\"" . str_replace("\"", "&quot;", (_post("lastname")?_post("lastname"):$user['LastName']  )) . "\"" , $form);
                $form = str_replace("%surname%", "\"" . str_replace("\"", "&quot;", (_post("surname")?_post("surname"):$user['SurName']  )) . "\"" , $form);
                $form = str_replace("%email%", "\"" . str_replace("\"", "&quot;", (_post("email")?_post("email"):$user['Email']  )) . "\"" , $form);
                $form = str_replace("%phone%", "\"" . str_replace("\"", "&quot;", (_post("phone")?_post("phone"):$user['phone']  )) . "\"" , $form);
                $form = str_replace("%address%", str_replace("\"", "&quot;", (_post("address")?_post("address"):$user['address']  )), $form);
                $form = str_replace("%car_type%", str_replace("\"", "&quot;", (_post("car_type")?_post("car_type"):$user['carType']  )), $form);
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

                return "<script>$js</script>" . $form;
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
				<form action=\"/profile/\" method=\"POST\">
				%msg%
				<input name=\"subscribe\" type=\"hidden\" value=\"%subscribe%\" />
				<input name=\"email\" type=\"text\" class=\"input01\" value=\"%email%\" /><input type=\"image\" src=\"images/butt_ok.gif\" class=\"ok_butt\" alt=\"ok\"  /><br />
				</form>
				</div>
				";
			$user = $this->getUserData($authenticationMgr->getUserID());
			
			if($user['subscribe'] && $user['userID']>1) {
				$tpl = str_replace("%msg%", "Отписаться от e-mail рассылку предложений по <strong>распродаже</strong>:<br /><br />", $tpl);
				$tpl = str_replace("%subscribe%", 0, $tpl);
				$tpl = str_replace("%email%", $vote['email'], $tpl);		
			} else {
				$tpl = str_replace("%msg%", 'Подписаться на e-mail рассылку предложений по <strong>распродаже</strong>:<br /><br />', $tpl);
				$tpl = str_replace("%subscribe%", 1, $tpl);
				$tpl = str_replace("%email%", ($vote['email']?$vote['email']:"E-mail"), $tpl);
			}
			return $tpl;
        }


    }
?>

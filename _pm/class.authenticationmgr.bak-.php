<?
/*
    Authentication Manager
*/
    class AuthenticationManager
    {
        var $userID;
		/**
		 *  __construct()
		 */
        function AuthenticationManager()
        {
            $this->userID = -1;// undefined
        }

        /**
         * Вернуть идентификатор пользователя
         *
         * @return integer
         */
        function getUserID()
        {
            if ($this->userID == -1)
            {
                $this->userID = 1; //anonymous
                $sessionID = $this->getSessionID();
                
//                trigger_error($sessionID, PM_WARNING);

                if ($sessionID)
                {
                    $q = 'SELECT u.userID FROM pm_sessions s, pm_users u WHERE sessionID=\'' . 
                          mysql_escape_string($sessionID) . '\' AND s.userID=u.userID LIMIT 1';

                    $qr = mysql_query($q);

                    if ($qr)
                    {
                        $count = mysql_num_rows($qr);
//                        trigger_error("Count: $count", PM_WARNING);
                        if ($count == 1)
                        {
                            list($uid) = mysql_fetch_row($qr);
                            $this->userID = $uid;
                        }
                    } else
                        trigger_error("Invaild query while reading session info - " . mysql_error(), PM_FATAL);
                } else {
                    $this->setSessionID($this->generateSessionID());
                }
            }
            
            return $this->userID;
        }
        
        /**
         * Вернет номер группы пользователя
         *
         * @return integer
         */
        function getUserGroup()
        {
            if ( $this->userID == -1 ) {
                $this->userID = $this->getUserID();
            }
            $qr = mysql_query('SELECT `groupID` FROM `pm_users` WHERE `userID`='.$this->userID.' LIMIT 1');
            if ($qr && mysql_num_rows($qr) ) {
                list($groupID) = mysql_fetch_row($qr);
                return $groupID;   
            }
            return 0;
        }

        /**
         * Загрузить юзеру с идентификатором $userID поле $field значением $value
         *
         * @param integer $userID
         * @param string $field
         * @param string $value
         * @return result
         */
        function setUserData($userID,$field,$value)
        {
        	return mysql_query("UPDATE `pm_users` SET `$field`='$value' WHERE `userID`='$userID'");
        }

        /**
         * Взять из БД данные о пользователе по одному из параметров
         *
         * @param integer $userID
         * @param string $login
         * @param string $email
         * @return array
         */
        function getUserData($userID, $login = '', $email = '')
        {
            //if ($userID < 1 && ($login == '' || $email== ''))
                //trigger_error("Invalid userID [$userID] or login [$login]", PM_FATAL);

            if ($login == ''){
                $q = "SELECT * FROM pm_users 
                        LEFT JOIN pm_as_cars ON pm_as_cars.carID = pm_users.carID 
                        LEFT JOIN pm_as_autocreators ON pm_as_autocreators.plantID = pm_as_cars.plantID 
                        WHERE userID=$userID LIMIT 1";
            } elseif($email) {
				$q = "SELECT * FROM pm_users 
				        LEFT JOIN pm_as_cars ON pm_as_cars.carID = pm_users.carID 
				        LEFT JOIN pm_as_autocreators ON pm_as_autocreators.plantID = pm_as_cars.plantID 
				        WHERE Email='$email' LIMIT 1";
            } else {
                $q = "SELECT * FROM pm_users 
                        LEFT JOIN pm_as_cars ON pm_as_cars.carID = pm_users.carID 
                        LEFT JOIN pm_as_autocreators ON pm_as_autocreators.plantID = pm_as_cars.plantID 
                        WHERE login='$login' LIMIT 1";
			}
			//echo $q.'<br>';
            $qr = mysql_query($q);

            if (!$qr)
                trigger_error("Invaid query: " . mysql_error(), PM_FATAL);

            if (mysql_num_rows($qr) != 1)
                return array();

            return mysql_fetch_assoc($qr);
        }

        function authenticate()
        {
            $q = "SELECT userID FROM pm_users WHERE login=\"" . mysql_escape_string(_post('login')) . 
                 "\" AND Password = \""  . mysql_escape_string(md5(_post('psw'))) . "\" AND uDeleted=0 AND isUserGroup=0";
            
            $qr = mysql_query($q);

            if (!$qr)
                trigger_error("Invaild query while reading user info - " . mysql_error(), PM_FATAL);

            $count = mysql_num_rows($qr);

            if ($count == 1)
            {
                list ($userID) = mysql_fetch_row($qr);
                if ($userID)
                {
//                    trigger_error("Auth userID: $userID", PM_WARNING);
                    $sessionID = _cookie("sessionID");
//                    trigger_error("Auth sessionID: $sessionID", PM_WARNING);
                    
                    if (!$sessionID)
                        $sessionID = $this->generateSessionID();

                    setcookie("sessionID", $sessionID, time() + (30 * 24 * 60 * 60), "/"); //expire in a month
                    mysql_query("REPLACE INTO pm_sessions (sessionID, userID, CreateTime) VALUES(\"$sessionID\", $userID, NOW())");
                    mysql_query("UPDATE pm_users SET LoginDate=NOW() WHERE userID=$userID");
                
                    $this->userID = $userID;
                    return true;
                }
            }   
            
            return false;
        }
        
        function endSession()
        {
            $sessionID = _cookie("sessionID");

            if ($sessionID)
            {
                $q = "UPDATE pm_sessions SET userID=1 WHERE sessionID=\"$sessionID\"";
                mysql_query($q);
            }
        }

        function generateSessionID()
        {
            srand();

            $cv = "";
            for ($ii=0;$ii<32;$ii++)
            {
                $rnd = rand(0,9);
                if ($rnd < 3)
                    $cv .= chr(rand(ord("a"),ord("z")));
                elseif ($rnd < 7)
                    $cv .= chr(rand(ord("A"),ord("Z")));
                else
                    $cv .= chr(rand(ord("0"),ord("9")));
            }

            return $cv;
        }

        /**
         * Возвращает из COOKIE идентификатор сессии
         *
         * @return string
         */
        function getSessionID()
        {
            return _cookie('sessionID');
        }

        function setSessionID($sessionID)
        {
            if (!$sessionID)
                trigger_error("No sessionID provided!", PM_FATAL);

            setcookie("sessionID", $sessionID, time() + (30 * 24 * 60 * 60),"/"); //expire in a month
            $qr = mysql_query("REPLACE INTO pm_sessions (sessionID, userID, CreateTime) VALUES('$sessionID', 1, NOW())");
            
            if (!$qr)
                trigger_error(mysql_error(), PM_FATAL);
        }
    }
?>
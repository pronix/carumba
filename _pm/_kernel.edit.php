<?

function processAdminCommand($cmd, $pageID)
{
    global $modulesMgr, $structureMgr, $authenticationMgr, $permissionsMgr, $cacheMgr, $templatesMgr;
    
    $res = "";
    
    $userGroupID = $authenticationMgr->getUserGroup();

    switch ($cmd)
    {
        case "editPage":
        {
            $res .= navi($pageID, false);
            $res .= editPage($pageID);
            break;
        }

        case "editDict":
        {
            $res .= editDict();
            break;
        }

        case "addDictValue":
        {
            $res .= addDictValue();
            break;
        }

        case "updateDictValues":
        {
            $res .= updateDictValues();
            break;
        }

        case "delDictValue":
        {
            $res .= delDictValue();
            break;
        }

        case "updatePage":
        {
            updatePage();
            break; //we always exit in prev. function
        }

        case "delPage":
        {
            if ($userGroupID == 5)
                delPage(_get("pageID"));
            break; //we always exit in prev. function
        }

        case "addPage":
        {
            $res .= navi(_get("pageID"), true);
            $res .= editPage(-1);
            break;
        }

        case "updChildren":
        {
            //first we should handle group movements
            $cmds = _postByPattern("/cmd\d+/");
            
            $cmdsFound = false;
            if (count($cmds) > 0)
            {
                foreach ($cmds as $cmdkey => $curcmd)
                {
                    if ($curcmd)
                    {
                        $cmdsFound = true;
                        switch($curcmd)
                        {
                            case "movePages":
                            {
                                if ($userGroupID == 5)
                                    movePages($pageID);
                                break;
                            }
                            case "moveToTheTop":
                            {
                                if ($userGroupID == 5)
                                    moveToTheTop($pageID);
                                break;
                            }
                            case "moveToTheBottom":
                            {
                                if ($userGroupID == 5)
                                    moveToTheBottom($pageID);
                                break;
                            }
                            case "delPages":
                            {
                                if ($userGroupID == 5)
                                    delPages($pageID);
                                break;
                            }
                        }
                        break;
                    }
                }

            }
            
            if (!$cmdsFound) //no cmds found, just update children
                updateChildren();

            break;
        }


        case "modparams":
        {
            global $modulesMgr;
            $ms = $modulesMgr->modules();
            $dicts = array();

            foreach ($ms as $mname => $m)
            {
                $d = GetCfg($mname . ".dictionaries");
                if ($d)
                    $dicts[$mname] = $d;
            }

            $res .= "<h4>Справочники</h4>";

            foreach($dicts as $mname => $dict)
            {
                $res .= GetCfg($mname . ".ModuleName") . ": ";
                
                $r1 = "";
                foreach ($dict as $dname => $d)
                {
                    if ($r1)
                        $r1 .= ", ";

                    $r1 .= "<a href=/admin/?cmd=editDict&moduleName=$mname&dict=$dname>$d[0]</a>";
                }

                $res .= "$r1<br>\n";
            }

            break;
        }

        case "cards" : {
        	$act = _get('act');
			$cardAdmin = new CardsAdminHandler();
        	if(isset($act) && $act!=''){
        		$res .= $cardAdmin->handleRequest($act);
        	}else{
        		$res .= $cardAdmin->getList();
        	}

        	break;
        }

		case "users" : {
			
			$handler = new UsersAdminHandler();
			$res .= $handler->getContent();
			if($handler->location!=''){
				header('Location: '.$handler->location);
			}
			break;
		}

		case "mails" : {
			$handler = new MailsAdminHandler();

			$act = _get('act');
			if($act == "send") {
				$handler->sendToMail();
			} else {
				$res .= $handler->getContent();
			}
			break;
		}
		
		case "banner" : {
			$handler = new BannerAdminHandler();

			$act = _get('act');
			$banID = _get('banID');
			switch($act) {
				case "save" : $handler->saveBanner(); break;
				case "delete": $handler->deleteBanner($banID); break;
				default : $res .= $handler->getContent($banID); break;
			}
			
			break;
		}
		case "vote" : {
			$handler = new VoteAdminHandler();

			$act = _get('act');
			$qID = _get('qID');
			switch($act) {
				case "save" : $handler->saveVote(); break;
				case "delete": $handler->deleteVote($qID); break;
				default : $res .= $handler->getContent($qID); break;
			}
			
			break;
		}
		
        default:
        {
            $res .= navi($pageID, false);
            $res .= "Unknown command: $cmd<br><br>\n";
        }

    }

    return $res;
}


function delPages($pageID)
{
	global $structureMgr;

    if (!$pageID)
        trigger_error("PageID must be specified for deleting", PM_FATAL);

    $chk = _postByPattern("/chk\d+/");

    if (count($chk) > 0) {
		//echo 'Deleting<br>';
		foreach($chk as $key=>$value) {
			delOnePage($value);
		}
    } 

	header("Location: /admin?pageID=$pageID");
    exit;
}

function movePages($pageID)
{
    $chk = _postByPattern("/chk\d+/");
	$destPageId = _post("branchName");
    if (count($chk) > 0) {
		//echo 'Moving<br>';
		foreach($chk as $key=>$value) {
			movePageToDestPage($value, $destPageId);
		}
        //print_r($chk);
	}

	header("Location: /admin?pageID=$destPageId");
    exit;
}

function movePageToDestPage($pageID, $destPageId)
{
	if (!$destPageId)
        trigger_error("destPageId must be specified for moving", PM_FATAL);
	if (!$pageID)
        trigger_error("PageID must be specified for moving", PM_FATAL);
	
	$query = "SELECT accID FROM pm_as_parts WHERE sID='".$pageID."'";
	$res = mysql_query($query);
	if(mysql_num_rows($res)) {
		$row = mysql_fetch_assoc($res);
		$accID = $row['accID'];
		$query = "SELECT accCatID FROM pm_as_categories WHERE sID='".$destPageId."'";
		$res1 = mysql_query($query);
		$row = mysql_fetch_assoc($res1);
		$accCatID = $row['accCatID'];

		$upQuery = "UPDATE pm_as_parts SET accCatID = '".$accCatID."' WHERE sID='".$pageID."'";
		
		mysql_query($upQuery);

		//$upQuery = "UPDATE pm_as_parts SET accCatID = '".$accCatID."' WHERE sID='".$pageID."'";
	}
	$query = "UPDATE pm_structure SET pms_sID = '".$destPageId."' WHERE sID='".$pageID."'";

	$query = "UPDATE pm_structure SET pms_sID = '".$destPageId."' WHERE sID='".$pageID."'";
	

	if (!mysql_query($query))
    {
        trigger_error("Error while moving page [$pageID] - " . mysql_error(), PM_FATAL);
    }

}

function moveToTheTop($pageID)
{
	global $structureMgr;
	//echo 'ep';
    if (!$pageID)
        trigger_error("PageID must be specified for deleting", PM_FATAL);

    $chk = _postByPattern("/chk\d+/");
	//echo count($chk).'<hr>';
    if (count($chk) > 0) {
        //echo 'ToTop<br>';
		foreach($chk as $key=>$value) {
			//echo 'We moving '.$key.' and '.$value.'to the Top<br>';
			movePage($value, "top");
		}
		//print_r($chk);

	}

	header("Location: /admin?pageID=$pageID");
}

function moveToTheBottom($pageID)
{
	global $structureMgr;

    if (!$pageID)
        trigger_error("PageID must be specified for moving", PM_FATAL);


    $chk = _postByPattern("/chk\d+/");

    if (count($chk) > 0) {
        //echo 'ToBottom<br>';
		foreach($chk as $key=>$value) {
			//echo 'We moving '.$key.' and '.$value.'to the Bottom<br>';
			movePage($value, "bottom");
		}
		//print_r($chk);
	}

	header("Location: /admin?pageID=$pageID");
}

function movePage($pageID, $place) 
{
	global $structureMgr;

	if (!$pageID)
        trigger_error("PageID must be specified for moving", PM_FATAL);

	//echo 'tut<br>';
	$orderNum = 0;
	switch($place) {
		case "top": $orderNum = getFirstIdInBranch($structureMgr->getParentPageID($pageID))-1; break;
		case "bottom": $orderNum = getLastIdInBranch($structureMgr->getParentPageID($pageID))+1; break;
	}
	
	$query = "UPDATE pm_structure SET OrderNumber = '".$orderNum."' WHERE sID='".$pageID."'";
	//echo $query;
	if (!mysql_query($query))
    {
        trigger_error("Error while moving page [$pageID] - " . mysql_error(), PM_FATAL);
    }
}

function getFirstIdInBranch($pms_sID)
{
	$query = "SELECT OrderNumber FROM pm_structure WHERE pms_sID='".$pms_sID."' ORDER BY OrderNumber Limit 1";
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);

	return $row['OrderNumber'];

}

function getLastIdInBranch($pms_sID)
{
	$query = "SELECT OrderNumber FROM pm_structure WHERE pms_sID='".$pms_sID."' ORDER BY OrderNumber desc Limit 1";
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);

	return $row['OrderNumber'];
}

function delOnePage($pageID)
{
    $q = "DELETE FROM pm_structure WHERE sID = $pageID";    

    if (!mysql_query($q))
    {
        trigger_error("Error while deleting page [$pageID] - " . mysql_error(), PM_FATAL);
    }
}

function delPage($pageID)
{
	global $structureMgr;

    if (!$pageID)
        trigger_error("PageID must be specified for deleting", PM_FATAL);

    $parent = $structureMgr->getParentPageID($pageID);

    if (!$parent)
        trigger_error("Invalid page [$pageID].", PM_FATAL);

    $default = $structureMgr->getDefaultPageID();

    if ($default == $pageID)
    {
        trigger_error("Default page will be removed. Please assign some other page as default for this site.", PM_WARNING);
        $structureMgr->unsetDefaultPageID();
    }


    delOnePage($pageID);

    header("Location: /admin/?pageID=$parent");
    exit;
}

function updatePage()
{
    global $structureMgr, $modulesMgr, $authenticationMgr;

    $pageID = _post("pageID");
    $pms_sID = _post("pms_sID");

    $isHidden = _post("isHidden");

    if ($isHidden == "")
        $isHidden = "0";
    else
        $isHidden = "1";

    $ShortTitle = _post("ShortTitle");
    $Title = _post("Title");
    $URLName = _post("URLName");
    $Content = _post("Content");
    $tplID = _post("tplID");

    $MetaDesc = _post("MetaDesc");
    $MetaKeywords = _post("MetaKeywords");

    $ModuleName = _post("ModuleName");
    $DataType = _post("DataType");

    //HERE WE MUST !!! CHECK FOR BAD VALUES AND REDIRECT TO editPage() IF THERE WERE SOME ERRORS

    
    if ($pageID == -1)
    {
        //mysql_query("START TRANSACTION");

        //INSERT IS HERE
        list ($orderNumber) = mysql_fetch_row(mysql_query("SELECT MAX(OrderNumber) + 1 FROM pm_structure WHERE pms_sID = " . prepareVar($pms_sID)));

        if (!$orderNumber)
            $orderNumber = 1;


        $q = sprintf("INSERT INTO pm_structure 
        (isHidden, ShortTitle, Title, URLName, Content, MetaDesc, MetaKeywords, userID, tplID, CreateDate, ModuleName, DataType, OrderNumber, pms_sID ) 
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), %s, %s, %s, %s)", $isHidden,
        prepareVar($ShortTitle), prepareVar($Title), prepareVar($URLName), prepareVar($Content), 
        prepareVar($MetaDesc), prepareVar($MetaKeywords), $authenticationMgr->getUserID(), prepareVar($tplID),
        prepareVar($ModuleName), prepareVar($DataType), $orderNumber, $pms_sID
        );

        $qr = mysql_query($q);

        if (!$qr)
        //mysql_query("ROLLBACK");
            trigger_error("Error adding item [$q] - " . mysql_error(), PM_FATAL);

        list ($pageID) = mysql_fetch_row(mysql_query("SELECT MAX(sID) FROM pm_structure"));

        if (!$modulesMgr->execute($ModuleName, "updateSpecificData", array($pageID, true /*create new*/), false))
        {
            //mysql_query("ROLLBACK");
            mysql_query("DELETE FROM pm_structure WHERE sID=$pageID");
            trigger_error("Specific module data saving failed, incomplete page removed.", PM_WARNING);

            $message = "Ошибка добавления. Подробности в журнале.";
            //must call here editPage() and exit;

        }
        else
            $message = "";
        
        if ($message)
            $message = "&message=" . urlencode($message);
 
        //mysql_query("COMMIT");

        header("Location: /admin/?pageID=$pms_sID" . $message);
        exit;
    }
    else
    {

        //UPDATE IS HERE
        $q = sprintf("UPDATE pm_structure 
        SET isHidden=%s, ShortTitle=%s, Title=%s, URLName=%s, Content=%s, MetaDesc=%s, MetaKeywords=%s, tplID=%s, CreateDate=NOW()
        WHERE sID=%s", $isHidden,
        prepareVar($ShortTitle), prepareVar($Title), prepareVar($URLName), prepareVar($Content), 
        prepareVar($MetaDesc), prepareVar($MetaKeywords), prepareVar($tplID), prepareVar($pageID)
        );

        //mysql_query("START TRANSACTION");
        $qr = mysql_query($q);

        if (!$qr)
            //mysql_query("ROLLBACK");
            trigger_error("Error updating item [$q] - " . mysql_error(), PM_FATAL);

        $md = $structureMgr->getMetaData($pageID);
        
        //here we must check for errors of updating and do the re-editing of page!!!
        $modulesMgr->execute($md["ModuleName"], "updateSpecificData", array($md["sID"]), false);

        //mysql_query("COMMIT");
        header("Location: /admin/?pageID=$md[pms_sID]");
        exit;
    }

}


function updateChildren()
{
    global $structureMgr, $modulesMgr;

    $ids = _postByPattern("/item\\d+/");
    $pageID = _post("pageID");

    foreach($ids as $k => $v)
    {
        $isHidden = _post("h$v");
        if ($isHidden)
            $isHidden = "1";
        else
            $isHidden = "0";


        $md = $structureMgr->getMetaData($v);

        if ($md["isHidden"] != $isHidden)
        {
            $q = sprintf("UPDATE pm_structure 
            SET isHidden=%s, CreateDate=NOW()
            WHERE sID=%s", $isHidden, prepareVar($v));

            //mysql_query("START TRANSACTION");
            $qr = mysql_query($q);

            if (!$qr)
                //mysql_query("ROLLBACK");
                trigger_error("Error updating item [$q] - " . mysql_error(), PM_FATAL);

            $md = $structureMgr->getMetaData($v);
        }
        //here we must check for errors of updating
        $modulesMgr->execute($md["ModuleName"], "updateAdditionalColumns", array($md["sID"]), false);
    }
    
    //mysql_query("COMMIT");
    header("Location: /admin/?pageID=$pageID");
    exit;
}


function renderProperties(&$eVars)
{
    $specificLines = "";

    foreach($eVars as $k => $v)
    {
                                   
        $specificLines .= "<tr><td valign=top>$v[0]</td><td>"; 
        switch ($v[1])
        {
            case "fieldset":
            {
                $specificLines .= "<fieldset>" . renderProperties(&$v[2]) . "</fieldset>";
                break;
            }
            
            case "text":
            {
                $specificLines .= "<input type=text name=\"$k\" value=\"$v[3]\" size=\"$v[2]\">";
                break;
            }
            
            case "checkbox":
            {
                $specificLines .= "<input type=checkbox name=\"$k\" value=\"1\"";
                if ($v[2])
                    $specificLines .= " checked";
                $specificLines .= ">";
                break;
            }
            case "dropdown":
            {
                $specificLines .= "<select name=\"$k\">";
                foreach($v[3] as $pnum => $pname)
                {
                    $specificLines .= "<option value=\"$pnum\"";
                    if ($pnum == $v[2])
                        $specificLines .= " selected";
                    $specificLines .= ">$pname</option>\n";
                }
                $specificLines .= "</select>";
                
                //for adding new values into dictionaries on-the-fly
                if (isset($v[4]))
                    $specificLines .= " &nbsp;&nbsp;$v[4]: <input type=\"text\" name=\"${k}_new\" size=\"$v[5]\">\n";

                break;

            }
            case "checkbox_list":
            {
                $nextBR = 0;
                
                $chLines = "";

                foreach($v[3] as $carID => $carModel)
                {
                    if ($nextBR == $v[4])
                    {
                        $chLines .= "</tr><tr>";
                        $nextBR = 1;
                    }
                    else
                    {
                        $cl = "";
                        $nextBR++;
                    }
                    $chLines .= "<td><input name=car_$carID value=1 type=checkbox";
                    if (isset($v[2][$carID]))
                        $chLines .= " checked";
                    $chLines .= ">$carModel</td>\n";
                }
                if ($chLines)
                    $specificLines .= "<table><tr>$chLines</tr></table>\n";
                break;
            }
        }
        $specificLines .=  "</td></tr>\n";
    }

    if ($specificLines)
        return "<table>\n" . $specificLines . "</table>\n";

    return "";
}


function editDict()
{
    $module = _get("moduleName");
    $ds = GetCfg($module . ".dictionaries");
    //print_r($ds);
    $res = "";
    
    if ($ds)
    {
        $dict = _get("dict");
        if (isset($ds[$dict]))
        {

            $d = $ds[$dict];
            $dname = $d[0];
            $dcols = $d[1];
            $res .= "<h4>Справочник модуля &laquo;" . GetCfg($module . ".ModuleName") . "&raquo; - $dname</h4>";

            //=================ADD BLOCK
            $res .= "<form method=post action=\"/admin/\">
            <input type=hidden name=cmd value=addDictValue>
            <input type=hidden name=moduleName value=$module>
            <input type=hidden name=dict value=$dict>
            <table cellpadding=4 cellspacing=0 border=0 style=\"border: double #A0A0A0;\">\n<tr class=\"top\">\n";
            
            $q = "";
            foreach ($dcols as $colName => $dcol)
            {
               $res .= "<td>" . $colName . "</td>";
               if ($q)
                  $q .= ", ";
               $q .= $dcol[0];
               $cols[] = $dcol;
            }

            $res .= "<td>&nbsp;</td>";
            $res .= "</tr>\n";

            $colcnt = count($cols) + 1;

            {
                $res .= "<tr class=sline1>";

                for ($i = 0; $i < count($cols); $i++)
                {
                    $res .= "<td>";
                    if ($cols[$i][2] == 1)
                    {
                        $res .= "<input type=text size=" . $cols[$i][1] . " value=\"\" name=" . $cols[$i][0] . ">";
                    }
                    else
                    if ($cols[$i][2] == 2)
                    {
                        $res .= "<textarea cols=" . $cols[$i][1] . " rows=3 name=" . $cols[$i][0] . "></textarea>";
                    }                    
                    else
                    {
                        $res .= "&nbsp;&nbsp;&nbsp;";
                    }
                    $res .= "</td>";

                }
                $res .= "<td>&nbsp;</td>";
                $res .= "</tr>\n";
            }
            
            $addRow = "<tr><td colspan=$colcnt><input type=submit value=\"Добавить\"><br><br></td></tr>";
            
            $res .= "$addRow</form>";



            //=================EDIT BLOCK
            $res .= "<form method=post action=\"/admin/\">
            <input type=hidden name=cmd value=updateDictValues>
            <input type=hidden name=moduleName value=$module>
            <input type=hidden name=dict value=$dict>
            <tr class=top>\n";

            foreach ($dcols as $colName => $dcol)
            {
               $res .= "<td>" . $colName . "</td>";
            }
            $res .= "<td>Удалить</td>";
            $res .= "</tr>\n";

            $saveRow = "<tr><td colspan=$colcnt><input type=submit value=\"Сохранить\"></td></tr>";


            $q = "SELECT $q FROM $dict";

            if ($d[2])
                $q .= " ORDER BY $d[2]";

            $qr = mysql_query($q);
            
            if (!$qr)
                trigger_error("Error getting dictionary values [$q] - " . mysql_error(), PM_FATAL);

            $rws = 0;
            $trline = 1;

            while (false !== ($row = mysql_fetch_row($qr)))
            {
                $res .= "\t<tr class=sline$trline>";

                for ($i = 0; $i < count($cols); $i++)
                {
                    $res .= "\t\t<td>";
                    $row[$i] = str_replace("\"", "&quot;", $row[$i]);
                    if ($cols[$i][2] == 1)
                    {
                        $res .= "<input type=text size=" . $cols[$i][1] . " value=\"$row[$i]\" name=" . $cols[$i][0] . "_" . $row[0] . ">";
                    }else
                    if ($cols[$i][2] == 2)
                    {
                        $res .= "<textarea cols=" . $cols[$i][1] . " rows=3 name=" . $cols[$i][0] . "_" . $row[0] . ">$row[$i]</textarea>";
                    }   
                    else
                    {
                        $res .= $row[$i] . "<input type=hidden name=" . $cols[$i][0] . "_" . $row[0] . " value=$row[$i]>";
                    }
                    $res .= "</td>\n";

                }

                $res .= "<td><a href=/admin/?cmd=delDictValue&moduleName=$module&dict=$dict&id=$row[0] onclick=\"return confirm('Действительно удалить?')\">удалить</a></td>";
                $res .= "</tr>\n";
                $rws++;
                if ($rws == 20)
                {
                    $res .= $saveRow;
                    $rws = 0;
                }
                if ($trline == 1)
                    $trline = 2;
                else
                    $trline = 1;

            }

            $res .= "$saveRow</table></form>";
        }
        else
            trigger_error("Undefined dictionary [$dict] for module [$module]", PM_FATAL);
    }
    else
        trigger_error("Undefined module [$module]", PM_FATAL);

    return $res;
}

function addDictValue()
{
    $module = _post("moduleName");
    $ds = GetCfg($module . ".dictionaries");
    
    $res = "";
    
    if ($ds)
    {
        $dict = _post("dict");
        if (isset($ds[$dict]))
        {
            $d = $ds[$dict];
            $dname = $d[0];
            $dcols = $d[1];

            $q = "";
            $v = "";
            foreach ($dcols as $colName => $dcol)
            {
               if ($dcol[2])
               {
                   if ($q)
                      $q .= ", ";
                   $q .= $dcol[0];
                   
                   if ($v)
                      $v .= ", ";
                   $v .= prepareVar(_post($dcol[0]));
               }
               $cols[] = $dcol;
            }

            $q = "INSERT INTO $dict ($q) VALUES ($v)";
            
            $qr = mysql_query($q);

            if (!$qr)
                trigger_error("Error adding new value into dictionary [$dict] for module [$module]", PM_FATAL);
            
            header("Status: 302 Moved");
            header("Location: /admin/?cmd=editDict&moduleName=$module&dict=$dict");
            exit(0);
        }
        else
            trigger_error("Undefined dictionary [$dict] for module [$module]", PM_FATAL);
    }
    else
        trigger_error("Undefined module [$module]", PM_FATAL);
}

function updateDictValues()
{
    $module = _post("moduleName");
    $ds = GetCfg($module . ".dictionaries");
    
    $res = "";
    
    if ($ds)
    {
        $dict = _post("dict");
        if (isset($ds[$dict]))
        {
            $d = $ds[$dict];
            $dname = $d[0];
            $dcols = $d[1];


            //$q = "DELETE FROM $dict WHERE {$dcols[id][0]}=" . _get("id");
            
            $ids = _postByPattern("/" . $dcols["id"][0]. "_\d+/");

            foreach($ids as $id)
            {
                $vals = "";
                $cl  = "";
                foreach($dcols as $cname => $col)
                {
                    if ($col[2])
                    {
                        if ($vals)
                            $vals .= ", ";

                        $vals .= $col[0] . "=" . prepareVar(_post($col[0] . "_" . $id));
                    }
                }
                $q = "UPDATE $dict SET $vals WHERE " . $dcols["id"][0] . "=$id";
                //print "$q<br>";
                

                $qr = mysql_query($q);

                if (!$qr)
                    trigger_error("Error updating a value in a dictionary [$dict] for module [$module]", PM_FATAL);
                   
            }

            header("Status: 302 Moved");
            header("Location: /admin/?cmd=editDict&moduleName=$module&dict=$dict");
            exit(0);
        }
        else
            trigger_error("Undefined dictionary [$dict] for module [$module]", PM_FATAL);
    }
    else
        trigger_error("Undefined module [$module]", PM_FATAL);
}

function delDictValue()
{
    $module = _get("moduleName");
    $ds = GetCfg($module . ".dictionaries");
    
    $res = "";
    
    if ($ds)
    {
        $dict = _get("dict");
        if (isset($ds[$dict]))
        {
            $d = $ds[$dict];
            $dname = $d[0];
            $dcols = $d[1];

            $q = "DELETE FROM $dict WHERE {$dcols[id][0]}=" . _get("id");
            
            $qr = mysql_query($q);

            if (!$qr)
                trigger_error("Error deleting a value from dictionary [$dict] for module [$module]", PM_FATAL);
            
            header("Status: 302 Moved");
            header("Location: /admin/?cmd=editDict&moduleName=$module&dict=$dict");
            exit(0);
        }
        else
            trigger_error("Undefined dictionary [$dict] for module [$module]", PM_FATAL);
    }
    else
        trigger_error("Undefined module [$module]", PM_FATAL);
}

function editPage($pageID)
{
    global $structureMgr, $modulesMgr, $templatesMgr;
    
    $specificLines = "";

    if ($pageID != -1)
    {
        $md = $structureMgr->getMetaData($pageID);

        $md["ShortTitle"] = str_replace("\"", "&quot;", $md["ShortTitle"]);
        $pageTemplate = "";

        $templates = $templatesMgr->getTemplates();

        $pageTemplate .= "<select name=\"tplID\">";
        foreach($templates as $tID => $tname)
        {
            $pageTemplate .= "<option value=\"$tID\"";
            if ($tID == $md["tplID"]) {
                $pageTemplate .= " selected";
			} else if($tID == 5) {
				$pageTemplate .= " selected";
			}
            $pageTemplate .= ">$tname</option>\n";
        }
        $pageTemplate .= "</select>\n";
        
        $submit = "Обновить";

        $dt = $modulesMgr->execute($md["ModuleName"], "getItemType", array($md["DataType"]), false);
        $idesc = $modulesMgr->execute($md["ModuleName"], "getItemDesc", array($md["DataType"]), false);
        $specDesc = $modulesMgr->execute($md["ModuleName"], "getSpecificBlockDesc", array($md["DataType"]), false);
        
        $eVars = $modulesMgr->execute($md["ModuleName"], "getSpecificDataForEditing", array($md["sID"]), false);



        if ($md["isHidden"])
             $isHidden = "checked";
        else
             $isHidden = "";

        $content = $structureMgr->getData($pageID);
        $header = "Обновление " . $dt[2];
        $title = "Общие сведения";
        $mtags = "Мета-теги";

        $hiddenForAdding = "";
    }
    else
    {
        $md["ShortTitle"] = "";
        $md["Title"] = "";
        $md["URLName"] = "";
        $md["MetaDesc"] = "";
        $md["MetaKeywords"] = "";
        $isHidden = "";
        $content = "";
        $pageTemplate = "";

        $templates = $templatesMgr->getTemplates();

        $tplID = $templatesMgr->getDefaultTplID();

        $pageTemplate .= "<select name=\"tplID\">";
        foreach($templates as $tID => $tname)
        {
            $pageTemplate .= "<option value=\"$tID\"";
            if ($tID == $tplID)
                $pageTemplate .= " selected";
            $pageTemplate .= ">$tname</option>\n";
        }
        $pageTemplate .= "</select>\n";


        $submit = "Добавить";
        
        $dt = $modulesMgr->execute(_var('ModuleName'), 'getItemType', array(_var('DataType')), false);
        $idesc = $modulesMgr->execute(_var('ModuleName'), 'getItemDesc', array(_var('DataType')), false);
        $specDesc = $modulesMgr->execute(_var('ModuleName'), 'getSpecificBlockDesc', array(_var('DataType')), false);
        
        $eVars = $modulesMgr->execute(_var("ModuleName"), "getSpecificDataForEditing", array(-1, _var("DataType"), _var("pageID")), false);

        $header = "Добавление " . $dt[2];
        $title = "Общие сведения";
        $mtags = "Мета-теги";
        $md["pms_sID"] = _var("pageID");
        $hiddenForAdding = "<input type=hidden name=\"ModuleName\" value=\"" . _var("ModuleName") . "\">\n" . 
        "<input type=hidden name=\"DataType\" value=\"" . _var("DataType") . "\">\n";
    }

//    $fromPost = array("ShortTitle", "Title", "URLName", "MetaDesc", "MetaKeywords", "isHidden", "Content");

    $specificLines = renderProperties(&$eVars);


    $active_color = "#DEEBFA";
    $back_color = "#EEFBFF";


    if ($specificLines)
    {
        $spec_link = "
        			<td class=\"tl1\"><a href=# onclick=\"show('specific'); return false;\">$specDesc</a></td>
        ";
        $spec_active = "
                   <td class=\"tl1a\"><strong>$specDesc</strong></td>
        ";
        $specJS = ", \"specific\"";
    }

    else
    {
        $spec_link = "";
        $spec_active = "";
        $specJS = "";
    }


    if ($idesc)
    {
/*         
         $editorDIV = <<<DIV
        	    <div class="tmenulevel_1">
        	    <table cellspacing="0" cellpadding="3"><tr>
        			<td class="tl1"><a href=# onclick="show('maindata'); return false;">$title</a></td>
        			<td class="tl1a"><strong>$idesc</strong></td>
        			<td class="tl1"><a href=# onclick="show('metadata'); return false;">$mtags</a></td>
        			$spec_link
        			</tr></table>
        		</div>
        		<div class="tmenulevel_2">
                    <textarea name="Content" id="Content" cols=80 rows=30>111</textarea>
        		</div>
DIV;
*/
         $editorDIV = <<<DIV
        	    <div class="tmenulevel_1">
        	    <table cellspacing="0" cellpadding="3"><tr>
        			<td class="tl1"><a href=# onclick="show('maindata'); return false;">$title</a></td>
        			<td class="tl1a"><strong>$idesc</strong></td>
        			<td class="tl1"><a href=# onclick="show('metadata'); return false;">$mtags</a></td>
        			$spec_link
        			</tr></table>
        		</div>
        		<div class="tmenulevel_2">
                    <textarea name="Content" id="Content" cols=80 rows=30  class="widgEditor nothing">$content</textarea>
        		</div>
DIV;
         $content = str_replace("\"", "\\\"", $content);
         $content = str_replace("\r\n", "\" + \r\n\"\\n", $content);

//         $editorLink = "initialSetContent();";
         $editorLink = "";

/*
         $editorJS = <<<JS
    <script language="javascript" type="text/javascript" src="/js/tiny_mce/tiny_mce_src.js"></script>
    <script language="javascript" type="text/javascript">
    	tinyMCE.init({
    		theme : "advanced",
    		mode : "exact",
    		cleanup: false,
    		language : "ru",
    		elements : "Content",
    		content_css : "/css/carumba.css",
    		width: "640",
    		height: "300",
    		plugins : "table",
    		relative_urls : false,
    		visual :  false,
    		theme_advanced_buttons3_add_before : "tablecontrols,separator",
    		theme_advanced_toolbar_location : "top",
    		theme_advanced_toolbar_align : "left",
    		theme_advanced_statusbar_location : "bottom",
    		debug : false
    	});

    	var contentWasSet = false;

    	function initialSetContent()
    	{
    	    if (!contentWasSet)
    	    {
                tinyMCE.setContent("$content");
                contentWasSet = true;
            }
    	}

    </script>	
JS;
*/
       $editorJS = <<<JS
<style type="text/css" media="all">
	@import "/js/widg/css/info.css";
	@import "/js/widg/css/main.css";
	@import "/js/widg/css/widgEditor.css";
</style>

<script type="text/javascript" src="/js/widg/widgEditor.js"></script>
<script type="text/javascript">

    	var contentWasSet = false;

    	function initialSetContent()
    	{
    	    if (!contentWasSet)
    	    {
                tinyMCE.setContent("$content");
                contentWasSet = true;
            }
    	}
</script>
JS;
    }
    else
    {
        $editorJS = "";
        $editorDIV = "";
        $editorLink = "";
    }


    $res = <<<EF
    <style>
        .tmenulevel_1 
        { margin: 0px; border: 0; padding: 5px; padding-bottom: 0px; position: relative; }
        .tmenulevel_2
        { margin-top: 0px; margin-left: 0px; border: 0; padding: 10px; padding-top: 8px; background-color: $active_color; position: relative; }
        .tl1a { background-color: $active_color; font-size: 12px; font-weight: bold;}
        .tl1  { font-size: 12px; }
    </style>
    
    <script>
        function hideAll()
        {
            ids = new Array("maindata", "dcontent", "metadata"$specJS);
            for (i=0; i < ids.length; i++)
            {
                d = document.getElementById(ids[i]);
                d.style.display = "none";
            }
        }

        function show(a)
        {
            hideAll();
            d = document.getElementById(a);
            d.style.display = "block";
        }

    </script>

    <div style="border: double #A0A0A0; padding: 5px; margin: 0px; background-color: #ffFFA0">
        <form action="/admin/" method="POST">
            <input type=hidden name=cmd value=updatePage>
            <input type=hidden name=pageID value="$pageID">
            <input type=hidden name=pms_sID value="$md[pms_sID]">
            $hiddenForAdding
            <h4>$header</h4>
            
            <div id="maindata">
        	    <div class="tmenulevel_1">
        	    <table cellspacing="0" cellpadding="4"><tr>
        			<td class="tl1a"><strong>$title</strong></td>
        			<td class="tl1"><a href=# onclick="show('dcontent');$editorLink return false;">$idesc</a></td>
        			<td class="tl1"><a href=# onclick="show('metadata'); return false;">$mtags</a></td>
        			$spec_link
        			</tr></table>
        		</div>
        		<div class="tmenulevel_2">
                    <table>
                        <tr>
                            <td>Заголовок</td><td><input type=text name="ShortTitle" value="$md[ShortTitle]" size=80></td></tr>
                        <tr>
                            <td>URL</td><td><input type=text name="URLName" value="$md[URLName]" size=80></td></tr>
                        <tr>
                            <td>&lt;Title&gt;</td><td><input type=text name="Title" value="$md[Title]" size=80></td></tr>
                        <tr>
                            <td>Шаблон страницы</td><td>$pageTemplate</td></tr>
                        <tr>
                            <td>Скрывать</td><td><input type=checkbox name="isHidden" value="1" $isHidden></td></tr>
                    </table>
        		</div>
	        </div>

            <div id="dcontent" style="display:none;">
                $editorDIV
	        </div>
    $editorJS

            <div id="metadata" style="display:none;">
        	    <div class="tmenulevel_1">
        	    <table cellspacing="0" cellpadding="3"><tr>
        			<td class="tl1"><a href=# onclick="show('maindata'); return false;">$title</a></td>
        			<td class="tl1"><a href=# onclick="show('dcontent');$editorLink return false;">$idesc</a></td>
        			<td class="tl1a"><strong>$mtags</strong></td>
        			$spec_link
        			</tr></table>
        		</div>
        		<div class="tmenulevel_2">
                    <table>
                        <tr>
                            <td>Ключевые слова</td><td><input type=text name="MetaKeywords" value="$md[MetaKeywords]" size=80></td></tr>
                        <tr>
                            <td valign=top>Описание</td><td><textarea name="MetaDesc" cols=60 rows=8>$md[MetaDesc]</textarea></td></tr>
                    </table>
        		</div>
	        </div>
            
            <div id="specific" style="display:none;">
        	    <div class="tmenulevel_1">
        	    <table cellspacing="0" cellpadding="3"><tr>
        			<td class="tl1"><a href=# onclick="show('maindata'); return false;">$title</a></td>
        			<td class="tl1"><a href=# onclick="show('dcontent');$editorLink return false;">$idesc</a></td>
        			<td class="tl1"><a href=# onclick="show('metadata'); return false;">$mtags</a></td>
        			$spec_active
        			</tr></table>
        		</div>
        		<div class="tmenulevel_2">
                    $specificLines
        		</div>
	        </div>

            <br>
            <input type=submit value="$submit">
        </form>
    </div>
EF;
    return $res;
}

function navi($pageID, $includeLast)
{
    global $structureMgr;
    
    $hierachy = $structureMgr->getCurrentBranch($pageID);

    $navi = '';

    $cnt = count($hierachy);

    for ($i=0; $i < $cnt; $i++)
    {
        $md = $structureMgr->getMetaData($hierachy[$i]);
        
        if (!$includeLast && ($i == $cnt - 1))
        {
            $navi .= " / $md[ShortTitle]";
        }
        else
            $navi .= " / <a href=\"/admin/?pageID=$md[sID]\">$md[ShortTitle]</a>";
    }

    $href = "http://" . getenv("HTTP_HOST") . $structureMgr->getPathByPageID($pageID, true);

    if ($href)
        $navi .= "<br/><div class=urlfromadm style=\"background-color: #FEFEF0; padding: 3px; margin-bottom: 5px; margin-top: 5px; text-align: right;\">
        URL: <a target=_blank class=urlfromadm href=\"$href\">$href</a></div>\n";
    else
        $navi .= "<br/><br/>";

    return $navi;
}
/**
 * Возвращает Контент Структуры сайта в админке
 *
 * @param integer $pageID
 * @return string
 */
function structure($pageID)
{
    //return '';
    global $structureMgr, $modulesMgr, $authenticationMgr;
        
    $userGroupID = $authenticationMgr->getUserGroup();

    $res = '';

    $branch = $structureMgr->getStructureForPageID($pageID, 2);

       
    $brCount = count($branch);

    $message = _get('message');

    if ($message)
        $message = '<br><center><span style="color: red; font-weight: bold;">'.$message.'</span></center><br><br>';

    $md = $structureMgr->getMetaData($pageID);
    
    //print_r($md);
    //if ( isset( $md['mod']->publicFunctions ) && in_array('getSubItemType', $md['mod']->publicFunctions ) )
    $addCols = $modulesMgr->execute($md['ModuleName'], 'getAdditionalColumns', array($md['DataType']), true);
    
    // execute( 'Articles', 'getAdditionalColumns', 'Article', true );
    // Выполнить функцию 'getAdditionalColumns' модуля 'Articles' с параметром 'Article'

   // print_r($addCols);
   // die('die');

    if ($addCols == NULL)
        $addCols = array();
    
    $dtForCols = (count($addCols) > 0) ? array_shift($addCols) : ''; //dataType to show real values in the columns
    $trline = 1;
    

    $dCols = array();
    foreach ($addCols as $k => $v)
    {
        $dCols[] = $k;
    }

    if ($dtForCols)
        $dataList = $modulesMgr->execute($md['ModuleName'], 'getDataListByPageID', array($pageID, $dCols), true);
    
    if (!isset($dataList) || $dataList == NULL)
        $dataList = array();

    //Look for dropdowns to generate JS arrays
    $scripts = '';
    
    foreach ($addCols as $addCol=>$addColVal)
    {
        switch ($addColVal[1])
        {
            case 'dropdown': 
            {
                $scripts .= "var $addCol = {";
                $j = 0;
                foreach ($addColVal[2] as $addColRow => $addColRowVal)
                {
                    if ($j != 0)
                        $scripts .= ', ';
                    $scripts .= '"' . $addColRow . '" : "' . $addColRowVal . '"';
                    $j = 1;
                }
                $scripts .= "};\n\n";
                break;
            }
        }
    }
    

    if ($scripts)
    {
        $scripts = "$scripts
        function writeDropDown(vName, sID, val)
        {
            var x;
            eval('x = ' + vName + ';');
            if (x)
            {
                document.write(\"<select name=\\\"\" + vName + \"\" + sID + \"\\\">\");
                for (a in x)
                {
                    document.write(\"<option\");
                    if (a == val)
                        document.write(\" selected\");

                    document.write(\" value=\" + a + \">\" + x[a] + \"</option>\");
                }
                document.write(\"</select>\");
            }
        }
        ";
    }

    /**
     * evil circle begin
     */
    
    $i1=0;
    for($i=0; $i<$brCount; $i++)
    {
        $cnt = count($branch[$i]['children']);

        if ($cnt > 0)
            $cntblock = "<br><div style=\"padding: 1px; margin-top: 3px; color: #808080\">[подразделов: $cnt]</div>";
        else
            $cntblock = '';

        if ($branch[$i]['isHidden'])
            $isHidden = ' checked';
        else
            $isHidden = '';
            

        $dt = $modulesMgr->execute($branch[$i]['ModuleName'], 'getSubItemType', array($branch[$i]['DataType']), false);
        $selfDt = $modulesMgr->execute($branch[$i]['ModuleName'], 'getItemType', array($branch[$i]['DataType']), false);

        $i1 = $i + 1;

        $res .= "<tr id=tr$i1 class=sline$trline><td>" . 
//        ($i + 1) 
        '<input type=checkbox name=chk' . $branch[$i]['sID'] . ' value="' . $branch[$i]['sID'] . '">'
        . '<input type=hidden name="item' . $branch[$i]['sID'] . '" value="' . $branch[$i]['sID'] . '">
        </td>';
        $res .= "<td class=\"idCol$trline\">" . $branch[$i]["sID"] . "</td>";
        
        
        $res .= '<td>';
        if (count($dt) > 0)
            $res .= '<a class=admlink href="/admin/?pageID=' . $branch[$i]['sID'] . '">' . 
                     $branch[$i]['ShortTitle'] . ' &raquo;</a> '.$cntblock;
        else
            $res .= $branch[$i]['ShortTitle'];
        $res .= '</td>';

        if ($userGroupID == 5 || $branch[$i]['DataType'] == 'CatItem')
            $res .= '<td align=center><a class=admedit href="/admin/?cmd=editPage&pageID=' . $branch[$i]['sID'] . '">править</a></td>';
        else
            $res .= '<td align=center></td>';
       
        // Собирает HTML-код строк таблицы для вывода списка разделов и их параметров
        
        foreach ($addCols as $addCol => $addColVal)
        {
            $res .= '<td align=center>';

            if ($dtForCols == $branch[$i]['DataType'])
            {
                switch ($addColVal[1])
                {
                    case 'label': 
                        $res .= $dataList[$branch[$i]['sID']][$addCol];
                        break;
                    case 'text': 
                        $res .= "<input type=text name=\"$addCol" . $branch[$i]["sID"] .
                                "\" value=\"" . $dataList[$branch[$i]["sID"]][$addCol]. "\" size=\"$addColVal[2]\">";
                        break;
                    case 'dropdown': 
                        $res .= "<script>writeDropDown('$addCol', '" . $branch[$i]["sID"].
                                "', '" . $dataList[$branch[$i]["sID"]][$addCol]. "');</script>";//$addColVal[2][1];//" - ";
                        break;
                    case 'checkbox': 
                        $res .= "<input type=checkbox name=\"$addCol" . $branch[$i]["sID"] . 
                                "\" value=\"1\"";
                        if ($dataList[$branch[$i]["sID"]][$addCol])
                            $res .= " checked";
                        $res .= ">";
                        break;
                    default: $res .= ' - ';
                }
            }
            else
            {
                $res .= " - ";
            }

            $res .= '</td>';
        }
        
        $delName = str_replace('"', '&quot;', $branch[$i]['ShortTitle']);

        $res .= 
        "<td align=center>" . $selfDt[0] . "</td>\n" .
        "<td align=center>" . GetCfg($branch[$i]['ModuleName'] . '.ModuleName') . "</td>\n" .
        "<td align=center>" . $branch[$i]["CreateDate"] . "</td>";
        $res .= "<td align=center><input type=checkbox value=\"1\" name=\"h" . $branch[$i]["sID"] . "\"$isHidden></td>\n";
        if ($userGroupID == 5)
        $res .= "<td align=center><a class=admdel href=\"/admin/?cmd=delPage&pageID=" . $branch[$i]["sID"] . "\" 
        onclick=\"return confirmDelete('" . $selfDt[1] . " &quot;" . $delName . "&quot;');\" style=\"color: red;\">удалить</a></td>\n";
        
        $res .= "</tr>\n";

        if ($trline == 1)
            $trline = 2;
        else
            $trline = 1;
        
        if (($i == $brCount - 1))
        {
            $colSpan = 9 + count($addCols);
            $res .= "<tr><td colspan=$colSpan class=idCol1>";
            if ($userGroupID == 5)
            {
            $res .= "<div style=\"float:left; font-weight: bold;\"><span style=\"vertical-align:absmiddle;\">Отмеченные: </span>
                <select name=cmd$i1 >
                    <option value=\"\">(выберите действие)";
                    
                                        
                        $res .= "<option value=\"movePages\">перенести в другой раздел";
                    
                    $res .= "<option value=\"moveToTheTop\">переместить в начало раздела
                    <option value=\"moveToTheBottom\">переместить в конец раздела";        
                        $res .= "<option value=\"delPages\">удалить";
                    
                $res .= "</select>
            </div>
			<div style=\"float:left; font-weight: bold;\"><span style=\"vertical-align:absmiddle;\">Раздел для переноса: </span>
                <select name=branchName >
                    ".
            getOptionBranches($pageID, $branch).   //-- it is very evil function
            "
                </select>
            </div>
			<div style=\"float:left;\"><button name=\"sender\" title=\"Выполнить\" onClick=\"document.forms.structure.submit();\">Выполнить</button></div>";
            
            }
            $res .= "<div style=\"float:right;\"><input type=submit name=update value=\"Обновить\"></div></tr>\n";
        }
    }

    /**
     * evil circle end
     */
    
    $addLinks = '';

    
    $listModules = $modulesMgr->modules();
    //print_r($listModules);
    
    foreach ($listModules as $modName => $modArr)
    {
        if ( isset( $modArr['mod']->publicFunctions ) && in_array('getSubItemType', $modArr['mod']->publicFunctions ) ) {
            $dt = $modulesMgr->execute($modName, 'getSubItemType', array($md['DataType']), true);
        } else {
            $dt = array();
        }
        
        $addLinks2 = '';

        foreach ($dt as $k => $v)
        {
            if ($addLinks2) {
                $addLinks2 .= ' | ';
            }
            if ($userGroupID == 5 || $k == 'CatItem')
            {
                $addLinks2 .= '<a class="admlink" href="/admin/?cmd=addPage&pageID='.
                            $pageID.'&ModuleName='.$modName.'&DataType='.$k.'">'.$v.'</a>';
            }
        }
        
        if ( $addLinks2 )
        {
            $addLinks .= '<tr><td>[' . GetCfg($modName.'.ModuleName') . '] &nbsp;</td><td>'.$addLinks2.'</td></tr>';
        }

    }
    if ($addLinks)
        $addLinks = '<div style="border: double #146E00; background-color: #C5FDB8; padding: 3px; font-size: 12px;">
        <table cellpadding=0 cellspacing=0 border=0>
            <tr>
                <td valign=top><strong>Добавить:</strong></td>
                <td valign=top>&nbsp;</td>
                <td valign=top>
                    <table border=0 cellpadding=0 cellspacing=0>'
                        .$addLinks.
                    '</table>
                </td>
            </tr>
        </table>
        </div>';
    
    if ($brCount < 20) {
        $addLinksTop = '';
    } else {
        $addLinksTop = $addLinks;
    }
        
    if (!$res)
        $res = '<tr><td colspan=7>подразделы отсутствуют</td></tr>';

    $thead = '';
    
    foreach ($addCols as $addCol => $addColVal)
    {
        $thead .= '<td align=center>' . $addColVal[0] . '</td>';
    }
    
    return
        "
        <script>
            function confirmDelete(txt)
            {
                return confirm(\"Действительно удалить \" + txt + \"?\");
            }
            $scripts
        </script>
$message

$addLinksTop<br>
<form id=structure method=post action=/admin/ style=\"margin: 0px;\">
<input type=hidden name=cmd value=updChildren>
<input type=hidden name=pageID value=$pageID>
<table width=100% cellpadding=4 cellspacing=0 border=0 style=\"border: double #A0A0A0;\"><tr class=\"top\">
<td><input type=checkbox name=checkall></td>
<td>id</td>
<td>Наименование</td>
<td align=center>&nbsp;</td>
$thead
<td align=center>Тип данных</td>
<td align=center>Модуль</td>
<td align=center>Дата обновления</td>
<td align=center>Скрывать</td>
<td align=center>&nbsp;</td>
</tr>
$res
</table>
</form><br>
$addLinks
";
}

/**
 * Enter description here...
 *
 * @param integer $pageID
 * @return unknown
 */
function getOptionBranches($pageID, &$branch)
{
	global $structureMgr;

	
    if (!$pageID)
        trigger_error('PageID must be specified for deleting', PM_FATAL);

	$content = '';
	
    $parent = $structureMgr->getParentPageID($pageID); // 1 MySQL query
    if ($parent) {
		$data = $structureMgr->getMetaData($parent);
		$content .= '<option value="'.$parent.'">'.$data['ShortTitle'].'</option>';
	}
	
	//$branch = $structureMgr->getStructureForPageID($pageID, 3);
        //print_r($branch);
        //die();

	foreach($branch as $item) {
		if($item['DataType'] != 'CatItem')
			$content .= '<option value="'.$item['sID'].'">'.$item['ShortTitle'].'</option>';	
	}

	//print ('content:'.$content);
	//die($pageID.', '.$parent);

	
	return $content;
}

function admMenu($cmd)
{
    global $templatesMgr;
    $items = array(
                   '' => 'Структура', 
                   'cards' => 'Клубные карты',
                   'users' => 'Пользователи', 
//                   "orders" => "Заказы", 
//                   "stat" => "Статистика",
                   'modparams' => 'Настройки модулей',
				   'banner' => 'Баннеры',
				   'vote' => 'Голосования',
				   'mails' => 'Отправить почту'
                   );

    $b = $templatesMgr->getValidTags($templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/admin/adm_menu.xml'),
                                     array('container', 'separator', 'item', 'activeitem'));

    $amItems = '';
    foreach($items as $_cmd => $menu)
    {
        if ($_cmd == $cmd)
            $ln = $b['activeitem'];
        else
            $ln = $b['item'];


        if (!$_cmd)
            $lnk = '/admin/';
        else
            $lnk = '/admin/?cmd='.$_cmd;

        $ln = str_replace('%text%', $menu, $ln);
        $ln = str_replace('%link%', $lnk, $ln);

        if ($amItems)
            $amItems .= $b['separator'];
        
        $amItems .= $ln;
    }


    $t = $b['container'];
    $t = str_replace('%items%', $amItems, $t);
    
    return $t;
}

?>  
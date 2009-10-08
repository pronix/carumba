<?php

	class Search extends AbstractModule
	{
		var $itemsCount = 0;
		
		var $LOW_DETAILED = 0;
		var $MEDIUM_DETAILED = 1;
		var $HI_DETAILED = 2;

		function Search()
		{
			$this->publicFunctions = array("getContent", "getSubItemType", "getItemType", "getItemDesc", 
            "getSpecificDataForEditing", "getSpecificBlockDesc", "updateSpecificData");

		}

		function getContent($args)
		{
			global $structureMgr, $templatesMgr;
			
			SetCfg("Search.itemsPerPage", 10);
			SetCfg("Search.itemsPerCol", 1);
			
			$searchtext = trim(_get("searchtext"));
			$searchtype = _get("searchtype");
			if(!$searchtype)$searchtype=0;
			

			$pageID = $args[0];
			
			$content = "";
			$pager = "";
			$msg = "";

			$topContent = $structureMgr->getData($pageID);

			

			$pNum = $structureMgr->getPageNumberByPageID($pageID);
            $URL = $structureMgr->getPathByPageID($pageID, false);
			
			$perPage = GetCfg("Search.itemsPerPage");

            $startFrom = ($pNum - 1) * $perPage;
            $endAt = $startFrom + $perPage - 1;
			
			$cats = $structureMgr->getStructureForPageID(3, 1);
			$content = "";
            
			$searchForm = "<form action=\"/search\" method=\"get\"><div class=\"podbor\"> 
              <p>%msg%</p>             
              <input name=\"searchtext\" type=\"text\" class=\"searchsel\" value=\"".$searchtext."\" />&nbsp;";
             $searchForm .= "<input type=\"image\" src=\"/images/search_butt_inside.gif\" class=\"search_butt\" alt=\"Искать\" />
			</div>		   
			</form>";
			
			$items = $this->getItems($searchtext, $startFrom, $endAt, $searchtype);
            $cnt = $this->itemsCount;
			
			
			if(strlen($searchtext) < 2) {
				$msg .= "Слишком короткий запрос. Введите запрос размером более трех символов";
			} else if(!$cnt) {
				$msg .= "На ваш запрос результатов не найдено";
			}
			else {
				$msg .= "На ваш запрос найдено <strong>".$cnt."</strong> результатов";

			
				if ($endAt >= $cnt)
					$endAt = $cnt - 1;

				$pagesCount = ceil($cnt / $perPage);
				
				$filter = "searchtext=".$searchtext;

				if ($pagesCount < $pNum)
				{
					trigger_error("Invalid pageNumber [$pNum of $pagesCount] - possibly hacking or serious change in DB", PM_ERROR);
				}
				else
				{
					if ($pagesCount > 1)
					{
						$tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/" . "pager.html");
						$purePager = "";

						for ($i=1; $i <= $pagesCount; $i++)
						{
							if ($i > 1)
							{
								$purePager .= " - ";
								$u = $URL . "/page" . $i;
							}
							else
							   $u = $URL;

							if ($filter)
								$u .= "?" . $filter;

							if ($i == $pNum)
							{
								$purePager .= $i;
							}
							else
							{
								$purePager .= "<a href=\"$u\" class=\"levm\">" . $i . "</a>";
							}
						}

						$pager = str_replace("%links%", $purePager, $tpl);
					}

				}
				
				$i = 1;
				$content .= "<div class=\"items\">\n<table cellpadding=\"0\" cellspacing=\"0\" class=\"items-table\">\n";
				foreach($items as $item) {
					$style = (($i > GetCfg("Search.itemsPerCol")) ? "dwn" : "up");
					$content .= $this->getFilledTemplate($item, $style);
					$i++;
				}
				$content .= "\n</table>\n</div>\n";
			}

			$searchForm = str_replace("%msg%", $msg, $searchForm);
			return $topContent.$searchForm.$pager.$content.$pager;
		}


		function getFilledTemplate($catItem, $style="mid")
        {
            global $structureMgr, $templatesMgr;
			
            if (count($catItem) == 0)
                trigger_error("Invaid function call - arguments array is empty.", PM_FATAL);
			
			$catItem["tplID"] = $catItem["Compatibility"] ? 1 : 2;

            $URL = $structureMgr->getPathByPageID($catItem["sID"], false);
            $tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Catalogue/item" . $catItem["tplID"] . ".html");
            $tpl = str_replace("%title%", $catItem["ShortTitle"], $tpl);
            $tpl = str_replace("%link%", $URL, $tpl);

			$tpl = str_replace("%style%", $style, $tpl);
            
			$bonusName="";
			$bonusValue="";
			$oldPriceStyle="";
			switch($catItem["ptID"])
			{
				case 2: $typeClass="t_bonus"; $bonusName = "<strong>Спецпредложение (<a href=\"/main/club\">по карте</a>):</strong>";  $bonusValue = "<span class=\"t_bonus\">".$catItem["ptPercent"]."% </span><br/> "; break;
				case 3: $typeClass="t_bonus"; $bonusName = "<strong>Спецпредложение (<a href=\"/main/club\">по карте</a>):</strong>";  $bonusValue = "<span class=\"t_bonus\">".$catItem["ptPercent"]."% </span><br/> "; break;
				case 4: $typeClass="t_bonus"; $bonusName = "<strong>Спецпредложение (<a href=\"/main/club\">по карте</a>):</strong>";  $bonusValue = "<span class=\"t_bonus\">".$catItem["ptPercent"]."% </span><br/> "; break;

				case 5: $typeClass="t_salepr"; $oldPriceStyle="<span class=\"t_old\">"; $bonusName = "<strong>Распродажа:</strong>";  $bonusValue = "<span class=\"t_sale\">".$catItem["ptPercent"]."% </span><br/> "; break;
				case 6: $typeClass="t_salepr"; $oldPriceStyle="<span class=\"t_old\">"; $bonusName = "<strong>Распродажа:</strong>";  $bonusValue = "<span class=\"t_sale\">".$catItem["ptPercent"]."% </span><br/> "; break;
				case 7: $typeClass="t_salepr"; $oldPriceStyle="<span class=\"t_old\">"; $bonusName = "<strong>Распродажа:</strong>";  $bonusValue = "<span class=\"t_sale\">".$catItem["ptPercent"]."% </span><br/> "; break;
			}
			if($bonusName && $bonusValue)
			{
				$tpl = str_replace("%bonus%", "</br>$bonusName - $bonusValue", $tpl);
			} else {
				$tpl = str_replace("%bonus%", "", $tpl);
			}

            //price generation must be moved to special function as it is called from at least two places
            if ($catItem["ptPercent"] == 0)
                $firstPrice = "<strong>" . round($catItem["salePrice"] - ($catItem["salePrice"] * 5 / 100)) . "</strong>";
            else
                $firstPrice = "<strong><font class=\"".$typeClass."\">" . 
                              round($catItem["salePrice"] - ($catItem["salePrice"] * $catItem["ptPercent"] / 100)) . 
                              "</font></strong>";


            $props = $this->getCatItemProperties($catItem["sID"], "CatItem", $structureMgr->getParentPageID($catItem["sID"]));
            
            $prop_list = "";
            foreach($props as $prop)
            {
               if ($prop[3] && !$prop[4])
               {
                   $prop_list .= "<strong>$prop[1]:</strong> $prop[3] $prop[2]<br/>\n";
               }
                 
            };

            $tpl = str_replace("%price%", "$firstPrice" . " / " . $oldPriceStyle.$catItem["salePrice"].($oldPriceStyle ? "</span>":"") . " руб.", $tpl);


            $tpl = str_replace("%producer%", $catItem["accPlantName"], $tpl);
            $tpl = str_replace("%props%", $prop_list, $tpl);
            
            if (!isset($catItem["Compatibility"]))
                $catItem["Compatibility"] = "";

            $tpl = str_replace("%car_compatibility%", $catItem["Compatibility"], $tpl);
            
            if ($catItem["smallPicture"] == NULL)
            {
                if (file_exists(GetCfg("ROOT") . $catItem["PicturePath"] . "/" . $catItem["sID"] . ".gif"))
                    $catItem["smallPicture"] = $catItem["PicturePath"] . "/" . $catItem["sID"] . ".gif";
                else
                if ($catItem["logotype"] == NULL)
                    $catItem["smallPicture"] = "/products/empty.gif";
                else
                    $catItem["smallPicture"] = $catItem["logotype"];
            }
            $tpl = str_replace("%picture%", "<img src=\"" . $catItem["smallPicture"] . "\" alt=\"" . $catItem["ShortTitle"] . "\"/>", $tpl);
            $tpl = str_replace("%goodID%", $catItem["accID"], $tpl);



            return $tpl;
        }

        function getFilledItemDescriptionTemplate($catItem)
        {
            global $structureMgr, $templatesMgr;
            
            if (count($catItem) == 0)
                trigger_error("Invaid function call - arguments array is empty.", PM_FATAL);

            $tplName = GetCfg("TemplatesPath") . "/Catalogue/" . $catItem["DescriptionTemplate"];
            
            $tpl = $templatesMgr->getTemplate(-1, $tplName);

            $blocks = $templatesMgr->getValidTags($tpl, 
                          array("container", "picture", "description", "spec", "details", "zoom"));
            
            //SPECIFICATIONS
            $spec = "";

            $specs = array("Compatibility" => "Марка", "accPlantName" => "Производитель");
			$count = 0;
            foreach ($specs as $key => $val)
            {
                if (isset($catItem[$key]) && $catItem[$key])
                {
					$style = ($count > 0) ? "mid" : "up";
                    $sp = $blocks["spec"];
					$sp = str_replace("%style%", $style, $sp);
                    $sp = str_replace("%spec_name%", $val, $sp);
                    $sp = str_replace("%spec_value%", $catItem[$key], $sp);
                    $spec .= $sp;
					$count++;
                }
            }
			
            
            $props = $this->getCatItemProperties($catItem["sID"], "CatItem", $structureMgr->getParentPageID($catItem["sID"]));
            
            $prop_list = "";
            foreach($props as $prop)
            {
               if ($prop[3] && !$prop[4])
               {
				    $style = ($count > 0) ? "mid" : "up";
                    $sp = $blocks["spec"];
					$sp = str_replace("%style%", $style, $sp);
                    $sp = str_replace("%spec_name%", $prop[1], $sp);
                    $sp = str_replace("%spec_value%", "$prop[3] $prop[2]", $sp);
                    $spec .= $sp;
					$count++;
               }
            };
				
			$bonusName="";
			$bonusValue="";
			$oldPriceStyle="";
			switch($catItem["ptID"])
			{
				case 2: $typeClass="t_bonus"; $bonusName = "<strong>Спецпредложение (<a href=\"/main/club\">по карте</a>):</strong>";  $bonusValue = "<span class=\"t_bonus\">".$catItem["ptPercent"]."% </span><br/> "; break;
				case 3: $typeClass="t_bonus"; $bonusName = "<strong>Спецпредложение (<a href=\"/main/club\">по карте</a>):</strong>";  $bonusValue = "<span class=\"t_bonus\">".$catItem["ptPercent"]."% </span><br/> "; break;
				case 4: $typeClass="t_bonus"; $bonusName = "<strong>Спецпредложение (<a href=\"/main/club\">по карте</a>):</strong>";  $bonusValue = "<span class=\"t_bonus\">".$catItem["ptPercent"]."% </span><br/> "; break;

				case 5: $typeClass="t_salepr"; $oldPriceStyle="<span class=\"t_old\">"; $bonusName = "<strong>Распродажа:</strong>";  $bonusValue = "<span class=\"t_sale\">".$catItem["ptPercent"]."% </span><br/> "; break;
				case 6: $typeClass="t_salepr"; $oldPriceStyle="<span class=\"t_old\">"; $bonusName = "<strong>Распродажа:</strong>";  $bonusValue = "<span class=\"t_sale\">".$catItem["ptPercent"]."% </span><br/> "; break;
				case 7: $typeClass="t_salepr"; $oldPriceStyle="<span class=\"t_old\">"; $bonusName = "<strong>Распродажа:</strong>";  $bonusValue = "<span class=\"t_sale\">".$catItem["ptPercent"]."% </span><br/> "; break;
			}
			//$tpl = str_replace("%bonus%", $bonus, $tpl);
			if($bonusName && $bonusValue)
			{
				$style = ($count > 0) ? "mid" : "up";
                $sp = $blocks["spec"];
				$sp = str_replace("%style%", $style, $sp);
				$sp = str_replace("%spec_name%", $bonusName, $sp);
				$sp = str_replace("%spec_value%", "- ".$bonusValue, $sp);
				$spec .= $sp;
				$count++;
			}
            //DETAILS
            $details = $structureMgr->getData($catItem["sID"]);
            if ($details)
                $blocks["details"] = str_replace("%content%", $details, $blocks["details"]);
            else
                $blocks["details"] = "";


            //ZOOM
            if (file_exists(GetCfg("ROOT") . $catItem["PicturePath"] . "/" . $catItem["sID"] . "_3.jpg"))
                $catItem["bigPicture"] = $catItem["PicturePath"] . "/" . $catItem["sID"] . "_3.jpg";
            else
                $catItem["bigPicture"] = "";

            if ($catItem["bigPicture"])
            {
                $zoom = str_replace("%link%", $catItem["bigPicture"], $blocks["zoom"]);
            }
            else
                $zoom = "";

            if (file_exists(GetCfg("ROOT") . $catItem["PicturePath"] . "/" . $catItem["sID"] . "_2.jpg"))
                $catItem["stdPicture"] = $catItem["PicturePath"] . "/" . $catItem["sID"] . "_2.jpg";
            else
            if (!isset($catItem["stdPicture"]) || !$catItem["stdPicture"])
            {
                $bigLogo = str_replace(".gif", "_2.gif", $catItem["logotype"]);
                if ($catItem["logotype"] != NULL && file_exists(GetCfg("ROOT") . $bigLogo))
                {
                    $catItem["stdPicture"] = $bigLogo;
                }
                else
                    $catItem["stdPicture"] = "/products/empty_2.gif";
            }

            /* !!!MAIN job here */
            

			
            if ($catItem["ptPercent"] == 0)
                $firstPrice = "<strong>" . round($catItem["salePrice"] - ($catItem["salePrice"] * 5 / 100)) . "</strong>";
            else
                $firstPrice = "<strong><font class=\"".$typeClass."\">" . 
                              round($catItem["salePrice"] - ($catItem["salePrice"] * $catItem["ptPercent"] / 100)) . 
                              "</font></strong>";

            //$tpl = str_replace("%price%", "$firstPrice" . " / " . $catItem["salePrice"] . " руб.", $tpl);
            $tpl = str_replace("%price%", "$firstPrice" . " / " . $oldPriceStyle.$catItem["salePrice"].($oldPriceStyle ? "</span>":"") . " руб.", $tpl);

            $blocks["description"] = str_replace("%price%",  "$firstPrice" . " / " . $oldPriceStyle.$catItem["salePrice"].($oldPriceStyle ? "</span>":""), $blocks["description"]);

			//$blocks["description"] = str_replace("%bonus%", $bonus, $blocks["description"]);

            $blocks["description"] = str_replace("%spec%", $spec, $blocks["description"]);
            $blocks["description"] = str_replace("%details%", $blocks["details"], $blocks["description"]);
            $blocks["description"] = str_replace("%goodID%", $catItem["accID"], $blocks["description"]);

            $blocks["picture"] = str_replace("%zoom%", $zoom, $blocks["picture"]);
            $blocks["picture"] = str_replace("%image%", $catItem["stdPicture"], $blocks["picture"]);
            $blocks["picture"] = str_replace("%good_name%", $catItem["ShortTitle"], $blocks["picture"]);
            
            $blocks["container"] = str_replace("%picture%", $blocks["picture"], $blocks["container"]);
            $blocks["container"] = str_replace("%description%", $blocks["description"], $blocks["container"]);

            return $blocks["container"];
        }
		
		function getCatItemProperties($pageID, $DataType, $parentID)
        {
            global $structureMgr;
            
            if ($pageID != -1)
                $md = $structureMgr->getMetaData($pageID);
            else
                $md["DataType"] = $DataType;

            $res = array();

            switch ($md["DataType"])
            {
                case "CatItem":
                {
                    if ($pageID != -1)
                    {
                        $q2 = "SELECT accID FROM pm_as_parts WHERE sID = $pageID";
                        list($accID) = mysql_fetch_row(mysql_query($q2));
                        if (!$accID)
                            trigger_error("Error fetching accID for CatItemProperties (sID=$pageID) [$q2]" . mysql_error(), PM_FATAL);


                        $q = "SELECT app.propListID, propName, accMeasure, propValue, isHidden FROM pm_as_prop_list apl, pm_as_parts_properties app
                        WHERE app.accID=$accID AND app.propListID = apl.propListID
                        ORDER BY apl.OrderNumber";

                        $qr = mysql_query($q);
                        if (!$qr)
                            trigger_error("Error while query [$q] - " . mysql_error(), PM_FATAL);
                        
                        while (false !== ($row = mysql_fetch_row($qr)))
                        {
                            $res[] = $row;
                        }
                    }
                    else
                    {
                        $branch = $structureMgr->getCurrentBranch($parentID);
                        for ($i = count($branch) - 1; $i >=0; $i--)
                        {
                            $accCatID = $this->getCatIDByPageID($branch[$i]);
                            if ($accCatID == -1)
                                break;

                            $q2 = "SELECT propListID, propName, accMeasure, '', isHidden FROM pm_as_prop_list WHERE accCatID=$accCatID";
                            $qr = mysql_query($q2);
                            if (!$qr)
                                trigger_error("Error fetching propNames for CatItems - " . mysql_error(), PM_FATAL);
                            if (mysql_num_rows($qr) > 0)
                            {
                                while (false !== ($prop = mysql_fetch_row($qr)))
                                {
                                    $res[] = $prop;
                                }
                                break;
                            }
                        }
                    }
                    return $res;
                }
                default:
                    return array();
            }
        }


		function getItems($searchtext, $startFrom, $endAt, $searchType = 0)
		{
			//echo $searchtext.'<br>';
			$searchArray = explode(" ",$searchtext);
			//print_r($searchArray);
			
			//$query = Array();
			$whereText = "";
			
			$whereArray = Array();
			
			$whereArray[] = "CONCAT(s.ShortTitle, ' ', s.Title, ' ', ap.accPlantName, ' ', prop.propValue) LIKE '%".str_replace(' ','%',$searchtext)."%'\n";
			$whereArray[] = "CONCAT(ap.accPlantName, ' ', s.ShortTitle, ' ', s.Title, ' ', prop.propValue) LIKE '%".str_replace(' ','%',$searchtext)."%'\n";
			$whereArray[] = "CONCAT(ap.accPlantName, ' ', prop.propValue, ' ', s.ShortTitle, ' ', s.Title) LIKE '%".str_replace(' ','%',$searchtext)."%'\n";
			$whereArray[] = "CONCAT(prop.propValue, ' ', ap.accPlantName, ' ', s.ShortTitle, ' ', s.Title) LIKE '%".str_replace(' ','%',$searchtext)."%'\n";
			$whereArray[] = "CONCAT(prop.propValue, ' ', s.ShortTitle, ' ', s.Title, ' ', ap.accPlantName) LIKE '%".str_replace(' ','%',$searchtext)."%'\n";

			$str .= implode(" || ", $whereArray);
			
			if($str)
			{
				$whereText .= " && ( ".$str." )\n";
			}
			
			$query = "SELECT SQL_CALC_FOUND_ROWS
								COUNT(p.accID) as numOfDoubles, 
								p.accID, p.sID, s.ShortTitle, deliveryCode, accPlantName, logotype, smallPicture, p.tplID, salePrice, 
								MustUseCompatibility, PicturePath, DescriptionTemplate, ptPercent,
								CONCAT(s.ShortTitle, ' ', s.Title, ' ', ap.accPlantName, ' ', prop.propValue),
								CONCAT(ap.accPlantName, ' ', s.ShortTitle, ' ', s.Title, ' ', prop.propValue),
								CONCAT(prop.propValue, ' ', ap.accPlantName, ' ', s.ShortTitle, ' ', s.Title),
								CONCAT(prop.propValue, ' ', s.ShortTitle, ' ', s.Title, ' ', ap.accPlantName)
								FROM `pm_as_parts` p
								LEFT JOIN pm_as_producer ap ON (ap.accPlantID = p.accPlantID)
								LEFT JOIN pm_structure s ON (p.sID = s.sID && s.isHidden = 0)
								LEFT JOIN pm_as_categories ac ON (s.pms_sID = ac.sID)
								LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
								LEFT JOIN pm_as_parts_properties prop ON (prop.accID = p.accID) 
								WHERE s.DataType = 'CatItem' && p.notAvailable = 0
								".$whereText."
								GROUP BY p.accID, s.sID
								ORDER BY numOfDoubles desc LIMIT ".$startFrom.",".GetCfg("Search.itemsPerPage")." ";
			//$bigQuery = implode(" UNION ",$query);
			//$bigQuery .= ";
			
			//echo $query.'<br><hr>';
			$result = mysql_query($query);
			////if (!$result)
            //    trigger_error("Invaid query. " . mysql_error(), PM_FATAL);
			
            //if (mysql_num_rows($result) == 0)
            //    trigger_error("Empty result for $query", PM_WARNING);
			
			$query = "SELECT FOUND_ROWS() as itemsCount";
			$res = mysql_query($query);
			$row = mysql_fetch_assoc($res);
			$this->itemsCount = $row['itemsCount'];
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
				//echo '<pre>';
				//print_r($item);
				//echo '</pre><br>';
				$catItems[] = $item;
			}

			
			//print_r($catItems);
			return $catItems;
		}

		function getSpecificDataForEditing($args)
        {
            return array();
        }

        function updateSpecificData($args)
        {
            return true;
        }

        function getSpecificBlockDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Search":
                    return "Параметры";
            }
            
            return "";
        }

        function getItemDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Search":
                    return "";
            }
            
            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Search":
                    return array("поиск", "поиска", "поиску"); //Именит, Род, Дат
            }
            
            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case "Catalogue":
                    return array("Search" => "поиск");
				case "Article":
                    return array("Search" => "поиск");
            }
            return array();
        }

        
	}

?>
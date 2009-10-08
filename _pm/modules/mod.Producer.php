<?
    class Producer extends AbstractModule
    {
        var $LOW_DETAILED = 0;
        var $MEDIUM_DETAILED = 1;
        var $HI_DETAILED = 2;
        
        function Producer()
        {
            $this->name = "Producer";
            $this->desc = "producer";
            $this->publicFunctions = array("getContent", "getItemType", "getSubItemType", "getItemDesc",
            "getSpecificDataForEditing", "getSpecificBlockDesc", "updateSpecificData", 'updateAdditionalColumns');
            $this->cmdFunctions = array();

            SetCfg("Producer.perPage", 50);
            SetCfg("Producer.itemsPerPage", 20);
            SetCfg("Producer.itemsPerCol", 2);
            
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
                case "ProducerAll":
                    return "Параметры";
                case "Producer":
                    return "Параметры";
            }

            return "";
        }

        function getItemDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "ProducerAll":
                    return "";
                case "Producer":
                    return "Производитель";
            }

            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "ProducerAll":
                    return array("о производителе", "о производителе", "о производителе"); //Именит, Род, Дат
                case "Producer":
                    return array("о производителе", "о производителе", "о производителе");
            }

            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case "Article":
                    return array("ProducerAll" => "о производителе");

                case "ProducerAll":
                    return array("Producer" => "производитель");
            }

            return array();
        }

        function getContent($args)
        {
            global $structureMgr;
            $metaData = $structureMgr->getMetaData($args[0]);

            $page = _get('pg');
            if ($page > 0) 
                $page = $page;
            else
                $page = 0;

            switch ($metaData["DataType"])
            {
                case "ProducerAll":
                    return $this->getProducerList($page,$args[0]);
                case "Producer":
                {                    
                    if (_get('products') == 1)
                        return $this->getProducts($args[0]);
                    else
                        return $this->getProducer($args[0]);
                }   
            }
                                 
        }

        function getProducer($prodID)
        {
            global $structureMgr, $templatesMgr;
            //$md = $structureMgr->getMetaData($pageID);
            
            $add_tpl = "";
            if (_get("print") == 1)
            {
                $add_tpl = "print_";   
            }
            $tplName = GetCfg("TemplatesPath") . "/" . $add_tpl . "producer.html";
            //print_r($tplName);
            $tpl = $templatesMgr->getTemplate(-1, $tplName);

            $blocks = $templatesMgr->getValidTags($tpl,
                          array("container", "print", "ref"));
            
            $q = "SELECT DISTINCT accPlantID, accPlantName, logotype, logotypeb, Content, pr.sID as ID FROM pm_structure s, pm_as_producer pr
            WHERE s.sID=$prodID AND pr.sID = s.sID AND isHidden=0";
            $qr = mysql_query($q);
            $r = mysql_fetch_array($qr);
            $URL = $structureMgr->getPathByPageID($r["ID"], true);
            
            
            $ref = "/ref.php?sID=".$prodID;
            $blocks["ref"] = str_replace("%link%", $ref, $blocks["ref"]);

            
            $blocks['container'] = str_replace('%image%', $r["logotypeb"], $blocks['container']); 
            $blocks['container'] = str_replace('%plant_name%', $r["accPlantName"], $blocks['container']); 
            $blocks['container'] = str_replace('%content%', $r["Content"], $blocks['container']); 
            $blocks['container'] = str_replace('%url%', $URL, $blocks['container']); 
            
            $blocks["print"] = str_replace("%link%", "href='".$structureMgr->getPathByPageID($prodID, true)."?print=1'", $blocks["print"]);
            
            $blocks["container"] = str_replace("%print%", $blocks["print"], $blocks["container"]);
            $blocks["container"] = str_replace("%ref%", $blocks["ref"], $blocks["container"]);
            
            return $blocks['container'];                         
        }
        
        function getPlantbyPageID($pageID)
        {
            $q = "SELECT DISTINCT accPlantID, accPlantName, logotype, logotypeb, Content, pr.sID as ID FROM pm_structure s, pm_as_producer pr
            WHERE s.sID=$pageID AND pr.sID = s.sID AND isHidden=0";
            $qr = mysql_query($q);
            $r = mysql_fetch_array($qr);
            return $r["accPlantID"];            
        }        
        function getFilledItemTemplate($catItem, $style="mid")
        {
            //print_r($catItem);
            //echo "<hr>";
            global $structureMgr, $templatesMgr;

            if (count($catItem) == 0)
                trigger_error("Invaid function call - arguments array is empty.", PM_FATAL);

            //echo $catItem["tplID"];

            $catItem['tplID'] = (!empty($catItem['Compatibility'])) ? 1 : 2;

            $URL = $structureMgr->getPathByPageID($catItem["sID"], false);
            $tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Catalogue/item" . $catItem["tplID"] . ".html");
            //print "/Catalogue/item" . $catItem["tplID"] . ".html<br />\n";
            $tpl = str_replace("%title%", $catItem["ShortTitle"], $tpl);
            $tpl = str_replace("%link%", $URL, $tpl);

            $tpl = str_replace("%style%", $style, $tpl);

            // begin Вывод рейтинга графичесики
            $rating = ($catItem['rating']) ? $catItem['rating'] : 0;
            include('_pm/modules/ratingGraph.php');
            // end Вывод рейтинга графичесики


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
                $firstPrice = "<strong><span class=\"".$typeClass."\">" .
                              round($catItem["salePrice"] - ($catItem["salePrice"] * $catItem["ptPercent"] / 100)) .
                              "</span></strong>";


            $props = $this->getCatItemProperties($catItem["sID"], "CatItem", $structureMgr->getParentPageID($catItem["sID"]));
            //print_r($props);
            $prop_list = "";

            foreach($props as $prop)
            {
                //print_r($prop);
                //echo "<hr>";
               if ($prop[3] && !$prop[4])
               {
                   //echo 'добавили';
                   $prop_list .= "<strong>$prop[1]:</strong> $prop[3] $prop[2]<br/>\n";
               }

            };

            $tpl = str_replace("%price%", "$firstPrice" . " / " . $oldPriceStyle.$catItem["salePrice"].($oldPriceStyle ? "</span>":"") . " руб.", $tpl);


            $tpl = str_replace("%producer%", $catItem["accPlantName"], $tpl);
            //echo $prop_list;
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
            $tpl = str_replace('%picture%', '<img src="'.$catItem['smallPicture'].'" alt="'.$catItem['ShortTitle'].'" />', $tpl);
            $tpl = str_replace("%goodID%", $catItem["accID"], $tpl);

            if ($catItem["xit"] == 1)
            {
                $tpl = str_replace("%xit%", "<div class=\"xit\"><img src=\"/images/xit.gif\" width=\"70\" height=\"17\" alt=\"\" /></div>", $tpl);
            } elseif ($catItem["new"] == 1)
            {
                $tpl = str_replace("%xit%", "<div class=\"xit\"><img src=\"/images/new1.gif\" width=\"70\" height=\"17\" alt=\"\" /></div>", $tpl);
            } else
            {
                $tpl = str_replace("%xit%", "", $tpl);
            }


            return $tpl;
        }        
        function getFilledTemplate($catItem, $detailed = 0, $columnStyle = 'tdupleft')
        {
            global $structureMgr, $templatesMgr;

            if (empty($catItem))
                trigger_error('Invaid function call - arguments array is empty.', PM_FATAL);

            $catItem['tplID'] = !empty( $catItem['Compatibility'] ) ? 1 : 2;


            $URL = $structureMgr->getPathByPageID($catItem['sID'], false);
            $tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/Catalogue/bonus'.$catItem['tplID'] . '.html');
            $tpl = str_replace('%title%', $catItem['ShortTitle'], $tpl);
            $tpl = str_replace('%link%', $URL, $tpl);

            // begin Вывод рейтинга графичесики
            $rating = ($catItem['rating']) ? $catItem['rating'] : 0;
            include('_pm/modules/ratingGraph.php');
            // end Вывод рейтинга графичесики

            $tpl = str_replace('%columnStyle%', $columnStyle, $tpl);

            $tpl = str_replace('%typename%', 'Распродажа', $tpl);
            $tpl = str_replace('%type%', 't_sale', $tpl);

            //price generation must be moved to special function as it is called from at least two places
            if ($catItem['ptPercent'] == 0) {
                $firstPrice = '<span class="t_salepr">' .
                        round($catItem['salePrice'] - ($catItem['salePrice'] * 5 / 100)) . '</span>';
            } else {
                $firstPrice = '<span class="t_salepr">' .
                              round($catItem['salePrice'] - ($catItem['salePrice'] * $catItem['ptPercent'] / 100)) .
                              '</span>';
            }

            $tpl = str_replace('%price%', '<span class="t_bonus">'.$firstPrice.
                        '</span> / <span class="t_old">' . $catItem['salePrice'] . '</span> руб.', $tpl);

            $tpl = str_replace('%bonus%', $catItem['ptPercent'], $tpl);

            //die('detailed = '.$detailed);

            if($detailed > 0) {
                $tpl = str_replace('%producer%', '<strong>Производитель: </strong>' . $catItem['accPlantName'], $tpl);
            } else {
                $tpl = str_replace('%producer%', '', $tpl);
            }

            $props = $this->getCatItemProperties($catItem['sID'], 'CatItem', $structureMgr->getParentPageID($catItem['sID']));

            
            if( ($detailed > 1) && !empty($props) )
            {
                $prop_list = '';
                foreach( $props as $prop )
                {
                   if ($prop[3] && !$prop[4])
                   {
                       $prop_list .= '<strong>'.$prop[1].':</strong> '.$prop[3].' '.$prop[2].'<br />';
                   }
                };

                $tpl = str_replace('%props%', $prop_list, $tpl);
                if ( !isset($catItem['Compatibility']) ) {
                    $catItem['Compatibility'] = '';
                } else {
                    $catItem['Compatibility'] = '<strong>Марка:</strong>' . $catItem['Compatibility'];
                }

                $tpl = str_replace('%car_compatibility%', $catItem['Compatibility'], $tpl);
            } elseif( ($detailed == 1) && !empty($props) ){
                $prop_list = '';
                //foreach($props as $prop)
                //{
                   if ($props[0][3] && !$props[0][4])
                   {
                       $prop_list = '<strong>'.$props[0][1].':</strong> '.$props[0][3].' '.$props[0][2].'<br />';
                   }

                //};
                $tpl = str_replace('%props%', $prop_list, $tpl);
                if (!isset($catItem['Compatibility'])) {
                    $catItem['Compatibility'] = '';
                } else
                {
                    $catItem['Compatibility'] = '<strong>Марка:</strong>' . $catItem['Compatibility'];
                }

                $tpl = str_replace('%car_compatibility%', $catItem['Compatibility'], $tpl);
            } else {
                $tpl = str_replace('%props%', '', $tpl);
                $tpl = str_replace('%car_compatibility%', '', $tpl);
            }
            if ($catItem['smallPicture'] == NULL)
            {
                if (file_exists(GetCfg('ROOT') . $catItem['PicturePath'] . '/' . $catItem['sID'] . '.gif'))
                    $catItem['smallPicture'] = $catItem['PicturePath'] . '/' . $catItem['sID'] . '.gif';
                else
                if ($catItem['logotype'] == NULL)
                    $catItem['smallPicture'] = '/products/empty.gif';
                else
                    $catItem['smallPicture'] = $catItem['logotype'];
            }
            $tpl = str_replace('%picture%', '<img width="70"  height="70" src="' .
                    $catItem['smallPicture'] . '" alt="' . $catItem['ShortTitle'] . '" />', $tpl);
            $tpl = str_replace('%goodID%', $catItem['accID'], $tpl); 

            if ($catItem["xit"] == 1)
            {
                $tpl = str_replace("%xit%", "<div class=\"xit\"><img src=\"/images/xit.gif\" width=\"70\" height=\"17\" alt=\"\" /></div>", $tpl);
            } elseif ($catItem["new"] == 1)
            {
                $tpl = str_replace("%xit%", "<div class=\"xit\"><img src=\"/images/new1.gif\" width=\"70\" height=\"17\" alt=\"\" /></div>", $tpl);
            } else
            {
                $tpl = str_replace("%xit%", "", $tpl);
            }

            return $tpl;
        }        
                
        function getProducts($pageID)
        {
            global $structureMgr, $templatesMgr;
            $content = '';
            $pager = '';
            
            $order = _get('order');

            //$topContent = $structureMgr->getData($pageID);

            $pNum = $structureMgr->getPageNumberByPageID($pageID);
            $URL = $structureMgr->getPathByPageID($pageID, false);

            $perPage = GetCfg('Producer.itemsPerPage');

            $startFrom = ($pNum - 1) * $perPage;
            $endAt = $startFrom + $perPage - 1;
            
            $plantID = $this->getPlantbyPageID($pageID);
             
            $items = $this->getItems($plantID,$startFrom, $endAt, $order);

            $cnt = $this->itemsCount;

            if ($endAt >= $cnt)
                $endAt = $cnt - 1;

            $pagesCount = ceil($cnt / $perPage);

            if ($pagesCount < $pNum)
            {
                trigger_error('Invalid pageNumber - possibly hacking or serious change in DB', PM_ERROR);
            }
            else
            {
                
                if ($pagesCount > 1)
                {
                    $tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/' . 'pager_filter1.html');
                    $purePager = '';

                    for ($i=1; $i <= $pagesCount; $i++)
                    {
                        $filter='products=1';
                        if ($i > 1)
                        {
                            $purePager .= ' - ';
                            $u = $URL . '/page' . $i;
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
                            $purePager .= '<a href="'.$u.'" class="levm">' . $i . '</a>';
                        }
                    }
                    switch ( $order ) {
                        case 'name' :$tpl = str_replace('%sel1%', 'selected="selected"', $tpl); break;
                        case 'price' :$tpl = str_replace('%sel2%', 'selected="selected"', $tpl); break;
                        case 'desc' :$tpl = str_replace('%sel3%', 'selected="selected"', $tpl); break;
                        case 'rating' :$tpl = str_replace('%sel4%', 'selected="selected"', $tpl); break;
                    }
                    $tpl = str_replace('%sel1%', '', $tpl);
                    $tpl = str_replace('%sel2%', '', $tpl);
                    $tpl = str_replace('%sel3%', '', $tpl);
                    $tpl = str_replace('%sel4%', '', $tpl);
                    $tpl = str_replace('%links%', $purePager, $tpl);
                    //$tpl = str_replace('action=""', 'action="?products=1"', $tpl);
                    

                    $tpl =str_replace('%catFilter%', '', $tpl);
                    $pager = str_replace('%links%', $purePager, $tpl);
                }
            }

            $i = 1;
            $content .= '<div class="items"><table cellpadding="0" cellspacing="0" class="items-table">';
            foreach ( $items as $item ) {
                if(($i-1) % GetCfg('Producer.itemsPerCol') == 0) {
                    $content .= '<tr>';
                }
                $style = 'td'.(($i > GetCfg('Producer.itemsPerCol')) ? 'dwn' : 'up').'left';
                $content .= $this->getFilledItemTemplate($item);
                if($i % GetCfg('Producer.itemsPerCol') == 0) {
                    $content .= '</tr>';
                }
                $i++;
            }
            $content .= '</table></div>';

            return $topContent.$pager.$content.$pager;        
        }
        
        function getCatItemProperties($pageID, $DataType, $parentID)
        {
            global $structureMgr;
            
            //print_r($pageID . " - " . $DataType . " - " . $parentID . "<br />");

            if ($pageID != -1)
                $md = $structureMgr->getMetaData($pageID);
            else
                $md['DataType'] = $DataType;

            $res = array();

            //print_r($md);

            switch ($md['DataType'])
            {
                case 'CatItem':
                {
                    if ($pageID != -1)
                    {
                        $q2 = 'SELECT accID FROM pm_as_parts WHERE sID = '.$pageID.' LIMIT 1';
                        list($accID) = mysql_fetch_row(mysql_query($q2));
                        if (!$accID)
                            trigger_error('Error fetching accID for CatItemProperties ' . mysql_error(), PM_FATAL);

                        $q = 'SELECT app.propListID, propName, accMeasure, propValue, isHidden
                                FROM pm_as_prop_list apl, pm_as_parts_properties app
                                WHERE app.accID='.$accID.' AND app.propListID = apl.propListID
                                ORDER BY apl.OrderNumber';

                        $qr = mysql_query($q);
                        if (!$qr)
                            trigger_error('Error while query - ' . mysql_error(), PM_FATAL);

                        while ( $row = mysql_fetch_row($qr) )
                        {
                            $res[] = $row;
                        }
                    } else {
                        $branch = $structureMgr->getCurrentBranch($parentID);
                        for ($i = count($branch) - 1; $i >=0; $i--)
                        {
                            $accCatID = $this->getCatIDByPageID($branch[$i]);
                            if ($accCatID == -1)
                                break;

                            $q2 = 'SELECT propListID, propName, accMeasure, \'\', isHidden
                                        FROM pm_as_prop_list WHERE accCatID='.$accCatID;
                            $qr = mysql_query($q2);
                            if (!$qr)
                                trigger_error('Error fetching propNames for CatItems - ' . mysql_error(), PM_FATAL);
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
                    return false;
            }
        }        
        function getItems($plantID,$startFrom, $endAt, $order)
        {
            $orderStr = ' ORDER BY ';
            if ($order == 'name') {
                $orderStr .= 'ShortTitle';
            } elseif ($order == 'price') {
                $orderStr .= 'salePrice';
            } elseif ($order == 'pricedesc') {
                $orderStr .= 'salePrice desc';
            } elseif ($order == 'rating') {
                $orderStr .= 'rating DESC, ShortTitle';
            } else {
                $orderStr .= 'ShortTitle';
            }

            $query = 'SELECT SQL_CALC_FOUND_ROWS
                    accID, p.sID, ShortTitle, deliveryCode, accPlantName, logotype, smallPicture, p.tplID, salePrice,
                    MustUseCompatibility, PicturePath, DescriptionTemplate, ptPercent, p.xit, p.new,
                        (SELECT SUM( r.grade ) / r.count /3
                        FROM pm_rating r
                        WHERE r.sID = s.sID
                        ) AS rating
                    FROM `pm_as_parts` p
                    LEFT JOIN pm_as_producer ap ON (ap.accPlantID = p.accPlantID)
                    LEFT JOIN pm_structure s ON (p.sID = s.sID)
                    LEFT JOIN pm_as_categories ac ON (s.pms_sID = ac.sID)
                    LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                    WHERE p.accPlantID='.$plantID.' AND s.isHidden=0
                    '.$orderStr.'
                    LIMIT '.$startFrom.','.GetCfg('Producer.itemsPerPage');
            $result = mysql_query($query);
            if (!$result)
                trigger_error('Invaid query. ' . mysql_error(), PM_FATAL);

            if (mysql_num_rows($result) == 0)
                trigger_error('Empty result for', PM_WARNING);

            $query = 'SELECT FOUND_ROWS() as itemsCount';
            $res = mysql_query($query);
            $row = mysql_fetch_assoc($res);
            $this->itemsCount = $row['itemsCount'];


            $catItems = array();

            while($item = mysql_fetch_assoc($result)) {
                if ($item['MustUseCompatibility'])
                {
                    $item['Compatibility'] = '';
                    $query2 = 'SELECT atc.carID, carModel, carName
                                   FROM pm_as_acc_to_cars atc
                                   LEFT JOIN pm_as_cars c ON (c.carID = atc.carID)
                                   WHERE accID=' . $item['accID'];
                    $result2 = mysql_query($query2);

                    if (!$result2)
                        trigger_error('Error retrieving car model links ' . mysql_error(), PM_FATAL);

                    while (false !== (list($carID, $carModel, $carName) = mysql_fetch_row($result2)))
                    {
                        if ($item['Compatibility'])
                            $item['Compatibility'] .= ', ';

                        $item['Compatibility'] .= $carModel;
                        if ($carName)
                            $item['Compatibility'] .= ' '.$carName;
                    }
                }
                $catItems[] = $item;
            }

            return $catItems;
        }        

        function getProducers($startFrom, $perPage)
        {
            $ids = "";

            for ($i = 0; $i < count($pageIDList); $i++)
            {
                if ($i > 0)
                    $ids .= ", ";
                $ids .= $pageIDList[$i];
            }

            $q = "SELECT DISTINCT accPlantID, accPlantName, logotype, pr.sID, s.URLName
                    FROM pm_structure s, pm_as_producer pr 
                    WHERE isHidden=0 AND pr.sID > 0 AND pr.sID=s.sID 
                    ORDER BY accPlantName LIMIT $startFrom, $perPage";
            $qr = mysql_query($q);

            if (!$qr)
                trigger_error("Error aqcuiring producers [$q] - " . mysql_error(), PM_FATAL);

            $res = array();

            while (false !== ($r = mysql_fetch_row($qr)))
            {
                $res[] = $r;
            }

            return $res;
        }        

        function getProducerList($page, $pageid)
        {
            global $structureMgr, $templatesMgr;
            
            $perPage = GetCfg('Producer.perPage');
            
            $q = "SELECT DISTINCT accPlantID, accPlantName, logotype FROM pm_structure s, pm_as_producer pr WHERE isHidden=0 AND pr.sID > 0 AND pr.sID=s.sID";
            $qr = mysql_query($q);
            $cnt = mysql_num_rows($qr);
            
            $pagesCount = ceil($cnt / $perPage);
            
            $startFrom = $page * $perPage;
            $purePager = "";
            $URL = $structureMgr->getPathByPageID($pageid, true);
                    for ($i=0; $i < $pagesCount; $i++)
                    {
                        if ($i > 0)
                        {
                            $purePager .= ' - ';
                            $u = $URL . '?pg=' . $i;
                        }
                        else
                           $u = $URL;

                        if ($filter)
                            $u .= '?' . $filter;

                        if ($i == $page)
                        {
                            $purePager .= ($i+1);
                        }
                        else
                        {
                            $purePager .= '<a href="'.$u.'" class="levm">' . ($i+1) . '</a>';
                        }
                    }            

                $producers = "
                
                <div class=\"podbor\">
                <table  cellspacing=\"0\" cellpadding=\"0\" class=\"both\">
                <tr>
                    <td class=\"leftpage\">
                        Страницы: $purePager
                    </td>
                    <td class=\"rightpage\">

                    </td>
                </tr>
                </table>
                </div>                
                <div class=\"podbor\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";

                $prodList = array();

                $prodList = $this->getProducers($startFrom, $perPage);

                for ($i=0; $i < count($prodList); $i++)
                {
                    if($i % 5 == 0) {

                        $producers .= "\n<tr>\n";
                    }
                    if (($prodList[$i][2]) && file_exists(GetCfg("ROOT") . $prodList[$i][2]))
                        $isrc = $prodList[$i][2];
                    else
                        $isrc = "/products/empty.gif";

                    $prodID = $prodList[$i][0];
                    $pg = "";
                    $URL = $structureMgr->getPathByPageID($prodList[$i][3], true);
                    $producers .= "<td align=\"center\" width=20%><a href=\"" . $URL . "\"><img alt=\"" . $prodList[$i][1] . "\" src=\"$isrc\" /></a><br /><a href=\"" . $URL . "\">" . $prodList[$i][1] .
                                  "</a><br /><br/><br/></td>\n";
                    if (($i+1) % 5 == 0 || $i==(count($prodList)-1) )
                        $producers .= "\n</tr>\n";
                }

                $producers .= "</table></div>
                                <div class=\"podbor\">
                <table  cellspacing=\"0\" cellpadding=\"0\" class=\"both\">
                <tr>
                    <td class=\"leftpage\">
                        Страницы: $purePager
                    </td>
                    <td class=\"rightpage\">

                    </td>
                </tr>
                </table>
                </div>";
            return $producers;
        }

        function updateAdditionalColumns($args)
        {
            return false;
        }
        
        
    }
?>

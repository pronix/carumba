<?php
    echo "<b>".$page["ShortTitle"]."</b><br />";
    echo $page["Content"];

    if (isset($_REQUEST["searchtext"]))
    {
        
        if (!isset($_GET["idpages"]))
        {
            $_GET["idpages"] = 0;
        }
        $prodpage = 5;  
        
            $searchtext = $_REQUEST["searchtext"];
            
            $searchArray = explode(' ',$searchtext);

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
                                p.accID, p.sID, s.pms_sID, s.ShortTitle, deliveryCode, accPlantName, logotype, smallPicture, p.tplID, salePrice,
                                MustUseCompatibility, PicturePath, DescriptionTemplate, ptPercent,
                                CONCAT(s.ShortTitle, ' ', s.Title, ' ', ap.accPlantName, ' ', prop.propValue),
                                CONCAT(ap.accPlantName, ' ', s.ShortTitle, ' ', s.Title, ' ', prop.propValue),
                                CONCAT(prop.propValue, ' ', ap.accPlantName, ' ', s.ShortTitle, ' ', s.Title),
                                CONCAT(prop.propValue, ' ', s.ShortTitle, ' ', s.Title, ' ', ap.accPlantName),
                                    (SELECT SUM( r.grade ) / r.count /3
                                    FROM pm_rating r
                                    WHERE r.sID = s.sID
                                    ) AS rating
                                FROM `pm_as_parts` p
                                LEFT JOIN pm_as_producer ap ON (ap.accPlantID = p.accPlantID)
                                LEFT JOIN pm_structure s ON (p.sID = s.sID && s.isHidden = 0)
                                LEFT JOIN pm_as_categories ac ON (s.pms_sID = ac.sID)
                                LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                                LEFT JOIN pm_as_parts_properties prop ON (prop.accID = p.accID)
                                WHERE s.DataType = 'CatItem' && p.notAvailable = 0
                                ".$whereText."
                                GROUP BY p.accID, s.sID
                                ".$orderStr." LIMIT ".($_GET["idpages"]*$prodpage).",".$prodpage;
            $result = mysql_query($query);
            $query = "SELECT FOUND_ROWS() as itemsCount";
            $res = mysql_query($query);
            $row = mysql_fetch_assoc($res);
            $prodcnt  = $row['itemsCount'];
            if ($prodcnt == 0)
            {
               echo "По Вашему запросу ничего не найдено!<br>"; 
            }
            
          $num_product = mysql_num_rows($result);        
          for ($i=0;$i<$num_product;$i++)
          {
            $product = mysql_fetch_array($result);
          $qr1 = mysql_query("SELECT sID, URLName 
                             FROM pm_structure 
                             WHERE sID=".$product["pms_sID"]);
          $rp = mysql_fetch_array($qr1);
            
            ?><a href="<?php echo getUrlByID("catalog",$rp["URLName"],0,$product["sID"]); ?>"><?php echo $product["ShortTitle"];  ?></a><br /><?php
            echo "<div class='prod'>";

                    $query2 = "SELECT atc.carID, carModel, carName FROM pm_as_acc_to_cars atc LEFT JOIN pm_as_cars c ON (c.carID = atc.carID)
                    WHERE accID=" . $product["accID"];
                    $result2 = mysql_query($query2);
                    $num_c = mysql_num_rows($result2); 
                    if ($num_c > 0)
                    {
                        $item["Compatibility"] = '';
                        while (false !== (list($carID, $carModel, $carName) = mysql_fetch_row($result2)))
                        {
                            if ($item["Compatibility"])
                                $item["Compatibility"] .= ", ";

                            $item["Compatibility"] .= "$carModel";
                            if ($carName)
                                $item["Compatibility"] .= " $carName";
                        }
                        echo "Марка: ".$item["Compatibility"]."<br />";
                    }
                    
            echo "Производитель: ".$product["accPlantName"]."<br />";
            $query_1="
            SELECT propValue, propName 
                FROM pm_as_parts_properties, pm_as_prop_list
            WHERE 
                pm_as_parts_properties.accID=".$product["accID"]." &&
                pm_as_parts_properties.propListID=pm_as_prop_list.propListID                
                ";
            $result1 = mysql_query($query_1);
            if (!$result1) {echo "<br>Ошибка обработки1.<br>";};
            $num_prop = mysql_num_rows($result1);        
            for ($j=0;$j<$num_prop;$j++)
            {
                $prop = mysql_fetch_array($result1);
                echo $prop["propName"].": ".$prop["propValue"]."<br />";
            }
            //beg
            $bonusName="";
            $bonusValue="";
            $oldPriceStyle="";
            switch($product["ptID"])
            {
                case 2: $typeClass="t_bonus"; $bonusName = "<strong>Спецпредложение (<a href=\"".$root_path."/club/\">по карте</a>):</strong>";  $bonusValue = "<span class=\"t_bonus\">".$product["ptPercent"]."% </span><br/> "; break;
                case 3: $typeClass="t_bonus"; $bonusName = "<strong>Спецпредложение (<a href=\"".$root_path."/club/\">по карте</a>):</strong>";  $bonusValue = "<span class=\"t_bonus\">".$product["ptPercent"]."% </span><br/> "; break;
                case 4: $typeClass="t_bonus"; $bonusName = "<strong>Спецпредложение (<a href=\"".$root_path."/club/\">по карте</a>):</strong>";  $bonusValue = "<span class=\"t_bonus\">".$product["ptPercent"]."% </span><br/> "; break;

                case 5: $typeClass="t_salepr"; $oldPriceStyle="<span class=\"t_old\">"; $bonusName = "<strong>Распродажа:</strong>";  $bonusValue = "<span class=\"t_sale\">".$product["ptPercent"]."% </span><br/> "; break;
                case 6: $typeClass="t_salepr"; $oldPriceStyle="<span class=\"t_old\">"; $bonusName = "<strong>Распродажа:</strong>";  $bonusValue = "<span class=\"t_sale\">".$product["ptPercent"]."% </span><br/> "; break;
                case 7: $typeClass="t_salepr"; $oldPriceStyle="<span class=\"t_old\">"; $bonusName = "<strong>Распродажа:</strong>";  $bonusValue = "<span class=\"t_sale\">".$product["ptPercent"]."% </span><br/> "; break;
            }
            if($bonusName && $bonusValue)
            {
                echo "$bonusName - $bonusValue";
            }

            if ($product["ptPercent"] == 0)
                $firstPrice = "<strong>" . round($product["salePrice"] - ($product["salePrice"] * 5 / 100)) . "</strong>";
            else
                $firstPrice = "<strong><span class=\"".$typeClass."\">" .
                              round($product["salePrice"] - ($product["salePrice"] * $product["ptPercent"] / 100)) .
                              "</span></strong>";

            $price_out = "$firstPrice" . " / " . $oldPriceStyle.$product["salePrice"].($oldPriceStyle ? "</span>":"") . " руб.";                  
            //end
            
            echo "</div>
            <div class='incart'><a href='".getUrlByID($page["URLName"]).$_GET["idpages"]."/pages/".$product["accID"]."/cart/".$searchtext."/'><img src=".$root_path."/img/incart.gif alt='Поместить в корзину' align='left' border='0'></a> <br class='small1' />".$price_out."</div>"; 
            if ($i < $num - 1)
            ?><img src="<?php echo $root_path;?>/img/polk_.gif" alt=""><br /><?php
          }
          
          if ($prodcnt > 0)
            echo "Страницы: ";
          $pages = ceil($prodcnt / $prodpage);
          for ($i=0;$i<$pages;$i++)
          {
              if ($_GET["idpages"] == $i)
                echo " - ".($i+1); 
              else
                echo " - <a href='".(getUrlByID($page["URLName"])).$i."/pages/".$searchtext."/'>".($i+1)."</a>"; 
          }
            
    } else echo "Введите запрос для поиска!<br>"; 
?>
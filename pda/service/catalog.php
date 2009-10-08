<?php
        $FILTER = array(
                                    '633' => array( "result"=>"масла",
                                                    "paramId"=>"2",
                                                    "showSubCats"=>true,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"chk",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>"4"
                                                  ),
                                    '7' => array(   'result'=>'шины',
                                                    'paramId'=>'2',
                                                    'showSubCats'=>true,
                                                    'showSubCatsOnly'=>false,
                                                    'catType'=>'',
                                                    'paramType'=>'chk',
                                                    'prodType'=>'chk',
                                                    'otherParamId'=>'3'
                                                   ),
                                    '1952' => array("result"=>"охранной системы",
                                                    "paramId"=>"1",
                                                    "showSubCats"=>true,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"chk",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  ),
                                    '1474' => array("result"=>"автолампы",
                                                    "paramId"=>"1",
                                                    "showSubCats"=>true,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"chk",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  ),
                                    '1580' => array("result"=>"автозвук",
                                                    "paramId"=>"1",
                                                    "showSubCats"=>true,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"chk",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  ),
                                    '8' => array(   "result"=>"аккумулятора",
                                                    "paramId"=>"2",
                                                    "showSubCats"=>true,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"chk",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  ),
                                    '1880' => array("result"=>"жидкости и смазки",
                                                    "paramId"=>"1",
                                                    "showSubCats"=>true,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"chk",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  ),
                                    '4' => array(   "result"=>"зап части",
                                                    "paramId"=>"2",
                                                    "showSubCats"=>false,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  ),
                                    '1951' => array("result"=>"автохимии",
                                                    "paramId"=>"1",
                                                    "showSubCats"=>true,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"chk",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                 ),
                                    '3412' => array("result"=>"доп. устройств",
                                                    "paramId"=>"1",
                                                    "showSubCats"=>true,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"chk",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                 ),
                                    '2255' => array("result"=>"присадок",
                                                    "paramId"=>"2",
                                                    "showSubCats"=>false,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"chk",
                                                    "paramType"=>"",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  ),
                                    '5' => array(   "result"=>"тюнинг ваз",
                                                    "paramId"=>"2",
                                                    "showSubCats"=>false,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"chk",
                                                    "paramType"=>"",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  ),
                                    '1949' => array("result"=>"аксессуаров",
                                                    "paramId"=>"2",
                                                    "showSubCats"=>false,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"chk",
                                                    "paramType"=>"",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  ),
                                    '1950' => array("result"=>"инструментов",
                                                    "paramId"=>"2",
                                                    "showSubCats"=>false,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"chk",
                                                    "paramType"=>"",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>""
                                                  )
                                    );
    
    if (isset($_GET["idcatalog"]))
    {
        $query_1="select sID, ShortTitle, URLName, pms_sID from `pm_structure` where DataType = 'Category'
                AND `isHidden` =0
                AND isDeleted =0 
                AND URLName='".(mysql_escape_string($_GET["idcatalog"]))."'";
        $result = mysql_query($query_1);
        if (!$result) {echo "<br>Ошибка обработки1.<br>";};
        $num = mysql_num_rows($result);
        if ($num > 0)
        {
          $cat = mysql_fetch_array($result);
          $catalogID = $cat["sID"];
          $catalogName = $cat["ShortTitle"];
          $catalogUrl = $cat["URLName"];
          $catalogParent = $cat["pms_sID"];
          $cntneed = 1;          
        } else
        {
            $catalogID = $page["sID"];
            $catalogName = $page["ShortTitle"]; 
            $catalogUrl = $page["URLName"];           
            $cntneed = 0;
        }
    } else
    {
       $catalogID = $page["sID"];
       $catalogName = $page["ShortTitle"];
       $catalogUrl = $page["URLName"];
       $cntneed = 0;
    }
    
    if (isset($_GET["idproduct"]) && ($_GET["idproduct"] > 0))
    {
          $query_1="
                SELECT pm_structure.ShortTitle, accPlantName, pm_as_parts.accID, salePrice, pm_structure.sID, pm_structure.Content, pm_as_categories.PicturePath, pm_as_pricetypes.ptPercent, pm_as_parts.ptID
                FROM pm_as_parts, pm_as_producer, pm_as_prop_list, pm_as_pricetypes, pm_structure LEFT JOIN pm_as_categories ON (pm_structure.pms_sID = pm_as_categories.sID)
                WHERE
                pm_as_pricetypes.ptID=pm_as_parts.ptID && 
                pm_structure.sID =".$_GET["idproduct"]." && 
                pm_as_parts.sID = pm_structure.sID && 
                pm_as_parts.notAvailable =0 && 
                pm_structure.isHidden =0 && 
                pm_structure.isDeleted =0 && 
                pm_structure.isVersionOfParent =0 && 
                pm_as_parts.accPlantID = pm_as_producer.accPlantID";
          $result = mysql_query($query_1);
          if (!$result) {echo "<br>Ошибка обработки11.<br>";};
          $num_product = 1;        
          for ($i=0;$i<$num_product;$i++)
          {
            $product = mysql_fetch_array($result);
            echo "<b>".$product["ShortTitle"]."</b><br />";
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
            echo "</div>";
            //if (file_exists('http://www.carumba.ru".$product["PicturePath"]."/".$product["sID"]."_2.jpg'))
              echo "<img src='http://www.carumba.ru".$product["PicturePath"]."/".$product["sID"]."_2.jpg' alt='' border=0>";
            echo "<div class='incart'><a href='".(getUrlByID($page["URLName"],$catalogUrl,$_GET["idgroup"],$product["sID"])).$product["accID"]."/cart/'><img src=".$root_path."/img/incart.gif alt='Поместить в корзину' align='left' border='0'></a> <br class='small1' />".$price_out."</div>"; 
            if ($i < $num - 1)
            ?><img src="<?php echo $root_path;?>/img/polk_.gif" alt=""><br /><?php
            echo "<b>Подробнее о товаре</b><div class='prod'>".$product["Content"]."</div>";
          }    
    } elseif ((isset($_GET["idgroup"]) && ($_GET["idgroup"] > 0)))
    { 
        if (!isset($_GET["idpages"]))
        {
            $_GET["idpages"] = 0;
        }
        $prodpage = 5;  
        $query_1="select propValue from `pm_as_parts_properties` where propId = '".$_GET["idgroup"]."'";
        $result = mysql_query($query_1);
        if (!$result) {echo "<br>Ошибка обработки1.<br>";};
        $num = mysql_num_rows($result);
        if ($num > 0)
        {
          $group = mysql_fetch_array($result);
          echo "<b>".$group["propValue"]."</b><br />";
          $query_1="
            SELECT count(*) as cnt
                FROM pm_structure, pm_as_parts, pm_as_producer, pm_as_parts_properties
            WHERE 
                pm_structure.pms_sID =".$catalogID." && 
                pm_as_parts.sID = pm_structure.sID && 
                pm_as_parts.accID = pm_as_parts_properties.accID &&                 
                pm_as_parts.notAvailable =0 && 
                pm_structure.isHidden =0 && 
                pm_structure.isDeleted =0 && 
                pm_structure.isVersionOfParent =0 &&
                pm_as_parts_properties.propValue = '".$group["propValue"]."' &&
                pm_as_parts.accPlantID = pm_as_producer.accPlantID";
          $result = mysql_query($query_1);
          if (!$result) {echo "<br>Ошибка обработки1.<br>";};
          $prodcnt = mysql_fetch_array($result);              
          $query_1="
            SELECT pm_structure.ShortTitle, accPlantName, pm_as_parts.accID, salePrice, pm_structure.sID, pm_as_parts.ptID, pm_as_pricetypes.ptPercent
                FROM pm_structure, pm_as_parts, pm_as_producer, pm_as_pricetypes, pm_as_parts_properties
            WHERE
                pm_as_pricetypes.ptID=pm_as_parts.ptID && 
                pm_structure.pms_sID =".$catalogID." && 
                pm_as_parts.sID = pm_structure.sID && 
                pm_as_parts.accID = pm_as_parts_properties.accID &&                                 
                pm_as_parts.notAvailable =0 && 
                pm_structure.isHidden =0 && 
                pm_structure.isDeleted =0 && 
                pm_structure.isVersionOfParent =0 &&
                pm_as_parts_properties.propValue = '".$group["propValue"]."' &&
                pm_as_parts.accPlantID = pm_as_producer.accPlantID
                order by pm_structure.OrderNumber desc
                limit ".($_GET["idpages"]*$prodpage).",".$prodpage;
          $result = mysql_query($query_1);
          if (!$result) {echo "<br>Ошибка обработки1.<br>";};
          $num_product = mysql_num_rows($result);        
          for ($i=0;$i<$num_product;$i++)
          {
            $product = mysql_fetch_array($result);
            ?><a href="<?php echo getUrlByID($page["URLName"],$catalogUrl,$_GET["idgroup"],$product["sID"]); ?>"><?php echo $product["ShortTitle"];  ?></a><br /><?php
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
            <div class='incart'><a href='".(getUrlByID($page["URLName"],$catalogUrl,$_GET["idgroup"])).$_GET["idpages"]."/pages/".$product["accID"]."/cart/'><img src=".$root_path."/img/incart.gif alt='Поместить в корзину' align='left' border='0'></a> <br class='small1' />".$price_out."</div>"; 
            if ($i < $num - 1)
             ?><img src="<?php echo $root_path;?>/img/polk_.gif" alt=""><br /><?php
          }
          
          echo "Страницы: ";
          $pages = ceil($prodcnt["cnt"] / $prodpage);
          for ($i=0;$i<$pages;$i++)
          {
              if ($_GET["idpages"] == $i)
                echo " - ".($i+1); 
              else
                echo " - <a href='".(getUrlByID($page["URLName"],$catalogUrl,$_GET["idgroup"])).$i."/pages/'>".($i+1)."</a>"; 
          }
        }
    } elseif ((isset($catalogParent))&&(isset($FILTER[$catalogParent]))&&(!$FILTER[$catalogParent]['showSubCats'])&&(isset($_GET["idcatalog"])))
    {
        if (!isset($_GET["idpages"]))
        {
            $_GET["idpages"] = 0;
        }
        $prodpage = 5;  
          echo "<b>".$catalogName."</b><br />";
          $query_1="
            SELECT count(*) as cnt
                FROM pm_structure, pm_as_parts, pm_as_producer
            WHERE 
                pm_structure.pms_sID =".$catalogID." && 
                pm_as_parts.sID = pm_structure.sID && 
                pm_as_parts.notAvailable =0 && 
                pm_structure.isHidden =0 && 
                pm_structure.isDeleted =0 && 
                pm_structure.isVersionOfParent =0 &&
                pm_as_parts.accPlantID = pm_as_producer.accPlantID";
          $result = mysql_query($query_1);
          if (!$result) {echo "<br>Ошибка обработки1.<br>";};
          $prodcnt = mysql_fetch_array($result);              
          $query_1="
            SELECT pm_structure.ShortTitle, accPlantName, pm_as_parts.accID, salePrice, pm_structure.sID, pm_as_parts.ptID, pm_as_pricetypes.ptPercent
                FROM pm_structure, pm_as_parts, pm_as_producer, pm_as_pricetypes
            WHERE
                pm_as_pricetypes.ptID=pm_as_parts.ptID && 
                pm_structure.pms_sID =".$catalogID." && 
                pm_as_parts.sID = pm_structure.sID && 
                pm_as_parts.notAvailable =0 && 
                pm_structure.isHidden =0 && 
                pm_structure.isDeleted =0 && 
                pm_structure.isVersionOfParent =0 &&
                pm_as_parts.accPlantID = pm_as_producer.accPlantID
                order by pm_structure.OrderNumber desc
                limit ".($_GET["idpages"]*$prodpage).",".$prodpage;
          $result = mysql_query($query_1);
          if (!$result) {echo "<br>Ошибка обработки1.<br>";};
          $num_product = mysql_num_rows($result);        
          for ($i=0;$i<$num_product;$i++)
          {
            $product = mysql_fetch_array($result);
            ?><a href="<?php echo getUrlByID($page["URLName"],$catalogUrl,0,$product["sID"]); ?>"><?php echo $product["ShortTitle"];  ?></a><br /><?php
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
            <div class='incart'><a href='".(getUrlByID($page["URLName"],$catalogUrl)).$_GET["idpages"]."/pages/".$product["accID"]."/cart/'><img src=".$root_path."/img/incart.gif alt='Поместить в корзину' align='left' border='0'></a> <br class='small1' />".$price_out."</div>"; 
            if ($i < $num - 1)
            ?><img src="<?php echo $root_path;?>/img/polk_.gif" alt=""><br /><?php
          }
          
          echo "Страницы: ";
          $pages = ceil($prodcnt["cnt"] / $prodpage);
          for ($i=0;$i<$pages;$i++)
          {
              if ($_GET["idpages"] == $i)
                echo " - ".($i+1); 
              else
                echo " - <a href='".(getUrlByID($page["URLName"],$catalogUrl)).$i."/pages/'>".($i+1)."</a>"; 
          }
    
    } else
    {        
        echo "<b>".$catalogName."</b><br />"; 
        if ($catalogID == 3)
            $order = '`OrderNumber`';
        else
            $order = '`ShortTitle`';
        $query_1="
              SELECT *
                FROM `pm_structure`
              WHERE `pms_sID` =".$catalogID."
                AND DataType = 'Category'
                AND `isHidden` =0
                AND isDeleted =0 
              ORDER BY ".$order." ASC";
        $result = mysql_query($query_1);
        if (!$result) {echo "<br>Ошибка обработки1.<br>";};
        $num_category = mysql_num_rows($result);        
        for ($i=0;$i<$num_category;$i++)
        {
            $catalog = mysql_fetch_array($result);
            $cnt["cnt"] = 1;
            if ($cntneed == 1)
            {
                $query_1="
                SELECT count(*) as cnt
                    FROM `pm_structure`
                WHERE `pms_sID` =".$catalog["sID"]."
                    AND DataType = 'Catitem'
                    AND `isHidden` =0
                    AND isDeleted =0";
                $result1 = mysql_query($query_1);
                if (!$result1) {echo "<br>Ошибка обработки1.<br>";};
                $cnt = mysql_fetch_array($result1);
             }
            if ($cnt["cnt"] > 0)
            {
                ?><a href="<?php echo getUrlByID($page["URLName"],$catalog["URLName"]); ?>"><?php echo $catalog["ShortTitle"]; 
                if ($cntneed == 1) echo " (".$cnt["cnt"].")"; ?></a><br /><?php 
                if ($i < $num - 1)
                    ?><img src="<?php echo $root_path;?>/img/polk_.gif" alt=""><br /><?php
            }
        }
        
        if ($num_category == 0)
        {    
        $query_1="
            SELECT pm_as_parts_properties.propValue, pm_as_parts_properties.propid, COUNT( pm_as_parts_properties.propID ) AS countNum,pm_as_prop_list.OrderNumber
            FROM pm_structure, pm_as_parts_properties, pm_as_parts, pm_as_prop_list
            WHERE                 
                pm_as_prop_list.propListID=pm_as_parts_properties.propListID &&
                pm_as_prop_list.OrderNumber = ".$FILTER[$catalogParent]['paramId']." &&
                pm_structure.pms_sID =".$catalogID." && 
                pm_as_parts.accID = pm_as_parts_properties.accID && 
                pm_as_parts.sID = pm_structure.sID && 
                pm_as_parts.notAvailable =0 && 
                pm_structure.isHidden =0 && 
                pm_structure.isDeleted =0 && 
                pm_structure.isVersionOfParent =0
            GROUP BY pm_as_parts_properties.propValue
            ORDER BY pm_as_parts_properties.propValue            
            ";
            
            $result = mysql_query($query_1);
            if (!$result) {echo "<br>Ошибка обработки1.<br>";};
            $num_category = mysql_num_rows($result);        
            for ($i=0;$i<$num_category;$i++)
            {
                $group = mysql_fetch_array($result);
                ?><a href="<?php echo getUrlByID($page["URLName"],$catalogUrl,$group["propid"]); ?>"><?php 
                echo $group["propValue"]." (".$group["countNum"].")"; ?></a><br /><?php 
                if ($i < $num - 1)
                ?><img src="<?php echo $root_path;?>/img/polk_.gif" alt=""><br /><?php
            }

        }
    }
    
?>    
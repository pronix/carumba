<?php
    class Catalogue extends AbstractModule
    {
        var $producers, $dNames;
		var $itemsCount = 0;

		var $FILTER_CONSTANT_ARRAY = array(
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
                                    '6542' => array("result"=>"gps",
                                                    "paramId"=>"1",
                                                    "showSubCats"=>false,
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
                                                    "otherParamId"=>"",
                                                    "plantID"=>"2"
                                                  ),
                                    '6792' => array(   "result"=>"аксессуары 4х4",
                                                    "paramId"=>"2",
                                                    "showSubCats"=>false,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>"",
                                                    "plantID"=>"4"
                                                  ),
                                    '6815' => array(   "result"=>"аксессуары ино",
                                                    "paramId"=>"2",
                                                    "showSubCats"=>false,
                                                    "showSubCatsOnly"=>false,
                                                    "catType"=>"",
                                                    "paramType"=>"",
                                                    "prodType"=>"chk",
                                                    "otherParamId"=>"",
                                                    "plantID"=>"4"
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
									                "paramId"=>"1",
									                "showSubCats"=>true,
									                "showSubCatsOnly"=>false,
									                "catType"=>"",
									                "paramType"=>"chk",
									                "prodType"=>"chk",
									                "otherParamId"=>""
									              )
                                                  
									);

        function __construct()
        {
            global $req_params;
            $this->name = 'Catalogue';
            $this->publicFunctions = array('getContent',
                                           'getItemType',
                                           'getSubItemType',
                                           'getItemDesc',
                                           'getSpecificDataForEditing',
                                           'updateSpecificData',
                                           'getSpecificBlockDesc',
                                           'getAdditionalColumns',
                                           'updateAdditionalColumns',
                                           'getDataListByPageID'
                                         );
            $this->producers = array();
            $this->priceTypes = array();
            $this->dNames = array(
                'CatItem' =>
                    array('accPlantID',
                          'deliveryID',
                          'deliveryCode',
                          'basePrice',
                          'salePrice',
                          'smallPicture',
                          'stdPicture',
                          'bigPicture',
                          'ptID',
                          'notAvailable',
                          'new',
                          'xit',
                          'main'
                         ),
                'Category' =>
                    array('MustUseCompatibility',
                          'PicturePath',
                          'DescriptionTemplate',
                          'FilterTemplate'
                         )
                    );

            $this->additionalColumns = array(
                        'CatItem' => array('basePrice' => 'text',
                                           'salePrice' => 'text',
                                           'ptID' => 'text',
                                           'notAvailable' => 'checkbox',
                                           'new' => 'checkbox',
                                           'xit' => 'checkbox',
                                           'main' => 'checkbox'
                                        ),
                        'Category' => array()
            );

            SetCfg('Catalogue.perPage', 10);
        }

        function getSpecificDataForEditing($args)
        {
            global $structureMgr, $templatesMgr;

            if ($args[0] != -1) {
                $md = $structureMgr->getMetaData($args[0]);
            } else {
                $md['DataType'] = $args[1];
            }

            switch ($md['DataType']) {

                case 'Category':
                    $sData = array();

                    if ($args[0] != -1) //for edit
                    {
                        $q = "SELECT MustUseCompatibility, PicturePath, DescriptionTemplate, FilterTemplate FROM pm_as_categories WHERE sID=$args[0]";

                        $qr = mysql_query($q);

                        if (!$qr)
                            trigger_error("Error while trying to get SpecificData [$q] - " . mysql_error(), PM_FATAL);

                        if (mysql_num_rows($qr) == 0)
                            trigger_error("Data incompatibiliy while trying to get SpecificData [$q]", PM_FATAL);

                        $r = mysql_fetch_assoc($qr);
                    }
                    else //for adding
                    {
                        foreach ($this->dNames[$md["DataType"]] as $dn)
                            $r[$dn] = "";
                    }

                    $sData["PicturePath"] = array ("Путь к изображениям товаров", "text", 80, $r["PicturePath"]);
                    $sData["DescriptionTemplate"] = array ("Шаблон страницы описания товара", "dropdown", "", array("details1.xml" => "details1.xml"));

                    $sData["FilterTemplate"] = array ("Шаблон для фильтра", "dropdown", $r["FilterTemplate"],
                                                      array("std_select.html" => "std_select.html",
													  "accumulator_select.html" => "accumulator_select.html",
                                                      "autostaff_select.html" => "autostaff_select.html",
                                                      "autostaff_select2.html" => "autostaff_select2.html",
													  "autosound_select.html" => "autosound_select.html",
													  "securitysystems_select.html" => "securitysystems_select.html",
                                                      "oils_select.html" => "oils_select.html",
                                                      "gps_select.html" => "gps_select.html",
                                                      "autolamps_select.html" => "autolamps_select.html",
													  "tire_select.html" => "tire_select.html",
                                                      "liquids_select.html" => "liquids_select.html"
                                                      )
                                                      );

                    $sData["MustUseCompatibility"] = array ("Использовать у товаров<br />совместимость с автомобилями", "checkbox", $r["MustUseCompatibility"]);
                    return $sData;
                    break;

                case 'CatItem':

                    $sData = array();

                    $this->getProducers();
                    $this->getPriceTypes();

                    $sData['accPlantID'] = array('Производитель', 'dropdown', '', $this->producers, /*, "новый", 20*/);

                    if ($args[0] != -1) //for edit
                    {
                        $q = 'SELECT accID, accPlantID, deliveryID, deliveryCode, basePrice,
                                salePrice, smallPicture, stdPicture, bigPicture,
                                MustUseCompatibility, ptID, tplID, xit, new, main
                                FROM pm_as_parts p LEFT JOIN pm_as_categories c ON (p.accCatID = c.accCatID)
                                WHERE p.sID='.$args[0];

                        $qr = mysql_query($q);

                        if (!$qr || mysql_num_rows($qr) != 1)
                            trigger_error("Error while trying to get SpecificData [$q] - " . mysql_error(), PM_FATAL);

                        $r = mysql_fetch_assoc($qr);

                    } else /*for adding */ {
                        $q = 'SELECT MustUseCompatibility FROM pm_as_categories WHERE sID='.$args[2].' LIMIT 1';

                        $qr = mysql_query($q);

                        if (!$qr)
                            trigger_error("Error while trying to get SpecificData [$q] - " . mysql_error(), PM_FATAL);

                        list($muc) = mysql_fetch_row($qr);

                        foreach ($this->dNames[$md['DataType']] as $dn)
                            $r[$dn] = '';

                        $r['MustUseCompatibility'] = $muc;
                        $r['tplID'] = '2';
                    }

                    $sData["accPlantID"][2] = $r["accPlantID"];

                    $sData["deliveryID"] = array ("Поставщик", "text", 10, $r["deliveryID"]);
                    $sData["deliveryCode"] = array ("Код поставщика", "text", 10, $r["deliveryCode"]);
                    $sData["basePrice"] = array ("Цена закупки, руб", "text", 10, $r["basePrice"]);
                    $sData["salePrice"] = array ("Базовая цена, руб", "text", 10, $r["salePrice"]);

                    $sData["ptID"] = array("Тип цены", "dropdown", $r["ptID"], $this->priceTypes, /*"новый", 20*/);
//!!!SD_EDIT                    $sData["tplID"] = array("Шаблон (1 - для запчастей, 2 - для остальных товаров)", "text", 4, $r["tplID"]);
                    $sData["notAvailable"] = array ("Нет в наличии", "checkbox", $r["notAvailable"]);
                    $sData["new"] = array ("Новинки", "checkbox", $r["new"]);
                    $sData["xit"] = array ("Хит", "checkbox", $r["xit"]);
                    $sData["main"] = array ("Главная", "checkbox", $r["main"]);

                    //Specific (per category) item properties
                    /**
                     * В переменную props записываются значения специальных параметров
                     */
                    $props = $this->getCatItemProperties( $args[0],
                                                          isset($args[1]) ? $args[1] : '',
                                                          isset($args[2]) ? $args[2] : -1
                                                         );

                    if (count($props) > 0)
                    {
                        $sData['Props'] = array('Специальные параметры', 'fieldset');

                        foreach ($props as $ip => $v)
                        {
                            $propName = $v[1];
                            if ($v[2] != '')
                                $propName .= ', ' . $v[2];

                            $sData['Props'][2]['prop' . $v[0]] = array($propName, 'text', 50, $v[3]);
                        }
                    }

                    $sData["smallPicture"] = array ("Маленькое изображение", "text", 65, $r["smallPicture"]);
                    $sData["stdPicture"] = array ("Стандартное изображение", "text", 65, $r["stdPicture"]);
                    $sData["bigPicture"] = array ("Большое изображение", "text", 65, $r["bigPicture"]);

                    if ($r["MustUseCompatibility"])
                    {
                        if ($args[0] != -1) //for edit
                        {
                            $ida = $args[0];
                        } else
                        {
                            $ida = $args[2];
                        }
                        
                        
                        if ($structureMgr->getFindPageID($ida, false, 4))
                        {
                            $plantID = 2;
                        } else
                        {
                            $plantID = 4;    
                        }
                        
                        $q2 = "SELECT c.carID, CONCAT(plantName, ' ', carModel) FROM pm_as_cars c LEFT JOIN pm_as_autocreators ac ON (ac.plantID = c.plantID) WHERE c.plantID=".$plantID;
                        $qr2 = mysql_query($q2);

                        if (!$qr2)
                            trigger_error("Error retrieving car models [$q2] - " . mysql_error(), PM_FATAL);

                        $cars = array();

                        while (false !== (list($carID, $carModel) = mysql_fetch_row($qr2)))
                        {
                            $cars[$carID] = $carModel;
                        }

                        $carLinks = array();

                        if ($args[0] != -1)
                        {
                            $q2 = "SELECT carID FROM pm_as_acc_to_cars WHERE accID=" . $r["accID"];
                            $qr2 = mysql_query($q2);

                            if (!$qr2)
                                trigger_error("Error retrieving car model links [$q2] - " . mysql_error(), PM_FATAL);

                            while (false !== (list($carID) = mysql_fetch_row($qr2)))
                            {
                                $carLinks[$carID] = "1";
                            }
                        }
                        $sData["carLinks"] = array("Совместимость", "checkbox_list", $carLinks, $cars, 3);
                    }
                    return $sData;
                    break;
            }

            return array();
        }

        function updateCompatibility($accID, $cleanBeforeInsert)
        {
            if (!$accID)
                trigger_error("accID must be provided", PM_FATAL);

            $q2 = "SELECT carID FROM pm_as_cars";
            $qr2 = mysql_query($q2);

            if (!$qr2)
                trigger_error("Error retrieving car models [$q2] - " . mysql_error(), PM_FATAL);

            if ($cleanBeforeInsert)
                mysql_query("DELETE FROM pm_as_acc_to_cars WHERE accID=$accID");

            while (false !== (list($carID) = mysql_fetch_row($qr2)))
            {
                $car = _post("car_" . $carID);
                if ($car)
                    mysql_query("INSERT INTO pm_as_acc_to_cars (carID, accID) VALUES($carID, $accID)");
            }

        }

        function updateProperties($accID, $props, $cleanBeforeInsert)
        {
            if (!$accID)
                trigger_error("accID must be provided", PM_FATAL);

            if (count($props) == 0)
                trigger_error("\$props cannot be empty", PM_FATAL);

            if ($cleanBeforeInsert)
                mysql_query("DELETE FROM pm_as_parts_properties WHERE accID=$accID");

            foreach ($props as $prop)
            {
                $nVar = _post("prop" . $prop[0]);
                {
                    $q = "INSERT INTO pm_as_parts_properties (accID, propListID, propValue) VALUES($accID, $prop[0], " . prepareVar($nVar) . ")";
                    if (!mysql_query($q))
                        trigger_error("Error inserting specific properties - " . mysql_error(), PM_FATAL);
                }
            }
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
            
            if (isset($args[1]) && $args[1])
            {
                //WE MUST INSERT
                $keys = "";
                $vals = "";

                switch ($md["DataType"])
                {
                    case "Category":
                    {
                        foreach ($this->dNames[$md["DataType"]] as $dn)
                        {
                            $v = prepareVar(_post($dn));
                            if ($keys)
                                $keys .= ", ";
                            $keys .= $dn;

                            if ($vals)
                                $vals .= ", ";
                            $vals .= $v;
                        }
                        $q = "INSERT INTO pm_as_categories (sID, $keys) VALUES (" . $args[0] . ", $vals)";

                        $qr = mysql_query($q);
                        break;
                    }
                    case "CatItem":
                    {
                        foreach ($this->dNames[$md["DataType"]] as $dn)
                        {
                            $v = prepareVar(_post($dn));
                            if ($keys)
                                $keys .= ", ";
                            $keys .= $dn;

                            if ($vals)
                                $vals .= ", ";
                            $vals .= $v;
                        }
                        $accCatID = $this->getCatIDByPageID($structureMgr->getParentPageID($args[0]));

                        $q = "INSERT INTO pm_as_parts (sID, accCatID, $keys) VALUES (" . $args[0] . ", $accCatID, $vals)";
						//echo $q;
                        $qr = mysql_query($q);
                        if (!$qr)
                            trigger_error("Inconsistency - couldn't add specific info for good of cat [$accCatID] - " . mysql_error(), PM_FATAL);

                        $q1 = "SELECT accID FROM pm_as_parts WHERE sID=" . $args[0];
                        $qr1 = mysql_query($q1);
                        if (!$q1)
                            trigger_error("Error while selecting new accID - " . mysql_error(), PM_FATAL);

                        list ($accID) = mysql_fetch_row($qr1);
                        $this->updateCompatibility($accID, false);

                        $props = $this->getCatItemProperties(-1, "CatItem", $structureMgr->getParentPageID($args[0]));
                        if (count($props) > 0)
                           $this->updateProperties($accID, $props, false);

                        break;
                    }
                }

                if (!$qr)
                {
                    trigger_error("Error while trying to insert SpecificData [$q] - " . mysql_error(), PM_WARNING);
                    return false;
                }
            }
            else
            {      
                //WE MUST UPDATE
                switch ($md["DataType"])
                {
                    case "Category":
                    {

                        foreach ($this->dNames[$md["DataType"]] as $dn)
                        {
                            $v = prepareVar(_post($dn));
                            if ($qSet)
                                $qSet .= ", ";
                            $qSet .= $dn . "=" . $v;
                        }
                        $q = "UPDATE pm_as_categories SET $qSet WHERE sID = " . $args[0];

                        $qr = mysql_query($q);
                        break;
                    }
                    case "CatItem":
                    {
                         
                        foreach ($this->dNames[$md["DataType"]] as $dn)
                        {
                            $v = prepareVar(_post($dn));
                            if ($qSet)
                                $qSet .= ", ";
                            $qSet .= $dn . "=" . $v;
                        }

                        $q = "UPDATE pm_as_parts SET $qSet WHERE sID = " . $args[0];
						//trigger_error($q, PM_FATAL);
                        $qr = mysql_query($q);

                        list ($accID) = mysql_fetch_row(mysql_query("SELECT accID FROM pm_as_parts WHERE sID=" . $args[0]));

                        $this->updateCompatibility($accID, true);

                        $props = $this->getCatItemProperties($args[0], "", -1);
                        if (count($props) > 0)
                           $this->updateProperties($accID, $props, true);

                        break;
                    }
                }

                if (!$qr)
                {
                    trigger_error("Error while trying to update SpecificData [$q] - " . mysql_error(), PM_FATAL);
                    return false;
                }
            }

            return true;
        }

        function updateAdditionalColumns($args)
        {
            global $structureMgr;

            $sID = $args[0];
            $qSet = "";
            if ($sID != -1)
            {
                $md = $structureMgr->getMetaData($sID);
            }
            else
            {
                trigger_error("pageID must be specified", PM_WARNING);
                return false;
            }

            {
                //WE MUST UPDATE
                switch ($md["DataType"])
                {
                    case "CatItem":
                    {
                        foreach ($this->additionalColumns[$md["DataType"]] as $ac => $acv)
                        {
                            $nv = _post("$ac$sID");

                            if ($acv == "checkbox")
                            {
                                if ($nv == "")
                                    $nv = "0";
                                else
                                    $nv = "1";
                            }

                            $v = prepareVar($nv);

                            if ($qSet)
                                $qSet .= ", ";
                            $qSet .= $ac . "=" . $v;
                        }

                        $q = "UPDATE pm_as_parts SET $qSet WHERE sID = " . $sID;
                    
                        $qr = mysql_query($q);
                        break;
                    }

                    case "Category":
                    {
                        $qr = 1;
                        break;
                    }
                }

                if (!$qr)
                {
                    trigger_error("Error while trying to update AdditionalColumns [$q] - " . mysql_error(), PM_FATAL);
                    return false;
                }
            }

            return true;
        }

        function getSpecificBlockDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case 'Category':
                    return 'Параметры категории';
                case 'CatItem':
                    return 'Характеристики';
            }

            return "";
        }

        function getItemDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case 'Category':
                    return '';//Описание категории - не нужно, поэтому пустота и закладка пропадёт
                case 'CatItem':
                    return 'Описание товара';
            }

            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case 'Category':
                    return array('категория', 'категорию', 'категории');
                case 'CatItem':
                    return array('товар', 'товар', 'товара');
            }

            return array();
        }


        function getAdditionalColumns($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case "Category":
                {
                    return array("CatItem",
                      "accPlantName" => array("Производитель", "label"),
                      "basePrice" => array("Цена закупки, руб", "text", 5),
                      "salePrice" => array("Базовая цена, руб", "text", 5),
                      "ptID" => array("Тип цены", "dropdown", $this->getPriceTypes()),
                      "notAvailable" => array("Нет в наличии", "checkbox"),
                      "new" => array("Новинки", "checkbox"),
                      "xit" => array("Хит", "checkbox"),
                      "main" => array("Главная", "checkbox")
                    );
                }
            }

            return array();
        }


        function getCatItemProperties($pageID, $DataType, $parentID)
        {
            global $structureMgr;

            if ($pageID != -1)
                $md = $structureMgr->getMetaData($pageID);
            else
                $md['DataType'] = $DataType;

            $res = array();

            switch ($md['DataType'])
            {
                case 'CatItem':
                {
                    if ($pageID != -1)
                    {
                        $q2 = 'SELECT accID FROM pm_as_parts WHERE sID = '.$pageID;
                        list($accID) = mysql_fetch_row(mysql_query($q2));
                        if (!$accID)
                            trigger_error("Error fetching accID for CatItemProperties (sID=$pageID) [$q2]" . mysql_error(), PM_FATAL);

                        // $accID = 1942
                        /*
                        $q = 'SELECT app.propListID, propName, accMeasure, propValue, isHidden
                                FROM pm_as_prop_list apl, pm_as_parts_properties app
                                WHERE app.accID='.$accID.' AND app.propListID = apl.propListID
                                ORDER BY apl.OrderNumber';
                        */
                        $q = 'SELECT app.propListID, propName, accMeasure, propValue, isHidden
                                FROM pm_as_prop_list apl, pm_as_parts_properties app, pm_as_parts ap
                                WHERE app.accID=ap.accID AND app.propListID = apl.propListID AND ap.sID='.$pageID.' 
                                ORDER BY apl.OrderNumber';

                        $qr = mysql_query($q);
                        if (!$qr)
                            trigger_error("Error while query [$q] - " . mysql_error(), PM_FATAL);
                        $i = 0;
                        while (false !== ($row = mysql_fetch_row($qr)))
                        {
                            $i++;
                            $res[] = $row;
                        }
                        if ($i > 0)
                        {
                            $listid = $res[0][0];
                            //trigger_error($accID, PM_FATAL);
                        
                            $q2 = "SELECT accCatID FROM `pm_as_prop_list` WHERE `propListID` =" .$listid;
                            $qr = mysql_query($q2);
                            if (!$qr)
                                trigger_error("Error fetching propNames for CatItems - " . mysql_error(), PM_FATAL);
                            $cat = mysql_fetch_row($qr);
                        
                            $q2 = "SELECT pl.propListID, propName, accMeasure, propValue, isHidden  FROM pm_as_prop_list pl left join pm_as_parts_properties p on pl.propListID=p.propListID AND p.accID=".$accID." WHERE  pl.accCatID=".$cat[0]." AND p.propValue is NULL";
                            $qr = mysql_query($q2);
                            if (!$qr)
                                trigger_error("Error fetching propNames for CatItems - " . mysql_error(), PM_FATAL);
                            if (mysql_num_rows($qr) > 0)
                            {
                                while (false !== ($prop = mysql_fetch_row($qr)))
                                {
                                    $res[] = $prop;
                                }                            
                            }
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

        function getSubItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Article":
                    return array("Category" => "категорию каталога");
                case "Category":
                    return array("CatItem" => "товар", "Category" => "категорию каталога");
                case "CatItem":
                    return array();
            }

            return array();
        }

        function getCatIDByPageID($pageID)
        {
            $q2 = "SELECT accCatID FROM pm_as_categories WHERE sID = $pageID";
            list($accCatID) = mysql_fetch_row(mysql_query($q2));
            if (!$accCatID)
                return -1;

            return $accCatID;
        }

        function getContent($args)
        {
            global $structureMgr;
            $md = $structureMgr->getMetaData($args[0]);

            switch ($md['DataType'])
            {
                case 'Category':
                    return $this->getCategory($args[0]);
                case 'CatItem':
                    return $this->getCatItem($args[0]);
                default:
                    trigger_error('Unknown datatype: ' . $md['DataType'], PM_FATAL);
            }
        }

        function getCatItem($pageID)
        {
            $ci = $this->getCatItemByPageID($pageID);
            $res = $this->getFilledItemDescriptionTemplate($ci);

            return $res;

        }

        function &filterByDataType(&$branch, $dataType, &$catIDList, &$prodIDList)
        {
            global $structureMgr;

            $res = array();
            $filtered1 = array();
            $gotFilter = 0;

            foreach ($branch as $item)
            {
                if ($gotFilter == 0)
                {
                    $filtered1 = $this->getCatItemsByParentPageID($structureMgr->getParentPageID($item["sID"]), _var("carID"), _var("producerID"), $catIDList, $prodIDList);
                    $gotFilter = 1;
                }

                if ($item["DataType"] == $dataType && isset($filtered1[$item["sID"]]))
                    $res[] = $item;

                if (count($item["children"] > 0))
                {
                    $app = $this->filterByDataType(&$item["children"], $dataType, $catIDList, $prodIDList);

                    for ($i = 0; $i < count($app); $i++)
                        $res[] = $app[$i];
                }
            }
            return $res;
        }

        /**
         * здесь надо менять, т.к. исп. некрасивые параметры из URL
         *
         * @param int $pageID
         * @return unknown
         */
        function getCategory($pageID)
        {
            global $structureMgr, $templatesMgr;
            $items = false;


            $producerID = _var('producerID');
            $carID = _var('carID');

            $car0 = _varByPattern('/c0-\\d+/');
            $cats = _varByPattern('/c-\\d+/');
            $prodIDs = _varByPattern('/p-\\d+/');
			$propVals = _varByPattern('/propVal-\\d+/');

			if (!(count($propVals) > 0 || count($prodIDs) > 0 ))
            {

				if ($carID !== "" || $producerID !== "" || count($car0) > 0 || count($cats) > 0 || count($prodIDs) > 0)
				{
					$catIDList = array();
					if (count($cats) == 0)
						$cats = $car0;

					foreach($cats as $cat => $v)
						$catIDList[] = $v;

					$prodIDList = array();

					foreach($prodIDs as $prod => $v)
						$prodIDList[] = $v;

					$branch2 = $structureMgr->getChildrenDataTypesForPageID($pageID, 2);
					$branch = $this->filterByDataType(&$branch2, "CatItem", &$catIDList, &$prodIDList);

					if (count($branch) > 0)
						$items = true;
				}
				else
				{
					$branch = $structureMgr->getChildrenDataTypesForPageID($pageID, 1);
					foreach ($branch as $i => $v)
					{
						if ($v['DataType'] == 'CatItem')
						{
							$items = true;
							break;
						}
					}
				}

			}

			if (count($propVals) > 0 || count($cats) > 0 || count($prodIDs) > 0 || count($car0) > 0 || $carID)
            {
				//print_r($propVals);
				//echo $pageID.' 1';
                return $this->getFilteredItems($pageID, $propVals, $prodIDs, $cats);
            }elseif ($items == true)
            {
				$cats['c-0']=$pageID;
				return $this->getFilteredItems($this->getParentPageID($pageID), $propVals, $prodIDs, $cats);
                //return $this->getItemsListByPageID($pageID, &$branch);
            }else
            {
                return $this->getCategoryFilter($pageID, &$branch);
            }
        }

        function getCarsList($plantID = 2)
        {
            
            $q = "SELECT carID, plantName, carModel FROM pm_as_cars c LEFT JOIN pm_as_autocreators a ON (a.plantID = c.plantID) WHERE c.plantID=".$plantID." ORDER BY carID";
            $qr = mysql_query($q);

            if (!$qr)
                trigger_error("Error getting cars list [$q] - " . mysql_error(), PM_FATAL);

            $cars = array();
            while (false !== ($car = mysql_fetch_row($qr)))
            {
                $cars[$car[0]] = array($car[1], $car[2] );
            }

            return $cars;
        }


		function getCategoryFilter($pageID, &$branch)
        {
			$error = _get("error");
            global $structureMgr, $templatesMgr;
			//echo $pageID."<br />";
            $carlist = "";
            $pager = "";

            //print_r($branch);

            $topContent = $structureMgr->getData($pageID);

            $sData = $this->getSpecificDataForEditing(array($pageID));

            $tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/Catalogue/" . $sData["FilterTemplate"][2]	);

            //We need to generate cars list
            if ($sData["MustUseCompatibility"][2])
            {                    
                if ($pageID == 4)
                {
                    $plantID = 2;
                } else
                {
                    $plantID = 4;    
                }                           
                $cars = $this->getCarsList($plantID);
                $carCount = count($cars);

                //script to switch layers with categories that match the concrete car
                $jsText = "<script>var tempCarID = 'catForCar0'; </script>";
                $carlist = $jsText . "<table width='100%'><tr><td>";
                $carlist .= "<input type=\"radio\" name=\"carID\" value=\"0\" checked=\"checked\" onclick=\"showCatForCar('catForCar0');\" /> <a class=\"levm\" href=\"?carID=0\">все</a><br />\n";

                $catsForCar = "";

                $sIDin = "";

                for ($i=0; $i < count($branch); $i++)
                {
                    if ($i > 0)
                        $sIDin .= ",";

                    $sIDin .= $branch[$i]["sID"];
                }
                $i = 0;
                foreach ($cars as $carID => $car)
                {
                    $URL = "?carID=$carID";
                    $add = "";
                    if ($plantID == 2) $add = $car[0] . " ";
                    $carlist .= "<input type=\"radio\" name=\"carID\" value=\"$carID\" onclick=\"showCatForCar('catForCar$carID');\" /> <a class=\"levm\" href=\"$URL\">" .  $add . $car[1] . "</a><br/>\n";
                    $i++;
                    if ($i == 17) $carlist .= "</td><td>";
                    $catsForCar .= "<div style=\"display: none;\" id=\"catForCar$carID\">";

/*
SELECT ShortTitle, COUNT( DISTINCT atc.accID ), c.sID
FROM pm_as_cars ac, pm_as_acc_to_cars atc, pm_as_parts ap, pm_as_categories c, pm_structure s
WHERE c.sID = s.sID AND atc.carID = ac.carID AND atc.accID = ap.accID
AND c.accCatID = ap.accCatID
AND ac.carID =$carID
GROUP BY c.sID
*/

                    $qx =
                    "
SELECT s2.ShortTitle, COUNT( DISTINCT s.sID ), s2.sID
FROM pm_structure s
LEFT JOIN pm_as_parts p ON ( p.sID = s.sID )
LEFT JOIN pm_as_acc_to_cars ac ON ( ac.accID = p.accID )
LEFT JOIN pm_as_cars c ON ( ac.carID = c.carID )
LEFT JOIN pm_structure s2 ON ( s2.sID = s.pms_sID )
WHERE s.pms_sID
IN ( $sIDin )
AND c.carID =$carID
GROUP BY s.pms_sID
ORDER BY s2.ShortTitle
";
//trigger_error($qx, PM_FATAL);
                    //print $qx;
                    $qrx = mysql_query($qx);

                    if (!$qrx)
                        trigger_error("Error acquiring cat for carID=$carID");

                    $catNum = 0;
                    while (false !== ($ci = mysql_fetch_row($qrx)))
                    {
                        if ($ci[1] > 0)
                        {
                            $bsID = $ci[2];
                            $URL = $structureMgr->getPathByPageID($ci[2], false);
                            $catsForCar .= "<input type=\"checkbox\" value=\"$bsID\" name=\"c-$catNum\" /> <a class=\"levm\" href=\"?c-$catNum=$bsID&amp;carID=$carID\">" . $ci[0] . " ($ci[1])</a><br/>\n";
                            $catNum++;
                        }
                    }

                    $catsForCar .= "</div>";
                }
                
                $carlist .= "</td></tr></table>";  

                $catsForCar .= "<div id=\"catForCar0\">";
                for ($i=0; $i < count($branch); $i++)
                {
                    $bsID = $branch[$i]["sID"];
                    $URL = $structureMgr->getPathByPageID($bsID, false);
                    $chCount = $structureMgr->getChildrenCount($bsID);
                    if ($chCount > 0)
                        $catsForCar .= "<input type=\"checkbox\" value=\"$bsID\" name=\"c-$i\" /> <a class=\"levm\" href=\"?c-$i=$bsID\">" . $branch[$i]["Title"] . " ($chCount)</a><br/>\n";

                }
                $catsForCar .= "</div>";

                $tpl = str_replace("%cars%", $carlist, $tpl);
                $tpl = str_replace("%categories%", $catsForCar, $tpl);
            }
            else
            {
                $res = "";
				$urlsForProds = array();
				$subcats_params = "";
				$j = 0;
                $ii = 0;

				foreach ($branch as $i => $v )
                {
                    $bsID = $v['sID'];
                    $URL = $structureMgr->getPathByPageID($v['sID'], false);

                    $chCount = $structureMgr->getChildrenCount($v['sID']);
                    if ($chCount > 0){
//////////////////Важное место////////////////////
						//echo $pageID."<br />";
						$subCats = $this->getSubCategoris($pageID, array($bsID), $this->FILTER_CONSTANT_ARRAY[$pageID]['paramId']);

						//print_r($this->FILTER_CONSTANT_ARRAY[$pageID]);
						//echo $this->FILTER_CONSTANT_ARRAY[$pageID]['showSubCats'];
						if($this->FILTER_CONSTANT_ARRAY[$pageID]['showSubCats']) {
							$res .= ($ii > 0 ? "<br />":"").$this->addInputType("c-$i", $bsID, "<a  href=\"?c-$i=$bsID\" class=\"bllink\"><strong>" . $branch[$i]["Title"] . " ($chCount)</strong></a>", $this->FILTER_CONSTANT_ARRAY[$pageID]['catType']).(count($subCats)?": ":"")."<br />\n";


							foreach($subCats as $subCat) {
								if($this->FILTER_CONSTANT_ARRAY[$pageID]['showSubCatsOnly']) {
									$subcats_params .= $this->addInputType("propVal-".$j, $bsID."_".$subCat['propValue'], "<a class=\"levm\" href=\"?propVal-".$j."=".$bsID."_".$subCat['propValue']."\">" . $subCat['propValue'] . " (".$subCat['countNum'].")</a>", $this->FILTER_CONSTANT_ARRAY[$pageID]['paramType'])."<br/>\n";
								} else {
									$res .= $this->addInputType("propVal-".$j, $bsID."_".$subCat['propValue'], "<a class=\"levm\" href=\"?propVal-".$j."=".$bsID."_".$subCat['propValue']."\">" . $subCat['propValue'] . " (".$subCat['countNum'].")</a>", $this->FILTER_CONSTANT_ARRAY[$pageID]['paramType'])."<br/>\n";
								}
								$j++;
							}
						}else {
							$res .= "<input type=\"checkbox\" value=\"$bsID\" name=\"c-$i\"><a  href=\"?c-$i=$bsID\"><strong>" . $branch[$i]["Title"] . " ($chCount)</strong></a><br/>\n";
						}
                        $ii++;
						$urlsForProds[] = "c-$i=$bsID";
//                        $res .= "<input type=\"checkbox\"> <a class=\"levm\" href=\"$URL\">" . $branch[$i]["Title"] . " ($chCount)</a><br/>\n";
					}

                }
				//producers list
                $producers = "<table width=\"100%\">";

                $pageIDList = array();
                for ($i=0; $i < count($branch); $i++)
                {
                    $pageIDList[] = $branch[$i]["sID"];
                }

                $prodList = array();

                if (count($pageIDList) > 0)
                    $prodList = $structureMgr->getProducersByPageIDList($pageIDList);


                for ($i=0; $i < count($prodList); $i++)
                {
					if($i % 3 == 0) {

						$producers .= "\n<tr>\n";
					}
                    if (($prodList[$i][2]) && file_exists(GetCfg("ROOT") . $prodList[$i][2]))
                        $isrc = $prodList[$i][2];
                    else
                        $isrc = "/products/empty.gif";



                    $prodID = $prodList[$i][0];
                    $producers .= "<td align=\"center\"><a href=\"?p-".$i."=$prodID&amp;".implode('&amp;',$urlsForProds)."\"><img alt=\"" . $prodList[$i][1] . "\" src=\"$isrc\" /></a><br />" . $prodList[$i][1] .
                                  "<br />".$this->addInputType("p-".$i, $prodList[$i][0], "", $this->FILTER_CONSTANT_ARRAY[$pageID]['prodType'])."<br/><br/></td>\n";
					if (($i+1) % 3 == 0 || $i==(count($prodList)-1) )
                        $producers .= "\n</tr>\n";
                }

                $producers .= "</table>";

				if($this->FILTER_CONSTANT_ARRAY[$pageID]['showSubCatsOnly']) {
					$tpl = str_replace("%categories_params%", $subcats_params, $tpl);
				} else {
					$tpl = str_replace("%categories_params%", "", $tpl);
				}
                $tpl = str_replace("%categories%", $res, $tpl);
                $tpl = str_replace("%producers%", $producers, $tpl);
            }
			if($error) {
				$nofind_tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/" . "Catalogue/no_find.html");
				$tpl = $nofind_tpl.$tpl;
			}
            return $tpl;
        }

		function addInputType($key, $value, $text, $type, $selected=0)
		{
			$item = "";
			switch($type)
			{
				case "chk": $item = "<input type=\"checkbox\" value=\"".$value."\" name=\"".$key."\" ".($selected?"checked=\"checked\"":"")." />".$text;break;
				case "select": $item = "<option value=\"".$value."\" ".($selected?"selected=\"selected\"":"").">".$text."</option>"; break;
				case "radio":
					$keyName = explode("-",$key);
					$item = "<input type=\"radio\" value=\"".$value."\" name=\"".$keyName[0]."-0\" ".($selected?"checked=\"checked\"":"")." />".$text; break;
				default: $item = $text; break;
			}
			return $item;
		}

		function getSubCategoris($pageID, $catIDs, $orderNum = 2, $filtred = 0)
		{
		    //$paramCatIDs = 'array['.join(', ', $catIDs).']';
		    //trigger_error("call getSubCategoris($pageID, $paramCatIDs, $orderNum, $filtred)", E_USER_NOTICE);
			$subCats = Array();
			/*$query = "SELECT pm_as_categories.accCatID, pm_as_prop_list.propListID, pm_as_prop_list.propName
						FROM pm_as_categories, pm_as_prop_list
						WHERE (pm_as_categories.sID = '".$pageID."' || pm_as_categories.sID = '".$catIDs[0]."')
						&& pm_as_prop_list.accCatID = pm_as_categories.accCatID
						&& pm_as_prop_list.OrderNumber = '".$orderNum."'";
			echo $query."<br />";
			$result = mysql_query($query);
			*/
			$catIDar = array();

			foreach($catIDs as $catID) {
				$catIDar[] = "pm_as_categories.sID = '".$catID."'";
			}
			$catIDar[] = "pm_as_categories.sID = '".$pageID."'";

			$catIDStr = "(".implode(" || ", $catIDar).")";
			/*if(!mysql_num_rows($result))
			{
				$query = "SELECT pm_as_categories.accCatID, pm_as_prop_list.propListID, pm_as_prop_list.propName
						FROM pm_as_categories, pm_as_prop_list
						WHERE ".$catIDStr."
						&& pm_as_prop_list.accCatID = pm_as_categories.accCatID
						&& pm_as_prop_list.OrderNumber = '".$orderNum."'";
				$result = mysql_query($query);
			}*/
			//if(mysql_num_rows($result))
			//{
			//	$row = mysql_fetch_assoc($result);
			//	$propListID = $row['propListID'];
				$subQuery = "SELECT DISTINCT pm_as_prop_list.propListID, pm_as_prop_list.propName, pm_as_categories.accCatID, pm_as_parts_properties.propValue, COUNT(pm_as_parts_properties.propID) as countNum
							FROM pm_as_categories, pm_structure, pm_as_prop_list, pm_as_parts_properties, pm_as_parts
							WHERE
							".$catIDStr."
							&& pm_as_categories.accCatID = pm_as_parts.accCatID
							&& pm_as_parts.accID = pm_as_parts_properties.accID
							&& pm_as_parts_properties.propListID = pm_as_prop_list.propListID
							&& pm_as_parts.sID = pm_structure.sID
							&& pm_as_parts.notAvailable = 0
							&& pm_structure.isHidden=0
							&& pm_structure.isDeleted = 0
							&& pm_structure.isVersionOfParent=0
							&& pm_as_prop_list.OrderNumber = '".$orderNum."'
							GROUP BY pm_as_parts_properties.propValue
							ORDER BY LENGTH(pm_as_parts_properties.propValue), pm_as_parts_properties.propValue";//
                            
                 //trigger_error($subQuery, PM_FATAL);
				//echo $subQuery."<hr>";
				//if($catIDs[0] == 1968 || $catIDs[0] == 2250)
				//	echo $subQuery."<hr>";
				$subresult = mysql_query($subQuery);
				if(mysql_num_rows($subresult)) {
					while($sub = mysql_fetch_assoc($subresult))
					{
						$subCats[] = $sub;
					}
				}
			//}

			return $subCats;
		}

		function getParentPageID($pageID)
		{
			$query = "SELECT pms_sID FROM pm_structure WHERE sID='".$pageID."'";
			$result = mysql_query($query);
			$row = mysql_fetch_assoc($result);
			return $row['pms_sID'];
		}

		function getFilteredItems($pageID, $propVals, $prodIDs, $cats) {
			global $structureMgr, $templatesMgr;
			//echo '<br />'.$pageID.' 2';

			SetCfg("Catalogue.itemsPerPage", 10);
			SetCfg("Catalogue.itemsPerCol", 1);

			//$car0 = _varByPattern('/c0-\\d+/');
            //$cats = _varByPattern('/c' . $carID . '-\\d+/');
            //$prodIDs = _varByPattern('/p-\\d+/');
			//$propVals = _varByPattern('/propVal-\\d+/');


			$from = _get("from");
			$to = _get("to");
			$propID = _get("propID");
			$carID = _get("carID");
			$order = _get("order");

			$filterTpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/" . "Catalogue/filterMenu.html");

			$branch = $structureMgr->getChildrenDataTypesForPageID($pageID, 1);
			//echo $pageID;
			$catFilter = "";
			$propsArray = array();
			$bsIDArray = array();
			foreach ($branch as $i => $v)
			{
				$bsID = $v["sID"];
				$URL = $structureMgr->getPathByPageID($v["sID"], false);
				$chCount = $structureMgr->getChildrenCount($v["sID"]);
				if ($chCount > 0){
					$catFilter .= "<img src=\"/images/arr_gray2.gif\" width=\"7\" height=\"9\"  alt=\"\" /><a  href=\"?c-$i=$bsID\" >" . $v["Title"] . " ($chCount)</a><br/>\n";
				}
				$bsIDArray[] = $bsID;

			}

			if ($this->FILTER_CONSTANT_ARRAY[$pageID]['otherParamId']) {
			    $subCats = $this->getSubCategoris($pageID, $bsIDArray, $this->FILTER_CONSTANT_ARRAY[$pageID]['otherParamId'], 1);
			} else {
			    $subCats = array();
			}

			$propsArray = $subCats;

			$pageIDList = array();

			foreach ($branch as $i => $v)
			{
				$pageIDList[] = $v["sID"];
			}

			$prodList = array();

			if (count($pageIDList) > 0)
				$prodList = $structureMgr->getProducersByPageIDList($pageIDList);

			$prods = "";
			foreach($prodList as $prodItem) {
				$prods .= "<option value=\"".$prodItem[0]."\" ".(in_array($prodItem[0], $prodIDs)?"selected":"").">".$prodItem[1]."</option>";
			}
			$filterTpl = str_replace("%prods%", $prods, $filterTpl);


			/**
			 * Код формирует доп. поле для фильра
			 * См. раздел шины, масла.
			 */
			$propsValues = "";
			foreach($propsArray as $propsArrayItem) {
				$propsValues .= $this->addInputType($propsArrayItem['propListID'], $propsArrayItem['propValue'], $propsArrayItem['propValue'], "select",($propsArrayItem['propValue'] == $propID?1:0));
			}
			if($this->FILTER_CONSTANT_ARRAY[$pageID]['otherParamId']) {
				$propsValues = '<tr>
								<td class="tdmidleft filtr">'.$propsArray[0]['propName'].'</td>
								<td class="tdmidright filtr"><select name="propID" class="widesel" id="propID">
                                              <option value="">-----</option>'.$propsValues.'</select>
								</td>
								</tr>';
				$filterTpl = str_replace("%propsValues%", $propsValues, $filterTpl);
			}else {
				$filterTpl = str_replace("%propsValues%", "", $filterTpl);
			}
			$filterTpl = str_replace("%filter_result%", $this->FILTER_CONSTANT_ARRAY[$pageID]['result'] , $filterTpl);
			$filterTpl = str_replace("%from%", $from, $filterTpl);
			$filterTpl = str_replace("%to%", $to, $filterTpl);
			$filterTpl = str_replace("%ShortTitle%", $ShortTitle, $filterTpl);
			//$filterTpl = str_replace("%actionUrl%", ($_SERVER['QUERY_STRING']?$_SERVER['HTTP_REFERER']."?".$_SERVER['QUERY_STRING']:""), $filterTpl);

			$props = array();
			$filterArray = array();

			$podborArray =  array();
			$hidenInputArray = array();

			//$props['1'][] = " p.sID = '".$pageID."'";
			//$props['2'][] = " s.pms_sID = '".$pageID."'";
			if($carID) {
				$hidenInputArray[] = '<input type="hidden" name="carID" value="'.$carID.'" />';
				$filterArray[]= "carID=".$carID;
			}
			if($order) {
				$hidenInputArray[] = '<input type="hidden" name="order" value="'.$order.'" />';
				$filterArray[]= "order=".$order;
			}
			if($propID) {
				$filterArray[]= "propID=".$propID;
			}
			if($from) {
				$filterArray[]= "from=".$from;
			}
			if($to) {
				$filterArray[]= "to=".$to;
			}
			foreach($cats as $key=>$value)
			{
				if($value) {
					$props['sID'][] = "s.pms_sID = '".$value."'";
					//$props['p.accCatID'][] = $param[0];
					$filterArray[]= $key."=".$value;
					$hidenInputArray[] = '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\n";
				}
			}

			foreach($propVals as $key=>$value)
			{
				if($value) {
					$param = explode("_",$value);
					$props['props'][] = "pp.propValue = '".$param[1]."' && s.pms_sID = '".$param[0]."'";
					//$props['p.accCatID'][] = $param[0];

					$podborArray['prop'][] = $param[1];

					$filterArray[]= $key."=".$value;
					$hidenInputArray[] = '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\n";
				}
			}

			foreach($prodIDs as $key=>$value)
			{
				if($value) {
					$props['prods'][] = "ap.accPlantID = '".$value."'";
					$filterArray[]= $key."=".$value;

					$query = "SELECT * FROM pm_as_producer WHERE accPlantID='".$value."'";
					$result = mysql_query($query);
					$row = mysql_fetch_assoc($result);
					$podborArray['prod'][] = $row['accPlantName'];
					$hidenInputArray[] = '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
				}
			}
			//if(count($podborArray))
			//{
				$catFilter = "<p><img src=\"/images/arr_gray2.gif\" width=\"7\" height=\"9\"  alt=\"\" />".$structureMgr->getTitleFromParams($cats)."</p>"."\n<p>".$catFilter."</p>";
			//}

			if(count($hidenInputArray))
			{
				$catFilter .= implode("\n",$hidenInputArray);
			}

			$filterTpl = str_replace("%catFilter%", $catFilter, $filterTpl);

			//$filter = $_SERVER['QUERY_STRING'];
			//echo $filter;
			$filter = implode("&amp;", $filterArray);

			$content = "";
			$pager = "";

			//$topContent = $structureMgr->getData($pageID);


			$pNum = _get("pNum");
			if(!$pNum) $pNum = 1;
			//$pNum = $structureMgr->getPageNumberByPageID($pageID);
            //$URL = $structureMgr->getPathByPageID($pageID, false);

			$perPage = GetCfg("Catalogue.itemsPerPage");

            $startFrom = ($pNum - 1) * $perPage;
            $endAt = $startFrom + $perPage - 1;

			$items = $this->getItemsWithFilter($props, $startFrom, $endAt, $from, $to, $propID, $carID, $order);

            $cnt = $this->itemsCount;

            if ($endAt >= $cnt)
                $endAt = $cnt - 1;

            $pagesCount = ceil($cnt / $perPage);
            if ($pagesCount < $pNum-1)
            {
                trigger_error("Invalid pageNumber [$pNum of $pagesCount] - possibly hacking or serious change in DB", PM_ERROR);
            }
            else
            {
                if ($pagesCount > 1)
                {
                    $tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/" . "pager_filter.html");
                    $purePager = "";

                    for ($i=1; $i <= $pagesCount; $i++)
                    {
                        /*if ($i > 1)
                        {*/
                            $purePager .= " - ";
                            $u = "?pNum=" . $i;
                        //}
                        //else
                        //   $u = "";

                        if ($filter)
                            $u .= "&amp;" . $filter;

                        if ($i == $pNum)
                        {
                            $purePager .= $i;
                        }
                        else
                        {
                            $purePager .= "<a href=\"$u\" class=\"levm\">" . $i . "</a>";
                        }
                    }

					switch($order) {
						case "name" :$tpl = str_replace("%sel1%", 'selected="selected"', $tpl); break;
						case "price" :$tpl = str_replace("%sel2%", 'selected="selected"', $tpl); break;
						case "desc" :$tpl = str_replace("%sel3%", 'selected="selected"', $tpl); break;
						case "rating" :$tpl = str_replace("%sel4%", 'selected="selected"', $tpl); break;
					}
					$tpl = str_replace("%sel1%", '', $tpl);
					$tpl = str_replace("%sel2%", '', $tpl);
					$tpl = str_replace("%sel3%", '', $tpl);
					$tpl = str_replace("%sel4%", '', $tpl);
					$tpl = str_replace("%links%", $purePager, $tpl);
					$pager = str_replace("%catFilter%", implode("\n",$hidenInputArray), $tpl);

					$filterTpl = str_replace("%links%", "Страницы: ".$purePager, $filterTpl);
                } else {
					$filterTpl = str_replace("%links%", " ", $filterTpl);
				}

			}


			$content .= "<div class=\"items\">\n<table cellpadding=\"0\" cellspacing=\"0\" class=\"items-table\">\n";
			//print_r($items);
			$i=0;
			//print_r($items);
			foreach($items as $item) {
				$style = ($i > 0) ? "mid" : "up";
                $content .= $this->getFilledItemTemplate($item, $style);
				$i++;
			}
			$content .= "\n</table>\n</div>\n";

			if(!count($items)) {
				$URL = $structureMgr->getPathByPageID($pageID, false);
				header('Location: '.$URL."?error=1");
			}
			return $filterTpl.$content.$pager;
		}

		function getItemsWithFilter($props, $startFrom, $endAt, $from, $to, $propID, $carID, $order)
		{
			//echo $propID."<hr>";
            
			$params = Array();
			$joinStr = "";
			//print_r($props);
			//echo '<hr>';
			if(count($props)) {
				foreach($props as $key=>$property){
					$paramsArray = Array();
					foreach($property as $value) {
						$paramsArray[] = $value;
					}
					if(count($paramsArray))
						$params[] = " (".implode(" || ", $paramsArray).") ";
				}
			}
			if($from) {
				$params[] = "p.salePrice >= '".$from."'";
			}
			if($to) {
				$params[] = "p.salePrice <= '".$to."'";
			}
			if($propID) {
                $params[] = "pp1.propValue = '".$propID."'";
                $joinStr .= " LEFT JOIN pm_as_parts_properties pp1 ON (pp1.accID = p.accID) ";
                //$joinStr .= " LEFT JOIN pm_as_parts_properties pp1 ON (pp1.accID = p.accID && pp1.propListID = '41') ";
			}
			if($carID) {
				$params[] = "atc.carID = '".$carID."'";
				$joinStr .= " LEFT JOIN pm_as_acc_to_cars atc ON (atc.accID = p.accID) ";
			}
			$paramsStr = implode(" && ", $params);

			if(strlen($paramsStr)) {
				$paramsStr = " && ".$paramsStr;
			}
				$orderStr = ' ORDER BY ';
				if ($order == 'name') {
					$orderStr .= 'ShortTitle';
				} elseif ($order == 'price') {
					$orderStr .= 'salePrice';
				} elseif ($order == 'pricedesc') {
					$orderStr .= 'salePrice DESC';
				} elseif ($order == 'rating') {
					$orderStr .= 'rating DESC, ShortTitle';
				} else {
					$orderStr .= 'ShortTitle';
				}
            //$orderStr = ' ORDER BY rating DESC';
			$query = "SELECT DISTINCT SQL_CALC_FOUND_ROWS
					p.accID, p.sID, ShortTitle, deliveryCode, accPlantName, logotype, smallPicture, s.tplID, salePrice,
					MustUseCompatibility, PicturePath, DescriptionTemplate, ptPercent, p.ptID, p.new, p.xit, p.main,
					    (SELECT SUM( r.grade ) / r.count /3
                        FROM pm_rating r
                        WHERE r.sID = s.sID
                        ) AS rating
					FROM `pm_as_parts` p
					LEFT JOIN pm_as_producer ap ON (ap.accPlantID = p.accPlantID)
					LEFT JOIN pm_as_parts_properties pp ON (pp.accID = p.accID)
					".$joinStr."
					LEFT JOIN pm_structure s ON (p.sID = s.sID)
					LEFT JOIN pm_as_categories ac ON (s.pms_sID = ac.sID)
					LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
					WHERE p.notAvailable = 0 && s.isHidden=0
					".$paramsStr."
					".$orderStr."
					LIMIT ".$startFrom.",".GetCfg("Catalogue.itemsPerPage");
            //trigger_error($query, PM_FATAL);
			//print $query."<br><br>".$pr."<br><br>".$pr1;
			$result = mysql_query($query);
			if (!$result)
                trigger_error("Invaid query. " . mysql_error(), PM_FATAL);

            if (mysql_num_rows($result) == 0)
                trigger_error("Empty result for $query", PM_WARNING);

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
				$catItems[] = $item;
			}


            //print_r($catItems);
			return $catItems;
		}

        function getItemsListByPageID($pageID, &$branch)
        {
            global $structureMgr, $templatesMgr;

			$prodIDs = _varByPattern('/p-\\d+/');
			$propVals = _varByPattern('/propVal-\\d+/');

			$res = "";
            $pager = "";

            $topContent = $structureMgr->getData($pageID);
			$metaData = $structureMgr->getMetaData($pageID);

			$from = _get("from");
			$to = _get("to");
			$propID = _get("propID");
			$order = _get("order");

			$filterTpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/" . "Catalogue/filterMenu.html");

			$br = $structureMgr->getChildrenDataTypesForPageID($this->getParentPageID($pageID), 1);
			//echo $pageID;
			$catFilter = "";
			$propsArray = array();
			$bsIDArray = array();
			for ($i=0; $i < count($branch); $i++)
			{
				$bsID = $branch[$i]["sID"];
				$URL = $structureMgr->getPathByPageID($branch[$i]["sID"], false);
				$chCount = $structureMgr->getChildrenCount($branch[$i]["sID"]);
				if ($chCount > 0){
					$catFilter .= "<img src=\"/images/arr_gray2.gif\" width=\"7\" height=\"9\"  alt=\"\" /><a  href=\"?c-$i=$bsID\" >" . $branch[$i]["Title"] . " ($chCount)</a><br/>\n";
				}
				$bsIDArray[] = $bsID;

			}
			$subCats = $this->getSubCategoris($this->getParentPageID($pageID), $bsIDArray, $this->FILTER_CONSTANT_ARRAY[$pageID]['otherParamId']);
			foreach($subCats as $key=>$value) {
				$propsArray[$key] = $value;
			}


			$pageIDList = array();
			for ($i=0; $i < count($br); $i++)
			{
				$pageIDList[] = $br[$i]["sID"];
			}

			$prodList = array();

			if (count($pageIDList) > 0)
				$prodList = $structureMgr->getProducersByPageIDList($pageIDList);

			$prods = "";
			foreach($prodList as $prodItem) {
				$prods .= "<option value=\"".$prodItem[0]."\" ".(in_array($prodItem[0], $prodIDs)?"selected":"").">".$prodItem[1]."</option>";
			}
			$filterTpl = str_replace("%prods%", $prods, $filterTpl);

			$propsValues = "";
			foreach($propsArray as $propsArrayItem) {
				$propsValues .= "<option value=\"".$propsArrayItem['propListID']."\" ".($propsArrayItem['propListID'] == $propID?"selected":"").">".$propsArrayItem['propValue']."</option>";
			}
			$filterTpl = str_replace("%propsValues%", $propsValues, $filterTpl);

			$filterTpl = str_replace("%filter_result%", $this->FILTER_CONSTANT_ARRAY[$pageID]['result'] , $filterTpl);

			$filterTpl = str_replace("%from%", $from, $filterTpl);
			$filterTpl = str_replace("%to%", $to, $filterTpl);
			//$filterTpl = str_replace("%ShortTitle%", $ShortTitle, $filterTpl);
			//$filterTpl = str_replace("%actionUrl%", $_SERVER['QUERY_STRING'], $filterTpl);


			$props = array();
			$filterArray = array();

			$podborArray =  array();
			$hidenInputArray = array();

			foreach($propVals as $key=>$value)
			{
				if($value) {
					$param = explode("_",$value);
					$props['props'][] = "pp.propValue = '".$param[1]."' && s.pms_sID = '".$param[0]."'";
					//$props['p.accCatID'][] = $param[0];

					$podborArray['prop'][] = $param[1];

					$filterArray[]= $key."=".$value;
					$hidenInputArray[] = '<input type="hidden" name="'.$key.'" value="'.$value.'">'."\n";
				}
			}

			foreach($prodIDs as $key=>$value)
			{
				if($value) {
					$props['prods'][] = "ap.accPlantID = '".$value."'";
					$filterArray[]= $key."=".$value;

					$query = "SELECT * FROM pm_as_producer WHERE accPlantID='".$value."'";
					$result = mysql_query($query);
					$row = mysql_fetch_assoc($result);
					$podborArray['prod'][] = $row['accPlantName'];
					$hidenInputArray[] = '<input type="hidden" name="'.$key.'" value="'.$value.'">';
				}
			}
			if(count($podborArray))
			{
				$catFilter = "<p><img src=\"/images/arr_gray2.gif\" width=\"7\" height=\"9\"  alt=\"\" />".implode(', ',$podborArray['prod'])." ".( (count($podborArray['prod']) && count($podborArray['prop']) ) ?":":"")." ".implode(', ',$podborArray['prop'])."</p>"."\n<p>".$catFilter."</p>";
			}
			if(count($hidenInputArray))
			{
				$catFilter .= implode("\n",$hidenInputArray);
			}
			$filterTpl = str_replace("%catFilter%", $catFilter, $filterTpl);


			$filter = $_SERVER['QUERY_STR'];
			$filter .= "&amp;". implode("&amp;", $filterArray);

            $pNum = $structureMgr->getPageNumberByPageID($pageID);
            $URL = $structureMgr->getPathByPageID($pageID, false);

            $carIDFilter = _var("carID");

            if ("" == $carIDFilter)
                $carIDFilter = "0";

            $producerIDFilter = _var("producerID");

            $prodIDFilter = _varByPattern('/p-\\d+/');


            $perPage = GetCfg("Catalogue.perPage");

            $startFrom = ($pNum - 1) * $perPage;
            $endAt = $startFrom + $perPage - 1;

            $cnt = count($branch);

            if ($endAt >= $cnt)
                $endAt = $cnt - 1;

            $pagesCount = ceil($cnt / $perPage);

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

                    $filter = "";

                    if (($carIDFilter != "") && (count($prodIDFilter) == 0))
                       $filter .= "carID=$carIDFilter";

                    if ($producerIDFilter != "")
                    {
                        if ($filter)
                            $filter .= "&amp;";
                        $filter .= "producerID=$producerIDFilter";
                    }
                    else
                    if ( count($prodIDFilter) > 0)
                    {
                        foreach ($prodIDFilter as $prod => $_p)
                        {
                            if ($filter)
                                $filter .= "&amp;";
                            $filter .= "$prod=$_p";
                        }
                    }

                    $cats = _varByPattern('/c' . $carIDFilter . '-\\d+/');
                    if (count($cats) > 0)
                    {
                        foreach ($cats as $cat => $v)
                        {
                            if ($filter)
                                $filter .= "&amp;";

                            $filter .= "$cat=$v";
                        }
                    }


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

                	$filterTpl = str_replace("%links%", "Страницы: ".$purePager, $filterTpl);
                } else {
					$filterTpl = str_replace("%links%", "", $filterTpl);
				}

                for ($i=$startFrom; $i <= $endAt; $i++)
                {
                    switch ($branch[$i]["DataType"])
                    {
                        case "CatItem":
                        {
                            $items = true;
                            $ci = $this->getCatItemByPageID($branch[$i]["sID"]);
							$style = ($i > $startFrom) ? "mid" : "up";
                            $res .= $this->getFilledItemTemplate($ci, $style);
                            break;
                        }
                    }
                }
            }

            return
            " $filterTpl
              <div class=\"items\"><table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\" bgcolor=\"#DCDDE0\" class=\"items-table\">
              $res
              </table></div>
              <img src=\"/images/pix.gif\" width=\"1\" height=\"10\" alt=\"\"  />
              $pager
              ";
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



            return $tpl;
        }

        function getFilledItemDescriptionTemplate(&$catItem)
        {
            global $structureMgr, $templatesMgr;

			if (count($catItem) == 0)
                trigger_error("Invaid function call - arguments array is empty.", PM_FATAL);
                
            $add_tpl = "";
            if (_get("print") == 1)
            {
                $add_tpl = "print_";   
            }
            $tplName = GetCfg("TemplatesPath") . "/Catalogue/" . $add_tpl . $catItem["DescriptionTemplate"];
            //print_r($tplName);
            $tpl = $templatesMgr->getTemplate(-1, $tplName);

            $blocks = $templatesMgr->getValidTags($tpl,
                          array("container", "picture", "description", "spec", "details", "zoom", "print", "producer", "ref"));

            //SPECIFICATIONS
            $spec = "";

            $specs = array("Compatibility" => "Марка", "accPlantName" => "Производитель");
			$count = 0;
            
            $plID = 0;
            foreach ($specs as $key => $val)
            {
                if (isset($catItem[$key]) && $catItem[$key])
                {
					$style = ($count > 0) ? "mid" : "up";
                    $sp = $blocks["spec"];
					$sp = str_replace("%style%", $style, $sp);
                    $sp = str_replace("%spec_name%", $val, $sp);
                    if ($key == "accPlantName")
                    {
                        if ($catItem["plID"] > 0)
                        {
                            $plID = $catItem["plID"];
                            $sp = str_replace("%spec_value%", "<a href='".$structureMgr->getPathByPageID($catItem["plID"], true)."'>".$catItem[$key]."</a>", $sp);
                        } else                                  
                        {
                            $sp = str_replace("%spec_value%", $catItem[$key], $sp);
                        }
                    } else
                        $sp = str_replace("%spec_value%", $catItem[$key], $sp);                   
                    $spec .= $sp;
					$count++;
                }
            }

            // trigger_error(_get("print"), PM_FATAL);
            
            
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


			$sID = $catItem['sID']; // ID товара в структуре


            //DETAILS & COMMENTS
            function pages($count, $per_page)
            {
                $out = 'Страницы';
                $page = (isset($_GET['compage'])&&is_numeric($_GET['compage'])) ? $_GET['compage'] : 1;
                $pages = ceil($count/$per_page);
                if (($page > $pages)||($page < 1)) $page = 1;
                for ($i=1; $i<=$pages; $i++) {
                    $out .= ' - ';
                    if ($i!=$page) {
                        $out .= '<a href="?compage='.$i.'">'.$i.'</a>';
                    } else {
                        $out .= $i;
                    }
                }
                return $out;
            }

			$iCountComments = 0;
			$oResult = mysql_query("SELECT COUNT(cID) FROM pm_comments WHERE sID = '$sID' AND public = '1'");
			if ($oResult && mysql_num_rows($oResult)) {
			    list($iCountComments) = mysql_fetch_array($oResult);
			}
			$blocks['details'] = str_replace('{com_count}', $iCountComments, $blocks['details']);
            $page = (isset($_GET['compage'])&&is_numeric($_GET['compage'])) ? $_GET['compage'] : 1;

            $com_num = 15;
            $pages = ceil($iCountComments/$com_num);
            if (($page > $pages) || ($page < 1)) $page = 1;
            $com_from = ($page-1)*$com_num;
        
            if ($catItem["xit"] == 1)
            {
                $blocks['details'] = str_replace('{new_det}', "<div class=\"right\"><img src=\"/images/xitd.gif\" border=\"0\" alt=\"\" /></div>", $blocks['details']);
            } elseif ($catItem["new"] == 1)
            {
                $blocks['details'] = str_replace('{new_det}', "<div class=\"right\"><img src=\"/images/newd.gif\" border=\"0\" alt=\"\" /></div>", $blocks['details']);
            } else
            {
                $blocks['details'] = str_replace('{new_det}', "" , $blocks['details']);
            }
            
            
            
            // получить комментарии страницы
			$oResult = mysql_query("SELECT * FROM pm_comments WHERE sID = '$sID' AND public='1' ORDER BY date ASC LIMIT $com_from, $com_num");

			$aComments = array();
            $htmlComments = '';
            if ($pages>1) {
                $htmlComments .= '<div class="comment">'.pages($iCountComments, $com_num).'</div>';
            }
            // составить html-код списка комментариев
            if ($oResult && mysql_num_rows($oResult)) {
                while ( $v = mysql_fetch_assoc($oResult) ) {
                    $htmlComments .= '<div class="comment">'.date('d-m-Y', $v['date']).' | <strong>';
                    if ($v['email']) {
                        $htmlComments .= '<a href="mailto:'.$v['email'].'">'.strip_tags($v['name']).'</a>';
                    } else {
                        $htmlComments .= strip_tags( $v['name'] );
                    }
                    $htmlComments .= '</strong><br />';
                    $htmlComments .= nl2br($v['comment']);
                    $htmlComments .= '</div>';
                }
            } else {
                $htmlComments .= '<div class="comment">Здесь пока никто не оставил комментариев.</div>';
            }

            // Защитный код
            $sCode = '0000'.rand(0, 9999);
            $sCode = substr($sCode, strlen($sCode)-4, 4);
            mysql_query("INSERT INTO pm_comments_codes (id, date, code) VALUES ('', ".time().", '$sCode')");
            $iCode = mysql_insert_id();
            if (empty($iCode)) die('bad arguments');

            mysql_query("DELETE FROM pm_comments_codes WHERE date < '".(time()-600)."'");

            $tplComments = file_get_contents('_pm/templates/Catalogue/comment.html');

            $tplComments = str_replace('{comments}', $htmlComments, $tplComments);
            $tplComments = str_replace('{code}', $iCode, $tplComments);
            $tplComments = str_replace('{sID}', $sID, $tplComments);

            $tplComments = str_replace('{name}', $_POST['comment']['Name'], $tplComments);
            $tplComments = str_replace('{email}', $_POST['comment']['Email'], $tplComments);
            $tplComments = str_replace('{text}', $_POST['comment']['Text'], $tplComments);
            if (isset($_POST['comment']['Error'])&&$_POST['comment']['Error']) {
                $sError = '<div style="padding:5px; margin:5px; text-align:center; border:1px solid red;">'.
                            $_POST['comment']['Error'].'</div>';
                $tplComments = str_replace('{error}', $sError, $tplComments);
            } else {
                $tplComments = str_replace('{error}', '', $tplComments);
            }

            $blocks['details'] = str_replace('{com_content}', $tplComments, $blocks['details']);

            $blocks['details'] = str_replace('{id}', $sID, $blocks['details']);

            $details = $structureMgr->getData($catItem["sID"]);
            if ($details) {
                $blocks['details'] = str_replace('{content}', $details, $blocks['details']);
                if (isset($_POST['comment']['sID'])) {
                    $blocks['details'] = str_replace('{detals_none}', 2, $blocks['details']);
                } else {
                    $blocks['details'] = str_replace('{detals_none}', 0, $blocks['details']);
                }
            } else {
                $blocks['details'] = str_replace('{content}', '', $blocks['details']);
                $blocks['details'] = str_replace('{detals_none}', 1, $blocks['details']);
            }


            //ZOOM
			//echo GetCfg("ROOT") . $catItem["PicturePath"] . "/" . $catItem["sID"] . "_3.jpg";
            if (file_exists(GetCfg("ROOT") . $catItem["PicturePath"] . "/" . $catItem["sID"] . "_3.jpg"))
                //$catItem["bigPicture"] = $catItem["PicturePath"] . "/" . $catItem["sID"] . "_3.jpg";
				$catItem["bigPicture"] = "/pic.php?sID=".$catItem["sID"];//$catItem["sID"];
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

			if($zoom) {
				 $blocks["picture"] = str_replace("%aopen%", "<a style=\"cursor: pointer\" onClick=\"openPopupWindow('".$catItem["bigPicture"]."', 450, 450); return false;\">", $blocks["picture"]);
				 $blocks["picture"] = str_replace("%aclose%", "</a>", $blocks["picture"]);
			} else {
				 $blocks["picture"] = str_replace("%aopen%", "", $blocks["picture"]);
				 $blocks["picture"] = str_replace("%aclose%", "", $blocks["picture"]);
			}

			/**
			 * Добавление поля "РЕЙТИНГ"
			 */
			$aRatingTypes = array(0=>'Общий рейтинг', 1=>'Функции', 2=>'Цена', 3=>'Качество');
			$aRating = array(0,0,0,0);
			$oResult = mysql_query("SELECT type, grade, count FROM pm_rating WHERE sID='$sID' LIMIT 3");
			$iRatingGrage = 0;
			$iRatingCount = 0;
			if ($oResult && mysql_num_rows($oResult)==3) {
			    while ($oRating = mysql_fetch_assoc($oResult)) {
    			    $aRating[$oRating['type']] = $oRating['grade'] / $oRating['count'];
    			    $iRatingCount = $oRating['count'];
			    }
			}
			$aRating[0] = ($aRating[1]+$aRating[2]+$aRating[3]) / 3;
			for ($i=0; $i<=3; $i++) {
			    $aRating[$i] = number_format( $aRating[$i], 1, '.', '' );
			}

			function rating($r, $name)
			{
			    list($r1, $r2) = explode('.', $r);
			    $content = '';
			    $count = 5;
			    if ($r2 >= 8) {
			        $r1++;
			        $r2 = 0;
			    } elseif ($r2 < 3) {
			        $r2 = 0 ;
			    } else {
			        $r2 = 1;
			    }
			    for ($i=1; $i <= $r1; $i++) {
			        $content .= '<img src="/images/star/'.$name.'.gif" alt="" />';
			        $count--;
			    }
			    if ($r2) {
			        $content .= '<img src="/images/star/'.$name.'_half.gif" alt="" />';
			        $count--;
			    }
			    while ($count) {
			        $content .= '<img src="/images/star/'.$name.'_empty.gif" alt="" />';
			        $count--;
			    }
			    return $content;
			}

			$htmlRating = '';
			$htmlRating.= '<div class="rating"><div class="red">'.$aRating[0].'</div><div class="text">'.$aRatingTypes[0].'</div><div class="stars">'.rating($aRating[0],'green').'</div></div>';
			for ($i=1; $i<=3; $i++) {
			    $htmlRating.= '<div class="rating1"><div class="black">'.$aRating[$i].'</div><div class="text">'.$aRatingTypes[$i].'</div><div class="stars">'.rating($aRating[$i],'black').'</div></div>';
			}
			$htmlRating.= '<div class="separate">';
		    $htmlRating.= '<img src="/images/podrobnee_ocenit.gif" id="ratingBtn" alt="Оценить" />'.
		                  '<div id="ratingStatus">Товар оценили ('.$iRatingCount.')</div></div>'.
                          '<input type="hidden" name="ratingsID" id="ratingsID" value="'.$sID.'" />';
			$blocks['picture'] = str_replace('%rating%', $htmlRating, $blocks['picture']);
			/**
			 * Конец кода для поля "РЕЙТИНГ"
			 */


            /* !!!MAIN job here */



            if ($catItem["ptPercent"] == 0 && $catItem["ptID"]!=8)
                $firstPrice = "<strong>" . round($catItem["salePrice"] - ($catItem["salePrice"] * 5 / 100)) . "</strong>". " / ";
            else if($catItem["ptID"]!=8)
                $firstPrice = "<strong><span class=\"".$typeClass."\">" .
                              round($catItem["salePrice"] - ($catItem["salePrice"] * $catItem["ptPercent"] / 100)) .
                              "</span></strong>". " / ";

            //$tpl = str_replace("%price%", "$firstPrice" . " / " . $catItem["salePrice"] . " руб.", $tpl);
            $tpl = str_replace("%price%", "$firstPrice"  . $oldPriceStyle.$catItem["salePrice"].($oldPriceStyle ? "</span>":"") . " руб.", $tpl);

            $blocks["description"] = str_replace("%price%",  "$firstPrice"  . $oldPriceStyle.$catItem["salePrice"].($oldPriceStyle ? "</span>":""), $blocks["description"]);
                                                                                  
			//$blocks["description"] = str_replace("%bonus%", $bonus, $blocks["description"]);

            $blocks["description"] = str_replace("%spec%", $spec, $blocks["description"]);
            $blocks["description"] = str_replace("%details%", $blocks["details"], $blocks["description"]);
            $blocks["description"] = str_replace("%goodID%", $catItem["accID"], $blocks["description"]);
            
            if ($plID > 0)
            {
                $rf = "href='".$structureMgr->getPathByPageID($plID, true)."'";
            
                $blocks["producer"] = str_replace("%link%", $rf, $blocks["producer"]);                
            } else
            {
                $blocks["producer"] = "";
            }

            $blocks["print"] = str_replace("%link%", "href='".$structureMgr->getPathByPageID($catItem["sID"], true)."?print=1'", $blocks["print"]);
            
            
            $ref = "/ref.php?sID=".$catItem["sID"];
            $blocks["ref"] = str_replace("%link%", $ref, $blocks["ref"]);
            
            $blocks["picture"] = str_replace("%zoom%", $zoom, $blocks["picture"]);
            $blocks["picture"] = str_replace("%print%", $blocks["print"], $blocks["picture"]);
            $blocks["picture"] = str_replace("%producer%", $blocks["producer"], $blocks["picture"]);
            $blocks["picture"] = str_replace("%ref%", $blocks["ref"], $blocks["picture"]);
            $blocks["picture"] = str_replace("%image%", $catItem["stdPicture"], $blocks["picture"]);
            $blocks["picture"] = str_replace("%good_name%", $catItem["ShortTitle"], $blocks["picture"]);

            $blocks["container"] = str_replace("%picture%", $blocks["picture"], $blocks["container"]);
            $blocks["container"] = str_replace("%description%", $blocks["description"], $blocks["container"]);
        
            return $blocks["container"];
        }

        function getCatItemByPageID($pageID)
        {
            if (!$pageID)
                trigger_error("Invalid call - empty pageID", PM_FATAL);

            $q = "
                 SELECT accID, p.sID, ShortTitle, deliveryCode, ap.accPlantID, ap.sID as plID ,accPlantName, logotype, smallPicture, s.tplID, salePrice,
                 MustUseCompatibility, PicturePath, DescriptionTemplate, ptPercent, p.ptID, p.xit, p.new, p.main
                 FROM `pm_as_parts` p
                 LEFT JOIN pm_as_producer ap ON (ap.accPlantID = p.accPlantID)
                 LEFT JOIN pm_structure s ON (p.sID = s.sID)
                 LEFT JOIN pm_as_categories ac ON (s.pms_sID = ac.sID)
                 LEFT JOIN pm_as_pricetypes pt ON (pt.ptID = p.ptID)
                 WHERE s.sID = $pageID";
            $qr = mysql_query($q);

            if (!$qr)
                trigger_error("Invaid query. " . mysql_error(), PM_FATAL);

            if  (mysql_num_rows($qr) == 0)
                trigger_error("Empty result for $q", PM_WARNING);

            $res = mysql_fetch_assoc($qr);
            if ($res["MustUseCompatibility"])
            {
                $res["Compatibility"] = "";
                $q2 = "SELECT atc.carID, carModel, carName FROM pm_as_acc_to_cars atc LEFT JOIN pm_as_cars c ON (c.carID = atc.carID)
                WHERE accID=" . $res["accID"];
                $qr2 = mysql_query($q2);

                if (!$qr2)
                    trigger_error("Error retrieving car model links [$q2] - " . mysql_error(), PM_FATAL);

                while (false !== (list($carID, $carModel, $carName) = mysql_fetch_row($qr2)))
                {
                    if ($res["Compatibility"])
                        $res["Compatibility"] .= ", ";

                    $res["Compatibility"] .= "$carModel";
                    if ($carName)
                        $res["Compatibility"] .= " $carName";
                }
            }

            return $res;
        }

        function getCatItemsByParentPageID($pageID, $carID, $producerID, &$catIDList, &$prodIDList)
        {
            if (!$pageID)
                trigger_error("Invalid call - empty pageID", PM_FATAL);

            if (!$carID)
                $wCarID = "";
            else
                $wCarID = " AND atc.carID=$carID";

            if (!$producerID)
                $wProducerID = "";
            else
                $wProducerID = " AND pr.accPlantID=$producerID";

            if (count($prodIDList) > 0)
            {
                $wProducerID = " AND pr.accPlantID IN (";
                for ($i=0; $i < count($prodIDList); $i++)
                {
                    if ($i)
                        $wProducerID .= ", ";
                    $wProducerID .= $prodIDList[$i];
                }
                $wProducerID .= ") ";
            }

            $wCatIDList = "";
            $catOrder = "";
            if (count($catIDList))
            {
                $wCatIDList = " AND s.pms_sID IN (";
                for ($i = 0; $i < count($catIDList); $i++)
                {
                    if ($i)
                        $wCatIDList .= ", ";
                    $wCatIDList .= $catIDList[$i];
                }
                $wCatIDList .= ")";
                $catOrder = "s.pms_sID, ";
            }

            $q = "SELECT s.sID
FROM pm_as_parts p
LEFT  JOIN pm_structure s ON ( p.sID = s.sID )
LEFT  JOIN pm_as_acc_to_cars atc ON ( atc.accID = p.accID )
LEFT  JOIN pm_as_producer pr ON ( p.accPlantID = pr.accPlantID )
WHERE s.pms_sID=$pageID $wCarID $wProducerID $wCatIDList
ORDER  BY $catOrder OrderNumber";
            $qr = mysql_query($q);
//            print $q . "<br />\n";

            if (!$qr)
                trigger_error("Invaid query. " . mysql_error(), PM_FATAL);

            $res = array();

            if  (mysql_num_rows($qr) == 0)
            {
//                trigger_error("Empty result for $q", PM_WARNING);
                return $res;
            }
//            print mysql_num_rows($qr) . "<br />\n";
            while (false !== (list ($sID) = mysql_fetch_row($qr)))
            {
                $res[$sID] = 1;
            }

            return $res;
        }


        function getDataListByPageID($args)
        {
            $pageID = $args[0];
            if (!$pageID)
                trigger_error("Invalid call - empty pageID", PM_FATAL);

            $cols = $args[1];
            if (count($cols) == 0)
                return trigger_error("Invalid call - empty cols array", PM_FATAL);

            $cl = "";
            $join = "";
            foreach($cols as $col)
            {
                if ($cl)
                    $cl .= ", ";
                $cl .= "$col";

                if ($col == "accPlantName")
                {
                   $join .= " LEFT JOIN pm_as_producer pr ON (pr.accPlantID = p.accPlantID) ";
                }
            }

            $q = "SELECT p.sID, $cl FROM pm_as_parts p LEFT JOIN pm_structure s ON (s.sID = p.sID) $join WHERE s.pms_sID = $pageID";
            $qr = mysql_query($q);

            if (!$qr)
                trigger_error("error getting DataList for pageID=$pageID [$q] - " . mysql_error(), PM_FATAL);

            $res = array();
            while (false !== ($r = mysql_fetch_assoc($qr)))
            {
               $res[$r["sID"]] = $r;
            }

            return $res;
        }

        function getProducers()
        {
            if (count($this->producers) == 0)
            {
                $q2 = "SELECT accPlantID, accPlantName FROM pm_as_producer WHERE accPlantID > 1 ORDER BY accPlantName";
                $qr2 = mysql_query($q2);

                if (!$qr2)
                    trigger_error("Error retrieving producers - " . mysql_error(), PM_FATAL);
                $this->producers[1] = "неизвестный";

                while (false !== (list($aid, $an) = mysql_fetch_row($qr2)))
                {
                    $this->producers[$aid] = $an;
                }
            }

            return $this->producers;
        }


        function getPriceTypes()
        {
            if (count($this->priceTypes) == 0)
            {
                $q2 = "SELECT ptID, CONCAT(ptName, ' (', ptPercent, '%)') FROM pm_as_pricetypes ORDER BY ptID";
                $qr2 = mysql_query($q2);

                if (!$qr2)
                    trigger_error("Error retrieving priceTypes - " . mysql_error(), PM_FATAL);

                while (false !== (list($ptid, $pn) = mysql_fetch_row($qr2)))
                {
                    $this->priceTypes[$ptid] = $pn;
                }
            }

            return $this->priceTypes;
        }
    }
?>

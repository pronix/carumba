<?php
/**
 *  Structure Manager
 *
 */

    class StructureManager
    {
        var $dblink;
        var $defaultPageID;
        var $defaultRootPageID;
        var $defaultAdminRootPageID;
        var $branch;
        var $metaData;
        var $pageNumber;

        function StructureManager()
        {
            $this->dblink = GetCfg('dblink');
            $this->defaultPageID = -1;
            $this->defaultRootPageID = -1;
            $this->defaultAdminRootPageID = -1;
            $this->branch = array();
            $this->metaData = array();
            $this->pageNumber = array();
        }

        function unsetDefaultPageID()
        {
            mysql_query("DELETE FROM pm_config WHERE var='DefaultPageID'");
        }

        function getDefaultPageID()
        {
            if ($this->defaultPageID == -1)
            {
                $q = "SELECT val FROM pm_config WHERE var='DefaultPageID' LIMIT 1";
                $qr = mysql_query($q, $this->dblink);
                if (!$qr)
                {
                    trigger_error("DefaultPageID not found in configuration table", PM_WARNING);

                    $q = "SELECT MIN(sID) FROM pm_structure WHERE pms_sID IS NULL LIMIT 1";
                    if (!$qr)
                        trigger_error(mysql_error(), PM_FATAL);

                    list ($pageID) = mysql_fetch_row($qr);
                    $this->defaultPageID = $pageID;
                    return $pageID;
                }

                list ($pageID) = mysql_fetch_row($qr);
                $this->defaultPageID = $pageID;
            }
            return $this->defaultPageID;
        }

        function getParentPageID($pageID)
        {
            if ($pageID)
            {
                $q = "SELECT pms_sID FROM pm_structure WHERE sID = '$pageID' AND isDeleted = 0 AND isVersionOfParent=0";
                $qr = mysql_query($q, $this->dblink);
                if (!$qr)
                    trigger_error(mysql_error(), PM_FATAL);

                list ($pageID) = mysql_fetch_row($qr);
                return $pageID;
            }
            return "";
        }

        function getChildrenCount($pageID)
        {
            if ($pageID)
            {
                $q = "SELECT COUNT(sID) FROM pm_structure WHERE pms_sID = '$pageID' AND isHidden=0 AND isDeleted = 0 AND isVersionOfParent=0";
                $qr = mysql_query($q, $this->dblink);
                if (!$qr)
                    trigger_error("Ошибка при вычислении количества потомков - " . mysql_error(), PM_FATAL);

                list ($count) = mysql_fetch_row($qr);
                return $count;
            }
            return "";
        }

        function getRootPageID()
        {
            if ($this->defaultRootPageID == -1)
            {
                $q = "SELECT val FROM pm_config WHERE var='PortalRootPageID' LIMIT 1";
                $qr = mysql_query($q, $this->dblink);
                if (!$qr)
                    trigger_error("PortalRootPageID not found in configuration table", PM_FATAL);

                list ($pageID) = mysql_fetch_row($qr);
                $this->defaultRootPageID = $pageID;
            }

            return $this->defaultRootPageID;
        }

        function getPathByPageID($id, $hideDefault)
        {
            if ($id == $this->getDefaultPageID() && $hideDefault == true)
                return '/';

            $q = "SELECT URLName, pms_sID FROM pm_structure WHERE sID = '$id'";
            $qr = mysql_query($q);

            if (!$qr || mysql_num_rows($qr) == 0)
                trigger_error("Requested pageID ($id) doesn't exist in structure. " . mysql_error(), PM_FATAL);

            list ($urlName, $pms_sID) = mysql_fetch_row($qr);

            if ($pms_sID == NULL)
            {
                return ($urlName) ? "/$urlName" : "";
            }
            else
                return $this->getPathByPageID($pms_sID, false) . ($urlName ? "/$urlName" : "/$id");
        }

        function getFindPageID($id, $hideDefault, $idcmp)
        {                
            //if ($idcmp == $id) return true;

            $q = "SELECT URLName, pms_sID FROM pm_structure WHERE sID = '$id'";
            $qr = mysql_query($q);

            if (!$qr || mysql_num_rows($qr) == 0)
                trigger_error("Requested pageID ($id) doesn't exist in structure. " . mysql_error(), PM_FATAL);

            list ($urlName, $pms_sID) = mysql_fetch_row($qr); 

            if ($pms_sID == NULL)
            {
                return 0;
            }
            else
            {
                if ($id == $idcmp) return 1;
                return $this->getFindPageID($pms_sID, false, $idcmp);
            }
        }


        /* Retrieves the number of page from URL (Ex.: http://site/cat/page2)
           getPageIDByPath must be called before this method to get the expected result
           */
        function getPageNumberByPageID($pageID)
        {
            if (!isset($this->pageNumber[$pageID]))
                return 1;
            else
                return $this->pageNumber[$pageID];
        }

        /**
         * Вернет ID страницы из параметров URI
         *
         * В функцию передается URI текущей страницы.
         *
         * Возвращает ID страницы из базы. Судя по всему для дальнейшей идетнификации шаблона.
         *
         * @param string $path
         * @return integer
         */
        function getPageIDByPath($path)
        {
            global $classErrorMessage;

            $pNumber = 1;

            if ($path == '/') {
                return $this->getDefaultPageID();
            } else {

                $path = rtrim($path, '/');

                $pathComponents = explode('/', $path);

                $parent = $this->getRootPageID();


                $cnt = count($pathComponents);

                /**
                 * Begin debugging code
                 */
                //print_r($pathComponents);
                //print $parent;
                //preg_match('/^pageID=(\d+)$/', $pathComponents[1], $match);
                //print_r($match);

                //die();
                /**
                 * End debugging code
                 */

                for ($i=1; $i < $cnt; $i++)
                {
                    //print $parent;
                    //we must skip page number in URL
                    if (($i == $cnt - 1) && preg_match('/^page(\d+)$/', $pathComponents[$i], $match))
                    {
                        $this->pageNumber[$parent] = $match[1];
                        break;
                    }

                    // пропускаем подкатегории каталога ссылок
                    /*
                    if (preg_match('/^item(\d+)$/', $pathComponents[$i], $match))
                    {
                        $this->itemPart[$parent] = $match[1];
                        continue;
                    }
                    */

                    $v = prepareVar($pathComponents[$i]);

                    //print $v;

                    // пропускаем добавление ссылки
                    /*
                    if ($v == "'add'") {
                        $this->itemPart[$parent] = 'add';
                        break;
                    }
                    */

                    $q = 'SELECT `sID` FROM `pm_structure`
                          WHERE (`pms_sID`="'.$parent.'" AND `URLName`='.$v.') OR (`URLName`="" AND `sID`='.$v.') LIMIT 1';


                    $qr = mysql_query($q);
                    if (!$qr) {
                        trigger_error(mysql_error(), PM_FATAL);
                    }

                    if (mysql_num_rows($qr) == 1) {
                        list ($parent) = mysql_fetch_row($qr);
                    } else {
                        $classErrorMessage = 'Couldn\'t find the specified URL - '.$path.'<b>';
                        $parent = NULL;
                        break;
                    }
                }

                return $parent;
            }
        }

        /**
         * Выбирает из БД основные МЕТА-параметры страницы и возвращает в виде ассоциативного массива.
         *
         * В функцию передается параметр ID-страницы. Данные выбираются из таблицы pm_structure
         * в соответствии с привилегиями пользователя.
         *
         * Не понятно, для чего идет соединение с таблицей пользователей pm_users
         *
         * @param integer $pageID
         * @return array
         */
        function getMetaData($pageID)
        {
            //print 'pageID='.$pageID;
            if (!$pageID)
                return array();

            if (isset($this->metaData[$pageID]))
                return $this->metaData[$pageID];

            $q = 'SELECT Title, MetaDesc, MetaKeywords, CONCAT(LastName, \' \', FirstName)
                    Author, CreateDate, URLName, ShortTitle, isHidden, sID, pms_sID, ModuleName,
                    DataType, LinkCSSClass, tplID, OrderField
                  FROM pm_structure
                  LEFT JOIN pm_users ON (pm_structure.userID = pm_users.userID)
                  WHERE sID="'.$pageID.'" AND isDeleted=0 AND isVersionOfParent=0 LIMIT 1';
            $qr = mysql_query($q);
            if (!$qr || mysql_num_rows($qr) == 0)
            {
                trigger_error('MetaData of page #'.$pageID.' is absent - ' . mysql_error(), PM_WARNING);
                return array();
            }

            $res = mysql_fetch_assoc($qr);

            if (!isset($res['URLName']) || $res['URLName'] == '') {
                $res['URLName'] = $pageID;
            }

            $this->metaData[$pageID] = $res;
            return $res;
        }

        function getData($pageID)
        {
            if (!$pageID)
                return "";

            $q = "SELECT Content FROM pm_structure WHERE sID=\"$pageID\" AND isDeleted=0 AND isVersionOfParent=0 LIMIT 1";
            $qr = mysql_query($q);

            if (!$qr || mysql_num_rows($qr) == 0)
            {
                trigger_error("Data of page #$pageID is absent - " . mysql_error(), PM_WARNING);
                return "";
            }

            list($res) = mysql_fetch_row($qr);

            return $res;
        }

        function getTemplateID($pageID)
        {
            //per pageID data must be cached later through getting
            //SELECT * FROM pm_structure and then returning the needed parts
            //temporary solution!!!

            $q = 'SELECT tplID FROM pm_structure WHERE sID="'.$pageID.'" AND isDeleted=0 AND isVersionOfParent=0 LIMIT 1';
            $qr = mysql_query($q);

            if (!$qr || mysql_num_rows($qr) == 0)
            {
                trigger_error("TemplateID for page #$pageID could't be found - " . mysql_error(), PM_WARNING);
                return 0;
            }

            list($templateID) = mysql_fetch_row($qr);
			if($templateID == 3 && strlen($this->getTitleFromParams() ) ) {
				$templateID = 4;
			}
            return $templateID;
        }

        function getStructureForPageID($pageID, $depth)
        {
            if ($depth == 0)
                return array();

            $md = $this->getMetaData($pageID);

            if (GetCfg('InAdmin') == true)
            {
                $isHidden = '';
                $md['OrderField'] = 'OrderNumber';
            }
            else
            {
                $isHidden = 'AND isHidden=0';
            }

            $q = 'SELECT sID, URLName, Title, ShortTitle, CreateDate, LinkCSSClass, ModuleName, DataType, isHidden
                    FROM pm_structure
                    WHERE pms_sID="'.$pageID.'" '.$isHidden.' AND isDeleted=0 AND isVersionOfParent=0
                    ORDER BY ' . $md['OrderField'];
            $qr = mysql_query($q);
            if (!$qr)
                trigger_error("Error aquiring structure for page #$pageID." . mysql_error(), PM_FATAL);

            $children = array();
            $chNum = 0;
            while (($child = mysql_fetch_assoc($qr)) !== false)
            {
                $child['children'] = $this->getStructureForPageID($child['sID'], $depth - 1);

                if (!isset($child['URLName']) || $child['URLName'] == '')
                    $child['URLName'] = $child['sID'];

                $children[$chNum] = $child;
                $chNum++;
            }

            return $children;
        }

        function getChildrenDataTypesForPageID($pageID, $depth)
        {
            if ($depth == 0)
                return array();

            $md = $this->getMetaData($pageID);

            if (GetCfg('InAdmin') == true)
            {
                $isHidden = '';
                $md['OrderField'] = 'OrderNumber';
            }
            else
            {
                $isHidden = 'AND isHidden=0';
            }

            $q = "SELECT sID, Title, ModuleName, DataType FROM pm_structure WHERE pms_sID='$pageID' $isHidden AND isDeleted=0 AND isVersionOfParent=0 ORDER BY " . $md["OrderField"];
            $qr = mysql_query($q);
            
            //trigger_error($q, PM_FATAL);
            
            if (!$qr)
                trigger_error("Error aquiring structure for page #$pageID." . mysql_error(), PM_FATAL);

            $children = array();
            $chNum = 0;
            while (($child = mysql_fetch_assoc($qr)) !== false)
            {
                $child["children"] = $this->getChildrenDataTypesForPageID($child["sID"], $depth - 1);

                $children[$chNum] = $child;
                $chNum++;
            }
            return $children;
        }

        function getCurrentBranch($pageID)
        {
            if (!$pageID)
                trigger_error("pageID must be supplied.", PM_ERROR);

            $branch = array($pageID);

            $pms_sID = $pageID;

            if ($pms_sID == $this->getRootPageID())
                return $branch;

            while (1)
            {
                $q = "SELECT pms_sID FROM pm_structure WHERE sID='$pms_sID'";
                $qr = mysql_query($q);
                if (!$qr || mysql_num_rows($qr) == 0)
                    trigger_error("Error filling current branch. $q" . mysql_error(), PM_FATAL);

                list ($pms_sID) = mysql_fetch_row($qr);

                if ($pms_sID != $this->getRootPageID())
                    array_unshift($branch, $pms_sID);
                else
                {
                    if (GetCfg("InAdmin"))
                        array_unshift($branch, $pms_sID);

                    break;
                }
            }

            return $branch;
        }

        function isInCurrentBranch($pageID, $currentPageID)
        {
            $branch = $this->getCurrentBranch($currentPageID);

            for ($i = 0; $i < count($branch); $i++)
                if ($branch[$i] == $pageID)
                    return true;

            return false;
        }

		function getProducersByPageIDList($pageIDList)
        {
            if (count($pageIDList) == 0)
                trigger_error("pageIDList should have at least one element", PM_FATAL);

            $ids = "";

            for ($i = 0; $i < count($pageIDList); $i++)
            {
                if ($i > 0)
                    $ids .= ", ";
                $ids .= $pageIDList[$i];
            }

            $q = "SELECT DISTINCT p.accPlantID, accPlantName, logotype FROM pm_as_parts p, pm_structure s, pm_as_producer pr
            WHERE pms_sID in ($ids) AND p.sID = s.sID AND pr.accPlantID = p.accPlantID AND isHidden=0";
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

        /**
         * Эта функция использует "некрасивые" URL-пераметры. Ее нужно переписать
         *
         * @param array $cats
         * @param array $prodIDs
         * @param array $propVals
         * @param array $carID
         * @return string
         */
		function getTitleFromParams($cats = 0, $prodIDs = 0, $propVals = 0, $carID = 0)
		{

			if(!$cats) {
				$cats = _varByPattern('/c-\\d+/');
			}
			if(!$prodIDs) {
				$prodIDs = _varByPattern('/p-\\d+/');
			}
			if(!$propVals) {
				$propVals = _varByPattern('/propVal-\\d+/');
			}
			if(!$carID) {
				$carID = _var('carID');
			}

			$shownCats = array();
			$shownProds = array();
			$shownProps = array();
			$resArray = array();

			foreach ( $cats as $pageID ) {
				if ( !in_array( $pageID, $shownCats ) ) {
					$metaData = $this->getMetaData($pageID);
					$resArray['cats'][] = ($metaData['Title'] ? $metaData['Title'] : $metaData['ShortTitle']);
					$shownCats[] = $pageID;
				}
			}

			foreach ($propVals as $prop) {
				$val = explode('_', $prop);
				if(!in_array($val[0], $shownCats)) {
					$metaData = $this->getMetaData($val[0]);
					$resArray['cats'][] = ($metaData['Title'] ? $metaData['Title'] : $metaData['ShortTitle']);
					$shownCats[] = $val[0];
				}
			}

			if ( $carID ) {
				$query = 'SELECT pm_as_cars.carName, pm_as_cars.carModel,
				                 pm_as_autocreators.plantName
				            FROM pm_as_cars, pm_as_autocreators
				            WHERE pm_as_cars.plantID = pm_as_autocreators.plantID
				                AND pm_as_cars.carID = "'.$carID.'"';
				$result = mysql_query($query);
				$row = mysql_fetch_assoc($result);
				$resArray['other'][] = $row['plantName'].' '.($row['carName'] ? $row['carModel'].$row['carName'] : $row['carModel'] );
			}

			foreach ( $prodIDs as $prodID ) {
				if ( !in_array($prodID, $shownProds) ) {
					$query = 'SELECT * FROM pm_as_producer WHERE accPlantID="'.$prodID.'"';
					$result = mysql_query($query);
					$row = mysql_fetch_assoc($result);
					$resArray['other'][] = $row['accPlantName'];
					$shownProds[] = $prodID;
				}
			}

			foreach ( $propVals as $prop ) {
				$val = explode('_', $prop);
				if(!in_array($val[1], $shownProps)) {
					$resArray['other'][] = $val[1];
					$shownProps[] = $val[0];
				}
			}

/*
			if ( empty($resArray) ) {
			    die('yes');
			} else {
			    die('no');
			}
*/
           // print_r($resArray);

            if ( isset($resArray['cats']) && count($resArray['cats']) ) {
                $str = trim(implode(', ', $resArray['cats']));
                if (!empty($resArray['other']) && count($resArray['other'])) {
                    $str.=': '.implode(', ', $resArray['other']);
                }
                return $str;
            } else {
                return '';
            }
            /*
			if ( !empty($resArray['other']) || !empty($resArray['cats']) ) {
			    if( count($resArray['other']) || count($resArray['cats'])) {
				    $str = trim(implode(', ', $resArray['cats'])).
				        ((count($resArray['other']) && count($resArray['cats'])) ? ': ' : '').
				        implode(', ', $resArray['other']);
			    }
			    return $str;
			} else {
			    return '';
			}
			*/
		}


    
    function getTitleFromParamsTranslit($cats = 0, $prodIDs = 0, $propVals = 0, $carID = 0)
    {

			if(!$cats) {
				$cats = _varByPattern('/c-\\d+/');
			}
			if(!$prodIDs) {
				$prodIDs = _varByPattern('/p-\\d+/');
			}
			if(!$propVals) {
				$propVals = _varByPattern('/propVal-\\d+/');
			}
			if(!$carID) {
				$carID = _var('carID');
			}

			$shownCats = array();
			$shownProds = array();
			$shownProps = array();
			$resArray = array();

			foreach ( $cats as $pageID ) {
				if ( !in_array( $pageID, $shownCats ) ) {
					$metaData = $this->getMetaData($pageID);
					$resArray['cats'][] = ($metaData['Title'] ? $metaData['Title'] : $metaData['ShortTitle']);
					$shownCats[] = $pageID;
				}
			}

			foreach ($propVals as $prop) {
				$val = explode('_', $prop);
				if(!in_array($val[0], $shownCats)) {
					$metaData = $this->getMetaData($val[0]);
					$resArray['cats'][] = ($metaData['Title'] ? $metaData['Title'] : $metaData['ShortTitle']);
					$shownCats[] = $val[0];
				}
			}

			if ( $carID ) {
				$query = 'SELECT pm_as_cars.carName, pm_as_cars.carModel,
				                 pm_as_autocreators.plantName
				            FROM pm_as_cars, pm_as_autocreators
				            WHERE pm_as_cars.plantID = pm_as_autocreators.plantID
				                AND pm_as_cars.carID = "'.$carID.'"';
				$result = mysql_query($query);
				$row = mysql_fetch_assoc($result);
				$resArray['other'][] = $row['plantName'].' '.($row['carName'] ? $row['carModel'].$row['carName'] : $row['carModel'] );
			}

			foreach ( $prodIDs as $prodID ) {
				if ( !in_array($prodID, $shownProds) ) {
					$query = 'SELECT * FROM pm_as_producer WHERE accPlantID="'.$prodID.'"';
					$result = mysql_query($query);
					$row = mysql_fetch_assoc($result);
					$resArray['other'][] = $row['accPlantName'];
					$shownProds[] = $prodID;
				}
			}

			foreach ( $propVals as $prop ) {
				$val = explode('_', $prop);
				if(!in_array($val[1], $shownProps)) {
					$resArray['other'][] = $val[1];
					$shownProps[] = $val[0];
				}
			}

            if ( isset($resArray['cats']) && count($resArray['cats']) ) {
                $str = trim(implode(', ', $resArray['cats']));
                if (!empty($resArray['other']) && count($resArray['other'])) {
                    foreach ( $resArray['other'] as $key => $val ) {
                        $query = sprintf( "SELECT propValue FROM pm_as_parts_properties WHERE propValueTranslit = '%s' LIMIT 1", $val );
                        $result = mysql_query($query);
                        if ( 0 != mysql_num_rows( $result ) )
                            $resArray['other'][$key] = mysql_result( $result, 0 );
                    }
                    $str.=': '.implode(', ', $resArray['other']);
                }
                return $str;
            } else {
                return '';
            }
		}


    }
?>
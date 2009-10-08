<?php

	class Bonus extends AbstractModule
	{
		var $itemsCount = 0;

		var $LOW_DETAILED = 0;
		var $MEDIUM_DETAILED = 1;
		var $HI_DETAILED = 2;

		function Bonus()
		{
		    $this->name = 'Bonus';
			$this->publicFunctions = array('getContent', 'getBlock', 'getSubItemType', 'getItemType', 'getItemDesc',
            'getSpecificDataForEditing', 'getSpecificBlockDesc', 'updateSpecificData', 'updateAdditionalColumns');

		}

		function getContent($args)
		{
			global $structureMgr, $templatesMgr;

			SetCfg('Bonus.itemsPerPage', 10);
			SetCfg('Bonus.itemsPerCol', 1);

			$order = _get('order');

			$pageID = $args[0];

			$content = '';
			$pager = '';

			$topContent = $structureMgr->getData($pageID);



			$pNum = $structureMgr->getPageNumberByPageID($pageID);
            $URL = $structureMgr->getPathByPageID($pageID, false);

			$perPage = GetCfg('Bonus.itemsPerPage');

            $startFrom = ($pNum - 1) * $perPage;
            $endAt = $startFrom + $perPage - 1;

			$items = $this->getItems($startFrom, $endAt, $order);

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
                    $tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/' . 'pager_filter.html');
                    $purePager = '';

                    for ($i=1; $i <= $pagesCount; $i++)
                    {
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
                            $purePager .= "<a href=\"$u\" class=\"levm\">$i</a>";
                        }
                    }

					switch($order) {
						case 'name' :$tpl = str_replace('%sel1%', 'selected="selected"', $tpl); break;
						case 'price' :$tpl = str_replace('%sel2%', 'selected="selected"', $tpl); break;
						case 'desc' :$tpl = str_replace('%sel3%', 'selected="selected"', $tpl); break;
					}
					$tpl = str_replace('%sel1%', '', $tpl);
					$tpl = str_replace('%sel2%', '', $tpl);
					$tpl = str_replace('%sel3%', '', $tpl);
					$tpl = str_replace('%links%', $purePager, $tpl);

					$tpl =str_replace('%catFilter%', '', $tpl);
                    $pager = str_replace('%links%', $purePager, $tpl);
                }

			}

			$i = 1;
			$content .= '<div class="items"><table cellpadding="0" cellspacing="0" class="items-table">';
			foreach($items as $item) {
				if(($i-1) % GetCfg('Bonus.itemsPerCol') == 0) {
					$content .= '<tr>';
				}
				$style = 'td'.(($i > GetCfg('Bonus.itemsPerCol')) ? 'dwn' : 'up').'left';
				$content .= $this->getFilledTemplate($item, $this->HI_DETAILED, $style);
				if($i % GetCfg('Bonus.itemsPerCol') == 0) {
					$content .= '</tr>';
				}
				$i++;
			}
			$content .= '</table></div>';

			return $topContent.$pager.$content.$pager;
		}

		function getBlock()
		{

          SetCfg('Bonus.itemsPerPage', 2);
          SetCfg('Bonus.itemsPerCol', 2);


          $content = '';

          $items = $this->getRandomBlockItems();
          $i = 1;
          $content .= '<div class="items"><table cellpadding="0" cellspacing="0" class="items-table">';
			 //print_r($items);
          foreach($items as $item) {
				 if(($i-1) % GetCfg('Bonus.itemsPerCol') == 0) {
					  $content .= '<tr>';
				 }
				 $style = 'td'.(($i > GetCfg('Bonus.itemsPerCol')) ? 'dwn' : 'up').(($i % GetCfg('Bonus.itemsPerCol') != 0) ? 'left' : 'right');
				 $content .= $this->getFilledTemplate($item, $this->MEDIUM_DETAILED, $style);
				 if($i % GetCfg('Bonus.itemsPerCol') == 0) {
					  $content .= '</tr>';
				 }
				 $i++;
         }
         $content .= '</table>';

         return $content.'<div class="more"><img src="/images/arr_gray2.gif" width="7" height="9"  alt="" /><a href="/main/bonus/">Подробнее о спецпредложениях</a></div></div>';
}

		function getFilledTemplate($catItem, $detailed = 0, $columnStyle = 'tdupleft')
		{
			  global $structureMgr, $templatesMgr;

			  //print_r($catItem);

           if (count($catItem) == 0) {
               trigger_error('Invaid function call - arguments array is empty.', PM_FATAL);
           }

           /*
           if( !isset( $catItem['Compatibility'] ) ) {
               trigger_error('Не установлен индекс "Compatibility" в массиве "catItem".', PM_FATAL);
		   }
		   */
           $catItem['tplID'] = $catItem['Compatibility'] ? 1 : 2;
           $URL = $structureMgr->getPathByPageID($catItem['sID'], false);
           $tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/Catalogue/bonus'.$catItem['tplID'] . '.html');
           $tpl = str_replace('%title%', $catItem['ShortTitle'], $tpl);
           $tpl = str_replace('%link%', $URL, $tpl);

			// begin Вывод рейтинга графичесики
			$rating = ($catItem['rating']) ? $catItem['rating'] : 0;
			include('_pm/modules/ratingGraph.php');
            // end Вывод рейтинга графичесики

            $tpl = str_replace('%columnStyle%', $columnStyle, $tpl);


           $tpl = str_replace('%typename%', 'Спецпредложение (<a href="/main/club">по карте</a>)', $tpl);
           $tpl = str_replace('%type%', 't_bonus', $tpl);

           //price generation must be moved to special function as it is called from at least two places
           if ($catItem['ptPercent'] == 0) {
               $firstPrice = '<strong>' . round($catItem['salePrice'] - ($catItem['salePrice'] * 5 / 100)) . '</strong>';
           } else {
               $firstPrice = '<strong><span class="t_bonus">' .
                             round($catItem['salePrice'] - ($catItem['salePrice'] * $catItem['ptPercent'] / 100)) .
                             '</span></strong>';
           }




            $tpl = str_replace('%price%', '<span class="t_bonus">'.$firstPrice.
                        '</span> / ' . $catItem['salePrice'] . ' руб.', $tpl);

			$tpl = str_replace('%bonus%', $catItem['ptPercent'], $tpl);

			if ($detailed > 0) {
				$tpl = str_replace('%producer%', '<strong>Производитель: </strong>' . $catItem['accPlantName'], $tpl);
			} else {
				$tpl = str_replace('%producer%', '', $tpl);
			}
			$props = $this->getCatItemProperties($catItem['sID'], 'CatItem', $structureMgr->getParentPageID($catItem['sID']));


			if ( ($detailed > 1) && !empty($props) )
			{
				$prop_list = '';
				foreach ($props as $prop)
				{
				   if ($prop[3] && !$prop[4])
				   {
					   //$prop_list .= "<strong>{$prop[1]}:</strong> {$prop[3]} {$prop[2]}<br />\n";
					   $prop_list .= '<strong>'.$prop[1].':</strong> '.$prop[3].' '.$prop[2].'<br />';
				   }

				}
				$tpl = str_replace('%props%', $prop_list, $tpl);
				if (!isset($catItem['Compatibility'])) {
					$catItem['Compatibility'] = '';
				} else {
					$catItem['Compatibility'] = '<strong>Марка:</strong>' . $catItem['Compatibility'];
				}

				$tpl = str_replace('%car_compatibility%', $catItem['Compatibility'], $tpl);
			} elseif( ($detailed == 1) && !empty($props) ) {
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
            $tpl = str_replace('%picture%', '<img width="70" height="70" src="'.$catItem['smallPicture'].'" alt="'. $catItem['ShortTitle'].'" />', $tpl);
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
                            trigger_error('Error fetching accID for CatItemProperties ' . mysql_error(), PM_FATAL);


                        $q = 'SELECT app.propListID, propName, accMeasure, propValue, isHidden
                                FROM pm_as_prop_list apl, pm_as_parts_properties app
                                WHERE app.accID='.$accID.' AND app.propListID = apl.propListID
                                ORDER BY apl.OrderNumber';

                        $qr = mysql_query($q);
                        if (!$qr)
                            trigger_error('Error while query - ' . mysql_error(), PM_FATAL);

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

                            $q2 = 'SELECT propListID, propName, accMeasure, \'\', isHidden FROM pm_as_prop_list WHERE accCatID='.$accCatID;
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
                    return array();
            }
        }

		function getRandomBlockItems()
		{
			$query = "SELECT DISTINCT
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
					WHERE pt.ptID = 2 || pt.ptID = 3 || pt.ptID = 4
					ORDER BY RAND()
					LIMIT ".GetCfg('Bonus.itemsPerPage');
			$result = mysql_query($query);
			if (!$result)
                trigger_error('Invaid query. ' . mysql_error(), PM_FATAL);

            if (mysql_num_rows($result) == 0)
                trigger_error('Empty result', PM_WARNING);

			$catItems = array();

			while($item = mysql_fetch_assoc($result)) {
				$item['Compatibility'] = '';
				if ($item['MustUseCompatibility'])
				{
					$query2 = 'SELECT atc.carID, carModel, carName
					               FROM pm_as_acc_to_cars atc
					               LEFT JOIN pm_as_cars c ON (c.carID = atc.carID)
					               WHERE accID=' . $item['accID'];
					$result2 = mysql_query($query2);

					if (!$result2)
						trigger_error('Error retrieving car model links' . mysql_error(), PM_FATAL);

					while (false !== (list($carID, $carModel, $carName) = mysql_fetch_row($result2)))
					{
						if ($item['Compatibility'])
							$item['Compatibility'] .= ', ';

						$item['Compatibility'] .= $carModel;
						if ($carName)
							$item['Compatibility'] .= ' $carName';
					}
				}
				$catItems[] = $item;
			}

			return $catItems;
		}

		function getItems($startFrom, $endAt, $order)
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
					WHERE pt.ptID = 2 || pt.ptID = 3 || pt.ptID = 4
					'.$orderStr.'
					LIMIT '.$startFrom.','.GetCfg('Bonus.itemsPerPage');
			$result = mysql_query($query);
			if (!$result)
                trigger_error('Invaid query. ' . mysql_error(), PM_FATAL);

            if (mysql_num_rows($result) == 0)
                trigger_error('Empty result', PM_WARNING);

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
						trigger_error('Error retrieving car model links' . mysql_error(), PM_FATAL);

					while (false !== (list($carID, $carModel, $carName) = mysql_fetch_row($result2)))
					{
						if ($item['Compatibility']) {
							$item['Compatibility'] .= ', ';
						}

						$item['Compatibility'] .= ' '.$carModel;

						if ($carName) {
							$item['Compatibility'] .= ' '.$carName;
						}
					}
				}
				$catItems[] = $item;
			}



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
                case 'Bonus':
                    return 'Параметры';
            }

            return '';
        }

        function getItemDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case 'Bonus':
                    return "";
            }

            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case 'Bonus':
                    return array('спец предложения',
                                 'спец предложений',
                                 'спец предложениям'
                                ); //Именит, Род, Дат
            }

            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case 'Catalogue':
                    return array('Bonus' => 'спец предложения');
				case 'Article':
                    return array('Bonus' => 'спец предложения');
            }
            return array();
        }

        function updateAdditionalColumns($args)
        {
            return false;
        }

	}

?>
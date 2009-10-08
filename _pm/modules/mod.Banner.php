<?php

	class Banner extends AbstractModule
	{
		function Banner()
		{
			$this->publicFunctions = array("getContent", "getBlock","getSubItemType", "getItemType", "getItemDesc", 
            "getSpecificDataForEditing", "getSpecificBlockDesc", "updateSpecificData");

		}

		function getContent()
		{
			$param = _get("param");
			if($param) {
				$banner = $this->getBannerByParam($param);
				$this->incClick($banner['banID']);
				//echo "location: ".$banner['link']. " from ".$_SERVER['HTTP_REFERER']."<br>";
				header("location: ".$banner['link']);
			} else {
				$content = $this->getRandomBanner();
				return $content;
			}
		}

		function getBlock()
		{
			$content = $this->getRandomBanner();
			return $content;
		}

		function getRandomBanner()
		{
			$banner = "";
			$query = "SELECT banID ,url, type, text, link, param FROM pm_banners WHERE isactive = 1 ORDER BY `show`, banID LIMIT 1";
			
      $result = mysql_query($query);
			if(mysql_num_rows($result)) {
				$row = mysql_fetch_assoc($result);
				$this->incShow($row['banID']);
				switch($row['type']) {
					case "swf" : {
						$banner = "				
						<object type=\"application/x-shockwave-flash\" data=\"".$row['url']."\" width=\"175\" height=\"300\">
						<param name=\"movie\" value=\"".$row['url']."\" />
						</object>";
						break;
					}
					default : {
						$banner = "<a href=\"/banner?param=".$row['param']."\"><img src=\"".$row['url']."\" alt=\"".$row['text']."\"  /></a>";
						break;
					}
				}
				
			}
			return $banner;
		}

		function incShow($banID)
		{
			if (!$banID)
			{
				trigger_error("Error no banID [$banID] ", PM_FATAL);
			}
			$banner = $this->getBanner($banID);

			$query = "UPDATE pm_banners SET `show` = '".($banner['show']+1)."' WHERE banID = '".$banID."'";
			if (!mysql_query($query))
			{
				trigger_error("Error while update banner [$banID] - $query " . mysql_error(), PM_FATAL);
			}
		}
		
		function getBanner($banID) {
			$banner = "";
			$query = "SELECT banID, url, type, text, link, click, `show`, param FROM pm_banners WHERE banID = '".$banID."'";
			$result = mysql_query($query);
			if(mysql_num_rows($result)) {
				$banner = mysql_fetch_assoc($result);
			}
			return $banner;	
		}

		function getBannerByParam($param) {
			$banner = "";
			$query = "SELECT banID, url, type, text, link, click, `show`, param FROM pm_banners WHERE param = '".$param."'";
			$result = mysql_query($query);
			if(mysql_num_rows($result)) {
				$banner = mysql_fetch_assoc($result);
			}
			return $banner;	
		}

		function incClick($banID)
		{
			if (!$banID)
			{
				trigger_error("Error no banID [$banID] ", PM_FATAL);
			}
			$banner = $this->getBanner($banID);

			$query = "UPDATE pm_banners SET click = '".($banner['click']+1)."' WHERE banID = '".$banID."'";
			if (!mysql_query($query))
			{
				trigger_error("Error while update banner [$banID] - $query " . mysql_error(), PM_FATAL);
			}
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
                case "Banner":
                    return "";
            }
            
            return "";
        }

        function getItemDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Banner":
                    return "";
            }
            
            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "Banner":
                    return array("баннер", "баннера", "баннеру"); //Именит, Род, Дат
            }
            
            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case "Catalogue":
                    return array("Banner" => "баннер");
				case "Article":
                    return array("Banner" => "баннер");
            }
            return array();
        }

	}

		

?>

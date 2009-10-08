<?
    class _service extends AbstractModule
    {
        function _service()
        {
            $this->name = "ServiceFuncs";
            $this->desc = "Provides a number of service functions to insert into templates. No editing is expected.";
            $this->publicFunctions = array("getHead", "getPageTitle", "authBlock");
        }

        function getPageTitle($args)
        {
            global $structureMgr;
            //print_r($args);
			$str = $structureMgr->getTitleFromParams();

            if ($args[0] != -1)
            {
                $metaData = $structureMgr->getMetaData($args[0]);
				if(!$str)
                return ($metaData["Title"] ? $metaData["Title"] : $metaData["ShortTitle"])." ".$str;
            }

            return $str;
        }

        function getHead($args)
        {
            global $structureMgr;
            
            if ($args[0] != -1)
                $metaData = $structureMgr->getMetaData($args[0]);
            else
                $metaData = array();

            if ($metaData["Title"] == "")
                $metaData["Title"] = $metaData["ShortTitle"];
			
			if($metaData["DataType"] == 'CatItem') {
				$metaData['MetaDesc'] = $metaData["Title"]. ' в Санкт-Петербурге';
				$metaData['MetaKeywords'] = $metaData["Title"]. ' в Санкт-Петербурге';
				if ($metaData["Title"]) 
					$metaData["Title"] .= " в Санкт-Петербурге";
				else
					$metaData["Title"] = GetCfg("SiteName");
			}else {
				if (!$metaData["Title"]) 
					$metaData["Title"] = GetCfg("SiteName");
			}

            

            $result = <<<EF
<title>$metaData[Title]</title>
<meta name="Description" content="$metaData[MetaDesc]" />
<meta name="Keywords" content="$metaData[MetaKeywords]" />
EF;
//<meta name="Author" content="$metaData[Author]">
            return $result;
        }


        function authBlock($args)
        {
            global $authenticationMgr, $templatesMgr;
            
            $containerID = $args["id"];

            if (!isset($args["TEMPLATE"]))
               trigger_error("template filename must be specified for autBlock [$containerID]", PM_FATAL);
            
            $tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/" . $args["TEMPLATE"]);

            $block = "";

            $userID = $authenticationMgr->getUserID();
            $tpl = str_replace("\r", "\\r", $tpl);
            $tpl = str_replace("\n", "\\n", $tpl);

            if ($userID > 1)
            {
                if (preg_match("/\<block\s+authenticated=\"1\"\s*\>(.*?)\<\/block\>/", $tpl, $match))
                    $block = $match[1];
                else
                    trigger_error("In template [" . $args["TEMPLATE"] . "] section authenticated=\"1\" not found.", PM_FATAL);

                $uData = $authenticationMgr->getUserData($userID);
                
                $block = str_replace("%username%", $uData["FirstName"] . " " . $uData["LastName"], $block);
				$block = str_replace("%carInfo%", $uData["plantName"] . " " . $uData["carModel"], $block);
            }
            else
            {
                if (preg_match("/\<block\s+authenticated=\"0\"\s*\>(.*?)\<\/block\>/", $tpl, $match))
                    $block = $match[1];
                else
                    trigger_error("In template [" . $args["TEMPLATE"]. "] section authenticated=\"0\" not found.", PM_FATAL);
				$block = str_replace("%carInfo%", "", $block);
                $block = str_replace("%currentpath%", str_replace("&","&amp;", getenv("REQUEST_URI")), $block);
            }

            $block = str_replace("\\r", "\r", $block);
            $block = str_replace("\\n", "\n", $block);
            return $block;

        }
    }
?>

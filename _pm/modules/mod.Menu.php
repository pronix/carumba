<?
    class Menu extends AbstractModule
    {
        function Menu()
        {
//            $this->name = "Menu";
//            $this->desc = "Provides a menu system";
            $this->publicFunctions = array("getMenu");
            $this->cmdFunctions = array();
        }

        function &parseXMLTemplate($xmlTemplate)
        {
            $tpl = array("container" => "%items%");

            if ($xmlTemplate == "")
                trigger_error("Empty template for parsing", PM_FATAL);
            
            $xmlTemplate = set_safe_newlines($xmlTemplate);

            $tpl["tpl"] = $xmlTemplate;
            
            $matches = array();

            if (preg_match("/\<type\>(.*?)\<\/type\>/", $xmlTemplate, $match_t)) {
                $tpl["type"] = $match_t[1];
            } else {
                $tpl["type"] = "VerticalTree";
            }
            if (preg_match_all('/<menu\s+level="(\d+)"\s*>(.*?)<\/menu>/is', $xmlTemplate, $match3))
            {
                for ($i = 0; $i < count($match3[0]); $i++)
                {
                    if (preg_match("/\<container\>(.*?)\<\/container\>/s", $match3[2][$i], $match4))
                        $tpl[$match3[1][$i]]["container"] = remove_safe_newlines($match4[1]);

                    if (preg_match("/\<separator\>(.*?)\<\/separator\>/s", $match3[2][$i], $match5))
                        $tpl[$match3[1][$i]]["separator"] = remove_safe_newlines($match5[1]);
                    
                    if (preg_match("/\<item\s+active=\"1\"\s*\>(.*?)\<\/item\>/s", $match3[2][$i], $match6))
                        $tpl[$match3[1][$i]]["activeitem"] = remove_safe_newlines($match6[1]);
                    
                    if (preg_match("/\<item\s+active=\"0\"\s*\>(.*?)\<\/item\>/s", $match3[2][$i], $match7))
                        $tpl[$match3[1][$i]]["item"] = remove_safe_newlines($match7[1]);
                }
            } else {
												    die('Совпадения не найдены');
												}

            $tpl["tpl"] = remove_safe_newlines($tpl["tpl"]);
            return $tpl;
        }
        
        function getMenu($args)
        { 
            global $structureMgr, $templatesMgr, $_defTemplate;
            $pageID = $args[0];
            $containerID = $args["id"];

            if (!isset($args["DEPTH"]))
                $level = 9999; //no limits for depth of menu
            else
            {
                $level = $args["DEPTH"];
                if ($level < 0)
                    $level = 9999;
            }

            if (!isset($args["EXPANDED"]))
                $expanded = false; //expand only if inside of a branch
            else
                $expanded = $args["EXPANDED"];

            if (!isset($args["TEMPLATE"]))
               trigger_error("template filename must be specified for menu [$containerID]", PM_FATAL);
            
            $xmlTemplate = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/" . $args["TEMPLATE"]);
            //print $xmlTemplate;
            $template = $this->parseXMLTemplate(&$xmlTemplate);
            //print "----------------------------\n";
            //print_r( $template );
            
            if (!isset($args["ROOTID"])) {
                $rootID = $structureMgr->getRootPageID();
            } else {
                $rootID = $args["ROOTID"];
                switch ($rootID)
                {
                    case "current":
                        $rootID = $pageID;
                        break;
                    case "parent":
                        $rootID = $structureMgr->getParentPageID($pageID);
                        break;
                }
            }
            
            $path = $structureMgr->getPathByPageID($rootID, false);
            $menuStructure = $structureMgr->getStructureForPageID($rootID, $level);

            $menu = $this->getMenuLevel(&$menuStructure, $pageID, $path, 1, $level, $expanded, &$template);
            return $menu;
        }



        function getMenuLevel(&$struct, $pageID, $path, $level, $maxlevel, $expanded, &$template)
        {
            global $structureMgr;

            if ($maxlevel < $level)
                return;

            $lines = '';
            $lines1 = '';

            //here we must setup vars $activeitem, $item, $separator, $container from $template reference
            $places = array('separator' => '' );

            $repl = array('activeitem', 'item', 'container', 'separator');
			$repl_count = count($repl);
			//print_r($template);
			//die($template[1]);
            for ($i = 0; $i < $repl_count; $i++)
            {
                if ( isset( $template[$level][$repl[$i]] ) ) {
                    $places[$repl[$i]] = $template[$level][$repl[$i]];
				} else {
                    $l = $level - 1;
                    while ($l > 0)
                    {
                        if (isset($template[$l][$repl[$i]]))
                        {
                            $places[$repl[$i]] = $template[$l][$repl[$i]];
                            break;
                        }
                        $l--;
                    }
                    if ($l == 0)
                    {
                        trigger_error("${repl[$i]} not found for level $level (maxlevel = $maxlevel)", PM_FATAL);
                    }
                }
            }
            $sep = $places["separator"];

            //navibar type generation start
            if ($template["type"] == "NavigationalBar")
            {
                $br = $structureMgr->getCurrentBranch($pageID);
                
                $lpath = "/";
                //special block to always have a link to root of the site
                if ($br[0] != $structureMgr->getDefaultPageID())
                {
                    $item = $structureMgr->getMetaData($structureMgr->getDefaultPageID());
                    $tmp = $places["item"];
                    $tmp = str_replace("%link%", $lpath, $tmp);
                    $tmp = str_replace("%text%", $item["ShortTitle"], $tmp);

                    if ($item["LinkCSSClass"] != "")
                        $tmp = str_replace("%css%", $item["LinkCSSClass"], $tmp);
                    else
                        $tmp = str_replace("%css%", "", $tmp);

                    $lines1 .= $tmp . $sep;
                }

				$str = $structureMgr->getTitleFromParams();
				
                for ($i = 0; $i < count($br); $i++)
                {
                    $item = $structureMgr->getMetaData($br[$i]);
                    if ($br[$i] != $structureMgr->getDefaultPageID())
                        $lpath .= "$item[URLName]";
                    
                    if ($item["isHidden"])
                    {
                        $lpath .= "/";
                        continue;
                    }

                    if ($i == count($br) - 1 && !strlen($str))
                        $tmp = $places["activeitem"];
                    else
                        $tmp = $places["item"];

                    $tmp = str_replace("%link%", $lpath, $tmp);
                    $tmp = str_replace("%text%", $item["ShortTitle"], $tmp);

                    $lines1 .= $tmp;

                    if ($i < count($br) - 1)
                        $lines1 .= $sep;
                    
                    //for all levels except first we must use full pathname
                    if ($br[$i] == $structureMgr->getDefaultPageID())
                        $lpath .= "$item[URLName]/";
                    else
                        $lpath .= "/";

                }
				//parsing args for search
				
				
				//$tmp = $places["activeitem"];
				//$tmp = str_replace("%link%", $_SERVER['QUERY_STRING'], $tmp);
                //$tmp = str_replace("%text%", $str, $tmp);
				//$lines1 .= $tmp;

				//end of parsing args for search
                if ($lines1)
                {
                    $tmp = $places["container"];
                    $tmp = str_replace("%items%", $lines1, $tmp);

                    return $tmp;
                }
                else
                    return "";
            } //navibar type menu generation end

            $chBlock = "";

            $count = count($struct);

            for ($i = 0; $i < $count; $i++)
            {
                $item = $struct[$i];
                if ($item["sID"] != $structureMgr->getDefaultPageID())
                    $lpath = "$path/$item[URLName]";
                else
                    $lpath = "/";

                if ($item["sID"] == $pageID || $structureMgr->isInCurrentBranch($item["sID"], $pageID))
                    $tmp = $places["activeitem"];
                else
                    $tmp = $places["item"];

                $tmp = str_replace("%link%", $lpath, $tmp);
                $tmp = str_replace("%text%", $item["ShortTitle"], $tmp);
                if ($item["LinkCSSClass"] != "")
                    $tmp = str_replace("%css%", $item["LinkCSSClass"], $tmp);
                else
                    $tmp = str_replace("%css%", "", $tmp);

                if ($i > 0)
                    $lines1 .= $sep;
                $lines1 .= $tmp;

                if (count($item["children"]) && ($expanded || $structureMgr->isInCurrentBranch($item["sID"], $pageID)))
                {
                    $chBlock = $this->getMenuLevel(&$item["children"], $pageID, "$path/$item[URLName]", $level + 1, $maxlevel, $expanded, $template);

                    if ($template["type"] == "VerticalTree")
                        $lines1 .= $chBlock;
                }
            }
            if ($lines1)
            {
                $tmp = $places["container"];
                $tmp = str_replace("%items%", $lines1, $tmp);
                
                if ($template["type"] == "HorizontalTabs")
                    $tmp .= $chBlock;

                return $tmp;
            }
            else
                return "";

        }
    }


?>
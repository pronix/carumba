<?
/*
    Templates Manager
*/
    class TemplatesManager
    {
        var $_templates;

        function TemplatesManager()
        {
             $this->_templates = array();
        }

        function &getFilledTemplate($pageID, $fileName)
        {
            global $structureMgr, $modulesMgr;
            
            if ($pageID != -1)
            {
                $templateID = $structureMgr->getTemplateID($pageID);

                $tplData = "";
                
                if ($templateID > 0)
                {
                    $tplData = $this->getTemplate($templateID, "");
                }
            }
            else
            {
                $tplData = $this->getTemplate(-1, $fileName);
            }

            if (!$tplData)
                trigger_error("Empty template data for template #$templateID", PM_FATAL);

            $filledData = $this->fillContainers($tplData, $templateID, $pageID);

            return $filledData;
        }
        
        function getDefaultTplID()
        {
            $q = "SELECT val FROM pm_config WHERE var='DefaultTplID'";
            $qr = mysql_query($q);
            if (!$qr)
            {
                trigger_error("DefaultTplID not found in configuration table", PM_WARNING);

                $q = "SELECT MIN(tplID) FROM pm_templates";
                $qr = mysql_query($q);
                if (!$qr)
                    trigger_error(mysql_error(), PM_FATAL);
                
                list ($tplID) = mysql_fetch_row($qr);
                return $tplID;
            }

            list ($tplID) = mysql_fetch_row($qr);
            return $tplID;
        }


        function getTemplate($templateID, $fileName)
        {
            if ($templateID != -1)
            {
                $q = "SELECT tplFilename FROM pm_templates WHERE tplID = '$templateID'";
                $qr = mysql_query($q);
                if (!$qr || mysql_num_rows($qr) == 0)
                    trigger_error("Unknown templateID", PM_FATAL);

                list($tplFilename) = mysql_fetch_row($qr);
                
                $fname = GetCfg("TemplatesPath") . "/$tplFilename";
            } else {
                $fname = $fileName;
            }

            if (isset($this->_templates[$fname]))
                return $this->_templates[$fname];

            if (!file_exists($fname))
                trigger_error("Template [$fname] not found", PM_FATAL);

            $fp = fopen($fname, "r"); 
            if (!$fp)
                trigger_error("Couldn't read template $fname", PM_FATAL);
            
            $contents = fread($fp, filesize($fname));
            fclose($fp);

            $this->_templates[$fname] = $contents;

            return $contents;
        }

        function getTemplates()
        {
            $tpls = array();
            $q = "SELECT tplID, tplName FROM pm_templates";
            $qr = mysql_query($q);
            if (!$qr || mysql_num_rows($qr) == 0)
                trigger_error("Error retrieving templates [$q] - " . mysql_error(), PM_FATAL);

            while (false !== (list($tID, $tName) = mysql_fetch_row($qr)))
            {
                $tpls[$tID] = $tName;
            }

            return $tpls;
        }

        function &fillContainers(&$tplData, $templateID, $pageID)
        {
            if (preg_match_all("/\<container ((?:.|^\>)*?)\/\>/", $tplData, $matches))
            {
                $filledData = $tplData;
                for ($i = 0; $i < count($matches[0]); $i++)
                {
                    $filledContainer = $this->fillContainer($matches[1][$i], $templateID, $pageID);
                    $filledData = str_replace($matches[0][$i], $filledContainer, $filledData);
                }                                                                          
            }
            return $filledData;
        }

        function &fillContainer(&$container, $templateID, $pageID)
        {
            global $modulesMgr, $structureMgr, $authenticationMgr, $permissionsMgr;
            $filledContainer = "";
//parse parameters
            $params = preg_split("/(?<=\")\s+/", $container, -1, PREG_SPLIT_NO_EMPTY);

            $cType = "inline";
            $id = "";
            $style = "";
            $class = "";
            $namedId = "";
            $moduleName = "";
            $funcName = "";

            $args = array($pageID);

            for ($i = 0; $i < count($params); $i++)
            {
                if (preg_match("/(\w+)\=\"((?:.|^\")+)\"/", $params[$i], $matches))
                {
                    switch ($matches[1])
                    {
                        case "type": 
                            $cType = $matches[2];
                            break;
                        
                        case "module": 
                            $moduleName = $matches[2];
                            break;
                        
                        case "function": 
                            $funcName = $matches[2];
                            break;
                        
                        case "id": 
                            $namedId = $matches[2];
                            $id = " id=\"" . $namedId . "\"";
                            $args["id"] = $namedId;
                            break;
                        
                        case "style":
                            $style = " style=\"" . $matches[2] . "\"";
                            break;

                        case "class":
                            $class = " class=\"" . $matches[2] . "\"";
                            break;

                       default:
                           $args[strtoupper($matches[1])] = $matches[2];
                    }
                }
            }

            if (!$namedId)
            {
                trigger_error("No [id] defined in container " . htmlentities($container), PM_WARNING);
                continue;
            }

//this type of content means that we must get the name of module from structure and call it's method "getContent"
            if ($cType == "MainContent")
            {
                $funcName = "getContent";
                $metaData = $structureMgr->getMetaData($pageID);
                $moduleName = $metaData["ModuleName"];
                $cType = "inline";
                
                if ($permissionsMgr->canUpdate($pageID, $authenticationMgr->getUserID()))
                {
                    require_once("_quickAdmin.php");
                    $filledContainer = quickAdminBlock($pageID);
                }
            }


//here we decide, what kind of container will be filled and how it will be performed
            if (!$moduleName || !$funcName)
            {
                list ($moduleName, $funcName) = $this->getTemplateContainerParams($templateID, $namedId);
            }
//next line does the mostly valueable thing in the whole system - it executes the needed function of exact module            
            $fillValue = $modulesMgr->execute($moduleName, $funcName, $args, false);

//this switch identifies the behaviour of replacing the container - with div or without it.
//maybe later some other types will appear, but I am not sure that it is necessary            
            switch ($cType)
            {
                case "div":
                    $filledContainer .= "<div$id$style$class>$fillValue</div>";
                    break;

                case "inline":
                    $filledContainer .= $fillValue;
                    break;
            }

            return $filledContainer;
        }

        function getTemplateContainerParams($templateID, $namedId)
        {
            $q = "SELECT tcModule, tcFunc FROM pm_template_containers WHERE tplID='$templateID' AND tcTplNamedID='$namedId'";
            $qr = mysql_query($q);
            if (!$qr || mysql_num_rows($qr) == 0)
                trigger_error("Couldn't acquire container info" . mysql_error(), PM_FATAL);
            $res = mysql_fetch_row($qr);

            return $res;
        }

        function getValidTags($tpl, $validTags)
        {
            $blocks = array();

            $tpl = set_safe_newlines($tpl);

            for ($i = 0; $i < count($validTags); $i++)
            {
                if (preg_match('/<' . $validTags[$i] . '>(.*?)<\/' . $validTags[$i] . '>/s', $tpl, $match))
                {
                    $blocks[$validTags[$i]] = $match[1];
                    $blocks[$validTags[$i]] = remove_safe_newlines($blocks[$validTags[$i]]);
                }
            }

            return $blocks;
        }
    }
?>

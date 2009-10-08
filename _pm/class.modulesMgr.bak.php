<?
/**
 *  Modules Manager
 * 
 */
    class ModulesManager
    {
        var $_modules;
		
		// __construct
        function ModulesManager()
        {
            $this->_modules = array();
            
            if (file_exists(GetCfg('ModulesPath')))
            {
                if ($dirHandle = opendir(GetCfg('ModulesPath'))) 
                {
                    while (false !== ($file = readdir($dirHandle))) 
                    {
                        if ($file != "." && $file != "..")
                        {
                            if (substr($file, 0, 4) == "mod.")
                            {
                                $m_name = substr($file, 4, strlen($file) - 8);
                                $this->_modules[$m_name] = array("fileName" => $file, "loaded" => 0, "mod" => NULL);
                            }
                            elseif (substr($file, 0, 4) == "cfg.")
                            {//reading cfg that is required to do per-module editing
                                require_once(GetCfg("ModulesPath") . "/" . $file);
                            }
                        }
                    }

                    closedir($dirHandle);
                }
            }
        }


        function modules()
        {
            return $this->_modules;
        }

        function moduleExists($moduleName)
        {
            return isset($this->_modules[$moduleName]);
        }

        function execute($moduleName, $funcName, $args, $silent)
        {
            if (isset($this->_modules[$moduleName]))
            {
                if ($this->_modules[$moduleName]["loaded"] == 0)
                {
                    $this->loadModule($moduleName);
                }
                return $this->_modules[$moduleName]["mod"]->execute($funcName, $args, $silent);
            }
            else
                trigger_error("Module [$moduleName] not found.", PM_FATAL);
        }

        function loadModule($moduleName)
        {
            if (isset($this->_modules[$moduleName]))
            {
                if ($this->_modules[$moduleName]["loaded"] == 0)
                {
                    require_once(GetCfg("ModulesPath") . "/" . $this->_modules[$moduleName]["fileName"]);

                    if (class_exists($moduleName))
                    {
                        $mod = new $moduleName();
                        if ($mod)
                            $this->_modules[$moduleName]["mod"] = $mod;
                        else
                            trigger_error("Couldn`t create an instance of [$moduleName] class.", PM_FATAL);
                    }
                    else
                        trigger_error("Module " . $this->_modules[$moduleName]["fileName"] . 
                                      " doesn`t contain a definition of [$moduleName] class.", PM_FATAL);

                    $this->_modules[$moduleName]["loaded"] = 1;
                }
                else
                    trigger_error("Trying to load already loaded module [$moduleName].", PM_WARNING);
            }
            else
                trigger_error("Module [$moduleName] not found.", PM_FATAL);
        }

        function processCommand($moduleName, $funcName)
        {
           if (!$this->moduleExists($moduleName))
               trigger_error("module `$moduleName` doesn't exist", PM_FATAL);

           $this->loadModule($moduleName);
           
           $mod = $this->_modules[$moduleName]["mod"];
           
           if (!isset($mod->cmdFunctions["$funcName"]))
               trigger_error("`$funcName` is not declared as a cmd", PM_FATAL);

           $mod->execute($funcName, array(), false);
        }
    }
?>

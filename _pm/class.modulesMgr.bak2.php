<?
/**
 *  Modules Manager
 * 
 */
    class ModulesManager
    {
        var $_modules;
		
		/**
		 *  __construct
		 */
        function ModulesManager()
        {
            $this->_modules = array();
            
            // Если каиалог модулей найден
            if (file_exists(GetCfg('ModulesPath')))
            {
                // Открыть каталог
                if ($dirHandle = opendir(GetCfg('ModulesPath'))) 
                {
                    // Перебирать файлы из каталога
                    while ( $file = readdir($dirHandle) ) 
                    {
                        // пропуская каталоги "." и ".."
                        if ($file != '.' && $file != '..')
                        {
                            // по маске "mod.*"
                            if (substr($file, 0, 4) == 'mod.')
                            {
                                $m_name = substr($file, 4, strlen($file) - 8);
                                
                                // Зарегистрировать модуль в объекте
                                $this->_modules[$m_name] = array('fileName' => $file, 'loaded' => 0, 'mod' => NULL);
                            
                            } elseif (substr($file, 0, 4) == 'cfg.') {

                                //reading cfg that is required to do per-module editing
                                // Иначе, если найден файл конфигурации модуля, 
                                // то загрузить конфигурацию в сценарий
                                require_once(GetCfg('ModulesPath') . '/' . $file);
                            }
                        }
                    }

                    // закрыть каталог
                    closedir($dirHandle);
                }
            }
        }

        /**
         * Возвращает массив из зарегистрированных модулей
         *
         * @return array
         */
        function modules()
        {
            return $this->_modules;
        }

        
        /**
         * Проверяет существование модуля
         *
         * @param string $moduleName
         * @return boolean
         */
        function moduleExists($moduleName)
        {
            return isset($this->_modules[$moduleName]);
        }
/**
 * Enter description here...
 *
 * 
 * 
 * При вызове из функции structure() [_kernel.edit.php] поступают ледующие переметры:
 *  
 * Вызов первый, который возвращает значение в $addCols
 * execute( 'Articles', 'getAdditionalColumns', 'Article', true );
 * 
 * @param string $moduleName
 * @param string $funcName
 * @param string $args
 * @param boolean $silent
 * @return unknown
 */
        function execute($moduleName, $funcName, $args, $silent)
        {
            // есди модуль зарегестрирован
            if (isset($this->_modules[$moduleName]))
            {
                // есди модуль не загружен, то загрузить
                if ($this->_modules[$moduleName]['loaded'] == 0)
                {
                    $this->loadModule($moduleName);
                }
                // вернуть ...
                return $this->_modules[$moduleName]['mod']->execute($funcName, $args, $silent);
            }
            else
                trigger_error('Module ['.$moduleName.'] not found.', PM_FATAL);
        }
        
        
        /**
         * Подключает код модуля $modulName и создает его экземляр
         *
         * @param string $moduleName
         */
        function loadModule($moduleName)
        {
            // Если модуль зарегистрирован
            if (isset($this->_modules[$moduleName]))
            {
                // Если модуль не загружен
                if ($this->_modules[$moduleName]['loaded'] == 0)
                {
                    // подключить к сценарию код модуля
                    require_once(GetCfg('ModulesPath') . '/' . $this->_modules[$moduleName]['fileName']);

                    // Если экземпляр модуля не создан 
                    if (class_exists($moduleName)) {
                        // создать экземпляр
                        $mod = new $moduleName();
                        if ($mod) {
                            $this->_modules[$moduleName]['mod'] = $mod;
                        } else {
                            trigger_error("Couldn`t create an instance of [$moduleName] class.", PM_FATAL);
                        }
                    } else {
                        trigger_error("Module " . $this->_modules[$moduleName]["fileName"] . 
                                      " doesn`t contain a definition of [$moduleName] class.", PM_FATAL);
                    }
                    
                    // Пометить, что модуль подключен и создан его экземпляр
                    $this->_modules[$moduleName]['loaded'] = 1;
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
           
           $mod = $this->_modules[$moduleName]['mod'];
           
           if (!isset($mod->cmdFunctions['$funcName']))
               trigger_error("`$funcName` is not declared as a cmd", PM_FATAL);

           $mod->execute($funcName, array(), false);
        }
    }
?>

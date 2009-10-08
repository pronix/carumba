<?
/******************************
Various managers initialization
*******************************/
    function initErrorLogging($filePath)
    {
        global $errorLog;
        $errorLog = new LogManager($filePath);
        register_shutdown_function(array(&$errorLog, "LogManagerDestroy"));
        set_error_handler("errorHandlerLogged");
    }

    function initDBLink($dbtype)
    {
        switch ($dbtype)
        {
            case "mysql":
            {
                include_once("mysql.link.php");
                break;
            }
            default:
            {
                trigger_error("Only mysql type is currently supported", PM_FATAL);
            }
        }
    }

    function initStructure()
    {
        global $structureMgr;

        if (class_exists("StructureManager"))
            $structureMgr = new StructureManager();
        if (!isset($structureMgr))
            trigger_error("Couldn`t create StructureManager", PM_FATAL);
    }

    function initModules()
    {
        global $modulesMgr;

        if (class_exists("ModulesManager"))
            $modulesMgr = new ModulesManager();
        if (!isset($modulesMgr))
            trigger_error("Couldn`t create ModulesManager", PM_FATAL);
    }

    function initTemplates()
    {
        global $templatesMgr;

        if (class_exists("TemplatesManager"))
            $templatesMgr = new TemplatesManager();
        if (!isset($templatesMgr))
            trigger_error("Couldn`t create TemplatesManager", PM_FATAL);
    }

    function initCache()
    {
        global $cacheMgr;

        if (class_exists("CacheManager"))
            $cacheMgr = new CacheManager();
        if ($cacheMgr == NULL)
            trigger_error("Couldn`t create CacheManager", PM_FATAL);
    }


    function initAuthentication()
    {
        global $authenticationMgr;

        if (class_exists("AuthenticationManager"))
            $authenticationMgr = new AuthenticationManager();
        if (!isset($authenticationMgr))
            trigger_error("Couldn`t create AuthenticationManager", PM_FATAL);
    }

    function initPermissions()
    {
        global $permissionsMgr;

        if (class_exists("PermissionsManager"))
            $permissionsMgr = new PermissionsManager();
        if (!isset($permissionsMgr))
            trigger_error("Couldn`t create PermissionsManager", PM_FATAL);
    }

    function initNotifications()
    {
        global $notificationsMgr;

        if (class_exists("NotificationsManager"))
            $notificationsMgr = new NotificationsManager();
        if (!isset($notificationsMgr))
            trigger_error("Couldn`t create NotificationsManager", PM_FATAL);
    }

?>
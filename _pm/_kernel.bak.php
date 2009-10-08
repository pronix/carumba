<?
/* 
   $Revision: 1.11.4.9 $ 
   $Date: 2006/02/14 08:19:29 $
*/
	// Если вызван не из index2.php
    if (!defined('FROM_INDEX')) exit(-1);
    
	// засекаем время
    $stime = get_microtime();

//    ob_start('ob_gzhandler');
	// включаем буферизацию
   ob_start();
/******************************************************************************
                                MAIN FLOW START
******************************************************************************/

	// устанавливаем пользовательский обработчик ошибок
    setUpErrorReporting();
/*
   Smart required kernel modules loading is impossible - 
   it's fucking PHP shit, that cannot do include() in global scope,
   when called from a function, so we'll do the stupid require()
   I don't want to call get_defined_vars() and globalize them...
*/
	
	require('IT.php');
  
	require('sd.class.template.php');
	require('sd.class.customSqlObjects.php');
	require('sd.class.Users.php');
	require('sd.class.Cards.php');
	require('sd.class.Mail.php');
	require('sd.class.Banner.php');
	require('sd.class.Vote.php');

    require('_kernel.config.php');
    require('_kernel.init.php');

    require('class.logMgr.php');

    initErrorLogging(GetCfg('ErrorLogPath'));

    initDBLink(GetCfg('dbtype'));

    require('class.structureMgr.php');
    require('class.cacheMgr.php');
    require('class.authenticationmgr.php');
    require('class.permissionsMgr.php');
    require('class.absModule.php');
    require('class.modulesMgr.php');
    require('class.templatesMgr.php');


    if (!class_exists('StructureManager'))
        trigger_error('Required class [StructureManager] not found', PM_FATAL);


    if (!class_exists('ModulesManager'))
        trigger_error('Required class [ModulesManager] not found', PM_FATAL);


    if (!class_exists('TemplatesManager'))
        trigger_error('Required class [TemplatesManager] not found', PM_FATAL);

    initStructure(); //$structureMgr will be created 
    
    initModules();  //$modulesMgr will be created
    initTemplates(); //$templatesMgr will be created
    
//    initNotifications(); //$ntfMgr will be created
    initCache(); //$cacheMgr will be created
    initAuthentication(); //$authenticationMgr will be created
    initPermissions(); //$permissionsMgr will be created
    

    processRequest();

    if (isset($_errors) && !GetCfg('SuppressErrors') && !empty($_errors))
        echoErrorBlock($_errors);

    $stime = round(get_microtime() - $stime, 3);
    
    $userID = $authenticationMgr->getUserID();

    if ($userID == 1) 
        $userName = 'anonymous';
    else
    {
        $ud = $authenticationMgr->getUserData($userID);
        $userName = $ud['Login'];
    }
    print "\n<!-- Generated in $stime seconds for user [ $userName ] -->\n";

/******************************************************************************
                                 MAIN FLOW END
******************************************************************************/
    ob_end_flush();

    exit(0);

/******************************************************************************
                        KERNEL FUNCTIONS DEFINITIONS
******************************************************************************/
//$CFG[] accessors
    function GetCfg($key)
    {
        global $CFG;
        if (isset($CFG[$key]))
            return $CFG[$key];
        else
            return ''; //TODO: check if this value is in the database, aquire it and cache in the $CFG array

    };
    
    function SetCfg($key, $val)
    {
        global $CFG;
        if ($key)
        {
            if ($val)
                $CFG[$key] = $val;
            else
                unset($CFG[$key]);
        }
    };

    function _get($var)
    {
        //global $HTTP_GET_VARS;
        if (isset($_GET[$var]))
            return $_GET[$var];

        return '';
    }

    function _post($var)
    {
        //global $HTTP_POST_VARS;
        if (isset($_POST[$var]))
            return $_POST[$var];

        return '';
    }

    function _var($var)
    {
        $res = _get($var);

        return ($res !== '') ? $res : _post($var);
    }

    function _getByPattern($pattern)
    {
        $keys = array_keys($_GET);
        $res = array();
        foreach ($keys as $v) {
            if (preg_match($pattern, $v)) {
                $res[$v] = $_GET[$v];
            }
        }

        return $res;
    }

    function _postByPattern($pattern)
    {
        global $_POST;
        $keys = array_keys($_POST);
        $res = array();

        foreach ($keys as $v)
        {
            if (preg_match($pattern, $v))
                $res[$v] = $_POST[$v];
        }

        return $res;
    }

    function _varByPattern($pattern)
    {
        $res = _getByPattern($pattern);

        return (count($res) > 0) ? $res : _postByPattern($pattern);
    }

    function _cookie($var)
    {
        global $_COOKIE;
        if (isset($_COOKIE[$var]))
            return $_COOKIE[$var];

        return '';
    }

    function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)((float)$usec + (float)$sec);
    }

    function set_safe_newlines($str)
    {
        $str = str_replace('\r', '\\r', $str);
        $str = str_replace('\n', '\\n', $str);

        return $str;
    }

    function remove_safe_newlines($str)
    {
        $str = str_replace('\\r', '\r', $str);
        $str = str_replace('\\n', '\n', $str);

        return $str;
    }

    function safe_numeric($num)
    {
        if (!is_numeric($num))
        {
            $num = preg_replace('/^.*(\d+).*$/', '\\1', $num);
            if (!is_numeric($num))
            {
                $num = 0;
            }
        }
        return $num;
    }

    function echoErrorBlock($errorBlock)
    {
        if ($errorBlock)
            echo "<div style=\"border: 1 solid #A0A0A0; 
                  background-color: #FFFDA0; padding: 5px;\">$errorBlock</div>\n";
    }

/******************************************************************************
          Simple error reporting without loaded Log Manager
******************************************************************************/
    function setUpErrorReporting()
    {
        define('PM_FATAL', E_USER_ERROR);
        define('PM_ERROR', E_USER_WARNING);
        define('PM_WARNING', E_USER_NOTICE);
        error_reporting(PM_FATAL | PM_ERROR | PM_WARNING | E_ERROR | E_COMPILE_ERROR );
        set_error_handler('errorHandler');
    }

    function errorHandler($errno, $errstr, $errfile, $errline) 
    { 
        global $_errors;
        switch ($errno) 
        {
        case PM_FATAL:
        case E_ERROR:
        case E_COMPILE_ERROR:
        case E_WARNING:
            if (isset($_errors))
                $_errors .= '<hr noshade size=1 width=100%>';

            echo "<div style=\"border: 1 solid #A0A0A0; 
              background-color: #FFFDA0; padding: 5px;\">$_errors
              <dt><b><span style='color:red'>Fatal error </span></b> at $errfile:$errline</dt>
              <dd>Description: $errstr</dd></div>\n";
            exit -1;
        case PM_ERROR:
            $_errors .= "<dt>Error at $errfile:$errline</dt><dd>Description: $errstr</dd>\n";
            break;
        case PM_WARNING:
            $_errors .= "<dt>Warning at $errfile:$errline</dt><dd>Description: $errstr</dd>\n";
            break;
        default:
            if (strpos($errstr, 'Deprecated') === false)
                $_errors .= "<dt>PHP error at $errfile:$errline</dt><dd>Description: $errstr</dd>\n";

            break;
        }
    }

    function errorHandlerLogged($errno, $errstr, $errfile, $errline) 
    { 
        global $errorLog, $_errors;
        switch ($errno) {
        case PM_FATAL:
            if (isset($_errors))
                $_errors .= '<hr noshade size=1 width=100%>';

            echo "<div style=\"border: 1 solid #A0A0A0; background-color: #FFFDA0; padding: 5px;\">
              $_errors<dt><b><span style='color:red'>Fatal error </span></b> at $errfile:$errline</dt>
              <dd>Description: $errstr</dd></div>\n";
            $errorLog->writeLine($errno, $errstr, $errfile, backtrace());
            exit -1;
        case PM_ERROR:
            $_errors .= "<dt>Error at $errfile:$errline</dt><dd>Description: $errstr</dd>\n";
            $errorLog->writeLine($errno, 'Error: ' . $errstr, $errfile, backtrace());
            break;
        case PM_WARNING:
            $_errors .= "<dt>Warning at $errfile:$errline</dt><dd>Description: $errstr</dd>\n";
            $errorLog->writeLine($errno, 'Warning: ' . $errstr, $errfile, $errline);
            break;
        default:
            {
                if (strpos($errstr, 'Deprecated') === false)
                {
                    $errors .= "<dt>PHP error at $errfile:$errline</dt><dd>Description: $errstr</dd>\n";
                    $errorLog->writeLine($errno, 'PHP error: ' . $errstr, $errfile, $errline);
                }
            }
            break;
        }
    }

    function backtrace()
    {
        if (!function_exists('debug_backtrace'))
            return '';

        $output = "\n";
        $backtrace = debug_backtrace();
        foreach ($backtrace as $bt) {
            $args = '';
            foreach ($bt['args'] as $a) {
                if (!empty($args)) {
                    $args .= ', ';
                }
                switch (gettype($a)) {
                case 'integer':
                case 'double':
                    $args .= $a;
                    break;
                case 'string':
                    $a = htmlspecialchars(substr($a, 0, 64)).((strlen($a) > 64) ? '...' : '');
                    $args .= "\"$a\"";
                    break;
                case 'array':
                    $args .= 'Array('.count($a).')';
                    break;
                case 'object':
                    $args .= 'Object('.get_class($a).')';
                    break;
                case 'resource':
                    $args .= 'Resource('.strstr($a, '#').')';
                    break;
                case 'boolean':
                    $args .= $a ? 'True' : 'False';
                    break;
                case 'NULL':
                    $args .= 'Null';
                    break;
                default:
                    $args .= 'Unknown';
                }
            }
            $output .= "{$bt['file']}:{$bt['line']} in {$bt['class']}{$bt['type']}{$bt['function']}($args)\n";
        }
        	
        return $output;
    }

/******************************************************************************
                              REQUEST PROCESSING
******************************************************************************/

function processRequest()
{
    global $modulesMgr;

//ModRewrite handling
    $redirectUrl = getenv('REDIRECT_URL');
    if (!$redirectUrl)
        $redirectUrl = '/';
//Kernel-level commands
    $cmd = _var('cmd');
    if ($cmd)
        processCommand($cmd);
    //Module-level commands
    $module = _post('module');
    $function = _post('function');

    // Выполнение функции $function из модуля $module
    if ($module && $function)
    {
       if ($modulesMgr->moduleExists($module))
           $modulesMgr->execute($module, $function, array(), true);
       else
           trigger_error("Invalid call to $module -> $function", PM_WARNING);
    }

    //Admin request handling        
    if (preg_match('/^(\/admin)$/', $redirectUrl, $match) || 
        preg_match('/^(\/admin\/).*/', $redirectUrl, $match))
    {
        // Удалить из URL строку "/admin/"
        $redirectUrl = str_replace($match[1], '', $redirectUrl);
        // Перейти в раздел администрирования
        processAdminRequest('/' . $redirectUrl);
        return;
    }

    //Carumba orders request handling        
    if (preg_match('/^(\/carorders)$/', $redirectUrl, $match) || 
        preg_match('/^(\/carorders\/).*/', $redirectUrl, $match))
    {
        $redirectUrl = str_replace($match[1], '', $redirectUrl);
        processOrdersRequest('/' . $redirectUrl);
        return;
    }

    if (preg_match('/^\/login\/?$/', $redirectUrl))
    {
        processLoginRequest();
    } else {
        processStdRequest($redirectUrl);
	}
}

function processCommand($cmd)
{
    global $modulesMgr, $structureMgr, $authenticationMgr, $permissionsMgr, $cacheMgr, $templatesMgr;
    switch ($cmd)
    {
    //authentication
        case 'login':
        {
            if ($authenticationMgr->authenticate() == true)
            {
                $redirectURL = _post('redirectURL');
                
                if ($redirectURL)
                {
                    header('Status: 302 Moved');
                    header("Location: $redirectURL");
                }
                else
                {
                    header('Status: 302 Moved');
                    header('Location: /');
                }
                exit;
            }
            else
            {
                $referrer = getenv('HTTP_REFERER');
                
                if (strpos($referrer, 'wrongLogin=true') === false)
                {
                    if (strpos($referrer, '?') === false)
                        $referrer .= '?';
                    else
                        $referrer .= '&';

                    $referrer .= 'wrongLogin=true';
                }
                
                header('Status: 302 Moved');
                header("Location: $referrer");

                exit;
            }
            break;
        }

        case 'logout':
        {
            $authenticationMgr->endSession();
            
            header('Status: 302 Moved');
            header('Location: /');

            exit;
        }

        default:
        {
            $moduleName = _post('module');
            if ($moduleName)
                $modulesMgr->processCommand($moduleName, $cmd);
        }
    }
}

function processStdRequest($url)
{
    global $modulesMgr, $structureMgr, $authenticationMgr, $permissionsMgr, $cacheMgr, $templatesMgr;
    
    SetCfg('InAdmin', false);
    
    
    $pageID = $structureMgr->getPageIDByPath($url); 
	
    $md = $structureMgr->getMetaData($pageID);

    if ($pageID && $md['isHidden'])
    {
        while ($md['isHidden'])
        {
            $md = $structureMgr->getMetaData($md['pms_sID']);
        }
        
        if ($md['sID'] == $structureMgr->getRootPageID()) {
            $md['sID'] = $structureMgr->getDefaultPageID();
        }

        $path = $structureMgr->getPathByPageID($md['sID'], true);

        header('Location: '.$path);
        exit(0);
    }

    if (!$pageID) {
        trigger_error('Beautiful 404 will be provided later (for URL:'.$url.'). But now it is just a fatal error.', PM_FATAL);
        header('location: /');
    }

    $userID = $authenticationMgr->getUserID();

    if (!$permissionsMgr->canRead($pageID, $userID))
    {
        if ($userID > 1)
            trigger_error("Beautiful 403 will be provided later. But now it is just a fatal error.", PM_FATAL);
        else
        {
//            trigger_error("Beautiful login form will be provided later. But now it is just a fatal error.", PM_FATAL);
            header('Location: /login');
            exit;
        }
    }                                       
   
    $fullPage = $cacheMgr->getCache($pageID);


    if ($fullPage == NULL) //just not cached yet
    {
        $fullPage = $templatesMgr->getFilledTemplate($pageID, '');
        $cacheMgr->setCache($pageID, $fullPage);
    } 

    //print headers for cache lifetime (Expires etc.) and some like X-Powered-By: PortalMaster System v.1.0
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');    
    header('Content-type: text/html; charset=windows-1251');
    print $fullPage;
    
}

function processAdminRequest($url)
{
    global $modulesMgr, $structureMgr, $authenticationMgr, $permissionsMgr, $cacheMgr, $templatesMgr;
    
    $userID = $authenticationMgr->getUserID();
    $userGroupID = $authenticationMgr->getUserGroup();
    
    // Если пользователь - гость, то отправить логиниться
    if ($userID == 1 || $userGroupID != 5 ) {
        header('Status: 302 Moved');
        header('Location: /login'); 
        exit();
    }

    SetCfg('InAdmin', true);

    //$pageID = isset($_GET['pageID']) ? $_GET['pageID'] : -1;
    
    if ($url == '/') //root of administrator`s page
    {
        $pageID = -1;
    } else { //direct link is provided to edit some content
        $pageID = $structureMgr->getPageIDByPath($url); 
        //$md = $structureMgr->getMetaData($pageID);
        
        if (!$pageID)
            trigger_error('Admin 404 will be provided later. But now it is just a fatal error.', PM_FATAL);

        if ( !$permissionsMgr->canUpdate($pageID, $userID))
            trigger_error('Beautiful 403 will be provided later. But now it is just a fatal error.', PM_FATAL);
        
    }

  
    
    include('_kernel.edit.php');

    $tpl = $templatesMgr->getTemplate(-1, GetCfg('TemplatesPath') . '/admin/page.html');

    $tpl = str_replace('%site_name%', GetCfg('SiteName'), $tpl);

    $res = '';
    $cmd = _var('cmd');

    if ($pageID == -1)
    {
        $pageID = _var('pageID');
        
        if (!$pageID)
            $pageID = $structureMgr->getRootPageID();
        
        //print 'pageID='.$pageID;

        if ($permissionsMgr->canUpdate($pageID, $userID) == false)
            trigger_error('Beautiful 403 will be provided later. But now it is just a fatal error.', PM_FATAL);
        
        if (!$cmd)
        {
            $res .= navi($pageID, false);
            $res .= structure($pageID); // this very evil function. Very slowed.
        }
        else
        {
            $res .= processAdminCommand($cmd, $pageID);
        }

    }
    else
    {
        if ($permissionsMgr->canUpdate($pageID, $userID) == false)
            trigger_error('Beautiful 403 will be provided later. But now it is just a fatal error.', PM_FATAL);
        
        $res .= processAdminCommand('editPage', $pageID);
    }

    $admMenu = admMenu($cmd);

    
    $tpl = str_replace('%content%', $res, $tpl);
    $tpl = str_replace('%adm_menu%', $admMenu, $tpl);
    print $tpl;
}

function processOrdersRequest($url)
{
    global $modulesMgr, $structureMgr, $authenticationMgr, $permissionsMgr, $cacheMgr, $templatesMgr;
    
    $userID = $authenticationMgr->getUserID();

    if ($userID == 1)
    {
        header('Status: 302 Moved');
        header('Location: /login'); 
        exit;
    }

    SetCfg('InOrders', true);

    include('_kernel.orders.php');


    $res .= processOrdersCommand('orders');

    print $res;
}

function processLoginRequest()
{        
    global $templatesMgr;
    print $templatesMgr->getFilledTemplate(-1, GetCfg('LoginTemplate'));
}


?>

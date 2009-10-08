<?
	//ob_start();
    global $CFG;
    
    $CFG = array();
        
//Site specific
    $CFG["RootURL"] = "http://www.carumba.ru/"; //not needed
    $CFG["SiteName"] = "Карумба.Ру";
    $CFG["DefaultDescription"] = "";
    $CFG["DefaultKeywords"] = "Автозапчасти, Масла, Тюнинг ВАЗ, Литые диски, Шины, Автосигнализации, Автозвук";
    
    $CFG["AdminNotificationEmail"] = "admin@portalmaster.ru";

//db setup
    $CFG["dbtype"] = "mysql"; //maybe in future we will add `pgsql`
    
    $CFG["mysql.dbname"] = "carumba";
    $CFG["mysql.host"] = "localhost";
    $CFG["mysql.port"] = "";
    $CFG["mysql.username"] = "webcarumba";
    $CFG["mysql.password"] = "6Fasj6FQ7d";



//Defaults
    
    $CFG["UTC"] = "+3";
//    $CFG["ROOT"] = str_replace(getenv("SCRIPT_NAME"), "", getenv("SCRIPT_FILENAME"));
    $CFG["ROOT"] = getenv("DOCUMENT_ROOT");

    if ($CFG["ROOT"] == "")
        $CFG["ROOT"] = ".";
    
    $CFG["ModulesPath"] = $CFG["ROOT"] . "/_pm/modules";
    $CFG["TemplatesPath"] = $CFG["ROOT"] . "/_pm/templates";
    $CFG["ErrorLogPath"] = $CFG["ROOT"] . "/_logs/error_log";
    $CFG["DateFormat"] = "%a %b %d %H:%M:%S %Y";

    $CFG["AdminTemplate"] = $CFG["TemplatesPath"] . "/admpage.html";
    $CFG["LoginTemplate"] = $CFG["TemplatesPath"] . "/loginpage.html";
    
    
    //if true, adminpanel will be shown as a small transparent block with link to open big block
    $CFG["AdminPanelShowHidden"] = false; 
    
    //if previous is false, this defines the timeout when inactive block should be hidden
    $CFG["HideAdminPanelTimeout"] = "20000";


    $CFG["SuppressErrors"] = FALSE;

    $CFG["ModuleNames"] = array("Articles" => "Публикации", "News" => "Новости", "Catalogue" => "Каталог", "Cart" => "Корзина заказа");

?>

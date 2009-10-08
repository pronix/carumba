<?
/*
    Cache Manager
*/
    class CacheManager
    {
        var $dblink;

        function CacheManager()
        {
            $this->dblink = GetCfg("dblink");    
        }

        function getCache($pageID)
        {
            return NULL; //anonymous. later will check cookie and sessions table
        }
        
        function setCache($pageID, $content)
        {//must put it into some directory
            return 0;
        }
    }
?>
<?
/*
    Permissions Manager
*/
    define("PERMISSIONS_PRESENT", 1);
    
    class PermissionsManager
    {
        var $dblink;

        function PermissionsManager()
        {
            $this->dblink = GetCfg("dblink");    
        }

        function canRead($pageID, $userID)
        {
            return true; //
        }
        
        function canUpdate($pageID, $userID)
        {
            global $authenticationMgr;
            $ud = $authenticationMgr->getUserData($userID, "");
            if (($ud["groupID"] == 5) || ($ud["groupID"] == 7))
                return true; //

            return false;
        }
    }
?>
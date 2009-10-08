<?

function quickAdminBlock($pageID)
{
    global $modulesMgr, $structureMgr, $authenticationMgr, $permissionsMgr, $cacheMgr, $templatesMgr;
    $realURL = $structureMgr->getPathByPageID($pageID, false);
    
    $metaData = $structureMgr->getMetaData($pageID);
    
    $dt = $metaData["DataType"];
    $dTypes = $modulesMgr->execute($metaData["ModuleName"], "getSubItemType", array($metaData["DataType"]), false);
    $addLinks = "";

    foreach ($dTypes as $k => $v)
    {
        if ($addLinks)
            $addLinks .= ", ";

        $addLinks .= "<a class=\"admlink\" target=\"_blank\" href=\"/admin/?cmd=addPage&amp;pageID=$pageID&amp;ModuleName=$metaData[ModuleName]&amp;DataType=$k\">$v</a>";
    }

    if ($addLinks)
    {
        $addLinks = " | <strong style=\"font-size: 13px;\">Добавить: </strong>$addLinks";
    }

    if (GetCfg("AdminPanelShowHidden"))
    {
        $mblock = "none"; $hblock = "block";
    }
    else
    {
        $hblock = "none"; $mblock = "block";
    }

    $timeout = GetCfg("HideAdminPanelTimeout");

    return
        "
        <script type=\"text/javascript\">
            function showBlock(b, show, centered)
            {
                bl = document.getElementById(b);
                if (bl)
                {
                    bl.style.display = (show ? 'block': 'none');
                    /*if (centered)
                    {
                        bl.style.left = (document.body.clientWidth/2) - (bl.offsetWidth/2);
                        bl.style.top = (document.body.clientHeight/2) - (bl.offsetHeight/2);
                    }*/
                }
            }

            function hideQuickAdmin()
            {
                showBlock('QAdm', 0, true); 
                showBlock('QAdmShow', 1, false);
            }



            var qaTimeOut = window.setTimeout(\"hideQuickAdmin();\", $timeout);
        </script>


<!-- QuickAdmin -->
        <style type=\"text/css\">
            .admblk { font-family: Tahoma, Serif; font-size: 14px; background-color: #F6F6F6; }
            a.admlink:link,a.admlink:visited,a.admlink:hover,a.admlink:active {font-size: 13px; font-weight: bold; text-decoration: underline;}
            a.admlink2:link,a.admlink2:visited,a.admlink2:hover,a.admlink2:active {font-size: 13px; font-weight: bold; color: #10A010;  text-decoration: underline;}
        </style>

        <div id=\"QAdm\" class=\"admblk\" style=\"display: $mblock;position: relative; width: 97%;
        border-style: double; border-color: #C6C6C6; padding: 0px 10px 10px 10px; margin: 5px;
        filter:progid:DXImageTransform.Microsoft.dropshadow(OffX=5, OffY=5, Color='gray', Positive='true');
         -moz-opacity: 0.9;\" onmouseover=\"window.clearTimeout(qaTimeOut);\">
            <div style=\"margin-bottom: 5px; margin-top: 0px;\">
            <a href=\"#\" onclick=\"showBlock('QAdm', 0, true); showBlock('QAdmShow', 1, false); return false;\" style=\"font-size: 10px;\">скрыть панель администратора</a>
            </div>
            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"admblk\" width=\"100%\">
                <tr><td>
                <a class=\"admlink\" target=\"_blank\" href=\"/admin$realURL\">Редактировать</a>
                 $addLinks
                 | <a class=\"admlink\" target=\"_blank\" href=\"/admin/?cmd=hidePage&amp;pageID=$pageID\" onclick=\"return confirm('Действительно скрыть текущий раздел со всеми подразделами?')\">Скрыть</a>
                 | <a class=\"admlink\" target=\"_blank\" href=\"/admin/?cmd=delPage&amp;pageID=$pageID\" style=\"color: #CE0530;\" onclick=\"return confirm('Действительно удалить текущий раздел со всеми подразделами?')\">Удалить</a>
                </td>
                <td align=right><a class=\"admlink2\" target=\"_blank\" href=\"/admin/\">Структура сайта</a>
                </td>
                </tr>
            </table>
        </div>

<!-- Hidden QuickAdmin -->

        <div id=\"QAdmShow\" style=\"display: $hblock; width: 150; margin: 5px;
        font-family: Tahoma, Serif; font-size: 9px; background-color: #F6F6F6; 
        border-style: solid; border-color: #334DFE; border-width: 1px;
        padding: 0px 5px 0px 4px; 
        filter:progid:DXImageTransform.Microsoft.Alpha(opacity=75)
               progid:DXImageTransform.Microsoft.dropshadow(OffX=3, OffY=3, Color='gray', Positive='true'); 
               -moz-opacity: 0.75;\">
        <a href=\"#\" onclick=\"showBlock('QAdmShow', 0, false); 
        showBlock('QAdm', 1, true); window.clearTimeout(qaTimeOut); return false;\" style=\"font-size: 10px;\">панель администратора</a>
        </div>
        \n";
}

?>

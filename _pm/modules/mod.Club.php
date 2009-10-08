<?

require_once("pw.Club.php");

    class Club extends AbstractModule
    {
        function Club()
        {
            $this->name = "Club";
            $this->desc = "Provides a news system with comments";
            $this->publicFunctions = array("getContent", "getSubItemType", "getItemType", "getItemDesc", "getSpecificDataForEditing", "getSpecificBlockDesc", "updateSpecificData", "newsBlock", 'updateAdditionalColumns');
            $this->cmdFunctions = array();

            SetCfg("Club.perPage", 10);
        }

        function getSpecificDataForEditing($args)
        {
            return array();
        }

        function updateSpecificData($args)
        {
            return true;
        }

        function getSpecificBlockDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "CardsList":
                    return "Параметры";
                case "CardItem":
                    return "Параметры";
            }

            return "";
        }

        function getItemDesc($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "CardsList":
                    return "";
                case "CardItem":
                    return "Отдельная карта";
            }

            return "";
        }

        function getItemType($args)
        {
            $DataType = $args[0];
            switch ($DataType)
            {
                case "CardsList":
                    return array("список карт", "список карт", "списка карт"); //Именит, Род, Дат
                case "CardItem":
                    return array("карта", "карту", "карт");
            }

            return array();
        }

        function getSubItemType($args)
        {
            $DataType = $args[0];

            switch ($DataType)
            {
                case "Article":
                    return array("CardsList" => "список карт");

                case "CardsList":
                    return array("CardItem" => "карту");
            }

            return array();
        }

        function getContent($args)
        {
            global $structureMgr;
            $metaData = $structureMgr->getMetaData($args[0]);
            switch ($metaData["DataType"])
            {
                case "CardsList":
                    return "<div class=\"podbor\">".$this->getNewsList($args[0], "newslist.xml", -1)."</div>";
                case "CardItem":
                    return "<div class=\"podbor\">".$this->getNews($args[0])."</div>";
            }
        }

        function getNews($pageID)
        {
            global $structureMgr;
            $md = $structureMgr->getMetaData($pageID);
            return $md["CreateDate"] . "<hr noshade size=1>" . $structureMgr->getData($pageID);
        }

        function getNewsList($pageID, $tplFilename, $count)
        {
            global $structureMgr, $templatesMgr;

            $res = "";

            $branch = $structureMgr->getStructureForPageID($pageID, 1);
            if (count($branch) == 0){

				$clubHandler = new ClubHandler();
                return $clubHandler->getContent();

            }

            $tpl = $templatesMgr->getTemplate(-1, GetCfg("TemplatesPath") . "/News/" . $tplFilename);

            $tpl = set_safe_newlines($tpl);

            $blocks = $templatesMgr->getValidTags(&$tpl, array("container", "item", "separator"));

            $from = count($branch);
            if (($count == -1) || ($count > $from))
                $to = 0;
            else
                $to = $from - $count;

            for ($i = $from - 1; $i >= $to; $i--)
            {
                $news = $branch[$i];
                $URL = $structureMgr->getPathByPageID($news["sID"], true);
                $bl = $blocks["item"];
                $bl = str_replace("%short_title%", $news["ShortTitle"], $bl);
                $bl = str_replace("%date%", $news["CreateDate"], $bl);
                $bl = str_replace("%news_link%", $URL, $bl);
                $bl = str_replace("%summary%", $structureMgr->getData($news["sID"]), $bl);

                if ($res && isset($blocks["separator"]))
                    $res .= $blocks["separator"];
                $res .= $bl;
            }
            $blocks["container"] = str_replace("%items%", $res, $blocks["container"]);

            return $blocks["container"];
        }

        function newsBlock($args)
        {
            if (!isset($args["TEMPLATE"]) || ($args["TEMPLATE"] == ""))
                trigger_error("Template for newsBlock must be specified", PM_FATAL);

            if (!isset($args["NEWSLINEID"]) || ($args["NEWSLINEID"] == ""))
                trigger_error("NewsLineID for newsBlock must be specified", PM_FATAL);

            if (!isset($args["NEWSCOUNT"]))
                $args["NEWSCOUNT"] = -1;

            return $this->getNewsList($args["NEWSLINEID"], $args["TEMPLATE"], $args["NEWSCOUNT"]);
        }


        function updateAdditionalColumns($args)
        {
            return false;
        }
    }
?>